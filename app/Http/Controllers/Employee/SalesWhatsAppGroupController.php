<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Concerns\HandlesSalesWhatsAppGroups;
use App\Http\Controllers\Controller;
use App\Models\WhatsAppGroup;

class SalesWhatsAppGroupController extends Controller
{
    use HandlesSalesWhatsAppGroups;

    public function __construct()
    {
        $this->middleware('sales.employee');
    }

    protected function waGroupsAudience(): string
    {
        return 'employee';
    }

    protected function waGroupsRoute(string $action, mixed ...$params): string
    {
        return match ($action) {
            'index' => route('employee.sales.whatsapp-groups.index'),
            'create' => route('employee.sales.whatsapp-groups.create'),
            'show' => route('employee.sales.whatsapp-groups.show', $params[0]),
            default => route('employee.sales.whatsapp-groups.index'),
        };
    }

    protected function waGroupsView(string $view): string
    {
        return 'employee.sales.whatsapp-groups.' . $view;
    }

    public function index()
    {
        return $this->waGroupsIndex();
    }

    public function create(\Illuminate\Http\Request $request)
    {
        return $this->waGroupsCreate($request);
    }

    public function store(\Illuminate\Http\Request $request)
    {
        return $this->waGroupsStore($request);
    }

    public function show(WhatsAppGroup $whatsappGroup)
    {
        return $this->waGroupsShow($whatsappGroup);
    }

    public function update(\Illuminate\Http\Request $request, WhatsAppGroup $whatsappGroup)
    {
        return $this->waGroupsUpdate($request, $whatsappGroup);
    }

    public function addParticipants(\Illuminate\Http\Request $request, WhatsAppGroup $whatsappGroup)
    {
        return $this->waGroupsAddParticipants($request, $whatsappGroup);
    }

    public function removeParticipant(WhatsAppGroup $whatsappGroup, \App\Models\WhatsAppGroupParticipant $participant)
    {
        return $this->waGroupsRemoveParticipant($whatsappGroup, $participant);
    }

    public function refreshInvite(WhatsAppGroup $whatsappGroup)
    {
        return $this->waGroupsRefreshInvite($whatsappGroup);
    }

    public function sync(WhatsAppGroup $whatsappGroup)
    {
        return $this->waGroupsSync($whatsappGroup);
    }

    public function leave(WhatsAppGroup $whatsappGroup)
    {
        return $this->waGroupsLeave($whatsappGroup);
    }

    public function importFromCrm(\Illuminate\Http\Request $request, WhatsAppGroup $whatsappGroup)
    {
        return $this->waGroupsImportFromCrm($request, $whatsappGroup);
    }
}
