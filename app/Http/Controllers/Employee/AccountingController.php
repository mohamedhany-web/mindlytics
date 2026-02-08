<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAgreement;
use App\Models\EmployeeSalaryDeduction;
use App\Models\EmployeeSalaryPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AccountingController extends Controller
{
    /**
     * عرض صفحة المحاسبة للموظف
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->isEmployee()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        // الحصول على الاتفاقية النشطة
        $activeAgreement = EmployeeAgreement::where('employee_id', $user->id)
            ->where('status', 'active')
            ->first();

        // الحصول على آخر دفعة راتب
        $lastPayment = EmployeeSalaryPayment::where('employee_id', $user->id)
            ->orderBy('payment_date', 'desc')
            ->first();

        // حساب إجمالي الخصومات للشهر الحالي
        $currentMonthDeductions = EmployeeSalaryDeduction::where('employee_id', $user->id)
            ->where('status', 'applied')
            ->whereYear('deduction_date', Carbon::now()->year)
            ->whereMonth('deduction_date', Carbon::now()->month)
            ->sum('amount');

        // حساب إجمالي الخصومات
        $totalDeductions = EmployeeSalaryDeduction::where('employee_id', $user->id)
            ->where('status', 'applied')
            ->sum('amount');

        // حساب إجمالي المدفوعات
        $totalPaid = EmployeeSalaryPayment::where('employee_id', $user->id)
            ->where('status', 'paid')
            ->sum('net_salary');

        // الحصول على الدفعات القادمة
        $upcomingPayments = EmployeeSalaryPayment::where('employee_id', $user->id)
            ->where('status', 'pending')
            ->where('payment_date', '>=', Carbon::now())
            ->orderBy('payment_date', 'asc')
            ->get();

        // الحصول على الخصومات الأخيرة
        $recentDeductions = EmployeeSalaryDeduction::where('employee_id', $user->id)
            ->where('status', 'applied')
            ->orderBy('deduction_date', 'desc')
            ->limit(10)
            ->get();

        // حساب تاريخ الاستحقاق القادم (عادة آخر يوم في الشهر)
        $nextPaymentDate = null;
        if ($activeAgreement) {
            $nextPaymentDate = Carbon::now()->endOfMonth();
            // إذا كان اليوم بعد تاريخ الاستحقاق، نأخذ الشهر القادم
            if (Carbon::now()->day > 25) {
                $nextPaymentDate = Carbon::now()->addMonth()->endOfMonth();
            }
        }

        $stats = [
            'base_salary' => $activeAgreement ? $activeAgreement->salary : 0,
            'current_month_deductions' => $currentMonthDeductions,
            'total_deductions' => $totalDeductions,
            'total_paid' => $totalPaid,
            'next_payment_date' => $nextPaymentDate,
            'net_salary' => $activeAgreement ? ($activeAgreement->salary - $currentMonthDeductions) : 0,
        ];

        return view('employee.accounting.index', compact(
            'activeAgreement',
            'lastPayment',
            'upcomingPayments',
            'recentDeductions',
            'stats'
        ));
    }
}
