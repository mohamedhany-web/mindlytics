<?php

namespace Tests\Unit;

use App\Models\SalesLead;
use App\Models\User;
use App\Services\SalesPipelineService;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SalesPipelineServiceTest extends TestCase
{
    private function leadAt(string $stage): SalesLead
    {
        $lead = new SalesLead;
        $lead->forceFill([
            'id' => 1,
            'stage' => $stage,
            'name' => 'Test Lead',
            'contact_attempts' => 0,
            'course_type' => null,
            'advanced_course_id' => null,
            'offline_course_id' => null,
            'course_id' => null,
            'next_follow_up_at' => now()->addDay(),
            'follow_up_channel' => 'call',
        ]);

        return $lead;
    }

    public function test_new_lead_can_jump_to_booking_or_offer(): void
    {
        $service = new SalesPipelineService;
        $lead = $this->leadAt('new_lead');
        $allowed = $service->allowedNextStages($lead);

        $this->assertContains('first_contact', $allowed);
        $this->assertContains('connected', $allowed);
        $this->assertContains('offer_sent', $allowed);
        $this->assertContains('payment_pending', $allowed);
        $this->assertContains('payment_received', $allowed);
        $this->assertContains(SalesLead::WON_STAGE, $allowed);
    }

    public function test_connected_can_fast_track_to_offer_or_booking(): void
    {
        $service = new SalesPipelineService;
        $lead = $this->leadAt('connected');
        $allowed = $service->allowedNextStages($lead);

        $this->assertContains('offer_sent', $allowed);
        $this->assertContains('interested', $allowed);
        $this->assertContains('payment_pending', $allowed);
        $this->assertContains('payment_received', $allowed);
    }

    public function test_interested_can_jump_to_payment(): void
    {
        $service = new SalesPipelineService;
        $lead = $this->leadAt('interested');
        $allowed = $service->allowedNextStages($lead);

        $this->assertContains('offer_sent', $allowed);
        $this->assertContains('payment_pending', $allowed);
        $this->assertContains('payment_received', $allowed);
        $this->assertContains(SalesLead::WON_STAGE, $allowed);
    }

    public function test_outcome_actions_surface_booking_from_first_contact(): void
    {
        $service = new SalesPipelineService;
        $lead = $this->leadAt('new_lead');
        $stages = collect($service->outcomeActions($lead))->pluck('stage')->all();

        $this->assertContains('payment_pending', $stages);
        $this->assertContains('follow_up_scheduled', $stages);
        $this->assertContains('connected', $stages);
    }

    public function test_transition_requires_notes(): void
    {
        $service = new SalesPipelineService;
        $lead = $this->leadAt('new_lead');
        $actor = new User;
        $actor->forceFill(['id' => 9, 'name' => 'Rep']);

        $this->expectException(ValidationException::class);

        $service->transition($lead, 'first_contact', [
            'call_answered' => '1',
            'notes' => 'قص',
            'next_follow_up_at' => now()->addDay()->toDateTimeString(),
            'follow_up_channel' => 'call',
        ], $actor);
    }

    public function test_booking_stages_require_course_link(): void
    {
        $service = new SalesPipelineService;
        $lead = $this->leadAt('offer_sent');
        $actor = new User;
        $actor->forceFill(['id' => 9, 'name' => 'Rep']);

        try {
            $service->transition($lead, 'payment_pending', [
                'notes' => 'العميل وافق ويريد الدفع الآن فوراً',
                'payment_method' => 'instapay',
                'payment_amount' => 1000,
                'payment_due_at' => now()->addDay()->toDateTimeString(),
                'next_follow_up_at' => now()->addDay()->toDateTimeString(),
                'follow_up_channel' => 'call',
            ], $actor);
            $this->fail('Expected ValidationException for missing course');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('course_ref_id', $e->errors());
        }
    }

    public function test_journey_buckets_collapse_long_pipeline(): void
    {
        $service = new SalesPipelineService;
        $this->assertSame('entered', $service->bucketForStage('new_lead'));
        $this->assertSame('contacted', $service->bucketForStage('connected'));
        $this->assertSame('payment', $service->bucketForStage('payment_pending'));
        $this->assertSame('won', $service->bucketForStage('enrollment_completed'));
        $this->assertCount(7, $service->journeyBuckets());
    }
}
