<?php

use App\Domains\Learning\Http\Controllers\Instructor\AssignmentController;
use App\Domains\Learning\Http\Controllers\Instructor\AttendanceController;
use App\Domains\Learning\Http\Controllers\Instructor\CurriculumController;
use App\Domains\Learning\Http\Controllers\Instructor\ExamController;
use App\Domains\Learning\Http\Controllers\Instructor\ExamQuestionController;
use App\Domains\Learning\Http\Controllers\Instructor\GroupController;
use App\Domains\Learning\Http\Controllers\Instructor\LearningPathController;
use App\Domains\Learning\Http\Controllers\Instructor\LearningPatternController;
use App\Domains\Learning\Http\Controllers\Instructor\LectureController;
use App\Domains\Learning\Http\Controllers\Instructor\LectureVideoQuestionController;
use App\Domains\Learning\Http\Controllers\Instructor\ManagementRequestController;
use App\Domains\Learning\Http\Controllers\Instructor\QuestionBankController;
use App\Domains\Learning\Http\Controllers\Instructor\QuestionController;
use App\Domains\Learning\Http\Controllers\Instructor\TaskController;
use Illuminate\Support\Facades\Route;

/**
 * المنهج، المحاضرات، الواجبات، الاختبارات، الأسئلة، المجموعات، المهام، المسارات، أنماط التعلم، الحضور، طلبات الإدارة.
 * يُحمَّل من داخل مجموعة instructor في routes/web/instructor.php وفي نفس موضعه القديم.
 */
Route::get('courses/{course}/curriculum', [CurriculumController::class, 'index'])->name('courses.curriculum');
Route::post('courses/{course}/curriculum/exams', [CurriculumController::class, 'storeExamFromCurriculum'])->name('courses.curriculum.exams.store');
Route::post('courses/{course}/curriculum/assignments', [CurriculumController::class, 'storeAssignmentFromCurriculum'])->name('courses.curriculum.assignments.store');
Route::post('courses/{course}/sections', [CurriculumController::class, 'storeSection'])->name('courses.sections.store');
Route::put('sections/{section}', [CurriculumController::class, 'updateSection'])->name('sections.update');
Route::put('sections/{section}/visibility', [CurriculumController::class, 'updateSectionVisibility'])->name('sections.visibility');
Route::delete('sections/{section}', [CurriculumController::class, 'destroySection'])->name('sections.destroy');
Route::post('sections/{section}/items', [CurriculumController::class, 'addItem'])->name('sections.items.store');
Route::put('curriculum-items/{item}/visibility', [CurriculumController::class, 'updateItemVisibility'])->name('curriculum-items.visibility');
Route::delete('curriculum-items/{item}', [CurriculumController::class, 'removeItem'])->name('curriculum-items.destroy');
Route::post('curriculum-items/{item}/move', [CurriculumController::class, 'moveItem'])->name('curriculum-items.move');
Route::post('courses/{course}/sections/order', [CurriculumController::class, 'updateSectionsOrder'])->name('courses.sections.order');
Route::post('sections/{section}/items/order', [CurriculumController::class, 'updateItemsOrder'])->name('sections.items.order');
Route::get('lectures/{lecture}/video-questions', [LectureVideoQuestionController::class, 'index'])->name('lectures.video-questions.index');
Route::post('lectures/{lecture}/video-questions', [LectureVideoQuestionController::class, 'store'])->name('lectures.video-questions.store');
Route::delete('lectures/{lecture}/video-questions/{videoQuestion}', [LectureVideoQuestionController::class, 'destroy'])->name('lectures.video-questions.destroy');
        
// تم إلغاء نظام الدروس — الاعتماد على المحاضرات فقط (إعادة توجيه الروابط القديمة)
Route::prefix('courses/{course}/lessons')->name('courses.lessons.')->group(function () {
    Route::get('/', fn($course) => redirect()->route('instructor.courses.curriculum', $course))->name('index');
    Route::get('/create', fn($course) => redirect()->route('instructor.lectures.index'))->name('create');
    Route::post('/', fn($course) => redirect()->route('instructor.courses.curriculum', $course)->with('info', 'تم إلغاء نظام الدروس؛ استخدم المحاضرات.'))->name('store');
    Route::get('/{lesson}', fn($course) => redirect()->route('instructor.lectures.index'))->name('show');
    Route::get('/{lesson}/edit', fn($course) => redirect()->route('instructor.lectures.index'))->name('edit');
    Route::put('/{lesson}', fn($course) => redirect()->route('instructor.courses.curriculum', $course))->name('update');
    Route::delete('/{lesson}', fn($course) => redirect()->route('instructor.courses.curriculum', $course))->name('destroy');
    Route::post('/{lesson}/toggle-status', fn($course) => redirect()->route('instructor.courses.curriculum', $course))->name('toggle-status');
    Route::post('/reorder', fn($course) => redirect()->route('instructor.courses.curriculum', $course))->name('reorder');
});

Route::get('/api/courses/{course}/lessons-list', fn($course) => response()->json([]));
        
// أنماط التعلم التفاعلية
Route::get('courses/{course}/learning-patterns', [LearningPatternController::class, 'index'])->name('learning-patterns.index');
Route::get('courses/{course}/learning-patterns/create', [LearningPatternController::class, 'create'])->name('learning-patterns.create');
Route::post('courses/{course}/learning-patterns', [LearningPatternController::class, 'store'])->name('learning-patterns.store');
Route::get('courses/{course}/learning-patterns/{pattern}', [LearningPatternController::class, 'show'])->name('learning-patterns.show');
Route::get('courses/{course}/learning-patterns/{pattern}/edit', [LearningPatternController::class, 'edit'])->name('learning-patterns.edit');
Route::put('courses/{course}/learning-patterns/{pattern}', [LearningPatternController::class, 'update'])->name('learning-patterns.update');
Route::delete('courses/{course}/learning-patterns/{pattern}', [LearningPatternController::class, 'destroy'])->name('learning-patterns.destroy');
Route::delete('courses/{course}/learning-patterns/{pattern}/attempts/{attempt}', [LearningPatternController::class, 'destroyAttempt'])->name('learning-patterns.attempts.destroy');
        
// API لدروس الكورس للمدرب
Route::resource('lectures', LectureController::class);
Route::post('/lectures/{lecture}/update-attendance', [LectureController::class, 'updateAttendance'])->name('lectures.update-attendance');
        
// المسار التعليمي للمدرب
Route::get('/learning-path', [LearningPathController::class, 'index'])->name('learning-path.index');
Route::get('/learning-path/{slug}', [LearningPathController::class, 'show'])->name('learning-path.show');
Route::post('/lectures/{lecture}/update-status', [LectureController::class, 'updateStatus'])->name('lectures.update-status');
Route::resource('groups', GroupController::class);
Route::post('/groups/{group}/add-member', [GroupController::class, 'addMember'])->name('groups.add-member');
Route::delete('/groups/{group}/remove-member', [GroupController::class, 'removeMember'])->name('groups.remove-member');
Route::resource('assignments', AssignmentController::class);
Route::get('/assignments/{assignment}/submissions', [AssignmentController::class, 'submissions'])->name('assignments.submissions');
Route::post('/assignments/{assignment}/grade/{submission}', [AssignmentController::class, 'grade'])->name('assignments.grade');
Route::resource('exams', ExamController::class);
Route::get('exams/{exam}/questions', [ExamQuestionController::class, 'manage'])->name('exams.questions.manage');
Route::post('exams/{exam}/questions/from-bank', [ExamQuestionController::class, 'addFromBank'])->name('exams.questions.add-from-bank');
Route::post('exams/{exam}/questions/new', [ExamQuestionController::class, 'createNew'])->name('exams.questions.create-new');
Route::delete('exams/{exam}/questions/{question}', [ExamQuestionController::class, 'remove'])->name('exams.questions.remove');
Route::post('exams/{exam}/questions/reorder', [ExamQuestionController::class, 'reorder'])->name('exams.questions.reorder');
        
// بنك الأسئلة
Route::resource('question-banks', QuestionBankController::class);
Route::post('question-banks/{questionBank}/questions', [QuestionController::class, 'store'])->name('question-banks.questions.store');
Route::get('question-banks/{questionBank}/questions/create', [QuestionController::class, 'create'])->name('question-banks.questions.create');
Route::resource('questions', QuestionController::class)->except(['create', 'store']);
Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
Route::get('/attendance/lecture/{lecture}', [AttendanceController::class, 'showLecture'])->name('attendance.lecture');
Route::resource('tasks', TaskController::class);
Route::get('/tasks/lectures', [TaskController::class, 'getLectures'])->name('tasks.lectures');
Route::post('/tasks/{task}/deliverables', [TaskController::class, 'submitDeliverable'])->name('tasks.submit-deliverable');
Route::put('/tasks/{task}/progress', [TaskController::class, 'updateProgress'])->name('tasks.update-progress');

// تقديم طلبات للإدارة
Route::prefix('management-requests')->name('management-requests.')->group(function () {
    Route::get('/', [ManagementRequestController::class, 'index'])->name('index');
    Route::get('/create', [ManagementRequestController::class, 'create'])->name('create');
    Route::post('/', [ManagementRequestController::class, 'store'])->name('store');
    Route::get('/{managementRequest}', [ManagementRequestController::class, 'show'])->name('show');
});
