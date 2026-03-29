<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeTask;
use App\Models\EmployeeTaskDeliverable;
use App\Services\EmployeeTaskDeliverableService;
use App\Support\MontageVideoHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EmployeeTaskController extends Controller
{
    public function __construct(
        protected EmployeeTaskDeliverableService $deliverableService
    ) {
    }

    /**
     * عرض قائمة مهام الموظف
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isEmployee()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        $query = $user->employeeTasks()->with(['assigner', 'deliverables']);

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب الأولوية
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('task_type')) {
            $query->where('task_type', $request->task_type);
        }

        $tasks = $query->latest()->paginate(15);

        $stats = [
            'total' => $user->employeeTasks()->count(),
            'pending' => $user->employeeTasks()->where('status', 'pending')->count(),
            'in_progress' => $user->employeeTasks()->where('status', 'in_progress')->count(),
            'completed' => $user->employeeTasks()->where('status', 'completed')->count(),
            'overdue' => $user->employeeTasks()
                ->where('deadline', '<', now())
                ->whereIn('status', ['pending', 'in_progress'])
                ->count(),
        ];

        return view('employee.tasks.index', compact('tasks', 'stats'));
    }

    /**
     * عرض تفاصيل مهمة
     */
    public function show(EmployeeTask $task)
    {
        $user = Auth::user();
        
        if (!$user->isEmployee() || $task->employee_id !== $user->id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        $task->load(['assigner', 'deliverables' => function ($q) {
            $q->with('reviewer')->orderByDesc('created_at');
        }]);
        
        return view('employee.tasks.show', compact('task'));
    }

    /**
     * تحديث حالة المهمة
     */
    public function updateStatus(Request $request, EmployeeTask $task)
    {
        $user = Auth::user();
        
        if (!$user->isEmployee() || $task->employee_id !== $user->id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,on_hold',
            'progress' => 'nullable|integer|min:0|max:100',
        ]);

        // تحديث التواريخ بناءً على الحالة
        if ($validated['status'] === 'in_progress' && !$task->started_at) {
            $validated['started_at'] = now();
        }

        if ($validated['status'] === 'completed') {
            $validated['completed_at'] = now();
            $validated['progress'] = 100;
        }

        $task->update($validated);

        return back()->with('success', 'تم تحديث حالة المهمة بنجاح');
    }

    /**
     * تسليم مهمة
     */
    public function submitDeliverable(Request $request, EmployeeTask $task)
    {
        $user = Auth::user();

        if (!$user->isEmployee() || $task->employee_id !== $user->id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

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
                        if (!$host || !$allowed) {
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

            $hash = MontageVideoHelper::linkUrlHash($validated['video_link_url']);
            if ($hash && EmployeeTaskDeliverable::query()
                ->where('task_id', $task->id)
                ->where('link_url_hash', $hash)
                ->exists()) {
                throw ValidationException::withMessages([
                    'video_link_url' => ['هذا الرابط مُسجّل مسبقاً في تسليمات هذه المهمة.'],
                ]);
            }
        } else {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'delivery_type' => 'required|in:file,image,link',
                'file' => 'nullable|file|max:10240|required_if:delivery_type,file,image',
                'link_url' => 'nullable|url|required_if:delivery_type,link',
            ]);
        }

        $filePath = null;
        $fileName = null;
        $fileType = null;
        $fileSize = null;
        $linkUrl = null;
        $deliveryType = 'file';
        $receivedFrom = null;
        $durationBefore = null;
        $durationAfter = null;
        $beforeMin = null;
        $afterMin = null;

        if ($isVideoEditing) {
            $linkUrl = $validated['video_link_url'];
            $deliveryType = 'link';
            $receivedFrom = $validated['received_from'] ?? null;
            $durationBefore = $validated['duration_before'] ?? null;
            $durationAfter = $validated['duration_after'] ?? null;
            $beforeMin = $validated['duration_before_minutes'] ?? null;
            $afterMin = $validated['duration_after_minutes'] ?? null;
            if ($beforeMin === null) {
                $beforeMin = MontageVideoHelper::parseDurationToMinutes($durationBefore);
            }
            if ($afterMin === null) {
                $afterMin = MontageVideoHelper::parseDurationToMinutes($durationAfter);
            }
            if ($beforeMin !== null) {
                $durationBefore = MontageVideoHelper::minutesToDisplay($beforeMin);
            }
            if ($afterMin !== null) {
                $durationAfter = MontageVideoHelper::minutesToDisplay($afterMin);
            }
        } else {
            if (in_array($validated['delivery_type'], ['file', 'image']) && $request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = $file->getClientOriginalName();
                $fileType = $file->getClientMimeType();
                $fileSize = $file->getSize();
                $folder = $validated['delivery_type'] === 'image' ? 'employee-deliverables/images' : 'employee-deliverables/files';
                $filePath = $file->store($folder, 'public');
            }
            $deliveryType = $validated['delivery_type'];
            if ($deliveryType === 'link') {
                $linkUrl = $validated['link_url'];
            }
        }

        $createPayload = [
            'task_id' => $task->id,
            'title' => $validated['title'] ?? ('تسليم مونتاج ' . now()->format('Y-m-d H:i')),
            'description' => $validated['description'] ?? null,
            'delivery_type' => $deliveryType,
            'link_url' => $linkUrl ?? ($isVideoEditing ? $validated['video_link_url'] : null),
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'received_from' => $receivedFrom,
            'duration_before' => $durationBefore,
            'duration_after' => $durationAfter,
            'status' => 'submitted',
            'submitted_at' => now(),
        ];
        if ($isVideoEditing) {
            $createPayload['duration_before_minutes'] = $beforeMin ?? null;
            $createPayload['duration_after_minutes'] = $afterMin ?? null;
        }
        EmployeeTaskDeliverable::create($createPayload);

        if ($task->status !== 'completed') {
            $task->update(['status' => 'in_progress']);
        }

        $message = $isVideoEditing
            ? 'تم تسليم المونتاج بنجاح'
            : ($task->isSales() ? 'تم تسليم مهمة المبيعات بنجاح' : 'تم تسليم المهمة بنجاح');
        return redirect()->to(route('employee.tasks.show', $task) . '?open=1')
            ->with('success', $message);
    }

    /**
     * تعديل تسليم (موظف صاحب المهمة فقط)
     */
    public function updateDeliverable(Request $request, EmployeeTask $task, EmployeeTaskDeliverable $deliverable)
    {
        $this->assertEmployeeOwnsDeliverable($task, $deliverable);

        $this->deliverableService->updateDeliverable($request, $task, $deliverable);

        return redirect()->to(route('employee.tasks.show', $task) . '?open=1')
            ->with('success', 'تم تحديث التسليم بنجاح');
    }

    /**
     * حذف تسليم
     */
    public function destroyDeliverable(EmployeeTask $task, EmployeeTaskDeliverable $deliverable)
    {
        $this->assertEmployeeOwnsDeliverable($task, $deliverable);

        $this->deliverableService->destroyDeliverable($task, $deliverable);

        return redirect()->to(route('employee.tasks.show', $task) . '?open=1')
            ->with('success', 'تم حذف التسليم');
    }

    /**
     * تحميل قالب Excel لتسليمات المونتاج
     */
    public function downloadMontageExcelTemplate(EmployeeTask $task)
    {
        $user = Auth::user();
        if (! $user->isEmployee() || $task->employee_id !== $user->id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }
        if (! $task->isVideoEditing()) {
            abort(404);
        }

        return $this->streamMontageExcelTemplateResponse();
    }

    /**
     * تصدير تسليمات المونتاج الحالية إلى Excel (للموظف فقط — بدون استيراد جماعي).
     */
    public function exportMontageDeliverables(EmployeeTask $task)
    {
        $user = Auth::user();
        if (! $user->isEmployee() || $task->employee_id !== $user->id) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }
        if (! $task->isVideoEditing()) {
            abort(404);
        }

        $deliverables = $task->deliverables()
            ->where('delivery_type', 'link')
            ->whereNotNull('link_url')
            ->orderBy('id')
            ->get();

        $filename = 'montage-deliverables-task-' . $task->id . '-' . now()->format('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($deliverables) {
            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();
            $rows = [
                ['رابط_الفيديو', 'عنوان', 'ممن_استلمته', 'دقائق_قبل', 'دقائق_بعد', 'مدة_قبل', 'مدة_بعد', 'ملاحظات'],
            ];
            foreach ($deliverables as $d) {
                $rows[] = [
                    $d->link_url,
                    $d->title,
                    $d->received_from,
                    $d->duration_before_minutes,
                    $d->duration_after_minutes,
                    $d->duration_before,
                    $d->duration_after,
                    $d->description,
                ];
            }
            $sheet->fromArray($rows);
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function streamMontageExcelTemplateResponse()
    {
        $filename = 'montage-deliverables-template.xlsx';

        return response()->streamDownload(function () {
            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->fromArray([
                ['رابط_الفيديو', 'عنوان', 'ممن_استلمته', 'دقائق_قبل', 'دقائق_بعد', 'مدة_قبل', 'مدة_بعد', 'ملاحظات'],
                ['https://iframe.mediadelivery.net/...', 'مثال', 'المصدر', 12, 11, '12:00', '10:30', '10:30 = دقيقة:ثانية؛ 1:05:00 = ساعة:دقيقة:ثانية'],
            ]);
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function assertEmployeeOwnsDeliverable(EmployeeTask $task, EmployeeTaskDeliverable $deliverable): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isEmployee() || (int) $task->employee_id !== (int) $user->id || (int) $deliverable->task_id !== (int) $task->id) {
            abort(403, 'غير مصرح لك بتعديل أو حذف هذا التسليم');
        }
    }
}
