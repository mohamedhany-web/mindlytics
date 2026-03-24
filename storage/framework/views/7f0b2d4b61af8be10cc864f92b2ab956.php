

<?php $__env->startSection('title', 'تفاصيل الفاتورة'); ?>
<?php $__env->startSection('header', 'تفاصيل الفاتورة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-10 invoice-print-wrapper">
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between" data-print-hide>
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">فاتورة رقم</p>
                <h2 class="mt-1 text-2xl font-bold text-slate-900">#<?php echo e($invoice->invoice_number); ?></h2>
                <p class="text-sm text-slate-500 mt-1">أُنشئت في <?php echo e($invoice->created_at->format('Y-m-d')); ?></p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="<?php echo e(route('admin.invoices.edit', $invoice)); ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:border-sky-300 hover:text-sky-600">
                    <i class="fas fa-edit"></i>
                    تعديل الفاتورة
                </a>
                <button type="button" onclick="window.printInvoice()" class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                    <i class="fas fa-print"></i>
                    طباعة الفاتورة
                </button>
                <a href="<?php echo e(route('admin.invoices.index')); ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    <i class="fas fa-arrow-right"></i>
                    العودة
                </a>
            </div>
        </div>

        <div id="invoice-print-area" class="px-5 py-6 sm:px-8 lg:px-12 space-y-10">
            <div class="flex flex-col gap-6 rounded-2xl border border-slate-200 bg-white/85 p-6 print-card">
                <div class="flex flex-col items-start gap-6 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400">Mindlytics</p>
                        <h1 class="mt-2 text-2xl font-bold text-slate-900">أكاديمية البرمجة</h1>
                        <p class="text-sm text-slate-500">support@mindlytics.academy · 0500 000 000</p>
                    </div>
                    <div class="text-sm text-slate-500 text-left md:text-right">
                        <p><span class="text-slate-400">رقم الفاتورة:</span> <span class="font-semibold text-slate-900">#<?php echo e($invoice->invoice_number); ?></span></p>
                        <p><span class="text-slate-400">تاريخ الإنشاء:</span> <span class="font-semibold text-slate-900"><?php echo e($invoice->created_at->format('Y-m-d')); ?></span></p>
                        <p><span class="text-slate-400">تاريخ الطباعة:</span> <span class="font-semibold text-slate-900"><?php echo e(now()->format('Y-m-d H:i')); ?></span></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50/60 p-5 text-sm text-slate-600 print-strip">
                        <h3 class="text-base font-semibold text-slate-900 mb-3">بيانات العميل</h3>
                        <dl class="space-y-2">
                            <div class="flex justify-between gap-6"><dt>الاسم</dt><dd class="font-semibold text-slate-900"><?php echo e($invoice->user->name ?? 'غير معروف'); ?></dd></div>
                            <div class="flex justify-between gap-6"><dt>رقم الهاتف</dt><dd class="font-semibold text-slate-900"><?php echo e($invoice->user->phone ?? '—'); ?></dd></div>
                            <div class="flex justify-between gap-6"><dt>البريد الإلكتروني</dt><dd class="font-semibold text-slate-900"><?php echo e($invoice->user->email ?? '—'); ?></dd></div>
                            <div class="flex justify-between gap-6"><dt>نوع الفاتورة</dt><dd class="font-semibold text-slate-900"><?php echo e($invoice->type); ?></dd></div>
                        </dl>
                    </div>
                    <div class="rounded-2xl bg-slate-50/60 p-5 text-sm text-slate-600 print-strip">
                        <h3 class="text-base font-semibold text-slate-900 mb-3">حالة الفاتورة</h3>
                        <dl class="space-y-2">
                            <div class="flex justify-between gap-6"><dt>الحالة</dt><dd>
                                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold status-badge" data-status="<?php echo e($invoice->status); ?>">
                                    <span class="h-2 w-2 rounded-full bg-current"></span>
                                    <?php echo e($invoice->status === 'paid' ? 'مدفوعة' : ($invoice->status === 'pending' ? 'معلقة' : 'متأخرة')); ?>

                                </span>
                            </dd></div>
                            <div class="flex justify-between gap-6"><dt>تاريخ الاستحقاق</dt><dd class="font-semibold text-slate-900"><?php echo e($invoice->due_date ? $invoice->due_date->format('Y-m-d') : '—'); ?></dd></div>
                            <div class="flex justify-between gap-6"><dt>آخر تحديث</dt><dd class="font-semibold text-slate-900"><?php echo e($invoice->updated_at->format('Y-m-d H:i')); ?></dd></div>
                            <div class="flex justify-between gap-6"><dt>ملاحظات</dt><dd class="font-semibold text-slate-900"><?php echo e($invoice->notes ?: '—'); ?></dd></div>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white/85 p-6 print-card">
                <h3 class="text-lg font-bold text-slate-900">ملخص الرسوم</h3>
                <table class="mt-4 w-full text-sm text-slate-600">
                    <tbody class="divide-y divide-slate-200">
                        <tr class="flex items-center justify-between py-3">
                            <td>المبلغ الفرعي</td>
                            <td class="font-semibold text-slate-900"><?php echo e(number_format($invoice->subtotal, 2)); ?> ج.م</td>
                        </tr>
                        <?php if($invoice->tax_amount > 0): ?>
                            <tr class="flex items-center justify-between py-3">
                                <td>الضريبة</td>
                                <td class="font-semibold text-slate-900"><?php echo e(number_format($invoice->tax_amount, 2)); ?> ج.م</td>
                            </tr>
                        <?php endif; ?>
                        <?php if($invoice->discount_amount > 0): ?>
                            <tr class="flex items-center justify-between py-3">
                                <td>الخصم</td>
                                <td class="font-semibold text-rose-600">-<?php echo e(number_format($invoice->discount_amount, 2)); ?> ج.م</td>
                            </tr>
                        <?php endif; ?>
                        <tr class="flex items-center justify-between py-3 text-lg font-bold text-slate-900">
                            <td>الإجمالي المستحق</td>
                            <td class="text-sky-600"><?php echo e(number_format($invoice->total_amount, 2)); ?> ج.م</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <?php if($invoice->payments && $invoice->payments->count() > 0): ?>
                <div class="rounded-2xl border border-slate-200 bg-white/85 p-6 print-card">
                    <h3 class="text-lg font-bold text-slate-900">سجل المدفوعات</h3>
                    <table class="mt-4 w-full text-sm text-slate-600">
                        <thead>
                            <tr class="grid grid-cols-3 gap-4 border-b border-slate-200 pb-3 text-xs uppercase tracking-widest text-slate-400">
                                <th class="text-right">رقم الدفعة</th>
                                <th class="text-center">تاريخ الدفع</th>
                                <th class="text-left">المبلغ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <?php $__currentLoopData = $invoice->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="grid grid-cols-3 items-center gap-4 py-3">
                                    <td class="text-right font-semibold text-slate-900"><?php echo e($payment->payment_number); ?></td>
                                    <td class="text-center"><?php echo e($payment->paid_at ? $payment->paid_at->format('Y-m-d') : '—'); ?></td>
                                    <td class="text-left font-semibold text-slate-900"><?php echo e(number_format($payment->amount, 2)); ?> ج.م</td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php $__env->startPush('styles'); ?>
<style>
    .print-strip { border: 1px solid rgba(226, 232, 240, 0.6); }
    .status-badge[data-status="paid"] { background: rgba(34, 197, 94, 0.18) !important; color: #15803d !important; }
    .status-badge[data-status="pending"] { background: rgba(245, 158, 11, 0.18) !important; color: #b45309 !important; }
    .status-badge[data-status="overdue"],
    .status-badge[data-status="unpaid"],
    .status-badge[data-status="cancelled"] { background: rgba(248, 113, 113, 0.18) !important; color: #b91c1c !important; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    window.printInvoice = function () {
        const printArea = document.getElementById('invoice-print-area');
        if (!printArea) return;

        const printWindow = window.open('', '_blank', 'width=900,height=1200');
        if (!printWindow) return;

        const headContent = document.head.cloneNode(true);
        const bodyContent = `
            <div class="invoice-print-container">
                <header class="invoice-print-header">
                    <div>
                        <p class="brand-label">Mindlytics</p>
                        <h1>أكاديمية البرمجة</h1>
                        <p class="brand-meta">support@mindlytics.academy · 0500 000 000</p>
                    </div>
                    <div class="invoice-meta">
                        <p><span>رقم الفاتورة:</span> #${'<?php echo e($invoice->invoice_number); ?>'}</p>
                        <p><span>تاريخ الإنشاء:</span> <?php echo e($invoice->created_at->format('Y-m-d')); ?></p>
                        <p><span>تاريخ الطباعة:</span> ${new Date().toLocaleString('ar-EG')}</p>
                    </div>
                </header>
                <main class="invoice-print-body">
                    ${printArea.innerHTML}
                </main>
                <footer class="invoice-print-footer">
                    <p>توقيع الإدارة</p>
                    <p class="signature-line"></p>
                </footer>
            </div>
        `;

        printWindow.document.open();
        printWindow.document.write('<!doctype html><html lang="ar" dir="rtl"></html>');
        printWindow.document.head.innerHTML = headContent.innerHTML;
        printWindow.document.body.innerHTML = bodyContent;

        const style = printWindow.document.createElement('style');
        style.innerHTML = `
            @page { size: A4 portrait; margin: 12mm 15mm; }
            body { font-family: 'IBM Plex Sans Arabic', system-ui, sans-serif; color: #0f172a; background: #fff; }
            .invoice-print-container { max-width: 760px; margin: 0 auto; display: flex; flex-direction: column; min-height: 100vh; }
            .invoice-print-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 24px; border-bottom: 1px solid #d8e3f0; padding-bottom: 16px; margin-bottom: 24px; }
            .invoice-meta p { margin: 0 0 4px 0; font-size: 12px; color: #334155; }
            .invoice-meta span { font-weight: 600; color: #0f172a; }
            .brand-label { text-transform: uppercase; letter-spacing: 4px; font-size: 11px; color: #64748b; margin-bottom: 4px; }
            .brand-meta { font-size: 12px; color: #475569; margin-top: 4px; }
            .invoice-print-body { flex: 1; display: flex; flex-direction: column; gap: 20px; }
            .invoice-print-body .print-card { border: 1px solid #d9e3f0; background: #ffffff; border-radius: 12px; padding: 18px 22px; }
            .invoice-print-body .print-strip { border: 1px solid #d9e3f0; background: #f8fafc; border-radius: 12px; padding: 18px 22px; }
            .invoice-print-body h3 { margin-top: 0; margin-bottom: 12px; font-size: 16px; }
            .invoice-print-body table { width: 100%; border-collapse: collapse; }
            .invoice-print-body thead { font-size: 11px; text-transform: uppercase; letter-spacing: 2px; color: #64748b; }
            .invoice-print-body tbody tr { border-bottom: 1px solid #eef2f7; }
            .invoice-print-body td, .invoice-print-body th { padding: 8px 0; font-size: 13px; }
            .invoice-print-footer { margin-top: 32px; padding-top: 16px; border-top: 1px solid #d8e3f0; text-align: left; font-size: 12px; color: #475569; }
            .signature-line { margin-top: 12px; width: 180px; height: 1px; background: #cbd5f5; }
        `;
        printWindow.document.head.appendChild(style);

        // wait for assets then print
        setTimeout(() => {
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        }, 500);
    };
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/invoices/show.blade.php ENDPATH**/ ?>