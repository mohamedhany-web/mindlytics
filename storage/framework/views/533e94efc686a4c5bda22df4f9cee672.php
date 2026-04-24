<?php $__env->startSection('title', 'إدارة تسجيل الطلاب - الأونلاين'); ?>
<?php $__env->startSection('header', 'إدارة تسجيل الطلاب - الأونلاين'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- إجمالي التسجيلات -->
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي التسجيلات</p>
                        <p class="text-3xl font-black text-gray-900"><?php echo e(number_format($stats['total'])); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                </div>
                <p class="text-xs text-blue-600">جميع تسجيلات الطلاب</p>
            </div>
        </div>

        <!-- في الانتظار -->
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-yellow-200/50 hover:border-yellow-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 251, 235, 0.95) 50%, rgba(254, 243, 199, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">في الانتظار</p>
                        <p class="text-3xl font-black text-yellow-700"><?php echo e(number_format($stats['pending'])); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                </div>
                <p class="text-xs text-yellow-600">بحاجة للتفعيل</p>
            </div>
        </div>

        <!-- نشط -->
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-green-200/50 hover:border-green-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 253, 250, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">نشط</p>
                        <p class="text-3xl font-black text-green-700"><?php echo e(number_format($stats['active'])); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
                <p class="text-xs text-green-600">مفعل ويتعلم</p>
            </div>
        </div>

        <!-- مكتمل -->
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-purple-200/50 hover:border-purple-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(250, 245, 255, 0.95) 50%, rgba(243, 232, 255, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">مكتمل</p>
                        <p class="text-3xl font-black text-purple-700"><?php echo e(number_format($stats['completed'])); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-graduation-cap text-2xl"></i>
                    </div>
                </div>
                <p class="text-xs text-purple-600">أنهى الكورس</p>
            </div>
        </div>
    </div>

    <!-- تفعيل سريع بالبريد الإلكتروني -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <i class="fas fa-bolt text-amber-500"></i>
                تفعيل سريع للكورس عن طريق البريد الإلكتروني
            </h3>
            <p class="text-xs sm:text-sm text-gray-500">
                أدخل بريد الطالب واختر الكورس، وسيتم إنشاء/تفعيل التسجيل مباشرة مع إرسال بريد تفعيل.
            </p>
        </div>

        <form method="POST" action="<?php echo e(route('admin.online-enrollments.quick-activate')); ?>" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php echo csrf_field(); ?>
            <div>
                <label for="quick_email" class="block text-sm font-medium text-gray-700 mb-2">بريد الطالب</label>
                <input type="email" name="email" id="quick_email"
                       value="<?php echo e(old('email')); ?>"
                       placeholder="student@example.com"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <?php $__errorArgs = ['quick_activate_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label for="quick_course" class="block text-sm font-medium text-gray-700 mb-2">الكورس</label>
                <select name="advanced_course_id" id="quick_course"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">اختر الكورس</option>
                    <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($course->id); ?>" <?php echo e(old('advanced_course_id') == $course->id ? 'selected' : ''); ?>>
                            <?php echo e($course->title); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['advanced_course_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="flex items-end">
                <button type="submit"
                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-emerald-600 to-green-500 text-white rounded-lg hover:from-emerald-700 hover:to-green-600 shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-check-circle mr-2"></i>
                    تفعيل الآن
                </button>
            </div>
        </form>
    </div>

    <!-- البحث والفلترة -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">البحث والفلترة</h3>
            <a href="<?php echo e(route('admin.online-enrollments.create')); ?>" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>
                تسجيل طالب جديد
            </a>
        </div>
        
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                <input type="text" name="search" id="search" value="<?php echo e(request('search')); ?>" 
                       placeholder="البحث بالاسم أو رقم الهاتف..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">جميع الحالات</option>
                    <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>في الانتظار</option>
                    <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>نشط</option>
                    <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>مكتمل</option>
                    <option value="suspended" <?php echo e(request('status') == 'suspended' ? 'selected' : ''); ?>>معلق</option>
                </select>
            </div>

            <div>
                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">الكورس</label>
                <select name="course_id" id="course_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">جميع الكورسات</option>
                    <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($course->id); ?>" <?php echo e(request('course_id') == $course->id ? 'selected' : ''); ?>>
                            <?php echo e($course->title); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="flex gap-2 items-end">
                <button type="submit" class="btn-primary flex-1">
                    <i class="fas fa-search mr-2"></i>
                    بحث
                </button>
                <a href="<?php echo e(route('admin.online-enrollments.index')); ?>" class="btn-secondary">
                    <i class="fas fa-refresh"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- البحث السريع بالهاتف -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">البحث السريع بالهاتف</h3>
        <div class="flex gap-4">
            <div class="flex-1">
                <input type="text" id="quickSearchPhone" placeholder="أدخل رقم هاتف الطالب أو ولي الأمر..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button type="button" onclick="quickSearchByPhone()" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                <i class="fas fa-search mr-2"></i>
                بحث سريع
            </button>
        </div>
        <div id="quickSearchResult" class="mt-4 hidden">
            <!-- نتائج البحث السريع ستظهر هنا -->
        </div>
    </div>

    <!-- قائمة التسجيلات -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <?php if($enrollments->count() > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الطالب</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الكورس</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التقدم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ التسجيل</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                    <div class="mr-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($enrollment->student->name); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo e($enrollment->student->phone); ?></div>
                                        <?php if($enrollment->student->parent_phone): ?>
                                            <div class="text-xs text-gray-400">ولي الأمر: <?php echo e($enrollment->student->parent_phone); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?php echo e($enrollment->course->title); ?></div>
                                <div class="text-sm text-gray-500">
                                    <?php echo e($enrollment->course->academicYear->name ?? 'غير محدد'); ?> - 
                                    <?php echo e($enrollment->course->academicSubject->name ?? 'غير محدد'); ?>

                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    <?php echo e($enrollment->status_color == 'green' ? 'bg-green-100 text-green-800' : ''); ?>

                                    <?php echo e($enrollment->status_color == 'yellow' ? 'bg-yellow-100 text-yellow-800' : ''); ?>

                                    <?php echo e($enrollment->status_color == 'blue' ? 'bg-blue-100 text-blue-800' : ''); ?>

                                    <?php echo e($enrollment->status_color == 'red' ? 'bg-red-100 text-red-800' : ''); ?>">
                                    <?php echo e($enrollment->status_text); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo e($enrollment->progress); ?>%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600"><?php echo e($enrollment->progress); ?>%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo e($enrollment->enrolled_at->format('d/m/Y')); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <a href="<?php echo e(route('admin.online-enrollments.show', $enrollment)); ?>" 
                                       class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <?php if($enrollment->status === 'pending'): ?>
                                        <form method="POST" action="<?php echo e(route('admin.online-enrollments.activate', $enrollment)); ?>" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="text-green-600 hover:text-green-900" 
                                                    onclick="return confirm('هل تريد تفعيل هذا التسجيل؟')"
                                                    title="تفعيل التسجيل">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </form>
                                    <?php elseif($enrollment->status === 'active'): ?>
                                        <form method="POST" action="<?php echo e(route('admin.online-enrollments.deactivate', $enrollment)); ?>" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="text-orange-600 hover:text-orange-900" 
                                                    onclick="return confirm('هل تريد إيقاف هذا التسجيل؟')"
                                                    title="إيقاف التسجيل">
                                                <i class="fas fa-pause"></i>
                                            </button>
                                        </form>
                                    <?php elseif($enrollment->status === 'suspended'): ?>
                                        <form method="POST" action="<?php echo e(route('admin.online-enrollments.activate', $enrollment)); ?>" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="text-emerald-600 hover:text-emerald-900" 
                                                    onclick="return confirm('هل تريد إعادة تفعيل هذا التسجيل وفتح الكورس للطالب مرة أخرى؟')"
                                                    title="إعادة تفعيل التسجيل">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <form method="POST" action="<?php echo e(route('admin.online-enrollments.destroy', $enrollment)); ?>" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-600 hover:text-red-900" 
                                                onclick="return confirm('هل تريد حذف هذا التسجيل؟')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                <?php echo e($enrollments->appends(request()->query())->links()); ?>

            </div>
        <?php else: ?>
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد تسجيلات</h3>
                <p class="text-gray-500 mb-4">لم يتم العثور على تسجيلات تطابق معايير البحث</p>
                <a href="<?php echo e(route('admin.online-enrollments.create')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    إضافة أول تسجيل
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function quickSearchByPhone() {
    const phone = document.getElementById('quickSearchPhone').value.trim();
    const resultDiv = document.getElementById('quickSearchResult');
    
    if (!phone) {
        alert('يرجى إدخال رقم الهاتف');
        return;
    }
    
    // إظهار loader
    resultDiv.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-blue-600"></i> جاري البحث...</div>';
    resultDiv.classList.remove('hidden');
    
    fetch(`<?php echo e(route('admin.online-enrollments.search-by-phone')); ?>?phone=${encodeURIComponent(phone)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const student = data.student;
                resultDiv.innerHTML = `
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h4 class="font-medium text-green-900 mb-2">تم العثور على الطالب:</h4>
                        <div class="text-sm">
                            <p><strong>الاسم:</strong> ${student.name}</p>
                            <p><strong>هاتف الطالب:</strong> ${student.phone}</p>
                            ${student.parent_phone ? `<p><strong>هاتف ولي الأمر:</strong> ${student.parent_phone}</p>` : ''}
                        </div>
                        <div class="mt-3">
                            <a href="<?php echo e(route('admin.online-enrollments.create')); ?>?student_id=${student.id}" 
                               class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                <i class="fas fa-plus mr-1"></i>
                                تسجيل في كورس
                            </a>
                        </div>
                    </div>
                `;
            } else {
                resultDiv.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <h4 class="font-medium text-red-900">${data.error}</h4>
                    </div>
                `;
            }
        })
        .catch(error => {
            resultDiv.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h4 class="font-medium text-red-900">حدث خطأ في البحث</h4>
                </div>
            `;
        });
}

// البحث عند الضغط على Enter
document.getElementById('quickSearchPhone').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        quickSearchByPhone();
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/online-enrollments/index.blade.php ENDPATH**/ ?>