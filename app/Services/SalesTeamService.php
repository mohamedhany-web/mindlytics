<?php

namespace App\Services;

use App\Models\SalesDailyReport;
use App\Models\SalesLead;
use App\Models\SalesLeadTransfer;
use App\Models\SalesTeam;
use App\Models\SalesTeamMember;
use App\Models\User;
use App\Models\WhatsAppConversation;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class SalesTeamService
{
    public function teamFor(User $user): ?SalesTeam
    {
        if ($user->hasSalesManagerPortalAccess()) {
            if ($user->isBusinessDeveloper()) {
                return SalesTeam::query()
                    ->active()
                    ->with(['members.user', 'manager'])
                    ->first();
            }

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
        if ($manager->isBusinessDeveloper()) {
            $team = SalesTeam::query()->active()->with(['members.user', 'manager'])->first();
            if ($team) {
                return $team;
            }

            $shell = new SalesTeam([
                'name' => 'Business Development — كل المبيعات',
                'manager_id' => $manager->id,
                'is_active' => true,
            ]);
            $shell->exists = false;

            return $shell;
        }

        $team = $this->teamFor($manager);
        if (! $team || ! $manager->isSalesManager()) {
            throw new HttpResponseException(
                response()->view('employee.sales-manager.no-team', [], 403)
            );
        }

        return $team;
    }

    /** @return list<int> */
    public function allSalesEmployeeIds(): array
    {
        return User::salesEmployees()
            ->where('is_active', true)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * أعضاء الفريق الظاهرون للمشاهد.
     * Business Developer يرى كل موظفي المبيعات عبر كل الفرق.
     *
     * @return list<int>
     */
    public function memberUserIds(SalesTeam $team, ?User $viewer = null): array
    {
        $viewer ??= auth()->user();
        if ($viewer?->isBusinessDeveloper()) {
            return $this->allSalesEmployeeIds();
        }

        if (! $team->exists) {
            return [];
        }

        return $team->memberUserIds();
    }

    /**
     * سجلات عضوية للاستخدام في قوائم التحويل/التوزيع (نفس شكل SalesTeamMember).
     *
     * @return Collection<int, SalesTeamMember>
     */
    public function memberRecords(User $viewer, ?SalesTeam $team = null): Collection
    {
        if ($viewer->isBusinessDeveloper()) {
            return User::salesEmployees()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(function (User $u) use ($team) {
                    $row = new SalesTeamMember([
                        'sales_team_id' => $team?->id,
                        'user_id' => $u->id,
                        'role' => SalesTeamMember::ROLE_MEMBER,
                    ]);
                    $row->setRelation('user', $u);

                    return $row;
                });
        }

        if (! $team?->exists) {
            return collect();
        }

        return $team->members()->where('role', 'member')->with('user:id,name')->get();
    }

    /** @return list<int> */
    public function visibleAssigneeIds(User $user): array
    {
        if ($user->isBusinessDeveloper()) {
            return $this->allSalesEmployeeIds();
        }

        if ($user->hasSalesManagerPortalAccess()) {
            $team = $this->teamFor($user);

            return $team ? $this->memberUserIds($team, $user) : [];
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

        if ($user->isBusinessDeveloper()) {
            return $lead->assigned_to === null
                || in_array((int) $lead->assigned_to, $this->allSalesEmployeeIds(), true)
                || User::salesManagers()->where('id', $lead->assigned_to)->exists();
        }

        if ($user->isSalesEmployee()) {
            return (int) $lead->assigned_to === (int) $user->id;
        }

        if ($user->hasSalesManagerPortalAccess()) {
            $team = $this->teamFor($user);
            if (! $team) {
                return false;
            }

            return in_array((int) $lead->assigned_to, $this->memberUserIds($team, $user), true);
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

        if (! $user->hasSalesManagerPortalAccess()) {
            return false;
        }

        $team = $this->teamFor($user);
        if (! $team && ! $user->isBusinessDeveloper()) {
            return false;
        }

        $memberIds = $user->isBusinessDeveloper()
            ? $this->allSalesEmployeeIds()
            : $this->memberUserIds($team, $user);

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
        $memberIds = $this->memberUserIds($team, $manager);

        if ($manager->isBusinessDeveloper()) {
            $memberIds = array_values(array_unique(array_merge(
                $memberIds,
                $this->allSalesEmployeeIds(),
                User::salesManagers()->where('is_active', true)->pluck('id')->map(fn ($id) => (int) $id)->all()
            )));
        }

        if (! in_array((int) $lead->assigned_to, $memberIds, true) && ! $manager->isBusinessDeveloper()) {
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
            $team->exists ? (int) $team->id : null
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
