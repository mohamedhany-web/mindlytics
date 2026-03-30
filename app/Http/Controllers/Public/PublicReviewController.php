<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\AcademicYear;
use App\Models\CourseReview;
use App\Models\LearningPathEnrollment;
use App\Models\LearningPathReview;
use App\Models\StudentCourseEnrollment;
use Illuminate\Http\Request;

class PublicReviewController extends Controller
{
    public function storeCourse(Request $request, int $courseId)
    {
        $course = AdvancedCourse::query()
            ->whereKey($courseId)
            ->where('is_active', true)
            ->firstOrFail();

        $isEnrolled = StudentCourseEnrollment::query()
            ->where('user_id', auth()->id())
            ->where('advanced_course_id', $course->id)
            ->where('status', 'active')
            ->exists();

        if (! $isEnrolled) {
            return back()->withErrors(['error' => 'يجب التسجيل في الكورس أولاً لإضافة تقييم.']);
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        CourseReview::updateOrCreate(
            [
                'course_id' => $course->id,
                'user_id' => auth()->id(),
            ],
            [
                'rating' => (int) $validated['rating'],
                'comment' => $validated['comment'],
                'review' => $validated['comment'],
                'status' => 'pending',
                'is_verified_purchase' => true,
                'is_approved' => false,
            ]
        );

        // لا نذكر المراجعة الإدارية للطالب حسب الطلب
        return back()->with('success', 'تم النشر');
    }

    public function storeLearningPath(Request $request, string $slug)
    {
        $academicYear = AcademicYear::active()
            ->get()
            ->first(function ($year) use ($slug) {
                return \Illuminate\Support\Str::slug($year->name) === $slug;
            });

        if (! $academicYear) {
            abort(404);
        }

        $isEnrolled = LearningPathEnrollment::query()
            ->where('user_id', auth()->id())
            ->where('academic_year_id', $academicYear->id)
            ->where('status', 'active')
            ->exists();

        if (! $isEnrolled) {
            return back()->withErrors(['error' => 'يجب الاشتراك في المسار أولاً لإضافة تقييم.']);
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        LearningPathReview::updateOrCreate(
            [
                'academic_year_id' => $academicYear->id,
                'user_id' => auth()->id(),
            ],
            [
                'rating' => (int) $validated['rating'],
                'comment' => $validated['comment'],
                'status' => 'pending',
                'is_verified_purchase' => true,
                'is_approved' => false,
            ]
        );

        return back()->with('success', 'تم النشر');
    }
}

