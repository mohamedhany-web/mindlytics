<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\PortfolioProject;
use App\Models\PortfolioProjectReview;
use App\Services\JourneyAchievementService;
use App\Services\JourneyProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PortfolioReviewController extends Controller
{
    public function __construct(
        private JourneyProfileService $journeyProfiles,
        private JourneyAchievementService $achievements,
    ) {
    }

    public function index(Request $request)
    {
        $query = $this->reviewableQuery();

        $status = $request->get('status');
        $allowed = [
            PortfolioProject::STATUS_PENDING_REVIEW,
            PortfolioProject::STATUS_RESUBMITTED,
            PortfolioProject::STATUS_CHANGES_REQUESTED,
            PortfolioProject::STATUS_APPROVED,
            PortfolioProject::STATUS_REJECTED,
            PortfolioProject::STATUS_PUBLISHED,
        ];

        if ($status && in_array($status, $allowed, true)) {
            $query->where('status', $status);
        } else {
            $query->whereIn('status', [
                PortfolioProject::STATUS_PENDING_REVIEW,
                PortfolioProject::STATUS_RESUBMITTED,
                PortfolioProject::STATUS_APPROVED,
            ]);
            $status = null;
        }

        $projects = $query->with([
            'user:id,name,profile_image,profile_image_disk',
            'academicYear:id,name',
            'advancedCourse:id,title',
            'offlineCourse:id,title,online_only',
        ])->latest()->paginate(15);

        return view('instructor.portfolio.index', compact('projects', 'status'));
    }

    public function show(PortfolioProject $project)
    {
        $this->authorizeReview($project);
        $project->load([
            'user',
            'academicYear',
            'advancedCourse',
            'offlineCourse',
            'images',
            'reviews.reviewer:id,name',
        ]);

        return view('instructor.portfolio.show', compact('project'));
    }

    public function approve(Request $request, PortfolioProject $project)
    {
        $this->authorizeReview($project);
        if (! $project->isReviewable()) {
            return back()->with('error', 'المشروع ليس في قائمة المراجعة حالياً.');
        }

        $data = $this->validatedRubric($request, false);

        DB::transaction(function () use ($project, $data, $request) {
            $project->fill([
                'status' => PortfolioProject::STATUS_APPROVED,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'instructor_notes' => $data['instructor_notes'] ?? null,
                'rejected_reason' => null,
            ]);
            $project->applyRubricScores($data);
            $project->save();

            $this->storeReview($project, PortfolioProjectReview::DECISION_APPROVED, $data);
        });

        return back()->with('success', 'تم اعتماد المشروع. يمكنك نشره في المعرض عند الاستعداد.');
    }

    public function reject(Request $request, PortfolioProject $project)
    {
        $this->authorizeReview($project);
        if (! $project->isReviewable()) {
            return back()->with('error', 'المشروع ليس في قائمة المراجعة حالياً.');
        }

        $data = $request->validate([
            'rejected_reason' => 'required|string|max:1000',
            'instructor_notes' => 'nullable|string|max:2000',
        ], [
            'rejected_reason.required' => 'سبب الرفض مطلوب حتى يفهم الطالب التعديل المطلوب.',
        ]);

        DB::transaction(function () use ($project, $data) {
            $project->update([
                'status' => PortfolioProject::STATUS_REJECTED,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'rejected_reason' => $data['rejected_reason'],
                'instructor_notes' => $data['instructor_notes'] ?? null,
            ]);
            $this->storeReview($project, PortfolioProjectReview::DECISION_REJECTED, $data);
        });

        return back()->with('success', 'تم رفض المشروع مع ملاحظات للطالب.');
    }

    public function requestChanges(Request $request, PortfolioProject $project)
    {
        $this->authorizeReview($project);
        if (! $project->isReviewable()) {
            return back()->with('error', 'المشروع ليس في قائمة المراجعة حالياً.');
        }

        $data = $this->validatedRubric($request, true);

        DB::transaction(function () use ($project, $data) {
            $project->fill([
                'status' => PortfolioProject::STATUS_CHANGES_REQUESTED,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'instructor_notes' => $data['instructor_notes'] ?? null,
                'rejected_reason' => $data['instructor_notes'] ?? 'مطلوب تعديلات قبل الاعتماد.',
            ]);
            $project->applyRubricScores($data);
            $project->save();
            $this->storeReview($project, PortfolioProjectReview::DECISION_CHANGES_REQUESTED, $data);
        });

        return back()->with('success', 'تم طلب تعديلات من الطالب.');
    }

    public function publish(PortfolioProject $project)
    {
        $this->authorizeReview($project);
        if ($project->status !== PortfolioProject::STATUS_APPROVED) {
            return back()->with('error', 'يجب اعتماد المشروع أولاً قبل النشر.');
        }

        DB::transaction(function () use ($project) {
            $project->update([
                'status' => PortfolioProject::STATUS_PUBLISHED,
                'published_at' => now(),
                'is_visible' => true,
            ]);

            $this->storeReview($project, PortfolioProjectReview::DECISION_PUBLISHED, [
                'instructor_notes' => 'تم النشر في معرض Mindlytics Journey.',
            ]);

            $profile = $this->journeyProfiles->ensureFor($project->user);
            $this->journeyProfiles->syncCompletion($profile);

            $this->achievements->ensureDefinitions();
            $this->achievements->syncForPublishedProject($project->fresh(['user']));
        });

        return back()->with('success', 'تم نشر المشروع في المعرض مع شارة Mindlytics Verified. يمكن للطالب مشاركة بطاقة الإنجاز الآن.');
    }

    public function toggleFeatured(PortfolioProject $project)
    {
        $this->authorizeReview($project);
        if ($project->status !== PortfolioProject::STATUS_PUBLISHED) {
            return back()->with('error', 'يمكن تمييز المشاريع المنشورة فقط.');
        }

        $project->update(['is_featured' => ! $project->is_featured]);

        if ($project->is_featured) {
            $this->achievements->ensureDefinitions();
            $this->achievements->grantFeatured($project->fresh(['user']));
        }

        return back()->with(
            'success',
            $project->is_featured
                ? 'تم تمييز المشروع كـ Featured by Mindlytics.'
                : 'تم إلغاء تمييز المشروع.'
        );
    }

    private function reviewableQuery()
    {
        $user = auth()->user();
        $pathIds = $user->teachingLearningPaths()->pluck('academic_years.id');
        $offlineIds = $user->offlineCourses()->pluck('id');

        return PortfolioProject::query()->where(function ($q) use ($pathIds, $offlineIds, $user) {
            if ($pathIds->isNotEmpty()) {
                $q->orWhereIn('academic_year_id', $pathIds);

                // كورسات مسجّلة مربوطة بمسارات يدرّسها المدرب
                $courseIds = AcademicYear::whereIn('id', $pathIds)
                    ->with('linkedCourses:id')
                    ->get()
                    ->pluck('linkedCourses')
                    ->flatten()
                    ->pluck('id')
                    ->unique()
                    ->filter();

                if ($courseIds->isNotEmpty()) {
                    $q->orWhere(function ($inner) use ($courseIds) {
                        $inner->whereNull('academic_year_id')
                            ->whereIn('advanced_course_id', $courseIds);
                    });
                }
            }

            if ($offlineIds->isNotEmpty()) {
                $q->orWhereIn('offline_course_id', $offlineIds);
            }

            // Avoid empty OR → no results: force impossible when instructor has no scope
            if ($pathIds->isEmpty() && $offlineIds->isEmpty()) {
                $q->whereRaw('1 = 0');
            }
        });
    }

    private function authorizeReview(PortfolioProject $project): void
    {
        $allowed = $this->reviewableQuery()->where('portfolio_projects.id', $project->id)->exists();
        if (! $allowed) {
            abort(403);
        }
    }

    private function validatedRubric(Request $request, bool $notesRequired): array
    {
        return $request->validate([
            'instructor_notes' => ($notesRequired ? 'required' : 'nullable') . '|string|max:2000',
            'rubric_code_quality' => 'nullable|integer|min:1|max:10',
            'rubric_ui_ux' => 'nullable|integer|min:1|max:10',
            'rubric_functionality' => 'nullable|integer|min:1|max:10',
            'rubric_problem_solving' => 'nullable|integer|min:1|max:10',
            'rubric_documentation' => 'nullable|integer|min:1|max:10',
        ], [
            'instructor_notes.required' => 'اكتب ملاحظات التعديل للطالب.',
        ]);
    }

    private function storeReview(PortfolioProject $project, string $decision, array $data): void
    {
        $scores = [
            'score_code_quality' => $data['rubric_code_quality'] ?? $project->rubric_code_quality,
            'score_ui_ux' => $data['rubric_ui_ux'] ?? $project->rubric_ui_ux,
            'score_functionality' => $data['rubric_functionality'] ?? $project->rubric_functionality,
            'score_problem_solving' => $data['rubric_problem_solving'] ?? $project->rubric_problem_solving,
            'score_documentation' => $data['rubric_documentation'] ?? $project->rubric_documentation,
        ];
        $values = array_filter($scores, fn ($v) => $v !== null);
        $avg = count($values) ? round(array_sum($values) / count($values), 2) : null;

        PortfolioProjectReview::create([
            'portfolio_project_id' => $project->id,
            'reviewed_by' => auth()->id(),
            'decision' => $decision,
            'score_code_quality' => $scores['score_code_quality'],
            'score_ui_ux' => $scores['score_ui_ux'],
            'score_functionality' => $scores['score_functionality'],
            'score_problem_solving' => $scores['score_problem_solving'],
            'score_documentation' => $scores['score_documentation'],
            'score_average' => $avg,
            'instructor_notes' => $data['instructor_notes'] ?? null,
            'rejected_reason' => $data['rejected_reason'] ?? null,
        ]);
    }
}
