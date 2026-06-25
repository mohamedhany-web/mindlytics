<?php $__env->startSection('title', 'الوظائف — HR'); ?>
<?php $__env->startSection('header', 'الوظائف — HR'); ?>

<?php $__env->startSection('content'); ?>

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.hr._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.hr._nav', ['active' => 'jobs'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.hr._page-header', [
        'title' => 'الوظائف',
        'subtitle' => 'أنشئ وظيفة، انشرها، ثم راقب التقديمات والتقييم داخل نظام ATS.',
        'icon' => 'fas fa-briefcase',
        'actions' => '<a href="' . route('admin.hr.jobs.create') . '" class="' . $hrBtnPrimary . '"><i class="fas fa-plus"></i> وظيفة جديدة</a>',
        'statCards' => [
            ['label' => 'إجمالي الوظائف', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-briefcase', 'bg' => 'bg-pink-100', 'text' => 'text-pink-600', 'description' => 'كل الوظائف المسجلة'],
            ['label' => 'مفتوحة', 'value' => number_format($stats['open'] ?? 0), 'icon' => 'fas fa-door-open', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => 'تقبل التقديم'],
            ['label' => 'منشورة', 'value' => number_format($stats['published'] ?? 0), 'icon' => 'fas fa-globe', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'description' => 'ظاهرة في صفحة التوظيف'],
            ['label' => 'طلبات التوظيف', 'value' => number_format($stats['applications'] ?? 0), 'icon' => 'fas fa-inbox', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'description' => 'إجمالي التقديمات'],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="<?php echo e($hrSectionClass); ?>">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-filter text-pink-600"></i>
                البحث والفلترة
            </h3>
        </div>
        <div class="p-5">
            <form method="get" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <div class="lg:col-span-2">
                    <label class="<?php echo e($hrLabelClass); ?>">بحث</label>
                    <input name="search" value="<?php echo e(request('search')); ?>" placeholder="عنوان / قسم / مكان…" class="<?php echo e($hrInputClass); ?>">
                </div>
                <div>
                    <label class="<?php echo e($hrLabelClass); ?>">النشر</label>
                    <select name="published" class="<?php echo e($hrSelectClass); ?>">
                        <option value="">الكل</option>
                        <option value="1" <?php if(request('published') === '1'): echo 'selected'; endif; ?>>منشور</option>
                        <option value="0" <?php if(request('published') === '0'): echo 'selected'; endif; ?>>غير منشور</option>
                    </select>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="<?php echo e($hrBtnDark); ?>"><i class="fas fa-search"></i> بحث</button>
                    <a href="<?php echo e(route('admin.hr.jobs.index')); ?>" class="<?php echo e($hrBtnSecondary); ?>">إعادة تعيين</a>
                </div>
            </form>
        </div>
    </section>

    <section class="<?php echo e($hrSectionClass); ?>">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/80">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-list text-pink-600"></i>
                قائمة الوظائف
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">الوظيفة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">القسم / المكان</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">منشور</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">طلبات</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php $__empty_1 = true; $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900"><?php echo e($job->title); ?></div>
                                <div class="text-xs text-slate-500 mt-0.5"><?php echo e($job->employment_type ?: '—'); ?></div>
                            </td>
                            <td class="px-6 py-4 text-slate-700">
                                <div><?php echo e($job->department ?: '—'); ?></div>
                                <div class="text-xs text-slate-500"><?php echo e($job->location ?: '—'); ?></div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if($job->is_published): ?>
                                    <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">منشور</span>
                                <?php else: ?>
                                    <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">مسودة</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center font-black text-slate-900 tabular-nums"><?php echo e($job->applications_count ?? 0); ?></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?php echo e(route('careers.show', $job)); ?>" target="_blank" class="<?php echo e($hrBtnSecondary); ?> !px-3 !py-2 text-xs">
                                        <i class="fas fa-external-link-alt"></i>
                                        عامة
                                    </a>
                                    <a href="<?php echo e(route('admin.hr.jobs.edit', $job)); ?>" class="<?php echo e($hrBtnPrimary); ?> !px-3 !py-2 text-xs">
                                        <i class="fas fa-edit"></i>
                                        تعديل
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500">لا توجد وظائف حتى الآن.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-200"><?php echo e($jobs->links()); ?></div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/hr/jobs/index.blade.php ENDPATH**/ ?>