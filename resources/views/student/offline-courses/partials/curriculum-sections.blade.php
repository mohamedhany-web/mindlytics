@php
    use Illuminate\Support\Str;

    $depth = $depth ?? 0;
    $sg = $studentRouteGroup ?? 'student.offline-courses';
    $indentPx = min($depth, 6) * 12;
    $sectionBubbles = ['var(--sp-sky)', 'var(--sp-lilac)', 'var(--sp-mint)', 'var(--sp-peach)'];
@endphp

@foreach($sections as $section)
    @php
        $itemsCount = $section->items->count();
        $childrenCount = $section->children?->count() ?? 0;
        $isRoot = ! $section->parent_id;
        $bubble = $sectionBubbles[$loop->index % count($sectionBubbles)];
    @endphp

    <details
        class="sp-oc-section-details sp-card overflow-hidden mb-3 last:mb-0"
        style="margin-inline-start: {{ $indentPx }}px"
        @if($loop->first && $isRoot && $depth === 0) open @endif
    >
        <summary class="flex items-center gap-3 px-4 py-4 sm:px-5 sm:py-4 bg-[#fafaf8] hover:bg-[#f7f7f5] transition-colors border-b border-black/5">
            <span class="sp-icon-bubble shrink-0 !w-11 !h-11" style="background:{{ $bubble }}">
                <x-student.figma-icon name="icon-path.svg" box="size-5" />
            </span>
            <div class="min-w-0 flex-1 text-start">
                <span class="sp-pill !py-0.5 !px-2 !text-[10px] mb-1.5 inline-flex">
                    {{ $isRoot ? __('student.oc_section_root') : __('student.oc_section_child') }}
                </span>
                <h4 class="text-base sm:text-lg font-extrabold leading-snug m-0">{{ $section->title }}</h4>
                @if(filled($section->description))
                    <p class="mt-1.5 text-sm leading-relaxed text-[var(--sp-muted)] m-0 {{ $isRoot ? '' : 'line-clamp-2' }} whitespace-pre-line">{{ $section->description }}</p>
                @endif
            </div>
            <div class="flex shrink-0 items-center gap-2">
                @if($itemsCount > 0)
                    <span class="sp-pill sp-pill--progress !py-1 !px-2 !text-[11px]">{{ $itemsCount }}</span>
                @endif
                @if($childrenCount > 0)
                    <span class="sp-pill !py-1 !px-2 !text-[11px]">{{ $childrenCount }}</span>
                @endif
                <span class="sp-oc-section-chevron flex h-9 w-9 items-center justify-center rounded-xl border border-black/5 bg-white text-[var(--sp-muted)]" aria-hidden="true">
                    <x-student.figma-icon name="icon-chevron.svg" box="size-3" />
                </span>
            </div>
        </summary>

        <div class="bg-white">
            @if($section->items->isNotEmpty())
                <div class="divide-y divide-black/5">
                    @foreach($section->items as $cItem)
                        @php $m = $cItem->item; @endphp
                        @if(!$m)
                            @continue
                        @endif

                        @if($m instanceof \App\Models\OfflineCurriculumNote)
                            <div class="flex gap-3 px-4 py-4 sm:px-5 sm:py-4">
                                <span class="sp-icon-bubble shrink-0 !w-10 !h-10" style="background:#f0f0ec">
                                    <x-student.figma-icon name="icon-messages.svg" box="size-5" />
                                </span>
                                <div class="min-w-0 flex-1">
                                    <span class="sp-pill !py-0.5 !px-2 !text-[10px] mb-1.5 inline-flex">{{ __('student.oc_item_note') }}</span>
                                    <p class="font-extrabold text-sm m-0">{{ $m->title }}</p>
                                    @if($m->body)
                                        <div class="mt-2 text-sm leading-relaxed text-[var(--sp-muted)] whitespace-pre-line">{{ $m->body }}</div>
                                    @endif
                                </div>
                            </div>
                        @elseif($m instanceof \App\Models\OfflineLecture)
                            <a href="{{ route($sg . '.lectures', $offlineCourse) }}#offline-lecture-{{ $m->id }}"
                               class="flex gap-3 px-4 py-4 sm:px-5 sm:py-4 hover:bg-[#fafaf8] transition-colors no-underline text-inherit group">
                                <span class="sp-icon-bubble shrink-0 !w-10 !h-10" style="background:var(--sp-lilac)">
                                    <x-student.figma-icon name="icon-classes.svg" box="size-5" />
                                </span>
                                <div class="min-w-0 flex-1 space-y-1.5">
                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <span class="sp-pill sp-pill--progress !py-0.5 !px-2 !text-[10px] mb-1 inline-flex">{{ __('student.oc_item_lecture') }}</span>
                                            <p class="text-base font-extrabold m-0 group-hover:text-[var(--sp-accent-text)]">{{ $m->title }}</p>
                                        </div>
                                        <span class="sp-pill sp-pill--upcoming shrink-0">{{ __('student.oc_view_lecture') }}</span>
                                    </div>
                                    @if($m->relationLoaded('groupSession') && $m->groupSession)
                                        <p class="text-xs font-bold text-[var(--sp-muted)] m-0 flex flex-wrap gap-x-2">
                                            <span>{{ $m->groupSession->session_date->translatedFormat('l j F Y') }}</span>
                                            @php $sgt = $m->groupSession->start_time; @endphp
                                            @if($sgt)
                                                <span>· {{ is_string($sgt) ? substr($sgt, 0, 5) : $sgt }}</span>
                                            @endif
                                            @if($m->groupSession->group)
                                                <span>· {{ $m->groupSession->group->name }}</span>
                                            @endif
                                        </p>
                                    @elseif($m->scheduled_at)
                                        <p class="text-xs font-bold text-[var(--sp-muted)] m-0">{{ $m->scheduled_at->translatedFormat('l j F Y — H:i') }}</p>
                                    @endif
                                    @if(filled($m->session_agenda))
                                        @php
                                            $agLines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $m->session_agenda))));
                                            $agShow = array_slice($agLines, 0, 4);
                                            $agMore = max(0, count($agLines) - 4);
                                        @endphp
                                        @if(count($agShow))
                                            <div class="rounded-[14px] bg-[#f7f7f5] px-3 py-2.5 mt-2">
                                                <p class="text-[10px] font-bold text-[var(--sp-muted)] m-0 mb-1.5 uppercase tracking-wide">{{ __('student.oc_curriculum_agenda') }}</p>
                                                <ul class="space-y-1 m-0 p-0 list-none">
                                                    @foreach($agShow as $line)
                                                        <li class="text-xs text-[var(--sp-text)] flex gap-2 leading-relaxed">
                                                            <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-[var(--sp-accent)]" aria-hidden="true"></span>
                                                            <span>{{ $line }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                @if($agMore > 0)
                                                    <p class="text-[11px] font-bold text-[var(--sp-muted)] m-0 mt-2">{{ __('student.oc_curriculum_agenda_more', ['count' => $agMore]) }}</p>
                                                @endif
                                            </div>
                                        @endif
                                    @endif
                                    @if($m->description)
                                        <p class="text-xs text-[var(--sp-muted)] m-0 line-clamp-2">{{ Str::limit($m->description, 180) }}</p>
                                    @endif
                                </div>
                            </a>
                        @elseif($m instanceof \App\Models\OfflineCourseResource)
                            <a href="{{ route($sg . '.resources', $offlineCourse) }}#offline-resource-{{ $m->id }}"
                               class="flex gap-3 px-4 py-4 sm:px-5 sm:py-4 hover:bg-[#fafaf8] transition-colors no-underline text-inherit group">
                                <span class="sp-icon-bubble shrink-0 !w-10 !h-10" style="background:var(--sp-sky)">
                                    <x-student.figma-icon name="icon-messages.svg" box="size-5" />
                                </span>
                                <div class="flex min-w-0 flex-1 flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0">
                                        <span class="sp-pill sp-pill--progress !py-0.5 !px-2 !text-[10px] mb-1 inline-flex">{{ __('student.oc_item_resource') }}</span>
                                        <p class="font-extrabold text-sm m-0 group-hover:text-[var(--sp-accent-text)]">{{ $m->title }}</p>
                                        @if($m->description)
                                            <p class="mt-0.5 text-xs text-[var(--sp-muted)] m-0 line-clamp-2">{{ Str::limit(strip_tags($m->description), 120) }}</p>
                                        @endif
                                    </div>
                                    <span class="sp-pill sp-pill--upcoming shrink-0 self-start sm:self-center">{{ __('student.oc_open_resource') }}</span>
                                </div>
                            </a>
                        @elseif($m instanceof \App\Models\OfflineActivity)
                            <a href="{{ route($sg . '.activities.show', [$offlineCourse, $m]) }}"
                               class="flex gap-3 px-4 py-4 sm:px-5 sm:py-4 hover:bg-[#fafaf8] transition-colors no-underline text-inherit group">
                                <span class="sp-icon-bubble shrink-0 !w-10 !h-10" style="background:var(--sp-amber-soft)">
                                    <x-student.figma-icon name="icon-star.svg" box="size-5" />
                                </span>
                                <div class="flex min-w-0 flex-1 flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0">
                                        <span class="sp-pill !py-0.5 !px-2 !text-[10px] mb-1 inline-flex">{{ __('student.oc_item_activity') }}</span>
                                        <p class="font-extrabold text-sm m-0 group-hover:text-[var(--sp-accent-text)]">{{ $m->title }}</p>
                                        <p class="text-[11px] text-[var(--sp-muted)] m-0">{{ $m->type }}</p>
                                    </div>
                                    <span class="sp-pill sp-pill--upcoming shrink-0 self-start sm:self-center">{{ __('student.oc_submit_activity') }}</span>
                                </div>
                            </a>
                        @elseif($m instanceof \App\Models\AdvancedExam)
                            <a href="{{ route('student.exams.show', $m) }}"
                               class="flex gap-3 px-4 py-4 sm:px-5 sm:py-4 hover:bg-[#fafaf8] transition-colors no-underline text-inherit group">
                                <span class="sp-icon-bubble shrink-0 !w-10 !h-10" style="background:var(--sp-mint)">
                                    <x-student.figma-icon name="icon-exams.svg" box="size-5" />
                                </span>
                                <div class="flex min-w-0 flex-1 flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0">
                                        <span class="sp-pill sp-pill--done !py-0.5 !px-2 !text-[10px] mb-1 inline-flex">{{ __('student.oc_item_exam') }}</span>
                                        <p class="font-extrabold text-sm m-0 group-hover:text-[var(--sp-accent-text)]">{{ $m->title }}</p>
                                        @if($m->description)
                                            <p class="mt-0.5 text-xs text-[var(--sp-muted)] m-0 line-clamp-2">{{ Str::limit(strip_tags($m->description), 120) }}</p>
                                        @endif
                                    </div>
                                    <span class="sp-pill sp-pill--progress shrink-0 self-start sm:self-center">{{ __('student.oc_open_exam') }}</span>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="px-4 py-6 sm:px-5 text-center text-sm text-[var(--sp-muted)] font-bold">
                    {{ __('student.oc_section_empty') }}
                </div>
            @endif

            @if($section->children && $section->children->isNotEmpty())
                <div class="bg-[#fafaf8] px-3 py-4 sm:px-4 border-t border-black/5">
                    @include('student.offline-courses.partials.curriculum-sections', [
                        'sections' => $section->children,
                        'offlineCourse' => $offlineCourse,
                        'channel' => $channel ?? 'offline',
                        'studentRouteGroup' => $sg,
                        'depth' => $depth + 1,
                    ])
                </div>
            @endif
        </div>
    </details>
@endforeach
