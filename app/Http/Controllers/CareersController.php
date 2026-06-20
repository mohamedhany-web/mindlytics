<?php

namespace App\Http\Controllers;

use App\Models\HrApplicationFile;
use App\Models\HrJobApplication;
use App\Models\HrJobPosting;
use App\Models\Notification;
use App\Models\User;
use App\Services\Hr\AtsScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CareersController extends Controller
{
    public function __construct(
        private readonly AtsScoringService $scoringService,
    ) {}
    public function index(): View
    {
        $jobs = HrJobPosting::query()
            ->published()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get();

        return view('careers.index', compact('jobs'));
    }

    public function show(HrJobPosting $job): View
    {
        abort_unless($job->is_published && $job->isOpen(), 404);

        return view('careers.show', compact('job'));
    }

    public function apply(Request $request, HrJobPosting $job): RedirectResponse
    {
        abort_unless($job->is_published && $job->isOpen(), 404);

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:60',
            'linkedin_url' => 'nullable|url|max:500',
            'portfolio_url' => 'nullable|url|max:500',
            'cover_letter' => 'nullable|string|max:10000',
            'cv' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:pdf,doc,docx,png,jpg,jpeg,zip|max:10240',
        ], [
            'cv.required' => 'يرجى رفع السيرة الذاتية.',
            'cv.mimes' => 'صيغة CV يجب أن تكون PDF أو Word.',
        ]);

        $application = HrJobApplication::create([
            'job_posting_id' => $job->id,
            'full_name' => $validated['full_name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'linkedin_url' => $validated['linkedin_url'] ?? null,
            'portfolio_url' => $validated['portfolio_url'] ?? null,
            'cover_letter' => $validated['cover_letter'] ?? null,
            'status' => 'applied',
            'source' => 'website',
            'submitted_at' => now(),
        ]);

        try {
            $this->storeFile($application, $request->file('cv'), 'cv');
            foreach ($request->file('attachments', []) as $file) {
                $this->storeFile($application, $file, 'attachment');
            }
        } catch (\Throwable $e) {
            Log::error('HR application file upload failed', [
                'application_id' => $application->id,
                'message' => $e->getMessage(),
            ]);
            $application->delete();

            return back()
                ->withInput()
                ->withErrors(['cv' => 'تعذّر رفع الملف. يرجى المحاولة مرة أخرى أو التواصل معنا.']);
        }

        try {
            $this->scoringService->processApplication($application->fresh(['cvFile', 'job']));
        } catch (\Throwable $e) {
            Log::warning('HR auto-scoring failed', [
                'application_id' => $application->id,
                'message' => $e->getMessage(),
            ]);
        }

        try {
            $this->notifyAdminsNewApplication($application);
        } catch (\Throwable $e) {
            Log::warning('HR application saved but admin notification failed', [
                'application_id' => $application->id,
                'message' => $e->getMessage(),
            ]);
        }

        return redirect()->route('careers.show', $job)->with('success', 'تم إرسال طلبك بنجاح. سيتم التواصل معك بعد المراجعة.');
    }

    private function storeFile(HrJobApplication $application, $file, string $kind): void
    {
        if (! $file) {
            return;
        }

        $orig = (string) $file->getClientOriginalName();
        $ext = strtolower((string) $file->getClientOriginalExtension());
        $base = Str::slug(pathinfo($orig, PATHINFO_FILENAME)) ?: 'file';
        $safeName = $base.'-'.Str::random(6).($ext ? '.'.$ext : '');

        $folder = "hr/recruitment/{$application->job_posting_id}/{$application->id}/".($kind === 'cv' ? 'cv' : 'attachments');
        [$disk, $path] = $this->uploadHrFile($folder, $file, $safeName);

        HrApplicationFile::create([
            'job_application_id' => $application->id,
            'kind' => $kind,
            'disk' => $disk,
            'path' => $path,
            'original_name' => $orig,
            'mime' => (string) ($file->getClientMimeType() ?: ''),
            'size' => (int) ($file->getSize() ?: 0),
        ]);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function uploadHrFile(string $folder, $file, string $safeName): array
    {
        $preferred = hr_recruitment_disk();
        $candidates = array_values(array_unique(array_filter([
            $preferred,
            $preferred !== 'public' ? 'public' : null,
        ])));

        $lastError = null;

        foreach ($candidates as $disk) {
            try {
                $path = Storage::disk($disk)->putFileAs($folder, $file, $safeName);
                if ($path) {
                    return [$disk, $path];
                }
            } catch (\Throwable $e) {
                $lastError = $e;
            }
        }

        throw new \RuntimeException($lastError?->getMessage() ?: 'Unable to store HR recruitment file.');
    }

    private function notifyAdminsNewApplication(HrJobApplication $application): void
    {
        $application->loadMissing('job');
        $jobTitle = $application->job?->title;
        $title = 'طلب توظيف جديد';
        $message = 'متقدم جديد: '.$application->full_name.($jobTitle ? ' — وظيفة: '.$jobTitle : '');

        $adminIds = User::query()
            ->whereIn('role', ['admin', 'super_admin'])
            ->where('is_active', true)
            ->pluck('id');

        foreach ($adminIds as $id) {
            Notification::create([
                'user_id' => (int) $id,
                'sender_id' => null,
                'title' => $title,
                'message' => $message,
                'type' => 'system',
                'priority' => 'high',
                'audience' => 'admin',
                'action_url' => route('admin.hr.applications.show', $application),
                'action_text' => 'فتح التقديم',
                'data' => ['kind' => 'hr_new_application', 'application_id' => $application->id],
            ]);
        }
    }
}
