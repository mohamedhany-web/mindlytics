<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAgreement;
use App\Models\EmployeeSalaryAddition;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeAdditionController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeSalaryAddition::with(['employee', 'creator']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('status') && in_array($request->status, ['pending', 'applied', 'cancelled'], true)) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type') && array_key_exists($request->type, EmployeeSalaryAddition::typeLabels())) {
            $query->where('type', $request->type);
        }
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('addition_number', 'like', "%{$s}%")
                    ->orWhere('title', 'like', "%{$s}%")
                    ->orWhereHas('employee', fn ($q2) => $q2->where('name', 'like', "%{$s}%"));
            });
        }

        $additions = $query->orderByDesc('addition_date')->paginate(20);
        $employees = User::employees()->where('is_active', true)->orderBy('name')->get();

        $stats = [
            'total' => EmployeeSalaryAddition::count(),
            'applied' => EmployeeSalaryAddition::where('status', 'applied')->count(),
            'total_amount' => EmployeeSalaryAddition::where('status', 'applied')->sum('amount'),
        ];

        return view('admin.employee-additions.index', compact('additions', 'employees', 'stats'));
    }

    public function create()
    {
        $employees = User::employees()->where('is_active', true)->orderBy('name')->get();

        return view('admin.employee-additions.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:'.implode(',', array_keys(EmployeeSalaryAddition::typeLabels())),
            'addition_date' => 'required|date',
            'status' => 'required|in:pending,applied,cancelled',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $agreement = EmployeeAgreement::where('employee_id', $validated['employee_id'])->where('status', 'active')->first();
        $validated['agreement_id'] = $agreement?->id;

        $addition = null;
        for ($i = 0; $i < 10; $i++) {
            try {
                $addition = EmployeeSalaryAddition::createWithAutoNumber($validated);
                break;
            } catch (UniqueConstraintViolationException $e) {
                if ($i >= 9) {
                    throw $e;
                }
            }
        }

        if (! $addition) {
            return back()->with('error', 'تعذر إنشاء الإضافة.');
        }

        if ($addition->status === 'applied') {
            $employee = User::find($addition->employee_id);
            if ($employee) {
                Notification::create([
                    'user_id' => $employee->id,
                    'sender_id' => Auth::id(),
                    'title' => 'إضافة خارجية على حسابك',
                    'message' => 'تمت إضافة '.number_format((float) $addition->amount, 2).' ج.م — '.$addition->title,
                    'type' => 'employee',
                    'priority' => 'normal',
                    'audience' => 'employee',
                    'action_url' => route('employee.accounting.index'),
                    'action_text' => 'المحاسبة',
                    'data' => ['kind' => 'salary_addition', 'addition_id' => $addition->id],
                ]);
            }
        }

        return redirect()->route('admin.employee-additions.index')->with('success', 'تمت إضافة المبلغ للموظف.');
    }

    public function show(EmployeeSalaryAddition $employee_addition)
    {
        $employee_addition->load(['employee', 'creator', 'agreement']);

        return view('admin.employee-additions.show', compact('employee_addition'));
    }

    public function edit(EmployeeSalaryAddition $employee_addition)
    {
        $employees = User::employees()->where('is_active', true)->orderBy('name')->get();

        return view('admin.employee-additions.edit', compact('employee_addition', 'employees'));
    }

    public function update(Request $request, EmployeeSalaryAddition $employee_addition)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:'.implode(',', array_keys(EmployeeSalaryAddition::typeLabels())),
            'addition_date' => 'required|date',
            'status' => 'required|in:pending,applied,cancelled',
            'notes' => 'nullable|string',
        ]);

        $employee_addition->update($validated);

        return redirect()->route('admin.employee-additions.show', $employee_addition)->with('success', 'تم التحديث.');
    }

    public function destroy(EmployeeSalaryAddition $employee_addition)
    {
        $employee_addition->delete();

        return redirect()->route('admin.employee-additions.index')->with('success', 'تم الحذف.');
    }
}
