<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::first();
if (!$user) {
    echo "No users\n";
    exit(1);
}

$lecture = App\Models\Lecture::find(11);
$progress = App\Models\LectureWatchProgress::firstOrNew([
    'lecture_id' => 11,
    'user_id' => $user->id,
]);
$progress->updateFromSample(3600, 3600, $lecture->min_watch_percent_to_unlock_next);
echo "Saved progress for lecture 11: {$progress->progress_percent}% completed=" . ($progress->is_completed ? 'yes' : 'no') . "\n";

$course = App\Models\AdvancedCourse::find(24);
$ctrl = app(App\Http\Controllers\Student\MyCourseController::class);
Auth::login($user);
$response = $ctrl->curriculumLocks(24);
$data = json_decode($response->getContent(), true);
echo "Lock lecture:5 = " . ($data['locks']['lecture:5'] ?? 'missing') . " (0=unlocked)\n";
