<?php

namespace App\Domains\Community\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\Community\Services\JourneyAnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JourneyAnalyticsController extends Controller
{
    public function __construct(private JourneyAnalyticsService $analytics)
    {
    }

    public function index(Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->get('from'))->startOfDay()
            : now()->subDays(29)->startOfDay();
        $to = $request->filled('to')
            ? Carbon::parse($request->get('to'))->endOfDay()
            : now()->endOfDay();

        $data = $this->analytics->dashboard($from, $to);

        return view('community::admin.portfolio.journey-analytics', $data);
    }
}
