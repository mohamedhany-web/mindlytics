<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\LearningPattern;
use App\Models\LearningPatternAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class LearningPatternController extends Controller
{
    /**
     * عرض نمط تعليمي للطالب
     */
    public function show(AdvancedCourse $course, LearningPattern $pattern)
    {
        $user = Auth::user();
        
        // التحقق من أن الطالب مسجل في الكورس
        if (!$user->activeCourses()->where('advanced_courses.id', $course->id)->exists()) {
            abort(403, 'غير مسموح لك بالوصول لهذا الكورس');
        }
        
        // التحقق من أن النمط ينتمي للكورس
        if ($pattern->advanced_course_id !== $course->id) {
            abort(404);
        }
        
        // التحقق من أن النمط نشط
        if (!$pattern->is_active) {
            return redirect()->route('my-courses.show', $course)
                ->with('error', 'هذا النمط غير متاح حالياً');
        }
        
        // جلب محاولات الطالب
        $userAttempts = $pattern->attempts()
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $bestAttempt = $pattern->getUserBestAttempt($user->id);
        $canAttempt = $pattern->canAttempt($user->id);
        
        $view = request('embed') ? 'student.learning-patterns.embed' : 'student.learning-patterns.show';
        return view($view, compact(
            'course',
            'pattern',
            'userAttempts',
            'bestAttempt',
            'canAttempt'
        ));
    }
    
    /**
     * بدء محاولة جديدة
     */
    public function startAttempt(Request $request, AdvancedCourse $course, LearningPattern $pattern)
    {
        $user = Auth::user();
        
        // التحقق من الصلاحيات
        if (!$user->activeCourses()->where('advanced_courses.id', $course->id)->exists()) {
            return response()->json(['error' => 'غير مسموح لك بالوصول لهذا الكورس'], 403);
        }
        
        if ($pattern->advanced_course_id !== $course->id || !$pattern->is_active) {
            return response()->json(['error' => 'النمط غير متاح'], 404);
        }
        
        // التحقق من إمكانية المحاولة
        if (!$pattern->canAttempt($user->id)) {
            return response()->json([
                'error' => 'لا يمكنك بدء محاولة جديدة',
                'reason' => $pattern->max_attempts 
                    ? 'تم الوصول للحد الأقصى من المحاولات' 
                    : 'المحاولات المتعددة غير مسموحة'
            ], 403);
        }
        
        // إنشاء محاولة جديدة (استخدام in_progress ليتطابق مع واجهة الطالب)
        $attempt = LearningPatternAttempt::create([
            'learning_pattern_id' => $pattern->id,
            'user_id' => $user->id,
            'status' => 'in_progress',
            'attempt_data' => [],
            'started_at' => now(),
        ]);
        
        // تحديث إحصائيات النمط
        $pattern->increment('total_attempts');
        
        return response()->json([
            'success' => true,
            'attempt' => $attempt,
            'time_limit' => $pattern->time_limit_minutes ? $pattern->time_limit_minutes * 60 : null,
        ]);
    }
    
    /**
     * حفظ تقدم المحاولة
     */
    public function saveProgress(Request $request, AdvancedCourse $course, LearningPattern $pattern, LearningPatternAttempt $attempt)
    {
        $user = Auth::user();
        
        // التحقق من الصلاحيات
        if (!$user->activeCourses()->where('advanced_courses.id', $course->id)->exists()) {
            return response()->json(['error' => 'غير مسموح لك بالوصول'], 403);
        }
        
        if ($attempt->user_id !== $user->id || $attempt->learning_pattern_id !== $pattern->id) {
            return response()->json(['error' => 'المحاولة غير موجودة'], 404);
        }
        
        if (in_array($attempt->status, ['completed', 'failed', 'abandoned'])) {
            return response()->json(['error' => 'لا يمكن تحديث محاولة مكتملة'], 400);
        }
        
        $validator = Validator::make($request->all(), [
            'attempt_data' => 'required|array',
            'time_spent_seconds' => 'nullable|integer|min:0',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // تنظيف البيانات من XSS
        $attemptData = $this->sanitizeAttemptData($request->attempt_data, $pattern->type);
        
        $attempt->update([
            'status' => 'in_progress',
            'attempt_data' => $attemptData,
            'time_spent_seconds' => $request->time_spent_seconds ?? $attempt->time_spent_seconds,
        ]);
        
        return response()->json(['success' => true, 'attempt' => $attempt]);
    }
    
    /**
     * إكمال المحاولة وتقييمها
     */
    public function completeAttempt(Request $request, AdvancedCourse $course, LearningPattern $pattern, LearningPatternAttempt $attempt)
    {
        $user = Auth::user();
        
        // التحقق من الصلاحيات
        if (!$user->activeCourses()->where('advanced_courses.id', $course->id)->exists()) {
            return response()->json(['error' => 'غير مسموح لك بالوصول'], 403);
        }
        
        if ($attempt->user_id !== $user->id || $attempt->learning_pattern_id !== $pattern->id) {
            return response()->json(['error' => 'المحاولة غير موجودة'], 404);
        }
        
        if ($attempt->status === 'completed') {
            return response()->json(['error' => 'المحاولة مكتملة بالفعل'], 400);
        }
        
        $validator = Validator::make($request->all(), [
            'attempt_data' => 'required|array',
            'time_spent_seconds' => 'nullable|integer|min:0',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // تنظيف البيانات
        $attemptData = $this->sanitizeAttemptData($request->attempt_data, $pattern->type);
        
        // تقييم المحاولة
        $evaluation = $this->evaluateAttempt($pattern, $attemptData);
        
        // تحديث المحاولة
        $attempt->update([
            'status' => $evaluation['status'],
            'attempt_data' => $attemptData,
            'score' => $evaluation['score'],
            'points_earned' => $evaluation['points_earned'],
            'completed_at' => now(),
            'time_spent_seconds' => $request->time_spent_seconds ?? $attempt->calculateTimeSpent(),
            'feedback' => $evaluation['feedback'] ?? null,
        ]);
        
        // تحديث إحصائيات النمط
        if ($evaluation['status'] === 'completed') {
            $pattern->increment('total_completions');
        }
        
        return response()->json([
            'success' => true,
            'attempt' => $attempt->fresh(),
            'evaluation' => $evaluation,
        ]);
    }
    
    /**
     * تنظيف بيانات المحاولة من XSS
     */
    private function sanitizeAttemptData(array $data, string $type): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeAttemptData($value, $type);
            } elseif (is_string($value)) {
                // تنظيف من XSS - السماح ببعض HTML للكود فقط
                if ($type === 'code_challenge' || $type === 'code_playground' || $type === 'debugging_exercise') {
                    // للكود، نحافظ على المحتوى لكن نزيل script tags
                    $sanitized[$key] = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $value);
                } else {
                    // للبيانات الأخرى، نزيل كل HTML
                    $sanitized[$key] = strip_tags($value);
                }
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }
    
    /**
     * تقييم المحاولة حسب نوع النمط
     */
    private function evaluateAttempt(LearningPattern $pattern, array $attemptData): array
    {
        $patternData = $pattern->pattern_data ?? [];
        
        switch ($pattern->type) {
            case 'interactive_quiz':
                return $this->evaluateQuiz($pattern, $attemptData, $patternData);
            
            case 'code_challenge':
                return $this->evaluateCodeChallenge($pattern, $attemptData, $patternData);
            
            case 'debugging_exercise':
                return $this->evaluateDebugging($pattern, $attemptData, $patternData);
            
            case 'flashcards':
                return $this->evaluateFlashcards($pattern, $attemptData, $patternData);
            
            default:
                // للأنماط الأخرى، نعتبرها مكتملة إذا تم إرسال البيانات
                return [
                    'status' => 'completed',
                    'score' => 0,
                    'points_earned' => $pattern->points,
                    'feedback' => 'تم إكمال النمط بنجاح',
                ];
        }
    }
    
    /**
     * تقييم الاختبار التفاعلي
     */
    private function evaluateQuiz(LearningPattern $pattern, array $attemptData, array $patternData): array
    {
        $questions = $patternData['questions'] ?? [];
        $totalQuestions = count($questions);
        $correctAnswers = 0;
        $feedback = [];
        
        foreach ($questions as $index => $question) {
            // استخدام index كـ ID إذا لم يكن هناك id محدد
            $questionId = $index;
            $userAnswer = $attemptData['answers'][$questionId] ?? $attemptData['answers'][$index] ?? null;
            $correctAnswer = $question['correct_answer'] ?? null;
            
            if ($userAnswer === $correctAnswer) {
                $correctAnswers++;
                $feedback[] = [
                    'question_id' => $questionId,
                    'correct' => true,
                    'message' => $question['feedback_correct'] ?? 'إجابة صحيحة!',
                ];
            } else {
                $feedback[] = [
                    'question_id' => $questionId,
                    'correct' => false,
                    'user_answer' => $userAnswer,
                    'correct_answer' => $correctAnswer,
                    'message' => $question['feedback_incorrect'] ?? 'إجابة خاطئة',
                ];
            }
        }
        
        $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;
        $pointsEarned = $score >= 60 ? $pattern->points : 0; // 60% للنجاح
        
        return [
            'status' => $score >= 60 ? 'completed' : 'failed',
            'score' => $score,
            'points_earned' => $pointsEarned,
            'feedback' => [
                'total_questions' => $totalQuestions,
                'correct_answers' => $correctAnswers,
                'score_percentage' => $score,
                'details' => $feedback,
            ],
        ];
    }
    
    /**
     * تقييم التحدي البرمجي
     */
    private function evaluateCodeChallenge(LearningPattern $pattern, array $attemptData, array $patternData): array
    {
        $userCode = $attemptData['code'] ?? '';
        $testCases = $patternData['test_cases'] ?? [];
        $passedTests = 0;
        $totalTests = count($testCases);
        
        // هنا يمكن إضافة تقييم فعلي للكود باستخدام sandbox
        // حالياً، نتحقق من أن الكود موجود
        if (empty(trim($userCode))) {
            return [
                'status' => 'failed',
                'score' => 0,
                'points_earned' => 0,
                'feedback' => 'يرجى كتابة الكود',
            ];
        }
        
        // للبساطة، نعتبر أن الكود صحيح إذا كان موجوداً
        // في الإنتاج، يجب استخدام sandbox لتقييم الكود فعلياً
        $score = 100; // مؤقت
        $pointsEarned = $pattern->points;
        
        return [
            'status' => 'completed',
            'score' => $score,
            'points_earned' => $pointsEarned,
            'feedback' => [
                'message' => 'تم إرسال الكود بنجاح. سيتم مراجعته من قبل المدرب.',
                'code_length' => strlen($userCode),
            ],
        ];
    }
    
    /**
     * تقييم تمرين تصحيح الأخطاء
     */
    private function evaluateDebugging(LearningPattern $pattern, array $attemptData, array $patternData): array
    {
        $userFixes = $attemptData['fixes'] ?? [];
        $expectedFixes = $patternData['expected_fixes'] ?? [];
        
        $correctFixes = 0;
        $totalFixes = count($expectedFixes);
        
        foreach ($expectedFixes as $index => $expectedFix) {
            $userFix = $userFixes[$index] ?? null;
            if ($userFix && $this->compareFixes($userFix, $expectedFix)) {
                $correctFixes++;
            }
        }
        
        $score = $totalFixes > 0 ? round(($correctFixes / $totalFixes) * 100) : 0;
        $pointsEarned = $score >= 70 ? $pattern->points : 0;
        
        return [
            'status' => $score >= 70 ? 'completed' : 'failed',
            'score' => $score,
            'points_earned' => $pointsEarned,
            'feedback' => [
                'correct_fixes' => $correctFixes,
                'total_fixes' => $totalFixes,
                'score_percentage' => $score,
            ],
        ];
    }
    
    /**
     * تقييم البطاقات التعليمية
     */
    private function evaluateFlashcards(LearningPattern $pattern, array $attemptData, array $patternData): array
    {
        $cards = $patternData['cards'] ?? [];
        $userAnswers = $attemptData['answers'] ?? [];
        $correctAnswers = 0;
        $totalCards = count($cards);
        
        foreach ($cards as $index => $card) {
            $userAnswer = $userAnswers[$index] ?? null;
            $correctAnswer = $card['back'] ?? '';
            
            if (strtolower(trim($userAnswer)) === strtolower(trim($correctAnswer))) {
                $correctAnswers++;
            }
        }
        
        $score = $totalCards > 0 ? round(($correctAnswers / $totalCards) * 100) : 0;
        $pointsEarned = $score >= 80 ? $pattern->points : 0;
        
        return [
            'status' => $score >= 80 ? 'completed' : 'failed',
            'score' => $score,
            'points_earned' => $pointsEarned,
            'feedback' => [
                'correct_answers' => $correctAnswers,
                'total_cards' => $totalCards,
                'score_percentage' => $score,
            ],
        ];
    }
    
    /**
     * مقارنة الإصلاحات
     */
    private function compareFixes($userFix, $expectedFix): bool
    {
        // مقارنة بسيطة - يمكن تحسينها
        return strtolower(trim($userFix)) === strtolower(trim($expectedFix));
    }
}
