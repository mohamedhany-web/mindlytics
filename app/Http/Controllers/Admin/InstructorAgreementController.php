<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstructorAgreement;
use App\Models\AgreementPayment;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class InstructorAgreementController extends Controller
{
    /**
     * عرض قائمة الاتفاقيات
     * محمي من: Unauthorized Access, SQL Injection, XSS
     */
    public function index(Request $request)
    {
        // التحقق من الصلاحيات
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        try {
            // Sanitization
            $instructorId = filter_var($request->input('instructor_id'), FILTER_VALIDATE_INT);
            $status = strip_tags(trim($request->input('status', '')));
            $type = strip_tags(trim($request->input('type', '')));
            $search = strip_tags(trim($request->input('search', '')));

            $query = InstructorAgreement::with(['instructor', 'createdBy'])
                ->withCount(['payments', 'paidPayments']);

            if ($instructorId && $instructorId > 0) {
                $query->where('instructor_id', $instructorId);
            }

            if ($status && in_array($status, ['draft', 'active', 'suspended', 'terminated', 'completed'])) {
                $query->where('status', $status);
            }

            if ($type && in_array($type, ['course_price', 'hourly_rate', 'monthly_salary'])) {
                $query->where('type', $type);
            }

            if ($search && strlen($search) <= 255) {
                $search = preg_replace('/[^a-zA-Z0-9\s\u0600-\u06FF]/', '', $search);
                $query->where(function($q) use ($search) {
                    $q->where('agreement_number', 'like', '%' . $search . '%')
                      ->orWhere('title', 'like', '%' . $search . '%')
                      ->orWhereHas('instructor', function($q) use ($search) {
                          $q->where('name', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                      });
                });
            }

            $agreements = $query->orderBy('created_at', 'desc')->paginate(20);
            // جلب جميع المدربين (instructor أو teacher) من عمود role فقط لتجنب أخطاء علاقة roles
            $instructors = User::whereIn('role', ['instructor', 'teacher'])
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'phone', 'role']);

            $stats = [
                'total' => InstructorAgreement::count(),
                'active' => InstructorAgreement::where('status', InstructorAgreement::STATUS_ACTIVE)->count(),
                'draft' => InstructorAgreement::where('status', InstructorAgreement::STATUS_DRAFT)->count(),
                'total_earned' => AgreementPayment::where('status', AgreementPayment::STATUS_PAID)->sum('amount'),
            ];

            return view('admin.agreements.index', compact('agreements', 'instructors', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error loading agreements: ' . $e->getMessage());
            abort(500, 'حدث خطأ أثناء تحميل الاتفاقيات');
        }
    }

    public function create()
    {
        // جلب جميع المدربين (instructor أو teacher)
        $instructors = User::where(function($q) {
                $q->where('role', 'instructor')
                  ->orWhere('role', 'teacher')
                  ->orWhereHas('roles', function($roleQuery) {
                      $roleQuery->whereIn('name', ['instructor', 'teacher']);
                  });
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        return view('admin.agreements.create', compact('instructors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'instructor_id' => 'required|exists:users,id',
            'type' => 'required|in:course_price,hourly_rate,monthly_salary',
            'rate' => 'required|numeric|min:0',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:draft,active,suspended,terminated,completed',
            'terms' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $agreement = InstructorAgreement::create([
            'instructor_id' => $request->instructor_id,
            'type' => $request->type,
            'rate' => $request->rate,
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'terms' => $request->terms,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.agreements.show', $agreement)
            ->with('success', 'تم إنشاء الاتفاقية بنجاح');
    }

    public function show(InstructorAgreement $agreement)
    {
        $agreement->load(['instructor', 'createdBy', 'payments.course', 'payments.lecture']);
        
        $stats = [
            'total_earned' => $agreement->paidPayments()->sum('amount'),
            'pending_amount' => $agreement->approvedPayments()->sum('amount'),
            'total_payments' => $agreement->payments()->count(),
            'paid_payments' => $agreement->paidPayments()->count(),
        ];

        return view('admin.agreements.show', compact('agreement', 'stats'));
    }

    public function edit(InstructorAgreement $agreement)
    {
        // جلب جميع المدربين (instructor أو teacher)
        $instructors = User::where(function($q) {
                $q->where('role', 'instructor')
                  ->orWhere('role', 'teacher')
                  ->orWhereHas('roles', function($roleQuery) {
                      $roleQuery->whereIn('name', ['instructor', 'teacher']);
                  });
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        return view('admin.agreements.edit', compact('agreement', 'instructors'));
    }

    public function update(Request $request, InstructorAgreement $agreement)
    {
        $request->validate([
            'instructor_id' => 'required|exists:users,id',
            'type' => 'required|in:course_price,hourly_rate,monthly_salary',
            'rate' => 'required|numeric|min:0',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:draft,active,suspended,terminated,completed',
            'terms' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $agreement->update($request->only([
            'instructor_id', 'type', 'rate', 'title', 'description',
            'start_date', 'end_date', 'status', 'terms', 'notes',
        ]));

        return redirect()->route('admin.agreements.show', $agreement)
            ->with('success', 'تم تحديث الاتفاقية بنجاح');
    }

    public function destroy(InstructorAgreement $agreement)
    {
        if ($agreement->payments()->where('status', AgreementPayment::STATUS_PAID)->exists()) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف الاتفاقية لأنها تحتوي على مدفوعات مكتملة');
        }

        $agreement->delete();

        return redirect()->route('admin.agreements.index')
            ->with('success', 'تم حذف الاتفاقية بنجاح');
    }
}
