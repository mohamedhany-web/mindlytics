<?php

namespace Tests\Unit;

use App\Models\AdvancedCourse;
use App\Models\CourseSection;
use App\Models\CurriculumItem;
use App\Models\ScholarshipGroup;
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

    private function item(int $id, string $scope = 'all', ?int $visibleUserId = null, ?ScholarshipGroup $group = null): CurriculumItem
    {
        $item = new CurriculumItem;
        $item->forceFill([
            'id' => $id,
            'visibility_scope' => $scope,
            'is_active' => true,
        ]);

        if ($scope === 'selected' && $visibleUserId) {
            $item->setRelation('visibleStudents', collect([$this->user($visibleUserId)]));
            $item->setRelation('visibleGroups', collect());
        } elseif ($scope === 'groups' && $group) {
            $item->setRelation('visibleStudents', collect());
            $item->setRelation('visibleGroups', collect([$group]));
        } else {
            $item->setRelation('visibleStudents', collect());
            $item->setRelation('visibleGroups', collect());
        }

        return $item;
    }

    private function section(int $id, string $scope = 'all', ?int $visibleUserId = null, $items = null, ?ScholarshipGroup $group = null): CourseSection
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
            $section->setRelation('visibleGroups', collect());
        } elseif ($scope === 'groups' && $group) {
            $section->setRelation('visibleStudents', collect());
            $section->setRelation('visibleGroups', collect([$group]));
        } else {
            $section->setRelation('visibleStudents', collect());
            $section->setRelation('visibleGroups', collect());
        }

        $section->setRelation('activeItems', collect($items ?? []));

        return $section;
    }

    private function groupWithMember(int $groupId, int $memberId): ScholarshipGroup
    {
        $group = new ScholarshipGroup;
        $group->forceFill(['id' => $groupId, 'name' => 'Group '.$groupId]);
        $group->setRelation('members', collect([$this->user($memberId)]));

        return $group;
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
        $this->assertCount(2, $filtered->first()->activeItems);
    }

    public function test_restricted_lecture_hides_only_that_lecture_other_section_items_remain(): void
    {
        $allowed = $this->user(5, 'Allowed');
        $other = $this->user(6, 'Other');
        $course = $this->scholarshipCourse();

        $section = $this->section(10, 'all', null, [
            $this->item(100, 'selected', 5),
            $this->item(101, 'all'),
            $this->item(102, 'all'),
        ]);

        $forAllowed = $this->service->filterSectionsForStudent(collect([$section]), $allowed, $course);
        $this->assertCount(3, $forAllowed->first()->activeItems);

        $section->setRelation('activeItems', collect([
            $this->item(100, 'selected', 5),
            $this->item(101, 'all'),
            $this->item(102, 'all'),
        ]));
        $forOther = $this->service->filterSectionsForStudent(collect([$section]), $other, $course);
        $this->assertCount(2, $forOther->first()->activeItems);
        $this->assertEqualsCanonicalizing([101, 102], $forOther->first()->activeItems->pluck('id')->all());
    }

    public function test_group_visibility_shows_lecture_only_to_group_members(): void
    {
        $member = $this->user(5);
        $outsider = $this->user(6);
        $course = $this->scholarshipCourse();
        $group = $this->groupWithMember(20, 5);

        $section = $this->section(10, 'all', null, [
            $this->item(100, 'groups', null, $group),
            $this->item(101, 'all'),
        ]);

        $forMember = $this->service->filterSectionsForStudent(collect([$section]), $member, $course);
        $this->assertCount(2, $forMember->first()->activeItems);

        $section->setRelation('activeItems', collect([
            $this->item(100, 'groups', null, $group),
            $this->item(101, 'all'),
        ]));
        $forOutsider = $this->service->filterSectionsForStudent(collect([$section]), $outsider, $course);
        $this->assertCount(1, $forOutsider->first()->activeItems);
        $this->assertSame(101, $forOutsider->first()->activeItems->first()->id);
    }

    public function test_restricted_section_by_group_hides_entire_section(): void
    {
        $member = $this->user(5);
        $outsider = $this->user(6);
        $course = $this->scholarshipCourse();
        $group = $this->groupWithMember(21, 5);

        $open = $this->section(10, 'all', null, [$this->item(101, 'all')]);
        $restricted = $this->section(11, 'groups', null, [$this->item(200, 'all')], $group);

        $forMember = $this->service->filterSectionsForStudent(collect([$open, $restricted]), $member, $course);
        $this->assertCount(2, $forMember);

        $open->setRelation('activeItems', collect([$this->item(101, 'all')]));
        $restricted = $this->section(11, 'groups', null, [$this->item(200, 'all')], $group);
        $forOutsider = $this->service->filterSectionsForStudent(collect([$open, $restricted]), $outsider, $course);
        $this->assertCount(1, $forOutsider);
        $this->assertSame(10, $forOutsider->first()->id);
    }

    public function test_is_scholarship_course_detection(): void
    {
        $this->assertFalse($this->service->isScholarshipCourse($this->regularCourse()));
        $this->assertTrue($this->service->isScholarshipCourse($this->scholarshipCourse()));
    }
}
