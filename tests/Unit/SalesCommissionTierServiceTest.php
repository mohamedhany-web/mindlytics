<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\SalesCommissionTierService;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SalesCommissionTierServiceTest extends TestCase
{
    private function tierUser(): User
    {
        $user = new User;
        $user->forceFill([
            'sales_commission_mode' => 'tier',
            'sales_commission_tiers' => null,
            'sales_commission_tier_period' => 'month',
        ]);

        return $user;
    }

    public function test_default_tiers_match_proposal_table(): void
    {
        $tiers = SalesCommissionTierService::defaultTiers();

        $this->assertCount(5, $tiers);
        $this->assertSame(1, $tiers[0]['min']);
        $this->assertSame(9, $tiers[0]['max']);
        $this->assertSame(50.0, $tiers[0]['rate']);
        $this->assertSame(0.0, $tiers[0]['bonus']);

        $this->assertSame(10, $tiers[1]['bonus_at']);
        $this->assertSame(500.0, $tiers[1]['bonus']);
        $this->assertSame(60.0, $tiers[1]['rate']);

        $this->assertSame(20, $tiers[2]['bonus_at']);
        $this->assertSame(1500.0, $tiers[2]['bonus']);

        $this->assertSame(30, $tiers[3]['bonus_at']);
        $this->assertSame(3000.0, $tiers[3]['bonus']);

        $this->assertNull($tiers[4]['max']);
        $this->assertSame(40, $tiers[4]['bonus_at']);
        $this->assertSame(5000.0, $tiers[4]['bonus']);
        $this->assertSame(100.0, $tiers[4]['rate']);
    }

    public static function rateProvider(): array
    {
        return [
            [1, 50.0],
            [9, 50.0],
            [10, 60.0],
            [19, 60.0],
            [20, 70.0],
            [29, 70.0],
            [30, 80.0],
            [39, 80.0],
            [40, 100.0],
            [55, 100.0],
        ];
    }

    #[DataProvider('rateProvider')]
    public function test_rate_for_sale_number(int $saleNumber, float $expected): void
    {
        $this->assertSame(
            $expected,
            SalesCommissionTierService::rateForSaleNumber($this->tierUser(), $saleNumber)
        );
    }

    public static function bonusProvider(): array
    {
        return [
            [1, 0.0],
            [9, 0.0],
            [10, 500.0],
            [11, 0.0],
            [20, 1500.0],
            [30, 3000.0],
            [40, 5000.0],
            [41, 0.0],
        ];
    }

    #[DataProvider('bonusProvider')]
    public function test_milestone_bonus(int $saleNumber, float $expected): void
    {
        $this->assertSame(
            $expected,
            SalesCommissionTierService::milestoneBonusForSaleNumber($this->tierUser(), $saleNumber)
        );
    }

    public function test_normalize_empty_falls_back_to_defaults(): void
    {
        $this->assertSame(
            SalesCommissionTierService::defaultTiers(),
            SalesCommissionTierService::normalizeTiers([])
        );
    }

    public function test_user_label_and_calc_for_tier(): void
    {
        $user = $this->tierUser();
        $this->assertSame('Tier System', $user->salesCommissionLabel());
        // بدون wins في الذاكرة: البيع التالي = 1 → 50 (يتطلب DB للعد؛ هنا نختبر الـ rate فقط عبر quote بعد mock count)
        $this->assertSame(50.0, SalesCommissionTierService::rateForSaleNumber($user, 1));
    }

    public function test_build_breakdown_progressive_totals(): void
    {
        $user = $this->tierUser();
        $leads = collect(range(1, 10))->map(fn ($i) => (object) [
            'id' => $i,
            'commission_amount' => 0,
        ]);

        // buildBreakdown يتوقع SalesLead models مع sum('commission_amount')
        $fakeLeads = collect(range(1, 10))->map(function ($i) {
            $lead = new \App\Models\SalesLead;
            $lead->forceFill(['id' => $i, 'commission_amount' => 0]);
            $lead->id = $i;

            return $lead;
        });

        $breakdown = SalesCommissionTierService::buildBreakdown($user, $fakeLeads);

        $this->assertSame(10, $breakdown['wins']);
        // 9×50 + 1×60 = 450+60 = 510
        $this->assertSame(510.0, $breakdown['progressive_commission']);
        $this->assertSame(500.0, $breakdown['milestones_bonus']);
        $this->assertSame(1010.0, $breakdown['progressive_total']);
        $this->assertSame(11, $breakdown['next_sale_number']);
        $this->assertSame(60.0, $breakdown['next_rate']);
        $this->assertSame(0.0, $breakdown['next_bonus']);
        $this->assertSame(10, $breakdown['lines'][9]['sale_number']);
        $this->assertSame(60.0, $breakdown['lines'][9]['rate']);
        $this->assertSame(500.0, $breakdown['lines'][9]['milestone_bonus']);
    }
}
