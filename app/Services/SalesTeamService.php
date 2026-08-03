<?php

namespace App\Services;

use App\Models\SalesDailyReport;
use App\Models\SalesLead;
use App\Models\SalesLeadTransfer;
use App\Models\SalesTeam;
use App\Models\User;
use App\Models\WhatsAppConversation;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class SalesTeamService
{
    public function teamFor(User $user): ?SalesTeam
    {
        if ($user->isSalesManager()) {
            return SalesTeam::query()
                ->active()
                ->where('manager_id', $user->id)
                ->with(['members.user', 'manager'])
                ->first();
        }

        if ($user->isSalesEmployee()) {
            $membership = $user->salesTeamMembership;
            if (! $membership) {
                return null;
            }

            return SalesTeam::query()
                ->active()
                ->whereKey($membership->sales_team_id)
                ->with(['members.user', 'manager'])
                ->first();
        }

        return null;
    }

    public function managedTeamOrFail(User $manager): SalesTeam
    {
        $team = $this->teamFor($manager);
        if (! $team || ! $manager->isSalesManager()) {
            throw new HttpResponseException(
                response()->view('employee.sales-manager.no-team', [], 403)
            );
        }

        return $team;
    }

    /** @return list<int> */
    public function memberUserIds(SalesTeam $team): array
    {
        return $team->memberUserIds();
    }

    /** @return list<int> */
    public function visibleAssigneeIds(User $user): array
    {
        if ($user->isSalesManager()) {
            $team = $this->teamFor($user);

            return $team ? $this->memberUserIds($team) : [];
        }

        if ($user->isSalesEmployee()) {
            return [(int) $user->id];
        }

        return [];
    }

    public function canAccessLead(User $user, SalesLead $lead): bool
    {
        if (in_array($user->role, ['admin', 'super_admin'], true)) {
            return true;
        }

        if ($user->isSalesEmployee()) {
            return (int) $lead->assigned_to === (int) $user->id;
        }

        if ($user->isSalesManager()) {
            $team = $this->teamFor($user);
            if (! $team) {
                return false;
            }

            return in_array((int) $lead->assigned_to, $this->memberUserIds($team), true);
        }

        return false;
    }

    public function canAccessConversation(User $user, WhatsAppConversation $conversation): bool
    {
        if ($conversation->isOwnedBySalesAgent((int) $user->id)) {
            return true;
        }

        if ((int) $conversation->assigned_to === (int) $user->id) {
            return true;
        }

        if (! $user->isSalesManager()) {
            return false;
        }

        $team = $this->teamFor($user);
        if (! $team) {
            return false;
        }

        $memberIds = $this->memberUserIds($team);

        if ((int) $conversation->assigned_to && in_array((int) $conversation->assigned_to, $memberIds, true)) {
            return true;
        }

        $conversation->loadMissing('salesLead');

        return $conversation->sales_lead_id
            && in_array((int) $conversation->salesLead?->assigned_to, $memberIds, true);
    }

    public function transferLead(User $manager, SalesLead $lead, int $toUserId, ?string $reason = null): SalesLeadTransfer
    {
        $team = $this->managedTeamOrFail($manager);
        $memberIds = $this->memberUserIds($team);

        if (! in_array((int) $lead->assigned_to, $memberIds, true)) {
            throw ValidationException::withMessages([
                'lead' => 'هذا العميل المحتمل غير تابع لفريقك.',
            ]);
        }

        if (! in_array($toUserId, $memberIds, true)) {
            throw ValidationException::withMessages([
                'to_user_id' => 'يجب تحويل العميل إلى عضو في فريقك.',
            ]);
        }

        return app(SalesLeadTransferService::class)->assign(
            $lead,
            $toUserId,
            $manager,
            $reason,
            SalesLeadTransferService::SOURCE_MANAGER,
            (int) $team->id
        );
    }

    public function syncMemberReportTeamId(User $member, SalesDailyReport $report): void
    {
        $team = $this->teamFor($member);
        if ($team && ! $report->sales_team_id) {
            $report->forceFill(['sales_team_id' => $team->id])->save();
        }
    }
}
