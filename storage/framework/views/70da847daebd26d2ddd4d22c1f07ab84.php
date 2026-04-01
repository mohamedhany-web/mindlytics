<?php
    $isVideo = $task->isVideoEditing();
    $rows = $task->deliverables->sortBy('created_at')->values();
?>
<div class="rounded-2xl border-2 border-slate-200 bg-white shadow-sm overflow-hidden mb-6" id="deliverables-quick-table">
    <div class="px-4 py-3 bg-gradient-to-l from-slate-50 to-white border-b border-slate-100 flex flex-wrap items-center justify-between gap-2">
        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <?php if($isVideo): ?>
                <span class="w-9 h-9 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center"><i class="fas fa-table"></i></span>
            <?php else: ?>
                <span class="w-9 h-9 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center"><i class="fas fa-table"></i></span>
            <?php endif; ?>
            جدول التسليمات
        </h2>
        <a href="#deliverables-section" class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-down"></i>
            إضافة أو تعديل تسليم
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="text-right px-3 py-2 font-semibold whitespace-nowrap">#</th>
                    <th class="text-right px-3 py-2 font-semibold">العنوان</th>
                    <th class="text-right px-3 py-2 font-semibold whitespace-nowrap">الحالة</th>
                    <?php if($isVideo): ?>
                        <th class="text-right px-3 py-2 font-semibold whitespace-nowrap">ممن استلمته</th>
                        <th class="text-right px-3 py-2 font-semibold whitespace-nowrap">قبل</th>
                        <th class="text-right px-3 py-2 font-semibold whitespace-nowrap">بعد</th>
                        <th class="text-right px-3 py-2 font-semibold">رابط الفيديو</th>
                    <?php else: ?>
                        <th class="text-right px-3 py-2 font-semibold whitespace-nowrap">النوع</th>
                        <th class="text-right px-3 py-2 font-semibold">المحتوى / المعاينة</th>
                    <?php endif; ?>
                    <th class="text-right px-3 py-2 font-semibold whitespace-nowrap">التاريخ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50/80 align-top">
                        <td class="px-3 py-2 font-mono text-xs text-gray-500"><?php echo e($index + 1); ?></td>
                        <td class="px-3 py-2 font-medium text-gray-900"><?php echo e($d->title ?: ('تسليم ' . ($index + 1))); ?></td>
                        <td class="px-3 py-2 whitespace-nowrap">
                            <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-semibold
                                <?php if($d->status === 'approved'): ?> bg-green-100 text-green-800
                                <?php elseif($d->status === 'rejected'): ?> bg-red-100 text-red-800
                                <?php elseif($d->status === 'submitted'): ?> bg-blue-100 text-blue-800
                                <?php else: ?> bg-gray-100 text-gray-700
                                <?php endif; ?>">
                                <?php if($d->status === 'approved'): ?> معتمد
                                <?php elseif($d->status === 'rejected'): ?> مرفوض
                                <?php elseif($d->status === 'submitted'): ?> مقدم
                                <?php else: ?> معلق
                                <?php endif; ?>
                            </span>
                        </td>
                        <?php if($isVideo): ?>
                            <td class="px-3 py-2 text-gray-800"><?php echo e($d->received_from ?: '—'); ?></td>
                            <td class="px-3 py-2 text-gray-800 whitespace-nowrap"><?php echo e($d->duration_before ?: '—'); ?></td>
                            <td class="px-3 py-2 text-gray-800 whitespace-nowrap"><?php echo e($d->duration_after ?: '—'); ?></td>
                            <td class="px-3 py-2">
                                <?php if($d->link_url): ?>
                                    <a href="<?php echo e($d->link_url); ?>" target="_blank" rel="noopener" class="text-violet-600 hover:text-violet-800 font-medium break-all max-w-[14rem] inline-block"><?php echo e(\Illuminate\Support\Str::limit($d->link_url, 42)); ?></a>
                                <?php else: ?>
                                    <span class="text-gray-400">—</span>
                                <?php endif; ?>
                            </td>
                        <?php else: ?>
                            <td class="px-3 py-2 whitespace-nowrap">
                                <?php if($d->delivery_type === 'link'): ?> رابط
                                <?php elseif($d->delivery_type === 'image'): ?> صورة
                                <?php else: ?> ملف
                                <?php endif; ?>
                            </td>
                            <td class="px-3 py-2">
                                <?php if($d->delivery_type === 'link' && $d->link_url): ?>
                                    <a href="<?php echo e($d->link_url); ?>" target="_blank" rel="noopener" class="text-blue-600 hover:text-blue-800 break-all max-w-[14rem] inline-block"><?php echo e(\Illuminate\Support\Str::limit($d->link_url, 42)); ?></a>
                                <?php elseif($d->delivery_type === 'image' && $d->file_path): ?>
                                    <a href="<?php echo e(\Illuminate\Support\Facades\Storage::url($d->file_path)); ?>" target="_blank" rel="noopener" class="inline-block">
                                        <img src="<?php echo e(\Illuminate\Support\Facades\Storage::url($d->file_path)); ?>" alt="" class="max-h-14 rounded border border-gray-200 object-cover">
                                    </a>
                                <?php elseif($d->file_path): ?>
                                    <a href="<?php echo e(\Illuminate\Support\Facades\Storage::url($d->file_path)); ?>" target="_blank" class="text-sky-600 hover:text-sky-800 font-medium"><i class="fas fa-file-download ml-1"></i><?php echo e(\Illuminate\Support\Str::limit($d->file_name ?? 'ملف', 24)); ?></a>
                                <?php else: ?>
                                    <span class="text-gray-400">—</span>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                        <td class="px-3 py-2 text-gray-500 whitespace-nowrap text-xs"><?php echo e($d->created_at->format('Y-m-d H:i')); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="<?php echo e($isVideo ? 8 : 6); ?>" class="px-4 py-8 text-center text-gray-500">
                            لا توجد تسليمات بعد. استخدم قسم «التسليمات» أدناه لإضافة أول تسليم.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/employee/tasks/partials/deliverables-summary-table.blade.php ENDPATH**/ ?>