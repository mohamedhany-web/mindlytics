@extends('layouts.student-dashboard')

@section('immersive', 'true')
@section('title', $exam->title)
@section('header', '')

@section('content')
@php $isRtl = app()->getLocale() === 'ar'; @endphp
<div class="min-h-screen" style="background:var(--sp-bg)" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    <div class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-black/5 shadow-sm px-4 lg:px-6 py-3">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3 min-w-0">
                <span class="sp-icon-bubble shrink-0" style="background:{{ $exam->source_bubble ?? 'var(--sp-peach)' }}">
                    <x-student.figma-icon :name="$exam->source_icon ?? 'icon-exams.svg'" box="size-5" />
                </span>
                <div class="min-w-0">
                    <h1 class="text-base lg:text-lg font-black text-[var(--sp-accent-text)] truncate m-0">{{ $exam->title }}</h1>
                    <p class="text-xs text-[var(--sp-muted)] truncate m-0">{{ $exam->source_label ?? '' }} · {{ $exam->course_label ?? ($exam->offlineCourse->title ?? $exam->course->title ?? '—') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2 lg:gap-3 flex-wrap">
                <div class="flex items-center gap-2 px-3 py-2 rounded-[16px] bg-[#f7f7f5]">
                    <x-student.figma-icon name="icon-calendar.svg" box="size-4" />
                    <div id="timer" class="text-lg font-black text-[var(--sp-accent-text)] tabular-nums">{{ sprintf('%02d:%02d', floor($attempt->remaining_time / 60), $attempt->remaining_time % 60) }}</div>
                </div>
                <div class="flex items-center gap-2 px-3 py-2 rounded-[16px] bg-[#f7f7f5]">
                    <x-student.figma-icon name="icon-messages.svg" box="size-4" />
                    <span id="progress-text" class="text-sm font-extrabold text-[var(--sp-accent-text)]">0 / {{ $questions->count() }}</span>
                </div>
                <button type="button" onclick="confirmSubmit()" class="sp-promo-btn !mt-0 !py-2.5 border-0 cursor-pointer">
                    <span class="hidden sm:inline">{{ __('student.exam_take_submit') }}</span>
                    <span class="sm:hidden">{{ __('student.exam_take_submit_short') }}</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Main content --}}
    <div class="flex" style="min-height: calc(100vh - 70px);">
        {{-- Question sidebar --}}
        <div class="hidden lg:flex w-64 xl:w-72 flex-col shrink-0 border-s border-black/5 bg-white">
            <div class="p-4 border-b border-black/5">
                <h3 class="font-extrabold text-[var(--sp-accent-text)] text-sm m-0">{{ __('student.exam_take_questions_nav') }}</h3>
                <div class="mt-2 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div id="sidebar-progress-bar" class="h-full rounded-full transition-all duration-500" style="width:0%;background:var(--sp-accent)"></div>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto p-3 space-y-1.5" id="questions-sidebar">
                @foreach($questions as $index => $examQuestion)
                    <button type="button" onclick="goToQuestion({{ $index }})"
                            id="question-nav-{{ $index }}"
                            class="exam-q-nav w-full text-start p-3 rounded-[16px] transition-all text-sm border bg-[#f7f7f5] hover:bg-[rgba(174,217,234,.15)] text-[var(--sp-muted)] border-transparent {{ $index == 0 ? 'is-active' : '' }}">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="exam-q-nav-num w-7 h-7 rounded-lg flex items-center justify-center text-xs font-black shrink-0 bg-white text-[var(--sp-muted)] {{ $index == 0 ? 'is-active' : '' }}">{{ $index + 1 }}</span>
                                <span class="truncate">{{ __('student.exam_take_question_n', ['n' => $index + 1]) }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="text-[10px] text-slate-400">{{ $examQuestion->marks }} ن</span>
                                <div class="w-3.5 h-3.5 rounded-full border-2 border-slate-300" id="question-status-{{ $index }}"></div>
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Questions area --}}
        <div class="flex-1 overflow-y-auto">
            {{-- Mobile question nav --}}
            <div class="lg:hidden p-3 border-b border-slate-200 bg-white overflow-x-auto">
                <div class="flex gap-2 min-w-max">
                    @foreach($questions as $index => $examQuestion)
                        <button type="button" onclick="goToQuestion({{ $index }})"
                                id="question-nav-mobile-{{ $index }}"
                                class="exam-q-mobile w-9 h-9 rounded-lg flex items-center justify-center text-xs font-black shrink-0 transition-all bg-[#f7f7f5] text-[var(--sp-muted)] {{ $index == 0 ? 'is-active' : '' }}">
                            {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="max-w-4xl mx-auto p-4 lg:p-8">
                @foreach($questions as $index => $examQuestion)
                    <div class="question-container {{ $index == 0 ? '' : 'hidden' }}" id="question-{{ $index }}">
                        <div class="bg-white rounded-[20px] border border-black/5 shadow-sm overflow-hidden">
                            <div class="px-5 lg:px-6 py-4 border-b border-black/5 bg-[#f7f7f5]/60">
                                <div class="flex items-center justify-between flex-wrap gap-3">
                                    <div class="flex items-center gap-3">
                                        <span class="w-10 h-10 rounded-[14px] flex items-center justify-center font-black" style="background:var(--sp-peach);color:var(--sp-accent-text)">{{ $index + 1 }}</span>
                                        <div>
                                            <h2 class="text-base lg:text-lg font-black text-[var(--sp-accent-text)] m-0">{{ __('student.exam_take_question_n', ['n' => $index + 1]) }}</h2>
                                            <div class="flex items-center gap-3 text-xs text-[var(--sp-muted)] mt-0.5">
                                                <span>{{ $examQuestion->marks }} {{ __('student.exam_take_points') }}</span>
                                                <span>{{ $examQuestion->question->type_text }}</span>
                                                @if($examQuestion->question->difficulty_level)
                                                    @php
                                                        $dc = match($examQuestion->question->difficulty_level) {
                                                            'easy' => 'bg-emerald-100 text-emerald-700',
                                                            'medium' => 'bg-amber-100 text-amber-700',
                                                            default => 'bg-red-100 text-red-700',
                                                        };
                                                    @endphp
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $dc }}">{{ $examQuestion->question->difficulty_text }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if($examQuestion->time_limit)
                                        <div class="text-center px-3 py-1.5 rounded-[14px]" style="background:var(--sp-amber-soft)">
                                            <div class="text-sm font-black text-[var(--sp-accent-text)]" id="question-timer-{{ $index }}">{{ gmdate('i:s', $examQuestion->time_limit) }}</div>
                                            <div class="text-[10px] text-[var(--sp-muted)]">{{ __('student.exam_take_question_time') }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Question body --}}
                            <div class="p-5 lg:p-6">
                                <div class="text-[var(--sp-text)] text-base lg:text-lg leading-relaxed mb-6">{{ $examQuestion->question->question }}</div>

                                @if($examQuestion->question->image_url)
                                    <div class="mb-5">
                                        <img src="{{ $examQuestion->question->secure_image_url }}"
                                             alt="صورة السؤال"
                                             class="max-w-full h-auto rounded-xl border border-slate-200 shadow-sm"
                                             style="max-height: 300px;">
                                    </div>
                                @endif

                                @if($examQuestion->question->audio_url)
                                    <div class="mb-5">
                                        <audio controls class="w-full rounded-lg">
                                            <source src="{{ $examQuestion->question->audio_url }}" type="audio/mpeg">
                                        </audio>
                                    </div>
                                @endif

                                @if($examQuestion->question->video_url)
                                    <div class="mb-5">
                                        <div class="bg-black rounded-xl overflow-hidden border border-slate-200" style="aspect-ratio: 16/9;">
                                            {!! \App\Helpers\VideoHelper::generateEmbedHtml($examQuestion->question->video_url, '100%', '100%') !!}
                                        </div>
                                    </div>
                                @endif

                                {{-- Answer options --}}
                                <div class="space-y-2.5" id="answer-options-{{ $index }}">
                                    @if($examQuestion->question->type == 'multiple_choice')
                                        @foreach($exam->randomize_options ? $examQuestion->question->shuffled_options : $examQuestion->question->options as $optionIndex => $option)
                                            <label class="flex items-center gap-3 p-4 rounded-[16px] border-2 border-black/5 hover:border-[var(--sp-accent)] hover:bg-[rgba(174,217,234,.15)] cursor-pointer transition-all group exam-option"
                                                   data-question="{{ $examQuestion->question->id }}" data-value="{{ $option }}">
                                                <input type="radio"
                                                       name="answer_{{ $examQuestion->question->id }}"
                                                       value="{{ $option }}"
                                                       class="w-5 h-5 text-sky-600 border-slate-300 focus:ring-sky-500 shrink-0"
                                                       onchange="saveAnswer({{ $examQuestion->question->id }}, '{{ addslashes($option) }}')">
                                                <span class="text-slate-700 group-hover:text-slate-900">{{ $option }}</span>
                                            </label>
                                        @endforeach

                                    @elseif($examQuestion->question->type == 'true_false')
                                        @foreach([['صح', 'fa-check-circle', 'emerald'], ['خطأ', 'fa-times-circle', 'red']] as $tf)
                                            <label class="flex items-center gap-3 p-4 rounded-[16px] border-2 border-black/5 hover:border-[var(--sp-accent)] hover:bg-[rgba(174,217,234,.15)] cursor-pointer transition-all group exam-option"
                                                   data-question="{{ $examQuestion->question->id }}" data-value="{{ $tf[0] }}">
                                                <input type="radio"
                                                       name="answer_{{ $examQuestion->question->id }}"
                                                       value="{{ $tf[0] }}"
                                                       class="w-5 h-5 text-sky-600 border-slate-300 focus:ring-sky-500 shrink-0"
                                                       onchange="saveAnswer({{ $examQuestion->question->id }}, '{{ $tf[0] }}')">
                                                <i class="fas {{ $tf[1] }} text-{{ $tf[2] }}-500"></i>
                                                <span class="text-slate-700 font-medium">{{ $tf[0] }}</span>
                                            </label>
                                        @endforeach

                                    @elseif($examQuestion->question->type == 'fill_blank')
                                        <input type="text"
                                               id="answer_{{ $examQuestion->question->id }}"
                                               placeholder="{{ __('student.exam_take_answer_placeholder') }}"
                                               class="w-full px-4 py-3 border-2 border-black/5 rounded-[16px] text-[var(--sp-text)] placeholder-[var(--sp-muted)] focus:ring-2 focus:ring-[var(--sp-accent)] focus:border-[var(--sp-accent)] transition-colors"
                                               onchange="saveAnswer({{ $examQuestion->question->id }}, this.value)">

                                    @elseif($examQuestion->question->type == 'short_answer' || $examQuestion->question->type == 'essay')
                                        <textarea id="answer_{{ $examQuestion->question->id }}"
                                                  rows="{{ $examQuestion->question->type == 'essay' ? 6 : 3 }}"
                                                  placeholder="اكتب إجابتك هنا..."
                                                  class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition-colors"
                                                  onchange="saveAnswer({{ $examQuestion->question->id }}, this.value)"></textarea>
                                    @endif
                                </div>
                            </div>

                            <div class="px-5 lg:px-6 py-4 border-t border-black/5 bg-[#f7f7f5]/40">
                                <div class="flex items-center justify-between gap-3">
                                    <button type="button" onclick="previousQuestion()" id="prev-btn"
                                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-[30px] font-extrabold text-sm transition-colors {{ $index == 0 ? 'bg-[#f7f7f5] text-[var(--sp-muted)] cursor-not-allowed' : 'bg-[#f7f7f5] hover:bg-[var(--sp-accent)] text-[var(--sp-accent-text)]' }}"
                                            {{ $index == 0 ? 'disabled' : '' }}>
                                        <x-student.figma-icon name="icon-chevron.svg" box="size-3.5" class="rtl:rotate-180" />
                                        {{ __('student.exam_take_prev') }}
                                    </button>
                                    <button type="button" onclick="nextQuestion()" id="next-btn" class="sp-promo-btn !mt-0 !py-2.5 border-0 cursor-pointer">
                                        {{ $index == $questions->count() - 1 ? __('student.exam_take_finish') : __('student.exam_take_next') }}
                                        <x-student.figma-icon name="icon-chevron.svg" box="size-3.5" class="opacity-80" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div id="submitModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(15,15,20,.45);backdrop-filter:blur(4px);">
        <div class="sp-card w-full max-w-md p-6" onclick="event.stopPropagation()">
            <div class="text-center">
                <span class="sp-icon-bubble mx-auto mb-4" style="background:var(--sp-peach)"><x-student.figma-icon name="icon-exams.svg" /></span>
                <h3 class="text-lg font-black text-[var(--sp-accent-text)] mb-2 m-0">{{ __('student.exam_take_confirm_submit_title') }}</h3>
                <p class="text-sm text-[var(--sp-muted)] mb-4 m-0">{{ __('student.exam_take_confirm_submit_body') }}</p>
                <div class="p-3 rounded-[16px] mb-5 text-sm" style="background:var(--sp-mint)">
                    <div class="flex justify-between"><span class="text-[var(--sp-muted)]">{{ __('student.exam_take_answered') }}</span><span class="font-extrabold"><span id="answered-count">0</span> / {{ $questions->count() }}</span></div>
                    <div class="flex justify-between mt-1.5"><span class="text-[var(--sp-muted)]">{{ __('student.exam_take_time_left') }}</span><span class="font-extrabold" id="submit-timer">--:--</span></div>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="submitExam()" class="sp-promo-btn !mt-0 flex-1 border-0 cursor-pointer">{{ __('student.exam_take_submit_btn') }}</button>
                    <button type="button" onclick="closeSubmitModal()" class="flex-1 px-5 py-3 rounded-[30px] font-extrabold bg-[#f7f7f5] text-[var(--sp-accent-text)] border-0 cursor-pointer">{{ __('student.exam_take_cancel') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div id="tabSwitchWarning" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(122,59,46,.92);backdrop-filter:blur(6px);">
        <div class="sp-card w-full max-w-md p-8 text-center">
            <span class="sp-icon-bubble mx-auto mb-4" style="background:#f9e4d7"><x-student.figma-icon name="icon-exams.svg" /></span>
            <h3 class="text-xl font-black text-[#7a3b2e] mb-3 m-0">{{ __('student.exam_take_warning_title') }}</h3>
            <p class="text-[var(--sp-muted)] mb-4 m-0">{{ __('student.exam_take_tab_switch_body') }}</p>
            <div id="warning-message" class="text-[#7a3b2e] font-extrabold mb-5"></div>
            <button onclick="acknowledgeWarning()" class="sp-promo-btn !mt-0 w-full border-0 cursor-pointer">{{ __('student.exam_take_warning_ack') }}</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
const examI18n = {
    backBlocked: @json(__('student.exam_take_back_blocked')),
    actionBlocked: @json(__('student.exam_take_action_blocked')),
    leaveConfirm: @json(__('student.exam_take_leave_confirm')),
    autoSubmit: @json(__('student.exam_take_auto_submit')),
    prev: @json(__('student.exam_take_prev')),
    next: @json(__('student.exam_take_next')),
    finish: @json(__('student.exam_take_finish')),
};
let currentQuestion = 0;
let totalQuestions = {{ $questions->count() }};
let examId = {{ $exam->id }};
let attemptId = {{ $attempt->id }};
let timeRemaining = {{ $attempt->remaining_time }};
let answers = {};
let timerInterval;
let tabSwitchCount = 0;
let examEnded = false;

document.addEventListener('DOMContentLoaded', function() {
    setupExamProtection();
    startTimer();
    loadSavedAnswers();

    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        if (!examEnded) {
            history.go(1);
            showTabSwitchWarning(examI18n.backBlocked);
        }
    };

    // Highlight selected option visually
    document.querySelectorAll('.exam-option').forEach(function(label) {
        var radio = label.querySelector('input[type="radio"]');
        if (radio) {
            radio.addEventListener('change', function() {
                label.closest('.space-y-2\\.5, .space-y-2')?.querySelectorAll('.exam-option').forEach(function(l) {
                    l.classList.remove('is-selected');
                });
                label.classList.add('is-selected');
            });
        }
    });
});

function setupExamProtection() {
    document.addEventListener('keydown', function(e) {
        if (e.key === 'PrintScreen' || e.key === 'F12' ||
            (e.ctrlKey && e.shiftKey && e.key === 'I') ||
            (e.ctrlKey && e.key === 'u') ||
            (e.ctrlKey && e.key === 's')) {
            e.preventDefault();
            showTabSwitchWarning(examI18n.actionBlocked);
            return false;
        }
    });

    document.addEventListener('visibilitychange', function() {
        if (document.hidden && !examEnded) logTabSwitch();
    });

    window.addEventListener('blur', function() {
        if (!examEnded) logTabSwitch();
    });

    window.addEventListener('beforeunload', function(e) {
        if (!examEnded) {
            e.preventDefault();
            e.returnValue = examI18n.leaveConfirm;
            return e.returnValue;
        }
    });
}

function startTimer() {
    updateTimerDisplay();
    timerInterval = setInterval(function() {
        timeRemaining--;
        updateTimerDisplay();
        if (timeRemaining <= 0) autoSubmitExam();
    }, 1000);
}

function updateTimerDisplay() {
    var minutes = Math.floor(timeRemaining / 60);
    var seconds = timeRemaining % 60;
    var timerText = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
    document.getElementById('timer').textContent = timerText;
    var submitTimer = document.getElementById('submit-timer');
    if (submitTimer) submitTimer.textContent = timerText;

    var timer = document.getElementById('timer');
    if (timeRemaining <= 300) {
        timer.className = 'text-lg font-bold text-red-600 tabular-nums animate-pulse';
    } else if (timeRemaining <= 600) {
        timer.className = 'text-lg font-bold text-amber-600 tabular-nums';
    }
}

function goToQuestion(index) {
    document.getElementById('question-' + currentQuestion).classList.add('hidden');
    document.querySelectorAll('.exam-q-nav').forEach(el => el.classList.remove('is-active'));
    document.querySelectorAll('.exam-q-nav-num').forEach(el => el.classList.remove('is-active'));
    document.querySelectorAll('.exam-q-mobile').forEach(el => el.classList.remove('is-active'));
    document.getElementById('question-nav-' + index)?.classList.add('is-active');
    document.getElementById('question-nav-' + index)?.querySelector('.exam-q-nav-num')?.classList.add('is-active');
    document.getElementById('question-nav-mobile-' + index)?.classList.add('is-active');

    currentQuestion = index;
    document.getElementById('question-' + currentQuestion).classList.remove('hidden');

    var prevBtn = document.getElementById('prev-btn');
    if (prevBtn) {
        prevBtn.disabled = (currentQuestion === 0);
        prevBtn.className = currentQuestion === 0
            ? 'inline-flex items-center gap-2 px-4 py-2.5 rounded-[30px] font-extrabold text-sm bg-[#f7f7f5] text-[var(--sp-muted)] cursor-not-allowed'
            : 'inline-flex items-center gap-2 px-4 py-2.5 rounded-[30px] font-extrabold text-sm bg-[#f7f7f5] hover:bg-[var(--sp-accent)] text-[var(--sp-accent-text)]';
    }

    var nextBtn = document.getElementById('next-btn');
    if (nextBtn) nextBtn.textContent = currentQuestion === totalQuestions - 1 ? examI18n.finish : examI18n.next;

    var newMobile = document.getElementById('question-nav-mobile-' + index);
    if (newMobile) newMobile.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
}

function nextQuestion() {
    if (currentQuestion < totalQuestions - 1) goToQuestion(currentQuestion + 1);
    else confirmSubmit();
}

function previousQuestion() {
    if (currentQuestion > 0) goToQuestion(currentQuestion - 1);
}

function saveAnswer(questionId, answer) {
    answers[questionId] = answer;

    var statusEl = document.getElementById('question-status-' + currentQuestion);
    if (statusEl) statusEl.className = 'w-3.5 h-3.5 rounded-full bg-emerald-500 ring-2 ring-emerald-200';

    fetch('{{ route("student.exams.save-answer", [$exam, $attempt]) }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ question_id: questionId, answer: answer })
    }).catch(function(err) { console.error('Error saving answer:', err); });

    updateProgress();
}

function updateProgress() {
    var count = Object.keys(answers).length;
    document.getElementById('progress-text').textContent = count + ' / ' + totalQuestions;
    var answeredCountEl = document.getElementById('answered-count');
    if (answeredCountEl) answeredCountEl.textContent = count;
    var bar = document.getElementById('sidebar-progress-bar');
    if (bar) bar.style.width = Math.round((count / totalQuestions) * 100) + '%';
}

function loadSavedAnswers() {
    @if($attempt->answers)
        var savedAnswers = @json($attempt->answers);
        for (var questionId in savedAnswers) {
            answers[questionId] = savedAnswers[questionId];
            var answerInput = document.querySelector('[name="answer_' + questionId + '"][value="' + savedAnswers[questionId] + '"]') ||
                             document.getElementById('answer_' + questionId);
            if (answerInput) {
                if (answerInput.type === 'radio') {
                    answerInput.checked = true;
                    var label = answerInput.closest('.exam-option');
                    if (label) label.classList.add('is-selected');
                } else {
                    answerInput.value = savedAnswers[questionId];
                }
            }
        }
        // Mark answered questions in sidebar
        var qIndex = 0;
        @foreach($questions as $idx => $eq)
            if (savedAnswers[{{ $eq->question->id }}]) {
                var s = document.getElementById('question-status-{{ $idx }}');
                if (s) s.className = 'w-3.5 h-3.5 rounded-full bg-emerald-500 ring-2 ring-emerald-200';
            }
        @endforeach
        updateProgress();
    @endif
}

function logTabSwitch() {
    tabSwitchCount++;
    fetch('{{ route("student.exams.tab-switch", [$exam, $attempt]) }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(function(r) { return r.json(); }).then(function(data) {
        if (data.exam_ended) {
            examEnded = true;
            clearInterval(timerInterval);
            alert(data.message);
            window.location.href = '{{ route("student.exams.index") }}';
        } else if (data.warning) {
            showTabSwitchWarning(data.message);
        }
    }).catch(function(err) { console.error('Error logging tab switch:', err); });
}

function showTabSwitchWarning(message) {
    document.getElementById('warning-message').textContent = message;
    document.getElementById('tabSwitchWarning').classList.remove('hidden');
}

function acknowledgeWarning() {
    document.getElementById('tabSwitchWarning').classList.add('hidden');
}

function confirmSubmit() {
    updateProgress();
    document.getElementById('submitModal').classList.remove('hidden');
}

function closeSubmitModal() {
    document.getElementById('submitModal').classList.add('hidden');
}

function submitExam() {
    examEnded = true;
    clearInterval(timerInterval);
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("student.exams.submit", [$exam, $attempt]) }}';
    var csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    document.body.appendChild(form);
    form.submit();
}

function autoSubmitExam() {
    examEnded = true;
    clearInterval(timerInterval);
    alert(examI18n.autoSubmit);
    fetch('{{ route("student.exams.submit", [$exam, $attempt]) }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(function(r) {
        if (r.ok) window.location.href = '{{ route("student.exams.index") }}';
    }).catch(function() {
        window.location.href = '{{ route("student.exams.index") }}';
    });
}
</script>

<style>
* { -webkit-user-select: none !important; user-select: none !important; }
input, textarea { -webkit-user-select: text !important; user-select: text !important; }
@media print { body { display: none !important; } }
.exam-q-nav.is-active { background: rgba(174,217,234,.25); border-color: var(--sp-accent); color: var(--sp-accent-text); font-weight: 800; }
.exam-q-nav-num.is-active { background: var(--sp-accent); color: var(--sp-accent-text); }
.exam-q-mobile.is-active { background: var(--sp-accent); color: var(--sp-accent-text); }
.exam-option.is-selected { border-color: var(--sp-accent) !important; background: rgba(174,217,234,.15) !important; }
</style>
@endpush
@endsection
