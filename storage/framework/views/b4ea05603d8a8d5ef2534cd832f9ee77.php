

<?php $__env->startSection('title', 'سجل أخطاء المنصة'); ?>
<?php $__env->startSection('header', 'سجل أخطاء المنصة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statCards = [
        ['label' => 'مفتوحة', 'value' => number_format($stats['open'] ?? 0), 'icon' => 'fas fa-bug', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600', 'description' => 'تحتاج معالجة'],
        ['label' => 'اليوم', 'value' => number_format($stats['today'] ?? 0), 'icon' => 'fas fa-calendar-day', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'description' => 'أخطاء جديدة'],
        ['label' => 'حرجة مفتوحة', 'value' => number_format($stats['critical'] ?? 0), 'icon' => 'fas fa-fire', 'bg' => 'bg-orange-100', 'text' => 'text-orange-600', 'description' => 'أولوية عالية'],
        ['label' => 'حُلّت هذا الأسبوع', 'value' => number_format($stats['resolved_week'] ?? 0), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => 'تم الإغلاق'],
    ];
    $quickFilters = [
        ['key' => 'unresolved', 'label' => 'غير محلولة', 'icon' => 'fas fa-exclamation-circle'],
        ['key' => 'today', 'label' => 'اليوم', 'icon' => 'fas fa-clock'],
        ['key' => 'critical', 'label' => 'حرجة', 'icon' => 'fas fa-fire'],
        ['key' => 'open', 'label' => 'مفتوحة فقط', 'icon' => 'fas fa-folder-open'],
    ];
?>

<div class="space-y-6">
    <?php if(!empty($setupRequired)): ?>
        <div class="rounded-xl border border-amber-300 bg-amber-50 px-4 py-4 text-sm text-amber-950">
            <p class="font-bold text-base mb-1"><i class="fas fa-database ml-1"></i> إعداد قاعدة البيانات مطلوب</p>
            <p class="mb-2">جدول <code class="font-mono text-xs bg-white px-1 rounded">platform_error_logs</code> غير موجود على السيرفر. نفّذ على الاستضافة:</p>
            <pre class="text-xs bg-slate-900 text-emerald-300 rounded-lg p-3 overflow-x-auto font-mono">php artisan migrate --force
php artisan route:clear
php artisan view:clear</pre>
        </div>
    <?php endif; ?>

    <?php if(!empty($loadError)): ?>
        <div class="rounded-xl border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-900 font-medium">
            <i class="fas fa-exclamation-triangle ml-1"></i><?php echo e($loadError); ?>

        </div>
    <?php endif; ?>

    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 font-medium">
            <i class="fas fa-check-circle ml-1"></i><?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-2xl font-black text-slate-900">مراقبة أخطاء المنصة</h2>
                <p class="text-sm text-slate-600 mt-1">كل خطأ 500 أو استثناء يُسجَّل تلقائياً مع المستخدم والرابط وسياق الطلب</p>
            </div>
            <a href="<?php echo e(route('admin.activity-log')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i class="fas fa-history"></i>
                سجل النشاطات
            </a>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 p-5 sm:p-6 border-b border-slate-100 bg-slate-50/50">
            <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl bg-white border border-slate-200 p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg <?php echo e($card['bg']); ?> <?php echo e($card['text']); ?> flex items-center justify-center">
                            <i class="<?php echo e($card['icon']); ?>"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500"><?php echo e($card['label']); ?></p>
                            <p class="text-xl font-black text-slate-900"><?php echo e($card['value']); ?></p>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-2"><?php echo e($card['description']); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php if($topFingerprints->isNotEmpty()): ?>
            <div class="px-5 sm:px-6 py-4 border-b border-slate-100 bg-rose-50/40">
                <p class="text-xs font-bold text-rose-800 mb-2"><i class="fas fa-layer-group ml-1"></i> أكثر الأخطاء تكراراً (غير محلولة)</p>
                <div class="flex flex-wrap gap-2">
                    <?php $__currentLoopData = $topFingerprints; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('admin.platform-errors.index', ['search' => \Illuminate\Support\Str::limit($fp->sample_message, 60)])); ?>"
                           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white border border-rose-200 text-xs text-rose-900 hover:bg-rose-50">
                            <span class="font-bold text-rose-600"><?php echo e($fp->hits); ?>×</span>
                            <span class="max-w-[220px] truncate"><?php echo e($fp->sample_class ?: \Illuminate\Support\Str::limit($fp->sample_message, 50)); ?></span>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 space-y-3">
            <div class="flex flex-wrap gap-2">
                <?php $__currentLoopData = $quickFilters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $qf): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('admin.platform-errors.index', array_merge(request()->except('quick', 'page'), ['quick' => $qf['key']]))); ?>"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border transition-colors
                       <?php echo e(request('quick') === $qf['key'] ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-400'); ?>">
                        <i class="<?php echo e($qf['icon']); ?>"></i><?php echo e($qf['label']); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if(request()->hasAny(['quick','status','level','user_id','search','date_from','date_to','guest_only'])): ?>
                    <a href="<?php echo e(route('admin.platform-errors.index')); ?>" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-semibold text-slate-500 hover:text-rose-600">
                        <i class="fas fa-times"></i> مسح الفلاتر
                    </a>
                <?php endif; ?>
            </div>

            <form method="GET" action="<?php echo e(route('admin.platform-errors.index')); ?>" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-3">
                <?php if(request('quick')): ?><input type="hidden" name="quick" value="<?php echo e(request('quick')); ?>"><?php endif; ?>
                <div class="xl:col-span-2">
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="بحث: رسالة، رابط، ملف، مستخدم…"
                           class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                </div>
                <div>
                    <select name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm">
                        <option value="">كل الحالات</option>
                        <?php $__currentLoopData = \App\Models\PlatformErrorLog::STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($k); ?>" <?php if(request('status') === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <select name="level" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm">
                        <option value="">كل المستويات</option>
                        <?php $__currentLoopData = \App\Models\PlatformErrorLog::LEVELS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($k); ?>" <?php if(request('level') === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <select name="user_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm">
                        <option value="">كل المستخدمين</option>
                        <?php $__currentLoopData = $userOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($u->id); ?>" <?php if((string) request('user_id') === (string) $u->id): echo 'selected'; endif; ?>><?php echo e($u->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold py-2.5">
                        <i class="fas fa-search ml-1"></i> فلترة
                    </button>
                </div>
                <div>
                    <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm" title="من تاريخ">
                </div>
                <div>
                    <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm" title="إلى تاريخ">
                </div>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700 px-1">
                    <input type="checkbox" name="guest_only" value="1" <?php if(request()->boolean('guest_only')): echo 'checked'; endif; ?> class="rounded border-slate-300 text-rose-600">
                    زوار فقط (بدون مستخدم)
                </label>
            </form>
        </div>

        <form method="POST" action="<?php echo e(route('admin.platform-errors.bulk')); ?>" id="bulk-errors-form">
            <?php echo csrf_field(); ?>
            <div class="px-5 sm:px-6 py-3 border-b border-slate-100 flex flex-wrap items-center gap-2 bg-slate-50/80">
                <span class="text-xs font-semibold text-slate-600">إجراء جماعي:</span>
                <button type="submit" name="status" value="acknowledged" class="px-3 py-1.5 rounded-lg bg-amber-100 text-amber-900 text-xs font-bold hover:bg-amber-200">تعيين: قيد المعالجة</button>
                <button type="submit" name="status" value="resolved" class="px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-900 text-xs font-bold hover:bg-emerald-200">تعيين: تم الحل</button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-3 w-10"><input type="checkbox" id="select-all-errors" class="rounded border-slate-300"></th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase">الوقت</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase">المستوى</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase">الرسالة</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase">المستخدم</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase">الرابط</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase">الحالة</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-slate-500 uppercase">تفاصيل</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php $__empty_1 = true; $__currentLoopData = $errors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $levelClass = match($err->level) {
                                    'critical' => 'bg-rose-100 text-rose-800 border-rose-200',
                                    'warning' => 'bg-amber-100 text-amber-800 border-amber-200',
                                    default => 'bg-slate-100 text-slate-800 border-slate-200',
                                };
                                $statusClass = match($err->status) {
                                    'resolved' => 'bg-emerald-100 text-emerald-800',
                                    'acknowledged' => 'bg-sky-100 text-sky-800',
                                    default => 'bg-rose-100 text-rose-800',
                                };
                            ?>
                            <tr class="hover:bg-slate-50/80 <?php echo e($err->status === 'open' ? 'bg-rose-50/20' : ''); ?>">
                                <td class="px-3 py-3 text-center">
                                    <input type="checkbox" name="ids[]" value="<?php echo e($err->id); ?>" form="bulk-errors-form" class="err-checkbox rounded border-slate-300">
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-600 whitespace-nowrap tabular-nums">
                                    <?php echo e($err->created_at->format('m-d H:i')); ?>

                                    <div class="text-[10px] text-slate-400"><?php echo e($err->created_at->diffForHumans()); ?></div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-bold border <?php echo e($levelClass); ?>">
                                        <?php echo e(\App\Models\PlatformErrorLog::levelLabel($err->level)); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-3 max-w-md">
                                    <p class="font-semibold text-slate-900 truncate" title="<?php echo e($err->message); ?>"><?php echo e($err->message); ?></p>
                                    <?php if($err->exception_class): ?>
                                        <p class="text-[11px] text-slate-500 truncate font-mono"><?php echo e(class_basename($err->exception_class)); ?></p>
                                    <?php endif; ?>
                                    <?php if($err->shortLocation()): ?>
                                        <p class="text-[10px] text-slate-400 truncate font-mono"><?php echo e($err->shortLocation()); ?></p>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <?php if($err->user_id): ?>
                                        <p class="font-semibold text-slate-800"><?php echo e($err->user?->name); ?></p>
                                        <p class="text-slate-500 truncate max-w-[140px]"><?php echo e($err->user?->email); ?></p>
                                    <?php else: ?>
                                        <span class="text-slate-400">زائر</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-xs max-w-[180px]">
                                    <?php if($err->url): ?>
                                        <span class="text-slate-600 truncate block" title="<?php echo e($err->url); ?>"><?php echo e(\Illuminate\Support\Str::limit($err->url, 45)); ?></span>
                                        <?php if($err->method): ?><span class="text-[10px] font-mono text-slate-400"><?php echo e($err->method); ?></span><?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-slate-300">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-bold <?php echo e($statusClass); ?>">
                                        <?php echo e(\App\Models\PlatformErrorLog::statusLabel($err->status)); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="<?php echo e(route('admin.platform-errors.show', $err)); ?>" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center text-slate-500">
                                    <i class="fas fa-check-circle text-4xl text-emerald-300 mb-3"></i>
                                    <p class="font-semibold text-slate-700">لا توجد أخطاء مطابقة للفلتر</p>
                                    <p class="text-sm mt-1">عند حدوث أي خطأ في المنصة سيظهر هنا تلقائياً</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>

        <?php if($errors->hasPages()): ?>
            <div class="px-5 py-4 border-t border-slate-100"><?php echo e($errors->links()); ?></div>
        <?php endif; ?>
    </section>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.getElementById('select-all-errors')?.addEventListener('change', function () {
        document.querySelectorAll('.err-checkbox').forEach(cb => { cb.checked = this.checked; });
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\platform-errors\index.blade.php ENDPATH**/ ?>