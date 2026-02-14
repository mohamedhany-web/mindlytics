<?php $__env->startSection('title', 'تفاصيل الكورس البرمجي'); ?>
<?php $__env->startSection('header', 'تفاصيل الكورس: ' . $advancedCourse->title); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <!-- هيدر الصفحة -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.advanced-courses.index')); ?>" class="hover:text-white">الكورسات</a>
                    <span class="mx-2">/</span>
                    <span class="text-white truncate"><?php echo e(Str::limit($advancedCourse->title, 40)); ?></span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1 truncate"><?php echo e($advancedCourse->title); ?></h1>
                <p class="text-sm text-white/90 mt-1">
                    <?php echo e($advancedCourse->academicYear->name ?? '—'); ?> · <?php echo e($advancedCourse->academicSubject->name ?? '—'); ?>

                </p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="<?php echo e(route('admin.advanced-courses.edit', $advancedCourse)); ?>" 
                   class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-edit"></i>
                    تعديل الكورس
                </a>
                <a href="<?php echo e(route('admin.advanced-courses.index')); ?>" 
                   class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-arrow-right"></i>
                    العودة للكورسات
                </a>
            </div>
        </div>
    </div>

    <!-- معلومات أساسية وإحصائيات -->
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- معلومات الكورس -->
        <div class="xl:col-span-3">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-lg font-bold text-gray-900">معلومات الكورس</h3>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo e($advancedCourse->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                            <?php echo e($advancedCourse->is_active ? 'نشط' : 'غير نشط'); ?>

                        </span>
                        <?php if($advancedCourse->is_featured): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-star ml-1"></i>
                                مرشح
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <div class="text-xs font-medium text-gray-500 mb-0.5">العنوان</div>
                            <div class="text-sm font-semibold text-gray-900 truncate" title="<?php echo e($advancedCourse->title); ?>"><?php echo e(Str::limit($advancedCourse->title, 25)); ?></div>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <div class="text-xs font-medium text-gray-500 mb-0.5">السنة / المادة</div>
                            <div class="text-sm font-semibold text-gray-900"><?php echo e($advancedCourse->academicYear->name ?? '—'); ?> · <?php echo e($advancedCourse->academicSubject->name ?? '—'); ?></div>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <div class="text-xs font-medium text-gray-500 mb-0.5">المستوى</div>
                            <div class="text-sm font-semibold text-gray-900">
                                <?php if($advancedCourse->level == 'beginner'): ?> مبتدئ
                                <?php elseif($advancedCourse->level == 'intermediate'): ?> متوسط
                                <?php elseif($advancedCourse->level == 'advanced'): ?> متقدم
                                <?php else: ?> —
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <div class="text-xs font-medium text-gray-500 mb-0.5">السعر / المدة</div>
                            <div class="text-sm font-semibold text-gray-900"><?php echo e(number_format($advancedCourse->price, 0)); ?> ج.م · <?php echo e($advancedCourse->duration_hours); ?> س</div>
                        </div>
                    </div>

                    <?php if($advancedCourse->description): ?>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">الوصف</label>
                            <div class="text-gray-900 bg-gray-50 p-4 rounded-xl border border-gray-100"><?php echo e($advancedCourse->description); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- إحصائيات سريعة -->
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-play-circle text-2xl text-blue-600"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-bold text-gray-900"><?php echo e($stats['total_lessons']); ?></p>
                        <p class="text-sm text-gray-500">دروس</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-users text-2xl text-green-600"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-bold text-gray-900"><?php echo e($stats['active_students']); ?></p>
                        <p class="text-sm text-gray-500">طالب نشط</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-clock text-2xl text-amber-600"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-bold text-gray-900"><?php echo e($stats['pending_orders']); ?></p>
                        <p class="text-sm text-gray-500">طلب معلق</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-purple-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-film text-2xl text-purple-600"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-2xl font-bold text-gray-900"><?php echo e(floor($stats['total_duration'] / 60)); ?></p>
                        <p class="text-sm text-gray-500">ساعة محتوى</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- تبويبات المحتوى -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden" x-data="{ activeTab: 'lessons' }">
        <div class="border-b border-gray-200 bg-gray-50/50">
            <nav class="flex flex-wrap gap-1 sm:gap-0 sm:space-x-8 sm:space-x-reverse px-4 sm:px-6">
                <button type="button" @click="activeTab = 'lessons'" 
                        :class="activeTab === 'lessons' ? 'border-indigo-500 text-indigo-600 bg-white shadow-sm' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-3 sm:px-1 border-b-2 font-medium text-sm transition-colors rounded-t-lg">
                    <i class="fas fa-play-circle ml-2"></i>
                    الدروس (<?php echo e($stats['total_lessons']); ?>)
                </button>
                <button type="button" @click="activeTab = 'students'" 
                        :class="activeTab === 'students' ? 'border-indigo-500 text-indigo-600 bg-white shadow-sm' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-3 sm:px-1 border-b-2 font-medium text-sm transition-colors rounded-t-lg">
                    <i class="fas fa-users ml-2"></i>
                    الطلاب (<?php echo e($stats['total_students']); ?>)
                </button>
                <button type="button" @click="activeTab = 'orders'" 
                        :class="activeTab === 'orders' ? 'border-indigo-500 text-indigo-600 bg-white shadow-sm' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-3 sm:px-1 border-b-2 font-medium text-sm transition-colors rounded-t-lg">
                    <i class="fas fa-shopping-cart ml-2"></i>
                    الطلبات (<?php echo e($advancedCourse->orders->count()); ?>)
                </button>
                <button type="button" @click="activeTab = 'actions'" 
                        :class="activeTab === 'actions' ? 'border-indigo-500 text-indigo-600 bg-white shadow-sm' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-3 sm:px-1 border-b-2 font-medium text-sm transition-colors rounded-t-lg">
                    <i class="fas fa-cogs ml-2"></i>
                    الإجراءات
                </button>
            </nav>
        </div>

        <div class="p-6 sm:p-8">
            <!-- تبويب الدروس -->
            <div x-show="activeTab === 'lessons'">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <h4 class="text-lg font-bold text-gray-900">دروس الكورس</h4>
                    <a href="<?php echo e(route('admin.courses.lessons.create', $advancedCourse)); ?>" 
                       class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-colors">
                        <i class="fas fa-plus"></i>
                        إضافة درس
                    </a>
                </div>

                <?php if($advancedCourse->lessons->count() > 0): ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $advancedCourse->lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $lessonIconBg = $lesson->type == 'video' ? 'bg-blue-100' : ($lesson->type == 'document' ? 'bg-green-100' : ($lesson->type == 'quiz' ? 'bg-yellow-100' : 'bg-purple-100'));
                                $lessonIconFa = $lesson->type == 'video' ? 'fa-play text-blue-600' : ($lesson->type == 'document' ? 'fa-file-alt text-green-600' : ($lesson->type == 'quiz' ? 'fa-question-circle text-yellow-600' : 'fa-tasks text-purple-600'));
                            ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100 hover:bg-gray-50/80 transition-colors">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 <?php echo e($lessonIconBg); ?>">
                                        <i class="fas <?php echo e($lessonIconFa); ?>"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-medium text-gray-900 truncate"><?php echo e($lesson->title); ?></p>
                                        <p class="text-sm text-gray-500">
                                            <?php echo e($lesson->duration_minutes ?? 0); ?> دقيقة ·
                                            <?php if($lesson->type == 'video'): ?> فيديو
                                            <?php elseif($lesson->type == 'document'): ?> مستند
                                            <?php elseif($lesson->type == 'quiz'): ?> كويز
                                            <?php else: ?> واجب
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?php echo e($lesson->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                        <?php echo e($lesson->is_active ? 'نشط' : 'غير نشط'); ?>

                                    </span>
                                    <a href="<?php echo e(route('admin.courses.lessons.show', [$advancedCourse, $lesson])); ?>" class="p-2 text-gray-400 hover:text-indigo-600 rounded-lg transition-colors" title="عرض"><i class="fas fa-eye"></i></a>
                                    <button type="button" onclick="toggleLessonStatus(<?php echo e($lesson->id); ?>)" class="p-2 text-gray-400 hover:text-amber-600 rounded-lg transition-colors" title="<?php echo e($lesson->is_active ? 'إيقاف' : 'تفعيل'); ?>"><i class="fas fa-power-off"></i></button>
                                    <a href="<?php echo e(route('admin.courses.lessons.edit', [$advancedCourse, $lesson])); ?>" class="p-2 text-gray-400 hover:text-indigo-600 rounded-lg transition-colors" title="تعديل"><i class="fas fa-edit"></i></a>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12 px-4">
                        <div class="w-20 h-20 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-play-circle text-4xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">لا توجد دروس</h3>
                        <p class="text-gray-500 mb-6">ابدأ بإضافة الدروس لهذا الكورس</p>
                        <a href="<?php echo e(route('admin.courses.lessons.create', $advancedCourse)); ?>" 
                           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                            <i class="fas fa-plus"></i>
                            إضافة أول درس
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- تبويب الطلاب -->
            <div x-show="activeTab === 'students'" style="display: none;">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <h4 class="text-lg font-bold text-gray-900">الطلاب المسجلين</h4>
                    <a href="<?php echo e(route('admin.advanced-courses.students', $advancedCourse)); ?>" 
                       class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-colors">
                        <i class="fas fa-user-plus"></i>
                        إضافة طالب
                    </a>
                </div>

                <?php if($advancedCourse->enrollments->count() > 0): ?>
                    <div class="overflow-x-auto rounded-xl border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الطالب</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التقدم</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ التسجيل</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__currentLoopData = $advancedCourse->enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                                    <span class="text-indigo-600 font-semibold">
                                                        <?php echo e(substr($enrollment->student->name, 0, 1)); ?>

                                                    </span>
                                                </div>
                                                <div class="mr-4">
                                                    <div class="text-sm font-medium text-gray-900"><?php echo e($enrollment->student->name); ?></div>
                                                    <div class="text-sm text-gray-500"><?php echo e($enrollment->student->email); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php
                                                $enrClass = $enrollment->status == 'active' ? 'bg-green-100 text-green-800' : ($enrollment->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : ($enrollment->status == 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800'));
                                            ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($enrClass); ?>">
                                                <?php echo e($enrollment->status_text); ?>

                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                                    <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: <?php echo e($enrollment->progress); ?>%"></div>
                                                </div>
                                                <span class="text-sm text-gray-900"><?php echo e($enrollment->progress); ?>%</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo e($enrollment->enrolled_at ? $enrollment->enrolled_at->format('Y-m-d') : 'غير محدد'); ?>

                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="<?php echo e(route('admin.online-enrollments.show', $enrollment)); ?>" 
                                               class="text-indigo-600 hover:text-indigo-800 font-semibold">عرض</a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12 px-4">
                        <div class="w-20 h-20 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-4xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">لا يوجد طلاب</h3>
                        <p class="text-gray-500 mb-4">لم يتم تسجيل أي طالب في هذا الكورس بعد</p>
                        <a href="<?php echo e(route('admin.advanced-courses.students', $advancedCourse)); ?>" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                            <i class="fas fa-user-plus"></i> إضافة طالب
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- تبويب الطلبات -->
            <div x-show="activeTab === 'orders'" style="display: none;">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <h4 class="text-lg font-bold text-gray-900">طلبات التسجيل</h4>
                    <a href="<?php echo e(route('admin.orders.index')); ?>?course_id=<?php echo e($advancedCourse->id); ?>" 
                       class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-colors">
                        <i class="fas fa-external-link-alt"></i>
                        عرض جميع الطلبات
                    </a>
                </div>

                <?php if($advancedCourse->orders->count() > 0): ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $advancedCourse->orders->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <span class="text-indigo-600 font-semibold">
                                            <?php echo e(substr($order->user->name, 0, 1)); ?>

                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900"><?php echo e($order->user->name); ?></p>
                                        <p class="text-sm text-gray-500"><?php echo e($order->created_at->diffForHumans()); ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <?php $orderClass = $order->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : ($order->status == 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($orderClass); ?>">
                                        <?php echo e($order->status_text); ?>

                                    </span>
                                    <a href="<?php echo e(route('admin.orders.show', $order)); ?>" 
                                       class="text-indigo-600 hover:text-indigo-800 font-semibold">
                                        <i class="fas fa-eye ml-1"></i> عرض
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12 px-4">
                        <div class="w-20 h-20 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shopping-cart text-4xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">لا توجد طلبات</h3>
                        <p class="text-gray-500">لا توجد طلبات تسجيل لهذا الكورس</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- تبويب الإجراءات -->
            <div x-show="activeTab === 'actions'" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <!-- تفعيل/إيقاف الكورس -->
                    <div class="p-5 border border-gray-200 rounded-2xl bg-white shadow-sm">
                        <h5 class="font-bold text-gray-900 mb-2">حالة الكورس</h5>
                        <p class="text-sm text-gray-500 mb-4">تفعيل أو إيقاف الكورس للطلاب</p>
                        <button type="button" onclick="toggleCourseStatus(<?php echo e($advancedCourse->id); ?>)" 
                                class="w-full <?php echo e($advancedCourse->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'); ?> text-white px-4 py-3 rounded-xl font-semibold transition-colors">
                            <?php echo e($advancedCourse->is_active ? 'إيقاف الكورس' : 'تفعيل الكورس'); ?>

                        </button>
                    </div>

                    <!-- ترشيح الكورس -->
                    <div class="p-5 border border-gray-200 rounded-2xl bg-white shadow-sm">
                        <h5 class="font-bold text-gray-900 mb-2">ترشيح الكورس</h5>
                        <p class="text-sm text-gray-500 mb-4">عرض الكورس في القائمة المرشحة</p>
                        <button type="button" onclick="toggleCourseFeatured(<?php echo e($advancedCourse->id); ?>)" 
                                class="w-full <?php echo e($advancedCourse->is_featured ? 'bg-amber-600 hover:bg-amber-700' : 'bg-blue-600 hover:bg-blue-700'); ?> text-white px-4 py-3 rounded-xl font-semibold transition-colors">
                            <?php echo e($advancedCourse->is_featured ? 'إلغاء الترشيح' : 'ترشيح الكورس'); ?>

                        </button>
                    </div>

                    <!-- نسخ الكورس -->
                    <div class="p-5 border border-gray-200 rounded-2xl bg-white shadow-sm">
                        <h5 class="font-bold text-gray-900 mb-2">نسخ الكورس</h5>
                        <p class="text-sm text-gray-500 mb-4">إنشاء نسخة من الكورس والدروس</p>
                        <form action="<?php echo e(route('admin.advanced-courses.duplicate', $advancedCourse)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" 
                                    onclick="return confirm('هل تريد إنشاء نسخة من هذا الكورس؟')"
                                    class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-xl font-semibold transition-colors">
                                نسخ الكورس
                            </button>
                        </form>
                    </div>

                    <!-- إحصائيات متقدمة -->
                    <div class="p-5 border border-gray-200 rounded-2xl bg-white shadow-sm">
                        <h5 class="font-bold text-gray-900 mb-2">الإحصائيات</h5>
                        <p class="text-sm text-gray-500 mb-4">عرض إحصائيات مفصلة للكورس</p>
                        <a href="<?php echo e(route('admin.advanced-courses.statistics', $advancedCourse)); ?>" 
                           class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-xl font-semibold transition-colors block text-center">
                            عرض الإحصائيات
                        </a>
                    </div>

                    <!-- إدارة الدروس -->
                    <div class="p-5 border border-gray-200 rounded-2xl bg-white shadow-sm">
                        <h5 class="font-bold text-gray-900 mb-2">إدارة الدروس</h5>
                        <p class="text-sm text-gray-500 mb-4">إضافة وتعديل دروس الكورس</p>
                        <a href="<?php echo e(route('admin.courses.lessons.index', $advancedCourse)); ?>" 
                           class="w-full bg-teal-600 hover:bg-teal-700 text-white px-4 py-3 rounded-xl font-semibold transition-colors block text-center">
                            إدارة الدروس
                        </a>
                    </div>

                    <!-- حذف الكورس -->
                    <div class="p-5 border border-red-200 rounded-2xl bg-red-50 shadow-sm">
                        <h5 class="font-bold text-red-900 mb-2">حذف الكورس</h5>
                        <p class="text-sm text-red-700 mb-4">حذف الكورس نهائياً (لا يمكن التراجع)</p>
                        <form action="<?php echo e(route('admin.advanced-courses.destroy', $advancedCourse)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" 
                                    onclick="return confirm('هل أنت متأكد من حذف هذا الكورس؟ هذا الإجراء لا يمكن التراجع عنه!')"
                                    class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-xl font-semibold transition-colors">
                                حذف الكورس
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
function toggleCourseStatus(courseId) {
    if (confirm('هل تريد تغيير حالة هذا الكورس؟')) {
        fetch(`/admin/advanced-courses/${courseId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ في تغيير حالة الكورس');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في تغيير حالة الكورس');
        });
    }
}

function toggleCourseFeatured(courseId) {
    if (confirm('هل تريد تغيير حالة ترشيح هذا الكورس؟')) {
        fetch(`/admin/advanced-courses/${courseId}/toggle-featured`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ في تغيير حالة الترشيح');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في تغيير حالة الترشيح');
        });
    }
}

function toggleLessonStatus(lessonId) {
    if (confirm('هل تريد تغيير حالة هذا الدرس؟')) {
        fetch(`/admin/courses/<?php echo e($advancedCourse->id); ?>/lessons/${lessonId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ في تغيير حالة الدرس');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في تغيير حالة الدرس');
        });
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/advanced-courses/show.blade.php ENDPATH**/ ?>