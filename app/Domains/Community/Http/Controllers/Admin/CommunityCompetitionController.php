<?php

namespace App\Domains\Community\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\Community\Models\CommunityCompetition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CommunityCompetitionController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super_admin');
    }

    public function index(): View
    {
        $competitions = CommunityCompetition::ordered()->paginate(15);
        return view('community::admin.community.competitions.index', compact('competitions'));
    }

    public function create(): View
    {
        return view('community::admin.community.competitions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date',
            'rules' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
        $validated['is_active'] = $request->boolean('is_active');
        CommunityCompetition::create($validated);
        return redirect()->route('admin.community.competitions.index')->with('success', 'تم إنشاء المسابقة بنجاح.');
    }

    public function edit(CommunityCompetition $competition): View
    {
        return view('community::admin.community.competitions.edit', compact('competition'));
    }

    public function update(Request $request, CommunityCompetition $competition): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date',
            'rules' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $competition->update($validated);
        return redirect()->route('admin.community.competitions.index')->with('success', 'تم تحديث المسابقة بنجاح.');
    }

    public function destroy(CommunityCompetition $competition): RedirectResponse
    {
        $competition->delete();
        return redirect()->route('admin.community.competitions.index')->with('success', 'تم حذف المسابقة.');
    }
}
