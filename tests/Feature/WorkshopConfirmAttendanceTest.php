<?php

namespace Tests\Feature;

use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use App\Services\WorkshopAttendanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopConfirmAttendanceTest extends TestCase
{
    use RefreshDatabase;

    private Workshop $workshop;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workshop = Workshop::create([
            'title' => 'ورشة اختبار',
            'slug' => 'test-workshop-confirm',
            'mode' => 'online',
            'is_active' => true,
        ]);
    }

    public function test_confirm_page_loads(): void
    {
        $this->get(route('public.workshops.confirm.show', $this->workshop->slug))
            ->assertOk()
            ->assertSee('تأكيد حضور الورشة')
            ->assertSee($this->workshop->title);
    }

    public function test_confirms_all_registrations_with_various_phone_formats(): void
    {
        $cases = [
            ['name' => 'أحمد محمد', 'phone' => '01012345678', 'input_phone' => '01012345678'],
            ['name' => 'Sara Ali', 'phone' => '+20 101 234 5679', 'input_phone' => '201012345679'],
            ['name' => 'محمد  حسن', 'phone' => '201012345680', 'input_phone' => '01012345680'],
            ['name' => 'Test User', 'phone' => '01112223344', 'input_phone' => '+201112223344'],
        ];

        foreach ($cases as $case) {
            WorkshopRegistration::create([
                'workshop_id' => $this->workshop->id,
                'name' => $case['name'],
                'phone' => $case['phone'],
                'attendance_mode' => 'online',
                'status' => 'confirmed',
                'checkin_token' => 'token-'.$case['name'],
            ]);
        }

        $service = app(WorkshopAttendanceService::class);

        foreach ($cases as $case) {
            $reg = $service->findRegistration($this->workshop, $case['name'], $case['input_phone']);
            $this->assertNotNull($reg, "Should find registration for {$case['name']}");
            $this->assertSame($case['name'], $reg->name);

            $result = $service->confirmByNameAndPhone($this->workshop, $case['name'], $case['input_phone']);
            $this->assertSame('success', $result['status'], $result['message']);
            $this->assertNotNull($result['registration']?->checked_in_at);
        }
    }

    public function test_post_confirm_marks_checked_in_and_handles_duplicate(): void
    {
        $registration = WorkshopRegistration::create([
            'workshop_id' => $this->workshop->id,
            'name' => 'mohamed hany',
            'phone' => '01203679764',
            'attendance_mode' => 'online',
            'status' => 'confirmed',
            'checkin_token' => 'token-1',
        ]);

        $this->post(route('public.workshops.confirm.store', $this->workshop->slug), [
            'name' => 'mohamed hany',
            'phone' => '01203679764',
        ])->assertSessionHas('success');

        $registration->refresh();
        $this->assertNotNull($registration->checked_in_at);

        $this->post(route('public.workshops.confirm.store', $this->workshop->slug), [
            'name' => 'mohamed hany',
            'phone' => '01203679764',
        ])->assertSessionHas('info');
    }

    public function test_unknown_registration_returns_error(): void
    {
        WorkshopRegistration::create([
            'workshop_id' => $this->workshop->id,
            'name' => 'Known User',
            'phone' => '01000000001',
            'attendance_mode' => 'online',
            'status' => 'confirmed',
            'checkin_token' => 'token-known',
        ]);

        $this->post(route('public.workshops.confirm.store', $this->workshop->slug), [
            'name' => 'Unknown Person',
            'phone' => '01999999999',
        ])->assertSessionHas('error');

        $service = app(WorkshopAttendanceService::class);
        $result = $service->confirmByNameAndPhone($this->workshop, 'Unknown Person', '01999999999');
        $this->assertSame('not_found', $result['status']);
    }

    public function test_name_matching_is_case_insensitive_with_extra_spaces(): void
    {
        WorkshopRegistration::create([
            'workshop_id' => $this->workshop->id,
            'name' => 'CoderMohamed Hany',
            'phone' => '01044610510',
            'attendance_mode' => 'online',
            'status' => 'confirmed',
            'checkin_token' => 'token-2',
        ]);

        $service = app(WorkshopAttendanceService::class);
        $reg = $service->findRegistration($this->workshop, 'codermohamed  hany', '01044610510');
        $this->assertNotNull($reg);

        $result = $service->confirmByNameAndPhone($this->workshop, 'CODERMOHAMED HANY', '201044610510');
        $this->assertSame('success', $result['status']);
    }
}
