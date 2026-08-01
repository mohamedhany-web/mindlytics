<?php

namespace Tests\Unit;

use App\Models\AdvancedCourse;
use App\Models\CourseSection;
use App\Models\CurriculumItem;
use App\Models\Lecture;
use App\Models\LectureWatchProgress;
use App\Models\User;
use App\Services\CourseProgressService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CourseProgressServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('lecture_watch_progress');
        Schema::dropIfExists('curriculum_items');
        Schema::dropIfExists('course_sections');
        Schema::dropIfExists('lectures');
        Schema::dropIfExists('advanced_courses');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->timestamps();
        });

        Schema::create('advanced_courses', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('course_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('advanced_course_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('title')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('lectures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->string('title')->nullable();
            $table->unsignedTinyInteger('min_watch_percent_to_unlock_next')->nullable();
            $table->timestamps();
        });

        Schema::create('lecture_video_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lecture_id');
            $table->timestamps();
        });

        Schema::create('curriculum_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_section_id');
            $table->string('item_type');
            $table->unsignedBigInteger('item_id');
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('lecture_watch_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lecture_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('watch_time_seconds')->default(0);
            $table->unsignedInteger('video_duration_seconds')->default(0);
            $table->unsignedTinyInteger('progress_percent')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });
    }

    private function makeUser(string $email): User
    {
        return User::withoutEvents(fn () => User::query()->create([
            'name' => 'Student',
            'email' => $email,
            'password' => bcrypt('password'),
        ]));
    }

    private function makeCourse(string $title): AdvancedCourse
    {
        return AdvancedCourse::withoutEvents(fn () => AdvancedCourse::query()->create([
            'title' => $title,
            'is_active' => true,
        ]));
    }

    private function makeLecture(int $courseId, string $title): Lecture
    {
        $lecture = new Lecture([
            'title' => $title,
            'min_watch_percent_to_unlock_next' => 90,
        ]);
        $lecture->course_id = $courseId;
        $lecture->save();

        return $lecture;
    }

    public function test_progress_uses_current_user_lecture_watch_not_another_student(): void
    {
        $course = $this->makeCourse('Data');
        $section = CourseSection::query()->create([
            'advanced_course_id' => $course->id,
            'title' => 'S1',
            'order' => 1,
            'is_active' => true,
        ]);

        $lectures = collect();
        for ($i = 1; $i <= 3; $i++) {
            $lecture = $this->makeLecture((int) $course->id, "L{$i}");
            CurriculumItem::query()->create([
                'course_section_id' => $section->id,
                'item_type' => Lecture::class,
                'item_id' => $lecture->id,
                'order' => $i,
                'is_active' => true,
            ]);
            $lectures->push($lecture);
        }

        $studentA = $this->makeUser('a@test.local');
        $studentB = $this->makeUser('b@test.local');

        // Student B completed all lectures (noise that used to leak via watchProgress->first())
        foreach ($lectures as $lecture) {
            LectureWatchProgress::query()->create([
                'lecture_id' => $lecture->id,
                'user_id' => $studentB->id,
                'watch_time_seconds' => 100,
                'video_duration_seconds' => 100,
                'progress_percent' => 100,
                'is_completed' => true,
            ]);
        }

        // Student A completed only 2/3
        foreach ($lectures->take(2) as $lecture) {
            LectureWatchProgress::query()->create([
                'lecture_id' => $lecture->id,
                'user_id' => $studentA->id,
                'watch_time_seconds' => 100,
                'video_duration_seconds' => 100,
                'progress_percent' => 100,
                'is_completed' => true,
            ]);
        }

        $sections = CourseSection::query()
            ->where('advanced_course_id', $course->id)
            ->with(['activeItems.item'])
            ->get();

        // Simulate buggy eager-load of ALL watch rows (no user filter)
        foreach ($sections as $sectionRow) {
            foreach ($sectionRow->activeItems as $item) {
                if ($item->item instanceof Lecture) {
                    $item->item->load('watchProgress');
                }
            }
        }

        $service = app(CourseProgressService::class);
        [$progressA] = $service->calculateFromSections($studentA, $course, $sections);
        [$progressB] = $service->calculateFromSections($studentB, $course, $sections);

        $this->assertSame(66.67, $progressA);
        $this->assertSame(100.0, $progressB);
    }

    public function test_progress_reaches_100_when_all_lectures_completed_for_user(): void
    {
        $course = $this->makeCourse('Full');
        $section = CourseSection::query()->create([
            'advanced_course_id' => $course->id,
            'title' => 'S1',
            'order' => 1,
            'is_active' => true,
        ]);

        $user = $this->makeUser('full@test.local');
        for ($i = 1; $i <= 5; $i++) {
            $lecture = $this->makeLecture((int) $course->id, "L{$i}");
            CurriculumItem::query()->create([
                'course_section_id' => $section->id,
                'item_type' => Lecture::class,
                'item_id' => $lecture->id,
                'order' => $i,
                'is_active' => true,
            ]);
            LectureWatchProgress::query()->create([
                'lecture_id' => $lecture->id,
                'user_id' => $user->id,
                'watch_time_seconds' => 50,
                'video_duration_seconds' => 50,
                'progress_percent' => 95,
                'is_completed' => true,
            ]);
        }

        $sections = CourseSection::query()
            ->where('advanced_course_id', $course->id)
            ->with(['activeItems.item'])
            ->get();

        $service = app(CourseProgressService::class);
        $service->loadCurriculumProgressForUser($sections, $user);
        [$progress, $total, $completed] = $service->calculateFromSections($user, $course, $sections);

        $this->assertSame(5, $total);
        $this->assertSame(5, $completed);
        $this->assertSame(100.0, $progress);
    }

    public function test_update_from_sample_never_decreases_progress(): void
    {
        $user = $this->makeUser('watch@test.local');
        $course = $this->makeCourse('Watch');
        $lecture = $this->makeLecture((int) $course->id, 'L');

        $progress = LectureWatchProgress::query()->create([
            'lecture_id' => $lecture->id,
            'user_id' => $user->id,
            'watch_time_seconds' => 90,
            'video_duration_seconds' => 100,
            'progress_percent' => 90,
            'is_completed' => true,
        ]);

        $progress->updateFromSample(10, 100, 90);
        $progress->refresh();

        $this->assertSame(90, (int) $progress->progress_percent);
        $this->assertTrue((bool) $progress->is_completed);
        $this->assertSame(90, (int) $progress->watch_time_seconds);
    }
}
