@extends('layouts.app')

@section('title', __('instructor.assignments'))
@section('header', __('instructor.assignments'))

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ __('instructor.assignments') }}</h1>
                <p class="text-sm text-slate-500 mt-0.5">{{ __('instructor.manage_assignments_submissions') }}</p>
            </div>
            <button onclick="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                <i class="fas fa-plus"></i>
                <span>{{ __('instructor.create_assignment') }}</span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">{{ __('instructor.total') }}</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center text-sky-600"><i class="fas fa-tasks"></i></div>
            </div>
        </div>
        <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">{{ __('instructor.published') }}</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['published'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">{{ __('instructor.draft') }}</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['draft'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600"><i class="fas fa-file-alt"></i></div>
            </div>
        </div>
        <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">{{ __('instructor.submissions') }}</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['total_submissions'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center text-violet-600"><i class="fas fa-file-upload"></i></div>
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white border border-slate-200 shadow-sm p-5">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="course_id" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('instructor.courses') }}</label>
                <select name="course_id" id="course_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                    <option value="">{{ __('instructor.all_courses') }}</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('common.status') }}</label>
                <select name="status" id="status" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                    <option value="">{{ __('instructor.all') }}</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>{{ __('instructor.published') }}</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ __('instructor.draft') }}</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>{{ __('instructor.archived') }}</option>
                </select>
            </div>
            <div>
                <label for="search" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('common.search') }}</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="{{ __('instructor.search_placeholder') }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-search ml-1"></i> {{ __('common.search') }}
                </button>
                @if(request()->anyFilled(['course_id', 'status', 'search']))
                    <a href="{{ route('instructor.assignments.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if($assignments->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($assignments as $assignment)
                <div class="rounded-xl bg-white border border-slate-200 shadow-sm hover:border-sky-300 hover:shadow-md transition-all p-5 h-full flex flex-col">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <h3 class="text-lg font-bold text-slate-800 line-clamp-2">{{ $assignment->title }}</h3>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold shrink-0
                            @if($assignment->status == 'published') bg-emerald-100 text-emerald-700
                            @elseif($assignment->status == 'draft') bg-amber-100 text-amber-700
                            @else bg-slate-100 text-slate-600
                            @endif">
                            @if($assignment->status == 'published') {{ __('instructor.published') }}
                            @elseif($assignment->status == 'draft') {{ __('instructor.draft') }}
                            @else {{ __('instructor.archived') }}
                            @endif
                        </span>
                    </div>

                    @if($assignment->description)
                        <p class="text-sm text-slate-600 mb-3 line-clamp-3">{{ $assignment->description }}</p>
                    @endif

                    <div class="space-y-1.5 text-sm text-slate-500 mb-4">
                        <div><i class="fas fa-book text-sky-500 ml-1"></i> <span class="font-semibold text-slate-700">الكورس:</span> {{ $assignment->course->title ?? '—' }}</div>
                        @if(!empty($assignment->course?->code))
                            <div><i class="fas fa-hashtag text-slate-400 ml-1"></i> <span class="font-semibold text-slate-700">كود الكورس:</span> {{ $assignment->course->code }}</div>
                        @endif
                        @if(!empty($assignment->course?->academicSubject?->name))
                            <div><i class="fas fa-layer-group text-indigo-500 ml-1"></i> <span class="font-semibold text-slate-700">المادة:</span> {{ $assignment->course->academicSubject->name }}</div>
                        @endif
                        @if(!empty($assignment->lesson?->title))
                            <div><i class="fas fa-list-alt text-emerald-500 ml-1"></i> <span class="font-semibold text-slate-700">الدرس:</span> {{ $assignment->lesson->title }}</div>
                        @endif
                        @if($assignment->due_date)
                            <div><i class="fas fa-calendar text-slate-400 ml-1"></i> {{ $assignment->due_date->format('Y/m/d') }}</div>
                        @endif
                        <div><i class="fas fa-file-upload text-violet-500 ml-1"></i> {{ $assignment->submissions_count }} {{ __('instructor.submission_single') }}</div>
                        <div><i class="fas fa-star text-amber-500 ml-1"></i> {{ $assignment->max_score }} {{ __('instructor.score_marks') }}</div>
                    </div>

                    <div class="mt-auto grid grid-cols-2 gap-2">
                        <a href="{{ route('instructor.assignments.show', $assignment) }}" class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold text-sm transition-colors">
                            <i class="fas fa-eye"></i> {{ __('common.view') }}
                        </a>
                        <a href="{{ route('instructor.assignments.submissions', $assignment) }}" class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-violet-500 hover:bg-violet-600 text-white rounded-xl font-semibold text-sm transition-colors">
                            <i class="fas fa-list"></i> {{ __('instructor.submissions') }}
                        </a>
                    </div>
                    <form method="POST" action="{{ route('instructor.assignments.destroy', $assignment) }}" class="mt-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('هل أنت متأكد من حذف الواجب؟')" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-xl font-semibold text-sm transition-colors">
                            <i class="fas fa-trash"></i> حذف الواجب
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
        <div class="flex justify-center">
            <div class="rounded-xl p-3 bg-white border border-slate-200 shadow-sm">{{ $assignments->links() }}</div>
        </div>
    @else
        <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/50 py-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-sky-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-tasks text-2xl text-sky-500"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">{{ __('instructor.no_assignments') }}</h3>
            <p class="text-sm text-slate-500 mb-4">{{ __('instructor.no_assignments_description') }}</p>
            <button onclick="openCreateModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                <i class="fas fa-plus"></i> {{ __('instructor.create_assignment') }}
            </button>
        </div>
    @endif
</div>

<!-- Modal إنشاء واجب -->
<div id="createAssignmentModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="flex items-center justify-center min-h-screen px-4 py-6">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeCreateModal()" id="modalOverlay"></div>
        <div class="relative bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col" id="modalPanel" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center text-sky-600">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div>
                        <h3 id="modal-title" class="text-lg font-bold text-slate-800">{{ __('instructor.create_assignment_modal_title') }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">{{ __('instructor.create_assignment_modal_subtitle') }}</p>
                    </div>
                </div>
                <button onclick="closeCreateModal()" class="p-2 rounded-lg text-slate-500 hover:bg-slate-200 hover:text-slate-700 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="overflow-y-auto flex-1 p-6">
                @include('instructor.assignments.create-form', ['courses' => $courses, 'isModal' => true])
            </div>
        </div>
    </div>
</div>

<script>
function openCreateModal() {
    var modal = document.getElementById('createAssignmentModal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(function() {
            if (typeof updateLessonsOnCourseChange === 'function') updateLessonsOnCourseChange();
        }, 100);
    }
}
function closeCreateModal() {
    var modal = document.getElementById('createAssignmentModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        var form = document.getElementById('assignmentForm');
        if (form) {
            form.reset();
            var lessonSelect = document.getElementById('lesson_id');
            if (lessonSelect && lessonSelect.children.length > 1) {
                while (lessonSelect.children.length > 1) lessonSelect.removeChild(lessonSelect.lastChild);
            }
        }
    }
}
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeCreateModal(); });
</script>
@endsection
