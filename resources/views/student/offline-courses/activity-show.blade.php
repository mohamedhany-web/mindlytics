@extends('layouts.student-dashboard')

@section('title', __('student.oc_activity_page_title', ['title' => $activity->title]))

@php
    $sg = $studentRouteGroup ?? 'student.offline-courses';
    $isOnline = ($channel ?? 'offline') === 'online';
    $channelLabel = $isOnline ? __('student.online_badge') : __('student.offline_badge');
    $listTitle = $isOnline ? __('student.my_online_courses') : __('student.offline_courses_title');
    $maxScore = (int) $activity->max_score;
@endphp

@push('styles')
@include('student.offline-courses.partials.los-styles')
@endpush

@section('content')
<div class="oc">
    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="{{ __('student.oc_breadcrumb') }}">
                <a href="{{ route('dashboard') }}">{{ __('los.page_title') }}</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route($sg . '.index') }}">{{ $listTitle }}</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route($sg . '.show', $offlineCourse) }}">{{ \Illuminate\Support\Str::limit($offlineCourse->title, 28) }}</a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700">{{ __('student.oc_activity_label') }}</span>
            </nav>
            <h1>{{ $activity->title }}</h1>
            <p class="sub">{{ __('student.oc_activity_subtitle', ['channel' => $channelLabel, 'course' => $offlineCourse->title]) }}</p>
        </div>
        <div class="oc-signals">
            <span class="oc-signal oc-signal-hot">{{ __('student.oc_points', ['count' => $maxScore]) }}</span>
        </div>
    </header>

<div class="space-y-6">
    <div class="mb-0">
        <a href="{{ route(($studentRouteGroup ?? 'student.offline-courses') . '.show', $offlineCourse) }}" class="oc-btn oc-btn-quiet" style="min-height:36px">
            <i class="fas fa-arrow-right text-xs"></i>
            {{ __('student.oc_back_to_course') }}
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-gray-100">
            <h1 class="text-xl font-bold text-gray-900">{{ $activity->title }}</h1>
            <p class="text-sm text-gray-600 mt-1">
                {{ $activity->type }}
                | {{ __('student.oc_max_score', ['score' => $activity->max_score]) }}
                @if($activity->due_date)| {{ __('student.oc_due_date_label', ['date' => $activity->due_date->format('Y-m-d')]) }}@endif
            </p>
        </div>
        <div class="p-5 sm:p-6 space-y-6">
            @if($activity->description)
                <div>
                    <h3 class="text-sm font-bold text-gray-700 mb-2">{{ __('student.oc_description') }}</h3>
                    <p class="text-gray-700 whitespace-pre-line">{{ $activity->description }}</p>
                </div>
            @endif
            @if($activity->instructions)
                <div>
                    <h3 class="text-sm font-bold text-gray-700 mb-2">{{ __('student.oc_submission_instructions') }}</h3>
                    <p class="text-gray-700 whitespace-pre-line">{{ $activity->instructions }}</p>
                </div>
            @endif
            @if($activity->attachments && count($activity->attachments) > 0)
                <div>
                    <h3 class="text-sm font-bold text-gray-700 mb-2">{{ __('student.oc_instructor_attachments') }}</h3>
                    <ul class="space-y-2">
                        @foreach($activity->attachments as $att)
                            <li>
                                <a href="{{ stored_upload_file_url($att) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-teal-700 hover:underline font-medium">
                                    <i class="fas fa-paperclip text-slate-400"></i>
                                    {{ $att['name'] ?? __('student.oc_file') }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($submission)
                <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4 sm:p-5 space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h3 class="font-bold text-slate-800">{{ __('student.oc_your_submission') }}</h3>
                        @if($submission->submitted_at)
                            <span class="text-xs text-slate-500">{{ __('student.oc_last_updated', ['datetime' => $submission->submitted_at->format('Y-m-d H:i')]) }}</span>
                        @endif
                    </div>
                    @if($submission->status === 'submitted')
                        <p class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">{{ __('student.oc_submission_pending') }}</p>
                    @endif
                    @if($submission->submission_content)
                        <div>
                            <h4 class="text-xs font-semibold text-slate-600 mb-1">{{ __('student.oc_submission_text_label') }}</h4>
                            <p class="text-gray-800 whitespace-pre-line text-sm">{{ $submission->submission_content }}</p>
                        </div>
                    @endif
                    @if($submission->attachments && count($submission->attachments))
                        <div>
                            <h4 class="text-xs font-semibold text-slate-600 mb-2">{{ __('student.oc_uploaded_files') }}</h4>
                            <ul class="space-y-2">
                                @foreach($submission->attachments as $f)
                                    <li>
                                        <a href="{{ offline_activity_submission_file_url($f) }}" target="_blank" rel="noopener" download="{{ $f['name'] ?? 'download' }}" class="inline-flex items-center gap-2 text-teal-700 hover:underline text-sm font-medium">
                                            <i class="fas fa-download text-slate-400"></i>
                                            {{ $f['name'] ?? __('student.oc_file') }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif

            @if($submission && $submission->status === 'graded')
                <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 sm:p-5 space-y-3">
                    <h3 class="font-bold text-emerald-900">{{ __('student.oc_grading_result') }}</h3>
                    <div class="flex flex-wrap items-baseline gap-3">
                        <p class="text-2xl font-bold text-emerald-800">{{ $submission->score }}</p>
                        <span class="text-emerald-700 font-medium">/ {{ $activity->max_score }}</span>
                        @if($maxScore > 0)
                            @php $pct = round((float) $submission->score / $maxScore * 100, 1); @endphp
                            <span class="text-sm text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-lg">{{ __('student.oc_percentage', ['pct' => $pct]) }}</span>
                        @endif
                    </div>
                    @if($submission->graded_at)
                        <p class="text-xs text-emerald-800/90">{{ __('student.oc_graded_at', ['datetime' => $submission->graded_at->format('Y-m-d H:i')]) }}</p>
                    @endif
                    @if($submission->relationLoaded('grader') && $submission->grader)
                        <p class="text-xs text-emerald-800/90">{{ __('student.oc_graded_by', ['name' => $submission->grader->name]) }}</p>
                    @endif
                    @if($submission->feedback)
                        <div>
                            <h4 class="text-xs font-semibold text-emerald-900 mb-1">{{ __('student.oc_instructor_feedback') }}</h4>
                            <p class="text-gray-800 whitespace-pre-line text-sm leading-relaxed">{{ $submission->feedback }}</p>
                        </div>
                    @else
                        <p class="text-sm text-emerald-800/80">{{ __('student.oc_no_feedback') }}</p>
                    @endif
                </div>
            @endif

            @if($activity->status !== 'published')
                <p class="text-amber-800 bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm">{{ __('student.oc_activity_unavailable') }}</p>
            @elseif(!$submission || $submission->status !== 'graded')
                <div class="border-t border-gray-100 pt-6">
                    <h3 class="text-sm font-bold text-gray-800 mb-3">@if($submission && $submission->status === 'submitted'){{ __('student.oc_update_submission') }}@else{{ __('student.oc_submit_activity_heading') }}@endif</h3>
                    <form action="{{ route(($studentRouteGroup ?? 'student.offline-courses') . '.activities.submit', [$offlineCourse, $activity]) }}" method="post" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('student.oc_submission_text_optional') }}</label>
                            <textarea name="submission_content" rows="5" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 focus:ring-2 focus:ring-teal-500" placeholder="{{ __('student.oc_submission_placeholder') }}">{{ old('submission_content', $submission->submission_content ?? '') }}</textarea>
                            @error('submission_content')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('student.oc_attachments_optional') }}</label>
                            <input type="file" name="attachments[]" multiple class="w-full rounded-xl border border-gray-200 px-4 py-2.5">
                            <p class="text-xs text-gray-500 mt-1">{{ __('student.oc_attachments_hint') }}</p>
                            @error('attachments.*')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="px-4 py-2.5 bg-[#2f7f7d] text-white rounded-xl font-semibold hover:bg-[#2f7f7d]">
                            @if($submission && $submission->status === 'submitted')
                                {{ __('student.oc_update_submission_btn') }}
                            @else
                                {{ __('student.oc_submit_activity_btn') }}
                            @endif
                        </button>
                    </form>
                </div>
            @elseif($submission && $submission->status === 'graded')
                <p class="text-sm text-slate-600 border border-slate-200 rounded-lg px-3 py-2 bg-slate-50">{{ __('student.oc_graded_locked') }}</p>
            @endif
        </div>
    </div>
</div>
</div>
@endsection
