

<?php $__env->startSection('title', 'الملف الشخصي'); ?>
<?php $__env->startSection('header', 'الملف الشخصي'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .profile-header-card {
        background: linear-gradient(135deg, rgba(44, 169, 189, 0.1) 0%, rgba(101, 219, 228, 0.05) 100%);
        border: 2px solid rgba(44, 169, 189, 0.2);
    }
    .info-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 2px solid rgba(44, 169, 189, 0.1);
    }
    .form-input:focus {
        box-shadow: 0 8px 20px rgba(44, 169, 189, 0.15);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $memberSince = $user->created_at ? $user->created_at->copy()->locale('ar')->translatedFormat('d F Y') : null;
    $lastLogin = $user->last_login_at ? $user->last_login_at->copy()->locale('ar')->diffForHumans() : null;
?>

<div class="space-y-6">
    <div class="profile-header-card rounded-2xl p-6 sm:p-8 shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center gap-5">
            <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white overflow-hidden flex items-center justify-center mx-auto sm:mx-0">
                <?php if($user->profile_image): ?>
                    <img src="<?php echo e($user->profile_image_url); ?>" alt="" class="w-full h-full object-cover">
                <?php else: ?>
                    <span class="text-4xl font-bold"><?php echo e(mb_substr($user->name, 0, 1, 'UTF-8')); ?></span>
                <?php endif; ?>
            </div>
            <div class="text-center sm:text-right">
                <h2 class="text-2xl font-black text-gray-900"><?php echo e($user->name); ?></h2>
                <p class="text-gray-600">مدير مكان — <?php echo e($location->name ?? ''); ?></p>
                <p class="text-sm text-gray-500 mt-1">عضو منذ <?php echo e($memberSince); ?></p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl p-5 border-2 border-blue-200/50 shadow-lg">
            <p class="text-sm text-gray-600">البريد</p>
            <p class="font-bold text-gray-900 mt-1" dir="ltr"><?php echo e($user->email); ?></p>
        </div>
        <div class="bg-white rounded-xl p-5 border-2 border-green-200/50 shadow-lg">
            <p class="text-sm text-gray-600">آخر تسجيل دخول</p>
            <p class="font-bold text-gray-900 mt-1"><?php echo e($lastLogin ?? '—'); ?></p>
        </div>
    </div>

    <div class="info-card rounded-2xl p-6 sm:p-8 shadow-lg">
        <h3 class="text-xl font-black text-gray-900 mb-6">تحديث البيانات وكلمة المرور</h3>
        <form method="POST" action="<?php echo e(route('place.office.profile.update')); ?>" enctype="multipart/form-data" class="space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">الاسم الكامل *</label>
                    <input type="text" name="name" value="<?php echo e(old('name', $user->name)); ?>" required
                           class="form-input w-full rounded-xl border-2 border-gray-200 px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">رقم الهاتف *</label>
                    <input type="text" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>" required
                           class="form-input w-full rounded-xl border-2 border-gray-200 px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-900 mb-2">البريد الإلكتروني *</label>
                    <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" required
                           class="form-input w-full rounded-xl border-2 border-gray-200 px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-900 mb-2">العنوان</label>
                    <input type="text" name="address" value="<?php echo e(old('address', $user->address)); ?>"
                           class="form-input w-full rounded-xl border-2 border-gray-200 px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">صورة الملف الشخصي</label>
                <input type="file" name="profile_image" accept="image/*"
                       class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700">
            </div>

            <div class="border-t pt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">كلمة المرور الحالية</label>
                    <input type="password" name="current_password" class="form-input w-full rounded-xl border-2 border-gray-200 px-4 py-3">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">كلمة مرور جديدة</label>
                    <input type="password" name="password" class="form-input w-full rounded-xl border-2 border-gray-200 px-4 py-3">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" class="form-input w-full rounded-xl border-2 border-gray-200 px-4 py-3">
                </div>
            </div>

            <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold">
                <i class="fas fa-save ml-2"></i>حفظ التغييرات
            </button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.place-manager', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\place-office\profile\index.blade.php ENDPATH**/ ?>