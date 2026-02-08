<?php $__env->startSection('title', 'تفاصيل الاختبار'); ?>
<?php $__env->startSection('header', 'تفاصيل الاختبار: ' . $exam->title); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800"><?php echo e($exam->title); ?></h1>
                <p class="text-sm text-slate-500 mt-0.5">عرض تفاصيل الاختبار والمحاولات</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?php echo e(route('instructor.exams.questions.manage', $exam)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-violet-500 hover:bg-violet-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-cogs"></i> إدارة الأسئلة
                </a>
                <a href="<?php echo e(route('instructor.exams.edit', $exam)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <a href="<?php echo e(route('instructor.exams.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <div class="xl:col-span-3">
            <div class="rounded-xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800">معلومات الاختبار</h3>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold <?php echo e($exam->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'); ?>">
                        <i class="fas <?php echo e($exam->is_active ? 'fa-check-circle' : 'fa-ban'); ?> ml-1"></i>
                        <?php echo e($exam->is_active ? 'نشط' : 'معطل'); ?>

                    </span>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-500 mb-1">العنوان</label>
                                <div class="font-bold text-slate-800 text-lg"><?php echo e($exam->title); ?></div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-500 mb-1">الكورس</label>
                                <div class="text-slate-800 font-semibold"><?php echo e($exam->offlineCourse->title ?? $exam->advancedCourse->title ?? '—'); ?> <?php if($exam->offline_course_id): ?><span class="text-amber-600">(أوفلاين)</span><?php endif; ?></div>
                                <?php if($exam->advancedCourse && $exam->advancedCourse->academicSubject): ?>
                                    <div class="text-sm text-slate-500"><?php echo e($exam->advancedCourse->academicSubject->name); ?></div>
                                <?php endif; ?>
                            </div>
                            <?php if($exam->lesson && !$exam->offline_course_id): ?>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-500 mb-1">الدرس</label>
                                    <div class="text-slate-800 font-semibold"><?php echo e($exam->lesson->title); ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-500 mb-1">مدة الاختبار</label>
                                <div class="text-slate-800 font-bold text-lg"><?php echo e($exam->duration_minutes); ?> دقيقة</div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-500 mb-1">الدرجة الكلية</label>
                                <div class="text-slate-800 font-bold text-lg"><?php echo e($exam->total_marks); ?> نقطة</div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-500 mb-1">درجة النجاح</label>
                                <div class="text-slate-800 font-bold text-lg"><?php echo e($exam->passing_marks); ?> نقطة</div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-500 mb-1">المحاولات المسموحة</label>
                                <div class="text-slate-800 font-bold text-lg"><?php echo e($exam->attempts_allowed == 0 ? 'غير محدود' : $exam->attempts_allowed); ?></div>
                            </div>
                        </div>
                    </div>
                    <?php if($exam->description): ?>
                        <div class="mt-6">
                            <label class="block text-sm font-semibold text-slate-500 mb-2">الوصف</label>
                            <div class="text-slate-700 bg-slate-50 p-4 rounded-xl border border-slate-200"><?php echo e($exam->description); ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if($exam->instructions): ?>
                        <div class="mt-6">
                            <label class="block text-sm font-semibold text-slate-500 mb-2">التعليمات</label>
                            <div class="text-slate-700 bg-slate-50 p-4 rounded-xl border border-slate-200 whitespace-pre-wrap"><?php echo e($exam->instructions); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center text-sky-600"><i class="fas fa-question-circle"></i></div>
                    <div>
                        <p class="text-2xl font-bold text-slate-800"><?php echo e($exam->questions->count()); ?></p>
                        <p class="text-xs font-semibold text-slate-500">أسئلة</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center text-violet-600"><i class="fas fa-users"></i></div>
                    <div>
                        <p class="text-2xl font-bold text-slate-800"><?php echo e($attemptStats['total']); ?></p>
                        <p class="text-xs font-semibold text-slate-500">محاولات</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600"><i class="fas fa-check-double"></i></div>
                    <div>
                        <p class="text-2xl font-bold text-slate-800"><?php echo e($attemptStats['completed']); ?></p>
                        <p class="text-xs font-semibold text-slate-500">مكتملة</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600"><i class="fas fa-star"></i></div>
                    <div>
                        <p class="text-2xl font-bold text-slate-800"><?php echo e(number_format($attemptStats['average_score'], 1)); ?></p>
                        <p class="text-xs font-semibold text-slate-500">متوسط الدرجات</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white border border-slate-200 shadow-sm overflow-hidden" x-data="{ activeTab: 'questions' }">
        <div class="border-b border-slate-200">
            <nav class="flex gap-6 px-6">
                <button type="button" @click="activeTab = 'questions'"
                        :class="activeTab === 'questions' ? 'border-sky-500 text-sky-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'"
                        class="py-4 px-1 border-b-2 text-sm transition-colors">
                    <i class="fas fa-question-circle ml-2"></i> الأسئلة (<?php echo e($exam->questions->count()); ?>)
                </button>
                <button type="button" @click="activeTab = 'attempts'"
                        :class="activeTab === 'attempts' ? 'border-sky-500 text-sky-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'"
                        class="py-4 px-1 border-b-2 text-sm transition-colors">
                    <i class="fas fa-users ml-2"></i> المحاولات (<?php echo e($attempts->total()); ?>)
                </button>
                <button type="button" @click="activeTab = 'settings'"
                        :class="activeTab === 'settings' ? 'border-sky-500 text-sky-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'"
                        class="py-4 px-1 border-b-2 text-sm transition-colors">
                    <i class="fas fa-cogs ml-2"></i> الإعدادات
                </button>
            </nav>
        </div>
        <div class="p-6">
            <div x-show="activeTab === 'questions'">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-bold text-slate-800">أسئلة الاختبار</h4>
                    <a href="<?php echo e(route('instructor.exams.questions.manage', $exam)); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-violet-500 hover:bg-violet-600 text-white rounded-xl font-semibold text-sm transition-colors">
                        <i class="fas fa-cogs"></i> إدارة الأسئلة
                    </a>
                </div>
                <?php if($exam->questions->count() > 0): ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $exam->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-sky-500 rounded-xl flex items-center justify-center text-white font-bold text-sm"><?php echo e($index + 1); ?></div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800"><?php echo e(Str::limit($question->question, 80)); ?></p>
                                        <div class="flex items-center gap-4 text-xs text-slate-500 mt-1">
                                            <span><?php echo e($question->pivot->marks ?? 1); ?> نقطة</span>
                                            <?php if($question->type): ?><span><?php echo e($question->type); ?></span><?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <div class="w-16 h-16 rounded-2xl bg-sky-100 flex items-center justify-center mx-auto mb-4"><i class="fas fa-question-circle text-2xl text-sky-500"></i></div>
                        <h3 class="text-lg font-bold text-slate-800 mb-2">لا توجد أسئلة</h3>
                        <p class="text-sm text-slate-500 mb-4">ابدأ بإضافة الأسئلة من صفحة إدارة الأسئلة</p>
                        <a href="<?php echo e(route('instructor.exams.questions.manage', $exam)); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                            <i class="fas fa-cogs"></i> إدارة الأسئلة
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <div x-show="activeTab === 'attempts'">
                <h4 class="text-lg font-bold text-slate-800 mb-4">محاولات الطلاب</h4>
                <?php if($attempts->count() > 0): ?>
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase">الطالب</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase">النتيجة</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase">الحالة</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase">التاريخ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                <?php $__currentLoopData = $attempts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attempt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-sky-100 rounded-xl flex items-center justify-center text-sky-600 font-bold text-sm"><?php echo e(substr($attempt->user->name ?? '?', 0, 1)); ?></div>
                                                <div>
                                                    <div class="text-sm font-semibold text-slate-800"><?php echo e($attempt->user->name ?? '—'); ?></div>
                                                    <div class="text-xs text-slate-500"><?php echo e($attempt->user->email ?? '—'); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php if($attempt->status === 'completed' && $attempt->score !== null): ?>
                                                <div class="text-sm font-semibold text-slate-800"><?php echo e(number_format($attempt->score, 1)); ?> / <?php echo e($exam->total_marks); ?></div>
                                                <div class="text-xs text-slate-500"><?php echo e(number_format(($attempt->score / $exam->total_marks) * 100, 1)); ?>%</div>
                                            <?php else: ?>
                                                <span class="text-sm text-slate-500">لم يكتمل</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold
                                                <?php if($attempt->status === 'completed'): ?> bg-emerald-100 text-emerald-700
                                                <?php elseif($attempt->status === 'in_progress'): ?> bg-amber-100 text-amber-700
                                                <?php else: ?> bg-slate-100 text-slate-600
                                                <?php endif; ?>">
                                                <?php echo e($attempt->status === 'completed' ? 'مكتمل' : ($attempt->status === 'in_progress' ? 'قيد التنفيذ' : 'غير مكتمل')); ?>

                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-600"><?php echo e($attempt->submitted_at ? $attempt->submitted_at->format('Y-m-d H:i') : $attempt->created_at->format('Y-m-d H:i')); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 flex justify-center"><div class="rounded-xl p-3 bg-white border border-slate-200"><?php echo e($attempts->links()); ?></div></div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <div class="w-16 h-16 rounded-2xl bg-sky-100 flex items-center justify-center mx-auto mb-4"><i class="fas fa-users text-2xl text-sky-500"></i></div>
                        <h3 class="text-lg font-bold text-slate-800 mb-2">لا توجد محاولات</h3>
                        <p class="text-sm text-slate-500">لم يقم أي طالب بأداء هذا الاختبار بعد</p>
                    </div>
                <?php endif; ?>
            </div>

            <div x-show="activeTab === 'settings'">
                <h4 class="text-lg font-bold text-slate-800 mb-4">إعدادات الاختبار</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <?php $__currentLoopData = [
                            ['randomize_questions', 'خلط الأسئلة'],
                            ['randomize_options', 'خلط الخيارات'],
                            ['show_results_immediately', 'عرض النتائج فوراً'],
                            ['show_correct_answers', 'عرض الإجابات الصحيحة'],
                            ['show_explanations', 'عرض شرح الإجابات'],
                            ['allow_review', 'السماح بمراجعة الإجابات'],
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $attr = $item[0]; $name = $item[1]; ?>
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <span class="text-sm font-medium text-slate-700"><?php echo e($name); ?></span>
                                <span class="text-sm font-semibold <?php echo e($exam->$attr ? 'text-emerald-600' : 'text-slate-500'); ?>"><?php echo e($exam->$attr ? 'مفعل' : 'معطل'); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/exams/show.blade.php ENDPATH**/ ?>