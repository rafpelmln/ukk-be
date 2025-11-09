<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityReport;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;

class ActivityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $participant = $this->resolveParticipant($request);

        $query = Activity::with('positions')
            ->where('status', '!=', 'cancelled')
            ->orderBy('datetime');

        if ($participant) {
            $query->visibleForParticipant($participant);
        }

        if ($request->boolean('upcoming', true)) {
            $query->where('datetime', '>=', Carbon::now()->subDays(7));
        }

        $activities = $query->get()->map(function (Activity $activity) use ($participant) {
            return $this->transformActivity($activity, $participant);
        });

        return response()->json([
            'success' => true,
            'data' => $activities,
        ]);
    }

    public function show(Request $request, Activity $activity): JsonResponse
    {
        $participant = $this->resolveParticipant($request);

        if ($participant && !$this->participantCanView($activity, $participant)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke kegiatan ini.',
            ], 403);
        }

        $activity->load('positions');

        return response()->json([
            'success' => true,
            'data' => $this->transformActivity($activity, $participant, true),
        ]);
    }

    public function checkIn(Request $request, Activity $activity): JsonResponse
    {
        $participant = $this->resolveParticipant($request, true);

        if (!$this->participantCanView($activity, $participant)) {
            return response()->json([
                'success' => false,
                'message' => 'Kegiatan ini tidak ditujukan untuk Anda.',
            ], 403);
        }

        if ($activity->status !== 'scheduled') {
            return response()->json([
                'success' => false,
                'message' => 'Kegiatan ini sudah tidak menerima presensi.',
            ], 422);
        }

        $validated = $request->validate([
            'status' => 'nullable|in:present,excused,absent',
            'notes' => 'nullable|string|max:500',
        ]);

        $report = ActivityReport::updateOrCreate(
            [
                'activity_id' => $activity->id,
                'participant_id' => $participant->id,
            ],
            [
                'status' => $validated['status'] ?? 'present',
                'notes' => $validated['notes'] ?? null,
                'checked_in_at' => now(),
            ],
        );

        return response()->json([
            'success' => true,
            'message' => 'Presensi berhasil direkam.',
            'data' => [
                'id' => $report->id,
                'status' => $report->status,
                'checked_in_at' => optional($report->checked_in_at)->toISOString(),
                'notes' => $report->notes,
            ],
        ], 201);
    }

    private function transformActivity(Activity $activity, ?Participant $participant = null, bool $includeReports = false): array
    {
        $report = null;
        if ($participant) {
            $report = $activity->reports()->where('participant_id', $participant->id)->first();
        }

        return [
            'id' => $activity->id,
            'slug' => $activity->slug,
            'title' => $activity->name,
            'description' => $activity->desc,
            'location' => $activity->location,
            'scheduled_at' => optional($activity->datetime)->toISOString(),
            'status' => $activity->status,
            'target_scope' => $activity->target_scope,
            'positions' => $activity->positions->map(fn($position) => [
                'id' => $position->id,
                'name' => $position->name,
            ]),
            'report' => $report ? [
                'id' => $report->id,
                'status' => $report->status,
                'checked_in_at' => optional($report->checked_in_at)->toISOString(),
                'notes' => $report->notes,
            ] : null,
            'reports' => $includeReports ? $activity->reports()->with('participant')->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'status' => $item->status,
                    'checked_in_at' => optional($item->checked_in_at)->toISOString(),
                    'notes' => $item->notes,
                    'participant' => [
                        'id' => $item->participant->id ?? null,
                        'name' => $item->participant->name ?? null,
                        'email' => $item->participant->email ?? null,
                    ],
                ];
            }) : null,
        ];
    }

    private function resolveParticipant(Request $request, bool $required = false): ?Participant
    {
        $participantId = $request->header('X-Participant-Id')
            ?? $request->query('participant_id');

        if (!$participantId) {
            if ($required) {
                throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => 'Participant ID diperlukan.',
                ], 400));
            }

            return null;
        }

        $participant = Participant::with('positions')->find($participantId);

        if (!$participant && $required) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Peserta tidak ditemukan.',
            ], 404));
        }

        return $participant;
    }

    private function participantCanView(Activity $activity, Participant $participant): bool
    {
        if ($activity->target_scope === 'all') {
            return true;
        }

        $participantPositionIds = $participant->positions->pluck('id')->all();
        if (empty($participantPositionIds)) {
            return false;
        }

        return $activity->positions()->whereIn('positions.id', $participantPositionIds)->exists();
    }
}
