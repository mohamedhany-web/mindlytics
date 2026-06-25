<?php $__env->startSection('title', 'المديونية والذمم'); ?>
<?php $__env->startSection('header', 'المديونية والذمم'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $rec = $snapshot['receivables'] ?? [];
    $pay = $snapshot['payables'] ?? [];
?>
<div class="space-y-6" x-data="receivablesPage()">

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">المديونية والذمم المالية</h2>
                    <p class="text-xs text-slate-600">ذمم مدينة، ديون مسجّلة، تمويل ذاتي — مع تتبع من استلفت منه وإيداع في المحفظة.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('admin.accounting.hub')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-calculator text-sky-600"></i> مركز المحاسبة
                </a>
                <a href="<?php echo e(route('admin.accounting.insights')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-sky-800 rounded-xl border border-sky-300 bg-sky-50 hover:bg-sky-100">
                    <i class="fas fa-chart-bar"></i> المؤشرات
                </a>
                <button type="button" @click="showDebtForm = !showDebtForm"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-white rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 shadow">
                    <i class="fas fa-plus"></i> تسجيل دين جديد
                </button>
            </div>
        </div>
    </section>

    <?php if(session('success')): ?>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900 text-sm font-semibold"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-900 text-sm">
            <ul class="list-disc list-inside"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
        </div>
    <?php endif; ?>

    
    <section x-show="showDebtForm" x-cloak class="rounded-2xl bg-white border-2 border-amber-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 bg-gradient-to-r from-amber-50 to-orange-50 border-b border-amber-200">
            <h3 class="text-lg font-black text-amber-900"><i class="fas fa-file-signature ml-2"></i>تسجيل دين / مديونية</h3>
            <p class="text-xs text-amber-800 mt-1">سجّل من استلفت منه الفلوس — ويمكنك إيداع المبلغ مباشرة في محفظة الأكاديمية.</p>
        </div>
        <form action="<?php echo e(route('admin.accounting.receivables.debts.store')); ?>" method="POST" class="p-5 sm:p-6 space-y-5">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2">نوع الدين <span class="text-red-500">*</span></label>
                    <select name="direction" x-model="debtDirection" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-500">
                        <option value="payable">دين علينا — استلفنا فلوس</option>
                        <option value="receivable">دين لنا — شخص مديون لنا</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2">اسم الطرف <span class="text-red-500">*</span></label>
                    <input type="text" name="party_name" value="<?php echo e(old('party_name')); ?>" required placeholder="مثال: أحمد محمد / البنك / شريك"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2">صلة القرابة / النوع</label>
                    <input type="text" name="party_relation" value="<?php echo e(old('party_relation')); ?>" placeholder="شريك، قريب، بنك، صديق..."
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2">رقم التواصل</label>
                    <input type="text" name="party_phone" value="<?php echo e(old('party_phone')); ?>" placeholder="اختياري"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2">المبلغ (ج.م) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" step="0.01" min="0.01" value="<?php echo e(old('amount')); ?>" required
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2">تاريخ الدين <span class="text-red-500">*</span></label>
                    <input type="date" name="debt_date" value="<?php echo e(old('debt_date', date('Y-m-d'))); ?>" required
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2">تاريخ الاستحقاق</label>
                    <input type="date" name="due_date" value="<?php echo e(old('due_date')); ?>"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-2">وصف / سبب الدين</label>
                    <input type="text" name="title" value="<?php echo e(old('title')); ?>" placeholder="مثال: قرض تشغيلي لشهر رمضان"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            
            <div x-show="debtDirection === 'payable'" class="rounded-xl border border-sky-200 bg-sky-50/80 p-4 space-y-3">
                <p class="text-sm font-bold text-sky-900"><i class="fas fa-wallet ml-1"></i> إيداع في محفظة الأكاديمية (اختياري)</p>
                <p class="text-xs text-sky-800">إذا استلفت الفلوس ونزلتها في محفظة (فودافون كاش، إنستاباي، بنك...) فعّل الخيار أدناه.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2">المحفظة</label>
                        <select name="wallet_id" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm bg-white focus:ring-2 focus:ring-sky-500">
                            <option value="">— بدون إيداع —</option>
                            <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($w->id); ?>" <?php if((string) old('wallet_id') === (string) $w->id): echo 'selected'; endif; ?>>
                                    <?php echo e($w->name); ?> — <?php echo e(\App\Models\Wallet::typeLabel($w->type)); ?> (<?php echo e(number_format($w->balance, 2)); ?> ج.م)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-semibold text-sky-900">
                            <input type="checkbox" name="deposit_to_wallet" value="1" class="rounded border-sky-400 text-sky-600 focus:ring-sky-500" <?php if(old('deposit_to_wallet')): echo 'checked'; endif; ?>>
                            إيداع المبلغ كاملاً في المحفظة المختارة
                        </label>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2">ملاحظات</label>
                <textarea name="notes" rows="2" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-500" placeholder="أي تفاصيل إضافية..."><?php echo e(old('notes')); ?></textarea>
            </div>

            <div class="flex flex-wrap gap-2">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white rounded-xl bg-amber-600 hover:bg-amber-700">
                    <i class="fas fa-save"></i> حفظ الدين
                </button>
                <button type="button" @click="showDebtForm = false" class="px-5 py-2.5 text-sm font-semibold rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50">إلغاء</button>
            </div>
        </form>
    </section>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="dashboard-stat-card rounded-2xl border-2 border-emerald-200/70 bg-gradient-to-br from-white via-white to-emerald-50/60 p-5 shadow-lg">
            <p class="text-xs font-bold text-emerald-800/80">ذمم مدينة (لنا)</p>
            <p class="text-2xl font-black text-emerald-700 mt-2"><?php echo e(number_format($rec['total'] ?? 0, 2)); ?></p>
            <p class="text-[11px] text-emerald-700/70 mt-1">فواتير + أوفلاين + أقساط + ديون مسجّلة</p>
        </div>
        <div class="dashboard-stat-card rounded-2xl border-2 border-rose-200/70 bg-gradient-to-br from-white via-white to-rose-50/60 p-5 shadow-lg">
            <p class="text-xs font-bold text-rose-800/80">التزامات (علينا)</p>
            <p class="text-2xl font-black text-rose-700 mt-2"><?php echo e(number_format($pay['total'] ?? 0, 2)); ?></p>
            <p class="text-[11px] text-rose-700/70 mt-1">سحوبات + تمويل ذاتي + ديون مستلفة</p>
        </div>
        <div class="dashboard-stat-card rounded-2xl border-2 border-amber-200/70 bg-gradient-to-br from-white via-white to-amber-50/60 p-5 shadow-lg">
            <p class="text-xs font-bold text-amber-800/80">ديون مستلفة (مسجّلة)</p>
            <p class="text-2xl font-black text-amber-800 mt-2"><?php echo e(number_format($debtStats['payable_remaining'] ?? 0, 2)); ?></p>
            <p class="text-[11px] text-amber-700/70 mt-1"><?php echo e($debtStats['payable_count'] ?? 0); ?> دين نشط</p>
        </div>
        <div class="dashboard-stat-card rounded-2xl border-2 border-violet-200/70 bg-gradient-to-br from-white via-white to-violet-50/60 p-5 shadow-lg">
            <p class="text-xs font-bold text-violet-800/80">ديون لنا (مسجّلة)</p>
            <p class="text-2xl font-black text-violet-800 mt-2"><?php echo e(number_format($debtStats['receivable_remaining'] ?? 0, 2)); ?></p>
            <p class="text-[11px] text-violet-700/70 mt-1"><?php echo e($debtStats['receivable_count'] ?? 0); ?> دين نشط</p>
        </div>
    </div>

    
    <section class="rounded-2xl border-2 shadow-lg overflow-hidden <?php echo e(($breakEven['tone'] ?? '') === 'good' ? 'border-emerald-300 bg-emerald-50/80' : (($breakEven['tone'] ?? '') === 'bad' ? 'border-rose-300 bg-rose-50/80' : 'border-amber-300 bg-amber-50/80')); ?>">
        <div class="px-5 py-4 flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-bold text-slate-600">بر الأمان — هذا الشهر</p>
                <h3 class="text-xl font-black text-slate-900 mt-1"><?php echo e($breakEven['label'] ?? '—'); ?></h3>
                <p class="text-sm text-slate-700 mt-1"><?php echo e($breakEven['detail'] ?? ''); ?></p>
            </div>
            <div class="text-sm space-y-1 text-left">
                <p>تمويل ذاتي تراكمي: <strong class="text-amber-700"><?php echo e(number_format($pocketExpensesTotal, 2)); ?></strong> ج.م</p>
                <p>ديون مستلفة متبقية: <strong class="text-rose-700"><?php echo e(number_format($debtStats['payable_remaining'] ?? 0, 2)); ?></strong> ج.م</p>
            </div>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-rose-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 bg-gradient-to-r from-rose-50 to-red-50 border-b border-rose-200 flex flex-wrap justify-between items-center gap-2">
            <h3 class="text-base font-black text-rose-900"><i class="fas fa-arrow-circle-up ml-2"></i>ديون علينا — استلفنا من...</h3>
            <span class="text-xs font-bold px-2 py-1 rounded-lg bg-rose-100 text-rose-800"><?php echo e(number_format($debtStats['payable_remaining'] ?? 0, 2)); ?> ج.م متبقي</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 border-b">
                        <th class="px-4 py-3 text-right font-semibold">الرقم</th>
                        <th class="px-4 py-3 text-right font-semibold">من (الطرف)</th>
                        <th class="px-4 py-3 text-right font-semibold">الوصف</th>
                        <th class="px-4 py-3 text-right font-semibold">المحفظة</th>
                        <th class="px-4 py-3 text-left font-semibold">الإجمالي</th>
                        <th class="px-4 py-3 text-left font-semibold">المدفوع</th>
                        <th class="px-4 py-3 text-left font-semibold">المتبقي</th>
                        <th class="px-4 py-3 text-right font-semibold">التاريخ</th>
                        <th class="px-4 py-3 text-right font-semibold">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $manualDebtsPayable; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-4 py-3 font-mono text-xs font-bold text-slate-700"><?php echo e($debt->debt_number); ?></td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-900"><?php echo e($debt->party_name); ?></div>
                            <?php if($debt->party_relation): ?><div class="text-xs text-slate-500"><?php echo e($debt->party_relation); ?></div><?php endif; ?>
                            <?php if($debt->party_phone): ?><div class="text-xs text-slate-400"><?php echo e($debt->party_phone); ?></div><?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($debt->title ?: '—'); ?></td>
                        <td class="px-4 py-3 text-xs">
                            <?php if($debt->wallet): ?>
                                <span class="text-sky-700"><?php echo e($debt->wallet->name); ?></span>
                                <?php if($debt->deposited_to_wallet): ?><span class="block text-emerald-600 font-semibold">✓ مُودَع</span><?php endif; ?>
                            <?php else: ?>
                                <span class="text-slate-400">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-left font-semibold"><?php echo e(number_format($debt->amount, 2)); ?></td>
                        <td class="px-4 py-3 text-left text-emerald-700"><?php echo e(number_format($debt->paid_amount, 2)); ?></td>
                        <td class="px-4 py-3 text-left font-bold text-rose-700"><?php echo e(number_format($debt->remaining_amount, 2)); ?></td>
                        <td class="px-4 py-3 text-slate-600"><?php echo e($debt->debt_date?->format('Y-m-d')); ?></td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <?php if((float)$debt->remaining_amount > 0): ?>
                            <button type="button" @click="openRepay(<?php echo e(json_encode($debt)); ?>)"
                                    class="text-emerald-600 hover:text-emerald-800 text-xs font-bold px-2 py-1 rounded-lg hover:bg-emerald-50">سداد</button>
                            <?php endif; ?>
                            <form action="<?php echo e(route('admin.accounting.receivables.debts.cancel', $debt)); ?>" method="POST" class="inline" onsubmit="return confirm('إلغاء هذا الدين؟');">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-bold px-2 py-1 rounded-lg hover:bg-rose-50">إلغاء</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="9" class="px-4 py-10 text-center text-slate-500">لا توجد ديون مسجّلة — اضغط «تسجيل دين جديد»</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-emerald-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-emerald-200">
            <h3 class="text-base font-black text-emerald-900"><i class="fas fa-arrow-circle-down ml-2"></i>ديون لنا — مستحق لنا من...</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 border-b">
                        <th class="px-4 py-3 text-right font-semibold">الرقم</th>
                        <th class="px-4 py-3 text-right font-semibold">المدين</th>
                        <th class="px-4 py-3 text-left font-semibold">المتبقي</th>
                        <th class="px-4 py-3 text-right font-semibold">التاريخ</th>
                        <th class="px-4 py-3 text-right font-semibold">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $manualDebtsReceivable; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-4 py-3 font-mono text-xs"><?php echo e($debt->debt_number); ?></td>
                        <td class="px-4 py-3 font-semibold"><?php echo e($debt->party_name); ?></td>
                        <td class="px-4 py-3 text-left font-bold text-emerald-700"><?php echo e(number_format($debt->remaining_amount, 2)); ?></td>
                        <td class="px-4 py-3"><?php echo e($debt->debt_date?->format('Y-m-d')); ?></td>
                        <td class="px-4 py-3">
                            <?php if((float)$debt->remaining_amount > 0): ?>
                            <button type="button" @click="openRepay(<?php echo e(json_encode($debt)); ?>)" class="text-emerald-600 text-xs font-bold">تحصيل</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">لا توجد</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b bg-slate-50 font-bold text-slate-800">فواتير معلقة (تلقائي)</div>
            <div class="overflow-x-auto max-h-64 overflow-y-auto">
                <table class="min-w-full text-sm">
                    <tbody class="divide-y">
                        <?php $__empty_1 = true; $__currentLoopData = $pendingInvoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr><td class="px-3 py-2"><a href="<?php echo e(route('admin.invoices.show', $inv)); ?>" class="text-sky-600"><?php echo e($inv->invoice_number); ?></a></td><td class="px-3 py-2"><?php echo e($inv->user->name ?? '—'); ?></td><td class="px-3 py-2 text-left font-semibold"><?php echo e(number_format($inv->total_amount, 2)); ?></td></tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td class="px-3 py-6 text-center text-slate-500">لا توجد</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b bg-slate-50 font-bold text-slate-800">متبقي أوفلاين (تلقائي)</div>
            <div class="overflow-x-auto max-h-64 overflow-y-auto">
                <table class="min-w-full text-sm">
                    <tbody class="divide-y">
                        <?php $__empty_1 = true; $__currentLoopData = $offlineDebts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr><td class="px-3 py-2"><?php echo e($row->student->name ?? '—'); ?></td><td class="px-3 py-2 text-xs"><?php echo e(Str::limit($row->course->title ?? '—', 30)); ?></td><td class="px-3 py-2 text-left font-semibold text-rose-700"><?php echo e(number_format($row->remaining_amount, 2)); ?></td></tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="3" class="px-3 py-6 text-center text-slate-500">لا توجد</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    
    <div x-show="showRepayModal" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showRepayModal = false">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full border border-slate-200">
            <div class="px-5 py-4 border-b bg-slate-50 rounded-t-2xl">
                <h3 class="font-black text-slate-900">تسجيل سداد / تحصيل</h3>
                <p class="text-xs text-slate-600 mt-1">الطرف: <strong x-text="repayDebt?.party_name"></strong> — متبقي: <strong class="text-rose-700" x-text="repayDebt?.remaining_amount"></strong> ج.م</p>
            </div>
            <form :action="repayAction" method="POST" class="p-5 space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">المبلغ *</label>
                    <input type="number" name="amount" step="0.01" min="0.01" :max="repayDebt?.remaining_amount" required
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">تاريخ السداد *</label>
                    <input type="date" name="paid_at" value="<?php echo e(date('Y-m-d')); ?>" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
                </div>
                <div x-show="repayDebt?.direction === 'payable'" class="space-y-2 rounded-xl border border-sky-200 bg-sky-50 p-3">
                    <label class="block text-xs font-semibold text-sky-900">السداد من محفظة (اختياري)</label>
                    <select name="wallet_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-white">
                        <option value="">— خارج المحفظة —</option>
                        <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($w->id); ?>"><?php echo e($w->name); ?> (<?php echo e(number_format($w->balance, 2)); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <label class="inline-flex items-center gap-2 text-xs font-semibold text-sky-900">
                        <input type="checkbox" name="withdraw_from_wallet" value="1" class="rounded text-sky-600">
                        خصم المبلغ من رصيد المحفظة
                    </label>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">ملاحظات</label>
                    <input type="text" name="notes" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" @click="showRepayModal = false" class="px-4 py-2 text-sm font-semibold rounded-xl border border-slate-300">إلغاء</button>
                    <button type="submit" class="px-4 py-2 text-sm font-bold text-white rounded-xl bg-emerald-600 hover:bg-emerald-700">تسجيل السداد</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function receivablesPage() {
    return {
        showDebtForm: <?php echo e($errors->any() || old('party_name') ? 'true' : 'false'); ?>,
        debtDirection: <?php echo json_encode(old('direction', 'payable'), 512) ?>,
        showRepayModal: false,
        repayDebt: null,
        repayAction: '',
        openRepay(debt) {
            this.repayDebt = debt;
            this.repayAction = "<?php echo e(url('admin/accounting/receivables/debts')); ?>/" + debt.id + "/repayment";
            this.showRepayModal = true;
        }
    };
}
document.addEventListener('alpine:init', () => Alpine.data('receivablesPage', receivablesPage));
if (window.Alpine) Alpine.data('receivablesPage', receivablesPage);
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/accounting/receivables.blade.php ENDPATH**/ ?>