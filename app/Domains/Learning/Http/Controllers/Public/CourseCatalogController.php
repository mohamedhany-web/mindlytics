<?php

namespace App\Domains\Learning\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Domains\Learning\Models\AcademicSubject;
use App\Domains\Learning\Models\AdvancedCourse;
use App\Domains\Accounting\Models\Package;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseCatalogController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $level = (string) $request->query('level', '');
        $subjectId = $request->query('subject');
        $featuredOnly = $request->boolean('featured');

        $allowedLevels = ['beginner', 'intermediate', 'advanced'];
        if ($level !== '' && ! in_array($level, $allowedLevels, true)) {
            $level = '';
        }

        $coursesQuery = AdvancedCourse::query()
            ->where('is_active', true)
            ->visibleOnCurrentHost()
            ->publicCatalog()
            ->with(['academicSubject', 'instructor'])
            ->withCount(['lectures', 'lessons']);

        if ($q !== '') {
            $like = '%'.$q.'%';
            $coursesQuery->where(function ($builder) use ($like) {
                $builder->where('title', 'like', $like)
                    ->orWhere('title_en', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('description_en', 'like', $like);
            });
        }

        if ($level !== '') {
            $coursesQuery->where('level', $level);
        }

        if (filled($subjectId) && ctype_digit((string) $subjectId)) {
            $coursesQuery->where('academic_subject_id', (int) $subjectId);
        }

        if ($featuredOnly) {
            $coursesQuery->where('is_featured', true);
        }

        $courses = $coursesQuery
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        $subjects = AcademicSubject::query()
            ->whereHas('advancedCourses', function ($builder) {
                $builder->where('is_active', true)
                    ->visibleOnCurrentHost()
                    ->publicCatalog();
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        $packages = Package::active()
            ->visibleOnCurrentHost()
            ->whereHas('courses', function ($builder) {
                $builder->where('advanced_courses.is_active', true)
                    ->visibleOnCurrentHost()
                    ->publicCatalog();
            })
            ->with(['courses' => function ($query) {
                $query->where('is_active', true)->visibleOnCurrentHost()->publicCatalog();
            }])
            ->withCount([
                'courses' => function ($builder) {
                    $builder->where('advanced_courses.is_active', true)
                        ->visibleOnCurrentHost()
                        ->publicCatalog();
                },
            ])
            ->orderByDesc('is_featured')
            ->orderByDesc('is_popular')
            ->orderBy('order')
            ->limit(6)
            ->get();

        $filters = [
            'q' => $q,
            'level' => $level,
            'subject' => filled($subjectId) ? (string) $subjectId : '',
            'featured' => $featuredOnly,
        ];

        $totalPublic = AdvancedCourse::query()
            ->where('is_active', true)
            ->visibleOnCurrentHost()
            ->publicCatalog()
            ->count();

        return view('learning::public.courses', compact('courses', 'packages', 'subjects', 'filters', 'totalPublic'));
    }
}
