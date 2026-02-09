

<?php $__env->startSection('title', 'طلبات المدربين - Mindlytics'); ?>
<?php $__env->startSection('header', 'طلبات المدربين للإدارة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 sm:space-y-10">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <!-- إحصائيات -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h2 class="text-2xl font-bold text-slate-900">طلبات المدربين</h2>
            <p class="text-sm text-slate-500 mt-2">مراجعة الطلبات المقدمة من المدربين والرد عليها</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-5 sm:p-8">
            <div class="rounded-2xl border border-slate-200 bg-white/70 p-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">الإجمالي</p>
                <p class="mt-3 text-2xl font-bold text-slate-900"><?php echo e($stats['total'] ?? 0); ?></p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-amber-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-amber-700">قيد المراجعة</p>
                <p class="mt-3 text-2xl font-bold text-amber-800"><?php echo e($stats['pending'] ?? 0); ?></p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-emerald-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-emerald-700">موافق عليها</p>
                <p class="mt-3 text-2xl font-bold text-emerald-800"><?php echo e($stats['approved'] ?? 0); ?></p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-rose-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-rose-700">مرفوضة</p>
                <p class="mt-3 text-2xl font-bold text-rose-800"><?php echo e($stats['rejected'] ?? 0); ?></p>
            </div>
        </div>
    </section>

    <!-- فلترة -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">البحث</label>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="موضوع، نص، أو اسم المدرب..."
                           class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">الحالة</label>
                    <select name="status" class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm">
                        <option value="">جميع الحالات</option>
                        <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>قيد المراجعة</option>
                        <option value="approved" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>>موافق عليها</option>
                        <option value="rejected" <?php echo e(request('status') == 'rejected' ? 'selected' : ''); ?>>مرفوضة</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-sky-700">
                        <i class="fas fa-search"></i>
                        بحث
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- قائمة الطلبات -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-inbox text-sky-600"></i>
                قائمة الطلبات
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-500">
                        <th class="px-6 py-4 text-right">الموضوع</th>
                        <th class="px-6 py-4 text-right">المدرب</th>
                        <th class="px-6 py-4 text-right">الحالة</th>
                        <th class="px-6 py-4 text-right">التاريخ</th>
                        <th class="px-6 py-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-slate-900"><?php echo e($req->subject); ?></div>
                            <div class="text-xs text-slate-500 mt-0.5"><?php echo e(Str::limit($req->message, 50)); ?></div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-700">
                            <?php echo e($req->instructor->name ?? '-'); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if($req->status == 'pending'): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">قيد المراجعة</span>
                            <?php elseif($req->status == 'approved'): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">موافق عليها</span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">مرفوضة</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            <?php echo e($req->created_at->format('Y-m-d H:i')); ?>

                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="<?php echo e(route('admin.instructor-requests.show', $req)); ?>"
                               class="inline-flex items-center justify-center w-9 h-9 bg-sky-50 hover:bg-sky-100 text-sky-600 rounded-xl transition-colors"
                               title="عرض والرد">
                                <i class="fas fa-eye text-sm"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                            <i class="fas fa-inbox text-4xl text-slate-300 mb-4"></i>
                            <p class="font-medium">لا توجد طلبات من المدربين</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($requests->hasPages()): ?>
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                <?php echo e($requests->appends(request()->query())->links()); ?>

            </div>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/instructor-requests/index.blade.php ENDPATH**/ ?>