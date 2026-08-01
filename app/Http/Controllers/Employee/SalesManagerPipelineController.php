<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Services\SalesPipelineAnalyticsService;
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

        return view('employee.sales-manager.pipeline', $data);
    }
}
