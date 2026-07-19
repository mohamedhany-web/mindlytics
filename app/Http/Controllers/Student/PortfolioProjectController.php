<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AdvancedCourse;
use App\Models\PortfolioProject;
use App\Models\PortfolioProjectImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PortfolioProjectController extends Controller
{
    public function index()
    {
        $projects = auth()->user()->portfolioProjects()->with(['academicYear', 'advancedCourse'])->latest()->paginate(10);
        return view('student.portfolio.index', compact('projects'));
    }

    public function create()
    {
        $user = auth()->user();
        // المسارات التعليمية التي التحق بها الطالب فقط
        $pathIds = $user->learningPathEnrollments()->where('status', 'active')->pluck('academic_year_id')->unique()->filter();
        $learningPaths = AcademicYear::where('is_active', true)->whereIn('id', $pathIds)->ordered()->get(['id', 'name']);
        // الكورسات التي اشتراها/سجّل فيها الطالب فقط (تحديد الجدول لتجنب ambiguous id)
        $courses = $user->activeCourses()->select('advanced_courses.id', 'advanced_courses.title')->get();
        return view('student.portfolio.create', compact('learningPaths', 'courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'project_type' => 'nullable|string|in:web_app,mobile_app,api,library,script,design,game,desktop,cli,other',
            'description' => 'nullable|string|max:2000',
            'project_url' => 'nullable|url|max:500',
            'github_url' => 'nullable|url|max:500',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'advanced_course_id' => 'nullable|exists:advanced_courses,id',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|max:2048',
        ], [
            'title.required' => 'عنوان المشروع مطلوب',
            'project_url.url' => 'رابط المشروع يجب أن يكون رابطاً صحيحاً',
            'github_url.url' => 'رابط GitHub يجب أن يكون رابطاً صحيحاً',
            'images.max' => 'حد أقصى 5 صور للمشروع',
            'images.*.image' => 'يجب أن يكون الملف صورة',
            'images.*.max' => 'كل صورة حد أقصى 2 ميجابايت',
        ]);

        $data = [
            'user_id' => auth()->id(),
            'title' => $request->title,
            'project_type' => $request->project_type ?: null,
            'description' => $request->description,
            'project_url' => $request->project_url,
            'github_url' => $request->github_url,
            'academic_year_id' => $request->academic_year_id ?: null,
            'advanced_course_id' => $request->advanced_course_id ?: null,
            'status' => PortfolioProject::STATUS_PENDING_REVIEW,
        ];

        $project = PortfolioProject::create($data);

        $dir = public_path('portfolio-images');
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        if ($request->hasFile('images')) {
            $sortOrder = 0;
            foreach ($request->file('images') as $file) {
                if ($file && $file->isValid()) {
                    $name = Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $file->move($dir, $name);
                    $path = 'portfolio-images/' . $name;
                    PortfolioProjectImage::create([
                        'portfolio_project_id' => $project->id,
                        'image_path' => $path,
                        'sort_order' => $sortOrder++,
                    ]);
                    if ($sortOrder === 1) {
                        $project->update(['image_path' => $path]);
                    }
                }
            }
        }

        return redirect()->route('student.portfolio.index')->with('success', 'تم رفع المشروع بنجاح. سيتم مراجعته من المدرب ثم النشر في البورتفوليو.');
    }
}
