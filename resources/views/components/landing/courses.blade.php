@php
    $courses = ($featuredCourses ?? collect())->take(6);
@endphp

<section class="lp-section lp-courses content-wrapper" data-node-id="17:651" id="featured-courses">
    <div class="lp-container">
        <div class="lp-courses__head lp-reveal" data-node-id="17:652">
            <div class="lp-courses__heading">
                <p class="lp-eyebrow">{{ __('landing.educo.courses.eyebrow') }}</p>
                <h2 class="lp-h1 text-center">{{ __('landing.educo.courses.title') }}</h2>
            </div>
            <p class="lp-lead text-center">{{ __('landing.educo.courses.desc') }}</p>
        </div>

        @if ($courses->isNotEmpty())
            <div class="lp-courses__grid" data-node-id="17:854">
                @foreach ($courses as $course)
                    @include('components.landing.course-card', [
                        'course' => $course,
                        'delay' => $loop->index * 60,
                    ])
                @endforeach
            </div>

            <div class="lp-courses__cta">
                <a href="{{ route('public.courses') }}" class="lp-btn-yellow">{{ __('landing.educo.courses.cta') }}</a>
            </div>
        @else
            <div class="text-center py-10">
                <p class="lp-lead mb-6">{{ __('landing.coming_soon_desc') }}</p>
                <a href="{{ route('public.courses') }}" class="lp-btn-yellow">{{ __('landing.educo.courses.cta') }}</a>
            </div>
        @endif
    </div>
</section>
