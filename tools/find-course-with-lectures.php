<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$courses = App\Models\AdvancedCourse::query()->limit(20)->get();
foreach ($courses as $c) {
    $lecCount = App\Models\Lecture::where('advanced_course_id', $c->id)->count();
    $itemCount = App\Models\CourseCurriculumItem::whereHas('section', fn($q) => $q->where('advanced_course_id', $c->id))->count();
    echo "course {$c->id}: {$c->title} lectures={$lecCount} curriculum_items={$itemCount}\n";
}

$lec = App\Models\Lecture::whereNotNull('recording_url')->where('recording_url', '!=', '')->first();
if ($lec) {
    echo "\nSample lecture {$lec->id} course={$lec->advanced_course_id} url=" . substr($lec->recording_url, 0, 60) . "\n";
}
