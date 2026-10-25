<?php

namespace App\Domains\Community\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Domains\Community\Models\CommunityCompetition;
use Illuminate\View\View;

class ChallengesController extends Controller
{
    public function index(): View
    {
        $competitions = CommunityCompetition::query()
            ->active()
            ->ordered()
            ->paginate(12);

        return view('community::public.challenges', compact('competitions'));
    }
}

