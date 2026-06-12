<?php $__env->startSection('title', $plan->title); ?>
<?php $__env->startSection('header', 'خطة التسويق: ' . $plan->title); ?>

<?php $__env->startSection('content'); ?>
<?php
    $planStatus = match($plan->status) {
        'draft' => ['مسودة', 'bg-gray-100 text-gray-800'],
        'active' => ['نشط', 'bg-emerald-100 text-emerald-800'],
        'paused' => ['متوقف', 'bg-amber-100 text-amber-800'],
        'completed' => ['مكتمل', 'bg-slate-200 text-slate-800'],
        default => [$plan->status, 'bg-gray-100'],
    };
    $evtStatus = fn ($s) => match($s) {
        'idea' => 'فكرة',
        'draft' => 'مسودة',
        'scheduled' => 'مجدول',
        'published' => 'منشور',
        'skipped' => 'تم التخطي',
        default => $s,
    };
?>
<div class="space-y-8" x-data="marketingPlanPage()">
    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 px-4 py-3 text-sm font-medium"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="space-y-2">
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex px-2 py-1 rounded-lg text-xs font-semibold <?php echo e($planStatus[1]); ?>"><?php echo e($planStatus[0]); ?></span>
                <?php if($plan->designTaskCycle): ?>
                    <a href="<?php echo e(route('employee.design-cycles.show', $plan->designTaskCycle)); ?>" class="text-sm font-semibold text-fuchsia-700 hover:text-fuchsia-900">
                        <i class="fas fa-link ml-1"></i> مرتبطة بدورة تصميم #<?php echo e($plan->designTaskCycle->id); ?>

                    </a>
                <?php endif; ?>
            </div>
            <?php if($plan->start_date || $plan->end_date): ?>
                <p class="text-sm text-gray-600">
                    <?php if($plan->start_date): ?><span>من <?php echo e($plan->start_date->format('Y-m-d')); ?></span><?php endif; ?>
                    <?php if($plan->end_date): ?><span class="mr-2">إلى <?php echo e($plan->end_date->format('Y-m-d')); ?></span><?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('employee.marketing-plans.index')); ?>" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-sm font-semibold text-slate-800">القائمة</a>
            <a href="<?php echo e(route('employee.marketing-plans.edit', $plan)); ?>" class="px-4 py-2 rounded-xl bg-pink-600 hover:bg-pink-700 text-white text-sm font-semibold">تعديل الخطة</a>
            <form method="post" action="<?php echo e(route('employee.marketing-plans.destroy', $plan)); ?>" onsubmit="return confirm('حذف الخطة وجميع المنصات والأحداث المرتبطة؟');">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="px-4 py-2 rounded-xl border border-red-200 text-red-700 hover:bg-red-50 text-sm font-semibold">حذف</button>
            </form>
        </div>
    </div>

    <?php if($plan->summary): ?>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-2">الملخص</h2>
            <p class="text-gray-800 whitespace-pre-wrap"><?php echo e($plan->summary); ?></p>
        </div>
    <?php endif; ?>
    <?php if($plan->goals): ?>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-2">الأهداف والاستراتيجية</h2>
            <p class="text-gray-800 whitespace-pre-wrap"><?php echo e($plan->goals); ?></p>
        </div>
    <?php endif; ?>

    <!-- المنصات -->
    <div class="rounded-2xl border border-pink-100 bg-gradient-to-b from-pink-50/40 to-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-pink-100 flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-lg font-bold text-gray-900"><i class="fas fa-share-alt text-pink-600 ml-2"></i> منصات السوشيال ميديا</h2>
            <p class="text-xs text-gray-600">لون كل منصة يُستخدم في عرض أحداثها على التقويم.</p>
        </div>
        <div class="p-5 space-y-6">
            <form method="post" action="<?php echo e(route('employee.marketing-plans.platforms.store', $plan)); ?>" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end rounded-xl border border-dashed border-pink-200 bg-white/80 p-4">
                <?php echo csrf_field(); ?>
                <div class="md:col-span-6">
                    <label class="block text-xs font-medium text-gray-600 mb-2">المنصات (اختيار متعدد)</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                        <?php $__currentLoopData = $platformLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="inline-flex items-center gap-2 rounded-lg border border-pink-100 bg-white px-3 py-2 text-sm">
                                <input type="checkbox" name="platform_keys[]" value="<?php echo e($key); ?>" class="rounded border-gray-300">
                                <span class="font-semibold text-gray-800"><?php echo e($label); ?></span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <p class="text-[11px] text-gray-500 mt-2">يمكنك اختيار Facebook + Instagram + TikTok مرة واحدة. (ملاحظة: «أخرى» تُضاف وحدها).</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">اسم مخصص (أخرى)</label>
                    <input type="text" name="custom_label" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="إن اخترت أخرى">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1">رابط البروفايل</label>
                    <input type="url" name="profile_url" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="https://">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">لون (#RRGGBB)</label>
                    <input type="text" name="color_hex" value="#6366f1" pattern="#[0-9A-Fa-f]{6}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm font-mono">
                </div>
                <div class="md:col-span-12">
                    <label class="block text-xs font-medium text-gray-600 mb-1">استراتيجية المنصة</label>
                    <textarea name="strategy_notes" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="نبرة المحتوى، الجمهور، أنواع المنشورات..."></textarea>
                </div>
                <div class="md:col-span-12">
                    <label class="block text-xs font-medium text-gray-600 mb-1">إيقاع النشر / التكرار</label>
                    <textarea name="cadence_notes" rows="1" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="مثال: ٣ ريلز أسبوعياً، ستوري يومي..."></textarea>
                </div>
                <div class="md:col-span-12">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-pink-600 text-white text-sm font-semibold hover:bg-pink-700"><i class="fas fa-plus ml-1"></i> إضافة منصة</button>
                </div>
            </form>

            <div class="space-y-4">
                <?php $__empty_1 = true; $__currentLoopData = $plan->platforms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="rounded-xl border border-gray-200 bg-white p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="w-4 h-4 rounded-full shrink-0 border border-gray-200" style="background-color: <?php echo e($plat->color_hex); ?>"></span>
                                <div>
                                    <p class="font-bold text-gray-900"><?php echo e($plat->displayName()); ?></p>
                                    <?php if($plat->profile_url): ?>
                                        <a href="<?php echo e($plat->profile_url); ?>" target="_blank" rel="noopener" class="text-xs text-blue-600 hover:underline break-all"><?php echo e(\Illuminate\Support\Str::limit($plat->profile_url, 56)); ?></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <form method="post" action="<?php echo e(route('employee.marketing-plans.platforms.destroy', [$plan, $plat])); ?>" onsubmit="return confirm('حذف هذه المنصة؟');" class="shrink-0">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800">حذف</button>
                            </form>
                        </div>
                        <?php if($plat->strategy_notes || $plat->cadence_notes): ?>
                            <div class="mt-3 grid md:grid-cols-2 gap-3 text-sm">
                                <?php if($plat->strategy_notes): ?><div class="text-gray-700"><span class="font-semibold text-gray-500">استراتيجية:</span> <?php echo e($plat->strategy_notes); ?></div><?php endif; ?>
                                <?php if($plat->cadence_notes): ?><div class="text-gray-700"><span class="font-semibold text-gray-500">الإيقاع:</span> <?php echo e($plat->cadence_notes); ?></div><?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <details class="mt-3 group">
                            <summary class="cursor-pointer text-sm font-semibold text-pink-700">تعديل المنصة</summary>
                            <form method="post" action="<?php echo e(route('employee.marketing-plans.platforms.update', [$plan, $plat])); ?>" class="mt-3 grid grid-cols-1 md:grid-cols-12 gap-3 border-t border-gray-100 pt-3">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                <div class="md:col-span-3">
                                    <label class="block text-xs text-gray-600 mb-1">المنصة</label>
                                    <select name="platform_key" class="w-full rounded-lg border px-3 py-2 text-sm">
                                        <?php $__currentLoopData = $platformLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>" <?php echo e($plat->platform_key === $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs text-gray-600 mb-1">اسم مخصص</label>
                                    <input type="text" name="custom_label" value="<?php echo e($plat->custom_label); ?>" class="w-full rounded-lg border px-3 py-2 text-sm">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-xs text-gray-600 mb-1">رابط</label>
                                    <input type="url" name="profile_url" value="<?php echo e($plat->profile_url); ?>" class="w-full rounded-lg border px-3 py-2 text-sm">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs text-gray-600 mb-1">لون</label>
                                    <input type="text" name="color_hex" value="<?php echo e($plat->color_hex); ?>" pattern="#[0-9A-Fa-f]{6}" class="w-full rounded-lg border px-3 py-2 text-sm font-mono">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs text-gray-600 mb-1">ترتيب</label>
                                    <input type="number" name="sort_order" value="<?php echo e($plat->sort_order); ?>" min="0" class="w-full rounded-lg border px-3 py-2 text-sm">
                                </div>
                                <div class="md:col-span-12">
                                    <label class="block text-xs text-gray-600 mb-1">استراتيجية</label>
                                    <textarea name="strategy_notes" rows="2" class="w-full rounded-lg border px-3 py-2 text-sm"><?php echo e($plat->strategy_notes); ?></textarea>
                                </div>
                                <div class="md:col-span-12">
                                    <label class="block text-xs text-gray-600 mb-1">إيقاع النشر</label>
                                    <textarea name="cadence_notes" rows="1" class="w-full rounded-lg border px-3 py-2 text-sm"><?php echo e($plat->cadence_notes); ?></textarea>
                                </div>
                                <div class="md:col-span-12">
                                    <button type="submit" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-semibold">حفظ</button>
                                </div>
                            </form>
                        </details>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-center text-gray-500 py-6">لم تُضف منصات بعد. أضف منصة لربط الأحداث بها ولون التقويم.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- أحداث التقويم -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h2 class="text-lg font-bold text-gray-900"><i class="fas fa-calendar-alt text-slate-600 ml-2"></i> التقويم والمحتوى المجدول</h2>
            <p class="text-sm text-gray-600 mt-1">الأحداث تظهر في <a href="<?php echo e(route('employee.calendar')); ?>" class="text-pink-600 font-semibold underline">تقويمك</a>. يمكن ربط حدث بدورة تصميم أو بمنصة.</p>
        </div>
        <div class="p-5 space-y-6">
            <form method="post" action="<?php echo e(route('employee.marketing-plans.events.store', $plan)); ?>" class="grid grid-cols-1 md:grid-cols-12 gap-3 rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                <?php echo csrf_field(); ?>
                <div class="md:col-span-6">
                    <label class="block text-xs font-medium text-gray-600 mb-1">عنوان الحدث <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="مثال: إطلاق حملة الريلز">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1">البداية <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="starts_at" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1">النهاية</label>
                    <input type="datetime-local" name="ends_at" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div class="md:col-span-4">
                    <label class="block text-xs font-medium text-gray-600 mb-2">المنصات (اختيار متعدد)</label>
                    <div class="rounded-lg border border-gray-300 bg-white p-3 space-y-2 max-h-40 overflow-auto">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" x-model="isGeneralEvent" class="rounded border-gray-300">
                            <span class="font-semibold text-gray-800">عام للخطة (بدون منصات)</span>
                        </label>
                        <div class="border-t border-gray-100 pt-2 grid grid-cols-1 gap-2">
                            <?php $__currentLoopData = $plan->platforms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="platform_ids[]" value="<?php echo e($plat->id); ?>" class="rounded border-gray-300" :disabled="isGeneralEvent">
                                    <span class="font-semibold text-gray-800"><?php echo e($plat->displayName()); ?></span>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <p class="text-[11px] text-gray-500 mt-2">لو اخترت “عام”، سيتم إنشاء حدث واحد غير مربوط بمنصة. لو اخترت منصات، سيتم إنشاء حدث لكل منصة بنفس المحتوى.</p>
                </div>
                <div class="md:col-span-4">
                    <label class="block text-xs font-medium text-gray-600 mb-1">الحالة</label>
                    <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <?php $__currentLoopData = ['idea' => 'فكرة', 'draft' => 'مسودة', 'scheduled' => 'مجدول', 'published' => 'منشور', 'skipped' => 'تم التخطي']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($v); ?>" <?php echo e($v === 'draft' ? 'selected' : ''); ?>><?php echo e($l); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="md:col-span-4">
                    <label class="block text-xs font-medium text-gray-600 mb-1">ربط بدورة تصميم</label>
                    <select name="design_task_cycle_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="">— لا —</option>
                        <?php $__currentLoopData = $cycles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c->id); ?>">#<?php echo e($c->id); ?> — <?php echo e($c->title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="md:col-span-12">
                    <label class="block text-xs font-medium text-gray-600 mb-1">تفاصيل / سكربت / ملاحظات</label>
                    <textarea name="body" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></textarea>
                </div>
                <div class="md:col-span-12">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-semibold hover:bg-slate-900"><i class="fas fa-calendar-plus ml-1"></i> إضافة للتقويم</button>
                </div>
            </form>

            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="text-right px-3 py-2 font-semibold">الوقت</th>
                            <th class="text-right px-3 py-2 font-semibold">العنوان</th>
                            <th class="text-right px-3 py-2 font-semibold">المنصة</th>
                            <th class="text-right px-3 py-2 font-semibold">الحالة</th>
                            <th class="text-right px-3 py-2 font-semibold">دورة تصميم</th>
                            <th class="text-right px-3 py-2 font-semibold"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $plan->calendarEvents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="align-top hover:bg-slate-50/80">
                                <td class="px-3 py-2 whitespace-nowrap text-gray-700">
                                    <?php echo e($ev->starts_at->format('Y-m-d H:i')); ?>

                                    <?php if($ev->ends_at): ?><div class="text-xs text-gray-500">→ <?php echo e($ev->ends_at->format('H:i')); ?></div><?php endif; ?>
                                </td>
                                <td class="px-3 py-2">
                                    <span class="font-medium text-gray-900"><?php echo e($ev->title); ?></span>
                                    <?php if($ev->body): ?><p class="text-xs text-gray-600 mt-1 whitespace-pre-wrap"><?php echo e(\Illuminate\Support\Str::limit($ev->body, 120)); ?></p><?php endif; ?>
                                </td>
                                <td class="px-3 py-2">
                                    <?php if($ev->platform): ?>
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold">
                                            <span class="w-2 h-2 rounded-full" style="background: <?php echo e($ev->platform->color_hex); ?>"></span>
                                            <?php echo e($ev->platform->displayName()); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 py-2 text-xs font-medium"><?php echo e($evtStatus($ev->status)); ?></td>
                                <td class="px-3 py-2 text-xs">
                                    <?php if($ev->design_task_cycle_id): ?>
                                        <a href="<?php echo e(route('employee.design-cycles.show', $ev->design_task_cycle_id)); ?>" class="text-fuchsia-700 font-semibold">#<?php echo e($ev->design_task_cycle_id); ?></a>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <details>
                                        <summary class="cursor-pointer text-xs font-semibold text-pink-700">تعديل</summary>
                                        <form method="post" action="<?php echo e(route('employee.marketing-plans.events.update', [$plan, $ev])); ?>" class="mt-2 space-y-2 p-3 bg-white border border-gray-200 rounded-lg min-w-[240px]">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PUT'); ?>
                                            <input type="text" name="title" value="<?php echo e($ev->title); ?>" required class="w-full rounded border px-2 py-1 text-xs">
                                            <input type="datetime-local" name="starts_at" value="<?php echo e($ev->starts_at->format('Y-m-d\TH:i')); ?>" required class="w-full rounded border px-2 py-1 text-xs">
                                            <input type="datetime-local" name="ends_at" value="<?php echo e($ev->ends_at ? $ev->ends_at->format('Y-m-d\TH:i') : ''); ?>" class="w-full rounded border px-2 py-1 text-xs">
                                            <select name="platform_id" class="w-full rounded border px-2 py-1 text-xs">
                                                <option value="">— عام —</option>
                                                <?php $__currentLoopData = $plan->platforms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($plat->id); ?>" <?php echo e((int)$ev->platform_id === (int)$plat->id ? 'selected' : ''); ?>><?php echo e($plat->displayName()); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <select name="status" class="w-full rounded border px-2 py-1 text-xs">
                                                <?php $__currentLoopData = ['idea', 'draft', 'scheduled', 'published', 'skipped']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($v); ?>" <?php echo e($ev->status === $v ? 'selected' : ''); ?>><?php echo e($evtStatus($v)); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <select name="design_task_cycle_id" class="w-full rounded border px-2 py-1 text-xs">
                                                <option value="">— لا —</option>
                                                <?php $__currentLoopData = $cycles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($c->id); ?>" <?php echo e((int)$ev->design_task_cycle_id === (int)$c->id ? 'selected' : ''); ?>>#<?php echo e($c->id); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <textarea name="body" rows="2" class="w-full rounded border px-2 py-1 text-xs"><?php echo e($ev->body); ?></textarea>
                                            <button type="submit" class="w-full py-1.5 rounded bg-slate-800 text-white text-xs font-semibold">حفظ</button>
                                        </form>
                                    </details>
                                    <form method="post" action="<?php echo e(route('employee.marketing-plans.events.destroy', [$plan, $ev])); ?>" class="mt-1" onsubmit="return confirm('حذف الحدث؟');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-xs text-red-600 font-semibold">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">لا توجد أحداث بعد.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('marketingPlanPage', () => ({
            isGeneralEvent: false,
        }));
    });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('marketingPlanPage', () => ({
            isGeneralEvent: false,
        }));
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/employee/marketing-plans/show.blade.php ENDPATH**/ ?>