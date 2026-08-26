<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\AdvertisingCampaign;
use App\Models\CampaignDailyReport;
use App\Models\User;
use App\Services\CampaignReportService;
use App\Services\SalesTeamService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesManagerCampaignReportController extends Controller
{
    public function __construct(
        private SalesTeamService $teamService,
        private CampaignReportService $campaigns,
    ) {
        $this->middleware('sales.manager');
    }

    public function index(Request $request): View
    {
        $user = Auth::user();
        $team = $this->teamService->managedTeamOrFail($user);
        $memberIds = $this->teamService->memberUserIds($team, $user);

        if (! $this->campaigns->tablesReady()) {
            return view('employee.sales-manager.campaign-reports.index', [
                'team' => $team,
                'ready' => false,
                'rows' => collect(),
                'totals' => [],
                'perCampaign' => collect(),
                'perRep' => collect(),
                'campaigns' => collect(),
                'salesReps' => collect(),
                'from' => now()->subDays(30)->startOfDay(),
                'to' => now()->endOfDay(),
                'campaignId' => null,
                'userId' => null,
            ]);
        }

        [$from, $to] = $this->range($request);
        $campaignId = $request->integer('campaign_id') ?: null;
        $userId = $request->integer('user_id') ?: null;
        if ($userId && ! in_array($userId, $memberIds, true)) {
            $userId = null;
        }

        $campaigns = AdvertisingCampaign::query()->orderBy('name')->get(['id', 'name', 'cost', 'platform']);
        $salesReps = User::query()
            ->whereIn('id', $memberIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $query = CampaignDailyReport::query()
            ->with(['campaign:id,name,platform,cost', 'user:id,name'])
            ->whereIn('user_id', $memberIds)
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

        $perRep = $rows->groupBy('user_id')->map(function ($group) {
            return [
                'user' => $group->first()->user,
                'messages' => (int) $group->sum('new_messages'),
                'qualified' => (int) $group->sum('qualified'),
                'converted' => (int) $group->sum('converted'),
                'entries' => $group->count(),
            ];
        })->sortByDesc('messages')->values();

        return view('employee.sales-manager.campaign-reports.index', [
            'team' => $team,
            'ready' => true,
            'rows' => $rows,
            'totals' => $totals,
            'perCampaign' => $perCampaign,
            'perRep' => $perRep,
            'campaigns' => $campaigns,
            'salesReps' => $salesReps,
            'from' => $from,
            'to' => $to,
            'campaignId' => $campaignId,
            'userId' => $userId,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $user = Auth::user();
        $team = $this->teamService->managedTeamOrFail($user);
        $memberIds = $this->teamService->memberUserIds($team, $user);

        [$from, $to] = $this->range($request);
        $campaignId = $request->integer('campaign_id') ?: null;
        $userId = $request->integer('user_id') ?: null;
        if ($userId && ! in_array($userId, $memberIds, true)) {
            $userId = null;
        }

        $query = CampaignDailyReport::query()
            ->with(['campaign:id,name', 'user:id,name'])
            ->whereIn('user_id', $memberIds)
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
            fprintf($out, "\xEF\xBB\xBF");
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
     * @return array{0: Carbon, 1: Carbon}
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
}
