<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmployeeLeaveController extends Controller
{
    /**
     * عرض قائمة طلبات الإجازة للموظف
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isEmployee()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        $query = $user->leaveRequests()->latest();

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب النوع
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $leaveRequests = $query->paginate(15);

        $stats = [
            'total' => $user->leaveRequests()->count(),
            'pending' => $user->leaveRequests()->where('status', 'pending')->count(),
            'approved' => $user->leaveRequests()->where('status', 'approved')->count(),
            'rejected' => $user->leaveRequests()->where('status', 'rejected')->count(),
        ];

        return view('employee.leaves.index', compact('leaveRequests', 'stats'));
    }

    /**
     * عرض نموذج إضافة طلب إجازة جديد
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isEmployee()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        return view('employee.leaves.create');
    }

    /**
     * حفظ طلب إجازة جديد
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isEmployee()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        $validated = $request->validate([
            'type' => 'required|in:annual,sick,emergency,unpaid,other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        // حساب عدد الأيام
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $days = $startDate->diffInDays($endDate) + 1;

        $validated['days'] = $days;
        $validated['employee_id'] = $user->id;
        $validated['status'] = 'pending';

        LeaveRequest::create($validated);

        return redirect()->route('employee.leaves.index')
                        ->with('success', 'تم تقديم طلب الإجازة بنجاح');
    }

    /**
     * عرض تفاصيل طلب إجازة
     */
    public function show(LeaveRequest $leave)
    {
        $user = Auth::user();
        
        if (!$user->isEmployee()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        // التأكد من أن الطلب يخص الموظف الحالي
        if ($leave->employee_id !== $user->id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الطلب');
        }

        $leave->load(['reviewer']);

        return view('employee.leaves.show', compact('leave'));
    }

    /**
     * إلغاء طلب إجازة
     */
    public function destroy(LeaveRequest $leave)
    {
        $user = Auth::user();
        
        if (!$user->isEmployee()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        // التأكد من أن الطلب يخص الموظف الحالي
        if ($leave->employee_id !== $user->id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الطلب');
        }

        // يمكن إلغاء الطلب فقط إذا كان قيد المراجعة
        if ($leave->status !== 'pending') {
            return redirect()->back()
                        ->with('error', 'لا يمكن إلغاء الطلب لأنه تمت مراجعته بالفعل');
        }

        $leave->update(['status' => 'cancelled']);

        return redirect()->route('employee.leaves.index')
                        ->with('success', 'تم إلغاء طلب الإجازة بنجاح');
    }
}
