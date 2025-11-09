<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeadershipStructureRequest;
use App\Models\LeadershipStructure;
use App\Models\Generation;
use App\Models\LeadershipStructureRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LeadershipStructureController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->query('per_page', 10);
        $allowed = [5, 10, 25, 50];
        if (!in_array($perPage, $allowed, true)) {
            $perPage = 10;
        }

        $structures = LeadershipStructure::query()
            ->with(['roles', 'generation'])
            ->orderByDesc('is_active')
            ->orderByDesc('period_year')
            ->paginate($perPage)
            ->withQueryString();

        return view('leadership-structures.index', compact('structures', 'perPage'));
    }

    public function create(): View
    {
        $generations = Generation::orderByDesc('started_at')->get();

        return view('leadership-structures.create', [
            'generations' => $generations,
        ]);
    }

    public function store(LeadershipStructureRequest $request): RedirectResponse
    {
        $payload = $this->buildPayload($request);

        $structure = LeadershipStructure::create($payload);
        $this->syncActiveState($structure);
        $this->syncRoles($structure, $request);

        return redirect()
            ->route('leadership-structures.index')
            ->with('success', 'Struktur kepemimpinan berhasil ditambahkan.');
    }

    public function edit(LeadershipStructure $leadershipStructure): View
    {
        $leadershipStructure->load('roles');
        $generations = Generation::orderByDesc('started_at')->get();

        return view('leadership-structures.edit', [
            'structure' => $leadershipStructure,
            'generations' => $generations,
        ]);
    }

    public function update(LeadershipStructureRequest $request, LeadershipStructure $leadershipStructure): RedirectResponse
    {
        $payload = $this->buildPayload($request, $leadershipStructure);

        $leadershipStructure->update($payload);
        $this->syncActiveState($leadershipStructure);
        $this->syncRoles($leadershipStructure, $request);

        return redirect()
            ->route('leadership-structures.index')
            ->with('success', 'Struktur kepemimpinan berhasil diperbarui.');
    }

    public function destroy(LeadershipStructure $leadershipStructure): RedirectResponse
    {
        $this->deleteImage($leadershipStructure->general_leader_photo_path);
        $leadershipStructure->loadMissing('roles');
        $leadershipStructure->roles->each(function (LeadershipStructureRole $role) {
            $this->deleteImage($role->photo_path);
        });

        $leadershipStructure->delete();

        return redirect()
            ->route('leadership-structures.index')
            ->with('success', 'Struktur kepemimpinan berhasil dihapus.');
    }

    public function toggle(LeadershipStructure $leadershipStructure): RedirectResponse
    {
        $newStatus = !$leadershipStructure->is_active;
        $leadershipStructure->update(['is_active' => $newStatus]);

        if ($newStatus) {
            LeadershipStructure::whereKeyNot($leadershipStructure->getKey())->update(['is_active' => false]);
        }

        return redirect()
            ->route('leadership-structures.index')
            ->with('success', 'Status periode berhasil diperbarui.');
    }

    /**
     * Prepare payload with validated fields + uploaded photos.
     */
    private function buildPayload(LeadershipStructureRequest $request, ?LeadershipStructure $structure = null): array
    {
        $data = $request->validated();
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        unset($data['roles']);

        if ($request->hasFile('general_leader_photo')) {
            if ($structure) {
                $this->deleteImage($structure->general_leader_photo_path);
            }
            $data['general_leader_photo_path'] = $this->storeCompressedImage($request->file('general_leader_photo'));
        } elseif ($structure) {
            $data['general_leader_photo_path'] = $structure->general_leader_photo_path;
        }

        $generation = Generation::find($data['generation_id'] ?? null);
        if ($generation) {
            $data['period_label'] = $generation->name ?? 'Periode';
            $start = optional($generation->started_at)->format('Y') ?? '—';
            $end = optional($generation->ended_at)->format('Y') ?? 'Sekarang';
            $data['period_year'] = trim($start . ' - ' . $end);
        }

        unset($data['general_leader_photo'], $data['roles']);

        return $data;
    }

    private function syncRoles(LeadershipStructure $structure, LeadershipStructureRequest $request): void
    {
        $rolesInput = $request->input('roles', []);
        $retainedIds = [];

        foreach ($rolesInput as $index => $roleData) {
            $roleId = $roleData['role_id'] ?? null;
            $payload = [
                'title' => trim($roleData['title'] ?? ''),
                'person_name' => trim($roleData['person_name'] ?? ''),
                'display_order' => $index + 1,
            ];

            if ($payload['title'] === '' || $payload['person_name'] === '') {
                continue;
            }

            $photoField = "roles.$index.photo";
            $uploadedPhoto = $request->file($photoField);

            if ($roleId) {
                /** @var LeadershipStructureRole|null $existing */
                $existing = $structure->roles()->whereKey($roleId)->first();
                if (!$existing) {
                    continue;
                }

                if ($uploadedPhoto) {
                    $this->deleteImage($existing->photo_path);
                    $payload['photo_path'] = $this->storeCompressedImage($uploadedPhoto);
                }

                $existing->update($payload);
                $retainedIds[] = $existing->id;
                continue;
            }

            if (!$uploadedPhoto) {
                // validation already prevents this, but guard anyway
                continue;
            }

            $payload['photo_path'] = $this->storeCompressedImage($uploadedPhoto);
            $newRole = $structure->roles()->create($payload);
            $retainedIds[] = $newRole->id;
        }

        $structure->roles()
            ->whereNotIn('id', $retainedIds)
            ->get()
            ->each(function (LeadershipStructureRole $role) {
                $this->deleteImage($role->photo_path);
                $role->delete();
            });
    }

    private function syncActiveState(LeadershipStructure $structure): void
    {
        if (!$structure->is_active) {
            return;
        }

        LeadershipStructure::whereKeyNot($structure->getKey())->update(['is_active' => false]);
    }

    private function storeCompressedImage(UploadedFile $file): string
    {
        $destination = public_path('foto/leadership-structures');
        if (!is_dir($destination)) {
            @mkdir($destination, 0755, true);
        }

        $targetSize = 350 * 1024; // Aim for ~350 KB
        $filename = Str::uuid() . '.jpg';
        $relativePath = 'foto/leadership-structures/' . $filename;

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

        $tempPath = tempnam(sys_get_temp_dir(), 'leadership_');
        $quality = 85;

        try {
            do {
                imagejpeg($canvas, $tempPath, $quality);
                $currentSize = filesize($tempPath) ?: 0;
                if ($currentSize <= $targetSize || $quality <= 30) {
                    break;
                }
                $quality -= 10;
            } while ($quality > 10);

            $finalPath = $destination . DIRECTORY_SEPARATOR . $filename;
            if (!@rename($tempPath, $finalPath)) {
                if (!@copy($tempPath, $finalPath)) {
                    throw new \RuntimeException('Gagal menyimpan foto.');
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
