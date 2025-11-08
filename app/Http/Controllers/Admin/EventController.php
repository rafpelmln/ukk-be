<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventOrder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->query('query', ''));
        $sort = $request->query('sort', 'created_at');
        $direction = $request->query('direction', 'desc');
        $perPage = (int) $request->query('per_page', 10);

        $allowedSorts = ['created_at', 'title', 'event_date'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'created_at';
        }

        $direction = $direction === 'asc' ? 'asc' : 'desc';

        $allowedPerPage = [5, 10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $events = Event::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%")
                        ->orWhere('subtitle', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('events.index', [
            'events' => $events,
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        return view('events.create');
    }

    public function show(Event $event)
    {
        $this->finalizeEventOrders($event);

        $event->loadCount('orders');

        $orders = EventOrder::with(['participant'])
            ->where('event_id', $event->id)
            ->latest()
            ->get();

        $summary = [
            'total' => $orders->count(),
            'pending' => $orders->where('status', 'pending')->count(),
            'paid' => $orders->where('status', 'paid')->count(),
            'completed' => $orders->where('status', 'completed')->count(),
        ];

        return view('events.show', compact('event', 'orders', 'summary'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->storeCompressedPhoto($request->file('photo'));
        }

        Event::create($data);

        return redirect()->route('events.index')->with('success', 'Event berhasil dibuat.');
    }

    public function edit(Event $event)
    {
        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $this->validatedData($request);

        if ($request->hasFile('photo')) {
            $this->deletePhoto($event->photo);
            $data['photo'] = $this->storeCompressedPhoto($request->file('photo'));
        }

        $event->update($data);

        return redirect()->route('events.index')->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event)
    {
        $this->deletePhoto($event->photo);
        $event->delete();

        return redirect()->route('events.index')->with('success', 'Event berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        $rawPrice = $request->input('price');
        if (is_string($rawPrice)) {
            $normalizedDigits = preg_replace('/[^0-9]/', '', $rawPrice);
            $request->merge([
                'price' => $normalizedDigits !== '' ? (float) $normalizedDigits : null,
            ]);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'photo' => 'nullable|image|max:2048',
        ]);

        if (!empty($validated['event_date'])) {
            $validated['event_date'] = Carbon::parse($validated['event_date'])->format('Y-m-d');
        } else {
            $validated['event_date'] = null;
        }

        return $validated;
    }

    private function storeCompressedPhoto(UploadedFile $file): string
    {
        $destination = public_path('foto/events');
        if (!is_dir($destination)) {
            @mkdir($destination, 0755, true);
        }

        $targetSize = 250 * 1024; // 250 KB
        $filename = Str::uuid()->toString() . '.jpg';
        $relativePath = 'foto/events/' . $filename;

        $imageData = @file_get_contents($file->getRealPath());
        if ($imageData === false) {
            $file->move($destination, $filename);
            return $relativePath;
        }

        $source = @imagecreatefromstring($imageData);
        if ($source === false) {
            $file->move($destination, $filename);
            return $relativePath;
        }

        $width = imagesx($source);
        $height = imagesy($source);

        $canvas = imagecreatetruecolor($width, $height);
        imagealphablending($canvas, true);
        $bg = imagecolorallocate($canvas, 255, 255, 255);
        imagefilledrectangle($canvas, 0, 0, $width, $height, $bg);
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $width, $height, $width, $height);

        $tempPath = tempnam(sys_get_temp_dir(), 'event_photo_');
        $quality = 85;
        $currentSize = null;

        try {
            do {
                imagejpeg($canvas, $tempPath, $quality);
                $currentSize = filesize($tempPath) ?: 0;
                if ($currentSize <= $targetSize || $quality <= 25) {
                    break;
                }
                $quality -= 10;
            } while ($quality > 10);

            if (($currentSize ?? filesize($tempPath)) > $targetSize) {
                imagejpeg($canvas, $tempPath, 10);
            }

            $finalPath = $destination . DIRECTORY_SEPARATOR . $filename;
            if (!@rename($tempPath, $finalPath)) {
                if (!@copy($tempPath, $finalPath)) {
                    throw new \RuntimeException('Gagal menyimpan foto event.');
                }
                @unlink($tempPath);
            }
        } finally {
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }

            if ($source instanceof \GdImage) {
                imagedestroy($source);
            }

            if ($canvas instanceof \GdImage) {
                imagedestroy($canvas);
            }
        }

        return $relativePath;
    }

    private function deletePhoto(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        $fullPath = public_path($path);
        if (file_exists($fullPath)) {
            @unlink($fullPath);
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function finalizeEventOrders(Event $event): void
    {
        if (!$event->event_date) {
            return;
        }

        $cutoff = Carbon::parse($event->event_date)->addDay()->endOfDay();

        if (now()->lessThanOrEqualTo($cutoff)) {
            return;
        }

        EventOrder::where('event_id', $event->id)
            ->where('status', 'paid')
            ->whereNull('checked_in_at')
            ->update([
                'status' => 'completed',
                'checked_in_at' => now(),
            ]);
    }
}
