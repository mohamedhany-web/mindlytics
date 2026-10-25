<?php

namespace App\Domains\Community\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Domains\Community\Models\UserAchievement;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function index()
    {
        $achievements = UserAchievement::where('user_id', auth()->id())
            ->with(['achievement'])
            ->orderBy('earned_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total' => $achievements->total(),
            'total_points' => UserAchievement::where('user_id', auth()->id())
                ->sum('points_earned') ?? 0,
        ];

        return view('community::student.achievements.index', compact('achievements', 'stats'));
    }

    public function show($id)
    {
        $achievement = UserAchievement::where('user_id', auth()->id())
            ->where('id', $id)
            ->with(['achievement'])
            ->firstOrFail();

        return view('community::student.achievements.show', compact('achievement'));
    }
}
