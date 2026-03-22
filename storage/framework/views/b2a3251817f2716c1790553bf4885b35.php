

<?php $__env->startSection('title', 'إضافة مهمة جديدة'); ?>
<?php $__env->startSection('header', 'إضافة مهمة جديدة'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <!-- الهيدر -->
    <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
        <div class="px-4 py-6 sm:px-8 sm:py-8 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-4">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-100 text-blue-700 text-sm font-semibold">
                        <i class="fas fa-tasks"></i>
                        إضافة مهمة جديدة
                    </span>
                    <h1 class="text-3xl font-black text-gray-900 leading-tight">تعيين مهمة جديدة لموظف</h1>
                    <p class="text-gray-600 text-lg">
                        قم بتعيين مهمة جديدة لأحد الموظفين مع تحديد الأولوية والموعد النهائي
                    </p>
                </div>
                <a href="<?php echo e(route('admin.employee-tasks.index')); ?>" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gray-500 hover:bg-gray-600 text-white text-sm font-bold shadow-lg hover:shadow-xl transition-all duration-300 w-full sm:w-auto">
                    <i class="fas fa-arrow-right"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </div>

    <!-- نموذج الإضافة -->
    <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-gray-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);">
        <form action="<?php echo e(route('admin.employee-tasks.store')); ?>" method="POST" class="p-6 sm:p-8 space-y-8">
            <?php echo csrf_field(); ?>

            <!-- معلومات المهمة -->
            <div class="space-y-6">
                <h2 class="text-xl font-bold text-gray-900 border-b border-gray-200 pb-3">معلومات المهمة</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="employee_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            الموظف <span class="text-red-500">*</span>
                        </label>
                        <select name="employee_id" id="employee_id" required
                                class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition">
                            <option value="">اختر الموظف</option>
                            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($employee->id); ?>"
                                        data-job-name="<?php echo e($employee->employeeJob?->name ?? ''); ?>"
                                        <?php echo e(old('employee_id') == $employee->id ? 'selected' : ''); ?>>
                                    <?php echo e($employee->name); ?>

                                    <?php if($employee->employee_code): ?>
                                        (<?php echo e($employee->employee_code); ?>)
                                    <?php endif; ?>
                                    <?php if($employee->employeeJob): ?>
                                        - <?php echo e($employee->employeeJob->name); ?>

                                    <?php endif; ?>
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['employee_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-xs text-rose-500"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="md:col-span-2">
                        <label for="task_type" class="block text-sm font-semibold text-gray-700 mb-2">
                            نوع المهمة <span class="text-red-500">*</span>
                        </label>
                        <select name="task_type" id="task_type" required
                                class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition">
                            <option value="general" <?php echo e(old('task_type', 'general') == 'general' ? 'selected' : ''); ?>>مهمة عامة</option>
                            <option value="video_editing" <?php echo e(old('task_type') == 'video_editing' ? 'selected' : ''); ?>>مونتاج فيديو</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">مهمة "مونتاج فيديو" تظهر للموظف مع حقول تسليم: الفيديو المستلم، ممن استلمه، مدة قبل/بعد المونتاج</p>
                        <?php $__errorArgs = ['task_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-xs text-rose-500"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                            عنوان المهمة <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="<?php echo e(old('title')); ?>" required
                               class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition"
                               placeholder="مثال: إعداد محتوى كورس Laravel">
                        <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-xs text-rose-500"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                            وصف المهمة
                        </label>
                        <textarea name="description" id="description" rows="4"
                                  class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition"
                                  placeholder="وصف تفصيلي للمهمة المطلوبة..."><?php echo e(old('description')); ?></textarea>
                        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-xs text-rose-500"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-semibold text-gray-700 mb-2">
                            الأولوية <span class="text-red-500">*</span>
                        </label>
                        <select name="priority" id="priority" required
                                class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition">
                            <option value="low" <?php echo e(old('priority') == 'low' ? 'selected' : ''); ?>>منخفضة</option>
                            <option value="medium" <?php echo e(old('priority') == 'medium' ? 'selected' : ''); ?>>متوسطة</option>
                            <option value="high" <?php echo e(old('priority') == 'high' ? 'selected' : ''); ?>>عالية</option>
                            <option value="urgent" <?php echo e(old('priority') == 'urgent' ? 'selected' : ''); ?>>عاجلة</option>
                        </select>
                        <?php $__errorArgs = ['priority'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-xs text-rose-500"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="deadline" class="block text-sm font-semibold text-gray-700 mb-2">
                            الموعد النهائي
                        </label>
                        <input type="date" name="deadline" id="deadline" value="<?php echo e(old('deadline')); ?>"
                               min="<?php echo e(date('Y-m-d', strtotime('+1 day'))); ?>"
                               class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition">
                        <?php $__errorArgs = ['deadline'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-xs text-rose-500"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                            ملاحظات إضافية
                        </label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition"
                                  placeholder="أي ملاحظات أو تعليمات إضافية..."><?php echo e(old('notes')); ?></textarea>
                        <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-xs text-rose-500"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <!-- أزرار الإجراءات -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6 border-t border-gray-100">
                <span class="text-xs text-gray-500">
                    سيتم تعيين المهمة بحالة "معلقة" ويمكن للموظف البدء فيها لاحقاً
                </span>
                <div class="flex flex-col md:flex-row md:items-center gap-3">
                    <a href="<?php echo e(route('admin.employee-tasks.index')); ?>" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 text-sm font-bold shadow-lg hover:shadow-xl transition-all duration-300">
                        <i class="fas fa-times"></i>
                        إلغاء
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 via-blue-500 to-blue-600 hover:from-blue-700 hover:via-blue-600 hover:to-blue-700 text-white px-6 py-3 text-sm font-bold shadow-lg shadow-blue-600/30 hover:shadow-xl hover:shadow-blue-600/40 hover:-translate-y-0.5 transition-all duration-300">
                        <i class="fas fa-save"></i>
                        حفظ المهمة
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var employeeSelect = document.getElementById('employee_id');
    var taskTypeSelect = document.getElementById('task_type');
    if (!employeeSelect || !taskTypeSelect) return;

    employeeSelect.addEventListener('change', function() {
        var opt = this.options[this.selectedIndex];
        var jobName = (opt && opt.getAttribute('data-job-name')) ? opt.getAttribute('data-job-name') : '';
        if (jobName && /مونتاج|فيديو|مونتاج فيديو|video|editing/i.test(jobName)) {
            taskTypeSelect.value = 'video_editing';
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\employee-tasks\create.blade.php ENDPATH**/ ?>