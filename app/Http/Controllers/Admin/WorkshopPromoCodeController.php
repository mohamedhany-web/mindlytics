<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\OfflineCourse;
use App\Models\User;
use App\Models\Workshop;
use App\Models\SalesLeadGroup;
use App\Models\WorkshopPromoActivation;
use App\Models\WorkshopPromoCode;
use App\Services\WorkshopPromoSalesService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WorkshopPromoCodeController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total' => 0,
            'active' => 0,
            'activations' => 0,
            'used' => 0,
        ];
        $recentActivations = collect();
        $promoCodes = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);

        if (Schema::hasTable('workshop_promo_codes')) {
            $query = WorkshopPromoCode::with(['workshop', 'creator'])
                ->withCount('activations')
                ->orderByDesc('created_at');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                if ($request->status === 'active') {
                    $query->active();
                } elseif ($request->status === 'expired') {
                    $query->where(function ($q) {
                        $q->where('is_active', false)
                            ->orWhere('expires_at', '<', now());
                    });
                }
            }

            if ($request->filled('workshop_id')) {
                $query->where('workshop_id', $request->workshop_id);
            }

            $promoCodes = $query->paginate(15)->withQueryString();
            $stats['total'] = WorkshopPromoCode::count();
            $stats['active'] = WorkshopPromoCode::active()->count();
        }

        if (Schema::hasTable('workshop_promo_activations')) {
            $stats['activations'] = WorkshopPromoActivation::count();
            $stats['used'] = WorkshopPromoActivation::where('status', 'used')->count();
            $recentActivations = WorkshopPromoActivation::with(['user', 'promoCode.workshop', 'salesLead.assignee'])
                ->latest('activated_at')
                ->limit(25)
                ->get();
            app(WorkshopPromoSalesService::class)->attachLeads($recentActivations);
        }

        $workshops = Workshop::orderByDesc('starts_at')->limit(50)->get(['id', 'title', 'slug']);
        $salesForm = $this->salesFormData();

        return view('admin.workshop-promo-codes.index', array_merge(
            compact('promoCodes', 'stats', 'workshops', 'recentActivations'),
            $salesForm
        ));
    }

    public function create()
    {
        return view('admin.workshop-promo-codes.create', $this->formData());
    }

    public function store(Request $request)
    {
        $validated = $this->validatePromo($request);

        WorkshopPromoCode::create(array_merge($validated, [
            'code' => strtoupper($validated['code']),
            'created_by' => auth()->id(),
        ]));

        return redirect()->route('admin.workshop-promo-codes.index')
            ->with('success', 'تم إنشاء كود الورشة بنجاح');
    }

    public function show(WorkshopPromoCode $workshopPromoCode)
    {
        $workshopPromoCode->load([
            'workshop',
            'creator',
            'activations.user',
            'activations.coupon',
            'activations.salesLead.assignee',
        ]);

        app(WorkshopPromoSalesService::class)->attachLeads($workshopPromoCode->activations);

        $stats = [
            'activations' => $workshopPromoCode->activations()->count(),
            'active' => $workshopPromoCode->activations()->where('status', 'active')->count(),
            'used' => $workshopPromoCode->activations()->where('status', 'used')->count(),
        ];

        return view('admin.workshop-promo-codes.show', array_merge(
            compact('workshopPromoCode', 'stats'),
            $this->salesFormData()
        ));
    }

    public function exportActivations(WorkshopPromoCode $workshopPromoCode): StreamedResponse
    {
        $workshopPromoCode->load([
            'activations.user',
            'activations.coupon',
            'activations.salesLead.assignee',
        ]);

        app(WorkshopPromoSalesService::class)->attachLeads($workshopPromoCode->activations);

        $activations = $workshopPromoCode->activations->sortByDesc('activated_at')->values();
        $safeCode = preg_replace('/[^\p{L}\p{N}\-_]+/u', '-', $workshopPromoCode->code) ?: 'promo';
        $filename = 'تفعيلات-كود-' . $safeCode . '-' . now()->format('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($activations, $workshopPromoCode) {
            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('تفعيلات الكود');
            $sheet->setRightToLeft(true);

            $rows = [
                ['كود الورشة', $workshopPromoCode->code],
                ['عنوان الكود', $workshopPromoCode->title],
                ['تاريخ التصدير', now()->format('Y-m-d H:i')],
                [],
                ['الاسم', 'البريد الإلكتروني', 'رقم الهاتف', 'تاريخ التفعيل', 'الحالة', 'كوبون مرتبط', 'مسند إلى', 'متابعة'],
            ];

            foreach ($activations as $act) {
                $lead = $act->resolvedLead ?? $act->salesLead;
                $rows[] = [
                    $act->user?->name ?? '—',
                    $act->user?->email ?? '—',
                    $act->user?->phone ?? '—',
                    $act->activated_at?->format('Y-m-d H:i') ?? '—',
                    $this->activationStatusLabel($act->status),
                    $act->coupon?->code ?? '—',
                    $lead?->assignee?->name ?? '—',
                    $lead?->next_follow_up_at?->format('Y-m-d H:i') ?? '—',
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

    private function activationStatusLabel(string $status): string
    {
        return match ($status) {
            WorkshopPromoActivation::STATUS_ACTIVE => 'مفعّل',
            WorkshopPromoActivation::STATUS_USED => 'استُخدم',
            WorkshopPromoActivation::STATUS_EXPIRED => 'منتهي',
            WorkshopPromoActivation::STATUS_CANCELLED => 'ملغي',
            default => $status,
        };
    }

    public function edit(WorkshopPromoCode $workshopPromoCode)
    {
        return view('admin.workshop-promo-codes.edit', array_merge(
            ['workshopPromoCode' => $workshopPromoCode],
            $this->formData()
        ));
    }

    public function update(Request $request, WorkshopPromoCode $workshopPromoCode)
    {
        $validated = $this->validatePromo($request, $workshopPromoCode->id);

        $workshopPromoCode->update(array_merge($validated, [
            'code' => strtoupper($validated['code']),
        ]));

        return redirect()->route('admin.workshop-promo-codes.show', $workshopPromoCode)
            ->with('success', 'تم تحديث الكود بنجاح');
    }

    public function destroy(WorkshopPromoCode $workshopPromoCode)
    {
        if ($workshopPromoCode->activations()->where('status', 'used')->exists()) {
            return back()->with('error', 'لا يمكن حذف كود تم استخدامه — عطّله بدلاً من ذلك');
        }

        $workshopPromoCode->delete();

        return redirect()->route('admin.workshop-promo-codes.index')
            ->with('success', 'تم حذف الكود');
    }

    public function storeActivationSalesTask(Request $request, WorkshopPromoActivation $activation)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|integer|exists:users,id',
            'next_follow_up_at' => 'required|date|after_or_equal:today',
            'sales_lead_group_id' => 'nullable|integer|exists:sales_lead_groups,id',
            'task_notes' => 'nullable|string|max:2000',
        ]);

        try {
            $lead = app(WorkshopPromoSalesService::class)->assignAndScheduleFollowUp(
                $activation,
                (int) $validated['assigned_to'],
                Carbon::parse($validated['next_follow_up_at']),
                isset($validated['sales_lead_group_id']) ? (int) $validated['sales_lead_group_id'] : null,
                $validated['task_notes'] ?? null,
            );

            $repName = $lead->assignee?->name ?? 'موظف المبيعات';
            $followUp = $lead->next_follow_up_at?->locale('ar')->translatedFormat('j F Y') ?? '';

            return back()->with('success', "تم الإسناد إلى {$repName} مع متابعة يوم {$followUp}.");
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function previewDiscount(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'offline_course_id' => 'required|exists:offline_courses,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $course = OfflineCourse::findOrFail($validated['offline_course_id']);
        $listPrice = (float) $course->price;

        $result = app(\App\Services\WorkshopPromoService::class)
            ->calculateOfflineDiscount($user, $course, $listPrice);

        if ($result['discount'] <= 0) {
            return response()->json(['has_discount' => false]);
        }

        return response()->json([
            'has_discount' => true,
            'discount_amount' => $result['discount'],
            'final_amount' => max(0, round($listPrice - $result['discount'], 2)),
            'promo_code' => $result['promo']?->code,
            'promo_title' => $result['promo']?->title,
            'discount_label' => $result['promo']?->discountLabel(),
        ]);
    }

    private function validatePromo(Request $request, ?int $ignoreId = null): array
    {
        $uniqueRule = 'unique:workshop_promo_codes,code';
        if ($ignoreId) {
            $uniqueRule .= ','.$ignoreId;
        }

        $validated = $request->validate([
            'workshop_id' => 'nullable|exists:workshops,id',
            'code' => 'required|string|max:32|'.$uniqueRule,
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'applies_to_online' => 'nullable|boolean',
            'applies_to_offline' => 'nullable|boolean',
            'applies_to_recorded' => 'nullable|boolean',
            'applicable_advanced_course_ids' => 'nullable|array',
            'applicable_advanced_course_ids.*' => 'exists:advanced_courses,id',
            'applicable_offline_course_ids' => 'nullable|array',
            'applicable_offline_course_ids.*' => 'exists:offline_courses,id',
            'max_activations' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1|max:10',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'nullable|boolean',
        ]);

        return [
            'workshop_id' => $validated['workshop_id'] ?? null,
            'code' => strtoupper(trim($validated['code'])),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'maximum_discount' => $validated['maximum_discount'] ?? null,
            'minimum_order_amount' => $validated['minimum_order_amount'] ?? null,
            'applies_to_online' => $request->boolean('applies_to_online'),
            'applies_to_offline' => $request->boolean('applies_to_offline'),
            'applies_to_recorded' => $request->boolean('applies_to_recorded'),
            'applicable_advanced_course_ids' => $validated['applicable_advanced_course_ids'] ?? null,
            'applicable_offline_course_ids' => $validated['applicable_offline_course_ids'] ?? null,
            'max_activations' => $validated['max_activations'] ?? null,
            'usage_limit_per_user' => $validated['usage_limit_per_user'] ?? 1,
            'starts_at' => $validated['starts_at'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ];
    }

    private function formData(): array
    {
        return [
            'workshops' => Workshop::orderByDesc('starts_at')->get(['id', 'title', 'slug', 'starts_at']),
            'advancedCourses' => AdvancedCourse::where('is_active', true)->orderBy('title')->get(['id', 'title', 'price']),
            'offlineCourses' => OfflineCourse::where('is_active', true)->orderBy('title')->get(['id', 'title', 'price']),
        ];
    }

    private function salesFormData(): array
    {
        return [
            'salesReps' => User::salesEmployees()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']),
            'salesLeadGroups' => SalesLeadGroup::query()
                ->orderBy('name')
                ->get(['id', 'name']),
        ];
    }
}
