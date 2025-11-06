<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeBanner;
use Illuminate\Http\JsonResponse;

class HomeBannerController extends Controller
{
    public function index(): JsonResponse
    {
        $banners = HomeBanner::query()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (HomeBanner $banner) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'subtitle' => $banner->subtitle,
                    'description' => $banner->description,
                    'button_label' => $banner->button_label,
                    'button_url' => $banner->button_url,
                    'image_url' => $banner->image_path ? asset($banner->image_path) : null,
                    'display_order' => $banner->display_order,
                    'created_at' => $banner->created_at,
                    'updated_at' => $banner->updated_at,
                ];
            });

        return response()->json([
            'data' => $banners,
        ]);
    }
}
