<?php $__env->startSection('title', __('instructor.profile') . ' - Mindlytics'); ?>
<?php $__env->startSection('header', __('instructor.profile')); ?>

<?php $__env->startSection('content'); ?>
<?php
    $user = auth()->user();
    $memberSince = $user->created_at ? $user->created_at->copy()->locale('ar')->translatedFormat('d F Y') : '—';
    $myCoursesCount = \App\Models\AdvancedCourse::where('instructor_id', $user->id)->count();
    $totalStudents = \App\Models\StudentCourseEnrollment::whereHas('course', function($q) use ($user) {
        $q->where('instructor_id', $user->id);
    })->where('status', 'active')->distinct('user_id')->count();
    $lastLogin = $user->last_login_at ? $user->last_login_at->copy()->locale('ar')->diffForHumans() : '—';
?>

<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-600"></i>
            <span class="font-semibold text-emerald-800"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <!-- الهيدر -->
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-1"><?php echo e(__('instructor.profile')); ?></h1>
        <p class="text-sm text-slate-500"><?php echo e(__('instructor.manage_profile_data')); ?></p>
    </div>

    <!-- بطاقة الملف + إحصائيات -->
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center gap-6 lg:gap-8">
            <div class="flex flex-col sm:flex-row sm:items-center gap-5">
                <div class="flex items-center justify-center h-24 w-24 sm:h-28 sm:w-28 rounded-2xl bg-sky-100 border border-slate-200 overflow-hidden shrink-0 mx-auto sm:mx-0">
                    <?php if($user->profile_image): ?>
                        <img src="<?php echo e(asset('storage/' . $user->profile_image)); ?>" alt="<?php echo e(__('instructor.profile_image')); ?>" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                        <span class="text-4xl font-bold text-sky-600 hidden"><?php echo e(mb_substr($user->name, 0, 1)); ?></span>
                    <?php else: ?>
                        <span class="text-4xl font-bold text-sky-600"><?php echo e(mb_substr($user->name, 0, 1)); ?></span>
                    <?php endif; ?>
                </div>
                <div class="flex-1 text-center sm:text-right">
                    <span class="inline-flex items-center gap-2 rounded-lg bg-sky-100 text-sky-700 px-3 py-1.5 text-xs font-semibold mb-2">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <?php echo e(__('instructor.instructor_role')); ?>

                    </span>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-1"><?php echo e($user->name); ?></h2>
                    <?php if($user->phone): ?>
                        <p class="text-sm text-slate-600 flex items-center justify-center sm:justify-end gap-2 mt-1">
                            <i class="fas fa-phone text-slate-400"></i>
                            <?php echo e($user->phone); ?>

                        </p>
                    <?php endif; ?>
                    <?php if($user->email): ?>
                        <p class="text-sm text-slate-600 flex items-center justify-center sm:justify-end gap-2 mt-0.5">
                            <i class="fas fa-envelope text-slate-400"></i>
                            <?php echo e($user->email); ?>

                        </p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 flex-1">
                <div class="rounded-xl p-4 bg-slate-50 border border-slate-200 text-center">
                    <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center text-sky-600 mx-auto mb-2">
                        <i class="fas fa-calendar-week text-sm"></i>
                    </div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-0.5"><?php echo e(__('instructor.join_date')); ?></p>
                    <p class="text-sm font-bold text-slate-800"><?php echo e($memberSince); ?></p>
                </div>
                <div class="rounded-xl p-4 bg-slate-50 border border-slate-200 text-center">
                    <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center text-violet-600 mx-auto mb-2">
                        <i class="fas fa-book-open text-sm"></i>
                    </div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-0.5"><?php echo e(__('instructor.my_courses')); ?></p>
                    <p class="text-sm font-bold text-slate-800"><?php echo e($myCoursesCount); ?></p>
                </div>
                <div class="rounded-xl p-4 bg-slate-50 border border-slate-200 text-center">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 mx-auto mb-2">
                        <i class="fas fa-user-graduate text-sm"></i>
                    </div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-0.5"><?php echo e(__('instructor.students')); ?></p>
                    <p class="text-sm font-bold text-slate-800"><?php echo e($totalStudents); ?></p>
                </div>
                <div class="rounded-xl p-4 bg-slate-50 border border-slate-200 text-center">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 mx-auto mb-2">
                        <i class="fas fa-clock-rotate-left text-sm"></i>
                    </div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-0.5"><?php echo e(__('instructor.last_login')); ?></p>
                    <p class="text-sm font-bold text-slate-800"><?php echo e($lastLogin); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:gap-8 lg:grid-cols-3">
        <!-- البطاقات الجانبية -->
        <div class="space-y-6">
            <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-sky-50 flex items-center justify-center text-sky-600">
                        <i class="fas fa-info-circle text-sm"></i>
                    </span>
                    <?php echo e(__('instructor.account_info')); ?>

                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-slate-600 font-medium"><?php echo e(__('instructor.membership_number')); ?></span>
                        <span class="font-bold text-slate-800">#<?php echo e(str_pad($user->id, 5, '0', STR_PAD_LEFT)); ?></span>
                    </div>
                    <div class="flex items-center justify-between gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-slate-600 font-medium"><?php echo e(__('instructor.account_type')); ?></span>
                        <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-sky-100 text-sky-700"><?php echo e(__('instructor.instructor_role')); ?></span>
                    </div>
                    <div class="flex items-center justify-between gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-slate-600 font-medium"><?php echo e(__('common.status')); ?></span>
                        <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg text-xs font-semibold <?php echo e($user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'); ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?php echo e($user->is_active ? 'bg-emerald-500' : 'bg-rose-500'); ?>"></span>
                            <?php echo e($user->is_active ? __('instructor.active_status') : __('instructor.not_active')); ?>

                        </span>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                        <i class="fas fa-lightbulb text-sm"></i>
                    </span>
                    <?php echo e(__('instructor.tips_for_instructor')); ?>

                </h3>
                <ul class="space-y-3 text-sm text-slate-600">
                    <li class="flex items-start gap-3 p-3 bg-sky-50/50 rounded-xl border border-slate-100">
                        <span class="text-sky-500 mt-0.5"><i class="fas fa-check-circle"></i></span>
                        <div>
                            <p class="font-semibold text-slate-800"><?php echo e(__('instructor.update_bio')); ?></p>
                            <p class="mt-0.5 text-xs text-slate-500"><?php echo e(__('instructor.add_bio_for_students')); ?></p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-emerald-500 mt-0.5"><i class="fas fa-lock"></i></span>
                        <div>
                            <p class="font-semibold text-slate-800"><?php echo e(__('instructor.strong_password')); ?></p>
                            <p class="mt-0.5 text-xs text-slate-500"><?php echo e(__('instructor.change_password_regularly')); ?></p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- نموذج التحديث -->
        <div class="lg:col-span-2">
            <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
                <h3 class="text-xl font-bold text-slate-800 mb-1"><?php echo e(__('instructor.update_data')); ?></h3>
                <p class="text-sm text-slate-500 mb-6"><?php echo e(__('instructor.update_data_subtitle')); ?></p>

                <form method="POST" action="<?php echo e(route('instructor.profile.update')); ?>" class="space-y-6" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2"><?php echo e(__('instructor.full_name')); ?></label>
                            <input type="text" name="name" value="<?php echo e(old('name', $user->name)); ?>" required
                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2"><?php echo e(__('instructor.phone')); ?></label>
                            <input type="text" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>" required
                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2"><?php echo e(__('instructor.email_optional')); ?></label>
                            <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>"
                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2"><?php echo e(__('instructor.bio_optional')); ?></label>
                            <textarea name="bio" rows="4" placeholder="<?php echo e(__('instructor.bio_placeholder_short')); ?>"
                                      class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors"><?php echo e(old('bio', $user->bio)); ?></textarea>
                            <?php $__errorArgs = ['bio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2"><?php echo e(__('instructor.profile_image')); ?></label>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-xl overflow-hidden border border-slate-200 bg-slate-50 flex items-center justify-center shrink-0">
                                <?php if($user->profile_image): ?>
                                    <img src="<?php echo e(asset('storage/' . $user->profile_image)); ?>" alt="<?php echo e(__('instructor.profile_image')); ?>" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                                    <i class="fas fa-user text-slate-400 text-2xl hidden"></i>
                                <?php else: ?>
                                    <i class="fas fa-user text-slate-400 text-2xl"></i>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1">
                                <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 transition-colors">
                                    <i class="fas fa-upload text-sky-500"></i>
                                    <span><?php echo e(__('instructor.choose_image_label')); ?></span>
                                    <input type="file" name="profile_image" accept="image/*" class="hidden">
                                </label>
                                <?php $__errorArgs = ['profile_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-5 space-y-4">
                        <h4 class="text-base font-bold text-slate-800"><?php echo e(__('instructor.change_password')); ?></h4>
                        <p class="text-xs text-slate-500"><?php echo e(__('instructor.leave_empty_if_no_change')); ?></p>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1"><?php echo e(__('instructor.current_password')); ?></label>
                                <input type="password" name="current_password"
                                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1"><?php echo e(__('instructor.new_password')); ?></label>
                                <input type="password" name="password"
                                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1"><?php echo e(__('instructor.confirm_password')); ?></label>
                                <input type="password" name="password_confirmation"
                                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between pt-4 border-t border-slate-200">
                        <a href="<?php echo e(route('dashboard')); ?>" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                            <i class="fas fa-arrow-right"></i>
                            <?php echo e(__('instructor.back_to_dashboard')); ?>

                        </a>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-500 hover:bg-sky-600 text-white px-6 py-2.5 text-sm font-semibold transition-colors">
                            <i class="fas fa-save"></i>
                            <?php echo e(__('instructor.save_changes')); ?>

                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\instructor\profile\index.blade.php ENDPATH**/ ?>