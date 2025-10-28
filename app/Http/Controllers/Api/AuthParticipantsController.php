<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\Generation;
use App\Models\Position;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthParticipantsController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('participants', 'username')],
            'email' => ['required', 'email', 'max:255', Rule::unique('participants', 'email')],
            'no_hp' => ['required', 'string', 'max:30'],
            'birthday' => ['nullable', 'date'],
            'from_school' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $participant = DB::transaction(function () use ($validated) {
            // Get active generation
            $activeGeneration = Generation::where('is_active', true)->first();
            if (!$activeGeneration) {
                throw ValidationException::withMessages([
                    'generation' => ['Tidak ada generasi yang aktif saat ini.'],
                ]);
            }

            // Get guest position
            $guestPosition = Position::where('slug', 'guest')->where('is_active', true)->first();
            if (!$guestPosition) {
                throw ValidationException::withMessages([
                    'position' => ['Position guest tidak ditemukan.'],
                ]);
            }

            // Prepare participant data
            $validated['password'] = Hash::make($validated['password']);
            $validated['generations_id'] = $activeGeneration->id;

            // Create participant
            $participant = Participant::create($validated);

            // Assign guest position to participant with UUID for pivot table
            $participant->positions()->attach($guestPosition->id, [
                'id' => (string) \Illuminate\Support\Str::uuid(),
            ]);

            return $participant;
        });

        return response()->json([
            'message' => 'Pendaftaran peserta berhasil.',
            'data' => [
                'id' => $participant->id,
                'name' => $participant->name,
                'username' => $participant->username,
                'email' => $participant->email,
                'no_hp' => $participant->no_hp,
                'birthday' => optional($participant->birthday)->toDateString(),
                'from_school' => $participant->from_school,
                'photo' => $participant->photo,
                'generations_id' => $participant->generations_id,
                'generation' => $participant->generation ? [
                    'id' => $participant->generation->id,
                    'name' => $participant->generation->name,
                    'singkatan' => $participant->generation->singkatan,
                ] : null,
                'positions' => $participant->positions->map(function ($position) {
                    return [
                        'id' => $position->id,
                        'name' => $position->name,
                    ];
                }),
            ],
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $participant = Participant::where('email', $validated['email'])->first();

        if (!$participant || !Hash::check($validated['password'], $participant->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password tidak valid.'],
            ]);
        }

        // Create session token (simple approach)
        $token = bin2hex(random_bytes(32));

        // You can store token in database or use Laravel Sanctum for better token management
        // For now, we'll just return user data

        // Load relationships
        $participant->load(['generation', 'positions']);

        return response()->json([
            'message' => 'Login berhasil.',
            'data' => [
                'user' => [
                    'id' => $participant->id,
                    'name' => $participant->name,
                    'username' => $participant->username,
                    'email' => $participant->email,
                    'no_hp' => $participant->no_hp,
                    'birthday' => optional($participant->birthday)->toDateString(),
                    'from_school' => $participant->from_school,
                    'photo' => $participant->photo,
                    'generations_id' => $participant->generations_id,
                    'generation' => $participant->generation ? [
                        'id' => $participant->generation->id,
                        'name' => $participant->generation->name,
                        'singkatan' => $participant->generation->singkatan,
                    ] : null,
                    'positions' => $participant->positions->map(function ($position) {
                        return [
                            'id' => $position->id,
                            'name' => $position->name,
                        ];
                    }),
                ],
                'token' => $token,
            ],
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Logout berhasil.',
        ], 200);
    }

    public function me(Request $request): JsonResponse
    {
        // Try to resolve participant via header X-Participant-Id or auth()
        $participantId = $request->header('X-Participant-Id') ?? null;
        $participant = null;

        if ($participantId) {
            $participant = Participant::with(['generation', 'positions'])->find($participantId);
        } elseif (auth()->check()) {
            $user = auth()->user();
            $participant = Participant::with(['generation', 'positions'])->find($user->id);
        }

        return response()->json([
            'message' => 'User profile retrieved successfully.',
            'data' => [
                'user' => $participant ? [
                    'id' => $participant->id,
                    'name' => $participant->name,
                    'username' => $participant->username,
                    'email' => $participant->email,
                    'no_hp' => $participant->no_hp,
                    'birthday' => optional($participant->birthday)->toDateString(),
                    'from_school' => $participant->from_school,
                    'photo' => $participant->photo,
                    'generations_id' => $participant->generations_id,
                    'generation' => $participant->generation ? [
                        'id' => $participant->generation->id,
                        'name' => $participant->generation->name,
                        'singkatan' => $participant->generation->singkatan,
                    ] : null,
                    'positions' => $participant->positions->map(function ($position) {
                        return [
                            'id' => $position->id,
                            'slug' => $position->slug ?? null,
                            'name' => $position->name,
                        ];
                    }),
                ] : null,
            ],
        ], 200);
    }
}
