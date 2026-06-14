<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$course = App\Models\AdvancedCourse::find(24);
if (!$course) {
    echo "Course 24 not found\n";
    exit(1);
}

$sections = $course->activeSections()->with(['activeItems' => fn ($q) => $q->orderBy('order')->with('item')])->orderBy('order')->get();

echo "Course 24: {$course->title}\n\n";
foreach ($sections as $section) {
    echo "Section {$section->id}: {$section->title} unlock_rule={$section->unlock_rule}\n";
    foreach ($section->activeItems as $ci) {
        $item = $ci->item;
        if (!$item) continue;
        $type = class_basename($item);
        $extra = '';
        if ($item instanceof App\Models\Lecture) {
            $extra = " min%={$item->min_watch_percent_to_unlock_next} dur={$item->duration_minutes} platform={$item->video_platform} url=" . substr($item->recording_url ?? '', 0, 60);
        }
        echo "  order={$ci->order} {$type} id={$item->id} title={$item->title}{$extra}\n";
    }
}

$ctrl = new App\Http\Controllers\Student\MyCourseController();
$ref = new ReflectionClass($ctrl);
$m = $ref->getMethod('buildNextItemMapByLectureId');
$m->setAccessible(true);
$nextMap = $m->invoke($ctrl, $sections);
echo "\nNext item map:\n";
print_r($nextMap);
