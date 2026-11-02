<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Domains\Learning\Models\AcademicYear;
use App\Domains\Learning\Models\AdvancedCourse;
use App\Domains\Learning\Models\LectureWatchProgress;
use App\Domains\Learning\Models\LessonProgress;
use App\Domains\Learning\Models\InstructorProfile;
use App\Domains\Offline\Models\OfflineCourse;
use App\Domains\Marketing\Models\PopupAd;
use App\Domains\Sales\Models\SalesCourseBoardEntry;
use App\Domains\Sales\Models\SalesDiplomaBoardEntry;
use App\Models\User;
use App\Support\BranchContext;
use Illuminate\Support\Collection;
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

        $statsCoursesAdvanced = AdvancedCourse::query()
            ->where('is_active', true)
            ->publicCatalog();
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
        $statsLearningPaths = AcademicYear::query()->where('is_active', true)->visibleOnCurrentHost()->count();

        // الكورسات المميزة على الصفحة الرئيسية: نفس فلترة النطاق الفرعي (لا تعرض كورسات المركز على sudan.*)
        $featuredCourses = AdvancedCourse::query()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->publicCatalog()
            ->visibleOnCurrentHost()
            ->with(['academicSubject', 'instructor'])
            ->withCount('lessons')
            ->orderByDesc('created_at')
            ->limit(12)
            ->get();

        $data = compact(
            'popupAd',
            'landingPaths',
            'statsLearners',
            'statsCourses',
            'statsCertificates',
            'statsLearningPaths',
            'featuredCourses'
        );

        // الصفحة الجديدة (docs/HOMEPAGE.md) بالعربي؛ الإنجليزي يفضل على الصفحة القديمة لحد ما تتترجم
        if (app()->getLocale() !== 'ar') {
            return view('welcome', $data);
        }

        $homeDiploma = SalesDiplomaBoardEntry::query()->active()->ordered()->first();

        return view('home.index', $data + [
            'homeDiploma' => $homeDiploma,
            'homeIntakes' => $this->homeIntakes(),
            'homeInstructors' => InstructorProfile::approved()->with('user')->orderBy('created_at')->limit(3)->get()
                ->filter(fn (InstructorProfile $p) => $p->user !== null)->values(),
        ]);
    }

    /**
     * الدفعات القادمة على الصفحة الرئيسية: من لوحة معلومات الدبلومات والكورسات (إدارة المبيعات).
     * الأقرب ميعاداً أولاً (دبلومات وكورسات)، واللي من غير تاريخ في الآخر.
     *
     * @return Collection<int, array{kind: string, type: string, name: string, start: ?string, days: ?string, mode: string, mode_key: string, price: ?string, url: ?string}>
     */
    private function homeIntakes(): Collection
    {
        $mode = function (?string $format): array {
            $f = mb_strtolower((string) $format);

            return match (true) {
                str_contains($f, 'حضور') || str_contains($f, 'offline') || str_contains($f, 'hybrid') => ['campus', filled($format) ? (string) $format : 'حضوري'],
                str_contains($f, 'مسجل') || str_contains($f, 'مسجّل') || str_contains($f, 'record') => ['recorded', (string) $format],
                default => ['online', filled($format) ? (string) $format : 'أونلاين مباشر'],
            };
        };
        $price = fn ($online) => $online !== null && (float) $online > 0 ? number_format((float) $online, 0).' ج.م' : null;

        $diplomas = SalesDiplomaBoardEntry::query()->active()->ordered()->limit(12)->get()
            ->map(function (SalesDiplomaBoardEntry $e) use ($mode, $price) {
                [$key, $label] = $mode($e->format);

                return [
                    'kind' => 'diploma', 'type' => 'دبلومة', 'name' => (string) $e->name,
                    'start' => $e->startDisplay(), 'sort' => $e->starts_at?->timestamp ?? PHP_INT_MAX,
                    'days' => filled($e->schedule_days) ? (string) $e->schedule_days : null,
                    'mode' => $label, 'mode_key' => $key, 'price' => $price($e->price_online), 'url' => $e->landingUrl(),
                ];
            });

        $courses = SalesCourseBoardEntry::query()->active()->ordered()->limit(12)->get()
            ->map(function (SalesCourseBoardEntry $e) use ($mode, $price) {
                [$key, $label] = $mode($e->format);
                return [
                    'kind' => 'course', 'type' => 'كورس', 'name' => (string) $e->name,
                    'start' => $e->startDisplay(), 'sort' => $e->starts_at?->timestamp ?? PHP_INT_MAX - 1,
                    'days' => filled($e->schedule_days) ? (string) $e->schedule_days : null,
                    'mode' => $label, 'mode_key' => $key, 'price' => $price($e->price_online), 'url' => $e->landingUrl(),
                ];
            });

        return $diplomas->concat($courses)->sortBy('sort')->take(9)
            ->map(fn (array $i) => collect($i)->except('sort')->all())->values();
    }

    /**
     * جلب المسارات التعليمية بنفس منطق صفحة المسارات (للاستخدام في الصفحة الرئيسية أو أي عرض عام).
     * @param int|null $limit عدد المسارات (null = بدون حد)
     */
    public static function getPublicLearningPaths(?int $limit = null): \Illuminate\Support\Collection
    {
        $query = AcademicYear::where('is_active', true)
            ->visibleOnCurrentHost()
            ->with(['linkedCourses' => function ($q) {
                $q->where('is_active', true)->visibleOnCurrentHost()->publicCatalog();
            }, 'academicSubjects' => function ($q) {
                $q->where('is_active', true);
            }])
            ->withCount([
                'linkedCourses' => function ($q) {
                    $q->where('is_active', true)->visibleOnCurrentHost()->publicCatalog();
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
                        ->publicCatalog()
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
