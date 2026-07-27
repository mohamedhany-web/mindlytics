<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvertisingCampaign;
use App\Models\CampaignDailyReport;
use App\Models\User;
use App\Services\CampaignReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdvertisingCampaignController extends Controller
{
    public function index(Request $request)
    {
        $campaigns = AdvertisingCampaign::query()
            ->withCount('salesEmployees')
            ->with('salesEmployees:id,name')
            ->orderByDesc('is_active')
            ->orderByDesc('created_at')
            ->paginate(15);

        $service = app(CampaignReportService::class);
        $aggregates = [];
        foreach ($campaigns as $campaign) {
            $aggregates[$campaign->id] = $service->aggregateForCampaign($campaign);
        }

        return view('admin.marketing.advertising-campaigns.index', compact('campaigns', 'aggregates'));
    }

    public function create()
    {
        $salesReps = $this->salesReps();

        return view('admin.marketing.advertising-campaigns.create', compact('salesReps'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateCampaign($request);

        $campaign = AdvertisingCampaign::create([
            'name' => $validated['name'],
            'platform' => $validated['platform'] ?? null,
            'description' => $validated['description'] ?? null,
            'cost' => $validated['cost'] ?? 0,
            'currency' => $validated['currency'] ?? 'EGP',
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
        ]);

        $campaign->salesEmployees()->sync($this->validSalesRepIds($request));

        return redirect()->route('admin.advertising-campaigns.index')
            ->with('success', 'تم إنشاء الحملة الإعلانية بنجاح.');
    }

    public function edit(AdvertisingCampaign $advertisingCampaign)
    {
        $salesReps = $this->salesReps();
        $assigned = $advertisingCampaign->salesEmployees()->pluck('users.id')->all();

        return view('admin.marketing.advertising-campaigns.edit', [
            'campaign' => $advertisingCampaign,
            'salesReps' => $salesReps,
            'assigned' => $assigned,
        ]);
    }

    public function update(Request $request, AdvertisingCampaign $advertisingCampaign)
    {
        $validated = $this->validateCampaign($request);

        $advertisingCampaign->update([
            'name' => $validated['name'],
            'platform' => $validated['platform'] ?? null,
            'description' => $validated['description'] ?? null,
            'cost' => $validated['cost'] ?? 0,
            'currency' => $validated['currency'] ?? 'EGP',
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'notes' => $validated['notes'] ?? null,
        ]);

        $advertisingCampaign->salesEmployees()->sync($this->validSalesRepIds($request));

        return redirect()->route('admin.advertising-campaigns.index')
            ->with('success', 'تم تحديث الحملة الإعلانية بنجاح.');
    }

    public function destroy(AdvertisingCampaign $advertisingCampaign)
    {
        $advertisingCampaign->delete();

        return redirect()->route('admin.advertising-campaigns.index')
            ->with('success', 'تم حذف الحملة الإعلانية.');
    }

    /**
     * تقارير الكامبين المرفوعة من السيلز (تظهر في قسم التسويق).
     */
    public function reports(Request $request)
    {
        [$from, $to] = $this->range($request);
        $campaignId = $request->integer('campaign_id') ?: null;
        $userId = $request->integer('user_id') ?: null;

        $campaigns = AdvertisingCampaign::orderBy('name')->get(['id', 'name', 'cost']);
        $salesReps = $this->salesReps();

        $query = CampaignDailyReport::query()
            ->with(['campaign:id,name,platform,cost', 'user:id,name'])
            ->whereBetween('report_date', [$from->toDateString(), $to->toDateString()])
            ->orderByDesc('report_date');

        if ($campaignId) {
            $query->where('advertising_campaign_id', $campaignId);
        }
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $rows = $query->get();

        $totals = [
            'new_messages' => (int) $rows->sum('new_messages'),
            'whatsapp_messages' => (int) $rows->sum('whatsapp_messages'),
            'messenger_messages' => (int) $rows->sum('messenger_messages'),
            'instagram_messages' => (int) $rows->sum('instagram_messages'),
            'qualified' => (int) $rows->sum('qualified'),
            'unqualified' => (int) $rows->sum('unqualified'),
            'converted' => (int) $rows->sum('converted'),
        ];

        // ملخص لكل حملة مع التكلفة وتكلفة كل نتيجة
        $perCampaign = $rows->groupBy('advertising_campaign_id')->map(function ($group) {
            $campaign = $group->first()->campaign;
            $messages = (int) $group->sum('new_messages');
            $qualified = (int) $group->sum('qualified');
            $converted = (int) $group->sum('converted');
            $cost = (float) ($campaign->cost ?? 0);

            return [
                'campaign' => $campaign,
                'messages' => $messages,
                'qualified' => $qualified,
                'unqualified' => (int) $group->sum('unqualified'),
                'converted' => $converted,
                'cost' => $cost,
                'cost_per_message' => $messages > 0 ? round($cost / $messages, 2) : null,
                'cost_per_converted' => $converted > 0 ? round($cost / $converted, 2) : null,
            ];
        })->values();

        return view('admin.marketing.advertising-campaigns.reports', compact(
            'rows', 'totals', 'perCampaign', 'campaigns', 'salesReps', 'from', 'to', 'campaignId', 'userId'
        ));
    }

    public function reportsExport(Request $request): StreamedResponse
    {
        [$from, $to] = $this->range($request);
        $campaignId = $request->integer('campaign_id') ?: null;
        $userId = $request->integer('user_id') ?: null;

        $query = CampaignDailyReport::query()
            ->with(['campaign:id,name', 'user:id,name'])
            ->whereBetween('report_date', [$from->toDateString(), $to->toDateString()])
            ->orderByDesc('report_date');

        if ($campaignId) {
            $query->where('advertising_campaign_id', $campaignId);
        }
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $rows = $query->get();
        $filename = 'campaign-reports-'.$from->toDateString().'-'.$to->toDateString().'.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fprintf($out, "\xEF\xBB\xBF"); // BOM لدعم العربية في Excel
            fputcsv($out, ['التاريخ', 'الحملة', 'الموظف', 'رسائل جديدة', 'واتساب', 'ماسنجر', 'إنستجرام', 'Qualified', 'Unqualified', 'Converted', 'ملاحظات']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->report_date?->toDateString(),
                    $r->campaign?->name,
                    $r->user?->name,
                    $r->new_messages,
                    $r->whatsapp_messages,
                    $r->messenger_messages,
                    $r->instagram_messages,
                    $r->qualified,
                    $r->unqualified,
                    $r->converted,
                    $r->notes,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * @return array<int, \Carbon\Carbon>
     */
    private function range(Request $request): array
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : now()->subDays(30)->startOfDay();
        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : now()->endOfDay();

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        return [$from, $to];
    }

    /**
     * @return \Illuminate\Support\Collection<int, User>
     */
    private function salesReps()
    {
        return User::salesEmployees()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * @return array<int, int>
     */
    private function validSalesRepIds(Request $request): array
    {
        $ids = collect($request->input('sales_reps', []))
            ->map(fn ($v) => (int) $v)
            ->filter()
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return [];
        }

        return User::salesEmployees()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function validateCampaign(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'platform' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:5000',
            'cost' => 'nullable|numeric|min:0|max:99999999',
            'currency' => 'nullable|string|max:3',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:5000',
            'sales_reps' => 'nullable|array',
            'sales_reps.*' => 'integer',
        ]);
    }
}
