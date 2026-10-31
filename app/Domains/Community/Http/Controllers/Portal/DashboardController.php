<?php

namespace App\Domains\Community\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Domains\Community\Models\CommunityCompetition;
use App\Domains\Community\Models\CommunityDataset;
use App\Domains\Community\Models\CommunityModel;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

/**
 * لوحة تحكم مجتمع البيانات والذكاء الاصطناعي — كروت وإحصائيات.
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $stats = [
            'competitions_count' => CommunityCompetition::active()->count(),
            'datasets_count' => CommunityDataset::public()->count(),
            'models_count' => CommunityModel::approved()->where('is_active', true)->count(),
        ];

        $recentCompetitions = CommunityCompetition::active()->ordered()->take(3)->get();
        $recentDatasets = CommunityDataset::public()->ordered()->take(4)->get();
        $recentModels = CommunityModel::approved()->where('is_active', true)->ordered()->take(4)->get();

        return view('community::portal.dashboard.index', [
            'user' => $user,
            'stats' => $stats,
            'recentCompetitions' => $recentCompetitions,
            'recentDatasets' => $recentDatasets,
            'recentModels' => $recentModels,
        ]);
    }
}
