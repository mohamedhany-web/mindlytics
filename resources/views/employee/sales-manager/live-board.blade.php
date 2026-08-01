@extends('layouts.employee')

@section('title', 'لوحة الفريق الحية')
@section('header', 'لوحة الفريق الحية — SOS')

@section('content')
<div class="space-y-4" id="live-board-root" data-poll="{{ route('employee.sales-manager.live-board', ['partial' => 1]) }}">
    @include('employee.sales-manager.live-board-partial')
</div>

@push('scripts')
<script>
(function () {
    var root = document.getElementById('live-board-root');
    if (!root) return;
    var url = root.getAttribute('data-poll');
    setInterval(function () {
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
            .then(function (r) { return r.text(); })
            .then(function (html) { root.innerHTML = html; })
            .catch(function () {});
    }, 60000);
})();
</script>
@endpush
@endsection
