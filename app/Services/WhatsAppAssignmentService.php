<?php

namespace App\Services;

use App\Models\User;
use App\Models\WhatsAppConversation;
use Illuminate\Support\Facades\Cache;

class WhatsAppAssignmentService
{
    /**
     * @return array<int, User>
     */
    public function eligibleAgents(?string $department = null): array
    {
        return User::query()
            ->where(function ($q) {
                $q->whereIn('role', ['admin', 'super_admin'])
                    ->orWhere(function ($q2) {
                        $q2->where('is_employee', true)
                            ->whereHas('employeeJob', fn ($j) => $j->where('code', 'sales'));
                    });
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'role'])
            ->all();
    }

    /**
     * @return array<int, User>
     */
    public function eligibleSalesStaff(): array
    {
        return User::query()
            ->where('is_active', true)
            ->where('is_employee', true)
            ->whereHas('employeeJob', fn ($j) => $j->whereIn('code', ['sales', 'sales_manager']))
            ->orderBy('name')
            ->get(['id', 'name', 'role'])
            ->all();
    }

    /**
     * مدراء المبيعات فقط (لتبليغات طابور طلبات واتساب).
     *
     * @return array<int, User>
     */
    public function eligibleSalesManagers(): array
    {
        return User::query()
            ->where('is_active', true)
            ->salesManagers()
            ->orderBy('name')
            ->get(['id', 'name', 'role'])
            ->all();
    }

    public function autoAssign(WhatsAppConversation $conversation): ?WhatsAppConversation
    {
        if ($conversation->assigned_to) {
            return $conversation;
        }

        if (config('whatsapp.assignment.strategy') === 'manual_queue') {
            return $conversation;
        }

        $strategy = (string) config('whatsapp.assignment.strategy', 'lead_owner_then_round_robin');

        if ($strategy === 'lead_owner_then_round_robin' && $conversation->sales_lead_id) {
            $lead = $conversation->salesLead;
            if ($lead?->assigned_to) {
                return app(WhatsAppCrmService::class)->assign($conversation, (int) $lead->assigned_to);
            }
        }

        $agents = $this->eligibleAgents($conversation->department);
        if ($agents === []) {
            return $conversation;
        }

        $index = (int) Cache::get('whatsapp_assignment_rr_index', 0);
        $agent = $agents[$index % count($agents)];

        Cache::forever('whatsapp_assignment_rr_index', ($index + 1) % count($agents));

        return app(WhatsAppCrmService::class)->assign($conversation, $agent->id);
    }
}
