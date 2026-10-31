<?php

namespace Tests\Smoke;

use App\Domains\Accounting\Models\Transaction;
use App\Domains\Hr\Models\EmployeeJob;
use App\Domains\Sales\Models\SalesActivity;
use App\Domains\Sales\Models\SalesInterestType;
use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Models\SalesTeam;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

/**
 * موديول المبيعات من أوله لآخره على نسخة MySQL (كل شيء يُتراجع عنه):
 * دورة الـ Lead كاملة حتى اعتماد الصفقة وصرف العمولة، التوزيع والتحويل والمجموعات،
 * التقارير اليومية وتقييم المدير، شات الفريق، تبديل الشيفتات، وإعدادات الأدمن.
 */
class SalesFlowsSmokeTest extends SmokeTestCase
{
    private User $admin;

    private User $rep;

    private User $rep2;

    private User $manager;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();

        $this->admin = User::where('role', 'super_admin')->where('is_active', true)->firstOrFail();
        $salesJob = EmployeeJob::where('code', 'sales')->firstOrFail();
        $this->rep = User::where('employee_job_id', $salesJob->id)->where('is_active', true)->firstOrFail();
        $this->manager = User::where('employee_job_id', EmployeeJob::where('code', 'sales_manager')->value('id'))->where('is_active', true)->firstOrFail();

        // موظف مبيعات ثانٍ في نفس فريق المدير (للتحويل والتوزيع وتبديل الشيفت)
        $this->rep2 = User::forceCreate([
            'name' => 'Smoke Sales Rep 2', 'email' => 'smoke-rep2-'.Str::lower(Str::random(6)).'@example.test',
            'phone' => '+2011'.random_int(10000000, 99999999), 'password' => bcrypt('password'),
            'role' => $this->rep->role, 'is_employee' => true, 'is_active' => true, 'employee_job_id' => $salesJob->id,
        ]);
        $team = SalesTeam::where('manager_id', $this->manager->id)->first();
        if ($team) {
            DB::table('sales_team_members')->insert(['sales_team_id' => $team->id, 'user_id' => $this->rep2->id, 'role' => 'member', 'joined_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
            if (! DB::table('sales_team_members')->where('sales_team_id', $team->id)->where('user_id', $this->rep->id)->exists()) {
                DB::table('sales_team_members')->insert(['sales_team_id' => $team->id, 'user_id' => $this->rep->id, 'role' => 'member', 'joined_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
            }
        }
    }

    public function test_lead_lifecycle_from_creation_to_approved_win_pays_commission_once(): void
    {
        $lead = $this->createLeadAs($this->rep, 'Smoke Lifecycle Lead');
        $this->assertSame($this->rep->id, (int) $lead->assigned_to);
        $this->assertSame('new_lead', $lead->stage);

        $this->ok($this->actingAs($this->rep)->post("/employee/sales/leads/{$lead->id}/quick-activity", ['type' => 'call', 'outcome' => 'interested']));
        $this->assertTrue(SalesActivity::where('sales_lead_id', $lead->id)->where('type', 'call')->exists());

        $this->ok($this->actingAs($this->rep)->post("/employee/sales/leads/{$lead->id}/next-follow", [
            'next_follow_up_at' => now()->addDays(2)->format('Y-m-d H:i'), 'follow_up_channel' => 'whatsapp', 'note' => 'smoke',
        ]));
        $this->ok($this->actingAs($this->rep)->post("/employee/sales/leads/{$lead->id}/activities", ['type' => 'note', 'title' => 'Smoke note', 'body' => 'x']));

        $this->ok($this->actingAs($this->rep)->post("/employee/sales/leads/{$lead->id}/pipeline", [
            'stage' => 'offer_sent', 'offer_price' => 1200, 'notes' => 'smoke: offer sent by phone',
        ]));
        $this->assertSame('offer_sent', $lead->fresh()->stage);

        $this->actingAs($this->rep)->get("/employee/sales/leads/{$lead->id}")->assertOk()->assertSee('Smoke Lifecycle Lead');

        // تغيير المرحلة من نموذج التعديل ممنوع (لازم من الـ Pipeline)
        $this->actingAs($this->rep)->put("/employee/sales/leads/{$lead->id}", $this->leadPayload('Smoke Lifecycle Lead', ['stage' => SalesLead::WON_STAGE, 'phone' => $lead->phone]))
            ->assertSessionHasErrors('stage');

        // الحجز يحتاج ربط كورس
        $this->actingAs($this->rep)->post("/employee/sales/leads/{$lead->id}/pipeline", ['stage' => SalesLead::WON_STAGE, 'expected_value' => 1200, 'notes' => 'smoke: enrolled now'])
            ->assertSessionHasErrors('course_ref_id');

        // الفوز: يصبح بانتظار اعتماد الأدمن
        $this->win($lead);
        $this->ok($this->actingAs($this->rep)->post("/employee/sales/leads/{$lead->id}/csat", ['csat_rating' => 5, 'csat_comment' => 'smoke']));
        $this->assertSame(5, (int) $lead->fresh()->csat_rating);

        $this->actingAs($this->admin)->get('/admin/sales/win-approvals')->assertOk()->assertSee('Smoke Lifecycle Lead');

        $commissionsBefore = Transaction::where('user_id', $this->rep->id)->where('category', 'commission')->count();
        $this->ok($this->actingAs($this->admin)->post("/admin/sales/win-approvals/{$lead->id}/approve", ['commission_amount' => 150, 'commission_notes' => 'smoke', 'override_reason' => 'smoke: paid outside the platform']));
        $this->assertNotNull($lead->fresh()->won_confirmed_at);
        $txns = Transaction::where('user_id', $this->rep->id)->where('category', 'commission')->latest('id')->get();
        $this->assertSame($commissionsBefore + 1, $txns->count());
        $this->assertEquals(150, (float) $txns->first()->amount);
        $this->assertStringStartsWith('SALE-COM-', $txns->first()->transaction_number);
        $this->assertSame(1, Transaction::where('transaction_number', $txns->first()->transaction_number)->count());

        // اعتماد ثانٍ يُرفض ولا يصرف عمولة أخرى
        $this->actingAs($this->admin)->post("/admin/sales/win-approvals/{$lead->id}/approve", ['commission_amount' => 150, 'override_reason' => 'smoke'])->assertSessionHasErrors();
        $this->assertSame($commissionsBefore + 1, Transaction::where('user_id', $this->rep->id)->where('category', 'commission')->count());

        foreach (["/admin/sales/leads/{$lead->id}", '/admin/sales/commissions', '/employee/sales/commissions', '/employee/sales/dashboard', '/employee/sales/reports'] as $url) {
            $actor = str_starts_with($url, '/admin') ? $this->admin : $this->rep;
            $this->assertLessThan(500, $this->actingAs($actor)->get($url)->getStatusCode(), $url);
        }
    }

    public function test_lost_lead_requires_reason_and_rejected_win_pays_nothing(): void
    {
        $lead = $this->createLeadAs($this->rep, 'Smoke Lost Lead');

        $this->actingAs($this->rep)->post("/employee/sales/leads/{$lead->id}/pipeline", ['stage' => 'lost', 'notes' => 'smoke: not interested'])
            ->assertSessionHasErrors('lost_reason');
        $this->ok($this->actingAs($this->rep)->post("/employee/sales/leads/{$lead->id}/pipeline", ['stage' => 'lost', 'lost_reason' => 'price', 'notes' => 'smoke: price too high']));
        $this->assertSame('lost', $lead->fresh()->stage);

        $won = $this->createLeadAs($this->rep, 'Smoke Rejected Win');
        $this->win($won, 500);
        $before = Transaction::where('category', 'commission')->count();
        $this->ok($this->actingAs($this->admin)->post("/admin/sales/win-approvals/{$won->id}/reject", ['rejection_reason' => 'smoke: no payment']));
        $this->assertNotSame(SalesLead::WON_STAGE, $won->fresh()->stage);
        $this->assertSame($before, Transaction::where('category', 'commission')->count());
    }

    public function test_employee_cannot_touch_another_reps_lead(): void
    {
        $lead = $this->createLeadAs($this->rep, 'Smoke Private Lead');
        $this->assertContains($this->actingAs($this->rep2)->get("/employee/sales/leads/{$lead->id}")->getStatusCode(), [403, 404, 302]);
        $this->assertContains($this->actingAs($this->rep2)->post("/employee/sales/leads/{$lead->id}/quick-activity", ['type' => 'note'])->getStatusCode(), [403, 404, 302]);
        $this->assertSame(0, SalesActivity::where('sales_lead_id', $lead->id)->where('user_id', $this->rep2->id)->count());
        $this->assertContains($this->actingAs($this->rep2)->delete("/employee/sales/leads/{$lead->id}")->getStatusCode(), [403, 404, 302]);
        $this->assertNotNull(SalesLead::find($lead->id));
    }

    public function test_distribution_transfer_and_groups(): void
    {
        $a = $this->createLeadAs($this->rep, 'Smoke Dist A');
        $b = $this->createLeadAs($this->rep, 'Smoke Dist B');

        // توزيع أدمن لـ lead واحد
        $this->ok($this->actingAs($this->admin)->post("/admin/sales/distribution/{$a->id}/assign", ['to_user_id' => $this->rep2->id, 'reason' => 'smoke']));
        $this->assertSame($this->rep2->id, (int) $a->fresh()->assigned_to);
        $this->assertTrue(DB::table('sales_lead_transfers')->where('sales_lead_id', $a->id)->exists());

        // تحويل من المدير داخل فريقه
        $this->ok($this->actingAs($this->manager)->post("/employee/sales-manager/leads/{$b->id}/transfer", ['to_user_id' => $this->rep2->id, 'reason' => 'smoke']));
        $this->assertSame($this->rep2->id, (int) $b->fresh()->assigned_to);

        // مجموعة: إنشاء، عرض، PDF، توزيع، استرجاع
        $c = $this->createLeadAs($this->admin, 'Smoke Group Lead', '/admin/sales/leads');
        $c->forceFill(['assigned_to' => null])->save(); // عميل في الـ pool بدون موظف، كما بعد الاستيراد
        $this->ok($this->actingAs($this->admin)->post('/admin/sales/groups', ['name' => 'Smoke Group', 'lead_ids' => [$c->id]]));
        $groupId = DB::table('sales_lead_groups')->where('name', 'Smoke Group')->value('id');
        $this->assertNotNull($groupId);
        foreach (["/admin/sales/groups/{$groupId}", "/admin/sales/groups/{$groupId}/print-pdf", '/admin/sales/groups'] as $url) {
            $this->assertLessThan(500, $this->actingAs($this->admin)->get($url)->getStatusCode(), $url);
        }
        $this->ok($this->actingAs($this->admin)->put("/admin/sales/groups/{$groupId}", ['name' => 'Smoke Group', 'member_ids' => [$this->rep->id], 'lead_ids' => [$c->id]]));
        if ($c->fresh()->assigned_to === null) {
            $this->ok($this->actingAs($this->admin)->post("/admin/sales/groups/{$groupId}/assign", ['employee_id' => $this->rep->id, 'count' => 1]));
        }
        $this->assertSame($this->rep->id, (int) $c->fresh()->assigned_to);
        $this->ok($this->actingAs($this->admin)->post("/admin/sales/groups/{$groupId}/reclaim", ['employee_id' => $this->rep->id]));
        $this->assertNull($c->fresh()->assigned_to, 'reclaim must return the lead to the group pool');
        $this->assertSame($groupId, (int) $c->fresh()->sales_lead_group_id);

        $this->ok($this->actingAs($this->rep)->post('/employee/sales/groups', ['name' => 'Smoke Rep Group']));
        $repGroup = DB::table('sales_lead_groups')->where('name', 'Smoke Rep Group')->value('id');
        $this->assertLessThan(500, $this->actingAs($this->rep)->get("/employee/sales/groups/{$repGroup}")->getStatusCode());
    }

    public function test_daily_report_team_report_and_scorecard_review(): void
    {
        $lead = $this->createLeadAs($this->rep, 'Smoke Report Lead');
        $this->actingAs($this->rep)->post("/employee/sales/leads/{$lead->id}/quick-activity", ['type' => 'call', 'outcome' => 'busy']);

        $this->ok($this->actingAs($this->rep)->post('/employee/sales/daily-reports/sync-auto'));
        $this->ok($this->actingAs($this->rep)->post('/employee/sales/daily-reports', [
            'report_date' => today()->toDateString(), 'action' => 'submit', 'calls_made' => 3, 'calls_answered' => 2, 'followups_done' => 1,
            'messages_replied' => 4, 'leads_qualified' => 1, 'bookings_from_leads' => 0, 'numbers_worked' => 3, 'meetings_held' => 0,
            'activity_notes' => 'smoke', 'productivity_notes' => 'smoke',
            'contacts' => array_map(fn ($i) => [
                'contact_name' => "Smoke contact {$i}", 'contact_phone' => '0100000000'.$i, 'interaction_type' => 'call',
                'client_status' => 'interested', 'client_problems' => 'needs installment plan',
            ], [1, 2, 3]),
        ]));
        $this->assertTrue(DB::table('sales_daily_reports')->where('user_id', $this->rep->id)->whereDate('report_date', today())->exists());

        $this->ok($this->actingAs($this->manager)->post('/employee/sales-manager/team-reports', [
            'report_date' => today()->toDateString(), 'team_summary' => 'smoke team summary', 'submit' => 1,
        ]));
        $this->ok($this->actingAs($this->manager)->post("/employee/sales-manager/scorecard/{$this->rep->id}/review", [
            'date' => today()->toDateString(), 'status' => 'reviewed', 'recommendation' => 'praise', 'manager_notes' => 'smoke',
        ]));

        $teamReport = DB::table('sales_team_daily_reports')->latest('id')->value('id');
        foreach (['/employee/sales-manager/scorecard', '/employee/sales-manager/team-reports', '/admin/sales/daily-reports', '/admin/sales/team-daily-reports'] as $url) {
            $actor = str_starts_with($url, '/admin') ? $this->admin : $this->manager;
            $this->assertLessThan(500, $this->actingAs($actor)->get($url)->getStatusCode(), $url);
        }
        if ($teamReport) {
            $this->assertLessThan(500, $this->actingAs($this->admin)->get("/admin/sales/team-daily-reports/{$teamReport}")->getStatusCode());
        }
    }

    public function test_team_chat_direct_message_reaction_and_delete(): void
    {
        $open = $this->actingAs($this->rep)->postJson('/employee/team-chat/conversations/direct', ['user_id' => $this->manager->id])->assertOk();
        $conversationId = $open->json('conversation.id') ?? $open->json('id') ?? $open->json('conversation_id');
        $this->assertNotNull($conversationId, 'openDirect must return the conversation: '.$open->getContent());

        $sent = $this->actingAs($this->rep)->postJson("/employee/team-chat/conversations/{$conversationId}/messages", ['body' => 'Smoke hello'])->assertOk();
        $messageId = $sent->json('message.id') ?? $sent->json('id');
        $this->assertNotNull($messageId, $sent->getContent());

        $this->actingAs($this->manager)->postJson("/employee/team-chat/messages/{$messageId}/reactions", ['emoji' => '👍'])->assertOk();
        $this->actingAs($this->manager)->postJson("/employee/team-chat/conversations/{$conversationId}/read")->assertOk();
        $this->actingAs($this->manager)->getJson('/employee/team-chat/conversations')->assertOk();

        // موظف ليس طرفاً في المحادثة لا يحذفها؛ صاحب الرسالة يحذفها (والمدير يملك الإشراف على فريقه)
        $this->assertContains($this->actingAs($this->rep2)->deleteJson("/employee/team-chat/messages/{$messageId}")->getStatusCode(), [403, 404]);
        $this->actingAs($this->rep)->deleteJson("/employee/team-chat/messages/{$messageId}")->assertOk();

        $this->actingAs($this->rep)->postJson('/employee/team-chat/conversations/team')->assertOk();
    }

    public function test_admin_settings_crud(): void
    {
        $admin = $this->actingAs($this->admin);

        $this->ok($admin->post('/admin/sales/categories', ['name' => 'Smoke Category', 'color' => '#123456']));
        $category = DB::table('sales_lead_categories')->where('name', 'Smoke Category')->value('id');
        $this->ok($admin->put("/admin/sales/categories/{$category}", ['name' => 'Smoke Category 2']));
        $this->ok($admin->delete("/admin/sales/categories/{$category}"));

        $this->ok($admin->post('/admin/sales/interest-types', ['name_ar' => 'اهتمام تجريبي']));
        $interest = SalesInterestType::where('name_ar', 'اهتمام تجريبي')->value('id');
        $this->ok($admin->put("/admin/sales/interest-types/{$interest}", ['name_ar' => 'اهتمام تجريبي ٢']));
        $this->ok($admin->put("/admin/sales/specialties/{$this->rep->id}", ['interest_type_ids' => [$interest]]));
        $this->ok($admin->delete("/admin/sales/interest-types/{$interest}"));

        $this->ok($admin->post('/admin/sales/policy/sections', ['title' => 'Smoke Section', 'sort_order' => 1]));
        $section = DB::table('sales_policy_sections')->where('title', 'Smoke Section')->value('id');
        $this->ok($admin->post('/admin/sales/policy/rules', ['sales_policy_section_id' => $section, 'section_id' => $section, 'title' => 'Smoke Rule', 'body' => 'x', 'sort_order' => 1]));
        $rule = DB::table('sales_policy_rules')->where('title', 'Smoke Rule')->value('id');
        $this->assertLessThan(500, $admin->get("/admin/sales/policy/rules/{$rule}/edit")->getStatusCode());
        $this->ok($admin->delete("/admin/sales/policy/rules/{$rule}"));
        $this->ok($admin->delete("/admin/sales/policy/sections/{$section}"));

        $newManager = User::forceCreate([
            'name' => 'Smoke Manager 2', 'email' => 'smoke-mgr2-'.Str::lower(Str::random(6)).'@example.test', 'phone' => '+2012'.random_int(10000000, 99999999),
            'password' => bcrypt('password'), 'role' => $this->manager->role, 'is_employee' => true, 'is_active' => true, 'employee_job_id' => $this->manager->employee_job_id,
        ]);
        $this->ok($admin->post('/admin/sales/sales-teams', ['name' => 'Smoke Team', 'manager_id' => $newManager->id, 'member_ids' => [$this->rep2->id]]));
        $team = SalesTeam::where('name', 'Smoke Team')->value('id');
        $this->ok($admin->delete("/admin/sales/sales-teams/{$team}"));

        $this->ok($admin->put("/admin/sales/org-chart/{$this->rep2->id}", ['sales_reports_to_id' => $this->manager->id]));

        $this->ok($admin->post('/admin/sales/day-blocks', ['name' => 'Smoke block', 'code' => 'smoke_'.Str::lower(Str::random(5)), 'start_time' => '10:00', 'end_time' => '11:00', 'activity_type' => 'calls', 'is_active' => 1]));
        $block = DB::table('sales_day_blocks')->where('name', 'Smoke block')->value('id');
        $this->ok($admin->put("/admin/sales/day-blocks/{$block}", ['name' => 'Smoke block 2', 'code' => 'smoke_upd_'.Str::lower(Str::random(5)), 'start_time' => '10:00', 'end_time' => '12:00', 'activity_type' => 'followup', 'is_active' => 1]));
        $this->ok($admin->delete("/admin/sales/day-blocks/{$block}"));

        $this->ok($admin->put('/admin/sales/daily-reports/settings', [
            'enabled' => 1, 'deadline_time' => '23:00', 'penalty_amount' => 50, 'penalty_title' => 'Smoke penalty',
            'penalty_type' => 'penalty', 'penalty_status' => 'pending', 'kpi_submission_target_pct' => 90,
        ]));

        $this->ok($admin->post('/admin/sales-course-board', ['title' => 'Smoke Board', 'name' => 'Smoke Board', 'slug' => 'smoke-board-'.Str::lower(Str::random(5)), 'is_active' => 1]));
        $board = DB::table('sales_course_board_entries')->latest('id')->first();
        if ($board) {
            $this->assertLessThan(500, $admin->get("/admin/sales-course-board/{$board->id}/edit")->getStatusCode());
        }
    }

    public function test_leads_import_from_csv(): void
    {
        $csv = "name,phone,email\nSmoke Import One,01012345671,one@example.test\nSmoke Import Two,01012345672,\n";
        $file = UploadedFile::fake()->createWithContent('leads.csv', $csv);
        $response = $this->actingAs($this->admin)->post('/admin/sales/leads/import', ['file' => $file, 'assigned_to' => $this->rep->id, 'category_id' => DB::table('sales_lead_categories')->value('id')]);
        $this->assertLessThan(500, $response->getStatusCode(), $this->describe($response));
        $this->assertGreaterThanOrEqual(1, SalesLead::where('name', 'like', 'Smoke Import%')->count(), 'import created no leads: '.$this->describe($response));
    }

    private function win(SalesLead $lead, float $value = 1200): void
    {
        $course = DB::table('advanced_courses')->where('is_active', true)->value('id');
        $this->assertNotNull($course, 'the smoke database needs an active course');
        $this->ok($this->actingAs($this->rep)->post("/employee/sales/leads/{$lead->id}/pipeline", [
            'stage' => SalesLead::WON_STAGE, 'expected_value' => $value, 'course_type' => 'advanced', 'course_ref_id' => $course, 'notes' => 'smoke: enrolled and paid',
        ]));
        $lead->refresh();
        $this->assertSame(SalesLead::WON_STAGE, $lead->stage);
        $this->assertNull($lead->won_confirmed_at);
    }

    private function createLeadAs(User $user, string $name, string $url = '/employee/sales/leads'): SalesLead
    {
        $payload = $this->leadPayload($name, $url === '/admin/sales/leads' ? ['assigned_to' => $this->rep->id, 'category_id' => DB::table('sales_lead_categories')->value('id')] : []);
        $this->ok($this->actingAs($user)->post($url, $payload));

        return SalesLead::where('phone', $payload['phone'])->latest('id')->firstOrFail();
    }

    /** @param  array<string, mixed>  $overrides */
    private function leadPayload(string $name, array $overrides = []): array
    {
        return array_merge([
            'name' => $name,
            'phone' => '010'.random_int(10000000, 99999999),
            'source' => array_key_first(SalesLead::SOURCES),
            'stage' => 'new_lead',
            'priority' => 'normal',
            'interest_type_id' => SalesInterestType::query()->value('id'),
            'next_follow_up_at' => now()->addDay()->format('Y-m-d H:i'),
            'follow_up_channel' => 'call',
        ], $overrides);
    }

    private function ok(TestResponse $response): TestResponse
    {
        $this->assertLessThan(500, $response->getStatusCode(), $this->describe($response));
        $this->assertNotContains($response->getStatusCode(), [403, 404, 419, 422], $this->describe($response));
        $errors = session('errors');
        $this->assertTrue(! $errors || $errors->isEmpty(), 'validation errors: '.($errors ? json_encode($errors->getBag('default')->toArray(), JSON_UNESCAPED_UNICODE) : ''));

        return $response;
    }

    private function describe(TestResponse $response): string
    {
        $e = $response->exception;

        return $response->getStatusCode().' '.($e ? get_class($e).': '.mb_substr($e->getMessage(), 0, 300).' @ '.str_replace(base_path().'/', '', $e->getFile()).':'.$e->getLine() : mb_substr(strip_tags((string) $response->getContent()), 0, 200));
    }
}
