<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$course = App\Models\AdvancedCourse::query()->first();
if (!$course) {
    echo "NO_COURSE\n";
    exit(1);
}

$html = view('student.my-courses.learn', [
    'course' => $course,
    'sections' => collect(),
    'progress' => 0,
    'totalLessons' => 0,
    'completedLessons' => 0,
    'sectionDescriptions' => [],
    'nextItemByLectureId' => [],
    'nextItemByLessonId' => [],
    'sidebarExams' => collect(),
    'lecturesDataJson' => '{}',
])->render();

file_put_contents(__DIR__ . '/learn-render.html', $html);
echo "VIEW_OK len=" . strlen($html) . "\n";

if (!preg_match('/function courseFocusMode\(\)/', $html)) {
    echo "MISSING courseFocusMode\n";
}
if (!preg_match('/id="learn-video-embed"/', $html)) {
    echo "MISSING learn-video-embed\n";
}
if (!preg_match('/x-data="courseFocusMode\(\)"/', $html)) {
    echo "MISSING x-data\n";
}
