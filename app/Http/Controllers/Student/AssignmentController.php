<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $courseIds = $user->activeCourses()->pluck('advanced_courses.id');

        $assignments = Assignment::whereIn('advanced_course_id', $courseIds)
            ->where('status', 'published')
            ->with(['course', 'lesson', 'teacher'])
            ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_date')
            ->get();

        $submissions = AssignmentSubmission::where('student_id', $user->id)
            ->whereIn('assignment_id', $assignments->pluck('id'))
            ->get()
            ->keyBy('assignment_id');

        return view('student.assignments.index', compact('assignments', 'submissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Assignment $assignment, Request $request)
    {
        $user = Auth::user();
        if (!$user->isEnrolledIn($assignment->advanced_course_id)) {
            abort(403, 'غير مسموح لك بعرض هذا الواجب');
        }

        $assignment->load(['course', 'lesson', 'teacher']);
        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $user->id)
            ->latest('submitted_at')
            ->first();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'id' => $assignment->id,
                'title' => $assignment->title,
                'description' => $assignment->description,
                'instructions' => $assignment->instructions,
                'due_date' => optional($assignment->due_date)->format('Y-m-d H:i'),
                'max_score' => $assignment->max_score,
                'course' => $assignment->course?->title,
                'lesson' => $assignment->lesson?->title,
                'submission' => $submission ? [
                    'status' => $submission->status,
                    'content' => $submission->content,
                    'score' => $submission->score,
                    'feedback' => $submission->feedback,
                    'submitted_at' => optional($submission->submitted_at)->format('Y-m-d H:i'),
                    'attachments' => $submission->attachments ?? [],
                ] : null,
            ]);
        }

        return view('student.assignments.show', compact('assignment', 'submission'));
    }

    public function submit(Request $request, Assignment $assignment)
    {
        $user = Auth::user();
        if (!$user->isEnrolledIn($assignment->advanced_course_id)) {
            abort(403, 'غير مسموح لك بتسليم هذا الواجب');
        }

        if (!$assignment->allow_late_submission && $assignment->due_date && now()->greaterThan($assignment->due_date)) {
            return back()->with('error', 'انتهى موعد التسليم لهذا الواجب');
        }

        $validated = $request->validate([
            'content' => 'nullable|string|max:10000',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:102400',
        ]);

        $submission = AssignmentSubmission::firstOrNew([
            'assignment_id' => $assignment->id,
            'student_id' => $user->id,
        ]);

        $attachments = is_array($submission->attachments) ? $submission->attachments : [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $disk = config('filesystems.assignments_disk', 'r2');
                $path = null;
                $url = null;
                try {
                    $path = $file->store('assignment-submissions', $disk);
                    $url = Storage::disk($disk)->url($path);
                } catch (\Throwable $e) {
                    $disk = 'public';
                    $path = $file->store('assignment-submissions', $disk);
                    $url = Storage::disk($disk)->url($path);
                }

                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'disk' => $disk,
                    'url' => $url,
                    'mime' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ];
            }
        }

        $submission->fill([
            'group_id' => $submission->group_id ?: null,
            'content' => $validated['content'] ?? null,
            'attachments' => $attachments,
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);
        $submission->save();

        try {
            app(\App\Http\Controllers\Student\MyCourseController::class)
                ->updateCourseProgress($user->id, (int) $assignment->advanced_course_id);
        } catch (\Throwable $e) {
            \Log::warning('Failed to update course progress after assignment submit: '.$e->getMessage());
        }

        return back()->with('success', 'تم تسليم الواجب بنجاح');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
