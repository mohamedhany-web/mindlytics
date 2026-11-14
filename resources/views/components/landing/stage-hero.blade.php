{{--
  Educo light stage hero for inner public pages.
  Props: eyebrow, title, lead, brand (bool), homeHref optional
--}}
@props([
    'eyebrow' => null,
    'title',
    'lead' => null,
    'brand' => true,
    'homeLabel' => null,
])

@php
    $homeLabel = $homeLabel ?: __('landing.educo.header.home');
@endphp

<section {{ $attributes->merge(['class' => 'lp-stage-hero']) }}>
    <div class="lp-container lp-reveal">
        <nav class="lp-stage-crumb" aria-label="Breadcrumb">
            <a href="{{ url('/') }}">{{ $homeLabel }}</a>
            <span aria-hidden="true">/</span>
            <span>{{ $title }}</span>
        </nav>

        @if ($brand)
            <p class="lp-stage-brand">Mind<span>lytics</span></p>
        @endif

        @if ($eyebrow)
            <p class="lp-eyebrow">{{ $eyebrow }}</p>
        @endif

        <h1 class="lp-h1">{{ $title }}</h1>

        @if ($lead)
            <p class="lp-lead">{{ $lead }}</p>
        @endif

        {{ $slot }}
    </div>
</section>
