<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\AdvancedCourse;
use App\Models\OfflineCourseGroup;
use App\Support\AcademyWhatsApp;
use App\Services\PaymentGatewaySettings;
use App\Support\PlatformSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AcademicYearController extends Controller
{
    /**
     * بوابة الاكتشاف: مسار كامل | كورسات مسجّلة | لايف
     */
    public function index(Request $request)
    {
        $intent = $request->query('intent', 'choose');
        if (! in_array($intent, ['choose', 'path', 'recorded', 'live'], true)) {
            $intent = 'choose';
        }

        $academicYears = AcademicYear::where('is_active', true)
            ->visibleOnCurrentHost()
            ->withCount('academicSubjects')
            ->orderBy('order')
            ->get();

        $allCourses = AdvancedCourse::where('is_active', true)
            ->visibleOnCurrentHost()
            ->publicCatalog()
            ->select([
                'id',
                'title',
                'category',
                'programming_language',
                'framework',
                'level',
                'duration_hours',
                'duration_minutes',
                'rating',
                'skills',
                'price',
                'is_free',
                'thumbnail',
                'created_at',
            ])
            ->orderByDesc('created_at')
            ->get();

        $tracks = $academicYears->map(function (AcademicYear $year) use ($allCourses) {
            return $this->hydrateTrack($year, $allCourses);
        });

        $recordedCourses = $allCourses->take(48)->map(function (AdvancedCourse $course) {
            $checkoutUrl = route('public.course.checkout', ['courseId' => $course->id, 'from' => 'portal']);
            $course->setAttribute('portal_checkout_url', $checkoutUrl);
            $course->setAttribute('whatsapp_url', AcademyWhatsApp::bookingUrl([
                'type' => 'recorded',
                'title' => $course->localized('title') ?: $course->title,
                'code' => 'COURSE-' . $course->id,
                'url' => $checkoutUrl,
                'price' => $course->is_free ? 'مجاني' : number_format((float) $course->price, 0),
            ]));

            return $course;
        });

        $offlineGroups = OfflineCourseGroup::query()
            ->where('is_active', true)
            ->where('status', 'active')
            ->where('public_booking_enabled', true)
            ->whereNotNull('public_slug')
            ->with(['course:id,title,price,is_active,status', 'instructor:id,name'])
            ->orderByDesc('start_date')
            ->limit(40)
            ->get()
            ->filter(fn (OfflineCourseGroup $g) => $g->course && $g->course->is_active && $g->course->status === 'active')
            ->values()
            ->map(function (OfflineCourseGroup $group) {
                $bookUrl = route('public.offline-groups.show', $group->public_slug);
                $group->setAttribute('channel', 'offline');
                $group->setAttribute('book_url', $bookUrl);
                $group->setAttribute('seats_left', $group->effectiveAvailableSeats('offline'));
                $group->setAttribute('whatsapp_url', AcademyWhatsApp::bookingUrl([
                    'type' => 'live_offline',
                    'title' => $group->name,
                    'code' => 'OFF-' . $group->id,
                    'url' => $bookUrl,
                    'price' => number_format((float) ($group->course->price ?? 0), 0),
                    'channel' => 'أوفلاين',
                ]));

                return $group;
            });

        $onlineGroups = OfflineCourseGroup::query()
            ->where('is_active', true)
            ->where('status', 'active')
            ->where('online_booking_enabled', true)
            ->whereNotNull('online_slug')
            ->with(['course:id,title,price,is_active,status', 'instructor:id,name'])
            ->orderByDesc('start_date')
            ->limit(40)
            ->get()
            ->filter(fn (OfflineCourseGroup $g) => $g->course && $g->course->is_active && $g->course->status === 'active')
            ->values()
            ->map(function (OfflineCourseGroup $group) {
                $bookUrl = route('public.online-groups.show', $group->online_slug);
                $group->setAttribute('channel', 'online');
                $group->setAttribute('book_url', $bookUrl);
                $group->setAttribute('seats_left', $group->effectiveAvailableSeats('online'));
                $group->setAttribute('whatsapp_url', AcademyWhatsApp::bookingUrl([
                    'type' => 'live_online',
                    'title' => $group->name,
                    'code' => 'ON-' . $group->id,
                    'url' => $bookUrl,
                    'price' => number_format((float) ($group->course->price ?? 0), 0),
                    'channel' => 'أونلاين',
                ]));

                return $group;
            });

        $tracks = $tracks->map(function (AcademicYear $year) {
            $slug = Str::slug($year->name) ?: ('year-' . $year->id);
            $pathUrl = route('public.learning-path.show', $slug);
            $checkoutUrl = route('public.learning-path.checkout', $slug);
            $year->setAttribute('path_url', $pathUrl);
            $year->setAttribute('path_checkout_url', $checkoutUrl);
            $year->setAttribute('whatsapp_url', AcademyWhatsApp::bookingUrl([
                'type' => 'path',
                'title' => $year->name,
                'code' => $year->code ?: ('PATH-' . $year->id),
                'url' => $pathUrl,
            ]));

            return $year;
        });

        return view('student.academic-years.index', [
            'intent' => $intent,
            'tracks' => $tracks,
            'recordedCourses' => $recordedCourses,
            'offlineGroups' => $offlineGroups,
            'onlineGroups' => $onlineGroups,
            'fawaterakEnabled' => PaymentGatewaySettings::isFawaterakEnabled(),
            'academyWhatsapp' => AcademyWhatsApp::academyDigits(),
            'contactWhatsapp' => PlatformSettings::contactPage()['whatsapp'] ?? '',
        ]);
    }

    /**
     * عرض المواد الدراسية لسنة معينة
     */
    public function subjects(AcademicYear $academicYear)
    {
        $subjects = AcademicSubject::where('academic_year_id', $academicYear->id)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        $allCourses = AdvancedCourse::where('is_active', true)
            ->visibleOnCurrentHost()
            ->select([
                'id',
                'title',
                'category',
                'programming_language',
                'framework',
                'level',
                'duration_hours',
                'duration_minutes',
                'rating',
                'skills',
                'price',
                'is_free',
                'created_at',
            ])
            ->get();

        $trackCourses = $this->filterCourses($allCourses, [$academicYear->code, $academicYear->name]);
        if ($trackCourses->isEmpty()) {
            $trackCourses = $allCourses;
        }

        $subjects = $subjects->map(function (AcademicSubject $subject) use ($trackCourses, $allCourses) {
            return $this->hydrateSubject($subject, $trackCourses, $allCourses);
        });

        return view('student.academic-years.subjects', [
            'academicYear' => $academicYear,
            'subjects' => $subjects,
        ]);
    }

    private function hydrateTrack(AcademicYear $year, Collection $courses): AcademicYear
    {
        $matchedCourses = $this->filterCourses($courses, [$year->code, $year->name, $year->description]);
        if ($matchedCourses->isEmpty()) {
            $matchedCourses = $courses;
        }

        $languages = $matchedCourses->pluck('programming_language')->filter()->unique()->values();
        $frameworks = $matchedCourses->pluck('framework')->filter()->unique()->values();
        $levels = $matchedCourses->pluck('level')->filter()->unique()->values();
        $minutes = $matchedCourses->sum(function ($course) {
            return ((int) ($course->duration_hours ?? 0) * 60) + (int) ($course->duration_minutes ?? 0);
        });

        $avgMinutes = $matchedCourses->count() > 0 ? (int) round($minutes / $matchedCourses->count()) : 0;

        $year->setAttribute('track_metrics', [
            'courses_count' => $matchedCourses->count(),
            'languages' => $languages->take(6),
            'frameworks' => $frameworks->take(6),
            'levels' => $levels,
            'avg_duration' => $this->formatDurationMinutes($avgMinutes),
            'avg_rating' => $matchedCourses->count() > 0 ? round((float) ($matchedCourses->avg('rating') ?? 0), 1) : null,
        ]);

        $year->setRelation('preview_courses', $matchedCourses->sortByDesc('created_at')->take(3));

        return $year;
    }

    private function hydrateSubject(AcademicSubject $subject, Collection $trackCourses, Collection $allCourses): AcademicSubject
    {
        $identifiers = [$subject->code, $subject->name, $subject->description];
        $matchedCourses = $this->filterCourses($trackCourses, $identifiers);

        if ($matchedCourses->isEmpty()) {
            $matchedCourses = $this->filterCourses($allCourses, $identifiers);
        }

        $languages = $matchedCourses->pluck('programming_language')->filter()->unique()->values();
        $frameworks = $matchedCourses->pluck('framework')->filter()->unique()->values();
        $levels = $matchedCourses->pluck('level')->filter()->unique()->values();

        $subject->setAttribute('catalog_stats', [
            'courses_count' => $matchedCourses->count(),
            'languages' => $languages->take(5),
            'frameworks' => $frameworks->take(5),
            'levels' => $levels,
        ]);

        $subject->setRelation('preview_courses', $matchedCourses->sortByDesc('created_at')->take(3));

        return $subject;
    }

    private function filterCourses(Collection $courses, array $identifiers): Collection
    {
        $needles = collect($identifiers)
            ->filter()
            ->map(function ($value) {
                return Str::of($value)->lower()->replace(['-', '_'], ' ')->squish();
            })
            ->filter(function ($value) {
                return $value->isNotEmpty();
            });

        if ($needles->isEmpty()) {
            return collect();
        }

        return $courses->filter(function (AdvancedCourse $course) use ($needles) {
            $fields = collect([
                $course->category,
                $course->programming_language,
                $course->framework,
                $course->level,
                $course->title,
            ])->merge((array) ($course->skills ?? []));

            return $fields->contains(function ($field) use ($needles) {
                if (empty($field)) {
                    return false;
                }

                $fieldValue = Str::of($field)->lower()->replace(['-', '_'], ' ')->squish();

                foreach ($needles as $needle) {
                    if ($needle->isNotEmpty() && Str::contains($fieldValue, $needle)) {
                        return true;
                    }
                }

                return false;
            });
        })->values();
    }

    private function formatDurationMinutes(int $minutes): ?string
    {
        if ($minutes <= 0) {
            return null;
        }

        $hours = intdiv($minutes, 60);
        $remaining = $minutes % 60;

        if ($hours === 0) {
            return $remaining . ' د';
        }

        if ($remaining === 0) {
            return $hours . ' س';
        }

        return $hours . ' س ' . $remaining . ' د';
    }
}
