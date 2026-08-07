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

    public function test_short_wrong_duration_does_not_complete_when_expected_duration_known(): void
    {
        $user = $this->makeUser('short@test.local');
        $course = $this->makeCourse('Short');
        $lecture = $this->makeLecture((int) $course->id, 'L');

        $progress = LectureWatchProgress::query()->create([
            'lecture_id' => $lecture->id,
            'user_id' => $user->id,
            'watch_time_seconds' => 0,
            'video_duration_seconds' => 0,
            'progress_percent' => 0,
            'is_completed' => false,
        ]);

        // Player wrongly reports 5s duration while curriculum says 10 minutes
        $progress->updateFromSample(5, 5, 90, 600);
        $progress->refresh();

        $this->assertSame(1, (int) $progress->progress_percent);
        $this->assertFalse((bool) $progress->is_completed);
        $this->assertSame(600, (int) $progress->video_duration_seconds);
    }

    public function test_sync_enrollment_progress_only_increases_by_default(): void
    {
        Schema::dropIfExists('student_course_enrollments');
        Schema::create('student_course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('advanced_course_id');
            $table->decimal('progress', 5, 2)->nullable();
            $table->timestamp('curriculum_completed_at')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        $user = $this->makeUser('enroll@test.local');
        $course = $this->makeCourse('Enroll');

        $enrollmentId = \DB::table('student_course_enrollments')->insertGetId([
            'user_id' => $user->id,
            'advanced_course_id' => $course->id,
            'progress' => 80,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $service = app(CourseProgressService::class);
        $service->syncEnrollmentProgress((int) $user->id, (int) $course->id, 50.0);
        $this->assertSame(80.0, (float) \DB::table('student_course_enrollments')->where('id', $enrollmentId)->value('progress'));

        $service->syncEnrollmentProgress((int) $user->id, (int) $course->id, 90.0);
        $this->assertSame(90.0, (float) \DB::table('student_course_enrollments')->where('id', $enrollmentId)->value('progress'));
        $this->assertNull(\DB::table('student_course_enrollments')->where('id', $enrollmentId)->value('curriculum_completed_at'));

        $service->syncEnrollmentProgress((int) $user->id, (int) $course->id, 100.0);
        $row = \DB::table('student_course_enrollments')->where('id', $enrollmentId)->first();
        $this->assertSame(100.0, (float) $row->progress);
        $this->assertNotNull($row->curriculum_completed_at);

        $service->syncEnrollmentProgress((int) $user->id, (int) $course->id, 40.0, true);
        $row = \DB::table('student_course_enrollments')->where('id', $enrollmentId)->first();
        $this->assertSame(40.0, (float) $row->progress);
        $this->assertNull($row->curriculum_completed_at);
    }

    public function test_percent_is_100_only_when_all_items_complete(): void
    {
        $service = app(CourseProgressService::class);
        $this->assertSame(100.0, $service->percentFromCounts(5, 5));
        $this->assertSame(0.0, $service->percentFromCounts(0, 0));
        $this->assertSame(66.67, $service->percentFromCounts(2, 3));
        $this->assertTrue($service->isFinishedPercent(100.0));
        $this->assertFalse($service->isFinishedPercent(99.99));
    }

    public function test_broken_curriculum_item_prevents_100_percent(): void
    {
        $course = $this->makeCourse('Broken');
        $section = CourseSection::query()->create([
            'advanced_course_id' => $course->id,
            'title' => 'S1',
            'order' => 1,
            'is_active' => true,
        ]);

        $user = $this->makeUser('broken@test.local');
        $lecture = $this->makeLecture((int) $course->id, 'L1');
        CurriculumItem::query()->create([
            'course_section_id' => $section->id,
            'item_type' => Lecture::class,
            'item_id' => $lecture->id,
            'order' => 1,
            'is_active' => true,
        ]);
        // عنصر مكسور (morph غير موجود)
        CurriculumItem::query()->create([
            'course_section_id' => $section->id,
            'item_type' => Lecture::class,
            'item_id' => 999999,
            'order' => 2,
            'is_active' => true,
        ]);

        LectureWatchProgress::query()->create([
            'lecture_id' => $lecture->id,
            'user_id' => $user->id,
            'watch_time_seconds' => 100,
            'video_duration_seconds' => 100,
            'progress_percent' => 100,
            'is_completed' => true,
        ]);

        $sections = CourseSection::query()
            ->where('advanced_course_id', $course->id)
            ->with(['activeItems.item'])
            ->get();

        $service = app(CourseProgressService::class);
        $service->loadCurriculumProgressForUser($sections, $user);
        [$progress, $total, $completed] = $service->calculateFromSections($user, $course, $sections);

        $this->assertSame(2, $total);
        $this->assertSame(1, $completed);
        $this->assertSame(50.0, $progress);
        $this->assertFalse($service->isFinishedPercent($progress));
    }
}
