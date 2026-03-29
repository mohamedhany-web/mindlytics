@php $lead = $lead ?? null; @endphp
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @include('employee.sales._lead_fields_inner', ['lead' => $lead])
</div>
