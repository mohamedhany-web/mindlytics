<?php

namespace App\Http\Controllers\Concerns;

use App\Models\SalesLead;
use App\Models\SalesLeadGroup;
use App\Models\WhatsAppGroup;
use App\Models\WhatsAppGroupParticipant;
use App\Services\WhatsAppGroupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

trait HandlesSalesWhatsAppGroups
{
    abstract protected function waGroupsAudience(): string;

    abstract protected function waGroupsRoute(string $action, mixed ...$params): string;

    abstract protected function waGroupsView(string $view): string;

    protected function waGroupService(): WhatsAppGroupService
    {
        return app(WhatsAppGroupService::class);
    }

    protected function isWaGroupsAdmin(): bool
    {
        return $this->waGroupsAudience() === 'admin';
    }

    public function waGroupsIndex(): View
    {
        $groups = WhatsAppGroup::query()
            ->visibleTo(Auth::user(), $this->isWaGroupsAdmin())
            ->with(['salesLeadGroup:id,name', 'creator:id,name'])
            ->withCount('participants')
            ->latest()
            ->paginate(20);

        $bridge = $this->waGroupService()->bridgeStatus();

        return view($this->waGroupsView('index'), compact('groups', 'bridge'));
    }

    public function waGroupsCreate(Request $request): View
    {
        $bridge = $this->waGroupService()->bridgeStatus();
        $crmGroups = $this->crmGroupsForSelect();
        $prefillCrmGroupId = (int) $request->query('crm_group');
        $prefillParticipants = collect();

        if ($prefillCrmGroupId > 0) {
            $crmGroup = SalesLeadGroup::query()->find($prefillCrmGroupId);
            if ($crmGroup && $this->canAccessCrmGroup($crmGroup)) {
                $prefillParticipants = $this->waGroupService()->participantsFromSalesLeadGroup(
                    $crmGroup,
                    $this->isWaGroupsAdmin() ? null : (int) Auth::id()
                );
            }
        }

        return view($this->waGroupsView('create'), compact('bridge', 'crmGroups', 'prefillCrmGroupId', 'prefillParticipants'));
    }

    public function waGroupsStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:120',
            'description' => 'nullable|string|max:2000',
            'sales_lead_group_id' => 'nullable|exists:sales_lead_groups,id',
            'announce_only' => 'nullable|boolean',
            'restrict_info' => 'nullable|boolean',
            'phones' => 'nullable|array',
            'phones.*' => 'nullable|string|max:30',
            'lead_ids' => 'nullable|array',
            'lead_ids.*' => 'integer|exists:sales_leads,id',
        ]);

        $participantRows = $this->buildParticipantRows($request);
        if ($participantRows === []) {
            return back()->withInput()->with('error', 'أضف رقماً واحداً على الأقل أو اختر عملاء.');
        }

        $crmGroupId = ! empty($validated['sales_lead_group_id']) ? (int) $validated['sales_lead_group_id'] : null;
        if ($crmGroupId) {
            $crmGroup = SalesLeadGroup::query()->findOrFail($crmGroupId);
            $this->authorizeCrmGroup($crmGroup);
        }

        $result = $this->waGroupService()->createAndProvision(
            $validated['subject'],
            $participantRows,
            (int) Auth::id(),
            $crmGroupId,
            $validated['description'] ?? null,
            $request->boolean('announce_only'),
            $request->boolean('restrict_info'),
        );

        if (! ($result['success'] ?? false)) {
            return back()->withInput()->with('error', $result['error'] ?? 'فشل إنشاء المجموعة');
        }

        /** @var WhatsAppGroup $group */
        $group = $result['group'];

        return redirect()->to($this->waGroupsRoute('show', $group))
            ->with('success', 'تم إنشاء مجموعة الواتساب «' . $group->subject . '»');
    }

    public function waGroupsShow(WhatsAppGroup $whatsappGroup): View
    {
        $this->authorizeWaGroup($whatsappGroup);
        $whatsappGroup->load(['participants.salesLead', 'salesLeadGroup', 'creator:id,name']);
        $bridge = $this->waGroupService()->bridgeStatus();
        $crmGroups = $this->crmGroupsForSelect();
        $availableLeads = $this->availableLeadsForAdd($whatsappGroup);

        return view($this->waGroupsView('show'), compact('whatsappGroup', 'bridge', 'crmGroups', 'availableLeads'));
    }

    public function waGroupsUpdate(Request $request, WhatsAppGroup $whatsappGroup): RedirectResponse
    {
        $this->authorizeWaGroup($whatsappGroup);

        $validated = $request->validate([
            'subject' => 'required|string|max:120',
            'description' => 'nullable|string|max:2000',
            'announce_only' => 'nullable|boolean',
            'restrict_info' => 'nullable|boolean',
        ]);

        $result = $this->waGroupService()->updateSettings($whatsappGroup, [
            'subject' => $validated['subject'],
            'description' => $validated['description'] ?? '',
            'announce_only' => $request->boolean('announce_only'),
            'restrict_info' => $request->boolean('restrict_info'),
        ]);

        if (! ($result['success'] ?? false)) {
            return back()->with('error', $result['error'] ?? 'فشل حفظ الإعدادات');
        }

        return back()->with('success', 'تم تحديث إعدادات المجموعة على واتساب.');
    }

    public function waGroupsAddParticipants(Request $request, WhatsAppGroup $whatsappGroup): RedirectResponse
    {
        $this->authorizeWaGroup($whatsappGroup);

        $validated = $request->validate([
            'phones' => 'nullable|array',
            'phones.*' => 'nullable|string|max:30',
            'lead_ids' => 'nullable|array',
            'lead_ids.*' => 'integer|exists:sales_leads,id',
        ]);

        $rows = $this->buildParticipantRows($request);
        if ($rows === []) {
            return back()->with('error', 'اختر عملاء أو أدخل أرقاماً.');
        }

        $result = $this->waGroupService()->addParticipants($whatsappGroup, $rows);
        if (! ($result['success'] ?? false)) {
            return back()->with('error', $result['error'] ?? 'فشل إضافة الأرقام');
        }

        return back()->with('success', 'تمت إضافة ' . ($result['added'] ?? 0) . ' رقم للمجموعة.');
    }

    public function waGroupsRemoveParticipant(WhatsAppGroup $whatsappGroup, WhatsAppGroupParticipant $participant): RedirectResponse
    {
        $this->authorizeWaGroup($whatsappGroup);
        if ((int) $participant->whatsapp_group_id !== (int) $whatsappGroup->id) {
            abort(404);
        }

        $result = $this->waGroupService()->removeParticipant($whatsappGroup, $participant);
        if (! ($result['success'] ?? false)) {
            return back()->with('error', $result['error'] ?? 'فشل الحذف');
        }

        return back()->with('success', 'تمت إزالة العضو من المجموعة.');
    }

    public function waGroupsRefreshInvite(WhatsAppGroup $whatsappGroup): RedirectResponse
    {
        $this->authorizeWaGroup($whatsappGroup);
        $result = $this->waGroupService()->refreshInviteLink($whatsappGroup);
        if (! ($result['success'] ?? false)) {
            return back()->with('error', $result['error'] ?? 'تعذّر جلب رابط الدعوة');
        }

        return back()->with('success', 'تم تحديث رابط الدعوة.');
    }

    public function waGroupsSync(WhatsAppGroup $whatsappGroup): RedirectResponse
    {
        $this->authorizeWaGroup($whatsappGroup);
        $result = $this->waGroupService()->syncFromBridge($whatsappGroup);
        if (! ($result['success'] ?? false)) {
            return back()->with('error', $result['error'] ?? 'تعذّر المزامنة');
        }

        return back()->with('success', 'تمت مزامنة بيانات المجموعة من واتساب.');
    }

    public function waGroupsLeave(WhatsAppGroup $whatsappGroup): RedirectResponse
    {
        $this->authorizeWaGroup($whatsappGroup);
        $result = $this->waGroupService()->leave($whatsappGroup);
        if (! ($result['success'] ?? false)) {
            return back()->with('error', $result['error'] ?? 'فشل الخروج');
        }

        return redirect()->to($this->waGroupsRoute('index'))->with('success', 'تم الخروج من المجموعة.');
    }

    public function waGroupsImportFromCrm(Request $request, WhatsAppGroup $whatsappGroup): RedirectResponse
    {
        $this->authorizeWaGroup($whatsappGroup);
        $validated = $request->validate([
            'sales_lead_group_id' => 'required|exists:sales_lead_groups,id',
        ]);

        $crmGroup = SalesLeadGroup::query()->findOrFail($validated['sales_lead_group_id']);
        $this->authorizeCrmGroup($crmGroup);

        $rows = $this->waGroupService()->participantsFromSalesLeadGroup(
            $crmGroup,
            $this->isWaGroupsAdmin() ? null : (int) Auth::id()
        )->values()->all();

        if ($rows === []) {
            return back()->with('error', 'لا يوجد عملاء بأرقام في هذه المجموعة.');
        }

        $result = $this->waGroupService()->addParticipants($whatsappGroup, $rows);

        if (! ($result['success'] ?? false)) {
            return back()->with('error', $result['error'] ?? 'فشل الاستيراد');
        }

        $whatsappGroup->update(['sales_lead_group_id' => $crmGroup->id]);

        return back()->with('success', 'تم استيراد ' . ($result['added'] ?? 0) . ' عميل من مجموعة CRM.');
    }

    protected function authorizeWaGroup(WhatsAppGroup $group): void
    {
        if ($this->isWaGroupsAdmin()) {
            return;
        }

        $userId = (int) Auth::id();
        if ((int) $group->created_by !== $userId && (int) $group->assigned_to !== $userId) {
            abort(403);
        }
    }

    protected function authorizeCrmGroup(SalesLeadGroup $group): void
    {
        if ($this->isWaGroupsAdmin()) {
            return;
        }

        if (! $group->userHasAccess((int) Auth::id())) {
            abort(403);
        }
    }

    protected function canAccessCrmGroup(SalesLeadGroup $group): bool
    {
        if ($this->isWaGroupsAdmin()) {
            return true;
        }

        return $group->userHasAccess((int) Auth::id());
    }

    /** @return \Illuminate\Support\Collection<int, SalesLeadGroup> */
    protected function crmGroupsForSelect()
    {
        $query = SalesLeadGroup::query()->orderBy('name');
        if (! $this->isWaGroupsAdmin()) {
            $query->forAssignee((int) Auth::id());
        }

        return $query->get(['id', 'name']);
    }

    /** @return \Illuminate\Support\Collection<int, SalesLead> */
    protected function availableLeadsForAdd(WhatsAppGroup $whatsappGroup)
    {
        $existingPhones = $whatsappGroup->participants()->pluck('phone')->all();
        $query = SalesLead::query()->whereNotNull('phone')->where('phone', '!=', '');
        if (! $this->isWaGroupsAdmin()) {
            $query->forAssignee((int) Auth::id());
        }

        return $query->orderBy('name')->get(['id', 'name', 'phone'])
            ->reject(fn (SalesLead $l) => in_array(app(WhatsAppService::class)->formatPhoneNumber($l->phone), $existingPhones, true));
    }

    /** @return array<int, array{phone: string, sales_lead_id?: int, display_name?: string}> */
    protected function buildParticipantRows(Request $request): array
    {
        $rows = [];
        $whatsapp = app(WhatsAppService::class);

        foreach ((array) $request->input('phones', []) as $phone) {
            $formatted = $whatsapp->formatPhoneNumber((string) $phone);
            if ($formatted !== '') {
                $rows[] = ['phone' => $formatted];
            }
        }

        $leadIds = array_filter(array_map('intval', (array) $request->input('lead_ids', [])));
        if ($leadIds !== []) {
            $leadsQuery = SalesLead::query()->whereIn('id', $leadIds)->whereNotNull('phone');
            if (! $this->isWaGroupsAdmin()) {
                $leadsQuery->forAssignee((int) Auth::id());
            }
            foreach ($leadsQuery->get(['id', 'name', 'phone']) as $lead) {
                $phone = $whatsapp->formatPhoneNumber($lead->phone);
                if ($phone !== '') {
                    $rows[] = [
                        'phone' => $phone,
                        'sales_lead_id' => $lead->id,
                        'display_name' => $lead->name,
                    ];
                }
            }
        }

        $unique = [];
        foreach ($rows as $row) {
            $unique[$row['phone']] = $row;
        }

        return array_values($unique);
    }
}
