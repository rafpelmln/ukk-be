<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('query'));
        $status = $request->query('status');

        $activities = Activity::query()
            ->with('positions')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            })
            ->when($status && in_array($status, ['scheduled', 'completed', 'cancelled'], true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderByDesc('datetime')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'scheduled' => Activity::where('status', 'scheduled')->count(),
            'completed' => Activity::where('status', 'completed')->count(),
            'cancelled' => Activity::where('status', 'cancelled')->count(),
            'total' => Activity::count(),
        ];

        return view('activities.index', [
            'activities' => $activities,
            'search' => $search,
            'status' => $status,
            'stats' => $stats,
        ]);
    }

    public function create(): View
    {
        $activity = new Activity([
            'datetime' => now()->addDays(1),
            'target_scope' => 'all',
            'status' => 'scheduled',
        ]);
        $activity->setRelation('positions', collect());

        return view('activities.create', [
            'positions' => Position::orderBy('name')->get(),
            'activity' => $activity,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $activity = Activity::create($data);
        $this->syncPositions($activity, $request);

        return redirect()->route('activities.index')->with('success', 'Kegiatan berhasil dibuat.');
    }

    public function show(Activity $activity): View
    {
        $activity->load(['positions', 'reports.participant']);

        return view('activities.show', [
            'activity' => $activity,
            'reports' => $activity->reports->sortByDesc('checked_in_at'),
        ]);
    }

    public function edit(Activity $activity): View
    {
        $activity->load('positions');

        return view('activities.edit', [
            'activity' => $activity,
            'positions' => Position::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Activity $activity): RedirectResponse
    {
        $data = $this->validatedData($request, $activity);
        $activity->update($data);
        $this->syncPositions($activity, $request);

        return redirect()->route('activities.index')->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        $activity->delete();

        return redirect()->route('activities.index')->with('success', 'Kegiatan berhasil dihapus.');
    }

    private function validatedData(Request $request, ?Activity $activity = null): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'datetime' => 'required|date',
            'target_scope' => 'required|in:all,positions',
            'status' => 'required|in:scheduled,completed,cancelled',
            'position_ids' => 'array|required_if:target_scope,positions',
            'position_ids.*' => 'uuid|exists:positions,id',
        ], [
            'position_ids.required_if' => 'Pilih minimal satu posisi ketika target khusus dipilih.',
        ]);

        $validated['datetime'] = Carbon::parse($validated['datetime']);
        $validated['is_finished'] = $validated['status'] === 'completed';

        return $validated;
    }

    private function syncPositions(Activity $activity, Request $request): void
    {
        $positionIds = $request->input('position_ids', []);
        if ($request->input('target_scope') === 'positions') {
            $activity->positions()->sync($positionIds);
        } else {
            $activity->positions()->detach();
        }
    }
}
