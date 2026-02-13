
<?php $__env->startSection('title', 'التسويق الشخصي - ملفات المدربين'); ?>
<?php $__env->startSection('header', 'التسويق الشخصي (المدربين)'); ?>
<?php $__env->startSection('content'); ?>
<div class="w-full space-y-6">
    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
            <h1 class="text-2xl font-bold text-slate-900">مراجعة ملفات المدربين التعريفية</h1>
            <p class="text-slate-500 mt-1">الملفات المعتمدة تظهر في الصفحة الرئيسية ضمن المدربين وعند عرض كل كورس.</p>
        </div>
        <div class="p-5 sm:p-8">
            <div class="flex flex-wrap gap-3 mb-6">
                <a href="<?php echo e(route('admin.personal-branding.index', ['status' => 'pending_review'])); ?>" class="rounded-2xl px-4 py-2 text-sm font-semibold <?php echo e(request('status') == 'pending_review' ? 'bg-amber-500 text-white' : 'bg-amber-100 text-amber-800'); ?>">قيد المراجعة (<?php echo e($counts['pending']); ?>)</a>
                <a href="<?php echo e(route('admin.personal-branding.index', ['status' => 'approved'])); ?>" class="rounded-2xl px-4 py-2 text-sm font-semibold <?php echo e(request('status') == 'approved' ? 'bg-emerald-500 text-white' : 'bg-emerald-100 text-emerald-800'); ?>">معتمد (<?php echo e($counts['approved']); ?>)</a>
                <a href="<?php echo e(route('admin.personal-branding.index', ['status' => 'rejected'])); ?>" class="rounded-2xl px-4 py-2 text-sm font-semibold <?php echo e(request('status') == 'rejected' ? 'bg-rose-500 text-white' : 'bg-rose-100 text-rose-800'); ?>">مرفوض (<?php echo e($counts['rejected']); ?>)</a>
                <a href="<?php echo e(route('admin.personal-branding.index')); ?>" class="rounded-2xl px-4 py-2 text-sm font-semibold bg-slate-100 text-slate-700">الكل</a>
            </div>
            <form method="GET" class="mb-6 flex gap-3">
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="بحث بالاسم أو البريد..." class="rounded-2xl border border-slate-200 px-4 py-2.5 text-sm w-64">
                <?php if(request('status')): ?><input type="hidden" name="status" value="<?php echo e(request('status')); ?>"><?php endif; ?>
                <button type="submit" class="rounded-2xl bg-sky-600 text-white px-4 py-2.5 text-sm font-semibold">بحث</button>
            </form>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-xs font-semibold uppercase text-slate-500">
                            <th class="px-4 py-3">المدرب</th>
                            <th class="px-4 py-3">العنوان التعريفي</th>
                            <th class="px-4 py-3">الحالة</th>
                            <th class="px-4 py-3">تاريخ التقديم</th>
                            <th class="px-4 py-3">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php $__empty_1 = true; $__currentLoopData = $profiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3"><div class="font-semibold"><?php echo e($p->user->name ?? '—'); ?></div><div class="text-xs text-slate-500"><?php echo e($p->user->email ?? ''); ?></div></td>
                            <td class="px-4 py-3"><?php echo e(Str::limit($p->headline ?? '—', 40)); ?></td>
                            <td class="px-4 py-3"><span class="rounded-full px-2 py-0.5 text-xs font-semibold <?php if($p->status == 'approved'): ?> bg-emerald-100 text-emerald-700 <?php elseif($p->status == 'pending_review'): ?> bg-amber-100 text-amber-700 <?php elseif($p->status == 'rejected'): ?> bg-rose-100 text-rose-700 <?php else: ?> bg-slate-100 text-slate-600 <?php endif; ?>"><?php echo e(\App\Models\InstructorProfile::statusLabel($p->status)); ?></span></td>
                            <td class="px-4 py-3"><?php echo e($p->submitted_at ? $p->submitted_at->format('Y-m-d') : '—'); ?></td>
                            <td class="px-4 py-3"><a href="<?php echo e(route('admin.personal-branding.show', $p)); ?>" class="text-sky-600 hover:text-sky-700 font-medium">عرض / مراجعة</a></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">لا توجد ملفات تعريفية.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4"><?php echo e($profiles->links()); ?></div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/marketing/personal-branding/index.blade.php ENDPATH**/ ?>