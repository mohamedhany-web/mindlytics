@php $lead = $lead ?? null; $groups = $groups ?? collect(); @endphp
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @include('employee.sales._lead_fields_inner', ['lead' => $lead, 'groups' => $groups])
</div>
