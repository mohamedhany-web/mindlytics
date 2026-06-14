<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$c = App\Models\AdvancedCourse::with(['lectures', 'sections.activeItems.item'])->first();
if (!$c) {
    echo "NO_COURSE\n";
    exit(1);
}
echo "course={$c->id}\n";
echo "lectures={$c->lectures->count()}\n";
echo "sections={$c->sections->count()}\n";
foreach ($c->sections as $s) {
    echo "section {$s->id} {$s->title} items={$s->activeItems->count()}\n";
    foreach ($s->activeItems as $ci) {
        $i = $ci->item;
        if ($i) {
            $cls = class_basename($i);
            $title = $i->title ?? '';
            $url = ($i instanceof App\Models\Lecture) ? ($i->recording_url ? 'HAS_VIDEO' : 'NO_VIDEO') : '';
            echo "  - {$cls} {$i->id} {$title} {$url}\n";
        }
    }
}

$lec = $c->lectures->first();
if ($lec) {
    echo "first_lecture recording_url=" . substr($lec->recording_url ?? 'NULL', 0, 80) . "\n";
    echo "platform=" . ($lec->video_platform ?? 'NULL') . "\n";
}
