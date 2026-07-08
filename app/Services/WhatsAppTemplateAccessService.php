<?php

namespace App\Services;

use App\Models\User;
use App\Models\WhatsAppMetaTemplate;
use App\Support\WhatsAppCloudSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class WhatsAppTemplateAccessService
{
    public const MODE_ALL = 'all';

    public const MODE_RESTRICTED = 'restricted';

    public function mode(): string
    {
        return WhatsAppCloudSettings::templateAccessMode();
    }

    public function isRestricted(): bool
    {
        return $this->mode() === self::MODE_RESTRICTED;
    }

    /**
     * @return array<string, string>
     */
    public function modeLabels(): array
    {
        return [
            self::MODE_ALL => 'جميع القوالب لكل موظفي المبيعات',
            self::MODE_RESTRICTED => 'قوالب محددة لكل موظف',
        ];
    }

    public function setMode(string $mode): void
    {
        WhatsAppCloudSettings::setTemplateAccessMode($mode);
    }

    public function bypassesRestrictions(?User $user): bool
    {
        if (! $this->isRestricted()) {
            return true;
        }

        if (! $user) {
            return false;
        }

        return in_array($user->role, ['super_admin', 'admin'], true);
    }

    /**
     * @return Builder<WhatsAppMetaTemplate>
     */
    public function approvedQueryForUser(?User $user, bool $forceAll = false): Builder
    {
        $query = WhatsAppMetaTemplate::query()
            ->where('status', WhatsAppMetaTemplate::STATUS_APPROVED);

        if ($forceAll || $this->bypassesRestrictions($user)) {
            return $query;
        }

        if (! $user || ! Schema::hasTable('whatsapp_meta_template_user')) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('assignedUsers', fn (Builder $q) => $q->where('users.id', $user->id));
    }

    public function userCanUseTemplate(?User $user, string $name, string $language, bool $forceAll = false): bool
    {
        if ($forceAll || $this->bypassesRestrictions($user)) {
            return WhatsAppMetaTemplate::query()
                ->where('name', $name)
                ->where('language', $language)
                ->where('status', WhatsAppMetaTemplate::STATUS_APPROVED)
                ->exists();
        }

        if (! $user || ! Schema::hasTable('whatsapp_meta_template_user')) {
            return false;
        }

        return WhatsAppMetaTemplate::query()
            ->where('name', $name)
            ->where('language', $language)
            ->where('status', WhatsAppMetaTemplate::STATUS_APPROVED)
            ->whereHas('assignedUsers', fn (Builder $q) => $q->where('users.id', $user->id))
            ->exists();
    }

    /**
     * @param  array<int, array<string, mixed>>  $templates
     * @return array<int, array<string, mixed>>
     */
    public function filterTemplateRowsForUser(array $templates, ?User $user, bool $forceAll = false): array
    {
        if ($forceAll || $this->bypassesRestrictions($user)) {
            return $templates;
        }

        if (! $user || ! Schema::hasTable('whatsapp_meta_template_user')) {
            return [];
        }

        $allowedKeys = $this->approvedQueryForUser($user)
            ->get(['name', 'language'])
            ->mapWithKeys(fn (WhatsAppMetaTemplate $t) => [$t->name.'|'.$t->language => true])
            ->all();

        return collect($templates)
            ->filter(function (array $row) use ($allowedKeys) {
                $key = ($row['name'] ?? '').'|'.($row['language'] ?? '');

                return isset($allowedKeys[$key]);
            })
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, User>
     */
    public function salesStaffForAssignment(): Collection
    {
        return User::query()
            ->salesStaff()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }

    /**
     * @param  array<int|string>  $userIds
     */
    public function syncTemplateAssignments(WhatsAppMetaTemplate $template, array $userIds): void
    {
        if (! Schema::hasTable('whatsapp_meta_template_user')) {
            return;
        }

        $allowedIds = $this->salesStaffForAssignment()->pluck('id')->all();
        $ids = collect($userIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0 && in_array($id, $allowedIds, true))
            ->unique()
            ->values()
            ->all();

        $template->assignedUsers()->sync($ids);
    }
}
