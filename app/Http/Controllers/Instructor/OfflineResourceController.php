<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\OfflineCourse;
use App\Models\OfflineCourseResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OfflineResourceController extends Controller
{
    /**
     * قائمة موارد الكورس الأوفلاين
     */
    public function index(OfflineCourse $offlineCourse)
    {
        $this->authorizeInstructor($offlineCourse);
        $channel = request()->query('channel') === 'online' ? 'online' : 'offline';

        $resources = $offlineCourse->resources()
            ->with('group')
            ->when($channel === 'online', function ($q) {
                $q->whereHas('group', fn ($g) => $g->where('online_booking_enabled', true));
            })
            ->ordered()
            ->get();

        $groups = $this->groupsForChannel($offlineCourse, $channel);

        return view('instructor.offline-courses.resources.index', compact('offlineCourse', 'resources', 'groups', 'channel'));
    }

    /**
     * نموذج إضافة مورد
     */
    public function create(OfflineCourse $offlineCourse)
    {
        $this->authorizeInstructor($offlineCourse);
        $channel = request()->query('channel') === 'online' ? 'online' : 'offline';

        $groups = $this->groupsForChannel($offlineCourse, $channel);

        return view('instructor.offline-courses.resources.create', compact('offlineCourse', 'groups', 'channel'));
    }

    /**
     * حفظ مورد جديد
     */
    public function store(Request $request, OfflineCourse $offlineCourse)
    {
        $this->authorizeInstructor($offlineCourse);
        $channel = $request->query('channel') === 'online' ? 'online' : 'offline';

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:file,link',
            'url' => 'nullable|required_if:type,link|url',
            'file' => 'nullable|file|max:51200',
            'files' => 'nullable|array',
            'files.*' => 'file|max:51200',
            'group_id' => 'nullable|exists:offline_course_groups,id',
        ], [
            'title.required' => 'عنوان المورد مطلوب',
            'type.required' => 'نوع المورد مطلوب',
        ]);

        if ($validated['type'] === 'file' && !$request->hasFile('file') && !$request->hasFile('files')) {
            return back()->withInput()->withErrors(['file' => 'يجب رفع ملف واحد على الأقل أو اختيار عدة ملفات.']);
        }

        $validated['instructor_id'] = Auth::id();
        $validated['offline_course_id'] = $offlineCourse->id;
        $validated['group_id'] = $validated['group_id'] ?? null;
        if ($validated['group_id']) {
            $allowedGroupExists = $this->groupsForChannel($offlineCourse, $channel)
                ->where('id', (int) $validated['group_id'])
                ->exists();
            if (! $allowedGroupExists) {
                return back()->withErrors(['group_id' => 'المجموعة المختارة غير متاحة لهذا النوع من الكورسات.'])->withInput();
            }
        }
        $validated['order'] = $offlineCourse->resources()->max('order') + 1;
        $validated['is_active'] = true;

        $attachments = [];
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('offline-resources/' . $offlineCourse->id, 'public');
            $attachments[] = ['path' => $path, 'name' => $file->getClientOriginalName()];
        }
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('offline-resources/' . $offlineCourse->id, 'public');
                $attachments[] = ['path' => $path, 'name' => $file->getClientOriginalName()];
            }
        }
        if (!empty($attachments)) {
            $validated['file_path'] = $attachments[0]['path'];
            $validated['file_name'] = $attachments[0]['name'];
            $validated['attachments'] = $attachments;
        }

        OfflineCourseResource::create($validated);

        return redirect()
            ->route('instructor.offline-courses.resources.index', ['offlineCourse' => $offlineCourse, 'channel' => $channel])
            ->with('success', 'تم إضافة المورد بنجاح');
    }

    /**
     * نموذج تعديل مورد
     */
    public function edit(OfflineCourse $offlineCourse, OfflineCourseResource $resource)
    {
        $this->authorizeInstructor($offlineCourse);
        $channel = request()->query('channel') === 'online' ? 'online' : 'offline';
        if ($resource->offline_course_id !== $offlineCourse->id) {
            abort(404);
        }

        $groups = $this->groupsForChannel($offlineCourse, $channel);

        return view('instructor.offline-courses.resources.edit', compact('offlineCourse', 'resource', 'groups', 'channel'));
    }

    /**
     * تحديث مورد
     */
    public function update(Request $request, OfflineCourse $offlineCourse, OfflineCourseResource $resource)
    {
        $this->authorizeInstructor($offlineCourse);
        $channel = $request->query('channel') === 'online' ? 'online' : 'offline';
        if ($resource->offline_course_id !== $offlineCourse->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:file,link',
            'url' => 'nullable|required_if:type,link|url',
            'file' => 'nullable|file|max:51200',
            'files' => 'nullable|array',
            'files.*' => 'file|max:51200',
            'group_id' => 'nullable|exists:offline_course_groups,id',
            'is_active' => 'boolean',
        ]);

        $resource->title = $validated['title'];
        $resource->description = $validated['description'] ?? null;
        $resource->type = $validated['type'];
        $resource->group_id = $validated['group_id'] ?? null;
        if ($resource->group_id) {
            $allowedGroupExists = $this->groupsForChannel($offlineCourse, $channel)
                ->where('id', (int) $resource->group_id)
                ->exists();
            if (! $allowedGroupExists) {
                return back()->withErrors(['group_id' => 'المجموعة المختارة غير متاحة لهذا النوع من الكورسات.'])->withInput();
            }
        }
        $resource->is_active = $request->boolean('is_active');

        if ($validated['type'] === 'link') {
            $resource->url = $validated['url'];
            $resource->file_path = null;
            $resource->file_name = null;
            $resource->attachments = null;
        } else {
            $resource->url = null;
            $currentAttachments = $resource->getAllFiles();
            $newAttachments = [];
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $path = $file->store('offline-resources/' . $offlineCourse->id, 'public');
                $newAttachments[] = ['path' => $path, 'name' => $file->getClientOriginalName()];
            }
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $file->store('offline-resources/' . $offlineCourse->id, 'public');
                    $newAttachments[] = ['path' => $path, 'name' => $file->getClientOriginalName()];
                }
            }
            if (!empty($newAttachments)) {
                $merged = array_merge($currentAttachments, $newAttachments);
                $resource->attachments = $merged;
                $resource->file_path = $merged[0]['path'];
                $resource->file_name = $merged[0]['name'];
            }
        }

        $resource->save();

        return redirect()
            ->route('instructor.offline-courses.resources.index', ['offlineCourse' => $offlineCourse, 'channel' => $channel])
            ->with('success', 'تم تحديث المورد بنجاح');
    }

    /**
     * حذف مورد
     */
    public function destroy(OfflineCourse $offlineCourse, OfflineCourseResource $resource)
    {
        $this->authorizeInstructor($offlineCourse);
        $channel = request()->query('channel') === 'online' ? 'online' : 'offline';
        if ($resource->offline_course_id !== $offlineCourse->id) {
            abort(404);
        }

        foreach ($resource->getAllFiles() as $file) {
            if (!empty($file['path'])) {
                Storage::disk('public')->delete($file['path']);
            }
        }
        $resource->delete();

        return redirect()
            ->route('instructor.offline-courses.resources.index', ['offlineCourse' => $offlineCourse, 'channel' => $channel])
            ->with('success', 'تم حذف المورد');
    }

    private function groupsForChannel(OfflineCourse $offlineCourse, string $channel)
    {
        $q = $offlineCourse->groups()->orderBy('name');
        if ($channel === 'online') {
            $q->where(function ($g) {
                $g->where('online_booking_enabled', true)
                    ->orWhere('current_students_online', '>', 0);
            });
        }

        return $q;
    }

    private function authorizeInstructor(OfflineCourse $offlineCourse): void
    {
        if ($offlineCourse->instructor_id !== Auth::id()) {
            abort(403, 'غير مسموح لك بإدارة هذا الكورس الأوفلاين');
        }
    }
}
