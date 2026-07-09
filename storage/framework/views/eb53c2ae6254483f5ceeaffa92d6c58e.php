<?php $__env->startSection('title', 'تسجيل طالب جديد'); ?>
<?php $__env->startSection('header', 'تسجيل طالب جديد'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <!-- معلومات التسجيل -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-200/80">
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-sky-50 via-blue-50 to-sky-50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h3 class="text-lg font-black bg-gradient-to-r from-sky-800 via-blue-700 to-sky-600 bg-clip-text text-transparent flex items-center gap-2">
                    <i class="fas fa-user-plus text-sky-600"></i>
                    تسجيل طالب في كورس أونلاين
                </h3>
                <a href="<?php echo e(route('admin.online-enrollments.index')); ?>" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition-all duration-300">
                    <i class="fas fa-arrow-right"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>

        <form method="POST" action="<?php echo e(route('admin.online-enrollments.store')); ?>" class="p-6 space-y-6">
            <?php echo csrf_field(); ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- الطالب -->
                <div>
                    <label for="user_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        اختيار الطالب <span class="text-red-500">*</span>
                    </label>
                    <div class="mb-2 flex items-center gap-2">
                        <i class="fas fa-search text-xs text-gray-400"></i>
                        <input id="studentSearchInput"
                               type="text"
                               placeholder="بحث سريع باسم الطالب أو رقم الهاتف داخل القائمة"
                               class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-white/80">
                    </div>
                    <select name="user_id" id="user_id" required
                            class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 bg-white/70 focus:ring-4 focus:ring-sky-500/20 focus:border-sky-500 transition">
                        <option value="">اختر الطالب</option>
                        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($student->id); ?>" 
                                    <?php echo e((old('user_id', request('student_id')) == $student->id) ? 'selected' : ''); ?>

                                    data-phone="<?php echo e($student->phone); ?>"
                                    data-parent-phone="<?php echo e($student->parent_phone); ?>">
                                <?php echo e($student->name); ?> - <?php echo e($student->phone); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- الكورس -->
                <div>
                    <label for="advanced_course_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        اختيار الكورس <span class="text-red-500">*</span>
                    </label>
                    <div class="mb-2 flex items-center gap-2">
                        <i class="fas fa-search text-xs text-gray-400"></i>
                        <input id="courseSearchInput"
                               type="text"
                               placeholder="بحث سريع باسم الكورس داخل القائمة"
                               class="w-full px-3 py-2 text-xs border border-gray-200 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-white/80">
                    </div>
                    <select name="advanced_course_id" id="advanced_course_id" required
                            class="w-full px-3 py-2 rounded-xl border-2 border-gray-200 bg-white/70 focus:ring-4 focus:ring-sky-500/20 focus:border-sky-500 transition">
                        <option value="">اختر الكورس</option>
                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($course->id); ?>" <?php echo e(old('advanced_course_id') == $course->id ? 'selected' : ''); ?>>
                                <?php echo e($course->title); ?> - <?php echo e($course->academicYear->name ?? 'غير محدد'); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['advanced_course_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- حالة التسجيل -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        حالة التسجيل <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">اختر حالة التسجيل</option>
                        <option value="pending" <?php echo e(old('status') == 'pending' ? 'selected' : ''); ?>>في الانتظار</option>
                        <option value="active" <?php echo e(old('status', 'active') == 'active' ? 'selected' : ''); ?>>نشط</option>
                    </select>
                    <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <p class="mt-1 text-xs text-gray-500">
                        "نشط" يعني أن الطالب يمكنه الوصول للكورس فوراً، وسيتم إرسال رسالة التفعيل إلى بريده الإلكتروني (إن كان مسجلاً)، وعند النشط تُحسب للمدرب نسبة من الكورس إن وُجدت اتفاقية.
                    </p>
                </div>

                <!-- مبلغ التفعيل (يظهر عند اختيار "نشط") — يُستخدم لحساب نسبة المدرب -->
                <div id="final_price_wrap" class="<?php echo e(old('status', 'active') !== 'active' ? 'hidden' : ''); ?>">
                    <label for="final_price" class="block text-sm font-medium text-gray-700 mb-2">
                        مبلغ التفعيل (ج.م) <span class="text-gray-400 text-xs">اختياري</span>
                    </label>
                    <input type="number" name="final_price" id="final_price" value="<?php echo e(old('final_price')); ?>" min="0" step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="اتركه فارغاً لاستخدام سعر الكورس">
                    <p class="mt-1 text-xs text-gray-500">إن وُجدت اتفاقية "نسبة من الكورس" للمدرب، تُحسب حصته من هذا المبلغ (أو سعر الكورس إن تركت الحقل فارغاً).</p>
                    <?php $__errorArgs = ['final_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div id="activate_free_wrap" class="<?php echo e(old('status', 'active') !== 'active' ? 'hidden' : ''); ?> md:col-span-2">
                    <label class="inline-flex items-start gap-3 cursor-pointer rounded-xl border border-emerald-200 bg-emerald-50/60 p-4 w-full">
                        <input type="checkbox" name="activate_as_free" value="1" id="activate_as_free"
                               <?php echo e(old('activate_as_free') ? 'checked' : ''); ?>

                               class="mt-1 rounded border-emerald-400 text-emerald-600 focus:ring-emerald-500">
                        <span>
                            <span class="block text-sm font-bold text-emerald-900">تفعيل مجاني (مخفي عن المدرب)</span>
                            <span class="block text-xs text-emerald-800/80 mt-1">بدون فاتورة أو نسبة مدرب — ولن تظهر بيانات الطالب في لوحة المدرب.</span>
                        </span>
                    </label>
                </div>

                <div id="payment_method_wrap" class="<?php echo e(old('status', 'active') !== 'active' ? 'hidden' : ''); ?>">
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">طريقة الدفع</label>
                    <select name="payment_method" id="payment_method"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="cash" <?php echo e(old('payment_method', 'cash') === 'cash' ? 'selected' : ''); ?>>نقدي</option>
                        <option value="bank_transfer" <?php echo e(old('payment_method') === 'bank_transfer' ? 'selected' : ''); ?>>تحويل بنكي</option>
                        <option value="online" <?php echo e(old('payment_method') === 'online' ? 'selected' : ''); ?>>أونلاين</option>
                        <option value="wallet" <?php echo e(old('payment_method') === 'wallet' ? 'selected' : ''); ?>>محفظة</option>
                        <option value="other" <?php echo e(old('payment_method') === 'other' ? 'selected' : ''); ?>>أخرى</option>
                    </select>
                    <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div id="wallet_wrap" class="<?php echo e(old('status', 'active') !== 'active' ? 'hidden' : ''); ?>">
                    <label for="wallet_id" class="block text-sm font-medium text-gray-700 mb-2">المحفظة</label>
                    <select name="wallet_id" id="wallet_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">اختر المحفظة</option>
                        <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wallet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($wallet->id); ?>" <?php if((string) old('wallet_id') === (string) $wallet->id): echo 'selected'; endif; ?>>
                                <?php echo e($wallet->name ?: \App\Models\Wallet::typeLabel($wallet->type)); ?>

                                (<?php echo e(number_format((float) $wallet->balance, 2)); ?> ج.م)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['wallet_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <!-- الملاحظات -->
            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    ملاحظات إدارية
                </label>
                <textarea name="notes" id="notes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="أي ملاحظات خاصة بهذا التسجيل (اختياري)"><?php echo e(old('notes')); ?></textarea>
                <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- معلومات الطالب المختار -->
            <div id="studentInfo" class="mt-6 hidden">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-medium text-blue-900 mb-2">معلومات الطالب المختار:</h4>
                    <div id="studentDetails" class="text-sm text-blue-800">
                        <!-- ستتم إضافة معلومات الطالب هنا بواسطة JavaScript -->
                    </div>
                </div>
            </div>

            <!-- البحث السريع بالهاتف -->
            <div class="mt-6 bg-gradient-to-r from-slate-50 to-sky-50 rounded-xl p-4 border border-sky-100">
                <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="fas fa-mobile-alt text-sky-500"></i>
                    البحث السريع عن الطالب برقم الهاتف
                </h4>
                <div class="flex gap-3">
                    <input type="text" id="quickPhoneSearch" placeholder="أدخل رقم هاتف الطالب أو ولي الأمر..."
                           class="flex-1 px-3 py-2 rounded-xl border-2 border-gray-200 bg-white/80 focus:ring-4 focus:ring-sky-500/20 focus:border-sky-500 transition">
                    <button type="button" onclick="searchByPhone()" 
                            class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 shadow-sm hover:shadow-md transition-all duration-200 flex items-center gap-2">
                        <i class="fas fa-search text-sm"></i>
                        بحث
                    </button>
                </div>
                <div id="phoneSearchResult" class="mt-3 hidden"></div>
            </div>

            <!-- أزرار الإجراءات -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
                    <a href="<?php echo e(route('admin.online-enrollments.index')); ?>" 
                       class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition-colors duration-200 inline-flex items-center justify-center gap-2">
                        <i class="fas fa-times text-sm"></i>
                        إلغاء
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 rounded-xl bg-gradient-to-r from-sky-600 via-blue-600 to-sky-600 text-white font-bold hover:from-sky-700 hover:via-blue-700 hover:to-sky-700 shadow-md hover:shadow-lg transition-all duration-200 inline-flex items-center justify-center gap-2">
                        <i class="fas fa-save text-sm"></i>
                        تسجيل الطالب
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// إظهار/إخفاء حقل مبلغ التفعيل حسب حالة التسجيل
function toggleActivePaymentFields() {
    var isActive = document.getElementById('status').value === 'active';
    var isFree = document.getElementById('activate_as_free') && document.getElementById('activate_as_free').checked;
    document.getElementById('final_price_wrap').classList.toggle('hidden', !isActive);
    var freeWrap = document.getElementById('activate_free_wrap');
    if (freeWrap) freeWrap.classList.toggle('hidden', !isActive);
    var paymentWrap = document.getElementById('payment_method_wrap');
    var walletWrap = document.getElementById('wallet_wrap');
    var showPaid = isActive && !isFree;
    if (paymentWrap) paymentWrap.classList.toggle('hidden', !showPaid);
    toggleWalletField();
}

function toggleWalletField() {
    var isActive = document.getElementById('status').value === 'active';
    var isFree = document.getElementById('activate_as_free') && document.getElementById('activate_as_free').checked;
    var paymentMethodEl = document.getElementById('payment_method');
    var walletWrap = document.getElementById('wallet_wrap');
    var walletSelectEl = document.getElementById('wallet_id');
    var isWallet = paymentMethodEl && paymentMethodEl.value === 'wallet';
    var showWallet = isActive && !isFree && isWallet;
    if (walletWrap) walletWrap.classList.toggle('hidden', !showWallet);
    if (walletSelectEl) {
        walletSelectEl.required = showWallet;
        if (!showWallet) walletSelectEl.value = '';
    }
}

document.getElementById('status').addEventListener('change', toggleActivePaymentFields);
document.getElementById('activate_as_free').addEventListener('change', toggleActivePaymentFields);
document.getElementById('payment_method').addEventListener('change', toggleWalletField);
toggleActivePaymentFields();

// عرض معلومات الطالب عند الاختيار
document.getElementById('user_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const studentInfo = document.getElementById('studentInfo');
    const studentDetails = document.getElementById('studentDetails');
    
    if (this.value) {
        const phone = selectedOption.getAttribute('data-phone');
        const parentPhone = selectedOption.getAttribute('data-parent-phone');
        
        let details = `
            <p><strong>الاسم:</strong> ${selectedOption.text.split(' - ')[0]}</p>
            <p><strong>هاتف الطالب:</strong> ${phone}</p>
        `;
        
        if (parentPhone) {
            details += `<p><strong>هاتف ولي الأمر:</strong> ${parentPhone}</p>`;
        }
        
        studentDetails.innerHTML = details;
        studentInfo.classList.remove('hidden');
    } else {
        studentInfo.classList.add('hidden');
    }
});

// البحث بالهاتف
function searchByPhone() {
    const phone = document.getElementById('quickPhoneSearch').value.trim();
    const resultDiv = document.getElementById('phoneSearchResult');
    
    if (!phone) {
        alert('يرجى إدخال رقم الهاتف');
        return;
    }
    
    // إظهار loader
    resultDiv.innerHTML = '<div class="text-center py-2"><i class="fas fa-spinner fa-spin text-blue-600"></i> جاري البحث...</div>';
    resultDiv.classList.remove('hidden');
    
    fetch(`<?php echo e(route('admin.online-enrollments.search-by-phone')); ?>?phone=${encodeURIComponent(phone)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const student = data.student;
                
                // اختيار الطالب في القائمة
                const userSelect = document.getElementById('user_id');
                userSelect.value = student.id;
                userSelect.dispatchEvent(new Event('change'));
                
                resultDiv.innerHTML = `
                    <div class="bg-green-50 border border-green-200 rounded p-3">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            <span class="text-green-800">تم العثور على الطالب واختياره تلقائياً</span>
                        </div>
                    </div>
                `;
                
                // إخفاء النتيجة بعد 3 ثوان
                setTimeout(() => {
                    resultDiv.classList.add('hidden');
                }, 3000);
            } else {
                resultDiv.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded p-3">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-600 mr-2"></i>
                            <span class="text-red-800">${data.error}</span>
                        </div>
                    </div>
                `;
            }
        })
        .catch(error => {
            resultDiv.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded p-3">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                        <span class="text-red-800">حدث خطأ في البحث</span>
                    </div>
                </div>
            `;
        });
}

// البحث عند الضغط على Enter
document.getElementById('quickPhoneSearch').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchByPhone();
    }
});

// إذا كان هناك student_id في الـ URL، إظهار معلومات الطالب
document.addEventListener('DOMContentLoaded', function() {
    const userSelect = document.getElementById('user_id');
    if (userSelect && userSelect.value) {
        userSelect.dispatchEvent(new Event('change'));
    }

    // بحث سريع داخل قائمة الطلاب (بالاسم أو الهاتف)
    const studentSearchInput = document.getElementById('studentSearchInput');
    if (studentSearchInput && userSelect) {
        studentSearchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            Array.from(userSelect.options).forEach((option, index) => {
                if (index === 0) return; // تخطي خيار "اختر الطالب"
                const text = option.text.toLowerCase();
                option.hidden = query && !text.includes(query);
            });
        });
    }

    // بحث سريع داخل قائمة الكورسات (بالاسم)
    const courseSelect = document.getElementById('advanced_course_id');
    const courseSearchInput = document.getElementById('courseSearchInput');
    if (courseSelect && courseSearchInput) {
        courseSearchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            Array.from(courseSelect.options).forEach((option, index) => {
                if (index === 0) return; // تخطي خيار "اختر الكورس"
                const text = option.text.toLowerCase();
                option.hidden = query && !text.includes(query);
            });
        });
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\online-enrollments\create.blade.php ENDPATH**/ ?>