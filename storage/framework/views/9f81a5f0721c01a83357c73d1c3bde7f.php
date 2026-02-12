

<?php $__env->startSection('title', 'الكورسات الأوفلاين'); ?>
<?php $__env->startSection('header', 'الكورسات الأوفلاين'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">الكورسات الأوفلاين</h1>
                <p class="text-gray-600 mt-1">إدارة وتنظيم الكورسات الأوفلاين في الأكاديمية</p>
            </div>
            <a href="<?php echo e(route('admin.offline-courses.create')); ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>
                إضافة كورس أوفلاين
            </a>
        </div>

        <!-- الفلاتر -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <form method="GET" action="<?php echo e(route('admin.offline-courses.index')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">البحث</label>
                    <input type="text" name="search" id="search" value="<?php echo e(request('search')); ?>" 
                           placeholder="البحث في عناوين الكورسات..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">جميع الحالات</option>
                        <option value="draft" <?php echo e(request('status') == 'draft' ? 'selected' : ''); ?>>مسودة</option>
                        <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>نشط</option>
                        <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>مكتمل</option>
                        <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>ملغي</option>
                    </select>
                </div>

                <div>
                    <label for="instructor_id" class="block text-sm font-medium text-gray-700 mb-1">المدرب</label>
                    <select name="instructor_id" id="instructor_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">جميع المدربين</option>
                        <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($instructor): ?>
                            <option value="<?php echo e($instructor->id); ?>" <?php echo e(request('instructor_id') == $instructor->id ? 'selected' : ''); ?>><?php echo e($instructor->name); ?></option>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-search mr-2"></i>
                        بحث
                    </button>
                    <?php if(request()->hasAny(['search', 'status', 'instructor_id'])): ?>
                        <a href="<?php echo e(route('admin.offline-courses.index')); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- الإحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-100/60 via-sky-100/40 to-blue-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي الكورسات</p>
                        <p class="text-3xl font-black text-gray-900"><?php echo e($stats['total']); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-book-reader text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-green-200/50 hover:border-green-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 253, 250, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-green-100/60 via-emerald-100/40 to-green-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-green-400/20 to-transparent rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">الكورسات النشطة</p>
                        <p class="text-3xl font-black text-green-700"><?php echo e($stats['active']); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-yellow-200/50 hover:border-yellow-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 251, 235, 0.95) 50%, rgba(254, 243, 199, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-yellow-100/60 via-amber-100/40 to-yellow-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-yellow-400/20 to-transparent rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">مسودات</p>
                        <p class="text-3xl font-black text-yellow-700"><?php echo e($stats['draft']); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-file-alt text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-gray-200/50 hover:border-gray-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(249, 250, 251, 0.95) 50%, rgba(243, 244, 246, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-gray-100/60 via-slate-100/40 to-gray-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-gray-400/20 to-transparent rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">مكتملة</p>
                        <p class="text-3xl font-black text-gray-800"><?php echo e($stats['completed']); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-gray-500 to-gray-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-check-double text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة الكورسات -->
    <?php if($courses->count() > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 border border-gray-200">
                <!-- هيدر البطاقة -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-purple-100/50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 truncate"><?php echo e($course->title); ?></h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            <?php if($course->status === 'active'): ?> bg-green-100 text-green-800
                            <?php elseif($course->status === 'draft'): ?> bg-yellow-100 text-yellow-800
                            <?php elseif($course->status === 'completed'): ?> bg-blue-100 text-blue-800
                            <?php else: ?> bg-red-100 text-red-800
                            <?php endif; ?>">
                            <?php if($course->status === 'active'): ?> نشط
                            <?php elseif($course->status === 'draft'): ?> مسودة
                            <?php elseif($course->status === 'completed'): ?> مكتمل
                            <?php else: ?> ملغي
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <!-- محتوى البطاقة -->
                <div class="px-6 py-4">
                    <?php if($course->description): ?>
                        <p class="text-sm text-gray-600 mb-4 line-clamp-2"><?php echo e(Str::limit($course->description, 100)); ?></p>
                    <?php endif; ?>

                    <div class="space-y-2">
                        <!-- المدرب -->
                        <div class="flex items-center text-sm">
                            <i class="fas fa-user-tie text-gray-400 w-4 ml-2"></i>
                            <span class="text-gray-600">المدرب:</span>
                            <span class="text-gray-900 mr-2 font-medium"><?php echo e($course->instructor?->name ?? '—'); ?></span>
                        </div>

                        <!-- المكان -->
                        <?php if($course->locationModel): ?>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-map-marker-alt text-gray-400 w-4 ml-2"></i>
                            <span class="text-gray-600">المكان:</span>
                            <span class="text-gray-900 mr-2 font-medium"><?php echo e($course->locationModel?->name ?? '—'); ?></span>
                        </div>
                        <?php elseif($course->location): ?>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-map-marker-alt text-gray-400 w-4 ml-2"></i>
                            <span class="text-gray-600">الموقع:</span>
                            <span class="text-gray-900 mr-2 font-medium"><?php echo e($course->location); ?></span>
                        </div>
                        <?php endif; ?>

                        <!-- الطلاب -->
                        <div class="flex items-center text-sm">
                            <i class="fas fa-users text-gray-400 w-4 ml-2"></i>
                            <span class="text-gray-600">الطلاب:</span>
                            <span class="text-gray-900 mr-2 font-medium"><?php echo e($course->current_students); ?> / <?php echo e($course->max_students); ?></span>
                        </div>

                        <!-- تاريخ البدء -->
                        <?php if($course->start_date): ?>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-calendar text-gray-400 w-4 ml-2"></i>
                            <span class="text-gray-600">تاريخ البدء:</span>
                            <span class="text-gray-900 mr-2 font-medium"><?php echo e($course->start_date->format('Y-m-d')); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-2">
                    <a href="<?php echo e(route('admin.offline-courses.show', $course)); ?>" 
                       class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg font-medium transition-colors">
                        <i class="fas fa-eye mr-1"></i>عرض
                    </a>
                    <a href="<?php echo e(route('admin.offline-courses.edit', $course)); ?>" 
                       class="px-3 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-white text-sm rounded-lg font-medium transition-colors">
                        <i class="fas fa-edit mr-1"></i>تعديل
                    </a>
                    <form action="<?php echo e(route('admin.offline-courses.destroy', $course)); ?>" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg font-medium transition-colors">
                            <i class="fas fa-trash mr-1"></i>حذف
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-xl shadow-lg p-12 text-center border border-gray-200">
            <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-chalkboard-teacher text-3xl text-gray-400"></i>
            </div>
            <p class="text-lg font-semibold text-gray-700 mb-2">لا توجد كورسات أوفلاين</p>
            <p class="text-sm text-gray-600 mb-6">ابدأ بإنشاء كورس أوفلاين جديد</p>
            <a href="<?php echo e(route('admin.offline-courses.create')); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-plus"></i>
                <span>إضافة كورس أوفلاين</span>
            </a>
        </div>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if($courses->hasPages()): ?>
    <div class="bg-white rounded-xl shadow-lg p-4 border border-gray-200">
        <?php echo e($courses->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/offline-courses/index.blade.php ENDPATH**/ ?>