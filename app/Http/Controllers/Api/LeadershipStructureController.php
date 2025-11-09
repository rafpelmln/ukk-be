<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeadershipStructure;
use App\Models\LeadershipStructureRole;
use Illuminate\Http\JsonResponse;

class LeadershipStructureController extends Controller
{
    public function index(): JsonResponse
    {
        $current = LeadershipStructure::query()
            ->with('roles')
            ->where('is_active', true)
            ->orderByDesc('updated_at')
            ->first();

        $previous = LeadershipStructure::query()
            ->where('is_active', false)
            ->orderByDesc('period_year')
            ->get()
            ->map(fn (LeadershipStructure $structure) => $this->formatPrevious($structure))
            ->values();

        return response()->json([
            'current' => $current ? $this->formatActive($current) : null,
            'previous' => $previous,
        ]);
    }

    private function formatActive(LeadershipStructure $structure): array
    {
        return [
            'id' => $structure->id,
            'period_label' => $structure->period_label,
            'period_year' => $structure->period_year,
            'ketua_umum' => $this->leaderPayload(
                $structure->general_leader_name,
                $structure->general_leader_photo_path
            ),
            'roles' => $structure->roles
                ->map(fn (LeadershipStructureRole $role) => [
                    'id' => $role->id,
                    'title' => $role->title,
                    'person_name' => $role->person_name,
                    'photo_url' => $role->photo_path ? asset($role->photo_path) : null,
                ])
                ->values(),
        ];
    }

    private function formatPrevious(LeadershipStructure $structure): array
    {
        return [
            'id' => $structure->id,
            'period_label' => $structure->period_label,
            'period_year' => $structure->period_year,
            'ketua' => $this->leaderPayload(
                $structure->general_leader_name,
                $structure->general_leader_photo_path
            ),
        ];
    }

    private function leaderPayload(?string $name, ?string $photoPath): ?array
    {
        if (empty($name)) {
            return null;
        }

        return [
            'name' => $name,
            'photo_url' => $photoPath ? asset($photoPath) : null,
        ];
    }
}
