<?php $__env->startSection('title', __('student.profile_title')); ?>

<?php
    $roleLabels = [
        'student' => __('student.student_role'),
        'teacher' => __('student.teacher_role'),
        'admin' => __('student.admin_role_label'),
        'super_admin' => __('student.super_admin_role'),
    ];
    $roleLabel = $roleLabels[$user->role] ?? __('student.user_role');
    $locale = app()->getLocale();
    $memberSince = $user->created_at?->copy()->locale($locale)->translatedFormat('d F Y') ?? '—';
    $coursesCount = method_exists($user, 'courseEnrollments') ? $user->courseEnrollments()->count() : 0;
    $notificationsCount = method_exists($user, 'customNotifications')
        ? $user->customNotifications()->count()
        : (method_exists($user, 'notifications') ? $user->notifications()->count() : 0);
    $lastLogin = $user->last_login_at?->copy()->locale($locale)->diffForHumans() ?? null;
?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('student.offline-courses.partials.los-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .pf-layout {
        display: grid;
        grid-template-columns: 280px minmax(0, 1fr);
        gap: 20px;
        align-items: start;
    }
    @media (max-width: 999px) {
        .pf-layout { grid-template-columns: 1fr; }
    }
    .pf-hero {
        display: flex; flex-wrap: wrap; align-items: center; gap: 16px;
    }
    .pf-av {
        width: 88px; height: 88px; border-radius: 18px; overflow: hidden; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: linear-gradient(145deg, #49A4A2, #2f7f7d); color: #fff;
        font-size: 2rem; font-weight: 800; border: 1px solid var(--ml-line);
    }
    .pf-av img { width: 100%; height: 100%; object-fit: cover; }
    .pf-hero h2 { margin: 0 0 6px; font-size: 1.25rem; font-weight: 700; }
    .pf-hero .meta { margin: 0; font-size: 13px; color: var(--ml-muted); }
    .pf-contact {
        display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px;
    }
    .pf-contact span {
        display: inline-flex; align-items: center; gap: 6px;
        min-height: 30px; padding: 0 10px; border-radius: 8px;
        background: var(--ml-well); border: 1px solid var(--ml-line);
        font-size: 12px; font-weight: 600; color: var(--ml-ink);
    }
    .pf-contact i { color: var(--ml-teal-deep); }
    .pf-form label {
        display: block; margin-bottom: 6px; font-size: 12px; font-weight: 700; color: var(--ml-ink);
    }
    .pf-form input[type="text"],
    .pf-form input[type="email"],
    .pf-form input[type="password"] {
        width: 100%; min-height: 42px; padding: 0 12px;
        border-radius: 12px; border: 1px solid var(--ml-line);
        background: var(--ml-surface); color: var(--ml-ink);
        font-family: inherit; font-size: 13px;
    }
    .pf-form input:focus {
        outline: none; border-color: rgba(73,164,162,0.55);
        box-shadow: 0 0 0 3px rgba(73,164,162,0.12);
    }
    .pf-form .err { margin: 6px 0 0; font-size: 12px; color: #b91c1c; font-weight: 600; }
    .pf-form .grid2 {
        display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;
    }
    .pf-form .grid3 {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;
    }
    @media (max-width: 720px) {
        .pf-form .grid2, .pf-form .grid3 { grid-template-columns: 1fr; }
    }
    .pf-photo {
        display: flex; flex-wrap: wrap; align-items: center; gap: 14px; margin-bottom: 16px;
    }
    .pf-photo .preview {
        width: 88px; height: 88px; border-radius: 16px; overflow: hidden;
        border: 1px dashed rgba(73,164,162,0.4); background: var(--ml-well);
        display: flex; align-items: center; justify-content: center; color: var(--ml-teal-deep);
    }
    .pf-photo .preview img { width: 100%; height: 100%; object-fit: cover; }
    .pf-photo label.pick {
        display: inline-flex; align-items: center; gap: 8px; cursor: pointer;
        min-height: 40px; padding: 0 14px; border-radius: 12px;
        border: 1px solid var(--ml-line); background: var(--ml-well);
        font-size: 12px; font-weight: 700; color: var(--ml-ink);
    }
    .pf-photo input[type="file"] { display: none; }
    .pf-pass {
        padding: 14px; border-radius: 12px; border: 1px dashed rgba(73,164,162,0.35);
        background: rgba(73,164,162,0.05); margin-bottom: 16px;
    }
    .pf-side-row {
        display: flex; justify-content: space-between; gap: 10px; align-items: center;
        padding: 10px 0; border-bottom: 1px solid var(--ml-line); font-size: 12px;
    }
    .pf-side-row:last-child { border-bottom: 0; }
    .pf-side-row .k { color: var(--ml-muted); font-weight: 600; }
    .pf-side-row .v { font-weight: 700; color: var(--ml-ink); text-align: end; }
    .pf-tip {
        display: flex; gap: 10px; padding: 10px 0; border-bottom: 1px solid var(--ml-line);
        font-size: 12px; line-height: 1.55;
    }
    .pf-tip:last-child { border-bottom: 0; }
    .pf-tip i { color: var(--ml-teal-deep); margin-top: 2px; }
    .pf-tip strong { display: block; margin-bottom: 2px; font-size: 13px; }
    .pf-tip p { margin: 0; color: var(--ml-muted); }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="oc">
    <?php if(session('success')): ?>
        <div class="oc-panel" style="border-color:rgba(16,185,129,0.35);background:rgba(16,185,129,0.08);margin-bottom:16px;color:#047857;font-size:13px;font-weight:600">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <header class="oc-chrome">
        <div>
            <nav class="oc-crumb" aria-label="<?php echo e(__('student.profile_title')); ?>">
                <a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('student.learning_center')); ?></a>
                <span aria-hidden="true">/</span>
                <span style="color:var(--ml-ink);font-weight:700"><?php echo e(__('student.profile_title')); ?></span>
            </nav>
            <h1><?php echo e(__('student.profile_title')); ?></h1>
            <p class="sub"><?php echo e(__('student.profile_subtitle')); ?></p>
        </div>
        <div class="oc-signals">
            <span class="oc-signal oc-signal-live"><?php echo e($roleLabel); ?></span>
            <span class="oc-signal <?php echo e($user->is_active ? 'oc-signal-hot' : 'oc-signal-warn'); ?>">
                <?php echo e($user->is_active ? __('student.profile_active') : __('student.profile_inactive')); ?>

            </span>
        </div>
    </header>

    <section class="oc-stage" style="margin-bottom:20px">
        <div class="pf-hero">
            <div class="pf-av" aria-hidden="true">
                <?php if($user->profile_image): ?>
                    <img src="<?php echo e($user->profile_image_url); ?>" alt="<?php echo e(__('student.profile_image_alt')); ?>">
                <?php else: ?>
                    <?php echo e(mb_substr($user->name, 0, 1)); ?>

                <?php endif; ?>
            </div>
            <div class="min-w-0">
                <span class="oc-badge oc-badge-live"><?php echo e($roleLabel); ?></span>
                <h2><?php echo e($user->name); ?></h2>
                <p class="meta"><?php echo e(__('student.profile_subtitle')); ?></p>
                <div class="pf-contact">
                    <span><i class="fas fa-phone"></i> <?php echo e($user->phone ?? '—'); ?></span>
                    <?php if($user->email): ?>
                        <span><i class="fas fa-envelope"></i> <?php echo e($user->email); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <div class="oc-pulse" aria-label="<?php echo e(__('student.profile_title')); ?>">
        <div>
            <span class="lbl"><?php echo e(__('student.join_date_label')); ?></span>
            <span class="val" style="font-size:1rem"><?php echo e($memberSince); ?></span>
        </div>
        <div>
            <span class="lbl"><?php echo e(__('student.active_courses_count')); ?></span>
            <span class="val teal"><?php echo e($coursesCount); ?></span>
        </div>
        <div>
            <span class="lbl"><?php echo e(__('student.notifications')); ?></span>
            <span class="val"><?php echo e($notificationsCount); ?></span>
        </div>
        <div>
            <span class="lbl"><?php echo e(__('student.last_login_label')); ?></span>
            <span class="val" style="font-size:1rem"><?php echo e($lastLogin ?: '—'); ?></span>
        </div>
    </div>

    <div class="pf-layout">
        <aside style="display:flex;flex-direction:column;gap:12px">
            <div class="oc-panel">
                <p class="oc-label"><?php echo e(__('student.profile_contact_info')); ?></p>
                <div class="pf-side-row">
                    <span class="k"><?php echo e(__('student.profile_membership_no')); ?></span>
                    <span class="v">#<?php echo e(str_pad((string) $user->id, 5, '0', STR_PAD_LEFT)); ?></span>
                </div>
                <div class="pf-side-row">
                    <span class="k"><?php echo e(__('student.profile_account_type')); ?></span>
                    <span class="v"><?php echo e($roleLabel); ?></span>
                </div>
                <div class="pf-side-row">
                    <span class="k"><?php echo e(__('student.profile_status')); ?></span>
                    <span class="v">
                        <span class="oc-badge <?php echo e($user->is_active ? 'oc-badge-ok' : 'oc-badge-bad'); ?>">
                            <?php echo e($user->is_active ? __('student.profile_active') : __('student.profile_inactive')); ?>

                        </span>
                    </span>
                </div>
                <p style="margin:12px 0 0;font-size:12px;color:var(--ml-muted);line-height:1.55">
                    <i class="fas fa-shield-halved" style="color:var(--ml-teal-deep);margin-inline-end:4px"></i>
                    <?php echo e(__('student.profile_security_note')); ?>

                </p>
            </div>

            <div class="oc-panel">
                <p class="oc-label"><?php echo e(__('student.profile_tips')); ?></p>
                <div class="pf-tip">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong><?php echo e(__('student.profile_tip_contact_title')); ?></strong>
                        <p><?php echo e(__('student.profile_tip_contact_desc')); ?></p>
                    </div>
                </div>
                <div class="pf-tip">
                    <i class="fas fa-lock"></i>
                    <div>
                        <strong><?php echo e(__('student.profile_tip_password_title')); ?></strong>
                        <p><?php echo e(__('student.profile_tip_password_desc')); ?></p>
                    </div>
                </div>
                <div class="pf-tip">
                    <i class="fas fa-bell"></i>
                    <div>
                        <strong><?php echo e(__('student.profile_tip_notif_title')); ?></strong>
                        <p><?php echo e(__('student.profile_tip_notif_desc')); ?></p>
                    </div>
                </div>
            </div>
        </aside>

        <div style="display:flex;flex-direction:column;gap:12px">
            <section class="oc-panel">
                <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:10px;margin-bottom:14px">
                    <div>
                        <p class="oc-label" style="margin:0"><?php echo e(__('student.profile_update_title')); ?></p>
                        <p style="margin:4px 0 0;font-size:12px;color:var(--ml-muted)"><?php echo e(__('student.profile_update_subtitle')); ?></p>
                    </div>
                    <span class="oc-badge oc-badge-live"><?php echo e(__('student.profile_data_safe')); ?></span>
                </div>

                <form method="POST" action="<?php echo e(route('profile.update')); ?>" enctype="multipart/form-data" class="pf-form">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="grid2">
                        <div>
                            <label for="pf-name"><?php echo e(__('student.profile_full_name')); ?></label>
                            <input id="pf-name" type="text" name="name" value="<?php echo e(old('name', $user->name)); ?>" required>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label for="pf-phone"><?php echo e(__('student.profile_phone')); ?></label>
                            <input id="pf-phone" type="text" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>" required>
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div style="margin-bottom:14px">
                        <label for="pf-email"><?php echo e(__('student.profile_email')); ?></label>
                        <input id="pf-email" type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <p class="oc-label"><?php echo e(__('student.profile_photo')); ?></p>
                    <div class="pf-photo">
                        <div class="preview">
                            <?php if($user->profile_image): ?>
                                <img src="<?php echo e($user->profile_image_url); ?>" alt="<?php echo e(__('student.profile_image_alt')); ?>">
                            <?php else: ?>
                                <i class="fas fa-camera"></i>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="pick">
                                <i class="fas fa-upload"></i>
                                <span><?php echo e(__('student.profile_photo_choose')); ?></span>
                                <input type="file" name="profile_image" accept="image/*">
                            </label>
                            <p style="margin:8px 0 0;font-size:11px;color:var(--ml-muted)"><?php echo e(__('student.profile_photo_hint')); ?></p>
                            <?php $__errorArgs = ['profile_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="pf-pass">
                        <div style="display:flex;flex-wrap:wrap;justify-content:space-between;gap:8px;margin-bottom:12px">
                            <div>
                                <strong style="font-size:13px"><?php echo e(__('student.profile_password_section')); ?></strong>
                                <p style="margin:4px 0 0;font-size:11px;color:var(--ml-muted)"><?php echo e(__('student.profile_password_hint')); ?></p>
                            </div>
                            <span class="oc-badge oc-badge-live"><?php echo e(__('student.profile_password_tip')); ?></span>
                        </div>
                        <div class="grid3">
                            <div>
                                <label for="pf-cur"><?php echo e(__('student.profile_current_password')); ?></label>
                                <input id="pf-cur" type="password" name="current_password" autocomplete="current-password">
                                <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label for="pf-new"><?php echo e(__('student.profile_new_password')); ?></label>
                                <input id="pf-new" type="password" name="password" autocomplete="new-password">
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label for="pf-conf"><?php echo e(__('student.profile_confirm_password')); ?></label>
                                <input id="pf-conf" type="password" name="password_confirmation" autocomplete="new-password">
                            </div>
                        </div>
                    </div>

                    <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;padding-top:12px;border-top:1px solid var(--ml-line)">
                        <p style="margin:0;font-size:11px;color:var(--ml-muted)"><?php echo e(__('student.profile_password_notify')); ?></p>
                        <div class="oc-nav" style="margin:0">
                            <a href="<?php echo e(route('dashboard')); ?>" class="oc-btn oc-btn-quiet"><?php echo e(__('student.profile_back_dashboard')); ?></a>
                            <button type="submit" class="oc-btn">
                                <i class="fas fa-save text-xs"></i> <?php echo e(__('student.profile_save')); ?>

                            </button>
                        </div>
                    </div>
                </form>
            </section>

            <section class="oc-panel">
                <p class="oc-label"><?php echo e(__('student.profile_recent_activity')); ?></p>
                <div class="pf-side-row">
                    <div>
                        <div class="v" style="text-align:start"><?php echo e(__('student.profile_last_system_activity')); ?></div>
                        <div class="k" style="margin-top:2px"><?php echo e(__('student.profile_last_system_desc')); ?></div>
                    </div>
                    <span class="k"><?php echo e($lastLogin ?: __('student.profile_just_now')); ?></span>
                </div>
                <div class="pf-side-row">
                    <div>
                        <div class="v" style="text-align:start"><?php echo e(__('student.profile_account_security')); ?></div>
                        <div class="k" style="margin-top:2px"><?php echo e(__('student.profile_account_security_desc')); ?></div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student-dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/student/profile/index.blade.php ENDPATH**/ ?>