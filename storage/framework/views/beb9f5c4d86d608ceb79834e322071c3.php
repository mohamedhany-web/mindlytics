<?php $inProgressAttemptScript = $userAttempts->filter(fn($a) => in_array($a->status, ['in_progress', 'started']))->first(); ?>
<script>
function learningPattern() {
    return {
        currentAttempt: null,
        attemptData: {},
        timeRemaining: null,
        timerInterval: null,
        showFeedbackModal: false,
        feedbackContent: '',
        init() {
            <?php if($inProgressAttemptScript): ?>
                const inProgressAttempt = <?php echo json_encode($inProgressAttemptScript, 15, 512) ?>;
                this.currentAttempt = inProgressAttempt;
                this.attemptData = inProgressAttempt.attempt_data || {};
            <?php endif; ?>
        },
        async startAttempt() {
            try {
                const response = await fetch('<?php echo e(route("my-courses.learning-patterns.start", [$course, $pattern])); ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (data.success) {
                    this.currentAttempt = data.attempt;
                    this.attemptData = {};
                    window.currentAttemptId = data.attempt.id;
                    window.currentAttemptData = {};
                    if (data.time_limit) { this.timeRemaining = data.time_limit; this.startTimer(); }
                    window.location.reload();
                } else { alert(data.error || 'حدث خطأ أثناء بدء المحاولة'); }
            } catch (error) { console.error('Error:', error); alert('حدث خطأ أثناء بدء المحاولة'); }
        },
        async saveProgress() {
            if (!this.currentAttempt) return;
            try {
                const response = await fetch(`<?php echo e(route("my-courses.learning-patterns.save-progress", [$course, $pattern, ":attempt"])); ?>`.replace(':attempt', this.currentAttempt.id), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: JSON.stringify({ attempt_data: this.attemptData, time_spent_seconds: this.calculateTimeSpent() })
                });
                if ((await response.json()).success) console.log('Progress saved');
            } catch (error) { console.error('Error saving progress:', error); }
        },
        async completeAttempt() {
            if (!this.currentAttempt) return;
            if (!confirm('هل أنت متأكد من إكمال المحاولة؟')) return;
            try {
                const response = await fetch(`<?php echo e(route("my-courses.learning-patterns.complete", [$course, $pattern, ":attempt"])); ?>`.replace(':attempt', this.currentAttempt.id), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: JSON.stringify({ attempt_data: this.attemptData, time_spent_seconds: this.calculateTimeSpent() })
                });
                const data = await response.json();
                if (data.success) { alert('تم إكمال المحاولة بنجاح!'); window.location.reload(); }
                else { alert(data.error || 'حدث خطأ أثناء إكمال المحاولة'); }
            } catch (error) { console.error('Error:', error); alert('حدث خطأ أثناء إكمال المحاولة'); }
        },
        startTimer() {
            this.timerInterval = setInterval(() => {
                if (this.timeRemaining > 0) this.timeRemaining--;
                else { clearInterval(this.timerInterval); alert('انتهى الوقت!'); this.completeAttempt(); }
            }, 1000);
        },
        calculateTimeSpent() { return Math.floor((Date.now() - new Date(this.currentAttempt.started_at).getTime()) / 1000); },
        formatTime(seconds) { const mins = Math.floor(seconds / 60); const secs = seconds % 60; return `${mins}:${secs.toString().padStart(2, '0')}`; },
        showFeedback(attemptId) {
            <?php $__currentLoopData = $userAttempts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attempt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                if (<?php echo e($attempt->id); ?> === attemptId) { this.feedbackContent = this.formatFeedback(<?php echo json_encode($attempt->feedback, 15, 512) ?>); this.showFeedbackModal = true; return; }
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        },
        formatFeedback(feedback) {
            if (!feedback) return '<p>لا توجد تفاصيل متاحة</p>';
            if (typeof feedback === 'string') { try { feedback = JSON.parse(feedback); } catch (e) { return '<p>' + this.escapeHtml(feedback) + '</p>'; } }
            let html = '<div class="space-y-4">';
            if (feedback.total_questions) {
                html += `<div class="bg-blue-50 p-4 rounded-lg"><div class="font-bold text-blue-900 mb-2">النتيجة الإجمالية</div><div class="text-blue-800">الإجابات الصحيحة: ${feedback.correct_answers} / ${feedback.total_questions}<br>النسبة: ${feedback.score_percentage}%</div></div>`;
            }
            if (feedback.details && Array.isArray(feedback.details)) {
                feedback.details.forEach((detail) => {
                    html += `<div class="p-3 rounded-lg ${detail.correct ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'}"><div class="font-bold ${detail.correct ? 'text-green-900' : 'text-red-900'}">${detail.correct ? '✓ صحيح' : '✗ خاطئ'}</div><div class="text-sm mt-1">${this.escapeHtml(detail.message || '')}</div></div>`;
                });
            }
            html += '</div>';
            if (!feedback.total_questions && (!feedback.details || feedback.details.length === 0)) return '<p class="text-slate-600">لا توجد تفاصيل متاحة لهذه المحاولة.</p>';
            return html;
        },
        escapeHtml(text) { if (!text) return ''; const div = document.createElement('div'); div.textContent = text; return div.innerHTML; },
        loadInProgressAttempt(attemptId) {
            <?php if($inProgressAttemptScript): ?>
                const attempt = <?php echo json_encode($inProgressAttemptScript, 15, 512) ?>;
                this.currentAttempt = attempt;
                this.attemptData = attempt.attempt_data || {};
            <?php endif; ?>
        },
        getCurrentAttemptId() { return this.currentAttempt ? this.currentAttempt.id : null; }
    };
}
function patternTypeHandler(patternType) {
    return {
        attemptData: window.currentAttemptData || {},
        output: '',
        parentComponent: null,
        init() {
            this.parentComponent = this.$el.closest('[x-data*="learningPattern"]').__x.$data;
            if (window.currentAttemptData) this.attemptData = window.currentAttemptData;
            setInterval(() => { this.autoSave(); }, 30000);
        },
        async runCode() {
            const code = this.attemptData.code || '';
            if (!code.trim()) { alert('يرجى كتابة الكود أولاً'); return; }
            this.output = 'جاري التشغيل...';
            try { setTimeout(() => { this.output = 'تم تشغيل الكود بنجاح!\n(هذه محاكاة - في الإنتاج سيتم تشغيل الكود فعلياً)'; }, 1000); } catch (error) { this.output = 'حدث خطأ: ' + error.message; }
        },
        async submitSolution() {
            if (!this.attemptData.code || !this.attemptData.code.trim()) { alert('يرجى كتابة الكود أولاً'); return; }
            if (this.parentComponent) { this.parentComponent.attemptData = this.attemptData; await this.parentComponent.completeAttempt(); }
        },
        loadStarterCode() { <?php if(isset($pattern->pattern_data['starter_code'])): ?> this.attemptData.code = <?php echo json_encode($pattern->pattern_data['starter_code'], 15, 512) ?>; <?php endif; ?> },
        async submitQuiz() {
            const answers = this.attemptData.answers || {};
            if (Object.keys(answers).length === 0) { alert('يرجى الإجابة على جميع الأسئلة'); return; }
            if (this.parentComponent) { this.parentComponent.attemptData = this.attemptData; await this.parentComponent.completeAttempt(); }
        },
        async submitProject() {
            if (!this.attemptData.project_code || !this.attemptData.project_code.trim()) { alert('يرجى كتابة كود المشروع أولاً'); return; }
            if (this.parentComponent) { this.parentComponent.attemptData = this.attemptData; await this.parentComponent.completeAttempt(); }
        },
        resetCode() { <?php if(isset($pattern->pattern_data['code_example'])): ?> this.attemptData.code = <?php echo json_encode($pattern->pattern_data['code_example'], 15, 512) ?>; <?php endif; ?> this.output = ''; },
        clearOutput() { this.output = ''; },
        async autoSave() { if (this.parentComponent && this.parentComponent.currentAttempt) { this.parentComponent.attemptData = this.attemptData; await this.parentComponent.saveProgress(); } }
    };
}
</script>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\learning-patterns\partials\pattern-scripts.blade.php ENDPATH**/ ?>