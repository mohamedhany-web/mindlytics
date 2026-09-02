<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AdvancedCourse;
use App\Models\JourneyProfile;
use App\Models\OfflineCourse;
use App\Models\PortfolioProject;
use App\Models\PortfolioProjectImage;
use App\Services\JourneyAchievementService;
use App\Services\JourneyProfileService;
use App\Services\JourneyShareCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PortfolioProjectController extends Controller
{
    public function __construct(
        private JourneyProfileService $journeyProfiles,
        private JourneyAchievementService $achievements,
        private JourneyShareCardService $shareCards,
    ) {
    }

    public function index()
    {
        $user = auth()->user();
        $profile = $this->journeyProfiles->ensureFor($user);
        $this->journeyProfiles->syncCompletion($profile);

        $projects = $user->portfolioProjects()
            ->with(['academicYear', 'advancedCourse', 'offlineCourse'])
            ->latest()
            ->paginate(12);

        $counts = [
            'total' => $user->portfolioProjects()->count(),
            'published' => $user->portfolioProjects()->where('status', PortfolioProject::STATUS_PUBLISHED)->count(),
            'in_review' => $user->portfolioProjects()->whereIn('status', PortfolioProject::REVIEWABLE_STATUSES)->count(),
            'needs_work' => $user->portfolioProjects()->whereIn('status', [PortfolioProject::STATUS_CHANGES_REQUESTED, PortfolioProject::STATUS_REJECTED])->count(),
            'featured' => $user->portfolioProjects()->where('is_featured', true)->count(),
        ];

        $achievements = $this->achievements->forUser($user);
        $shareCards = $this->shareCards;

        return view('student.portfolio.index', compact('projects', 'profile', 'counts', 'achievements', 'shareCards'));
    }

    public function create()
    {
        return view('student.portfolio.create', $this->formOptions());
    }

    public function store(Request $request)
    {
        $data = $this->validatedProject($request);
        $action = $request->input('action', 'submit'); // draft | submit

        $programType = PortfolioProject::resolveProgramType(
            $data['academic_year_id'] ?? null,
            $data['advanced_course_id'] ?? null,
            $data['offline_course_id'] ?? null
        );

        $project = PortfolioProject::create([
            'user_id' => auth()->id(),
            'title' => $data['title'],
            'project_type' => $data['project_type'] ?? null,
            'is_capstone' => (bool) ($data['is_capstone'] ?? false),
            'description' => $data['description'] ?? null,
            'technologies' => $this->parseTechnologies($data['technologies'] ?? null),
            'what_i_learned' => $data['what_i_learned'] ?? null,
            'challenges' => $data['challenges'] ?? null,
            'project_url' => $data['project_url'] ?? null,
            'github_url' => $data['github_url'] ?? null,
            'academic_year_id' => $data['academic_year_id'] ?? null,
            'advanced_course_id' => $data['advanced_course_id'] ?? null,
            'offline_course_id' => $data['offline_course_id'] ?? null,
            'program_type' => $programType,
            'status' => $action === 'draft'
                ? PortfolioProject::STATUS_DRAFT
                : PortfolioProject::STATUS_PENDING_REVIEW,
        ]);

        $this->storeImages($request, $project);
        $this->journeyProfiles->syncCompletion($this->journeyProfiles->ensureFor(auth()->user()));

        $message = $action === 'draft'
            ? 'تم حفظ المسودة. يمكنك إكمالها وإرسالها للمراجعة لاحقاً.'
            : 'تم إرسال المشروع للمراجعة. بعد اعتماد المدرب يمكن نشره في رحلتك العامة.';

        return redirect()->route('student.portfolio.index')->with('success', $message);
    }

    public function edit(PortfolioProject $project)
    {
        $this->authorizeOwner($project);
        if (! $project->isEditableByStudent()) {
            return redirect()->route('student.portfolio.index')
                ->with('error', 'لا يمكن تعديل المشروع في حالته الحالية.');
        }

        $project->load('images');

        return view('student.portfolio.edit', array_merge($this->formOptions(), compact('project')));
    }

    public function update(Request $request, PortfolioProject $project)
    {
        $this->authorizeOwner($project);
        if (! $project->isEditableByStudent()) {
            return back()->with('error', 'لا يمكن تعديل المشروع في حالته الحالية.');
        }

        $data = $this->validatedProject($request);
        $action = $request->input('action', 'submit');

        $programType = PortfolioProject::resolveProgramType(
            $data['academic_year_id'] ?? null,
            $data['advanced_course_id'] ?? null,
            $data['offline_course_id'] ?? null
        );

        $wasChangesRequested = $project->status === PortfolioProject::STATUS_CHANGES_REQUESTED
            || $project->status === PortfolioProject::STATUS_REJECTED;

        $status = PortfolioProject::STATUS_DRAFT;
        if ($action !== 'draft') {
            $status = $wasChangesRequested
                ? PortfolioProject::STATUS_RESUBMITTED
                : PortfolioProject::STATUS_PENDING_REVIEW;
        }

        $project->update([
            'title' => $data['title'],
            'project_type' => $data['project_type'] ?? null,
            'is_capstone' => (bool) ($data['is_capstone'] ?? false),
            'description' => $data['description'] ?? null,
            'technologies' => $this->parseTechnologies($data['technologies'] ?? null),
            'what_i_learned' => $data['what_i_learned'] ?? null,
            'challenges' => $data['challenges'] ?? null,
            'project_url' => $data['project_url'] ?? null,
            'github_url' => $data['github_url'] ?? null,
            'academic_year_id' => $data['academic_year_id'] ?? null,
            'advanced_course_id' => $data['advanced_course_id'] ?? null,
            'offline_course_id' => $data['offline_course_id'] ?? null,
            'program_type' => $programType,
            'status' => $status,
            'revision_count' => $wasChangesRequested && $action !== 'draft'
                ? $project->revision_count + 1
                : $project->revision_count,
            'rejected_reason' => $action !== 'draft' ? null : $project->rejected_reason,
        ]);

        $this->storeImages($request, $project);
        $this->journeyProfiles->syncCompletion($this->journeyProfiles->ensureFor(auth()->user()));

        return redirect()->route('student.portfolio.index')->with('success', 'تم تحديث المشروع بنجاح.');
    }

    public function editJourney()
    {
        $profile = $this->journeyProfiles->ensureFor(auth()->user());
        $this->journeyProfiles->syncCompletion($profile);

        return view('student.portfolio.journey', compact('profile'));
    }

    public function updateJourney(Request $request)
    {
        $profile = $this->journeyProfiles->ensureFor(auth()->user());

        $data = $request->validate([
            'display_name' => 'nullable|string|max:120',
            'headline' => 'nullable|string|max:180',
            'bio' => 'nullable|string|max:2000',
            'career_goal' => 'nullable|string|max:255',
            'github_url' => 'nullable|url|max:500',
            'linkedin_url' => 'nullable|url|max:500',
            'website_url' => 'nullable|url|max:500',
            'visibility' => ['required', Rule::in([
                JourneyProfile::VISIBILITY_PRIVATE,
                JourneyProfile::VISIBILITY_UNLISTED,
                JourneyProfile::VISIBILITY_PUBLIC,
            ])],
            'is_open_to_work' => 'nullable|boolean',
            'slug' => [
                'nullable',
                'string',
                'max:80',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('journey_profiles', 'slug')->ignore($profile->id),
            ],
        ], [
            'slug.regex' => 'الرابط المختصر يجب أن يكون بالإنجليزية الصغيرة والأرقام والشرطات فقط.',
            'slug.unique' => 'هذا الرابط مستخدم بالفعل.',
        ]);

        if (! empty($data['slug'])) {
            $profile->slug = $data['slug'];
        }

        $profile->fill([
            'display_name' => $data['display_name'] ?? $profile->display_name,
            'headline' => $data['headline'] ?? null,
            'bio' => $data['bio'] ?? null,
            'career_goal' => $data['career_goal'] ?? null,
            'github_url' => $data['github_url'] ?? null,
            'linkedin_url' => $data['linkedin_url'] ?? null,
            'website_url' => $data['website_url'] ?? null,
            'visibility' => $data['visibility'],
            'is_open_to_work' => $request->boolean('is_open_to_work'),
        ]);

        if ($profile->visibility === JourneyProfile::VISIBILITY_PUBLIC && ! $profile->published_at) {
            $profile->published_at = now();
        }

        $profile->save();
        $this->journeyProfiles->syncCompletion($profile);

        if ($profile->visibility === JourneyProfile::VISIBILITY_PUBLIC) {
            $this->achievements->ensureDefinitions();
            $this->achievements->grantProfilePublic(auth()->user());
        }

        // Keep user headline/bio in sync for mobile peers
        auth()->user()->update([
            'headline' => $profile->headline,
            'bio' => $profile->bio,
        ]);

        return back()->with('success', 'تم تحديث ملف رحلتك بنجاح.');
    }

    private function formOptions(): array
    {
        $user = auth()->user();
        $pathIds = $user->learningPathEnrollments()->whereIn('status', ['active', 'completed'])->pluck('academic_year_id')->unique()->filter();
        $learningPaths = AcademicYear::where('is_active', true)->whereIn('id', $pathIds)->ordered()->get(['id', 'name']);
        $courses = $user->activeCourses()->select('advanced_courses.id', 'advanced_courses.title')->get();

        $offlineIds = $user->offlineEnrollments()->whereIn('status', ['active', 'completed'])->pluck('offline_course_id')->unique()->filter();
        $offlineCourses = OfflineCourse::whereIn('id', $offlineIds)->get(['id', 'title', 'online_only']);

        return compact('learningPaths', 'courses', 'offlineCourses');
    }

    private function validatedProject(Request $request): array
    {
        $user = auth()->user();
        $pathIds = $user->learningPathEnrollments()->whereIn('status', ['active', 'completed'])->pluck('academic_year_id')->all();
        $courseIds = $user->activeCourses()->pluck('advanced_courses.id')->all();
        $offlineIds = $user->offlineEnrollments()->whereIn('status', ['active', 'completed'])->pluck('offline_course_id')->all();

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'project_type' => 'nullable|string|in:web_app,mobile_app,api,library,script,design,game,desktop,cli,other',
            'is_capstone' => 'nullable|boolean',
            'description' => 'nullable|string|max:5000',
            'technologies' => 'nullable|string|max:500',
            'what_i_learned' => 'nullable|string|max:3000',
            'challenges' => 'nullable|string|max:3000',
            'project_url' => 'nullable|url|max:500',
            'github_url' => 'nullable|url|max:500',
            'academic_year_id' => ['nullable', 'integer', Rule::in($pathIds ?: [0])],
            'advanced_course_id' => ['nullable', 'integer', Rule::in($courseIds ?: [0])],
            'offline_course_id' => ['nullable', 'integer', Rule::in($offlineIds ?: [0])],
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|max:2048',
            'action' => 'nullable|in:draft,submit',
        ], [
            'title.required' => 'عنوان المشروع مطلوب',
            'project_url.url' => 'رابط المشروع يجب أن يكون رابطاً صحيحاً',
            'github_url.url' => 'رابط GitHub يجب أن يكون رابطاً صحيحاً',
            'images.max' => 'حد أقصى 5 صور للمشروع',
            'academic_year_id.in' => 'المسار المحدد غير متاح لحسابك',
            'advanced_course_id.in' => 'الكورس المسجّل المحدد غير متاح لحسابك',
            'offline_course_id.in' => 'الدبلوم/الكورس الحضوري المحدد غير متاح لحسابك',
        ]);

        if (empty($data['academic_year_id']) && empty($data['advanced_course_id']) && empty($data['offline_course_id'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'advanced_course_id' => 'اختر كورساً مسجّلاً أو دبلوماً (مسار / أونلاين / أوفلاين) لربط المشروع برحلتك.',
            ]);
        }

        return $data;
    }

    private function parseTechnologies(?string $raw): ?array
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }

        return collect(preg_split('/[,،|]+/u', $raw))
            ->map(fn ($t) => trim($t))
            ->filter()
            ->unique()
            ->values()
            ->take(20)
            ->all();
    }

    private function storeImages(Request $request, PortfolioProject $project): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $dir = public_path('portfolio-images');
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $sortOrder = (int) $project->images()->max('sort_order');
        foreach ($request->file('images') as $file) {
            if ($file && $file->isValid()) {
                $name = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $file->move($dir, $name);
                $path = 'portfolio-images/' . $name;
                PortfolioProjectImage::create([
                    'portfolio_project_id' => $project->id,
                    'image_path' => $path,
                    'sort_order' => ++$sortOrder,
                ]);
                if (! $project->image_path) {
                    $project->update(['image_path' => $path]);
                }
            }
        }
    }

    private function authorizeOwner(PortfolioProject $project): void
    {
        if ((int) $project->user_id !== (int) auth()->id()) {
            abort(403);
        }
    }
}
