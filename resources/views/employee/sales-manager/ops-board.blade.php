@extends('layouts.employee')

@section('title', 'متابعة الفريق اليوم')
@section('header', 'متابعة الفريق اليومية')

@section('content')
@php
    $pollQuery = array_filter([
        'partial' => 1,
        'date' => $date->toDateString(),
        'employee_id' => $filters['employee_id'] ?? null,
        'work_mode' => $filters['work_mode'] ?? null,
        'attendance' => $filters['attendance'] ?? null,
        'presence' => $filters['presence'] ?? null,
    ], fn ($v) => $v !== null && $v !== '');
@endphp
<div class="space-y-4" id="ops-board-root"
     data-poll="{{ route('employee.sales-manager.ops-board', $pollQuery) }}">
    @include('employee.sales-manager.ops-board-partial')
</div>

@push('scripts')
<script>
(function () {
    var root = document.getElementById('ops-board-root');
    if (!root) return;
    var url = root.getAttribute('data-poll');
    setInterval(function () {
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
            .then(function (r) { return r.text(); })
            .then(function (html) {
                root.innerHTML = html;
            })
            .catch(function () {});
    }, 30000);
})();
</script>
@endpush
@endsection
