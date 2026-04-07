<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstallmentAgreement;
use App\Models\InstallmentPayment;
use App\Models\InstallmentPlan;
use Carbon\Carbon;
use Illuminate\View\View;

class AccountingInstallmentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage.installments');
    }

    /**
     * لوحة تحكم محاسبية شاملة للتقسيط ومتابعة العمليات.
     */
    public function index(): View
    {
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $stats = [
            'agreements_total' => InstallmentAgreement::count(),
            'agreements_active' => InstallmentAgreement::where('status', InstallmentAgreement::STATUS_ACTIVE)->count(),
            'agreements_overdue' => InstallmentAgreement::where('status', InstallmentAgreement::STATUS_OVERDUE)->count(),
            'agreements_completed' => InstallmentAgreement::where('status', InstallmentAgreement::STATUS_COMPLETED)->count(),
            'agreements_cancelled' => InstallmentAgreement::where('status', InstallmentAgreement::STATUS_CANCELLED)->count(),
            'online_linked' => InstallmentAgreement::whereNotNull('student_course_enrollment_id')->count(),
            'offline_linked' => InstallmentAgreement::whereNotNull('offline_course_enrollment_id')->count(),
            'total_financed' => (float) InstallmentAgreement::sum('total_amount'),
            'total_deposits_planned' => (float) InstallmentAgreement::sum('deposit_amount'),
            'pending_installments_sum' => (float) InstallmentPayment::where('status', InstallmentPayment::STATUS_PENDING)->sum('amount'),
            'overdue_installments_sum' => (float) InstallmentPayment::where('status', InstallmentPayment::STATUS_OVERDUE)->sum('amount'),
            'paid_installments_month' => (float) InstallmentPayment::where('status', InstallmentPayment::STATUS_PAID)
                ->whereBetween('paid_at', [$monthStart, $monthEnd])
                ->sum('amount'),
            'paid_count_month' => InstallmentPayment::where('status', InstallmentPayment::STATUS_PAID)
                ->whereBetween('paid_at', [$monthStart, $monthEnd])
                ->count(),
            'pending_count' => InstallmentPayment::where('status', InstallmentPayment::STATUS_PENDING)->count(),
            'overdue_payments_count' => InstallmentPayment::where('status', InstallmentPayment::STATUS_OVERDUE)->count(),
            'active_plans' => InstallmentPlan::where('is_active', true)->count(),
            'month_label' => $monthStart->translatedFormat('F Y'),
        ];

        $recentPayments = InstallmentPayment::query()
            ->with(['agreement.student', 'agreement.course', 'agreement.offlineEnrollment.course', 'payment'])
            ->where('status', InstallmentPayment::STATUS_PAID)
            ->whereNotNull('paid_at')
            ->orderByDesc('paid_at')
            ->limit(25)
            ->get();

        $upcomingDues = InstallmentPayment::query()
            ->with(['agreement.student', 'agreement.course', 'agreement.offlineEnrollment.course'])
            ->whereIn('status', [InstallmentPayment::STATUS_PENDING, InstallmentPayment::STATUS_OVERDUE])
            ->whereDate('due_date', '<=', now()->addDays(14)->toDateString())
            ->orderBy('due_date')
            ->orderBy('sequence_number')
            ->limit(40)
            ->get();

        return view('admin.accounting.installments', compact('stats', 'recentPayments', 'upcomingDues'));
    }
}
