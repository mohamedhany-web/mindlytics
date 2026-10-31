@php
    $courses = $courses ?? [];
@endphp

<form action="{{ route('instructor.assignments.store') }}" method="POST" id="assignmentForm" class="space-y-5">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="md:col-span-2">
            <label for="advanced_course_id" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('instructor.course_label') }} <span class="text-red-500">*</span></label>
            <select name="advanced_course_id" id="advanced_course_id" required
                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
                <option value="">{{ __('instructor.choose_course_option') }}</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ old('advanced_course_id', request('advanced_course_id')) == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                @endforeach
            </select>
            @error('advanced_course_id')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label for="lesson_id" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('instructor.lesson_optional') }}</label>
            <select name="lesson_id" id="lesson_id"
                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
                <option value="">{{ __('instructor.no_lesson_option') }}</option>
            </select>
            <p class="mt-1 text-xs text-slate-500">{{ __('instructor.lesson_filled_by_course') }}</p>
            @error('lesson_id')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        @php $courseGroups = $courseGroups ?? collect(); @endphp
        <div class="md:col-span-2">
            <label for="group_id" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('instructor.for_group_optional') }}</label>
            <select name="group_id" id="group_id"
                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
                <option value="">{{ __('instructor.for_all_students_option') }}</option>
                @foreach($courseGroups as $cid => $groups)
                    @foreach($groups as $g)
                        <option value="{{ $g->id }}" data-course-id="{{ $cid }}" class="group-option" {{ old('group_id', request('group_id')) == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                    @endforeach
                @endforeach
            </select>
            <p class="mt-1 text-xs text-slate-500">{{ __('instructor.group_assignment_hint') }}</p>
            @error('group_id')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label for="title" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('instructor.assignment_title_required') }} <span class="text-red-500">*</span></label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white"
                   placeholder="{{ __('instructor.assignment_title_required') }}">
            @error('title')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label for="description" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('instructor.description') }}</label>
            <textarea name="description" id="description" rows="3"
                      class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white resize-none"
                      placeholder="{{ __('instructor.description') }}...">{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label for="instructions" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('instructor.instructions_label') }}</label>
            <textarea name="instructions" id="instructions" rows="4"
                      class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white resize-none"
                      placeholder="{{ __('instructor.instructions_label') }}...">{{ old('instructions') }}</textarea>
            @error('instructions')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="due_date" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('instructor.due_date') }}</label>
            <input type="datetime-local" name="due_date" id="due_date" value="{{ old('due_date') }}"
                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
            @error('due_date')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="max_score" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('instructor.total_score_label') }} <span class="text-red-500">*</span></label>
            <input type="number" name="max_score" id="max_score" value="{{ old('max_score', 100) }}" min="1" max="1000" required
                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
            @error('max_score')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label class="inline-flex items-center gap-3 cursor-pointer p-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors w-full">
                <input type="checkbox" name="allow_late_submission" id="allow_late_submission" value="1" {{ old('allow_late_submission') ? 'checked' : '' }}
                       class="w-5 h-5 rounded border-slate-300 text-sky-500 focus:ring-sky-500/20">
                <span class="text-sm font-medium text-slate-700">{{ __('instructor.allow_late_submission_label') }}</span>
            </label>
        </div>

        <div class="md:col-span-2">
            <label for="status" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('common.status') }} <span class="text-red-500">*</span></label>
            <select name="status" id="status" required
                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
                <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>{{ __('instructor.draft') }}</option>
                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>{{ __('instructor.published') }}</option>
                <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>{{ __('instructor.archived') }}</option>
            </select>
            @error('status')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3 pt-5 border-t border-slate-200">
        @if(isset($isModal) && $isModal)
            <button type="button" onclick="closeCreateModal()"
                    class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                <i class="fas fa-times ml-2"></i> {{ __('common.cancel') }}
            </button>
        @else
            <a href="{{ route('instructor.assignments.index') }}"
               class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors text-center">
                <i class="fas fa-times ml-2"></i> {{ __('common.cancel') }}
            </a>
        @endif
        <button type="submit"
                class="px-6 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
            <i class="fas fa-save ml-2"></i> {{ __('instructor.create_assignment') }}
        </button>
    </div>
</form>

<script>
if (typeof updateLessonsOnCourseChange === 'undefined') {
    window.updateLessonsOnCourseChange = function() {
        var courseSelect = document.getElementById('advanced_course_id');
        if (!courseSelect) return;
        courseSelect.addEventListener('change', function() {
            var courseId = this.value;
            var lessonSelect = document.getElementById('lesson_id');
            if (!lessonSelect) return;
            while (lessonSelect.children.length > 1) lessonSelect.removeChild(lessonSelect.lastChild);
            if (courseId) {
                var loadingOption = document.createElement('option');
                loadingOption.value = '';
                loadingOption.textContent = @json(__('instructor.loading_text'));
                loadingOption.disabled = true;
                lessonSelect.appendChild(loadingOption);
                lessonSelect.disabled = true;
                fetch('/instructor/api/courses/' + courseId + '/lessons-list', { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function(r) { return r.json(); })
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
            } else {
                lessonSelect.disabled = false;
            }
        });
    };
    document.addEventListener('DOMContentLoaded', function() { updateLessonsOnCourseChange(); });
}
</script>
