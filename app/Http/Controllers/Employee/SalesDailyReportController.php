<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesDailyReport;
use App\Models\SalesLead;
use App\Services\SalesDailyReportService;
use App\Support\SalesDailyReportSettings;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesDailyReportController extends Controller
{
    public function index(Request $request, SalesDailyReportService $service): View
    {
        $user = Auth::user();
        $date = $request->filled('date')
            ? Carbon::parse($request->date)->startOfDay()
            : today();

        $report = SalesDailyReport::forUser($user->id)
            ->whereDate('report_date', $date)
            ->with('contacts.lead')
            ->first();

        $recent = SalesDailyReport::forUser($user->id)
            ->orderByDesc('report_date')
            ->limit(14)
            ->get();

        $todaySubmitted = $service->todayReportFor($user)?->isSubmitted() ?? false;
        $settings = SalesDailyReportSettings::all();
        $isWorkDayToday = $service->isWorkDay($date, $user);
        $isWeeklyOffToday = $user->isWeeklyOff($date);
        $isLeaveToday = $user->isOnApprovedLeave($date);

        return view('employee.sales.daily-reports.index', compact(
            'report',
            'recent',
            'date',
            'todaySubmitted',
            'settings',
            'service',
            'isWorkDayToday',
            'isWeeklyOffToday',
            'isLeaveToday'
        ));
    }

    public function edit(Request $request, SalesDailyReportService $service): View|RedirectResponse
    {
        $user = Auth::user();
        $date = $request->filled('date')
            ? Carbon::parse($request->date)->startOfDay()
            : today();

        if ($date->isFuture()) {
            return redirect()->route('employee.sales.daily-reports.index')
                ->with('error', 'لا يمكن تعبئة تقرير لتاريخ مستقبلي.');
        }

        if (! app(SalesDailyReportService::class)->isWorkDay($date, $user)) {
            $reason = $user->isOnApprovedLeave($date)
                ? 'أنت في إجازة معتمدة في هذا التاريخ.'
                : 'هذا اليوم يوافق إجازتك الأسبوعية ('.($user->weeklyOffDayLabel() ?? 'عطلة').').';

            return redirect()->route('employee.sales.daily-reports.index', ['date' => $date->toDateString()])
                ->with('info', 'لا يُطلَب تقرير يومي: '.$reason);
        }

        $report = SalesDailyReport::forUser($user->id)
            ->whereDate('report_date', $date)
            ->with('contacts.lead')
            ->first();

        if ($report?->isSubmitted()) {
            return redirect()->route('employee.sales.daily-reports.index', ['date' => $date->toDateString()])
                ->with('info', 'تم تسليم تقرير هذا اليوم ولا يمكن تعديله.');
        }

        $leads = SalesLead::query()
            ->forAssignee($user->id)
            ->openPipeline()
            ->orderBy('name')
            ->get(['id', 'name', 'phone', 'stage']);

        $fieldLabels = collect($service->requiredFieldKeys())
            ->mapWithKeys(fn ($k) => [$k => $service->fieldLabel($k)]);

        return view('employee.sales.daily-reports.edit', compact('report', 'date', 'leads', 'fieldLabels'));
    }

    public function store(Request $request, SalesDailyReportService $service): RedirectResponse
    {
        $user = Auth::user();
        $validated = $this->validatedRequest($request);
        $date = Carbon::parse($validated['report_date'])->startOfDay();
        $submit = ($validated['action'] ?? 'draft') === 'submit';

        $service->saveReport($user, $date, $validated, $validated['contacts'] ?? [], $submit);

        $msg = $submit
            ? 'تم تسليم التقرير اليومي بنجاح.'
            : 'تم حفظ المسودة. أكمل الحقول ثم اضغط «تسليم نهائي» لتجنب الخصم التلقائي.';

        return redirect()->route('employee.sales.daily-reports.index', ['date' => $date->toDateString()])
            ->with('success', $msg);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedRequest(Request $request): array
    {
        $data = $request->validate([
            'report_date' => 'required|date|before_or_equal:today',
            'action' => 'required|in:draft,submit',
            'messages_replied' => 'nullable|integer|min:0',
            'leads_qualified' => 'nullable|integer|min:0',
            'bookings_from_leads' => 'nullable|integer|min:0',
            'activity_notes' => 'nullable|string|max:5000',
            'numbers_worked' => 'nullable|integer|min:0',
            'followups_done' => 'nullable|integer|min:0',
            'calls_made' => 'nullable|integer|min:0',
            'meetings_held' => 'nullable|integer|min:0',
            'calls_answered' => 'nullable|integer|min:0',
            'productivity_notes' => 'nullable|string|max:5000',
            'contacts' => 'nullable|array',
            'contacts.*.sales_lead_id' => 'nullable|integer|exists:sales_leads,id',
            'contacts.*.contact_name' => 'nullable|string|max:255',
            'contacts.*.contact_phone' => 'nullable|string|max:64',
            'contacts.*.interaction_type' => 'nullable|in:call,meeting',
            'contacts.*.client_status' => 'nullable|string|max:2000',
            'contacts.*.client_problems' => 'nullable|string|max:5000',
        ]);

        $data['contacts'] = array_values(array_filter(
            $data['contacts'] ?? [],
            fn ($c) => ! empty($c['contact_phone']) || ! empty($c['sales_lead_id']) || ! empty($c['client_status']) || ! empty($c['client_problems'])
        ));

        return $data;
    }
}
