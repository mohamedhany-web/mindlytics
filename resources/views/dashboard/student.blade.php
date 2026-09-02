@extends('layouts.student-dashboard')

@section('title', __('student.dashboard_title'))

@section('page_heading')
    {{ __('student.welcome_back_name', ['name' => auth()->user()->name]) }}
@endsection

@php
    use App\Support\StudentFigmaAssets;
    $sp = StudentFigmaAssets::urls();
    $isStudent = auth()->user()->role === 'student' || strtolower((string) auth()->user()->role) === 'student';
    $scholarshipOnlyPortal = $isStudent && auth()->user()->usesScholarshipOnlyPortal();
    $activeCourses = $activeCourses ?? collect();
    $upcomingAssignments = $upcomingAssignments ?? collect();
    $upcomingExams = $upcomingExams ?? collect();
    $recentExamAttempts = $recentExamAttempts ?? collect();
    $recentCertificates = $recentCertificates ?? collect();
    $pendingScholarshipRegistrations = $pendingScholarshipRegistrations ?? collect();
    $stats = $stats ?? ['active_courses' => 0, 'completed_courses' => 0, 'total_progress' => 0, 'pending_orders' => 0];

    $bubbleColors = ['#f9e4d7', '#d7e8f9', '#d7eef5', '#f9f0d7', '#dcdef2'];
    $cellIcons = ['icon-cell-1.svg', 'icon-cell-code.svg', 'icon-cell-camera.svg'];
    $mentorAvatars = [$sp['avatar_2'], $sp['avatar_3'], $sp['avatar_4']];

    $now = now();
    $calMonth = $now->copy()->startOfMonth();
    $calDays = [];
    $startDow = (int) $calMonth->dayOfWeek; // 0=Sun
    for ($i = 0; $i < $startDow; $i++) {
        $prev = $calMonth->copy()->subDays($startDow - $i);
        $calDays[] = ['d' => $prev->day, 'muted' => true, 'today' => false];
    }
    $daysInMonth = $calMonth->daysInMonth;
    for ($d = 1; $d <= $daysInMonth; $d++) {
        $calDays[] = ['d' => $d, 'muted' => false, 'today' => $d === (int) $now->day];
    }
    while (count($calDays) % 7 !== 0) {
        $next = count($calDays) - ($startDow + $daysInMonth) + 1;
        $calDays[] = ['d' => $next, 'muted' => true, 'today' => false];
    }

    $weekBars = [55, 44, 23, 100, 60, 50, 65];
    $progressVal = max(1, min(100, (int) ($stats['total_progress'] ?? 0)));
    $weekBars[3] = $progressVal;
@endphp

@section('content')
<div class="space-y-5">
    @if($pendingScholarshipRegistrations->isNotEmpty())
        <div class="sp-scholarship">
            <div class="flex items-start gap-3">
                <div class="sp-icon-bubble" style="background:#f5d9a6">
                    <i class="fas fa-hourglass-half text-[var(--sp-accent-text)]"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h2 class="text-base font-extrabold text-amber-950 m-0">{{ __('student.scholarship_pending_title') }}</h2>
                    <p class="text-sm text-amber-900/80 mt-1 mb-3">{{ __('student.scholarship_pending_desc') }}</p>
                    <ul class="space-y-2 m-0 p-0 list-none">
                        @foreach($pendingScholarshipRegistrations as $scholarshipRegistration)
                            <li class="rounded-2xl bg-white border border-amber-100 px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div>
                                    <p class="font-bold text-slate-900 m-0">{{ $scholarshipRegistration->program?->name }}</p>
                                    <p class="text-xs text-slate-500 mt-1 mb-0">{{ __('student.registered_at') }}: {{ $scholarshipRegistration->registered_at?->format('Y-m-d H:i') }}</p>
                                </div>
                                <span class="sp-pill sp-pill--upcoming">{{ __('student.awaiting_activation') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="grid gap-5 xl:grid-cols-[minmax(0,1.7fr)_minmax(280px,1fr)] sp-dashboard-grid">
        {{-- LEFT / MAIN --}}
        <div class="space-y-5 min-w-0">
            {{-- New / featured courses row --}}
            <section>
                <div class="flex items-center justify-between gap-3 mb-3">
                    <h2 class="sp-section-title">{{ __('student.new_courses') }}</h2>
                    <a href="{{ $scholarshipOnlyPortal ? route('my-courses.index') : route('academic-years') }}" class="sp-link">{{ __('student.view_all') }}</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($activeCourses->take(3) as $index => $course)
                        @php
                            $lessons = (int) ($course->lessons_count ?? $course->lectures_count ?? 0);
                            $rating = $course->rating ? number_format((float) $course->rating, 1) : '—';
                            $type = $course->academicSubject->name ?? ($course->level ?? __('student.not_specified'));
                            $title = method_exists($course, 'localized') ? ($course->localized('title') ?: $course->title) : $course->title;
                        @endphp
                        <a href="{{ route('my-courses.show', $course->id) }}" class="sp-course-mini no-underline text-inherit">
                            <div class="flex items-center gap-2.5">
                                <span class="sp-icon-bubble" style="background: {{ $bubbleColors[$index % count($bubbleColors)] }}">
                                    <x-student.figma-icon :name="$cellIcons[$index % count($cellIcons)]" />
                                </span>
                                <div class="min-w-0">
                                    <p class="font-extrabold text-[15px] m-0 truncate">{{ $title }}</p>
                                    <p class="text-sm text-[var(--sp-text)] m-0 mt-1 opacity-80">{{ $lessons }} {{ __('student.lessons_count') }}</p>
                                </div>
                            </div>
                            <div class="flex gap-5">
                                <div>
                                    <p class="text-sm m-0 opacity-70">{{ __('student.rate') }}</p>
                                    <div class="flex items-center gap-1 mt-1">
                                        <img src="{{ $sp['star'] }}" alt="" class="size-4">
                                        <span class="font-extrabold text-[15px]">{{ $rating }}</span>
                                    </div>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm m-0 opacity-70">{{ __('student.type') }}</p>
                                    <p class="font-extrabold text-[15px] m-0 mt-1 truncate">{{ $type }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="sp-course-mini sm:col-span-2 lg:col-span-3 text-center py-8">
                            <p class="font-bold m-0 mb-2">{{ __('student.no_active_courses') }}</p>
                            <p class="text-sm text-[var(--sp-muted)] m-0 mb-4">{{ __('student.start_journey_now') }}</p>
                            @unless($scholarshipOnlyPortal)
                                <a href="{{ route('academic-years') }}" class="sp-promo-btn inline-flex">{{ __('student.explore_courses') }}</a>
                            @endunless
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Activity + Daily schedule --}}
            <div class="grid grid-cols-1 lg:grid-cols-[1.15fr_0.85fr] gap-4">
                <section class="sp-card p-6" x-data="{ period: 'weekly', open: false }" @click.away="open = false">
                    <div class="flex items-start justify-between gap-3 mb-6">
                        <div>
                            <h2 class="sp-section-title">{{ __('student.hours_activity') }}</h2>
                            <div class="flex items-center gap-2 mt-2 text-sm">
                                <img src="{{ $sp['trend'] }}" alt="" class="size-5">
                                <span class="font-bold text-emerald-700">{{ (int) $stats['total_progress'] }}%</span>
                                <span class="text-[var(--sp-muted)]" x-text="period === 'weekly' ? @js(__('student.vs_last_week')) : @js(__('student.vs_last_month'))"></span>
                            </div>
                        </div>
                        <div class="sp-menu">
                            <button type="button" class="sp-menu-btn" @click="open = !open" :aria-expanded="open.toString()">
                                <span x-text="period === 'weekly' ? @js(__('student.weekly')) : @js(__('student.monthly'))"></span>
                                <img src="{{ $sp['dropdown'] }}" alt="" class="size-2.5 rtl:rotate-180">
                            </button>
                            <div class="sp-menu-panel" x-show="open" x-cloak x-transition>
                                <a href="#" :class="period === 'weekly' && 'is-active'" @click.prevent="period = 'weekly'; open = false">{{ __('student.weekly') }}</a>
                                <a href="#" :class="period === 'monthly' && 'is-active'" @click.prevent="period = 'monthly'; open = false">{{ __('student.monthly') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-3 items-end h-44">
                        <div class="flex flex-col justify-between text-xs text-[var(--sp-muted)] h-full py-1">
                            <span>8h</span><span>6h</span><span>4h</span><span>2h</span><span>1h</span>
                        </div>
                        <div class="flex-1 flex items-end justify-between gap-2 h-full pb-1 border-b border-black/5">
                            @foreach($weekBars as $i => $h)
                                @php $monthH = max(12, min(100, (int) round($h * 0.85 + ($i * 3)))); @endphp
                                <div class="sp-bar {{ $i === 3 ? 'is-hot' : '' }}"
                                     :style="'height:' + (period === 'weekly' ? {{ max(12, $h) }} : {{ $monthH }}) + '%'"></div>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex justify-between ps-8 mt-2 text-xs text-[var(--sp-muted)] font-semibold" x-show="period === 'weekly'">
                        @foreach([__('student.day_su'), __('student.day_mo'), __('student.day_tu'), __('student.day_we'), __('student.day_th'), __('student.day_fr'), __('student.day_sa')] as $day)
                            <span>{{ $day }}</span>
                        @endforeach
                    </div>
                    <div class="flex justify-between ps-8 mt-2 text-xs text-[var(--sp-muted)] font-semibold" x-show="period === 'monthly'" x-cloak>
                        @foreach([__('student.week_1'), __('student.week_2'), __('student.week_3'), __('student.week_4'), __('student.week_5'), __('student.week_6'), __('student.week_7')] as $week)
                            <span>{{ $week }}</span>
                        @endforeach
                    </div>
                </section>

                <section class="sp-card p-6">
                    <h2 class="sp-section-title mb-4">{{ __('student.daily_schedule') }}</h2>
                    <div class="space-y-3">
                        @php
                            $scheduleItems = collect();
                            foreach ($upcomingExams->take(2) as $exam) {
                                $scheduleItems->push([
                                    'title' => $exam->title ?? __('student.exams'),
                                    'meta' => __('student.exams'),
                                    'url' => route('student.exams.show', $exam->id),
                                    'color' => '#d7e8f9',
                                    'icon' => 'icon-cell-code.svg',
                                ]);
                            }
                            foreach ($upcomingAssignments->take(4 - $scheduleItems->count()) as $assignment) {
                                $scheduleItems->push([
                                    'title' => $assignment->title ?? __('student.assignments'),
                                    'meta' => __('student.assignments'),
                                    'url' => route('student.assignments.index'),
                                    'color' => '#f9e4d7',
                                    'icon' => 'icon-cell-1.svg',
                                ]);
                            }
                            foreach ($activeCourses->take(4 - $scheduleItems->count()) as $idx => $course) {
                                $title = method_exists($course, 'localized') ? ($course->localized('title') ?: $course->title) : $course->title;
                                $scheduleItems->push([
                                    'title' => $title,
                                    'meta' => __('student.my_courses'),
                                    'url' => route('my-courses.show', $course->id),
                                    'color' => $bubbleColors[$idx % count($bubbleColors)],
                                    'icon' => $cellIcons[$idx % count($cellIcons)],
                                ]);
                            }
                        @endphp
                        @forelse($scheduleItems->take(4) as $item)
                            <a href="{{ $item['url'] }}" class="sp-schedule-row">
                                <span class="sp-icon-bubble rounded-[20px]" style="background:{{ $item['color'] }}; width:64px;height:64px;border-radius:20px">
                                    <x-student.figma-icon :name="$item['icon']" />
                                </span>
                                <span class="flex-1 min-w-0">
                                    <span class="block font-extrabold text-[15px] truncate">{{ $item['title'] }}</span>
                                    <span class="block text-sm mt-1 opacity-75 truncate">{{ $item['meta'] }}</span>
                                </span>
                                <span class="size-8 rounded-md bg-[#f5f5f5] inline-flex items-center justify-center">
                                    <img src="{{ $sp['chevron'] }}" alt="" class="size-5 rtl:rotate-180">
                                </span>
                            </a>
                        @empty
                            <p class="text-sm text-[var(--sp-muted)] m-0 py-6 text-center">{{ __('student.no_schedule') }}</p>
                        @endforelse
                    </div>
                </section>
            </div>

            {{-- Courses you're taking --}}
            <section>
                <div class="flex items-center justify-between gap-3 mb-3">
                    <h2 class="sp-section-title">{{ __('student.courses_taking') }}</h2>
                    <div class="flex items-center gap-2">
                        <div class="sp-menu" x-data="{ open: false }" @click.away="open = false">
                            <button type="button" class="sp-menu-btn sp-menu-btn--ghost" @click="open = !open" :aria-expanded="open.toString()">
                                {{ __('student.action') }}
                                <img src="{{ $sp['dropdown'] }}" alt="" class="size-2.5">
                            </button>
                            <div class="sp-menu-panel" x-show="open" x-cloak x-transition>
                                <a href="{{ route('my-courses.index') }}">{{ __('student.view_all_courses') }}</a>
                                @unless($scholarshipOnlyPortal)
                                    <a href="{{ route('academic-years') }}">{{ __('student.explore_courses') }}</a>
                                @endunless
                                <a href="{{ route('student.assignments.index') }}">{{ __('student.assignments') }}</a>
                            </div>
                        </div>
                        <a href="{{ route('my-courses.index') }}" class="size-9 rounded-full bg-[var(--sp-accent)] inline-flex items-center justify-center shadow-sm" title="{{ __('student.my_courses') }}">
                            <img src="{{ $sp['plus'] }}" alt="" class="size-4">
                        </a>
                    </div>
                </div>
                <div class="space-y-3">
                    @forelse($activeCourses->take(3) as $index => $course)
                        @php
                            $progress = (float) ($course->pivot->progress ?? optional($course->enrollment ?? null)->progress ?? 0);
                            $title = method_exists($course, 'localized') ? ($course->localized('title') ?: $course->title) : $course->title;
                            $teacher = $course->teacher->name ?? $course->instructor->name ?? __('student.not_specified');
                            $hours = (int) ($course->duration_hours ?? 0);
                            $remaining = max(0, 100 - (int) round($progress));
                        @endphp
                        <a href="{{ route('my-courses.show', $course->id) }}" class="sp-process-row">
                            <span class="sp-icon-bubble rounded-[20px]" style="background:{{ $bubbleColors[$index % count($bubbleColors)] }};width:64px;height:64px;border-radius:20px">
                                <x-student.figma-icon :name="$cellIcons[$index % count($cellIcons)]" />
                            </span>
                            <span class="flex-1 min-w-0">
                                <span class="block font-extrabold text-[15px] truncate">{{ $title }}</span>
                                <span class="flex items-center gap-1.5 mt-1.5 text-sm">
                                    <span class="size-[18px] rounded-full bg-[#e8e8e4] overflow-hidden inline-flex">
                                        <img src="{{ $mentorAvatars[$index % count($mentorAvatars)] }}" alt="" class="size-full object-cover">
                                    </span>
                                    <span class="truncate">{{ $teacher }}</span>
                                </span>
                            </span>
                            <span class="hidden sm:block min-w-[100px]">
                                <span class="block font-extrabold text-[15px]">{{ __('student.remaining') }}</span>
                                <span class="block text-sm mt-1">{{ $hours > 0 ? $hours . 'h' : $remaining . '%' }}</span>
                            </span>
                            <span class="relative size-[30px] inline-flex items-center justify-center shrink-0">
                                <img src="{{ $sp['progress_ring'] }}" alt="" class="absolute inset-0 size-full">
                                <svg viewBox="0 0 36 36" class="relative size-full -rotate-90">
                                    <circle cx="18" cy="18" r="15" fill="none" stroke="transparent" stroke-width="3"></circle>
                                    <circle cx="18" cy="18" r="15" fill="none" stroke="var(--sp-accent)" stroke-width="3"
                                            stroke-linecap="round"
                                            stroke-dasharray="{{ 2 * 3.14159 * 15 }}"
                                            stroke-dashoffset="{{ (2 * 3.14159 * 15) * (1 - min(100, $progress) / 100) }}"></circle>
                                </svg>
                            </span>
                            <span class="text-sm font-semibold w-10">{{ (int) round($progress) }}%</span>
                        </a>
                    @empty
                        <div class="sp-card p-8 text-center text-sm text-[var(--sp-muted)]">{{ __('student.no_active_courses') }}</div>
                    @endforelse
                </div>
            </section>
        </div>

        {{-- RIGHT RAIL --}}
        <aside class="space-y-5 min-w-0 sp-right-rail">
            @unless($scholarshipOnlyPortal)
            <section class="sp-promo">
                <div class="sp-promo-copy">
                    <p class="m-0 text-sm font-bold opacity-80">{{ auth()->user()->name }}</p>
                    <h3 class="m-0 mt-4 text-xl font-extrabold">{{ __('student.go_premium') }}</h3>
                    <p class="m-0 mt-2 text-sm text-white/80 max-w-[12rem] leading-relaxed">{{ __('student.go_premium_desc') }}</p>
                    <a href="{{ route('academic-years') }}" class="sp-promo-btn self-start">{{ __('student.get_access') }}</a>
                </div>
                <div class="sp-promo-art hidden sm:block">
                    <img src="{{ $sp['promo'] }}?v=3" alt="" width="160" height="190">
                </div>
            </section>
            @else
            <section class="sp-card p-6">
                <h3 class="sp-section-title">{{ __('student.total_progress') }}</h3>
                <p class="text-4xl font-black mt-3 mb-0 text-[var(--sp-accent-text)]">{{ (int) $stats['total_progress'] }}%</p>
                <p class="text-sm text-[var(--sp-muted)] mt-2 mb-0">{{ __('student.from_course_completion') }}</p>
            </section>
            @endunless

            <section class="sp-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <a href="{{ route('calendar') }}" class="size-9 rounded-xl bg-[#f5f5f5] inline-flex items-center justify-center">
                        <img src="{{ $sp['cal_arrow'] }}" alt="" class="size-5 rtl:rotate-180">
                    </a>
                    <h2 class="sp-section-title">{{ $now->translatedFormat('F, Y') }}</h2>
                    <a href="{{ route('calendar') }}" class="size-9 rounded-xl bg-[#f5f5f5] inline-flex items-center justify-center">
                        <img src="{{ $sp['cal_arrow'] }}" alt="" class="size-5 rotate-180 rtl:rotate-0">
                    </a>
                </div>
                <div class="grid grid-cols-7 gap-y-1 text-center mb-1">
                    @foreach([__('student.day_s'), __('student.day_m'), __('student.day_t'), __('student.day_w'), __('student.day_t'), __('student.day_f'), __('student.day_s')] as $label)
                        <span class="text-sm font-bold py-2">{{ $label }}</span>
                    @endforeach
                </div>
                <div class="grid grid-cols-7 gap-y-1 justify-items-center">
                    @foreach($calDays as $day)
                        @if($day['muted'])
                            <span class="sp-cal-day is-muted">{{ $day['d'] }}</span>
                        @else
                            <a href="{{ route('calendar') }}" class="sp-cal-day {{ $day['today'] ? 'is-today' : '' }}" title="{{ __('student.open_calendar') }}">{{ $day['d'] }}</a>
                        @endif
                    @endforeach
                </div>
            </section>

            <section>
                <div class="flex items-center justify-between gap-3 mb-3">
                    <h2 class="sp-section-title">{{ __('student.assignments') }}</h2>
                    <a href="{{ route('student.assignments.index') }}" class="size-9 rounded-full bg-[var(--sp-accent)] inline-flex items-center justify-center">
                        <img src="{{ $sp['plus'] }}" alt="" class="size-4">
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($upcomingAssignments->take(3) as $index => $assignment)
                        @php
                            $due = $assignment->due_date ? \Illuminate\Support\Carbon::parse($assignment->due_date) : null;
                            $status = 'upcoming';
                            if ($due && $due->isPast()) $status = 'progress';
                            $pill = $status === 'progress' ? 'sp-pill--progress' : 'sp-pill--upcoming';
                            $label = $status === 'progress' ? __('student.status_in_progress') : __('student.status_upcoming');
                        @endphp
                        <a href="{{ route('student.assignments.index') }}" class="sp-assign-row">
                            <span class="sp-icon-bubble rounded-[20px]" style="background:{{ $bubbleColors[$index % count($bubbleColors)] }};width:64px;height:64px;border-radius:20px">
                                <x-student.figma-icon :name="$cellIcons[$index % count($cellIcons)]" />
                            </span>
                            <span class="flex-1 min-w-0">
                                <span class="block font-extrabold text-[15px] truncate">{{ $assignment->title }}</span>
                                <span class="block text-sm mt-1.5 opacity-75">{{ $due?->format('d M, h:i A') ?? '—' }}</span>
                            </span>
                            <span class="sp-pill {{ $pill }}">{{ $label }}</span>
                        </a>
                    @empty
                        <div class="sp-card p-6 text-center text-sm text-[var(--sp-muted)]">{{ __('student.no_assignments') }}</div>
                    @endforelse
                </div>
            </section>

            @if($recentExamAttempts->isNotEmpty())
            <section class="sp-card p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="sp-section-title">{{ __('student.exam_results') }}</h2>
                    <a href="{{ route('student.exams.index') }}" class="sp-link">{{ __('student.view_all') }}</a>
                </div>
                <div class="space-y-2">
                    @foreach($recentExamAttempts->take(3) as $attempt)
                        @php
                            $examResultUrl = ($attempt->exam_id && $attempt->id)
                                ? route('student.exams.result', [$attempt->exam_id, $attempt->id])
                                : route('student.exams.index');
                        @endphp
                        <a href="{{ $examResultUrl }}" class="flex items-center justify-between gap-2 text-sm no-underline text-inherit rounded-xl px-2 py-2 hover:bg-[#f7f7f5]">
                            <span class="font-bold truncate">{{ $attempt->exam->title ?? __('student.exam_deleted') }}</span>
                            <span class="sp-pill sp-pill--done">{{ $attempt->score ?? '—' }}%</span>
                        </a>
                    @endforeach
                </div>
            </section>
            @endif

            @if($recentCertificates->isNotEmpty())
            <section class="sp-card p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="sp-section-title">{{ __('student.issued_certificates') }}</h2>
                    <a href="{{ route('student.certificates.index') }}" class="sp-link">{{ __('student.view_all') }}</a>
                </div>
                <div class="space-y-2">
                    @foreach($recentCertificates->take(3) as $certificate)
                        <a href="{{ route('student.certificates.show', $certificate) }}" class="block text-sm font-bold truncate no-underline text-inherit rounded-xl px-2 py-2 hover:bg-[#f7f7f5]">
                            {{ $certificate->title ?? $certificate->course->title ?? __('student.certificate_untitled') }}
                        </a>
                    @endforeach
                </div>
            </section>
            @endif
        </aside>
    </div>
</div>
@endsection
