@php
    $title = $title ?? '';
    $subtitle = $subtitle ?? null;
    $actions = $actions ?? [];
@endphp
<div class="crm-module-header">
    <div>
        <h1 class="crm-title">{{ $title }}</h1>
        @if($subtitle)
            <p class="crm-subtitle">{{ $subtitle }}</p>
        @endif
    </div>
    @if(count($actions))
        <div class="crm-actions">
            @foreach($actions as $action)
                @if(($action['type'] ?? 'link') === 'button')
                    <button type="{{ $action['button_type'] ?? 'button' }}" class="crm-btn {{ $action['class'] ?? 'crm-btn-primary' }}" @foreach(($action['attrs'] ?? []) as $k => $v) {{ $k }}="{{ $v }}" @endforeach>
                        @if(!empty($action['icon']))<i class="{{ $action['icon'] }}"></i>@endif
                        {{ $action['label'] }}
                    </button>
                @else
                    <a href="{{ $action['url'] }}" class="crm-btn {{ $action['class'] ?? 'crm-btn-primary' }}">
                        @if(!empty($action['icon']))<i class="{{ $action['icon'] }}"></i>@endif
                        {{ $action['label'] }}
                    </a>
                @endif
            @endforeach
        </div>
    @endif
</div>
