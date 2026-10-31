<?php

namespace App\Domains\Sales\Support;

use App\Domains\Sales\Models\CrmAccount;
use App\Domains\Sales\Models\CrmCampaign;
use App\Domains\Sales\Models\CrmCase;
use App\Domains\Sales\Models\CrmContact;
use App\Domains\Sales\Models\CrmDocument;
use App\Domains\Sales\Models\SalesActivity;
use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Services\SalesTeamService;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * من يرى ماذا في شاشات CRM الموحّدة (docs/SALES_CRM.md):
 * - موظف المبيعات: عملاؤه فقط.
 * - مدير المبيعات: عملاء فريقه (Business Developer: كل موظفي المبيعات).
 * - الأدمن: الكل، بما فيهم العملاء بلا موظف.
 * نفس الشاشات للجميع؛ الدور يحدد البيانات والإجراءات المتاحة، لا شكل النظام.
 */
final class CrmScope
{
    public const ROLE_REP = 'rep';

    public const ROLE_MANAGER = 'manager';

    public const ROLE_ADMIN = 'admin';

    /**
     * @param  list<int>|null  $assigneeIds  null = بلا قيد (الأدمن)
     */
    private function __construct(
        public readonly User $user,
        public readonly string $role,
        public readonly ?array $assigneeIds,
        public readonly string $routePrefix,
    ) {}

    public static function for(User $user, bool $adminArea = false): self
    {
        $platformAdmin = $user->isAdmin()
            || $user->isSuperAdmin()
            || in_array((string) ($user->role ?? ''), ['admin', 'super_admin'], true);

        if ($adminArea && $platformAdmin) {
            return new self($user, self::ROLE_ADMIN, null, 'admin.crm.');
        }

        $teams = app(SalesTeamService::class);
        if ($user->hasSalesManagerPortalAccess()) {
            return new self($user, self::ROLE_MANAGER, array_values(array_unique(array_map('intval', $teams->visibleAssigneeIds($user)))), 'employee.crm.');
        }

        return new self($user, self::ROLE_REP, [(int) $user->id], 'employee.crm.');
    }

    public function isRep(): bool
    {
        return $this->role === self::ROLE_REP;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /** عملاء ضمن النطاق. */
    public function leads(): Builder
    {
        return $this->scoped(SalesLead::query());
    }

    public function accounts(): Builder
    {
        return $this->scoped(CrmAccount::query());
    }

    public function contacts(): Builder
    {
        return $this->scoped(CrmContact::query());
    }

    public function campaigns(): Builder
    {
        return $this->scoped(CrmCampaign::query());
    }

    public function cases(): Builder
    {
        return $this->scoped(CrmCase::query());
    }

    public function documents(): Builder
    {
        return $this->scoped(CrmDocument::query());
    }

    public function activities(): Builder
    {
        $query = SalesActivity::query();
        if ($this->assigneeIds !== null) {
            $ids = $this->assigneeIds === [] ? [0] : $this->assigneeIds;
            $query->where(function ($q) use ($ids) {
                $q->whereIn('user_id', $ids)
                    ->orWhereHas('lead', fn ($l) => $l->whereIn('assigned_to', $ids));
            });
        }

        return $query;
    }

    private function scoped(Builder $query, string $column = 'assigned_to'): Builder
    {
        if ($this->assigneeIds !== null) {
            $query->whereIn($column, $this->assigneeIds === [] ? [0] : $this->assigneeIds);
        }

        return $query;
    }

    public function canSee(SalesLead $lead): bool
    {
        return $this->assigneeIds === null || in_array((int) $lead->assigned_to, $this->assigneeIds, true);
    }

    public function canSeeAssigned(?int $assignedTo): bool
    {
        return $this->assigneeIds === null || ($assignedTo !== null && in_array((int) $assignedTo, $this->assigneeIds, true));
    }

    /** يقدر يعدّل بيانات العميل ويحرّك مرحلته (صاحب العميل أو الأدمن). */
    public function canWorkOn(SalesLead $lead): bool
    {
        return $this->isAdmin() || ($this->isRep() && (int) $lead->assigned_to === (int) $this->user->id);
    }

    /** تحويل العميل لموظف آخر وتحويله لطالب (ربطه بطلب). */
    public function canManage(): bool
    {
        return ! $this->isRep();
    }

    /** موظفو المبيعات الذين يظهرون في الفلاتر وقوائم التحويل. */
    public function members(): Collection
    {
        $query = User::salesEmployees()->where('is_active', true)->orderBy('name');
        if ($this->assigneeIds !== null) {
            $query->whereIn('id', $this->assigneeIds === [] ? [0] : $this->assigneeIds);
        }

        return $query->get(['id', 'name']);
    }

    public function route(string $name): string
    {
        return $this->routePrefix.$name;
    }
}
