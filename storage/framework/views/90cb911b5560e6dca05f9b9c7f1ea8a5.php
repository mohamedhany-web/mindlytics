<?php $__env->startSection('title', 'دروس ' . $course->title); ?>
<?php $__env->startSection('header', 'دروس ' . $course->title); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                    <a href="<?php echo e(route('instructor.courses.index')); ?>" class="hover:text-sky-600 transition-colors">الكورسات</a>
                    <span>/</span>
                    <a href="<?php echo e(route('instructor.courses.show', $course->id)); ?>" class="hover:text-sky-600 transition-colors truncate max-w-[180px]"><?php echo e($course->title); ?></a>
                    <span>/</span>
                    <span class="text-slate-700 font-medium">الدروس</span>
                </nav>
                <h1 class="text-2xl font-bold text-slate-800">إدارة دروس الكورس</h1>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('instructor.courses.lessons.create', $course->id)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-plus"></i>
                    إضافة درس جديد
                </a>
                <a href="<?php echo e(route('instructor.courses.show', $course->id)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    رجوع
                </a>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
    <div class="rounded-xl p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between gap-4">
        <span class="flex items-center gap-2"><i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?></span>
        <button type="button" onclick="this.parentElement.remove()" class="p-1 rounded hover:bg-emerald-100 text-emerald-600">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
    <div class="rounded-xl p-4 bg-red-50 border border-red-200 text-red-800 flex items-center justify-between gap-4">
        <span class="flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?></span>
        <button type="button" onclick="this.parentElement.remove()" class="p-1 rounded hover:bg-red-100 text-red-600">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <?php endif; ?>

    <!-- معلومات الكورس -->
    <div class="rounded-xl p-5 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-800 mb-1"><?php echo e($course->title); ?></h2>
                <p class="text-sm text-slate-500 flex items-center gap-2">
                    <i class="fas fa-book-open text-sky-500"></i>
                    عدد الدروس: <strong class="text-slate-700"><?php echo e($lessons->total()); ?></strong>
                </p>
            </div>
            <a href="<?php echo e(route('instructor.courses.curriculum', $course->id)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-violet-500 hover:bg-violet-600 text-white rounded-xl font-semibold transition-colors shrink-0">
                <i class="fas fa-sitemap"></i>
                المنهج الدراسي
            </a>
        </div>
    </div>

    <!-- قائمة الدروس -->
    <div class="rounded-xl overflow-hidden bg-white border border-slate-200 shadow-sm">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-list text-sky-500"></i>
                قائمة الدروس
            </h3>
        </div>

        <?php if($lessons->count() > 0): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-right py-3 px-4 text-xs font-semibold text-slate-500 w-12">#</th>
                        <th class="text-right py-3 px-4 text-xs font-semibold text-slate-500">عنوان الدرس</th>
                        <th class="text-right py-3 px-4 text-xs font-semibold text-slate-500 w-24">النوع</th>
                        <th class="text-right py-3 px-4 text-xs font-semibold text-slate-500 w-24">المدة</th>
                        <th class="text-right py-3 px-4 text-xs font-semibold text-slate-500 w-20">الترتيب</th>
                        <th class="text-right py-3 px-4 text-xs font-semibold text-slate-500 w-28">الحالة</th>
                        <th class="text-center py-3 px-4 text-xs font-semibold text-slate-500 w-36">الإجراءات</th>
                    </tr>
                </thead>
                <tbody id="lessons-sortable">
                    <?php $__currentLoopData = $lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr data-lesson-id="<?php echo e($lesson->id); ?>" class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                        <td class="py-3 px-4">
                            <i class="fas fa-grip-vertical text-slate-400 cursor-move"></i>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                <?php if($lesson->type === 'video'): ?>
                                    <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center shrink-0">
                                        <i class="fas fa-video text-red-500 text-sm"></i>
                                    </div>
                                <?php elseif($lesson->type === 'text'): ?>
                                    <div class="w-9 h-9 rounded-lg bg-sky-50 flex items-center justify-center shrink-0">
                                        <i class="fas fa-file-alt text-sky-500 text-sm"></i>
                                    </div>
                                <?php elseif($lesson->type === 'document'): ?>
                                    <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center shrink-0">
                                        <i class="fas fa-file-pdf text-amber-500 text-sm"></i>
                                    </div>
                                <?php else: ?>
                                    <div class="w-9 h-9 rounded-lg bg-violet-50 flex items-center justify-center shrink-0">
                                        <i class="fas fa-question-circle text-violet-500 text-sm"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="font-semibold text-slate-800"><?php echo e($lesson->title); ?></div>
                                    <?php if($lesson->is_free): ?>
                                        <span class="text-xs text-emerald-600 font-medium">مجاني</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <?php if($lesson->type === 'video'): ?>
                                <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-medium bg-red-100 text-red-700">فيديو</span>
                            <?php elseif($lesson->type === 'text'): ?>
                                <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-medium bg-sky-100 text-sky-700">نص</span>
                            <?php elseif($lesson->type === 'document'): ?>
                                <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-medium bg-amber-100 text-amber-700">ملف</span>
                            <?php else: ?>
                                <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-medium bg-violet-100 text-violet-700">اختبار</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 text-sm text-slate-600">
                            <?php if($lesson->duration_minutes): ?>
                                <?php echo e($lesson->duration_minutes); ?> د
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-slate-700 text-sm font-semibold"><?php echo e($lesson->order); ?></span>
                        </td>
                        <td class="py-3 px-4">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox"
                                       class="toggle-status w-4 h-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500"
                                       data-lesson-id="<?php echo e($lesson->id); ?>"
                                       <?php echo e($lesson->is_active ? 'checked' : ''); ?>>
                                <span class="text-sm font-medium <?php echo e($lesson->is_active ? 'text-emerald-600' : 'text-slate-500'); ?>">
                                    <?php echo e($lesson->is_active ? 'نشط' : 'غير نشط'); ?>

                                </span>
                            </label>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center justify-center gap-1">
                                <a href="<?php echo e(route('instructor.courses.lessons.show', [$course->id, $lesson->id])); ?>"
                                   class="p-2 rounded-lg bg-sky-100 hover:bg-sky-200 text-sky-600 transition-colors" title="عرض">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <a href="<?php echo e(route('instructor.courses.lessons.edit', [$course->id, $lesson->id])); ?>"
                                   class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors" title="تعديل">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                <button type="button"
                                        class="delete-lesson p-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 transition-colors"
                                        data-lesson-id="<?php echo e($lesson->id); ?>"
                                        data-lesson-title="<?php echo e($lesson->title); ?>"
                                        title="حذف">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <?php if($lessons->hasPages()): ?>
        <div class="px-5 py-4 border-t border-slate-200">
            <?php echo e($lessons->links()); ?>

        </div>
        <?php endif; ?>
        <?php else: ?>
        <div class="text-center py-16">
            <div class="w-20 h-20 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-book-open text-3xl text-slate-400"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">لا توجد دروس حتى الآن</h3>
            <p class="text-slate-500 mb-6">ابدأ بإضافة دروس لهذا الكورس</p>
            <a href="<?php echo e(route('instructor.courses.lessons.create', $course->id)); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                <i class="fas fa-plus"></i>
                إضافة أول درس
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal تأكيد الحذف -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4" role="dialog">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-xl border border-slate-200">
        <h3 class="text-lg font-bold text-slate-800 mb-2">تأكيد الحذف</h3>
        <p class="text-slate-600 mb-4">هل أنت متأكد من حذف الدرس: <strong id="lesson-title-to-delete" class="text-slate-800"></strong>؟</p>
        <p class="text-sm text-red-600 mb-6">هذا الإجراء لا يمكن التراجع عنه.</p>
        <div class="flex gap-3">
            <button type="button" id="delete-modal-cancel" class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                إلغاء
            </button>
            <form id="delete-form" method="POST" class="flex-1" style="display: inline;">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="w-full px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl font-semibold transition-colors">
                    حذف
                </button>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '<?php echo e(csrf_token()); ?>';

    document.querySelectorAll('.toggle-status').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const lessonId = this.dataset.lessonId;
            const url = '/instructor/courses/<?php echo e($course->id); ?>/lessons/' + lessonId + '/toggle-status';
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const label = this.nextElementSibling;
                    label.textContent = data.is_active ? 'نشط' : 'غير نشط';
                    label.classList.toggle('text-emerald-600', data.is_active);
                    label.classList.toggle('text-slate-500', !data.is_active);
                }
            })
            .catch(() => {
                this.checked = !this.checked;
                alert('حدث خطأ أثناء تحديث الحالة');
            });
        });
    });

    const modal = document.getElementById('deleteModal');
    const cancelBtn = document.getElementById('delete-modal-cancel');

    document.querySelectorAll('.delete-lesson').forEach(btn => {
        btn.addEventListener('click', function() {
            const lessonId = this.dataset.lessonId;
            const lessonTitle = this.dataset.lessonTitle;
            document.getElementById('lesson-title-to-delete').textContent = lessonTitle;
            document.getElementById('delete-form').action = '/instructor/courses/<?php echo e($course->id); ?>/lessons/' + lessonId;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    });

    function closeDeleteModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    if (cancelBtn) cancelBtn.addEventListener('click', closeDeleteModal);
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeDeleteModal();
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\instructor\lessons\index.blade.php ENDPATH**/ ?>