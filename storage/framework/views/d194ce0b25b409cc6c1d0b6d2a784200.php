<?php $__env->startSection('title', 'كورساتي - Mindlytics'); ?>
<?php $__env->startSection('header', 'كورساتي'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-1">كورساتي</h1>
        <p class="text-sm text-slate-500">الكورسات التي تم تعيينها لك</p>
    </div>

    <!-- الإحصائيات -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">إجمالي الكورسات</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800"><?php echo e($stats['total'] ?? 0); ?></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 flex items-center justify-center">
                <i class="fas fa-book text-sky-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">نشطة</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800"><?php echo e($stats['active'] ?? 0); ?></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
                <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">معطلة</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800"><?php echo e($stats['inactive'] ?? 0); ?></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
                <i class="fas fa-ban text-amber-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">إجمالي الطلاب</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800"><?php echo e($stats['total_students'] ?? 0); ?></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-violet-50 flex items-center justify-center">
                <i class="fas fa-user-graduate text-violet-600 text-lg"></i>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 shadow-sm">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-semibold text-slate-700 mb-2">البحث</label>
                <input type="text" name="search" id="search" value="<?php echo e(request('search')); ?>" 
                       placeholder="البحث في عناوين الكورسات..."
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
            </div>
            <div>
                <label for="status" class="block text-sm font-semibold text-slate-700 mb-2">الحالة</label>
                <select name="status" id="status" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                    <option value="">جميع الحالات</option>
                    <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>نشط</option>
                    <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>معطل</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-search"></i>
                    <span>بحث</span>
                </button>
                <?php if(request()->anyFilled(['search', 'status'])): ?>
                    <a href="<?php echo e(route('instructor.courses.index')); ?>" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors inline-flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- قائمة الكورسات -->
    <?php if($courses->count() > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-2xl overflow-hidden bg-white border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                <div class="px-5 py-4 border-b border-slate-200">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="text-lg font-bold text-slate-800 truncate flex-1"><?php echo e($course->title); ?></h3>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold shrink-0 <?php echo e($course->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'); ?>">
                            <i class="fas <?php echo e($course->is_active ? 'fa-check-circle' : 'fa-ban'); ?>"></i>
                            <?php echo e($course->is_active ? 'نشط' : 'معطل'); ?>

                        </span>
                    </div>
                </div>

                <div class="px-5 py-4">
                    <?php if($course->description): ?>
                        <p class="text-sm text-slate-600 mb-4 line-clamp-2"><?php echo e(Str::limit($course->description, 100)); ?></p>
                    <?php endif; ?>

                    <div class="space-y-2 mb-4">
                        <?php if($course->academicYear): ?>
                        <div class="flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-graduation-cap text-sky-600 text-xs"></i>
                            </div>
                            <span class="text-slate-500">السنة:</span>
                            <span class="text-slate-800 font-medium"><?php echo e($course->academicYear->name); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($course->academicSubject): ?>
                        <div class="flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-book text-violet-600 text-xs"></i>
                            </div>
                            <span class="text-slate-500">المادة:</span>
                            <span class="text-slate-800 font-medium"><?php echo e($course->academicSubject->name); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($course->programming_language): ?>
                        <div class="flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-cyan-50 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-code text-cyan-600 text-xs"></i>
                            </div>
                            <span class="text-slate-500">اللغة:</span>
                            <span class="text-slate-800 font-medium"><?php echo e($course->programming_language); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($course->level): ?>
                        <div class="flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-signal text-emerald-600 text-xs"></i>
                            </div>
                            <span class="text-slate-500">المستوى:</span>
                            <span class="text-slate-800 font-medium">
                                <?php if($course->level == 'beginner'): ?> مبتدئ
                                <?php elseif($course->level == 'intermediate'): ?> متوسط
                                <?php else: ?> متقدم
                                <?php endif; ?>
                            </span>
                        </div>
                        <?php endif; ?>
                        <?php if($course->price && $course->price > 0): ?>
                        <div class="flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-money-bill-wave text-amber-600 text-xs"></i>
                            </div>
                            <span class="text-slate-500">السعر:</span>
                            <span class="text-slate-800 font-semibold"><?php echo e(number_format($course->price, 2)); ?> ج.م</span>
                        </div>
                        <?php else: ?>
                        <div class="flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-gift text-emerald-600 text-xs"></i>
                            </div>
                            <span class="text-emerald-600 font-semibold">مجاني</span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="px-5 py-3 bg-slate-50/80 border-t border-slate-200">
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div>
                            <div class="text-lg font-bold text-slate-800"><?php echo e($course->lectures_count ?? 0); ?></div>
                            <div class="text-xs text-slate-500 font-medium">محاضرة</div>
                        </div>
                        <div>
                            <div class="text-lg font-bold text-slate-800"><?php echo e($course->enrollments_count ?? 0); ?></div>
                            <div class="text-xs text-slate-500 font-medium">طالب</div>
                        </div>
                    </div>
                </div>

                <div class="px-5 py-4 border-t border-slate-200">
                    <a href="<?php echo e(route('instructor.courses.show', $course)); ?>" 
                       class="w-full inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2.5 rounded-xl font-semibold transition-colors">
                        <i class="fas fa-eye"></i>
                        <span>عرض التفاصيل</span>
                    </a>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="mt-6 flex justify-center">
            <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-sm">
                <?php echo e($courses->links()); ?>

            </div>
        </div>
    <?php else: ?>
        <div class="rounded-2xl p-12 sm:p-16 text-center bg-white border border-slate-200 shadow-sm">
            <div class="w-24 h-24 rounded-2xl bg-sky-50 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-book-open text-4xl text-sky-500"></i>
            </div>
            <h3 class="text-xl sm:text-2xl font-bold text-slate-800 mb-2">لا توجد كورسات</h3>
            <p class="text-slate-500 max-w-md mx-auto">لم يتم تعيين أي كورسات لك بعد</p>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/courses/index.blade.php ENDPATH**/ ?>