<?php

namespace App\Domains\Sales\Http\Controllers\Crm;

use App\Domains\Messaging\Models\MetaSocialConversation;
use App\Domains\Messaging\Models\WhatsAppConversation;
use App\Domains\Sales\Support\CrmScope;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/**
 * صندوق المحادثات الموحّد في CRM: واتساب و Messenger و Instagram في قائمة واحدة مرتبة بآخر رسالة.
 * القراءة والرد بيفضلوا في صناديق الوارد الحالية (زرار «فتح»)؛ هنا القائمة والربط بالعميل.
 * النطاق: الموظف محادثاته (مكلَّف بيها أو عميلها بتاعه)، المدير فريقه، الأدمن الكل.
 */
class SalesCrmInboxController extends Controller
{
    use InteractsWithCrm;

    public const QUICK = [
        'all' => 'الكل',
        'unread' => 'غير مقروءة',
        'open' => 'مفتوحة',
        'no_lead' => 'من غير عميل',
        'unassigned' => 'من غير موظف',
    ];

    public const CHANNELS = ['all' => 'كل القنوات', 'whatsapp' => 'واتساب', 'messenger' => 'Messenger', 'instagram' => 'Instagram'];

    private const PER_PAGE = 25;

    public function index(Request $request): View
    {
        $scope = $this->scope($request);
        $f = [
            'quick' => array_key_exists((string) $request->query('quick'), self::QUICK) ? (string) $request->query('quick') : 'all',
            'channel' => array_key_exists((string) $request->query('channel'), self::CHANNELS) ? (string) $request->query('channel') : 'all',
            'q' => trim((string) $request->query('q', '')),
            'assigned_to' => $scope->isRep() ? null : ($request->integer('assigned_to') ?: null),
        ];
        if ($f['quick'] === 'unassigned' && ! $this->canSeeUnassigned($scope)) {
            $f['quick'] = 'all';
        }

        $page = max(1, $request->integer('page', 1));
        $take = $page * self::PER_PAGE;

        $wa = $this->whatsapp($scope, $f);
        $meta = $this->meta($scope, $f);
        $total = ($wa ? (clone $wa)->count() : 0) + ($meta ? (clone $meta)->count() : 0);

        $rows = collect()
            ->concat($wa ? $wa->with(['assignee:id,name', 'salesLead:id,name,assigned_to'])->orderByDesc('last_message_at')->limit($take)->get()->map(fn ($c) => $this->waRow($scope, $c)) : [])
            ->concat($meta ? $meta->with(['assignee:id,name', 'salesLead:id,name,assigned_to'])->orderByDesc('last_message_at')->limit($take)->get()->map(fn ($c) => $this->metaRow($scope, $c)) : [])
            ->sortByDesc(fn ($r) => $r['at']?->timestamp ?? 0)
            ->slice(($page - 1) * self::PER_PAGE, self::PER_PAGE)->values();

        $counts = [];
        foreach (array_keys(self::QUICK) as $k) {
            if ($k === 'unassigned' && ! $this->canSeeUnassigned($scope)) {
                continue;
            }
            $w = $this->whatsapp($scope, ['quick' => $k] + $f);
            $m = $this->meta($scope, ['quick' => $k] + $f);
            $counts[$k] = ($w ? $w->count() : 0) + ($m ? $m->count() : 0);
        }

        return $this->view($scope, 'inbox', 'inbox', [
            'rows' => new LengthAwarePaginator($rows, $total, self::PER_PAGE, $page, ['path' => $request->url(), 'query' => $request->except('page')]),
            'filters' => $f,
            'counts' => $counts,
            'members' => $scope->isRep() ? collect() : $scope->members(),
            'classic' => $this->classicLinks($scope),
        ]);
    }

    private function canSeeUnassigned(CrmScope $scope): bool
    {
        return ! $scope->isRep() && $scope->can('inbox.unassigned');
    }

    private function whatsapp(CrmScope $scope, array $f): ?Builder
    {
        if (! in_array($f['channel'], ['all', 'whatsapp'], true) || ! Schema::hasTable((new WhatsAppConversation)->getTable())) {
            return null;
        }

        $q = $this->scoped(WhatsAppConversation::query(), $scope, $f);
        if ($f['q'] !== '') {
            $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $f['q']).'%';
            $q->where(fn ($w) => $w->where('contact_name', 'like', $like)->orWhere('phone_number', 'like', $like)->orWhereHas('salesLead', fn ($l) => $l->where('name', 'like', $like)));
        }
        if ($f['quick'] === 'open') {
            $q->whereIn('status', [WhatsAppConversation::STATUS_OPEN, WhatsAppConversation::STATUS_PENDING, WhatsAppConversation::STATUS_WAITING_CUSTOMER]);
        }

        return $q;
    }

    private function meta(CrmScope $scope, array $f): ?Builder
    {
        if (! in_array($f['channel'], ['all', 'messenger', 'instagram'], true) || ! Schema::hasTable((new MetaSocialConversation)->getTable())) {
            return null;
        }

        $q = $this->scoped(MetaSocialConversation::query(), $scope, $f);
        if ($f['channel'] !== 'all') {
            $q->where('platform', $f['channel']);
        }
        if ($f['q'] !== '') {
            $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $f['q']).'%';
            $q->where(fn ($w) => $w->where('participant_name', 'like', $like)->orWhere('participant_username', 'like', $like)->orWhere('phone', 'like', $like)->orWhereHas('salesLead', fn ($l) => $l->where('name', 'like', $like)));
        }
        if ($f['quick'] === 'open') {
            $q->where('status', MetaSocialConversation::STATUS_OPEN);
        }

        return $q;
    }

    /** نفس قاعدة صناديق الوارد: المحادثة تخص الموظف لو مكلَّف بيها أو العميل المربوط بيها بتاعه. */
    private function scoped(Builder $q, CrmScope $scope, array $f): Builder
    {
        $ids = $f['assigned_to'] ? [$f['assigned_to']] : $scope->assigneeIds;

        if ($f['quick'] === 'unassigned') {
            $q->whereNull('assigned_to');
            if ($scope->isManager()) {
                // طابور الفريق: من غير موظف، ومش مربوطة بعميل لموظف برة الفريق
                $q->where(fn ($w) => $w->whereNull('sales_lead_id')->orWhereHas('salesLead', fn ($l) => $l->whereIn('assigned_to', $scope->assigneeIds ?: [0])));
            }
        } elseif ($ids !== null) {
            $ids = $ids === [] ? [0] : $ids;
            $q->where(fn ($w) => $w->whereIn('assigned_to', $ids)->orWhereHas('salesLead', fn ($l) => $l->whereIn('assigned_to', $ids)));
        }

        match ($f['quick']) {
            'unread' => $q->where('unread_count', '>', 0),
            'no_lead' => $q->whereNull('sales_lead_id'),
            default => null,
        };

        return $q;
    }

    private function waRow(CrmScope $scope, WhatsAppConversation $c): array
    {
        $route = match (true) {
            $scope->isAdmin() => 'admin.whatsapp.inbox.conversation',
            $scope->isManager() => 'employee.sales-manager.whatsapp.inbox.conversation',
            default => 'employee.sales.whatsapp.inbox.conversation',
        };

        return [
            'channel' => 'واتساب', 'icon' => 'fab fa-whatsapp', 'color' => '#128C7E',
            'name' => $c->displayName(), 'contact' => $c->phone_number,
            'preview' => $c->last_message_preview, 'incoming' => $c->last_message_direction !== 'outbound',
            'at' => $c->last_message_at, 'unread' => (int) $c->unread_count,
            'status' => WhatsAppConversation::STATUSES[$c->status] ?? $c->status,
            'assignee' => $c->assignee?->name,
            'lead' => $c->salesLead && $scope->canSee($c->salesLead) ? $c->salesLead : null,
            'url' => Route::has($route) ? route($route, $c->id) : null,
        ];
    }

    private function metaRow(CrmScope $scope, MetaSocialConversation $c): array
    {
        $route = $scope->isAdmin() ? 'admin.meta-social.inbox.index' : 'employee.sales.meta-social.inbox.index';
        $ig = $c->platform === MetaSocialConversation::PLATFORM_INSTAGRAM;

        return [
            'channel' => $ig ? 'Instagram' : 'Messenger', 'icon' => $ig ? 'fab fa-instagram' : 'fab fa-facebook-messenger', 'color' => $ig ? '#C13584' : '#0084FF',
            'name' => $c->displayName(), 'contact' => $c->phone ?: ($c->participant_username ? '@'.$c->participant_username : null),
            'preview' => $c->last_message_preview, 'incoming' => true,
            'at' => $c->last_message_at, 'unread' => (int) $c->unread_count,
            'status' => $c->status === MetaSocialConversation::STATUS_CLOSED ? 'مقفولة' : 'مفتوحة',
            'assignee' => $c->assignee?->name,
            'lead' => $c->salesLead && $scope->canSee($c->salesLead) ? $c->salesLead : null,
            'url' => Route::has($route) ? route($route, ['conversation' => $c->id]) : null,
        ];
    }

    /** @return list<array{label: string, url: string}> */
    private function classicLinks(CrmScope $scope): array
    {
        $links = match (true) {
            $scope->isRep() => [['صندوق واتساب', 'employee.sales.whatsapp.inbox.index'], ['Messenger و Instagram', 'employee.sales.meta-social.inbox.index']],
            $scope->isManager() => [['واتساب الفريق', 'employee.sales-manager.whatsapp.inbox.index'], ['طلبات جديدة بدون موظف', 'employee.sales-manager.whatsapp.queue.index'], ['Messenger و Instagram', 'employee.sales.meta-social.inbox.index']],
            default => [['صندوق واتساب', 'admin.whatsapp.inbox'], ['Messenger و Instagram', 'admin.meta-social.inbox.index']],
        };

        return array_values(array_map(fn ($l) => ['label' => $l[0], 'url' => route($l[1])], array_filter($links, fn ($l) => Route::has($l[1]))));
    }
}
