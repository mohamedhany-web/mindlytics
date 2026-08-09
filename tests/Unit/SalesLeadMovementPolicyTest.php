<?php

namespace Tests\Unit;

use App\Models\SalesLead;
use App\Services\SalesLeadMovementPolicy;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SalesLeadMovementPolicyTest extends TestCase
{
    public function test_open_lead_requires_status_next_action_and_follow_up(): void
    {
        $policy = new SalesLeadMovementPolicy;

        try {
            $policy->assertOpenLeadHasMovement([
                'stage' => 'new_lead',
            ]);
            $this->fail('Expected ValidationException');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('next_follow_up_at', $e->errors());
            $this->assertArrayHasKey('follow_up_channel', $e->errors());
        }
    }

    public function test_past_follow_up_is_rejected_for_open_lead(): void
    {
        $policy = new SalesLeadMovementPolicy;

        $this->expectException(ValidationException::class);

        $policy->assertOpenLeadHasMovement([
            'stage' => 'connected',
            'next_follow_up_at' => now()->subDay()->toDateTimeString(),
            'follow_up_channel' => 'call',
        ]);
    }

    public function test_valid_open_lead_movement_passes(): void
    {
        $policy = new SalesLeadMovementPolicy;

        $policy->assertOpenLeadHasMovement([
            'stage' => 'new_lead',
            'next_follow_up_at' => now()->addDay()->setTime(10, 0)->toDateTimeString(),
            'follow_up_channel' => 'whatsapp',
        ]);

        $this->assertTrue(true);
    }

    public function test_closed_and_won_stages_skip_movement_requirement(): void
    {
        $policy = new SalesLeadMovementPolicy;

        $policy->assertOpenLeadHasMovement(['stage' => 'lost']);
        $policy->assertOpenLeadHasMovement(['stage' => SalesLead::WON_STAGE]);
        $policy->assertOpenLeadHasMovement(['stage' => 'dormant']);

        $this->assertFalse($policy->requiresActiveMovement('lost'));
        $this->assertFalse($policy->requiresActiveMovement(SalesLead::WON_STAGE));
        $this->assertTrue($policy->requiresActiveMovement('new_lead'));
    }

    public function test_with_create_defaults_fills_missing_movement_fields(): void
    {
        $policy = new SalesLeadMovementPolicy;
        $out = $policy->withCreateDefaults([
            'stage' => 'new_lead',
            'name' => 'Test',
        ]);

        $this->assertNotEmpty($out['next_follow_up_at']);
        $this->assertSame('call', $out['follow_up_channel']);
        $this->assertInstanceOf(Carbon::class, Carbon::parse($out['next_follow_up_at']));
    }

    public function test_existing_future_follow_up_is_reused_on_partial_update(): void
    {
        $policy = new SalesLeadMovementPolicy;
        $lead = new SalesLead;
        $lead->forceFill([
            'stage' => 'interested',
            'next_follow_up_at' => now()->addDays(2),
            'follow_up_channel' => 'call',
        ]);

        $policy->assertOpenLeadHasMovement([
            'stage' => 'interested',
            'notes' => 'only notes changed',
        ], $lead);

        $this->assertTrue(true);
    }
}
