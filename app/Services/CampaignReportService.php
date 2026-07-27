<?php

namespace App\Services;

use App\Models\AdvertisingCampaign;
use App\Models\CampaignDailyReport;
use App\Models\SalesDailyReport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class CampaignReportService
{
    /**
     * الحقول الرقمية لتقرير الكامبين اليومي.
     *
     * @return array<string, string>
     */
    public function fieldLabels(): array
    {
        return [
            'new_messages' => 'رسائل جديدة',
            'whatsapp_messages' => 'واتساب',
            'messenger_messages' => 'ماسنجر',
            'instagram_messages' => 'إنستجرام',
            'qualified' => 'Qualified',
            'unqualified' => 'Unqualified',
            'converted' => 'Converted',
        ];
    }

    /**
     * @return list<string>
     */
    public function numericKeys(): array
    {
        return array_keys($this->fieldLabels());
    }

    public function tablesReady(): bool
    {
        return Schema::hasTable('advertising_campaigns')
            && Schema::hasTable('campaign_daily_reports');
    }

    /**
     * الحملات النشطة المسندة لموظف مبيعات في تاريخ معيّن.
     *
     * @return \Illuminate\Support\Collection<int, AdvertisingCampaign>
     */
    public function campaignsForUser(User $user, Carbon $date)
    {
        if (! $this->tablesReady()) {
            return collect();
        }

        return AdvertisingCampaign::query()
            ->active()
            ->whereHas('salesEmployees', fn ($q) => $q->where('users.id', $user->id))
            ->where(function ($q) use ($date) {
                $q->whereNull('start_date')->orWhereDate('start_date', '<=', $date->toDateString());
            })
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')->orWhereDate('end_date', '>=', $date->toDateString());
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * تقارير الكامبين المحفوظة لموظف في تاريخ (مفهرسة بمعرّف الحملة).
     *
     * @return \Illuminate\Support\Collection<int, CampaignDailyReport>
     */
    public function existingEntries(User $user, Carbon $date)
    {
        if (! $this->tablesReady()) {
            return collect();
        }

        return CampaignDailyReport::query()
            ->where('user_id', $user->id)
            ->whereDate('report_date', $date->toDateString())
            ->get()
            ->keyBy('advertising_campaign_id');
    }

    /**
     * حفظ/تحديث مدخلات الكامبين لموظف في تاريخ معيّن.
     *
     * @param  array<int, array<string, mixed>>  $entries  مفتاحها معرّف الحملة
     */
    public function saveEntries(User $user, Carbon $date, array $entries, ?SalesDailyReport $report = null): void
    {
        if (! $this->tablesReady() || $entries === []) {
            return;
        }

        $assignedIds = $this->campaignsForUser($user, $date)->pluck('id')->all();
        if ($assignedIds === []) {
            return;
        }

        foreach ($entries as $campaignId => $values) {
            $campaignId = (int) $campaignId;
            if (! in_array($campaignId, $assignedIds, true)) {
                continue;
            }

            $payload = [];
            foreach ($this->numericKeys() as $key) {
                $payload[$key] = max(0, (int) ($values[$key] ?? 0));
            }
            $payload['notes'] = isset($values['notes']) && trim((string) $values['notes']) !== ''
                ? trim((string) $values['notes'])
                : null;
            $payload['sales_daily_report_id'] = $report?->id;

            CampaignDailyReport::updateOrCreate(
                [
                    'advertising_campaign_id' => $campaignId,
                    'user_id' => $user->id,
                    'report_date' => $date->toDateString(),
                ],
                $payload
            );
        }
    }

    /**
     * تجميع نتائج حملة (للتقارير في قسم التسويق).
     *
     * @return array{new_messages:int,whatsapp_messages:int,messenger_messages:int,instagram_messages:int,qualified:int,unqualified:int,converted:int,days:int}
     */
    public function aggregateForCampaign(AdvertisingCampaign $campaign, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $q = CampaignDailyReport::where('advertising_campaign_id', $campaign->id);
        if ($from) {
            $q->whereDate('report_date', '>=', $from->toDateString());
        }
        if ($to) {
            $q->whereDate('report_date', '<=', $to->toDateString());
        }

        return [
            'new_messages' => (int) (clone $q)->sum('new_messages'),
            'whatsapp_messages' => (int) (clone $q)->sum('whatsapp_messages'),
            'messenger_messages' => (int) (clone $q)->sum('messenger_messages'),
            'instagram_messages' => (int) (clone $q)->sum('instagram_messages'),
            'qualified' => (int) (clone $q)->sum('qualified'),
            'unqualified' => (int) (clone $q)->sum('unqualified'),
            'converted' => (int) (clone $q)->sum('converted'),
            'days' => (int) (clone $q)->distinct('report_date')->count('report_date'),
        ];
    }
}
