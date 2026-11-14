@php
    use App\Support\LandingAssets;
    $a = LandingAssets::urls();

    // Accept InstructorProfile model or homepage DTO/object
    if (isset($profile) && $profile) {
        $user = $profile->user;
        $name = $user->name ?? '';
        $role = $profile->headline ?: __('landing.educo.instructors.fallback_role');
        $img = $profile->photo_url;
        $coursesCount = (int) ($coursesCount ?? $profile->courses_count ?? 0);
        $skills = method_exists($profile, 'getSkillsListAttribute') || isset($profile->skills)
            ? ($profile->skills_list ?? [])
            : [];
        $href = $user
            ? route('public.instructors.show', $user)
            : route('public.instructors.index');
        $parts = preg_split('/\s+/u', trim($name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $initials = collect($parts)->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    } else {
        $instructor = $instructor ?? null;
        $name = $instructor->name ?? '';
        $role = $instructor->headline
            ?? $instructor->role
            ?? __('landing.educo.instructors.fallback_role');
        $img = $instructor->photo_url ?? null;
        $coursesCount = (int) ($instructor->courses_count ?? $coursesCount ?? 0);
        $skills = $instructor->skills_list ?? $instructor->skills ?? [];
        if (is_string($skills)) {
            $skills = preg_split('/[\r\n,،]+/u', $skills, -1, PREG_SPLIT_NO_EMPTY) ?: [];
            $skills = array_values(array_filter(array_map('trim', $skills)));
        }
        $href = ! empty($instructor->id)
            ? route('public.instructors.show', $instructor->id)
            : route('public.instructors.index');
        $initials = $instructor->initials
            ?? (mb_strtoupper(mb_substr($name, 0, 1)) ?: '?');
    }

    if (($initials ?? '') === '') {
        $initials = '?';
    }
    $skillsPreview = collect($skills)->take(3)->values();
    $delay = (int) ($delay ?? 0);
@endphp
<a
    href="{{ $href }}"
    class="lp-instructor-card lp-reveal"
    style="--lp-delay:{{ $delay }}ms"
    aria-label="{{ $name }}"
>
    <div class="lp-instructor-card__photo {{ $img ? '' : 'is-empty' }}">
        @if ($img)
            <img
                src="{{ $img }}"
                alt="{{ $name }}"
                loading="lazy"
                width="270"
                height="270"
                onerror="this.style.display='none'; this.parentElement.classList.add('is-empty'); var i=this.parentElement.querySelector('.lp-instructor-card__initials'); if(i){ i.hidden=false; }"
            >
            <span class="lp-instructor-card__initials" hidden aria-hidden="true">{{ $initials }}</span>
        @else
            <span class="lp-instructor-card__initials" aria-hidden="true">{{ $initials }}</span>
        @endif
        <div class="lp-instructor-card__badge">
            <img src="{{ $a['instructor_thumb'] }}" alt="" width="14" height="14">
            <span>
                @if ($coursesCount > 0)
                    {{ trans_choice('landing.educo.instructors.courses_badge', $coursesCount, ['count' => $coursesCount]) }}
                @else
                    {{ __('landing.educo.instructors.verified_badge') }}
                @endif
            </span>
        </div>
    </div>
    <div class="lp-instructor-card__body">
        <p class="lp-instructor-card__role">{{ $role }}</p>
        <div class="lp-instructor-card__name">
            <strong>{{ $name }}</strong>
            <img src="{{ $a['instructor_verified'] }}" alt="" width="26" height="26">
        </div>
        @if ($skillsPreview->isNotEmpty())
            <div class="lp-instructor-card__skills">
                @foreach ($skillsPreview as $skill)
                    <span>{{ $skill }}</span>
                @endforeach
            </div>
        @endif
        <div class="lp-instructor-card__meta">
            <span>
                <img src="{{ $a['instructor_video'] }}" alt="" width="16" height="16">
                {{ __('landing.educo.instructors.catalog_view_profile') }}
            </span>
        </div>
    </div>
</a>
