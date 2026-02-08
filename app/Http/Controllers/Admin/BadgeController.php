<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    public function index(Request $request)
    {
        $query = Badge::withCount('users')
            ->orderBy('created_at', 'desc');

        if ($request->filled('category')) {
            $query->where(function($q) use ($request) {
                $q->where('category', $request->category)
                  ->orWhere('type', $request->category);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $badges = $query->paginate(20);

        $stats = [
            'total' => Badge::count(),
            'active' => Badge::where('is_active', true)->count(),
        ];

        return view('admin.badges.index', compact('badges', 'stats'));
    }

    public function create()
    {
        return view('admin.badges.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Badge::create([
            'code' => strtoupper(str_replace(' ', '_', $validated['name'])),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? 'fas fa-award',
            'type' => $validated['category'] ?? 'skill',
            'color' => $validated['color'] ?? '#3B82F6',
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.badges.index')
            ->with('success', 'تم إنشاء الشارة بنجاح');
    }

    public function show(Badge $badge)
    {
        $badge->load(['users']);
        return view('admin.badges.show', compact('badge'));
    }

    public function edit(Badge $badge)
    {
        return view('admin.badges.edit', compact('badge'));
    }

    public function update(Request $request, Badge $badge)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $badge->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? 'fas fa-award',
            'type' => $validated['category'] ?? 'skill',
            'color' => $validated['color'] ?? '#3B82F6',
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.badges.index')
            ->with('success', 'تم تحديث الشارة بنجاح');
    }

    public function destroy(Badge $badge)
    {
        $badge->delete();
        return redirect()->route('admin.badges.index')
            ->with('success', 'تم حذف الشارة بنجاح');
    }
}
