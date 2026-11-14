{{-- Shared public page hero strip — use inside layouts.public content --}}
@props([
    'eyebrow' => null,
    'title',
    'lead' => null,
])
<section class="lp-section" style="padding-block: 2.75rem 1.5rem; background: var(--lp-cream);">
    <div class="lp-container lp-reveal">
        @if ($eyebrow)
            <p class="lp-eyebrow">{{ $eyebrow }}</p>
        @endif
        <h1 class="lp-h1" style="margin: 0.35rem 0 0.65rem;">{{ $title }}</h1>
        @if ($lead)
            <p class="lp-lead" style="max-width: 40rem; margin: 0;">{{ $lead }}</p>
        @endif
        {{ $slot }}
    </div>
</section>
