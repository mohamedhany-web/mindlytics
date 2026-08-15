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
}
