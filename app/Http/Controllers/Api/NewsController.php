<?php

namespace App\Http\Controllers\Api;
use App\Models\News;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager-load tags and return computed attributes (photo_url) for API clients
        $news = News::with([
            'category:id,name,slug,color',
            'tags:id,name,slug',
        ])->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $news,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Try to find by id (uuid) or slug
        $newsItem = News::with([
                'category:id,name,slug,color',
                'tags:id,name,slug',
            ])
            ->where('id', $id)
            ->orWhere('slug', $id)
            ->first();

        if (!$newsItem) {
            return response()->json(['message' => 'News item not found'], 404);
        }

        return response()->json(['data' => $newsItem]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
