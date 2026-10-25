<?php

use App\Domains\Learning\Http\Controllers\Admin\CourseController;
use App\Domains\Learning\Http\Controllers\Admin\CourseLessonController;
use App\Domains\Learning\Http\Controllers\Admin\ExamController;
use App\Domains\Learning\Http\Controllers\Admin\PracticeController;
use App\Domains\Learning\Http\Controllers\Admin\QuestionBankController;
use App\Domains\Learning\Http\Controllers\Admin\QuestionCategoryController;
use App\Domains\Learning\Http\Controllers\Admin\SubjectController;
use Illuminate\Support\Facades\Route;

/**
 * الدروس والاختبارات وبنوك الأسئلة والواجبات والكورسات.
 * يُحمَّل من داخل مجموعة admin في routes/web/admin.php (prefix: admin، name: admin.، role middleware)
 * وفي نفس موضعه القديم.
 */
Route::get('/courses/{course}/lessons-list', function(\App\Domains\Learning\Models\AdvancedCourse $course) {
    return response()->json($course->lessons()->active()->select('id', 'title')->get());
});

// إدارة دروس الكورسات
Route::prefix('courses/{course}/lessons')->name('courses.lessons.')->group(function () {
    Route::get('/', [CourseLessonController::class, 'index'])->name('index');
    Route::get('/create', [CourseLessonController::class, 'create'])->name('create');
    Route::post('/', [CourseLessonController::class, 'store'])->name('store');
    Route::get('/{lesson}', [CourseLessonController::class, 'show'])->name('show');
    Route::get('/{lesson}/edit', [CourseLessonController::class, 'edit'])->name('edit');
    Route::put('/{lesson}', [CourseLessonController::class, 'update'])->name('update');
    Route::delete('/{lesson}', [CourseLessonController::class, 'destroy'])->name('destroy');
    Route::post('/{lesson}/toggle-status', [CourseLessonController::class, 'toggleStatus'])->name('toggle-status');
    Route::post('/reorder', [CourseLessonController::class, 'reorder'])->name('reorder');
});

// إدارة بنك الأسئلة
Route::prefix('question-bank')->name('question-bank.')->group(function () {
    Route::get('/', [QuestionBankController::class, 'index'])->name('index');
    Route::get('/create', [QuestionBankController::class, 'create'])->name('create');
    Route::post('/', [QuestionBankController::class, 'store'])->name('store');
    Route::get('/{question}', [QuestionBankController::class, 'show'])->name('show');
    Route::get('/{question}/edit', [QuestionBankController::class, 'edit'])->name('edit');
    Route::put('/{question}', [QuestionBankController::class, 'update'])->name('update');
    Route::delete('/{question}', [QuestionBankController::class, 'destroy'])->name('destroy');
    Route::post('/{question}/duplicate', [QuestionBankController::class, 'duplicate'])->name('duplicate');
    Route::post('/export', [QuestionBankController::class, 'export'])->name('export');
    Route::post('/import', [QuestionBankController::class, 'import'])->name('import');
});

// إدارة تصنيفات الأسئلة
Route::prefix('question-categories')->name('question-categories.')->group(function () {
    Route::get('/', [QuestionCategoryController::class, 'index'])->name('index');
    Route::get('/create', [QuestionCategoryController::class, 'create'])->name('create');
    Route::post('/', [QuestionCategoryController::class, 'store'])->name('store');
    Route::get('/{questionCategory}', [QuestionCategoryController::class, 'show'])->name('show');
    Route::get('/{questionCategory}/edit', [QuestionCategoryController::class, 'edit'])->name('edit');
    Route::put('/{questionCategory}', [QuestionCategoryController::class, 'update'])->name('update');
    Route::delete('/{questionCategory}', [QuestionCategoryController::class, 'destroy'])->name('destroy');
    Route::post('/reorder', [QuestionCategoryController::class, 'reorder'])->name('reorder');
    Route::get('/subjects-by-year/{year}', [QuestionCategoryController::class, 'getSubjectsByYear'])->name('subjects-by-year');
});

// إدارة الامتحانات (مسار الكورس قبل المسارات الأخرى لتفادي التعارض)
Route::prefix('exams')->name('exams.')->group(function () {
    Route::get('/course/{course}', [ExamController::class, 'indexByCourse'])->name('by-course');
    Route::get('/', [ExamController::class, 'index'])->name('index');
    Route::get('/create', [ExamController::class, 'create'])->name('create');
    Route::post('/', [ExamController::class, 'store'])->name('store');
    Route::get('/{exam}', [ExamController::class, 'show'])->name('show');
    Route::get('/{exam}/edit', [ExamController::class, 'edit'])->name('edit');
    Route::put('/{exam}', [ExamController::class, 'update'])->name('update');
    Route::delete('/{exam}', [ExamController::class, 'destroy'])->name('destroy');
    Route::get('/{exam}/questions', [ExamController::class, 'manageQuestions'])->name('questions.manage');
    Route::post('/{exam}/questions', [ExamController::class, 'addQuestion'])->name('questions.add');
    Route::delete('/{exam}/questions/{examQuestion}', [ExamController::class, 'removeQuestion'])->name('questions.remove');
    Route::post('/{exam}/questions/reorder', [ExamController::class, 'reorderQuestions'])->name('questions.reorder');
    Route::post('/{exam}/toggle-publish', [ExamController::class, 'togglePublish'])->name('toggle-publish');
    Route::post('/{exam}/toggle-status', [ExamController::class, 'toggleStatus'])->name('toggle-status');
    Route::get('/{exam}/statistics', [ExamController::class, 'statistics'])->name('statistics');
    Route::get('/{exam}/preview', [ExamController::class, 'preview'])->name('preview');
    Route::post('/{exam}/duplicate', [ExamController::class, 'duplicate'])->name('duplicate');
});

// إدارة التمارين العملية (Practice / Learning Patterns)
Route::prefix('practice')->name('practice.')->group(function () {
    Route::get('/', [PracticeController::class, 'index'])->name('index');
    Route::get('/{pattern}', [PracticeController::class, 'show'])->name('show');
});

// إدارة المواد الدراسية القديمة
Route::resource('subjects', SubjectController::class);

// إدارة الكورسات القديمة
Route::resource('courses', CourseController::class);
