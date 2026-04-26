<?php
    $gs = $groupSessions ?? collect();
    $selName = $name ?? 'offline_group_session_id';
    $selRequired = $required ?? false;
    $selValue = $value ?? null;
    $variant = $variant ?? 'default';
    $isModal = $variant === 'modal';
    $statusShort = [
        'scheduled' => 'مجدولة',
        'completed' => 'مكتملة',
        'cancelled' => 'ملغاة',
    ];
?>
<?php if($gs->isNotEmpty()): ?>
    <div class="<?php echo e($isModal ? 'rounded-xl border border-slate-200 bg-slate-50/60 p-4 sm:p-5 shadow-sm' : ''); ?>">
        <label class="block font-semibold text-slate-800 mb-1.5 <?php echo e($isModal ? 'text-xs sm:text-sm' : 'text-sm'); ?>">
            الجلسة التي ستُوصَّف هذه المحاضرة لها
            <?php if($selRequired): ?><span class="text-red-500">*</span><?php endif; ?>
        </label>
        <p class="text-slate-600 <?php echo e($isModal ? 'text-xs sm:text-sm mb-3 leading-relaxed' : 'text-xs mb-2'); ?>">
            <?php if($isModal): ?>
                اختر جلسة من التقويم (<?php echo e($gs->count()); ?> متاحة). المحتوى يصف ما ستقدّمه في تلك الجلسة.
            <?php else: ?>
                نفس الجلسات التي تظهر في تقويم المدرب بعد إنشائها من الإدارة.
            <?php endif; ?>
        </p>
        <select name="<?php echo e($selName); ?>"
                class="w-full rounded-xl border border-slate-200 bg-white shadow-sm focus:border-slate-400 focus:ring-2 focus:ring-slate-200 <?php echo e($isModal ? 'text-sm px-3 py-2.5 min-h-[2.75rem]' : 'text-sm px-4 py-2.5'); ?>"
                <?php if($selRequired): ?> required <?php endif; ?>>
            <option value="">— اختر جلسة من القائمة —</option>
            <?php $__currentLoopData = $gs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $t = $s->title ?: 'جلسة';
                    $st = is_string($s->start_time) ? substr($s->start_time, 0, 5) : $s->start_time;
                    $et = is_string($s->end_time) ? substr($s->end_time, 0, 5) : $s->end_time;
                    $grp = $s->group->name ?? 'مجموعة';
                    $stLabel = $statusShort[$s->status] ?? $s->status;
                    $dur = (int) ($s->duration_minutes ?? 0);
                    $label = $s->session_date->format('Y/m/d').' · '.$st.'–'.$et.' · '.$dur.'د · '.$grp.' · '.$t.' ('.$stLabel.')';
                ?>
                <option value="<?php echo e($s->id); ?>" <?php if((string) old($selName, $selValue) === (string) $s->id): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php if (! ($isModal)): ?>
            <p class="text-xs text-slate-500 mt-1">نفس الجلسات التي تظهر في تقويم المدرب بعد إنشائها من الإدارة.</p>
        <?php endif; ?>
        <?php $__errorArgs = [$selName];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 <?php echo e($isModal ? 'text-sm' : 'text-sm'); ?> mt-2"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
<?php else: ?>
    <div class="rounded-xl border border-amber-200 bg-amber-50/80 px-3 py-2 <?php echo e($isModal ? 'text-sm p-4' : 'text-xs'); ?> text-amber-900">
        لا توجد جلسات مسجّلة للمجموعات بعد. عند إنشاء الجلسات من الإدارة ستظهر هنا وللمدرب في التقويم، ويمكنك حينها ربط كل محاضرة بجلسة.
    </div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/offline-courses/lectures/partials/session-select.blade.php ENDPATH**/ ?>