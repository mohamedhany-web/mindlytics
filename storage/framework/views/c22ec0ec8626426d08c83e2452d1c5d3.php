

<?php $__env->startSection('title', 'التسويق الشخصي - الملف التعريفي'); ?>
<?php $__env->startSection('header', 'التسويق الشخصي'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-600"></i>
            <span class="font-semibold text-emerald-800"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-rose-600"></i>
            <span class="font-semibold text-rose-800"><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <h1 class="text-2xl font-bold text-slate-800 mb-1">الملف التعريفي (Personal Branding)</h1>
        <p class="text-sm text-slate-500">أضف بياناتك وصورتك وخبراتك. بعد المراجعة من الإدارة سيظهر ملفك في صفحة «المدربين» على الموقع وعند عرض كل كورس تدرّسه.</p>
        <div class="mt-3">
            <span class="rounded-full px-3 py-1 text-xs font-semibold
                <?php if($profile->status == 'approved'): ?> bg-emerald-100 text-emerald-700
                <?php elseif($profile->status == 'pending_review'): ?> bg-amber-100 text-amber-700
                <?php elseif($profile->status == 'rejected'): ?> bg-rose-100 text-rose-700
                <?php else: ?> bg-slate-100 text-slate-600
                <?php endif; ?>">
                الحالة: <?php echo e(\App\Models\InstructorProfile::statusLabel($profile->status)); ?>

            </span>
            <?php if($profile->rejection_reason): ?>
                <p class="text-sm text-rose-600 mt-2">سبب الرفض: <?php echo e($profile->rejection_reason); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <form method="POST" action="<?php echo e(route('instructor.personal-branding.update')); ?>" enctype="multipart/form-data" class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm space-y-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">الصورة الشخصية</label>
            <?php if($profile->photo_path): ?>
                <div class="w-24 h-24 rounded-xl border border-slate-200 overflow-hidden bg-slate-100 relative mb-2">
                    <img src="<?php echo e($profile->photo_url); ?>" alt="صورة الملف التعريفي" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                    <div class="hidden absolute inset-0 w-full h-full bg-slate-200 flex items-center justify-center text-slate-500"><i class="fas fa-user text-3xl"></i></div>
                </div>
            <?php endif; ?>
            <input type="file" name="photo" accept="image/*" class="block w-full text-sm text-slate-500 file:mr-4 file:rounded-xl file:border-0 file:bg-sky-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-sky-700 hover:file:bg-sky-100">
            <?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">العنوان التعريفي</label>
            <input type="text" name="headline" value="<?php echo e(old('headline', $profile->headline)); ?>" placeholder="مثال: مدرب برمجة وتطوير ويب" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
            <?php $__errorArgs = ['headline'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">نبذة عنك</label>
            <textarea name="bio" rows="5" placeholder="اكتب نبذة تعريفية تظهر في صفحتك للزوار..." class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm"><?php echo e(old('bio', $profile->bio)); ?></textarea>
            <?php $__errorArgs = ['bio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">الخبرات في المجال</label>
            <textarea name="experience" rows="10" placeholder="اذكر خبراتك العملية والتعليمية... (حتى 50 ألف حرف)" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm"><?php echo e(old('experience', $profile->experience)); ?></textarea>
            <?php $__errorArgs = ['experience'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">المهارات</label>
            <p class="text-xs text-slate-500 mb-2">مهارة واحدة في كل سطر، أو افصل بينها بفاصلة (،) أو شرطة سطر جديد. ستظهر في صفحتك كشارات مرتبة.</p>
            <textarea name="skills" rows="5" placeholder="مثال:&#10;إدارة المشاريع&#10;التدريب الاحترافي&#10;تحليل البيانات، العرض التقديمي" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm"><?php echo e(old('skills', $profile->skills)); ?></textarea>
            <?php $__errorArgs = ['skills'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <?php
                $skillsPreview = $profile->skills_list;
                if (old('skills') !== null) {
                    $split = preg_split('/[\r\n,،]+/u', old('skills'), -1, PREG_SPLIT_NO_EMPTY);
                    $skillsPreview = array_values(array_filter(array_map('trim', $split)));
                }
            ?>
            <?php if(count($skillsPreview) > 0): ?>
            <p class="text-xs text-slate-500 mt-2">معاينة (<?php echo e(count($skillsPreview)); ?> مهارة):</p>
            <div class="flex flex-wrap gap-2 mt-1">
                <?php $__currentLoopData = $skillsPreview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="inline-flex items-center rounded-lg bg-sky-50 text-sky-700 px-2.5 py-1 text-xs font-medium"><?php echo e($skill); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">رابط LinkedIn</label>
            <input type="url" name="linkedin" value="<?php echo e(old('linkedin', $profile->social_links['linkedin'] ?? '')); ?>" placeholder="https://www.linkedin.com/in/اسم-المستخدم" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" dir="ltr">
            <p class="text-xs text-slate-500 mt-1">اختياري — سيظهر في صفحتك للزوار للتواصل معك على لينكد إن.</p>
            <?php $__errorArgs = ['linkedin'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="rounded-xl bg-sky-600 text-white px-5 py-2.5 text-sm font-semibold hover:bg-sky-700">حفظ التعديلات</button>
        </div>
    </form>

    <?php if(in_array($profile->status, ['draft', 'rejected'])): ?>
    <form method="POST" action="<?php echo e(route('instructor.personal-branding.submit')); ?>" class="inline mt-4">
        <?php echo csrf_field(); ?>
        <button type="submit" class="rounded-xl bg-amber-500 text-white px-5 py-2.5 text-sm font-semibold hover:bg-amber-600">إرسال للمراجعة والنشر</button>
    </form>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/personal-branding/edit.blade.php ENDPATH**/ ?>