<div class="rounded-xl bg-white border border-slate-200 p-6 shadow-sm"
     x-data="quizHandler()"
     x-init="init()">
    <h3 class="text-lg font-bold text-slate-800 mb-4">اختبار تفاعلي</h3>
    
    <?php
        $questions = $pattern->pattern_data['questions'] ?? [];
    ?>
    
    <?php if(count($questions) > 0): ?>
        <div class="space-y-5">
            <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="border border-slate-200 rounded-xl p-4 bg-slate-50/50">
                    <h4 class="font-bold text-slate-800 mb-3">
                        سؤال <?php echo e($index + 1); ?>: <?php echo e($question['question'] ?? ''); ?>

                    </h4>
                    
                    <?php if(isset($question['type']) && $question['type'] === 'true_false'): ?>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 cursor-pointer p-2 hover:bg-slate-100 rounded-lg transition-colors">
                                <input type="radio" name="answers[<?php echo e($index); ?>]" value="true" x-model="attemptData.answers['<?php echo e($index); ?>']" @change="autoSave()" class="w-4 h-4 text-sky-500">
                                <span class="text-slate-700">صحيح</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer p-2 hover:bg-slate-100 rounded-lg transition-colors">
                                <input type="radio" name="answers[<?php echo e($index); ?>]" value="false" x-model="attemptData.answers['<?php echo e($index); ?>']" @change="autoSave()" class="w-4 h-4 text-sky-500">
                                <span class="text-slate-700">خطأ</span>
                            </label>
                        </div>
                    <?php else: ?>
                        <div class="space-y-2">
                            <?php if(isset($question['options'])): ?>
                                <?php $__currentLoopData = $question['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optIndex => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="flex items-center gap-2 cursor-pointer p-2 hover:bg-slate-100 rounded-lg transition-colors">
                                        <input type="radio" name="answers[<?php echo e($index); ?>]" value="<?php echo e($optIndex); ?>" x-model="attemptData.answers['<?php echo e($index); ?>']" @change="autoSave()" class="w-4 h-4 text-sky-500">
                                        <span class="text-slate-700"><?php echo e($option); ?></span>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        
        <div class="mt-6 flex gap-3">
            <button @click="submitQuiz()" 
                    class="flex-1 px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-semibold transition-colors">
                <i class="fas fa-check ml-2"></i>
                إرسال الإجابات
            </button>
        </div>
    <?php else: ?>
        <div class="text-center py-8 text-slate-500">
            <p>لا توجد أسئلة متاحة</p>
        </div>
    <?php endif; ?>
</div>

<script>
function quizHandler() {
    return {
        attemptData: { answers: {} },
        parentComponent: null,
        
        init() {
            const el = this.$el.closest('[x-data*="learningPattern"]');
            if (el && el.__x) this.parentComponent = el.__x.$data;
            if (window.currentAttemptData && window.currentAttemptData.answers) {
                this.attemptData.answers = window.currentAttemptData.answers;
            }
        },
        
        async submitQuiz() {
            const totalQuestions = <?php echo e(count($questions)); ?>;
            const answeredQuestions = Object.keys(this.attemptData.answers).length;
            
            if (answeredQuestions < totalQuestions) {
                if (!confirm(`لقد أجبت على ${answeredQuestions} من ${totalQuestions} سؤال. هل تريد المتابعة؟`)) {
                    return;
                }
            }
            
            this.$dispatch('pattern-complete', this.attemptData);
        },
        
        async autoSave() {
            if (this.parentComponent) {
                this.parentComponent.attemptData = this.attemptData;
                await this.parentComponent.saveProgress();
            }
        }
    };
}
</script>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\learning-patterns\types\interactive_quiz.blade.php ENDPATH**/ ?>