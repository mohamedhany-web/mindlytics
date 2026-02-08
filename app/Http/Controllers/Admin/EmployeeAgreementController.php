<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAgreement;
use App\Models\EmployeeSalaryDeduction;
use App\Models\EmployeeSalaryPayment;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'salary' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:draft,active,suspended,terminated,completed',
            'contract_terms' => 'nullable|string',
            'agreement_terms' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $agreement = EmployeeAgreement::create([
                'employee_id' => $request->employee_id,
                'agreement_number' => EmployeeAgreement::generateAgreementNumber(),
                'title' => $request->title,
                'description' => $request->description,
                'salary' => $request->salary,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
                'contract_terms' => $request->contract_terms,
                'agreement_terms' => $request->agreement_terms,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ]);

            // تسجيل النشاط
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'employee_agreement_created',
                'model_type' => 'EmployeeAgreement',
                'model_id' => $agreement->id,
                'new_values' => $agreement->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.employee-agreements.show', $agreement)
                ->with('success', 'تم إنشاء الاتفاقية بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating employee agreement: ' . $e->getMessage());
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء الاتفاقية');
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
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'salary' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:draft,active,suspended,terminated,completed',
            'contract_terms' => 'nullable|string',
            'agreement_terms' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $oldValues = $employeeAgreement->toArray();
            $employeeAgreement->update($request->all());

            // تسجيل النشاط
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'employee_agreement_updated',
                'model_type' => 'EmployeeAgreement',
                'model_id' => $employeeAgreement->id,
                'old_values' => $oldValues,
                'new_values' => $employeeAgreement->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.employee-agreements.show', $employeeAgreement)
                ->with('success', 'تم تحديث الاتفاقية بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating employee agreement: ' . $e->getMessage());
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث الاتفاقية');
        }
    }

    /**
     * حذف اتفاقية
     */
    public function destroy(EmployeeAgreement $employeeAgreement)
    {
        try {
            // التحقق من وجود مدفوعات أو خصومات مرتبطة
            if ($employeeAgreement->payments()->where('status', 'paid')->exists()) {
                return redirect()->back()
                    ->with('error', 'لا يمكن حذف الاتفاقية لأنها تحتوي على مدفوعات مكتملة');
            }

            $oldValues = $employeeAgreement->toArray();
            $employeeAgreement->delete();

            // تسجيل النشاط
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'employee_agreement_deleted',
                'model_type' => 'EmployeeAgreement',
                'model_id' => $employeeAgreement->id,
                'old_values' => $oldValues,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return redirect()->route('admin.employee-agreements.index')
                ->with('success', 'تم حذف الاتفاقية بنجاح');
        } catch (\Exception $e) {
            Log::error('Error deleting employee agreement: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حذف الاتفاقية');
        }
    }
}
