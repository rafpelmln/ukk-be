<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VisionMissionEntry;
use Illuminate\Http\JsonResponse;

class VisionMissionController extends Controller
{
    public function index(): JsonResponse
    {
        $vision = VisionMissionEntry::query()
            ->active()
            ->type('vision')
            ->orderBy('created_at')
            ->get()
            ->map(fn (VisionMissionEntry $entry) => $this->formatEntry($entry));

        $mission = VisionMissionEntry::query()
            ->active()
            ->type('mission')
            ->orderBy('created_at')
            ->get()
            ->map(fn (VisionMissionEntry $entry) => $this->formatEntry($entry));

        return response()->json([
            'vision' => $vision,
            'mission' => $mission,
        ]);
    }

    private function formatEntry(VisionMissionEntry $entry): array
    {
        return [
            'id' => $entry->id,
            'title' => $entry->title,
            'content' => $entry->content,
            'type' => $entry->type,
        ];
    }
}
