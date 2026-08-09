<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\Certificate;
use App\Services\CertificateIssueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function __construct(
        private CertificateIssueService $issuer,
    ) {}

    public function index(): View
    {
        $certificates = Certificate::where('user_id', auth()->id())
            ->with('course')
            ->where(function ($q) {
                $q->where('status', 'issued')->orWhere('is_verified', true);
            })
            ->orderByRaw('COALESCE(issued_at, issue_date) DESC')
            ->paginate(15);

        $eligible = $this->issuer->eligibleEnrollments(auth()->user());

        $stats = [
            'total' => $certificates->total(),
            'issued' => Certificate::where('user_id', auth()->id())
                ->where(function ($q) {
                    $q->where('status', 'issued')->orWhere('is_verified', true);
                })->count(),
            'ready' => $eligible->count(),
        ];

        return view('student.certificates.index', compact('certificates', 'stats', 'eligible'));
    }

    public function claim(AdvancedCourse $course): View|RedirectResponse
    {
        $enrollment = $this->issuer->eligibleEnrollments(auth()->user())
            ->firstWhere('advanced_course_id', $course->id);

        if (! $enrollment) {
            $existing = Certificate::query()
                ->where('user_id', auth()->id())
                ->where('course_id', $course->id)
                ->where(function ($q) {
                    $q->where('status', 'issued')->orWhere('is_verified', true);
                })
                ->first();

            if ($existing) {
                return redirect()->route('student.certificates.show', $existing)
                    ->with('success', 'الشهادة صادرة مسبقاً.');
            }

            return redirect()->route('student.certificates.index')
                ->with('error', 'يجب إكمال الكورس بنسبة 100٪ أولاً.');
        }

        $designs = CertificateIssueService::designCatalog();
        $course->loadMissing('instructor:id,name');

        return view('student.certificates.claim', [
            'course' => $course,
            'enrollment' => $enrollment,
            'designs' => $designs,
            'defaultName' => auth()->user()->name,
        ]);
    }

    public function storeClaim(Request $request, AdvancedCourse $course): RedirectResponse
    {
        $validated = $request->validate([
            'template' => 'required|string|in:'.implode(',', array_keys(CertificateIssueService::designCatalog())),
            'display_name' => 'nullable|string|max:120',
        ], [
            'template.required' => 'اختر تصميم الشهادة.',
            'template.in' => 'تصميم الشهادة غير صالح.',
        ]);

        $certificate = $this->issuer->issueForCompletedCourse(
            auth()->user(),
            $course,
            $validated['template'],
            $validated['display_name'] ?? null,
        );

        return redirect()
            ->route('student.certificates.show', $certificate)
            ->with('success', 'تم إصدار شهادتك برقم تسلسلي موثّق: '.$certificate->serial_number);
    }

    public function show(Certificate $certificate): View
    {
        if ($certificate->user_id !== auth()->id()) {
            abort(403);
        }

        $certificate->load(['course.instructor', 'instructor', 'user']);
        if (! $certificate->serial_number) {
            $certificate->serial_number = Certificate::generateSerialNumber();
            $certificate->certificate_hash = $certificate->generateHash();
            $certificate->save();
        }
        $branding = \App\Models\CertificateBranding::current();

        return view('student.certificates.show', compact('certificate', 'branding'));
    }

    public function designPreview(string $key): View
    {
        $designs = CertificateIssueService::designCatalog();
        abort_unless(isset($designs[$key]), 404);

        return view('student.certificates.design-preview', [
            'key' => $key,
            'design' => $designs[$key],
            'sampleName' => auth()->user()->name ?? 'Student Name',
            'sampleCourse' => 'Course Title',
            'sampleInstructor' => 'Instructor',
        ]);
    }
}
