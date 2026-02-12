<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstructorAgreement;
use App\Models\OfflineCourse;
use App\Models\User;
use Illuminate\Http\Request;

class OfflineAgreementController extends Controller
{
    /**
     * عرض قائمة اتفاقيات المدربين
     */
    public function index(Request $request)
    {
        $query = InstructorAgreement::with(['instructor', 'course']);

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('agreement_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('instructor', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب المدرب
        if ($request->filled('instructor_id')) {
            $query->where('instructor_id', $request->instructor_id);
        }

        $agreements = $query->latest()->paginate(20);

        $instructors = User::where('role', 'instructor')->where('is_active', true)->get();
        $offlineCourses = OfflineCourse::where('is_active', true)->get();

        return view('admin.offline-agreements.index', compact('agreements', 'instructors', 'offlineCourses'));
    }

    /**
     * عرض صفحة إنشاء اتفاقية
     */
    public function create()
    {
        $instructors = User::where('role', 'instructor')->where('is_active', true)->get();
        $offlineCourses = OfflineCourse::where('is_active', true)->get();

        return view('admin.offline-agreements.create', compact('instructors', 'offlineCourses'));
    }

    /**
     * حفظ اتفاقية جديدة
     */
    public function store(Request $request)
    {
        $rules = [
            'instructor_id' => 'required|exists:users,id',
            'offline_course_id' => 'nullable|exists:offline_courses,id',
            'billing_type' => 'required|in:per_session,monthly,full_course',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'salary_per_session' => 'nullable|numeric|min:0',
            'sessions_count' => 'nullable|integer|min:0',
            'monthly_amount' => 'nullable|numeric|min:0',
            'months_count' => 'nullable|integer|min:1',
            'total_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,active,completed,cancelled',
            'terms' => 'nullable|string',
            'notes' => 'nullable|string',
        ];

        $validated = $request->validate($rules);

        $billingType = $validated['billing_type'];

        if ($billingType === 'per_session') {
            $validated['salary_per_session'] = (float) ($request->input('salary_per_session', 0) ?: 0);
            $validated['sessions_count'] = (int) ($request->input('sessions_count', 0) ?: 0);
            $validated['total_amount'] = $validated['salary_per_session'] * max(1, $validated['sessions_count']);
            $validated['monthly_amount'] = null;
            $validated['months_count'] = null;
        } elseif ($billingType === 'monthly') {
            $validated['monthly_amount'] = (float) ($request->input('monthly_amount', 0) ?: 0);
            $validated['months_count'] = (int) ($request->input('months_count', 1) ?: 1);
            $validated['total_amount'] = $validated['monthly_amount'] * max(1, $validated['months_count']);
            $validated['salary_per_session'] = 0;
            $validated['sessions_count'] = 0;
        } else {
            $validated['total_amount'] = (float) ($request->input('total_amount', 0) ?: 0);
            $validated['salary_per_session'] = 0;
            $validated['sessions_count'] = 0;
            $validated['monthly_amount'] = null;
            $validated['months_count'] = null;
        }

        $validated['agreement_number'] = InstructorAgreement::generateAgreementNumber();

        InstructorAgreement::create($validated);

        return redirect()->route('admin.offline-agreements.index')
                        ->with('success', 'تم إنشاء الاتفاقية بنجاح');
    }

    /**
     * عرض تفاصيل اتفاقية
     */
    public function show(InstructorAgreement $agreement)
    {
        $agreement->load(['instructor', 'course']);
        
        return view('admin.offline-agreements.show', compact('agreement'));
    }

    /**
     * عرض صفحة تعديل اتفاقية
     */
    public function edit(InstructorAgreement $agreement)
    {
        $instructors = User::where('role', 'instructor')->where('is_active', true)->get();
        $offlineCourses = OfflineCourse::where('is_active', true)->get();

        return view('admin.offline-agreements.edit', compact('agreement', 'instructors', 'offlineCourses'));
    }

    /**
     * تحديث اتفاقية
     */
    public function update(Request $request, InstructorAgreement $agreement)
    {
        $rules = [
            'instructor_id' => 'required|exists:users,id',
            'offline_course_id' => 'nullable|exists:offline_courses,id',
            'billing_type' => 'required|in:per_session,monthly,full_course',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'salary_per_session' => 'nullable|numeric|min:0',
            'sessions_count' => 'nullable|integer|min:0',
            'monthly_amount' => 'nullable|numeric|min:0',
            'months_count' => 'nullable|integer|min:1',
            'total_amount' => 'nullable|numeric|min:0',
            'payment_status' => 'required|in:pending,partial,paid,overdue',
            'status' => 'required|in:draft,active,completed,cancelled',
            'terms' => 'nullable|string',
            'notes' => 'nullable|string',
        ];

        $validated = $request->validate($rules);

        $billingType = $validated['billing_type'];

        if ($billingType === 'per_session') {
            $validated['salary_per_session'] = (float) ($request->input('salary_per_session', 0) ?: 0);
            $validated['sessions_count'] = (int) ($request->input('sessions_count', 0) ?: 0);
            $validated['total_amount'] = $validated['salary_per_session'] * max(1, $validated['sessions_count']);
            $validated['monthly_amount'] = null;
            $validated['months_count'] = null;
        } elseif ($billingType === 'monthly') {
            $validated['monthly_amount'] = (float) ($request->input('monthly_amount', 0) ?: 0);
            $validated['months_count'] = (int) ($request->input('months_count', 1) ?: 1);
            $validated['total_amount'] = $validated['monthly_amount'] * max(1, $validated['months_count']);
            $validated['salary_per_session'] = 0;
            $validated['sessions_count'] = 0;
        } else {
            $validated['total_amount'] = (float) ($request->input('total_amount', 0) ?: 0);
            $validated['salary_per_session'] = 0;
            $validated['sessions_count'] = 0;
            $validated['monthly_amount'] = null;
            $validated['months_count'] = null;
        }

        $agreement->update($validated);

        return redirect()->route('admin.offline-agreements.show', $agreement)
                        ->with('success', 'تم تحديث الاتفاقية بنجاح');
    }

    /**
     * حذف اتفاقية
     */
    public function destroy(InstructorAgreement $agreement)
    {
        $agreement->delete();

        return redirect()->route('admin.offline-agreements.index')
                        ->with('success', 'تم حذف الاتفاقية بنجاح');
    }
}
