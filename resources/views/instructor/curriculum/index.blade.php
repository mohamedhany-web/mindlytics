@extends('layouts.app')

@section('title', 'بناء المنهج - ' . $course->title)
@section('header', 'بناء المنهج - ' . $course->title)

@push('styles')
<style>
    .sortable-ghost { opacity: 0.4; }
    #lectureModal { backdrop-filter: blur(4px); }
    .section-block.collapsed .section-body { display: none; }
    .section-block.collapsed .section-chevron { transform: rotate(-90deg); }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-1">بناء المنهج الدراسي</h1>
                <p class="text-sm text-slate-500">{{ $course->title }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('instructor.lectures.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>المحاضرات</span>
                </a>
                <a href="{{ route('instructor.courses.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    <span>العودة</span>
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- الأقسام والمنهج -->
        <div class="lg:col-span-2 space-y-6">
            <!-- الأقسام -->
            <div id="sections-container">
                @forelse($sections as $section)
                    <div class="rounded-xl bg-white border border-slate-200 shadow-sm hover:shadow-md transition-shadow mb-4 section-block" data-section-id="{{ $section->id }}">
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
                                <button onclick="event.stopPropagation(); editSection({{ $section->id }}, '{{ addslashes($section->title) }}', '{{ addslashes($section->description ?? '') }}')" 
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
                            <button onclick="showAddLectureModal({{ $section->id }})" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-sky-500 hover:bg-sky-600 text-white rounded-lg text-xs font-semibold transition-colors">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <span>محاضرة</span>
                            </button>
                            <button type="button" onclick="showAddExamModal({{ $section->id }})" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-violet-500 hover:bg-violet-600 text-white rounded-lg text-xs font-semibold transition-colors">
                                <i class="fas fa-clipboard-check"></i>
                                <span>امتحان</span>
                            </button>
                            <button type="button" onclick="showAddAssignmentModal({{ $section->id }})" 
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
                                                <span class="font-semibold text-slate-800 truncate">{{ $item->item->title }}</span>
                                                <span class="text-xs text-slate-500 shrink-0">(محاضرة)</span>
                                                <div class="flex items-center gap-1 shrink-0">
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
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 bg-white rounded-xl border border-dashed border-slate-200">
                        <i class="fas fa-folder-open text-4xl text-slate-300 mb-4"></i>
                        <p class="text-slate-600 mb-4">لا توجد أقسام بعد</p>
                        <button onclick="showAddSectionModal()" 
                                class="inline-flex items-center gap-2 px-6 py-3 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                            <i class="fas fa-plus"></i>
                            إضافة قسم جديد
                        </button>
                    </div>
                @endforelse
            </div>

            @if($sections->count() > 0)
                <button onclick="showAddSectionModal()" 
                        class="w-full py-3 bg-white border border-slate-200 rounded-xl font-semibold text-slate-700 hover:bg-slate-50 transition-colors inline-flex items-center justify-center gap-2">
                    <i class="fas fa-plus"></i>
                    إضافة قسم جديد
                </button>
            @endif
        </div>

        <!-- العناصر المتاحة -->
        <div class="rounded-xl p-5 bg-slate-50 border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-800">العناصر المتاحة</h3>
                <a href="{{ route('instructor.learning-patterns.index', $course) }}" 
                   class="px-3 py-2 bg-violet-500 hover:bg-violet-600 text-white rounded-lg text-xs font-semibold transition-colors">
                    <i class="fas fa-puzzle-piece ml-1"></i>
                    إدارة الأنماط
                </a>
            </div>

            @if($availableLectures->count() > 0)
                <div class="mb-5">
                    <h4 class="text-sm font-semibold text-slate-600 mb-2 flex items-center gap-2">
                        <i class="fas fa-chalkboard-teacher text-sky-500"></i>
                        المحاضرات ({{ $availableLectures->count() }})
                    </h4>
                    <div class="space-y-2">
                        @foreach($availableLectures as $lecture)
                            <div class="p-3 bg-white rounded-lg border border-slate-200 hover:border-sky-300 hover:bg-sky-50/50 transition-all cursor-pointer"
                                 onclick="showAddItemModal('App\\Models\\Lecture', {{ $lecture->id }}, '{{ addslashes($lecture->title) }}', 'محاضرة')">
                                <div class="font-semibold text-sm text-slate-800">{{ $lecture->title }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($availableAssignments->count() > 0)
                <div class="mb-5">
                    <h4 class="text-sm font-semibold text-slate-600 mb-2 flex items-center gap-2">
                        <i class="fas fa-tasks text-emerald-500"></i>
                        الواجبات ({{ $availableAssignments->count() }})
                    </h4>
                    <div class="space-y-2">
                        @foreach($availableAssignments as $assignment)
                            <div class="p-3 bg-white rounded-lg border border-slate-200 hover:border-sky-300 hover:bg-sky-50/50 transition-all cursor-pointer"
                                 onclick="showAddItemModal('App\\Models\\Assignment', {{ $assignment->id }}, '{{ addslashes($assignment->title) }}', 'واجب')">
                                <div class="font-semibold text-sm text-slate-800">{{ $assignment->title }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(isset($availableExams) && $availableExams->count() > 0)
                <div class="mb-5">
                    <h4 class="text-sm font-semibold text-slate-600 mb-2 flex items-center gap-2">
                        <i class="fas fa-clipboard-check text-violet-500"></i>
                        الامتحانات ({{ $availableExams->count() }})
                    </h4>
                    <div class="space-y-2">
                        @foreach($availableExams as $exam)
                            <div class="p-3 bg-white rounded-lg border border-slate-200 hover:border-sky-300 hover:bg-sky-50/50 transition-all cursor-pointer"
                                 onclick="showAddItemModal('App\\Models\\AdvancedExam', {{ $exam->id }}, '{{ addslashes($exam->title) }}', 'امتحان')">
                                <div class="font-semibold text-sm text-slate-800">{{ $exam->title }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(isset($availableLearningPatterns) && $availableLearningPatterns->count() > 0)
                <div class="mb-5">
                    <h4 class="text-sm font-semibold text-slate-600 mb-2 flex items-center gap-2">
                        <i class="fas fa-puzzle-piece text-amber-500"></i>
                        الأنماط التعليمية ({{ $availableLearningPatterns->count() }})
                    </h4>
                    <div class="space-y-2">
                        @foreach($availableLearningPatterns as $pattern)
                            @php $typeInfo = $pattern->getTypeInfo(); @endphp
                            <div class="p-3 bg-white rounded-lg border border-slate-200 hover:border-sky-300 hover:bg-sky-50/50 transition-all cursor-pointer"
                                 onclick="showAddItemModal('App\\Models\\LearningPattern', {{ $pattern->id }}, '{{ addslashes($pattern->title) }}', '{{ addslashes($typeInfo['name'] ?? 'نمط تعليمي') }}')">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <i class="{{ $typeInfo['icon'] ?? 'fas fa-puzzle-piece' }} text-amber-500"></i>
                                    <span class="font-semibold text-sm text-slate-800">{{ $pattern->title }}</span>
                                </div>
                                <div class="text-xs text-slate-500">{{ $typeInfo['name'] ?? 'نمط تعليمي' }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($availableLectures->count() == 0 && $availableAssignments->count() == 0 && (!isset($availableExams) || $availableExams->count() == 0) && (!isset($availableLearningPatterns) || $availableLearningPatterns->count() == 0))
                <div class="text-center py-8 text-slate-500">
                    <i class="fas fa-check-circle text-2xl mb-2 text-emerald-400"></i>
                    <p class="text-sm">جميع العناصر مضافة للمنهج</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal إضافة/تعديل قسم -->
<div id="sectionModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-xl border border-slate-200">
        <h3 class="text-xl font-bold text-slate-800 mb-4" id="modalTitle">إضافة قسم جديد</h3>
        <form id="sectionForm" onsubmit="saveSection(event)">
            <input type="hidden" id="sectionId">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-slate-700 mb-2">عنوان القسم</label>
                <input type="text" id="sectionTitle" required 
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-slate-700 mb-2">الوصف (اختياري)</label>
                <textarea id="sectionDescription" rows="3"
                          class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    حفظ
                </button>
                <button type="button" onclick="closeSectionModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal إضافة محاضرة (عرض الصفحة) -->
<div id="lectureModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 overflow-y-auto p-4">
    <div class="bg-white rounded-2xl p-6 w-full max-w-6xl my-8 max-h-[90vh] overflow-y-auto shadow-xl border border-slate-200">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-slate-800">إضافة محاضرة جديدة</h3>
            <button onclick="closeLectureModal()" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="lectureForm" onsubmit="saveLecture(event)">
            @csrf
            <input type="hidden" id="lectureSectionId">
            <input type="hidden" name="course_id" value="{{ $course->id }}">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">عنوان المحاضرة <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="lectureTitle" required placeholder="مثال: مقدمة في JavaScript"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الوصف</label>
                    <textarea name="description" id="lectureDescription" rows="3" placeholder="وصف مختصر للمحاضرة..."
                              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800"></textarea>
                </div>
                <input type="hidden" name="course_lesson_id" value="">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">التاريخ والوقت <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="scheduled_at" id="lectureScheduledAt" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">المدة (بالدقائق) <span class="text-red-500">*</span></label>
                        <input type="number" name="duration_minutes" id="lectureDuration" value="60" min="15" max="480" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                    </div>
                </div>
            </div>
            <div class="space-y-5">
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-2"><i class="fas fa-video text-sky-500 ml-1"></i> رابط تسجيل المحاضرة (اختياري)</label>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2">اختر المشغل</label>
                        <div class="grid grid-cols-4 gap-2 mb-3">
                            <button type="button" onclick="selectVideoPlatform('youtube', this)"
                                    class="platform-btn p-3 border-2 border-slate-200 rounded-lg text-center hover:border-sky-400 transition-colors" data-platform="youtube">
                                <i class="fab fa-youtube text-red-600 text-xl mb-1 block"></i>
                                <span class="text-xs font-semibold text-slate-700">YouTube</span>
                            </button>
                            <button type="button" onclick="selectVideoPlatform('vimeo', this)"
                                    class="platform-btn p-3 border-2 border-slate-200 rounded-lg text-center hover:border-sky-400 transition-colors" data-platform="vimeo">
                                <i class="fab fa-vimeo text-blue-500 text-xl mb-1 block"></i>
                                <span class="text-xs font-semibold text-slate-700">Vimeo</span>
                            </button>
                            <button type="button" onclick="selectVideoPlatform('google_drive', this)"
                                    class="platform-btn p-3 border-2 border-slate-200 rounded-lg text-center hover:border-sky-400 transition-colors" data-platform="google_drive">
                                <i class="fab fa-google-drive text-green-600 text-xl mb-1 block"></i>
                                <span class="text-xs font-semibold text-slate-700">Drive</span>
                            </button>
                            <button type="button" onclick="selectVideoPlatform('direct', this)"
                                    class="platform-btn p-3 border-2 border-slate-200 rounded-lg text-center hover:border-sky-400 transition-colors" data-platform="direct">
                                <i class="fas fa-file-video text-purple-600 text-xl mb-1 block"></i>
                                <span class="text-xs font-semibold text-slate-700">مباشر</span>
                            </button>
                        </div>
                        <input type="hidden" name="video_platform" id="lectureVideoPlatform" value="">
                    </div>
                    <div>
                        <input type="url" name="recording_url" id="lectureRecordingUrl" placeholder="ضع رابط الفيديو هنا..." oninput="previewLectureVideo()"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                        <p class="mt-1 text-xs text-slate-500" id="lectureVideoPlaceholder"></p>
                    </div>
                    <div id="lectureVideoPreview" class="hidden mt-3 bg-black rounded-lg overflow-hidden" style="aspect-ratio: 16/9; max-height: 200px;">
                        <div id="lectureVideoPreviewContent" class="w-full h-full flex items-center justify-center text-white">
                            <i class="fas fa-spinner fa-spin text-2xl"></i>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">رابط تسجيل Teams</label>
                        <input type="url" name="teams_registration_link" id="lectureTeamsRegistration" placeholder="https://teams.microsoft.com/..."
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">رابط اجتماع Teams</label>
                        <input type="url" name="teams_meeting_link" id="lectureTeamsMeeting" placeholder="https://teams.microsoft.com/..."
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الملاحظات</label>
                    <textarea name="notes" id="lectureNotes" rows="3" placeholder="ملاحظات إضافية..."
                              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800"></textarea>
                </div>
                <div class="space-y-2">
                    <label class="flex items-center gap-3 p-3 bg-sky-50 rounded-xl cursor-pointer border border-sky-100">
                        <input type="checkbox" name="has_attendance_tracking" value="1" checked class="w-4 h-4 text-sky-500 border-slate-300 rounded">
                        <span class="font-semibold text-slate-800">تتبع الحضور</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 bg-emerald-50 rounded-xl cursor-pointer border border-emerald-100">
                        <input type="checkbox" name="has_assignment" value="1" class="w-4 h-4 text-sky-500 border-slate-300 rounded">
                        <span class="font-semibold text-slate-800">يوجد واجب</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 bg-amber-50 rounded-xl cursor-pointer border border-amber-100">
                        <input type="checkbox" name="has_evaluation" value="1" class="w-4 h-4 text-sky-500 border-slate-300 rounded">
                        <span class="font-semibold text-slate-800">يوجد تقييم</span>
                    </label>
                </div>
            </div>
            <div class="flex gap-3 mt-6 col-span-full">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-save"></i>
                    <span id="lectureSubmitText">حفظ وإضافة للمنهج</span>
                </button>
                <button type="button" onclick="closeLectureModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    إلغاء
                </button>
            </div>
            </div>
            <input type="hidden" id="lectureEditId" name="lecture_id">
        </form>
    </div>
</div>

<!-- Modal إضافة امتحان من المنهج (عريض) -->
<div id="examModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-4xl my-8">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-violet-100 flex items-center justify-center text-violet-600"><i class="fas fa-clipboard-check"></i></div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">إضافة امتحان جديد</h3>
                    <p class="text-xs text-slate-500">يُضاف لهذا القسم في الكورس الحالي</p>
                </div>
            </div>
            <button type="button" onclick="closeExamModal()" class="p-2 rounded-lg text-slate-500 hover:bg-slate-200 transition-colors"><i class="fas fa-times"></i></button>
        </div>
        <form id="examForm" onsubmit="saveExam(event)">
            @csrf
            <input type="hidden" name="section_id" id="examSectionId" value="">
            <input type="hidden" name="course_lesson_id" value="">
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">عنوان الامتحان <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="examTitle" required placeholder="مثال: اختبار الوحدة الأولى"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">الوصف</label>
                    <textarea name="description" id="examDescription" rows="3" placeholder="وصف مختصر..."
                              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800 resize-none"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">الدرجة الكلية <span class="text-red-500">*</span></label>
                        <input type="number" name="total_marks" id="examTotalMarks" value="100" min="1" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">درجة النجاح <span class="text-red-500">*</span></label>
                        <input type="number" name="passing_marks" id="examPassingMarks" value="60" min="0" step="0.5" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">المدة (دقيقة) <span class="text-red-500">*</span></label>
                        <input type="number" name="duration_minutes" id="examDuration" value="60" min="5" max="480" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">المحاولات <span class="text-red-500">*</span></label>
                        <input type="number" name="attempts_allowed" id="examAttempts" value="1" min="1" max="10" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-200 flex gap-3">
                <button type="submit" id="examSubmitBtn" class="flex-1 px-4 py-2.5 bg-violet-500 hover:bg-violet-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-plus ml-1"></i> إنشاء وإضافة للمنهج
                </button>
                <button type="button" onclick="closeExamModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal إنشاء واجب وإضافة للمنهج (عريض) -->
<div id="assignmentModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-4xl my-8">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600"><i class="fas fa-tasks"></i></div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">إنشاء واجب جديد</h3>
                    <p class="text-xs text-slate-500">يُضاف مباشرة لهذا القسم في الكورس الحالي</p>
                </div>
            </div>
            <button type="button" onclick="closeAssignmentModal()" class="p-2 rounded-lg text-slate-500 hover:bg-slate-200 transition-colors"><i class="fas fa-times"></i></button>
        </div>
        <form id="assignmentFormCurriculum" onsubmit="saveAssignment(event)">
            <input type="hidden" name="section_id" id="assignmentSectionId" value="">
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">عنوان الواجب <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="assignmentTitle" required placeholder="مثال: واجب الوحدة الأولى"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">الوصف</label>
                    <textarea name="description" id="assignmentDescription" rows="2" placeholder="وصف مختصر..."
                              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">التعليمات</label>
                    <textarea name="instructions" id="assignmentInstructions" rows="2" placeholder="تعليمات للطلاب..."
                              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">تاريخ الاستحقاق</label>
                    <input type="datetime-local" name="due_date" id="assignmentDueDate"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">الدرجة الكلية <span class="text-red-500">*</span></label>
                    <input type="number" name="max_score" id="assignmentMaxScore" value="100" min="1" max="1000" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                </div>
                <div class="flex items-end gap-4 md:col-span-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="allow_late_submission" id="assignmentAllowLate" value="1"
                               class="w-4 h-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500/20">
                        <span class="text-sm font-medium text-slate-700">السماح بالتسليم المتأخر</span>
                    </label>
                    <div class="w-40">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">الحالة</label>
                        <select name="status" id="assignmentStatus"
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                            <option value="draft">مسودة</option>
                            <option value="published">منشور</option>
                            <option value="archived">مؤرشف</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-200 flex gap-3">
                <button type="submit" id="assignmentSubmitBtn" class="flex-1 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-plus ml-1"></i> إنشاء وإضافة للمنهج
                </button>
                <button type="button" onclick="closeAssignmentModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal إضافة عنصر -->
<div id="itemModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-xl border border-slate-200">
        <h3 class="text-xl font-bold text-slate-800 mb-2">إضافة عنصر للمنهج</h3>
        <p class="text-sm text-slate-600 mb-4" id="itemName"></p>
        <form id="itemForm" onsubmit="addItem(event)">
            <input type="hidden" id="itemType">
            <input type="hidden" id="itemId">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-slate-700 mb-2">اختر القسم</label>
                <select id="targetSection" required
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 text-slate-800">
                    <option value="">اختر القسم</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}">{{ $section->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    إضافة
                </button>
                <button type="button" onclick="closeItemModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let currentSectionId = null;
let currentItemType = null;
let currentItemId = null;

function toggleSection(sectionId) {
    const block = document.querySelector('.section-block[data-section-id="' + sectionId + '"]');
    if (!block) return;
    block.classList.toggle('collapsed');
    const chevron = document.querySelector('.section-chevron[data-section-id="' + sectionId + '"]');
    if (chevron) chevron.style.transform = block.classList.contains('collapsed') ? 'rotate(-90deg)' : '';
}

function showAddExamModal(sectionId) {
    document.getElementById('examSectionId').value = sectionId;
    document.getElementById('examForm').reset();
    document.getElementById('examSectionId').value = sectionId;
    document.getElementById('examTotalMarks').value = 100;
    document.getElementById('examPassingMarks').value = 60;
    document.getElementById('examDuration').value = 60;
    document.getElementById('examAttempts').value = 1;
    document.getElementById('examModal').classList.remove('hidden');
    document.getElementById('examModal').classList.add('flex');
}

function closeExamModal() {
    document.getElementById('examModal').classList.add('hidden');
    document.getElementById('examModal').classList.remove('flex');
}

function showAddAssignmentModal(sectionId) {
    document.getElementById('assignmentSectionId').value = sectionId;
    document.getElementById('assignmentFormCurriculum').reset();
    document.getElementById('assignmentSectionId').value = sectionId;
    document.getElementById('assignmentMaxScore').value = 100;
    document.getElementById('assignmentModal').classList.remove('hidden');
    document.getElementById('assignmentModal').classList.add('flex');
}

function closeAssignmentModal() {
    document.getElementById('assignmentModal').classList.add('hidden');
    document.getElementById('assignmentModal').classList.remove('flex');
}

function saveAssignment(e) {
    e.preventDefault();
    var form = document.getElementById('assignmentFormCurriculum');
    var btn = document.getElementById('assignmentSubmitBtn');
    var formData = new FormData(form);
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin ml-1"></i> جاري الحفظ...';
    fetch('{{ route("instructor.courses.curriculum.assignments.store", $course) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            closeAssignmentModal();
            location.reload();
        } else {
            alert(data.message || 'حدث خطأ');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plus ml-1"></i> إنشاء وإضافة للمنهج';
        }
    })
    .catch(function() {
        alert('حدث خطأ في الاتصال');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plus ml-1"></i> إنشاء وإضافة للمنهج';
    });
}

function saveExam(e) {
    e.preventDefault();
    const form = document.getElementById('examForm');
    const btn = document.getElementById('examSubmitBtn');
    const sectionId = document.getElementById('examSectionId').value;
    const totalMarks = parseFloat(document.getElementById('examTotalMarks').value) || 100;
    const passingMarks = parseFloat(document.getElementById('examPassingMarks').value) || 60;
    if (passingMarks > totalMarks) {
        alert('درجة النجاح يجب ألا تتجاوز الدرجة الكلية');
        return;
    }
    const formData = new FormData(form);
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin ml-1"></i> جاري الحفظ...';
    fetch('{{ route("instructor.courses.curriculum.exams.store", $course) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeExamModal();
            if (data.redirect) {
                if (confirm('تم إنشاء الامتحان بنجاح. هل تريد الانتقال الآن لإضافة الأسئلة؟')) {
                    window.location.href = data.redirect;
                } else {
                    location.reload();
                }
            } else {
                location.reload();
            }
        } else {
            alert(data.message || 'حدث خطأ');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plus ml-1"></i> إنشاء وإضافة للمنهج';
        }
    })
    .catch(err => {
        console.error(err);
        alert('حدث خطأ في الاتصال');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plus ml-1"></i> إنشاء وإضافة للمنهج';
    });
}

function showAddSectionModal() {
    document.getElementById('sectionId').value = '';
    document.getElementById('sectionTitle').value = '';
    document.getElementById('sectionDescription').value = '';
    document.getElementById('modalTitle').textContent = 'إضافة قسم جديد';
    document.getElementById('sectionModal').classList.remove('hidden');
    document.getElementById('sectionModal').classList.add('flex');
}

function editSection(id, title, description) {
    document.getElementById('sectionId').value = id;
    document.getElementById('sectionTitle').value = title;
    document.getElementById('sectionDescription').value = description;
    document.getElementById('modalTitle').textContent = 'تعديل القسم';
    document.getElementById('sectionModal').classList.remove('hidden');
    document.getElementById('sectionModal').classList.add('flex');
}

function closeSectionModal() {
    document.getElementById('sectionModal').classList.add('hidden');
    document.getElementById('sectionModal').classList.remove('flex');
}

function saveSection(e) {
    e.preventDefault();
    const id = document.getElementById('sectionId').value;
    const title = document.getElementById('sectionTitle').value;
    const description = document.getElementById('sectionDescription').value;
    
    const url = id 
        ? `/instructor/sections/${id}`
        : `/instructor/courses/{{ $course->id }}/sections`;
    const method = id ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ title, description })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('حدث خطأ: ' + (data.message || 'خطأ غير معروف'));
        }
    })
    .catch(err => {
        console.error(err);
        alert('حدث خطأ أثناء الحفظ');
    });
}

function deleteSection(id) {
    if (!confirm('هل أنت متأكد من حذف هذا القسم؟ سيتم حذف جميع العناصر بداخله.')) return;
    
    fetch(`/instructor/sections/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('حدث خطأ: ' + (data.message || 'خطأ غير معروف'));
        }
    });
}

function showAddItemModal(type, id, name, typeName) {
    currentItemType = type;
    currentItemId = id;
    document.getElementById('itemType').value = type;
    document.getElementById('itemId').value = id;
    document.getElementById('itemName').textContent = `إضافة ${typeName}: ${name}`;
    document.getElementById('targetSection').value = '';
    document.getElementById('itemModal').classList.remove('hidden');
    document.getElementById('itemModal').classList.add('flex');
}

function closeItemModal() {
    document.getElementById('itemModal').classList.add('hidden');
    document.getElementById('itemModal').classList.remove('flex');
}

function addItem(e) {
    e.preventDefault();
    const sectionId = document.getElementById('targetSection').value;
    
    fetch(`/instructor/sections/${sectionId}/items`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            item_type: currentItemType,
            item_id: currentItemId
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('حدث خطأ: ' + (data.message || 'خطأ غير معروف'));
        }
    });
}

function removeItem(id) {
    if (!confirm('هل أنت متأكد من حذف هذا العنصر من المنهج؟')) return;
    
    fetch(`/instructor/curriculum-items/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('حدث خطأ: ' + (data.message || 'خطأ غير معروف'));
        }
    });
}

function showAddLectureModal(sectionId) {
    currentSectionId = sectionId;
    document.getElementById('lectureSectionId').value = sectionId;
    document.getElementById('lectureEditId').value = '';
    // مسح الحقول
    document.getElementById('lectureForm').reset();
    document.getElementById('lectureForm').querySelector('input[name="course_id"]').value = {{ $course->id }};
    const hasAttendance = document.getElementById('lectureForm').querySelector('input[name="has_attendance_tracking"]');
    if (hasAttendance) hasAttendance.checked = true;
    // تعيين التاريخ الحالي كقيمة افتراضية
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    document.getElementById('lectureScheduledAt').value = now.toISOString().slice(0, 16);
    document.getElementById('lectureDuration').value = 60;
    
    // تحديث العنوان والنص
    document.querySelector('#lectureModal h3').textContent = 'إضافة محاضرة جديدة';
    document.getElementById('lectureSubmitText').textContent = 'حفظ وإضافة للمنهج';
    
    // إعادة تعيين المشغل
    selectedVideoPlatform = '';
    document.getElementById('lectureVideoPlatform').value = '';
    document.getElementById('lectureVideoPreview').classList.add('hidden');
    document.querySelectorAll('.platform-btn').forEach(btn => {
        btn.classList.remove('border-sky-500', 'bg-sky-50');
        btn.classList.add('border-slate-200');
    });
    
    document.getElementById('lectureModal').classList.remove('hidden');
    document.getElementById('lectureModal').classList.add('flex');
}

// تعديل المحاضرة من باني الدورات
async function editLectureFromCurriculum(lectureId, sectionId) {
    try {
        const response = await fetch(`/instructor/lectures/${lectureId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            // إذا لم يكن JSON، جرب تحميل الصفحة
            window.location.href = `/instructor/lectures/${lectureId}/edit`;
            return;
        }
        
        const lecture = await response.json();
        
        console.log('=== Loaded lecture data ===');
        console.log('Full lecture object:', lecture);
        console.log('recording_url:', lecture.recording_url);
        console.log('video_platform:', lecture.video_platform);
        console.log('recording_url type:', typeof lecture.recording_url);
        console.log('recording_url length:', lecture.recording_url ? lecture.recording_url.length : 0);
        
        // ملء النموذج
        document.getElementById('lectureEditId').value = lectureId;
        document.getElementById('lectureSectionId').value = sectionId;
        document.getElementById('lectureTitle').value = lecture.title || '';
        document.getElementById('lectureDescription').value = lecture.description || '';
        // تحويل التاريخ
        if (lecture.scheduled_at) {
            const scheduledDate = new Date(lecture.scheduled_at);
            scheduledDate.setMinutes(scheduledDate.getMinutes() - scheduledDate.getTimezoneOffset());
            document.getElementById('lectureScheduledAt').value = scheduledDate.toISOString().slice(0, 16);
        }
        
        document.getElementById('lectureDuration').value = lecture.duration_minutes || 60;
        
        // تعيين الرابط - مهم جداً - نستخدم setTimeout لضمان أن الحقل موجود
        setTimeout(() => {
            const recordingUrlInput = document.getElementById('lectureRecordingUrl');
            if (recordingUrlInput) {
                const urlValue = lecture.recording_url || '';
                recordingUrlInput.value = urlValue;
                console.log('Set recording_url input to:', urlValue);
                console.log('Input value after setting:', recordingUrlInput.value);
                
                // إطلاق event لتفعيل oninput إذا كان موجوداً
                recordingUrlInput.dispatchEvent(new Event('input', { bubbles: true }));
            } else {
                console.error('lectureRecordingUrl input not found!');
            }
        }, 100);
        
        document.getElementById('lectureTeamsRegistration').value = lecture.teams_registration_link || '';
        document.getElementById('lectureTeamsMeeting').value = lecture.teams_meeting_link || '';
        document.getElementById('lectureNotes').value = lecture.notes || '';
        
        // تحديد المشغل - نستخدم setTimeout لضمان أن الحقول موجودة
        setTimeout(() => {
            let platformSet = false;
            
            // أولاً: محاولة استخدام video_platform المحفوظ
            if (lecture.video_platform) {
                console.log('Setting platform from lecture.video_platform:', lecture.video_platform);
                const platformBtn = document.querySelector(`[data-platform="${lecture.video_platform}"]`);
                if (platformBtn) {
                    // حفظ قيمة الرابط قبل selectVideoPlatform (لأنه قد يمسحها)
                    const savedUrl = lecture.recording_url || '';
                    selectVideoPlatform(lecture.video_platform, platformBtn);
                    // استعادة الرابط بعد selectVideoPlatform
                    setTimeout(() => {
                        const recordingUrlInput = document.getElementById('lectureRecordingUrl');
                        if (recordingUrlInput && savedUrl) {
                            recordingUrlInput.value = savedUrl;
                            console.log('Restored recording_url after platform selection:', savedUrl);
                        }
                    }, 50);
                    platformSet = true;
                } else {
                    console.warn('Platform button not found for:', lecture.video_platform);
                }
            }
            
            // ثانياً: إذا لم يتم تعيين المشغل، حاول اكتشافه من الرابط
            if (!platformSet && lecture.recording_url) {
                console.log('Auto-detecting platform from URL:', lecture.recording_url);
                const url = lecture.recording_url;
                let detectedPlatform = null;
                let platformBtn = null;
                
                if (url.includes('youtube.com') || url.includes('youtu.be')) {
                    detectedPlatform = 'youtube';
                    platformBtn = document.querySelector('[data-platform="youtube"]');
                } else if (url.includes('vimeo.com')) {
                    detectedPlatform = 'vimeo';
                    platformBtn = document.querySelector('[data-platform="vimeo"]');
                } else if (url.includes('drive.google.com')) {
                    detectedPlatform = 'google_drive';
                    platformBtn = document.querySelector('[data-platform="google_drive"]');
                } else if (url.match(/\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i)) {
                    detectedPlatform = 'direct';
                    platformBtn = document.querySelector('[data-platform="direct"]');
                }
                
                if (platformBtn && detectedPlatform) {
                    console.log('Auto-detected:', detectedPlatform);
                    // حفظ قيمة الرابط قبل selectVideoPlatform
                    const savedUrl = lecture.recording_url || '';
                    selectVideoPlatform(detectedPlatform, platformBtn);
                    // استعادة الرابط بعد selectVideoPlatform
                    setTimeout(() => {
                        const recordingUrlInput = document.getElementById('lectureRecordingUrl');
                        if (recordingUrlInput && savedUrl) {
                            recordingUrlInput.value = savedUrl;
                            console.log('Restored recording_url after auto-detection:', savedUrl);
                        }
                    }, 50);
                    platformSet = true;
                }
            }
            
            // معاينة الفيديو إذا كان موجوداً - بعد تعيين المشغل والرابط
            if (lecture.recording_url) {
                console.log('Setting up video preview for URL:', lecture.recording_url);
                setTimeout(() => {
                    previewLectureVideo();
                }, 400);
            } else {
                console.warn('No recording_url found in lecture data');
            }
        }, 200);
        
        // تحديث الخيارات
        const hasAttendance = document.querySelector('input[name="has_attendance_tracking"]');
        const hasAssignment = document.querySelector('input[name="has_assignment"]');
        const hasEvaluation = document.querySelector('input[name="has_evaluation"]');
        
        if (hasAttendance) hasAttendance.checked = lecture.has_attendance_tracking || false;
        if (hasAssignment) hasAssignment.checked = lecture.has_assignment || false;
        if (hasEvaluation) hasEvaluation.checked = lecture.has_evaluation || false;
        
        // تحديث العنوان والنص
        document.querySelector('#lectureModal h3').textContent = 'تعديل المحاضرة';
        document.getElementById('lectureSubmitText').textContent = 'حفظ التعديلات';
        
        // فتح Modal
        document.getElementById('lectureModal').classList.remove('hidden');
        document.getElementById('lectureModal').classList.add('flex');
        
    } catch (error) {
        console.error('Error loading lecture:', error);
        // في حالة الخطأ، افتح صفحة التعديل
        window.location.href = `/instructor/lectures/${lectureId}/edit`;
    }
}

// حذف المحاضرة من باني الدورات
async function deleteLectureFromCurriculum(lectureId, curriculumItemId) {
    if (!confirm('هل أنت متأكد من حذف هذه المحاضرة؟ سيتم حذفها من المنهج أيضاً.')) {
        return;
    }
    
    try {
        const response = await fetch(`/instructor/lectures/${lectureId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error('فشل حذف المحاضرة');
        }
        
        const data = await response.json();
        
        if (data.success || response.ok) {
            // حذف العنصر من المنهج أيضاً
            if (curriculumItemId) {
                await removeItem(curriculumItemId);
            } else {
                location.reload();
            }
        } else {
            throw new Error(data.message || 'فشل حذف المحاضرة');
        }
    } catch (error) {
        console.error('Error deleting lecture:', error);
        alert('حدث خطأ أثناء حذف المحاضرة: ' + error.message);
    }
}

function closeLectureModal() {
    document.getElementById('lectureModal').classList.add('hidden');
    document.getElementById('lectureModal').classList.remove('flex');
    document.getElementById('lectureForm').reset();
    document.getElementById('lectureEditId').value = '';
    document.getElementById('lectureSectionId').value = '';
    currentSectionId = null;
    
    // إعادة تعيين المشغل
    selectedVideoPlatform = '';
    document.getElementById('lectureVideoPlatform').value = '';
    document.getElementById('lectureVideoPreview').classList.add('hidden');
    document.querySelectorAll('.platform-btn').forEach(btn => {
        btn.classList.remove('border-sky-500', 'bg-sky-50');
        btn.classList.add('border-slate-200');
    });
}

function saveLecture(e) {
    e.preventDefault();
    const form = document.getElementById('lectureForm');
    const formData = new FormData(form);
    const sectionId = document.getElementById('lectureSectionId').value;
    const lectureId = document.getElementById('lectureEditId').value;
    
    // التأكد من أن video_platform و recording_url موجودان في البيانات
    const videoPlatformInput = document.getElementById('lectureVideoPlatform');
    const recordingUrlInput = document.getElementById('lectureRecordingUrl');
    
    const videoPlatform = selectedVideoPlatform || (videoPlatformInput ? videoPlatformInput.value : '');
    const recordingUrl = recordingUrlInput ? recordingUrlInput.value.trim() : '';
    
    console.log('Form data before save:');
    console.log('- video_platform:', videoPlatform);
    console.log('- recording_url:', recordingUrl);
    console.log('- selectedVideoPlatform:', selectedVideoPlatform);
    console.log('- videoPlatformInput value:', videoPlatformInput ? videoPlatformInput.value : 'N/A');
    console.log('- recordingUrlInput value:', recordingUrlInput ? recordingUrlInput.value : 'N/A');
    
    // التأكد من إضافة recording_url إلى formData
    if (recordingUrl) {
        formData.set('recording_url', recordingUrl);
        console.log('Set recording_url in formData:', recordingUrl);
    } else {
        // إذا كان فارغاً، أرسل string فارغ
        formData.set('recording_url', '');
        console.log('Set recording_url to empty string');
    }
    
    // التأكد من إضافة video_platform إلى formData
    if (videoPlatform) {
        formData.set('video_platform', videoPlatform);
        console.log('Set video_platform in formData:', videoPlatform);
    } else if (recordingUrl) {
        // إذا لم يكن platform محدداً لكن يوجد رابط، حاول اكتشافه
        console.log('No platform selected, trying to auto-detect from URL');
        let detectedPlatform = '';
        if (recordingUrl.includes('youtube.com') || recordingUrl.includes('youtu.be')) {
            detectedPlatform = 'youtube';
        } else if (recordingUrl.includes('vimeo.com')) {
            detectedPlatform = 'vimeo';
        } else if (recordingUrl.includes('drive.google.com')) {
            detectedPlatform = 'google_drive';
        } else if (recordingUrl.match(/\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i)) {
            detectedPlatform = 'direct';
        }
        
        if (detectedPlatform) {
            formData.set('video_platform', detectedPlatform);
            console.log('Auto-detected and set video_platform:', detectedPlatform);
        }
    } else {
        // إذا لم يكن هناك رابط ولا platform، أرسل string فارغ
        formData.set('video_platform', '');
        console.log('Set video_platform to empty string');
    }
    
    // طباعة جميع البيانات في formData للتحقق
    console.log('All formData entries:');
    for (let pair of formData.entries()) {
        console.log('- ' + pair[0] + ': ' + pair[1]);
    }
    
    // إضافة section_id للبيانات
    formData.append('section_id', sectionId);
    
    // تحديد URL والـ method
    let url = '{{ route("instructor.lectures.store") }}';
    let method = 'POST';
    
    if (lectureId) {
        url = `/instructor/lectures/${lectureId}`;
        method = 'PUT';
        formData.append('_method', 'PUT');
        console.log('Updating lecture:', lectureId);
    } else {
        console.log('Creating new lecture');
    }
    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(async res => {
        const contentType = res.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return res.json();
        } else {
            const text = await res.text();
            throw new Error('Expected JSON but got HTML');
        }
    })
    .then(data => {
        console.log('Save response:', data);
        if (data.success || (lectureId && !data.error)) {
            if (lectureId) {
                // تم التعديل - إعادة تحميل الصفحة
                console.log('Lecture updated successfully, reloading page...');
                location.reload();
            } else {
                // إذا تم إنشاء المحاضرة بنجاح، أضفها تلقائياً للقسم
                if (data.lecture && data.lecture.id && sectionId) {
                    console.log('Lecture created successfully, adding to section...');
                    addLectureToSection(data.lecture.id, sectionId);
                } else {
                    console.log('Lecture created but no section ID, reloading page...');
                    location.reload();
                }
            }
        } else {
            console.error('Save failed:', data);
            alert('حدث خطأ: ' + (data.message || 'خطأ غير معروف'));
        }
    })
    .catch(err => {
        console.error('Error saving lecture:', err);
        console.error('Error details:', err.message, err.stack);
        
        // إذا كان هناك أخطاء في التحقق من Laravel
        if (err.message && err.message.includes('422')) {
            fetch('{{ route("instructor.lectures.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.errors) {
                    let errorMsg = 'أخطاء في النموذج:\n';
                    Object.values(data.errors).forEach(errors => {
                        errors.forEach(error => errorMsg += error + '\n');
                    });
                    alert(errorMsg);
                }
            });
        } else {
            alert('حدث خطأ أثناء حفظ المحاضرة');
        }
    });
}

function addLectureToSection(lectureId, sectionId) {
    fetch(`/instructor/sections/${sectionId}/items`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            item_type: 'App\\Models\\Lecture',
            item_id: lectureId
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('تم إنشاء المحاضرة لكن حدث خطأ في إضافتها للمنهج: ' + (data.message || ''));
            location.reload();
        }
    })
    .catch(err => {
        console.error(err);
        alert('تم إنشاء المحاضرة لكن حدث خطأ في إضافتها للمنهج');
        location.reload();
    });
}

// اختيار المشغل للفيديو
let selectedVideoPlatform = '';
function selectVideoPlatform(platform, button) {
    selectedVideoPlatform = platform;
    const platformInput = document.getElementById('lectureVideoPlatform');
    if (platformInput) {
        platformInput.value = platform;
        console.log('Platform selected:', platform, 'Input value set to:', platformInput.value);
    } else {
        console.error('lectureVideoPlatform input not found!');
    }
    
    // تحديث الأزرار
    document.querySelectorAll('.platform-btn').forEach(btn => {
        btn.classList.remove('border-sky-500', 'bg-sky-50');
        btn.classList.add('border-slate-200');
    });
    if (button) {
        button.classList.remove('border-slate-200');
        button.classList.add('border-sky-500', 'bg-sky-50');
    }
    
    // تحديث placeholder
    const placeholder = document.getElementById('lectureVideoPlaceholder');
    const input = document.getElementById('lectureRecordingUrl');
    
    if (input && placeholder) {
        // حفظ القيمة الحالية قبل التحديث
        const currentValue = input.value;
        
        switch(platform) {
            case 'youtube':
                placeholder.textContent = 'مثال: https://www.youtube.com/watch?v=VIDEO_ID أو https://youtu.be/VIDEO_ID';
                input.placeholder = 'الصق رابط YouTube هنا...';
                break;
            case 'vimeo':
                placeholder.textContent = 'مثال: https://vimeo.com/VIDEO_ID';
                input.placeholder = 'الصق رابط Vimeo هنا...';
                break;
            case 'google_drive':
                placeholder.textContent = 'مثال: https://drive.google.com/file/d/FILE_ID/view';
                input.placeholder = 'الصق رابط Google Drive هنا...';
                break;
            case 'direct':
                placeholder.textContent = 'مثال: https://example.com/video.mp4';
                input.placeholder = 'الصق رابط الفيديو المباشر هنا...';
                break;
            default:
                placeholder.textContent = '';
                input.placeholder = 'ضع رابط الفيديو هنا...';
        }
        
        // استعادة القيمة إذا كانت موجودة (للتعديل)
        if (currentValue) {
            input.value = currentValue;
            console.log('Preserved recording_url value:', currentValue);
        } else {
            // مسح المعاينة السابقة فقط إذا لم تكن هناك قيمة
            document.getElementById('lectureVideoPreview').classList.add('hidden');
        }
    }
}

// معاينة الفيديو
function previewLectureVideo() {
    const url = document.getElementById('lectureRecordingUrl').value.trim();
    let platform = selectedVideoPlatform || document.getElementById('lectureVideoPlatform').value;
    
    // إذا لم يكن platform محدداً، حاول اكتشافه تلقائياً
    if (!platform && url) {
        if (url.includes('youtube.com') || url.includes('youtu.be')) {
            platform = 'youtube';
            selectedVideoPlatform = 'youtube';
            document.getElementById('lectureVideoPlatform').value = 'youtube';
            // تحديث زر YouTube
            const youtubeBtn = document.querySelector('[data-platform="youtube"]');
            if (youtubeBtn) {
                selectVideoPlatform('youtube', youtubeBtn);
            }
        } else if (url.includes('vimeo.com')) {
            platform = 'vimeo';
            selectedVideoPlatform = 'vimeo';
            document.getElementById('lectureVideoPlatform').value = 'vimeo';
            const vimeoBtn = document.querySelector('[data-platform="vimeo"]');
            if (vimeoBtn) {
                selectVideoPlatform('vimeo', vimeoBtn);
            }
        } else if (url.includes('drive.google.com')) {
            platform = 'google_drive';
            selectedVideoPlatform = 'google_drive';
            document.getElementById('lectureVideoPlatform').value = 'google_drive';
            const driveBtn = document.querySelector('[data-platform="google_drive"]');
            if (driveBtn) {
                selectVideoPlatform('google_drive', driveBtn);
            }
        } else if (url.match(/\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i)) {
            platform = 'direct';
            selectedVideoPlatform = 'direct';
            document.getElementById('lectureVideoPlatform').value = 'direct';
            const directBtn = document.querySelector('[data-platform="direct"]');
            if (directBtn) {
                selectVideoPlatform('direct', directBtn);
            }
        }
    }
    
    const previewDiv = document.getElementById('lectureVideoPreview');
    const previewContent = document.getElementById('lectureVideoPreviewContent');
    
    if (!url || !platform) {
        previewDiv.classList.add('hidden');
        return;
    }
    
    previewDiv.classList.remove('hidden');
    previewContent.innerHTML = '<i class="fas fa-spinner fa-spin text-2xl"></i>';
    
    let html = '';
    let isValid = false;
    
    try {
        // YouTube
        if (platform === 'youtube') {
            let videoId = null;
            
            // نمط 1: youtube.com/watch?v=VIDEO_ID أو youtube.com/watch?v=VIDEO_ID&si=...
            const watchMatch = url.match(/[?&]v=([a-zA-Z0-9_-]{11})/);
            if (watchMatch && watchMatch[1]) {
                videoId = watchMatch[1];
            }
            
            // نمط 2: youtu.be/VIDEO_ID أو youtu.be/VIDEO_ID?si=...
            if (!videoId) {
                const shortMatch = url.match(/youtu\.be\/([a-zA-Z0-9_-]{11})/);
                if (shortMatch && shortMatch[1]) {
                    videoId = shortMatch[1];
                }
            }
            
            // نمط 3: youtube.com/embed/VIDEO_ID
            if (!videoId) {
                const embedMatch = url.match(/embed\/([a-zA-Z0-9_-]{11})/);
                if (embedMatch && embedMatch[1]) {
                    videoId = embedMatch[1];
                }
            }
            
            // نمط 4: youtube.com/v/VIDEO_ID
            if (!videoId) {
                const vMatch = url.match(/\/v\/([a-zA-Z0-9_-]{11})/);
                if (vMatch && vMatch[1]) {
                    videoId = vMatch[1];
                }
            }
            
            // نمط 5: أي رابط يحتوي على 11 حرف/رقم متتالي (video ID) في رابط YouTube
            if (!videoId) {
                const genericMatch = url.match(/([a-zA-Z0-9_-]{11})/);
                if (genericMatch && genericMatch[1] && (url.includes('youtube') || url.includes('youtu.be'))) {
                    videoId = genericMatch[1];
                }
            }
            
            if (videoId && videoId.length === 11) {
                isValid = true;
                const origin = encodeURIComponent(window.location.origin);
                html = '<iframe src="https://www.youtube.com/embed/' + videoId + '?rel=0&modestbranding=1&showinfo=0&controls=1&enablejsapi=1&origin=' + origin + '&autoplay=0" width="100%" height="100%" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="border-radius: 0.75rem;"></iframe>';
            } else {
                console.error('Could not extract YouTube video ID from:', url);
            }
        }
        // Vimeo
        else if (platform === 'vimeo') {
            const pattern = /vimeo\.com\/(?:.*\/)?(\d+)/;
            const match = url.match(pattern);
            if (match && match[1]) {
                isValid = true;
                html = '<iframe src="https://player.vimeo.com/video/' + match[1] + '?title=0&byline=0&portrait=0&controls=1" width="100%" height="100%" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen style="border-radius: 0.75rem;"></iframe>';
            }
        }
        // Google Drive
        else if (platform === 'google_drive') {
            const pattern = /drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/;
            const match = url.match(pattern);
            if (match && match[1]) {
                isValid = true;
                html = '<iframe src="https://drive.google.com/file/d/' + match[1] + '/preview" width="100%" height="100%" frameborder="0" allow="autoplay" style="border-radius: 0.75rem;"></iframe>';
            }
        }
        // Direct Video
        else if (platform === 'direct') {
            const pattern = /\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i;
            if (pattern.test(url)) {
                isValid = true;
                const escapedUrl = url.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                html = '<video controls width="100%" height="100%" style="max-height: 100%; border-radius: 0.75rem;" class="w-full h-full"><source src="' + escapedUrl + '" type="video/mp4">متصفحك لا يدعم تشغيل الفيديو.</video>';
            }
        }
        
        if (isValid && html) {
            previewContent.innerHTML = html;
        } else {
            previewContent.innerHTML = '<div class="text-center p-4"><i class="fas fa-exclamation-triangle text-yellow-400 text-2xl mb-2"></i><p class="text-sm">الرابط غير صحيح أو غير مدعوم</p></div>';
        }
    } catch (error) {
        console.error('Error generating preview:', error);
        previewContent.innerHTML = '<div class="text-center p-4"><i class="fas fa-exclamation-circle text-red-400 text-2xl mb-2"></i><p class="text-sm">حدث خطأ في عرض المعاينة</p></div>';
    }
}

// إعادة تعيين عند إغلاق modal
function closeLectureModal() {
    document.getElementById('lectureModal').classList.add('hidden');
    document.getElementById('lectureForm').reset();
    selectedVideoPlatform = '';
    document.getElementById('lectureVideoPlatform').value = '';
    document.getElementById('lectureVideoPreview').classList.add('hidden');
    document.querySelectorAll('.platform-btn').forEach(btn => {
        btn.classList.remove('border-sky-500', 'bg-sky-50');
        btn.classList.add('border-slate-200');
    });
}
</script>
@endpush
@endsection
