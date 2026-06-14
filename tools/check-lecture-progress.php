<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$lectures = App\Models\Lecture::query()
    ->where('title', 'like', '%ريفرش%')
    ->orWhere('title', 'like', '%تجريب%')
    ->with(['watchProgress', 'course'])
    ->get(['id', 'title', 'course_id', 'min_watch_percent_to_unlock_next']);

foreach ($lectures as $l) {
    $wp = $l->watchProgress->first();
    echo sprintf(
        "Lecture %d course=%d title=%s min%%=%s progress=%s completed=%s\n",
        $l->id,
        $l->course_id,
        $l->title,
        var_export($l->min_watch_percent_to_unlock_next, true),
        $wp ? $wp->progress_percent : 'null',
        $wp ? ($wp->is_completed ? '1' : '0') : 'null'
    );
}
