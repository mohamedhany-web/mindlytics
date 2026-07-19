@php
    $depth = $depth ?? 0;
    $sg = $studentRouteGroup ?? 'student.offline-courses';
    $indentRem = min($depth, 8) * 0.5;
@endphp
@foreach($sections as $section)
    @php
        $itemsCount = $section->items->count();
        $childrenCount = $section->children?->count() ?? 0;
        $isRoot = ! $section->parent_id;
    @endphp
    <article
        class="curriculum-section-card rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden mb-4 last:mb-0"
        style="margin-inline-start: {{ $indentRem }}rem"
    >
        <details class="group/section" @if($loop->first && $isRoot && $depth === 0) open @endif>
            <summary class="curriculum-section-summary flex cursor-pointer list-none items-center gap-3 px-4 py-3.5 sm:px-5 sm:py-4 bg-gradient-to-l from-white to-slate-50/90 hover:from-sky-50/30 hover:to-slate-50 border-b border-slate-100 transition-colors">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-600 ring-1 ring-sky-200/60 shadow-sm">
                    <i class="fas {{ $isRoot ? 'fa-folder-open' : 'fa-folder' }} text-lg"></i>
                </span>
                <div class="min-w-0 flex-1 text-start">
                    @if($isRoot)
                        <span class="mb-0.5 inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-slate-600">قسم رئيسي</span>
                    @else
                        <span class="mb-0.5 inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-slate-600">قسم فرعي</span>
                    @endif
                    <h3 class="text-base sm:text-lg font-bold leading-snug text-slate-900">{{ $section->title }}</h3>
                    @if($section->description && $isRoot)
                        <p class="mt-1.5 text-sm leading-relaxed text-slate-600 line-clamp-3 sm:line-clamp-none whitespace-pre-line">{{ $section->description }}</p>
                    @elseif($section->description && ! $isRoot)
                        <p class="mt-1 text-xs leading-relaxed text-slate-600 line-clamp-2">{{ $section->description }}</p>
                    @endif
                </div>
                <div class="flex shrink-0 flex-col items-end gap-1.5 sm:flex-row sm:items-center sm:gap-2">
                    @if($itemsCount > 0)
                        <span class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-bold text-slate-600 shadow-sm">
                            <i class="fas fa-layer-group text-sky-500 text-[10px]"></i>
                            {{ $itemsCount }}
                        </span>
                    @endif
                    @if($childrenCount > 0)
                        <span class="inline-flex items-center gap-1 rounded-lg border border-violet-100 bg-violet-50 px-2.5 py-1 text-[11px] font-bold text-violet-800">
                            <i class="fas fa-sitemap text-[10px]"></i>
                            {{ $childrenCount }}
                        </span>
                    @endif
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition-transform duration-200 group-open/section:rotate-180" aria-hidden="true">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </span>
                </div>
            </summary>

            <div class="bg-slate-50/40">
                @if($section->items->isNotEmpty())
                    <ul class="divide-y divide-slate-100 border-b border-slate-100/80" role="list">
                        @foreach($section->items as $cItem)
                            @php $m = $cItem->item; @endphp
                            @if(!$m)
                                @continue
                            @endif

                            @if($m instanceof \App\Models\OfflineCurriculumNote)
                                <li class="flex gap-3 px-4 py-3.5 sm:px-5 sm:py-4 transition-colors hover:bg-white/80">
                                    <span class="mt-0.5 w-1 shrink-0 self-stretch rounded-full bg-slate-300" aria-hidden="true"></span>
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-200 text-slate-700 ring-1 ring-slate-300/40">
                                        <i class="fas fa-align-right text-sm"></i>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <span class="mb-1 inline-block text-[10px] font-bold uppercase tracking-wide text-slate-500">ملاحظة</span>
                                        <p class="font-semibold text-slate-900">{{ $m->title }}</p>
                                        @if($m->body)
                                            <div class="mt-1.5 text-sm leading-relaxed text-slate-600 whitespace-pre-line">{{ $m->body }}</div>
                                        @endif
                                    </div>
                                </li>
                            @elseif($m instanceof \App\Models\OfflineLecture)
                                <li class="flex gap-3 px-4 py-3.5 sm:px-5 sm:py-4 transition-colors hover:bg-white/80">
                                    <span class="mt-0.5 w-1 shrink-0 self-stretch rounded-full bg-violet-500" aria-hidden="true"></span>
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-violet-600 ring-1 ring-violet-200/60">
                                        <i class="fas fa-chalkboard-teacher text-sm"></i>
                                    </span>
                                    <div class="min-w-0 flex-1 space-y-2">
                                        <div class="flex flex-wrap items-start justify-between gap-2">
                                            <div class="min-w-0">
                                                <span class="mb-1 inline-block text-[10px] font-bold uppercase tracking-wide text-violet-700">محاضرة</span>
                                                <p class="text-base font-bold text-slate-900">{{ $m->title }}</p>
                                            </div>
                                            <a href="{{ route($sg . '.lectures', $offlineCourse) }}#offline-lecture-{{ $m->id }}" class="inline-flex shrink-0 items-center gap-1.5 rounded-xl bg-violet-600 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-violet-700">
                                                عرض
                                                <i class="fas fa-chevron-left text-[10px] opacity-90"></i>
                                            </a>
                                        </div>
                                        @if($m->relationLoaded('groupSession') && $m->groupSession)
                                            <p class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-semibold text-violet-800">
                                                <i class="far fa-calendar-check text-violet-500"></i>
                                                <span>{{ $m->groupSession->session_date->translatedFormat('l j F Y') }}</span>
                                                @php $sgt = $m->groupSession->start_time; @endphp
                                                <span class="text-slate-500">· {{ is_string($sgt) ? substr($sgt, 0, 5) : $sgt }}</span>
                                                @if($m->groupSession->group)
                                                    <span class="text-slate-500">· {{ $m->groupSession->group->name }}</span>
                                                @endif
                                            </p>
                                        @endif
                                        @if($m->scheduled_at)
                                            <p class="text-xs text-slate-500"><i class="far fa-clock ml-1 text-slate-400"></i>{{ $m->scheduled_at->translatedFormat('l j F Y — H:i') }}</p>
                                        @endif
                                        @if(filled($m->session_agenda))
                                            @php
                                                $agLines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $m->session_agenda))));
                                                $agShow = array_slice($agLines, 0, 5);
                                                $agMore = max(0, count($agLines) - 5);
                                            @endphp
                                            @if(count($agShow))
                                                <div class="rounded-xl border border-slate-100 bg-white/90 px-3 py-2.5">
                                                    <p class="mb-1.5 text-[10px] font-bold uppercase tracking-wide text-slate-500">برنامج اليوم</p>
                                                    <ul class="space-y-1.5 text-xs text-slate-700">
                                                        @foreach($agShow as $line)
                                                            <li class="flex gap-2 leading-relaxed">
                                                                <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-violet-400" aria-hidden="true"></span>
                                                                <span>{{ $line }}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                    @if($agMore > 0)
                                                        <p class="mt-2 text-[11px] font-medium text-slate-400">+ {{ $agMore }} نقطة في صفحة المحاضرة</p>
                                                    @endif
                                                </div>
                                            @endif
                                        @endif
                                        @if($m->description)
                                            <p class="text-xs leading-relaxed text-slate-600 line-clamp-3">{{ Str::limit($m->description, 220) }}</p>
                                        @endif
                                        @include('partials.offline-mindmap-visual', ['text' => $m->offline_attendee_mindmap])
                                    </div>
                                </li>
                            @elseif($m instanceof \App\Models\OfflineCourseResource)
                                <li class="flex gap-3 px-4 py-3.5 sm:px-5 sm:py-4 transition-colors hover:bg-white/80">
                                    <span class="mt-0.5 w-1 shrink-0 self-stretch rounded-full bg-sky-500" aria-hidden="true"></span>
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-600 ring-1 ring-sky-200/60">
                                        <i class="fas fa-file-alt text-sm"></i>
                                    </span>
                                    <div class="flex min-w-0 flex-1 flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="min-w-0">
                                            <span class="mb-1 inline-block text-[10px] font-bold uppercase tracking-wide text-sky-700">مورد</span>
                                            <p class="font-semibold text-slate-900">{{ $m->title }}</p>
                                            @if($m->description)
                                                <p class="mt-0.5 text-xs text-slate-500 line-clamp-2">{{ Str::limit(strip_tags($m->description), 140) }}</p>
                                            @endif
                                        </div>
                                        <a href="{{ route($sg . '.resources', $offlineCourse) }}#offline-resource-{{ $m->id }}" class="inline-flex shrink-0 items-center gap-1.5 self-start rounded-xl border border-sky-200 bg-white px-3 py-2 text-xs font-bold text-sky-700 shadow-sm transition hover:bg-sky-50 sm:self-center">
                                            فتح
                                            <i class="fas fa-chevron-left text-[10px]"></i>
                                        </a>
                                    </div>
                                </li>
                            @elseif($m instanceof \App\Models\OfflineActivity)
                                <li class="flex gap-3 px-4 py-3.5 sm:px-5 sm:py-4 transition-colors hover:bg-white/80">
                                    <span class="mt-0.5 w-1 shrink-0 self-stretch rounded-full bg-amber-500" aria-hidden="true"></span>
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700 ring-1 ring-amber-200/60">
                                        <i class="fas fa-tasks text-sm"></i>
                                    </span>
                                    <div class="flex min-w-0 flex-1 flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="min-w-0">
                                            <span class="mb-1 inline-block text-[10px] font-bold uppercase tracking-wide text-amber-800">نشاط</span>
                                            <p class="font-semibold text-slate-900">{{ $m->title }}</p>
                                            <p class="text-[11px] text-slate-500">{{ $m->type }}</p>
                                        </div>
                                        <a href="{{ route($sg . '.activities.show', [$offlineCourse, $m]) }}" class="inline-flex shrink-0 items-center gap-1.5 self-start rounded-xl bg-amber-500 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-amber-600 sm:self-center">
                                            عرض / تسليم
                                            <i class="fas fa-chevron-left text-[10px]"></i>
                                        </a>
                                    </div>
                                </li>
                            @elseif($m instanceof \App\Models\AdvancedExam)
                                <li class="flex gap-3 px-4 py-3.5 sm:px-5 sm:py-4 transition-colors hover:bg-white/80">
                                    <span class="mt-0.5 w-1 shrink-0 self-stretch rounded-full bg-emerald-500" aria-hidden="true"></span>
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200/60">
                                        <i class="fas fa-clipboard-check text-sm"></i>
                                    </span>
                                    <div class="flex min-w-0 flex-1 flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="min-w-0">
                                            <span class="mb-1 inline-block text-[10px] font-bold uppercase tracking-wide text-emerald-800">اختبار</span>
                                            <p class="font-semibold text-slate-900">{{ $m->title }}</p>
                                            @if($m->description)
                                                <p class="mt-0.5 text-xs text-slate-500 line-clamp-2">{{ Str::limit(strip_tags($m->description), 120) }}</p>
                                            @endif
                                        </div>
                                        <a href="{{ route('student.exams.show', $m) }}" class="inline-flex shrink-0 items-center gap-1.5 self-start rounded-xl border border-emerald-200 bg-white px-3 py-2 text-xs font-bold text-emerald-800 shadow-sm transition hover:bg-emerald-50 sm:self-center">
                                            الاختبار
                                            <i class="fas fa-chevron-left text-[10px]"></i>
                                        </a>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @else
                    <div class="border-b border-slate-100 px-4 py-6 text-center text-sm text-slate-400">لا عناصر في هذا القسم.</div>
                @endif

                @if($section->children && $section->children->isNotEmpty())
                    <div class="bg-slate-50/80 px-3 py-4 sm:px-4">
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
    </article>
@endforeach
