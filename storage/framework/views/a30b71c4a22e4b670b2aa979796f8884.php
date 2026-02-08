<?php $__env->startSection('title', 'الملف الشخصي - المدرب'); ?>
<?php $__env->startSection('header', 'الملف الشخصي'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .profile-header-card {
        background: linear-gradient(135deg, rgba(44, 169, 189, 0.1) 0%, rgba(31, 58, 86, 0.08) 100%);
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
    .stats-mini-card {
        background: linear-gradient(135deg, rgba(44, 169, 189, 0.05) 0%, rgba(31, 58, 86, 0.03) 100%);
        border: 1.5px solid rgba(44, 169, 189, 0.15);
        transition: all 0.3s;
    }
    .stats-mini-card:hover {
        transform: translateY(-2px);
        border-color: rgba(44, 169, 189, 0.3);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $user = auth()->user();
    $roleMeta = ['label' => 'مدرب', 'color' => 'from-[#1F3A56] via-[#2CA9BD] to-[#65DBE4]', 'chip' => 'bg-gradient-to-r from-[#2CA9BD]/15 to-[#65DBE4]/15 text-[#1F3A56] border-2 border-[#2CA9BD]/30'];
    $memberSince = $user->created_at ? $user->created_at->copy()->locale('ar')->translatedFormat('d F Y') : '—';
    $myCoursesCount = \App\Models\AdvancedCourse::where('instructor_id', $user->id)->count();
    $totalStudents = \App\Models\StudentCourseEnrollment::whereHas('course', function($q) use ($user) {
        $q->where('instructor_id', $user->id);
    })->where('status', 'active')->distinct('user_id')->count();
    $lastLogin = $user->last_login_at ? $user->last_login_at->copy()->locale('ar')->diffForHumans() : '—';
    $stats = [
        ['icon' => 'fa-calendar-week', 'label' => 'تاريخ الانضمام', 'value' => $memberSince, 'color' => 'from-[#2CA9BD] to-[#65DBE4]'],
        ['icon' => 'fa-book-open', 'label' => 'كورساتي', 'value' => $myCoursesCount, 'color' => 'from-purple-500 to-indigo-600'],
        ['icon' => 'fa-user-graduate', 'label' => 'الطلاب', 'value' => $totalStudents, 'color' => 'from-green-500 to-emerald-600'],
        ['icon' => 'fa-clock-rotate-left', 'label' => 'آخر تسجيل دخول', 'value' => $lastLogin, 'color' => 'from-[#FFD34E] to-amber-500'],
    ];
?>

<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-2xl bg-gradient-to-r from-green-500/20 to-emerald-500/20 border-2 border-green-500/30 px-6 py-4 flex items-center gap-3">
            <i class="fas fa-check-circle text-green-600 text-xl"></i>
            <span class="font-bold text-green-800"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <!-- الهيدر -->
    <div class="profile-header-card rounded-2xl p-6 sm:p-8 shadow-lg">
        <div class="flex flex-col lg:flex-row items-start lg:items-center gap-6 lg:justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center gap-5 w-full lg:w-auto">
                <div class="profile-avatar flex items-center justify-center h-24 w-24 sm:h-28 sm:w-28 rounded-2xl bg-gradient-to-br <?php echo e($roleMeta['color']); ?> text-white overflow-hidden mx-auto sm:mx-0">
                    <?php if($user->profile_image): ?>
                        <img src="<?php echo e(asset($user->profile_image)); ?>" alt="صورة الملف الشخصي" class="w-full h-full object-cover">
                    <?php else: ?>
                        <span class="text-4xl sm:text-5xl font-black leading-none"><?php echo e(mb_substr($user->name, 0, 1)); ?></span>
                    <?php endif; ?>
                </div>
                <div class="flex-1 text-center sm:text-right">
                    <span class="inline-flex items-center gap-2 rounded-xl <?php echo e($roleMeta['chip']); ?> px-4 py-2 text-xs font-bold mb-3">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <?php echo e($roleMeta['label']); ?>

                    </span>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-[#1C2C39] mb-2"><?php echo e($user->name); ?></h1>
                    <p class="text-sm sm:text-base text-[#1F3A56] font-medium">إدارة بياناتك وإعدادات حسابك كمدرب</p>
                    <div class="flex flex-col sm:flex-row sm:justify-end gap-3 text-sm mt-3">
                        <span class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#2CA9BD]/10 to-[#65DBE4]/10 text-[#1F3A56] px-4 py-2 font-bold border-2 border-[#2CA9BD]/20">
                            <i class="fas fa-phone text-[#2CA9BD]"></i>
                            <?php echo e($user->phone ?? '—'); ?>

                        </span>
                        <?php if($user->email): ?>
                            <span class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#2CA9BD]/10 to-[#65DBE4]/10 text-[#1F3A56] px-4 py-2 font-bold border-2 border-[#2CA9BD]/20">
                                <i class="fas fa-envelope text-[#2CA9BD]"></i>
                                <?php echo e($user->email); ?>

                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 w-full lg:w-auto">
                <?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="stats-mini-card rounded-xl p-4 text-center">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br <?php echo e($stat['color']); ?> flex items-center justify-center text-white mx-auto mb-2 shadow-md">
                            <i class="fas <?php echo e($stat['icon']); ?> text-sm"></i>
                        </div>
                        <div class="text-xs font-semibold text-[#1F3A56] mb-1 uppercase tracking-wide"><?php echo e($stat['label']); ?></div>
                        <div class="text-base sm:text-lg font-black text-[#1C2C39]"><?php echo e($stat['value']); ?></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:gap-8 lg:grid-cols-3">
        <!-- البطاقات الجانبية -->
        <div class="space-y-6">
            <div class="info-card rounded-2xl p-6 shadow-lg">
                <h2 class="text-lg sm:text-xl font-black text-[#1C2C39] mb-5 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-[#2CA9BD] to-[#65DBE4] flex items-center justify-center text-white">
                        <i class="fas fa-info-circle text-sm"></i>
                    </div>
                    <span>معلومات الحساب</span>
                </h2>
                <div class="space-y-4 text-sm">
                    <div class="flex items-center justify-between gap-4 p-3 bg-gradient-to-r from-[#2CA9BD]/5 to-[#65DBE4]/5 rounded-xl">
                        <div class="flex items-center gap-3 text-[#1F3A56]">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-[#2CA9BD] to-[#65DBE4] text-white shadow-md"><i class="fas fa-id-badge"></i></span>
                            <span class="font-bold">رقم العضوية</span>
                        </div>
                        <span class="text-[#1C2C39] font-black text-base">#<?php echo e(str_pad($user->id, 5, '0', STR_PAD_LEFT)); ?></span>
                    </div>
                    <div class="flex items-center justify-between gap-4 p-3 bg-gradient-to-r from-purple-500/5 to-indigo-500/5 rounded-xl">
                        <div class="flex items-center gap-3 text-[#1F3A56]">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-[#1F3A56] to-[#2CA9BD] text-white shadow-md"><i class="fas fa-chalkboard-teacher"></i></span>
                            <span class="font-bold">نوع الحساب</span>
                        </div>
                        <span class="px-3 py-1.5 rounded-xl text-xs font-bold <?php echo e($roleMeta['chip']); ?>"><?php echo e($roleMeta['label']); ?></span>
                    </div>
                    <div class="flex items-center justify-between gap-4 p-3 bg-gradient-to-r from-green-500/5 to-emerald-500/5 rounded-xl">
                        <div class="flex items-center gap-3 text-[#1F3A56]">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 text-white shadow-md"><i class="fas fa-signal"></i></span>
                            <span class="font-bold">الحالة</span>
                        </div>
                        <span class="inline-flex items-center gap-2 rounded-xl px-3 py-1.5 text-xs font-bold <?php echo e($user->is_active ? 'bg-gradient-to-r from-green-500/15 to-emerald-600/15 text-green-700 border-2 border-green-500/30' : 'bg-gradient-to-r from-red-500/15 to-rose-600/15 text-red-700 border-2 border-red-500/30'); ?>">
                            <span class="relative flex h-2 w-2">
                                <span class="absolute inline-flex h-full w-full rounded-full opacity-75 <?php echo e($user->is_active ? 'bg-green-500 animate-ping' : 'bg-red-500'); ?>"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full <?php echo e($user->is_active ? 'bg-green-500' : 'bg-red-500'); ?>"></span>
                            </span>
                            <?php echo e($user->is_active ? 'نشط' : 'غير نشط'); ?>

                        </span>
                    </div>
                </div>
            </div>

            <div class="info-card rounded-2xl p-6 shadow-lg">
                <h2 class="text-lg sm:text-xl font-black text-[#1C2C39] mb-5 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-[#FFD34E] to-amber-500 flex items-center justify-center text-white">
                        <i class="fas fa-lightbulb text-sm"></i>
                    </div>
                    <span>نصائح للمدرب</span>
                </h2>
                <ul class="space-y-4 text-sm text-[#1F3A56]">
                    <li class="flex items-start gap-3 p-3 bg-gradient-to-r from-[#2CA9BD]/5 to-[#65DBE4]/5 rounded-xl">
                        <span class="mt-1 text-[#2CA9BD]"><i class="fas fa-check-circle"></i></span>
                        <div>
                            <p class="font-bold text-[#1C2C39]">حدّث السيرة المختصرة</p>
                            <p class="mt-1 text-xs text-[#1F3A56]">أضف نبذة عنك تظهر للطلاب لتعزيز الثقة.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3 p-3 bg-gradient-to-r from-green-500/5 to-emerald-500/5 rounded-xl">
                        <span class="mt-1 text-green-600"><i class="fas fa-lock"></i></span>
                        <div>
                            <p class="font-bold text-[#1C2C39]">كلمة مرور قوية</p>
                            <p class="mt-1 text-xs text-[#1F3A56]">غيّر كلمة المرور دورياً لحماية حسابك.</p>
                        </div>
                    </li>
                </ul>
            </div>

        </div>

        <!-- نموذج التحديث -->
        <div class="lg:col-span-2 space-y-6">
            <div class="info-card rounded-2xl p-6 sm:p-8 shadow-lg">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
                    <div>
                        <h3 class="text-xl sm:text-2xl font-black text-[#1C2C39] mb-2">تحديث البيانات</h3>
                        <p class="text-sm sm:text-base text-[#1F3A56] font-medium">مراجعة وتحديث معلوماتك في أي وقت</p>
                    </div>
                    <span class="inline-flex items-center gap-2 text-xs font-bold rounded-xl bg-gradient-to-r from-[#2CA9BD]/10 to-[#65DBE4]/10 text-[#2CA9BD] border-2 border-[#2CA9BD]/20 px-4 py-2">
                        <i class="fas fa-shield-check"></i>
                        بياناتك مشفرة وآمنة
                    </span>
                </div>

                <form method="POST" action="<?php echo e(route('instructor.profile.update')); ?>" class="space-y-8" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="group">
                            <label class="block text-sm font-bold text-[#1C2C39] mb-2">الاسم الكامل</label>
                            <div class="relative">
                                <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-[#1F3A56] group-focus-within:text-[#2CA9BD] transition-colors"></i>
                                <input type="text" name="name" value="<?php echo e(old('name', $user->name)); ?>" required
                                       class="form-input w-full rounded-xl border-2 border-[#2CA9BD]/20 bg-white px-11 py-3.5 text-[#1C2C39] font-medium shadow-sm focus:border-[#2CA9BD] focus:ring-4 focus:ring-[#2CA9BD]/20">
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
                            <label class="block text-sm font-bold text-[#1C2C39] mb-2">رقم الهاتف</label>
                            <div class="relative">
                                <i class="fas fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-[#1F3A56] group-focus-within:text-[#2CA9BD] transition-colors"></i>
                                <input type="text" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>" required
                                       class="form-input w-full rounded-xl border-2 border-[#2CA9BD]/20 bg-white px-11 py-3.5 text-[#1C2C39] font-medium shadow-sm focus:border-[#2CA9BD] focus:ring-4 focus:ring-[#2CA9BD]/20">
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
                            <label class="block text-sm font-bold text-[#1C2C39] mb-2">البريد الإلكتروني (اختياري)</label>
                            <div class="relative">
                                <i class="fas fa-at absolute left-4 top-1/2 -translate-y-1/2 text-[#1F3A56] group-focus-within:text-[#2CA9BD] transition-colors"></i>
                                <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>"
                                       class="form-input w-full rounded-xl border-2 border-[#2CA9BD]/20 bg-white px-11 py-3.5 text-[#1C2C39] font-medium shadow-sm focus:border-[#2CA9BD] focus:ring-4 focus:ring-[#2CA9BD]/20">
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

                        <div class="md:col-span-2 group">
                            <label class="block text-sm font-bold text-[#1C2C39] mb-2">نبذة عنك (اختياري)</label>
                            <textarea name="bio" rows="4" placeholder="اكتب نبذة قصيرة تظهر للطلاب..."
                                      class="form-input w-full rounded-xl border-2 border-[#2CA9BD]/20 bg-white px-4 py-3.5 text-[#1C2C39] font-medium shadow-sm focus:border-[#2CA9BD] focus:ring-4 focus:ring-[#2CA9BD]/20"><?php echo e(old('bio', $user->bio)); ?></textarea>
                            <?php $__errorArgs = ['bio'];
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
                        <label class="block text-sm font-bold text-[#1C2C39] mb-3">صورة الملف الشخصي</label>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-2xl overflow-hidden border-2 border-dashed border-[#2CA9BD]/30 bg-gradient-to-br from-[#2CA9BD]/5 to-[#65DBE4]/5 flex items-center justify-center">
                                <?php if($user->profile_image): ?>
                                    <img src="<?php echo e(asset($user->profile_image)); ?>" alt="صورة الملف الشخصي" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <i class="fas fa-camera text-[#2CA9BD] text-3xl"></i>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1">
                                <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border-2 border-dashed border-[#2CA9BD]/30 bg-gradient-to-r from-[#2CA9BD]/10 to-[#65DBE4]/10 px-6 py-3 text-sm font-bold text-[#1C2C39] hover:from-[#2CA9BD]/20 hover:to-[#65DBE4]/20 transition-all">
                                    <i class="fas fa-upload text-[#2CA9BD]"></i>
                                    <span>اختر صورة (PNG أو JPG - حد أقصى 2 ميجابايت)</span>
                                    <input type="file" name="profile_image" accept="image/*" class="hidden">
                                </label>
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

                    <div class="space-y-6 rounded-2xl border-2 border-dashed border-[#2CA9BD]/20 bg-gradient-to-r from-[#2CA9BD]/5 to-[#65DBE4]/5 p-6">
                        <h4 class="text-base sm:text-lg font-black text-[#1C2C39] mb-1">تغيير كلمة المرور</h4>
                        <p class="text-xs text-[#1F3A56] font-medium mb-4">اترك الحقول فارغة إذا لم ترغب في التغيير</p>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div class="group">
                                <label class="block text-xs font-bold uppercase tracking-wide text-[#1F3A56] mb-2">كلمة المرور الحالية</label>
                                <input type="password" name="current_password"
                                       class="form-input w-full rounded-xl border-2 border-[#2CA9BD]/20 bg-white px-4 py-3 text-sm text-[#1C2C39] font-medium focus:border-[#2CA9BD] focus:ring-4 focus:ring-[#2CA9BD]/20">
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
                                <label class="block text-xs font-bold uppercase tracking-wide text-[#1F3A56] mb-2">كلمة المرور الجديدة</label>
                                <input type="password" name="password"
                                       class="form-input w-full rounded-xl border-2 border-green-500/20 bg-white px-4 py-3 text-sm text-[#1C2C39] font-medium focus:border-green-500 focus:ring-4 focus:ring-green-500/20">
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
                                <label class="block text-xs font-bold uppercase tracking-wide text-[#1F3A56] mb-2">تأكيد كلمة المرور</label>
                                <input type="password" name="password_confirmation"
                                       class="form-input w-full rounded-xl border-2 border-green-500/20 bg-white px-4 py-3 text-sm text-[#1C2C39] font-medium focus:border-green-500 focus:ring-4 focus:ring-green-500/20">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between pt-4 border-t-2 border-[#2CA9BD]/10">
                        <a href="<?php echo e(route('dashboard')); ?>" class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-[#2CA9BD]/20 bg-white px-6 py-3 text-sm font-bold text-[#1C2C39] hover:border-[#2CA9BD]/40 hover:bg-[#2CA9BD]/5 transition-all">
                            <i class="fas fa-arrow-right"></i>
                            رجوع إلى اللوحة
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#2CA9BD] via-[#65DBE4] to-[#2CA9BD] px-6 py-3 text-sm font-bold text-white shadow-lg shadow-[#2CA9BD]/30 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-[#2CA9BD]/30 transition-all transform hover:scale-105">
                            <i class="fas fa-save"></i>
                            حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/profile/index.blade.php ENDPATH**/ ?>