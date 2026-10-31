@extends('layouts.student-dashboard')

@section('title', __('student.group_assignments_title') . ' — ' . $group->name)
@section('header', __('student.group_assignments_title'))

@php
    $courseTitle = $group->course?->localized('title') ?? $group->course?->title ?? '—';
@endphp

@section('content')
<div class="space-y-5" x-data="{ openId: null }">
    @if(session('success'))
        <div class="sp-card !rounded-[16px] px-4 py-3 text-sm font-bold" style="background:var(--sp-mint);color:var(--sp-accent-text)">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="sp-card !rounded-[16px] px-4 py-3 text-sm font-bold bg-[#f9e4d7] text-[#7a3b2e]">
            {{ session('error') }}
        </div>
    @endif

    <nav class="flex flex-wrap items-center gap-2 text-sm font-bold text-[var(--sp-muted)]">
        <a href="{{ route('student.groups.index') }}" class="sp-link">{{ __('student.my_groups_title') }}</a>
        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180" />
        <a href="{{ route('student.groups.show', $group) }}" class="sp-link truncate max-w-[40vw]">{{ $group->name }}</a>
        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180" />
        <span class="text-[var(--sp-text)]">{{ __('student.group_assignments_title') }}</span>
    </nav>

    <section class="sp-card p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="min-w-0">
                <p class="text-sm font-bold text-[var(--sp-muted)] m-0 mb-1">{{ $group->name }}</p>
                <h2 class="sp-section-title m-0">{{ __('student.group_assignments_title') }}</h2>
                <p class="text-sm text-[var(--sp-muted)] m-0 mt-2">{{ $courseTitle }}</p>
                <p class="text-sm text-[var(--sp-muted)] m-0 mt-1">{{ __('student.group_assignments_hint') }}</p>
            </div>
            <a href="{{ route('student.groups.show', $group) }}"
               class="inline-flex items-center justify-center gap-2 rounded-[30px] px-4 py-2.5 text-sm font-extrabold bg-[#f7f7f5] text-[var(--sp-accent-text)] hover:bg-[var(--sp-accent)] transition-colors shrink-0">
                <x-student.figma-icon name="icon-messages.svg" box="size-4" />
                {{ __('student.group_back_to_chat') }}
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-5">
            <div class="rounded-[20px] bg-[#f7f7f5] p-4">
                <p class="text-xs font-bold text-[var(--sp-muted)] m-0">{{ __('student.groups_stat_assignments') }}</p>
                <p class="text-xl font-black text-[var(--sp-accent-text)] m-0 mt-1">{{ $assignments->count() }}</p>
            </div>
            <div class="rounded-[20px] bg-[#f7f7f5] p-4">
                <p class="text-xs font-bold text-[var(--sp-muted)] m-0">{{ __('student.groups_pending_assignments') }}</p>
                <p class="text-xl font-black text-[var(--sp-accent-text)] m-0 mt-1">{{ $pendingCount }}</p>
            </div>
            <div class="rounded-[20px] bg-[#f7f7f5] p-4 col-span-2 sm:col-span-1">
                <p class="text-xs font-bold text-[var(--sp-muted)] m-0">{{ __('student.group_submitted_count') }}</p>
                <p class="text-xl font-black text-[var(--sp-accent-text)] m-0 mt-1">{{ $submittedCount }}</p>
            </div>
        </div>
    </section>

    @forelse($assignments as $assignment)
        @php
            $sub = $assignment->group_submission ?? null;
            $isSubmitted = (bool) $sub;
            $isOverdue = !$isSubmitted && $assignment->due_date && $assignment->due_date->isPast();
            $isGraded = $sub && in_array($sub->status, ['graded', 'returned'], true);
        @endphp
        <article class="sp-card p-5 sm:p-6 space-y-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        @if($isSubmitted && $isGraded)
                            <span class="sp-pill sp-pill--done">{{ __('student.group_status_graded') }}</span>
                        @elseif($isSubmitted)
                            <span class="sp-pill sp-pill--done">{{ __('student.group_status_submitted') }}</span>
                        @elseif($isOverdue)
                            <span class="sp-pill sp-pill--upcoming">{{ __('student.group_status_overdue') }}</span>
                        @else
                            <span class="sp-pill sp-pill--progress">{{ __('student.group_status_pending') }}</span>
                        @endif
                        @if($assignment->max_score)
                            <span class="text-xs font-bold text-[var(--sp-muted)]">
                                {{ __('student.group_max_score') }}: {{ $assignment->max_score }}
                            </span>
                        @endif
                    </div>
                    <h3 class="font-extrabold text-base m-0 leading-snug">{{ $assignment->title }}</h3>
                    @if($assignment->due_date)
                        <p class="text-sm text-[var(--sp-muted)] m-0 mt-2 flex items-center gap-1.5">
                            <x-student.figma-icon name="icon-calendar.svg" box="size-3.5" />
                            {{ __('student.group_due_date') }}: {{ $assignment->due_date->format('Y/m/d H:i') }}
                        </p>
                    @endif
                    @if($assignment->description)
                        <p class="text-sm text-[var(--sp-text)] m-0 mt-3 leading-relaxed whitespace-pre-line">{{ $assignment->description }}</p>
                    @endif
                </div>

                @unless($isSubmitted)
                    <button type="button"
                            class="sp-promo-btn !mt-0 shrink-0 border-0 cursor-pointer"
                            @click="openId = openId === {{ $assignment->id }} ? null : {{ $assignment->id }}">
                        <span x-text="openId === {{ $assignment->id }} ? '{{ __('student.group_cancel_submit') }}' : '{{ __('student.group_submit_cta') }}'"></span>
                    </button>
                @endunless
            </div>

            @unless($isSubmitted)
                <form x-show="openId === {{ $assignment->id }}"
                      x-cloak
                      x-transition
                      action="{{ route('student.groups.assignments.submit', [$group, $assignment]) }}"
                      method="POST"
                      enctype="multipart/form-data"
                      class="rounded-[20px] bg-[#f7f7f5] p-4 sm:p-5 space-y-4 border border-black/5">
                    @csrf
                    <div>
                        <label class="block text-sm font-extrabold text-[var(--sp-text)] mb-2" for="content-{{ $assignment->id }}">
                            {{ __('student.group_submit_content') }}
                        </label>
                        <textarea
                            id="content-{{ $assignment->id }}"
                            name="content"
                            rows="4"
                            class="w-full rounded-[16px] border border-black/5 bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--sp-accent)]"
                            placeholder="{{ __('student.group_submit_content_placeholder') }}"
                        ></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-extrabold text-[var(--sp-text)] mb-2" for="files-{{ $assignment->id }}">
                            {{ __('student.group_submit_attachments') }}
                        </label>
                        <input
                            id="files-{{ $assignment->id }}"
                            type="file"
                            name="attachments[]"
                            multiple
                            accept=".pdf,.doc,.docx,.zip,.rar,.jpg,.jpeg,.png"
                            class="block w-full text-sm text-[var(--sp-muted)] file:me-3 file:rounded-full file:border-0 file:bg-[var(--sp-accent)] file:px-4 file:py-2 file:text-sm file:font-extrabold file:text-[var(--sp-accent-text)]"
                        >
                        <p class="text-xs text-[var(--sp-muted)] m-0 mt-2">{{ __('student.group_submit_attachments_hint') }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="submit" class="sp-promo-btn !mt-0 border-0 cursor-pointer">
                            {{ __('student.group_confirm_submit') }}
                        </button>
                        <button type="button"
                                class="rounded-[30px] px-4 py-2.5 text-sm font-extrabold text-[var(--sp-muted)] hover:text-[var(--sp-accent-text)]"
                                @click="openId = null">
                            {{ __('common.cancel') }}
                        </button>
                    </div>
                </form>
            @else
                @php $sub = $assignment->group_submission; @endphp
                <div class="rounded-[20px] bg-[#f7f7f5] p-4 sm:p-5 space-y-4">
                    <div class="flex flex-wrap items-center gap-3 text-sm">
                        <span class="font-extrabold">{{ __('student.group_status_submitted') }}</span>
                        @if($sub->submitted_at)
                            <span class="text-[var(--sp-muted)]">{{ $sub->submitted_at->diffForHumans() }}</span>
                        @endif
                    </div>

                    @if($sub->content)
                        <div>
                            <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mb-1">{{ __('student.group_submit_content') }}</p>
                            <p class="text-sm m-0 whitespace-pre-wrap leading-relaxed">{{ $sub->content }}</p>
                        </div>
                    @endif

                    @if($sub->attachments && count($sub->attachments) > 0)
                        <div>
                            <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mb-2">{{ __('student.group_uploaded_attachments') }}</p>
                            <ul class="space-y-2 m-0 p-0 list-none">
                                @foreach($sub->attachments as $att)
                                    @php
                                        $path = is_string($att) ? $att : ($att['path'] ?? $att['url'] ?? null);
                                        $url = is_array($att) && !empty($att['url'])
                                            ? $att['url']
                                            : ($path ? (str_starts_with($path, 'http') ? $path : url('storage/' . $path)) : '#');
                                        $label = is_array($att) ? ($att['name'] ?? basename($path ?? 'attachment')) : basename($att);
                                    @endphp
                                    <li>
                                        <a href="{{ $url }}" target="_blank" rel="noopener" class="sp-link inline-flex items-center gap-2 text-sm font-bold">
                                            <x-student.figma-icon name="icon-orders.svg" box="size-3.5" />
                                            {{ $label }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($sub->score !== null || $sub->feedback || in_array($sub->status, ['graded', 'returned'], true))
                        <div class="rounded-[16px] bg-white p-4 border border-black/5 space-y-3">
                            <h4 class="font-extrabold text-sm m-0 flex items-center gap-2">
                                <x-student.figma-icon name="icon-certificates.svg" box="size-4" />
                                {{ __('student.group_grade_section') }}
                            </h4>
                            @if($sub->score !== null)
                                <p class="text-sm m-0">
                                    <span class="font-bold text-[var(--sp-muted)]">{{ __('student.group_score') }}:</span>
                                    <span class="font-black text-[var(--sp-accent-text)] text-lg ms-1">{{ $sub->score }}</span>
                                    <span class="text-[var(--sp-muted)]">/ {{ $assignment->max_score }}</span>
                                </p>
                            @endif
                            @if(in_array($sub->status, ['graded', 'returned'], true))
                                <p class="text-sm m-0">
                                    <span class="font-bold text-[var(--sp-muted)]">{{ __('student.group_grade_status') }}:</span>
                                    @if($sub->status === 'graded')
                                        <span class="sp-pill sp-pill--done ms-1">{{ __('student.group_status_graded') }}</span>
                                    @else
                                        <span class="sp-pill sp-pill--progress ms-1">{{ __('student.group_status_returned') }}</span>
                                    @endif
                                </p>
                            @endif
                            @if($sub->feedback)
                                <div>
                                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mb-1">{{ __('student.group_feedback') }}</p>
                                    <p class="text-sm m-0 whitespace-pre-wrap leading-relaxed bg-[#f7f7f5] rounded-[12px] p-3">{{ $sub->feedback }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endunless
        </article>
    @empty
        <div class="sp-card p-10 text-center">
            <span class="sp-icon-bubble mx-auto mb-4" style="background:var(--sp-amber-soft);width:56px;height:56px">
                <x-student.figma-icon name="icon-messages.svg" box="size-7" />
            </span>
            <h3 class="font-extrabold text-lg m-0 mb-2">{{ __('student.group_no_assignments') }}</h3>
            <p class="text-sm text-[var(--sp-muted)] m-0 mb-5 max-w-md mx-auto">{{ __('student.group_no_assignments_hint') }}</p>
            <a href="{{ route('student.groups.show', $group) }}" class="sp-promo-btn inline-flex">{{ __('student.group_back_to_chat') }}</a>
        </div>
    @endforelse
</div>
@endsection
