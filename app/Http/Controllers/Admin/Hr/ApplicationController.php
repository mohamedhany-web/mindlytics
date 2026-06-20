<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\HrApplicationScore;
use App\Models\HrJobApplication;
use App\Models\HrJobPosting;
use App\Models\HrRubric;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $q = HrJobApplication::query()
            ->with(['job:id,title', 'cvFile', 'score'])
            ->orderByDesc('submitted_at')
            ->orderByDesc('id');

        if ($request->filled('job_id')) {
            $q->where('job_posting_id', (int) $request->job_id);
        }
        if ($request->filled('status') && array_key_exists($request->status, HrJobApplication::STATUSES)) {
            $q->where('status', $request->status);
        }
        if ($request->filled('min_score')) {
            $min = (float) $request->min_score;
            $q->whereHas('score', fn ($qq) => $qq->where('total_score', '>=', $min));
        }
        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $q->where(function ($qq) use ($s) {
                $qq->where('full_name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $applications = $q->paginate(25)->withQueryString();
        $jobs = HrJobPosting::query()->orderByDesc('updated_at')->get(['id', 'title']);

        $stats = [
            'total' => HrJobApplication::count(),
            'new' => HrJobApplication::where('status', 'new')->count(),
            'interview' => HrJobApplication::where('status', 'interview')->count(),
            'hired' => HrJobApplication::where('status', 'hired')->count(),
        ];

        return view('admin.hr.applications.index', compact('applications', 'jobs', 'stats'));
    }

    public function show(HrJobApplication $application): View
    {
        $application->load(['job', 'files', 'score.rubric', 'score.scorer']);
        $rubrics = HrRubric::query()->orderByDesc('is_default')->orderBy('name')->get();

        return view('admin.hr.applications.show', compact('application', 'rubrics'));
    }

    public function updateStatus(Request $request, HrJobApplication $application): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:'.implode(',', array_keys(HrJobApplication::STATUSES)),
        ]);

        $application->update(['status' => $validated['status']]);

        return back()->with('success', 'تم تحديث الحالة.');
    }

    public function saveScore(Request $request, HrJobApplication $application): RedirectResponse
    {
        $validated = $request->validate([
            'rubric_id' => 'required|integer|exists:hr_rubrics,id',
            'scores' => 'required|array',
            'notes' => 'nullable|string|max:5000',
        ]);

        $rubric = HrRubric::findOrFail((int) $validated['rubric_id']);
        $criteria = is_array($rubric->criteria_json) ? $rubric->criteria_json : [];

        $scoresIn = $validated['scores'];
        $scoresOut = [];
        $total = 0.0;

        foreach ($criteria as $c) {
            $key = (string) ($c['key'] ?? '');
            if ($key === '') {
                continue;
            }
            $weight = (float) ($c['weight'] ?? 1);
            $max = (float) ($c['max'] ?? 10);
            $raw = $scoresIn[$key] ?? 0;
            $val = max(0.0, min((float) $raw, $max));
            $scoresOut[$key] = $val;
            $total += $val * $weight;
        }

        $score = HrApplicationScore::updateOrCreate(
            ['job_application_id' => $application->id],
            [
                'rubric_id' => $rubric->id,
                'scores_json' => $scoresOut,
                'total_score' => round($total, 2),
                'notes' => $validated['notes'] ?? null,
                'scored_by' => auth()->id(),
                'scored_at' => now(),
            ]
        );

        // إشعار داخلي للأدمن (اختياري): سجل للمراجعة فقط (لن نرسل للمتقدم)
        try {
            Notification::create([
                'user_id' => auth()->id(),
                'sender_id' => null,
                'title' => 'تم تقييم متقدم',
                'message' => 'تم حفظ تقييم المتقدم «'.$application->full_name.'» بدرجة '.$score->total_score.'.',
                'type' => 'system',
                'priority' => 'normal',
                'audience' => 'admin',
                'action_url' => route('admin.hr.applications.show', $application),
                'action_text' => 'فتح التقديم',
                'data' => ['kind' => 'hr_application_scored', 'application_id' => $application->id],
            ]);
        } catch (\Throwable) {
            // ignore
        }

        return back()->with('success', 'تم حفظ التقييم.');
    }
}

