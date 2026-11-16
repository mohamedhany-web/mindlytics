<?php

namespace App\Domains\Marketing\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\Marketing\Models\AdvertisingCampaign;
use App\Domains\Marketing\Models\CampaignDailyReport;
use App\Domains\Marketing\Services\CampaignReportService;
use App\Domains\Messaging\Services\MetaAdsGraphService;
use App\Domains\Messaging\Services\MetaCampaignLeadSyncService;
use App\Domains\Messaging\Support\MetaAdsSettings;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdvertisingCampaignController extends Controller
{
    public function index(MetaAdsGraphService $metaAds)
    {
        if (! MetaAdsSettings::hasAccessToken()) {
            return redirect()
                ->route('admin.meta-ads.settings')
                ->with('error', 'اربط Meta من السوشيال ميديا أولاً لعرض الحملات.');
        }

        if (! MetaAdsSettings::isReady()) {
            return redirect()
                ->route('admin.meta-ads.settings')
                ->with('error', 'اختر حساب إعلانات (Ad Account) من إعدادات Meta Ads.');
        }

        $connection = $metaAds->connectionMeta();
        $list = $metaAds->listCampaigns(100);
        $campaigns = ($list['success'] ?? false) ? ($list['data'] ?? []) : [];
        $error = ($list['success'] ?? false) ? null : ($list['error'] ?? 'تعذر جلب الحملات من ميتا');

        $active = 0;
        $paused = 0;
        foreach ($campaigns as $c) {
            $status = strtoupper((string) ($c['effective_status'] ?? $c['status'] ?? ''));
            if ($status === 'ACTIVE') {
                $active++;
            } else {
                $paused++;
            }
        }

        $metaIds = collect($campaigns)->pluck('id')->filter()->map(fn ($id) => (string) $id)->all();
        $linkedLocals = AdvertisingCampaign::query()
            ->whereIn('meta_campaign_id', $metaIds)
            ->withCount('salesEmployees')
            ->get()
            ->keyBy(fn (AdvertisingCampaign $row) => (string) $row->meta_campaign_id);

        $stats = [
            'total' => count($campaigns),
            'active' => $active,
            'inactive' => $paused,
            'total_cost' => (float) $linkedLocals->sum('cost'),
        ];

        return view('marketing::admin.advertising-campaigns.index', [
            'campaigns' => $campaigns,
            'linkedLocals' => $linkedLocals,
            'connection' => $connection,
            'error' => $error,
            'stats' => $stats,
            'metaAds' => $metaAds,
        ]);
    }

    public function create()
    {
        return redirect()->route('admin.meta-ads.campaigns.create');
    }

    /**
     * إعداد محلي (تكلفة / سيلز) لحملة Meta — ينشئ السجل المحلي إن لم يوجد.
     */
    public function localSettings(string $metaCampaign, MetaAdsGraphService $metaAds)
    {
        $metaCampaign = trim($metaCampaign);
        abort_if($metaCampaign === '', 404);

        $name = $metaCampaign;
        $meta = $metaAds->getCampaign($metaCampaign);
        if ($meta['success'] ?? false) {
            $name = (string) ($meta['data']['name'] ?? $metaCampaign);
        }

        $local = AdvertisingCampaign::query()->firstOrCreate(
            ['meta_campaign_id' => $metaCampaign],
            [
                'name' => $name,
                'meta_campaign_name' => $name,
                'platform' => 'facebook',
                'cost' => 0,
                'currency' => 'EGP',
                'is_active' => true,
                'created_by' => Auth::id(),
            ]
        );

        if ($local->meta_campaign_name !== $name || $local->name === $metaCampaign) {
            $local->fill([
                'meta_campaign_name' => $name,
                'name' => $local->wasRecentlyCreated || $local->name === $metaCampaign ? $name : $local->name,
            ])->save();
        }

        return redirect()->route('admin.advertising-campaigns.edit', $local);
    }

    public function syncMetaCampaignLeads(
        string $metaCampaign,
        MetaCampaignLeadSyncService $sync,
        MetaAdsGraphService $metaAds,
    ): RedirectResponse {
        $metaCampaign = trim($metaCampaign);
        abort_if($metaCampaign === '', 404);

        $name = null;
        $meta = $metaAds->getCampaign($metaCampaign);
        if ($meta['success'] ?? false) {
            $name = (string) ($meta['data']['name'] ?? $metaCampaign);
        }

        $local = AdvertisingCampaign::query()->where('meta_campaign_id', $metaCampaign)->first();
        $result = $sync->syncByMetaCampaignId($metaCampaign, $local, $name, Auth::id());

        return redirect()
            ->route('admin.advertising-campaigns.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function store(Request $request)
    {
        $validated = $this->validateCampaign($request);
        [$metaCampaignId, $metaCampaignName] = $this->resolveMetaCampaignLink($validated);

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
            'meta_campaign_id' => $metaCampaignId,
            'meta_campaign_name' => $metaCampaignName,
            'created_by' => Auth::id(),
        ]);

        $campaign->salesEmployees()->sync($this->validSalesRepIds($request));

        return redirect()->route('admin.advertising-campaigns.edit', $campaign)
            ->with('success', 'تم إنشاء الحملة الإعلانية بنجاح.');
    }

    public function edit(AdvertisingCampaign $advertisingCampaign, MetaAdsGraphService $metaAds)
    {
        $salesReps = $this->salesReps();
        $assigned = $advertisingCampaign->salesEmployees()->pluck('users.id')->all();
        [$metaCampaigns, $metaCampaignsError] = $this->metaCampaignOptions($metaAds);

        return view('marketing::admin.advertising-campaigns.edit', [
            'campaign' => $advertisingCampaign,
            'salesReps' => $salesReps,
            'assigned' => $assigned,
            'metaCampaigns' => $metaCampaigns,
            'metaCampaignsError' => $metaCampaignsError,
        ]);
    }

    public function update(Request $request, AdvertisingCampaign $advertisingCampaign)
    {
        $validated = $this->validateCampaign($request);
        [$metaCampaignId, $metaCampaignName] = $this->resolveMetaCampaignLink($validated);

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
            'meta_campaign_id' => $metaCampaignId,
            'meta_campaign_name' => $metaCampaignName,
        ]);

        $advertisingCampaign->salesEmployees()->sync($this->validSalesRepIds($request));

        return redirect()->route('admin.advertising-campaigns.edit', $advertisingCampaign)
            ->with('success', 'تم تحديث الحملة الإعلانية بنجاح.');
    }

    public function syncMetaLeads(
        AdvertisingCampaign $advertisingCampaign,
        MetaCampaignLeadSyncService $sync,
    ): RedirectResponse {
        $metaCampaignId = trim((string) ($advertisingCampaign->meta_campaign_id ?? ''));
        if ($metaCampaignId === '') {
            return redirect()
                ->route('admin.advertising-campaigns.edit', $advertisingCampaign)
                ->with('error', 'اربط الحملة بكامبين Meta أولاً ثم اسحب الليدز.');
        }

        $result = $sync->syncByMetaCampaignId(
            $metaCampaignId,
            $advertisingCampaign,
            $advertisingCampaign->meta_campaign_name,
            Auth::id(),
        );

        return redirect()
            ->route('admin.advertising-campaigns.edit', $advertisingCampaign)
            ->with($result['success'] ? 'success' : 'error', $result['message']);
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

        return view('marketing::admin.advertising-campaigns.reports', compact(
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
            'meta_campaign_id' => 'nullable|string|max:64',
            'sales_reps' => 'nullable|array',
            'sales_reps.*' => 'integer',
        ]);
    }

    /**
     * @return array{0: list<array{id: string, name: string, status?: string}>, 1: ?string}
     */
    private function metaCampaignOptions(MetaAdsGraphService $metaAds): array
    {
        if (! MetaAdsSettings::hasAccessToken() || ! MetaAdsSettings::isReady()) {
            return [[], 'اربط Meta واختر Ad Account من إعدادات Meta Ads لعرض الحملات.'];
        }

        $list = $metaAds->listCampaigns(100);
        if (! ($list['success'] ?? false)) {
            return [[], (string) ($list['error'] ?? 'تعذر جلب حملات Meta')];
        }

        $options = [];
        foreach (($list['data'] ?? []) as $row) {
            if (! is_array($row) || blank($row['id'] ?? null)) {
                continue;
            }
            $options[] = [
                'id' => (string) $row['id'],
                'name' => (string) ($row['name'] ?? $row['id']),
                'status' => (string) ($row['effective_status'] ?? $row['status'] ?? ''),
            ];
        }

        return [$options, null];
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array{0: ?string, 1: ?string}
     */
    private function resolveMetaCampaignLink(array $validated): array
    {
        $metaCampaignId = trim((string) ($validated['meta_campaign_id'] ?? ''));
        if ($metaCampaignId === '') {
            return [null, null];
        }

        $name = null;
        if (MetaAdsSettings::isReady()) {
            $list = app(MetaAdsGraphService::class)->listCampaigns(100);
            if ($list['success'] ?? false) {
                foreach (($list['data'] ?? []) as $row) {
                    if ((string) ($row['id'] ?? '') === $metaCampaignId) {
                        $name = (string) ($row['name'] ?? $metaCampaignId);
                        break;
                    }
                }
            }
        }

        return [$metaCampaignId, $name];
    }
}
