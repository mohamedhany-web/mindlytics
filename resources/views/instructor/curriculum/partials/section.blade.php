@php
    $depth = $depth ?? 0;
    $marginClass = $depth > 0 ? 'mr-4 border-r-2 border-slate-200' : '';
@endphp
<div class="rounded-xl bg-white border border-slate-200 shadow-sm hover:shadow-md transition-shadow mb-4 section-block {{ $marginClass }}" data-section-id="{{ $section->id }}" style="{{ $depth > 0 ? 'margin-right: ' . ($depth * 1.5) . 'rem;' : '' }}">
    <div class="flex items-center justify-between p-4 cursor-pointer section-header" onclick="toggleSection({{ $section->id }})">
        <div class="flex items-center gap-3 flex-1 min-w-0">
            <span class="section-chevron text-slate-400 transition-transform duration-200" data-section-id="{{ $section->id }}">
                <i class="fas fa-chevron-down"></i>
            </span>
            <div class="flex-1 min-w-0">
                <h3 class="text-lg font-bold text-slate-800">{{ $section->title }}</h3>
                @if($section->description)
                    <p class="text-sm text-slate-500 truncate">{{ $section->description }}</p>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0" onclick="event.stopPropagation();">
            <button onclick="event.stopPropagation(); editSection({{ $section->id }}, '{{ addslashes($section->title) }}', '{{ addslashes($section->description ?? '') }}', {{ $section->parent_id ?? 'null' }}, '{{ $section->unlock_rule ?? 'previous_all_items' }}', {{ $section->unlock_percent !== null ? (int)$section->unlock_percent : 'null' }})"
                    class="p-2 rounded-lg bg-sky-100 hover:bg-sky-200 text-sky-600 text-sm transition-colors" title="تعديل القسم">
                <i class="fas fa-edit"></i>
            </button>
            <button onclick="event.stopPropagation(); deleteSection({{ $section->id }})"
                    class="p-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 text-sm transition-colors" title="حذف القسم">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>

    <div class="section-body px-4 pb-4 border-t border-slate-100">
        <div class="mb-4 flex flex-wrap items-center gap-2 p-3 bg-slate-50 rounded-lg border border-slate-200 mt-4">
            <span class="text-xs font-semibold text-slate-600 mr-2">إضافة:</span>
            <button type="button" onclick="event.stopPropagation(); showAddSubSectionModal({{ $section->id }})"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-600 hover:bg-slate-700 text-white rounded-lg text-xs font-semibold transition-colors"
                    title="قسم فرعي داخل هذا القسم">
                <i class="fas fa-folder-plus"></i>
                <span>قسم فرعي</span>
            </button>
            <button onclick="event.stopPropagation(); showAddLectureModal({{ $section->id }})"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-sky-500 hover:bg-sky-600 text-white rounded-lg text-xs font-semibold transition-colors">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>محاضرة</span>
            </button>
            <button type="button" onclick="event.stopPropagation(); showAddExamModal({{ $section->id }})"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-violet-500 hover:bg-violet-600 text-white rounded-lg text-xs font-semibold transition-colors">
                <i class="fas fa-clipboard-check"></i>
                <span>امتحان</span>
            </button>
            <button type="button" onclick="event.stopPropagation(); showAddAssignmentModal({{ $section->id }})"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-xs font-semibold transition-colors">
                <i class="fas fa-tasks"></i>
                <span>واجب</span>
            </button>
            <a href="{{ route('instructor.learning-patterns.create', $course) }}?section_id={{ $section->id }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-semibold transition-colors"
               title="إضافة نمط تعليمي تفاعلي">
                <i class="fas fa-puzzle-piece"></i>
                <span>نمط تعليمي</span>
            </a>
        </div>

        <div class="items-container" data-section-id="{{ $section->id }}">
            @php $sectionItems = $section->items->filter(fn($i) => !($i->item instanceof \App\Models\CourseLesson)); @endphp
            @forelse($sectionItems as $item)
                <div class="item-card rounded-lg p-3 mb-2 bg-white border border-slate-200 hover:border-sky-300 hover:shadow-sm transition-all cursor-move" data-item-id="{{ $item->id }}">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <i class="fas fa-grip-vertical text-slate-400 cursor-move shrink-0"></i>
                            @if($item->item instanceof \App\Models\Lecture)
                                <i class="fas fa-chalkboard-teacher text-sky-500 shrink-0"></i>
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="font-semibold text-slate-800 truncate">{{ $item->item->title }}</span>
                                    @if($item->item->videoQuestions()->exists())
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[10px] shrink-0" title="توجد أسئلة مرتبطة بالفيديو">
                                            <i class="fas fa-question-circle"></i>
                                            <span>أسئلة فيديو</span>
                                        </span>
                                    @endif
                                </div>
                                <span class="text-xs text-slate-500 shrink-0">(محاضرة)</span>
                                <div class="flex items-center gap-1 shrink-0">
                                    <button type="button" onclick="openVideoQuestionsModal({{ $item->item->id }}, '{{ addslashes($item->item->title) }}')" class="p-1.5 rounded bg-amber-100 hover:bg-amber-200 text-amber-700 text-xs" title="أسئلة الفيديو"><i class="fas fa-question-circle"></i></button>
                                    <button onclick="editLectureFromCurriculum({{ $item->item->id }}, {{ $section->id }})" class="p-1.5 rounded bg-sky-100 hover:bg-sky-200 text-sky-600 text-xs" title="تعديل المحاضرة"><i class="fas fa-edit"></i></button>
                                    <button onclick="deleteLectureFromCurriculum({{ $item->item->id }}, {{ $item->id }})" class="p-1.5 rounded bg-red-50 hover:bg-red-100 text-red-600 text-xs" title="حذف المحاضرة"><i class="fas fa-trash"></i></button>
                                </div>
                            @elseif($item->item instanceof \App\Models\Assignment)
                                <i class="fas fa-tasks text-emerald-500 shrink-0"></i>
                                <span class="font-semibold text-slate-800 truncate">{{ $item->item->title }}</span>
                                <span class="text-xs text-slate-500 shrink-0">(واجب)</span>
                                <div class="flex items-center gap-1 shrink-0">
                                    <a href="{{ route('instructor.assignments.edit', $item->item) }}" class="p-1.5 rounded bg-emerald-100 hover:bg-emerald-200 text-emerald-600 text-xs" title="تعديل الواجب"><i class="fas fa-edit"></i></a>
                                    <button onclick="removeItem({{ $item->id }})" class="p-1.5 rounded bg-red-50 hover:bg-red-100 text-red-600 text-xs" title="إزالة من المنهج"><i class="fas fa-times"></i></button>
                                </div>
                            @elseif($item->item instanceof \App\Models\AdvancedExam || $item->item instanceof \App\Models\Exam)
                                <i class="fas fa-clipboard-check text-violet-500 shrink-0"></i>
                                <span class="font-semibold text-slate-800 truncate">{{ $item->item->title }}</span>
                                <span class="text-xs text-slate-500 shrink-0">(امتحان)</span>
                                <div class="flex items-center gap-1 shrink-0">
                                    @if($item->item instanceof \App\Models\AdvancedExam)
                                        <a href="{{ route('instructor.exams.edit', $item->item) }}" class="p-1.5 rounded bg-violet-100 hover:bg-violet-200 text-violet-600 text-xs" title="تعديل الامتحان"><i class="fas fa-edit"></i></a>
                                    @endif
                                    <button onclick="removeItem({{ $item->id }})" class="p-1.5 rounded bg-red-50 hover:bg-red-100 text-red-600 text-xs" title="إزالة من المنهج"><i class="fas fa-times"></i></button>
                                </div>
                            @elseif($item->item instanceof \App\Models\LearningPattern)
                                @php $typeInfo = $item->item->getTypeInfo(); @endphp
                                <i class="{{ $typeInfo['icon'] ?? 'fas fa-puzzle-piece' }} text-amber-500 shrink-0"></i>
                                <span class="font-semibold text-slate-800 truncate">{{ $item->item->title }}</span>
                                <span class="text-xs text-slate-500 shrink-0">({{ $typeInfo['name'] ?? 'نمط تعليمي' }})</span>
                                <div class="flex items-center gap-1 shrink-0">
                                    <a href="{{ route('instructor.learning-patterns.edit', [$course, $item->item]) }}" class="p-1.5 rounded bg-amber-100 hover:bg-amber-200 text-amber-600 text-xs" title="تعديل النمط"><i class="fas fa-edit"></i></a>
                                    <button onclick="removeItem({{ $item->id }})" class="p-1.5 rounded bg-red-50 hover:bg-red-100 text-red-600 text-xs" title="إزالة من المنهج"><i class="fas fa-times"></i></button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-slate-500 border border-dashed border-slate-200 rounded-lg bg-slate-50/50">
                    <i class="fas fa-inbox text-2xl mb-2 text-slate-400"></i>
                    <p class="text-sm mb-1">لا توجد عناصر في هذا القسم</p>
                    <p class="text-xs text-slate-400">أضف محاضرات أو امتحانات أو واجبات من الأزرار أعلاه</p>
                </div>
            @endforelse
        </div>

        @if($section->children && $section->children->count() > 0)
            <div class="sections-children mt-4 pr-4 border-r-2 border-slate-100 space-y-4" data-parent-id="{{ $section->id }}" style="margin-right: 1rem;">
                @foreach($section->children as $child)
                    @include('instructor.curriculum.partials.section', ['section' => $child, 'depth' => $depth + 1])
                @endforeach
            </div>
        @else
            <div class="sections-children empty-drop-zone mt-4 pr-4 border-r-2 border-slate-100 border-dashed min-h-[52px] rounded-lg bg-slate-50/70 flex items-center justify-center transition-all" data-parent-id="{{ $section->id }}" style="margin-right: 1rem;" data-empty="1"><span class="text-xs text-slate-400 opacity-0 group-hover:opacity-100 curriculum-drag-hint">أفلت قسم هنا</span></div>
        @endif
    </div>
</div>
