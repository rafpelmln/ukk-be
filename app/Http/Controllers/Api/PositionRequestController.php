<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Participant;
use App\Models\Position;
use App\Models\ParticipantPositionRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PositionRequestController extends Controller
{
    /**
     * List position requests for the current participant
     */
    public function index(Request $request): JsonResponse
    {
        $participant = $this->resolveParticipant($request);
        if (!$participant) {
            return response()->json(['message' => 'Participant not found.'], 404);
        }

        $requests = $participant->positionRequests()->with('position')->orderBy('created_at', 'desc')->get()->map(function ($r) {
            return [
                'id' => $r->id,
                'position' => $r->position ? [
                    'id' => $r->position->id,
                    'slug' => $r->position->slug ?? null,
                    'name' => $r->position->name ?? null,
                ] : null,
                'status' => $r->status,
                'notes' => $r->notes ?? null,
                'admin_notes' => $r->admin_notes ?? null,
                'created_at' => $r->created_at,
                'updated_at' => $r->updated_at,
            ];
        });

        return response()->json(['message' => 'Requests retrieved', 'data' => ['requests' => $requests]], 200);
    }

    /**
     * Create a new position request
     */
    public function store(Request $request): JsonResponse
    {
        $participant = $this->resolveParticipant($request);
        if (!$participant) {
            return response()->json(['message' => 'Participant not found.'], 404);
        }

        $validated = $request->validate([
            'position_slug' => ['required', 'string'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ]);

        $position = Position::where('slug', $validated['position_slug'])->where('is_active', true)->first();
        if (!$position) {
            throw ValidationException::withMessages(['position' => ['Posisi tidak ditemukan atau tidak aktif.']]);
        }

        // Check if participant already has the position
        $hasPosition = $participant->positions()->where('position_id', $position->id)->exists();
        if ($hasPosition) {
            throw ValidationException::withMessages(['position' => ['Anda sudah memiliki posisi ini.']]);
        }

        // Check for existing pending request for same position
        $existing = ParticipantPositionRequest::where('participants_id', $participant->id)
            ->where('position_id', $position->id)
            ->where('status', 'pending')
            ->first();
        if ($existing) {
            throw ValidationException::withMessages(['request' => ['Anda sudah memiliki request yang menunggu untuk posisi ini.']]);
        }

        $req = ParticipantPositionRequest::create([
            'id' => (string) Str::uuid(),
            'participants_id' => $participant->id,
            'position_id' => $position->id,
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        $req->load('position');

        return response()->json(['message' => 'Request created', 'data' => ['request' => $req]], 201);
    }

    /**
     * Cancel (delete) a pending request
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $participant = $this->resolveParticipant($request);
        if (!$participant) {
            return response()->json(['message' => 'Participant not found.'], 404);
        }

        $req = ParticipantPositionRequest::where('id', $id)->where('participants_id', $participant->id)->first();
        if (!$req) {
            return response()->json(['message' => 'Request not found.'], 404);
        }

        if ($req->status !== 'pending') {
            return response()->json(['message' => 'Hanya request pending yang dapat dibatalkan.'], 422);
        }

        $req->delete();

        return response()->json(['message' => 'Request dibatalkan.'], 200);
    }

    /**
     * Helper to resolve participant from header or auth
     */
    protected function resolveParticipant(Request $request): ?Participant
    {
        $participantId = $request->header('X-Participant-Id') ?? $request->input('participant_id');

        if ($participantId) {
            return Participant::find($participantId);
        }

        // Fallback to auth user if available
        if (auth()->check()) {
            $user = auth()->user();
            return Participant::find($user->id);
        }

        return null;
    }
}
