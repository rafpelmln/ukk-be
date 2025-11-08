<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PositionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PositionRequestController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = trim((string) $request->query('query'));
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;

        $query = PositionRequest::query()
            ->with(['participant', 'position'])
            ->when($status && in_array($status, ['pending', 'approved', 'rejected']), function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($search !== '', function ($q) use ($search) {
                $q->whereHas('participant', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('position', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('updated_at', 'desc');

        $positionRequests = $query->paginate($perPage)->withQueryString();

        $stats = [
            'pending' => PositionRequest::where('status', 'pending')->count(),
            'approved' => PositionRequest::where('status', 'approved')->count(),
            'rejected' => PositionRequest::where('status', 'rejected')->count(),
            'total' => PositionRequest::count(),
        ];

        return view('position-requests.index', [
            'positionRequests' => $positionRequests,
            'stats' => $stats,
            'status' => $status,
            'search' => $search,
            'perPage' => $perPage,
        ]);
    }

    public function show(PositionRequest $positionRequest): View
    {
        return view('position-requests.show', [
            'positionRequest' => $positionRequest->load(['participant', 'position']),
        ]);
    }

    public function approve(PositionRequest $positionRequest): RedirectResponse
    {
        DB::transaction(function () use ($positionRequest) {
            $positionRequest->update(['status' => 'approved']);

            $participant = $positionRequest->participant()->lockForUpdate()->first();
            $positionId = $positionRequest->position_id;

            if ($participant && $positionId) {
                $participant->positions()->sync([$positionId]);
            }
        });

        $participantName = optional($positionRequest->participant)->name ?? 'peserta';

        return redirect()->route('position-requests.index')
            ->with('success', "Pengajuan untuk {$participantName} telah disetujui.");
    }

    public function reject(Request $request, PositionRequest $positionRequest): RedirectResponse
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $positionRequest->update([
            'status' => 'rejected',
            'notes' => $request->input('notes'),
        ]);

        return redirect()->route('position-requests.index')
            ->with('success', "Pengajuan untuk {$positionRequest->participant->name} telah ditolak.");
    }

    public function destroy(PositionRequest $positionRequest): RedirectResponse
    {
        $positionRequest->delete();

        return redirect()->route('position-requests.index')
            ->with('success', 'Pengajuan telah dihapus.');
    }
}
