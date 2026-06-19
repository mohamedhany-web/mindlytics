

<?php $__env->startSection('title', 'معاينة الامتحان'); ?>
<?php $__env->startSection('header', 'معاينة الامتحان'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <!-- الهيدر -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.exams.index')); ?>" class="hover:text-white">الامتحانات</a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.exams.by-course', $exam->advanced_course_id)); ?>" class="hover:text-white"><?php echo e(Str::limit($exam->course?->title ?? '', 25)); ?></a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.exams.show', $exam)); ?>" class="hover:text-white"><?php echo e(Str::limit($exam->title, 25)); ?></a>
                    <span class="mx-2">/</span>
                    <span class="text-white">المعاينة</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">معاينة الامتحان</h1>
                <p class="text-sm text-white/90 mt-1"><?php echo e($exam->title); ?> — <?php echo e($exam->examQuestions->count()); ?> سؤال</p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="<?php echo e(route('admin.exams.show', $exam)); ?>" class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    العودة للامتحان
                </a>
                <a href="<?php echo e(route('admin.exams.by-course', $exam->advanced_course_id)); ?>" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-list"></i>
                    امتحانات الكورس
                </a>
                <a href="<?php echo e(route('admin.exams.questions.manage', $exam)); ?>" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-cog"></i>
                    إدارة الأسئلة
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- معلومات الامتحان -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-info-circle text-indigo-600"></i>
                    <?php echo e($exam->title); ?>

                </h2>
            </div>
            <div class="p-6 space-y-4">
                <?php if($exam->description): ?>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">وصف الامتحان</h3>
                        <p class="text-gray-600 leading-relaxed"><?php echo nl2br(e($exam->description)); ?></p>
                    </div>
                <?php endif; ?>

                <?php if($exam->instructions): ?>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">تعليمات الامتحان</h3>
                        <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100">
                            <p class="text-indigo-900 leading-relaxed"><?php echo nl2br(e($exam->instructions)); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">المدة</div>
                        <div class="font-bold text-gray-900 mt-1"><?php echo e($exam->duration_minutes); ?> دقيقة</div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">عدد الأسئلة</div>
                        <div class="font-bold text-gray-900 mt-1"><?php echo e($exam->examQuestions->count()); ?> سؤال</div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">إجمالي الدرجات</div>
                        <div class="font-bold text-gray-900 mt-1"><?php echo e($exam->total_marks ?? $exam->calculateTotalMarks()); ?></div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">درجة النجاح</div>
                        <div class="font-bold text-gray-900 mt-1"><?php echo e($exam->passing_marks); ?>%</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الأسئلة -->
        <?php if($exam->examQuestions->count() > 0): ?>
            <div class="space-y-6">
                <?php $__currentLoopData = $exam->examQuestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $examQuestion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $q = $examQuestion->question; ?>
                    <?php if(!$q): ?>
                        <?php continue; ?>
                    <?php endif; ?>
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="p-6">
                            <!-- رأس السؤال -->
                            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 text-sm font-bold">
                                        <?php echo e($index + 1); ?>

                                    </span>
                                    <?php if($examQuestion->is_required): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-100 text-red-800">
                                            إجباري
                                        </span>
                                    <?php endif; ?>
                                    <span class="text-sm font-semibold text-gray-600">(<?php echo e($examQuestion->marks); ?> نقطة)</span>
                                </div>
                                <div class="text-xs text-gray-500 flex items-center gap-2">
                                    <span><?php echo e($q->getTypeLabel()); ?></span>
                                    <?php if($q->category): ?>
                                        <span>|</span>
                                        <span><?php echo e($q->category->name); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- نص السؤال -->
                            <div class="mb-4 text-gray-900 text-lg leading-relaxed question-content">
                                <?php echo nl2br(e($q->question)); ?>

                            </div>

                            <!-- صورة السؤال -->
                            <?php if($q->image_url && $q->getImageUrl()): ?>
                                <div class="mb-4">
                                    <img src="<?php echo e($q->getImageUrl()); ?>" alt="صورة السؤال" class="max-w-full h-auto rounded-xl border border-gray-200 shadow-sm">
                                </div>
                            <?php endif; ?>

                            <!-- الخيارات -->
                            <?php if($q->type === 'multiple_choice' && $q->options && count($q->options) > 0): ?>
                                <div class="space-y-2">
                                    <?php $__currentLoopData = $q->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optIndex => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="flex items-center p-3 bg-gray-50 rounded-xl border border-gray-100">
                                            <span class="w-8 h-8 rounded-lg bg-white border-2 border-gray-200 flex items-center justify-center ml-3 text-sm font-bold text-gray-600"><?php echo e(chr(65 + $optIndex)); ?></span>
                                            <span class="text-gray-900"><?php echo e($option); ?></span>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php elseif($q->type === 'true_false'): ?>
                                <div class="space-y-2">
                                    <div class="flex items-center p-3 bg-gray-50 rounded-xl border border-gray-100">
                                        <span class="w-8 h-8 rounded-lg bg-white border-2 border-gray-200 flex items-center justify-center ml-3 text-sm font-bold text-gray-600">أ</span>
                                        <span class="text-gray-900">صحيح</span>
                                    </div>
                                    <div class="flex items-center p-3 bg-gray-50 rounded-xl border border-gray-100">
                                        <span class="w-8 h-8 rounded-lg bg-white border-2 border-gray-200 flex items-center justify-center ml-3 text-sm font-bold text-gray-600">ب</span>
                                        <span class="text-gray-900">خطأ</span>
                                    </div>
                                </div>
                            <?php elseif($q->type === 'fill_blank'): ?>
                                <div class="p-4 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                    <span class="text-sm text-gray-500">منطقة الإجابة (املأ الفراغ)</span>
                                </div>
                            <?php elseif(in_array($q->type, ['short_answer', 'essay'])): ?>
                                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 min-h-[120px]">
                                    <span class="text-sm text-gray-500"><?php echo e($q->type === 'essay' ? 'منطقة الإجابة المقالية' : 'منطقة الإجابة القصيرة'); ?></span>
                                </div>
                            <?php elseif($q->type === 'matching'): ?>
                                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                                    <span class="text-sm text-gray-500">سؤال مطابقة — قائمة العناصر</span>
                                </div>
                            <?php elseif($q->type === 'ordering'): ?>
                                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                                    <span class="text-sm text-gray-500">سؤال ترتيب — قائمة العناصر</span>
                                </div>
                            <?php endif; ?>

                            <?php if($examQuestion->time_limit): ?>
                                <div class="mt-3 text-xs text-gray-500 flex items-center gap-1">
                                    <i class="fas fa-clock"></i>
                                    وقت الإجابة المخصص: <?php echo e($examQuestion->time_limit); ?> ثانية
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- ملخص الامتحان -->
            <div class="bg-indigo-50 rounded-2xl p-6 border border-indigo-100">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-indigo-900 mb-2 flex items-center gap-2">
                            <i class="fas fa-file-alt text-indigo-600"></i>
                            ملخص الامتحان
                        </h3>
                        <ul class="text-indigo-800 text-sm space-y-1">
                            <li>إجمالي الأسئلة: <?php echo e($exam->examQuestions->count()); ?></li>
                            <li>إجمالي الدرجات: <?php echo e($exam->total_marks ?? $exam->calculateTotalMarks()); ?></li>
                            <li>الأسئلة الإجبارية: <?php echo e($exam->examQuestions->where('is_required', true)->count()); ?></li>
                            <li>الأسئلة الاختيارية: <?php echo e($exam->examQuestions->where('is_required', false)->count()); ?></li>
                        </ul>
                    </div>
                    <div class="text-indigo-400">
                        <i class="fas fa-clipboard-check text-4xl"></i>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8 text-center">
                <div class="w-20 h-20 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-question-circle text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">لا توجد أسئلة في الامتحان</h3>
                <p class="text-gray-600 mb-6">أضف أسئلة من صفحة إدارة الأسئلة ثم عاين الامتحان مرة أخرى.</p>
                <a href="<?php echo e(route('admin.exams.questions.manage', $exam)); ?>" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-plus"></i>
                    إدارة الأسئلة
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.question-content {
    font-family: 'IBM Plex Sans Arabic', sans-serif;
    line-height: 1.8;
}
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\exams\preview.blade.php ENDPATH**/ ?>