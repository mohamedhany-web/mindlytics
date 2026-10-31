<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Domains\Learning\Models\AdvancedCourse;
use App\Domains\Accounting\Models\Wallet;
use App\Domains\Accounting\Models\Order;
use App\Domains\Accounting\Models\Invoice;
use App\Domains\Accounting\Models\Payment;
use App\Domains\Accounting\Models\Transaction;
use App\Domains\Accounting\Models\Expense;
use App\Domains\Accounting\Models\Subscription;
use App\Domains\Accounting\Models\InstallmentPlan;
use App\Domains\Accounting\Models\InstallmentAgreement;
use App\Domains\Accounting\Models\InstallmentPayment;
use App\Domains\Learning\Models\StudentCourseEnrollment;
use Carbon\Carbon;

class AccountingSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n💰 إنشاء بيانات نظام المحاسبة المترابطة...\n";
        echo "=" . str_repeat("=", 60) . "\n";

        // الحصول على المستخدمين
        $admin = User::where('role', 'admin')->where('is_active', true)->first();
        $students = User::where('role', 'student')->where('is_active', true)->take(5)->get();
        
        if ($students->isEmpty()) {
            echo "⚠️  لا يوجد طلاب! يرجى إنشاء طلاب أولاً.\n";
            return;
        }

        $adminId = $admin->id ?? 1;

        // الحصول على الكورسات
        $courses = AdvancedCourse::where('is_active', true)->take(5)->get();
        
        if ($courses->isEmpty()) {
            echo "⚠️  لا يوجد كورسات! يرجى إنشاء كورسات أولاً.\n";
            return;
        }

        DB::beginTransaction();

        try {
            // 1. إنشاء محافظ للطلاب
            echo "📱 إنشاء المحافظ...\n";
            $wallets = [];
            foreach ($students as $student) {
                $wallet = Wallet::firstOrCreate(
                    ['user_id' => $student->id],
                    [
                        'name' => 'محفظة ' . $student->name,
                        'type' => 'vodafone_cash',
                        'account_number' => '010' . str_pad($student->id, 8, '0', STR_PAD_LEFT),
                        'bank_name' => null,
                        'account_holder' => $student->name,
                        'is_active' => true,
                        'balance' => rand(0, 5000),
                        'pending_balance' => 0,
                        'currency' => 'EGP',
                    ]
                );
                $wallets[$student->id] = $wallet;
                echo "  ✓ محفظة للطالب: {$student->name}\n";
            }

            // 2. إنشاء طلبات مع الموافقة عليها (Order → Invoice → Payment → Transaction)
            echo "\n🛒 إنشاء طلبات وربطها بالفواتير والمدفوعات...\n";
            $orders = [];
            $invoices = [];
            $payments = [];
            $transactions = [];

            foreach ($students->take(3) as $index => $student) {
                $course = $courses[$index % $courses->count()];
                $amount = $course->price ?? rand(200, 800);

                // إنشاء Order
                $order = Order::create([
                    'user_id' => $student->id,
                    'advanced_course_id' => $course->id,
                    'amount' => $amount,
                    'payment_method' => ['bank_transfer', 'cash', 'other'][rand(0, 2)],
                    'wallet_id' => rand(0, 1) ? $wallets[$student->id]->id : null,
                    'payment_proof' => 'payment-proofs/sample-' . $student->id . '.jpg',
                    'status' => 'approved',
                    'notes' => 'طلب تجريبي لاختبار الترابط',
                    'approved_at' => Carbon::now()->subDays(rand(1, 30)),
                    'approved_by' => $adminId,
                ]);

                // إنشاء Invoice
                $invoice = Invoice::create([
                    'invoice_number' => 'INV-' . str_pad(Invoice::count() + 1, 8, '0', STR_PAD_LEFT),
                    'user_id' => $student->id,
                    'type' => 'course',
                    'description' => 'فاتورة شراء كورس: ' . $course->title,
                    'subtotal' => $amount,
                    'tax_amount' => 0,
                    'discount_amount' => 0,
                    'total_amount' => $amount,
                    'status' => 'paid',
                    'due_date' => Carbon::now()->subDays(rand(1, 10)),
                    'paid_at' => Carbon::now()->subDays(rand(1, 10)),
                    'notes' => 'فاتورة تلقائية من طلب رقم: ' . $order->id,
                    'items' => [
                        [
                            'description' => $course->title,
                            'quantity' => 1,
                            'unit_price' => $amount,
                            'total' => $amount,
                        ]
                    ],
                ]);

                // إنشاء Payment
                $payment = Payment::create([
                    'payment_number' => 'PAY-' . str_pad(Payment::count() + 1, 8, '0', STR_PAD_LEFT),
                    'invoice_id' => $invoice->id,
                    'user_id' => $student->id,
                    'payment_method' => $order->payment_method,
                    'wallet_id' => $order->wallet_id,
                    'amount' => $amount,
                    'currency' => 'EGP',
                    'status' => 'completed',
                    'paid_at' => $invoice->paid_at,
                    'processed_by' => $adminId,
                    'notes' => 'دفعة تلقائية من طلب رقم: ' . $order->id,
                ]);

                // إنشاء Transaction (credit - إيراد)
                $transaction = Transaction::create([
                    'transaction_number' => 'TXN-' . str_pad(Transaction::count() + 1, 8, '0', STR_PAD_LEFT),
                    'user_id' => $student->id,
                    'payment_id' => $payment->id,
                    'invoice_id' => $invoice->id,
                    'expense_id' => null,
                    'subscription_id' => null,
                    'type' => 'credit',
                    'category' => 'course_payment',
                    'amount' => $amount,
                    'currency' => 'EGP',
                    'description' => 'إيراد من شراء كورس: ' . $course->title . ' - فاتورة: ' . $invoice->invoice_number,
                    'status' => 'completed',
                    'metadata' => [
                        'order_id' => $order->id,
                        'invoice_id' => $invoice->id,
                        'payment_id' => $payment->id,
                        'course_id' => $course->id,
                    ],
                    'created_by' => $adminId,
                ]);

                // ربط Order بـ Invoice و Payment
                $order->update([
                    'invoice_id' => $invoice->id,
                    'payment_id' => $payment->id,
                ]);

                // إنشاء Enrollment
                StudentCourseEnrollment::firstOrCreate(
                    [
                        'user_id' => $student->id,
                        'advanced_course_id' => $course->id,
                    ],
                    [
                        'enrolled_at' => Carbon::now()->subDays(rand(1, 20)),
                        'activated_at' => Carbon::now()->subDays(rand(1, 20)),
                        'activated_by' => $adminId,
                        'status' => 'active',
                        'progress' => rand(0, 100),
                        'invoice_id' => $invoice->id,
                        'payment_id' => $payment->id,
                        'original_price' => $amount,
                        'final_price' => $amount,
                        'payment_method' => in_array($order->payment_method, ['bank_transfer', 'cash']) ? $order->payment_method : 'bank_transfer',
                    ]
                );

                $orders[] = $order;
                $invoices[] = $invoice;
                $payments[] = $payment;
                $transactions[] = $transaction;

                echo "  ✓ Order #{$order->id} → Invoice #{$invoice->id} → Payment #{$payment->id} → Transaction #{$transaction->id}\n";
            }

            // 3. إنشاء مصروفات (Expense → Transaction)
            echo "\n💸 إنشاء مصروفات وربطها بالمعاملات...\n";
            $expenses = [];

            $expenseCategories = ['operational', 'marketing', 'salaries', 'utilities', 'equipment', 'maintenance'];
            $expenseTitles = [
                'شراء معدات للقاعة',
                'إعلانات على وسائل التواصل',
                'رواتب الموظفين',
                'فاتورة الكهرباء',
                'صيانة الأجهزة',
                'شراء كتب تعليمية',
            ];

            for ($i = 0; $i < 5; $i++) {
                $expense = Expense::create([
                    'expense_number' => 'EXP-' . str_pad(Expense::count() + 1, 8, '0', STR_PAD_LEFT),
                    'title' => $expenseTitles[$i] ?? 'مصروف تجريبي ' . ($i + 1),
                    'description' => 'مصروف تجريبي لاختبار الترابط في نظام المحاسبة',
                    'category' => $expenseCategories[$i % count($expenseCategories)],
                    'amount' => rand(100, 2000),
                    'currency' => 'EGP',
                    'expense_date' => Carbon::now()->subDays(rand(1, 30)),
                    'payment_method' => ['cash', 'bank_transfer', 'card'][rand(0, 2)],
                    'wallet_id' => rand(0, 1) && !empty($wallets) ? $wallets[array_rand($wallets)]->id : null,
                    'reference_number' => 'REF-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                    'status' => 'approved',
                    'approved_by' => $adminId,
                    'approved_at' => Carbon::now()->subDays(rand(1, 20)),
                    'notes' => 'مصروف تجريبي',
                    'created_by' => $adminId,
                ]);

                // إنشاء Transaction (debit - مصروف)
                $expenseTransaction = Transaction::create([
                    'transaction_number' => 'TXN-' . str_pad(Transaction::count() + 1, 8, '0', STR_PAD_LEFT),
                    'user_id' => $adminId,
                    'payment_id' => null,
                    'invoice_id' => null,
                    'expense_id' => $expense->id,
                    'subscription_id' => null,
                    'type' => 'debit',
                    'category' => 'other',
                    'amount' => $expense->amount,
                    'currency' => 'EGP',
                    'description' => 'مصروف: ' . $expense->title . ' - رقم المصروف: ' . $expense->expense_number,
                    'status' => 'completed',
                    'metadata' => [
                        'expense_id' => $expense->id,
                        'expense_number' => $expense->expense_number,
                        'category' => $expense->category,
                    ],
                    'created_by' => $adminId,
                ]);

                $expense->update(['transaction_id' => $expenseTransaction->id]);
                $expenses[] = $expense;

                echo "  ✓ Expense #{$expense->id} → Transaction #{$expenseTransaction->id}\n";
            }

            // 4. إنشاء اشتراكات (Subscription → Invoice)
            echo "\n📅 إنشاء اشتراكات وربطها بالفواتير...\n";
            $subscriptions = [];

            foreach ($students->skip(3)->take(2) as $student) {
                $subscriptionType = ['monthly', 'quarterly', 'yearly'][rand(0, 2)];
                $price = ['monthly' => 99, 'quarterly' => 249, 'yearly' => 899][$subscriptionType];

                // إنشاء Invoice
                $subscriptionInvoice = Invoice::create([
                    'invoice_number' => 'INV-' . str_pad(Invoice::count() + 1, 8, '0', STR_PAD_LEFT),
                    'user_id' => $student->id,
                    'type' => 'subscription',
                    'description' => 'فاتورة اشتراك: ' . $subscriptionType,
                    'subtotal' => $price,
                    'tax_amount' => 0,
                    'discount_amount' => 0,
                    'total_amount' => $price,
                    'status' => 'pending',
                    'due_date' => Carbon::now()->addDays(30),
                    'notes' => 'فاتورة اشتراك تجريبي',
                    'items' => [
                        [
                            'description' => 'اشتراك ' . $subscriptionType,
                            'quantity' => 1,
                            'price' => $price,
                            'total' => $price,
                        ]
                    ],
                ]);

                // إنشاء Subscription
                $subscription = Subscription::create([
                    'user_id' => $student->id,
                    'subscription_type' => $subscriptionType,
                    'plan_name' => 'خطة ' . $subscriptionType,
                    'price' => $price,
                    'start_date' => Carbon::now(),
                    'end_date' => match($subscriptionType) {
                        'monthly' => Carbon::now()->addMonth(),
                        'quarterly' => Carbon::now()->addMonths(3),
                        'yearly' => Carbon::now()->addYear(),
                        default => Carbon::now()->addMonth(),
                    },
                    'status' => 'active',
                    'auto_renew' => rand(0, 1) === 1,
                    'billing_cycle' => 1,
                    'invoice_id' => $subscriptionInvoice->id,
                ]);

                $subscriptions[] = $subscription;
                echo "  ✓ Subscription #{$subscription->id} → Invoice #{$subscriptionInvoice->id}\n";
            }

            // 5. إنشاء اتفاقيات تقسيط (InstallmentAgreement → InstallmentPayments)
            echo "\n📋 إنشاء اتفاقيات تقسيط...\n";
            
            // الحصول على أو إنشاء InstallmentPlan
            $installmentPlan = InstallmentPlan::first();
            if (!$installmentPlan && !empty($courses)) {
                $course = $courses->first();
                $installmentPlan = InstallmentPlan::create([
                    'name' => 'خطة تقسيط تجريبية',
                    'slug' => 'installment-plan-test',
                    'description' => 'خطة تقسيط تجريبية لاختبار الترابط',
                    'advanced_course_id' => $course->id,
                    'total_amount' => $course->price ?? 500,
                    'deposit_amount' => 100,
                    'installments_count' => 4,
                    'frequency_unit' => 'month',
                    'frequency_interval' => 1,
                    'grace_period_days' => 5,
                    'auto_generate_on_enrollment' => false,
                    'is_active' => true,
                ]);
            }

            if ($installmentPlan && !empty($orders)) {
                $order = $orders[0];
                $enrollment = StudentCourseEnrollment::where('user_id', $order->user_id)
                    ->where('advanced_course_id', $order->advanced_course_id)
                    ->first();

                if ($enrollment) {
                    $agreement = InstallmentAgreement::create([
                        'installment_plan_id' => $installmentPlan->id,
                        'student_course_enrollment_id' => $enrollment->id,
                        'user_id' => $order->user_id,
                        'advanced_course_id' => $order->advanced_course_id,
                        'total_amount' => $order->amount,
                        'deposit_amount' => 100,
                        'installments_count' => 4,
                        'start_date' => Carbon::now(),
                        'status' => 'active',
                        'notes' => 'اتفاقية تقسيط تجريبية',
                        'created_by' => $adminId,
                    ]);

                    $agreement->generateSchedule();

                    // دفع قسط واحد كمثال
                    $firstPayment = $agreement->payments()->first();
                    if ($firstPayment) {
                        $installmentInvoice = Invoice::create([
                            'invoice_number' => 'INV-' . str_pad(Invoice::count() + 1, 8, '0', STR_PAD_LEFT),
                            'user_id' => $order->user_id,
                            'type' => 'course',
                            'description' => 'فاتورة قسط تقسيط - قسط رقم: ' . $firstPayment->sequence_number,
                            'subtotal' => $firstPayment->amount,
                            'tax_amount' => 0,
                            'discount_amount' => 0,
                            'total_amount' => $firstPayment->amount,
                            'status' => 'paid',
                            'due_date' => $firstPayment->due_date,
                            'paid_at' => Carbon::now()->subDays(5),
                            'notes' => 'فاتورة قسط تقسيط',
                            'items' => [
                                [
                                    'description' => 'قسط تقسيط - قسط رقم: ' . $firstPayment->sequence_number,
                                    'quantity' => 1,
                                    'price' => $firstPayment->amount,
                                    'total' => $firstPayment->amount,
                                ]
                            ],
                        ]);

                        $installmentPayment = Payment::create([
                            'payment_number' => 'PAY-' . str_pad(Payment::count() + 1, 8, '0', STR_PAD_LEFT),
                            'invoice_id' => $installmentInvoice->id,
                            'user_id' => $order->user_id,
                            'payment_method' => 'bank_transfer',
                            'amount' => $firstPayment->amount,
                            'currency' => 'EGP',
                            'status' => 'completed',
                            'paid_at' => Carbon::now()->subDays(5),
                            'processed_by' => $adminId,
                            'installment_payment_id' => $firstPayment->id,
                            'notes' => 'دفعة قسط تقسيط',
                        ]);

                        $installmentTransaction = Transaction::create([
                            'transaction_number' => 'TXN-' . str_pad(Transaction::count() + 1, 8, '0', STR_PAD_LEFT),
                            'user_id' => $order->user_id,
                            'payment_id' => $installmentPayment->id,
                            'invoice_id' => $installmentInvoice->id,
                            'expense_id' => null,
                            'subscription_id' => null,
                            'type' => 'credit',
                            'category' => 'course_payment',
                            'amount' => $firstPayment->amount,
                            'currency' => 'EGP',
                            'description' => 'دفعة قسط تقسيط - قسط رقم: ' . $firstPayment->sequence_number,
                            'status' => 'completed',
                            'metadata' => [
                                'installment_agreement_id' => $agreement->id,
                                'installment_payment_id' => $firstPayment->id,
                                'sequence_number' => $firstPayment->sequence_number,
                            ],
                            'created_by' => $adminId,
                        ]);

                        $firstPayment->update(['payment_id' => $installmentPayment->id, 'status' => 'paid', 'paid_at' => Carbon::now()->subDays(5)]);

                        echo "  ✓ InstallmentAgreement #{$agreement->id} → InstallmentPayment #{$firstPayment->id} → Payment #{$installmentPayment->id} → Transaction #{$installmentTransaction->id}\n";
                    }
                }
            }

            // 6. إنشاء مدفوعات إضافية غير مرتبطة بطلبات
            echo "\n💳 إنشاء مدفوعات إضافية...\n";
            if (!empty($invoices)) {
                foreach (array_slice($invoices, 0, 2) as $invoice) {
                    if ($invoice->remaining_amount > 0) {
                        $partialPayment = Payment::create([
                            'payment_number' => 'PAY-' . str_pad(Payment::count() + 1, 8, '0', STR_PAD_LEFT),
                            'invoice_id' => $invoice->id,
                            'user_id' => $invoice->user_id,
                            'payment_method' => 'cash',
                            'amount' => $invoice->remaining_amount,
                            'currency' => 'EGP',
                            'status' => 'completed',
                            'paid_at' => Carbon::now()->subDays(rand(1, 5)),
                            'processed_by' => $adminId,
                            'notes' => 'دفعة إضافية',
                        ]);

                        $partialTransaction = Transaction::create([
                            'transaction_number' => 'TXN-' . str_pad(Transaction::count() + 1, 8, '0', STR_PAD_LEFT),
                            'user_id' => $invoice->user_id,
                            'payment_id' => $partialPayment->id,
                            'invoice_id' => $invoice->id,
                            'expense_id' => null,
                            'subscription_id' => null,
                            'type' => 'credit',
                            'category' => 'course_payment',
                            'amount' => $partialPayment->amount,
                            'currency' => 'EGP',
                            'description' => 'دفعة إضافية للفاتورة: ' . $invoice->invoice_number,
                            'status' => 'completed',
                            'metadata' => [
                                'invoice_id' => $invoice->id,
                                'payment_id' => $partialPayment->id,
                            ],
                            'created_by' => $adminId,
                        ]);

                        echo "  ✓ Payment #{$partialPayment->id} → Transaction #{$partialTransaction->id} للفاتورة #{$invoice->id}\n";
                    }
                }
            }

            DB::commit();

            echo "\n✅ تم إنشاء بيانات نظام المحاسبة بنجاح!\n";
            echo "=" . str_repeat("=", 60) . "\n";
            echo "📊 الإحصائيات:\n";
            echo "  • الطلبات (Orders): " . Order::count() . "\n";
            echo "  • الفواتير (Invoices): " . Invoice::count() . "\n";
            echo "  • المدفوعات (Payments): " . Payment::count() . "\n";
            echo "  • المعاملات المالية (Transactions): " . Transaction::count() . "\n";
            echo "  • المصروفات (Expenses): " . Expense::count() . "\n";
            echo "  • الاشتراكات (Subscriptions): " . Subscription::count() . "\n";
            echo "  • المحافظ (Wallets): " . Wallet::count() . "\n";
            echo "\n✨ جميع البيانات مترابطة بشكل صحيح!\n";

        } catch (\Exception $e) {
            DB::rollBack();
            echo "\n❌ خطأ: " . $e->getMessage() . "\n";
            echo "📍 السطر: " . $e->getLine() . " في " . $e->getFile() . "\n";
            throw $e;
        }
    }
}
