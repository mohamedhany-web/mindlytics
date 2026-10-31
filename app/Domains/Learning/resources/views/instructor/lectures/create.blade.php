@extends('layouts.app')

@section('title', __('instructor.add_new_lecture') . ' - Mindlytics')
@section('header', __('instructor.add_new_lecture'))

@push('styles')
<style>
    .video-preview-container {
        min-height: 300px;
        background: #f8fafc;
        border: 2px dashed #e2e8f0;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    .video-preview-container.has-video {
        border: 2px solid #0ea5e9;
        background: #000;
    }
    .video-preview-container iframe,
    .video-preview-container video {
        width: 100%;
        height: 100%;
        min-height: 300px;
    }
    .platform-selector {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .platform-option {
        padding: 1.5rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.75rem;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
        background: white;
    }
    .platform-option:hover {
        border-color: #0ea5e9;
        background: #f0f9ff;
    }
    .platform-option.active {
        border-color: #0ea5e9;
        background: #e0f2fe;
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.2);
    }
    .platform-option i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        display: block;
    }
    .video-info-card {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-top: 1rem;
    }
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid #f1f5f9;
        border-top-color: #0ea5e9;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush

@section('content')
@php
    $placeholders = [
        'youtube' => __('instructor.paste_youtube'),
        'vimeo' => __('instructor.paste_vimeo'),
        'google_drive' => __('instructor.paste_drive'),
        'direct' => __('instructor.paste_direct'),
        'bunny' => __('instructor.paste_bunny'),
        'default' => __('instructor.paste_video'),
    ];
@endphp
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6 mb-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="{{ route('instructor.lectures.index') }}" class="hover:text-sky-600 transition-colors">{{ __('instructor.lectures') }}</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">{{ __('instructor.add_new_lecture') }}</span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-chalkboard-teacher text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800">{{ __('instructor.add_new_lecture') }}</h1>
                    <p class="text-sm text-slate-600 mt-0.5">{{ __('instructor.create_lecture_subtitle') }}</p>
                </div>
            </div>
            <a href="{{ route('instructor.lectures.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                <i class="fas fa-arrow-right"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('instructor.lectures.store') }}" method="POST" enctype="multipart/form-data"
          class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden"
          x-data="videoPreviewData()">
        @csrf
        <div class="p-6 sm:p-8 space-y-8">
            <!-- Basic info -->
            <div class="space-y-6">
                <h2 class="text-lg font-bold text-slate-800 border-b border-slate-200 pb-2">{{ __('instructor.basic_info') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="course_id" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('instructor.course') }} <span class="text-red-500">*</span></label>
                        <select name="course_id" id="course_id" required
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
                            <option value="">{{ __('instructor.choose_course') }}</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ (old('course_id', request('course_id')) == $course->id) ? 'selected' : '' }}>{{ $course->title }}</option>
                            @endforeach
                        </select>
                        @error('course_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="course_lesson_id" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('instructor.lesson_optional') }}</label>
                        <select name="course_lesson_id" id="course_lesson_id"
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
                            <option value="">{{ __('instructor.no_lesson') }}</option>
                            @foreach($lessons as $lesson)
                                <option value="{{ $lesson->id }}" {{ old('course_lesson_id') == $lesson->id ? 'selected' : '' }}>{{ $lesson->title }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-slate-500">{{ __('instructor.lesson_link_hint') }}</p>
                        @error('course_lesson_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('instructor.lecture_title') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                               placeholder="{{ __('instructor.lecture_title_placeholder') }}"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
                        @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('instructor.description') }}</label>
                        <textarea name="description" id="description" rows="3" placeholder="{{ __('instructor.description_placeholder') }}"
                                  class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white resize-none">{{ old('description') }}</textarea>
                        @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Recording link -->
            <div class="space-y-6 pt-6 border-t border-slate-200">
                <h2 class="text-lg font-bold text-slate-800 border-b border-slate-200 pb-2">
                    <i class="fas fa-video text-sky-600 ml-1"></i>
                    {{ __('instructor.recording_link_section') }}
                </h2>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-3">{{ __('instructor.video_source_question') }} <span class="text-red-500">*</span></label>
                    <div class="platform-selector">
                        <div class="platform-option" :class="{ 'active': selectedPlatform === 'youtube' }" @click="selectPlatform('youtube')">
                            <i class="fab fa-youtube text-red-600"></i>
                            <div class="font-bold text-slate-800 text-sm mt-1">YouTube</div>
                        </div>
                        <div class="platform-option" :class="{ 'active': selectedPlatform === 'vimeo' }" @click="selectPlatform('vimeo')">
                            <i class="fab fa-vimeo text-blue-500"></i>
                            <div class="font-bold text-slate-800 text-sm mt-1">Vimeo</div>
                        </div>
                        <div class="platform-option" :class="{ 'active': selectedPlatform === 'google_drive' }" @click="selectPlatform('google_drive')">
                            <i class="fab fa-google-drive text-green-600"></i>
                            <div class="font-bold text-slate-800 text-sm mt-1">Google Drive</div>
                        </div>
                        <div class="platform-option" :class="{ 'active': selectedPlatform === 'direct' }" @click="selectPlatform('direct')">
                            <i class="fas fa-file-video text-purple-600"></i>
                            <div class="font-bold text-slate-800 text-sm mt-1">{{ __('instructor.direct_link') }}</div>
                        </div>
                        <div class="platform-option" :class="{ 'active': selectedPlatform === 'bunny' }" @click="selectPlatform('bunny')">
                            <i class="fas fa-cloud text-orange-600"></i>
                            <div class="font-bold text-slate-800 text-sm mt-1">Bunny.net</div>
                        </div>
                    </div>
                    <input type="hidden" name="video_platform" x-model="selectedPlatform" required>
                </div>

                <div x-show="selectedPlatform" x-transition class="space-y-4">
                    <div>
                        <label for="recording_url" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('instructor.video_url') }} <span class="text-red-500">*</span></label>
                        <div class="flex gap-3 flex-wrap">
                            <input type="url" id="recording_url" name="recording_url"
                                   x-model="videoUrl"
                                   @input.debounce.1000ms="updatePreview()"
                                   @paste.debounce.1000ms="updatePreview()"
                                   @blur="updatePreview()"
                                   value="{{ old('recording_url') }}"
                                   :placeholder="getPlaceholder()"
                                   class="flex-1 min-w-[200px] px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
                            <button type="button" @click="updatePreview()"
                                    :disabled="!selectedPlatform || !videoUrl || isLoading"
                                    class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap">
                                <span x-show="!isLoading"><i class="fas fa-search ml-1"></i> {{ __('instructor.read_link') }}</span>
                                <span x-show="isLoading" class="flex items-center gap-2"><span class="loading-spinner"></span> {{ __('instructor.reading_link') }}</span>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-slate-500" x-show="selectedPlatform"><i class="fas fa-info-circle ml-1"></i> {{ __('instructor.video_info_auto') }}</p>
                        @error('recording_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div x-show="videoInfo" class="video-info-card" x-transition>
                        <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><i class="fas fa-info-circle text-sky-600"></i> {{ __('instructor.video_info') }}</h4>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div><span class="font-semibold text-slate-600">{{ __('instructor.title_label') }}:</span> <span class="text-slate-800" x-text="videoInfo?.title || '{{ addslashes(__('instructor.not_available')) }}'"></span></div>
                            <div><span class="font-semibold text-slate-600">{{ __('instructor.duration_label') }}:</span> <span class="text-slate-800" x-text="videoInfo?.duration || '{{ addslashes(__('instructor.not_available')) }}'"></span></div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('instructor.video_preview') }}</label>
                        <div class="video-preview-container" :class="{ 'has-video': hasPreview }">
                            <div x-show="!hasPreview && selectedPlatform" class="text-center text-slate-500 p-8">
                                <i class="fas fa-video text-4xl mb-3 text-slate-300"></i>
                                <p class="font-bold text-slate-600">{{ __('instructor.video_preview') }}</p>
                                <p class="text-sm">{{ __('instructor.video_preview_hint') }}</p>
                            </div>
                            <div x-ref="previewContainer" class="w-full h-full flex items-center justify-center p-4" style="min-height: 300px;"></div>
                        </div>
                    </div>
                </div>

                <div x-show="!selectedPlatform" class="bg-sky-50 border border-sky-200 rounded-xl p-6 text-center">
                    <i class="fas fa-hand-point-up text-3xl text-sky-500 mb-2"></i>
                    <p class="font-bold text-slate-800">{{ __('instructor.choose_video_source_first') }}</p>
                    <p class="text-sm text-slate-600">{{ __('instructor.choose_platform_hint') }}</p>
                </div>
            </div>

            <!-- Date & duration -->
            <div class="space-y-6 pt-6 border-t border-slate-200">
                <h2 class="text-lg font-bold text-slate-800 border-b border-slate-200 pb-2">
                    <i class="fas fa-calendar-alt text-sky-600 ml-1"></i>
                    {{ __('instructor.date_time') }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="scheduled_at" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('instructor.date_time') }} <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="scheduled_at" id="scheduled_at" value="{{ old('scheduled_at') }}" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
                        @error('scheduled_at')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="duration_minutes" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('instructor.duration_minutes_label') }} <span class="text-red-500">*</span></label>
                        <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', 60) }}" min="15" max="480" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white">
                        @error('duration_minutes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="min_watch_percent_to_unlock_next" class="block text-sm font-semibold text-slate-700 mb-1">نسبة المشاهدة المطلوبة لفتح المحاضرة التالية</label>
                        <input type="number" name="min_watch_percent_to_unlock_next" id="min_watch_percent_to_unlock_next"
                               value="{{ old('min_watch_percent_to_unlock_next', 0) }}" min="0" max="100"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white"
                               placeholder="مثال: 80 يعني يجب مشاهدة 80% من هذه المحاضرة لفتح التالية">
                        <p class="mt-1 text-xs text-slate-500">اتركها 0 أو فارغة إذا لم ترغب في قفل المحاضرة التالية على نسبة مشاهدة معينة.</p>
                    </div>
                </div>
            </div>

            <!-- مواد المحاضرة -->
            <div class="space-y-6 pt-6 border-t border-slate-200">
                <h2 class="text-lg font-bold text-slate-800 border-b border-slate-200 pb-2">
                    <i class="fas fa-paperclip text-sky-600 ml-1"></i>
                    مواد المحاضرة (اختياري)
                </h2>
                <p class="text-sm text-slate-600">يمكنك رفع ملفات (PDF، Word، عروض...) وتحديد ظهورها للطالب.</p>
                <div id="materials-container" class="space-y-4">
                    <div class="material-row flex flex-wrap items-end gap-3 p-4 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="flex-1 min-w-[180px]">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">الملف</label>
                            <input type="file" name="material_files[]" class="w-full text-sm text-slate-700 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-sky-100 file:text-sky-700 file:font-semibold file:cursor-pointer hover:file:bg-sky-200" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.zip,.rar,.png,.jpg,.jpeg">
                        </div>
                        <div class="w-48">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">عنوان (اختياري)</label>
                            <input type="text" name="material_titles[]" placeholder="مثال: ملخص المحاضرة" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                        </div>
                        <label class="flex items-center gap-2 pb-2">
                            <input type="hidden" name="material_visible[]" value="0">
                            <input type="checkbox" name="material_visible[]" value="1" checked class="w-4 h-4 text-sky-600 rounded">
                            <span class="text-sm font-medium text-slate-700">ظاهر للطالب</span>
                        </label>
                        <button type="button" class="remove-material px-3 py-2 bg-rose-100 text-rose-700 rounded-lg text-sm font-medium hover:bg-rose-200" style="display:none;"><i class="fas fa-times ml-1"></i> حذف</button>
                    </div>
                </div>
                <button type="button" id="add-material-btn" class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-100 text-sky-700 rounded-xl font-semibold text-sm hover:bg-sky-200 transition-colors">
                    <i class="fas fa-plus"></i>
                    إضافة مادة أخرى
                </button>
            </div>

            <!-- Notes -->
            <div class="space-y-6 pt-6 border-t border-slate-200">
                <h2 class="text-lg font-bold text-slate-800 border-b border-slate-200 pb-2">
                    <i class="fas fa-sticky-note text-sky-600 ml-1"></i>
                    {{ __('instructor.notes_section') }}
                </h2>
                <div>
                    <label for="notes" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('instructor.additional_notes') }}</label>
                    <textarea name="notes" id="notes" rows="4" placeholder="{{ __('instructor.notes_placeholder') }}"
                              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 bg-white resize-none">{{ old('notes') }}</textarea>
                    @error('notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Options -->
            <div class="space-y-6 pt-6 border-t border-slate-200">
                <h2 class="text-lg font-bold text-slate-800 border-b border-slate-200 pb-2">
                    <i class="fas fa-cog text-sky-600 ml-1"></i>
                    {{ __('instructor.options_section') }}
                </h2>
                <div class="space-y-3">
                    <label class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl cursor-pointer hover:bg-sky-50/50 border border-slate-200 hover:border-sky-200 transition-colors">
                        <input type="checkbox" name="has_attendance_tracking" value="1" {{ old('has_attendance_tracking', true) ? 'checked' : '' }}
                               class="w-5 h-5 text-sky-600 border-slate-300 rounded focus:ring-sky-500">
                        <div>
                            <div class="font-bold text-slate-800">{{ __('instructor.attendance_tracking') }}</div>
                            <div class="text-sm text-slate-600">{{ __('instructor.attendance_tracking_desc') }}</div>
                        </div>
                    </label>
                    <label class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl cursor-pointer hover:bg-sky-50/50 border border-slate-200 hover:border-sky-200 transition-colors">
                        <input type="checkbox" name="has_assignment" value="1" {{ old('has_assignment') ? 'checked' : '' }}
                               class="w-5 h-5 text-sky-600 border-slate-300 rounded focus:ring-sky-500">
                        <div>
                            <div class="font-bold text-slate-800">{{ __('instructor.has_assignment') }}</div>
                            <div class="text-sm text-slate-600">{{ __('instructor.has_assignment_desc') }}</div>
                        </div>
                    </label>
                    <label class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl cursor-pointer hover:bg-sky-50/50 border border-slate-200 hover:border-sky-200 transition-colors">
                        <input type="checkbox" name="has_evaluation" value="1" {{ old('has_evaluation') ? 'checked' : '' }}
                               class="w-5 h-5 text-sky-600 border-slate-300 rounded focus:ring-sky-500">
                        <div>
                            <div class="font-bold text-slate-800">{{ __('instructor.has_evaluation') }}</div>
                            <div class="text-sm text-slate-600">{{ __('instructor.has_evaluation_desc') }}</div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="px-6 sm:px-8 py-5 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-end gap-3">
            <a href="{{ route('instructor.lectures.index') }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                {{ __('common.cancel') }}
            </a>
            <button type="submit" class="px-6 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-semibold transition-colors shadow-lg hover:shadow-xl">
                <i class="fas fa-save ml-2"></i>
                {{ __('instructor.save_lecture') }}
            </button>
        </div>
    </form>
</div>

<script>
window.__lecturePlaceholders = @json($placeholders);

function videoPreviewData() {
    return {
        selectedPlatform: '{{ old('video_platform', '') }}',
        videoUrl: '{{ old('recording_url', '') }}',
        videoInfo: null,
        isLoading: false,
        hasPreview: false,
        selectPlatform(platform) {
            this.selectedPlatform = platform;
            this.videoUrl = '';
            this.hasPreview = false;
            this.videoInfo = null;
            this.clearPreview();
        },
        getPlaceholder() {
            const p = window.__lecturePlaceholders || {};
            if (this.selectedPlatform === 'youtube') return p.youtube || '';
            if (this.selectedPlatform === 'vimeo') return p.vimeo || '';
            if (this.selectedPlatform === 'google_drive') return p.google_drive || '';
            if (this.selectedPlatform === 'direct') return p.direct || '';
            if (this.selectedPlatform === 'bunny') return p.bunny || '';
            return p.default || '';
        },
        updatePreview() {
            if (!this.videoUrl || !this.selectedPlatform) { this.hasPreview = false; this.clearPreview(); return; }
            const url = String(this.videoUrl).trim();
            if (!url) { this.hasPreview = false; this.clearPreview(); return; }
            this.generatePreview(url);
            this.fetchVideoInfo();
        },
        generatePreview(url) {
            try {
                const container = this.$refs.previewContainer;
                if (!container) return;
                let html = '', isValid = false;
                const t = window.__lecturePlaceholders || {};
                const youtubeInvalid = '{{ addslashes(__('instructor.youtube_invalid')) }}';
                const vimeoInvalid = '{{ addslashes(__('instructor.vimeo_invalid')) }}';
                const driveNote = '{{ addslashes(__('instructor.drive_note')) }}';
                const directInvalid = '{{ addslashes(__('instructor.direct_invalid')) }}';
                const bunnyInvalid = '{{ addslashes(__('instructor.bunny_invalid')) }}';
                const previewError = '{{ addslashes(__('instructor.preview_error')) }}';

                if (this.selectedPlatform === 'youtube') {
                    let videoId = (url.match(/[?&]v=([a-zA-Z0-9_-]{11})/) || [])[1]
                        || (url.match(/youtu\.be\/([a-zA-Z0-9_-]{11})/) || [])[1]
                        || (url.match(/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/) || [])[1]
                        || (url.match(/youtube\.com\/v\/([a-zA-Z0-9_-]{11})/) || [])[1];
                    if (videoId && videoId.length === 11) {
                        isValid = true;
                        const origin = encodeURIComponent(window.location.origin);
                        html = '<iframe src="https://www.youtube.com/embed/' + videoId + '?rel=0&modestbranding=1&showinfo=0&controls=1&enablejsapi=1&origin=' + origin + '" width="100%" height="400" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="border-radius: 0.75rem;"></iframe>';
                    }
                    if (!isValid) html = '<div class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm"><i class="fas fa-exclamation-circle ml-1"></i> ' + youtubeInvalid + '</div>';
                } else if (this.selectedPlatform === 'vimeo') {
                    const m = url.match(/vimeo\.com\/(?:.*\/)?(\d+)/);
                    if (m && m[1]) {
                        isValid = true;
                        html = '<iframe src="https://player.vimeo.com/video/' + m[1] + '?title=0&byline=0&portrait=0" width="100%" height="400" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen style="border-radius: 0.75rem;"></iframe>';
                    }
                    if (!isValid) html = '<div class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm"><i class="fas fa-exclamation-circle ml-1"></i> ' + vimeoInvalid + '</div>';
                } else if (this.selectedPlatform === 'google_drive') {
                    const m = url.match(/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/);
                    if (m && m[1]) {
                        isValid = true;
                        html = '<iframe src="https://drive.google.com/file/d/' + m[1] + '/preview" width="100%" height="400" frameborder="0" allow="autoplay" style="border-radius: 0.75rem;"></iframe>';
                    }
                    if (!isValid) html = '<div class="p-4 bg-amber-50 border border-amber-200 rounded-lg text-amber-800 text-sm"><i class="fas fa-info-circle ml-1"></i> ' + driveNote + '</div>';
                } else if (this.selectedPlatform === 'direct') {
                    if (/\.(mp4|webm|ogg|avi|mov)(\?.*)?$/i.test(url)) {
                        isValid = true;
                        const esc = url.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                        html = '<video controls width="100%" height="400" style="max-height: 400px; border-radius: 0.75rem;" class="w-full"><source src="' + esc + '" type="video/mp4">Your browser does not support video.</video>';
                    }
                    if (!isValid) html = '<div class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm"><i class="fas fa-exclamation-circle ml-1"></i> ' + directInvalid + '</div>';
                } else if (this.selectedPlatform === 'bunny') {
                    const bunnyMatch = url.match(/mediadelivery\.net\/embed\/(\d+)\/([a-zA-Z0-9_-]+)/);
                    if (bunnyMatch && bunnyMatch[1] && bunnyMatch[2]) {
                        isValid = true;
                        const embedUrl = url.split('?')[0];
                        const src = embedUrl.startsWith('http') ? embedUrl : ('https://' + embedUrl.replace(/^\/+/, ''));
                        html = '<iframe src="' + src.replace(/"/g, '&quot;') + '" width="100%" height="400" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; picture-in-picture" allowfullscreen style="border-radius: 0.75rem;"></iframe>';
                    }
                    if (!isValid) html = '<div class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm"><i class="fas fa-exclamation-circle ml-1"></i> ' + bunnyInvalid + '</div>';
                }
                if (html) { container.innerHTML = html; this.hasPreview = true; } else { this.clearPreview(); }
            } catch (e) {
                console.error(e);
                const container = this.$refs.previewContainer;
                const previewError = '{{ addslashes(__('instructor.preview_error')) }}';
                if (container) { container.innerHTML = '<div class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">' + previewError + '</div>'; this.hasPreview = true; }
            }
        },
        clearPreview() {
            const c = this.$refs.previewContainer;
            if (c) c.innerHTML = '';
            this.hasPreview = false;
        },
        async fetchVideoInfo() {
            if (!this.videoUrl || !this.selectedPlatform) return;
            this.isLoading = true;
            this.videoInfo = null;
            try {
                const r = await fetch('/api/video/info', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ url: this.videoUrl, platform: this.selectedPlatform })
                });
                const data = await r.json();
                if (data.success) this.videoInfo = data.data;
            } catch (e) { console.log('Video info fetch failed:', e); }
            finally { this.isLoading = false; }
        }
    };
}

document.addEventListener('DOMContentLoaded', function() {
    var materialsContainer = document.getElementById('materials-container');
    var addMaterialBtn = document.getElementById('add-material-btn');
    if (materialsContainer && addMaterialBtn) {
        addMaterialBtn.addEventListener('click', function() {
            var first = materialsContainer.querySelector('.material-row');
            if (!first) return;
            var clone = first.cloneNode(true);
            clone.querySelector('input[type="file"]').value = '';
            clone.querySelector('input[type="text"]').value = '';
            clone.querySelector('input[type="checkbox"]').checked = true;
            clone.querySelector('.remove-material').style.display = 'inline-flex';
            materialsContainer.appendChild(clone);
        });
        materialsContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-material')) {
                var row = e.target.closest('.material-row');
                if (materialsContainer.querySelectorAll('.material-row').length > 1) row.remove();
            }
        });
    }

    const courseSelect = document.getElementById('course_id');
    if (!courseSelect) return;
    courseSelect.addEventListener('change', function() {
        const courseId = this.value;
        const lessonSelect = document.getElementById('course_lesson_id');
        while (lessonSelect.children.length > 1) lessonSelect.removeChild(lessonSelect.lastChild);
        if (!courseId) return;
        fetch('/api/courses/' + courseId + '/lessons')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.lessons) {
                    data.lessons.forEach(function(lesson) {
                        const opt = document.createElement('option');
                        opt.value = lesson.id;
                        opt.textContent = lesson.title;
                        lessonSelect.appendChild(opt);
                    });
                }
            })
            .catch(function() {
                fetch('{{ route('instructor.lectures.create') }}?course_id=' + courseId)
                    .then(function(r) { return r.text(); })
                    .then(function(html) {
                        const doc = new DOMParser().parseFromString(html, 'text/html');
                        const sel = doc.getElementById('course_lesson_id');
                        if (sel) Array.from(sel.options).forEach(function(o) {
                            if (o.value) {
                                const no = document.createElement('option');
                                no.value = o.value;
                                no.textContent = o.textContent;
                                lessonSelect.appendChild(no);
                            }
                        });
                    })
                    .catch(function() {});
            });
    });
});
</script>
@endsection
