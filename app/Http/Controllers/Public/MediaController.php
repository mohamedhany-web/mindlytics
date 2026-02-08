<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\MediaGallery;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $query = MediaGallery::active()->with('uploadedBy');

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $media = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $categories = MediaGallery::active()
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->values();

        $stats = [
            'images' => MediaGallery::active()->byType('image')->count(),
            'videos' => MediaGallery::active()->byType('video')->count(),
            'documents' => MediaGallery::active()->byType('document')->count(),
        ];

        return view('public.media.index', compact('media', 'categories', 'stats'));
    }

    public function show(MediaGallery $media)
    {
        if (!$media->is_active) {
            abort(404);
        }

        $media->increment('views_count');
        $media->load('uploadedBy');

        return view('public.media.show', compact('media'));
    }
}
