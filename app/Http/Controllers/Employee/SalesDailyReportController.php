<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesDailyReport;
use App\Models\SalesLead;
use App\Services\CampaignReportService;
use App\Services\SalesDailyReportService;
use App\Services\SalesNotificationService;
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
            ->with(['contacts.lead', 'autoDeduction'])
            ->first();

        $todaySubmitted = $service->todayReportFor($user)?->isSubmitted() ?? false;
        $settings = SalesDailyReportSettings::all();
        $isWorkDayToday = $service->isWorkDay($date, $user);
        $isWeeklyOffToday = $user->isWeeklyOff($date);
        $isLeaveToday = $user->isAttendanceExcused($date);

        $autoSynced = false;
        if ($date->isToday() && $isWorkDayToday && ! ($report?->isSubmitted())) {
            $synced = $service->syncAutoDraft($user, $date);
            if ($synced) {
                $report = $synced;
                $autoSynced = true;
            }
        }

        $service->applyDuePenaltiesInRange(
            now()->subDays(3)->startOfDay(),
            now()->startOfDay(),
            collect([$user])
        );

        $recent = SalesDailyReport::forUser($user->id)
            ->with('autoDeduction')
            ->orderByDesc('report_date')
            ->limit(14)
            ->get();

        return view('employee.sales.daily-reports.index', compact(
            'report',
            'recent',
            'date',
            'todaySubmitted',
            'settings',
            'service',
            'isWorkDayToday',
            'isWeeklyOffToday',
            'isLeaveToday',
            'autoSynced'
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
            $reason = $user->isAttendanceExcused($date)
                ? 'أنت في إجازة / إذن غياب معتمد في هذا التاريخ.'
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

        $report = $service->syncAutoDraft($user, $date);
        $autoFilled = (bool) $report;

        $kpiComparison = $report
            ? $service->kpiComparisonForReport($user, $report, $date)
            : $service->kpiComparisonForReport($user, array_fill_keys($service->requiredFieldKeys(), 0), $date);

        $dailyResults = app(\App\Services\SalesDailyResultService::class)->comparisonFor($user, $date);

        $settings = SalesDailyReportSettings::all();

        $todayLeads = $service->leadsTouchedOnDate($user, $date);

        $leads = SalesLead::query()
            ->forAssignee($user->id)
            ->openPipeline()
            ->orderBy('name')
            ->get(['id', 'name', 'phone', 'stage']);

        $fieldLabels = collect($service->requiredFieldKeys())
            ->mapWithKeys(fn ($k) => [$k => $service->fieldLabel($k)]);

        $suggestedContacts = $service->suggestedContactsForReport($user, $date, $report);

        $campaignService = app(CampaignReportService::class);
        $campaigns = $campaignService->campaignsForUser($user, $date);
        $campaignEntries = $campaignService->existingEntries($user, $date);
        $campaignFieldLabels = $campaignService->fieldLabels();

        return view('employee.sales.daily-reports.edit', compact(
            'report', 'date', 'leads', 'fieldLabels', 'autoFilled', 'kpiComparison', 'dailyResults', 'settings', 'todayLeads', 'suggestedContacts',
            'campaigns', 'campaignEntries', 'campaignFieldLabels'
        ));
    }

    public function syncAuto(Request $request, SalesDailyReportService $service): RedirectResponse
    {
        $user = Auth::user();
        $date = $request->filled('date')
            ? Carbon::parse($request->date)->startOfDay()
            : today();

        if ($date->isFuture()) {
            return back()->with('error', 'لا يمكن مزامنة تقرير مستقبلي.');
        }

        $existing = SalesDailyReport::forUser($user->id)
            ->whereDate('report_date', $date)
            ->first();

        if ($existing?->isSubmitted()) {
            return back()->with('info', 'التقرير مسلّم ولا يمكن تحديثه تلقائياً.');
        }

        $report = $service->syncAutoDraft($user, $date);

        if (! $report) {
            return back()->with('info', 'لا يُطلَب تقرير لهذا اليوم.');
        }

        return redirect()->route('employee.sales.daily-reports.edit', ['date' => $date->toDateString()])
            ->with('success', 'تم تحديث التقرير تلقائياً من نشاطك ('.$date->format('Y-m-d').').');
    }

    public function store(Request $request, SalesDailyReportService $service): RedirectResponse
    {
        $user = Auth::user();
        $validated = $this->validatedRequest($request);
        $date = Carbon::parse($validated['report_date'])->startOfDay();
        $submit = ($validated['action'] ?? 'draft') === 'submit';

        $report = $service->saveReport($user, $date, $validated, $validated['contacts'] ?? [], $submit);

        app(CampaignReportService::class)->saveEntries($user, $date, $validated['campaigns'] ?? [], $report);

        $comparison = null;
        if ($submit) {
            $comparison = $service->kpiComparisonForReport($user, $report, $date);
            app(SalesNotificationService::class)->notifyDailyReportSubmitted($user, $report, $comparison);
        }

        $msg = $submit
            ? 'تم تسليم التقرير — '.($comparison['status_label'] ?? '')
            : 'تم حفظ المسودة. أكمل الحقول اليدوية ثم اضغط «تسليم نهائي».';

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
            'campaigns' => 'nullable|array',
            'campaigns.*.new_messages' => 'nullable|integer|min:0|max:100000',
            'campaigns.*.whatsapp_messages' => 'nullable|integer|min:0|max:100000',
            'campaigns.*.messenger_messages' => 'nullable|integer|min:0|max:100000',
            'campaigns.*.instagram_messages' => 'nullable|integer|min:0|max:100000',
            'campaigns.*.qualified' => 'nullable|integer|min:0|max:100000',
            'campaigns.*.unqualified' => 'nullable|integer|min:0|max:100000',
            'campaigns.*.converted' => 'nullable|integer|min:0|max:100000',
            'campaigns.*.notes' => 'nullable|string|max:5000',
        ]);

        $data['contacts'] = array_values(array_filter(
            $data['contacts'] ?? [],
            fn ($c) => ! empty($c['contact_phone']) || ! empty($c['sales_lead_id']) || ! empty($c['client_status']) || ! empty($c['client_problems'])
        ));

        return $data;
    }
}
