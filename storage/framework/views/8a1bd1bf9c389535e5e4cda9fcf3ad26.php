

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- الهيدر -->
        <div class="bg-white shadow-lg rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">تعديل الفصل: <?php echo e($classroom->name); ?></h1>
                        <p class="text-sm text-gray-500 mt-1">تحديث بيانات الفصل الدراسي</p>
                    </div>
                    <div class="flex space-x-2">
                        <a href="<?php echo e(route('admin.classrooms.show', $classroom)); ?>" 
                           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-eye mr-2"></i>
                            عرض الفصل
                        </a>
                        <a href="<?php echo e(route('admin.classrooms.index')); ?>" 
                           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-arrow-right mr-2"></i>
                            العودة للقائمة
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- نموذج التعديل -->
        <div class="bg-white shadow-lg rounded-lg">
            <form method="POST" action="<?php echo e(route('admin.classrooms.update', $classroom)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- اسم الفصل -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                اسم الفصل <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="<?php echo e(old('name', $classroom->name)); ?>" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل اسم الفصل">
                            <?php $__errorArgs = ['name'];
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

                        <!-- المدرسة -->
                        <div>
                            <label for="school_id" class="block text-sm font-medium text-gray-700 mb-2">
                                المدرسة <span class="text-red-500">*</span>
                            </label>
                            <select name="school_id" id="school_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- اختر المدرسة --</option>
                                <?php $__currentLoopData = $schools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($school->id); ?>" <?php echo e(old('school_id', $classroom->school_id) == $school->id ? 'selected' : ''); ?>>
                                        <?php echo e($school->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['school_id'];
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

                        <!-- المعلم -->
                        <div>
                            <label for="teacher_id" class="block text-sm font-medium text-gray-700 mb-2">
                                المعلم المسؤول
                            </label>
                            <select name="teacher_id" id="teacher_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- اختر المعلم (اختياري) --</option>
                                <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($teacher->id); ?>" <?php echo e(old('teacher_id', $classroom->teacher_id) == $teacher->id ? 'selected' : ''); ?>>
                                        <?php echo e($teacher->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['teacher_id'];
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

                        <!-- الوصف -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                الوصف
                            </label>
                            <textarea name="description" id="description" rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="أدخل وصف الفصل (اختياري)"><?php echo e(old('description', $classroom->description)); ?></textarea>
                            <?php $__errorArgs = ['description'];
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

                        <!-- حالة الفصل -->
                        <div class="md:col-span-2">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1" 
                                       <?php echo e(old('is_active', $classroom->is_active) ? 'checked' : ''); ?>

                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_active" class="mr-2 block text-sm text-gray-700">
                                    الفصل نشط
                                </label>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                يمكن للطلاب التسجيل في الفصول النشطة فقط
                            </p>
                        </div>
                    </div>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            آخر تحديث: <?php echo e($classroom->updated_at->format('Y-m-d H:i')); ?>

                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="<?php echo e(route('admin.classrooms.index')); ?>" 
                               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                إلغاء
                            </a>
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-save mr-2"></i>
                                حفظ التغييرات
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- معلومات إضافية -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- إحصائيات الفصل -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-users text-blue-600"></i>
                            </div>
                        </div>
                        <div class="mr-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">عدد الطلاب</dt>
                                <dd class="text-lg font-bold text-gray-900"><?php echo e($classroom->students()->count()); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- تاريخ الإنشاء -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-calendar text-green-600"></i>
                            </div>
                        </div>
                        <div class="mr-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">تاريخ الإنشاء</dt>
                                <dd class="text-lg font-bold text-gray-900"><?php echo e($classroom->created_at->format('Y-m-d')); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الحالة -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-<?php echo e($classroom->is_active ? 'green' : 'red'); ?>-100 $classroom->is_active ? 'green' : 'red' }}-900 rounded-full flex items-center justify-center">
                                <i class="fas fa-<?php echo e($classroom->is_active ? 'check' : 'times'); ?> text-<?php echo e($classroom->is_active ? 'green' : 'red'); ?>-600 $classroom->is_active ? 'green' : 'red' }}-400"></i>
                            </div>
                        </div>
                        <div class="mr-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">الحالة</dt>
                                <dd class="text-lg font-bold text-<?php echo e($classroom->is_active ? 'green' : 'red'); ?>-600 $classroom->is_active ? 'green' : 'red' }}-400">
                                    <?php echo e($classroom->is_active ? 'نشط' : 'معطل'); ?>

                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/classrooms/edit.blade.php ENDPATH**/ ?>