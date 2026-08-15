<?php

namespace App\Services;

use App\Models\SalesActivity;
use App\Models\SalesLead;
use App\Models\SalesLeadGroup;
use App\Models\User;
use App\Models\WhatsAppContact;
use App\Models\WhatsAppConversation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
