@php
    use App\Support\LandingAssets;
    $a = LandingAssets::urls();
    $title = $course->localized('title') ?? $course->title;
    $category = optional($course->academicSubject)->localized('name')
        ?? optional($course->academicSubject)->name
        ?? __('landing.educo.courses.fallback_category');
    $instructorName = optional($course->instructor)->name
        ?? __('landing.educo.courses.fallback_instructor');
    $instructorImg = optional($course->instructor)->profile_image_url
        ?? optional($course->instructor)->avatar_url
        ?? null;
    $cover = method_exists($course, 'thumbnailUrl')
        ? $course->thumbnailUrl()
        : (filled($course->thumbnail ?? null)
            ? asset('storage/'.ltrim(str_replace('\\', '/', $course->thumbnail), '/'))
            : null);
    $lessons = (int) ($course->lessons_count ?? $course->lectures_count ?? 0);
    $rawRating = $course->rating ?? null;
    $rating = $rawRating !== null && $rawRating !== ''
        ? number_format((float) $rawRating, 1)
        : null;
    $delay = (int) ($delay ?? 0);
    $initial = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($title, 0, 1));
@endphp
<a href="{{ route('public.course.show', $course->id) }}" class="lp-course-card lp-reveal" style="--lp-delay:{{ $delay }}ms">
    <div class="lp-course-card__img">
        @if ($cover)
            <img
                src="{{ $cover }}"
                alt="{{ $title }}"
                loading="lazy"
                decoding="async"
                class="lp-course-card__thumb"
                onerror="this.classList.add('is-broken'); this.nextElementSibling && this.nextElementSibling.classList.remove('is-hidden');"
            >
            <span class="lp-course-card__placeholder is-hidden" aria-hidden="true">{{ $initial }}</span>
        @else
            <span class="lp-course-card__placeholder" aria-hidden="true">{{ $initial }}</span>
        @endif
    </div>
    <div class="lp-course-card__body">
        <div class="lp-course-card__meta">
            <span class="lp-course-card__badge">{{ $category }}</span>
            @if ($rating)
                <div class="lp-course-card__rating">
                    <span>{{ $rating }}</span>
                    <img src="{{ $a['course_star'] }}" alt="" width="12" height="12">
                </div>
            @endif
        </div>
        <h3 class="lp-course-card__title">{{ $title }}</h3>
        <div class="lp-course-card__stats">
            <span>
                <img src="{{ $a['course_book'] }}" alt="" width="16" height="16">
                {{ $lessons }} {{ __('landing.educo.courses.lessons') }}
            </span>
            <span>
                <img src="{{ $a['course_clock'] }}" alt="" width="16" height="16">
                @if (!empty($course->display_duration_label))
                    {{ $course->display_duration_label }}
                @elseif (!empty($course->duration_hours))
                    {{ (int) $course->duration_hours }} {{ __('public.hours') }}
                @else
                    {{ __('landing.educo.courses.duration') }}
                @endif
            </span>
        </div>
        <div class="lp-course-card__foot">
            <div class="lp-course-card__teacher">
                <div class="lp-course-card__avatar">
                    @if ($instructorImg)
                        <img src="{{ $instructorImg }}" alt="" loading="lazy">
                    @endif
                </div>
                <strong>{{ $instructorName }}</strong>
            </div>
            <div class="lp-course-card__price">
                <x-course-price-display :course="$course" size="sm" />
            </div>
        </div>
    </div>
</a>
