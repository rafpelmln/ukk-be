<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeBanner;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HomeBannerController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('query', ''));
        $sort = $request->query('sort', 'display_order');
        $direction = $request->query('direction', 'asc');
        $perPage = (int) $request->query('per_page', 10);

        $allowedSorts = ['display_order', 'title', 'created_at', 'is_active'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'display_order';
        }

        $direction = $direction === 'desc' ? 'desc' : 'asc';

        $allowedPerPage = [5, 10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $banners = HomeBanner::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%")
                        ->orWhere('subtitle', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction)
            ->orderBy('display_order')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('home-banners.index', [
            'banners' => $banners,
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        return view('home-banners.create');
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request, true);

        if ($request->hasFile('image')) {
            $data['image_path'] = $this->storeCompressedImage($request->file('image'));
        }

        HomeBanner::create($data);

        return redirect()
            ->route('home-banners.index')
            ->with('success', 'Banner beranda berhasil dibuat.');
    }

    public function edit(HomeBanner $homeBanner)
    {
        return view('home-banners.edit', [
            'banner' => $homeBanner,
        ]);
    }

    public function update(Request $request, HomeBanner $homeBanner)
    {
        $data = $this->validatedData($request, false);

        if ($request->hasFile('image')) {
            $this->deleteImage($homeBanner->image_path);
            $data['image_path'] = $this->storeCompressedImage($request->file('image'));
        }

        $homeBanner->update($data);

        return redirect()
            ->route('home-banners.index')
            ->with('success', 'Banner beranda berhasil diperbarui.');
    }

    public function destroy(HomeBanner $homeBanner)
    {
        $this->deleteImage($homeBanner->image_path);
        $homeBanner->delete();

        return redirect()
            ->route('home-banners.index')
            ->with('success', 'Banner beranda berhasil dihapus.');
    }

    public function toggle(HomeBanner $homeBanner)
    {
        $homeBanner->update([
            'is_active' => !$homeBanner->is_active,
        ]);

        return redirect()
            ->route('home-banners.index')
            ->with('success', 'Status banner diperbarui.');
    }

    private function validatedData(Request $request, bool $isCreate): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_label' => 'nullable|string|max:100',
            'button_url' => 'nullable|url|max:2048',
            'display_order' => 'nullable|integer|min:0|max:65535',
            'is_active' => 'nullable|boolean',
        ];

        $rules['image'] = ($isCreate ? 'required' : 'nullable') . '|image|max:2048';

        $validated = $request->validate($rules, [
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $validated['display_order'] = $validated['display_order'] ?? 0;
        $validated['is_active'] = isset($validated['is_active']) ? (bool) $validated['is_active'] : true;

        return $validated;
    }

    private function storeCompressedImage(UploadedFile $file): string
    {
        $destination = public_path('foto/home-banners');
        if (!is_dir($destination)) {
            @mkdir($destination, 0755, true);
        }

        $targetSize = 300 * 1024; // 300 KB target after compression
        $filename = Str::uuid()->toString() . '.jpg';
        $relativePath = 'foto/home-banners/' . $filename;

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
        $background = imagecolorallocate($canvas, 255, 255, 255);
        imagefilledrectangle($canvas, 0, 0, $width, $height, $background);
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $width, $height, $width, $height);

        $tempPath = tempnam(sys_get_temp_dir(), 'home_banner_');
        $quality = 85;

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
                    throw new \RuntimeException('Gagal menyimpan gambar banner.');
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

    private function deleteImage(?string $path): void
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
}
