<?php

namespace Tests\Smoke;

use App\Domains\Accounting\Models\Order;
use App\Domains\Accounting\Models\Transaction;
use App\Domains\Accounting\Models\Wallet;
use App\Domains\Accounting\Services\TransactionRefundService;
use App\Domains\Hr\Models\EmployeeJob;
use App\Domains\Hr\Models\EmployeeSalaryDeduction;
use App\Domains\Learning\Models\AdvancedCourse;
use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Services\SalesCommissionSettlementService;
use App\Domains\Sales\Services\SalesDailyKpiPenaltyService;
use App\Domains\Sales\Services\SalesWinCommissionService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;

/**
 * سلامة المبيعات مع الفلوس (docs/SALES_INTEGRITY.md) على نسخة MySQL، كل شيء يُتراجع عنه:
 * الطلب المعتمد يربط الـ Lead تلقائياً، العمولة على المدفوع فعلاً، الاسترداد يسترجع العمولة،
 * لا عمولتين لنفس الصفقة، رقم الهاتف له مالك واحد، وخصم الـ KPI مقترح لا تلقائي.
 */
class SalesIntegritySmokeTest extends SmokeTestCase
{
    private User $admin;

    private User $rep;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        $this->admin = User::where('role', 'super_admin')->where('is_active', true)->firstOrFail();
        $this->rep = User::where('employee_job_id', EmployeeJob::where('code', 'sales')->value('id'))->where('is_active', true)->firstOrFail();
    }

    public function test_approved_order_links_the_lead_and_commission_uses_the_paid_amount(): void
    {
        [$student, $digits] = $this->student();
        $lead = $this->lead('010'.$digits, ['stage' => 'offer_sent', 'expected_value' => 5000]); // الموظف كتب 5000
        $order = $this->approvedOrder($student, 800);

        $lead->refresh();
        $this->assertSame($order->id, (int) $lead->order_id, 'the order must be linked to the lead (phone +20… vs 010…)');
        $this->assertSame($student->id, (int) $lead->student_user_id);
        $this->assertSame(SalesLead::WON_STAGE, $lead->stage);
        $this->assertEquals(800, (float) $lead->expected_value);
        $this->assertNull($lead->won_confirmed_at, 'commission still needs the admin approval');

        $this->ok($this->actingAs($this->admin)->post("/admin/sales/win-approvals/{$lead->id}/approve", []));
        $lead->refresh();
        $this->assertNotNull($lead->won_confirmed_at);
        $txn = Transaction::findOrFail($lead->commission_transaction_id);
        $this->assertSame($order->id, $txn->metadata['order_id'] ?? null);
        $this->assertEquals(800, (float) ($txn->metadata['expected_value'] ?? 0), 'commission base is the paid amount, not the typed 5000');
    }

    public function test_win_without_a_paid_order_needs_an_order_number_or_a_written_reason(): void
    {
        $lead = $this->lead('010'.random_int(10000000, 99999999), ['stage' => SalesLead::WON_STAGE, 'expected_value' => 1000]);

        $this->actingAs($this->admin)->post("/admin/sales/win-approvals/{$lead->id}/approve", ['commission_amount' => 100])->assertSessionHasErrors();
        $this->assertNull($lead->fresh()->won_confirmed_at);

        $this->ok($this->actingAs($this->admin)->post("/admin/sales/win-approvals/{$lead->id}/approve", ['commission_amount' => 100, 'override_reason' => 'دفع كاش في المقر']));
        $lead->refresh();
        $this->assertNotNull($lead->won_confirmed_at);
        $this->assertSame('دفع كاش في المقر', $lead->won_override_reason);

        // ربط يدوي برقم الطلب
        [$student] = $this->student();
        $order = $this->approvedOrder($student, 600);
        $other = $this->lead('011'.random_int(10000000, 99999999), ['stage' => SalesLead::WON_STAGE, 'expected_value' => 99]);
        $this->ok($this->actingAs($this->admin)->post("/admin/sales/win-approvals/{$other->id}/approve", ['order_id' => $order->id]));
        $other->refresh();
        $this->assertSame($order->id, (int) $other->order_id);
        $this->assertEquals(600, (float) $other->expected_value);
        $this->assertNotNull($other->won_confirmed_at);
    }

    public function test_two_stale_approvals_pay_the_commission_once(): void
    {
        $lead = $this->lead('010'.random_int(10000000, 99999999), ['stage' => SalesLead::WON_STAGE, 'expected_value' => 1000]);
        Auth::login($this->admin);
        $a = SalesLead::find($lead->id);
        $b = SalesLead::find($lead->id); // ضغطتان متزامنتان: نسختان قبل أي اعتماد
        $service = app(SalesWinCommissionService::class);

        $this->assertTrue($service->approveAndPayCommission($a, 100.0, null, 'smoke')['success']);
        $this->assertFalse($service->approveAndPayCommission($b, 100.0, null, 'smoke')['success']);
        $this->assertSame(1, Transaction::where('category', 'commission')->where('type', 'credit')->where('metadata->sales_lead_id', $lead->id)->count());
    }

    public function test_refund_reverses_commission_before_and_after_settlement(): void
    {
        [$student, $digits] = $this->student();
        $lead = $this->lead('010'.$digits, ['stage' => 'interested']);
        $order = $this->approvedOrder($student, 1000);
        $this->ok($this->actingAs($this->admin)->post("/admin/sales/win-approvals/{$lead->fresh()->id}/approve", ['commission_amount' => 200]));
        $lead->refresh();
        $this->assertEquals(200, (float) $lead->commission_amount);

        // استرداد جزئي 40٪ قبل المخالصة ← تُسترجع 80 والمخالصة تدفع 120
        $this->refund($order, 400);
        $lead->refresh();
        $this->assertEquals(80, (float) $lead->commission_reversed_amount);
        $this->assertTrue(Transaction::where('category', 'commission')->where('type', 'debit')->where('metadata->sales_lead_id', $lead->id)->where('amount', 80)->exists());
        $settled = app(SalesCommissionSettlementService::class)->settle($this->rep, [$lead->id], 'smoke', $this->admin->id);
        $this->assertTrue($settled['success'], $settled['error'] ?? '');
        $this->assertEquals(120, (float) $settled['amount']);

        // بعد المخالصة: الأدمن يعلّم الدفعة «مستردة» بالكامل من شاشة الدفعات ← 120 أخرى كخصم «معلّق» تعتمده الموارد البشرية
        $payment = $order->payment()->firstOrFail();
        $this->ok($this->actingAs($this->admin)->put("/admin/payments/{$payment->id}", [
            'invoice_id' => $payment->invoice_id, 'user_id' => $payment->user_id, 'payment_method' => 'bank_transfer',
            'amount' => $payment->amount, 'status' => 'refunded', 'notes' => 'smoke full refund',
        ]));
        $lead->refresh();
        $this->assertEquals(200, (float) $lead->commission_reversed_amount);
        $deduction = EmployeeSalaryDeduction::where('employee_id', $this->rep->id)->where('notes', 'like', "%lead:{$lead->id} %")->latest('id')->first();
        $this->assertNotNull($deduction, 'a settled commission is recovered through a pending HR deduction');
        $this->assertSame('pending', $deduction->status);
        $this->assertEquals(120, (float) $deduction->amount);
    }

    public function test_phone_has_one_owner_across_reps(): void
    {
        $digits = (string) random_int(10000000, 99999999);
        $existing = $this->lead('010'.$digits, ['stage' => 'interested']);
        $rep2 = User::forceCreate([
            'name' => 'Smoke Rep Dup', 'email' => 'smoke-dup-'.Str::lower(Str::random(6)).'@example.test', 'phone' => '+2012'.random_int(10000000, 99999999),
            'password' => bcrypt('password'), 'role' => $this->rep->role, 'is_employee' => true, 'is_active' => true, 'employee_job_id' => $this->rep->employee_job_id,
        ]);

        // نفس الرقم بصيغة دولية عند موظف آخر ← مرفوض مع اسم المالك
        $this->actingAs($rep2)->post('/employee/sales/leads', $this->payload('+20 10'.$digits))
            ->assertSessionHasErrors('phone');
        $this->assertSame(1, SalesLead::where('phone', 'like', '%'.$digits)->count());

        // نفس الموظف: تنبيه فقط (يُسمح)
        $this->ok($this->actingAs($this->rep)->post('/employee/sales/leads', $this->payload('0020 10'.$digits)));

        // الـ Lead الخاسر لا يحجز الرقم
        SalesLead::where('phone', 'like', '%'.$digits)->update(['stage' => 'lost']);
        $this->ok($this->actingAs($rep2)->post('/employee/sales/leads', $this->payload('010'.$digits)));
        $this->assertSame($rep2->id, (int) SalesLead::where('phone', '010'.$digits)->where('stage', '!=', 'lost')->value('assigned_to'));
    }

    public function test_kpi_penalty_is_a_proposal_until_approved(): void
    {
        $service = app(SalesDailyKpiPenaltyService::class);
        $method = new \ReflectionMethod($service, 'createPenaltyIfNeeded');
        $date = now()->subDays(40)->startOfDay();
        $deduction = $method->invoke($service, $this->rep, $date, 'smoke_metric', 10.0, 100.0, 10.0, ['amount' => 25.0, 'title' => 'Smoke KPI']);

        $this->assertNotNull($deduction);
        $this->assertSame('pending', $deduction->status, 'KPI deductions must wait for a manager/HR approval');
    }

    /** @return array{0: User, 1: string} */
    private function student(): array
    {
        $digits = (string) random_int(10000000, 99999999);

        return [User::forceCreate([
            'name' => 'Smoke Buyer', 'email' => 'smoke-buyer-'.Str::lower(Str::random(6)).'@example.test', 'phone' => '+2010'.$digits,
            'password' => bcrypt('password'), 'role' => 'student', 'is_active' => true,
        ]), $digits];
    }

    /** @param  array<string, mixed>  $attrs */
    private function lead(string $phone, array $attrs = []): SalesLead
    {
        $lead = new SalesLead;
        $lead->forceFill(array_merge([
            'name' => 'Smoke Integrity Lead', 'phone' => $phone, 'source' => 'website', 'stage' => 'new_lead', 'priority' => 'normal',
            'assigned_to' => $this->rep->id, 'created_by' => $this->rep->id, 'last_contacted_at' => now(),
            'interest_type_id' => DB::table('sales_interest_types')->value('id'), 'category_id' => DB::table('sales_lead_categories')->value('id'),
        ], $attrs))->save();

        return $lead;
    }

    private function approvedOrder(User $student, float $amount): Order
    {
        $course = AdvancedCourse::where('is_active', true)->firstOrFail();
        $wallet = Wallet::create(['name' => 'Smoke Wallet '.Str::random(4), 'type' => 'vodafone_cash', 'balance' => 0, 'is_active' => true]);
        $order = Order::forceCreate([
            'user_id' => $student->id, 'advanced_course_id' => $course->id, 'original_amount' => $amount, 'discount_amount' => 0, 'amount' => $amount,
            'payment_method' => 'bank_transfer', 'wallet_id' => $wallet->id, 'status' => Order::STATUS_PENDING,
        ]);
        $this->actingAs($this->admin)->postJson("/admin/orders/{$order->id}/approve")->assertOk()->assertJson(['success' => true]);

        return $order->fresh();
    }

    private function refund(Order $order, float $amount): void
    {
        $txn = Transaction::where('payment_id', $order->payment_id)->where('type', 'credit')->where('category', '!=', 'commission')
            ->where('status', '!=', 'reversed')->latest('id')->first()
            ?? Transaction::where('payment_id', $order->payment_id)->where('type', 'credit')->latest('id')->firstOrFail();
        app(TransactionRefundService::class)->process($txn, $amount, 'smoke refund', $this->admin->id);
    }

    /** @return array<string, mixed> */
    private function payload(string $phone): array
    {
        return [
            'name' => 'Smoke Dup Lead', 'phone' => $phone, 'source' => 'website', 'stage' => 'new_lead', 'priority' => 'normal',
            'interest_type_id' => DB::table('sales_interest_types')->value('id'),
            'next_follow_up_at' => now()->addDay()->format('Y-m-d H:i'), 'follow_up_channel' => 'call',
        ];
    }

    private function ok(\Illuminate\Testing\TestResponse $response): void
    {
        $this->assertLessThan(500, $response->getStatusCode(), $response->exception?->getMessage() ?? '');
        $errors = session('errors');
        $this->assertTrue(! $errors || $errors->isEmpty(), 'errors: '.($errors ? json_encode($errors->getBag('default')->toArray(), JSON_UNESCAPED_UNICODE) : ''));
    }
}
