

<?php $__env->startSection('title', 'تعديل محاضرة - كورس ' . (($channel ?? 'offline') === 'online' ? 'أونلاين' : 'أوفلاين')); ?>
<?php $__env->startSection('header', 'تعديل محاضرة'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="<?php echo e(route('instructor.offline-courses.lectures.index', ['offlineCourse' => $offlineCourse, 'channel' => ($channel ?? 'offline')])); ?>" class="hover:text-amber-600">الجلسات</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">تعديل</span>
        </nav>
        <h1 class="text-xl font-bold text-slate-800">تعديل المحاضرة</h1>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <form action="<?php echo e(route('instructor.offline-courses.lectures.update', ['offlineCourse' => $offlineCourse, 'lecture' => $lecture, 'channel' => ($channel ?? 'offline')])); ?>" method="post" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">عنوان المحاضرة <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="<?php echo e(old('title', $lecture->title)); ?>" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500">
                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <?php echo $__env->make('instructor.offline-courses.lectures.partials.session-select', [
                    'groupSessions' => $groupSessions ?? collect(),
                    'required' => ($hasGroupSessions ?? false),
                    'value' => old('offline_group_session_id', $lecture->offline_group_session_id),
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">الوصف</label>
                    <textarea name="description" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500"><?php echo e(old('description', $lecture->description)); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">برنامج اليوم (نقطة لكل سطر)</label>
                    <textarea name="session_agenda" rows="5" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500 font-mono text-sm"><?php echo e(old('session_agenda', $lecture->session_agenda)); ?></textarea>
                    <?php $__errorArgs = ['session_agenda'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <?php echo $__env->make('instructor.offline-courses.lectures.partials.offline-mindmap-field', ['variant' => 'default', 'value' => old('offline_attendee_mindmap', $lecture->offline_attendee_mindmap)], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php if(!($hasGroupSessions ?? false)): ?>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">موعد المحاضرة</label>
                            <input type="datetime-local" name="scheduled_at" value="<?php echo e(old('scheduled_at', $lecture->scheduled_at ? $lecture->scheduled_at->format('Y-m-d\TH:i') : '')); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">المدة (دقيقة)</label>
                            <input type="number" name="duration_minutes" value="<?php echo e(old('duration_minutes', $lecture->duration_minutes)); ?>" min="0" max="600" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500">
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-xs text-slate-500 rounded-xl border border-slate-100 bg-slate-50 px-3 py-2">عند ربط المحاضرة بجلسة، يُحدَّد الموعد والمدة من الجلسة (يمكنك تغيير الجلسة من القائمة أعلاه).</p>
                <?php endif; ?>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">رابط الميتينج (للأونلاين)</label>
                    <input type="url" name="meeting_url" value="<?php echo e(old('meeting_url', $lecture->meeting_url)); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500">
                    <?php $__errorArgs = ['meeting_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">رابط تسجيل المحاضرة</label>
                    <input type="url" name="recording_url" value="<?php echo e(old('recording_url', $lecture->recording_url)); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">روابط تحميل</label>
                    <div id="downloadLinks">
                        <?php $links = $lecture->download_links ?? []; ?>
                        <?php $__empty_1 = true; $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="flex gap-2 mb-2">
                                <input type="text" name="download_links[<?php echo e($i); ?>][label]" value="<?php echo e($link['label'] ?? ''); ?>" placeholder="النص" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5">
                                <input type="url" name="download_links[<?php echo e($i); ?>][url]" value="<?php echo e($link['url'] ?? ''); ?>" placeholder="الرابط" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5">
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="flex gap-2 mb-2">
                                <input type="text" name="download_links[0][label]" placeholder="النص" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5">
                                <input type="url" name="download_links[0][url]" placeholder="الرابط" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5">
                            </div>
                        <?php endif; ?>
                    </div>
                    <button type="button" id="addLink" class="text-sm text-violet-600 hover:text-violet-700 font-medium">+ إضافة رابط</button>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">مرفقات إضافية (ملفات جديدة)</label>
                    <input type="file" name="attachments[]" multiple class="w-full rounded-xl border border-slate-200 px-4 py-2.5">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">ملاحظات</label>
                    <textarea name="notes" rows="2" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500"><?php echo e(old('notes', $lecture->notes)); ?></textarea>
                </div>
                <?php if($groups->isNotEmpty()): ?>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">لمجموعة محددة</label>
                        <select name="group_id" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500">
                            <option value="">كل الطلاب</option>
                            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($g->id); ?>" <?php echo e(old('group_id', $lecture->group_id) == $g->id ? 'selected' : ''); ?>><?php echo e($g->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                <?php endif; ?>
                <div>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $lecture->is_active) ? 'checked' : ''); ?> class="rounded border-slate-300">
                        <span class="text-sm font-semibold text-slate-700">نشط (يظهر للطلاب)</span>
                    </label>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="px-4 py-2.5 bg-violet-600 text-white rounded-xl font-semibold hover:bg-violet-700">حفظ</button>
                <a href="<?php echo e(route('instructor.offline-courses.lectures.index', ['offlineCourse' => $offlineCourse, 'channel' => ($channel ?? 'offline')])); ?>" class="px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl font-semibold hover:bg-slate-200">إلغاء</a>
            </div>
        </form>
    </div>
</div>
<script>
var linkIndex = <?php echo e(count($lecture->download_links ?? [])); ?>;
document.getElementById('addLink').addEventListener('click', function() {
    var div = document.createElement('div');
    div.className = 'flex gap-2 mb-2';
    div.innerHTML = '<input type="text" name="download_links[' + linkIndex + '][label]" placeholder="النص" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5">' +
        '<input type="url" name="download_links[' + linkIndex + '][url]" placeholder="الرابط" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5">';
    document.getElementById('downloadLinks').appendChild(div);
    linkIndex++;
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/instructor/offline-courses/lectures/edit.blade.php ENDPATH**/ ?>