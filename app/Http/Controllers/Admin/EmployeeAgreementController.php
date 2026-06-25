<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAgreement;
use App\Models\EmployeeSalaryDeduction;
use App\Models\EmployeeSalaryPayment;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EmployeeAgreementController extends Controller
{
    /**
     * عرض قائمة اتفاقيات الموظفين
     */
    public function index(Request $request)
    {
        try {
            // Sanitization
            $employeeId = filter_var($request->input('employee_id'), FILTER_VALIDATE_INT);
            $status = strip_tags(trim($request->input('status', '')));
            $search = strip_tags(trim($request->input('search', '')));

            $query = EmployeeAgreement::with(['employee', 'creator'])
                ->withCount(['deductions', 'payments']);

            if ($employeeId && $employeeId > 0) {
                $query->where('employee_id', $employeeId);
            }

            if ($status && in_array($status, ['draft', 'active', 'suspended', 'terminated', 'completed'])) {
                $query->where('status', $status);
            }

            if ($search && strlen($search) <= 255) {
                $search = preg_replace('/[^a-zA-Z0-9\s\u0600-\u06FF]/', '', $search);
                $query->where(function($q) use ($search) {
                    $q->where('agreement_number', 'like', '%' . $search . '%')
                      ->orWhere('title', 'like', '%' . $search . '%')
                      ->orWhereHas('employee', function($q) use ($search) {
                          $q->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                      });
                });
            }

            $agreements = $query->orderBy('created_at', 'desc')->paginate(20);
            
            // جلب جميع الموظفين
            $employees = User::where('is_employee', true)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            $stats = [
                'total' => EmployeeAgreement::count(),
                'active' => EmployeeAgreement::where('status', 'active')->count(),
                'draft' => EmployeeAgreement::where('status', 'draft')->count(),
                'total_salary' => EmployeeAgreement::where('status', 'active')->sum('salary'),
            ];

            return view('admin.employee-agreements.index', compact('agreements', 'employees', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error loading employee agreements: ' . $e->getMessage());
            abort(500, 'حدث خطأ أثناء تحميل اتفاقيات الموظفين');
        }
    }

    /**
     * عرض صفحة إنشاء اتفاقية جديدة
     */
    public function create()
    {
        $employees = User::where('is_employee', true)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.employee-agreements.create', compact('employees'));
    }

    /**
     * حفظ اتفاقية جديدة
     */
    public function store(Request $request)
    {
        try {
            $endDateRaw = $request->input('end_date');
            $endDateNormalized = (is_string($endDateRaw) && trim($endDateRaw) !== '') ? trim($endDateRaw) : null;
            $request->merge(['end_date' => $endDateNormalized]);

            $validator = Validator::make($request->all(), [
                'employee_id' => 'required|exists:users,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'salary' => 'required|numeric|min:0',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date',
                'status' => 'required|in:draft,active,suspended,terminated,completed',
                'contract_terms' => 'nullable|string',
                'agreement_terms' => 'nullable|string',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return redirect()->route('admin.employee-agreements.create')
                    ->withInput()
                    ->withErrors($validator);
            }

            $validated = $validator->validated();

            if (!empty($validated['end_date']) && $validated['end_date'] < $validated['start_date']) {
                return redirect()->route('admin.employee-agreements.create')
                    ->withInput()
                    ->withErrors(['end_date' => ['تاريخ الانتهاء يجب أن يكون في نفس تاريخ البدء أو بعده.']]);
            }

            DB::beginTransaction();

            $agreement = EmployeeAgreement::create([
                'employee_id' => $validated['employee_id'],
                'agreement_number' => EmployeeAgreement::generateAgreementNumber(),
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'salary' => $validated['salary'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
                'status' => $validated['status'],
                'contract_terms' => $validated['contract_terms'] ?? null,
                'agreement_terms' => $validated['agreement_terms'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('admin.employee-agreements.index')
                ->with('success', 'تم إنشاء الاتفاقية بنجاح');
        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            $msg = $e->getMessage();
            $file = $e->getFile();
            $line = $e->getLine();
            Log::error('Employee agreement store FAILED', [
                'class' => get_class($e),
                'message' => $msg,
                'file' => $file,
                'line' => $line,
                'trace' => $e->getTraceAsString(),
            ]);
            $safeMsg = mb_substr($msg, 0, 500);
            try {
                return redirect()->to(url('/admin/employee-agreements/create'))
                    ->with('error', 'خطأ عند الحفظ: ' . $safeMsg);
            } catch (\Throwable $redirectEx) {
                Log::error('Redirect failed after store error', [
                    'message' => $redirectEx->getMessage(),
                    'original' => $msg,
                ]);
                abort(500, 'خطأ عند الحفظ: ' . $safeMsg);
            }
        }
    }

    /**
     * عرض تفاصيل اتفاقية
     */
    public function show(EmployeeAgreement $employeeAgreement)
    {
        $employeeAgreement->load([
            'employee',
            'creator',
            'deductions' => function($q) {
                $q->orderBy('deduction_date', 'desc');
            },
            'payments' => function($q) {
                $q->orderBy('payment_date', 'desc');
            }
        ]);

        $stats = [
            'total_deductions' => $employeeAgreement->deductions()->where('status', 'applied')->sum('amount'),
            'total_payments' => $employeeAgreement->payments()->where('status', 'paid')->sum('net_salary'),
            'pending_payments' => $employeeAgreement->payments()->where('status', 'pending')->count(),
            'total_paid_amount' => $employeeAgreement->payments()->where('status', 'paid')->sum('net_salary'),
        ];

        return view('admin.employee-agreements.show', compact('employeeAgreement', 'stats'));
    }

    /**
     * عرض صفحة تعديل اتفاقية
     */
    public function edit(EmployeeAgreement $employeeAgreement)
    {
        $employees = User::where('is_employee', true)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.employee-agreements.edit', compact('employeeAgreement', 'employees'));
    }

    /**
     * تحديث اتفاقية
     */
    public function update(Request $request, EmployeeAgreement $employeeAgreement)
    {
        $request->merge(['end_date' => trim((string) $request->input('end_date', '')) !== '' ? $request->input('end_date') : null]);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'salary' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'status' => 'required|in:draft,active,suspended,terminated,completed',
            'contract_terms' => 'nullable|string',
            'agreement_terms' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.employee-agreements.edit', $employeeAgreement)
                ->withInput()
                ->withErrors($validator);
        }

        $validated = $validator->validated();

        if (!empty($validated['end_date']) && $validated['end_date'] < $validated['start_date']) {
            return redirect()->route('admin.employee-agreements.edit', $employeeAgreement)
                ->withInput()
                ->withErrors(['end_date' => ['تاريخ الانتهاء يجب أن يكون في نفس تاريخ البدء أو بعده.']]);
        }

        try {
            DB::beginTransaction();
            $employeeAgreement->update($validated);
            DB::commit();

            return redirect()->route('admin.employee-agreements.show', $employeeAgreement)
                ->with('success', 'تم تحديث الاتفاقية بنجاح');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Employee agreement update failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return redirect()->route('admin.employee-agreements.edit', $employeeAgreement)
                ->withInput()
                ->with('error', 'خطأ عند الحفظ: ' . $e->getMessage());
        }
    }

    /**
     * حذف اتفاقية
     */
    public function destroy(EmployeeAgreement $employeeAgreement)
    {
        try {
            if ($employeeAgreement->payments()->where('status', 'paid')->exists()) {
                return redirect()->route('admin.employee-agreements.index')
                    ->with('error', 'لا يمكن حذف الاتفاقية لأنها تحتوي على مدفوعات مكتملة');
            }

            $employeeAgreement->delete();

            return redirect()->route('admin.employee-agreements.index')
                ->with('success', 'تم حذف الاتفاقية بنجاح');
        } catch (\Throwable $e) {
            Log::error('Error deleting employee agreement: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return redirect()->route('admin.employee-agreements.index')
                ->with('error', 'حدث خطأ أثناء حذف الاتفاقية');
        }
    }

    /**
     * إنشاء دفعة راتب جديدة للاتفاقية
     */
    public function storePayment(Request $request, EmployeeAgreement $employeeAgreement)
    {
        if (! $employeeAgreement->isPayrollActive()) {
            return back()->with('error', 'لا يمكن إنشاء دفعة راتب — الاتفاقية موقوفة أو غير نشطة.');
        }

        $request->validate([
            'payment_date' => 'required|date',
            'total_deductions' => 'nullable|numeric|min:0',
        ]);

        $baseSalary = (float) $employeeAgreement->salary;
        $totalDeductions = (float) ($request->total_deductions ?? 0);
        $netSalary = $baseSalary - $totalDeductions;

        $payment = EmployeeSalaryPayment::create([
            'employee_id' => $employeeAgreement->employee_id,
            'agreement_id' => $employeeAgreement->id,
            'payment_number' => EmployeeSalaryPayment::generatePaymentNumber(),
            'base_salary' => $baseSalary,
            'total_deductions' => $totalDeductions,
            'net_salary' => $netSalary,
            'payment_date' => $request->payment_date,
            'status' => 'pending',
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.employee-agreements.show', $employeeAgreement)
            ->with('success', 'تم إنشاء دفعة الراتب. قم بالتحويل للموظف ثم ارفع إيصال التحويل من جدول المدفوعات.');
    }

    /**
     * تسجيل دفع الراتب ورفع إيصال التحويل
     */
    public function markPaymentPaid(Request $request, EmployeeSalaryPayment $payment)
    {
        if ($payment->status !== 'pending' && $payment->status !== 'overdue') {
            return back()->with('error', 'هذه الدفعة ليست قيد الانتظار للدفع.');
        }

        $request->validate([
            'transfer_receipt' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string|max:500',
        ]);

        $file = $request->file('transfer_receipt');
        $path = $file->store('receipts/employee-salary-payments', 'public');

        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
            'transfer_receipt_path' => $path,
            'notes' => $request->filled('notes')
                ? trim(($payment->notes ?? '') . "\n" . '[تحويل] ' . $request->notes)
                : $payment->notes,
        ]);

        $agreement = $payment->agreement;
        return redirect()->route('admin.employee-agreements.show', $agreement)
            ->with('success', 'تم تسجيل الدفع ورفع إيصال التحويل. ستظهر المدفوعة في قسم المحاسبة للموظف.');
    }

    /**
     * إيقاف الاتفاقية — تجميد الموظف أو إنهاء علاقة العمل (لا رواتب جديدة).
     */
    public function stop(Request $request, EmployeeAgreement $employeeAgreement)
    {
        if (! in_array($employeeAgreement->status, ['active', 'draft', 'suspended'], true)) {
            return back()->with('error', 'الاتفاقية موقوفة أو منتهية بالفعل.');
        }

        $validated = $request->validate([
            'stop_type' => 'required|in:suspended,terminated',
            'deactivate_account' => 'nullable|boolean',
            'stop_note' => 'nullable|string|max:1000',
        ]);

        $stopType = $validated['stop_type'];
        $deactivate = $request->boolean('deactivate_account', $stopType === 'terminated');
        $today = now()->toDateString();
        $label = $stopType === 'suspended' ? 'تجميد الاتفاقية' : 'إيقاف الاتفاقية / مغادرة الشركة';
        $noteLine = '['.now()->format('Y-m-d H:i')."] {$label}";
        if (! empty($validated['stop_note'])) {
            $noteLine .= ' — '.$validated['stop_note'];
        }

        try {
            DB::transaction(function () use ($employeeAgreement, $stopType, $deactivate, $today, $noteLine) {
                $employeeAgreement->update([
                    'status' => $stopType,
                    'end_date' => $employeeAgreement->end_date ?? $today,
                    'notes' => trim(($employeeAgreement->notes ?? '')."\n".$noteLine),
                ]);

                $employeeAgreement->payments()
                    ->whereIn('status', ['pending', 'overdue'])
                    ->update(['status' => 'cancelled']);

                if ($deactivate && $employeeAgreement->employee) {
                    $employeeAgreement->employee->update(['is_active' => false]);
                }
            });

            $msg = $stopType === 'suspended'
                ? 'تم تجميد الاتفاقية — لن تُنشأ رواتب جديدة حتى إعادة التفعيل.'
                : 'تم إيقاف الاتفاقية — الموظف لن يُحسب عليه راتب جديد.';

            if ($deactivate) {
                $msg .= ' تم تعطيل حساب الموظف.';
            }

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            Log::error('Employee agreement stop failed: '.$e->getMessage(), [
                'agreement_id' => $employeeAgreement->id,
            ]);

            return back()->with('error', 'تعذّر إيقاف الاتفاقية: '.mb_substr($e->getMessage(), 0, 200));
        }
    }

    /**
     * إعادة تفعيل اتفاقية مجمّدة.
     */
    public function reactivate(EmployeeAgreement $employeeAgreement)
    {
        if ($employeeAgreement->status !== 'suspended') {
            return back()->with('error', 'يمكن إعادة التفعيل للاتفاقيات المجمّدة فقط. للموقوف نهائياً أنشئ اتفاقية جديدة.');
        }

        try {
            DB::transaction(function () use ($employeeAgreement) {
                $employeeAgreement->update([
                    'status' => 'active',
                    'end_date' => null,
                    'notes' => trim(($employeeAgreement->notes ?? '')."\n[".now()->format('Y-m-d H:i').'] إعادة تفعيل الاتفاقية'),
                ]);

                if ($employeeAgreement->employee && ! $employeeAgreement->employee->is_active) {
                    $employeeAgreement->employee->update(['is_active' => true]);
                }
            });

            return back()->with('success', 'تم إعادة تفعيل الاتفاقية — يمكن إنشاء رواتب من جديد.');
        } catch (\Throwable $e) {
            Log::error('Employee agreement reactivate failed: '.$e->getMessage());

            return back()->with('error', 'تعذّر إعادة التفعيل.');
        }
    }
}
