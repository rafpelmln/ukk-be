<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VisionMissionBatchRequest;
use App\Http\Requests\VisionMissionEntryRequest;
use App\Models\VisionMissionEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VisionMissionEntryController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->query('type', 'vision');
        if (!in_array($type, ['vision', 'mission'], true)) {
            $type = 'vision';
        }

        $perPage = (int) $request->query('per_page', 10);
        $allowed = [5, 10, 25, 50];
        if (!in_array($perPage, $allowed, true)) {
            $perPage = 10;
        }

        $entries = VisionMissionEntry::query()
            ->where('type', $type)
            ->orderBy('created_at')
            ->paginate($perPage)
            ->withQueryString();

        $counts = [
            'vision' => VisionMissionEntry::where('type', 'vision')->count(),
            'mission' => VisionMissionEntry::where('type', 'mission')->count(),
        ];

        return view('vision-mission.index', compact('entries', 'type', 'perPage', 'counts'));
    }

    public function create(): View
    {
        $vision = VisionMissionEntry::where('type', 'vision')->latest()->first();
        $missions = VisionMissionEntry::where('type', 'mission')->orderBy('created_at')->get();

        return view('vision-mission.create', compact('vision', 'missions'));
    }

    public function store(VisionMissionBatchRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            VisionMissionEntry::whereIn('type', ['vision', 'mission'])->delete();

            VisionMissionEntry::create([
                'type' => 'vision',
                'title' => $data['vision_title'],
                'content' => $data['vision_content'],
                'is_active' => $data['vision_is_active'] ?? true,
            ]);

            foreach ($data['missions'] as $mission) {
                VisionMissionEntry::create([
                    'type' => 'mission',
                    'title' => $mission['title'] ?? 'Misi',
                    'content' => $mission['content'],
                    'is_active' => true,
                ]);
            }
        });

        return redirect()
            ->route('vision-mission.index')
            ->with('success', 'Konten visi & misi berhasil diperbarui.');
    }

    public function edit(VisionMissionEntry $visionMission): View
    {
        return view('vision-mission.edit', [
            'entry' => $visionMission,
        ]);
    }

    public function update(VisionMissionEntryRequest $request, VisionMissionEntry $visionMission): RedirectResponse
    {
        $data = $this->validatedData($request);

        $visionMission->update($data);

        return redirect()
            ->route('vision-mission.index', ['type' => $visionMission->type])
            ->with('success', 'Data visi & misi berhasil diperbarui.');
    }

    public function destroy(VisionMissionEntry $visionMission): RedirectResponse
    {
        $type = $visionMission->type;
        $visionMission->delete();

        return redirect()
            ->route('vision-mission.index', ['type' => $type])
            ->with('success', 'Data visi & misi berhasil dihapus.');
    }

    public function toggle(VisionMissionEntry $visionMission): RedirectResponse
    {
        $visionMission->update(['is_active' => !$visionMission->is_active]);

        return redirect()
            ->route('vision-mission.index', ['type' => $visionMission->type])
            ->with('success', 'Status entri berhasil diperbarui.');
    }

    private function validatedData(VisionMissionEntryRequest $request): array
    {
        $data = $request->validated();

        return [
            'type' => $data['type'],
            'title' => $data['title'],
            'content' => $data['content'],
            'is_active' => $data['is_active'] ?? false,
        ];
    }
}
