<?php
    $embed = !empty($embed);
    $inProgressAttempt = $userAttempts->filter(fn($a) => in_array($a->status, ['in_progress', 'started']))->first();
?>
<div class="<?php echo e($embed ? 'w-full max-w-none min-w-0' : 'max-w-4xl mx-auto'); ?> space-y-6" x-data="learningPattern()" x-init="init()" @pattern-complete="attemptData = $event.detail; completeAttempt()">
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 mb-1"><?php echo e($pattern->title); ?></h1>
                <?php if($pattern->description): ?>
                    <p class="text-slate-600 text-sm mt-1"><?php echo e($pattern->description); ?></p>
                <?php endif; ?>
            </div>
            <?php if($embed): ?>
                <a href="<?php echo e(route('my-courses.learn', $course)); ?>" target="_parent" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-arrow-right ml-2"></i> العودة للمنهج
                </a>
            <?php else: ?>
                <a href="<?php echo e(route('my-courses.learn', $course)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-arrow-right ml-2"></i> العودة
                </a>
            <?php endif; ?>
        </div>
        <div class="flex flex-wrap gap-3 mt-4 pt-4 border-t border-slate-100">
            <?php $typeInfo = $pattern->getTypeInfo(); ?>
            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-100 text-amber-800 rounded-lg text-sm font-semibold"><i class="<?php echo e($typeInfo['icon'] ?? 'fas fa-puzzle-piece'); ?>"></i> <?php echo e($typeInfo['name'] ?? 'نمط تعليمي'); ?></span>
            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-sky-100 text-sky-700 rounded-lg text-sm font-semibold"><i class="fas fa-star"></i> <?php echo e($pattern->points); ?> نقطة</span>
            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-100 text-slate-600 rounded-lg text-sm font-semibold">مستوى <?php echo e($pattern->difficulty_level); ?>/5</span>
            <?php if($pattern->time_limit_minutes): ?>
                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-100 text-slate-600 rounded-lg text-sm font-semibold"><i class="fas fa-clock"></i> <?php echo e($pattern->time_limit_minutes); ?> دقيقة</span>
            <?php endif; ?>
            <?php if($pattern->is_required): ?>
                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-100 text-amber-700 rounded-lg text-sm font-semibold">إلزامي</span>
            <?php endif; ?>
        </div>
    </div>

    <?php if($pattern->instructions): ?>
        <div class="rounded-xl bg-sky-50 border border-sky-200 p-4">
            <h3 class="font-bold text-sky-900 mb-2 flex items-center gap-2"><i class="fas fa-info-circle"></i> التعليمات</h3>
            <div class="text-sky-800 whitespace-pre-wrap text-sm"><?php echo e($pattern->instructions); ?></div>
        </div>
    <?php endif; ?>

    <?php if($userAttempts->count() > 0): ?>
        <div class="rounded-xl bg-white border border-slate-200 p-5 shadow-sm">
            <h3 class="text-lg font-bold text-slate-800 mb-4">محاولاتك السابقة</h3>
            <div class="space-y-3">
                <?php $__currentLoopData = $userAttempts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attempt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="rounded-xl p-4 border <?php echo e($attempt->status === 'completed' ? 'bg-emerald-50/50 border-emerald-200' : ($attempt->status === 'failed' ? 'bg-red-50/50 border-red-200' : 'bg-slate-50/50 border-slate-200')); ?>">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div>
                                <span class="font-bold text-slate-800">محاولة #<?php echo e($userAttempts->count() - $loop->index); ?></span>
                                <span class="text-sm text-slate-500 mr-2">— <?php echo e($attempt->created_at->format('Y/m/d H:i')); ?></span>
                                <div class="text-sm mt-1">
                                    <?php if($attempt->status === 'completed'): ?>
                                        <span class="text-emerald-600 font-semibold"><i class="fas fa-check-circle ml-1"></i> مكتملة</span>
                                        <?php if($attempt->score !== null): ?> — <?php echo e($attempt->score); ?>% <?php endif; ?>
                                        <?php if($attempt->points_earned > 0): ?> — <?php echo e($attempt->points_earned); ?> نقطة <?php endif; ?>
                                    <?php elseif($attempt->status === 'failed'): ?>
                                        <span class="text-red-600 font-semibold"><i class="fas fa-times-circle ml-1"></i> فشلت</span>
                                    <?php else: ?>
                                        <span class="text-amber-600 font-semibold"><i class="fas fa-clock ml-1"></i> قيد التنفيذ</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if($attempt->feedback && $attempt->status === 'completed'): ?>
                                <button @click="showFeedback(<?php echo e($attempt->id); ?>)" class="px-3 py-1.5 bg-sky-500 hover:bg-sky-600 text-white rounded-lg text-sm font-semibold">
                                    <i class="fas fa-eye ml-1"></i> التفاصيل
                                </button>
                            <?php elseif(in_array($attempt->status, ['in_progress', 'started'])): ?>
                                <span class="text-sky-600 text-sm font-medium">أكمل التحدي أدناه</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="rounded-xl bg-white border border-slate-200 p-6 shadow-sm">
        <?php if($canAttempt): ?>
            <?php if(!$inProgressAttempt): ?>
                <div class="text-center py-4">
                    <button type="button"
                        @click.prevent="startAttempt()"
                        class="px-8 py-4 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-bold shadow-md transition-colors">
                        <i class="fas fa-play ml-2"></i> بدء المحاولة
                    </button>
                </div>
            <?php endif; ?>

            <?php if($inProgressAttempt): ?>
                <script>window.currentAttemptId = <?php echo e($inProgressAttempt->id); ?>; window.currentAttemptData = <?php echo json_encode($inProgressAttempt->attempt_data ?? [], 15, 512) ?>;</script>
            <?php else: ?>
                <script>window.currentAttemptId = null; window.currentAttemptData = {};</script>
            <?php endif; ?>

            <div id="patternContent"
                 x-show="currentAttempt || <?php echo e($inProgressAttempt ? 'true' : 'false'); ?>"
                 x-data="patternTypeHandler('<?php echo e($pattern->type); ?>')">
                <?php
                    $typeView = 'student.learning-patterns.types.' . $pattern->type;
                    $typeViewExists = view()->exists($typeView);
                ?>
                <?php if($typeViewExists): ?>
                    <?php echo $__env->make($typeView, ['pattern' => $pattern, 'attempt' => $inProgressAttempt], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php else: ?>
                    <?php echo $__env->make('student.learning-patterns.types.generic', ['pattern' => $pattern, 'attempt' => $inProgressAttempt], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4"><i class="fas fa-lock text-2xl text-slate-400"></i></div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">لا يمكنك بدء محاولة جديدة</h3>
                <p class="text-slate-600">
                    <?php if($pattern->max_attempts): ?>
                        لقد وصلت للحد الأقصى من المحاولات (<?php echo e($pattern->max_attempts); ?>)
                    <?php else: ?>
                        المحاولات المتعددة غير مسموحة
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>

    <!-- مودال تفاصيل المحاولة - داخل نفس مكوّن Alpine ليعمل الإغلاق -->
    <div x-show="showFeedbackModal"
         x-cloak
         x-transition
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60" @click="showFeedbackModal = false" aria-hidden="true"></div>
        <div class="relative z-10 bg-white rounded-2xl p-6 max-w-2xl w-full max-h-[85vh] overflow-y-auto border border-slate-200 shadow-2xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-slate-800">تفاصيل المحاولة</h3>
                <button type="button"
                        @click="showFeedbackModal = false"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition-colors">
                    <i class="fas fa-times ml-1"></i> إغلاق
                </button>
            </div>
            <div x-html="feedbackContent"></div>
            <div class="mt-6 pt-4 border-t border-slate-200">
                <button type="button"
                        @click="showFeedbackModal = false"
                        class="w-full py-3 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold transition-colors">
                    <i class="fas fa-times ml-1"></i> إغلاق
                </button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\student\learning-patterns\partials\pattern-content.blade.php ENDPATH**/ ?>