

<?php $__env->startSection('title', 'تعديل المهمة'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">تعديل المهمة: <?php echo e($task->title); ?></h1>

            <form action="<?php echo e(route('admin.tasks.update', $task)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">المدرب *</label>
                        <select name="user_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($user->id); ?>" <?php echo e(old('user_id', $task->user_id) == $user->id ? 'selected' : ''); ?>><?php echo e($user->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">عنوان المهمة *</label>
                        <input type="text" name="title" value="<?php echo e(old('title', $task->title)); ?>" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                        <textarea name="description" rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500"><?php echo e(old('description', $task->description)); ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الأولوية</label>
                            <select name="priority"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                <option value="low" <?php echo e(old('priority', $task->priority) == 'low' ? 'selected' : ''); ?>>منخفضة</option>
                                <option value="medium" <?php echo e(old('priority', $task->priority) == 'medium' ? 'selected' : ''); ?>>متوسطة</option>
                                <option value="high" <?php echo e(old('priority', $task->priority) == 'high' ? 'selected' : ''); ?>>عالية</option>
                                <option value="urgent" <?php echo e(old('priority', $task->priority) == 'urgent' ? 'selected' : ''); ?>>عاجلة</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                            <select name="status"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                <option value="pending" <?php echo e(old('status', $task->status) == 'pending' ? 'selected' : ''); ?>>في الانتظار</option>
                                <option value="in_progress" <?php echo e(old('status', $task->status) == 'in_progress' ? 'selected' : ''); ?>>قيد التنفيذ</option>
                                <option value="completed" <?php echo e(old('status', $task->status) == 'completed' ? 'selected' : ''); ?>>مكتملة</option>
                                <option value="cancelled" <?php echo e(old('status', $task->status) == 'cancelled' ? 'selected' : ''); ?>>ملغاة</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الاستحقاق</label>
                        <input type="datetime-local" name="due_date" 
                               value="<?php echo e(old('due_date', $task->due_date ? $task->due_date->format('Y-m-d\TH:i') : '')); ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    </div>

                    <div class="flex justify-end space-x-4 space-x-reverse">
                        <a href="<?php echo e(route('admin.tasks.index')); ?>" class="btn-secondary">
                            إلغاء
                        </a>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save ml-2"></i>
                            حفظ التغييرات
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/tasks/edit.blade.php ENDPATH**/ ?>