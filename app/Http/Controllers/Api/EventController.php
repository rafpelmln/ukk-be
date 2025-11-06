<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $limit = (int) $request->query('limit', 0);

        $query = Event::query()
            ->orderBy('created_at', 'desc')
            ->orderBy('event_date', 'desc');

        if ($limit > 0) {
            $events = $query->limit($limit)->get();
        } else {
            $events = $query->get();
        }

        return response()->json([
            'data' => $events,
        ]);
    }

    public function show(string $id)
    {
        $event = Event::where('id', $id)->first();

        if (!$event) {
            return response()->json([
                'message' => 'Event tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'data' => $event,
        ]);
    }
}
