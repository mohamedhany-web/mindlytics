@extends('layouts.app')

@section('title', $exam->title)
@section('header', '')

@section('content')
<div class="min-h-screen bg-slate-50/80" dir="rtl">
    {{-- Top exam bar --}}
    <div class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-200 shadow-sm px-4 lg:px-6 py-3">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 flex items-center justify-center shrink-0 shadow-lg shadow-sky-500/20">
                    <i class="fas fa-clipboard-check text-white text-sm"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-base lg:text-lg font-bold text-slate-800 truncate">{{ $exam->title }}</h1>
                    <p class="text-xs text-slate-500 truncate">{{ $exam->offlineCourse->title ?? $exam->course->title ?? '—' }}@if($exam->offline_course_id) <span class="text-amber-600">(أوفلاين)</span>@endif</p>
                </div>
            </div>

            <div class="flex items-center gap-3 lg:gap-4 flex-wrap">
                {{-- Timer --}}
                <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-50 border border-slate-200">
                    <i class="fas fa-clock text-amber-500 text-sm"></i>
                    <div id="timer" class="text-lg font-bold text-slate-800 tabular-nums">{{ sprintf('%02d:%02d', floor($attempt->remaining_time / 60), $attempt->remaining_time % 60) }}</div>
                </div>

                {{-- Progress --}}
                <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-50 border border-slate-200">
                    <i class="fas fa-tasks text-sky-500 text-sm"></i>
                    <span id="progress-text" class="text-sm font-semibold text-slate-700">0 / {{ $questions->count() }}</span>
                </div>

                {{-- Submit button --}}
                <button type="button" onclick="confirmSubmit()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold text-sm transition-colors shadow-lg shadow-emerald-500/20">
                    <i class="fas fa-check"></i>
                    <span class="hidden sm:inline">تسليم الامتحان</span>
                    <span class="sm:hidden">تسليم</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Main content --}}
    <div class="flex" style="min-height: calc(100vh - 70px);">
        {{-- Question sidebar --}}
        <div class="hidden lg:flex w-64 xl:w-72 flex-col shrink-0 border-l border-slate-200 bg-white">
            <div class="p-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 text-sm">قائمة الأسئلة</h3>
                <div class="mt-2 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div id="sidebar-progress-bar" class="h-full bg-sky-500 rounded-full transition-all duration-500" style="width: 0%"></div>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto p-3 space-y-1.5" id="questions-sidebar">
                @foreach($questions as $index => $examQuestion)
                    <button type="button" onclick="goToQuestion({{ $index }})"
                            id="question-nav-{{ $index }}"
                            class="w-full text-right p-3 rounded-xl transition-all text-sm
                                   {{ $index == 0 ? 'bg-sky-50 text-sky-700 border-2 border-sky-300 font-semibold' : 'bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200' }}">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-7 h-7 rounded-lg {{ $index == 0 ? 'bg-sky-500 text-white' : 'bg-slate-200 text-slate-600' }} flex items-center justify-center text-xs font-bold shrink-0">{{ $index + 1 }}</span>
                                <span class="truncate">السؤال {{ $index + 1 }}</span>
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
                                class="w-9 h-9 rounded-lg flex items-center justify-center text-xs font-bold shrink-0 transition-all
                                       {{ $index == 0 ? 'bg-sky-500 text-white' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                            {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="max-w-4xl mx-auto p-4 lg:p-8">
                @foreach($questions as $index => $examQuestion)
                    <div class="question-container {{ $index == 0 ? '' : 'hidden' }}" id="question-{{ $index }}">
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                            {{-- Question header --}}
                            <div class="px-5 lg:px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                                <div class="flex items-center justify-between flex-wrap gap-3">
                                    <div class="flex items-center gap-3">
                                        <span class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center font-bold">{{ $index + 1 }}</span>
                                        <div>
                                            <h2 class="text-base lg:text-lg font-bold text-slate-800">السؤال {{ $index + 1 }}</h2>
                                            <div class="flex items-center gap-3 text-xs text-slate-500 mt-0.5">
                                                <span><i class="fas fa-star ml-1 text-amber-400"></i>{{ $examQuestion->marks }} نقطة</span>
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
                                        <div class="text-center px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200">
                                            <div class="text-sm font-bold text-amber-600" id="question-timer-{{ $index }}">{{ gmdate('i:s', $examQuestion->time_limit) }}</div>
                                            <div class="text-[10px] text-amber-500">وقت السؤال</div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Question body --}}
                            <div class="p-5 lg:p-6">
                                <div class="text-slate-800 text-base lg:text-lg leading-relaxed mb-6">{{ $examQuestion->question->question }}</div>

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
                                            <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-slate-200 hover:border-sky-300 hover:bg-sky-50/30 cursor-pointer transition-all group exam-option"
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
                                            <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-slate-200 hover:border-sky-300 hover:bg-sky-50/30 cursor-pointer transition-all group exam-option"
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
                                               placeholder="اكتب إجابتك هنا..."
                                               class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition-colors"
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

                            {{-- Navigation footer --}}
                            <div class="px-5 lg:px-6 py-4 border-t border-slate-100 bg-slate-50/30">
                                <div class="flex items-center justify-between gap-3">
                                    <button type="button" onclick="previousQuestion()" id="prev-btn"
                                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-medium text-sm transition-colors {{ $index == 0 ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}"
                                            {{ $index == 0 ? 'disabled' : '' }}>
                                        <i class="fas fa-arrow-right"></i>
                                        السابق
                                    </button>

                                    <div class="hidden sm:flex items-center gap-2 text-sm text-slate-500">
                                        <div class="w-24 h-2 bg-slate-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-sky-500 rounded-full transition-all duration-300"
                                                 style="width: {{ (($index + 1) / $questions->count()) * 100 }}%"></div>
                                        </div>
                                        <span class="tabular-nums">{{ $index + 1 }}/{{ $questions->count() }}</span>
                                    </div>

                                    <button type="button" onclick="nextQuestion()" id="next-btn"
                                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-semibold text-sm transition-colors shadow-sm">
                                        {{ $index == $questions->count() - 1 ? 'إنهاء' : 'التالي' }}
                                        <i class="fas fa-arrow-left"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Submit confirmation modal --}}
    <div id="submitModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-md p-6" onclick="event.stopPropagation()">
            <div class="text-center">
                <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">تأكيد تسليم الامتحان</h3>
                <p class="text-sm text-slate-500 mb-4">هل أنت متأكد من تسليم الامتحان؟ لن تتمكن من تعديل إجاباتك بعد التسليم.</p>
                <div class="p-3 bg-sky-50 rounded-xl border border-sky-100 mb-5">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600">الأسئلة المجابة</span>
                        <span class="font-bold text-sky-700"><span id="answered-count">0</span> من {{ $questions->count() }}</span>
                    </div>
                    <div class="flex justify-between text-sm mt-1.5">
                        <span class="text-slate-600">الوقت المتبقي</span>
                        <span class="font-bold text-amber-600" id="submit-timer">--:--</span>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="submitExam()" class="flex-1 px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-colors">
                        <i class="fas fa-check ml-1"></i> تسليم
                    </button>
                    <button type="button" onclick="closeSubmitModal()" class="flex-1 px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition-colors">
                        إلغاء
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab switch warning --}}
    <div id="tabSwitchWarning" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(185,28,28,0.92);backdrop-filter:blur(6px);">
        <div class="bg-white rounded-2xl border border-red-200 shadow-2xl w-full max-w-md p-8 text-center">
            <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-red-800 mb-3">تحذير!</h3>
            <p class="text-slate-600 mb-4">تم رصد تبديل التبويب. هذا مخالف لقواعد الامتحان.</p>
            <div id="warning-message" class="text-red-600 font-semibold mb-5"></div>
            <button onclick="acknowledgeWarning()" class="w-full px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-colors">
                فهمت، أعود للامتحان
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
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
            showTabSwitchWarning('محاولة العودة للخلف ممنوعة أثناء الامتحان');
        }
    };

    // Highlight selected option visually
    document.querySelectorAll('.exam-option').forEach(function(label) {
        var radio = label.querySelector('input[type="radio"]');
        if (radio) {
            radio.addEventListener('change', function() {
                label.closest('.space-y-2\\.5').querySelectorAll('.exam-option').forEach(function(l) {
                    l.classList.remove('border-sky-500', 'bg-sky-50/50');
                    l.classList.add('border-slate-200');
                });
                label.classList.remove('border-slate-200');
                label.classList.add('border-sky-500', 'bg-sky-50/50');
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
            showTabSwitchWarning('هذا الإجراء ممنوع أثناء الامتحان');
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
            e.returnValue = 'هل تريد مغادرة الامتحان؟ سيتم تسليم إجاباتك الحالية.';
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

    // Desktop sidebar
    var oldNav = document.getElementById('question-nav-' + currentQuestion);
    var newNav = document.getElementById('question-nav-' + index);
    if (oldNav) {
        oldNav.classList.remove('bg-sky-50', 'text-sky-700', 'border-sky-300', 'font-semibold');
        oldNav.classList.add('bg-slate-50', 'text-slate-600', 'border-slate-200');
        var oldNum = oldNav.querySelector('span:first-child');
        if (oldNum) { oldNum.classList.remove('bg-sky-500', 'text-white'); oldNum.classList.add('bg-slate-200', 'text-slate-600'); }
    }
    if (newNav) {
        newNav.classList.remove('bg-slate-50', 'text-slate-600', 'border-slate-200');
        newNav.classList.add('bg-sky-50', 'text-sky-700', 'border-sky-300', 'font-semibold');
        var newNum = newNav.querySelector('span:first-child');
        if (newNum) { newNum.classList.remove('bg-slate-200', 'text-slate-600'); newNum.classList.add('bg-sky-500', 'text-white'); }
    }

    // Mobile nav
    var oldMobile = document.getElementById('question-nav-mobile-' + currentQuestion);
    var newMobile = document.getElementById('question-nav-mobile-' + index);
    if (oldMobile) { oldMobile.classList.remove('bg-sky-500', 'text-white'); oldMobile.classList.add('bg-slate-100', 'text-slate-600', 'border', 'border-slate-200'); }
    if (newMobile) { newMobile.classList.remove('bg-slate-100', 'text-slate-600', 'border', 'border-slate-200'); newMobile.classList.add('bg-sky-500', 'text-white'); }

    currentQuestion = index;
    document.getElementById('question-' + currentQuestion).classList.remove('hidden');

    var prevBtn = document.getElementById('prev-btn');
    prevBtn.disabled = (currentQuestion === 0);
    prevBtn.className = currentQuestion === 0
        ? 'inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-medium text-sm bg-slate-100 text-slate-400 cursor-not-allowed'
        : 'inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-medium text-sm bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors';

    var nextBtn = document.getElementById('next-btn');
    nextBtn.innerHTML = currentQuestion === totalQuestions - 1
        ? 'إنهاء <i class="fas fa-arrow-left"></i>'
        : 'التالي <i class="fas fa-arrow-left"></i>';

    // Scroll mobile nav into view
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
                    if (label) { label.classList.remove('border-slate-200'); label.classList.add('border-sky-500', 'bg-sky-50/50'); }
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
    alert('انتهى الوقت المحدد للامتحان. سيتم تسليم إجاباتك تلقائياً.');
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
::-webkit-scrollbar { width: 5px; }
::-webkit-scrollbar-track { background: #f1f5f9; }
::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
</style>
@endpush
@endsection
