@php
    $depth = $depth ?? 0;
    $sg = $studentRouteGroup ?? 'student.offline-courses';
    $indentRem = min($depth, 6) * 0.65;
    $dateLocale = app()->getLocale();
@endphp
@foreach($sections as $section)
    @php
        $itemsCount = $section->items->count();
        $childrenCount = $section->children?->count() ?? 0;
        $isRoot = ! $section->parent_id;
    @endphp
    <article class="oc-sec" @if($depth > 0) style="margin-inline-start: {{ $indentRem }}rem" @endif>
        <details @if($loop->first && $isRoot && $depth === 0) open @endif>
            <summary class="oc-sec-sum">
                <span class="oc-sec-ico" aria-hidden="true">
                    <i class="fas {{ $isRoot ? 'fa-folder-open' : 'fa-folder' }}"></i>
                </span>
                <div class="oc-sec-body">
                    <span class="tag">{{ $isRoot ? __('student.oc_section_main') : __('student.oc_section_sub') }}</span>
                    <h3>{{ $section->title }}</h3>
                    @if($section->description)
                        <p class="{{ $isRoot ? '' : 'line-clamp-2' }}">{{ $section->description }}</p>
                    @endif
                </div>
                <div class="oc-sec-meta">
                    @if($itemsCount > 0)
                        <span class="pill"><i class="fas fa-layer-group" style="font-size:9px;opacity:.7"></i> {{ $itemsCount }}</span>
                    @endif
                    @if($childrenCount > 0)
                        <span class="pill"><i class="fas fa-sitemap" style="font-size:9px;opacity:.7"></i> {{ $childrenCount }}</span>
                    @endif
                    <span class="chev" aria-hidden="true"><i class="fas fa-chevron-down text-[10px]"></i></span>
                </div>
            </summary>

            @if($section->items->isNotEmpty())
                <ul class="oc-items" role="list">
                    @foreach($section->items as $cItem)
                        @php $m = $cItem->item; @endphp
                        @if(! $m)
                            @continue
                        @endif

                        @if($m instanceof \App\Models\OfflineCurriculumNote)
                            <li class="oc-item" style="cursor:default">
                                <span class="dot note"><i class="fas fa-align-right"></i></span>
                                <div class="txt">
                                    <span class="kind">{{ __('student.oc_kind_note') }}</span>
                                    <strong>{{ $m->title }}</strong>
                                    @if($m->body)
                                        <p>{{ $m->body }}</p>
                                    @endif
                                </div>
                            </li>
                        @elseif($m instanceof \App\Models\OfflineLecture)
                            <li>
                                <a class="oc-item" href="{{ route($sg . '.lectures', $offlineCourse) }}#offline-lecture-{{ $m->id }}">
                                    <span class="dot lecture"><i class="fas fa-chalkboard-teacher"></i></span>
                                    <div class="txt">
                                        <span class="kind">{{ __('student.oc_kind_lecture') }}</span>
                                        <strong>{{ $m->title }}</strong>
                                        @if($m->relationLoaded('groupSession') && $m->groupSession)
                                            <p>
                                                {{ $m->groupSession->session_date->locale($dateLocale)->translatedFormat('l j F Y') }}
                                                @php $sgt = $m->groupSession->start_time; @endphp
                                                · {{ is_string($sgt) ? substr($sgt, 0, 5) : $sgt }}
                                            </p>
                                        @elseif($m->scheduled_at)
                                            <p>{{ $m->scheduled_at->locale($dateLocale)->translatedFormat('l j F Y — H:i') }}</p>
                                        @endif
                                        @if($m->description)
                                            <p>{{ \Illuminate\Support\Str::limit($m->description, 160) }}</p>
                                        @endif
                                        @include('partials.offline-mindmap-visual', ['text' => $m->offline_attendee_mindmap])
                                    </div>
                                    <span class="go">{{ __('student.oc_view') }}</span>
                                </a>
                            </li>
                        @elseif($m instanceof \App\Models\OfflineCourseResource)
                            <li>
                                <a class="oc-item" href="{{ route($sg . '.resources', $offlineCourse) }}#offline-resource-{{ $m->id }}">
                                    <span class="dot resource"><i class="fas fa-file-alt"></i></span>
                                    <div class="txt">
                                        <span class="kind">{{ __('student.oc_kind_resource') }}</span>
                                        <strong>{{ $m->title }}</strong>
                                        @if($m->description)
                                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($m->description), 120) }}</p>
                                        @endif
                                    </div>
                                    <span class="go">{{ __('student.oc_open') }}</span>
                                </a>
                            </li>
                        @elseif($m instanceof \App\Models\OfflineActivity)
                            <li>
                                <a class="oc-item" href="{{ route($sg . '.activities.show', [$offlineCourse, $m]) }}">
                                    <span class="dot activity"><i class="fas fa-tasks"></i></span>
                                    <div class="txt">
                                        <span class="kind">{{ __('student.oc_kind_activity') }}</span>
                                        <strong>{{ $m->title }}</strong>
                                        <p>{{ $m->type }}</p>
                                    </div>
                                    <span class="go">{{ __('student.oc_submit') }}</span>
                                </a>
                            </li>
                        @elseif($m instanceof \App\Models\AdvancedExam)
                            <li>
                                <a class="oc-item" href="{{ route('student.exams.show', $m) }}">
                                    <span class="dot exam"><i class="fas fa-clipboard-check"></i></span>
                                    <div class="txt">
                                        <span class="kind">{{ __('student.oc_kind_exam') }}</span>
                                        <strong>{{ $m->title }}</strong>
                                        @if($m->description)
                                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($m->description), 120) }}</p>
                                        @endif
                                    </div>
                                    <span class="go">{{ __('student.oc_open') }}</span>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @else
                <div style="padding:16px;text-align:center;font-size:13px;color:var(--ml-muted);border-top:1px solid var(--ml-line)">
                    {{ __('student.oc_section_empty') }}
                </div>
            @endif

            @if($section->children && $section->children->isNotEmpty())
                <div class="oc-children">
                    @include('student.offline-courses.partials.curriculum-sections', [
                        'sections' => $section->children,
                        'offlineCourse' => $offlineCourse,
                        'channel' => $channel ?? 'offline',
                        'studentRouteGroup' => $sg,
                        'depth' => $depth + 1,
                    ])
                </div>
            @endif
        </details>
    </article>
@endforeach
