<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Lecture;
use App\Models\AdvancedCourse;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LectureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $instructor = Auth::user();
        
        // جلب الكورسات التي يدرسها المدرب مع إحصائيات
        $query = AdvancedCourse::where('instructor_id', $instructor->id)
            ->withCount([
                'lectures',
                'enrollments',
                'sections'
            ])
            ->with(['academicYear', 'academicSubject']);
        
        // فلترة حسب الحالة
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $courses = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // إحصائيات عامة
        $stats = [
            'total_courses' => AdvancedCourse::where('instructor_id', $instructor->id)->count(),
            'active_courses' => AdvancedCourse::where('instructor_id', $instructor->id)->where('is_active', true)->count(),
            'total_lectures' => Lecture::where('instructor_id', $instructor->id)->count(),
            'scheduled_lectures' => Lecture::where('instructor_id', $instructor->id)->where('status', 'scheduled')->count(),
            'total_students' => \App\Models\StudentCourseEnrollment::whereHas('course', function($q) use ($instructor) {
                $q->where('instructor_id', $instructor->id);
            })->where('status', 'active')->count(),
        ];
        
        return view('instructor.lectures.index', compact('courses', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $instructor = Auth::user();
        
        $courses = AdvancedCourse::where('instructor_id', $instructor->id)
            ->where('is_active', true)
            ->orderBy('title')
            ->get();
        
        // جلب دروس الكورس المحدد (إذا كان موجوداً)
        $lessons = collect();
        if (request()->filled('course_id')) {
            $lessons = \App\Models\CourseLesson::where('advanced_course_id', request('course_id'))
                ->where('is_active', true)
                ->orderBy('order')
                ->get();
        }
        
        return view('instructor.lectures.create', compact('courses', 'lessons'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $instructor = Auth::user();
        
        $validated = $request->validate([
            'course_id' => 'required|exists:advanced_courses,id',
            'course_lesson_id' => 'nullable|exists:course_lessons,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'teams_registration_link' => 'nullable|url',
            'teams_meeting_link' => 'nullable|url',
            'recording_url' => 'nullable|url',
            'video_platform' => 'nullable|in:youtube,vimeo,google_drive,direct',
            'notes' => 'nullable|string',
            'has_attendance_tracking' => 'boolean',
            'has_assignment' => 'boolean',
            'has_evaluation' => 'boolean',
        ], [
            'course_id.required' => 'يجب اختيار الكورس',
            'course_id.exists' => 'الكورس المحدد غير موجود',
            'title.required' => 'عنوان المحاضرة مطلوب',
            'scheduled_at.required' => 'موعد المحاضرة مطلوب',
            'scheduled_at.date' => 'موعد المحاضرة يجب أن يكون تاريخ صحيح',
            'duration_minutes.required' => 'مدة المحاضرة مطلوبة',
            'duration_minutes.min' => 'مدة المحاضرة يجب أن تكون 15 دقيقة على الأقل',
            'duration_minutes.max' => 'مدة المحاضرة يجب ألا تتجاوز 480 دقيقة (8 ساعات)',
        ]);
        
        // التحقق من أن الكورس يخص هذا المدرب
        $course = AdvancedCourse::where('id', $validated['course_id'])
            ->where('instructor_id', $instructor->id)
            ->firstOrFail();
        
        $validated['instructor_id'] = $instructor->id;
        $validated['status'] = 'scheduled';
        $validated['has_attendance_tracking'] = $request->has('has_attendance_tracking');
        $validated['has_assignment'] = $request->has('has_assignment');
        $validated['has_evaluation'] = $request->has('has_evaluation');
        
        // التأكد من حفظ recording_url و video_platform بشكل صريح
        if ($request->has('recording_url')) {
            $validated['recording_url'] = $request->input('recording_url');
        } else {
            $validated['recording_url'] = null;
        }
        
        // إذا كان recording_url موجوداً و video_platform غير موجود، حاول اكتشافه
        if (isset($validated['recording_url']) && $validated['recording_url'] && !isset($validated['video_platform'])) {
            $recordingUrl = $validated['recording_url'];
            if (strpos($recordingUrl, 'youtube.com') !== false || strpos($recordingUrl, 'youtu.be') !== false) {
                $validated['video_platform'] = 'youtube';
            } elseif (strpos($recordingUrl, 'vimeo.com') !== false) {
                $validated['video_platform'] = 'vimeo';
            } elseif (strpos($recordingUrl, 'drive.google.com') !== false) {
                $validated['video_platform'] = 'google_drive';
            } elseif (preg_match('/\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i', $recordingUrl)) {
                $validated['video_platform'] = 'direct';
            }
        }
        
        // التأكد من حفظ video_platform إذا كان موجوداً في الطلب
        if ($request->has('video_platform') && $request->input('video_platform')) {
            $validated['video_platform'] = $request->input('video_platform');
        }
        
        \Log::info('Creating lecture', [
            'recording_url' => $validated['recording_url'] ?? 'not set',
            'video_platform' => $validated['video_platform'] ?? 'not set',
            'all_validated' => $validated
        ]);
        
        $lecture = Lecture::create($validated);
        
        // إذا كان الطلب AJAX (من popup)، أرجع JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء المحاضرة بنجاح',
                'lecture' => $lecture,
            ]);
        }
        
        return redirect()->route('instructor.lectures.show', $lecture)
            ->with('success', 'تم إنشاء المحاضرة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Lecture $lecture)
    {
        $instructor = Auth::user();
        
        // التحقق من أن المحاضرة تخص هذا المدرب
        if ($lecture->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بالوصول لهذه المحاضرة');
        }
        
        $lecture->load(['course', 'instructor', 'attendanceRecords.user']);
        
        // إذا كان الطلب AJAX، أرجع JSON
        if (request()->ajax() || request()->wantsJson()) {
            // إعادة تحميل المحاضرة من قاعدة البيانات للتأكد من أحدث البيانات
            $lecture->refresh();
            
            \Log::info('Returning lecture JSON', [
                'lecture_id' => $lecture->id,
                'recording_url' => $lecture->recording_url,
                'video_platform' => $lecture->video_platform,
            ]);
            
            return response()->json([
                'id' => $lecture->id,
                'title' => $lecture->title,
                'description' => $lecture->description,
                'course_id' => $lecture->course_id,
                'course_lesson_id' => $lecture->course_lesson_id,
                'scheduled_at' => $lecture->scheduled_at ? $lecture->scheduled_at->toIso8601String() : null,
                'duration_minutes' => $lecture->duration_minutes,
                'recording_url' => $lecture->recording_url ?? '', // التأكد من إرجاع string حتى لو كان null
                'video_platform' => $lecture->video_platform ?? '', // التأكد من إرجاع string حتى لو كان null
                'teams_registration_link' => $lecture->teams_registration_link ?? '',
                'teams_meeting_link' => $lecture->teams_meeting_link ?? '',
                'notes' => $lecture->notes ?? '',
                'has_attendance_tracking' => $lecture->has_attendance_tracking ?? false,
                'has_assignment' => $lecture->has_assignment ?? false,
                'has_evaluation' => $lecture->has_evaluation ?? false,
                'status' => $lecture->status ?? 'scheduled',
            ]);
        }
        
        // جلب الطلاب المسجلين في الكورس
        $enrollments = \App\Models\StudentCourseEnrollment::where('advanced_course_id', $lecture->course_id)
            ->where('status', 'active')
            ->with('user')
            ->get();
        
        // جلب سجلات الحضور
        $attendanceRecords = AttendanceRecord::where('lecture_id', $lecture->id)
            ->with('student')
            ->get()
            ->keyBy('student_id');
        
        // إحصائيات الحضور
        $attendanceStats = [
            'total_students' => $enrollments->count(),
            'present' => $attendanceRecords->where('status', 'present')->count(),
            'late' => $attendanceRecords->where('status', 'late')->count(),
            'absent' => $attendanceRecords->where('status', 'absent')->count(),
            'partial' => $attendanceRecords->where('status', 'partial')->count(),
        ];
        
        return view('instructor.lectures.show', compact('lecture', 'enrollments', 'attendanceRecords', 'attendanceStats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lecture $lecture)
    {
        $instructor = Auth::user();
        
        // التحقق من أن المحاضرة تخص هذا المدرب
        if ($lecture->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بتعديل هذه المحاضرة');
        }
        
        $courses = AdvancedCourse::where('instructor_id', $instructor->id)
            ->where('is_active', true)
            ->orderBy('title')
            ->get();
        
        // جلب دروس الكورس
        $lessons = \App\Models\CourseLesson::where('advanced_course_id', $lecture->course_id)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
        
        return view('instructor.lectures.edit', compact('lecture', 'courses', 'lessons'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lecture $lecture)
    {
        $instructor = Auth::user();
        
        // التحقق من أن المحاضرة تخص هذا المدرب
        if ($lecture->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بتعديل هذه المحاضرة');
        }
        
        $validated = $request->validate([
            'course_id' => 'required|exists:advanced_courses,id',
            'course_lesson_id' => 'nullable|exists:course_lessons,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'teams_registration_link' => 'nullable|url',
            'teams_meeting_link' => 'nullable|url',
            'recording_url' => 'nullable|url',
            'video_platform' => 'nullable|in:youtube,vimeo,google_drive,direct',
            'notes' => 'nullable|string',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'has_attendance_tracking' => 'boolean',
            'has_assignment' => 'boolean',
            'has_evaluation' => 'boolean',
        ]);
        
        // التحقق من أن الكورس يخص هذا المدرب
        $course = AdvancedCourse::where('id', $validated['course_id'])
            ->where('instructor_id', $instructor->id)
            ->firstOrFail();
        
        $validated['has_attendance_tracking'] = $request->has('has_attendance_tracking');
        $validated['has_assignment'] = $request->has('has_assignment');
        $validated['has_evaluation'] = $request->has('has_evaluation');
        
        $lecture->update($validated);
        
        // إذا كان الطلب AJAX (من popup)، أرجع JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث المحاضرة بنجاح',
                'lecture' => $lecture->fresh(),
            ]);
        }
        
        return redirect()->route('instructor.lectures.show', $lecture)
            ->with('success', 'تم تحديث المحاضرة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lecture $lecture)
    {
        $instructor = Auth::user();
        
        // التحقق من أن المحاضرة تخص هذا المدرب
        if ($lecture->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بحذف هذه المحاضرة');
        }
        
        $lecture->delete();
        
        // إذا كان الطلب AJAX (من popup)، أرجع JSON
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حذف المحاضرة بنجاح',
            ]);
        }
        
        return redirect()->route('instructor.lectures.index')
            ->with('success', 'تم حذف المحاضرة بنجاح');
    }

    /**
     * تحديث حالة الحضور لطالب
     */
    public function updateAttendance(Request $request, Lecture $lecture)
    {
        $instructor = Auth::user();
        
        // التحقق من أن المحاضرة تخص هذا المدرب
        if ($lecture->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بتحديث الحضور لهذه المحاضرة');
        }
        
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'status' => 'required|in:present,late,absent,partial',
            'joined_at' => 'nullable|date',
            'left_at' => 'nullable|date',
            'attendance_minutes' => 'nullable|integer|min:0',
        ]);
        
        $attendanceRecord = AttendanceRecord::updateOrCreate(
            [
                'lecture_id' => $lecture->id,
                'student_id' => $validated['student_id'],
            ],
            [
                'status' => $validated['status'],
                'joined_at' => $validated['joined_at'] ?? now(),
                'left_at' => $validated['left_at'] ?? null,
                'attendance_minutes' => $validated['attendance_minutes'] ?? 0,
                'total_minutes' => $lecture->duration_minutes,
                'attendance_percentage' => isset($validated['attendance_minutes']) && $validated['attendance_minutes'] > 0
                    ? ($validated['attendance_minutes'] / $lecture->duration_minutes) * 100 
                    : 0,
                'source' => 'manual',
            ]
        );
        
        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الحضور بنجاح',
            'record' => $attendanceRecord->load('student'),
        ]);
    }

    /**
     * تحديث حالة المحاضرة
     */
    public function updateStatus(Request $request, Lecture $lecture)
    {
        $instructor = Auth::user();
        
        // التحقق من أن المحاضرة تخص هذا المدرب
        if ($lecture->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بتحديث حالة هذه المحاضرة');
        }
        
        $validated = $request->validate([
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
        ]);
        
        $lecture->update(['status' => $validated['status']]);
        
        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة المحاضرة بنجاح',
            'lecture' => $lecture,
        ]);
    }
}
