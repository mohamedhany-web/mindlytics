<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ModeratorMarketingPlan;
use App\Models\User;
use Illuminate\Http\Request;

class ModeratorMarketingPlanController extends Controller
{
    public function index(Request $request)
    {
        $query = ModeratorMarketingPlan::query()
            ->with(['moderator'])
            ->withCount(['platforms', 'calendarEvents']);

        if ($request->filled('moderator_id')) {
            $query->where('moderator_id', $request->moderator_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $q = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $request->q) . '%';
            $query->where('title', 'like', $q);
        }

        $plans = $query->latest()->paginate(25)->withQueryString();

        $moderators = User::query()
            ->moderatorEmployees()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.moderator-marketing-plans.index', compact('plans', 'moderators'));
    }

    public function show(ModeratorMarketingPlan $plan)
    {
        $plan->load([
            'moderator',
            'platforms',
            'calendarEvents' => fn ($q) => $q->with('platform')->orderBy('starts_at'),
            'designTaskCycle',
        ]);

        return view('admin.moderator-marketing-plans.show', ['plan' => $plan]);
    }
}
