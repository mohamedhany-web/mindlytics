<?php

namespace App\Domains\Sales\Services;

use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Support\PhoneKey;
use Illuminate\Validation\ValidationException;

/**
 * رقم الهاتف له مالك واحد (docs/SALES_INTEGRITY.md): لا يُسجَّل نفس العميل عند موظفَين،
 * وإلا طالب الاثنان بنفس البيعة ونفس العمولة. الانتقال بين الموظفين يتم بالتحويل الرسمي فقط.
 * الـ Lead الخاسر لا يحجز الرقم: يمكن لأي موظف فتحه من جديد.
 */
class SalesLeadOwnership
{
    /** أي Lead نشط بنفس الرقم (بأي صيغة). */
    public function activeLeadWithPhone(?string $phone, ?int $ignoreLeadId = null): ?SalesLead
    {
        $tail = PhoneKey::likeTail($phone);
        if ($tail === null) {
            return null;
        }

        return SalesLead::query()
            ->with('assignee:id,name')
            ->where('phone', 'like', $tail)
            ->where('stage', '!=', 'lost')
            ->when($ignoreLeadId, fn ($q) => $q->where('id', '!=', $ignoreLeadId))
            ->orderByDesc('updated_at')
            ->get()
            ->first(fn (SalesLead $l) => PhoneKey::same($l->phone, $phone));
    }

    /** يرمي خطأ تحقق إن كان الرقم مسجّلاً عند موظف آخر. */
    public function assertAvailableFor(?string $phone, ?int $assigneeId, ?int $ignoreLeadId = null): void
    {
        $existing = $this->activeLeadWithPhone($phone, $ignoreLeadId);
        if (! $existing || ! $existing->assigned_to || (int) $existing->assigned_to === (int) $assigneeId) {
            return;
        }

        throw ValidationException::withMessages([
            'phone' => 'هذا الرقم مسجّل بالفعل كعميل عند '.($existing->assignee?->name ?? 'موظف آخر')
                .' (Lead #'.$existing->id.'). اطلب تحويله من مدير المبيعات بدلاً من تسجيله مرة أخرى.',
        ]);
    }
}
