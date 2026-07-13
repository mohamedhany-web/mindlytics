

<?php $__env->startSection('title', 'إرسال دعوة جروب من Excel'); ?>
<?php $__env->startSection('header', 'قسم الواتساب'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $isConnected = (bool) ($connectionMeta['can_send'] ?? false);
    $preview = session('excel_preview');
?>

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <?php echo $__env->make('admin.whatsapp._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.whatsapp._page-header', [
        'title' => 'إرسال دعوة جروب واتساب من Excel',
        'subtitle' => 'ارفع الأرقام → رتّبها وصحّحها → أنشئ قالب الدعوة لـ Meta → أرسل دفعة تظهر في المحادثات',
        'icon' => 'fas fa-file-excel',
        'actions' => '
            <a href="' . route('admin.whatsapp.excel-campaign.sample') . '" class="' . $waBtnSecondary . '"><i class="fas fa-download"></i> نموذج Excel</a>
            <a href="' . route('admin.whatsapp.batches.index') . '" class="' . $waBtnSecondary . '"><i class="fas fa-layer-group"></i> دفعات الإرسال</a>
            <a href="' . route('admin.whatsapp.inbox') . '" class="' . $waBtnSecondary . '"><i class="fas fa-inbox"></i> المحادثات</a>
        ',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if(! $isOfficial || ! $isConnected): ?>
        <div class="rounded-2xl border-2 border-amber-300 bg-amber-50 p-5 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-amber-900">الواتساب غير جاهز للإرسال</h3>
                <p class="text-sm text-amber-800 mt-1">أكمل ربط Meta Cloud API قبل إنشاء القالب أو الإرسال الجماعي.</p>
            </div>
            <a href="<?php echo e(route('admin.whatsapp.settings')); ?>" class="<?php echo e($waBtnPrimary); ?>">إعدادات الربط</a>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <section class="<?php echo e($waSectionClass); ?> xl:col-span-2">
            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
                <h3 class="font-bold text-slate-900 flex items-center gap-2"><i class="fas fa-users text-emerald-600"></i> بيانات الدعوة والجروب</h3>
            </div>
            <div class="p-5 space-y-5">
                <form method="POST" action="<?php echo e(route('admin.whatsapp.excel-campaign.template')); ?>" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">اسم الجروب <span class="text-rose-500">*</span></label>
                            <input type="text" name="group_name" value="<?php echo e(old('group_name')); ?>" required maxlength="120"
                                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm" placeholder="مثال: جروب دفعة مارس">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">لينك جروب واتساب <span class="text-rose-500">*</span></label>
                            <input type="url" name="group_link" value="<?php echo e(old('group_link')); ?>" required maxlength="500"
                                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm" dir="ltr" placeholder="https://chat.whatsapp.com/XXXX">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">تسمية القالب (للعرض)</label>
                            <input type="text" name="display_name" value="<?php echo e(old('display_name')); ?>" maxlength="255"
                                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm" placeholder="مثال: دعوة جروب دفعة مارس">
                            <p class="text-xs text-slate-500 mt-1">اسم واضح للموظفين — يُملأ تلقائياً من اسم الجروب إن تركته فارغاً.</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">اسم القالب في Meta (اختياري)</label>
                            <input type="text" name="template_name" value="<?php echo e(old('template_name')); ?>" maxlength="512"
                                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono" dir="ltr" placeholder="group_invite_....">
                            <p class="text-xs text-slate-500 mt-1">حروف إنجليزية صغيرة وأرقام و _ فقط. يُولَّد تلقائياً إن تركته فارغاً.</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">نص قالب الدعوة</label>
                            <textarea name="body_text" rows="8" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm" placeholder="نص القالب"><?php echo e(old('body_text', $defaultBody)); ?></textarea>
                            <div class="mt-2 flex flex-wrap gap-2 text-[11px]">
                                <?php $__currentLoopData = $variableLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="inline-flex px-2 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100"><?php echo '{{'.$k.'}}'; ?> = <?php echo e($label); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <p class="text-xs text-amber-700 mt-2">مهم: ضع كود الدعوة فقط داخل المتغير بعد <code dir="ltr">chat.whatsapp.com/</code> — Meta ترفض الرابط الكامل داخل المثال.</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 pt-2 border-t border-slate-200">
                        <button type="submit" name="submit_now" value="1" class="<?php echo e($waBtnPrimary); ?>" <?php echo e((! $isConnected) ? 'disabled' : ''); ?>>
                            <i class="fas fa-paper-plane"></i> إنشاء القالب وإرساله لـ Meta
                        </button>
                        <button type="submit" name="submit_now" value="0" class="<?php echo e($waBtnSecondary); ?>" <?php echo e((! $isConnected) ? 'disabled' : ''); ?>>
                            حفظ كمسودة فقط
                        </button>
                    </div>
                </form>
                <form method="POST" action="<?php echo e(route('admin.whatsapp.excel-campaign.sync')); ?>" class="pt-2">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="<?php echo e($waBtnSecondary); ?>"><i class="fas fa-sync"></i> مزامنة حالة القوالب</button>
                </form>
            </div>
        </section>

        <section class="<?php echo e($waSectionClass); ?>">
            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
                <h3 class="font-bold text-slate-900">قوالب الدعوة الأخيرة</h3>
            </div>
            <div class="p-4 space-y-3 max-h-[28rem] overflow-y-auto">
                <?php $__empty_1 = true; $__currentLoopData = $recentDrafts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tpl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="rounded-xl border border-slate-200 p-3 text-sm">
                        <div class="font-semibold text-slate-800 truncate"><?php echo e($tpl->displayTitle()); ?></div>
                        <div class="text-[11px] text-slate-500 font-mono truncate" dir="ltr"><?php echo e($tpl->name); ?></div>
                        <div class="mt-1 flex items-center justify-between gap-2">
                            <span class="text-xs px-2 py-0.5 rounded-full
                                <?php echo e($tpl->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($tpl->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600')); ?>">
                                <?php echo e($tpl->statusLabel()); ?>

                            </span>
                            <span class="text-[11px] text-slate-400"><?php echo e($tpl->updated_at?->diffForHumans()); ?></span>
                        </div>
                        <?php if($tpl->rejection_reason): ?>
                            <p class="text-xs text-rose-600 mt-2"><?php echo e($tpl->rejection_reason); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-sm text-slate-500 text-center py-8">لا توجد قوالب دعوة بعد.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <section class="<?php echo e($waSectionClass); ?>">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="font-bold text-slate-900 flex items-center gap-2"><i class="fas fa-file-excel text-emerald-600"></i> ملف الأرقام والإرسال</h3>
            <p class="text-xs text-slate-500">الأعمدة: الاسم (اختياري) + الهاتف (إلزامي). الأرقام تُطبَّع لصيغة مصر الدولية تلقائياً.</p>
        </div>
        <div class="p-5 space-y-5">
            <form method="POST" action="<?php echo e(route('admin.whatsapp.excel-campaign.preview')); ?>" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-3 items-end">
                <?php echo csrf_field(); ?>
                <div class="flex-1 w-full">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">رفع Excel للمعاينة والترتيب</label>
                    <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required
                           class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm bg-white">
                </div>
                <button type="submit" class="<?php echo e($waBtnSecondary); ?>"><i class="fas fa-list-check"></i> معاينة الأرقام</button>
            </form>

            <?php if($preview): ?>
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/40 p-4">
                    <div class="flex flex-wrap gap-3 text-sm mb-3">
                        <span class="font-semibold text-emerald-800">صالح: <?php echo e(number_format($preview['valid_count'] ?? 0)); ?></span>
                        <span class="text-amber-700">متخطى: <?php echo e(number_format($preview['skipped_count'] ?? 0)); ?></span>
                        <span class="text-slate-500">صفوف: <?php echo e(number_format($preview['total_rows'] ?? 0)); ?></span>
                        <span class="text-slate-400" dir="ltr"><?php echo e($preview['file_name'] ?? ''); ?></span>
                    </div>
                    <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-slate-600">
                                <tr>
                                    <th class="px-3 py-2 text-right">الاسم</th>
                                    <th class="px-3 py-2 text-right">الرقم الأصلي</th>
                                    <th class="px-3 py-2 text-right">بعد التطبيع</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php $__currentLoopData = ($preview['valid'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="px-3 py-2"><?php echo e($row['name'] ?? '—'); ?></td>
                                        <td class="px-3 py-2 font-mono text-xs" dir="ltr"><?php echo e($row['raw_phone'] ?? '—'); ?></td>
                                        <td class="px-3 py-2 font-mono text-xs text-emerald-700" dir="ltr"><?php echo e($row['phone'] ?? '—'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if(! empty($preview['skipped'])): ?>
                        <details class="mt-3 text-xs text-slate-600">
                            <summary class="cursor-pointer font-semibold">عرض المتخطّى (عينة)</summary>
                            <ul class="mt-2 space-y-1 list-disc pr-5">
                                <?php $__currentLoopData = $preview['skipped']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>سطر <?php echo e($skip['row'] ?? '?'); ?>: <?php echo e($skip['reason'] ?? ''); ?> <?php if(!empty($skip['value'])): ?> — <span dir="ltr"><?php echo e($skip['value']); ?></span><?php endif; ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </details>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('admin.whatsapp.excel-campaign.send')); ?>" enctype="multipart/form-data" class="space-y-4 border-t border-slate-200 pt-5">
                <?php echo csrf_field(); ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">ملف Excel للإرسال <span class="text-rose-500">*</span></label>
                        <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">القالب المعتمد <span class="text-rose-500">*</span></label>
                        <select name="template_id" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm">
                            <option value="">اختر قالباً معتمداً من Meta</option>
                            <?php $__currentLoopData = $approvedTemplates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tpl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($tpl->id); ?>" <?php if((string) old('template_id', session('created_template_id')) === (string) $tpl->id): echo 'selected'; endif; ?>>
                                    <?php echo e($tpl->displayLabel()); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">اسم الجروب <span class="text-rose-500">*</span></label>
                        <input type="text" name="group_name" value="<?php echo e(old('group_name')); ?>" required maxlength="120"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">لينك الجروب <span class="text-rose-500">*</span></label>
                        <input type="url" name="group_link" value="<?php echo e(old('group_link')); ?>" required maxlength="500"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm" dir="ltr">
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="<?php echo e($waBtnPrimary); ?>" <?php echo e((! $isConnected) ? 'disabled' : ''); ?>

                            onclick="return confirm('بدء إرسال دعوات الجروب للمستلمين في الملف؟')">
                        <i class="fas fa-paper-plane"></i> إرسال والتحويل لدفعات الإرسال
                    </button>
                    <a href="<?php echo e(route('admin.whatsapp.templates.index')); ?>" class="<?php echo e($waBtnSecondary); ?>">إدارة كل القوالب</a>
                </div>
            </form>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/whatsapp/excel-campaign.blade.php ENDPATH**/ ?>