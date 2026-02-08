<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAgreement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgreementController extends Controller
{
    /**
     * عرض قائمة اتفاقيات الموظف
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->isEmployee()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        $agreements = EmployeeAgreement::where('employee_id', $user->id)
            ->with(['creator', 'deductions', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get();

        $activeAgreement = $agreements->where('status', 'active')->first();

        return view('employee.agreements.index', compact('agreements', 'activeAgreement'));
    }

    /**
     * عرض تفاصيل اتفاقية
     */
    public function show(EmployeeAgreement $agreement)
    {
        $user = Auth::user();
        
        if (!$user->isEmployee() || $agreement->employee_id !== $user->id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        $agreement->load(['creator', 'deductions', 'payments']);

        $stats = [
            'total_deductions' => $agreement->deductions()->where('status', 'applied')->sum('amount'),
            'total_payments' => $agreement->payments()->where('status', 'paid')->sum('net_salary'),
            'pending_payments' => $agreement->payments()->where('status', 'pending')->count(),
        ];

        return view('employee.agreements.show', compact('agreement', 'stats'));
    }
}
