<?php $__env->startSection('title', 'دورة تصميم #'.$designCycle->id); ?>
<?php $__env->startSection('header', 'دورة تصميم #'.$designCycle->id); ?>

<?php
    use App\Models\DesignTaskCycle;
    use App\Models\DesignCycleModeratorPlannerItem;
    use App\Models\EmployeeTaskDeliverable;

    $step = match ($designCycle->status) {
        DesignTaskCycle::STATUS_PENDING_DESIGN, DesignTaskCycle::STATUS_DESIGN_IN_PROGRESS => 1,
        DesignTaskCycle::STATUS_DESIGN_SUBMITTED => 2,
        DesignTaskCycle::STATUS_MODERATOR_DELIVERY_PENDING => 3,
        DesignTaskCycle::STATUS_COMPLETED => 4,
        DesignTaskCycle::STATUS_CANCELLED => 0,
        default => 1,
    };

    $fmtBytes = function (?int $bytes): string {
        if ($bytes === null || $bytes <= 0) {
            return '—';
        }
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        $v = (float) $bytes;
        while ($v >= 1024 && $i < count($units) - 1) {
            $v /= 1024;
            $i++;
        }

        return number_format($v, $i > 0 ? 1 : 0).' '.$units[$i];
    };
?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-none space-y-8">
    <?php if($errors->any()): ?>
        <div class="rounded-xl border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm"><?php echo e($errors->first()); ?></div>
    <?php endif; ?>

    
    <div class="flex flex-wrap items-center gap-2">
        <a href="<?php echo e(route('employee.design-cycles.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-sm font-semibold text-slate-800">
            <i class="fas fa-arrow-right"></i> القائمة
        </a>
        <?php if($designCycle->designerTask): ?>
            <a href="<?php echo e(route('employee.tasks.show', $designCycle->designerTask)); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-sky-100 hover:bg-sky-200 text-sm font-semibold text-sky-900">
                <i class="fas fa-paint-brush"></i> مهمة المصمم
            </a>
        <?php endif; ?>
        <?php if($designCycle->moderatorDeliveryTask): ?>
            <a href="<?php echo e(route('employee.tasks.show', $designCycle->moderatorDeliveryTask)); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-100 hover:bg-emerald-200 text-sm font-semibold text-emerald-900">
                <i class="fas fa-upload"></i> مهمة تسليمك النهائي
            </a>
        <?php endif; ?>
        <a href="<?php echo e(route('employee.tasks.index', ['task_type' => 'design_moderator_delivery'])); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-sm font-semibold text-indigo-900">
            <i class="fas fa-tasks"></i> مهام التسليم في «مهامي»
        </a>
    </div>

    
    <?php if($step > 0): ?>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 shadow-sm">
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-4">مسار عملك على هذه الدورة</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <?php $__currentLoopData = [
                    1 => ['label' => 'انتظار/تنفيذ المصمم', 'icon' => 'fa-user-clock'],
                    2 => ['label' => 'مراجعة التسليمات', 'icon' => 'fa-eye'],
                    3 => ['label' => 'تسليمك النهائي', 'icon' => 'fa-paper-plane'],
                    4 => ['label' => 'مكتملة', 'icon' => 'fa-check-circle'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="relative rounded-xl border-2 px-3 py-3 text-center transition
                        <?php echo e($step === $n ? 'border-fuchsia-500 bg-fuchsia-50 shadow-md' : ($step > $n ? 'border-emerald-200 bg-emerald-50/50' : 'border-gray-100 bg-gray-50/80 opacity-80')); ?>">
                        <div class="text-lg mb-1 <?php echo e($step >= $n ? 'text-fuchsia-700' : 'text-gray-400'); ?>">
                            <i class="fas <?php echo e($meta['icon']); ?>"></i>
                        </div>
                        <p class="text-xs font-bold leading-snug text-gray-800"><?php echo e($meta['label']); ?></p>
                        <?php if($step > $n): ?>
                            <span class="absolute top-2 left-2 text-emerald-600 text-xs"><i class="fas fa-check"></i></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>

    
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-4 w-full">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-xl font-black text-gray-900"><?php echo e($designCycle->title); ?></h2>
            <span class="inline-flex px-3 py-1 rounded-xl text-xs font-bold bg-fuchsia-50 text-fuchsia-800 border border-fuchsia-100">
                <?php echo e(DesignTaskCycle::statusLabel($designCycle->status)); ?>

            </span>
        </div>
        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
            <div class="rounded-xl bg-slate-50 px-4 py-3 border border-slate-100">
                <dt class="text-gray-500 text-xs font-semibold mb-1">المصمم</dt>
                <dd class="font-bold text-gray-900"><?php echo e($designCycle->designer->name ?? '—'); ?></dd>
            </div>
            <div class="rounded-xl bg-slate-50 px-4 py-3 border border-slate-100">
                <dt class="text-gray-500 text-xs font-semibold mb-1">حد تسليم المصمم</dt>
                <dd class="font-bold text-gray-900"><?php echo e($designCycle->deadline_at?->format('Y-m-d H:i')); ?></dd>
            </div>
            <div class="rounded-xl bg-slate-50 px-4 py-3 border border-slate-100">
                <dt class="text-gray-500 text-xs font-semibold mb-1">الأولوية</dt>
                <dd class="font-bold text-gray-900"><?php echo e($designCycle->priority); ?></dd>
            </div>
            <div class="rounded-xl bg-slate-50 px-4 py-3 border border-slate-100">
                <dt class="text-gray-500 text-xs font-semibold mb-1">تسليم المصمم (أول مرة)</dt>
                <dd class="font-bold text-gray-900"><?php echo e($designCycle->designer_submitted_at?->format('Y-m-d H:i') ?? '—'); ?></dd>
            </div>
        </dl>
        <?php if($designCycle->description): ?>
            <div>
                <p class="text-xs font-semibold text-gray-500 mb-1">الوصف</p>
                <p class="text-gray-800 whitespace-pre-wrap text-sm"><?php echo e($designCycle->description); ?></p>
            </div>
        <?php endif; ?>
        <div>
            <p class="text-xs font-semibold text-gray-500 mb-1">مواصفات التصميم</p>
            <p class="text-gray-800 whitespace-pre-wrap text-sm leading-relaxed"><?php echo e($designCycle->specifications); ?></p>
        </div>
    </div>

    
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden w-full">
        <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-2 bg-gradient-to-l from-violet-50 to-fuchsia-50">
            <div>
                <h3 class="text-lg font-black text-gray-900">جدول التسليمات</h3>
                <p class="text-xs text-gray-600 mt-0.5">كل ما رُفع من المصمم ومنك — معاينة، روابط، وأنواع مختلفة</p>
            </div>
            <span class="text-xs font-bold px-3 py-1 rounded-full bg-white/80 border border-fuchsia-100 text-fuchsia-800"><?php echo e($deliverablesTimeline->count()); ?> تسليم</span>
        </div>
        <div class="overflow-x-auto">
            <?php if($deliverablesTimeline->isEmpty()): ?>
                <div class="p-10 text-center text-gray-500 text-sm">
                    <i class="fas fa-inbox text-3xl text-gray-300 mb-3 block"></i>
                    لا توجد تسليمات بعد. عند رفع المصمم أو عند رفعك للتسليم النهائي ستظهر هنا تلقائياً.
                </div>
            <?php else: ?>
                <table class="min-w-[900px] w-full text-sm">
                    <thead class="bg-slate-800 text-white text-xs uppercase tracking-wide">
                        <tr>
                            <th class="text-right px-4 py-3 font-bold">المصدر</th>
                            <th class="text-right px-4 py-3 font-bold">العنوان</th>
                            <th class="text-right px-4 py-3 font-bold">النوع</th>
                            <th class="text-right px-4 py-3 font-bold">معاينة / الوصول</th>
                            <th class="text-right px-4 py-3 font-bold">الحجم</th>
                            <th class="text-right px-4 py-3 font-bold">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__currentLoopData = $deliverablesTimeline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                /** @var \App\Models\EmployeeTaskDeliverable $d */
                                $d = $row['deliverable'];
                            ?>
                            <tr class="hover:bg-fuchsia-50/30 align-top">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <?php if($row['source'] === 'designer'): ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-sky-100 text-sky-900 border border-sky-200">
                                            <i class="fas fa-paint-brush"></i> مصمم
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-900 border border-emerald-200">
                                            <i class="fas fa-user-tie"></i> مشرف
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-bold text-gray-900"><?php echo e($d->title); ?></p>
                                    <?php if($d->description): ?>
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-2"><?php echo e($d->description); ?></p>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <?php
                                        $type = $d->delivery_type;
                                        $typeClass = match ($type) {
                                            'image' => 'bg-pink-100 text-pink-800 border-pink-200',
                                            'link' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
                                            'file' => 'bg-amber-100 text-amber-900 border-amber-200',
                                            default => 'bg-gray-100 text-gray-800 border-gray-200',
                                        };
                                    ?>
                                    <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-bold border <?php echo e($typeClass); ?>">
                                        <?php echo e(EmployeeTaskDeliverable::deliveryTypeLabel($type)); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if($type === 'link' && $d->link_url): ?>
                                        <a href="<?php echo e($d->link_url); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-semibold text-xs break-all">
                                            <i class="fas fa-external-link-alt shrink-0"></i> فتح الرابط
                                        </a>
                                    <?php elseif(in_array($type, ['file', 'image'], true) && $d->publicFileUrl()): ?>
                                        <?php if($type === 'image'): ?>
                                            <a href="<?php echo e($d->publicFileUrl()); ?>" target="_blank" rel="noopener" class="block">
                                                <img src="<?php echo e($d->publicFileUrl()); ?>" alt="" class="max-h-28 rounded-lg border border-gray-200 shadow-sm object-contain bg-gray-50">
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?php echo e($d->publicFileUrl()); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-1 mt-2 text-violet-700 hover:text-violet-900 font-semibold text-xs">
                                            <i class="fas fa-download"></i> تحميل <?php if($d->file_name): ?> (<?php echo e($d->file_name); ?>) <?php endif; ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-xs font-mono text-gray-700 whitespace-nowrap"><?php echo e($fmtBytes($d->file_size)); ?></td>
                                <td class="px-4 py-3 text-xs text-gray-600 whitespace-nowrap"><?php echo e($d->created_at?->format('Y-m-d H:i')); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="rounded-2xl border border-indigo-200 bg-white shadow-sm overflow-hidden w-full">
        <div class="px-5 py-4 border-b border-indigo-100 bg-gradient-to-l from-indigo-50 to-slate-50">
            <h3 class="text-lg font-black text-gray-900">تنظيم عملك على هذه الدورة</h3>
            <p class="text-xs text-gray-600 mt-1 max-w-3xl">
                اربط المهام بـ <strong>قسم</strong> أو <strong>فترة اليوم</strong> و<strong>موعد</strong> لتنظيم يومك؛ يمكنك استخدامها كقائمة تحقق قبل التحويل للأقسام أو المهام العامة في النظام.
            </p>
        </div>

        <div class="p-4 sm:p-5 space-y-4">
            <?php if($designCycle->moderatorPlannerItems->isEmpty()): ?>
                <p class="text-sm text-gray-500 text-center py-6">لا توجد بنود بعد — أضف أول مهمة فرعية أدناه (مثال: مراجعة QA، رفع للسوشيال، إرسال للطباعة).</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php $__currentLoopData = $designCycle->moderatorPlannerItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <form method="post" action="<?php echo e(route('employee.design-cycles.planner-items.update', [$designCycle, $item])); ?>" class="rounded-xl border border-gray-200 bg-gray-50/50 p-4 space-y-3">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start">
                                <div class="md:col-span-3">
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">المهمة</label>
                                    <input type="text" name="title" value="<?php echo e(old('title', $item->title)); ?>" required class="w-full rounded-lg border-gray-200 text-sm py-2 px-3">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">القسم / الجهة</label>
                                    <input type="text" name="department" value="<?php echo e(old('department', $item->department)); ?>" placeholder="مثال: سوشيال، طباعة، QA"
                                           class="w-full rounded-lg border-gray-200 text-sm py-2 px-3" list="dept-suggestions-<?php echo e($designCycle->id); ?>">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">فترة اليوم</label>
                                    <input type="text" name="time_slot" value="<?php echo e(old('time_slot', $item->time_slot)); ?>" placeholder="صباحاً، ظهراً، مساءً"
                                           class="w-full rounded-lg border-gray-200 text-sm py-2 px-3" list="slot-suggestions-<?php echo e($designCycle->id); ?>">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">موعد</label>
                                    <input type="date" name="due_date" value="<?php echo e(old('due_date', $item->due_date?->format('Y-m-d'))); ?>"
                                           class="w-full rounded-lg border-gray-200 text-sm py-2 px-3">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">الحالة</label>
                                    <select name="status" class="w-full rounded-lg border-gray-200 text-sm py-2 px-3 font-semibold">
                                        <?php $__currentLoopData = [
                                            DesignCycleModeratorPlannerItem::STATUS_PENDING => DesignCycleModeratorPlannerItem::statusLabel('pending'),
                                            DesignCycleModeratorPlannerItem::STATUS_IN_PROGRESS => DesignCycleModeratorPlannerItem::statusLabel('in_progress'),
                                            DesignCycleModeratorPlannerItem::STATUS_DONE => DesignCycleModeratorPlannerItem::statusLabel('done'),
                                            DesignCycleModeratorPlannerItem::STATUS_SKIPPED => DesignCycleModeratorPlannerItem::statusLabel('skipped'),
                                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($val); ?>" <?php if(old('status', $item->status) === $val): echo 'selected'; endif; ?>><?php echo e($lab); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="md:col-span-1 flex items-end gap-2">
                                    <button type="submit" class="flex-1 py-2 rounded-lg bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700" title="حفظ">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">ملاحظات</label>
                                <input type="text" name="notes" value="<?php echo e(old('notes', $item->notes)); ?>" placeholder="تفاصيل إضافية، رابط، اسم مسؤول..."
                                       class="w-full rounded-lg border-gray-200 text-sm py-2 px-3">
                            </div>
                        </form>
                        <div class="flex justify-end -mt-2 mb-2">
                            <form method="post" action="<?php echo e(route('employee.design-cycles.planner-items.destroy', [$designCycle, $item])); ?>" onsubmit="return confirm('حذف هذا البند؟');" class="inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-semibold px-2 py-1">
                                    <i class="fas fa-trash-alt ml-1"></i> حذف البند
                                </button>
                            </form>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <datalist id="dept-suggestions-<?php echo e($designCycle->id); ?>">
                <option value="سوشيال ميديا">
                <option value="التسويق">
                <option value="الطباعة">
                <option value="مراجعة الجودة">
                <option value="الإدارة">
                <option value="المحتوى التعليمي">
            </datalist>
            <datalist id="slot-suggestions-<?php echo e($designCycle->id); ?>">
                <option value="صباحاً (9–12)">
                <option value="منتصف اليوم (12–3)">
                <option value="مساءً (3–6)">
                <option value="مرن">
            </datalist>

            <div class="rounded-xl border-2 border-dashed border-indigo-200 bg-indigo-50/30 p-4">
                <p class="text-xs font-bold text-indigo-900 mb-3">إضافة بند جديد للجدول</p>
                <form method="post" action="<?php echo e(route('employee.design-cycles.planner-items.store', $designCycle)); ?>" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    <?php echo csrf_field(); ?>
                    <div class="md:col-span-4">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">المهمة <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="<?php echo e(old('title')); ?>" required placeholder="مثال: مراجعة الملف قبل الإرسال"
                               class="w-full rounded-lg border-gray-200 text-sm py-2 px-3">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">القسم</label>
                        <input type="text" name="department" value="<?php echo e(old('department')); ?>" placeholder="قسم" class="w-full rounded-lg border-gray-200 text-sm py-2 px-3" list="dept-suggestions-<?php echo e($designCycle->id); ?>">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">فترة اليوم</label>
                        <input type="text" name="time_slot" value="<?php echo e(old('time_slot')); ?>" placeholder="فترة" class="w-full rounded-lg border-gray-200 text-sm py-2 px-3" list="slot-suggestions-<?php echo e($designCycle->id); ?>">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">موعد</label>
                        <input type="date" name="due_date" value="<?php echo e(old('due_date')); ?>" class="w-full rounded-lg border-gray-200 text-sm py-2 px-3">
                    </div>
                    <div class="md:col-span-2">
                        <button type="submit" class="w-full py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-black hover:bg-indigo-700">
                            <i class="fas fa-plus ml-1"></i> إضافة
                        </button>
                    </div>
                    <div class="md:col-span-12">
                        <input type="text" name="notes" value="<?php echo e(old('notes')); ?>" placeholder="ملاحظات (اختياري)" class="w-full rounded-lg border-gray-200 text-sm py-2 px-3">
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <?php if($designCycle->status === DesignTaskCycle::STATUS_DESIGN_SUBMITTED && ! $designCycle->moderator_delivery_task_id): ?>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-6 shadow-sm space-y-4 w-full">
            <h3 class="font-black text-emerald-950 text-lg">الخطوة التالية: تسليمك النهائي</h3>
            <p class="text-sm text-emerald-900/90">بعد مراجعة جدول التسليمات أعلاه، أنشئ مهمة التسليم النهائي ثم ارفع من «مهامي» وأكمل المهمة.</p>
            <form method="post" action="<?php echo e(route('employee.design-cycles.moderator-delivery.store', $designCycle)); ?>" class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-3xl">
                <?php echo csrf_field(); ?>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">ملاحظات التسليم (اختياري)</label>
                    <textarea name="delivery_notes" rows="2" class="w-full rounded-xl border-gray-200 px-4 py-2"><?php echo e(old('delivery_notes')); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">موعد تسليمك النهائي</label>
                    <input type="date" name="deadline" value="<?php echo e(old('deadline')); ?>" class="w-full rounded-xl border-gray-200 px-4 py-2">
                    <p class="text-xs text-gray-500 mt-1">إن تركتها فارغة يُضبط تلقائياً بعد 3 أيام.</p>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full md:w-auto px-8 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-sm shadow-lg">
                        إنشاء مهمة التسليم النهائي
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <?php if(! in_array($designCycle->status, [DesignTaskCycle::STATUS_COMPLETED, DesignTaskCycle::STATUS_CANCELLED], true)): ?>
        <form method="post" action="<?php echo e(route('employee.design-cycles.cancel', $designCycle)); ?>" onsubmit="return confirm('إلغاء هذا الطلب؟');" class="inline">
            <?php echo csrf_field(); ?>
            <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-semibold underline">إلغاء الطلب</button>
        </form>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/employee/design-cycles/show.blade.php ENDPATH**/ ?>