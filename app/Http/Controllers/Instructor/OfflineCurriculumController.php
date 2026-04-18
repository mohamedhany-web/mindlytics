<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\AdvancedExam;
use App\Models\OfflineActivity;
use App\Models\OfflineCourse;
use App\Models\OfflineCourseResource;
use App\Models\OfflineCourseSection;
use App\Models\OfflineCurriculumItem;
use App\Models\OfflineCurriculumNote;
use App\Models\OfflineGroupSession;
use App\Models\OfflineLecture;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfflineCurriculumController extends Controller
{
    /** @var array<int, class-string> */
    private const ITEM_TYPES = [
        OfflineLecture::class,
        OfflineCourseResource::class,
        OfflineActivity::class,
        AdvancedExam::class,
        OfflineCurriculumNote::class,
    ];

    /** أنواع يمكن ربطها من لوحة «إضافة للمنهج» (الملاحظات تُنشأ من داخل القسم) */
    private const ATTACH_ITEM_TYPES = [
        OfflineLecture::class,
        OfflineCourseResource::class,
        OfflineActivity::class,
        AdvancedExam::class,
    ];

    public function index(OfflineCourse $offlineCourse)
    {
        $this->authorizeOfflineCourse($offlineCourse);

        $curriculumChannel = request()->query('channel') === 'online' ? 'online' : 'offline';

        $groupSessions = OfflineGroupSession::query()
            ->forOfflineCourse($offlineCourse, $curriculumChannel)
            ->with('group')
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->get();

        $allSections = $offlineCourse->offlineCourseSections()
            ->with(['items' => function ($q) {
                $q->orderBy('order')->with(['item' => function (MorphTo $morph) {
                    $morph->morphWith([
                        OfflineLecture::class => ['groupSession.group'],
                    ]);
                }]);
            }])
            ->orderBy('order')
            ->get();

        foreach ($allSections as $section) {
            $section->setRelation('children', $allSections->where('parent_id', $section->id)->values());
        }
        $sections = $allSections->whereNull('parent_id')->values();

        $sectionsFlat = $this->flattenSectionsForSelect($sections);

        $sectionIds = $allSections->pluck('id');

        $usedLectureIds = OfflineCurriculumItem::query()
            ->whereIn('offline_course_section_id', $sectionIds)
            ->where('item_type', OfflineLecture::class)
            ->pluck('item_id');

        $usedResourceIds = OfflineCurriculumItem::query()
            ->whereIn('offline_course_section_id', $sectionIds)
            ->where('item_type', OfflineCourseResource::class)
            ->pluck('item_id');

        $usedActivityIds = OfflineCurriculumItem::query()
            ->whereIn('offline_course_section_id', $sectionIds)
            ->where('item_type', OfflineActivity::class)
            ->pluck('item_id');

        $usedExamIds = OfflineCurriculumItem::query()
            ->whereIn('offline_course_section_id', $sectionIds)
            ->where('item_type', AdvancedExam::class)
            ->pluck('item_id');

        $lectures = $offlineCourse->offlineLectures()
            ->when($usedLectureIds->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $usedLectureIds))
            ->orderBy('order')
            ->get();

        $resources = $offlineCourse->resources()
            ->when($usedResourceIds->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $usedResourceIds))
            ->orderBy('order')
            ->get();

        $activities = $offlineCourse->activities()
            ->when($usedActivityIds->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $usedActivityIds))
            ->orderBy('created_at', 'desc')
            ->get();

        $exams = AdvancedExam::query()
            ->where('offline_course_id', $offlineCourse->id)
            ->when($usedExamIds->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $usedExamIds))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('instructor.offline-curriculum.index', compact(
            'offlineCourse',
            'sections',
            'sectionsFlat',
            'lectures',
            'resources',
            'activities',
            'exams',
            'groupSessions',
            'curriculumChannel'
        ));
    }

    public function storeSection(Request $request, OfflineCourse $offlineCourse)
    {
        $this->authorizeOfflineCourse($offlineCourse);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:offline_course_sections,id',
        ]);

        $parentId = $validated['parent_id'] ?? null;
        if ($parentId) {
            OfflineCourseSection::where('id', $parentId)
                ->where('offline_course_id', $offlineCourse->id)
                ->firstOrFail();
            $lastOrder = OfflineCourseSection::where('parent_id', $parentId)->max('order') ?? 0;
        } else {
            $lastOrder = $offlineCourse->offlineCourseSections()->whereNull('parent_id')->max('order') ?? 0;
        }

        OfflineCourseSection::create([
            'offline_course_id' => $offlineCourse->id,
            'parent_id' => $parentId,
            'title' => $validated['title'],
            'description' => $parentId ? null : ($validated['description'] ?? null),
            'order' => $lastOrder + 1,
            'is_active' => true,
        ]);

        return back()->with('success', 'تم إنشاء القسم بنجاح.');
    }

    public function updateSection(Request $request, OfflineCourse $offlineCourse, OfflineCourseSection $section)
    {
        $this->authorizeOfflineCourse($offlineCourse);
        $this->assertSectionCourse($offlineCourse, $section);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $payload = ['title' => $validated['title']];
        if (! $section->parent_id) {
            $payload['description'] = $validated['description'] ?? null;
        }
        $section->update($payload);

        return back()->with('success', 'تم تحديث القسم.');
    }

    public function destroySection(OfflineCourse $offlineCourse, OfflineCourseSection $section)
    {
        $this->authorizeOfflineCourse($offlineCourse);
        $this->assertSectionCourse($offlineCourse, $section);
        $section->delete();

        return back()->with('success', 'تم حذف القسم وما يتبعه.');
    }

    public function attachItem(Request $request, OfflineCourse $offlineCourse)
    {
        $this->authorizeOfflineCourse($offlineCourse);

        $validated = $request->validate([
            'offline_course_section_id' => 'required|exists:offline_course_sections,id',
            'item_type' => 'required|string|in:'.implode(',', self::ATTACH_ITEM_TYPES),
            'item_id' => 'required|integer|min:1',
        ]);

        $section = OfflineCourseSection::query()
            ->where('offline_course_id', $offlineCourse->id)
            ->where('id', $validated['offline_course_section_id'])
            ->firstOrFail();

        $type = $validated['item_type'];
        $model = $type::findOrFail($validated['item_id']);
        $this->assertItemBelongsToCourse($offlineCourse, $model);

        $exists = OfflineCurriculumItem::query()
            ->where('offline_course_section_id', $section->id)
            ->where('item_type', $type)
            ->where('item_id', $model->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'هذا العنصر مضاف مسبقاً في نفس القسم.');
        }

        $lastOrder = $section->items()->max('order') ?? 0;
        OfflineCurriculumItem::create([
            'offline_course_section_id' => $section->id,
            'item_type' => $type,
            'item_id' => $model->id,
            'order' => $lastOrder + 1,
            'is_active' => true,
        ]);

        return back()->with('success', 'تمت إضافة العنصر للمنهج.');
    }

    public function storeNote(Request $request, OfflineCourse $offlineCourse, OfflineCourseSection $section)
    {
        $this->authorizeOfflineCourse($offlineCourse);
        $this->assertSectionCourse($offlineCourse, $section);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'nullable|string',
        ]);

        $note = OfflineCurriculumNote::create([
            'offline_course_id' => $offlineCourse->id,
            'title' => $validated['title'],
            'body' => $validated['body'] ?? null,
        ]);

        $lastOrder = $section->items()->max('order') ?? 0;
        OfflineCurriculumItem::create([
            'offline_course_section_id' => $section->id,
            'item_type' => OfflineCurriculumNote::class,
            'item_id' => $note->id,
            'order' => $lastOrder + 1,
            'is_active' => true,
        ]);

        return back()->with('success', 'تمت إضافة الملاحظة التوضيحية للمنهج.');
    }

    public function destroyItem(OfflineCourse $offlineCourse, OfflineCurriculumItem $item)
    {
        $this->authorizeOfflineCourse($offlineCourse);
        $item->load('section');
        if (! $item->section || (int) $item->section->offline_course_id !== (int) $offlineCourse->id) {
            abort(404);
        }

        if ($item->item_type === OfflineCurriculumNote::class && $item->item) {
            $item->item->delete();
        } else {
            $item->delete();
        }

        return back()->with('success', 'تمت إزالة العنصر من المنهج.');
    }

    public function moveSection(Request $request, OfflineCourse $offlineCourse, OfflineCourseSection $section)
    {
        $this->authorizeOfflineCourse($offlineCourse);
        $this->assertSectionCourse($offlineCourse, $section);

        $direction = $request->input('direction') === 'down' ? 'down' : 'up';
        $siblings = $offlineCourse->offlineCourseSections()
            ->where('parent_id', $section->parent_id)
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $idx = $siblings->search(fn ($s) => (int) $s->id === (int) $section->id);
        if ($idx === false) {
            abort(404);
        }
        $swapIdx = $direction === 'up' ? $idx - 1 : $idx + 1;
        if ($swapIdx < 0 || $swapIdx >= $siblings->count()) {
            return back();
        }

        $a = $siblings[$idx];
        $b = $siblings[$swapIdx];
        $tmp = $a->order;
        $a->update(['order' => $b->order]);
        $b->update(['order' => $tmp]);

        return back();
    }

    public function moveItem(Request $request, OfflineCourse $offlineCourse, OfflineCurriculumItem $item)
    {
        $this->authorizeOfflineCourse($offlineCourse);
        $item->load('section');
        if (! $item->section || (int) $item->section->offline_course_id !== (int) $offlineCourse->id) {
            abort(404);
        }
        $section = $item->section;

        $direction = $request->input('direction') === 'down' ? 'down' : 'up';
        $siblings = $section->items()->orderBy('order')->orderBy('id')->get();
        $idx = $siblings->search(fn ($i) => (int) $i->id === (int) $item->id);
        if ($idx === false) {
            abort(404);
        }
        $swapIdx = $direction === 'up' ? $idx - 1 : $idx + 1;
        if ($swapIdx < 0 || $swapIdx >= $siblings->count()) {
            return back();
        }

        $a = $siblings[$idx];
        $b = $siblings[$swapIdx];
        $tmp = $a->order;
        $a->update(['order' => $b->order]);
        $b->update(['order' => $tmp]);

        return back();
    }

    private function authorizeOfflineCourse(OfflineCourse $offlineCourse): void
    {
        if ((int) $offlineCourse->instructor_id !== (int) Auth::id()) {
            abort(403, 'غير مسموح لك بإدارة هذا الكورس.');
        }
    }

    private function assertSectionCourse(OfflineCourse $offlineCourse, OfflineCourseSection $section): void
    {
        if ((int) $section->offline_course_id !== (int) $offlineCourse->id) {
            abort(404);
        }
    }

    private function assertItemBelongsToCourse(OfflineCourse $offlineCourse, object $model): void
    {
        if ($model instanceof OfflineLecture || $model instanceof OfflineCourseResource || $model instanceof OfflineActivity) {
            if ((int) $model->offline_course_id !== (int) $offlineCourse->id) {
                abort(403, 'العنصر لا يتبع هذا الكورس.');
            }
        } elseif ($model instanceof AdvancedExam) {
            if ((int) ($model->offline_course_id ?? 0) !== (int) $offlineCourse->id) {
                abort(403, 'الامتحان لا يتبع هذا الكورس الأوفلاين.');
            }
        } elseif ($model instanceof OfflineCurriculumNote) {
            if ((int) $model->offline_course_id !== (int) $offlineCourse->id) {
                abort(403, 'الملاحظة لا تتبع هذا الكورس.');
            }
        } else {
            abort(400, 'نوع عنصر غير مدعوم.');
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<int, OfflineCourseSection>  $sections
     * @return list<array{id: int, label: string}>
     */
    private function flattenSectionsForSelect($sections, int $depth = 0): array
    {
        $out = [];
        foreach ($sections as $section) {
            $prefix = $depth > 0 ? str_repeat('— ', $depth) : '';
            $out[] = ['id' => $section->id, 'label' => $prefix.$section->title];
            if ($section->children && $section->children->isNotEmpty()) {
                $out = array_merge($out, $this->flattenSectionsForSelect($section->children, $depth + 1));
            }
        }

        return $out;
    }
}
