<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAgreement;
use App\Models\EmployeeSalaryDeduction;
use App\Models\User;
use App\Mail\EmployeeDeductionAddedMail;
use App\Support\SalesDailyReportSettings;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class EmployeeDeductionController extends Controller
{
    /**
     * عرض قائمة خصومات الموظفين
     */
    public function index(Request $request): View
    {
        $query = EmployeeSalaryDeduction::with(['employee', 'agreement', 'creator']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status') && in_array($request->status, ['pending', 'applied', 'cancelled'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type') && in_array($request->type, ['tax', 'insurance', 'loan', 'penalty', 'other'])) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('deduction_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('employee_code', 'like', "%{$search}%");
                    });
            });
        }

        $deductions = $query->orderBy('deduction_date', 'desc')->orderBy('id', 'desc')->paginate(20);
        $employees = User::employees()->where('is_active', true)->orderBy('name')->get();

        $stats = [
            'total' => EmployeeSalaryDeduction::count(),
            'pending' => EmployeeSalaryDeduction::where('status', 'pending')->count(),
            'applied' => EmployeeSalaryDeduction::where('status', 'applied')->count(),
            'total_amount' => EmployeeSalaryDeduction::where('status', 'applied')->sum('amount'),
        ];

        return view('admin.employee-deductions.index', compact('deductions', 'employees', 'stats'));
    }

    /**
     * نموذج إضافة خصم
     */
    public function create(): View
    {
        $employees = User::employees()->where('is_active', true)->orderBy('name')->get();
        $agreements = EmployeeAgreement::with('employee')->where('status', 'active')->orderBy('created_at', 'desc')->get();

        return view('admin.employee-deductions.create', compact('employees', 'agreements'));
    }

    /**
     * حفظ خصم جديد
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'agreement_id' => 'nullable|exists:employee_agreements,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:tax,insurance,loan,penalty,other',
            'deduction_date' => 'required|date',
            'status' => 'required|in:pending,applied,cancelled',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();

        // إذا لم تُختر اتفاقية، ربط بأول اتفاقية نشطة للموظف إن وُجدت
        if (empty($validated['agreement_id'])) {
            $agreement = EmployeeAgreement::where('employee_id', $validated['employee_id'])->where('status', 'active')->first();
            $validated['agreement_id'] = $agreement?->id;
        }

        $deduction = null;
        for ($attempt = 0; $attempt < 10; $attempt++) {
            try {
                $deduction = EmployeeSalaryDeduction::createWithAutoDeductionNumber($validated);
                break;
            } catch (UniqueConstraintViolationException $e) {
                if ($attempt >= 9 || ! str_contains($e->getMessage(), 'deduction_number')) {
                    throw $e;
                }
            }
        }

        if (! $deduction instanceof EmployeeSalaryDeduction) {
            throw new \RuntimeException('تعذر إنشاء رقم خصم فريد بعد عدة محاولات.');
        }

        try {
            $employee = User::find($deduction->employee_id);
            if ($employee && $employee->email) {
                Mail::to($employee->email)->send(new EmployeeDeductionAddedMail($deduction));
            }
        } catch (\Throwable $e) {
            \Log::warning('Failed to send deduction email to employee: ' . ($e->getMessage()));
        }

        return redirect()->route('admin.employee-deductions.index')
            ->with('success', 'تم إضافة الخصم بنجاح.');
    }

    /**
     * إعدادات خصم التقرير اليومي للمبيعات.
     */
    public function dailyReportPenaltySettings(): View
    {
        return view('admin.employee-deductions.daily-report-penalty-settings', [
            'settings' => SalesDailyReportSettings::all(),
        ]);
    }

    /**
     * حفظ إعدادات خصم التقرير اليومي للمبيعات.
     */
    public function updateDailyReportPenaltySettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'enabled' => 'nullable|boolean',
            'work_days_only' => 'nullable|boolean',
            'deadline_time' => 'required|string|regex:/^\d{2}:\d{2}$/',
            'penalty_enabled' => 'nullable|boolean',
            'penalty_amount' => 'required|numeric|min:0.01',
            'penalty_title' => 'required|string|max:255',
            'penalty_description' => 'nullable|string|max:2000',
            'penalty_type' => 'required|in:tax,insurance,loan,penalty,other',
            'penalty_status' => 'required|in:pending,applied,cancelled',
            'kpi_submission_target_pct' => 'required|numeric|min:50|max:100',
        ]);

        SalesDailyReportSettings::save([
            'enabled' => $request->boolean('enabled'),
            'work_days_only' => $request->boolean('work_days_only'),
            'deadline_time' => $validated['deadline_time'],
            'penalty_enabled' => $request->boolean('penalty_enabled'),
            'penalty_amount' => (float) $validated['penalty_amount'],
            'penalty_title' => $validated['penalty_title'],
            'penalty_description' => $validated['penalty_description'] ?? '',
            'penalty_type' => $validated['penalty_type'],
            'penalty_status' => $validated['penalty_status'],
            'kpi_submission_target_pct' => (float) $validated['kpi_submission_target_pct'],
        ]);

        return redirect()->route('admin.employee-deductions.daily-report-penalty-settings')
            ->with('success', 'تم حفظ إعدادات الخصم التلقائي للتقارير اليومية.');
    }

    /**
     * عرض تفاصيل خصم
     */
    public function show(EmployeeSalaryDeduction $employee_deduction): View
    {
        $employee_deduction->load(['employee', 'agreement', 'creator']);

        return view('admin.employee-deductions.show', ['employeeDeduction' => $employee_deduction]);
    }

    /**
     * نموذج تعديل خصم
     */
    public function edit(EmployeeSalaryDeduction $employee_deduction): View
    {
        $employees = User::employees()->where('is_active', true)->orderBy('name')->get();
        $agreements = EmployeeAgreement::with('employee')->where('status', 'active')->orderBy('created_at', 'desc')->get();

        return view('admin.employee-deductions.edit', ['employeeDeduction' => $employee_deduction, 'employees' => $employees, 'agreements' => $agreements]);
    }

    /**
     * تحديث خصم
     */
    public function update(Request $request, EmployeeSalaryDeduction $employee_deduction): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'agreement_id' => 'nullable|exists:employee_agreements,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:tax,insurance,loan,penalty,other',
            'deduction_date' => 'required|date',
            'status' => 'required|in:pending,applied,cancelled',
            'notes' => 'nullable|string',
        ]);

        $employee_deduction->update($validated);

        return redirect()->route('admin.employee-deductions.index')
            ->with('success', 'تم تحديث الخصم بنجاح.');
    }

    /**
     * حذف خصم
     */
    public function destroy(EmployeeSalaryDeduction $employee_deduction): RedirectResponse
    {
        $employee_deduction->delete();

        return redirect()->route('admin.employee-deductions.index')
            ->with('success', 'تم حذف الخصم بنجاح.');
    }
}
