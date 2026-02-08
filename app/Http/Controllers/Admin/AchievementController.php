<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function index(Request $request)
    {
        $query = Achievement::withCount('users')
            ->orderBy('created_at', 'desc');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $achievements = $query->paginate(20);

        $stats = [
            'total' => Achievement::count(),
            'active' => Achievement::where('is_active', true)->count(),
        ];

        return view('admin.achievements.index', compact('achievements', 'stats'));
    }

    public function create()
    {
        return view('admin.achievements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'icon' => 'nullable|string',
            'points' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        Achievement::create([
            'code' => strtoupper(str_replace(' ', '_', $validated['name'])),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? 'fas fa-medal',
            'type' => $validated['category'] ?? 'custom',
            'points_reward' => $validated['points'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.achievements.index')
            ->with('success', 'تم إنشاء الإنجاز بنجاح');
    }

    public function show(Achievement $achievement)
    {
        $achievement->load(['users']);
        return view('admin.achievements.show', compact('achievement'));
    }

    public function edit(Achievement $achievement)
    {
        return view('admin.achievements.edit', compact('achievement'));
    }

    public function update(Request $request, Achievement $achievement)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'icon' => 'nullable|string',
            'points' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $achievement->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? 'fas fa-medal',
            'type' => $validated['category'] ?? 'custom',
            'points_reward' => $validated['points'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.achievements.index')
            ->with('success', 'تم تحديث الإنجاز بنجاح');
    }

    public function destroy(Achievement $achievement)
    {
        $achievement->delete();
        return redirect()->route('admin.achievements.index')
            ->with('success', 'تم حذف الإنجاز بنجاح');
    }
}
