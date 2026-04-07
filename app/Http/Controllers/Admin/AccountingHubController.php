<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstallmentAgreement;
use App\Models\InstallmentPayment;
use App\Models\Invoice;
use App\Models\OfflineCourseEnrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\WithdrawalRequest;
use Carbon\Carbon;
use Illuminate\View\View;

class AccountingHubController extends Controller
{
    public function hub(): View
    {
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $snapshot = [
            'month_label' => $monthStart->translatedFormat('F Y'),
            'revenue_month' => (float) Payment::where('status', 'completed')
                ->whereBetween('paid_at', [$monthStart, $monthEnd])
                ->sum('amount'),
            'pending_invoices' => Invoice::where('status', 'pending')->count(),
            'pending_invoices_amount' => (float) Invoice::where('status', 'pending')->sum('total_amount'),
            'pending_orders' => Order::where('status', Order::STATUS_PENDING)->count(),
            'pending_withdrawals' => WithdrawalRequest::where('status', WithdrawalRequest::STATUS_PENDING)->count(),
            'pending_withdrawals_amount' => (float) WithdrawalRequest::where('status', WithdrawalRequest::STATUS_PENDING)->sum('amount'),
            'active_subscriptions' => Subscription::where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('end_date')->orWhere('end_date', '>=', now()->toDateString());
                })
                ->count(),
            'installment_agreements_active' => InstallmentAgreement::whereIn('status', [
                InstallmentAgreement::STATUS_ACTIVE,
                InstallmentAgreement::STATUS_OVERDUE,
            ])->count(),
            'installment_pending_total' => (float) InstallmentPayment::where('status', InstallmentPayment::STATUS_PENDING)->sum('amount'),
            'offline_paid_month' => (float) OfflineCourseEnrollment::whereBetween('enrolled_at', [$monthStart, $monthEnd])
                ->sum('paid_amount'),
        ];

        $sections = $this->hubSections();

        return view('admin.accounting.hub', compact('snapshot', 'sections'));
    }

    public function chart(): View
    {
        $chart = config('accounting_chart', []);

        return view('admin.accounting.chart', compact('chart'));
    }

    /**
     * @return array<int, array{title: string, items: array<int, array{label: string, route: string, icon: string, hint?: string}>}>
     */
    private function hubSections(): array
    {
        return [
            [
                'title' => 'التقارير والشجرة',
                'items' => [
                    ['label' => 'التقارير المحاسبية الشاملة', 'route' => 'admin.accounting.reports', 'icon' => 'fa-chart-pie', 'hint' => 'فلاتر زمنية، رسوم بيانية، تصدير Excel'],
                    ['label' => 'شجرة الحسابات', 'route' => 'admin.accounting.chart', 'icon' => 'fa-sitemap', 'hint' => 'خريطة الحسابات وربطها بوحدات النظام'],
                ],
            ],
            [
                'title' => 'السجلات المحاسبية الأساسية',
                'items' => [
                    ['label' => 'الفواتير', 'route' => 'admin.invoices.index', 'icon' => 'fa-file-invoice'],
                    ['label' => 'المدفوعات', 'route' => 'admin.payments.index', 'icon' => 'fa-credit-card'],
                    ['label' => 'المعاملات المالية', 'route' => 'admin.transactions.index', 'icon' => 'fa-exchange-alt'],
                    ['label' => 'المحافظ البنكية والنقدية', 'route' => 'admin.wallets.index', 'icon' => 'fa-wallet'],
                    ['label' => 'المصروفات', 'route' => 'admin.expenses.index', 'icon' => 'fa-receipt'],
                ],
            ],
            [
                'title' => 'إيرادات الأكاديمية (كورسات وحجوزات)',
                'items' => [
                    ['label' => 'طلبات الشراء — أونلاين ومسارات', 'route' => 'admin.orders.index', 'icon' => 'fa-shopping-cart'],
                    ['label' => 'التسجيل في الكورسات الأونلاين', 'route' => 'admin.online-enrollments.index', 'icon' => 'fa-laptop'],
                    ['label' => 'حجوزات الكورسات الأوفلاين', 'route' => 'admin.offline-course-bookings.index', 'icon' => 'fa-building'],
                    ['label' => 'إدارة الأونلاين — كورسات وجداول', 'route' => 'admin.online-management.index', 'icon' => 'fa-laptop-house', 'hint' => 'تسجيل بالإيميل، كورس أونلاين فقط'],
                    ['label' => 'حجوزات الكورسات الأونلاين (مجموعات)', 'route' => 'admin.online-course-bookings.index', 'icon' => 'fa-calendar-check'],
                    ['label' => 'كورسات أوفلاين — إدارة', 'route' => 'admin.offline-courses.index', 'icon' => 'fa-chalkboard-teacher'],
                    ['label' => 'تسجيلات المسارات التعليمية', 'route' => 'admin.learning-path-enrollments.index', 'icon' => 'fa-route'],
                ],
            ],
            [
                'title' => 'التقسيط والاشتراكات',
                'items' => [
                    ['label' => 'لوحة التقسيط المحاسبية', 'route' => 'admin.accounting.installments', 'icon' => 'fa-tachometer-alt', 'hint' => 'مؤشرات، أقساط قادمة، آخر السداد'],
                    ['label' => 'خطط التقسيط', 'route' => 'admin.installments.plans.index', 'icon' => 'fa-layer-group'],
                    ['label' => 'اتفاقيات التقسيط', 'route' => 'admin.installments.agreements.index', 'icon' => 'fa-handshake'],
                    ['label' => 'حجز يدوي + تقسيط (كورس)', 'route' => 'admin.installments.agreements.manual-booking', 'icon' => 'fa-user-plus', 'hint' => 'أونلاين أو أوفلاين بالمجموعة'],
                    ['label' => 'اشتراكات المنصة', 'route' => 'admin.subscriptions.index', 'icon' => 'fa-calendar-alt'],
                ],
            ],
            [
                'title' => 'المدربون — مستحقات وسحوبات',
                'items' => [
                    ['label' => 'ماليات المدربين (رواتب/نسب)', 'route' => 'admin.salaries.index', 'icon' => 'fa-money-check-alt'],
                    ['label' => 'حسابات المدربين', 'route' => 'admin.accounting.instructor-accounts.index', 'icon' => 'fa-user-tie'],
                    ['label' => 'طلبات السحب', 'route' => 'admin.withdrawals.index', 'icon' => 'fa-hand-holding-usd'],
                ],
            ],
            [
                'title' => 'الموظفون والعقود',
                'items' => [
                    ['label' => 'اتفاقيات الموظفين', 'route' => 'admin.employee-agreements.index', 'icon' => 'fa-users-cog'],
                ],
            ],
            [
                'title' => 'تسويق يؤثر على الإيراد',
                'items' => [
                    ['label' => 'الكوبونات والخصومات', 'route' => 'admin.coupons.index', 'icon' => 'fa-ticket-alt'],
                    ['label' => 'برامج الإحالة', 'route' => 'admin.referral-programs.index', 'icon' => 'fa-gift'],
                ],
            ],
        ];
    }
}
