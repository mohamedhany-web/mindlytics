

<?php $__env->startSection('title', 'سجلات التحقق الثنائي (2FA) - Mindlytics'); ?>
<?php $__env->startSection('header', 'تسجيلات الدخول (2FA)'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statCards = [
        [
            'label' => 'إجمالي السجلات',
            'value' => number_format($stats['total'] ?? 0),
            'icon' => 'fas fa-shield-alt',
            'color' => 'text-sky-500 bg-sky-100/70',
        ],
        [
            'label' => 'اليوم',
            'value' => number_format($stats['today'] ?? 0),
            'icon' => 'fas fa-calendar-day',
            'color' => 'text-emerald-500 bg-emerald-100/70',
        ],
        [
            'label' => 'إرسال رمز',
            'value' => number_format($stats['challenge_sent'] ?? 0),
            'icon' => 'fas fa-paper-plane',
            'color' => 'text-amber-500 bg-amber-100/70',
        ],
        [
            'label' => 'تحقق ناجح',
            'value' => number_format($stats['verified'] ?? 0),
            'icon' => 'fas fa-check-circle',
            'color' => 'text-green-500 bg-green-100/70',
        ],
        [
            'label' => 'فشل التحقق',
            'value' => number_format($stats['failed'] ?? 0),
            'icon' => 'fas fa-times-circle',
            'color' => 'text-rose-500 bg-rose-100/70',
        ],
    ];
?>

<div class="space-y-6 sm:space-y-10">
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h2 class="text-2xl font-bold text-slate-900">سجلات التحقق الثنائي (2FA)</h2>
            <p class="text-sm text-slate-500 mt-2">جميع أحداث تسجيل الدخول بالتحقق الثنائي (إرسال رمز، تحقق ناجح، فشل)</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 p-5 sm:p-8">
            <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-2xl border border-slate-200 bg-white/70 p-5 flex flex-col gap-2 card-hover-effect">
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-500"><?php echo e($card['label']); ?></p>
                    <div class="flex items-center justify-between">
                        <p class="text-2xl font-bold text-slate-900"><?php echo e($card['value']); ?></p>
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl <?php echo e($card['color']); ?>">
                            <i class="<?php echo e($card['icon']); ?> text-xl"></i>
                        </span>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-filter text-sky-600"></i>
                فلترة وبحث
            </h3>
        </div>
        <div class="px-5 py-6 sm:px-8 lg:px-12">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">البحث (بريد / مستخدم)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="البحث..."
                               class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 pr-10 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">نوع الحدث</label>
                    <select name="event" class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
                        <option value="">جميع الأحداث</option>
                        <option value="challenge_sent" <?php echo e(request('event') === 'challenge_sent' ? 'selected' : ''); ?>>إرسال رمز</option>
                        <option value="verified" <?php echo e(request('event') === 'verified' ? 'selected' : ''); ?>>تحقق ناجح</option>
                        <option value="failed" <?php echo e(request('event') === 'failed' ? 'selected' : ''); ?>>فشل التحقق</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">من تاريخ</label>
                    <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>"
                           class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">إلى تاريخ</label>
                    <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>"
                           class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400 transition-all">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 rounded-2xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all">
                        <i class="fas fa-search"></i>
                        <span>بحث</span>
                    </button>
                    <?php if(request()->anyFilled(['search', 'event', 'date_from', 'date_to'])): ?>
                        <a href="<?php echo e(route('admin.two-factor-logs.index')); ?>" class="px-4 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-2xl font-semibold transition-colors" title="مسح الفلتر">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </section>

    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-list text-sky-600"></i>
                السجلات
            </h3>
            <p class="text-sm text-slate-500 mt-1">
                <span class="font-semibold text-sky-600"><?php echo e($logs->total()); ?></span> سجل
            </p>
        </div>

        <?php if($logs->count() > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr class="text-xs font-semibold uppercase tracking-widest text-slate-500">
                            <th class="px-6 py-4 text-right">المستخدم</th>
                            <th class="px-6 py-4 text-right">البريد</th>
                            <th class="px-6 py-4 text-right">الحدث</th>
                            <th class="px-6 py-4 text-right">IP</th>
                            <th class="px-6 py-4 text-right">التاريخ والوقت</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white/80 text-sm text-slate-700">
                        <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <?php if($log->user): ?>
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-sky-500 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-sm shadow-md">
                                                <?php echo e(mb_substr($log->user->name ?? 'غ', 0, 1, 'UTF-8')); ?>

                                            </div>
                                            <p class="font-semibold text-slate-900"><?php echo e($log->user->name); ?></p>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-slate-400">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4"><?php echo e($log->email ?? '—'); ?></td>
                                <td class="px-6 py-4">
                                    <?php
                                        $eventClasses = [
                                            'challenge_sent' => 'bg-amber-100 text-amber-700',
                                            'verified' => 'bg-green-100 text-green-700',
                                            'failed' => 'bg-rose-100 text-rose-700',
                                        ];
                                        $c = $eventClasses[$log->event] ?? 'bg-slate-100 text-slate-700';
                                    ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold <?php echo e($c); ?>">
                                        <?php echo e($log->event_label); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs"><?php echo e($log->ip_address ?? '—'); ?></td>
                                <td class="px-6 py-4 text-xs text-slate-500">
                                    <p class="font-medium"><?php echo e($log->created_at->format('Y-m-d')); ?></p>
                                    <p><?php echo e($log->created_at->format('H:i:s')); ?></p>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php if($logs->hasPages()): ?>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                    <?php echo e($logs->appends(request()->query())->links()); ?>

                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="p-12 text-center">
                <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-slate-100 to-slate-200 rounded-full flex items-center justify-center">
                    <i class="fas fa-shield-alt text-slate-400 text-5xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">لا توجد سجلات 2FA</h3>
                <p class="text-slate-600 max-w-md mx-auto">لا توجد أحداث تحقق ثنائي مطابقة للفلتر</p>
            </div>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/two-factor-logs/index.blade.php ENDPATH**/ ?>