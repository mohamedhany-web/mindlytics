<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$c = App\Models\AdvancedCourse::with(['lectures', 'sections.activeItems.item'])->find(23);
echo "Course 23: {$c->title}\n";
echo "lectures relation: {$c->lectures->count()}\n";
echo "sections: {$c->sections->count()}\n";

$allSections = $c->activeSections()->with(['activeItems.item'])->orderBy('order')->get();
echo "activeSections: {$allSections->count()}\n";
foreach ($allSections as $s) {
    echo "  section {$s->id} {$s->title} items={$s->activeItems->count()}\n";
    foreach ($s->activeItems as $ci) {
        $i = $ci->item;
        echo "    item type=" . ($ci->item_type ?? '?') . " id=" . ($ci->item_id ?? '?') . " resolved=" . ($i ? class_basename($i) . ':' . $i->id : 'NULL') . "\n";
    }
}
