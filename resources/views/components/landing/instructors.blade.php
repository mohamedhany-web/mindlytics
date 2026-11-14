@php
    $instructors = collect($landingInstructors ?? []);
@endphp

<section class="lp-section lp-instructors content-wrapper" data-node-id="25:1331">
    <div class="lp-container">
        <div class="lp-instructors__head" data-node-id="25:1332">
            <div class="lp-courses__heading">
                <p class="lp-eyebrow">{{ __('landing.educo.instructors.eyebrow') }}</p>
                <h2 class="lp-h1 text-center">{{ __('landing.educo.instructors.title') }}</h2>
            </div>
            <p class="lp-lead text-center">{{ __('landing.educo.instructors.desc') }}</p>
        </div>

        @if ($instructors->isNotEmpty())
            <div class="lp-instructors__grid" data-node-id="25:1472">
                @foreach ($instructors as $instructor)
                    @include('components.landing.instructor-card', [
                        'instructor' => $instructor,
                        'delay' => $loop->index * 60,
                    ])
                @endforeach
            </div>
        @else
            <div class="lp-instructors__empty">
                <p class="lp-lead">{{ __('landing.educo.instructors.empty') }}</p>
            </div>
        @endif

        <div class="lp-instructors__cta">
            <a href="{{ route('public.instructors.index') }}" class="lp-btn-yellow">{{ __('landing.educo.instructors.cta') }}</a>
        </div>
    </div>
</section>
