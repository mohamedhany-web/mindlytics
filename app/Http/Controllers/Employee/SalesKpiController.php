<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Services\SalesKpiService;
use Illuminate\Support\Facades\Auth;

class SalesKpiController extends Controller
{
    public function index(SalesKpiService $kpi)
    {
        $user = Auth::user();
        $report = $kpi->buildReport($user);

        return view('employee.sales.kpi.index', compact('report'));
    }
}
