@php
    $active = $active ?? 'hub';
@endphp
<nav class="cm-tabs" aria-label="{{ __('landing.educo.community.hub') }}">
    <a href="{{ route('public.community.index') }}" @class(['cm-tab', 'is-active' => $active === 'hub'])>
        {{ __('landing.educo.community.hub') }}
    </a>
    <a href="{{ route('community.data.index') }}" @class(['cm-tab', 'is-active' => $active === 'data'])>
        {{ __('landing.educo.community.nav_data') }}
    </a>
    <a href="{{ route('community.models.index') }}" @class(['cm-tab', 'is-active' => $active === 'models'])>
        {{ __('landing.educo.community.nav_models') }}
    </a>
    <a href="{{ route('community.contributors.index') }}" @class(['cm-tab', 'is-active' => $active === 'people'])>
        {{ __('landing.educo.community.nav_people') }}
    </a>
</nav>
