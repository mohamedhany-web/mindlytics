

<?php $__env->startSection('title', 'اتفاقيات الموظفين - Mindlytics'); ?>
<?php $__env->startSection('header', 'اتفاقيات الموظفين'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <!-- الهيدر -->
    <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-500 via-emerald-600 to-teal-600 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-user-tie text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900">إدارة اتفاقيات الموظفين</h2>
                    <p class="text-sm text-slate-600 mt-1">إدارة عقود العمل والرواتب للموظفين</p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.employee-agreements.create')); ?>" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                <i class="fas fa-plus"></i>
                إضافة اتفاقية جديدة
            </a>
        </div>
    </section>

    <!-- إحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-emerald-200/50 hover:border-emerald-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(236, 253, 245, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-100/60 via-green-100/40 to-teal-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-emerald-400/20 to-transparent rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-emerald-800/80 mb-1">إجمالي الاتفاقيات</p>
                        <p class="text-4xl font-black bg-gradient-to-r from-emerald-700 via-green-600 to-teal-600 bg-clip-text text-transparent drop-shadow-sm"><?php echo e(number_format($stats['total'])); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 via-emerald-600 to-teal-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(16, 185, 129, 0.4);">
                        <i class="fas fa-handshake text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="dashboard-card rounded-2xl p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-100/60 via-sky-100/40 to-blue-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-blue-800/80 mb-1">اتفاقيات نشطة</p>
                        <p class="text-4xl font-black bg-gradient-to-r from-blue-700 via-blue-600 to-sky-600 bg-clip-text text-transparent drop-shadow-sm"><?php echo e(number_format($stats['active'])); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(59, 130, 246, 0.4);">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="dashboard-card rounded-2xl p-6 card-hover-effect relative overflow-hidden group border-2 border-amber-200/50 hover:border-amber-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 251, 235, 0.95) 50%, rgba(254, 243, 199, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-100/60 via-yellow-100/40 to-orange-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-amber-400/20 to-transparent rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-amber-800/80 mb-1">مسودات</p>
                        <p class="text-4xl font-black bg-gradient-to-r from-amber-700 via-yellow-600 to-orange-600 bg-clip-text text-transparent drop-shadow-sm"><?php echo e(number_format($stats['draft'])); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-amber-500 via-yellow-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(245, 158, 11, 0.4);">
                        <i class="fas fa-file-alt text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="dashboard-card rounded-2xl p-6 card-hover-effect relative overflow-hidden group border-2 border-purple-200/50 hover:border-purple-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(250, 245, 255, 0.95) 50%, rgba(243, 232, 255, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-100/60 via-violet-100/40 to-fuchsia-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-purple-400/20 to-transparent rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-purple-800/80 mb-1">إجمالي الرواتب</p>
                        <p class="text-4xl font-black bg-gradient-to-r from-purple-700 via-violet-600 to-fuchsia-600 bg-clip-text text-transparent drop-shadow-sm"><?php echo e(number_format($stats['total_salary'], 2)); ?></p>
                        <p class="text-xs font-medium text-purple-700/70 mt-1">ج.م</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 via-violet-500 to-fuchsia-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(168, 85, 247, 0.4);">
                        <i class="fas fa-money-bill-wave text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- البحث والفلترة -->
    <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-filter text-emerald-600"></i>
                البحث والفلترة
            </h3>
        </div>
        <div class="p-6">
            <form method="GET" action="<?php echo e(route('admin.employee-agreements.index')); ?>" id="filterForm" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">البحث</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" value="<?php echo e(htmlspecialchars(request('search') ?? '', ENT_QUOTES, 'UTF-8')); ?>" maxlength="255" placeholder="رقم الاتفاقية، اسم الموظف" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 pr-10 text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all" />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">الموظف</label>
                    <select name="employee_id" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        <option value="">جميع الموظفين</option>
                        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($employee->id); ?>" <?php echo e(request('employee_id') == $employee->id ? 'selected' : ''); ?>><?php echo e(htmlspecialchars($employee->name, ENT_QUOTES, 'UTF-8')); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">الحالة</label>
                    <select name="status" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        <option value="">جميع الحالات</option>
                        <option value="draft" <?php echo e(request('status') == 'draft' ? 'selected' : ''); ?>>مسودة</option>
                        <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>نشط</option>
                        <option value="suspended" <?php echo e(request('status') == 'suspended' ? 'selected' : ''); ?>>معلق</option>
                        <option value="terminated" <?php echo e(request('status') == 'terminated' ? 'selected' : ''); ?>>منتهي</option>
                        <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>مكتمل</option>
                    </select>
                </div>
                <div class="flex items-end gap-2 md:col-span-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-search"></i>
                        بحث
                    </button>
                    <?php if(request()->anyFilled(['search', 'employee_id', 'status'])): ?>
                    <a href="<?php echo e(route('admin.employee-agreements.index')); ?>" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors" title="مسح الفلتر">
                        <i class="fas fa-times"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </section>

    <!-- قائمة الاتفاقيات -->
    <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white flex items-center justify-between">
            <div>
                <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-file-contract text-emerald-600"></i>
                    قائمة الاتفاقيات
                </h3>
                <p class="text-sm text-slate-600 mt-1">
                    <span class="font-semibold text-emerald-600"><?php echo e($agreements->total()); ?></span> اتفاقية
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">رقم الاتفاقية</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">الموظف</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">الراتب</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">تاريخ البدء</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <?php $__empty_1 = true; $__currentLoopData = $agreements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agreement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50 transition-colors duration-200">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900"><?php echo e(htmlspecialchars($agreement->agreement_number ?? 'N/A', ENT_QUOTES, 'UTF-8')); ?></div>
                                <div class="text-xs text-slate-500 mt-1"><?php echo e(htmlspecialchars($agreement->title ?? '', ENT_QUOTES, 'UTF-8')); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center text-white font-bold text-sm shadow-md">
                                        <?php echo e(mb_substr($agreement->employee->name ?? '', 0, 1, 'UTF-8')); ?>

                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900"><?php echo e(htmlspecialchars($agreement->employee->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8')); ?></p>
                                        <p class="text-xs text-slate-500"><?php echo e(htmlspecialchars($agreement->employee->email ?? '-', ENT_QUOTES, 'UTF-8')); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900"><?php echo e(number_format($agreement->salary ?? 0, 2)); ?> ج.م</div>
                                <div class="text-xs text-slate-500">شهرياً</div>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                    $statusBadges = [
                                        'draft' => ['label' => 'مسودة', 'classes' => 'bg-slate-100 text-slate-700 border-slate-200'],
                                        'active' => ['label' => 'نشط', 'classes' => 'bg-emerald-100 text-emerald-700 border-emerald-200'],
                                        'suspended' => ['label' => 'معلق', 'classes' => 'bg-amber-100 text-amber-700 border-amber-200'],
                                        'terminated' => ['label' => 'منتهي', 'classes' => 'bg-rose-100 text-rose-700 border-rose-200'],
                                        'completed' => ['label' => 'مكتمل', 'classes' => 'bg-blue-100 text-blue-700 border-blue-200'],
                                    ];
                                    $status = $statusBadges[$agreement->status] ?? ['label' => $agreement->status, 'classes' => 'bg-slate-100 text-slate-700 border-slate-200'];
                                ?>
                                <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1 text-xs font-semibold border <?php echo e($status['classes']); ?>">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    <?php echo e($status['label']); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-700">
                                <div class="font-medium"><?php echo e($agreement->start_date ? $agreement->start_date->format('Y-m-d') : '-'); ?></div>
                                <?php if($agreement->end_date): ?>
                                    <div class="text-slate-500">حتى <?php echo e($agreement->end_date->format('Y-m-d')); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="<?php echo e(route('admin.employee-agreements.show', $agreement)); ?>" 
                                       class="w-9 h-9 flex items-center justify-center bg-emerald-50 hover:bg-emerald-100 text-emerald-600 rounded-lg transition-colors duration-200"
                                       title="عرض">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="<?php echo e(route('admin.employee-agreements.edit', $agreement)); ?>" 
                                       class="w-9 h-9 flex items-center justify-center bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-lg transition-colors duration-200"
                                       title="تعديل">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center">
                                        <i class="fas fa-user-tie text-slate-400 text-2xl"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900">لا توجد اتفاقيات</p>
                                        <p class="text-sm text-slate-600 mt-1">ابدأ بإنشاء اتفاقية جديدة</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($agreements->hasPages()): ?>
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                <?php echo e($agreements->appends(request()->query())->links()); ?>

            </div>
        <?php endif; ?>
    </section>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// حماية من XSS في البحث
const filterForm = document.getElementById('filterForm');
if (filterForm) {
    filterForm.addEventListener('submit', function(e) {
        // Sanitization يتم في الخادم
    });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/employee-agreements/index.blade.php ENDPATH**/ ?>