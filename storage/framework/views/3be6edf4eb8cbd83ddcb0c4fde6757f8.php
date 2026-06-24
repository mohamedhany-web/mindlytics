

<?php $__env->startSection('title', 'تعديل مورد - كورس ' . (($channel ?? 'offline') === 'online' ? 'أونلاين' : 'أوفلاين')); ?>
<?php $__env->startSection('header', 'تعديل مورد'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="<?php echo e(route('instructor.offline-courses.index', ['channel' => ($channel ?? 'offline')])); ?>" class="hover:text-amber-600"><?php echo e(($channel ?? 'offline') === 'online' ? 'كورساتي الأونلاين' : 'كورساتي الأوفلاين'); ?></a>
            <span class="mx-2">/</span>
            <a href="<?php echo e(route('instructor.offline-courses.show', ['offline_course' => $offlineCourse, 'channel' => ($channel ?? 'offline')])); ?>" class="hover:text-amber-600 truncate max-w-[12rem] sm:max-w-none"><?php echo e($offlineCourse->title); ?></a>
            <span class="mx-2">/</span>
            <a href="<?php echo e(route('instructor.offline-courses.resources.index', ['offlineCourse' => $offlineCourse, 'channel' => ($channel ?? 'offline')])); ?>" class="hover:text-amber-600">الموارد</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">تعديل</span>
        </nav>
        <p class="text-xs font-bold text-amber-800 mb-1">مورد ضمن هذا الكورس</p>
        <h1 class="text-xl font-bold text-slate-800">تعديل: <?php echo e($resource->title); ?></h1>
        <p class="text-sm text-slate-600 mt-1">الكورس: <?php echo e($offlineCourse->title); ?></p>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <form action="<?php echo e(route('instructor.offline-courses.resources.update', ['offlineCourse' => $offlineCourse, 'resource' => $resource, 'channel' => ($channel ?? 'offline')])); ?>" method="post" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">العنوان <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="<?php echo e(old('title', $resource->title)); ?>" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-sky-500">
                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">الوصف</label>
                    <textarea name="description" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-sky-500"><?php echo e(old('description', $resource->description)); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">نوع المورد</label>
                    <select name="type" id="resourceType" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-sky-500">
                        <option value="file" <?php echo e(old('type', $resource->type) === 'file' ? 'selected' : ''); ?>>ملف مرفوع</option>
                        <option value="link" <?php echo e(old('type', $resource->type) === 'link' ? 'selected' : ''); ?>>رابط</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">ربط بمحاضرة (اختياري)</label>
                    <?php $selectedLectureIds = collect(old('lecture_ids', $resource->lectures->pluck('id')->all() ?? []))->map(fn($v)=>(int)$v)->all(); ?>
                    <select name="lecture_ids[]" multiple class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-sky-500 min-h-[3.25rem]">
                        <?php $__currentLoopData = ($lectures ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $dateLabel = optional($lec->groupSession)->session_date
                                    ? \Carbon\Carbon::parse($lec->groupSession->session_date)->format('Y-m-d')
                                    : ($lec->scheduled_at ? $lec->scheduled_at->format('Y-m-d') : null);
                                $groupLabel = optional(optional($lec->groupSession)->group)->name ?? optional($lec->group)->name;
                                $label = trim(($dateLabel ? $dateLabel.' — ' : '') . ($groupLabel ? $groupLabel.' — ' : '') . ($lec->title ?? 'محاضرة'));
                            ?>
                            <option value="<?php echo e($lec->id); ?>" <?php echo e(in_array($lec->id, $selectedLectureIds, true) ? 'selected' : ''); ?>>
                                <?php echo e($label); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <p class="text-xs text-slate-500 mt-1">لو تركته فارغًا سيظهر كـ “مورد عام”.</p>
                    <?php $__errorArgs = ['lecture_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <?php $__errorArgs = ['lecture_ids.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div id="fileField" class="">
                    <?php $allFiles = $resource->getAllFiles(); ?>
                    <?php if(count($allFiles) > 0): ?>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">الملفات الحالية (<?php echo e(count($allFiles)); ?>)</label>
                        <ul class="text-sm text-slate-600 mb-3 space-y-1">
                            <?php $__currentLoopData = $allFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><i class="fas fa-file ml-1"></i> <?php echo e($f['name'] ?? 'ملف'); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php endif; ?>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">إضافة ملف جديد أو عدة ملفات (اختياري)</label>
                    <input type="file" name="file" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 mb-2">
                    <input type="file" name="files[]" multiple class="w-full rounded-xl border border-slate-200 px-4 py-2.5">
                    <p class="text-xs text-slate-500 mt-1">الملفات الجديدة تُضاف للموجود. الحد الأقصى 50 ميجا لكل ملف.</p>
                    <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <?php $__errorArgs = ['files.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div id="linkField" class="">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">الرابط</label>
                    <input type="url" name="url" value="<?php echo e(old('url', $resource->url)); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-sky-500">
                    <?php $__errorArgs = ['url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <?php if($groups->isNotEmpty()): ?>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">لمجموعة محددة</label>
                        <select name="group_id" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-sky-500">
                            <option value="">كل الطلاب</option>
                            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($g->id); ?>" <?php echo e(old('group_id', $resource->group_id) == $g->id ? 'selected' : ''); ?>><?php echo e($g->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                <?php endif; ?>
                <div>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $resource->is_active) ? 'checked' : ''); ?> class="rounded border-slate-300">
                        <span class="text-sm font-semibold text-slate-700">نشط (يظهر للطلاب)</span>
                    </label>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="px-4 py-2.5 bg-sky-600 text-white rounded-xl font-semibold hover:bg-sky-700">حفظ</button>
                <a href="<?php echo e(route('instructor.offline-courses.resources.index', ['offlineCourse' => $offlineCourse, 'channel' => ($channel ?? 'offline')])); ?>" class="px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl font-semibold hover:bg-slate-200">إلغاء</a>
            </div>
        </form>
    </div>
</div>
<script>
document.getElementById('resourceType').addEventListener('change', function() {
    var type = this.value;
    document.getElementById('fileField').classList.toggle('hidden', type !== 'file');
    document.getElementById('linkField').classList.toggle('hidden', type !== 'link');
});
document.getElementById('resourceType').dispatchEvent(new Event('change'));
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\instructor\offline-courses\resources\edit.blade.php ENDPATH**/ ?>