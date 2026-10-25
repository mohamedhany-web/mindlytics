@extends('layouts.app')

@section('title', __('instructor.create_group_new') . ' - Mindlytics')
@section('header', __('instructor.create_group_new'))

@push('styles')
<style>
    .form-card { background: #fff; border: 1px solid rgb(226 232 240); border-radius: 1rem; transition: box-shadow 0.2s; }
    .form-card:focus-within { box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
    .form-input, .form-select, .form-textarea {
        border: 1px solid rgb(226 232 240); border-radius: 0.75rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none; border-color: rgb(14 165 233); box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
    }
    .input-wrapper { position: relative; }
    .input-wrapper i { position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: rgb(148 163 184); pointer-events: none; }
    .input-wrapper textarea + i, .input-wrapper i.textarea-icon { top: 1.25rem; transform: none; }
    .input-wrapper input, .input-wrapper select, .input-wrapper textarea { padding-right: 2.75rem; }
</style>
@endpush

@section('content')
<div class="w-full max-w-full px-4 sm:px-6 lg:px-8 py-6">
    <div class="w-full max-w-5xl mx-auto">
        <!-- الهيدر -->
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6 mb-6">
            <nav class="text-sm text-slate-500 mb-2">
                <a href="{{ route('instructor.groups.index') }}" class="hover:text-sky-600 transition-colors">{{ __('instructor.groups') }}</a>
                <span class="mx-2">/</span>
                <span class="text-slate-700 font-semibold">{{ __('instructor.create_group_new') }}</span>
            </nav>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">
                    <i class="fas fa-users text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800">{{ __('instructor.create_group_new') }}</h1>
                    <p class="text-sm text-slate-600 mt-0.5">{{ __('instructor.add_new_group_desc') }}</p>
                </div>
            </div>
        </div>

        <!-- بطاقة النموذج -->
        <div class="form-card shadow-sm p-6 sm:p-8">
            <form action="{{ route('instructor.groups.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- الكورس -->
                    <div class="md:col-span-2">
                        <label for="course_id" class="block text-sm font-semibold text-slate-700 mb-2">
                            {{ __('instructor.course_label') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-book-open"></i>
                            <select name="course_id" id="course_id" required
                                    class="form-select w-full px-4 py-3 rounded-xl bg-white text-slate-800">
                                <option value="">{{ __('instructor.choose_course_option') }}</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id', request('course_id')) == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('course_id')
                            <p class="mt-2 text-sm text-red-500 flex items-center gap-2">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- اسم المجموعة -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">
                            {{ __('instructor.group_name_required') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-tag"></i>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="form-input w-full px-4 py-3 rounded-xl bg-white text-slate-800"
                                   placeholder="{{ __('instructor.group_name_required') }}">
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm text-red-500 flex items-center gap-2">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- الوصف -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">{{ __('instructor.description') }}</label>
                        <div class="input-wrapper">
                            <i class="fas fa-align-right textarea-icon" style="top: 1.25rem; transform: none;"></i>
                            <textarea name="description" id="description" rows="4"
                                      class="form-textarea w-full px-4 py-3 rounded-xl bg-white text-slate-800 resize-none"
                                      placeholder="{{ __('instructor.group_description_placeholder') }}">{{ old('description') }}</textarea>
                        </div>
                        @error('description')
                            <p class="mt-2 text-sm text-red-500 flex items-center gap-2">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- قائد المجموعة -->
                    <div>
                        <label for="leader_id" class="block text-sm font-semibold text-slate-700 mb-2">{{ __('instructor.group_leader_label') }}</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user-tie"></i>
                            <select name="leader_id" id="leader_id"
                                    class="form-select w-full px-4 py-3 rounded-xl bg-white text-slate-800">
                                <option value="">{{ __('instructor.no_leader_option') }}</option>
                            </select>
                        </div>
                        <p class="mt-2 text-xs text-slate-500 flex items-center gap-1">
                            <i class="fas fa-info-circle"></i>
                            {{ __('instructor.choose_course_first') }}
                        </p>
                        @error('leader_id')
                            <p class="mt-2 text-sm text-red-500 flex items-center gap-2">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- الحد الأقصى للأعضاء -->
                    <div>
                        <label for="max_members" class="block text-sm font-semibold text-slate-700 mb-2">
                            {{ __('instructor.max_members_required') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-hashtag"></i>
                            <input type="number" name="max_members" id="max_members" value="{{ old('max_members', 10) }}"
                                   min="2" max="50" required
                                   class="form-input w-full px-4 py-3 rounded-xl bg-white text-slate-800">
                        </div>
                        @error('max_members')
                            <p class="mt-2 text-sm text-red-500 flex items-center gap-2">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- الحالة -->
                    <div class="md:col-span-2">
                        <label for="status" class="block text-sm font-semibold text-slate-700 mb-2">
                            {{ __('common.status') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-circle"></i>
                            <select name="status" id="status" required
                                    class="form-select w-full px-4 py-3 rounded-xl bg-white text-slate-800">
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>{{ __('instructor.active') }}</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>{{ __('instructor.inactive') }}</option>
                                <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>{{ __('instructor.archived') }}</option>
                            </select>
                        </div>
                        @error('status')
                            <p class="mt-2 text-sm text-red-500 flex items-center gap-2">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- الأزرار -->
                <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3 pt-6 mt-8 border-t border-slate-200">
                    <a href="{{ route('instructor.groups.index') }}"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3 border border-slate-300 rounded-xl text-slate-700 hover:bg-slate-50 font-semibold transition-colors">
                        <i class="fas fa-times"></i> {{ __('common.cancel') }}
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold shadow-sm hover:shadow transition-colors">
                        <i class="fas fa-save"></i> {{ __('instructor.create_group_btn') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@php
    $groupCreateI18n = [
        'loading' => __('instructor.loading_text'),
        'no_students' => __('instructor.no_students_in_course_msg'),
        'error' => __('instructor.error_fetching_data'),
    ];
@endphp
<script>
(function() {
    const courseSelect = document.getElementById('course_id');
    const leaderSelect = document.getElementById('leader_id');
    const i18n = @json($groupCreateI18n);

    function loadStudents(courseId) {
        while (leaderSelect.children.length > 1) leaderSelect.removeChild(leaderSelect.lastChild);
        if (!courseId) return;

        const loadingOpt = document.createElement('option');
        loadingOpt.value = ''; loadingOpt.textContent = i18n.loading; loadingOpt.disabled = true;
        leaderSelect.appendChild(loadingOpt);
        leaderSelect.disabled = true;

        fetch('/api/courses/' + courseId + '/students', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
            .then(r => r.ok ? r.json() : Promise.reject(new Error('Network error')))
            .then(data => {
                loadingOpt.remove();
                if (data.students && data.students.length > 0) {
                    data.students.forEach(s => {
                        const o = document.createElement('option');
                        o.value = s.id;
                        o.textContent = s.name || s.full_name || (s.first_name + ' ' + (s.last_name || ''));
                        leaderSelect.appendChild(o);
                    });
                } else {
                    const noOpt = document.createElement('option');
                    noOpt.value = ''; noOpt.textContent = i18n.no_students; noOpt.disabled = true;
                    leaderSelect.appendChild(noOpt);
                }
                leaderSelect.disabled = false;
            })
            .catch(err => {
                console.error(err);
                loadingOpt.remove();
                const errOpt = document.createElement('option');
                errOpt.value = ''; errOpt.textContent = i18n.error; errOpt.disabled = true;
                leaderSelect.appendChild(errOpt);
                leaderSelect.disabled = false;
            });
    }

    courseSelect.addEventListener('change', function() { loadStudents(this.value); });

    if (courseSelect.value) loadStudents(courseSelect.value);
})();
</script>
@endsection
