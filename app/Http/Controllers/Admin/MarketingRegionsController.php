<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MarketingRegionsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketingRegionsController extends Controller
{
    public function index(Request $request, MarketingRegionsService $regions): View
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $metric = (string) $request->query('metric', 'combined');

        $data = $regions->dashboard(
            is_string($from) ? $from : null,
            is_string($to) ? $to : null,
            $metric
        );

        return view('admin.marketing.regions.index', [
            'data' => $data,
        ]);
    }
}
