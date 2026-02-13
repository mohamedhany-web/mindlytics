

<?php $__env->startSection('title', 'رقابة المدربين'); ?>
<?php $__env->startSection('header', 'رقابة المدربين'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="bg-white rounded-3xl shadow-lg p-6 border border-slate-200 overflow-hidden">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold text-slate-900">جميع المدربين — رقابة شاملة</h1>
            <p class="text-slate-500 text-sm">اضغط على اسم المدرب لعرض كل بياناته وتقاريره، أو صدّر تقرير Excel.</p>
        </div>
        <form method="GET" class="mb-6 flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="بحث بالاسم، البريد، الهاتف..." class="rounded-2xl border border-slate-200 px-4 py-2.5 text-sm w-64">
            <button type="submit" class="rounded-2xl bg-sky-600 hover:bg-sky-700 text-white px-4 py-2.5 text-sm font-semibold">بحث</button>
        </form>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-right">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase text-slate-500">
                        <th class="px-6 py-3">المدرب</th>
                        <th class="px-6 py-3">البريد / الهاتف</th>
                        <th class="px-6 py-3">الكورسات</th>
                        <th class="px-6 py-3">الاتفاقيات</th>
                        <th class="px-6 py-3">آخر نشاط</th>
                        <th class="px-6 py-3">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <a href="<?php echo e(route('admin.quality-control.instructors.show', $instructor)); ?>" class="font-semibold text-sky-600 hover:text-sky-700"><?php echo e($instructor->name); ?></a>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600"><?php echo e($instructor->email ?? '—'); ?><br><span class="text-slate-500"><?php echo e($instructor->phone ?? '—'); ?></span></td>
                        <td class="px-6 py-4"><?php echo e($instructor->courses_count); ?></td>
                        <td class="px-6 py-4"><?php echo e($instructor->agreements_count); ?></td>
                        <td class="px-6 py-4 text-sm"><?php echo e($instructor->last_activity ? $instructor->last_activity->diffForHumans() : '—'); ?></td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="<?php echo e(route('admin.quality-control.instructors.show', $instructor)); ?>" class="inline-flex items-center gap-1 rounded-xl bg-sky-100 text-sky-700 px-3 py-1.5 text-xs font-semibold hover:bg-sky-200">عرض التفاصيل</a>
                                <a href="<?php echo e(route('admin.quality-control.instructors.export', $instructor)); ?>" class="inline-flex items-center gap-1 rounded-xl bg-emerald-100 text-emerald-700 px-3 py-1.5 text-xs font-semibold hover:bg-emerald-200"><i class="fas fa-file-excel"></i> Excel</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="mt-4 px-2"><?php echo e($instructors->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/quality-control/instructors.blade.php ENDPATH**/ ?>