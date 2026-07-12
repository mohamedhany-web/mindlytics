<?php

namespace App\Services;

use App\Models\AdvancedCourse;
use App\Models\CourseSection;
use App\Models\CurriculumItem;
use App\Models\ScholarshipGroup;
use App\Models\ScholarshipRegistration;
use App\Models\User;
use Illuminate\Support\Collection;

class ScholarshipCurriculumVisibilityService
{
    public const SCOPE_ALL = 'all';

    public const SCOPE_SELECTED = 'selected';

    public const SCOPE_GROUPS = 'groups';

    public function isScholarshipCourse(?AdvancedCourse $course): bool
    {
        if (! $course) {
            return false;
        }

        return (bool) $course->is_scholarship_only || ! empty($course->scholarship_program_id);
    }

    /**
     * @return Collection<int, User>
     */
    public function selectableStudents(AdvancedCourse $course): Collection
    {
        if (! $this->isScholarshipCourse($course)) {
            return collect();
        }

        if ($course->scholarship_program_id) {
            $userIds = ScholarshipRegistration::query()
                ->where('scholarship_program_id', $course->scholarship_program_id)
                ->activated()
                ->pluck('user_id')
                ->unique()
                ->filter()
                ->values();

            if ($userIds->isNotEmpty()) {
                return User::query()
                    ->whereIn('id', $userIds)
                    ->orderBy('name')
                    ->get(['id', 'name', 'email']);
            }
        }

        return $course->activeStudents()
            ->orderBy('users.name')
            ->get(['users.id', 'users.name', 'users.email']);
    }

    /**
     * @return Collection<int, ScholarshipGroup>
     */
    public function selectableGroups(AdvancedCourse $course): Collection
    {
        if (! $this->isScholarshipCourse($course) || empty($course->scholarship_program_id)) {
            return collect();
        }

        return ScholarshipGroup::query()
            ->where('scholarship_program_id', $course->scholarship_program_id)
            ->withCount('members')
            ->orderBy('name')
            ->get();
    }

    public function sectionVisibleTo(CourseSection $section, User $user): bool
    {
        $scope = $section->visibility_scope ?? self::SCOPE_ALL;

        if ($scope === self::SCOPE_ALL || $scope === null || $scope === '') {
            return true;
        }

        if ($scope === self::SCOPE_SELECTED) {
            if ($section->relationLoaded('visibleStudents')) {
                return $section->visibleStudents->contains(fn ($u) => (int) $u->id === (int) $user->id);
            }

            return $section->visibleStudents()->where('users.id', $user->id)->exists();
        }

        if ($scope === self::SCOPE_GROUPS) {
            return $this->userInVisibleGroups($section->relationLoaded('visibleGroups')
                ? $section->visibleGroups
                : $section->visibleGroups()->with('members:id')->get(), $user);
        }

        return true;
    }

    public function itemVisibleTo(CurriculumItem $item, User $user): bool
    {
        $scope = $item->visibility_scope ?? self::SCOPE_ALL;

        if ($scope === self::SCOPE_ALL || $scope === null || $scope === '') {
            return true;
        }

        if ($scope === self::SCOPE_SELECTED) {
            if ($item->relationLoaded('visibleStudents')) {
                return $item->visibleStudents->contains(fn ($u) => (int) $u->id === (int) $user->id);
            }

            return $item->visibleStudents()->where('users.id', $user->id)->exists();
        }

        if ($scope === self::SCOPE_GROUPS) {
            return $this->userInVisibleGroups($item->relationLoaded('visibleGroups')
                ? $item->visibleGroups
                : $item->visibleGroups()->with('members:id')->get(), $user);
        }

        return true;
    }

    /**
     * @param  Collection<int, ScholarshipGroup>  $groups
     */
    private function userInVisibleGroups(Collection $groups, User $user): bool
    {
        foreach ($groups as $group) {
            if ($group->relationLoaded('members')) {
                if ($group->members->contains(fn ($u) => (int) $u->id === (int) $user->id)) {
                    return true;
                }
            } elseif ($group->members()->where('users.id', $user->id)->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  Collection<int, CourseSection>  $sections
     * @return Collection<int, CourseSection>
     */
    public function filterSectionsForStudent(Collection $sections, User $user, ?AdvancedCourse $course = null): Collection
    {
        if ($sections->isEmpty()) {
            return $sections;
        }

        $course = $course ?? $sections->first()?->course;
        if (! $this->isScholarshipCourse($course)) {
            return $sections;
        }

        return $sections
            ->filter(function (CourseSection $section) use ($user, $sections) {
                if (! $this->sectionVisibleTo($section, $user)) {
                    return false;
                }

                $parentId = $section->parent_id;
                while ($parentId) {
                    $parent = $sections->firstWhere('id', $parentId);
                    if (! $parent) {
                        break;
                    }
                    if (! $this->sectionVisibleTo($parent, $user)) {
                        return false;
                    }
                    $parentId = $parent->parent_id;
                }

                return true;
            })
            ->map(function (CourseSection $section) use ($user) {
                $items = $section->relationLoaded('activeItems')
                    ? $section->activeItems
                    : ($section->relationLoaded('items') ? $section->items : collect());

                $filtered = $items
                    ->filter(fn (CurriculumItem $item) => $this->itemVisibleTo($item, $user))
                    ->values();

                if ($section->relationLoaded('activeItems')) {
                    $section->setRelation('activeItems', $filtered);
                }
                if ($section->relationLoaded('items')) {
                    $section->setRelation('items', $filtered);
                }

                return $section;
            })
            ->values();
    }

    /**
     * @param  Collection<int, CourseSection>  $allSectionsFlat
     * @return Collection<int, CourseSection>
     */
    public function buildVisibleTree(Collection $allSectionsFlat, User $user, ?AdvancedCourse $course = null): Collection
    {
        $visible = $this->filterSectionsForStudent($allSectionsFlat, $user, $course);

        foreach ($visible as $section) {
            $children = $visible->where('parent_id', $section->id)->values();
            $section->setRelation('children', $children);
        }

        return $visible->whereNull('parent_id')->values();
    }

    public function contentVisibleToStudent(AdvancedCourse $course, string $itemType, int $itemId, User $user): bool
    {
        if (! $this->isScholarshipCourse($course)) {
            return true;
        }

        $curriculumItem = CurriculumItem::query()
            ->where('item_type', $itemType)
            ->where('item_id', $itemId)
            ->whereHas('section', fn ($q) => $q->where('advanced_course_id', $course->id))
            ->with([
                'section.visibleStudents',
                'section.visibleGroups.members:id',
                'visibleStudents',
                'visibleGroups.members:id',
            ])
            ->first();

        if (! $curriculumItem) {
            return true;
        }

        if (! $this->sectionVisibleTo($curriculumItem->section, $user)) {
            return false;
        }

        return $this->itemVisibleTo($curriculumItem, $user);
    }

    /**
     * @param  array<int>|null  $studentIds
     * @param  array<int>|null  $groupIds
     */
    public function syncSectionVisibility(
        CourseSection $section,
        AdvancedCourse $course,
        string $scope,
        ?array $studentIds = [],
        ?array $groupIds = []
    ): void {
        if (! $this->isScholarshipCourse($course)) {
            $section->visibility_scope = self::SCOPE_ALL;
            $section->save();
            $section->visibleStudents()->detach();
            $section->visibleGroups()->detach();

            return;
        }

        $scope = in_array($scope, [self::SCOPE_SELECTED, self::SCOPE_GROUPS], true)
            ? $scope
            : self::SCOPE_ALL;

        $section->visibility_scope = $scope;
        $section->save();

        if ($scope === self::SCOPE_SELECTED) {
            $section->visibleGroups()->detach();
            $allowed = $this->selectableStudents($course)->pluck('id')->all();
            $ids = collect($studentIds ?? [])
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id > 0 && in_array($id, $allowed, true))
                ->unique()
                ->values()
                ->all();
            $section->visibleStudents()->sync($ids);

            return;
        }

        if ($scope === self::SCOPE_GROUPS) {
            $section->visibleStudents()->detach();
            $allowed = $this->selectableGroups($course)->pluck('id')->all();
            $ids = collect($groupIds ?? [])
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id > 0 && in_array($id, $allowed, true))
                ->unique()
                ->values()
                ->all();
            $section->visibleGroups()->sync($ids);

            return;
        }

        $section->visibleStudents()->detach();
        $section->visibleGroups()->detach();
    }

    /**
     * @param  array<int>|null  $studentIds
     * @param  array<int>|null  $groupIds
     */
    public function syncItemVisibility(
        CurriculumItem $item,
        AdvancedCourse $course,
        string $scope,
        ?array $studentIds = [],
        ?array $groupIds = []
    ): void {
        if (! $this->isScholarshipCourse($course)) {
            $item->visibility_scope = self::SCOPE_ALL;
            $item->save();
            $item->visibleStudents()->detach();
            $item->visibleGroups()->detach();

            return;
        }

        $scope = in_array($scope, [self::SCOPE_SELECTED, self::SCOPE_GROUPS], true)
            ? $scope
            : self::SCOPE_ALL;

        $item->visibility_scope = $scope;
        $item->save();

        if ($scope === self::SCOPE_SELECTED) {
            $item->visibleGroups()->detach();
            $allowed = $this->selectableStudents($course)->pluck('id')->all();
            $ids = collect($studentIds ?? [])
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id > 0 && in_array($id, $allowed, true))
                ->unique()
                ->values()
                ->all();
            $item->visibleStudents()->sync($ids);

            return;
        }

        if ($scope === self::SCOPE_GROUPS) {
            $item->visibleStudents()->detach();
            $allowed = $this->selectableGroups($course)->pluck('id')->all();
            $ids = collect($groupIds ?? [])
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id > 0 && in_array($id, $allowed, true))
                ->unique()
                ->values()
                ->all();
            $item->visibleGroups()->sync($ids);

            return;
        }

        $item->visibleStudents()->detach();
        $item->visibleGroups()->detach();
    }
}
