<?php $__env->startSection('title', $exam->title); ?>
<?php $__env->startSection('header', ''); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-slate-950 text-white">
    <!-- شريط التحكم العلوي -->
    <div class="bg-slate-900/95 backdrop-blur px-6 py-4 flex items-center justify-between border-b border-slate-700 shadow-lg">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-sky-600 rounded-xl flex items-center justify-center shrink-0">
                <i class="fas fa-clipboard-check text-white"></i>
            </div>
            <div>
                <h1 class="text-lg font-semibold"><?php echo e($exam->title); ?></h1>
                <p class="text-sm text-slate-400"><?php echo e($exam->offlineCourse->title ?? $exam->course->title ?? '—'); ?> <?php if($exam->offline_course_id): ?><span class="text-amber-400">(أوفلاين)</span><?php endif; ?></p>
            </div>
        </div>
        
        <div class="flex items-center gap-6">
            <!-- العداد التنازلي -->
            <div class="text-center px-4 py-2 rounded-xl bg-slate-800/80 border border-slate-600">
                <div id="timer" class="text-2xl font-bold text-amber-400 tabular-nums"><?php echo e(sprintf('%02d:%02d', floor($attempt->remaining_time / 60), $attempt->remaining_time % 60)); ?></div>
                <div class="text-xs text-slate-400">الوقت المتبقي</div>
            </div>
            
            <!-- التقدم -->
            <div class="text-center px-4 py-2 rounded-xl bg-slate-800/80 border border-slate-600">
                <div id="progress-text" class="text-lg font-semibold text-sky-400">0 / <?php echo e($questions->count()); ?></div>
                <div class="text-xs text-slate-400">الأسئلة المجابة</div>
            </div>

            <!-- زر التسليم -->
            <button type="button" onclick="confirmSubmit()" 
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors shadow-lg">
                <i class="fas fa-check ml-2"></i>
                تسليم الامتحان
            </button>
        </div>
    </div>

    <!-- محتوى الامتحان -->
    <div class="flex h-[calc(100vh-4.5rem)]">
        <!-- قائمة الأسئلة الجانبية -->
        <div class="w-64 bg-slate-900/50 border-l border-slate-700 overflow-y-auto flex flex-col shrink-0">
            <div class="p-4 border-b border-slate-700">
                <h3 class="font-semibold text-slate-200">قائمة الأسئلة</h3>
            </div>
            <div class="p-3 space-y-2">
                <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $examQuestion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button type="button" onclick="goToQuestion(<?php echo e($index); ?>)" 
                            id="question-nav-<?php echo e($index); ?>"
                            class="w-full text-right p-3 rounded-xl transition-all question-nav-btn
                                   <?php echo e($index == 0 ? 'bg-sky-600 text-white ring-2 ring-sky-400' : 'bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-600'); ?>">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium">السؤال <?php echo e($index + 1); ?></span>
                            <div class="w-4 h-4 rounded-full border-2 border-slate-400" id="question-status-<?php echo e($index); ?>"></div>
                        </div>
                        <div class="text-xs text-slate-400 mt-0.5"><?php echo e($examQuestion->marks); ?> نقطة</div>
                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <!-- منطقة الأسئلة -->
        <div class="flex-1 overflow-y-auto bg-slate-950">
            <div class="max-w-4xl mx-auto p-6 lg:p-8">
                <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $examQuestion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="question-container <?php echo e($index == 0 ? '' : 'hidden'); ?>" id="question-<?php echo e($index); ?>">
                        <div class="bg-slate-800/80 rounded-2xl p-6 lg:p-8 border border-slate-700 shadow-xl">
                            <!-- رأس السؤال -->
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h2 class="text-xl font-bold text-white">السؤال <?php echo e($index + 1); ?></h2>
                                    <div class="flex items-center gap-4 text-sm text-slate-400 mt-1">
                                        <span><?php echo e($examQuestion->marks); ?> نقطة</span>
                                        <span><?php echo e($examQuestion->question->type_text); ?></span>
                                        <?php if($examQuestion->question->difficulty_level): ?>
                                            <?php
                                                $difficultyClass = 'bg-red-900 text-red-300';
                                                if ($examQuestion->question->difficulty_level == 'easy') {
                                                    $difficultyClass = 'bg-green-900 text-green-300';
                                                } elseif ($examQuestion->question->difficulty_level == 'medium') {
                                                    $difficultyClass = 'bg-yellow-900 text-yellow-300';
                                                }
                                            ?>
                                            <span class="px-2 py-1 rounded text-xs <?php echo e($difficultyClass); ?>">
                                                <?php echo e($examQuestion->question->difficulty_text); ?>

                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if($examQuestion->time_limit): ?>
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-yellow-400" id="question-timer-<?php echo e($index); ?>"><?php echo e(gmdate('i:s', $examQuestion->time_limit)); ?></div>
                                        <div class="text-xs text-slate-400">وقت السؤال</div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- نص السؤال -->
                            <div class="mb-6">
                                <div class="text-lg text-white leading-relaxed"><?php echo e($examQuestion->question->question); ?></div>
                                
                                <!-- الوسائط -->
                                <?php if($examQuestion->question->image_url): ?>
                                    <div class="mt-4">
                                        <img src="<?php echo e($examQuestion->question->secure_image_url); ?>" 
                                             alt="صورة السؤال" 
                                             class="max-w-full h-auto rounded-xl border border-slate-600"
                                             style="max-height: 300px;">
                                    </div>
                                <?php endif; ?>

                                <?php if($examQuestion->question->audio_url): ?>
                                    <div class="mt-4">
                                        <audio controls class="w-full">
                                            <source src="<?php echo e($examQuestion->question->audio_url); ?>" type="audio/mpeg">
                                            متصفحك لا يدعم تشغيل الصوت.
                                        </audio>
                                    </div>
                                <?php endif; ?>

                                <?php if($examQuestion->question->video_url): ?>
                                    <div class="mt-4">
                                        <div class="bg-black rounded-lg overflow-hidden" style="aspect-ratio: 16/9;">
                                            <?php echo \App\Helpers\VideoHelper::generateEmbedHtml($examQuestion->question->video_url, '100%', '100%'); ?>

                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- خيارات الإجابة -->
                            <div class="space-y-3" id="answer-options-<?php echo e($index); ?>">
                                <?php if($examQuestion->question->type == 'multiple_choice'): ?>
                                    <?php $__currentLoopData = $exam->randomize_options ? $examQuestion->question->shuffled_options : $examQuestion->question->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optionIndex => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="flex items-center p-4 bg-slate-700 hover:bg-slate-600 rounded-xl cursor-pointer transition-colors">
                                            <input type="radio" 
                                                   name="answer_<?php echo e($examQuestion->question->id); ?>" 
                                                   value="<?php echo e($option); ?>"
                                                   class="w-5 h-5 text-sky-600 bg-slate-600 border-slate-500 focus:ring-sky-500"
                                                   onchange="saveAnswer(<?php echo e($examQuestion->question->id); ?>, '<?php echo e($option); ?>')">
                                            <span class="mr-3 text-white"><?php echo e($option); ?></span>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <?php elseif($examQuestion->question->type == 'true_false'): ?>
                                    <label class="flex items-center p-4 bg-slate-700 hover:bg-slate-600 rounded-xl cursor-pointer transition-colors">
                                        <input type="radio" 
                                               name="answer_<?php echo e($examQuestion->question->id); ?>" 
                                               value="صح"
                                               class="w-5 h-5 text-sky-600 bg-slate-600 border-slate-500 focus:ring-sky-500"
                                               onchange="saveAnswer(<?php echo e($examQuestion->question->id); ?>, 'صح')">
                                        <span class="mr-3 text-white">صح</span>
                                    </label>
                                    <label class="flex items-center p-4 bg-slate-700 hover:bg-slate-600 rounded-xl cursor-pointer transition-colors">
                                        <input type="radio" 
                                               name="answer_<?php echo e($examQuestion->question->id); ?>" 
                                               value="خطأ"
                                               class="w-5 h-5 text-sky-600 bg-slate-600 border-slate-500 focus:ring-sky-500"
                                               onchange="saveAnswer(<?php echo e($examQuestion->question->id); ?>, 'خطأ')">
                                        <span class="mr-3 text-white">خطأ</span>
                                    </label>

                                <?php elseif($examQuestion->question->type == 'fill_blank'): ?>
                                    <input type="text" 
                                           id="answer_<?php echo e($examQuestion->question->id); ?>"
                                           placeholder="اكتب إجابتك هنا..."
                                           class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-xl text-white placeholder-slate-400 focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                                           onchange="saveAnswer(<?php echo e($examQuestion->question->id); ?>, this.value)">

                                <?php elseif($examQuestion->question->type == 'short_answer' || $examQuestion->question->type == 'essay'): ?>
                                    <textarea id="answer_<?php echo e($examQuestion->question->id); ?>"
                                              rows="<?php echo e($examQuestion->question->type == 'essay' ? 6 : 3); ?>"
                                              placeholder="اكتب إجابتك هنا..."
                                              class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-xl text-white placeholder-slate-400 focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                                              onchange="saveAnswer(<?php echo e($examQuestion->question->id); ?>, this.value)"></textarea>
                                <?php endif; ?>
                            </div>

                            <!-- أزرار التنقل -->
                            <div class="flex items-center justify-between mt-8 pt-6 border-t border-slate-600">
                                <button type="button" onclick="previousQuestion()" 
                                        id="prev-btn"
                                        class="px-6 py-2.5 bg-slate-600 hover:bg-slate-500 text-white rounded-xl font-medium transition-colors <?php echo e($index == 0 ? 'opacity-50 cursor-not-allowed' : ''); ?>"
                                        <?php echo e($index == 0 ? 'disabled' : ''); ?>>
                                    <i class="fas fa-arrow-right ml-2"></i>
                                    السابق
                                </button>

                                <div class="text-center min-w-[120px]">
                                    <div class="w-full bg-slate-700 rounded-full h-2 mb-1.5">
                                        <div class="bg-sky-600 h-2 rounded-full transition-all duration-300" 
                                             style="width: <?php echo e((($index + 1) / $questions->count()) * 100); ?>%"></div>
                                    </div>
                                    <span class="text-sm text-slate-400"><?php echo e($index + 1); ?> من <?php echo e($questions->count()); ?></span>
                                </div>

                                <button type="button" onclick="nextQuestion()" 
                                        id="next-btn"
                                        class="px-6 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-medium transition-colors">
                                    التالي
                                    <i class="fas fa-arrow-left mr-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <!-- نافذة تأكيد التسليم -->
    <div id="submitModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
        <div class="bg-slate-800 border border-slate-600 rounded-2xl shadow-2xl w-full max-w-md p-6" onclick="event.stopPropagation()">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-900">
                    <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-white mt-4">تأكيد تسليم الامتحان</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-slate-300">
                        هل أنت متأكد من تسليم الامتحان؟ لن تتمكن من تعديل إجاباتك بعد التسليم.
                    </p>
                    <div class="mt-4 p-3 bg-blue-900 rounded border border-blue-700">
                        <div class="text-sm text-blue-200">
                            <div>الأسئلة المجابة: <span id="answered-count">0</span> من <?php echo e($questions->count()); ?></div>
                            <div>الوقت المتبقي: <span id="submit-timer">--:--</span></div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3 justify-center pt-2">
                    <button type="button" onclick="submitExam()" 
                            class="px-5 py-2.5 bg-emerald-600 text-white font-semibold rounded-xl hover:bg-emerald-700 transition-colors">
                        تسليم
                    </button>
                    <button type="button" onclick="closeSubmitModal()" 
                            class="px-5 py-2.5 bg-slate-600 text-white font-semibold rounded-xl hover:bg-slate-500 transition-colors">
                        إلغاء
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- تحذير تبديل التبويب -->
    <div id="tabSwitchWarning" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-red-900/95">
        <div class="bg-slate-900 rounded-2xl border border-red-500 p-8 max-w-md w-full text-center shadow-2xl">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-600 mb-4">
                <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-4">تحذير!</h3>
            <p class="text-red-100 mb-6">
                تم رصد تبديل التبويب. هذا مخالف لقواعد الامتحان.
            </p>
            <div id="warning-message" class="text-yellow-300 font-medium mb-6"></div>
            <button onclick="acknowledgeWarning()" 
                    class="bg-white text-red-600 px-6 py-3 rounded-xl font-bold hover:bg-slate-100 transition-colors">
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

// تهيئة الامتحان
document.addEventListener('DOMContentLoaded', function() {
    setupExamProtection();
    startTimer();
    loadSavedAnswers();
    
    // منع العودة للخلف
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        if (!examEnded) {
            history.go(1);
            showTabSwitchWarning('محاولة العودة للخلف ممنوعة أثناء الامتحان');
        }
    };
});

// إعداد حماية الامتحان
function setupExamProtection() {
    // تم إزالة منع النقر بالزر الأيمن

    // منع اختصارات لوحة المفاتيح
    document.addEventListener('keydown', function(e) {
        // منع Print Screen وF12 وCtrl+Shift+I
        if (e.key === 'PrintScreen' || e.key === 'F12' || 
            (e.ctrlKey && e.shiftKey && e.key === 'I') ||
            (e.ctrlKey && e.key === 'u') ||
            (e.ctrlKey && e.key === 's')) {
            e.preventDefault();
            showTabSwitchWarning('هذا الإجراء ممنوع أثناء الامتحان');
            return false;
        }
    });

    // مراقبة تغيير النافذة
    document.addEventListener('visibilitychange', function() {
        if (document.hidden && !examEnded) {
            logTabSwitch();
        }
    });

    window.addEventListener('blur', function() {
        if (!examEnded) {
            logTabSwitch();
        }
    });

    // منع إغلاق النافذة
    window.addEventListener('beforeunload', function(e) {
        if (!examEnded) {
            e.preventDefault();
            e.returnValue = 'هل تريد مغادرة الامتحان؟ سيتم تسليم إجاباتك الحالية.';
            return e.returnValue;
        }
    });
}

// بدء العداد التنازلي
function startTimer() {
    updateTimerDisplay();
    
    timerInterval = setInterval(function() {
        timeRemaining--;
        updateTimerDisplay();
        
        if (timeRemaining <= 0) {
            autoSubmitExam();
        }
    }, 1000);
}

function updateTimerDisplay() {
    const minutes = Math.floor(timeRemaining / 60);
    const seconds = timeRemaining % 60;
    const timerText = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    
    document.getElementById('timer').textContent = timerText;
    document.getElementById('submit-timer').textContent = timerText;
    
    // تغيير لون العداد عند اقتراب انتهاء الوقت
    const timer = document.getElementById('timer');
    if (timeRemaining <= 300) { // 5 دقائق
        timer.className = 'text-2xl font-bold text-red-400';
    } else if (timeRemaining <= 600) { // 10 دقائق
        timer.className = 'text-2xl font-bold text-yellow-400';
    }
}

// الانتقال بين الأسئلة
function goToQuestion(index) {
    // إخفاء السؤال الحالي
    document.getElementById(`question-${currentQuestion}`).classList.add('hidden');
    document.getElementById(`question-nav-${currentQuestion}`).classList.remove('bg-sky-600', 'text-white', 'ring-2', 'ring-sky-400');
    document.getElementById(`question-nav-${currentQuestion}`).classList.add('bg-slate-800', 'text-slate-300', 'border', 'border-slate-600');
    
    // إظهار السؤال الجديد
    currentQuestion = index;
    document.getElementById(`question-${currentQuestion}`).classList.remove('hidden');
    document.getElementById(`question-nav-${currentQuestion}`).classList.remove('bg-slate-800', 'bg-slate-700', 'text-slate-300', 'border');
    document.getElementById(`question-nav-${currentQuestion}`).classList.add('bg-sky-600', 'text-white', 'ring-2', 'ring-sky-400');
    
    // تحديث أزرار التنقل
    document.getElementById('prev-btn').disabled = (currentQuestion === 0);
    document.getElementById('prev-btn').className = currentQuestion === 0 ? 
        'px-6 py-2 bg-slate-600 text-white rounded-xl font-medium opacity-50 cursor-not-allowed' :
        'px-6 py-2 bg-slate-600 hover:bg-slate-500 text-white rounded-xl font-medium transition-colors';
        
    document.getElementById('next-btn').textContent = currentQuestion === totalQuestions - 1 ? 'إنهاء' : 'التالي';
}

function nextQuestion() {
    if (currentQuestion < totalQuestions - 1) {
        goToQuestion(currentQuestion + 1);
    } else {
        confirmSubmit();
    }
}

function previousQuestion() {
    if (currentQuestion > 0) {
        goToQuestion(currentQuestion - 1);
    }
}

// حفظ الإجابة
function saveAnswer(questionId, answer) {
    answers[questionId] = answer;
    
    // تحديث حالة السؤال في القائمة الجانبية
    const statusIndicator = document.getElementById(`question-status-${currentQuestion}`);
    statusIndicator.className = 'w-4 h-4 rounded-full bg-emerald-500';
    
    // إرسال الإجابة للخادم
    fetch(`<?php echo e(route('student.exams.save-answer', [$exam, $attempt])); ?>`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify({
            question_id: questionId,
            answer: answer
        })
    }).catch(error => {
        console.error('Error saving answer:', error);
    });
    
    updateProgress();
}

function updateProgress() {
    const answeredCount = Object.keys(answers).length;
    document.getElementById('progress-text').textContent = `${answeredCount} / ${totalQuestions}`;
    document.getElementById('answered-count').textContent = answeredCount;
}

function loadSavedAnswers() {
    // تحميل الإجابات المحفوظة من المحاولة
    <?php if($attempt->answers): ?>
        const savedAnswers = <?php echo json_encode($attempt->answers, 15, 512) ?>;
        for (let questionId in savedAnswers) {
            answers[questionId] = savedAnswers[questionId];
            
            // تحديث واجهة المستخدم
            const answerInput = document.querySelector(`[name="answer_${questionId}"][value="${savedAnswers[questionId]}"]`) ||
                               document.getElementById(`answer_${questionId}`);
            
            if (answerInput) {
                if (answerInput.type === 'radio') {
                    answerInput.checked = true;
                } else {
                    answerInput.value = savedAnswers[questionId];
                }
            }
        }
        updateProgress();
    <?php endif; ?>
}

// تسجيل تبديل التبويب
function logTabSwitch() {
    tabSwitchCount++;
    
    fetch(`<?php echo e(route('student.exams.tab-switch', [$exam, $attempt])); ?>`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.exam_ended) {
            examEnded = true;
            clearInterval(timerInterval);
            alert(data.message);
            window.location.href = '<?php echo e(route("student.exams.index")); ?>';
        } else if (data.warning) {
            showTabSwitchWarning(data.message);
        }
    })
    .catch(error => {
        console.error('Error logging tab switch:', error);
    });
}

function showTabSwitchWarning(message) {
    document.getElementById('warning-message').textContent = message;
    document.getElementById('tabSwitchWarning').classList.remove('hidden');
}

function acknowledgeWarning() {
    document.getElementById('tabSwitchWarning').classList.add('hidden');
}

// تأكيد التسليم
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
    
    // إرسال نموذج التسليم
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo e(route("student.exams.submit", [$exam, $attempt])); ?>';
    
    const csrfToken = document.createElement('input');
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
    
    // تسليم تلقائي
    fetch(`<?php echo e(route('student.exams.submit', [$exam, $attempt])); ?>`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        }
    })
    .then(response => {
        if (response.ok) {
            window.location.href = '<?php echo e(route("student.exams.index")); ?>';
        }
    })
    .catch(error => {
        console.error('Error auto-submitting exam:', error);
        window.location.href = '<?php echo e(route("student.exams.index")); ?>';
    });
}

// منع التلاعب
Object.defineProperty(console, 'log', {
    value: function() {
        logTabSwitch();
    }
});
</script>

<style>
/* إخفاء شريط التمرير وحماية إضافية */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #374151;
}

::-webkit-scrollbar-thumb {
    background: #6b7280;
    border-radius: 3px;
}

/* منع التحديد */
* {
    -webkit-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
    user-select: none !important;
    -webkit-user-drag: none !important;
}

/* السماح بالتحديد في حقول الإدخال فقط */
input, textarea {
    -webkit-user-select: text !important;
    -moz-user-select: text !important;
    -ms-user-select: text !important;
    user-select: text !important;
}

/* منع الطباعة */
@media print {
    body { display: none !important; }
}
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/exams/take.blade.php ENDPATH**/ ?>