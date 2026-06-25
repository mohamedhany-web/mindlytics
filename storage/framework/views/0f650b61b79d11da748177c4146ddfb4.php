<?php $__env->startSection('title', 'تفاصيل النمط التعليمي - ' . $pattern->title); ?>
<?php $__env->startSection('header', 'تفاصيل النمط التعليمي'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-1"><?php echo e($pattern->title); ?></h1>
                <p class="text-sm text-slate-500"><?php echo e($course->title); ?></p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('instructor.learning-patterns.index', $course)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-arrow-right ml-2"></i> العودة
                </a>
                <a href="<?php echo e(route('instructor.learning-patterns.edit', [$course, $pattern])); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-edit ml-2"></i> تعديل
                </a>
                <a href="<?php echo e(route('instructor.courses.curriculum', $course)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-book ml-2"></i> إضافة للمنهج
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 mb-4">تفاصيل النمط</h2>
                
                <?php
                    $typeInfo = $pattern->getTypeInfo();
                ?>
                
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                            <i class="<?php echo e($typeInfo['icon'] ?? 'fas fa-puzzle-piece'); ?> text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">نوع النمط</p>
                            <p class="font-bold text-slate-800"><?php echo e($typeInfo['name'] ?? 'نمط تعليمي'); ?></p>
                        </div>
                    </div>
                    
                    <?php if($pattern->description): ?>
                        <div>
                            <p class="text-xs text-slate-500 mb-1">الوصف</p>
                            <p class="text-slate-700"><?php echo e($pattern->description); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($pattern->instructions): ?>
                        <div>
                            <p class="text-xs text-slate-500 mb-1">التعليمات</p>
                            <div class="text-slate-700 whitespace-pre-wrap"><?php echo e($pattern->instructions); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if($pattern->pattern_data && count($pattern->pattern_data) > 0): ?>
                <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-800 mb-4">البيانات التفاعلية</h2>
                    <div class="space-y-4">
                        <?php if($pattern->type === 'code_challenge'): ?>
                            <?php if(isset($pattern->pattern_data['problem_description'])): ?>
                                <div>
                                    <h4 class="font-bold text-[#1C2C39] mb-2">وصف التحدي</h4>
                                    <div class="bg-slate-50 rounded-xl p-4 whitespace-pre-wrap text-slate-700"><?php echo e($pattern->pattern_data['problem_description']); ?></div>
                                </div>
                            <?php endif; ?>
                            <?php if(isset($pattern->pattern_data['language'])): ?>
                                <div>
                                    <h4 class="font-bold text-slate-800 mb-2">لغة البرمجة</h4>
                                    <div class="bg-sky-50 rounded-xl p-4 text-sky-800"><?php echo e($pattern->pattern_data['language']); ?></div>
                                </div>
                            <?php endif; ?>
                        <?php elseif($pattern->type === 'interactive_quiz'): ?>
                            <?php if(isset($pattern->pattern_data['questions'])): ?>
                                <div>
                                    <h4 class="font-bold text-slate-800 mb-2">الأسئلة (<?php echo e(count($pattern->pattern_data['questions'])); ?>)</h4>
                                    <div class="space-y-3">
                                        <?php $__currentLoopData = $pattern->pattern_data['questions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                                                <div class="font-bold mb-2 text-slate-800">سؤال <?php echo e($index + 1); ?>: <?php echo e($question['question'] ?? ''); ?></div>
                                                <div class="text-sm text-slate-600">
                                                    النوع: <?php echo e($question['type'] ?? 'multiple_choice'); ?>

                                                    <?php if(isset($question['correct_answer'])): ?>
                                                        | الإجابة الصحيحة: <?php echo e($question['correct_answer']); ?>

                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php elseif($pattern->type === 'live_coding'): ?>
                            <?php if(isset($pattern->pattern_data['video_url'])): ?>
                                <div>
                                    <h4 class="font-bold text-slate-800 mb-2">رابط الفيديو</h4>
                                    <div class="bg-slate-50 rounded-xl p-4">
                                        <a href="<?php echo e($pattern->pattern_data['video_url']); ?>" target="_blank" class="text-sky-600 hover:underline">
                                            <?php echo e($pattern->pattern_data['video_url']); ?>

                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php elseif($pattern->type === 'flashcards'): ?>
                            <?php if(isset($pattern->pattern_data['flashcards'])): ?>
                                <div>
                                    <h4 class="font-bold text-slate-800 mb-2">البطاقات (<?php echo e(count($pattern->pattern_data['flashcards'])); ?>)</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <?php $__currentLoopData = $pattern->pattern_data['flashcards']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                                                <div class="text-xs text-slate-500 mb-1">بطاقة <?php echo e($index + 1); ?></div>
                                                <div class="font-bold mb-1 text-slate-800"><?php echo e($card['front'] ?? ''); ?></div>
                                                <div class="text-sm text-slate-600"><?php echo e($card['back'] ?? ''); ?></div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="bg-slate-50 rounded-xl p-4">
                                <pre class="text-sm text-slate-700 whitespace-pre-wrap"><?php echo e(json_encode($pattern->pattern_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 mb-4">الإحصائيات</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">النقاط</span>
                        <span class="font-bold text-sky-600"><?php echo e($pattern->points); ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">مستوى الصعوبة</span>
                        <span class="font-bold text-slate-800"><?php echo e($pattern->difficulty_level); ?>/5</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">إجمالي المحاولات</span>
                        <span class="font-bold text-sky-600"><?php echo e($pattern->total_attempts); ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">إجمالي الإكمالات</span>
                        <span class="font-bold text-emerald-600"><?php echo e($pattern->total_completions); ?></span>
                    </div>
                    <?php if($pattern->time_limit_minutes): ?>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600">الحد الزمني</span>
                            <span class="font-bold text-amber-600"><?php echo e($pattern->time_limit_minutes); ?> دقيقة</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 mb-4">الإعدادات</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">إلزامي</span>
                        <?php if($pattern->is_required): ?>
                            <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-lg text-xs font-semibold">نعم</span>
                        <?php else: ?>
                            <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-semibold">لا</span>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">محاولات متعددة</span>
                        <?php if($pattern->allow_multiple_attempts): ?>
                            <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-semibold">مسموح</span>
                        <?php else: ?>
                            <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-semibold">غير مسموح</span>
                        <?php endif; ?>
                    </div>
                    <?php if($pattern->max_attempts): ?>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600">الحد الأقصى</span>
                            <span class="font-bold text-slate-800"><?php echo e($pattern->max_attempts); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">الحالة</span>
                        <?php if($pattern->is_active): ?>
                            <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-semibold">نشط</span>
                        <?php else: ?>
                            <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-semibold">غير نشط</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if($pattern->attempts->count() > 0): ?>
        <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-800 mb-4">المحاولات الأخيرة</h2>
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="text-right py-3 px-4 text-sm font-bold text-slate-800">الطالب</th>
                            <th class="text-right py-3 px-4 text-sm font-bold text-slate-800">الحالة</th>
                            <th class="text-right py-3 px-4 text-sm font-bold text-slate-800">النتيجة</th>
                            <th class="text-right py-3 px-4 text-sm font-bold text-slate-800">التاريخ</th>
                            <th class="text-right py-3 px-4 text-sm font-bold text-slate-800">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $pattern->attempts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attempt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="border-b border-slate-100 hover:bg-slate-50/50">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center text-xs font-bold">
                                            <?php echo e(substr($attempt->user->name ?? 'غير معروف', 0, 1)); ?>

                                        </div>
                                        <span class="text-sm text-slate-800"><?php echo e($attempt->user->name ?? 'غير معروف'); ?></span>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <?php if($attempt->status === 'completed'): ?>
                                        <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-semibold">مكتمل</span>
                                    <?php elseif($attempt->status === 'in_progress'): ?>
                                        <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-lg text-xs font-semibold">قيد التنفيذ</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-semibold"><?php echo e($attempt->status); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4">
                                    <?php if($attempt->score !== null): ?>
                                        <span class="font-bold text-sky-600"><?php echo e($attempt->score); ?>%</span>
                                    <?php else: ?>
                                        <span class="text-slate-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-sm text-slate-600">
                                    <?php echo e($attempt->created_at->format('Y/m/d H:i')); ?>

                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <form action="<?php echo e(route('instructor.learning-patterns.attempts.destroy', [$course, $pattern, $attempt])); ?>" method="POST" class="inline" onsubmit="return confirm('إزالة هذه المحاولة للسماح للطالب بالمحاولة مرة أخرى؟');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-sky-100 hover:bg-sky-200 text-sky-700 text-xs font-semibold transition-colors" title="إعادة المحاولة">
                                                <i class="fas fa-redo text-xs"></i>
                                                إعادة المحاولة
                                            </button>
                                        </form>
                                        <form action="<?php echo e(route('instructor.learning-patterns.attempts.destroy', [$course, $pattern, $attempt])); ?>" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه المحاولة؟');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 text-xs font-semibold transition-colors" title="حذف المحاولة">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                                حذف
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/instructor/learning-patterns/show.blade.php ENDPATH**/ ?>