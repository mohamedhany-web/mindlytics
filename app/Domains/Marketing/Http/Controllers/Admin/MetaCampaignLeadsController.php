<?php

namespace App\Domains\Marketing\Http\Controllers\Admin;

use App\Domains\Marketing\Models\AdvertisingCampaign;
use App\Domains\Marketing\Services\MetaCampaignLeadsExcelExportService;
use App\Domains\Messaging\Models\MetaCampaignLeadSyncRun;
use App\Domains\Messaging\Services\MetaAdsGraphService;
use App\Domains\Sales\Models\SalesLead;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MetaCampaignLeadsController extends Controller
{
    public function index(string $metaCampaign, Request $request, MetaAdsGraphService $metaAds): View
    {
        $metaCampaign = trim($metaCampaign);
        abort_if($metaCampaign === '', 404);

        $local = AdvertisingCampaign::query()->where('meta_campaign_id', $metaCampaign)->first();
        $campaignName = $local?->meta_campaign_name ?: $local?->name;
        if (! $campaignName) {
            $meta = $metaAds->getCampaign($metaCampaign);
            if ($meta['success'] ?? false) {
                $campaignName = (string) ($meta['data']['name'] ?? $metaCampaign);
            } else {
                $campaignName = $metaCampaign;
            }
        }

        $query = $this->leadsQuery($metaCampaign, $local?->id, $request);
        $leads = (clone $query)
            ->with(['assignee'])
            ->orderByDesc('id')
            ->paginate(40)
            ->withQueryString();

        $statsBase = SalesLead::query()->where(function ($q) use ($metaCampaign, $local) {
            $q->where('meta_campaign_id', $metaCampaign);
            if ($local) {
                $q->orWhere('advertising_campaign_id', $local->id);
            }
        });

        $stats = [
            'total' => (clone $statsBase)->count(),
            'forms' => (clone $statsBase)->whereNotNull('meta_leadgen_id')->count(),
            'inbox' => (clone $statsBase)->whereNull('meta_leadgen_id')->count(),
            'unassigned' => (clone $statsBase)->where(function ($q) {
                $q->whereNull('assigned_to')->orWhere('assigned_to', 0);
            })->count(),
        ];

        $runs = MetaCampaignLeadSyncRun::query()
            ->where('meta_campaign_id', $metaCampaign)
            ->with('requester:id,name')
            ->latest('id')
            ->limit(12)
            ->get();

        $latestRun = $runs->first();
        $importBatches = (clone $statsBase)
            ->whereNotNull('import_batch')
            ->distinct()
            ->orderByDesc('import_batch')
            ->limit(30)
            ->pluck('import_batch');

        return view('marketing::admin.advertising-campaigns.meta-leads', [
            'metaCampaignId' => $metaCampaign,
            'campaignName' => $campaignName,
            'local' => $local,
            'leads' => $leads,
            'stats' => $stats,
            'runs' => $runs,
            'latestRun' => $latestRun,
            'importBatches' => $importBatches,
            'filters' => [
                'q' => trim((string) $request->input('q', '')),
                'source_type' => (string) $request->input('source_type', ''),
                'import_batch' => (string) $request->input('import_batch', ''),
            ],
        ]);
    }

    public function status(string $metaCampaign): JsonResponse
    {
        $metaCampaign = trim($metaCampaign);
        abort_if($metaCampaign === '', 404);

        $local = AdvertisingCampaign::query()->where('meta_campaign_id', $metaCampaign)->first();
        $statsBase = SalesLead::query()->where(function ($q) use ($metaCampaign, $local) {
            $q->where('meta_campaign_id', $metaCampaign);
            if ($local) {
                $q->orWhere('advertising_campaign_id', $local->id);
            }
        });

        $latestRun = MetaCampaignLeadSyncRun::query()
            ->where('meta_campaign_id', $metaCampaign)
            ->latest('id')
            ->first();

        $latestLeads = (clone $statsBase)
            ->orderByDesc('id')
            ->limit(8)
            ->get(['id', 'name', 'phone', 'meta_leadgen_id', 'created_at'])
            ->map(fn (SalesLead $l) => [
                'id' => $l->id,
                'name' => $l->name,
                'phone' => $l->phone,
                'type' => filled($l->meta_leadgen_id) ? 'form' : 'inbox',
                'created_at' => optional($l->created_at)?->timezone(config('app.timezone'))->format('H:i:s'),
            ])
            ->values();

        return response()->json([
            'stats' => [
                'total' => (clone $statsBase)->count(),
                'forms' => (clone $statsBase)->whereNotNull('meta_leadgen_id')->count(),
                'inbox' => (clone $statsBase)->whereNull('meta_leadgen_id')->count(),
                'unassigned' => (clone $statsBase)->where(function ($q) {
                    $q->whereNull('assigned_to')->orWhere('assigned_to', 0);
                })->count(),
            ],
            'run' => $latestRun ? [
                'id' => $latestRun->id,
                'status' => $latestRun->status,
                'status_label' => $latestRun->statusLabel(),
                'status_class' => $latestRun->statusBadgeClass(),
                'message' => $latestRun->message,
                'error' => $latestRun->error,
                'created_count' => (int) $latestRun->created_count,
                'updated_count' => (int) $latestRun->updated_count,
                'conversations_linked' => (int) $latestRun->conversations_linked,
                'skipped_count' => (int) $latestRun->skipped_count,
                'import_batch' => $latestRun->import_batch,
                'phase' => data_get($latestRun->diagnostics, 'phase'),
                'is_active' => in_array($latestRun->status, [
                    MetaCampaignLeadSyncRun::STATUS_QUEUED,
                    MetaCampaignLeadSyncRun::STATUS_RUNNING,
                ], true),
            ] : null,
            'latest_leads' => $latestLeads,
        ]);
    }

    public function export(
        string $metaCampaign,
        Request $request,
        MetaAdsGraphService $metaAds,
        MetaCampaignLeadsExcelExportService $excel,
    ): StreamedResponse {
        $metaCampaign = trim($metaCampaign);
        abort_if($metaCampaign === '', 404);

        $local = AdvertisingCampaign::query()->where('meta_campaign_id', $metaCampaign)->first();
        $campaignName = $local?->meta_campaign_name ?: $local?->name ?: $metaCampaign;
        if ($campaignName === $metaCampaign) {
            $meta = $metaAds->getCampaign($metaCampaign);
            if ($meta['success'] ?? false) {
                $campaignName = (string) ($meta['data']['name'] ?? $metaCampaign);
            }
        }

        $query = $this->leadsQuery($metaCampaign, $local?->id, $request);
        $context = 'بواسطة: '.(auth()->user()->name ?? 'admin');
        $spreadsheet = $excel->buildSpreadsheet($query, $campaignName, $metaCampaign, $context);
        $safeName = preg_replace('/[^\p{L}\p{N}\-_]+/u', '-', $campaignName) ?: 'campaign';
        $filename = 'meta-leads-'.$safeName.'-'.now()->format('Y-m-d-His').'.xlsx';

        return response()->streamDownload(function () use ($excel, $spreadsheet) {
            $excel->writeToOutput($spreadsheet);
            $spreadsheet->disconnectWorksheets();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<SalesLead>
     */
    private function leadsQuery(string $metaCampaignId, ?int $localId, Request $request)
    {
        $query = SalesLead::query()->where(function ($q) use ($metaCampaignId, $localId) {
            $q->where('meta_campaign_id', $metaCampaignId);
            if ($localId) {
                $q->orWhere('advertising_campaign_id', $localId);
            }
        });

        $q = trim((string) $request->input('q', ''));
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('meta_leadgen_id', 'like', "%{$q}%")
                    ->orWhere('meta_ad_id', 'like', "%{$q}%")
                    ->orWhere('import_batch', 'like', "%{$q}%");
            });
        }

        $sourceType = (string) $request->input('source_type', '');
        if ($sourceType === 'form') {
            $query->whereNotNull('meta_leadgen_id');
        } elseif ($sourceType === 'inbox') {
            $query->whereNull('meta_leadgen_id');
        }

        $batch = trim((string) $request->input('import_batch', ''));
        if ($batch !== '') {
            $query->where('import_batch', $batch);
        }

        return $query;
    }
}
