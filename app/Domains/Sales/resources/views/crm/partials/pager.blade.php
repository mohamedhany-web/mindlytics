{{-- عدّاد وأسهم الصفحات بنمط SuiteCRM: « ‹ (1 - 20 من 418) › » --}}
@php
    $from = $paginator->firstItem() ?? 0;
    $to = $paginator->lastItem() ?? 0;
@endphp
<div class="crm-pager" style="display: flex; align-items: center; gap: 4px">
    @if($paginator->onFirstPage())
        <span class="crm-pg" aria-hidden="true">«</span><span class="crm-pg" aria-hidden="true">‹</span>
    @else
        <a href="{{ $paginator->url(1) }}" aria-label="الصفحة الأولى">«</a><a href="{{ $paginator->previousPageUrl() }}" aria-label="الصفحة السابقة">‹</a>
    @endif
    <span style="font-size: 12px; font-weight: 700; padding: 0 4px">({{ $from }} - {{ $to }} من {{ $paginator->total() }})</span>
    @if($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" aria-label="الصفحة التالية">›</a><a href="{{ $paginator->url($paginator->lastPage()) }}" aria-label="الصفحة الأخيرة">»</a>
    @else
        <span class="crm-pg" aria-hidden="true">›</span><span class="crm-pg" aria-hidden="true">»</span>
    @endif
</div>
