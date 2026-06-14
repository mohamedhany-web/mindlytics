<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Total lectures: " . App\Models\Lecture::count() . "\n";
foreach (App\Models\Lecture::all() as $l) {
    echo "  lecture {$l->id} course={$l->course_id} " . substr($l->title ?? '', 0, 50) . " video=" . ($l->recording_url ? 'yes' : 'no') . "\n";
}

foreach (App\Models\AdvancedCourse::all() as $c) {
    $rel = $c->lectures()->count();
    if ($rel > 0) {
        echo "Course {$c->id} ({$c->title}): lectures={$rel}\n";
    }
}
