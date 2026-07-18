{{-- Mindlytics Learning OS — Command Palette / Quick Switcher --}}
@php
    $paletteItems = [
        ['label' => __('common.palette_learning_space'), 'hint' => __('common.palette_learning_space_hint'), 'url' => route('dashboard'), 'icon' => 'fa-house', 'search' => 'dashboard home مساحة'],
        ['label' => __('common.palette_continue'), 'hint' => __('common.palette_continue_hint'), 'url' => route('my-courses.index'), 'icon' => 'fa-book-open', 'search' => 'courses continue مقررات'],
        ['label' => __('student.exams'), 'hint' => __('common.palette_exams_hint'), 'url' => route('student.exams.index'), 'icon' => 'fa-file-lines', 'search' => 'exams اختبار'],
        ['label' => __('student.assignments'), 'hint' => __('common.palette_assignments_hint'), 'url' => route('student.assignments.index'), 'icon' => 'fa-tasks', 'search' => 'assignments واجبات'],
        ['label' => __('student.calendar'), 'hint' => __('common.palette_calendar_hint'), 'url' => route('calendar'), 'icon' => 'fa-calendar', 'search' => 'calendar تقويم'],
        ['label' => __('student.certificates'), 'hint' => __('common.palette_certificates_hint'), 'url' => route('student.certificates.index'), 'icon' => 'fa-certificate', 'search' => 'certificates شهادات'],
        ['label' => __('student.notifications'), 'hint' => __('common.palette_notifications_hint'), 'url' => route('notifications'), 'icon' => 'fa-bell', 'search' => 'notifications إشعارات'],
        ['label' => __('student.my_groups'), 'hint' => __('common.palette_groups_hint'), 'url' => route('student.groups.index'), 'icon' => 'fa-users', 'search' => 'groups مجموعات'],
        ['label' => __('student.profile'), 'hint' => __('common.palette_profile_hint'), 'url' => route('profile'), 'icon' => 'fa-user', 'search' => 'profile ملف'],
        ['label' => __('common.ai_guide'), 'hint' => __('common.palette_ai_hint'), 'url' => route('dashboard').'#los-ai', 'icon' => 'fa-wand-magic-sparkles', 'search' => 'ai coach mentor ذكاء'],
    ];
    if (!auth()->user()?->usesScholarshipOnlyPortal()) {
        array_splice($paletteItems, 2, 0, [[
            'label' => __('common.palette_browse'),
            'hint' => __('common.palette_browse_hint'),
            'url' => route('academic-years'),
            'icon' => 'fa-compass',
            'search' => 'browse catalog استكشف',
        ]]);
    }
@endphp

<div data-los-palette class="los-palette-backdrop" style="display:none" role="dialog" aria-modal="true" aria-label="{{ __('common.command_palette') }}">
    <div class="los-palette" @click.stop>
        <input type="search"
               data-los-palette-input
               placeholder="{{ __('common.palette_placeholder') }}"
               autocomplete="off"
               aria-label="{{ __('common.quick_search') }}">
        <div class="los-palette-list">
            @foreach($paletteItems as $item)
                <a href="{{ $item['url'] }}"
                   class="los-palette-item"
                   data-los-palette-item
                   data-search="{{ $item['search'] }} {{ $item['label'] }} {{ $item['hint'] }}"
                   @click="typeof MindlyticsLOS !== 'undefined' && MindlyticsLOS.closePalette()">
                    <i class="fas {{ $item['icon'] }}" aria-hidden="true"></i>
                    <span>
                        {{ $item['label'] }}
                        <small style="display:block;font-size:11px;font-weight:500;color:var(--ml-muted);margin-top:2px">{{ $item['hint'] }}</small>
                    </span>
                </a>
            @endforeach
            <div data-los-palette-empty class="los-palette-empty" style="display:none">{{ __('common.palette_no_results') }}</div>
        </div>
        <div class="los-palette-foot">
            <span>{{ __('common.palette_enter') }}</span>
            <span>{{ __('common.palette_esc') }}</span>
            <span>Ctrl/⌘ K</span>
        </div>
    </div>
</div>
