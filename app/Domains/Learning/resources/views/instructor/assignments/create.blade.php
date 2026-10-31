@extends('layouts.app')

@section('title', __('instructor.create_assignment') . ' - Mindlytics')
@section('header', __('instructor.create_assignment'))

@push('styles')
<style>
    .form-card { background: #fff; border: 1px solid rgb(226 232 240); border-radius: 1rem; transition: box-shadow 0.2s; }
    .form-card:focus-within { box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- الهيدر -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6 mb-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="{{ route('instructor.assignments.index') }}" class="hover:text-sky-600 transition-colors">{{ __('instructor.assignments') }}</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">{{ __('instructor.create_assignment') }}</span>
        </nav>
        <div class="flex flex-wrap items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                <i class="fas fa-tasks text-lg"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800">{{ __('instructor.create_assignment') }}</h1>
                <p class="text-sm text-slate-600 mt-0.5">{{ __('instructor.add_assignment_for_course') }}</p>
                @if(request('group_id') && isset($courseGroups))
                    @php
                        $selectedGroup = collect($courseGroups)->flatten(1)->firstWhere('id', (int) request('group_id'));
                    @endphp
                    @if($selectedGroup)
                        <span class="inline-flex items-center gap-1.5 mt-2 px-2.5 py-1 rounded-lg text-xs font-semibold bg-sky-100 text-sky-700">
                            <i class="fas fa-users"></i> {{ __('instructor.assignment_for_group') }}: {{ $selectedGroup->name }}
                        </span>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- بطاقة النموذج -->
    <div class="form-card shadow-sm p-6 sm:p-8">
        @include('learning::instructor.assignments.create-form', ['courses' => $courses, 'courseGroups' => $courseGroups ?? collect()])
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const courseSelect = document.getElementById('advanced_course_id');
    if (!courseSelect) return;

    const newCourseSelect = courseSelect.cloneNode(true);
    courseSelect.parentNode.replaceChild(newCourseSelect, courseSelect);

    function updateGroupOptions(courseId) {
        const groupSelect = document.getElementById('group_id');
        if (!groupSelect) return;
        const opts = groupSelect.querySelectorAll('.group-option');
        opts.forEach(function(o) {
            o.style.display = (o.getAttribute('data-course-id') === courseId) ? '' : 'none';
            o.disabled = (o.getAttribute('data-course-id') !== courseId);
        });
        if (!courseId) groupSelect.value = '';
        else {
            const sel = groupSelect.querySelector('.group-option[value="' + groupSelect.value + '"]');
            if (sel && sel.getAttribute('data-course-id') !== courseId) groupSelect.value = '';
        }
    }

    newCourseSelect.addEventListener('change', function() {
        const courseId = this.value;
        updateGroupOptions(courseId);
        const lessonSelect = document.getElementById('lesson_id');
        if (!lessonSelect) return;

        while (lessonSelect.children.length > 1) lessonSelect.removeChild(lessonSelect.lastChild);
        if (!courseId) return;

        const loadingOption = document.createElement('option');
        loadingOption.value = '';
        loadingOption.textContent = @json(__('instructor.loading_text'));
        loadingOption.disabled = true;
        lessonSelect.appendChild(loadingOption);
        lessonSelect.disabled = true;

        fetch('/instructor/api/courses/' + courseId + '/lessons-list', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
            .then(function(r) { return r.ok ? r.json() : Promise.reject(); })
            .then(function(data) {
                loadingOption.remove();
                var lessons = Array.isArray(data) ? data : (data.lessons || []);
                if (lessons.length > 0) {
                    lessons.forEach(function(lesson) {
                        var opt = document.createElement('option');
                        opt.value = lesson.id;
                        opt.textContent = lesson.title || 'درس ' + (lesson.order || '');
                        lessonSelect.appendChild(opt);
                    });
                } else {
                    var noOpt = document.createElement('option');
                    noOpt.value = '';
                    noOpt.textContent = @json(__('instructor.no_lessons_in_course'));
                    noOpt.disabled = true;
                    lessonSelect.appendChild(noOpt);
                }
                lessonSelect.disabled = false;
            })
            .catch(function() {
                loadingOption.remove();
                var errOpt = document.createElement('option');
                errOpt.value = '';
                errOpt.textContent = @json(__('instructor.error_occurred'));
                errOpt.disabled = true;
                lessonSelect.appendChild(errOpt);
                lessonSelect.disabled = false;
            });
    });

    updateGroupOptions(newCourseSelect.value);
});
</script>
@endsection
