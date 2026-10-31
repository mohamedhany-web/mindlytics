<?php

namespace App\Domains\Accounting\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\Accounting\Models\AgreementPayment;
use App\Domains\Accounting\Models\InstructorAgreement;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * حسابات المدربين — رؤية كاملة: اتفاقيات، رواتب، مدفوعات، أرباح نسبة الكورس.
 */
class InstructorAccountController extends Controller
{
    /**
     * قائمة حسابات المدربين (كل من له اتفاقية أو مدفوعة)
     */
    public function index(Request $request)
    {
        $instructorIdsFromPayments = AgreementPayment::distinct()->pluck('instructor_id');
        $instructorIdsFromAgreements = InstructorAgreement::distinct()->pluck('instructor_id');
        $instructorIds = $instructorIdsFromPayments->merge($instructorIdsFromAgreements)->unique()->values();

        $query = User::whereIn('id', $instructorIds)->orderBy('name');
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }
        $instructors = $query->get();

        $statsByInstructor = [];
        foreach ($instructors as $instructor) {
            $payments = AgreementPayment::where('instructor_id', $instructor->id)->get();
            $agreementsCount = InstructorAgreement::where('instructor_id', $instructor->id)->count();
            $statsByInstructor[$instructor->id] = [
                'agreements_count' => $agreementsCount,
                'pending_total' => $payments->where('status', AgreementPayment::STATUS_APPROVED)->sum('amount'),
                'paid_total' => $payments->where('status', AgreementPayment::STATUS_PAID)->sum('amount'),
                'pending_count' => $payments->where('status', AgreementPayment::STATUS_APPROVED)->count(),
                'paid_count' => $payments->where('status', AgreementPayment::STATUS_PAID)->count(),
                'total_earned' => $payments->where('status', AgreementPayment::STATUS_PAID)->sum('amount'),
            ];
        }

        $globalStats = [
            'instructors_count' => $instructors->count(),
            'pending_total' => AgreementPayment::where('status', AgreementPayment::STATUS_APPROVED)->sum('amount'),
            'paid_total' => AgreementPayment::where('status', AgreementPayment::STATUS_PAID)->sum('amount'),
        ];

        return view('accounting::admin.accounting.instructor-accounts.index', compact('instructors', 'statsByInstructor', 'globalStats'));
    }

    /**
     * صفحة حساب مدرب واحد — اتفاقيات، مدفوعات، تفعيلات نسبة الكورس، إجماليات
     */
    public function show(User $instructor)
    {
        $agreements = InstructorAgreement::where('instructor_id', $instructor->id)
            ->with(['advancedCourse', 'payments' => fn ($q) => $q->with(['enrollment.student', 'course'])])
            ->orderByDesc('created_at')
            ->get();

        $payments = AgreementPayment::with(['agreement', 'enrollment.student', 'course'])
            ->where('instructor_id', $instructor->id)
            ->orderByDesc('created_at')
            ->get();

        $activationPayments = $payments->where('type', AgreementPayment::TYPE_COURSE_ACTIVATION);

        $totals = [
            'pending' => $payments->where('status', AgreementPayment::STATUS_APPROVED)->sum('amount'),
            'paid' => $payments->where('status', AgreementPayment::STATUS_PAID)->sum('amount'),
            'from_activations' => $activationPayments->sum('amount'),
        ];

        return view('accounting::admin.accounting.instructor-accounts.show', compact(
            'instructor',
            'agreements',
            'payments',
            'activationPayments',
            'totals'
        ));
    }
}
