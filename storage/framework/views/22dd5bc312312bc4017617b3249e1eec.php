

<?php $__env->startSection('title', 'الإحالات - Mindlytics'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-user-friends text-sky-600 ml-3"></i>
                    إدارة الإحالات
                </h1>
                <p class="text-gray-600">عرض وإدارة جميع الإحالات والعمولات</p>
            </div>
            <a href="<?php echo e(route('admin.referral-programs.index')); ?>" 
               class="bg-gradient-to-l from-purple-600 to-purple-500 hover:from-purple-700 hover:to-purple-600 text-white px-5 py-2.5 rounded-lg font-medium transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2">
                <i class="fas fa-gift"></i>
                <span>برامج الإحالات</span>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <!-- إجمالي الإحالات -->
        <div class="bg-gradient-to-br from-sky-50 to-slate-50 rounded-2xl shadow-xl p-6 border border-sky-200 hover:shadow-2xl transition-all duration-300 card-hover-effect relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-sky-100/50 to-slate-100/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي الإحالات</p>
                        <p class="text-4xl font-black bg-gradient-to-r from-sky-600 via-sky-700 to-slate-600 bg-clip-text text-transparent"><?php echo e(number_format($stats['total'])); ?></p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <i class="fas fa-chart-line text-sky-500 ml-2"></i>
                    <span class="text-gray-600 font-medium">جميع الإحالات المسجلة</span>
                </div>
            </div>
        </div>

        <!-- مكتملة -->
        <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-2xl shadow-xl p-6 border border-emerald-200 hover:shadow-2xl transition-all duration-300 card-hover-effect relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-100/50 to-green-100/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 mb-1">مكتملة</p>
                        <p class="text-4xl font-black text-emerald-600"><?php echo e(number_format($stats['completed'])); ?></p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <i class="fas fa-gift text-emerald-500 ml-2"></i>
                    <span class="text-gray-600 font-medium">تم الحصول على المكافأة</span>
                </div>
            </div>
        </div>

        <!-- قيد الانتظار -->
        <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-2xl shadow-xl p-6 border border-amber-200 hover:shadow-2xl transition-all duration-300 card-hover-effect relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-100/50 to-yellow-100/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 mb-1">قيد الانتظار</p>
                        <p class="text-4xl font-black text-amber-600"><?php echo e(number_format($stats['pending'])); ?></p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-hourglass-half text-white text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <i class="fas fa-clock text-amber-500 ml-2"></i>
                    <span class="text-gray-600 font-medium">في انتظار الشراء</span>
                </div>
            </div>
        </div>

        <!-- إجمالي المكافآت -->
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl shadow-xl p-6 border border-purple-200 hover:shadow-2xl transition-all duration-300 card-hover-effect relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-100/50 to-pink-100/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي المكافآت</p>
                        <p class="text-3xl font-black text-purple-600"><?php echo e(number_format($stats['total_rewards'], 2)); ?> ج.م</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-gift text-white text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <i class="fas fa-coins text-purple-500 ml-2"></i>
                    <span class="text-gray-600 font-medium">مكافآت من الإحالات</span>
                </div>
            </div>
        </div>

        <!-- إجمالي الخصومات -->
        <div class="bg-gradient-to-br from-rose-50 to-pink-50 rounded-2xl shadow-xl p-6 border border-rose-200 hover:shadow-2xl transition-all duration-300 card-hover-effect relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-rose-100/50 to-pink-100/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي الخصومات</p>
                        <p class="text-3xl font-black text-rose-600"><?php echo e(number_format($stats['total_discounts'], 2)); ?> ج.م</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-rose-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-tag text-white text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <i class="fas fa-percent text-rose-500 ml-2"></i>
                    <span class="text-gray-600 font-medium">خصومات مطبقة</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-xl p-6 mb-8 border border-gray-200 hover:shadow-2xl transition-all duration-300 card-hover-effect">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200 bg-gradient-to-r from-sky-50 to-slate-50 -m-6 p-6 rounded-t-2xl">
            <i class="fas fa-filter text-sky-600"></i>
            <h3 class="text-lg font-black text-gray-900">فلترة وبحث الإحالات</h3>
        </div>
        <form method="GET" action="<?php echo e(route('admin.referrals.index')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                       placeholder="اسم المستخدم، رقم الهاتف، كود الإحالة..."
                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <select name="status" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                    <option value="">جميع الحالات</option>
                    <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>قيد الانتظار</option>
                    <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>مكتملة</option>
                    <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>ملغاة</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">البرنامج</label>
                <select name="program_id" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all">
                    <option value="">جميع البرامج</option>
                    <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($program->id); ?>" <?php echo e(request('program_id') == $program->id ? 'selected' : ''); ?>><?php echo e($program->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-gradient-to-l from-sky-600 to-sky-500 hover:from-sky-700 hover:to-sky-600 text-white px-5 py-3 rounded-xl font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-search ml-2"></i>
                    بحث
                </button>
                <a href="<?php echo e(route('admin.referrals.index')); ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-3 rounded-xl font-medium transition-colors">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Referrals List -->
    <?php if($referrals->count() > 0): ?>
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200 hover:shadow-2xl transition-all duration-300 card-hover-effect">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-sky-50 to-slate-50">
            <h3 class="text-lg font-black text-gray-900 flex items-center gap-2">
                <i class="fas fa-list text-sky-600"></i>
                قائمة الإحالات
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المحيل</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المحال</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">البرنامج</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">كود الإحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الخصم المطبق</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المكافأة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $referrals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $referral): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-sky-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                        <?php echo e(substr($referral->referrer->name ?? 'N', 0, 1)); ?>

                                    </div>
                                </div>
                                <div class="mr-4">
                                    <div class="text-sm font-medium text-gray-900"><?php echo e($referral->referrer->name ?? 'غير معروف'); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo e($referral->referrer->phone ?? 'N/A'); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-bold">
                                        <?php echo e(substr($referral->referred->name ?? 'N', 0, 1)); ?>

                                    </div>
                                </div>
                                <div class="mr-4">
                                    <div class="text-sm font-medium text-gray-900"><?php echo e($referral->referred->name ?? 'غير معروف'); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo e($referral->referred->phone ?? 'N/A'); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900"><?php echo e($referral->referralProgram->name ?? '-'); ?></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-mono font-bold text-gray-900"><?php echo e($referral->referral_code ?? $referral->code ?? '-'); ?></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                <?php if($referral->status == 'completed'): ?> bg-emerald-100 text-emerald-800
                                <?php elseif($referral->status == 'pending'): ?> bg-amber-100 text-amber-800
                                <?php else: ?> bg-red-100 text-red-800
                                <?php endif; ?>">
                                <?php if($referral->status == 'completed'): ?> مكتملة
                                <?php elseif($referral->status == 'pending'): ?> قيد الانتظار
                                <?php else: ?> ملغاة
                                <?php endif; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <?php echo e(number_format($referral->discount_amount ?? 0, 2)); ?> ج.م
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-emerald-600">
                            <?php echo e(number_format($referral->reward_amount ?? 0, 2)); ?> ج.م
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo e($referral->created_at->format('d/m/Y')); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="<?php echo e(route('admin.referrals.show', $referral)); ?>" 
                               class="text-sky-600 hover:text-sky-900">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <?php echo e($referrals->links()); ?>

        </div>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-2xl shadow-xl p-16 text-center border border-gray-200 hover:shadow-2xl transition-all duration-300 card-hover-effect">
        <div class="w-32 h-32 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
            <i class="fas fa-user-friends text-5xl text-gray-400"></i>
        </div>
        <p class="text-gray-600 text-xl font-semibold mb-2">لا توجد إحالات</p>
        <p class="text-gray-500 text-sm mb-6">ابدأ بإنشاء برنامج إحالات لتفعيل نظام الإحالات</p>
        <a href="<?php echo e(route('admin.referral-programs.index')); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-sky-600 to-sky-500 hover:from-sky-700 hover:to-sky-600 text-white rounded-xl font-medium transition-all duration-200 shadow-md hover:shadow-lg">
            <i class="fas fa-gift"></i>
            <span>إنشاء برنامج إحالات</span>
        </a>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\referrals\index.blade.php ENDPATH**/ ?>