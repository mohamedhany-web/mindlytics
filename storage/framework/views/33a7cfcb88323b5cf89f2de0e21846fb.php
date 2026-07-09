<?php $__env->startSection('title', 'إضافة دفعة جديدة'); ?>
<?php $__env->startSection('header', 'إضافة دفعة جديدة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">إضافة دفعة جديدة</h1>
        
        <form action="<?php echo e(route('admin.payments.store')); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2 space-y-2">
                    <label for="payment-client-search" class="block text-sm font-medium text-gray-700 mb-2">بحث عن عميل</label>
                    <input type="search"
                           id="payment-client-search"
                           autocomplete="off"
                           placeholder="البريد الإلكتروني، الاسم، أو الهاتف…"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 placeholder:text-gray-400">
                    <p class="text-xs text-gray-500">يُصفّي قائمة العملاء أدناه دون إعادة تحميل الصفحة.</p>
                </div>
                <div>
                    <label for="payment-user-select" class="block text-sm font-medium text-gray-700 mb-2">العميل *</label>
                    <select name="user_id" id="payment-user-select" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">اختر العميل</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $searchBlob = \Illuminate\Support\Str::lower(trim(implode(' ', array_filter([
                                $user->name ?? '',
                                $user->email ?? '',
                                $user->phone ?? '',
                            ]))));
                        ?>
                        <option value="<?php echo e($user->id); ?>" data-search="<?php echo e(e($searchBlob)); ?>">
                            <?php echo e($user->name); ?> — <?php echo e($user->email); ?><?php if(!empty($user->phone)): ?> — <?php echo e($user->phone); ?><?php endif; ?>
                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الفاتورة *</label>
                    <select name="invoice_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <?php if($invoices->isEmpty()): ?>
                            <option value="" disabled selected>لا توجد فواتير مستحقة حاليًا</option>
                        <?php else: ?>
                            <option value="">اختر الفاتورة</option>
                            <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($invoice->id); ?>">
                                <?php echo e($invoice->invoice_number); ?> · <?php echo e($invoice->user->name); ?> · متبقي <?php echo e(number_format($invoice->remaining_amount, 2)); ?> ج.م
                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </select>
                    <?php if($invoices->isEmpty()): ?>
                        <p class="mt-2 text-xs text-amber-600">لا توجد فواتير بحاجة إلى دفع في الوقت الحالي.</p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المبلغ *</label>
                    <input type="number" name="amount" step="0.01" min="0" required value="<?php echo e(old('amount')); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">طريقة الدفع *</label>
                    <select name="payment_method" id="payment-method-select" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="cash" <?php echo e(old('payment_method', 'cash') === 'cash' ? 'selected' : ''); ?>>نقدي</option>
                        <option value="card" <?php echo e(old('payment_method') === 'card' ? 'selected' : ''); ?>>بطاقة</option>
                        <option value="bank_transfer" <?php echo e(old('payment_method') === 'bank_transfer' ? 'selected' : ''); ?>>تحويل بنكي</option>
                        <option value="online" <?php echo e(old('payment_method') === 'online' ? 'selected' : ''); ?>>دفع إلكتروني</option>
                        <option value="wallet" <?php echo e(old('payment_method') === 'wallet' ? 'selected' : ''); ?>>محفظة</option>
                        <option value="other" <?php echo e(old('payment_method') === 'other' ? 'selected' : ''); ?>>أخرى</option>
                    </select>
                </div>

                <div id="payment-wallet-row" class="md:col-span-2 rounded-xl border border-sky-100 bg-sky-50/50 p-4 space-y-2" style="display: none;">
                    <label for="payment-wallet-select" class="block text-sm font-medium text-gray-800">المحفظة التي استلمت الدفعة *</label>
                    <p class="text-xs text-gray-600">يظهر <strong>الرصيد الحالي</strong> لكل محفظة؛ عند الحفظ يُضاف مبلغ الدفعة إلى رصيد المحفظة المختارة وتُسجَّل حركة إيداع.</p>
                    <select name="wallet_id" id="payment-wallet-select"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 <?php $__errorArgs = ['wallet_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <option value=""><?php echo e($wallets->isEmpty() ? 'لا توجد محافظ مفعّلة' : 'اختر المحفظة'); ?></option>
                        <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wallet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($wallet->id); ?>"
                                    data-balance="<?php echo e($wallet->balance); ?>"
                                    <?php echo e((string) old('wallet_id') === (string) $wallet->id ? 'selected' : ''); ?>>
                                <?php echo e($wallet->name); ?> — <?php echo e(\App\Models\Wallet::typeLabel($wallet->type)); ?> — الرصيد <?php echo e(number_format($wallet->balance, 2)); ?> ج.م
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['wallet_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <p id="payment-wallet-balance-hint" class="hidden text-sm font-medium text-sky-800"></p>
                    <?php if($wallets->isEmpty()): ?>
                        <p class="text-sm text-amber-700">
                            لا توجد محافظ مفعّلة. أنشئ محفظة من
                            <a href="<?php echo e(route('admin.wallets.index')); ?>" class="font-semibold text-sky-700 underline">إدارة المحافظ</a>.
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500"><?php echo e(old('notes')); ?></textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                    إضافة الدفعة
                </button>
                <a href="<?php echo e(route('admin.payments.index')); ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var search = document.getElementById('payment-client-search');
    var select = document.getElementById('payment-user-select');
    if (!search || !select) return;

    var pool = Array.prototype.slice.call(select.options, 1).map(function (o) {
        return {
            v: o.value,
            t: o.text,
            s: (o.getAttribute('data-search') || o.text || '').toLowerCase()
        };
    });

    function applyFilter() {
        var q = search.value.trim().toLowerCase().replace(/\s+/g, ' ');
        var prev = select.value;

        while (select.options.length > 1) {
            select.remove(1);
        }

        pool.forEach(function (p) {
            if (!q || p.s.indexOf(q) !== -1) {
                var opt = document.createElement('option');
                opt.value = p.v;
                opt.textContent = p.t;
                opt.setAttribute('data-search', p.s);
                select.appendChild(opt);
            }
        });

        var stillThere = Array.prototype.some.call(select.options, function (o) {
            return o.value === prev;
        });
        select.value = stillThere ? prev : '';
    }

    search.addEventListener('input', applyFilter);

    var methodSelect = document.getElementById('payment-method-select');
    var walletRow = document.getElementById('payment-wallet-row');
    var walletSelect = document.getElementById('payment-wallet-select');
    var walletHint = document.getElementById('payment-wallet-balance-hint');

    function updateWalletBalanceHint() {
        if (!walletSelect || !walletHint) return;
        var opt = walletSelect.options[walletSelect.selectedIndex];
        if (!opt || !opt.value) {
            walletHint.classList.add('hidden');
            walletHint.textContent = '';
            return;
        }
        var bal = opt.getAttribute('data-balance');
        if (bal === null || bal === '') {
            walletHint.classList.add('hidden');
            return;
        }
        var n = parseFloat(bal, 10);
        if (isNaN(n)) n = 0;
        walletHint.textContent = 'الرصيد الحالي في هذه المحفظة (قبل إضافة هذه الدفعة): ' + n.toLocaleString('ar-EG', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ج.م';
        walletHint.classList.remove('hidden');
    }

    function syncWalletRow() {
        if (!methodSelect || !walletRow || !walletSelect) return;
        var isWallet = methodSelect.value === 'wallet';
        walletRow.style.display = isWallet ? '' : 'none';
        walletSelect.disabled = !isWallet;
        walletSelect.required = isWallet;
        if (!isWallet) {
            walletSelect.value = '';
            if (walletHint) {
                walletHint.classList.add('hidden');
                walletHint.textContent = '';
            }
        } else {
            updateWalletBalanceHint();
        }
    }

    if (methodSelect) {
        methodSelect.addEventListener('change', syncWalletRow);
        if (walletSelect) {
            walletSelect.addEventListener('change', updateWalletBalanceHint);
        }
        syncWalletRow();
    }
});
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\payments\create.blade.php ENDPATH**/ ?>