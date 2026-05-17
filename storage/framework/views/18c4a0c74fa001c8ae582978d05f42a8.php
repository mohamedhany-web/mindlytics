

<?php $__env->startSection('title', 'إنشاء فاتورة جديدة'); ?>
<?php $__env->startSection('header', 'إنشاء فاتورة جديدة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">إنشاء فاتورة جديدة</h1>
        
        <form action="<?php echo e(route('admin.invoices.store')); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2 space-y-2">
                    <label for="invoice-client-search" class="block text-sm font-medium text-gray-700 mb-2">بحث عن عميل</label>
                    <input type="search"
                           id="invoice-client-search"
                           autocomplete="off"
                           placeholder="البريد الإلكتروني، الاسم، أو الهاتف…"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 placeholder:text-gray-400">
                    <p class="text-xs text-gray-500">يُصفّي قائمة العملاء أدناه دون إعادة تحميل الصفحة.</p>
                </div>
                <div>
                    <label for="invoice-user-select" class="block text-sm font-medium text-gray-700 mb-2">العميل *</label>
                    <select name="user_id" id="invoice-user-select" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع الفاتورة *</label>
                    <select name="type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="course">كورس</option>
                        <option value="subscription">اشتراك</option>
                        <option value="other">أخرى</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المبلغ الفرعي *</label>
                    <input type="number" name="subtotal" step="0.01" min="0" required value="<?php echo e(old('subtotal')); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الضريبة</label>
                    <input type="number" name="tax_amount" step="0.01" min="0" value="<?php echo e(old('tax_amount', 0)); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الخصم</label>
                    <input type="number" name="discount_amount" step="0.01" min="0" value="<?php echo e(old('discount_amount', 0)); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الاستحقاق</label>
                    <input type="date" name="due_date" value="<?php echo e(old('due_date')); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                <textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500"><?php echo e(old('description')); ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500"><?php echo e(old('notes')); ?></textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                    إنشاء الفاتورة
                </button>
                <a href="<?php echo e(route('admin.invoices.index')); ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors">
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
    var search = document.getElementById('invoice-client-search');
    var select = document.getElementById('invoice-user-select');
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
});
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/invoices/create.blade.php ENDPATH**/ ?>