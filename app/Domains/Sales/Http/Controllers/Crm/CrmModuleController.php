<?php

namespace App\Domains\Sales\Http\Controllers\Crm;

use App\Domains\Accounting\Models\Order;
use App\Domains\Sales\Http\Controllers\Crm\Concerns\InteractsWithCrm;
use App\Domains\Sales\Models\CrmAccount;
use App\Domains\Sales\Models\CrmCampaign;
use App\Domains\Sales\Models\CrmCase;
use App\Domains\Sales\Models\CrmContact;
use App\Domains\Sales\Models\CrmDocument;
use App\Domains\Sales\Models\SalesActivity;
use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Support\CrmScope;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * وحدات SuiteCRM الجديدة داخل CRM المبيعات (Accounts…Admin).
 */
class CrmModuleController extends Controller
{
    use InteractsWithCrm;

    // ─── Accounts ─────────────────────────────────────────────

    public function accountsIndex(Request $request): View
    {
        $scope = $this->scope($request);
        $q = trim((string) $request->query('q', ''));
        $rows = $scope->accounts()
            ->with('assignee:id,name')
            ->when($q !== '', fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
            }))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return $this->crmView($scope, 'accounts', 'accounts.index', compact('rows', 'q'));
    }

    public function accountsCreate(Request $request): View
    {
        $scope = $this->scope($request);

        return $this->crmView($scope, 'accounts', 'accounts.form', [
            'account' => new CrmAccount(['assigned_to' => $scope->user->id]),
            'members' => $scope->members(),
        ]);
    }

    public function accountsStore(Request $request): RedirectResponse
    {
        $scope = $this->scope($request);
        $data = $this->validateAccount($request, $scope);
        $data['created_by'] = $scope->user->id;
        $account = CrmAccount::create($data);

        return redirect()->route($scope->route('accounts.show'), $account)->with('success', 'تم إنشاء الحساب.');
    }

    public function accountsShow(Request $request, CrmAccount $account): View
    {
        $scope = $this->scope($request);
        abort_unless($scope->canSeeAssigned($account->assigned_to), 404);
        $account->load(['assignee:id,name', 'contacts' => fn ($q) => $q->latest('id')->limit(20), 'cases' => fn ($q) => $q->latest('id')->limit(10)]);
        $leads = Schema::hasColumn('sales_leads', 'crm_account_id')
            ? $scope->leads()->where('crm_account_id', $account->id)->latest('id')->limit(15)->get()
            : collect();

        return $this->crmView($scope, 'accounts', 'accounts.show', compact('account', 'leads'));
    }

    public function accountsEdit(Request $request, CrmAccount $account): View
    {
        $scope = $this->scope($request);
        abort_unless($scope->canSeeAssigned($account->assigned_to), 404);

        return $this->crmView($scope, 'accounts', 'accounts.form', [
            'account' => $account,
            'members' => $scope->members(),
        ]);
    }

    public function accountsUpdate(Request $request, CrmAccount $account): RedirectResponse
    {
        $scope = $this->scope($request);
        abort_unless($scope->canSeeAssigned($account->assigned_to), 404);
        $account->update($this->validateAccount($request, $scope));

        return redirect()->route($scope->route('accounts.show'), $account)->with('success', 'تم تحديث الحساب.');
    }

    // ─── Contacts ─────────────────────────────────────────────

    public function contactsIndex(Request $request): View
    {
        $scope = $this->scope($request);
        $q = trim((string) $request->query('q', ''));
        $rows = $scope->contacts()
            ->with(['assignee:id,name', 'account:id,name'])
            ->when($q !== '', fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
            }))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return $this->crmView($scope, 'contacts', 'contacts.index', compact('rows', 'q'));
    }

    public function contactsCreate(Request $request): View
    {
        $scope = $this->scope($request);

        return $this->crmView($scope, 'contacts', 'contacts.form', [
            'contact' => new CrmContact(['assigned_to' => $scope->user->id]),
            'accounts' => $scope->accounts()->orderBy('name')->limit(200)->get(['id', 'name']),
            'members' => $scope->members(),
        ]);
    }

    public function contactsStore(Request $request): RedirectResponse
    {
        $scope = $this->scope($request);
        $data = $this->validateContact($request, $scope);
        $data['created_by'] = $scope->user->id;
        $contact = CrmContact::create($data);

        return redirect()->route($scope->route('contacts.show'), $contact)->with('success', 'تم إنشاء جهة الاتصال.');
    }

    public function contactsShow(Request $request, CrmContact $contact): View
    {
        $scope = $this->scope($request);
        abort_unless($scope->canSeeAssigned($contact->assigned_to), 404);
        $contact->load(['assignee:id,name', 'account:id,name', 'lead:id,name', 'cases' => fn ($q) => $q->latest('id')->limit(10)]);

        return $this->crmView($scope, 'contacts', 'contacts.show', compact('contact'));
    }

    public function contactsEdit(Request $request, CrmContact $contact): View
    {
        $scope = $this->scope($request);
        abort_unless($scope->canSeeAssigned($contact->assigned_to), 404);

        return $this->crmView($scope, 'contacts', 'contacts.form', [
            'contact' => $contact,
            'accounts' => $scope->accounts()->orderBy('name')->limit(200)->get(['id', 'name']),
            'members' => $scope->members(),
        ]);
    }

    public function contactsUpdate(Request $request, CrmContact $contact): RedirectResponse
    {
        $scope = $this->scope($request);
        abort_unless($scope->canSeeAssigned($contact->assigned_to), 404);
        $contact->update($this->validateContact($request, $scope));

        return redirect()->route($scope->route('contacts.show'), $contact)->with('success', 'تم تحديث جهة الاتصال.');
    }

    // ─── Activities (calls/meetings/tasks/notes/emails) ────────

    public function activitiesIndex(Request $request, ?string $type = null): View
    {
        $scope = $this->scope($request);
        $type = $type ?: $request->query('type');
        $allowed = array_keys(SalesActivity::TYPES);
        if ($type && ! in_array($type, $allowed, true)) {
            $type = null;
        }

        $rows = $scope->activities()
            ->with(['lead:id,name', 'user:id,name'])
            ->when($type, fn ($q) => $q->where('type', $type))
            ->latest('id')
            ->paginate(30)
            ->withQueryString();

        $module = match ($type) {
            'call' => 'activities',
            'meeting' => 'activities',
            'follow_up' => 'activities',
            'note' => 'activities',
            'email' => 'activities',
            default => 'activities',
        };

        return $this->crmView($scope, $module, 'activities.index', [
            'rows' => $rows,
            'type' => $type,
            'title' => $type ? (SalesActivity::TYPES[$type] ?? 'الأنشطة') : 'الأنشطة',
        ]);
    }

    public function activitiesCreate(Request $request): View
    {
        $scope = $this->scope($request);

        return $this->crmView($scope, 'activities', 'activities.form', [
            'activity' => new SalesActivity([
                'type' => $request->query('type', 'call'),
                'status' => 'planned',
                'user_id' => $scope->user->id,
                'scheduled_at' => now()->addHour(),
            ]),
            'leads' => $scope->leads()->orderBy('name')->limit(200)->get(['id', 'name']),
        ]);
    }

    public function activitiesStore(Request $request): RedirectResponse
    {
        $scope = $this->scope($request);
        $data = $request->validate([
            'type' => ['required', Rule::in(array_keys(SalesActivity::TYPES))],
            'title' => 'nullable|string|max:255',
            'body' => 'nullable|string|max:5000',
            'status' => ['nullable', Rule::in(array_keys(SalesActivity::STATUSES))],
            'direction' => 'nullable|string|max:20',
            'scheduled_at' => 'nullable|date',
            'due_at' => 'nullable|date',
            'sales_lead_id' => 'nullable|integer',
            'outcome' => 'nullable|string|max:40',
        ]);

        if (! empty($data['sales_lead_id'])) {
            $lead = SalesLead::find($data['sales_lead_id']);
            abort_unless($lead && $scope->canSee($lead), 403);
        }

        $data['user_id'] = $scope->user->id;
        $data['status'] = $data['status'] ?? 'planned';
        $activity = SalesActivity::create($data);

        return redirect()->route($scope->route('activities.show'), $activity)->with('success', 'تم تسجيل النشاط.');
    }

    public function activitiesShow(Request $request, SalesActivity $activity): View
    {
        $scope = $this->scope($request);
        $activity->load(['lead:id,name', 'user:id,name']);
        abort_unless(
            $scope->assigneeIds === null
            || in_array((int) $activity->user_id, $scope->assigneeIds, true)
            || ($activity->lead && $scope->canSee($activity->lead)),
            404
        );

        return $this->crmView($scope, 'activities', 'activities.show', compact('activity'));
    }

    // ─── Calendar ─────────────────────────────────────────────

    public function calendar(Request $request): View
    {
        $scope = $this->scope($request);
        $from = now()->startOfDay();
        $to = now()->addDays(14)->endOfDay();

        $followUps = $scope->leads()->openPipeline()
            ->whereBetween('next_follow_up_at', [$from, $to])
            ->with('assignee:id,name')
            ->orderBy('next_follow_up_at')
            ->limit(100)
            ->get();

        $activities = $scope->activities()
            ->where(function ($q) use ($from, $to) {
                $q->whereBetween('scheduled_at', [$from, $to])
                    ->orWhereBetween('due_at', [$from, $to]);
            })
            ->with(['lead:id,name', 'user:id,name'])
            ->orderByRaw('COALESCE(scheduled_at, due_at)')
            ->limit(100)
            ->get();

        return $this->crmView($scope, 'calendar', 'calendar.index', compact('followUps', 'activities', 'from', 'to'));
    }

    // ─── Campaigns ────────────────────────────────────────────

    public function campaignsIndex(Request $request): View
    {
        $scope = $this->scope($request);
        $rows = $scope->campaigns()->with('assignee:id,name')->latest('id')->paginate(25)->withQueryString();

        return $this->crmView($scope, 'campaigns', 'campaigns.index', compact('rows'));
    }

    public function campaignsCreate(Request $request): View
    {
        $scope = $this->scope($request);

        return $this->crmView($scope, 'campaigns', 'campaigns.form', [
            'campaign' => new CrmCampaign(['status' => 'planning', 'assigned_to' => $scope->user->id]),
            'members' => $scope->members(),
        ]);
    }

    public function campaignsStore(Request $request): RedirectResponse
    {
        $scope = $this->scope($request);
        $data = $this->validateCampaign($request, $scope);
        $data['created_by'] = $scope->user->id;
        $campaign = CrmCampaign::create($data);

        return redirect()->route($scope->route('campaigns.show'), $campaign)->with('success', 'تم إنشاء الحملة.');
    }

    public function campaignsShow(Request $request, CrmCampaign $campaign): View
    {
        $scope = $this->scope($request);
        abort_unless($scope->canSeeAssigned($campaign->assigned_to), 404);
        $campaign->load('assignee:id,name');
        $leads = Schema::hasColumn('sales_leads', 'crm_campaign_id')
            ? $scope->leads()->where('crm_campaign_id', $campaign->id)->latest('id')->limit(30)->get()
            : collect();

        return $this->crmView($scope, 'campaigns', 'campaigns.show', compact('campaign', 'leads'));
    }

    public function campaignsEdit(Request $request, CrmCampaign $campaign): View
    {
        $scope = $this->scope($request);
        abort_unless($scope->canSeeAssigned($campaign->assigned_to), 404);

        return $this->crmView($scope, 'campaigns', 'campaigns.form', [
            'campaign' => $campaign,
            'members' => $scope->members(),
        ]);
    }

    public function campaignsUpdate(Request $request, CrmCampaign $campaign): RedirectResponse
    {
        $scope = $this->scope($request);
        abort_unless($scope->canSeeAssigned($campaign->assigned_to), 404);
        $campaign->update($this->validateCampaign($request, $scope));

        return redirect()->route($scope->route('campaigns.show'), $campaign)->with('success', 'تم تحديث الحملة.');
    }

    // ─── Cases ────────────────────────────────────────────────

    public function casesIndex(Request $request): View
    {
        $scope = $this->scope($request);
        $status = $request->query('status');
        $rows = $scope->cases()
            ->with(['assignee:id,name', 'lead:id,name', 'contact:id,name'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return $this->crmView($scope, 'cases', 'cases.index', compact('rows', 'status'));
    }

    public function casesCreate(Request $request): View
    {
        $scope = $this->scope($request);

        return $this->crmView($scope, 'cases', 'cases.form', [
            'case' => new CrmCase([
                'status' => 'new',
                'priority' => 'medium',
                'assigned_to' => $scope->user->id,
                'sales_lead_id' => $request->integer('sales_lead_id') ?: null,
            ]),
            'members' => $scope->members(),
            'leads' => $scope->leads()->orderBy('name')->limit(150)->get(['id', 'name']),
            'contacts' => $scope->contacts()->orderBy('name')->limit(150)->get(['id', 'name']),
            'accounts' => $scope->accounts()->orderBy('name')->limit(150)->get(['id', 'name']),
        ]);
    }

    public function casesStore(Request $request): RedirectResponse
    {
        $scope = $this->scope($request);
        $data = $this->validateCase($request, $scope);
        $data['created_by'] = $scope->user->id;
        $case = CrmCase::create($data);

        return redirect()->route($scope->route('cases.show'), $case)->with('success', 'تم فتح التذكرة.');
    }

    public function casesShow(Request $request, CrmCase $case): View
    {
        $scope = $this->scope($request);
        abort_unless($scope->canSeeAssigned($case->assigned_to), 404);
        $case->load(['assignee:id,name', 'lead:id,name', 'contact:id,name', 'account:id,name']);

        return $this->crmView($scope, 'cases', 'cases.show', ['caseRow' => $case]);
    }

    public function casesEdit(Request $request, CrmCase $case): View
    {
        $scope = $this->scope($request);
        abort_unless($scope->canSeeAssigned($case->assigned_to), 404);

        return $this->crmView($scope, 'cases', 'cases.form', [
            'case' => $case,
            'members' => $scope->members(),
            'leads' => $scope->leads()->orderBy('name')->limit(150)->get(['id', 'name']),
            'contacts' => $scope->contacts()->orderBy('name')->limit(150)->get(['id', 'name']),
            'accounts' => $scope->accounts()->orderBy('name')->limit(150)->get(['id', 'name']),
        ]);
    }

    public function casesUpdate(Request $request, CrmCase $case): RedirectResponse
    {
        $scope = $this->scope($request);
        abort_unless($scope->canSeeAssigned($case->assigned_to), 404);
        $data = $this->validateCase($request, $scope);
        if (($data['status'] ?? null) === 'closed' && ! $case->closed_at) {
            $data['closed_at'] = now();
        }
        if (($data['status'] ?? null) !== 'closed') {
            $data['closed_at'] = null;
        }
        $case->update($data);

        return redirect()->route($scope->route('cases.show'), $case)->with('success', 'تم تحديث التذكرة.');
    }

    // ─── Documents ────────────────────────────────────────────

    public function documentsIndex(Request $request): View
    {
        $scope = $this->scope($request);
        $rows = $scope->documents()->with('assignee:id,name')->latest('id')->paginate(25)->withQueryString();

        return $this->crmView($scope, 'documents', 'documents.index', compact('rows'));
    }

    public function documentsCreate(Request $request): View
    {
        $scope = $this->scope($request);

        $leadId = $request->integer('sales_lead_id') ?: $request->integer('parent_id') ?: null;

        return $this->crmView($scope, 'documents', 'documents.form', [
            'document' => new CrmDocument(['assigned_to' => $scope->user->id]),
            'members' => $scope->members(),
            'leads' => $scope->leads()->orderBy('name')->limit(150)->get(['id', 'name']),
            'selectedLeadId' => $leadId,
        ]);
    }

    public function documentsStore(Request $request): RedirectResponse
    {
        $scope = $this->scope($request);
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:80',
            'description' => 'nullable|string|max:2000',
            'assigned_to' => 'nullable|integer',
            'sales_lead_id' => 'nullable|integer',
            'file' => 'required|file|max:20480',
        ]);
        abort_unless($this->canAssignTo($scope, isset($data['assigned_to']) ? (int) $data['assigned_to'] : null), 403);

        $file = $request->file('file');
        $path = $file->store('crm-documents/'.now()->format('Y/m'), 'public');

        $doc = CrmDocument::create([
            'title' => $data['title'],
            'category' => $data['category'] ?? null,
            'description' => $data['description'] ?? null,
            'disk' => 'public',
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'parent_type' => ! empty($data['sales_lead_id']) ? SalesLead::class : null,
            'parent_id' => $data['sales_lead_id'] ?? null,
            'assigned_to' => $data['assigned_to'] ?? $scope->user->id,
            'created_by' => $scope->user->id,
        ]);

        return redirect()->route($scope->route('documents.show'), $doc)->with('success', 'تم رفع المستند.');
    }

    public function documentsShow(Request $request, CrmDocument $document): View
    {
        $scope = $this->scope($request);
        abort_unless($scope->canSeeAssigned($document->assigned_to), 404);
        $document->load('assignee:id,name');

        return $this->crmView($scope, 'documents', 'documents.show', compact('document'));
    }

    // ─── Quotes (Orders) ──────────────────────────────────────

    public function quotesIndex(Request $request): View
    {
        $scope = $this->scope($request);
        $q = trim((string) $request->query('q', ''));

        $leadIds = $scope->leads()->whereNotNull('order_id')->pluck('order_id', 'id');
        $rows = Order::query()
            ->with(['user:id,name', 'course:id,title'])
            ->when($scope->assigneeIds !== null, function ($query) use ($scope) {
                $assignees = $scope->assigneeIds === [] ? [0] : $scope->assigneeIds;
                $query->whereIn('id', function ($sub) use ($assignees) {
                    $sub->select('order_id')->from('sales_leads')
                        ->whereNotNull('order_id')
                        ->whereIn('assigned_to', $assignees);
                });
            })
            ->when($q !== '', fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('order_number', 'like', "%{$q}%")->orWhere('id', $q);
            }))
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $leadByOrder = $scope->leads()->whereIn('order_id', $rows->pluck('id'))->get(['id', 'name', 'order_id'])->keyBy('order_id');

        return $this->crmView($scope, 'quotes', 'quotes.index', compact('rows', 'q', 'leadByOrder'));
    }

    public function quotesShow(Request $request, Order $order): View
    {
        $scope = $this->scope($request);
        $lead = $scope->leads()->where('order_id', $order->id)->first();
        abort_unless($lead || $scope->isAdmin(), 404);
        $order->load(['user:id,name', 'course:id,title', 'payment', 'invoice']);

        return $this->crmView($scope, 'quotes', 'quotes.show', compact('order', 'lead'));
    }

    // ─── Reports hub ──────────────────────────────────────────

    public function reportsIndex(Request $request): View
    {
        $scope = $this->scope($request);
        $links = $this->reportLinks($scope);

        return $this->crmView($scope, 'reports', 'reports.index', compact('links'));
    }

    public function reportsKpi(Request $request): View
    {
        $scope = $this->scope($request);
        $leads = $scope->leads();
        $open = (clone $leads)->openPipeline()->count();
        $today = (clone $leads)->openPipeline()->whereBetween('next_follow_up_at', [today(), today()->endOfDay()])->count();
        $overdue = (clone $leads)->openPipeline()->where('next_follow_up_at', '<', now())->count();
        $wonMonth = (clone $leads)->whereNotNull('won_confirmed_at')->where('won_confirmed_at', '>=', now()->startOfMonth())->count();

        return $this->crmView($scope, 'reports', 'reports.kpi', compact('open', 'today', 'overdue', 'wonMonth'));
    }

    public function reportsDaily(Request $request): View
    {
        $scope = $this->scope($request);
        $activities = $scope->activities()->whereDate('created_at', today())->with(['lead:id,name', 'user:id,name'])->latest('id')->limit(50)->get();

        return $this->crmView($scope, 'reports', 'reports.daily', compact('activities'));
    }

    public function reportsCampaigns(Request $request): View
    {
        $scope = $this->scope($request);
        $campaigns = $scope->campaigns()->latest('id')->limit(50)->get();

        return $this->crmView($scope, 'reports', 'reports.campaigns', compact('campaigns'));
    }

    public function inboxIndex(Request $request): View
    {
        $scope = $this->scope($request);
        $waRoute = $scope->isRep() && \Illuminate\Support\Facades\Route::has('employee.sales.whatsapp.inbox.index')
            ? route('employee.sales.whatsapp.inbox.index')
            : ($scope->isManager() && \Illuminate\Support\Facades\Route::has('employee.sales-manager.whatsapp.inbox.index')
                ? route('employee.sales-manager.whatsapp.inbox.index')
                : (\Illuminate\Support\Facades\Route::has('admin.whatsapp.inbox') ? route('admin.whatsapp.inbox') : null));
        $metaRoute = $scope->isAdmin() && \Illuminate\Support\Facades\Route::has('admin.meta-social.inbox.index')
            ? route('admin.meta-social.inbox.index')
            : (\Illuminate\Support\Facades\Route::has('employee.sales.meta-social.inbox.index')
                ? route('employee.sales.meta-social.inbox.index')
                : null);

        return $this->crmView($scope, 'inbox', 'inbox.index', compact('waRoute', 'metaRoute'));
    }

    public function winApprovalsIndex(Request $request): View
    {
        $scope = $this->scope($request);
        abort_unless($scope->isAdmin(), 403);

        $leads = SalesLead::query()
            ->pendingWinApproval()
            ->with(['assignee:id,name'])
            ->orderByDesc('closed_at')
            ->paginate(25)
            ->withQueryString();

        return $this->crmView($scope, 'admin', 'win-approvals.index', compact('leads'));
    }

    // ─── Admin hub ────────────────────────────────────────────

    public function adminIndex(Request $request): View
    {
        $scope = $this->scope($request);
        abort_unless($scope->canManage(), 403);

        $has = fn (string $r) => \Illuminate\Support\Facades\Route::has($r);
        $access = app(\App\Domains\Sales\Services\CrmAccessService::class);
        $ok = fn (string $feature) => $access->allows($scope->user, $feature, $scope);

        $cards = array_values(array_filter([
            $scope->isAdmin() && $ok('ops.access_matrix') && $has($scope->route('access.index')) ? ['title' => 'صلاحيات CRM', 'url' => route($scope->route('access.index')), 'icon' => 'fa-user-shield', 'feature' => 'ops.access_matrix'] : null,
            $scope->isAdmin() && $ok('ops.win_approvals') && $has('admin.sales.win-approvals.index') ? ['title' => 'اعتماد صفقات الفوز', 'url' => route('admin.sales.win-approvals.index'), 'icon' => 'fa-trophy'] : ($scope->isAdmin() && $ok('ops.win_approvals') ? ['title' => 'اعتماد صفقات الفوز', 'url' => route($scope->route('win-approvals.index')), 'icon' => 'fa-trophy'] : null),
            $ok('module.opportunities') ? ['title' => 'الفرص بانتظار الاعتماد', 'url' => route($scope->route('opportunities.index'), ['quick' => 'won_pending']), 'icon' => 'fa-handshake'] : null,
            $scope->isAdmin() && $ok('ops.distribution') && $has('admin.sales.distribution.index') ? ['title' => 'لوحة التوزيع', 'url' => route('admin.sales.distribution.index'), 'icon' => 'fa-share-nodes'] : null,
            $scope->isAdmin() && $ok('ops.org_chart') && $has('admin.sales.org-chart.index') ? ['title' => 'هيكل السيلز', 'url' => route('admin.sales.org-chart.index'), 'icon' => 'fa-sitemap'] : null,
            $scope->isAdmin() && $ok('ops.sales_teams') && $has('admin.sales.sales-teams.index') ? ['title' => 'فرق المبيعات', 'url' => route('admin.sales.sales-teams.index'), 'icon' => 'fa-users'] : null,
            $scope->isAdmin() && $ok('ops.specialties') && $has('admin.sales.specialties.index') ? ['title' => 'تخصصات السيلز', 'url' => route('admin.sales.specialties.index'), 'icon' => 'fa-tags'] : null,
            $scope->isAdmin() && $ok('ops.transfer') && $has('admin.sales.transfer.index') ? ['title' => 'تحويل بيانات', 'url' => route('admin.sales.transfer.index'), 'icon' => 'fa-right-left'] : null,
            $scope->isAdmin() && $ok('ops.policy') && $has('admin.sales.policy.index') ? ['title' => 'قواعد السيلز', 'url' => route('admin.sales.policy.index'), 'icon' => 'fa-scale-balanced'] : null,
            $scope->isAdmin() && $ok('ops.kpi') && $has('admin.sales.kpi.index') ? ['title' => 'مراقبة KPIs', 'url' => route('admin.sales.kpi.index'), 'icon' => 'fa-chart-pie'] : null,
            $scope->isAdmin() && $ok('ops.kpi') && $has('admin.sales.kpi.targets') ? ['title' => 'أهداف المبيعات', 'url' => route('admin.sales.kpi.targets'), 'icon' => 'fa-bullseye'] : null,
            $scope->isAdmin() && $ok('ops.reports_admin') && $has('admin.sales.reports.index') ? ['title' => 'تقارير المبيعات', 'url' => route('admin.sales.reports.index'), 'icon' => 'fa-file-invoice'] : null,
            $scope->isAdmin() && $ok('ops.daily_reports') && $has('admin.sales.daily-reports.index') ? ['title' => 'التقارير اليومية', 'url' => route('admin.sales.daily-reports.index'), 'icon' => 'fa-calendar-day'] : null,
            $scope->isAdmin() && $ok('ops.commissions') && $has('admin.sales.commissions.index') ? ['title' => 'الكوميشن', 'url' => route('admin.sales.commissions.index'), 'icon' => 'fa-coins'] : null,
            $scope->isAdmin() && $ok('ops.groups') && $has('admin.sales.groups.index') ? ['title' => 'مجموعات العملاء', 'url' => route('admin.sales.groups.index'), 'icon' => 'fa-layer-group'] : null,
            $scope->isAdmin() && $ok('ops.import') && $has('admin.sales.leads.import') ? ['title' => 'استيراد عملاء', 'url' => route('admin.sales.leads.import'), 'icon' => 'fa-file-import'] : null,
            $scope->isAdmin() && $ok('ops.shifts') && $has('admin.sales.shifts.index') ? ['title' => 'شيفتات وقنوات', 'url' => route('admin.sales.shifts.index'), 'icon' => 'fa-clock'] : null,
            $scope->isAdmin() && $ok('ops.compliance') && $has('admin.sales.crm-compliance.index') ? ['title' => 'تدقيق CRM', 'url' => route('admin.sales.crm-compliance.index'), 'icon' => 'fa-clipboard-check'] : null,
            $scope->isAdmin() && $ok('ops.audit') && $has('admin.sales.audit-log.index') ? ['title' => 'سجل الأنشطة', 'url' => route('admin.sales.audit-log.index'), 'icon' => 'fa-history'] : null,
            $scope->isAdmin() && $ok('ops.course_board') && $has('admin.sales-course-board.index') ? ['title' => 'معلومات الكورسات', 'url' => route('admin.sales-course-board.index'), 'icon' => 'fa-table'] : null,
            $scope->isAdmin() && $ok('ops.course_board') && $has('admin.sales-diploma-board.index') ? ['title' => 'توصيف الدبلومات', 'url' => route('admin.sales-diploma-board.index'), 'icon' => 'fa-graduation-cap'] : null,
            $scope->isAdmin() && $ok('ops.whatsapp') && $has('admin.whatsapp.inbox') ? ['title' => 'واتساب', 'url' => route('admin.whatsapp.inbox'), 'icon' => 'fa-comment-dots'] : null,
            $scope->isAdmin() && $ok('ops.meta_agents') && $has('admin.meta-social.agents.index') ? ['title' => 'ربط موظفي Meta', 'url' => route('admin.meta-social.agents.index'), 'icon' => 'fa-link'] : null,
            $ok('ops.social_report') && $has($scope->route('reports.social')) ? ['title' => 'تقرير ماسنجر وواتساب', 'url' => route($scope->route('reports.social')), 'icon' => 'fa-chart-bar'] : null,
            $ok('ops.distribution') && $has('employee.sales-manager.distribution.index') ? ['title' => 'توزيع الاهتمام', 'url' => route('employee.sales-manager.distribution.index'), 'icon' => 'fa-share-nodes'] : null,
            $ok('ops.scorecard') && $has('employee.sales-manager.scorecard.index') ? ['title' => 'Scorecard', 'url' => route('employee.sales-manager.scorecard.index'), 'icon' => 'fa-shield-halved'] : null,
            $ok('ops.attendance') && $has('employee.sales-manager.attendance.index') ? ['title' => 'حضور الفريق', 'url' => route('employee.sales-manager.attendance.index'), 'icon' => 'fa-clock'] : null,
            $ok('ops.live_board') && $has('employee.sales-manager.live-board') ? ['title' => 'اللوحة الحية', 'url' => route('employee.sales-manager.live-board'), 'icon' => 'fa-tv'] : null,
            $ok('ops.live_board') && $has('employee.sales-manager.ops-board') ? ['title' => 'متابعة الفريق', 'url' => route('employee.sales-manager.ops-board'), 'icon' => 'fa-broadcast-tower'] : null,
            $ok('ops.org_chart') && $has('employee.sales-manager.org-chart.index') ? ['title' => 'هيكل الفريق', 'url' => route('employee.sales-manager.org-chart.index'), 'icon' => 'fa-sitemap'] : null,
            $ok('ops.whatsapp') && $has('employee.sales-manager.whatsapp.queue.index') ? ['title' => 'طابور واتساب', 'url' => route('employee.sales-manager.whatsapp.queue.index'), 'icon' => 'fa-bell'] : null,
            $ok('module.calendar') ? ['title' => 'التقويم', 'url' => route($scope->route('calendar.index')), 'icon' => 'fa-calendar'] : null,
            $ok('module.reports') ? ['title' => 'التقارير', 'url' => route($scope->route('reports.index')), 'icon' => 'fa-chart-line'] : null,
            $ok('module.inbox') ? ['title' => 'المحادثات', 'url' => route($scope->route('inbox.index')), 'icon' => 'fa-comments'] : null,
        ], fn ($c) => ! empty($c['url'])));

        return $this->crmView($scope, 'admin', 'admin.index', compact('cards'));
    }

    // ─── validation helpers ───────────────────────────────────

    private function validateAccount(Request $request, CrmScope $scope): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:40',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:120',
            'account_type' => ['nullable', Rule::in(array_keys(CrmAccount::TYPES))],
            'billing_address' => 'nullable|string|max:2000',
            'description' => 'nullable|string|max:5000',
            'assigned_to' => 'nullable|integer',
        ]);
        abort_unless($this->canAssignTo($scope, isset($data['assigned_to']) ? (int) $data['assigned_to'] : null), 403);
        $data['assigned_to'] = $data['assigned_to'] ?? $scope->user->id;

        return $data;
    }

    private function validateContact(Request $request, CrmScope $scope): array
    {
        $data = $request->validate([
            'first_name' => 'nullable|string|max:120',
            'last_name' => 'nullable|string|max:120',
            'name' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:120',
            'phone' => 'nullable|string|max:40',
            'email' => 'nullable|email|max:255',
            'crm_account_id' => 'nullable|integer',
            'description' => 'nullable|string|max:5000',
            'assigned_to' => 'nullable|integer',
        ]);
        abort_unless($this->canAssignTo($scope, isset($data['assigned_to']) ? (int) $data['assigned_to'] : null), 403);
        $data['assigned_to'] = $data['assigned_to'] ?? $scope->user->id;
        if (empty($data['name'])) {
            $data['name'] = trim(($data['first_name'] ?? '').' '.($data['last_name'] ?? '')) ?: 'جهة اتصال';
        }

        return $data;
    }

    private function validateCampaign(Request $request, CrmScope $scope): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'status' => ['required', Rule::in(array_keys(CrmCampaign::STATUSES))],
            'campaign_type' => ['nullable', Rule::in(array_keys(CrmCampaign::TYPES))],
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:5000',
            'assigned_to' => 'nullable|integer',
        ]);
        abort_unless($this->canAssignTo($scope, isset($data['assigned_to']) ? (int) $data['assigned_to'] : null), 403);
        $data['assigned_to'] = $data['assigned_to'] ?? $scope->user->id;

        return $data;
    }

    private function validateCase(Request $request, CrmScope $scope): array
    {
        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'status' => ['required', Rule::in(array_keys(CrmCase::STATUSES))],
            'priority' => ['required', Rule::in(array_keys(CrmCase::PRIORITIES))],
            'type' => 'nullable|string|max:60',
            'description' => 'nullable|string|max:5000',
            'resolution' => 'nullable|string|max:5000',
            'crm_account_id' => 'nullable|integer',
            'crm_contact_id' => 'nullable|integer',
            'sales_lead_id' => 'nullable|integer',
            'assigned_to' => 'nullable|integer',
        ]);
        abort_unless($this->canAssignTo($scope, isset($data['assigned_to']) ? (int) $data['assigned_to'] : null), 403);
        $data['assigned_to'] = $data['assigned_to'] ?? $scope->user->id;

        return $data;
    }

    /** @return list<array{title: string, url: string, desc: string}> */
    private function reportLinks(CrmScope $scope): array
    {
        return [
            ['title' => 'KPI والأداء', 'url' => route($scope->route('reports.kpi')), 'desc' => 'أهداف يومية ونسب الإنجاز'],
            ['title' => 'التقارير اليومية', 'url' => route($scope->route('reports.daily')), 'desc' => 'نشاط اليوم'],
            ['title' => 'تقارير الحملات', 'url' => route($scope->route('reports.campaigns')), 'desc' => 'أداء الحملات'],
            ['title' => 'الفرص', 'url' => route($scope->route('opportunities.index')), 'desc' => 'Pipeline والعمولات'],
            ['title' => 'الحملات', 'url' => route($scope->route('campaigns.index')), 'desc' => 'إدارة حملات CRM'],
        ];
    }
}
