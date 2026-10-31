<?php

namespace App\Domains\Community\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Domains\Community\Models\CommunityCompetition;
use App\Domains\Community\Models\CommunityDataset;
use App\Domains\Community\Models\CommunityModel;
use App\Domains\Community\Models\ContributorProfile;

/**
 * مجتمع البيانات والذكاء الاصطناعي
 * (مسابقات، مجموعات بيانات، مناقشات - مستوحى من Kaggle)
 */
class CommunityController extends Controller
{
    /**
     * صفحة المجتمع الرئيسية — بوابة Educo للاستكشاف والانضمام.
     */
    public function index()
    {
        $stats = [
            'datasets' => CommunityDataset::public()->count(),
            'models' => CommunityModel::public()->count(),
            'contributors' => ContributorProfile::approved()->count(),
            'competitions' => CommunityCompetition::active()->count(),
        ];

        $datasets = CommunityDataset::public()
            ->with('creator:id,name,profile_image,profile_image_disk')
            ->ordered()
            ->take(6)
            ->get();

        $models = CommunityModel::public()
            ->with(['creator:id,name,profile_image,profile_image_disk', 'dataset:id,title'])
            ->ordered()
            ->take(4)
            ->get();

        $contributors = ContributorProfile::approved()
            ->with('user:id,name,profile_image,profile_image_disk')
            ->orderByDesc('reviewed_at')
            ->take(6)
            ->get();

        return view('community::public.community.index', compact(
            'stats',
            'datasets',
            'models',
            'contributors'
        ));
    }
}
