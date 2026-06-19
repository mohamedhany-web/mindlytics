<?php
    $invoice = $invoice ?? null;
    $isEdit = $invoice !== null;
    $formAction = $formAction ?? ($isEdit ? route('admin.invoices.update', $invoice) : route('admin.invoices.store'));
    $formMethod = $isEdit ? 'PUT' : 'POST';
?>

<form action="<?php echo e($formAction); ?>" method="POST" class="space-y-6" id="invoiceForm">
    <?php echo csrf_field(); ?>
    <?php if($isEdit): ?>
        <?php echo method_field('PUT'); ?>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
            <p class="font-bold mb-1">يرجى تصحيح الأخطاء التالية:</p>
            <ul class="list-disc list-inside space-y-0.5">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($err); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <?php if(!$isEdit): ?>
        <div class="md:col-span-2 space-y-2">
            <label for="invoice-client-search" class="block text-xs font-bold text-slate-700">بحث عن عميل</label>
            <input type="search" id="invoice-client-search" autocomplete="off" placeholder="البريد، الاسم، أو الهاتف…"
                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            <p class="text-[11px] text-slate-500">يُصفّي قائمة العملاء دون إعادة تحميل الصفحة.</p>
        </div>
        <?php endif; ?>

        <div>
            <label for="invoice-user-select" class="block text-xs font-bold text-slate-700 mb-1">العميل *</label>
            <select name="user_id" id="invoice-user-select" required
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 <?php $__errorArgs = ['user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-rose-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <option value="">اختر العميل</option>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $searchBlob = \Illuminate\Support\Str::lower(trim(implode(' ', array_filter([
                            $user->name ?? '',
                            $user->email ?? '',
                            $user->phone ?? '',
                        ]))));
                        $sel = (int) old('user_id', $invoice->user_id ?? 0) === (int) $user->id;
                    ?>
                    <option value="<?php echo e($user->id); ?>" data-search="<?php echo e(e($searchBlob)); ?>" <?php if($sel): echo 'selected'; endif; ?>>
                        <?php echo e($user->name); ?> — <?php echo e($user->email); ?><?php if(!empty($user->phone)): ?> — <?php echo e($user->phone); ?><?php endif; ?>
                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">نوع الفاتورة *</label>
            <select name="type" required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-rose-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php echo $__env->make('admin.invoices.partials.type-options', ['selected' => old('type', $invoice->type ?? 'course')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </select>
            <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">المبلغ الفرعي *</label>
            <input type="number" name="subtotal" step="0.01" min="0" required data-invoice-field="subtotal"
                   value="<?php echo e(old('subtotal', $invoice->subtotal ?? '')); ?>"
                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 <?php $__errorArgs = ['subtotal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-rose-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['subtotal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">الضريبة</label>
            <input type="number" name="tax_amount" step="0.01" min="0" data-invoice-field="tax"
                   value="<?php echo e(old('tax_amount', $invoice->tax_amount ?? 0)); ?>"
                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">الخصم</label>
            <input type="number" name="discount_amount" step="0.01" min="0" data-invoice-field="discount"
                   value="<?php echo e(old('discount_amount', $invoice->discount_amount ?? 0)); ?>"
                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
        </div>

        <?php if($isEdit): ?>
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">الحالة *</label>
            <select name="status" required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-rose-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php echo $__env->make('admin.invoices.partials.status-options', ['selected' => old('status', $invoice->status ?? 'pending')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </select>
            <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <?php endif; ?>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">تاريخ الاستحقاق</label>
            <input type="date" name="due_date"
                   value="<?php echo e(old('due_date', $invoice && $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '')); ?>"
                   <?php if(!$isEdit): ?> min="<?php echo e(now()->format('Y-m-d')); ?>" <?php endif; ?>
                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
        </div>
    </div>

    <div class="rounded-xl border border-blue-100 bg-blue-50/60 px-4 py-3 flex flex-wrap items-center justify-between gap-2">
        <span class="text-xs font-bold text-blue-900">الإجمالي التقديري</span>
        <span id="invoice-total-preview" class="text-lg font-black text-blue-800 tabular-nums">0.00 ج.م</span>
    </div>

    <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">الوصف</label>
        <textarea name="description" rows="3" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><?php echo e(old('description', $invoice->description ?? '')); ?></textarea>
    </div>

    <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">ملاحظات</label>
        <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"><?php echo e(old('notes', $invoice->notes ?? '')); ?></textarea>
    </div>

    <div class="flex flex-wrap gap-3 pt-2">
        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-5 py-2.5 text-sm font-bold text-white">
            <i class="fas fa-save"></i>
            <?php echo e($isEdit ? 'تحديث الفاتورة' : 'إنشاء الفاتورة'); ?>

        </button>
        <a href="<?php echo e($isEdit ? route('admin.invoices.show', $invoice) : route('admin.invoices.index')); ?>"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
            إلغاء
        </a>
    </div>
</form>

<?php if (! $__env->hasRenderedOnce('f2050071-6a00-4815-b371-359b6da91797')): $__env->markAsRenderedOnce('f2050071-6a00-4815-b371-359b6da91797'); ?>
<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    function num(el, name) {
        var n = parseFloat(el.querySelector('[name="' + name + '"]')?.value || '0');
        return isNaN(n) ? 0 : n;
    }
    function updateTotal(form) {
        var sub = num(form, 'subtotal');
        var tax = num(form, 'tax_amount');
        var disc = num(form, 'discount_amount');
        var total = Math.max(0, sub + tax - disc);
        var out = form.querySelector('#invoice-total-preview');
        if (out) out.textContent = total.toFixed(2) + ' ج.م';
    }
    document.querySelectorAll('#invoiceForm').forEach(function (form) {
        form.querySelectorAll('[data-invoice-field]').forEach(function (el) {
            el.addEventListener('input', function () { updateTotal(form); });
        });
        updateTotal(form);
    });

    var search = document.getElementById('invoice-client-search');
    var select = document.getElementById('invoice-user-select');
    if (search && select) {
        var pool = Array.prototype.slice.call(select.options, 1).map(function (o) {
            return { v: o.value, t: o.text, s: (o.getAttribute('data-search') || o.text || '').toLowerCase() };
        });
        search.addEventListener('input', function () {
            var q = search.value.trim().toLowerCase().replace(/\s+/g, ' ');
            var prev = select.value;
            while (select.options.length > 1) select.remove(1);
            pool.forEach(function (p) {
                if (!q || p.s.indexOf(q) !== -1) {
                    var opt = document.createElement('option');
                    opt.value = p.v;
                    opt.textContent = p.t;
                    opt.setAttribute('data-search', p.s);
                    select.appendChild(opt);
                }
            });
            var still = Array.prototype.some.call(select.options, function (o) { return o.value === prev; });
            select.value = still ? prev : '';
        });
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\invoices\partials\form.blade.php ENDPATH**/ ?>