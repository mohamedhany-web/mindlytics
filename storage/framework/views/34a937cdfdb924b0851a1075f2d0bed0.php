<?php $__env->startSection('title', 'تفاصيل الامتحان'); ?>
<?php $__env->startSection('header', 'تفاصيل الامتحان'); ?>

<?php
    $stats = $exam->stats ?? [];
    $averageScore = $stats['average_score'] ?? 0;
    $passRate = $stats['pass_rate'] ?? 0;
?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full px-4 py-6 space-y-6" x-data="{ activeTab: 'questions' }">
    <!-- الهيدر -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.exams.index')); ?>" class="hover:text-white">الامتحانات</a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.exams.by-course', $exam->advanced_course_id)); ?>" class="hover:text-white"><?php echo e(Str::limit($exam->course->title ?? '', 30)); ?></a>
                    <span class="mx-2">/</span>
                    <span class="text-white"><?php echo e(Str::limit($exam->title, 40)); ?></span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1"><?php echo e($exam->title); ?></h1>
                <p class="text-sm text-white/90 mt-1"><?php echo e($exam->course->title ?? ''); ?> — <?php echo e($exam->duration_minutes); ?> دقيقة</p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="<?php echo e(route('admin.exams.edit', $exam)); ?>" class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-edit"></i>
                    تعديل
                </a>
                <a href="<?php echo e(route('admin.exams.by-course', $exam->advanced_course_id)); ?>" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-arrow-right"></i>
                    رجوع لامتحانات الكورس
                </a>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-xl bg-green-50 border border-green-200 text-green-800 px-4 py-3 flex items-center gap-2">
            <i class="fas fa-check-circle text-green-600"></i>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl bg-red-50 border border-red-200 text-red-800 px-4 py-3 flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-red-600"></i>
            <span><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <!-- معلومات الامتحان + إحصائيات -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- بطاقة معلومات الامتحان -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-indigo-600"></i>
                        معلومات الامتحان
                    </h2>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold <?php echo e($exam->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                            <?php echo e($exam->is_active ? 'نشط' : 'غير نشط'); ?>

                        </span>
                        <?php if($exam->is_published): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                <i class="fas fa-globe ml-1"></i>
                                منشور
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">الكورس</p>
                            <p class="text-gray-900 font-medium"><?php echo e($exam->course->title ?? '—'); ?></p>
                            <?php if($exam->course && $exam->course->academicSubject): ?>
                                <p class="text-sm text-gray-500 mt-0.5"><?php echo e($exam->course->academicSubject->name); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php if($exam->lesson): ?>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">الدرس</p>
                            <p class="text-gray-900 font-medium"><?php echo e($exam->lesson->title); ?></p>
                        </div>
                        <?php endif; ?>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">مدة الامتحان</p>
                            <p class="text-gray-900 font-medium"><?php echo e($exam->duration_minutes); ?> دقيقة</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">درجة النجاح</p>
                            <p class="text-gray-900 font-medium"><?php echo e($exam->passing_marks); ?>%</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">المحاولات المسموحة</p>
                            <p class="text-gray-900 font-medium"><?php echo e($exam->attempts_allowed == 0 ? 'غير محدود' : $exam->attempts_allowed); ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">إجمالي الدرجات</p>
                            <p class="text-gray-900 font-medium"><?php echo e($exam->total_marks ?? '—'); ?></p>
                        </div>
                    </div>
                    <?php if($exam->description): ?>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">الوصف</p>
                            <p class="text-gray-700"><?php echo e($exam->description); ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if($exam->instructions): ?>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">التعليمات</p>
                            <div class="text-gray-700 bg-gray-50 p-4 rounded-xl whitespace-pre-wrap border border-gray-100"><?php echo e($exam->instructions); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- إحصائيات سريعة -->
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-question-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900"><?php echo e($exam->examQuestions->count()); ?></p>
                    <p class="text-sm text-gray-500">أسئلة</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900"><?php echo e($exam->attempts->count()); ?></p>
                    <p class="text-sm text-gray-500">محاولات</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-star text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900"><?php echo e(number_format($averageScore, 1)); ?></p>
                    <p class="text-sm text-gray-500">متوسط الدرجات</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-percentage text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900"><?php echo e(number_format($passRate, 1)); ?>%</p>
                    <p class="text-sm text-gray-500">معدل النجاح</p>
                </div>
            </div>
        </div>
    </div>

    <!-- تبويبات المحتوى -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="border-b border-gray-200 bg-gray-50/50">
            <nav class="flex flex-wrap gap-1 p-2" role="tablist">
                <button type="button" @click="activeTab = 'questions'" :class="activeTab === 'questions' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-sm transition-colors border border-gray-200">
                    <i class="fas fa-question-circle"></i>
                    الأسئلة (<?php echo e($exam->examQuestions->count()); ?>)
                </button>
                <button type="button" @click="activeTab = 'attempts'" :class="activeTab === 'attempts' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-sm transition-colors border border-gray-200">
                    <i class="fas fa-users"></i>
                    المحاولات (<?php echo e($exam->attempts->count()); ?>)
                </button>
                <button type="button" @click="activeTab = 'settings'" :class="activeTab === 'settings' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-sm transition-colors border border-gray-200">
                    <i class="fas fa-cogs"></i>
                    الإعدادات
                </button>
                <button type="button" @click="activeTab = 'actions'" :class="activeTab === 'actions' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-sm transition-colors border border-gray-200">
                    <i class="fas fa-tools"></i>
                    الإجراءات
                </button>
            </nav>
        </div>

        <div class="p-6">
            <!-- تبويب الأسئلة -->
            <div x-show="activeTab === 'questions'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                    <h3 class="text-lg font-bold text-gray-900">أسئلة الامتحان</h3>
                    <a href="<?php echo e(route('admin.exams.questions.manage', $exam)); ?>" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-colors">
                        <i class="fas fa-cog"></i>
                        إدارة الأسئلة
                    </a>
                </div>

                <?php if($exam->examQuestions->count() > 0): ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $exam->examQuestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $examQuestion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $diffClass = $examQuestion->question->difficulty_level == 'easy' ? 'bg-green-100 text-green-800' : ($examQuestion->question->difficulty_level == 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                            ?>
                            <div class="flex flex-wrap items-center justify-between gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                                <div class="flex items-center gap-4 min-w-0 flex-1">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0 font-bold"><?php echo e($examQuestion->order); ?></div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900"><?php echo e(Str::limit($examQuestion->question->question ?? '', 100)); ?></p>
                                        <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 mt-1">
                                            <span><?php echo e($examQuestion->question->getTypeLabel() ?? '—'); ?></span>
                                            <span><?php echo e($examQuestion->marks); ?> نقطة</span>
                                            <?php if($examQuestion->question && $examQuestion->question->category): ?>
                                                <span><?php echo e($examQuestion->question->category->name); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold <?php echo e($diffClass); ?>">
                                    <?php echo e($examQuestion->question->getDifficultyLabel() ?? '—'); ?>

                                </span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                        <i class="fas fa-question-circle text-5xl text-gray-300 mb-4"></i>
                        <h4 class="text-lg font-bold text-gray-900 mb-2">لا توجد أسئلة</h4>
                        <p class="text-gray-500 mb-4">ابدأ بإضافة الأسئلة لهذا الامتحان</p>
                        <a href="<?php echo e(route('admin.exams.questions.manage', $exam)); ?>" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                            <i class="fas fa-plus"></i>
                            إضافة أسئلة
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- تبويب المحاولات -->
            <div x-show="activeTab === 'attempts'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak style="display: none;">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                    <h3 class="text-lg font-bold text-gray-900">محاولات الطلاب</h3>
                    <a href="<?php echo e(route('admin.exams.statistics', $exam)); ?>" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-colors">
                        <i class="fas fa-chart-bar"></i>
                        إحصائيات مفصلة
                    </a>
                </div>

                <?php if($exam->attempts->count() > 0): ?>
                    <div class="overflow-x-auto rounded-xl border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الطالب</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">النتيجة</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الوقت</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الحالة</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">التاريخ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__currentLoopData = $exam->attempts->take(20); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attempt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-semibold text-sm"><?php echo e(substr($attempt->user->name ?? '?', 0, 1)); ?></div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900"><?php echo e($attempt->user->name ?? '—'); ?></div>
                                                    <div class="text-xs text-gray-500"><?php echo e($attempt->user->email ?? ''); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if($attempt->status === 'completed'): ?>
                                                <span class="text-sm font-medium text-gray-900"><?php echo e(number_format($attempt->score ?? 0, 1)); ?> / <?php echo e($exam->total_marks); ?></span>
                                                <span class="text-xs text-gray-500 block"><?php echo e(number_format($attempt->percentage ?? 0, 1)); ?>%</span>
                                            <?php else: ?>
                                                <span class="text-sm text-gray-500">لم يكتمل</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo e($attempt->formatted_time ?? '—'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold <?php echo e($attempt->result_color == 'green' ? 'bg-green-100 text-green-800' : ($attempt->result_color == 'red' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')); ?>">
                                                <?php echo e($attempt->result_status ?? '—'); ?>

                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo e($attempt->created_at->format('Y-m-d H:i')); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                        <i class="fas fa-users text-5xl text-gray-300 mb-4"></i>
                        <h4 class="text-lg font-bold text-gray-900 mb-2">لا توجد محاولات</h4>
                        <p class="text-gray-500">لم يقم أي طالب بأداء هذا الامتحان بعد</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- تبويب الإعدادات -->
            <div x-show="activeTab === 'settings'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak style="display: none;">
                <h3 class="text-lg font-bold text-gray-900 mb-6">إعدادات الامتحان</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                        <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-eye text-indigo-600"></i> إعدادات العرض</h4>
                        <ul class="space-y-3 text-sm">
                            <li class="flex items-center justify-between"><span class="text-gray-600">خلط الأسئلة</span><span class="font-semibold <?php echo e($exam->randomize_questions ? 'text-green-600' : 'text-gray-500'); ?>"><?php echo e($exam->randomize_questions ? 'مفعل' : 'معطل'); ?></span></li>
                            <li class="flex items-center justify-between"><span class="text-gray-600">خلط الخيارات</span><span class="font-semibold <?php echo e($exam->randomize_options ? 'text-green-600' : 'text-gray-500'); ?>"><?php echo e($exam->randomize_options ? 'مفعل' : 'معطل'); ?></span></li>
                            <li class="flex items-center justify-between"><span class="text-gray-600">عرض النتائج فوراً</span><span class="font-semibold <?php echo e($exam->show_results_immediately ? 'text-green-600' : 'text-gray-500'); ?>"><?php echo e($exam->show_results_immediately ? 'مفعل' : 'معطل'); ?></span></li>
                            <li class="flex items-center justify-between"><span class="text-gray-600">عرض الإجابات الصحيحة</span><span class="font-semibold <?php echo e($exam->show_correct_answers ? 'text-green-600' : 'text-gray-500'); ?>"><?php echo e($exam->show_correct_answers ? 'مفعل' : 'معطل'); ?></span></li>
                        </ul>
                    </div>
                    <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                        <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-shield-alt text-indigo-600"></i> إعدادات الأمان</h4>
                        <ul class="space-y-3 text-sm">
                            <li class="flex items-center justify-between"><span class="text-gray-600">منع تبديل التبويبات</span><span class="font-semibold <?php echo e($exam->prevent_tab_switch ? 'text-green-600' : 'text-gray-500'); ?>"><?php echo e($exam->prevent_tab_switch ? 'مفعل' : 'معطل'); ?></span></li>
                            <li class="flex items-center justify-between"><span class="text-gray-600">تسليم تلقائي</span><span class="font-semibold <?php echo e($exam->auto_submit ? 'text-green-600' : 'text-gray-500'); ?>"><?php echo e($exam->auto_submit ? 'مفعل' : 'معطل'); ?></span></li>
                            <li class="flex items-center justify-between"><span class="text-gray-600">تتطلب كاميرا</span><span class="font-semibold <?php echo e($exam->require_camera ? 'text-green-600' : 'text-gray-500'); ?>"><?php echo e($exam->require_camera ? 'مطلوبة' : 'غير مطلوبة'); ?></span></li>
                            <li class="flex items-center justify-between"><span class="text-gray-600">تتطلب مايكروفون</span><span class="font-semibold <?php echo e($exam->require_microphone ? 'text-green-600' : 'text-gray-500'); ?>"><?php echo e($exam->require_microphone ? 'مطلوب' : 'غير مطلوب'); ?></span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- تبويب الإجراءات -->
            <div x-show="activeTab === 'actions'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak style="display: none;">
                <h3 class="text-lg font-bold text-gray-900 mb-6">الإجراءات</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="p-5 rounded-2xl border-2 border-gray-200 hover:border-indigo-200 transition-colors">
                        <h4 class="font-bold text-gray-900 mb-1">حالة الامتحان</h4>
                        <p class="text-sm text-gray-500 mb-4">تفعيل أو إيقاف</p>
                        <button type="button" onclick="toggleExamStatus(<?php echo e($exam->id); ?>)" class="w-full <?php echo e($exam->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'); ?> text-white px-4 py-2.5 rounded-xl font-semibold transition-colors">
                            <?php echo e($exam->is_active ? 'إيقاف الامتحان' : 'تفعيل الامتحان'); ?>

                        </button>
                    </div>
                    <div class="p-5 rounded-2xl border-2 border-gray-200 hover:border-indigo-200 transition-colors">
                        <h4 class="font-bold text-gray-900 mb-1">حالة النشر</h4>
                        <p class="text-sm text-gray-500 mb-4">نشر للطلاب</p>
                        <button type="button" onclick="toggleExamPublish(<?php echo e($exam->id); ?>)" class="w-full <?php echo e($exam->is_published ? 'bg-amber-600 hover:bg-amber-700' : 'bg-blue-600 hover:bg-blue-700'); ?> text-white px-4 py-2.5 rounded-xl font-semibold transition-colors">
                            <?php echo e($exam->is_published ? 'إلغاء النشر' : 'نشر الامتحان'); ?>

                        </button>
                    </div>
                    <div class="p-5 rounded-2xl border-2 border-gray-200 hover:border-indigo-200 transition-colors">
                        <h4 class="font-bold text-gray-900 mb-1">معاينة</h4>
                        <p class="text-sm text-gray-500 mb-4">كطالب</p>
                        <a href="<?php echo e(route('admin.exams.preview', $exam)); ?>" class="block w-full text-center bg-teal-600 hover:bg-teal-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-colors">
                            معاينة الامتحان
                        </a>
                    </div>
                    <div class="p-5 rounded-2xl border-2 border-gray-200 hover:border-indigo-200 transition-colors">
                        <h4 class="font-bold text-gray-900 mb-1">نسخ الامتحان</h4>
                        <p class="text-sm text-gray-500 mb-4">إنشاء نسخة</p>
                        <form action="<?php echo e(route('admin.exams.duplicate', $exam)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" onclick="return confirm('هل تريد إنشاء نسخة من هذا الامتحان؟')" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-colors">
                                نسخ الامتحان
                            </button>
                        </form>
                    </div>
                    <div class="p-5 rounded-2xl border-2 border-red-200 bg-red-50/50">
                        <h4 class="font-bold text-red-900 mb-1">حذف الامتحان</h4>
                        <p class="text-sm text-red-700 mb-4">حذف نهائي</p>
                        <form action="<?php echo e(route('admin.exams.destroy', $exam)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" onclick="return confirm('هل أنت متأكد من حذف هذا الامتحان؟ لا يمكن التراجع.');" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-colors">
                                حذف الامتحان
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function toggleExamStatus(examId) {
    if (!confirm('هل تريد تغيير حالة هذا الامتحان؟')) return;
    fetch('/admin/exams/' + examId + '/toggle-status', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) { if (data.success) { location.reload(); } else { alert(data.message || 'حدث خطأ'); } })
    .catch(function() { alert('حدث خطأ'); });
}
function toggleExamPublish(examId) {
    if (!confirm('هل تريد تغيير حالة نشر هذا الامتحان؟')) return;
    fetch('/admin/exams/' + examId + '/toggle-publish', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) { if (data.success) { location.reload(); } else { alert(data.message || 'حدث خطأ'); } })
    .catch(function() { alert('حدث خطأ'); });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/exams/show.blade.php ENDPATH**/ ?>