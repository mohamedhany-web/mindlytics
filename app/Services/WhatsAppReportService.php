<?php

namespace App\Services;

use App\Models\SalesLead;
use App\Models\User;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppConversationEvent;
use App\Models\WhatsAppConversationMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WhatsAppReportService
{
    /**
     * @return array<string, mixed>
     */
    public function dashboard(?Carbon $from = null, ?Carbon $to = null): array
    {
        $from ??= now()->subDays(30)->startOfDay();
        $to ??= now()->endOfDay();

        if (! Schema::hasTable('whatsapp_conversations')) {
            return ['ready' => false];
        }

        $conversations = WhatsAppConversation::query()->whereBetween('created_at', [$from, $to]);
        $messages = WhatsAppConversationMessage::query()->whereBetween('created_at', [$from, $to]);

        $byStatus = WhatsAppConversation::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        $byAgent = WhatsAppConversation::query()
            ->whereNotNull('assigned_to')
            ->select('assigned_to', DB::raw('count(*) as total'))
            ->groupBy('assigned_to')
            ->with('assignee:id,name')
            ->get()
            ->map(fn ($row) => [
                'id' => $row->assigned_to,
                'name' => $row->assignee?->name ?? '—',
                'total' => (int) $row->total,
            ])
            ->sortByDesc('total')
            ->values()
            ->all();

        $inbound = (clone $messages)->where('direction', 'inbound')->count();
        $outbound = (clone $messages)->where('direction', 'outbound')->count();

        $linkedLeads = WhatsAppConversation::query()->whereNotNull('sales_lead_id')->count();
        $wonLinked = WhatsAppConversation::query()
            ->whereNotNull('sales_lead_id')
            ->whereHas('salesLead', fn ($q) => $q->where('stage', SalesLead::WON_STAGE))
            ->count();

        $dailyVolume = WhatsAppConversationMessage::query()
            ->whereBetween('created_at', [$from, $to])
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('count(*) as total'))
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day')
            ->all();

        $topTags = DB::table('whatsapp_conversation_tag')
            ->join('whatsapp_tags', 'whatsapp_tags.id', '=', 'whatsapp_conversation_tag.tag_id')
            ->select('whatsapp_tags.name', DB::raw('count(*) as total'))
            ->groupBy('whatsapp_tags.name')
            ->orderByDesc('total')
            ->limit(8)
            ->get()
            ->map(fn ($r) => ['name' => $r->name, 'total' => (int) $r->total])
            ->all();

        $pipelineBreakdown = WhatsAppConversation::query()
            ->whereNotNull('sales_lead_id')
            ->join('sales_leads', 'sales_leads.id', '=', 'whatsapp_conversations.sales_lead_id')
            ->select('sales_leads.stage', DB::raw('count(*) as total'))
            ->groupBy('sales_leads.stage')
            ->pluck('total', 'stage')
            ->all();

        $agents = User::query()
            ->whereIn('id', WhatsAppConversation::query()->whereNotNull('assigned_to')->distinct()->pluck('assigned_to'))
            ->get(['id', 'name']);

        $agentPerformance = $agents->map(function (User $user) use ($from, $to) {
            $convIds = WhatsAppConversation::query()->where('assigned_to', $user->id)->pluck('id');

            return [
                'id' => $user->id,
                'name' => $user->name,
                'conversations' => $convIds->count(),
                'replies' => WhatsAppConversationMessage::query()
                    ->whereIn('conversation_id', $convIds)
                    ->where('direction', 'outbound')
                    ->where('sent_by_user_id', $user->id)
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),
                'open' => WhatsAppConversation::query()
                    ->where('assigned_to', $user->id)
                    ->whereIn('status', ['open', 'pending', 'waiting_customer'])
                    ->count(),
            ];
        })->sortByDesc('replies')->values()->all();

        return [
            'ready' => true,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'totals' => [
                'conversations' => WhatsAppConversation::query()->count(),
                'new_in_period' => (clone $conversations)->count(),
                'messages_in_period' => (clone $messages)->count(),
                'inbound' => $inbound,
                'outbound' => $outbound,
                'unread' => (int) WhatsAppConversation::query()->sum('unread_count'),
                'linked_leads' => $linkedLeads,
                'won_from_whatsapp' => $wonLinked,
            ],
            'by_status' => collect($byStatus)->map(fn ($t, $s) => [
                'status' => $s,
                'label' => WhatsAppConversation::statusLabel($s),
                'total' => (int) $t,
            ])->values()->all(),
            'by_agent' => $byAgent,
            'agent_performance' => $agentPerformance,
            'daily_volume' => $dailyVolume,
            'top_tags' => $topTags,
            'pipeline_breakdown' => collect($pipelineBreakdown)->map(fn ($t, $stage) => [
                'stage' => $stage,
                'label' => SalesLead::STAGES[$stage] ?? $stage,
                'total' => (int) $t,
            ])->values()->all(),
            'recent_events' => WhatsAppConversationEvent::query()
                ->with('performer:id,name')
                ->latest('created_at')
                ->limit(15)
                ->get()
                ->map(fn ($e) => [
                    'title' => $e->title,
                    'description' => $e->description,
                    'performer' => $e->performer?->name,
                    'at' => $e->created_at?->diffForHumans(),
                ])
                ->all(),
        ];
    }
}
