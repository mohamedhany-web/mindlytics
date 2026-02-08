<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// التحقق من الكورس
$course = \App\Models\AdvancedCourse::find(15);

if ($course) {
    echo "Course ID: 15\n";
    echo "Title: " . $course->title . "\n";
    echo "Instructor ID: " . $course->instructor_id . "\n";
    
    if ($course->instructor) {
        echo "Instructor Name: " . $course->instructor->name . "\n";
    } else {
        echo "Instructor: Not assigned\n";
    }
} else {
    echo "Course not found\n";
}

echo "\n--- Current User (ID: 3) ---\n";
$user = \App\Models\User::find(3);
if ($user) {
    echo "User ID: " . $user->id . "\n";
    echo "Name: " . $user->name . "\n";
    echo "Role: " . $user->role . "\n";
} else {
    echo "User not found\n";
}
