<?php

namespace App\Services;

use App\Models\EmployeeTask;
use App\Models\EmployeeTaskDeliverable;
use App\Support\MontageVideoHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EmployeeTaskDeliverableService
{
    public function __construct(
        protected EmployeeDeliverableStorageService $deliverableStorage
    ) {}

    /**
     * تحديث تسليم مهمة (موظف أو إدمن بعد التحقق من الصلاحية).
     */
    public function updateDeliverable(Request $request, EmployeeTask $task, EmployeeTaskDeliverable $deliverable): void
    {
        $this->assertDeliverableBelongsToTask($task, $deliverable);

        $isVideoEditing = $task->isVideoEditing()
            || $request->input('task_type_context') === 'video_editing';

        if ($isVideoEditing) {
            $validated = $request->validate([
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'video_link_url' => [
                    'required',
                    'url',
                    function ($attribute, $value, $fail) {
                        $host = parse_url($value, PHP_URL_HOST);
                        $hostLower = $host ? strtolower($host) : '';
                        $allowed = str_contains($hostLower, 'bunny')
                            || str_contains($hostLower, 'b-cdn')
                            || str_contains($hostLower, 'mediadelivery');
                        if (! $host || ! $allowed) {
                            $fail('رابط الفيديو يجب أن يكون من Bunny (bunny.net أو b-cdn.net أو mediadelivery.net) فقط.');
                        }
                    },
                ],
                'received_from' => 'required|string|max:255',
                'duration_before' => 'nullable|string|max:100',
                'duration_after' => 'nullable|string|max:100',
                'duration_before_minutes' => 'nullable|integer|min:0|max:999999',
                'duration_after_minutes' => 'nullable|integer|min:0|max:999999',
            ]);

            $newHash = MontageVideoHelper::linkUrlHash($validated['video_link_url']);
            if ($newHash !== $deliverable->link_url_hash) {
                $dup = EmployeeTaskDeliverable::query()
                    ->where('task_id', $task->id)
                    ->where('link_url_hash', $newHash)
                    ->where('id', '!=', $deliverable->id)
                    ->exists();
                if ($dup) {
                    throw ValidationException::withMessages([
                        'video_link_url' => ['هذا الرابط مُسجّل مسبقاً في تسليمات هذه المهمة.'],
                    ]);
                }
            }

            $beforeMin = $validated['duration_before_minutes'] ?? null;
            $afterMin = $validated['duration_after_minutes'] ?? null;
            if ($beforeMin === null) {
                $beforeMin = MontageVideoHelper::parseDurationToMinutes($validated['duration_before'] ?? null);
            }
            if ($afterMin === null) {
                $afterMin = MontageVideoHelper::parseDurationToMinutes($validated['duration_after'] ?? null);
            }

            $textBefore = $beforeMin !== null
                ? MontageVideoHelper::minutesToDisplay($beforeMin)
                : ($validated['duration_before'] ?? null);
            $textAfter = $afterMin !== null
                ? MontageVideoHelper::minutesToDisplay($afterMin)
                : ($validated['duration_after'] ?? null);

            $deliverable->update([
                'title' => $validated['title'] ?? $deliverable->title,
                'description' => $validated['description'] ?? null,
                'delivery_type' => 'link',
                'link_url' => $validated['video_link_url'],
                'received_from' => $validated['received_from'] ?? null,
                'duration_before' => $textBefore,
                'duration_after' => $textAfter,
                'duration_before_minutes' => $beforeMin,
                'duration_after_minutes' => $afterMin,
                'status' => 'submitted',
                'submitted_at' => $deliverable->submitted_at ?? now(),
            ]);

            return;
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'delivery_type' => 'required|in:file,image,link',
            'file' => 'nullable|file|max:10240',
            'link_url' => 'nullable|url|required_if:delivery_type,link',
        ]);

        if (in_array($validated['delivery_type'], ['file', 'image'], true) && ! $request->hasFile('file')) {
            $needsFile = ! $deliverable->file_path
                || $deliverable->delivery_type === 'link'
                || ($deliverable->delivery_type !== $validated['delivery_type']);
            if ($needsFile) {
                throw ValidationException::withMessages([
                    'file' => ['يرجى رفع ملف أو صورة لهذا النوع.'],
                ]);
            }
        }

        $filePath = $deliverable->file_path;
        $fileDisk = $deliverable->file_disk;
        $fileName = $deliverable->file_name;
        $fileType = $deliverable->file_type;
        $fileSize = $deliverable->file_size;
        $linkUrl = $deliverable->link_url;

        if (in_array($validated['delivery_type'], ['file', 'image'], true) && $request->hasFile('file')) {
            $this->deliverableStorage->deleteIfExists($deliverable->file_path, $deliverable->file_disk);
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $fileType = $file->getClientMimeType();
            $fileSize = $file->getSize();
            $stored = $this->deliverableStorage->storeUploadedFile($file, $validated['delivery_type']);
            $filePath = $stored['path'];
            $fileDisk = $stored['disk'];
            $linkUrl = null;
        } elseif ($validated['delivery_type'] === 'link') {
            $this->deliverableStorage->deleteIfExists($deliverable->file_path, $deliverable->file_disk);
            $filePath = null;
            $fileDisk = null;
            $fileName = null;
            $fileType = null;
            $fileSize = null;
            $linkUrl = $validated['link_url'];
        }

        $deliverable->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'delivery_type' => $validated['delivery_type'],
            'link_url' => $linkUrl,
            'file_path' => $filePath,
            'file_disk' => $fileDisk,
            'file_name' => $fileName,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'status' => 'submitted',
        ]);
    }

    /**
     * حذف تسليم وملفه المرفوع إن وُجد.
     */
    public function destroyDeliverable(EmployeeTask $task, EmployeeTaskDeliverable $deliverable): void
    {
        $this->assertDeliverableBelongsToTask($task, $deliverable);

        $this->deliverableStorage->deleteIfExists($deliverable->file_path, $deliverable->file_disk);

        $deliverable->delete();
    }

    private function assertDeliverableBelongsToTask(EmployeeTask $task, EmployeeTaskDeliverable $deliverable): void
    {
        if ((int) $deliverable->task_id !== (int) $task->id) {
            abort(404);
        }
    }
}
