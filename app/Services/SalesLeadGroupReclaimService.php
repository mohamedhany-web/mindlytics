<?php

namespace App\Services;

use App\Models\SalesActivity;
use App\Models\SalesLead;
use App\Models\SalesLeadGroup;
use App\Models\SalesLeadTransfer;
use App\Models\User;
use App\Models\WhatsAppContact;
use App\Models\WhatsAppConversation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class SalesLeadGroupReclaimService
{
    /**
     * سحب عملاء المجموعة من محفظة الموظف: تتشال من عنده وتفضل في المجموعة
     * بكل الملاحظات والأنشطة السابقة.
     */
    public function reclaimFromEmployee(
        SalesLeadGroup $group,
        int $employeeId,
        User $actor,
        string $reason = 'سحب بيانات المجموعة من الموظف',
    ): int {
        $employee = User::query()->find($employeeId);
        $employeeName = $employee?->name ?? ('#'.$employeeId);

        $leads = SalesLead::query()
            ->where('sales_lead_group_id', $group->id)
            ->where('assigned_to', $employeeId)
            ->get(['id', 'name', 'assigned_to', 'notes', 'sales_lead_group_id']);

        if ($leads->isEmpty()) {
            return 0;
        }

        $leadIds = $leads->pluck('id')->map(fn ($id) => (int) $id)->all();

        return (int) DB::transaction(function () use ($group, $employeeId, $employeeName, $actor, $reason, $leadIds) {
            SalesLead::query()->whereIn('id', $leadIds)->update(['assigned_to' => null]);

            $this->recordReclaimActivities($leadIds, $actor, $employeeName, $reason);
            $this->unassignWhatsApp($leadIds, $employeeId);

            SalesAuditService::log(
                'sales_group_leads_reclaimed',
                $group,
                ['assigned_to' => $employeeId, 'lead_ids' => $leadIds],
                ['assigned_to' => null, 'kept_in_group' => true],
                'سحب '.$this->arabicCount(count($leadIds)).' من «'.$group->name.'» من محفظة '.$employeeName.' — الملاحظات محفوظة مع المجموعة. '.$reason
            );

            return count($leadIds);
        });
    }

    /**
     * ترحيل عملاء المجموعة غير المسندين إلى محفظة الموظف بعدد أو نطاق (من–إلى).
     * الترقيم 1-based حسب ترتيب الاسم داخل عملاء المجموعة بدون موظف.
     */
    public function assignToEmployee(
        SalesLeadGroup $group,
        int $employeeId,
        User $actor,
        ?int $count = null,
        ?int $from = null,
        ?int $to = null,
        string $reason = 'ترحيل بيانات المجموعة للموظف',
    ): int {
        if (! $group->userHasAccess($employeeId)) {
            throw ValidationException::withMessages([
                'employee_id' => 'الموظف يجب أن يكون له صلاحية على هذه المجموعة أولاً.',
            ]);
        }

        $employee = User::salesEmployees()->where('is_active', true)->whereKey($employeeId)->first();
        if (! $employee) {
            throw ValidationException::withMessages([
                'employee_id' => 'الموظف الوجهة يجب أن يكون موظف مبيعات فعّال.',
            ]);
        }

        $orderedIds = SalesLead::query()
            ->where('sales_lead_group_id', $group->id)
            ->where(function ($q) {
                $q->whereNull('assigned_to')->orWhere('assigned_to', 0);
            })
            ->orderBy('name')
            ->orderBy('id')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        if ($orderedIds === []) {
            throw ValidationException::withMessages([
                'count' => 'لا يوجد عملاء عند المجموعة بدون موظف لترحيلهم.',
            ]);
        }

        $leadIds = $this->selectLeadIds($orderedIds, $count, $from, $to);
        if ($leadIds === []) {
            return 0;
        }

        $employeeName = $employee->name;

        return (int) DB::transaction(function () use ($group, $employeeId, $employeeName, $actor, $reason, $leadIds) {
            SalesLead::query()->whereIn('id', $leadIds)->update(['assigned_to' => $employeeId]);

            $this->recordAssignActivities($leadIds, $actor, $employeeName, $reason);
            $this->recordAssignTransfers($leadIds, $employeeId, $actor, $reason);
            $this->assignWhatsApp($leadIds, $employeeId);

            SalesAuditService::log(
                'sales_group_leads_assigned',
                $group,
                ['assigned_to' => null, 'lead_ids' => $leadIds],
                ['assigned_to' => $employeeId],
                'ترحيل '.$this->arabicCount(count($leadIds)).' من «'.$group->name.'» إلى محفظة '.$employeeName.' — بقوا في المجموعة. '.$reason
            );

            return count($leadIds);
        });
    }

    /**
     * اختيار شريحة من قائمة مرتبة: عدد معين، أو من رقم إلى رقم (1-based).
     *
     * @param  list<int>  $orderedLeadIds
     * @return list<int>
     */
    public function selectLeadIds(array $orderedLeadIds, ?int $count, ?int $from, ?int $to): array
    {
        $total = count($orderedLeadIds);
        if ($total === 0) {
            return [];
        }

        $hasRange = $from !== null || $to !== null;
        if (! $hasRange && ($count === null || $count < 1)) {
            throw ValidationException::withMessages([
                'count' => 'حدد عدداً من العملاء، أو نطاقاً من رقم إلى رقم.',
            ]);
        }

        if ($hasRange) {
            $start = max(1, (int) ($from ?? 1));
            $end = min($total, (int) ($to ?? $total));
            if ($start > $end) {
                throw ValidationException::withMessages([
                    'from' => 'رقم البداية يجب ألا يتجاوز رقم النهاية أو عدد العملاء المتاحين ('.$total.').',
                ]);
            }

            return array_values(array_slice($orderedLeadIds, $start - 1, $end - $start + 1));
        }

        return array_values(array_slice($orderedLeadIds, 0, min($count, $total)));
    }

    /**
     * @param  list<int>  $previousMemberIds
     * @param  list<int>  $nextMemberIds
     */
    public function reclaimFromRemovedMembers(
        SalesLeadGroup $group,
        array $previousMemberIds,
        array $nextMemberIds,
        User $actor,
    ): int {
        $removed = $this->removedMemberIds($previousMemberIds, $nextMemberIds);
        $total = 0;

        foreach ($removed as $userId) {
            $total += $this->reclaimFromEmployee(
                $group,
                $userId,
                $actor,
                'إزالة الموظف من المجموعة'
            );
        }

        return $total;
    }

    /**
     * @param  list<int>  $previous
     * @param  list<int>  $next
     * @return list<int>
     */
    public function removedMemberIds(array $previous, array $next): array
    {
        $previous = array_values(array_unique(array_map('intval', $previous)));
        $next = array_values(array_unique(array_map('intval', $next)));

        return array_values(array_diff($previous, $next));
    }

    /**
     * @param  list<int>  $leadIds
     */
    private function recordReclaimActivities(array $leadIds, User $actor, string $employeeName, string $reason): void
    {
        if ($leadIds === [] || ! Schema::hasTable('sales_activities')) {
            return;
        }

        $now = now();
        $rows = [];
        foreach ($leadIds as $leadId) {
            $rows[] = [
                'sales_lead_id' => $leadId,
                'user_id' => $actor->id,
                'type' => 'note',
                'title' => 'سحب من محفظة الموظف',
                'body' => 'تم سحب العميل من محفظة «'.$employeeName.'» إلى المجموعة. كل الملاحظات والأنشطة السابقة محفوظة. '.$reason,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, 200) as $chunk) {
            SalesActivity::query()->insert($chunk);
        }
    }

    /**
     * @param  list<int>  $leadIds
     */
    private function unassignWhatsApp(array $leadIds, int $employeeId): void
    {
        if ($leadIds === []) {
            return;
        }

        if (Schema::hasTable('whatsapp_conversations') && Schema::hasColumn('whatsapp_conversations', 'assigned_to')) {
            WhatsAppConversation::query()
                ->whereIn('sales_lead_id', $leadIds)
                ->where('assigned_to', $employeeId)
                ->update(['assigned_to' => null]);
        }

        if (Schema::hasTable('whatsapp_contacts') && Schema::hasColumn('whatsapp_contacts', 'assigned_to')) {
            WhatsAppContact::query()
                ->whereIn('sales_lead_id', $leadIds)
                ->where('assigned_to', $employeeId)
                ->update(['assigned_to' => null]);
        }
    }

    /**
     * @param  list<int>  $leadIds
     */
    private function recordAssignActivities(array $leadIds, User $actor, string $employeeName, string $reason): void
    {
        if ($leadIds === [] || ! Schema::hasTable('sales_activities')) {
            return;
        }

        $now = now();
        $rows = [];
        foreach ($leadIds as $leadId) {
            $rows[] = [
                'sales_lead_id' => $leadId,
                'user_id' => $actor->id,
                'type' => 'note',
                'title' => 'ترحيل إلى محفظة الموظف',
                'body' => 'تم ترحيل العميل من بيانات المجموعة إلى محفظة «'.$employeeName.'». بقي داخل المجموعة. '.$reason,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, 200) as $chunk) {
            SalesActivity::query()->insert($chunk);
        }
    }

    /**
     * @param  list<int>  $leadIds
     */
    private function recordAssignTransfers(array $leadIds, int $employeeId, User $actor, string $reason): void
    {
        if ($leadIds === [] || ! Schema::hasTable('sales_lead_transfers')) {
            return;
        }

        $now = now();
        $hasSource = Schema::hasColumn('sales_lead_transfers', 'source');
        $rows = [];
        foreach ($leadIds as $leadId) {
            $row = [
                'sales_lead_id' => $leadId,
                'from_user_id' => null,
                'to_user_id' => $employeeId,
                'transferred_by' => $actor->id,
                'sales_team_id' => null,
                'reason' => $reason,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            if ($hasSource) {
                $row['source'] = SalesLeadTransferService::SOURCE_ADMIN_BULK;
            }
            $rows[] = $row;
        }

        foreach (array_chunk($rows, 200) as $chunk) {
            SalesLeadTransfer::query()->insert($chunk);
        }
    }

    /**
     * @param  list<int>  $leadIds
     */
    private function assignWhatsApp(array $leadIds, int $employeeId): void
    {
        if ($leadIds === []) {
            return;
        }

        if (Schema::hasTable('whatsapp_conversations') && Schema::hasColumn('whatsapp_conversations', 'assigned_to')) {
            WhatsAppConversation::query()
                ->whereIn('sales_lead_id', $leadIds)
                ->where(function ($q) {
                    $q->whereNull('assigned_to')->orWhere('assigned_to', 0);
                })
                ->update(['assigned_to' => $employeeId]);
        }

        if (Schema::hasTable('whatsapp_contacts') && Schema::hasColumn('whatsapp_contacts', 'assigned_to')) {
            WhatsAppContact::query()
                ->whereIn('sales_lead_id', $leadIds)
                ->where(function ($q) {
                    $q->whereNull('assigned_to')->orWhere('assigned_to', 0);
                })
                ->update(['assigned_to' => $employeeId]);
        }
    }

    private function arabicCount(int $count): string
    {
        if ($count === 1) {
            return 'عميل واحد';
        }

        if ($count === 2) {
            return 'عميلين';
        }

        if ($count >= 3 && $count <= 10) {
            return $count.' عملاء';
        }

        return $count.' عميلاً';
    }
}
