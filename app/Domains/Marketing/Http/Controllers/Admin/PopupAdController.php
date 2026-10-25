<?php

namespace App\Domains\Marketing\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\Marketing\Models\PopupAd;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PopupAdController extends Controller
{
    public function index(Request $request)
    {
        $ads = PopupAd::orderBy('order')->orderByDesc('created_at')->paginate(15);
        return view('marketing::admin.popup-ads.index', compact('ads'));
    }

    public function create()
    {
        return view('marketing::admin.popup-ads.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:5000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'link_url' => 'nullable|url|max:500',
            'cta_text' => 'nullable|string|max:100',
            'starts_at' => 'required|date',
            'duration_days' => 'required|integer|min:1|max:365',
            'max_views_per_visitor' => 'required|integer|min:1|max:100',
            'is_active' => 'boolean',
        ]);

        $startsAt = Carbon::parse($validated['starts_at']);
        $endsAt = $startsAt->copy()->addDays((int) $validated['duration_days']);

        $imagePath = $request->hasFile('image') ? $request->file('image')->store('popup_ads', 'public') : null;

        PopupAd::create([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'image' => $imagePath,
            'link_url' => $validated['link_url'] ?? null,
            'cta_text' => $validated['cta_text'] ?? null,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'max_views_per_visitor' => (int) $validated['max_views_per_visitor'],
            'is_active' => $request->boolean('is_active', true),
            'order' => PopupAd::max('order') + 1,
        ]);

        return redirect()->route('admin.popup-ads.index')
            ->with('success', 'تم إنشاء الإعلان بنجاح');
    }

    public function edit(PopupAd $popupAd)
    {
        return view('marketing::admin.popup-ads.edit', compact('popupAd'));
    }

    public function update(Request $request, PopupAd $popupAd)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:5000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'link_url' => 'nullable|url|max:500',
            'cta_text' => 'nullable|string|max:100',
            'starts_at' => 'required|date',
            'duration_days' => 'required|integer|min:1|max:365',
            'max_views_per_visitor' => 'required|integer|min:1|max:100',
            'is_active' => 'boolean',
        ]);

        $startsAt = Carbon::parse($validated['starts_at']);
        $endsAt = $startsAt->copy()->addDays((int) $validated['duration_days']);

        $data = [
            'title' => $validated['title'],
            'body' => $validated['body'],
            'link_url' => $validated['link_url'] ?? null,
            'cta_text' => $validated['cta_text'] ?? null,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'max_views_per_visitor' => (int) $validated['max_views_per_visitor'],
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('image')) {
            if ($popupAd->image) {
                Storage::disk('public')->delete($popupAd->image);
            }
            $data['image'] = $request->file('image')->store('popup_ads', 'public');
        }

        $popupAd->update($data);

        return redirect()->route('admin.popup-ads.index')
            ->with('success', 'تم تحديث الإعلان بنجاح');
    }

    public function destroy(PopupAd $popupAd)
    {
        if ($popupAd->image) {
            Storage::disk('public')->delete($popupAd->image);
        }
        $popupAd->delete();
        return redirect()->route('admin.popup-ads.index')
            ->with('success', 'تم حذف الإعلان بنجاح');
    }
}
