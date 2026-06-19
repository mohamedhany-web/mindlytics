

<?php $__env->startSection('title', 'تفاصيل المكان'); ?>
<?php $__env->startSection('header', 'تفاصيل المكان'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900"><?php echo e($offlineLocation->name); ?></h1>
                <p class="text-gray-600 mt-1">عرض تفاصيل المكان</p>
            </div>
            <div class="flex gap-2">
                <a href="<?php echo e(route('admin.offline-locations.index')); ?>" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-arrow-right mr-2"></i>العودة
                </a>
                <a href="<?php echo e(route('admin.offline-locations.edit', $offlineLocation)); ?>" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-edit mr-2"></i>تعديل
                </a>
            </div>
        </div>
    </div>

    <!-- الإحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي الكورسات</p>
                        <p class="text-3xl font-black text-gray-900"><?php echo e($stats['total_courses']); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-book-reader text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-green-200/50 hover:border-green-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 253, 250, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">الكورسات النشطة</p>
                        <p class="text-3xl font-black text-green-700"><?php echo e($stats['active_courses']); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- معلومات المكان -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">معلومات المكان</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php if($offlineLocation->address): ?>
            <div>
                <p class="text-sm text-gray-600 mb-1">العنوان</p>
                <p class="font-semibold text-gray-900 text-lg"><?php echo e($offlineLocation->address); ?></p>
            </div>
            <?php endif; ?>
            <?php if($offlineLocation->city): ?>
            <div>
                <p class="text-sm text-gray-600 mb-1">المدينة</p>
                <p class="font-semibold text-gray-900 text-lg"><?php echo e($offlineLocation->city); ?></p>
            </div>
            <?php endif; ?>
            <?php if($offlineLocation->phone): ?>
            <div>
                <p class="text-sm text-gray-600 mb-1">رقم الهاتف</p>
                <p class="font-semibold text-gray-900 text-lg"><?php echo e($offlineLocation->phone); ?></p>
            </div>
            <?php endif; ?>
            <?php if($offlineLocation->capacity > 0): ?>
            <div>
                <p class="text-sm text-gray-600 mb-1">السعة</p>
                <p class="font-semibold text-gray-900 text-lg"><?php echo e($offlineLocation->capacity); ?> شخص</p>
            </div>
            <?php endif; ?>
            <div>
                <p class="text-sm text-gray-600 mb-1">الحالة</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold <?php echo e($offlineLocation->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'); ?>">
                    <?php echo e($offlineLocation->is_active ? 'نشط' : 'غير نشط'); ?>

                </span>
            </div>
        </div>
        <?php if($offlineLocation->description): ?>
        <div class="mt-6 pt-6 border-t border-gray-200">
            <p class="text-sm text-gray-600 mb-2">الوصف</p>
            <p class="text-gray-900 leading-relaxed"><?php echo e($offlineLocation->description); ?></p>
        </div>
        <?php endif; ?>
    </div>

    <!-- الفوترة ومدير المكان -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 space-y-6">
        <h2 class="text-xl font-bold text-gray-900">الفوترة والمخالصة الشهرية</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500">سعر الساعة</span><p class="font-semibold"><?php echo e($offlineLocation->hourly_rate ? number_format((float) $offlineLocation->hourly_rate, 2).' ج.م' : '—'); ?></p></div>
            <div><span class="text-gray-500">المحفظة</span><p class="font-semibold"><?php echo e($offlineLocation->defaultWallet?->name ?? '—'); ?></p></div>
        </div>
        <div class="flex gap-3 text-sm">
            <a href="<?php echo e(route('admin.place-settlements.index', ['location_id' => $offlineLocation->id])); ?>" class="text-blue-600">مخالصات هذا المكان</a>
            <a href="<?php echo e(route('admin.place-usage-logs.index', ['location_id' => $offlineLocation->id])); ?>" class="text-blue-600">سجلات الساعات</a>
        </div>
    </div>

    <?php if(session('generated_place_manager_password')): ?>
        <div class="rounded-2xl border-2 border-amber-300 bg-amber-50 p-6 text-amber-950 shadow-lg">
            <h3 class="font-black text-lg mb-2 flex items-center gap-2"><i class="fas fa-key text-amber-600"></i> بيانات الدخول (عرض لمرة واحدة)</h3>
            <p class="text-sm mb-4 text-amber-900/90">أرسل هذه البيانات لمدير المكان عبر قناة آمنة.</p>
            <div class="grid sm:grid-cols-3 gap-4 text-sm">
                <div class="bg-white/90 rounded-xl p-4 border border-amber-200">
                    <p class="text-xs font-semibold text-amber-800 mb-1">البريد (اسم المستخدم)</p>
                    <p class="font-mono break-all" dir="ltr"><?php echo e(session('generated_place_manager_email')); ?></p>
                </div>
                <div class="bg-white/90 rounded-xl p-4 border border-amber-200">
                    <p class="text-xs font-semibold text-amber-800 mb-1">رقم الهاتف</p>
                    <p class="font-mono break-all" dir="ltr"><?php echo e(session('generated_place_manager_phone')); ?></p>
                </div>
                <div class="bg-white/90 rounded-xl p-4 border border-amber-200">
                    <p class="text-xs font-semibold text-amber-800 mb-1">كلمة المرور</p>
                    <p class="font-mono break-all select-all" dir="ltr"><?php echo e(session('generated_place_manager_password')); ?></p>
                </div>
            </div>
            <p class="text-xs mt-4 text-amber-900/80">
                تسجيل الدخول: <a class="underline font-semibold text-blue-700" href="<?php echo e(url('/login')); ?>" dir="ltr"><?php echo e(url('/login')); ?></a>
                — لوحة المكان: <a class="underline font-semibold text-blue-700" href="<?php echo e(url('/place-office')); ?>" dir="ltr"><?php echo e(url('/place-office')); ?></a>
            </p>
        </div>
    <?php endif; ?>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900">مديرو المكان</h3>
            <p class="text-xs text-slate-600 mt-1">حسابات بصلاحية <code class="text-xs bg-slate-200 px-1.5 py-0.5 rounded">place_manager</code> — نفس تصميم لوحة الموظف</p>
        </div>

        <?php if(($placeManagers ?? collect())->isNotEmpty()): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-xs font-semibold uppercase text-slate-700">
                            <th class="px-6 py-4 text-right">الاسم</th>
                            <th class="px-6 py-4 text-right">البريد</th>
                            <th class="px-6 py-4 text-right">الهاتف</th>
                            <th class="px-6 py-4 text-right">آخر دخول</th>
                            <th class="px-6 py-4 text-right">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $placeManagers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-6 py-4 font-semibold"><?php echo e($m->name); ?></td>
                                <td class="px-6 py-4 font-mono text-xs" dir="ltr"><?php echo e($m->email); ?></td>
                                <td class="px-6 py-4 font-mono text-xs" dir="ltr"><?php echo e($m->phone); ?></td>
                                <td class="px-6 py-4 text-slate-600"><?php echo e($m->last_login_at?->diffForHumans() ?? '—'); ?></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold <?php echo e($m->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'); ?>">
                                        <?php echo e($m->is_active ? 'نشط' : 'غير نشط'); ?>

                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <form action="<?php echo e(route('admin.offline-locations.place-managers.store', $offlineLocation)); ?>" method="POST" class="p-6 space-y-6">
                <?php echo csrf_field(); ?>
                <h4 class="font-bold text-gray-900 border-b pb-3">إنشاء حساب مدير المكان — بيانات كاملة</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الاسم الكامل *</label>
                        <input type="text" name="name" value="<?php echo e(old('name')); ?>" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني (اسم المستخدم) *</label>
                        <input type="email" name="email" value="<?php echo e(old('email')); ?>" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg" dir="ltr">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف *</label>
                        <input type="text" name="phone" value="<?php echo e(old('phone')); ?>" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg" dir="ltr">
                        <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور *</label>
                        <input type="password" name="password" required minlength="8" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg" dir="ltr">
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">العنوان</label>
                        <input type="text" name="address" value="<?php echo e(old('address')); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-blue-600">
                            <span class="text-sm font-medium text-gray-700">حساب نشط</span>
                        </label>
                    </div>
                </div>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold">
                    <i class="fas fa-user-plus ml-2"></i>إنشاء حساب مدير المكان
                </button>
            </form>
        <?php endif; ?>
    </section>

    <!-- الكورسات المرتبطة -->
    <?php if($offlineLocation->courses->count() > 0): ?>
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-xl font-bold text-gray-900 mb-4">الكورسات المرتبطة</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php $__currentLoopData = $offlineLocation->courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('admin.offline-courses.show', $course)); ?>" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-900"><?php echo e($course->title); ?></h3>
                            <p class="text-sm text-gray-600"><?php echo e($course->instructor->name); ?></p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($course->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'); ?>">
                            <?php echo e($course->status === 'active' ? 'نشط' : $course->status); ?>

                        </span>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/offline-locations/show.blade.php ENDPATH**/ ?>