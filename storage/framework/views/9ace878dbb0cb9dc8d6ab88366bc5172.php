

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- الهيدر -->
        <div class="bg-white shadow-lg rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">إدارة طلاب الفصل: <?php echo e($classroom->name); ?></h1>
                        <p class="text-sm text-gray-500 mt-1">
                            المدرسة: <?php echo e($classroom->school->name ?? 'غير محدد'); ?>

                            <?php if($classroom->teacher): ?>
                                | المعلم: <?php echo e($classroom->teacher->name); ?>

                            <?php endif; ?>
                        </p>
                    </div>
                    <a href="<?php echo e(route('admin.classrooms.index')); ?>" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-arrow-right mr-2"></i>
                        العودة للفصول
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- قائمة الطلاب الحاليين -->
            <div class="bg-white shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        الطلاب المسجلين (<?php echo e($classroom->students->count()); ?>)
                    </h2>
                </div>
                <div class="p-6">
                    <?php if($classroom->students->count() > 0): ?>
                        <div class="space-y-4">
                            <?php $__currentLoopData = $classroom->students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-user-graduate text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-900"><?php echo e($student->name); ?></h3>
                                        <p class="text-sm text-gray-500"><?php echo e($student->email); ?></p>
                                        <?php if($student->pivot->enrolled_at): ?>
                                            <p class="text-xs text-gray-400">
                                                مسجل منذ: <?php echo e(\Carbon\Carbon::parse($student->pivot->enrolled_at)->format('Y-m-d')); ?>

                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        <?php echo e($student->pivot->is_active ? 'bg-green-100 text-green-800 ': ''bg-red-100 text-red-800); ?>">']
                                        <?php echo e($student->pivot->is_active ? 'نشط' : 'معطل'); ?>

                                    </span>
                                    <form method="POST" action="<?php echo e(route('admin.classrooms.remove-student', [$classroom, $student])); ?>" 
                                          class="inline" onsubmit="return confirm('هل أنت متأكد من إزالة هذا الطالب من الفصل؟')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            <i class="fas fa-trash mr-1"></i>
                                            إزالة
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <div class="text-gray-500">
                                <i class="fas fa-user-graduate text-4xl mb-4"></i>
                                <p class="text-lg font-medium">لا يوجد طلاب مسجلين</p>
                                <p class="text-sm mt-2">لم يتم تسجيل أي طالب في هذا الفصل بعد</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- إضافة طلاب جدد -->
            <div class="bg-white shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">إضافة طالب جديد</h2>
                </div>
                <div class="p-6">
                    <?php if($availableStudents->count() > 0): ?>
                        <form method="POST" action="<?php echo e(route('admin.classrooms.add-student', $classroom)); ?>" class="mb-6">
                            <?php echo csrf_field(); ?>
                            <div class="mb-4">
                                <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    اختر طالب
                                </label>
                                <select name="student_id" id="student_id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">-- اختر طالب --</option>
                                    <?php $__currentLoopData = $availableStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($student->id); ?>"><?php echo e($student->name); ?> (<?php echo e($student->email); ?>)</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['student_id'];
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
                            <button type="submit" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                إضافة الطالب
                            </button>
                        </form>

                        <!-- قائمة الطلاب المتاحين للإضافة -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-sm font-medium text-gray-700 mb-4">
                                الطلاب المتاحين (<?php echo e($availableStudents->count()); ?>)
                            </h3>
                            <div class="max-h-64 overflow-y-auto space-y-2">
                                <?php $__currentLoopData = $availableStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center">
                                                <i class="fas fa-user text-gray-600 text-xs"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900"><?php echo e($student->name); ?></p>
                                            <p class="text-xs text-gray-500"><?php echo e($student->email); ?></p>
                                        </div>
                                    </div>
                                    <form method="POST" action="<?php echo e(route('admin.classrooms.add-student', $classroom)); ?>" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="student_id" value="<?php echo e($student->id); ?>">
                                        <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            <i class="fas fa-plus mr-1"></i>
                                            إضافة
                                        </button>
                                    </form>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <div class="text-gray-500">
                                <i class="fas fa-users text-4xl mb-4"></i>
                                <p class="text-lg font-medium">لا يوجد طلاب متاحين</p>
                                <p class="text-sm mt-2">جميع الطلاب مسجلين بالفعل في هذا الفصل أو لا يوجد طلاب في النظام</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- إحصائيات سريعة -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
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
                                <dt class="text-sm font-medium text-gray-500 truncate">إجمالي الطلاب</dt>
                                <dd class="text-lg font-bold text-gray-900"><?php echo e($classroom->students->count()); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                        </div>
                        <div class="mr-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">الطلاب النشطين</dt>
                                <dd class="text-lg font-bold text-gray-900">
                                    <?php echo e($classroom->students->where('pivot.is_active', true)->count()); ?>

                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-plus text-yellow-600"></i>
                            </div>
                        </div>
                        <div class="mr-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">متاح للإضافة</dt>
                                <dd class="text-lg font-bold text-gray-900"><?php echo e($availableStudents->count()); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>










<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/classrooms/students.blade.php ENDPATH**/ ?>