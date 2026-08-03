<?php

namespace App\Services;

use App\Models\SalesLead;
use App\Models\SalesLeadTransfer;
use App\Models\SalesTeam;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class SalesLeadTransferService
{
    public const SOURCE_MANUAL = 'manual';
    public const SOURCE_MANAGER = 'manager';
    public const SOURCE_ADMIN_BULK = 'admin_bulk';
    public const SOURCE_DISTRIBUTION = 'distribution';

    public function ready(): bool
    {
        return Schema::hasTable('sales_lead_transfers');
    }

    /**
     * تحويل/تعيين عميل مع تسجيل صف تحويل دائماً.
     */
    public function assign(
        SalesLead $lead,
        int $toUserId,
        ?User $actor = null,
        ?string $reason = null,
        string $source = self::SOURCE_MANUAL,
        ?int $teamId = null,
    ): SalesLeadTransfer {
        if (! $this->ready()) {
            throw ValidationException::withMessages(['lead' => 'جدول التحويلات غير جاهز — شغّل migrate.']);
        }

        $actor = $actor ?? Auth::user();
        if (! $actor) {
            throw ValidationException::withMessages(['lead' => 'يجب تسجيل الدخول لإتمام التحويل.']);
        }

        if (! User::salesEmployees()->where('is_active', true)->whereKey($toUserId)->exists()) {
            throw ValidationException::withMessages(['to_user_id' => 'الموظف الوجهة يجب أن يكون موظف مبيعات فعّال.']);
        }

        $fromId = $lead->assigned_to ? (int) $lead->assigned_to : null;
        if ($fromId === $toUserId) {
            throw ValidationException::withMessages(['to_user_id' => 'العميل مسند بالفعل لهذا الموظف.']);
        }

        return DB::transaction(function () use ($lead, $toUserId, $actor, $reason, $source, $teamId, $fromId) {
            $lead->update(['assigned_to' => $toUserId]);

            $attrs = [
                'sales_lead_id' => $lead->id,
                'from_user_id' => $fromId,
                'to_user_id' => $toUserId,
                'transferred_by' => $actor->id,
                'sales_team_id' => $teamId,
                'reason' => $reason,
            ];

            if (Schema::hasColumn('sales_lead_transfers', 'source')) {
                $attrs['source'] = $source;
            }
            if (Schema::hasColumn('sales_lead_transfers', 'interest_type_id')) {
                $attrs['interest_type_id'] = $lead->interest_type_id;
            }

            $transfer = SalesLeadTransfer::query()->create($attrs);

            SalesAuditService::log(
                'sales_lead_transferred',
                $lead->fresh(),
                ['assigned_to' => $fromId],
                ['assigned_to' => $toUserId, 'source' => $source],
                'تحويل العميل «'.$lead->name.'» — مصدر: '.$source
            );

            return $transfer;
        });
    }

    /**
     * تحويل جماعي (مثل توزيع الأدمن) مع صف لكل lead.
     *
     * @param  array<int, list<int>>  $assignment  toUserId => leadIds
     */
    public function bulkAssign(
        array $assignment,
        int $fromUserId,
        ?User $actor = null,
        string $source = self::SOURCE_ADMIN_BULK,
        ?string $reason = null,
    ): int {
        $actor = $actor ?? Auth::user();
        $count = 0;

        foreach ($assignment as $toUserId => $leadIds) {
            if ($leadIds === []) {
                continue;
            }

            $leads = SalesLead::query()->withTrashed()->whereIn('id', $leadIds)->get();
            foreach ($leads as $lead) {
                if ((int) $lead->assigned_to === (int) $toUserId) {
                    continue;
                }
                $this->assign($lead, (int) $toUserId, $actor, $reason, $source, null);
                $count++;
            }
        }

        return $count;
    }
}
