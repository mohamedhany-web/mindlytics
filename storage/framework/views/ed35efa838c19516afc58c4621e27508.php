

<?php $__env->startSection('title', 'طلبات التوظيف — HR'); ?>
<?php $__env->startSection('header', 'طلبات التوظيف — HR'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.hr._shared', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php
    $statusLabels = \App\Models\HrJobApplication::STATUSES;
    $statusBadges = [
        'new' => 'bg-sky-100 text-sky-700 border-sky-200',
        'screening' => 'bg-amber-100 text-amber-700 border-amber-200',
        'interview' => 'bg-violet-100 text-violet-700 border-violet-200',
        'offer' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
        'hired' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'rejected' => 'bg-rose-100 text-rose-700 border-rose-200',
    ];
?>

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.hr._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.hr._nav', ['active' => 'applications'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.hr._page-header', [
        'title' => 'طلبات التوظيف',
        'subtitle' => 'فلترة حسب الوظيفة، الحالة، والحد الأدنى للتقييم.',
        'icon' => 'fas fa-inbox',
        'statCards' => [
            ['label' => 'إجمالي التقديمات', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-inbox', 'bg' => 'bg-pink-100', 'text' => 'text-pink-600'],
            ['label' => 'جديد', 'value' => number_format($stats['new'] ?? 0), 'icon' => 'fas fa-star', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600'],
            ['label' => 'مقابلة', 'value' => number_format($stats['interview'] ?? 0), 'icon' => 'fas fa-user-tie', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600'],
            ['label' => 'تم التعيين', 'value' => number_format($stats['hired'] ?? 0), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
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
            <form method="get" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="<?php echo e($hrLabelClass); ?>">بحث</label>
                    <input name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم / إيميل / هاتف…" class="<?php echo e($hrInputClass); ?>">
                </div>
                <div>
                    <label class="<?php echo e($hrLabelClass); ?>">الوظيفة</label>
                    <select name="job_id" class="<?php echo e($hrSelectClass); ?>">
                        <option value="">الكل</option>
                        <?php $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($job->id); ?>" <?php if((string) request('job_id') === (string) $job->id): echo 'selected'; endif; ?>><?php echo e($job->title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="<?php echo e($hrLabelClass); ?>">الحالة</label>
                    <select name="status" class="<?php echo e($hrSelectClass); ?>">
                        <option value="">الكل</option>
                        <?php $__currentLoopData = $statusLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($k); ?>" <?php if((string) request('status') === (string) $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="<?php echo e($hrLabelClass); ?>">الحد الأدنى للتقييم</label>
                    <input name="min_score" value="<?php echo e(request('min_score')); ?>" type="number" step="0.01" class="<?php echo e($hrInputClass); ?>">
                </div>
                <div class="md:col-span-2 lg:col-span-4 flex flex-wrap gap-2">
                    <button type="submit" class="<?php echo e($hrBtnDark); ?>"><i class="fas fa-search"></i> بحث</button>
                    <a href="<?php echo e(route('admin.hr.applications.index')); ?>" class="<?php echo e($hrBtnSecondary); ?>">إعادة تعيين</a>
                </div>
            </form>
        </div>
    </section>

    <section class="<?php echo e($hrSectionClass); ?>">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/80">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-users text-pink-600"></i>
                قائمة المتقدمين
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">المتقدم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">الوظيفة</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">التقييم</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php $__empty_1 = true; $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900"><?php echo e($app->full_name); ?></div>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    <?php echo e($app->email ?: '—'); ?><?php if($app->phone): ?> · <?php echo e($app->phone); ?><?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-800 font-semibold"><?php echo e($app->job?->title ?: '—'); ?></td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold border <?php echo e($statusBadges[$app->status] ?? 'bg-slate-100 text-slate-700 border-slate-200'); ?>">
                                    <?php echo e($statusLabels[$app->status] ?? $app->status); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-black text-slate-900 tabular-nums">
                                <?php echo e($app->score?->total_score !== null ? number_format((float) $app->score->total_score, 2) : '—'); ?>

                            </td>
                            <td class="px-6 py-4 text-left">
                                <a href="<?php echo e(route('admin.hr.applications.show', $app)); ?>" class="<?php echo e($hrBtnPrimary); ?> !px-3 !py-2 text-xs">
                                    <i class="fas fa-eye"></i>
                                    فتح
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500">لا توجد تقديمات.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-200"><?php echo e($applications->links()); ?></div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\hr\applications\index.blade.php ENDPATH**/ ?>