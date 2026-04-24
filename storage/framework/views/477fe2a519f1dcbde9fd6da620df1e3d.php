<?php $__env->startSection('title', 'إعدادات النظام'); ?>
<?php $__env->startSection('header', 'إعدادات النظام'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full bg-gradient-to-b from-slate-50 via-white to-slate-100">
    <div class="w-full max-w-[1500px] mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8 space-y-6">

        <?php if(session('success')): ?>
            <div class="rounded-2xl border border-emerald-200/80 bg-emerald-50/90 text-emerald-900 px-5 py-4 text-sm font-semibold shadow-sm flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-white"><i class="fas fa-check"></i></span>
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('warning')): ?>
            <div class="rounded-2xl border border-amber-200/80 bg-amber-50/90 text-amber-950 px-5 py-4 text-sm font-semibold shadow-sm flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-white"><i class="fas fa-exclamation-triangle"></i></span>
                <?php echo e(session('warning')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-900 shadow-sm">
                <p class="font-bold mb-2">يرجى تصحيح ما يلي:</p>
                <ul class="list-disc list-inside space-y-1">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($e); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="rounded-3xl border border-slate-200/80 bg-white p-5 lg:p-6 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h2 class="text-2xl lg:text-3xl font-black text-slate-900 tracking-tight">إعدادات النظام العامة</h2>
                    <p class="mt-2 text-slate-600 text-sm lg:text-base max-w-3xl leading-relaxed">إدارة الهوية البصرية وطرق الدفع من مكان واحد. التغييرات تنعكس مباشرة على صفحات المنصة.</p>
                </div>
                <div class="inline-flex items-center gap-2 rounded-2xl bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700">
                    <i class="fas fa-shield-alt text-emerald-600"></i>
                    إعدادات آمنة ومباشرة
                </div>
            </div>
        </div>

        <form method="post" action="<?php echo e(route('admin.system-settings.update')); ?>" enctype="multipart/form-data" class="space-y-8">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 lg:gap-8">

                
                <section class="rounded-3xl border border-slate-200/80 bg-white shadow-xl shadow-slate-200/50 overflow-hidden">
                    <div class="px-6 py-5 lg:px-8 lg:py-6 border-b border-slate-100 bg-gradient-to-l from-blue-600 to-indigo-600 text-white">
                        <div class="flex items-center gap-3">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 backdrop-blur">
                                <i class="fas fa-palette text-xl"></i>
                            </span>
                            <div>
                                <h3 class="text-lg font-black">الهوية البصرية</h3>
                                <p class="text-sm text-blue-100/90 mt-0.5">الشعار والأيقونة تظهر في لوحة الإدارة وأنحاء الموقع</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 lg:p-8 space-y-8">
                        <div class="grid sm:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <label class="text-sm font-bold text-slate-800">شعار الموقع (Logo)</label>
                                <p class="text-xs text-slate-500 leading-relaxed">PNG, JPG, WebP, GIF — حتى 2 ميغابايت</p>
                                <div class="flex items-center gap-4">
                                    <div class="h-28 w-28 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 flex items-center justify-center p-2 overflow-hidden shrink-0">
                                        <img src="<?php echo e($logoUrl); ?>" alt="" class="max-h-full max-w-full object-contain" id="logo-preview">
                                    </div>
                                    <input type="file" name="logo" accept="image/png,image/jpeg,image/jpg,image/webp,image/gif"
                                           class="block w-full min-w-0 text-sm text-slate-700 file:mr-0 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                                </div>
                            </div>
                            <div class="space-y-3">
                                <label class="text-sm font-bold text-slate-800">أيقونة المتصفح (Favicon)</label>
                                <p class="text-xs text-slate-500 leading-relaxed">ICO, PNG, SVG, WebP — حتى 4 ميغابايت</p>
                                <div class="flex items-center gap-4">
                                    <div class="h-20 w-20 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 flex items-center justify-center p-2 shrink-0">
                                        <img src="<?php echo e($faviconUrl); ?>" alt="" class="max-h-full max-w-full object-contain" id="favicon-preview" width="48" height="48">
                                    </div>
                                    <input type="file" name="favicon" accept=".ico,.png,.svg,.webp"
                                           class="block w-full min-w-0 text-sm text-slate-700 file:mr-0 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-slate-700 file:text-white hover:file:bg-slate-800 cursor-pointer">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                
                <section class="rounded-3xl border border-slate-200/80 bg-white shadow-xl shadow-slate-200/50 overflow-hidden">
                    <div class="px-6 py-5 lg:px-8 lg:py-6 border-b border-slate-100 bg-gradient-to-l from-emerald-600 to-teal-600 text-white">
                        <div class="flex items-center gap-3">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 backdrop-blur">
                                <i class="fas fa-money-bill-wave text-xl"></i>
                            </span>
                            <div>
                                <h3 class="text-lg font-black">طريقة الدفع عبر المنصة</h3>
                                <p class="text-sm text-emerald-100/90 mt-0.5">تؤثر على صفحة إتمام الطلب (كورس/مسار) وصفحة تفاصيل فاتورتك</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 lg:p-8 space-y-5">
                        <p class="text-sm text-slate-600 leading-relaxed">اختر كيف يدفع العميل: تحويل يدوي مع رفع إيصال، أو بوابة كاشير، أو فواتيرك (iframe) لشراء الكورسات مع مفاتيح Vendor/Provider في ملف البيئة.</p>

                        <div class="grid gap-4">
                            <label class="relative flex cursor-pointer rounded-2xl border-2 p-4 transition-all has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50 border-slate-200 hover:border-slate-300">
                                <input type="radio" name="platform_payment_mode" value="manual" class="mt-1 text-emerald-600 focus:ring-emerald-500" <?php if(old('platform_payment_mode', $platformPaymentMode) === 'manual'): echo 'checked'; endif; ?>>
                                <div class="mr-4 flex-1">
                                    <span class="font-bold text-slate-900">دفع يدوي</span>
                                    <p class="text-sm text-slate-600 mt-1">صفحة الطلب تعرض بيانات التحويل ورفع صورة الإيصال. مناسب للمراجعة اليدوية قبل التفعيل.</p>
                                </div>
                            </label>

                            <label class="relative flex cursor-pointer rounded-2xl border-2 p-4 transition-all has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50/50 border-slate-200 hover:border-slate-300">
                                <input type="radio" name="platform_payment_mode" value="kashier" class="mt-1 text-blue-600 focus:ring-blue-500" <?php if(old('platform_payment_mode', $platformPaymentMode) === 'kashier'): echo 'checked'; endif; ?>>
                                <div class="mr-4 flex-1">
                                    <span class="font-bold text-slate-900">بوابة كاشير (الحالية)</span>
                                    <p class="text-sm text-slate-600 mt-1">دفع إلكتروني عبر iframe كاشير كما هو مُفعّل اليوم في النظام.</p>
                                </div>
                            </label>

                            <label class="relative flex cursor-pointer rounded-2xl border-2 p-4 transition-all has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/50 border-slate-200 hover:border-slate-300">
                                <input type="radio" name="platform_payment_mode" value="fawaterak" class="mt-1 text-indigo-600 focus:ring-indigo-500" <?php if(old('platform_payment_mode', $platformPaymentMode) === 'fawaterak'): echo 'checked'; endif; ?>>
                                <div class="mr-4 flex-1">
                                    <span class="font-bold text-slate-900">فواتيرك (iframe — الكورسات)</span>
                                    <p class="text-sm text-slate-600 mt-1">دفع إلكتروني عبر إضافة فواتيرك في صفحة إتمام طلب الكورس. يتطلب FAWATERAK_VENDOR_KEY و FAWATERAK_PROVIDER_KEY و FAWATERAK_INTEGRATION=iframe في .env وتطابق نطاق الـ HMAC مع لوحة فواتيرك.</p>
                                </div>
                            </label>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white p-4 space-y-2">
                            <label class="flex items-start gap-3 cursor-pointer text-sm text-slate-800">
                                <input type="checkbox" name="fawaterak_gateway_on" value="1" class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" <?php if(old('fawaterak_gateway_on', $fawaterakGatewayEnabled ?? true)): echo 'checked'; endif; ?>>
                                <span><span class="font-bold">تفعيل بوابة فواتيرك</span> — عند إلغاء التفعيل لن يظهر مسار الدفع الإلكتروني لفواتيرك حتى لو كان الوضع «فواتيرك» محدداً.</span>
                            </label>
                        </div>

                        <div id="gateway-fees" class="rounded-2xl border border-amber-200/80 bg-amber-50/40 p-5 space-y-4">
                            <h4 class="text-sm font-black text-amber-950 flex items-center gap-2">
                                <i class="fas fa-percentage text-amber-600"></i>
                                عمولة بوابة الدفع (محاسبية)
                            </h4>
                            <p class="text-xs text-amber-900/85 leading-relaxed">تُحسب عند إتمام دفع أونلاين عبر كاشير أو فواتيرك (وليس التحويل اليدوي). تُسجَّل في الدفعة وتُنشأ لها <strong>معاملة مدينة</strong> (فئة fee) في سجل المعاملات. يمكن أيضاً ضبط القيم عبر متغيرات البيئة <code class="bg-white/80 px-1 rounded">GATEWAY_FEE_MODE</code> و<code class="bg-white/80 px-1 rounded">GATEWAY_FEE_PERCENT</code> و<code class="bg-white/80 px-1 rounded">GATEWAY_FEE_FIXED_EGP</code> كافتراضي قبل دمج JSON.</p>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">طريقة احتساب العمولة</label>
                                <select name="gateway_fee_mode" class="w-full max-w-md rounded-xl border border-slate-200 px-3 py-2.5 text-sm">
                                    <option value="none" <?php if(old('gateway_fee_mode', $gatewayFeeMode ?? 'none') === 'none'): echo 'selected'; endif; ?>>بدون عمولة مسجّلة</option>
                                    <option value="percent" <?php if(old('gateway_fee_mode', $gatewayFeeMode ?? 'none') === 'percent'): echo 'selected'; endif; ?>>نسبة من إجمالي العملية (%)</option>
                                    <option value="fixed" <?php if(old('gateway_fee_mode', $gatewayFeeMode ?? 'none') === 'fixed'): echo 'selected'; endif; ?>>مبلغ ثابت لكل عملية (ج.م)</option>
                                </select>
                            </div>
                            <div class="grid sm:grid-cols-2 gap-4 max-w-2xl">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">النسبة % (عند اختيار نسبة)</label>
                                    <input type="number" name="gateway_fee_percent" step="0.01" min="0" max="100" value="<?php echo e(old('gateway_fee_percent', $gatewayFeePercent ?? '0')); ?>" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">مبلغ ثابت ج.م (عند اختيار ثابت)</label>
                                    <input type="number" name="gateway_fee_fixed" step="0.01" min="0" value="<?php echo e(old('gateway_fee_fixed', $gatewayFeeFixed ?? '0')); ?>" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-slate-50 border border-slate-200/80 p-4 text-xs text-slate-600 leading-relaxed">
                            <i class="fas fa-lightbulb text-amber-500 ml-1"></i>
                            تأكد من ضبط <strong>محافظ المنصة</strong> من الإدارة عند استخدام الدفع اليدوي حتى تظهر أرقام التحويل للطلاب.
                        </div>
                    </div>
                </section>
            </div>

            <div class="sticky bottom-4 z-20">
                <div class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-white/95 backdrop-blur px-4 py-3 shadow-lg">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl bg-gradient-to-l from-blue-600 to-indigo-600 text-white text-sm font-black shadow-lg shadow-blue-500/25 hover:from-blue-700 hover:to-indigo-700 transition-all">
                        <i class="fas fa-save"></i>
                        حفظ كل الإعدادات
                    </button>
                    <span class="hidden sm:inline text-xs text-slate-500">يتم تطبيق التغييرات فور الحفظ</span>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/system-settings/index.blade.php ENDPATH**/ ?>