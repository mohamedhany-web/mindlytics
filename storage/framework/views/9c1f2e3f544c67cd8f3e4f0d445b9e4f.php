

<?php $__env->startSection('title', 'تفاصيل اتفاقية الموظف - Mindlytics'); ?>
<?php $__env->startSection('header', 'تفاصيل اتفاقية الموظف'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <!-- Header -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
                    <i class="fas fa-file-contract text-emerald-600"></i>
                    <?php echo e($employeeAgreement->title); ?>

                </h2>
                <p class="text-sm text-slate-500 mt-2">رقم الاتفاقية: <span class="font-semibold"><?php echo e($employeeAgreement->agreement_number); ?></span></p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?php echo e(route('admin.employee-agreements.edit', $employeeAgreement)); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-amber-600 rounded-xl shadow hover:bg-amber-700 transition-all">
                    <i class="fas fa-edit"></i>
                    تعديل
                </a>
                <a href="<?php echo e(route('admin.employee-agreements.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition-all">
                    <i class="fas fa-arrow-right"></i>
                    رجوع
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-5 sm:p-8">
            <div class="rounded-2xl border border-slate-200 bg-white/70 p-5">
                <p class="text-xs font-semibold text-slate-500 mb-2">الراتب الأساسي</p>
                <p class="text-2xl font-bold text-slate-900"><?php echo e(number_format($employeeAgreement->salary, 2)); ?> ج.م</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white/70 p-5">
                <p class="text-xs font-semibold text-slate-500 mb-2">إجمالي الخصومات</p>
                <p class="text-2xl font-bold text-red-600"><?php echo e(number_format($stats['total_deductions'], 2)); ?> ج.م</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white/70 p-5">
                <p class="text-xs font-semibold text-slate-500 mb-2">إجمالي المدفوعات</p>
                <p class="text-2xl font-bold text-emerald-600"><?php echo e(number_format($stats['total_payments'], 2)); ?> ج.م</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white/70 p-5">
                <p class="text-xs font-semibold text-slate-500 mb-2">الدفعات المعلقة</p>
                <p class="text-2xl font-bold text-amber-600"><?php echo e($stats['pending_payments']); ?></p>
            </div>
        </div>
    </section>

    <!-- Agreement Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <!-- Basic Info -->
            <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900">معلومات الاتفاقية</h3>
                </div>
                <div class="px-5 py-6 sm:px-8 lg:px-12 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">الموظف</p>
                            <p class="text-sm font-semibold text-slate-900"><?php echo e($employeeAgreement->employee->name); ?></p>
                            <p class="text-xs text-slate-500"><?php echo e($employeeAgreement->employee->email); ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">الراتب</p>
                            <p class="text-sm font-semibold text-slate-900"><?php echo e(number_format($employeeAgreement->salary, 2)); ?> ج.م</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">الحالة</p>
                            <?php
                                $statusBadges = [
                                    'draft' => ['label' => 'مسودة', 'classes' => 'bg-slate-100 text-slate-700'],
                                    'active' => ['label' => 'نشط', 'classes' => 'bg-emerald-100 text-emerald-700'],
                                    'suspended' => ['label' => 'معلق', 'classes' => 'bg-amber-100 text-amber-700'],
                                    'terminated' => ['label' => 'منتهي', 'classes' => 'bg-rose-100 text-rose-700'],
                                    'completed' => ['label' => 'مكتمل', 'classes' => 'bg-blue-100 text-blue-700'],
                                ];
                                $status = $statusBadges[$employeeAgreement->status] ?? ['label' => $employeeAgreement->status, 'classes' => 'bg-slate-100 text-slate-700'];
                            ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold <?php echo e($status['classes']); ?>">
                                <?php echo e($status['label']); ?>

                            </span>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">تاريخ البدء</p>
                            <p class="text-sm font-semibold text-slate-900"><?php echo e($employeeAgreement->start_date->format('Y-m-d')); ?></p>
                        </div>
                        <?php if($employeeAgreement->end_date): ?>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">تاريخ الانتهاء</p>
                            <p class="text-sm font-semibold text-slate-900"><?php echo e($employeeAgreement->end_date->format('Y-m-d')); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if($employeeAgreement->description): ?>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">الوصف</p>
                        <p class="text-sm text-slate-700"><?php echo e($employeeAgreement->description); ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if($employeeAgreement->contract_terms): ?>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">شروط العقد</p>
                        <div class="text-sm text-slate-700 whitespace-pre-line"><?php echo e($employeeAgreement->contract_terms); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if($employeeAgreement->agreement_terms): ?>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">بنود الاتفاقية</p>
                        <div class="text-sm text-slate-700 whitespace-pre-line"><?php echo e($employeeAgreement->agreement_terms); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if($employeeAgreement->notes): ?>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">ملاحظات</p>
                        <div class="text-sm text-slate-700 whitespace-pre-line"><?php echo e($employeeAgreement->notes); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Deductions -->
            <?php if($employeeAgreement->deductions->count() > 0): ?>
            <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900">الخصومات</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">رقم الخصم</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">العنوان</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">النوع</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">المبلغ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">التاريخ</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            <?php $__currentLoopData = $employeeAgreement->deductions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deduction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900"><?php echo e($deduction->deduction_number); ?></td>
                                <td class="px-6 py-4 text-sm text-slate-900"><?php echo e($deduction->title); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-800">
                                        <?php if($deduction->type === 'tax'): ?> ضريبة
                                        <?php elseif($deduction->type === 'insurance'): ?> تأمين
                                        <?php elseif($deduction->type === 'loan'): ?> قرض
                                        <?php elseif($deduction->type === 'penalty'): ?> غرامة
                                        <?php else: ?> أخرى
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600"><?php echo e(number_format($deduction->amount, 2)); ?> ج.م</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900"><?php echo e($deduction->deduction_date->format('Y-m-d')); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </section>
            <?php endif; ?>

            <!-- Payments -->
            <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h3 class="text-lg font-bold text-slate-900">سجل المدفوعات</h3>
                    <?php if($employeeAgreement->status === 'active'): ?>
                    <button type="button" onclick="document.getElementById('new-payment-form').classList.toggle('hidden')" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 transition-all">
                        <i class="fas fa-plus"></i>
                        إنشاء دفعة راتب
                    </button>
                    <?php endif; ?>
                </div>
                <!-- نموذج إنشاء دفعة راتب -->
                <?php if($employeeAgreement->status === 'active'): ?>
                <div id="new-payment-form" class="hidden px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-slate-50/50">
                    <form action="<?php echo e(route('admin.employee-agreements.payments.store', $employeeAgreement)); ?>" method="POST" class="flex flex-wrap items-end gap-4">
                        <?php echo csrf_field(); ?>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">تاريخ الاستحقاق</label>
                            <input type="date" name="payment_date" value="<?php echo e(now()->endOfMonth()->format('Y-m-d')); ?>" required class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">الخصومات (ج.م)</label>
                            <input type="number" name="total_deductions" value="0" min="0" step="0.01" class="rounded-xl border border-slate-200 px-3 py-2 text-sm w-28">
                        </div>
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-xs font-semibold text-slate-500 mb-1">ملاحظات</label>
                            <input type="text" name="notes" placeholder="اختياري" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-sm font-semibold hover:bg-emerald-700">إنشاء الدفعة</button>
                    </form>
                </div>
                <?php endif; ?>
                <?php if($employeeAgreement->payments->count() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">رقم الدفعة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">تاريخ الاستحقاق</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">الراتب الأساسي</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">الخصومات</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">صافي الراتب</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">الحالة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">إجراء</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            <?php $__currentLoopData = $employeeAgreement->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900"><?php echo e($payment->payment_number); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900"><?php echo e($payment->payment_date->format('Y-m-d')); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900"><?php echo e(number_format($payment->base_salary, 2)); ?> ج.م</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600"><?php echo e(number_format($payment->total_deductions, 2)); ?> ج.م</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-emerald-600"><?php echo e(number_format($payment->net_salary, 2)); ?> ج.م</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        <?php if($payment->status === 'paid'): ?> bg-emerald-100 text-emerald-800
                                        <?php elseif($payment->status === 'pending'): ?> bg-amber-100 text-amber-800
                                        <?php elseif($payment->status === 'overdue'): ?> bg-rose-100 text-rose-800
                                        <?php else: ?> bg-slate-100 text-slate-800
                                        <?php endif; ?>">
                                        <?php if($payment->status === 'paid'): ?> مدفوعة
                                        <?php elseif($payment->status === 'pending'): ?> معلقة
                                        <?php elseif($payment->status === 'overdue'): ?> متأخرة
                                        <?php else: ?> ملغاة
                                        <?php endif; ?>
                                    </span>
                                    <?php if($payment->status === 'paid' && $payment->transfer_receipt_path): ?>
                                        <a href="<?php echo e(asset('storage/' . $payment->transfer_receipt_path)); ?>" target="_blank" class="block text-xs text-blue-600 hover:underline mt-1"><i class="fas fa-receipt mr-1"></i>الإيصال</a>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if(in_array($payment->status, ['pending', 'overdue'])): ?>
                                        <button type="button" class="open-pay-modal text-sm text-emerald-600 hover:text-emerald-800 font-medium" data-action="<?php echo e(route('admin.employee-agreements.payments.mark-paid', $payment)); ?>" data-payment-num="<?php echo e($payment->payment_number); ?>">دفع ورفع إيصال</button>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="px-5 py-8 text-center text-slate-500">لا توجد مدفوعات حتى الآن. استخدم "إنشاء دفعة راتب" لإضافة دفعة ثم تنفيذ التحويل ورفع الإيصال.</div>
                <?php endif; ?>
            </section>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 sm:space-y-6">
            <?php $emp = $employeeAgreement->employee; ?>
            <?php if($emp && ($emp->bank_name || $emp->bank_account_number || $emp->bank_account_holder_name)): ?>
            <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900"><i class="fas fa-university text-indigo-600 mr-2"></i>البيانات البنكية للموظف</h3>
                </div>
                <div class="px-5 py-6 sm:px-8 lg:px-12 space-y-2 text-sm">
                    <?php if($emp->bank_name): ?><p><span class="text-slate-500">البنك:</span> <span class="font-semibold text-slate-900"><?php echo e($emp->bank_name); ?></span></p><?php endif; ?>
                    <?php if($emp->bank_branch): ?><p><span class="text-slate-500">الفرع:</span> <?php echo e($emp->bank_branch); ?></p><?php endif; ?>
                    <?php if($emp->bank_account_number): ?><p><span class="text-slate-500">رقم الحساب:</span> <span class="font-mono font-semibold"><?php echo e($emp->bank_account_number); ?></span></p><?php endif; ?>
                    <?php if($emp->bank_account_holder_name): ?><p><span class="text-slate-500">اسم صاحب الحساب:</span> <?php echo e($emp->bank_account_holder_name); ?></p><?php endif; ?>
                    <?php if($emp->bank_iban): ?><p><span class="text-slate-500">الآيبان:</span> <span class="font-mono"><?php echo e($emp->bank_iban); ?></span></p><?php endif; ?>
                </div>
            </section>
            <?php endif; ?>
            <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900">معلومات إضافية</h3>
                </div>
                <div class="px-5 py-6 sm:px-8 lg:px-12 space-y-4">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">منشئ الاتفاقية</p>
                        <p class="text-sm font-semibold text-slate-900"><?php echo e($employeeAgreement->creator->name ?? 'غير محدد'); ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">تاريخ الإنشاء</p>
                        <p class="text-sm text-slate-700"><?php echo e($employeeAgreement->created_at->format('Y-m-d H:i')); ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">آخر تحديث</p>
                        <p class="text-sm text-slate-700"><?php echo e($employeeAgreement->updated_at->format('Y-m-d H:i')); ?></p>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- نافذة دفع ورفع إيصال (خارج الجدول لضمان ظهورها بشكل سليم) -->
    <div id="pay-receipt-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4" style="direction: rtl; background: rgba(0,0,0,0.5);">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden" onclick="event.stopPropagation()">
            <div class="p-6 border-b border-slate-200">
                <h4 class="text-lg font-bold text-slate-900">تسجيل الدفع ورفع إيصال التحويل</h4>
                <p id="pay-modal-payment-num" class="text-sm text-slate-500 mt-1"></p>
            </div>
            <form id="pay-receipt-form" method="POST" enctype="multipart/form-data" class="p-6">
                <?php echo csrf_field(); ?>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">إيصال التحويل *</label>
                        <input type="file" name="transfer_receipt" accept=".pdf,.jpg,.jpeg,.png" required class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2">
                        <p class="text-xs text-slate-500 mt-1">PDF أو صورة، حجم أقصى 5 ميجا</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">ملاحظات (اختياري)</label>
                        <textarea name="notes" rows="2" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm"></textarea>
                    </div>
                </div>
                <div class="flex gap-2 mt-6">
                    <button type="submit" class="flex-1 py-2.5 bg-emerald-600 text-white rounded-xl font-semibold hover:bg-emerald-700">تسجيل الدفع</button>
                    <button type="button" id="pay-modal-close" class="px-4 py-2.5 border border-slate-200 rounded-xl font-medium text-slate-700 hover:bg-slate-100">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        (function() {
            var modal = document.getElementById('pay-receipt-modal');
            var form = document.getElementById('pay-receipt-form');
            var paymentNumEl = document.getElementById('pay-modal-payment-num');
            document.querySelectorAll('.open-pay-modal').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    form.action = this.getAttribute('data-action');
                    var num = this.getAttribute('data-payment-num');
                    paymentNumEl.textContent = num ? 'الدفعة: ' + num : '';
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                });
            });
            document.getElementById('pay-modal-close').addEventListener('click', function() {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            });
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            });
        })();
    </script>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/employee-agreements/show.blade.php ENDPATH**/ ?>