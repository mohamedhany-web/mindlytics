<?php

namespace Tests\Unit;

use App\Models\AdvancedCourse;
use App\Models\CourseSection;
use App\Models\CurriculumItem;
use App\Models\User;
use App\Services\ScholarshipCurriculumVisibilityService;
use Tests\TestCase;

class ScholarshipCurriculumVisibilityTest extends TestCase
{
    private ScholarshipCurriculumVisibilityService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ScholarshipCurriculumVisibilityService::class);
    }

    private function user(int $id, string $name = 'Student'): User
    {
        $user = new User;
        $user->forceFill(['id' => $id, 'name' => $name]);

        return $user;
    }

    private function scholarshipCourse(): AdvancedCourse
    {
        $course = new AdvancedCourse;
        $course->forceFill([
            'id' => 2,
            'is_scholarship_only' => true,
            'scholarship_program_id' => 9,
        ]);

        return $course;
    }

    private function regularCourse(): AdvancedCourse
    {
        $course = new AdvancedCourse;
        $course->forceFill([
            'id' => 1,
            'is_scholarship_only' => false,
            'scholarship_program_id' => null,
        ]);

        return $course;
    }

    private function item(int $id, string $scope = 'all', ?int $visibleUserId = null): CurriculumItem
    {
        $item = new CurriculumItem;
        $item->forceFill([
            'id' => $id,
            'visibility_scope' => $scope,
            'is_active' => true,
        ]);

        if ($scope === 'selected' && $visibleUserId) {
            $u = $this->user($visibleUserId);
            $item->setRelation('visibleStudents', collect([$u]));
        } else {
            $item->setRelation('visibleStudents', collect());
        }

        return $item;
    }

    private function section(int $id, string $scope = 'all', ?int $visibleUserId = null, $items = null): CourseSection
    {
        $section = new CourseSection;
        $section->forceFill([
            'id' => $id,
            'title' => 'Section '.$id,
            'visibility_scope' => $scope,
            'advanced_course_id' => 2,
            'parent_id' => null,
        ]);

        if ($scope === 'selected' && $visibleUserId) {
            $section->setRelation('visibleStudents', collect([$this->user($visibleUserId)]));
        } else {
            $section->setRelation('visibleStudents', collect());
        }

        $section->setRelation('activeItems', collect($items ?? []));

        return $section;
    }

    public function test_non_scholarship_course_ignores_visibility_restrictions(): void
    {
        $student = $this->user(10);
        $course = $this->regularCourse();

        $section = $this->section(1, 'selected', 999, [
            $this->item(100, 'selected', 999),
            $this->item(101, 'all'),
        ]);

        $filtered = $this->service->filterSectionsForStudent(collect([$section]), $student, $course);

        $this->assertCount(1, $filtered);
        $this->assertCount(2, $filtered->first()->activeItems, 'الكورس العادي يظهر كل العناصر حتى لو فيها قيود');
    }

    public function test_restricted_lecture_hides_only_that_lecture_other_section_items_remain(): void
    {
        $allowed = $this->user(5, 'Allowed');
        $other = $this->user(6, 'Other');
        $course = $this->scholarshipCourse();

        $restrictedLecture = $this->item(100, 'selected', 5);
        $openLecture = $this->item(101, 'all');
        $openExam = $this->item(102, 'all');

        $section = $this->section(10, 'all', null, [
            $restrictedLecture,
            $openLecture,
            $openExam,
        ]);

        $forAllowed = $this->service->filterSectionsForStudent(collect([$section]), $allowed, $course);
        $this->assertCount(1, $forAllowed);
        $this->assertCount(3, $forAllowed->first()->activeItems, 'الطالب المختار يرى المحاضرة المقيدة + باقي العناصر');
        $this->assertEqualsCanonicalizing(
            [100, 101, 102],
            $forAllowed->first()->activeItems->pluck('id')->all()
        );

        // إعادة بناء العناصر لأن الفلتر يعدّل العلاقة
        $section->setRelation('activeItems', collect([
            $this->item(100, 'selected', 5),
            $this->item(101, 'all'),
            $this->item(102, 'all'),
        ]));

        $forOther = $this->service->filterSectionsForStudent(collect([$section]), $other, $course);
        $this->assertCount(1, $forOther, 'القسم نفسه يبقى ظاهرًا للطالب الآخر');
        $this->assertCount(2, $forOther->first()->activeItems, 'يخفى فقط المحاضرة المقيدة');
        $this->assertEqualsCanonicalizing(
            [101, 102],
            $forOther->first()->activeItems->pluck('id')->all()
        );
        $this->assertFalse(
            $forOther->first()->activeItems->contains('id', 100),
            'المحاضرة المقيدة لا تظهر لغير المختارين'
        );
    }

    public function test_restricted_section_hides_entire_section_for_non_selected_students(): void
    {
        $allowed = $this->user(5);
        $other = $this->user(6);
        $course = $this->scholarshipCourse();

        $openSection = $this->section(10, 'all', null, [$this->item(101, 'all')]);
        $restrictedSection = $this->section(11, 'selected', 5, [$this->item(200, 'all')]);

        $forAllowed = $this->service->filterSectionsForStudent(
            collect([$openSection, $restrictedSection]),
            $allowed,
            $course
        );
        $this->assertCount(2, $forAllowed);
        $this->assertNotNull($forAllowed->firstWhere('id', 11));

        $openSection->setRelation('activeItems', collect([$this->item(101, 'all')]));
        $restrictedSection->setRelation('activeItems', collect([$this->item(200, 'all')]));
        $restrictedSection->setRelation('visibleStudents', collect([$this->user(5)]));

        $forOther = $this->service->filterSectionsForStudent(
            collect([$openSection, $restrictedSection]),
            $other,
            $course
        );
        $this->assertCount(1, $forOther);
        $this->assertSame(10, $forOther->first()->id);
        $this->assertNull($forOther->firstWhere('id', 11));
    }

    public function test_is_scholarship_course_detection(): void
    {
        $this->assertFalse($this->service->isScholarshipCourse($this->regularCourse()));

        $onlyFlag = new AdvancedCourse;
        $onlyFlag->forceFill(['is_scholarship_only' => true, 'scholarship_program_id' => null]);
        $this->assertTrue($this->service->isScholarshipCourse($onlyFlag));

        $linked = new AdvancedCourse;
        $linked->forceFill(['is_scholarship_only' => false, 'scholarship_program_id' => 3]);
        $this->assertTrue($this->service->isScholarshipCourse($linked));
    }

    public function test_instructor_ui_flag_only_true_for_scholarship_courses(): void
    {
        $this->assertFalse($this->service->isScholarshipCourse($this->regularCourse()));
        $this->assertTrue($this->service->isScholarshipCourse($this->scholarshipCourse()));
    }
}
