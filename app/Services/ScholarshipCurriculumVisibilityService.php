<?php

namespace App\Services;

use App\Models\AdvancedCourse;
use App\Models\CourseSection;
use App\Models\CurriculumItem;
use App\Models\ScholarshipRegistration;
use App\Models\User;
use Illuminate\Support\Collection;

class ScholarshipCurriculumVisibilityService
{
    public const SCOPE_ALL = 'all';

    public const SCOPE_SELECTED = 'selected';

    public function isScholarshipCourse(?AdvancedCourse $course): bool
    {
        if (! $course) {
            return false;
        }

        return (bool) $course->is_scholarship_only || ! empty($course->scholarship_program_id);
    }

    /**
     * طلبة المنحة المتاح اختيارهم في توصيف المنهج.
     *
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

    public function sectionVisibleTo(CourseSection $section, User $user): bool
    {
        $scope = $section->visibility_scope ?? self::SCOPE_ALL;
        if ($scope !== self::SCOPE_SELECTED) {
            return true;
        }

        if ($section->relationLoaded('visibleStudents')) {
            return $section->visibleStudents->contains(fn ($u) => (int) $u->id === (int) $user->id);
        }

        return $section->visibleStudents()->where('users.id', $user->id)->exists();
    }

    public function itemVisibleTo(CurriculumItem $item, User $user): bool
    {
        $scope = $item->visibility_scope ?? self::SCOPE_ALL;
        if ($scope !== self::SCOPE_SELECTED) {
            return true;
        }

        if ($item->relationLoaded('visibleStudents')) {
            return $item->visibleStudents->contains(fn ($u) => (int) $u->id === (int) $user->id);
        }

        return $item->visibleStudents()->where('users.id', $user->id)->exists();
    }

    /**
     * تصفية الأقسام والعناصر حسب ظهور الطالب (للمنح فقط).
     * يُرجع مجموعة مسطّحة من الأقسام المرئية مع عناصرها المصفّاة.
     *
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

                // إخفاء القسم الفرعي إذا كان أحد الآباء مخفياً عن الطالب
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
     * بناء شجرة أطفال بعد التصفية (يستبعد الأقسام الفرعية المخفية).
     *
     * @param  Collection<int, CourseSection>  $allSectionsFlat
     * @return Collection<int, CourseSection> الجذور فقط
     */
    public function buildVisibleTree(Collection $allSectionsFlat, User $user, ?AdvancedCourse $course = null): Collection
    {
        $visible = $this->filterSectionsForStudent($allSectionsFlat, $user, $course);
        $visibleIds = $visible->pluck('id')->all();

        foreach ($visible as $section) {
            $children = $visible->where('parent_id', $section->id)->values();
            $section->setRelation('children', $children);
        }

        return $visible->whereNull('parent_id')->values();
    }

    /**
     * هل العنصر (محاضرة/واجب/…) ظاهر لهذا الطالب ضمن كورس المنحة؟
     */
    public function contentVisibleToStudent(AdvancedCourse $course, string $itemType, int $itemId, User $user): bool
    {
        if (! $this->isScholarshipCourse($course)) {
            return true;
        }

        $curriculumItem = CurriculumItem::query()
            ->where('item_type', $itemType)
            ->where('item_id', $itemId)
            ->whereHas('section', fn ($q) => $q->where('advanced_course_id', $course->id))
            ->with(['section.visibleStudents', 'visibleStudents'])
            ->first();

        // محتوى غير مربوط بالمنهج: لا نفرض قيود الظهور
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
     */
    public function syncSectionVisibility(CourseSection $section, AdvancedCourse $course, string $scope, ?array $studentIds): void
    {
        if (! $this->isScholarshipCourse($course)) {
            $section->visibility_scope = self::SCOPE_ALL;
            $section->save();
            $section->visibleStudents()->detach();

            return;
        }

        $scope = $scope === self::SCOPE_SELECTED ? self::SCOPE_SELECTED : self::SCOPE_ALL;
        $section->visibility_scope = $scope;
        $section->save();

        if ($scope !== self::SCOPE_SELECTED) {
            $section->visibleStudents()->detach();

            return;
        }

        $allowed = $this->selectableStudents($course)->pluck('id')->all();
        $ids = collect($studentIds ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0 && in_array($id, $allowed, true))
            ->unique()
            ->values()
            ->all();

        $section->visibleStudents()->sync($ids);
    }

    /**
     * @param  array<int>|null  $studentIds
     */
    public function syncItemVisibility(CurriculumItem $item, AdvancedCourse $course, string $scope, ?array $studentIds): void
    {
        if (! $this->isScholarshipCourse($course)) {
            $item->visibility_scope = self::SCOPE_ALL;
            $item->save();
            $item->visibleStudents()->detach();

            return;
        }

        $scope = $scope === self::SCOPE_SELECTED ? self::SCOPE_SELECTED : self::SCOPE_ALL;
        $item->visibility_scope = $scope;
        $item->save();

        if ($scope !== self::SCOPE_SELECTED) {
            $item->visibleStudents()->detach();

            return;
        }

        $allowed = $this->selectableStudents($course)->pluck('id')->all();
        $ids = collect($studentIds ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0 && in_array($id, $allowed, true))
            ->unique()
            ->values()
            ->all();

        $item->visibleStudents()->sync($ids);
    }
}
