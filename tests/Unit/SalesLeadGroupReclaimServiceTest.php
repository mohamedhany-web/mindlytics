<?php

namespace Tests\Unit;

use App\Services\SalesLeadGroupReclaimService;
use Tests\TestCase;

class SalesLeadGroupReclaimServiceTest extends TestCase
{
    public function test_removed_member_ids_are_the_ones_no_longer_in_the_group(): void
    {
        $service = new SalesLeadGroupReclaimService;

        $this->assertSame(
            [7, 9],
            $service->removedMemberIds([3, 7, 9], [3, 5])
        );
    }

    public function test_removed_member_ids_empty_when_membership_unchanged(): void
    {
        $service = new SalesLeadGroupReclaimService;

        $this->assertSame([], $service->removedMemberIds([4, 8], [8, 4]));
    }

    public function test_select_lead_ids_by_count(): void
    {
        $service = new SalesLeadGroupReclaimService;
        $ids = [10, 20, 30, 40, 50];

        $this->assertSame([10, 20], $service->selectLeadIds($ids, 2, null, null));
        $this->assertSame($ids, $service->selectLeadIds($ids, 99, null, null));
    }

    public function test_select_lead_ids_by_range(): void
    {
        $service = new SalesLeadGroupReclaimService;
        $ids = [10, 20, 30, 40, 50];

        $this->assertSame([10, 20, 30], $service->selectLeadIds($ids, 99, 1, 3));
        $this->assertSame([40, 50], $service->selectLeadIds($ids, null, 4, 5));
        $this->assertSame([30, 40, 50], $service->selectLeadIds($ids, null, 3, null));
        $this->assertSame([10, 20], $service->selectLeadIds($ids, null, null, 2));
    }

    public function test_select_lead_ids_requires_count_or_range(): void
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        (new SalesLeadGroupReclaimService)->selectLeadIds([1, 2, 3], null, null, null);
    }
}
