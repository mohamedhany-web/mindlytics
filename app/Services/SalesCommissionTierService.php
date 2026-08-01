<?php

namespace App\Services;

use App\Models\SalesLead;
use App\Models\User;
use Carbon\Carbon;

class SalesCommissionTierService
{
    /**
     * الشرائح الافتراضية من جدول الحوافز.
     *
     * @return list<array{min:int,max:int|null,rate:float,bonus:float,bonus_at:int|null}>
     */
    public static function defaultTiers(): array
    {
        return [
            ['min' => 1, 'max' => 9, 'rate' => 50.0, 'bonus' => 0.0, 'bonus_at' => null],
            ['min' => 10, 'max' => 19, 'rate' => 60.0, 'bonus' => 500.0, 'bonus_at' => 10],
            ['min' => 20, 'max' => 29, 'rate' => 70.0, 'bonus' => 1500.0, 'bonus_at' => 20],
            ['min' => 30, 'max' => 39, 'rate' => 80.0, 'bonus' => 3000.0, 'bonus_at' => 30],
            ['min' => 40, 'max' => null, 'rate' => 100.0, 'bonus' => 5000.0, 'bonus_at' => 40],
        ];
    }

    /**
     * @param  mixed  $raw
     * @return list<array{min:int,max:int|null,rate:float,bonus:float,bonus_at:int|null}>
     */
    public static function normalizeTiers($raw): array
    {
        if (! is_array($raw) || $raw === []) {
            return self::defaultTiers();
        }

        $tiers = [];
        foreach ($raw as $row) {
            if (! is_array($row)) {
                continue;
            }
            $min = (int) ($row['min'] ?? 0);
            if ($min < 1) {
                continue;
            }
            $max = array_key_exists('max', $row) && $row['max'] !== null && $row['max'] !== ''
                ? (int) $row['max']
                : null;
            $bonusAt = array_key_exists('bonus_at', $row) && $row['bonus_at'] !== null && $row['bonus_at'] !== ''
                ? (int) $row['bonus_at']
                : null;

            $tiers[] = [
                'min' => $min,
                'max' => $max,
                'rate' => round((float) ($row['rate'] ?? 0), 2),
                'bonus' => round((float) ($row['bonus'] ?? 0), 2),
                'bonus_at' => $bonusAt,
            ];
        }

        usort($tiers, fn ($a, $b) => $a['min'] <=> $b['min']);

        return $tiers !== [] ? $tiers : self::defaultTiers();
    }

    /**
     * @return list<array{min:int,max:int|null,rate:float,bonus:float,bonus_at:int|null}>
     */
    public static function tiersFor(User $user): array
    {
        return self::normalizeTiers($user->sales_commission_tiers);
    }

    public static function rateForSaleNumber(User $user, int $saleNumber): float
    {
        if ($saleNumber < 1) {
            return 0.0;
        }

        foreach (self::tiersFor($user) as $tier) {
            $inMin = $saleNumber >= $tier['min'];
            $inMax = $tier['max'] === null || $saleNumber <= $tier['max'];
            if ($inMin && $inMax) {
                return (float) $tier['rate'];
            }
        }

        $last = collect(self::tiersFor($user))->last();

        return (float) ($last['rate'] ?? 0);
    }

    public static function milestoneBonusForSaleNumber(User $user, int $saleNumber): float
    {
        foreach (self::tiersFor($user) as $tier) {
            if ($tier['bonus_at'] !== null && (int) $tier['bonus_at'] === $saleNumber) {
                return max(0.0, (float) $tier['bonus']);
            }
        }

        return 0.0;
    }

    /**
     * @return array{0: Carbon|null, 1: Carbon|null}
     */
    public static function periodRange(User $user, ?Carbon $at = null): array
    {
        $at = $at?->copy() ?? now();
        $period = (string) ($user->sales_commission_tier_period ?? 'month');

        if ($period === 'all') {
            return [null, null];
        }

        return [$at->copy()->startOfMonth(), $at->copy()->endOfMonth()];
    }

    public static function confirmedWinsCount(User $user, ?Carbon $at = null, ?int $excludeLeadId = null): int
    {
        [$start, $end] = self::periodRange($user, $at);

        $q = SalesLead::query()
            ->where('assigned_to', $user->id)
            ->where('stage', SalesLead::WON_STAGE)
            ->whereNotNull('won_confirmed_at');

        if ($start && $end) {
            $q->whereBetween('won_confirmed_at', [$start, $end]);
        }

        if ($excludeLeadId) {
            $q->where('id', '!=', $excludeLeadId);
        }

        return (int) $q->count();
    }

    /**
     * حساب عمولة/بونص عند اعتماد win جديد (الترتيب = wins المعتمدة في الفترة + 1).
     *
     * @return array{sale_number:int,rate:float,commission:float,bonus:float,total:float}
     */
    public static function quoteNextWin(User $user, ?Carbon $at = null): array
    {
        $saleNumber = self::confirmedWinsCount($user, $at) + 1;
        $rate = self::rateForSaleNumber($user, $saleNumber);
        $bonus = self::milestoneBonusForSaleNumber($user, $saleNumber);

        return [
            'sale_number' => $saleNumber,
            'rate' => $rate,
            'commission' => $rate,
            'bonus' => $bonus,
            'total' => round($rate + $bonus, 2),
        ];
    }

    /**
     * تفصيل كامل لـ wins معتمدة في الفترة (تدريجي حسب ترتيب الاعتماد).
     *
     * @param  \Illuminate\Support\Collection<int, SalesLead>  $confirmedLeadsOrdered
     * @return array{
     *   wins:int,
     *   current_tier:array|null,
     *   next_sale_number:int,
     *   next_rate:float,
     *   next_bonus:float,
     *   progressive_commission:float,
     *   milestones_bonus:float,
     *   progressive_total:float,
     *   paid_commission:float,
     *   lines:list<array{lead:SalesLead,sale_number:int,rate:float,milestone_bonus:float}>,
     *   tiers:list<array>,
     *   period:string
     * }
     */
    public static function buildBreakdown(User $user, $confirmedLeadsOrdered): array
    {
        $tiers = self::tiersFor($user);
        $lines = [];
        $progressive = 0.0;
        $milestones = 0.0;
        $saleNumber = 0;

        foreach ($confirmedLeadsOrdered as $lead) {
            $saleNumber++;
            $rate = self::rateForSaleNumber($user, $saleNumber);
            $bonus = self::milestoneBonusForSaleNumber($user, $saleNumber);
            $progressive += $rate;
            $milestones += $bonus;
            $lines[] = [
                'lead' => $lead,
                'sale_number' => $saleNumber,
                'rate' => $rate,
                'milestone_bonus' => $bonus,
            ];
        }

        $currentTier = null;
        foreach ($tiers as $tier) {
            $n = max(1, $saleNumber);
            if ($n >= $tier['min'] && ($tier['max'] === null || $n <= $tier['max'])) {
                $currentTier = $tier;
                break;
            }
        }

        $next = $saleNumber + 1;

        return [
            'wins' => $saleNumber,
            'current_tier' => $currentTier,
            'next_sale_number' => $next,
            'next_rate' => self::rateForSaleNumber($user, $next),
            'next_bonus' => self::milestoneBonusForSaleNumber($user, $next),
            'progressive_commission' => round($progressive, 2),
            'milestones_bonus' => round($milestones, 2),
            'progressive_total' => round($progressive + $milestones, 2),
            'paid_commission' => round((float) $confirmedLeadsOrdered->sum('commission_amount'), 2),
            'lines' => $lines,
            'tiers' => $tiers,
            'period' => (string) ($user->sales_commission_tier_period ?? 'month'),
        ];
    }
}
