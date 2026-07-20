<?php

namespace Tests\Unit;

use App\Models\SalesCourseCommissionAgreement;
use App\Models\SalesLead;
use App\Models\User;
use App\Services\SalesCommissionTierService;
use App\Services\SalesCourseCommissionResolver;
use Tests\TestCase;

class SalesCourseCommissionResolverTest extends TestCase
{
    public function test_rate_and_bonus_from_tiers(): void
    {
        $resolver = new SalesCourseCommissionResolver;
        $tiers = SalesCommissionTierService::defaultTiers();

        $this->assertSame(50.0, $resolver->rateFromTiers($tiers, 1));
        $this->assertSame(60.0, $resolver->rateFromTiers($tiers, 10));
        $this->assertSame(500.0, $resolver->bonusFromTiers($tiers, 10));
        $this->assertSame(0.0, $resolver->bonusFromTiers($tiers, 11));
    }

    public function test_make_course_key(): void
    {
        $this->assertSame('advanced:12', SalesCourseCommissionAgreement::makeCourseKey('advanced', 12));
        $this->assertSame('offline:3', SalesCourseCommissionAgreement::makeCourseKey('offline', 3));
    }

    public function test_fallback_fixed_without_agreement(): void
    {
        $rep = new User;
        $rep->forceFill([
            'sales_commission_mode' => 'fixed',
            'sales_commission_value' => 40,
        ]);
        $rep->id = 999001;

        $lead = new SalesLead;
        $lead->forceFill(['expected_value' => 1000]);

        $quote = (new SalesCourseCommissionResolver)->quoteForLead($rep, $lead);
        $this->assertSame('user', $quote['source']);
        $this->assertSame(40.0, $quote['total']);
    }

    public function test_calc_mode_labels_cover_all_options(): void
    {
        $this->assertArrayHasKey('tier_course', SalesCourseCommissionAgreement::CALC_MODES);
        $this->assertArrayHasKey('tier_course_global_count', SalesCourseCommissionAgreement::CALC_MODES);
        $this->assertArrayHasKey('tier_global', SalesCourseCommissionAgreement::CALC_MODES);
        $this->assertArrayHasKey('fixed', SalesCourseCommissionAgreement::CALC_MODES);
        $this->assertArrayHasKey('percent', SalesCourseCommissionAgreement::CALC_MODES);
        $this->assertCount(3, SalesCourseCommissionAgreement::COURSE_TYPES);
    }
}
