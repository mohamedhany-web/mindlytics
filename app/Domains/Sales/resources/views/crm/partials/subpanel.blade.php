@php
    $title = $title ?? '';
    $count = $count ?? null;
    $actionUrl = $actionUrl ?? null;
    $actionLabel = $actionLabel ?? '＋ إضافة';
@endphp
<section class="crm-subpanel">
    <div class="crm-subpanel-head">
        <h3>{{ $title }}@if($count !== null) <span class="crm-muted">({{ $count }})</span>@endif</h3>
        @if($actionUrl)
            <a href="{{ $actionUrl }}" class="crm-btn crm-btn-primary crm-btn-sm">{{ $actionLabel }}</a>
        @endif
    </div>
    <div>
        {{ $slot }}
    </div>
</section>
