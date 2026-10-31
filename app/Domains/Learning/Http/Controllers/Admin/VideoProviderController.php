<?php

namespace App\Domains\Learning\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\Learning\Models\VideoProvider;
use Illuminate\Http\Request;

class VideoProviderController extends Controller
{
    public function index()
    {
        $providers = VideoProvider::orderBy('name')->get();

        return view('learning::admin.video-providers.index', compact('providers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:video_providers,slug',
            'platform' => 'required|string|max:50',
            'is_active' => 'nullable|boolean',
            'library_id' => 'nullable|string|max:255',
            'cdn_hostname' => 'nullable|string|max:255',
            'api_key' => 'nullable|string|max:255',
            'token_auth_key' => 'nullable|string|max:255',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        VideoProvider::create($data);

        return redirect()->route('admin.video-providers.index')
            ->with('success', 'تم إنشاء مصدر الفيديو بنجاح.');
    }

    public function update(Request $request, VideoProvider $videoProvider)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:video_providers,slug,' . $videoProvider->id,
            'platform' => 'required|string|max:50',
            'is_active' => 'nullable|boolean',
            'library_id' => 'nullable|string|max:255',
            'cdn_hostname' => 'nullable|string|max:255',
            'api_key' => 'nullable|string|max:255',
            'token_auth_key' => 'nullable|string|max:255',
        ]);

        $data['is_active'] = $request->boolean('is_active', false);

        $videoProvider->update($data);

        return redirect()->route('admin.video-providers.index')
            ->with('success', 'تم تحديث مصدر الفيديو بنجاح.');
    }
}

