<?php $__env->startSection('title', 'مجموعات الكورس الأوفلاين'); ?>
<?php $__env->startSection('header', 'مجموعات الكورس الأوفلاين'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <nav class="text-sm text-gray-500 mb-1">
                    <a href="<?php echo e(route('admin.offline-courses.index')); ?>" class="hover:text-blue-600">الكورسات الأوفلاين</a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.offline-courses.show', $offlineCourse)); ?>" class="hover:text-blue-600"><?php echo e($offlineCourse->title); ?></a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-700 font-semibold">المجموعات</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900">مجموعات: <?php echo e($offlineCourse->title); ?></h1>
                <p class="text-gray-600 mt-1">إدارة مجموعات الكورس الأوفلاين</p>
            </div>
            <a href="<?php echo e(route('admin.offline-courses.show', $offlineCourse)); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                <i class="fas fa-arrow-right mr-2"></i>
                العودة للكورس
            </a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <!-- إضافة مجموعة -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-lg font-bold text-gray-900 mb-4">إضافة مجموعة جديدة</h2>
        <form action="<?php echo e(route('admin.offline-courses.groups.store', $offlineCourse)); ?>" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php echo csrf_field(); ?>
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">اسم المجموعة <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label for="instructor_id" class="block text-sm font-medium text-gray-700 mb-1">المدرب <span class="text-red-500">*</span></label>
                <select name="instructor_id" id="instructor_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">اختر المدرب</option>
                    <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($instructor->id); ?>" <?php echo e(old('instructor_id') == $instructor->id ? 'selected' : ''); ?>><?php echo e($instructor->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['instructor_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label for="max_students" class="block text-sm font-medium text-gray-700 mb-1">الحد الأقصى للطلاب <span class="text-red-500">*</span></label>
                <input type="number" name="max_students" id="max_students" value="<?php echo e(old('max_students', 30)); ?>" min="1" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <?php $__errorArgs = ['max_students'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">المكان</label>
                <input type="text" name="location" id="location" value="<?php echo e(old('location')); ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <?php $__errorArgs = ['location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label for="class_time" class="block text-sm font-medium text-gray-700 mb-1">وقت الحصة</label>
                <input type="datetime-local" name="class_time" id="class_time" value="<?php echo e(old('class_time')); ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <?php $__errorArgs = ['class_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="md:col-span-2 lg:col-span-1">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">الوصف</label>
                <textarea name="description" id="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?php echo e(old('description')); ?></textarea>
                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="md:col-span-2 lg:col-span-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    إضافة المجموعة
                </button>
            </div>
        </form>
    </div>

    <!-- قائمة المجموعات -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">المجموعات (<?php echo e($groups->count()); ?>)</h2>
        </div>
        <?php if($groups->count() > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المدرب</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الطلاب</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المكان</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900"><?php echo e($group->name); ?></div>
                                <?php if($group->description): ?>
                                    <div class="text-sm text-gray-500"><?php echo e(Str::limit($group->description, 50)); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?php echo e($group->instructor->name ?? '—'); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?php echo e($group->current_students ?? 0); ?> / <?php echo e($group->max_students); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?php echo e($group->location ?? '—'); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                    $statusClass = [
                                        'active' => 'bg-green-100 text-green-800',
                                        'completed' => 'bg-blue-100 text-blue-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                    ];
                                ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo e($statusClass[$group->status] ?? 'bg-gray-100 text-gray-800'); ?>">
                                    <?php echo e($group->status === 'active' ? 'نشط' : ($group->status === 'completed' ? 'منتهي' : 'ملغي')); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button type="button" onclick="openEditModal(<?php echo e(json_encode($group)); ?>)" class="text-yellow-600 hover:text-yellow-800 font-medium ml-2">تعديل</button>
                                <form action="<?php echo e(route('admin.offline-courses.groups.destroy', [$offlineCourse, $group])); ?>" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه المجموعة؟');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium">حذف</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="px-6 py-12 text-center text-gray-500">
                <i class="fas fa-users-cog text-4xl text-gray-300 mb-3"></i>
                <p>لا توجد مجموعات لهذا الكورس. أضف مجموعة من النموذج أعلاه.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- نافذة تعديل المجموعة -->
<div id="editGroupModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">تعديل المجموعة</h3>
            <form id="editGroupForm" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-1">اسم المجموعة <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="edit_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="edit_instructor_id" class="block text-sm font-medium text-gray-700 mb-1">المدرب <span class="text-red-500">*</span></label>
                        <select name="instructor_id" id="edit_instructor_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($instructor->id); ?>"><?php echo e($instructor->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label for="edit_max_students" class="block text-sm font-medium text-gray-700 mb-1">الحد الأقصى للطلاب <span class="text-red-500">*</span></label>
                        <input type="number" name="max_students" id="edit_max_students" min="1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="edit_status" class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                        <select name="status" id="edit_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="active">نشط</option>
                            <option value="completed">منتهي</option>
                            <option value="cancelled">ملغي</option>
                        </select>
                    </div>
                    <div>
                        <label for="edit_location" class="block text-sm font-medium text-gray-700 mb-1">المكان</label>
                        <input type="text" name="location" id="edit_location" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="edit_class_time" class="block text-sm font-medium text-gray-700 mb-1">وقت الحصة</label>
                        <input type="datetime-local" name="class_time" id="edit_class_time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-1">الوصف</label>
                        <textarea name="description" id="edit_description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                </div>
                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium">إلغاء</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const courseId = <?php echo e($offlineCourse->id); ?>;
    const baseUrl = "<?php echo e(url('admin/offline-courses/' . $offlineCourse->id . '/groups')); ?>";

    function openEditModal(group) {
        document.getElementById('edit_name').value = group.name || '';
        document.getElementById('edit_instructor_id').value = group.instructor_id || '';
        document.getElementById('edit_max_students').value = group.max_students || '';
        document.getElementById('edit_status').value = group.status || 'active';
        document.getElementById('edit_location').value = group.location || '';
        document.getElementById('edit_description').value = group.description || '';
        if (group.class_time) {
            const d = new Date(group.class_time);
            document.getElementById('edit_class_time').value = d.toISOString().slice(0, 16);
        } else {
            document.getElementById('edit_class_time').value = '';
        }
        document.getElementById('editGroupForm').action = baseUrl + '/' + group.id;
        document.getElementById('editGroupModal').classList.remove('hidden');
        document.getElementById('editGroupModal').classList.add('flex');
    }

    function closeEditModal() {
        document.getElementById('editGroupModal').classList.add('hidden');
        document.getElementById('editGroupModal').classList.remove('flex');
    }

    document.getElementById('editGroupModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/offline-courses/groups/index.blade.php ENDPATH**/ ?>