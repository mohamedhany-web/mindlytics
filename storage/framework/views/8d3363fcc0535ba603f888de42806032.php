<?php $__env->startSection('title', 'إضافة مستخدم جديد - Mindlytics'); ?>
<?php $__env->startSection('header', 'إضافة مستخدم جديد'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-user-plus text-lg"></i>
                </div>
                <div>
                    <nav class="text-xs font-medium text-slate-500 flex flex-wrap items-center gap-2 mb-1">
                        <a href="<?php echo e(route('admin.dashboard')); ?>" class="text-blue-600 hover:text-blue-700">لوحة التحكم</a>
                        <span>/</span>
                        <a href="<?php echo e(route('admin.users.index')); ?>" class="text-blue-600 hover:text-blue-700">إدارة المستخدمين</a>
                        <span>/</span>
                        <span class="text-slate-600">إضافة مستخدم</span>
                    </nav>
                    <h2 class="text-2xl font-black text-slate-900 mt-1">إنشاء حساب مستخدم جديد</h2>
                    <p class="text-sm text-slate-600 mt-1">أدخل بيانات المستخدم الأساسية، حدد دوره، واضبط حالة الحساب قبل الحفظ.</p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.users.index')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                <i class="fas fa-arrow-right"></i>
                العودة للقائمة
            </a>
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <form method="POST" action="<?php echo e(route('admin.users.store')); ?>" id="createUserForm" class="space-y-6">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-5">
                        <div class="flex items-center gap-3 pb-4 border-b border-slate-200">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                                <i class="fas fa-user text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">المعلومات الأساسية</h3>
                                <p class="text-xs text-slate-600 mt-1">بيانات الهوية والتواصل يتم استخدامها في التنبيهات وتسجيل الدخول.</p>
                            </div>
                        </div>
                        <?php
                            $phoneCountries = $phoneCountries ?? config('phone_countries.countries', []);
                            $defaultCountry = $defaultCountry ?? collect($phoneCountries)->firstWhere('code', config('phone_countries.default_country', 'SA'));
                            $defaultDialCode = (is_array($defaultCountry) && isset($defaultCountry['dial_code'])) ? $defaultCountry['dial_code'] : '+966';
                        ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1">
                                <label for="name" class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                    <i class="fas fa-user text-blue-600 text-sm"></i>
                                    الاسم الكامل <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" value="<?php echo e(old('name', '')); ?>" required maxlength="255" pattern="^[\p{Arabic}\s\p{N}]+$" title="الرجاء إدخال اسم صحيح (عربي فقط)" placeholder="أدخل الاسم الكامل" class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all hover:border-slate-400" />
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1"><i class="fas fa-exclamation-circle"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="space-y-1">
                                <label for="phone" class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                    <i class="fas fa-phone text-blue-600 text-sm"></i>
                                    رقم الهاتف <span class="text-rose-500">*</span>
                                </label>
                                <div class="flex rounded-xl overflow-hidden border-2 border-slate-300 bg-white focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 hover:border-slate-400 transition-all" dir="ltr">
                                    <select name="country_code" id="country_code" required aria-label="كود الدولة" class="shrink-0 w-32 md:w-36 rounded-l-xl border-0 border-l-0 border-r-2 border-slate-300 bg-slate-50 px-3 py-3 text-sm text-slate-900 focus:ring-0 focus:border-slate-300 cursor-pointer">
                                        <?php if(empty($phoneCountries)): ?>
                                            <option value="+966" selected>+966 السعودية</option>
                                        <?php endif; ?>
                                        <?php $__currentLoopData = $phoneCountries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $optValue = ($c['dial_code'] ?? '') === '' ? 'OTHER' : $c['dial_code'];
                                                $current = old('country_code', $defaultDialCode);
                                                $selected = ($current === ($c['dial_code'] ?? '')) || (($c['dial_code'] ?? '') === '' && $current === 'OTHER');
                                            ?>
                                            <option value="<?php echo e($optValue); ?>" <?php echo e($selected ? 'selected' : ''); ?>><?php echo e($c['dial_code'] ?: '—'); ?> <?php echo e($c['name_ar']); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <input type="tel" name="phone" id="phone" value="<?php echo e(old('phone', '')); ?>" required placeholder="xxxxxxxx" maxlength="15" dir="ltr" aria-label="رقم الهاتف" class="flex-1 min-w-0 rounded-r-xl border-0 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0 focus:border-0" />
                                </div>
                                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1"><i class="fas fa-exclamation-circle"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="space-y-1 md:col-span-2">
                                <label for="email" class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                    <i class="fas fa-envelope text-blue-600 text-sm"></i>
                                    البريد الإلكتروني <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="email" name="email" id="email" value="<?php echo e(old('email', '')); ?>" required maxlength="255" placeholder="example@mindlytics.com" class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 pr-10 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all hover:border-slate-400" />
                                    <i class="fas fa-envelope absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                                </div>
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1"><i class="fas fa-exclamation-circle"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <p class="mt-1.5 text-xs text-slate-500 flex items-center gap-1"><i class="fas fa-info-circle text-blue-500"></i>سيتم استخدام البريد الإلكتروني في إرسال الإشعارات والتنبيهات.</p>
                            </div>
                            <div class="space-y-1 md:col-span-2">
                                <label for="password" class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                    <i class="fas fa-lock text-blue-600 text-sm"></i>
                                    كلمة المرور <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" name="password" id="password" required minlength="8" maxlength="255" autocomplete="new-password" placeholder="أدخل كلمة مرور قوية" class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 pr-10 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all hover:border-slate-400" />
                                    <button type="button" onclick="togglePasswordVisibility('password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 rounded p-1">
                                        <i class="fas fa-eye" id="password-eye"></i>
                                    </button>
                                </div>
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1"><i class="fas fa-exclamation-circle"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <p class="mt-1.5 text-xs text-slate-600 flex items-center gap-1"><i class="fas fa-shield-alt text-emerald-500"></i>يجب أن تحتوي كلمة المرور على 8 أحرف على الأقل.</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-5">
                        <div class="flex items-center gap-3 pb-4 border-b border-slate-200">
                            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                                <i class="fas fa-user-shield text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">الدور والصلاحيات</h3>
                                <p class="text-xs text-slate-600 mt-1">حدد مستوى الوصول المسموح للمستخدم وحالة الحساب عند الإنشاء.</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1">
                                <label for="role" class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                    <i class="fas fa-user-tag text-indigo-600 text-sm"></i>
                                    الدور <span class="text-rose-500">*</span>
                                </label>
                                <select name="role" id="role" required class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all hover:border-slate-400 cursor-pointer">
                                    <option value="">اختر الدور</option>
                                    <option value="super_admin" <?php echo e(old('role') == 'super_admin' ? 'selected' : ''); ?>>إداري</option>
                                    <option value="instructor" <?php echo e(old('role') == 'instructor' ? 'selected' : ''); ?>>مدرس</option>
                                    <option value="student" <?php echo e(old('role') == 'student' ? 'selected' : ''); ?>>طالب</option>
                                </select>
                                <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1"><i class="fas fa-exclamation-circle"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="space-y-1">
                                <label for="is_active" class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                    <i class="fas fa-toggle-on text-indigo-600 text-sm"></i>
                                    حالة الحساب <span class="text-rose-500">*</span>
                                </label>
                                <select name="is_active" id="is_active" required class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all hover:border-slate-400 cursor-pointer">
                                    <option value="1" <?php echo e(old('is_active', '1') == '1' ? 'selected' : ''); ?>>نشط</option>
                                    <option value="0" <?php echo e(old('is_active') == '0' ? 'selected' : ''); ?>>غير نشط</option>
                                </select>
                                <?php $__errorArgs = ['is_active'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1"><i class="fas fa-exclamation-circle"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div class="rounded-xl border-2 border-indigo-200 bg-gradient-to-br from-indigo-50 to-blue-50 p-5 text-sm text-indigo-900 shadow-sm">
                            <h4 class="font-bold mb-3 flex items-center gap-2 text-base">
                                <i class="fas fa-info-circle text-indigo-600"></i>
                                وصف سريع للأدوار
                            </h4>
                            <ul class="space-y-2.5 text-xs">
                                <li class="flex items-start gap-2.5">
                                    <i class="fas fa-shield-alt text-indigo-600 mt-0.5 flex-shrink-0"></i>
                                    <div><strong class="text-indigo-900">إداري:</strong> صلاحيات كاملة لإدارة المنصة والمستخدمين.</div>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <i class="fas fa-chalkboard-teacher text-indigo-600 mt-0.5 flex-shrink-0"></i>
                                    <div><strong class="text-indigo-900">مدرس:</strong> إدارة المحتوى التعليمي، المحاضرات، الامتحانات.</div>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <i class="fas fa-graduation-cap text-indigo-600 mt-0.5 flex-shrink-0"></i>
                                    <div><strong class="text-indigo-900">طالب:</strong> الوصول للكورسات، أداء الواجبات والامتحانات.</div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-3">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-200">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600">
                                <i class="fas fa-align-right text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">معلومات إضافية</h3>
                                <p class="text-xs text-slate-600 mt-1">معلومات إضافية عن المستخدم (اختياري).</p>
                            </div>
                        </div>
                        <div>
                            <label for="bio" class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-align-right text-purple-600 text-sm"></i>
                                نبذة تعريفية (اختياري)
                            </label>
                            <textarea name="bio" id="bio" rows="4" maxlength="1000" placeholder="اكتب ملخصاً عن خبرات المستخدم أو ملاحظات داخلية..." class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm leading-6 text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all resize-none hover:border-slate-400"><?php echo e(old('bio', '')); ?></textarea>
                            <p class="mt-1.5 text-xs text-slate-500 flex items-center gap-1"><i class="fas fa-info-circle text-purple-500"></i>الحد الأقصى 1000 حرف. سيتم تنقية HTML تلقائياً.</p>
                            <?php $__errorArgs = ['bio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1"><i class="fas fa-exclamation-circle"></i><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-xl border border-slate-200 bg-white p-6">
                        <h3 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-600"></i>
                            إرشادات إنشاء الحساب
                        </h3>
                        <ul class="space-y-3 text-sm text-slate-600">
                            <li class="flex items-start gap-2.5">
                                <i class="fas fa-check-circle text-emerald-500 mt-0.5 flex-shrink-0"></i>
                                <span>تأكد من صحة رقم الهاتف والبريد الإلكتروني لأنهما يُستخدمان في تسجيل الدخول والإشعارات.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="fas fa-check-circle text-emerald-500 mt-0.5 flex-shrink-0"></i>
                                <span>اختر الدور المناسب بناءً على مهام المستخدم في الفريق.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="fas fa-check-circle text-emerald-500 mt-0.5 flex-shrink-0"></i>
                                <span>يمكن تفعيل الحساب لاحقاً إذا رغبت في المراجعة أولاً.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="fas fa-check-circle text-emerald-500 mt-0.5 flex-shrink-0"></i>
                                <span>أضف نبذة تعريفية للمدربين لعرضها في صفحة الكورس.</span>
                            </li>
                        </ul>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
                        <p class="text-xs text-slate-600"><span class="text-rose-500 font-bold">*</span> الحقول المطلوبة لإكمال إنشاء الحساب.</p>
                        <div class="flex flex-col gap-3">
                            <button type="submit" id="submitBtn" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg hover:shadow-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-[1.02] active:scale-[0.98]">
                                <i class="fas fa-save"></i>
                                <span>إنشاء المستخدم</span>
                            </button>
                            <a href="<?php echo e(route('admin.users.index')); ?>" class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-slate-300 px-6 py-3.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-400 transition-all">
                                <i class="fas fa-times"></i>
                                <span>إلغاء والعودة</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    // حماية من XSS - تنقية البيانات قبل الإرسال
    function sanitizeInput(input) {
        if (!input) return '';
        const div = document.createElement('div');
        div.textContent = input;
        return div.innerHTML;
    }

    // عرض/إخفاء كلمة المرور
    function togglePasswordVisibility(fieldId) {
        const field = document.getElementById(fieldId);
        const eye = document.getElementById(fieldId + '-eye');
        if (field.type === 'password') {
            field.type = 'text';
            eye.classList.remove('fa-eye');
            eye.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            eye.classList.remove('fa-eye-slash');
            eye.classList.add('fa-eye');
        }
    }

    // التحقق من صحة رقم الهاتف (أرقام فقط، الطول يتحقق منه السيرفر حسب الدولة)
    document.getElementById('phone').addEventListener('input', function () {
        let sanitized = this.value.replace(/\D/g, '');
        this.value = sanitized;
        if (sanitized.length > 15) {
            this.value = sanitized.slice(0, 15);
        }
        this.setCustomValidity(sanitized.length && sanitized.length < 6 ? 'رقم الهاتف قصير جداً' : '');
    });

    // التحقق من صحة الاسم (عربي فقط)
    document.getElementById('name').addEventListener('input', function () {
        const arabicPattern = /^[\u0600-\u06FF\s]+$/;
        if (this.value && !arabicPattern.test(this.value.trim())) {
            this.setCustomValidity('الاسم يجب أن يحتوي على أحرف عربية فقط');
        } else {
            this.setCustomValidity('');
        }
    });

    // التحقق من صحة البريد الإلكتروني
    document.getElementById('email').addEventListener('input', function () {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (this.value && !emailPattern.test(this.value.trim())) {
            this.setCustomValidity('البريد الإلكتروني غير صحيح');
        } else {
            this.setCustomValidity('');
        }
    });

    // التحقق من قوة كلمة المرور
    document.getElementById('password').addEventListener('input', function () {
        if (this.value.length < 8) {
            this.setCustomValidity('كلمة المرور يجب أن تكون 8 أحرف على الأقل');
        } else {
            this.setCustomValidity('');
        }
    });

    // منع إرسال النموذج المتكرر (Double Submit Protection)
    let formSubmitting = false;
    document.getElementById('createUserForm').addEventListener('submit', function(e) {
        if (formSubmitting) {
            e.preventDefault();
            return false;
        }
        formSubmitting = true;
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>جاري الإنشاء...</span>';
    });

</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/users/create.blade.php ENDPATH**/ ?>