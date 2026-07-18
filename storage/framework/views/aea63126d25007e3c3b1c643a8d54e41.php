<?php $__env->startSection('title', 'Practice'); ?>

<?php $__env->startSection('content'); ?>
    <div class="p-6">
        <div class="flex items-start justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-black text-slate-900">Practice (التمارين العملية)</h1>
                <p class="text-sm text-slate-600 mt-1">إدارة التمارين/الأنماط التعليمية (Learning Patterns) المرتبطة بالكورسات المتطورة.</p>
            </div>
        </div>

        <form method="GET" class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-600">بحث</label>
                    <input name="q" value="<?php echo e(request('q')); ?>" class="mt-1 w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500" placeholder="عنوان / وصف..." />
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-600">النوع</label>
                    <select name="type" class="mt-1 w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">الكل</option>
                        <?php $__currentLoopData = $availableTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php if(request('type') === $key): echo 'selected'; endif; ?>><?php echo e($info['name'] ?? $key); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-600">Course ID</label>
                    <input name="course_id" value="<?php echo e(request('course_id')); ?>" class="mt-1 w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500" placeholder="مثال: 12" />
                </div>
                <div class="flex items-end gap-2">
                    <button class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold">تصفية</button>
                    <a href="<?php echo e(route('admin.practice.index')); ?>" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold">مسح</a>
                </div>
            </div>
        </form>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-700">
                        <tr>
                            <th class="text-start px-4 py-3 font-black">#</th>
                            <th class="text-start px-4 py-3 font-black">العنوان</th>
                            <th class="text-start px-4 py-3 font-black">النوع</th>
                            <th class="text-start px-4 py-3 font-black">الكورس</th>
                            <th class="text-start px-4 py-3 font-black">المدرب</th>
                            <th class="text-start px-4 py-3 font-black">النقاط</th>
                            <th class="text-start px-4 py-3 font-black">الحالة</th>
                            <th class="text-start px-4 py-3 font-black"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__empty_1 = true; $__currentLoopData = $patterns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pattern): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php $typeInfo = $pattern->getTypeInfo(); ?>
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-3 text-slate-600 font-semibold"><?php echo e($pattern->id); ?></td>
                                <td class="px-4 py-3">
                                    <div class="font-black text-slate-900"><?php echo e($pattern->title ?: 'بدون عنوان'); ?></div>
                                    <?php if($pattern->description): ?>
                                        <div class="text-xs text-slate-500 line-clamp-1"><?php echo e(\Illuminate\Support\Str::limit($pattern->description, 90)); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-800 font-bold">
                                        <i class="<?php echo e($typeInfo['icon'] ?? 'fas fa-puzzle-piece'); ?>"></i>
                                        <span><?php echo e($typeInfo['name'] ?? $pattern->type); ?></span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-700">
                                    <div class="font-semibold"><?php echo e($pattern->course?->title ?? '—'); ?></div>
                                    <div class="text-xs text-slate-500">ID: <?php echo e($pattern->advanced_course_id); ?></div>
                                </td>
                                <td class="px-4 py-3 text-slate-700"><?php echo e($pattern->instructor?->name ?? '—'); ?></td>
                                <td class="px-4 py-3 text-slate-700 font-semibold"><?php echo e($pattern->points ?? 0); ?></td>
                                <td class="px-4 py-3">
                                    <?php if($pattern->is_active): ?>
                                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 font-black text-xs">نشط</span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 rounded-full bg-rose-100 text-rose-700 font-black text-xs">غير نشط</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <a href="<?php echo e(route('admin.practice.show', $pattern)); ?>" class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs">عرض</a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-slate-500 font-semibold">لا توجد تمارين مطابقة.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                <?php echo e($patterns->links()); ?>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\practice\index.blade.php ENDPATH**/ ?>