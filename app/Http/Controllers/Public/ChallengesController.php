<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CommunityCompetition;
use Illuminate\View\View;

class ChallengesController extends Controller
{
    public function index(): View
    {
        $competitions = CommunityCompetition::query()
            ->active()
            ->ordered()
            ->paginate(12);

        return view('public.challenges', compact('competitions'));
    }
}

