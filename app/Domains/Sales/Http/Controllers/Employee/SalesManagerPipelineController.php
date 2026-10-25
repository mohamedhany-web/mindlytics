<?php

namespace App\Domains\Sales\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Domains\Sales\Services\SalesPipelineAnalyticsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesManagerPipelineController extends Controller
{
    public function __construct()
    {
        $this->middleware('sales.manager');
    }

    public function index(SalesPipelineAnalyticsService $analytics): View
    {
        $data = $analytics->boardForManager(Auth::user());

        return view('sales::manager.pipeline', $data);
    }
}
