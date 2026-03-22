<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Lecture;
use App\Models\LectureVideoQuestion;
use App\Models\QuestionBank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LectureVideoQuestionController extends Controller
{
    private function authorizeLecture(Lecture $lecture): void
    {
        if ($lecture->course->instructor_id !== Auth::id()) {
            abort(403, 'غير مسموح لك بإدارة أسئلة هذه المحاضرة');
        }
    }

    /**
     * قائمة أسئلة الفيديو لمحاضرة + بنوك الأسئلة (للمودال).
     */
    public function index(Lecture $lecture): JsonResponse
    {
        $this->authorizeLecture($lecture);
        $questions = $lecture->videoQuestions()->with('question')->orderBy('timestamp_seconds')->get()->map(function ($q) {
            $payload = $q->getPayloadForStudent();
            $showCount = $q->show_count;
            $showCountLabel = $showCount === null || $showCount == 0 ? 'كل مرة' : ($showCount == 1 ? 'مرة واحدة' : $showCount . ' مرات');
            $timestampLabel = $q->show_at_end ? 'نهاية الفيديو' : (floor($q->timestamp_seconds / 60) . ':' . str_pad($q->timestamp_seconds % 60, 2, '0', STR_PAD_LEFT));
            return [
                'id' => $q->id,
                'timestamp_seconds' => $q->timestamp_seconds,
                'show_at_end' => (bool) $q->show_at_end,
                'timestamp_label' => $timestampLabel,
                'question_source' => $q->question_source,
                'question_text' => $payload['text'] ?? '',
                'on_wrong' => $q->on_wrong,
                'rewind_seconds' => $q->rewind_seconds,
                'points' => $q->points,
                'show_count' => $showCount,
                'show_count_label' => $showCountLabel,
            ];
        });
        // إحضار بنوك الأسئلة الخاصة بالمدرب الحالي + البنوك العامة (instructor_id null)، بشرط أن تكون نشطة
        $instructor = Auth::user();
        $banks = QuestionBank::where(function ($q) {
                $q->where('is_active', true)->orWhereNull('is_active');
            })
            ->where(function ($q) use ($instructor) {
                $q->whereNull('instructor_id')
                  ->orWhere('instructor_id', $instructor->id)
                  ->orWhere('created_by', $instructor->id);
            })
            ->orderBy('title')
            ->get(['id', 'title']);
        $bankQuestions = [];
        foreach ($banks as $bank) {
            /* كل أسئلة البنك للاختيار (بما فيها القديمة التي حُفظت is_active=0 بسبب خطأ has('is_active',true) في التخزين) */
            $bankQuestions[(string) $bank->id] = $bank->questions()
                ->orderBy('created_at')
                ->get(['id', 'question', 'type', 'options', 'correct_answer'])
                ->map(function ($q) {
                    return ['id' => $q->id, 'text' => $q->question, 'type' => $q->type, 'options' => $q->options];
                })->values()->all();
        }

        return response()->json([
            'video_questions' => $questions,
            'question_banks' => $banks,
            'bank_questions' => $bankQuestions,
        ]);
    }

    /**
     * إضافة سؤال فيديو.
     */
    public function store(Request $request, Lecture $lecture): JsonResponse
    {
        $this->authorizeLecture($lecture);
        $request->merge([
            'show_at_end' => $request->boolean('show_at_end') ? 1 : 0,
        ]);

        $validated = $request->validate([
            'show_at_end' => 'nullable|boolean',
            'timestamp_minutes' => 'required_unless:show_at_end,1|nullable|numeric|min:0|max:999',
            'timestamp_seconds_extra' => 'nullable|integer|min:0|max:59',
            'question_source' => 'required|in:bank,custom',
            'question_id' => 'required_if:question_source,bank|nullable|exists:questions,id',
            'custom_question_text' => 'required_if:question_source,custom|nullable|string|max:5000',
            'custom_options' => 'required_if:question_source,custom|nullable|array',
            'custom_options.*' => 'string|max:1000',
            'custom_correct_answer' => 'required_if:question_source,custom|nullable|string|max:500',
            'on_wrong' => 'required|in:rewind,continue',
            'rewind_seconds' => 'nullable|integer|min:0|max:3600',
            'points' => 'nullable|integer|min:1|max:100',
            'show_count' => 'nullable|integer|min:0|max:255',
        ], [
            'timestamp_minutes.required' => 'حدد الدقيقة في الفيديو',
            'question_source.required' => 'حدد مصدر السؤال',
            'custom_question_text.required_if' => 'نص السؤال المخصص مطلوب',
            'custom_correct_answer.required_if' => 'الإجابة الصحيحة مطلوبة',
        ]);

        $showAtEnd = ((int) ($validated['show_at_end'] ?? 0)) === 1;
        $timestamp_seconds = 0;
        if (!$showAtEnd) {
            $minutes = (int) floor((float) ($validated['timestamp_minutes'] ?? 0));
            $extra = (int) ($validated['timestamp_seconds_extra'] ?? 0);
            $timestamp_seconds = $minutes * 60 + min(59, $extra);
        }

        if ($validated['question_source'] === 'bank') {
            $q = \App\Models\Question::with('questionBank')->find($validated['question_id']);
            if (! $q || ! $q->questionBank) {
                return response()->json(['success' => false, 'message' => 'السؤال غير متاح'], 422);
            }
            $bank = $q->questionBank;
            $uid = (int) Auth::id();
            $canUseBank = $bank->instructor_id === null
                || (int) $bank->instructor_id === $uid
                || (int) ($bank->created_by ?? 0) === $uid;
            if (! $canUseBank) {
                return response()->json(['success' => false, 'message' => 'السؤال غير متاح لهذا الحساب'], 422);
            }
        }

        $lastOrder = $lecture->videoQuestions()->max('order') ?? 0;
        $vq = LectureVideoQuestion::create([
            'lecture_id' => $lecture->id,
            'timestamp_seconds' => $timestamp_seconds,
            'show_at_end' => $showAtEnd,
            'question_source' => $validated['question_source'],
            'question_id' => $validated['question_source'] === 'bank' ? $validated['question_id'] : null,
            'custom_question_text' => $validated['question_source'] === 'custom' ? $validated['custom_question_text'] : null,
            'custom_options' => $validated['question_source'] === 'custom' ? array_values($validated['custom_options']) : null,
            'custom_correct_answer' => $validated['question_source'] === 'custom' ? $validated['custom_correct_answer'] : null,
            'on_wrong' => $validated['on_wrong'],
            'rewind_seconds' => $validated['on_wrong'] === 'rewind' ? (int) ($validated['rewind_seconds'] ?? 0) : 0,
            'points' => (int) ($validated['points'] ?? 1),
            'show_count' => isset($validated['show_count']) ? (int) $validated['show_count'] : 1,
            'order' => $lastOrder + 1,
        ]);

        $payload = $vq->getPayloadForStudent();
        return response()->json([
            'success' => true,
            'message' => 'تمت إضافة السؤال',
            'question' => [
                'id' => $vq->id,
                'timestamp_seconds' => $vq->timestamp_seconds,
                'show_at_end' => (bool) $vq->show_at_end,
                'timestamp_label' => $vq->show_at_end ? 'نهاية الفيديو' : (floor($vq->timestamp_seconds / 60) . ':' . str_pad($vq->timestamp_seconds % 60, 2, '0', STR_PAD_LEFT)),
                'question_source' => $vq->question_source,
                'question_text' => $payload['text'] ?? '',
                'on_wrong' => $vq->on_wrong,
                'rewind_seconds' => $vq->rewind_seconds,
                'points' => $vq->points,
                'show_count' => $vq->show_count,
                'show_count_label' => ($vq->show_count === null || $vq->show_count == 0) ? 'كل مرة' : ($vq->show_count == 1 ? 'مرة واحدة' : $vq->show_count . ' مرات'),
            ],
        ]);
    }

    /**
     * حذف سؤال فيديو.
     */
    public function destroy(Lecture $lecture, LectureVideoQuestion $videoQuestion): JsonResponse
    {
        $this->authorizeLecture($lecture);
        if ($videoQuestion->lecture_id !== $lecture->id) {
            abort(404);
        }
        $videoQuestion->delete();
        return response()->json(['success' => true, 'message' => 'تم حذف السؤال']);
    }
}
