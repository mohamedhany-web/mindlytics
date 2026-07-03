<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsAppReportController extends Controller
{
    public function __construct(private WhatsAppReportService $reports) {}

    public function index(Request $request): View
    {
        $from = $request->date('from') ?? now()->subDays(30);
        $to = $request->date('to') ?? now();

        $report = $this->reports->dashboard($from->copy()->startOfDay(), $to->copy()->endOfDay());

        return view('admin.whatsapp.reports.index', compact('report', 'from', 'to'));
    }
}
