<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AdvancedCourse;
use App\Models\LectureWatchProgress;
use App\Models\LessonProgress;
use App\Models\OfflineCourse;
use App\Models\PopupAd;
use App\Models\User;
use App\Support\BranchContext;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * الصفحة الرئيسية (Landing).
 * اللغة تُحدد عبر Middleware SetLandingLocale من ?lang= أو الجلسة.
 */
class LandingController extends Controller
{
    public function index(): View
    {
        $popupAd = null;
        $ad = PopupAd::activeNow()->first();
        if ($ad) {
            $key = 'popup_ad_' . $ad->id . '_views';
            $views = (int) session($key, 0);
            if ($views < $ad->max_views_per_visitor) {
                session([$key => $views + 1]);
                $popupAd = $ad;
            }
        }

        // نفس مسارات صفحة المسارات التعليمية بكل بياناتها (سعر المسار المستقل، عدد الكورسات، الصورة، إلخ)
        $landingPaths = $this->getPublicLearningPaths(12);

        $branch = app(BranchContext::class)->branch;

        $statsLearnersQuery = User::query()
            ->where('role', 'student')
            ->where('is_active', true);
        if ($branch) {
            $statsLearnersQuery->where('branch_id', $branch->id);
        }
        $statsLearners = $statsLearnersQuery->count();

        $statsCoursesAdvanced = AdvancedCourse::query()->where('is_active', true);
        if ($branch) {
            $statsCoursesAdvanced->where('branch_id', $branch->id);
        }
        $statsCourses = $statsCoursesAdvanced->count();
        $statsCourses += OfflineCourse::query()
            ->where('is_active', true)
            ->where('status', 'active')
            ->visibleOnCurrentHost()
            ->count();

        // خانة الشهادات في الصفحة الرئيسية تعرض نفس عدد الطلاب النشطين (حسب الطلب)
        $statsCertificates = $statsLearners;

        // إحصائية أهم للزائر: عدد المسارات التعليمية النشطة (Academic Years)
        $statsLearningPaths = AcademicYear::query()->where('is_active', true)->count();

        return view('welcome', compact(
            'popupAd',
            'landingPaths',
            'statsLearners',
            'statsCourses',
            'statsCertificates',
            'statsLearningPaths'
        ));
    }

    /**
     * جلب المسارات التعليمية بنفس منطق صفحة المسارات (للاستخدام في الصفحة الرئيسية أو أي عرض عام).
     * @param int|null $limit عدد المسارات (null = بدون حد)
     */
    public static function getPublicLearningPaths(?int $limit = null): \Illuminate\Support\Collection
    {
        $query = AcademicYear::where('is_active', true)
            ->with(['linkedCourses' => function ($q) {
                $q->where('is_active', true)->visibleOnCurrentHost();
            }, 'academicSubjects' => function ($q) {
                $q->where('is_active', true);
            }])
            ->withCount([
                'linkedCourses' => function ($q) {
                    $q->where('is_active', true)->visibleOnCurrentHost();
                },
                'academicSubjects',
            ])
            ->orderBy('order');

        if ($limit !== null) {
            $query->limit($limit);
        }

        $academicYears = $query->get();

        return $academicYears->map(function ($year) {
            $linkedCourses = $year->linkedCourses ?? collect();
            $subjectCourses = collect();
            if ($year->academicSubjects && $year->academicSubjects->isNotEmpty()) {
                $subjectIds = $year->academicSubjects->pluck('id')->toArray();
                if (!empty($subjectIds)) {
                    $subjectCourses = AdvancedCourse::where('is_active', true)
                        ->whereIn('academic_subject_id', $subjectIds)
                        ->visibleOnCurrentHost()
                        ->get();
                }
            }
            $courses = $linkedCourses->merge($subjectCourses)->unique('id');
            $slug = Str::slug($year->name);
            $thumb = $year->thumbnail ? str_replace('\\', '/', $year->thumbnail) : null;
            $imageUrl = $thumb ? asset('storage/' . $thumb) : null;

            return (object) [
                'id' => $year->id,
                'name' => $year->name,
                'description' => $year->description,
                'slug' => $slug,
                'price' => (float) ($year->price ?? 0),
                'courses_count' => $courses->count(),
                'thumbnail' => $year->thumbnail,
                'image_url' => $imageUrl,
                'icon' => $year->icon,
                'color' => $year->color,
                'code' => $year->code,
            ];
        });
    }
}
