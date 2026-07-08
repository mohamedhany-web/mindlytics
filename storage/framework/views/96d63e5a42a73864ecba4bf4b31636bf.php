<?php
    $__pageLocale = app()->getLocale();
    $__pageRtl = $__pageLocale === 'ar';
?>
<!DOCTYPE html>
<html lang="<?php echo e($__pageLocale); ?>" dir="<?php echo e($__pageRtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>التسجيل في الورشة - <?php echo e($workshop->title); ?> | Mindlytics</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Cairo', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-b from-slate-900 via-slate-950 to-slate-900 min-h-screen flex items-center justify-center px-4 py-8">
    <div class="max-w-4xl w-full">
        <div class="mb-6 text-center text-white">
            <h1 class="text-3xl md:text-4xl font-black mb-2">
                التسجيل في الورشة
            </h1>
            <p class="text-sm text-slate-200">
                املأ البيانات التالية للحجز في الورشة، وسيتم التواصل معك لتأكيد الاشتراك.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- معلومات الورشة -->
            <div class="bg-slate-900/80 border border-slate-700/70 rounded-2xl p-6 shadow-2xl backdrop-blur">
                <h2 class="text-xl font-bold text-white mb-3 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-400 text-white shadow-lg">
                        <i class="fas fa-people-arrows"></i>
                    </span>
                    <span><?php echo e($workshop->title); ?></span>
                </h2>
                <div class="space-y-3 text-sm text-slate-200">
                    <?php if($workshop->starts_at): ?>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-blue-400"></i>
                            <span>تاريخ البداية: <?php echo e($workshop->starts_at->format('Y-m-d H:i')); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if($workshop->ends_at): ?>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-clock text-blue-400"></i>
                            <span>تاريخ النهاية: <?php echo e($workshop->ends_at->format('Y-m-d H:i')); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php
                        $total = $workshop->max_seats ?: null;
                        $registeredCount = $workshop->registrations()->count();
                    ?>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-users text-emerald-400"></i>
                        <?php if($total): ?>
                            <span>المقاعد: <?php echo e($registeredCount); ?> / <?php echo e($total); ?> <?php if(!is_null($remaining)): ?> (متبقي <?php echo e($remaining); ?>) <?php endif; ?></span>
                        <?php else: ?>
                            <span>المقاعد: غير محدودة</span>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-location-dot text-pink-400"></i>
                        <?php if($workshop->mode === 'online'): ?>
                            <span>طريقة الحضور: أونلاين (عن بُعد)</span>
                        <?php elseif($workshop->mode === 'offline'): ?>
                            <span>طريقة الحضور: في المكان (أوفلاين)</span>
                        <?php else: ?>
                            <span>طريقة الحضور: يمكن اختيار أونلاين أو أوفلاين</span>
                        <?php endif; ?>
                    </div>
                    <?php if($workshop->description): ?>
                        <div class="pt-3 border-t border-slate-700/60 text-xs text-slate-200 whitespace-pre-line leading-relaxed">
                            <?php echo e($workshop->description); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- فورم التسجيل -->
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 p-6">
                <?php if(session('success')): ?>
                    <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        <span><?php echo e(session('success')); ?></span>
                    </div>
                <?php endif; ?>
                <?php if(session('error')): ?>
                    <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo e(session('error')); ?></span>
                    </div>
                <?php endif; ?>
                <?php if($errors->any()): ?>
                    <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if(!is_null($remaining) && $remaining <= 0): ?>
                    <p class="text-sm text-rose-600 font-semibold">
                        تم اكتمال العدد في هذه الورشة، ولا يمكن استقبال تسجيلات جديدة حالياً.
                    </p>
                <?php else: ?>
                    <form method="POST" action="<?php echo e(route('public.workshops.register', $workshop->slug)); ?>" class="space-y-4">
                        <?php echo csrf_field(); ?>
                        <div class="space-y-1">
                            <label for="name" class="block text-sm font-semibold text-slate-800">الاسم الكامل<span class="text-rose-500 ml-1">*</span></label>
                            <input type="text" id="name" name="name" value="<?php echo e(old('name')); ?>"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/70 focus:border-blue-500"
                                   required>
                        </div>
                        <div class="space-y-1">
                            <label for="email" class="block text-sm font-semibold text-slate-800">البريد الإلكتروني</label>
                            <input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/70 focus:border-blue-500">
                        </div>
                        <div class="space-y-1">
                            <label for="phone" class="block text-sm font-semibold text-slate-800">رقم الجوال / واتساب</label>
                            <input type="text" id="phone" name="phone" value="<?php echo e(old('phone')); ?>"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/70 focus:border-blue-500">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-sm font-semibold text-slate-800">طريقة الحضور</label>
                            <?php if($workshop->mode === 'online'): ?>
                                <p class="text-xs text-slate-600 mb-1">هذه الورشة تُقدم أونلاين فقط.</p>
                                <input type="hidden" name="attendance_mode" value="online">
                                <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                    <i class="fas fa-globe"></i>
                                    أونلاين (عن بُعد)
                                </span>
                            <?php elseif($workshop->mode === 'offline'): ?>
                                <p class="text-xs text-slate-600 mb-1">هذه الورشة تُقدم في المكان فقط.</p>
                                <input type="hidden" name="attendance_mode" value="offline">
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                                    <i class="fas fa-building"></i>
                                    في المكان (أوفلاين)
                                </span>
                            <?php else: ?>
                                <p class="text-xs text-slate-600 mb-1">اختر كيف تفضل حضور الورشة:</p>
                                <div class="flex flex-col gap-2 text-sm">
                                    <label class="inline-flex items-center gap-2">
                                        <input type="radio" name="attendance_mode" value="online" class="text-blue-600 border-slate-300 focus:ring-blue-500"
                                               <?php echo e(old('attendance_mode', 'online') === 'online' ? 'checked' : ''); ?>>
                                        <span>أونلاين (عن بُعد)</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2">
                                        <input type="radio" name="attendance_mode" value="offline" class="text-blue-600 border-slate-300 focus:ring-blue-500"
                                               <?php echo e(old('attendance_mode') === 'offline' ? 'checked' : ''); ?>>
                                        <span>في المكان (أوفلاين)</span>
                                    </label>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="space-y-1">
                            <label for="notes" class="block text-sm font-semibold text-slate-800">ملاحظات إضافية</label>
                            <textarea id="notes" name="notes" rows="3"
                                      class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/70 focus:border-blue-500"
                                      placeholder="مثلاً: مستواك الحالي، الأمور التي تود التركيز عليها في الورشة، أو أي متطلبات خاصة."><?php echo e(old('notes')); ?></textarea>
                        </div>
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 px-6 py-3 text-sm font-semibold text-white shadow-lg hover:shadow-2xl transition-all duration-200">
                            <i class="fas fa-paper-plane"></i>
                            <span>إرسال طلب التسجيل</span>
                        </button>
                        <p class="text-[11px] text-slate-500 text-center mt-2">
                            بتعبئة هذا النموذج فأنت توافق على تواصل فريق Mindlytics معك بخصوص تفاصيل الورشة ومواعيدها.
                        </p>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" defer></script>
</body>
</html>

<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\public\workshop-register.blade.php ENDPATH**/ ?>