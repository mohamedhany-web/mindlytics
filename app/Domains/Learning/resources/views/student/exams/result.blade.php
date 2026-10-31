@extends('layouts.student-dashboard')

@section('title', __('student.exam_result_title'))
@section('header', __('student.exam_result_title'))

@section('content')
@php
    $passed = $attempt->status === 'completed' && $attempt->percentage >= $exam->passing_marks;
    $ringColor = $passed ? 'var(--sp-accent)' : '#f9e4d7';
@endphp

<div class="space-y-5 max-w-6xl">
    <nav class="flex flex-wrap items-center gap-2 text-sm font-bold text-[var(--sp-muted)]">
        <a href="{{ $exam->module_route ?? route('student.exams.index') }}" class="sp-link">{{ $exam->source_label ?? __('student.exams') }}</a>
        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180" />
        <a href="{{ route('student.exams.show', $exam) }}" class="sp-link truncate max-w-[40vw]">{{ $exam->title }}</a>
        <x-student.figma-icon name="icon-chevron.svg" box="size-3" class="opacity-40 rtl:rotate-180" />
        <span class="text-[var(--sp-text)]">{{ __('student.exam_result_title') }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <aside class="space-y-4">
            <section class="sp-card p-6 text-center space-y-4">
                <span class="sp-icon-bubble mx-auto" style="background:{{ $exam->source_bubble ?? 'var(--sp-peach)' }};width:56px;height:56px">
                    <x-student.figma-icon :name="$exam->source_icon ?? 'icon-exams.svg'" box="size-7" />
                </span>
                <div>
                    <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mb-1">{{ $exam->title }}</p>
                    <span class="sp-pill sp-pill--progress mb-2">{{ $exam->source_label ?? '' }}</span>
                    <p class="text-4xl font-black m-0" style="color:var(--sp-accent-text)">{{ number_format($attempt->percentage, 1) }}%</p>
                    <span class="sp-pill {{ $passed ? 'sp-pill--done' : 'sp-pill--upcoming' }} mt-2">{{ $attempt->result_status }}</span>
                </div>

                <div class="relative w-32 h-32 mx-auto">
                    <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36" aria-hidden="true">
                        <path stroke="#f0f0ec" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                        <path stroke="{{ $ringColor }}" stroke-width="3" fill="none" stroke-linecap="round" stroke-dasharray="{{ min(100, max(0, $attempt->percentage)) }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                    </svg>
                </div>

                <dl class="space-y-2 m-0 text-sm text-start">
                    <div class="flex justify-between gap-3 rounded-[14px] bg-[#f7f7f5] px-3 py-2.5">
                        <dt class="font-bold text-[var(--sp-muted)]">{{ __('student.exam_result_score') }}</dt>
                        <dd class="m-0 font-extrabold">{{ $attempt->score ?? 0 }} / {{ $exam->total_marks ?? $exam->calculateTotalMarks() }}</dd>
                    </div>
                    <div class="flex justify-between gap-3 rounded-[14px] bg-[#f7f7f5] px-3 py-2.5">
                        <dt class="font-bold text-[var(--sp-muted)]">{{ __('student.exam_result_time') }}</dt>
                        <dd class="m-0 font-extrabold">{{ $attempt->formatted_time ?? __('student.exam_time_unknown') }}</dd>
                    </div>
                    <div class="flex justify-between gap-3 rounded-[14px] bg-[#f7f7f5] px-3 py-2.5">
                        <dt class="font-bold text-[var(--sp-muted)]">{{ __('student.exam_result_submitted_at') }}</dt>
                        <dd class="m-0 font-extrabold">{{ $attempt->submitted_at ? $attempt->submitted_at->format('Y/m/d H:i') : '—' }}</dd>
                    </div>
                </dl>

                @if($attempt->auto_submitted)
                    <div class="rounded-[14px] px-3 py-2 text-xs font-bold" style="background:var(--sp-amber-soft);color:var(--sp-accent-text)">{{ __('student.exam_result_auto_submitted') }}</div>
                @endif
                @if($attempt->tab_switches > 0)
                    <div class="rounded-[14px] px-3 py-2 text-xs font-bold bg-[#f9e4d7] text-[#7a3b2e]">{{ __('student.exam_result_tab_switches', ['count' => $attempt->tab_switches]) }}</div>
                @endif

                <div class="flex flex-col gap-2 pt-1">
                    <a href="{{ route('student.exams.show', $exam) }}" class="sp-promo-btn !mt-0 text-center">{{ __('student.exam_result_back_exam') }}</a>
                    <a href="{{ route('student.exams.index') }}" class="inline-flex items-center justify-center rounded-[30px] px-4 py-2.5 text-sm font-extrabold bg-[#f7f7f5] text-[var(--sp-accent-text)] hover:bg-[var(--sp-accent)] transition-colors">{{ __('student.exam_result_back_list') }}</a>
                </div>
            </section>

            @if($attempt->feedback)
                <section class="sp-card p-5 space-y-2">
                    <h3 class="font-extrabold text-sm m-0">{{ __('student.exam_result_reviewer_feedback') }}</h3>
                    <p class="text-sm leading-relaxed m-0 whitespace-pre-wrap">{{ $attempt->feedback }}</p>
                    @if($attempt->reviewed_by)
                        <p class="text-xs text-[var(--sp-muted)] m-0 pt-2 border-t border-black/5">
                            {{ __('student.exam_result_reviewed_by', ['name' => $attempt->reviewer->name ?? __('student.exam_reviewer_default')]) }}
                            @if($attempt->reviewed_at) · {{ $attempt->reviewed_at->format('Y/m/d H:i') }} @endif
                        </p>
                    @endif
                </section>
            @endif
        </aside>

        <div class="lg:col-span-2">
            @if($exam->show_correct_answers || $exam->allow_review)
                <section class="sp-card overflow-hidden">
                    <div class="px-5 py-4 border-b border-black/5">
                        <h2 class="font-extrabold text-base m-0">{{ __('student.exam_result_review_questions') }}</h2>
                        <p class="text-sm text-[var(--sp-muted)] m-0 mt-1">{{ __('student.exam_result_review_hint') }}</p>
                    </div>
                    <div class="divide-y divide-black/5">
                        @foreach(($reviewQuestions ?? collect()) as $index => $examQuestion)
                            @php
                                $question = $examQuestion->question;
                                if (!$question) continue;
                                $userAnswer = $attempt->answers[$question->id] ?? null;
                                $isCorrect = $question->isCorrectAnswer($userAnswer);
                            @endphp
                            <article class="p-5 space-y-4">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="sp-pill sp-pill--progress">{{ __('student.exam_result_question', ['n' => $index + 1]) }}</span>
                                        <span class="text-xs font-bold text-[var(--sp-muted)]">{{ $examQuestion->marks }} {{ __('student.exam_result_point') }}</span>
                                    </div>
                                    @if($isCorrect === true)
                                        <span class="sp-pill sp-pill--done">{{ __('student.exam_result_correct') }}</span>
                                    @elseif($isCorrect === false)
                                        <span class="sp-pill sp-pill--upcoming">{{ __('student.exam_result_wrong') }}</span>
                                    @else
                                        <span class="sp-pill sp-pill--progress">{{ __('student.exam_result_needs_review') }}</span>
                                    @endif
                                </div>

                                <div class="text-sm sm:text-base leading-relaxed whitespace-pre-wrap">{!! nl2br(e($question->question)) !!}</div>

                                @if($question->image_url)
                                    <img src="{{ $question->getImageUrl() }}" alt="" class="max-w-full h-auto rounded-[16px] border border-black/5">
                                @endif

                                @if($question->type === 'multiple_choice' && $question->options)
                                    <div class="space-y-2">
                                        @foreach($question->options as $optionIndex => $option)
                                            @php
                                                $isUserAnswer = $userAnswer == $optionIndex || $userAnswer == $option;
                                                $isCorrectAnswer = in_array($optionIndex, (array) $question->correct_answer) || in_array($option, (array) $question->correct_answer);
                                                $rowClass = 'border-black/5 bg-[#f7f7f5]';
                                                if ($exam->show_correct_answers && $isCorrectAnswer) $rowClass = 'border-[var(--sp-accent)] bg-[rgba(174,217,234,.25)]';
                                                elseif ($isUserAnswer && !$isCorrectAnswer) $rowClass = 'border-[#f9e4d7] bg-[#f9e4d7]/30';
                                                elseif ($isUserAnswer) $rowClass = 'border-[var(--sp-accent)] bg-[rgba(174,217,234,.15)]';
                                            @endphp
                                            <div class="flex flex-wrap items-center justify-between gap-2 p-3 rounded-[14px] border {{ $rowClass }}">
                                                <span class="text-sm font-semibold">{{ chr(65 + $optionIndex) }}. {{ $option }}</span>
                                                <div class="flex gap-1">
                                                    @if($isUserAnswer)<span class="sp-pill sp-pill--progress !text-[10px]">{{ __('student.exam_result_your_answer') }}</span>@endif
                                                    @if($exam->show_correct_answers && $isCorrectAnswer)<span class="sp-pill sp-pill--done !text-[10px]">{{ __('student.exam_result_correct_answer') }}</span>@endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($question->type === 'true_false')
                                    <div class="space-y-2">
                                        @foreach(['true' => __('student.exam_true'), 'false' => __('student.exam_false')] as $value => $label)
                                            @php
                                                $isUserAnswer = $userAnswer === $value || $userAnswer === ($value === 'true' ? 'صح' : 'خطأ');
                                                $isCorrectAnswer = $question->correct_answer === $value;
                                            @endphp
                                            <div class="flex justify-between gap-2 p-3 rounded-[14px] border border-black/5 bg-[#f7f7f5]">
                                                <span>{{ $label }}</span>
                                                <div class="flex gap-1">
                                                    @if($isUserAnswer)<span class="sp-pill sp-pill--progress !text-[10px]">{{ __('student.exam_result_your_answer') }}</span>@endif
                                                    @if($exam->show_correct_answers && $isCorrectAnswer)<span class="sp-pill sp-pill--done !text-[10px]">{{ __('student.exam_result_correct_answer') }}</span>@endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif(in_array($question->type, ['fill_blank', 'short_answer', 'essay']))
                                    <div class="rounded-[14px] bg-[#f7f7f5] p-3 text-sm">
                                        <p class="text-xs font-bold text-[var(--sp-muted)] m-0 mb-1">{{ __('student.exam_result_your_answer') }}</p>
                                        <p class="m-0 whitespace-pre-wrap">{!! $userAnswer ? nl2br(e($userAnswer)) : '<em>'.e(__('student.exam_result_no_answer')).'</em>' !!}</p>
                                    </div>
                                    @if($exam->show_correct_answers && $question->correct_answer && !in_array($question->type, ['essay']))
                                        <div class="rounded-[14px] p-3 text-sm" style="background:var(--sp-mint)">
                                            <p class="text-xs font-bold m-0 mb-1">{{ __('student.exam_result_correct_answer') }}</p>
                                            <p class="m-0">{{ is_array($question->correct_answer) ? implode(' / ', $question->correct_answer) : $question->correct_answer }}</p>
                                        </div>
                                    @endif
                                @endif

                                @if($exam->show_explanations && $question->explanation)
                                    <div class="rounded-[14px] p-4 text-sm" style="background:var(--sp-sky)">
                                        <p class="font-extrabold m-0 mb-1">{{ __('student.exam_result_explanation') }}</p>
                                        <p class="m-0 leading-relaxed whitespace-pre-wrap">{{ $question->explanation }}</p>
                                    </div>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </section>
            @else
                <section class="sp-card p-10 text-center">
                    <span class="sp-icon-bubble mx-auto mb-4" style="background:var(--sp-lilac)">
                        <x-student.figma-icon name="icon-exams.svg" />
                    </span>
                    <h3 class="font-extrabold m-0 mb-2">{{ __('student.exam_result_review_unavailable') }}</h3>
                    <p class="text-sm text-[var(--sp-muted)] m-0">{{ __('student.exam_result_review_unavailable_hint') }}</p>
                </section>
            @endif
        </div>
    </div>
</div>
@endsection
