<?php $__env->startSection('title', 'الملف الشخصي'); ?>
<?php $__env->startSection('header', 'الملف الشخصي'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .profile-header-card {
        background: linear-gradient(135deg, rgba(44, 169, 189, 0.1) 0%, rgba(101, 219, 228, 0.05) 100%);
        border: 2px solid rgba(44, 169, 189, 0.2);
    }

    .profile-avatar {
        transition: all 0.3s;
        box-shadow: 0 10px 30px rgba(44, 169, 189, 0.2);
    }

    .profile-avatar:hover {
        transform: scale(1.05);
        box-shadow: 0 15px 40px rgba(44, 169, 189, 0.3);
    }

    .info-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 2px solid rgba(44, 169, 189, 0.1);
        transition: all 0.3s;
    }

    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(44, 169, 189, 0.1);
        border-color: rgba(44, 169, 189, 0.3);
    }

    .form-input {
        transition: all 0.3s;
    }

    .form-input:focus {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(44, 169, 189, 0.15);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $memberSince = $user->created_at ? $user->created_at->copy()->locale('ar')->translatedFormat('d F Y') : null;
    $tasksCount = $user->employeeTasks()->count();
    $leavesCount = \App\Models\LeaveRequest::where('employee_id', $user->id)->count();
    $lastLogin = $user->last_login_at ? $user->last_login_at->copy()->locale('ar')->diffForHumans() : null;
?>

<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            <i class="fas fa-check-circle ml-2"></i>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <!-- الهيدر -->
    <div class="profile-header-card rounded-2xl p-6 sm:p-8 shadow-lg">
        <div class="flex flex-col lg:flex-row items-start lg:items-center gap-6 lg:justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center gap-5 w-full lg:w-auto">
                <div class="profile-avatar flex items-center justify-center h-24 w-24 sm:h-28 sm:w-28 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white overflow-hidden mx-auto sm:mx-0">
                    <?php if($user->profile_image): ?>
                        <img src="<?php echo e($user->profile_image_url); ?>" alt="صورة الملف الشخصي" class="w-full h-full object-cover">
                    <?php else: ?>
                        <span class="text-4xl font-bold"><?php echo e(mb_substr($user->name, 0, 1, 'UTF-8')); ?></span>
                    <?php endif; ?>
                </div>
                <div class="text-center sm:text-right">
                    <h2 class="text-2xl sm:text-3xl font-black text-gray-900 mb-2"><?php echo e($user->name); ?></h2>
                    <p class="text-gray-600 font-medium mb-1">
                        <?php if($user->employeeJob): ?>
                            <?php echo e($user->employeeJob->name); ?>

                        <?php else: ?>
                            موظف
                        <?php endif; ?>
                        <?php if($user->employee_code): ?>
                            <span class="text-gray-500">(<?php echo e($user->employee_code); ?>)</span>
                        <?php endif; ?>
                    </p>
                    <p class="text-sm text-gray-500">عضو منذ <?php echo e($memberSince); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-6 border-2 border-blue-200/50 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي المهام</p>
                    <p class="text-3xl font-black text-gray-900"><?php echo e($tasksCount); ?></p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-tasks text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border-2 border-green-200/50 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">الإجازات</p>
                    <p class="text-3xl font-black text-gray-900"><?php echo e($leavesCount); ?></p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-calendar-alt text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border-2 border-purple-200/50 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">تاريخ الانضمام</p>
                    <p class="text-lg font-black text-gray-900"><?php echo e($memberSince ?: '—'); ?></p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-calendar text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border-2 border-yellow-200/50 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-1">آخر تسجيل دخول</p>
                    <p class="text-lg font-black text-gray-900"><?php echo e($lastLogin ?: '—'); ?></p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- النماذج -->
    <div class="info-card rounded-2xl p-6 sm:p-8 shadow-lg">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
            <div>
                <h3 class="text-xl sm:text-2xl font-black text-gray-900 mb-2">تحديث البيانات الأساسية</h3>
                <p class="text-sm sm:text-base text-gray-600 font-medium">قم بمراجعة معلوماتك وتحديثها في أي وقت</p>
            </div>
        </div>

        <form method="POST" action="<?php echo e(route('employee.profile.update')); ?>" class="space-y-8" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="group">
                    <label class="block text-sm font-bold text-gray-900 mb-2">الاسم الكامل</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="name" value="<?php echo e(old('name', $user->name)); ?>" required
                               class="form-input w-full rounded-xl border-2 border-gray-200 bg-white px-11 py-3 text-gray-900 font-medium focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20">
                    </div>
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-600 text-xs mt-2 font-semibold"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="group">
                    <label class="block text-sm font-bold text-gray-900 mb-2">رقم الهاتف</label>
                    <div class="relative">
                        <i class="fas fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>" required
                               class="form-input w-full rounded-xl border-2 border-gray-200 bg-white px-11 py-3 text-gray-900 font-medium focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20">
                    </div>
                    <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-600 text-xs mt-2 font-semibold"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="md:col-span-2 group">
                    <label class="block text-sm font-bold text-gray-900 mb-2">البريد الإلكتروني (اختياري)</label>
                    <div class="relative">
                        <i class="fas fa-at absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>"
                               class="form-input w-full rounded-xl border-2 border-gray-200 bg-white px-11 py-3 text-gray-900 font-medium focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20">
                    </div>
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-600 text-xs mt-2 font-semibold"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="space-y-4">
                <label class="block text-sm font-bold text-gray-900 mb-3">صورة الملف الشخصي</label>
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-2xl overflow-hidden border-2 border-dashed border-blue-300 bg-gray-50 flex items-center justify-center">
                        <?php if($user->profile_image): ?>
                            <img src="<?php echo e($user->profile_image_url); ?>" alt="صورة الملف الشخصي" class="w-full h-full object-cover">
                        <?php else: ?>
                            <i class="fas fa-camera text-blue-500 text-3xl"></i>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1">
                        <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border-2 border-dashed border-blue-300 bg-blue-50 px-6 py-3 text-sm font-bold text-gray-900 hover:bg-blue-100 transition-all">
                            <i class="fas fa-upload text-blue-500"></i>
                            <span>اختر صورة جديدة (PNG أو JPG)</span>
                            <input type="file" name="profile_image" accept="image/*" class="hidden">
                        </label>
                        <p class="mt-2 text-xs text-gray-600 font-medium">الحد الأقصى لحجم الملف 2 ميجابايت.</p>
                        <?php $__errorArgs = ['profile_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-600 text-xs mt-2 font-semibold"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <div class="space-y-6 rounded-2xl border-2 border-dashed border-blue-200 bg-blue-50 p-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h4 class="text-base sm:text-lg font-black text-gray-900 mb-1">تغيير كلمة المرور</h4>
                        <p class="text-xs text-gray-600 font-medium">اترك الحقول فارغة إذا لم ترغب في التغيير الآن</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div class="group">
                        <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 mb-2">كلمة المرور الحالية</label>
                        <input type="password" name="current_password"
                               class="form-input w-full rounded-xl border-2 border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 font-medium focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20">
                        <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-600 text-xs mt-2 font-semibold"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="group">
                        <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 mb-2">كلمة المرور الجديدة</label>
                        <input type="password" name="password"
                               class="form-input w-full rounded-xl border-2 border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 font-medium focus:border-green-500 focus:ring-4 focus:ring-green-500/20">
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-600 text-xs mt-2 font-semibold"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="group">
                        <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 mb-2">تأكيد كلمة المرور</label>
                        <input type="password" name="password_confirmation"
                               class="form-input w-full rounded-xl border-2 border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 font-medium focus:border-green-500 focus:ring-4 focus:ring-green-500/20">
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between pt-4 border-t-2 border-gray-200">
                <div class="text-xs text-gray-600 flex items-center gap-2 font-medium">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    <span>سيتم إرسال إشعار إلى بريدك في حال تغيير كلمة المرور.</span>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="<?php echo e(route('employee.dashboard')); ?>" class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-gray-300 bg-white px-6 py-3 text-sm font-bold text-gray-900 hover:border-gray-400 transition-all">
                        <i class="fas fa-arrow-right"></i>
                        رجوع إلى اللوحة
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg hover:shadow-xl transition-all">
                        <i class="fas fa-save"></i>
                        حفظ التعديلات
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\employee\profile\index.blade.php ENDPATH**/ ?>