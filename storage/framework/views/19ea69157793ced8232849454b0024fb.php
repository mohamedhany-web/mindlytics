<?php $__env->startSection('title', 'أكواد خصم الورش - Mindlytics'); ?>
<?php $__env->startSection('header', 'أكواد خصم الورش'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background:#f8fafc;min-height:100vh;">
    <?php echo $__env->make('admin.marketing._flash', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.marketing._tabs', ['active' => 'promo'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="rounded-2xl bg-white/95 border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 bg-gradient-to-r from-violet-50 to-white flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-ticket-alt text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">أكواد خصم الورش</h2>
                    <p class="text-sm text-slate-600 mt-1">كود مرتبط بورشة — الطالب يفعّله عند التسجيل ويحصل على خصم على الكورسات</p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.workshop-promo-codes.create')); ?>" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-violet-600 to-purple-600 px-5 py-3 text-sm font-semibold text-white shadow-lg hover:shadow-xl transition-all">
                <i class="fas fa-plus"></i> كود جديد
            </a>
        </div>
    </section>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <?php $__currentLoopData = [
            ['label' => 'إجمالي الأكواد', 'value' => $stats['total'], 'icon' => 'fa-tags', 'color' => 'violet'],
            ['label' => 'أكواد نشطة', 'value' => $stats['active'], 'icon' => 'fa-bolt', 'color' => 'emerald'],
            ['label' => 'تفعيلات', 'value' => $stats['activations'], 'icon' => 'fa-user-check', 'color' => 'sky'],
            ['label' => 'استُخدمت', 'value' => $stats['used'], 'icon' => 'fa-shopping-cart', 'color' => 'amber'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-500 mb-1"><?php echo e($card['label']); ?></p>
                        <p class="text-3xl font-black text-slate-900"><?php echo e(number_format($card['value'])); ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-<?php echo e($card['color']); ?>-100 text-<?php echo e($card['color']); ?>-600 flex items-center justify-center">
                        <i class="fas <?php echo e($card['icon']); ?>"></i>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <form method="GET" class="p-4 border-b border-slate-100 flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="text-xs font-semibold text-slate-500">بحث</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="كود أو عنوان..."
                       class="w-full mt-1 rounded-lg border border-slate-200 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">الحالة</label>
                <select name="status" class="mt-1 rounded-lg border border-slate-200 px-3 py-2 text-sm">
                    <option value="">الكل</option>
                    <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>نشط</option>
                    <option value="expired" <?php if(request('status') === 'expired'): echo 'selected'; endif; ?>>منتهي</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">الورشة</label>
                <select name="workshop_id" class="mt-1 rounded-lg border border-slate-200 px-3 py-2 text-sm">
                    <option value="">الكل</option>
                    <?php $__currentLoopData = $workshops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ws): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($ws->id); ?>" <?php if((string) request('workshop_id') === (string) $ws->id): echo 'selected'; endif; ?>><?php echo e($ws->title); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold">تصفية</button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-right font-bold">الكود</th>
                        <th class="px-4 py-3 text-right font-bold">الورشة</th>
                        <th class="px-4 py-3 text-right font-bold">الخصم</th>
                        <th class="px-4 py-3 text-right font-bold">التفعيلات</th>
                        <th class="px-4 py-3 text-right font-bold">ينتهي</th>
                        <th class="px-4 py-3 text-right font-bold">الحالة</th>
                        <th class="px-4 py-3 text-center font-bold">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $promoCodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $promo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3">
                                <div class="font-mono font-bold text-violet-700"><?php echo e($promo->code); ?></div>
                                <div class="text-xs text-slate-500"><?php echo e($promo->title); ?></div>
                            </td>
                            <td class="px-4 py-3 text-slate-700"><?php echo e($promo->workshop?->title ?? '—'); ?></td>
                            <td class="px-4 py-3 font-semibold"><?php echo e($promo->discountLabel()); ?></td>
                            <td class="px-4 py-3">
                                <span class="font-bold"><?php echo e($promo->activations_count); ?></span>
                                <?php if($promo->max_activations): ?>
                                    <span class="text-slate-400">/ <?php echo e($promo->max_activations); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-slate-600"><?php echo e($promo->expiryLabel()); ?></td>
                            <td class="px-4 py-3">
                                <?php if($promo->isValid()): ?>
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">نشط</span>
                                <?php else: ?>
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600">منتهي</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="<?php echo e(route('admin.workshop-promo-codes.show', $promo)); ?>" class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100" title="عرض"><i class="fas fa-eye text-xs"></i></a>
                                    <a href="<?php echo e(route('admin.workshop-promo-codes.edit', $promo)); ?>" class="w-8 h-8 flex items-center justify-center rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100" title="تعديل"><i class="fas fa-edit text-xs"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-slate-500">
                                <i class="fas fa-ticket-alt text-4xl text-slate-300 mb-3 block"></i>
                                لا توجد أكواد — أنشئ كوداً مرتبطاً بورشة
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($promoCodes->hasPages()): ?>
            <div class="px-4 py-3 border-t border-slate-100"><?php echo e($promoCodes->links()); ?></div>
        <?php endif; ?>
    </div>

    
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-800">آخر التفعيلات</h2>
                <p class="text-sm text-slate-500 mt-0.5">من سجّل وفعّل كود ورشة — يظهر هنا فور التسجيل</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-violet-50 text-violet-700 text-sm font-medium">
                <i class="fas fa-bolt text-xs"></i>
                <?php echo e(number_format($stats['activations'])); ?> تفعيل
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-right font-semibold">الطالب</th>
                        <th class="px-4 py-3 text-right font-semibold">الكود</th>
                        <th class="px-4 py-3 text-right font-semibold">الورشة</th>
                        <th class="px-4 py-3 text-right font-semibold">الخصم</th>
                        <th class="px-4 py-3 text-right font-semibold">الحالة</th>
                        <th class="px-4 py-3 text-right font-semibold">تاريخ التفعيل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $recentActivations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $act): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3">
                                <?php if($act->user): ?>
                                    <div class="font-medium text-slate-800"><?php echo e($act->user->name); ?></div>
                                    <div class="text-xs text-slate-500"><?php echo e($act->user->email ?? $act->user->phone); ?></div>
                                <?php else: ?>
                                    <span class="text-slate-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <?php if($act->promoCode): ?>
                                    <a href="<?php echo e(route('admin.workshop-promo-codes.show', $act->promoCode)); ?>" class="font-mono text-violet-700 hover:underline"><?php echo e($act->promoCode->code); ?></a>
                                <?php else: ?>
                                    <span class="text-slate-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-slate-600"><?php echo e($act->promoCode?->workshop?->title ?? '—'); ?></td>
                            <td class="px-4 py-3">
                                <?php if($act->promoCode): ?>
                                    <?php if($act->promoCode->discount_type === 'percentage'): ?>
                                        <?php echo e(rtrim(rtrim(number_format((float) $act->promoCode->discount_value, 2), '0'), '.')); ?>%
                                    <?php else: ?>
                                        <?php echo e(number_format((float) $act->promoCode->discount_value, 0)); ?> ج.م
                                    <?php endif; ?>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <?php if($act->status === 'used'): ?>
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">مستخدم</span>
                                <?php elseif($act->status === 'expired'): ?>
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">منتهي</span>
                                <?php else: ?>
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">نشط</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-slate-600 whitespace-nowrap"><?php echo e($act->activated_at?->format('Y-m-d H:i') ?? '—'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <i class="fas fa-user-clock text-3xl text-slate-300 mb-2 block"></i>
                                لا توجد تفعيلات بعد — عند تسجيل طالب بكود ورشة سيظهر هنا مباشرة
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/workshop-promo-codes/index.blade.php ENDPATH**/ ?>