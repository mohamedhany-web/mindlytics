

<?php $__env->startSection('title', 'إضافة محاضرة - كورس ' . (($channel ?? 'offline') === 'online' ? 'أونلاين' : 'أوفلاين')); ?>
<?php $__env->startSection('header', 'إضافة محاضرة'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- هيدر الصفحة (عرض الصفحة الكامل) -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6 mb-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="<?php echo e(route('instructor.offline-courses.index', ['channel' => ($channel ?? 'offline')])); ?>" class="hover:text-amber-600 transition-colors"><?php echo e(($channel ?? 'offline') === 'online' ? 'كورساتي الأونلاين' : 'كورساتي الأوفلاين'); ?></a>
            <span class="mx-2">/</span>
            <a href="<?php echo e(route('instructor.offline-courses.show', ['offline_course' => $offlineCourse, 'channel' => ($channel ?? 'offline')])); ?>" class="hover:text-amber-600 transition-colors"><?php echo e($offlineCourse->title); ?></a>
            <span class="mx-2">/</span>
            <?php if(old('curriculum_section_id', $curriculumSectionId ?? null)): ?>
                <a href="<?php echo e(route('instructor.offline-courses.curriculum.index', $offlineCourse)); ?>?channel=<?php echo e(urlencode($channel ?? 'offline')); ?>" class="hover:text-amber-600 transition-colors">المنهج</a>
                <span class="mx-2">/</span>
            <?php endif; ?>
            <a href="<?php echo e(route('instructor.offline-courses.lectures.index', ['offlineCourse' => $offlineCourse, 'channel' => ($channel ?? 'offline')])); ?>" class="hover:text-amber-600 transition-colors">الجلسات</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">إضافة محاضرة</span>
        </nav>
        <div class="flex flex-wrap items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center shrink-0">
                <i class="fas fa-chalkboard-teacher text-lg"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800">إضافة محاضرة (<?php echo e(($channel ?? 'offline') === 'online' ? 'أونلاين' : 'أوفلاين'); ?>)</h1>
                <p class="text-sm text-slate-600 mt-0.5">
                    <?php if(old('curriculum_section_id', $curriculumSectionId ?? null)): ?>
                        ستُضاف المحاضرة تلقائياً إلى القسم الذي اخترته في المنهج بعد الحفظ.
                    <?php else: ?>
                        إضافة محاضرة مع برنامج اليوم، تسجيل، روابط تحميل ومرفقات للطلاب.
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>

    <!-- بطاقة النموذج -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="<?php echo e(route('instructor.offline-courses.lectures.store', ['offlineCourse' => $offlineCourse, 'channel' => ($channel ?? 'offline')])); ?>" method="post" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php if(old('curriculum_section_id', $curriculumSectionId ?? null)): ?>
                <input type="hidden" name="curriculum_section_id" value="<?php echo e(old('curriculum_section_id', $curriculumSectionId)); ?>">
            <?php endif; ?>
            <?php $__errorArgs = ['curriculum_section_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">عنوان المحاضرة <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="<?php echo e(old('title')); ?>" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500" placeholder="مثال: المحاضرة الأولى">
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
                    'value' => old('offline_group_session_id'),
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">الوصف</label>
                    <textarea name="description" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500" placeholder="شرح عام للمحاضرة أو أهدافها"><?php echo e(old('description')); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">برنامج اليوم (نقاط)</label>
                    <textarea name="session_agenda" rows="5" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500 font-mono text-sm" placeholder="اكتب كل نقطة في سطر منفصل، مثال:&#10;- مراجعة الدرس السابق&#10;- شرح الوحدة الجديدة&#10;- تطبيق عملي"><?php echo e(old('session_agenda')); ?></textarea>
                    <p class="text-xs text-slate-500 mt-1">يُعرض للطلاب كقائمة نقاط داخل صفحة الكورس والمنهج.</p>
                    <?php $__errorArgs = ['session_agenda'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <?php echo $__env->make('instructor.offline-courses.lectures.partials.offline-mindmap-field', ['variant' => 'default', 'value' => old('offline_attendee_mindmap')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php if(!($hasGroupSessions ?? false)): ?>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">موعد المحاضرة</label>
                            <input type="datetime-local" name="scheduled_at" value="<?php echo e(old('scheduled_at')); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">المدة (دقيقة)</label>
                            <input type="number" name="duration_minutes" value="<?php echo e(old('duration_minutes')); ?>" min="0" max="600" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500">
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-xs text-slate-500 rounded-xl border border-slate-100 bg-slate-50 px-3 py-2">عند اختيار جلسة من التقويم، يُحدَّد موعد المحاضرة ومدتها تلقائياً من بيانات الجلسة.</p>
                <?php endif; ?>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">رابط الميتينج (للأونلاين)</label>
                    <input type="url" name="meeting_url" value="<?php echo e(old('meeting_url')); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500" placeholder="https://meet.google.com/...">
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
                    <input type="url" name="recording_url" value="<?php echo e(old('recording_url')); ?>" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500" placeholder="https://...">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">روابط تحميل (اختياري)</label>
                    <div id="downloadLinks">
                        <div class="flex gap-2 mb-2">
                            <input type="text" name="download_links[0][label]" placeholder="النص" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5">
                            <input type="url" name="download_links[0][url]" placeholder="الرابط" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5">
                        </div>
                    </div>
                    <button type="button" id="addLink" class="text-sm text-violet-600 font-medium">+ إضافة رابط</button>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">مرفقات (ملفات)</label>
                    <input type="file" name="attachments[]" multiple class="w-full rounded-xl border border-slate-200 px-4 py-2.5">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">ملاحظات</label>
                    <textarea name="notes" rows="2" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500"><?php echo e(old('notes')); ?></textarea>
                </div>
                <?php if($groups->isNotEmpty()): ?>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">لمجموعة محددة (اختياري)</label>
                        <select name="group_id" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-violet-500">
                            <option value="">كل الطلاب</option>
                            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($g->id); ?>" <?php echo e(old('group_id') == $g->id ? 'selected' : ''); ?>><?php echo e($g->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                <?php endif; ?>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="px-4 py-2.5 bg-violet-600 text-white rounded-xl font-semibold hover:bg-violet-700">حفظ</button>
                <a href="<?php echo e(old('curriculum_section_id', $curriculumSectionId ?? null) ? route('instructor.offline-courses.curriculum.index', $offlineCourse).'?channel='.urlencode($channel ?? 'offline') : route('instructor.offline-courses.lectures.index', ['offlineCourse' => $offlineCourse, 'channel' => ($channel ?? 'offline')])); ?>" class="px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl font-semibold hover:bg-slate-200">إلغاء</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->startPush('scripts'); ?>
<script>
var linkIndex = 1;
document.getElementById('addLink').addEventListener('click', function() {
    var div = document.createElement('div');
    div.className = 'flex gap-2 mb-2';
    div.innerHTML = '<input type="text" name="download_links[' + linkIndex + '][label]" placeholder="النص" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5">' +
        '<input type="url" name="download_links[' + linkIndex + '][url]" placeholder="الرابط" class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5">';
    document.getElementById('downloadLinks').appendChild(div);
    linkIndex++;
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\instructor\offline-courses\lectures\create.blade.php ENDPATH**/ ?>