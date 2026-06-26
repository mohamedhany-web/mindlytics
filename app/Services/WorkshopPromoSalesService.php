<?php

namespace App\Services;

use App\Models\SalesActivity;
use App\Models\SalesLead;
use App\Models\SalesLeadGroup;
use App\Models\User;
use App\Models\WorkshopPromoActivation;
use App\Models\WorkshopPromoCode;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WorkshopPromoSalesService
{
    public function leadForActivation(WorkshopPromoActivation $activation): ?SalesLead
    {
        $activation->loadMissing(['user', 'promoCode.workshop', 'salesLead.assignee']);

        if ($activation->sales_lead_id && $activation->salesLead) {
            return $activation->salesLead;
        }

        $user = $activation->user;
        if (! $user) {
            return null;
        }

        $lead = $this->findLeadByUserContact($user);
        if (! $lead) {
            $lead = SalesLead::query()
                ->with('assignee')
                ->where('notes', 'like', '%[workshop_promo_activation:'.$activation->id.']%')
                ->orderByDesc('id')
                ->first();
        }

        if ($lead && Schema::hasColumn('workshop_promo_activations', 'sales_lead_id') && ! $activation->sales_lead_id) {
            $activation->update(['sales_lead_id' => $lead->id]);
        }

        return $lead;
    }

    /**
     * @param  Collection<int, WorkshopPromoActivation>  $activations
     */
    public function attachLeads(Collection $activations): void
    {
        foreach ($activations as $activation) {
            $activation->setRelation('resolvedLead', $this->leadForActivation($activation));
        }
    }

    public function assignAndScheduleFollowUp(
        WorkshopPromoActivation $activation,
        int $assigneeId,
        Carbon $followUpAt,
        ?int $groupId = null,
        ?string $taskNotes = null
    ): SalesLead {
        if (! User::salesEmployees()->where('is_active', true)->whereKey($assigneeId)->exists()) {
            throw new \InvalidArgumentException('يرجى اختيار موظف مبيعات فعّال.');
        }

        if ($groupId !== null) {
            $group = SalesLeadGroup::query()->find($groupId);
            if (! $group || ! $group->includesAllMembers([$assigneeId])) {
                throw new \InvalidArgumentException('مجموعة العملاء لا تشمل موظف المبيعات المحدد.');
            }
        }

        $activation->loadMissing(['user', 'promoCode.workshop']);

        return DB::transaction(function () use ($activation, $assigneeId, $followUpAt, $groupId, $taskNotes) {
            $existing = $this->leadForActivation($activation);
            $previousAssignee = $existing?->assigned_to;
            $promo = $activation->promoCode;
            $workshopTitle = $promo?->workshop?->title ?? 'ورشة';
            $notesBlock = $this->buildActivationNotes($activation, $promo);

            if ($existing) {
                $existing->update([
                    'assigned_to' => $assigneeId,
                    'sales_lead_group_id' => $groupId ?? $existing->sales_lead_group_id,
                    'next_follow_up_at' => $followUpAt,
                    'notes' => trim(($existing->notes ?? '')."\n\n".$notesBlock),
                    'interest' => $existing->interest ?: 'كود خصم ورشة: '.($promo?->code ?? '—'),
                ]);
                $lead = $existing->fresh(['assignee']);
            } else {
                $user = $activation->user;
                $lead = SalesLead::create([
                    'assigned_to' => $assigneeId,
                    'created_by' => auth()->id(),
                    'sales_lead_group_id' => $groupId,
                    'import_batch' => 'WPROMO-'.$activation->id.'-'.now()->format('YmdHis'),
                    'name' => $user?->name ?: 'عميل كود ورشة',
                    'phone' => $user?->phone,
                    'email' => $user?->email,
                    'source' => 'event',
                    'stage' => 'new',
                    'priority' => 'normal',
                    'interest' => 'كود خصم ورشة: '.($promo?->code ?? '—').' — '.$workshopTitle,
                    'notes' => $notesBlock,
                    'next_follow_up_at' => $followUpAt,
                ]);
            }

            if (Schema::hasColumn('workshop_promo_activations', 'sales_lead_id')) {
                $activation->update(['sales_lead_id' => $lead->id]);
            }

            $followUpLabel = $followUpAt->locale('ar')->translatedFormat('l j F Y');
            $body = trim(
                'متابعة مجدولة يوم '.$followUpLabel.'.'
                .($promo ? "\nالكود: {$promo->code}" : '')
                .($taskNotes ? "\n".$taskNotes : '')
            );

            SalesActivity::create([
                'sales_lead_id' => $lead->id,
                'user_id' => auth()->id(),
                'type' => 'follow_up',
                'title' => 'متابعة — كود ورشة',
                'body' => $body,
            ]);

            SalesAuditService::log(
                $previousAssignee && (int) $previousAssignee !== $assigneeId
                    ? 'sales_lead_reassigned'
                    : 'sales_lead_created_admin',
                $lead,
                $previousAssignee ? ['assigned_to' => $previousAssignee] : null,
                ['assigned_to' => $assigneeId, 'next_follow_up_at' => $followUpAt->toDateTimeString()],
                'إسناد من كود ورشة — '.$lead->name
            );

            app(SalesNotificationService::class)->notifyLeadAssigned($lead, $previousAssignee);

            return $lead->load('assignee');
        });
    }

    private function buildActivationNotes(WorkshopPromoActivation $activation, ?WorkshopPromoCode $promo): string
    {
        $workshopTitle = $promo?->workshop?->title ?? '—';

        return trim(
            "تم الإسناد من أكواد خصم الورش.\n"
            ."[workshop_promo_activation:{$activation->id}]\n"
            .'الكود: '.($promo?->code ?? '—')."\n"
            ."الورشة: {$workshopTitle}\n"
            .'حالة التفعيل: '.$activation->status."\n"
            .'تاريخ التفعيل: '.optional($activation->activated_at)->format('Y-m-d H:i')
        );
    }

    private function findLeadByUserContact(User $user): ?SalesLead
    {
        $email = $user->email ? strtolower(trim($user->email)) : null;
        $phoneVariants = $this->phoneMatchVariants($user->phone);

        if (! $email && $phoneVariants === []) {
            return null;
        }

        return SalesLead::query()
            ->with('assignee')
            ->where(function ($q) use ($email, $phoneVariants) {
                if ($email) {
                    $q->whereRaw('LOWER(TRIM(email)) = ?', [$email]);
                }
                if ($phoneVariants !== []) {
                    $q->orWhereIn('phone', $phoneVariants);
                }
            })
            ->orderByDesc('id')
            ->first();
    }

    /**
     * @return list<string>
     */
    private function phoneMatchVariants(?string $phone): array
    {
        if ($phone === null || trim($phone) === '') {
            return [];
        }

        $digits = preg_replace('/[^0-9]/', '', $phone) ?? '';
        if ($digits === '') {
            return [];
        }

        $variants = array_unique(array_filter([
            $phone,
            $digits,
            ltrim($digits, '0'),
            str_starts_with($digits, '20') ? '0'.substr($digits, 2) : null,
            str_starts_with($digits, '20') ? '+'.$digits : null,
            ! str_starts_with($digits, '20') ? '20'.$digits : null,
        ]));

        return array_values($variants);
    }
}
