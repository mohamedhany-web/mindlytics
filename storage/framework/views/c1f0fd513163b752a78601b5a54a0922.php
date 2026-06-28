

<?php $__env->startSection('title', 'طلبات التوظيف — HR'); ?>
<?php $__env->startSection('header', 'طلبات التوظيف — HR'); ?>

<?php $__env->startSection('content'); ?>

<?php
    $statusLabels = \App\Models\HrJobApplication::STATUSES;
    $statusBadges = [
        'applied' => 'bg-sky-100 text-sky-700 border-sky-200',
        'under_review' => 'bg-amber-100 text-amber-700 border-amber-200',
        'interview' => 'bg-violet-100 text-violet-700 border-violet-200',
        'accepted' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'rejected' => 'bg-rose-100 text-rose-700 border-rose-200',
    ];
?>

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.hr._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.hr._nav', ['active' => 'applications'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.hr._page-header', [
        'title' => 'طلبات التوظيف — ATS',
        'subtitle' => 'التقديمات مجمّعة حسب الوظيفة ومرتّبة تلقائياً حسب السكور (Rule-Based).',
        'icon' => 'fas fa-inbox',
        'statCards' => [
            ['label' => 'إجمالي التقديمات', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-inbox', 'bg' => 'bg-pink-100', 'text' => 'text-pink-600'],
            ['label' => 'تم التقديم', 'value' => number_format($stats['applied'] ?? 0), 'icon' => 'fas fa-star', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600'],
            ['label' => 'مقابلة', 'value' => number_format($stats['interview'] ?? 0), 'icon' => 'fas fa-user-tie', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600'],
            ['label' => 'مقبول', 'value' => number_format($stats['accepted'] ?? 0), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
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
            <form method="get" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="<?php echo e($hrLabelClass); ?>">بحث</label>
                    <input name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم / إيميل / هاتف…" class="<?php echo e($hrInputClass); ?>">
                </div>
                <div>
                    <label class="<?php echo e($hrLabelClass); ?>">الوظيفة</label>
                    <select name="job_id" class="<?php echo e($hrSelectClass); ?>">
                        <option value="">الكل</option>
                        <?php $__currentLoopData = $allJobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                    <label class="<?php echo e($hrLabelClass); ?>">المهارة</label>
                    <input name="skill" value="<?php echo e(request('skill')); ?>" placeholder="Excel, SQL…" class="<?php echo e($hrInputClass); ?>">
                </div>
                <div>
                    <label class="<?php echo e($hrLabelClass); ?>">الحد الأدنى للسكور</label>
                    <input name="min_score" value="<?php echo e(request('min_score')); ?>" type="number" step="0.01" min="0" max="100" placeholder="80" class="<?php echo e($hrInputClass); ?>">
                </div>
                <div>
                    <label class="<?php echo e($hrLabelClass); ?>">الحد الأدنى للخبرة (سنوات)</label>
                    <input name="min_experience" value="<?php echo e(request('min_experience')); ?>" type="number" step="0.5" min="0" placeholder="2" class="<?php echo e($hrInputClass); ?>">
                </div>
                <div>
                    <label class="<?php echo e($hrLabelClass); ?>">المؤهل الدراسي</label>
                    <select name="education" class="<?php echo e($hrSelectClass); ?>">
                        <option value="">الكل</option>
                        <?php $__currentLoopData = $educationLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($k); ?>" <?php if((string) request('education') === (string) $k): echo 'selected'; endif; ?>><?php echo e($meta['label'] ?? $k); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="md:col-span-2 lg:col-span-3 flex flex-wrap gap-2">
                    <button type="submit" class="<?php echo e($hrBtnDark); ?>"><i class="fas fa-search"></i> بحث</button>
                    <a href="<?php echo e(route('admin.hr.applications.index')); ?>" class="<?php echo e($hrBtnSecondary); ?>">إعادة تعيين</a>
                </div>
            </form>
        </div>
    </section>

    <div class="space-y-4">
        <?php $__empty_1 = true; $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <section class="<?php echo e($hrSectionClass); ?> overflow-hidden">
                <details class="group" <?php if($loop->first): ?> open <?php endif; ?>>
                    <summary class="cursor-pointer list-none px-5 py-4 border-b border-slate-200 bg-gradient-to-r from-pink-50 to-sky-50 hover:from-pink-100/80 hover:to-sky-100/80 transition-colors">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-pink-600 to-rose-500 flex items-center justify-center text-white shadow-md shrink-0">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-lg font-black text-slate-900 truncate"><?php echo e($job->title); ?></h3>
                                    <p class="text-xs text-slate-600 mt-0.5">
                                        <?php if($job->normalizedRequiredSkills() !== []): ?>
                                            مهارات: <?php echo e(implode(' · ', array_slice($job->normalizedRequiredSkills(), 0, 4))); ?>

                                        <?php else: ?>
                                            <?php echo e($job->department ?: '—'); ?>

                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-sm font-bold text-slate-800">
                                    <i class="fas fa-users text-pink-600 text-xs"></i>
                                    <?php echo e($job->applications_count); ?> متقدم
                                </span>
                                <i class="fas fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
                            </div>
                        </div>
                    </summary>

                    <div class="overflow-x-auto">
                        <?php if($job->applications->isEmpty()): ?>
                            <div class="px-6 py-10 text-center text-slate-500 text-sm">لا توجد تقديمات لهذه الوظيفة ضمن الفلتر الحالي.</div>
                        <?php else: ?>
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">#</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">المتقدم</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">السكور</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">الخبرة</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">الحالة</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    <?php $__currentLoopData = $job->applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rank => $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="hover:bg-slate-50/70 transition-colors">
                                            <td class="px-6 py-4 text-slate-400 font-bold tabular-nums"><?php echo e($rank + 1); ?></td>
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-slate-900"><?php echo e($app->full_name); ?></div>
                                                <div class="text-xs text-slate-500 mt-0.5">
                                                    <?php echo e($app->email ?: '—'); ?><?php if($app->phone): ?> · <?php echo e($app->phone); ?><?php endif; ?>
                                                </div>
                                                <?php if($app->normalizedParsedSkills() !== []): ?>
                                                    <div class="flex flex-wrap gap-1 mt-2">
                                                        <?php $__currentLoopData = array_slice($app->normalizedParsedSkills(), 0, 4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600"><?php echo e($sk); ?></span>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <?php $score = $app->displayScore(); ?>
                                                <?php if($score !== null): ?>
                                                    <span class="inline-flex items-center justify-center min-w-[3rem] px-2.5 py-1 rounded-lg text-sm font-black <?php echo e($score >= 80 ? 'bg-emerald-100 text-emerald-700' : ($score >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700')); ?>">
                                                        <?php echo e(number_format($score, 0)); ?>

                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-slate-400">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 text-center text-slate-700 tabular-nums">
                                                <?php echo e($app->parsed_experience_years !== null ? number_format((float) $app->parsed_experience_years, 1).' سنة' : '—'); ?>

                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold border <?php echo e($statusBadges[$app->status] ?? 'bg-slate-100 text-slate-700 border-slate-200'); ?>">
                                                    <?php echo e($statusLabels[$app->status] ?? $app->status); ?>

                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-left">
                                                <div class="flex flex-wrap items-center justify-end gap-2">
                                                    <a href="<?php echo e(route('admin.hr.applications.show', $app)); ?>" class="<?php echo e($hrBtnPrimary); ?> !px-3 !py-2 text-xs">
                                                        <i class="fas fa-eye"></i> فتح
                                                    </a>
                                                    <form method="post" action="<?php echo e(route('admin.hr.applications.destroy', $app)); ?>"
                                                          onsubmit="return confirm('حذف تقديم «<?php echo e(addslashes($app->full_name)); ?>»؟');" class="inline">
                                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100">
                                                            <i class="fas fa-trash-alt"></i> حذف
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </details>
            </section>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <section class="<?php echo e($hrSectionClass); ?>">
                <div class="px-6 py-14 text-center text-slate-500">
                    <i class="fas fa-inbox text-4xl text-slate-300 mb-3"></i>
                    <p class="font-semibold">لا توجد وظائف أو تقديمات مطابقة للبحث.</p>
                </div>
            </section>
        <?php endif; ?>
    </div>

    <?php if($jobs->hasPages()): ?>
        <div class="flex justify-center"><?php echo e($jobs->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/hr/applications/index.blade.php ENDPATH**/ ?>