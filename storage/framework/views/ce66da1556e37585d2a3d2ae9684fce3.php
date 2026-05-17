<?php $__env->startSection('title', $exam->title); ?>
<?php $__env->startSection('header', ''); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-slate-50/80" dir="rtl">
    
    <div class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-200 shadow-sm px-4 lg:px-6 py-3">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 flex items-center justify-center shrink-0 shadow-lg shadow-sky-500/20">
                    <i class="fas fa-clipboard-check text-white text-sm"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-base lg:text-lg font-bold text-slate-800 truncate"><?php echo e($exam->title); ?></h1>
                    <p class="text-xs text-slate-500 truncate"><?php echo e($exam->offlineCourse->title ?? $exam->course->title ?? '—'); ?><?php if($exam->offline_course_id): ?> <span class="text-amber-600">(أوفلاين)</span><?php endif; ?></p>
                </div>
            </div>

            <div class="flex items-center gap-3 lg:gap-4 flex-wrap">
                
                <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-50 border border-slate-200">
                    <i class="fas fa-clock text-amber-500 text-sm"></i>
                    <div id="timer" class="text-lg font-bold text-slate-800 tabular-nums"><?php echo e(sprintf('%02d:%02d', floor($attempt->remaining_time / 60), $attempt->remaining_time % 60)); ?></div>
                </div>

                
                <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-50 border border-slate-200">
                    <i class="fas fa-tasks text-sky-500 text-sm"></i>
                    <span id="progress-text" class="text-sm font-semibold text-slate-700">0 / <?php echo e($questions->count()); ?></span>
                </div>

                
                <button type="button" onclick="confirmSubmit()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold text-sm transition-colors shadow-lg shadow-emerald-500/20">
                    <i class="fas fa-check"></i>
                    <span class="hidden sm:inline">تسليم الامتحان</span>
                    <span class="sm:hidden">تسليم</span>
                </button>
            </div>
        </div>
    </div>

    
    <div class="flex" style="min-height: calc(100vh - 70px);">
        
        <div class="hidden lg:flex w-64 xl:w-72 flex-col shrink-0 border-l border-slate-200 bg-white">
            <div class="p-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 text-sm">قائمة الأسئلة</h3>
                <div class="mt-2 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div id="sidebar-progress-bar" class="h-full bg-sky-500 rounded-full transition-all duration-500" style="width: 0%"></div>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto p-3 space-y-1.5" id="questions-sidebar">
                <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $examQuestion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button type="button" onclick="goToQuestion(<?php echo e($index); ?>)"
                            id="question-nav-<?php echo e($index); ?>"
                            class="w-full text-right p-3 rounded-xl transition-all text-sm
                                   <?php echo e($index == 0 ? 'bg-sky-50 text-sky-700 border-2 border-sky-300 font-semibold' : 'bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200'); ?>">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-7 h-7 rounded-lg <?php echo e($index == 0 ? 'bg-sky-500 text-white' : 'bg-slate-200 text-slate-600'); ?> flex items-center justify-center text-xs font-bold shrink-0"><?php echo e($index + 1); ?></span>
                                <span class="truncate">السؤال <?php echo e($index + 1); ?></span>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="text-[10px] text-slate-400"><?php echo e($examQuestion->marks); ?> ن</span>
                                <div class="w-3.5 h-3.5 rounded-full border-2 border-slate-300" id="question-status-<?php echo e($index); ?>"></div>
                            </div>
                        </div>
                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
        <div class="flex-1 overflow-y-auto">
            
            <div class="lg:hidden p-3 border-b border-slate-200 bg-white overflow-x-auto">
                <div class="flex gap-2 min-w-max">
                    <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $examQuestion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" onclick="goToQuestion(<?php echo e($index); ?>)"
                                id="question-nav-mobile-<?php echo e($index); ?>"
                                class="w-9 h-9 rounded-lg flex items-center justify-center text-xs font-bold shrink-0 transition-all
                                       <?php echo e($index == 0 ? 'bg-sky-500 text-white' : 'bg-slate-100 text-slate-600 border border-slate-200'); ?>">
                            <?php echo e($index + 1); ?>

                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <div class="max-w-4xl mx-auto p-4 lg:p-8">
                <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $examQuestion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="question-container <?php echo e($index == 0 ? '' : 'hidden'); ?>" id="question-<?php echo e($index); ?>">
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                            
                            <div class="px-5 lg:px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                                <div class="flex items-center justify-between flex-wrap gap-3">
                                    <div class="flex items-center gap-3">
                                        <span class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center font-bold"><?php echo e($index + 1); ?></span>
                                        <div>
                                            <h2 class="text-base lg:text-lg font-bold text-slate-800">السؤال <?php echo e($index + 1); ?></h2>
                                            <div class="flex items-center gap-3 text-xs text-slate-500 mt-0.5">
                                                <span><i class="fas fa-star ml-1 text-amber-400"></i><?php echo e($examQuestion->marks); ?> نقطة</span>
                                                <span><?php echo e($examQuestion->question->type_text); ?></span>
                                                <?php if($examQuestion->question->difficulty_level): ?>
                                                    <?php
                                                        $dc = match($examQuestion->question->difficulty_level) {
                                                            'easy' => 'bg-emerald-100 text-emerald-700',
                                                            'medium' => 'bg-amber-100 text-amber-700',
                                                            default => 'bg-red-100 text-red-700',
                                                        };
                                                    ?>
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold <?php echo e($dc); ?>"><?php echo e($examQuestion->question->difficulty_text); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if($examQuestion->time_limit): ?>
                                        <div class="text-center px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200">
                                            <div class="text-sm font-bold text-amber-600" id="question-timer-<?php echo e($index); ?>"><?php echo e(gmdate('i:s', $examQuestion->time_limit)); ?></div>
                                            <div class="text-[10px] text-amber-500">وقت السؤال</div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            
                            <div class="p-5 lg:p-6">
                                <div class="text-slate-800 text-base lg:text-lg leading-relaxed mb-6"><?php echo e($examQuestion->question->question); ?></div>

                                <?php if($examQuestion->question->image_url): ?>
                                    <div class="mb-5">
                                        <img src="<?php echo e($examQuestion->question->secure_image_url); ?>"
                                             alt="صورة السؤال"
                                             class="max-w-full h-auto rounded-xl border border-slate-200 shadow-sm"
                                             style="max-height: 300px;">
                                    </div>
                                <?php endif; ?>

                                <?php if($examQuestion->question->audio_url): ?>
                                    <div class="mb-5">
                                        <audio controls class="w-full rounded-lg">
                                            <source src="<?php echo e($examQuestion->question->audio_url); ?>" type="audio/mpeg">
                                        </audio>
                                    </div>
                                <?php endif; ?>

                                <?php if($examQuestion->question->video_url): ?>
                                    <div class="mb-5">
                                        <div class="bg-black rounded-xl overflow-hidden border border-slate-200" style="aspect-ratio: 16/9;">
                                            <?php echo \App\Helpers\VideoHelper::generateEmbedHtml($examQuestion->question->video_url, '100%', '100%'); ?>

                                        </div>
                                    </div>
                                <?php endif; ?>

                                
                                <div class="space-y-2.5" id="answer-options-<?php echo e($index); ?>">
                                    <?php if($examQuestion->question->type == 'multiple_choice'): ?>
                                        <?php $__currentLoopData = $exam->randomize_options ? $examQuestion->question->shuffled_options : $examQuestion->question->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optionIndex => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-slate-200 hover:border-sky-300 hover:bg-sky-50/30 cursor-pointer transition-all group exam-option"
                                                   data-question="<?php echo e($examQuestion->question->id); ?>" data-value="<?php echo e($option); ?>">
                                                <input type="radio"
                                                       name="answer_<?php echo e($examQuestion->question->id); ?>"
                                                       value="<?php echo e($option); ?>"
                                                       class="w-5 h-5 text-sky-600 border-slate-300 focus:ring-sky-500 shrink-0"
                                                       onchange="saveAnswer(<?php echo e($examQuestion->question->id); ?>, '<?php echo e(addslashes($option)); ?>')">
                                                <span class="text-slate-700 group-hover:text-slate-900"><?php echo e($option); ?></span>
                                            </label>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    <?php elseif($examQuestion->question->type == 'true_false'): ?>
                                        <?php $__currentLoopData = [['صح', 'fa-check-circle', 'emerald'], ['خطأ', 'fa-times-circle', 'red']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tf): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-slate-200 hover:border-sky-300 hover:bg-sky-50/30 cursor-pointer transition-all group exam-option"
                                                   data-question="<?php echo e($examQuestion->question->id); ?>" data-value="<?php echo e($tf[0]); ?>">
                                                <input type="radio"
                                                       name="answer_<?php echo e($examQuestion->question->id); ?>"
                                                       value="<?php echo e($tf[0]); ?>"
                                                       class="w-5 h-5 text-sky-600 border-slate-300 focus:ring-sky-500 shrink-0"
                                                       onchange="saveAnswer(<?php echo e($examQuestion->question->id); ?>, '<?php echo e($tf[0]); ?>')">
                                                <i class="fas <?php echo e($tf[1]); ?> text-<?php echo e($tf[2]); ?>-500"></i>
                                                <span class="text-slate-700 font-medium"><?php echo e($tf[0]); ?></span>
                                            </label>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    <?php elseif($examQuestion->question->type == 'fill_blank'): ?>
                                        <input type="text"
                                               id="answer_<?php echo e($examQuestion->question->id); ?>"
                                               placeholder="اكتب إجابتك هنا..."
                                               class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition-colors"
                                               onchange="saveAnswer(<?php echo e($examQuestion->question->id); ?>, this.value)">

                                    <?php elseif($examQuestion->question->type == 'short_answer' || $examQuestion->question->type == 'essay'): ?>
                                        <textarea id="answer_<?php echo e($examQuestion->question->id); ?>"
                                                  rows="<?php echo e($examQuestion->question->type == 'essay' ? 6 : 3); ?>"
                                                  placeholder="اكتب إجابتك هنا..."
                                                  class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition-colors"
                                                  onchange="saveAnswer(<?php echo e($examQuestion->question->id); ?>, this.value)"></textarea>
                                    <?php endif; ?>
                                </div>
                            </div>

                            
                            <div class="px-5 lg:px-6 py-4 border-t border-slate-100 bg-slate-50/30">
                                <div class="flex items-center justify-between gap-3">
                                    <button type="button" onclick="previousQuestion()" id="prev-btn"
                                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-medium text-sm transition-colors <?php echo e($index == 0 ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'); ?>"
                                            <?php echo e($index == 0 ? 'disabled' : ''); ?>>
                                        <i class="fas fa-arrow-right"></i>
                                        السابق
                                    </button>

                                    <div class="hidden sm:flex items-center gap-2 text-sm text-slate-500">
                                        <div class="w-24 h-2 bg-slate-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-sky-500 rounded-full transition-all duration-300"
                                                 style="width: <?php echo e((($index + 1) / $questions->count()) * 100); ?>%"></div>
                                        </div>
                                        <span class="tabular-nums"><?php echo e($index + 1); ?>/<?php echo e($questions->count()); ?></span>
                                    </div>

                                    <button type="button" onclick="nextQuestion()" id="next-btn"
                                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-semibold text-sm transition-colors shadow-sm">
                                        <?php echo e($index == $questions->count() - 1 ? 'إنهاء' : 'التالي'); ?>

                                        <i class="fas fa-arrow-left"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    
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
                        <span class="font-bold text-sky-700"><span id="answered-count">0</span> من <?php echo e($questions->count()); ?></span>
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

<?php $__env->startPush('scripts'); ?>
<script>
let currentQuestion = 0;
let totalQuestions = <?php echo e($questions->count()); ?>;
let examId = <?php echo e($exam->id); ?>;
let attemptId = <?php echo e($attempt->id); ?>;
let timeRemaining = <?php echo e($attempt->remaining_time); ?>;
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

    fetch('<?php echo e(route("student.exams.save-answer", [$exam, $attempt])); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
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
    <?php if($attempt->answers): ?>
        var savedAnswers = <?php echo json_encode($attempt->answers, 15, 512) ?>;
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
        <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $eq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            if (savedAnswers[<?php echo e($eq->question->id); ?>]) {
                var s = document.getElementById('question-status-<?php echo e($idx); ?>');
                if (s) s.className = 'w-3.5 h-3.5 rounded-full bg-emerald-500 ring-2 ring-emerald-200';
            }
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        updateProgress();
    <?php endif; ?>
}

function logTabSwitch() {
    tabSwitchCount++;
    fetch('<?php echo e(route("student.exams.tab-switch", [$exam, $attempt])); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
    }).then(function(r) { return r.json(); }).then(function(data) {
        if (data.exam_ended) {
            examEnded = true;
            clearInterval(timerInterval);
            alert(data.message);
            window.location.href = '<?php echo e(route("student.exams.index")); ?>';
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
    form.action = '<?php echo e(route("student.exams.submit", [$exam, $attempt])); ?>';
    var csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '<?php echo e(csrf_token()); ?>';
    form.appendChild(csrfToken);
    document.body.appendChild(form);
    form.submit();
}

function autoSubmitExam() {
    examEnded = true;
    clearInterval(timerInterval);
    alert('انتهى الوقت المحدد للامتحان. سيتم تسليم إجاباتك تلقائياً.');
    fetch('<?php echo e(route("student.exams.submit", [$exam, $attempt])); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
    }).then(function(r) {
        if (r.ok) window.location.href = '<?php echo e(route("student.exams.index")); ?>';
    }).catch(function() {
        window.location.href = '<?php echo e(route("student.exams.index")); ?>';
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/student/exams/take.blade.php ENDPATH**/ ?>