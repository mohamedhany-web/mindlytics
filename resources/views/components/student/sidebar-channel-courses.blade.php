@props([
    'enrollments',
    'count' => 0,
    'icon',
    'label',
    'indexRoute',
    'showRoute',
    'isSectionActive' => false,
    'activeCourseId' => null,
    'defaultOpen' => false,
    'maxVisible' => 4,
])

@if($count > 0)
    <details class="sp-nav-details" @if($defaultOpen) open @endif>
        <summary class="sp-nav-link sp-nav-details-summary {{ $isSectionActive ? 'is-active' : '' }}">
            <span class="sp-nav-ico"><x-student.figma-icon :name="$icon" /></span>
            <span class="flex-1 min-w-0 truncate">{{ $label }}</span>
            <span class="sp-nav-badge">{{ $count }}</span>
            <x-student.figma-icon name="icon-dropdown.svg" box="size-3.5" class="sp-nav-chevron shrink-0 opacity-60" />
        </summary>
        <div class="sp-nav-tree">
            @foreach($enrollments->take($maxVisible) as $enrollment)
                @php $course = $enrollment->course; @endphp
                <a href="{{ route($showRoute, $course) }}"
                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                   class="sp-nav-tree-link {{ (int) $activeCourseId === (int) $course->id ? 'is-active' : '' }}"
                   title="{{ $course->title }}">
                    <span class="truncate">{{ \Illuminate\Support\Str::limit($course->title, 26) }}</span>
                    <span class="sp-nav-mini">{{ number_format((float) $enrollment->progress, 0) }}%</span>
                </a>
            @endforeach
            @if($count > $maxVisible)
                <a href="{{ $indexRoute }}" @click="if (window.innerWidth < 1024) sidebarOpen = false" class="sp-nav-tree-more">
                    {{ __('student.oc_sidebar_more_courses', ['count' => $count - $maxVisible]) }}
                </a>
            @elseif($count > 1)
                <a href="{{ $indexRoute }}" @click="if (window.innerWidth < 1024) sidebarOpen = false" class="sp-nav-tree-more">
                    {{ __('student.oc_sidebar_all_in_section') }}
                </a>
            @endif
        </div>
    </details>
@else
    <a href="{{ $indexRoute }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
       class="sp-nav-link {{ $isSectionActive ? 'is-active' : '' }}">
        <span class="sp-nav-ico"><x-student.figma-icon :name="$icon" /></span>
        <span class="flex-1 min-w-0">{{ $label }}</span>
    </a>
@endif
