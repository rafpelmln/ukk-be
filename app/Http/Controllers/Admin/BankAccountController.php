<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BankAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('query', ''));
        $sort = $request->query('sort', 'created_at');
        $direction = $request->query('direction', 'desc');
        $perPage = (int) $request->query('per_page', 10);

        $allowedSorts = ['created_at', 'nama_bank', 'nama', 'no_rek'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'created_at';
        }

        $direction = $direction === 'asc' ? 'asc' : 'desc';

        $allowedPerPage = [5, 10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $bankAccounts = BankAccount::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('nama_bank', 'like', "%{$search}%")
                        ->orWhere('nama', 'like', "%{$search}%")
                        ->orWhere('no_rek', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('bank-accounts.index', [
            'bankAccounts' => $bankAccounts,
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('bank-accounts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->storeCompressedPhoto($request->file('photo'));
        }

        BankAccount::create($data);

        return redirect()->route('bank-accounts.index')->with('success', 'Rekening bank berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BankAccount $bankAccount)
    {
        return view('bank-accounts.edit', compact('bankAccount'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BankAccount $bankAccount)
    {
        $data = $this->validatedData($request);

        if ($request->hasFile('photo')) {
            $this->deletePhoto($bankAccount->photo);
            $data['photo'] = $this->storeCompressedPhoto($request->file('photo'));
        }

        $bankAccount->update($data);

        return redirect()->route('bank-accounts.index')->with('success', 'Rekening bank berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankAccount $bankAccount)
    {
        $this->deletePhoto($bankAccount->photo);
        $bankAccount->delete();

        return redirect()->route('bank-accounts.index')->with('success', 'Rekening bank berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'nama_bank' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'no_rek' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ]);
    }

    private function storeCompressedPhoto(UploadedFile $file): string
    {
        $destination = public_path('foto/bank-accounts');
        if (!is_dir($destination)) {
            @mkdir($destination, 0755, true);
        }

        $targetSize = 250 * 1024; // 250 KB
        $filename = Str::uuid()->toString() . '.jpg';
        $relativePath = 'foto/bank-accounts/' . $filename;

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

        $tempPath = tempnam(sys_get_temp_dir(), 'bank_account_photo_');
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
                    throw new \RuntimeException('Gagal menyimpan foto rekening bank.');
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
}
