<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DesignTaskCycle;
use App\Models\User;
use App\Services\EmployeeMonthlyPerformanceExcelExport;
use App\Services\EmployeeMonthlyPerformanceReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DesignTaskCycleController extends Controller
{
    public function index(Request $request)
    {
        $query = DesignTaskCycle::query()
            ->with(['moderator', 'designer', 'designerTask', 'moderatorDeliveryTask']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('moderator_id')) {
            $query->where('moderator_id', $request->moderator_id);
        }
        if ($request->filled('designer_employee_id')) {
            $query->where('designer_employee_id', $request->designer_employee_id);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', '%'.$s.'%')
                    ->orWhere('id', $s)
                    ->orWhereHas('moderator', fn ($q) => $q->where('name', 'like', '%'.$s.'%'))
                    ->orWhereHas('designer', fn ($q) => $q->where('name', 'like', '%'.$s.'%'));
            });
        }

        $cycles = $query->latest()->paginate(25)->withQueryString();

        $moderators = User::moderatorEmployees()->where('is_active', true)->orderBy('name')->get();
        $designers = User::designerEmployees()->where('is_active', true)->orderBy('name')->get();

        $stats = [
            'total' => DesignTaskCycle::count(),
            'pending' => DesignTaskCycle::whereIn('status', [
                DesignTaskCycle::STATUS_PENDING_DESIGN,
                DesignTaskCycle::STATUS_DESIGN_IN_PROGRESS,
            ])->count(),
            'awaiting_moderator' => DesignTaskCycle::where('status', DesignTaskCycle::STATUS_DESIGN_SUBMITTED)->count(),
            'in_delivery' => DesignTaskCycle::where('status', DesignTaskCycle::STATUS_MODERATOR_DELIVERY_PENDING)->count(),
            'completed' => DesignTaskCycle::where('status', DesignTaskCycle::STATUS_COMPLETED)->count(),
        ];

        return view('admin.design-task-cycles.index', compact('cycles', 'moderators', 'designers', 'stats'));
    }

    public function show(DesignTaskCycle $designTaskCycle)
    {
        $designTaskCycle->load([
            'moderator.employeeJob',
            'designer.employeeJob',
            'designerTask.deliverables',
            'moderatorDeliveryTask.deliverables',
        ]);

        return view('admin.design-task-cycles.show', compact('designTaskCycle'));
    }

    public function updateNotes(Request $request, DesignTaskCycle $designTaskCycle)
    {
        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:10000'],
        ]);

        $designTaskCycle->update(['admin_notes' => $validated['admin_notes'] ?? null]);

        return back()->with('success', 'تم حفظ ملاحظات الإدارة.');
    }

    public function cancel(Request $request, DesignTaskCycle $designTaskCycle)
    {
        if (in_array($designTaskCycle->status, [DesignTaskCycle::STATUS_COMPLETED, DesignTaskCycle::STATUS_CANCELLED], true)) {
            return back()->withErrors(['error' => 'الدورة منتهية أو ملغاة.']);
        }

        $designTaskCycle->update(['status' => DesignTaskCycle::STATUS_CANCELLED]);

        return back()->with('success', 'تم إلغاء الدورة من قبل الإدارة.');
    }

    /**
     * تقرير أداء شهري تفصيلي للموظفين (مهام + دورات التصميم).
     */
    public function performanceReport(Request $request, EmployeeMonthlyPerformanceReportService $reportService)
    {
        [$start, $end, $year, $month] = $this->performanceReportPeriod($request);

        $report = $reportService->analyze($start, $end);

        return view('admin.design-task-cycles.performance-report', [
            'rows' => $report['rows'],
            'summary' => $report['summary'],
            'start' => $report['start'],
            'end' => $report['end'],
            'year' => $year,
            'month' => $month,
        ]);
    }

    /**
     * تصدير تقرير الأداء الشهري إلى Excel (أوراق متعددة، تنسيق جاهز للطباعة).
     */
    public function performanceReportExcel(Request $request, EmployeeMonthlyPerformanceReportService $reportService, EmployeeMonthlyPerformanceExcelExport $excelExport)
    {
        [$start, $end] = $this->performanceReportPeriod($request);

        $report = $reportService->analyze($start, $end);

        return $excelExport->streamResponse($report);
    }

    /**
     * @return array{0: \Carbon\Carbon, 1: \Carbon\Carbon, 2: int, 3: int}
     */
    private function performanceReportPeriod(Request $request): array
    {
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);
        $year = max(2000, min(2100, $year));
        $month = max(1, min(12, $month));

        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        return [$start, $end, $year, $month];
    }
}
