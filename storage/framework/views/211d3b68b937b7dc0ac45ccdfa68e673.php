

<?php $__env->startSection('title', $exam->title); ?>
<?php $__env->startSection('header', $exam->title); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6">
    <!-- هيدر الصفحة -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6 mb-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="<?php echo e(route('dashboard')); ?>" class="hover:text-sky-600 transition-colors">لوحة التحكم</a>
            <span class="mx-2">/</span>
            <a href="<?php echo e(route('student.exams.index')); ?>" class="hover:text-sky-600 transition-colors">امتحاناتي</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold"><?php echo e(Str::limit($exam->title, 40)); ?></span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-clipboard-list text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800"><?php echo e($exam->title); ?></h1>
                    <p class="text-sm text-slate-600 mt-0.5">
                        <?php echo e($exam->offlineCourse->title ?? $exam->course->title ?? '—'); ?>

                        <?php if($exam->offline_course_id): ?><span class="text-amber-600">(أوفلاين)</span><?php endif; ?>
                    </p>
                </div>
            </div>
            <a href="<?php echo e(route('student.exams.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                <i class="fas fa-arrow-right"></i>
                العودة
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- المحتوى الرئيسي -->
        <div class="xl:col-span-2 space-y-6">
            <!-- تفاصيل الامتحان -->
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between flex-wrap gap-2">
                    <h2 class="text-lg font-bold text-slate-800">تفاصيل الامتحان</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo e($exam->isAvailable() ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'); ?>">
                        <?php echo e($exam->isAvailable() ? 'متاح الآن' : 'غير متاح'); ?>

                    </span>
                </div>
                <div class="p-6 space-y-6">
                    <?php if($exam->description): ?>
                        <div>
                            <h3 class="text-sm font-semibold text-slate-700 mb-1">الوصف</h3>
                            <p class="text-slate-600"><?php echo e($exam->description); ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if($exam->instructions): ?>
                        <div>
                            <h3 class="text-sm font-semibold text-slate-700 mb-2">تعليمات الامتحان</h3>
                            <div class="bg-sky-50 border border-sky-200 rounded-xl p-4 text-sky-900 whitespace-pre-wrap"><?php echo e($exam->instructions); ?></div>
                        </div>
                    <?php endif; ?>
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-200">
                        <div>
                            <span class="text-sm text-slate-500">المدة</span>
                            <p class="font-semibold text-slate-800"><?php echo e($exam->duration_minutes); ?> دقيقة</p>
                        </div>
                        <div>
                            <span class="text-sm text-slate-500">عدد الأسئلة</span>
                            <p class="font-semibold text-slate-800"><?php echo e($exam->examQuestions->count()); ?> سؤال</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- المحاولات السابقة -->
            <?php if($previousAttempts->count() > 0): ?>
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50">
                        <h2 class="text-lg font-bold text-slate-800">محاولاتك السابقة</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase">المحاولة</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase">النتيجة</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase">الوقت</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase">التاريخ</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase">الحالة</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                <?php $__currentLoopData = $previousAttempts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $attempt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="px-4 py-3 text-sm text-slate-800"><?php echo e($index + 1); ?></td>
                                        <td class="px-4 py-3 text-sm">
                                            <?php if($attempt->status === 'completed'): ?>
                                                <span class="font-semibold <?php echo e($attempt->result_color == 'green' ? 'text-emerald-600' : 'text-red-600'); ?>"><?php echo e(number_format($attempt->percentage, 1)); ?>%</span>
                                            <?php else: ?>
                                                <span class="text-slate-500">غير مكتمل</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-600"><?php echo e($attempt->formatted_time); ?></td>
                                        <td class="px-4 py-3 text-sm text-slate-600"><?php echo e($attempt->created_at->format('Y-m-d H:i')); ?></td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?php echo e($attempt->result_color == 'green' ? 'bg-emerald-100 text-emerald-800' : ($attempt->result_color == 'red' ? 'bg-red-100 text-red-800' : 'bg-slate-100 text-slate-800')); ?>"><?php echo e($attempt->result_status); ?></span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <?php if($exam->show_results_immediately && $attempt->status === 'completed'): ?>
                                                <a href="<?php echo e(route('student.exams.result', [$exam, $attempt])); ?>" class="text-sky-600 hover:text-sky-700 text-sm font-medium">عرض النتيجة</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- الشريط الجانبي -->
        <div class="space-y-6">
            <!-- معلومات سريعة + زر البدء -->
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-800">معلومات الامتحان</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">مدة الامتحان</span>
                        <span class="font-semibold text-slate-800"><?php echo e($exam->duration_minutes); ?> دقيقة</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">عدد الأسئلة</span>
                        <span class="font-semibold text-slate-800"><?php echo e($exam->examQuestions->count()); ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">إجمالي الدرجات</span>
                        <span class="font-semibold text-slate-800"><?php echo e($exam->total_marks); ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">درجة النجاح</span>
                        <span class="font-semibold text-slate-800"><?php echo e($exam->passing_marks); ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">المحاولات المسموحة</span>
                        <span class="font-semibold text-slate-800"><?php echo e($exam->attempts_allowed == 0 ? 'غير محدود' : $exam->attempts_allowed); ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">محاولاتك</span>
                        <span class="font-semibold text-slate-800"><?php echo e($previousAttempts->count()); ?></span>
                    </div>
                    <?php if($exam->start_time): ?>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">يبدأ في</span>
                            <span class="font-semibold text-slate-800"><?php echo e($exam->start_time->format('Y-m-d H:i')); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if($exam->end_time): ?>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">ينتهي في</span>
                            <span class="font-semibold text-slate-800"><?php echo e($exam->end_time->format('Y-m-d H:i')); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if($exam->prevent_tab_switch || $exam->require_camera || $exam->require_microphone || $exam->auto_submit): ?>
                <div class="rounded-xl bg-amber-50 border border-amber-200 p-4">
                    <h4 class="font-semibold text-amber-900 mb-2"><i class="fas fa-shield-alt ml-1"></i> متطلبات الأمان</h4>
                    <ul class="space-y-1.5 text-sm text-amber-800">
                        <?php if($exam->prevent_tab_switch): ?><li><i class="fas fa-exclamation-triangle ml-1"></i> ممنوع تبديل التبويبات</li><?php endif; ?>
                        <?php if($exam->require_camera): ?><li><i class="fas fa-video ml-1"></i> يتطلب تفعيل الكاميرا</li><?php endif; ?>
                        <?php if($exam->require_microphone): ?><li><i class="fas fa-microphone ml-1"></i> يتطلب تفعيل المايكروفون</li><?php endif; ?>
                        <?php if($exam->auto_submit): ?><li><i class="fas fa-clock ml-1"></i> تسليم تلقائي عند انتهاء الوقت</li><?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- زر بدء الامتحان -->
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 text-center">
                    <?php if($exam->canAttempt(auth()->id())): ?>
                        <p class="text-sm text-slate-600 mb-4">تأكد من قراءة التعليمات قبل البدء. لن تتمكن من العودة بعد البدء.</p>
                        <form action="<?php echo e(route('student.exams.start', $exam)); ?>" method="POST" id="start-exam-form">
                            <?php echo csrf_field(); ?>
                            <button type="button" onclick="confirmStart()" class="w-full px-6 py-3.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-bold text-lg transition-colors">
                                <i class="fas fa-play ml-2"></i>
                                ابدأ الامتحان الآن
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-2">
                            <i class="fas fa-times-circle text-4xl text-red-500 mb-3"></i>
                            <h4 class="text-lg font-bold text-red-800 mb-1">غير متاح للبدء</h4>
                            <p class="text-sm text-red-700">
                                <?php if($previousAttempts->count() >= $exam->attempts_allowed && $exam->attempts_allowed > 0): ?>
                                    لقد استنفدت عدد المحاولات (<?php echo e($exam->attempts_allowed); ?>)
                                <?php elseif(!$exam->isAvailable()): ?>
                                    الامتحان غير متاح حالياً
                                <?php else: ?>
                                    غير مصرح لك بأداء هذا الامتحان
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if($previousAttempts->where('status', 'completed')->count() > 0): ?>
                <?php $bestScore = $previousAttempts->where('status', 'completed')->max('percentage'); $lastAttempt = $previousAttempts->where('status', 'completed')->first(); ?>
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50">
                        <h3 class="text-lg font-bold text-slate-800">أفضل نتيجة</h3>
                    </div>
                    <div class="p-6 text-center">
                        <div class="text-3xl font-bold text-sky-600"><?php echo e(number_format($bestScore, 1)); ?>%</div>
                        <?php if($exam->show_results_immediately && $lastAttempt): ?>
                            <a href="<?php echo e(route('student.exams.result', [$exam, $lastAttempt])); ?>" class="inline-block mt-3 text-sky-600 hover:text-sky-700 font-medium text-sm">عرض آخر نتيجة</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- نافذة تأكيد البدء -->
<div id="confirmModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-md w-full p-6" onclick="event.stopPropagation()">
        <div class="text-center">
            <div class="w-14 h-14 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">تأكيد بدء الامتحان</h3>
            <p class="text-sm text-slate-600 mb-4">هل أنت متأكد من بدء الامتحان؟ لن تتمكن من العودة أو إيقاف الامتحان بعد البدء.</p>
            <?php if($exam->prevent_tab_switch): ?>
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 font-medium">
                    <i class="fas fa-warning ml-1"></i> ممنوع تبديل التبويبات أثناء الامتحان
                </div>
            <?php endif; ?>
            <div class="flex gap-3 justify-center">
                <button type="button" onclick="startExam()" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-semibold transition-colors">ابدأ</button>
                <button type="button" onclick="closeModal()" class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl font-semibold transition-colors">إلغاء</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function confirmStart() { document.getElementById('confirmModal').classList.remove('hidden'); }
function closeModal() { document.getElementById('confirmModal').classList.add('hidden'); }
function startExam() { document.getElementById('start-exam-form').submit(); }
document.getElementById('confirmModal').addEventListener('click', function(e) { if (e.target === this) closeModal(); });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/exams/show.blade.php ENDPATH**/ ?>