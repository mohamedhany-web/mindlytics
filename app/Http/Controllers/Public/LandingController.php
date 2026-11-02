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

        $homeDiploma = SalesDiplomaBoardEntry::query()->active()->ordered()->with('academicYear')->first();
        $homeIntakes = $this->homeIntakes();

        return view('home.index', $data + [
            'homeDiploma' => $homeDiploma,
            'homeIntakes' => $homeIntakes,
            // نفس شرط صفحة المحاضر (InstructorController@show) عشان الرابط ما يرجعش 404
            'homeInstructors' => InstructorProfile::approved()->with('user')->orderBy('created_at')->limit(6)->get()
                ->filter(fn (InstructorProfile $p) => $p->user !== null && $p->user->isInstructor())->take(3)->values(),
            'homeFacts' => $this->homeFacts($homeIntakes),
        ]);
    }

    /**
     * الدفعات القادمة على الصفحة الرئيسية: من لوحة معلومات الدبلومات والكورسات (إدارة المبيعات).
     * الأقرب ميعاداً أولاً (دبلومات وكورسات)، واللي من غير تاريخ في الآخر.
     *
     * @return Collection<int, array{kind: string, type: string, name: string, start: ?string, days: ?string, mode: string, mode_key: string, price: ?string, url: ?string}>
     */
    /**
     * حقائق بتظهر في الرئيسية (الحضوري، التقسيط) بتتحسب من البيانات بدل ما تتكتب ثابتة.
     *
     * @return array{campus: bool, installments: bool}
     */
    private function homeFacts(Collection $intakes): array
    {
        $campus = $intakes->contains('mode_key', 'campus')
            || OfflineCourse::query()->where('is_active', true)->where('status', 'active')->visibleOnCurrentHost()->exists();

        return [
            'campus' => $campus,
            'installments' => \App\Domains\Accounting\Models\InstallmentPlan::query()->where('is_active', true)->exists(),
        ];
    }

    private function homeIntakes(): Collection
    {
        $ar = app()->getLocale() === 'ar';
        $mode = function (?string $format) use ($ar): array {
            $f = mb_strtolower((string) $format);

            return match (true) {
                str_contains($f, 'حضور') || str_contains($f, 'offline') || str_contains($f, 'hybrid') => ['campus', filled($format) ? (string) $format : ($ar ? 'حضوري' : 'On campus')],
                str_contains($f, 'مسجل') || str_contains($f, 'مسجّل') || str_contains($f, 'record') => ['recorded', (string) $format],
                default => ['online', filled($format) ? (string) $format : ($ar ? 'أونلاين مباشر' : 'Live online')],
            };
        };
        // السعر من نفس مصدر الـ checkout (المسار أو الكورس المربوط)، وإلا سعر اللوحة
        $price = fn (?float $amount) => match (true) {
            $amount === null => null,
            $amount <= 0 => $ar ? 'مجاني' : 'Free',
            default => number_format($amount, 0).($ar ? ' ج.م' : ' EGP'),
        };
        $plan = function (SalesCourseBoardEntry $e) use ($ar): ?string {
            $p = $e->installmentPlan();

            return $p && (int) $p->installments_count > 1 ? ($ar ? 'تقسيط على '.$p->installments_count.' أقساط' : $p->installments_count.' instalments') : null;
        };

        $diplomas = SalesDiplomaBoardEntry::query()->active()->ordered()->with('academicYear')->limit(12)->get()
            ->map(function (SalesDiplomaBoardEntry $e) use ($mode, $price, $ar) {
                [$key, $label] = $mode($e->format);

                return [
                    'kind' => 'diploma', 'type' => $ar ? 'دبلومة' : 'Diploma', 'name' => (string) $e->name,
                    'start' => $e->startDisplay(), 'sort' => $e->starts_at?->timestamp ?? PHP_INT_MAX,
                    'days' => filled($e->schedule_days) ? (string) $e->schedule_days : null,
                    'mode' => $label, 'mode_key' => $key, 'price' => $price($e->publicPrice()), 'url' => $e->detailsUrl(), 'installments' => null,
                ];
            });

        $courses = SalesCourseBoardEntry::query()->active()->ordered()->with('advancedCourse')->limit(12)->get()
            ->map(function (SalesCourseBoardEntry $e) use ($mode, $price, $plan, $ar) {
                [$key, $label] = $mode($e->format);
                return [
                    'kind' => 'course', 'type' => $ar ? 'كورس' : 'Course', 'name' => (string) $e->name,
                    'start' => $e->startDisplay(), 'sort' => $e->starts_at?->timestamp ?? PHP_INT_MAX - 1,
                    'days' => filled($e->schedule_days) ? (string) $e->schedule_days : null,
                    'mode' => $label, 'mode_key' => $key, 'price' => $price($e->publicPrice()), 'url' => $e->detailsUrl(), 'installments' => $plan($e),
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
