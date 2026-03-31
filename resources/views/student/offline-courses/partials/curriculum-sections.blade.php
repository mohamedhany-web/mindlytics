@foreach($sections as $section)
    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden shadow-sm">
        <div class="px-4 py-3 bg-gradient-to-l from-sky-50 to-indigo-50 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-base">{{ $section->title }}</h3>
            @if($section->description && ! $section->parent_id)
                <p class="text-sm text-slate-600 mt-1 leading-relaxed whitespace-pre-line">{{ $section->description }}</p>
            @endif
        </div>
        <div class="p-4 space-y-2">
            @forelse($section->items as $cItem)
                @php $m = $cItem->item; @endphp
                @if(!$m)
                    @continue
                @endif
                <div class="flex flex-wrap items-start gap-3 p-3 rounded-lg border border-slate-100 bg-slate-50/60 hover:border-sky-200 transition-colors">
                    @if($m instanceof \App\Models\OfflineCurriculumNote)
                        <span class="shrink-0 w-9 h-9 rounded-lg bg-slate-200 text-slate-700 flex items-center justify-center"><i class="fas fa-align-right text-sm"></i></span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-900">{{ $m->title }}</p>
                            @if($m->body)
                                <div class="text-sm text-slate-600 mt-1 leading-relaxed whitespace-pre-line">{{ $m->body }}</div>
                            @endif
                        </div>
                    @elseif($m instanceof \App\Models\OfflineLecture)
                        <span class="shrink-0 w-9 h-9 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center"><i class="fas fa-chalkboard-teacher text-sm"></i></span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-900">{{ $m->title }}</p>
                            @if($m->description)
                                <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">{{ Str::limit(strip_tags($m->description), 120) }}</p>
                            @endif
                            <a href="{{ route('student.offline-courses.lectures', ['offlineCourse' => $offlineCourse, 'channel' => ($channel ?? request('channel', 'offline'))]) }}#offline-lecture-{{ $m->id }}" class="inline-flex items-center gap-1.5 mt-2 text-sm font-semibold text-violet-600 hover:text-violet-800">
                                عرض المحاضرة <i class="fas fa-arrow-left text-xs"></i>
                            </a>
                        </div>
                    @elseif($m instanceof \App\Models\OfflineCourseResource)
                        <span class="shrink-0 w-9 h-9 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center"><i class="fas fa-file-alt text-sm"></i></span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-900">{{ $m->title }}</p>
                            @if($m->description)
                                <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">{{ Str::limit(strip_tags($m->description), 120) }}</p>
                            @endif
                            <a href="{{ route('student.offline-courses.resources', ['offlineCourse' => $offlineCourse, 'channel' => ($channel ?? request('channel', 'offline'))]) }}#offline-resource-{{ $m->id }}" class="inline-flex items-center gap-1.5 mt-2 text-sm font-semibold text-sky-600 hover:text-sky-800">
                                فتح المورد <i class="fas fa-arrow-left text-xs"></i>
                            </a>
                        </div>
                    @elseif($m instanceof \App\Models\OfflineActivity)
                        <span class="shrink-0 w-9 h-9 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center"><i class="fas fa-tasks text-sm"></i></span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-900">{{ $m->title }}</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">{{ $m->type }}</p>
                            <a href="{{ route('student.offline-courses.activities.show', ['offlineCourse' => $offlineCourse, 'activity' => $m, 'channel' => ($channel ?? request('channel', 'offline'))]) }}" class="inline-flex items-center gap-1.5 mt-2 text-sm font-semibold text-amber-700 hover:text-amber-900">
                                عرض / تسليم <i class="fas fa-arrow-left text-xs"></i>
                            </a>
                        </div>
                    @elseif($m instanceof \App\Models\AdvancedExam)
                        <span class="shrink-0 w-9 h-9 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center"><i class="fas fa-clipboard-check text-sm"></i></span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-900">{{ $m->title }}</p>
                            @if($m->description)
                                <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">{{ Str::limit(strip_tags($m->description), 100) }}</p>
                            @endif
                            <a href="{{ route('student.exams.show', $m->id) }}" class="inline-flex items-center gap-1.5 mt-2 text-sm font-semibold text-emerald-700 hover:text-emerald-900">
                                صفحة الاختبار <i class="fas fa-arrow-left text-xs"></i>
                            </a>
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-sm text-slate-400 text-center py-4">لا عناصر في هذا القسم.</p>
            @endforelse
        </div>
        @if($section->children && $section->children->isNotEmpty())
            <div class="px-3 pb-3 space-y-3 border-t border-slate-100 bg-slate-50/30">
                @include('student.offline-courses.partials.curriculum-sections', ['sections' => $section->children, 'offlineCourse' => $offlineCourse])
            </div>
        @endif
    </div>
@endforeach
