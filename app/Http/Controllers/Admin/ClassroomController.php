<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassroomController extends Controller
{
    /**
     * عرض قائمة الفصول الدراسية
     */
    public function index(Request $request)
    {
        $query = Classroom::with(['school', 'teacher'])
            ->withCount('students');

        // فلترة حسب المدرسة
        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        // فلترة حسب المعلم
        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // البحث في الاسم
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $classrooms = $query->orderBy('created_at', 'desc')->paginate(15);

        // بيانات للفلاتر
        $schools = School::where('is_active', true)->get();
        $teachers = User::where('role', 'instructor')->get();

        return view('admin.classrooms.index', compact('classrooms', 'schools', 'teachers'));
    }

    /**
     * عرض صفحة إنشاء فصل جديد
     */
    public function create()
    {
        $schools = School::where('is_active', true)->get();
        $teachers = User::where('role', 'instructor')->get();

        return view('admin.classrooms.create', compact('schools', 'teachers'));
    }

    /**
     * حفظ فصل جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'school_id' => 'required|exists:schools,id',
            'teacher_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'اسم الفصل مطلوب',
            'name.max' => 'اسم الفصل لا يجب أن يتجاوز 255 حرف',
            'school_id.required' => 'المدرسة مطلوبة',
            'school_id.exists' => 'المدرسة المحددة غير موجودة',
            'teacher_id.exists' => 'المعلم المحدد غير موجود',
        ]);

        Classroom::create($request->all());

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'تم إنشاء الفصل بنجاح');
    }

    /**
     * عرض تفاصيل فصل محدد
     */
    public function show(Classroom $classroom)
    {
        $classroom->load(['school', 'teacher', 'students', 'courses']);

        return view('admin.classrooms.show', compact('classroom'));
    }

    /**
     * عرض صفحة تعديل فصل
     */
    public function edit(Classroom $classroom)
    {
        $schools = School::where('is_active', true)->get();
        $teachers = User::where('role', 'instructor')->get();

        return view('admin.classrooms.edit', compact('classroom', 'schools', 'teachers'));
    }

    /**
     * تحديث بيانات فصل
     */
    public function update(Request $request, Classroom $classroom)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'school_id' => 'required|exists:schools,id',
            'teacher_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'اسم الفصل مطلوب',
            'name.max' => 'اسم الفصل لا يجب أن يتجاوز 255 حرف',
            'school_id.required' => 'المدرسة مطلوبة',
            'school_id.exists' => 'المدرسة المحددة غير موجودة',
            'teacher_id.exists' => 'المعلم المحدد غير موجود',
        ]);

        $classroom->update($request->all());

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'تم تحديث الفصل بنجاح');
    }

    /**
     * حذف فصل
     */
    public function destroy(Classroom $classroom)
    {
        try {
            $classroom->delete();
            return redirect()->route('admin.classrooms.index')
                ->with('success', 'تم حذف الفصل بنجاح');
        } catch (\Exception $e) {
            return redirect()->route('admin.classrooms.index')
                ->with('error', 'حدث خطأ أثناء حذف الفصل');
        }
    }

    /**
     * تبديل حالة الفصل (نشط/غير نشط)
     */
    public function toggleStatus(Classroom $classroom)
    {
        $classroom->update([
            'is_active' => !$classroom->is_active
        ]);

        $status = $classroom->is_active ? 'تم تفعيل' : 'تم إلغاء تفعيل';
        
        return response()->json([
            'success' => true,
            'message' => $status . ' الفصل بنجاح',
            'is_active' => $classroom->is_active
        ]);
    }

    /**
     * إدارة الطلاب في الفصل
     */
    public function students(Classroom $classroom)
    {
        $classroom->load(['students', 'school']);
        $availableStudents = User::where('role', 'student')
            ->whereNotIn('id', $classroom->students->pluck('id'))
            ->get();

        return view('admin.classrooms.students', compact('classroom', 'availableStudents'));
    }

    /**
     * إضافة طالب إلى الفصل
     */
    public function addStudent(Request $request, Classroom $classroom)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
        ], [
            'student_id.required' => 'الطالب مطلوب',
            'student_id.exists' => 'الطالب المحدد غير موجود',
        ]);

        // التحقق من أن المستخدم طالب
        $student = User::where('id', $request->student_id)
            ->where('role', 'student')
            ->first();

        if (!$student) {
            return back()->with('error', 'المستخدم المحدد ليس طالباً');
        }

        // التحقق من عدم وجود الطالب في الفصل
        if ($classroom->students()->where('student_id', $request->student_id)->exists()) {
            return back()->with('error', 'الطالب موجود بالفعل في هذا الفصل');
        }

        $classroom->students()->attach($request->student_id, [
            'enrolled_at' => now(),
            'is_active' => true,
        ]);

        return back()->with('success', 'تم إضافة الطالب إلى الفصل بنجاح');
    }

    /**
     * إزالة طالب من الفصل
     */
    public function removeStudent(Classroom $classroom, User $student)
    {
        $classroom->students()->detach($student->id);

        return back()->with('success', 'تم إزالة الطالب من الفصل بنجاح');
    }
}