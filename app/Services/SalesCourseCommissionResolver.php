<?php

namespace App\Services;

use App\Models\AdvancedCourse;
use App\Models\Course;
use App\Models\OfflineCourse;
use App\Models\SalesCourseCommissionAgreement;
use App\Models\SalesLead;
use App\Models\User;
use Carbon\Carbon;

class SalesCourseCommissionResolver
{
    public function findAgreement(User $rep, SalesLead $lead): ?SalesCourseCommissionAgreement
    {
        $type = (string) ($lead->course_type ?? '');
        $courseId = $lead->linkedCourseId();
        if (! in_array($type, ['advanced', 'offline', 'legacy'], true) || ! $courseId) {
            return null;
        }

        return SalesCourseCommissionAgreement::query()
            ->where('user_id', $rep->id)
            ->where('is_active', true)
            ->where('course_key', SalesCourseCommissionAgreement::makeCourseKey($type, $courseId))
            ->first();
    }

    /**
     * @return array{
     *   source:string,
     *   agreement:?SalesCourseCommissionAgreement,
     *   calc_mode:string,
     *   sale_number:?int,
     *   rate:float,
     *   commission:float,
     *   bonus:float,
     *   total:float,
     *   course_type:?string,
     *   course_id:?int
     * }
     */
    public function quoteForLead(User $rep, SalesLead $lead, ?Carbon $at = null): array
    {
        $agreement = $this->findAgreement($rep, $lead);
        $base = max(0.0, (float) ($lead->expected_value ?? 0));
        $courseType = $lead->course_type;
        $courseId = $lead->linkedCourseId();

        if (! $agreement) {
            return $this->quoteFallback($rep, $base, $courseType, $courseId, $at);
        }

        $mode = (string) $agreement->calc_mode;

        return match ($mode) {
            'fixed' => [
                'source' => 'agreement',
                'agreement' => $agreement,
                'calc_mode' => 'fixed',
                'sale_number' => null,
                'rate' => round(max(0.0, (float) ($agreement->commission_value ?? 0)), 2),
                'commission' => round(max(0.0, (float) ($agreement->commission_value ?? 0)), 2),
                'bonus' => 0.0,
                'total' => round(max(0.0, (float) ($agreement->commission_value ?? 0)), 2),
                'course_type' => $courseType,
                'course_id' => $courseId,
            ],
            'percent' => (function () use ($agreement, $base, $courseType, $courseId) {
                $pct = max(0.0, (float) ($agreement->commission_value ?? 0));
                $amount = round($base * ($pct / 100), 2);

                return [
                    'source' => 'agreement',
                    'agreement' => $agreement,
                    'calc_mode' => 'percent',
                    'sale_number' => null,
                    'rate' => $pct,
                    'commission' => $amount,
                    'bonus' => 0.0,
                    'total' => $amount,
                    'course_type' => $courseType,
                    'course_id' => $courseId,
                ];
            })(),
            'tier_global' => $this->quoteUserTier($rep, $base, $courseType, $courseId, $at, $agreement, 'tier_global'),
            'tier_course' => $this->quoteAgreementTier($rep, $lead, $agreement, $at, true),
            'tier_course_global_count' => $this->quoteAgreementTier($rep, $lead, $agreement, $at, false),
            default => $this->quoteFallback($rep, $base, $courseType, $courseId, $at),
        };
    }

    /**
     * @return array{source:string,agreement:?SalesCourseCommissionAgreement,calc_mode:string,sale_number:?int,rate:float,commission:float,bonus:float,total:float,course_type:?string,course_id:?int}
     */
    private function quoteFallback(User $rep, float $base, ?string $courseType, ?int $courseId, ?Carbon $at): array
    {
        $mode = (string) ($rep->sales_commission_mode ?? 'none');

        if ($mode === 'tier') {
            return $this->quoteUserTier($rep, $base, $courseType, $courseId, $at, null, 'tier');
        }

        $commission = $rep->calculateSalesCommissionAmount($base);

        return [
            'source' => 'user',
            'agreement' => null,
            'calc_mode' => $mode,
            'sale_number' => null,
            'rate' => $commission,
            'commission' => $commission,
            'bonus' => 0.0,
            'total' => $commission,
            'course_type' => $courseType,
            'course_id' => $courseId,
        ];
    }

    /**
     * @return array{source:string,agreement:?SalesCourseCommissionAgreement,calc_mode:string,sale_number:?int,rate:float,commission:float,bonus:float,total:float,course_type:?string,course_id:?int}
     */
    private function quoteUserTier(
        User $rep,
        float $base,
        ?string $courseType,
        ?int $courseId,
        ?Carbon $at,
        ?SalesCourseCommissionAgreement $agreement,
        string $calcMode
    ): array {
        $quote = SalesCommissionTierService::quoteNextWin($rep, $at);

        return [
            'source' => $agreement ? 'agreement' : 'user',
            'agreement' => $agreement,
            'calc_mode' => $calcMode,
            'sale_number' => $quote['sale_number'],
            'rate' => $quote['rate'],
            'commission' => $quote['commission'],
            'bonus' => $quote['bonus'],
            'total' => $quote['total'],
            'course_type' => $courseType,
            'course_id' => $courseId,
        ];
    }

    /**
     * @return array{source:string,agreement:SalesCourseCommissionAgreement,calc_mode:string,sale_number:int,rate:float,commission:float,bonus:float,total:float,course_type:?string,course_id:?int}
     */
    private function quoteAgreementTier(
        User $rep,
        SalesLead $lead,
        SalesCourseCommissionAgreement $agreement,
        ?Carbon $at,
        bool $countPerCourse
    ): array {
        $tiers = $agreement->normalizedTiers();
        $saleNumber = $this->confirmedWinsCount($rep, $lead, $agreement, $countPerCourse, $at) + 1;
        $rate = $this->rateFromTiers($tiers, $saleNumber);
        $bonus = $this->bonusFromTiers($tiers, $saleNumber);

        return [
            'source' => 'agreement',
            'agreement' => $agreement,
            'calc_mode' => $countPerCourse ? 'tier_course' : 'tier_course_global_count',
            'sale_number' => $saleNumber,
            'rate' => $rate,
            'commission' => $rate,
            'bonus' => $bonus,
            'total' => round($rate + $bonus, 2),
            'course_type' => $lead->course_type,
            'course_id' => $lead->linkedCourseId(),
        ];
    }

    public function confirmedWinsCount(
        User $rep,
        SalesLead $lead,
        SalesCourseCommissionAgreement $agreement,
        bool $countPerCourse,
        ?Carbon $at = null,
        ?int $excludeLeadId = null
    ): int {
        $at = $at?->copy() ?? now();
        $period = (string) ($agreement->tier_period ?? 'month');
        $start = null;
        $end = null;
        if ($period !== 'all') {
            $start = $at->copy()->startOfMonth();
            $end = $at->copy()->endOfMonth();
        }

        $q = SalesLead::query()
            ->where('assigned_to', $rep->id)
            ->where('stage', 'won')
            ->whereNotNull('won_confirmed_at');

        if ($start && $end) {
            $q->whereBetween('won_confirmed_at', [$start, $end]);
        }

        if ($countPerCourse) {
            $type = (string) $agreement->course_type;
            $courseId = $agreement->courseId();
            $q->where('course_type', $type);
            match ($type) {
                'advanced' => $q->where('advanced_course_id', $courseId),
                'offline' => $q->where('offline_course_id', $courseId),
                'legacy' => $q->where('course_id', $courseId),
                default => null,
            };
        }

        if ($excludeLeadId) {
            $q->where('id', '!=', $excludeLeadId);
        }

        return (int) $q->count();
    }

    /**
     * @param  list<array{min:int,max:int|null,rate:float,bonus:float,bonus_at:int|null}>  $tiers
     */
    public function rateFromTiers(array $tiers, int $saleNumber): float
    {
        if ($saleNumber < 1) {
            return 0.0;
        }
        foreach ($tiers as $tier) {
            $inMin = $saleNumber >= $tier['min'];
            $inMax = $tier['max'] === null || $saleNumber <= $tier['max'];
            if ($inMin && $inMax) {
                return (float) $tier['rate'];
            }
        }
        $last = collect($tiers)->last();

        return (float) ($last['rate'] ?? 0);
    }

    /**
     * @param  list<array{min:int,max:int|null,rate:float,bonus:float,bonus_at:int|null}>  $tiers
     */
    public function bonusFromTiers(array $tiers, int $saleNumber): float
    {
        foreach ($tiers as $tier) {
            if ($tier['bonus_at'] !== null && (int) $tier['bonus_at'] === $saleNumber) {
                return max(0.0, (float) $tier['bonus']);
            }
        }

        return 0.0;
    }

    /**
     * @return list<array{id:int,title:string,price:float}>
     */
    public static function listCourses(string $type): array
    {
        return match ($type) {
            'advanced' => AdvancedCourse::query()
                ->where('is_active', true)
                ->orderBy('title')
                ->get(['id', 'title', 'price'])
                ->map(fn ($c) => ['id' => (int) $c->id, 'title' => (string) $c->title, 'price' => (float) ($c->price ?? 0)])
                ->all(),
            'offline' => OfflineCourse::query()
                ->where('is_active', true)
                ->orderBy('title')
                ->get(['id', 'title', 'price'])
                ->map(fn ($c) => ['id' => (int) $c->id, 'title' => (string) $c->title, 'price' => (float) ($c->price ?? 0)])
                ->all(),
            'legacy' => Course::query()
                ->orderBy('title')
                ->get(['id', 'title', 'price'])
                ->map(fn ($c) => ['id' => (int) $c->id, 'title' => (string) $c->title, 'price' => (float) ($c->price ?? 0)])
                ->all(),
            default => [],
        };
    }

    public static function resolveCoursePrice(string $type, int $courseId): ?float
    {
        $price = match ($type) {
            'advanced' => AdvancedCourse::query()->whereKey($courseId)->value('price'),
            'offline' => OfflineCourse::query()->whereKey($courseId)->value('price'),
            'legacy' => Course::query()->whereKey($courseId)->value('price'),
            default => null,
        };

        return $price !== null ? (float) $price : null;
    }
}
