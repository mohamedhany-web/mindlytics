<?php $__env->startSection('title', 'لوحة الإدارة - Mindlytics'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- إجمالي المستخدمين -->
        <?php $usersMetric = $metrics['users'] ?? null; $usersTrend = $usersMetric['trend'] ?? null; ?>
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 w-full" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-100/60 via-sky-100/40 to-blue-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full opacity-80" aria-hidden="true"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-base sm:text-sm font-bold text-blue-800/80 mb-2 sm:mb-1">إجمالي المستخدمين</p>
                        <p class="text-5xl sm:text-4xl font-black bg-gradient-to-r from-blue-700 via-blue-600 to-sky-600 bg-clip-text text-transparent drop-shadow-sm"><?php echo e(number_format($usersMetric['total'] ?? 0)); ?></p>
                    </div>
                    <div class="card-icon w-20 h-20 sm:w-16 sm:h-16 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300 flex-shrink-0 mr-3 sm:mr-0" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #0284c7 100%); box-shadow: 0 8px 20px 0 rgba(59, 130, 246, 0.4);">
                        <i class="fas fa-users text-white text-2xl sm:text-xl"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-blue-700/70">مستخدمون جدد هذا الشهر: <span class="font-bold text-blue-800"><?php echo e(number_format($usersMetric['new_this_month'] ?? 0)); ?></span></p>
                <?php if($usersTrend): ?>
                    <?php
                        $diff = (int) round($usersTrend['difference']);
                        $percent = $usersTrend['percent'];
                        $positive = $diff >= 0;
                    ?>
                    <div class="mt-3 flex items-center gap-2 text-sm flex-wrap">
                        <span class="font-bold <?php echo e($positive ? 'text-emerald-600' : 'text-rose-500'); ?>">
                            <?php echo e($positive ? '+' : ''); ?><?php echo e(number_format($diff)); ?>

                        </span>
                        <span class="text-blue-700/70">عن الشهر الماضي</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold <?php echo e($positive ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200'); ?>">
                            <?php echo e($percent >= 0 ? '+' : ''); ?><?php echo e(number_format($percent, 1)); ?>%
                        </span>
                </div>
                <?php else: ?>
                    <p class="mt-3 text-sm text-blue-600/60">لا توجد بيانات مقارنة للشهر السابق.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- الطلاب -->
        <?php $studentsMetric = $metrics['students'] ?? null; $studentsTrend = $studentsMetric['trend'] ?? null; ?>
        <div class="dashboard-card rounded-2xl p-6 card-hover-effect relative overflow-hidden group border-2 border-emerald-200/50 hover:border-emerald-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(236, 253, 245, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-100/60 via-green-100/40 to-teal-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-emerald-400/15 to-transparent rounded-full opacity-80" aria-hidden="true"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-emerald-800/80 mb-1">الطلاب</p>
                        <p class="text-4xl font-black bg-gradient-to-r from-emerald-700 via-green-600 to-teal-600 bg-clip-text text-transparent drop-shadow-sm"><?php echo e(number_format($studentsMetric['total'] ?? 0)); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 via-green-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(16, 185, 129, 0.4);">
                        <i class="fas fa-user-graduate text-white text-xl"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-emerald-700/70">طلاب جدد هذا الشهر: <span class="font-bold text-emerald-800"><?php echo e(number_format($studentsMetric['new_this_month'] ?? 0)); ?></span></p>
                <?php if($studentsTrend): ?>
                    <?php
                        $diff = (int) round($studentsTrend['difference']);
                        $percent = $studentsTrend['percent'];
                        $positive = $diff >= 0;
                    ?>
                    <div class="mt-3 flex items-center gap-2 text-sm flex-wrap">
                        <span class="font-bold <?php echo e($positive ? 'text-emerald-600' : 'text-rose-500'); ?>">
                            <?php echo e($positive ? '+' : ''); ?><?php echo e(number_format($diff)); ?>

                        </span>
                        <span class="text-emerald-700/70">عن الشهر الماضي</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold <?php echo e($positive ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200'); ?>">
                            <?php echo e($percent >= 0 ? '+' : ''); ?><?php echo e(number_format($percent, 1)); ?>%
                        </span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- المدربين -->
        <?php $instructorsMetric = $metrics['instructors'] ?? null; $instructorsTrend = $instructorsMetric['trend'] ?? null; ?>
        <div class="dashboard-card rounded-2xl p-6 card-hover-effect relative overflow-hidden group border-2 border-indigo-200/50 hover:border-indigo-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(238, 242, 255, 0.95) 50%, rgba(224, 231, 255, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-100/60 via-purple-100/40 to-violet-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-indigo-400/15 to-transparent rounded-full opacity-80" aria-hidden="true"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-indigo-800/80 mb-1">المدربين</p>
                        <p class="text-4xl font-black bg-gradient-to-r from-indigo-700 via-purple-600 to-violet-600 bg-clip-text text-transparent drop-shadow-sm"><?php echo e(number_format($instructorsMetric['total'] ?? 0)); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 via-purple-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(99, 102, 241, 0.4);">
                        <i class="fas fa-user-tie text-white text-xl"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-indigo-700/70">مدربون جدد هذا الشهر: <span class="font-bold text-indigo-800"><?php echo e(number_format($instructorsMetric['new_this_month'] ?? 0)); ?></span></p>
                <?php if($instructorsTrend): ?>
                    <?php
                        $diff = (int) round($instructorsTrend['difference']);
                        $percent = $instructorsTrend['percent'];
                        $positive = $diff >= 0;
                    ?>
                    <div class="mt-3 flex items-center gap-2 text-sm flex-wrap">
                        <span class="font-bold <?php echo e($positive ? 'text-emerald-600' : 'text-rose-500'); ?>">
                            <?php echo e($positive ? '+' : ''); ?><?php echo e(number_format($diff)); ?>

                        </span>
                        <span class="text-indigo-700/70">عن الشهر الماضي</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold <?php echo e($positive ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200'); ?>">
                            <?php echo e($percent >= 0 ? '+' : ''); ?><?php echo e(number_format($percent, 1)); ?>%
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- الكورسات -->
        <?php $coursesMetric = $metrics['courses'] ?? null; $coursesTrend = $coursesMetric['trend'] ?? null; ?>
        <div class="dashboard-card rounded-2xl p-6 card-hover-effect relative overflow-hidden group border-2 border-amber-200/50 hover:border-amber-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 251, 235, 0.95) 50%, rgba(254, 243, 199, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-100/60 via-orange-100/40 to-yellow-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-amber-400/15 to-transparent rounded-full opacity-80" aria-hidden="true"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-amber-800/80 mb-1">الكورسات</p>
                        <p class="text-4xl font-black bg-gradient-to-r from-amber-700 via-orange-600 to-yellow-600 bg-clip-text text-transparent drop-shadow-sm"><?php echo e(number_format($coursesMetric['total'] ?? 0)); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-amber-500 via-orange-500 to-yellow-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(245, 158, 11, 0.4);">
                        <i class="fas fa-book text-white text-xl"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-amber-700/70">كورسات جديدة هذا الشهر: <span class="font-bold text-amber-800"><?php echo e(number_format($coursesMetric['new_this_month'] ?? 0)); ?></span></p>
                <?php if($coursesTrend): ?>
                    <?php
                        $diff = (int) round($coursesTrend['difference']);
                        $percent = $coursesTrend['percent'];
                        $positive = $diff >= 0;
                    ?>
                    <div class="mt-3 flex items-center gap-2 text-sm flex-wrap">
                        <span class="font-bold <?php echo e($positive ? 'text-emerald-600' : 'text-rose-500'); ?>">
                            <?php echo e($positive ? '+' : ''); ?><?php echo e(number_format($diff)); ?>

                        </span>
                        <span class="text-amber-700/70">عن الشهر الماضي</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold <?php echo e($positive ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200'); ?>">
                            <?php echo e($percent >= 0 ? '+' : ''); ?><?php echo e(number_format($percent, 1)); ?>%
                        </span>
                    </div>
                <?php endif; ?>
                </div>
            </div>
        </div>

    <!-- إحصائيات مالية -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- إجمالي الإيرادات -->
        <?php $revenueMetric = $metrics['monthly_revenue'] ?? null; $revenueTrend = $revenueMetric['trend'] ?? null; ?>
        <div class="dashboard-card rounded-2xl p-6 card-hover-effect relative overflow-hidden group border-2 border-emerald-200/50 hover:border-emerald-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(236, 253, 245, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-100/60 via-green-100/40 to-teal-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-emerald-400/15 to-transparent rounded-full opacity-80" aria-hidden="true"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-emerald-800/80 mb-1">إجمالي الإيرادات</p>
                        <p class="text-3xl font-black bg-gradient-to-r from-emerald-700 via-green-600 to-teal-600 bg-clip-text text-transparent drop-shadow-sm"><?php echo e(number_format($stats['total_revenue'] ?? 0, 2)); ?> <span class="text-lg">ج.م</span></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 via-green-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(16, 185, 129, 0.4);">
                        <i class="fas fa-money-bill-wave text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

        <!-- إيرادات الشهر -->
        <div class="dashboard-card rounded-2xl p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-100/60 via-sky-100/40 to-cyan-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-400/20 to-transparent rounded-full opacity-80" aria-hidden="true"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-blue-800/80 mb-1">إيرادات الشهر</p>
                        <p class="text-3xl font-black bg-gradient-to-r from-blue-700 via-sky-600 to-cyan-600 bg-clip-text text-transparent drop-shadow-sm"><?php echo e(number_format($revenueMetric['current'] ?? 0, 2)); ?> <span class="text-lg">ج.م</span></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 via-sky-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(59, 130, 246, 0.4);">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                </div>
                <?php if($revenueTrend): ?>
                    <?php
                        $diff = $revenueTrend['difference'];
                        $percent = $revenueTrend['percent'];
                        $positive = $diff >= 0;
                    ?>
                    <div class="mt-3 flex items-center gap-2 text-sm flex-wrap">
                        <span class="font-bold <?php echo e($positive ? 'text-emerald-600' : 'text-rose-500'); ?>">
                            <?php echo e($positive ? '+' : ''); ?><?php echo e(number_format($diff, 2)); ?> ج.م
                        </span>
                        <span class="text-blue-700/70">عن الشهر الماضي</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold <?php echo e($positive ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200'); ?>">
                            <?php echo e($percent >= 0 ? '+' : ''); ?><?php echo e(number_format($percent, 1)); ?>%
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- الفواتير المعلقة -->
        <?php $pendingMetric = $metrics['pending_invoices'] ?? null; $pendingTrend = $pendingMetric['trend'] ?? null; ?>
        <div class="dashboard-card rounded-2xl p-6 card-hover-effect relative overflow-hidden group border-2 border-yellow-200/50 hover:border-yellow-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 251, 235, 0.95) 50%, rgba(254, 243, 199, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-yellow-100/60 via-amber-100/40 to-orange-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-yellow-400/15 to-transparent rounded-full opacity-80" aria-hidden="true"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-yellow-800/80 mb-1">فواتير معلقة</p>
                        <p class="text-3xl font-black bg-gradient-to-r from-yellow-700 via-amber-600 to-orange-600 bg-clip-text text-transparent drop-shadow-sm"><?php echo e(number_format($pendingMetric['total'] ?? 0)); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 via-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(245, 158, 11, 0.4);">
                        <i class="fas fa-file-invoice text-white text-xl"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-yellow-700/70">فواتير جديدة هذا الشهر: <span class="font-bold text-yellow-800"><?php echo e(number_format($pendingMetric['new_this_month'] ?? 0)); ?></span></p>
                <?php if($pendingTrend): ?>
                    <?php
                        $diff = (int) round($pendingTrend['difference']);
                        $percent = $pendingTrend['percent'];
                        $positive = $diff >= 0;
                    ?>
                    <div class="mt-3 flex items-center gap-2 text-sm flex-wrap">
                        <span class="font-bold <?php echo e($positive ? 'text-yellow-600' : 'text-rose-500'); ?>">
                            <?php echo e($positive ? '+' : ''); ?><?php echo e(number_format($diff)); ?>

                        </span>
                        <span class="text-yellow-700/70">عن الشهر الماضي</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold <?php echo e($positive ? 'bg-yellow-100 text-yellow-700 border border-yellow-200' : 'bg-rose-100 text-rose-700 border border-rose-200'); ?>">
                            <?php echo e($percent >= 0 ? '+' : ''); ?><?php echo e(number_format($percent, 1)); ?>%
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- التسجيلات النشطة -->
        <?php $enrollmentsMetric = $metrics['enrollments'] ?? null; $enrollmentsTrend = $enrollmentsMetric['trend'] ?? null; ?>
        <div class="dashboard-card rounded-2xl p-6 card-hover-effect relative overflow-hidden group border-2 border-purple-200/50 hover:border-purple-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(250, 245, 255, 0.95) 50%, rgba(243, 232, 255, 0.9) 100%);">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-100/60 via-pink-100/40 to-fuchsia-50/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-purple-400/15 to-transparent rounded-full opacity-80" aria-hidden="true"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-purple-800/80 mb-1">التسجيلات النشطة</p>
                        <p class="text-3xl font-black bg-gradient-to-r from-purple-700 via-pink-600 to-fuchsia-600 bg-clip-text text-transparent drop-shadow-sm"><?php echo e(number_format($enrollmentsMetric['total'] ?? 0)); ?></p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 via-pink-500 to-fuchsia-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(168, 85, 247, 0.4);">
                        <i class="fas fa-user-check text-white text-xl"></i>
                    </div>
                </div>
                <p class="text-xs font-medium text-purple-700/70">تسجيلات جديدة هذا الشهر: <span class="font-bold text-purple-800"><?php echo e(number_format($enrollmentsMetric['new_this_month'] ?? 0)); ?></span></p>
                <?php if($enrollmentsTrend): ?>
                    <?php
                        $diff = (int) round($enrollmentsTrend['difference']);
                        $percent = $enrollmentsTrend['percent'];
                        $positive = $diff >= 0;
                    ?>
                    <div class="mt-3 flex items-center gap-2 text-sm flex-wrap">
                        <span class="font-bold <?php echo e($positive ? 'text-emerald-600' : 'text-rose-500'); ?>">
                            <?php echo e($positive ? '+' : ''); ?><?php echo e(number_format($diff)); ?>

                        </span>
                        <span class="text-purple-700/70">عن الشهر الماضي</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold <?php echo e($positive ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200'); ?>">
                            <?php echo e($percent >= 0 ? '+' : ''); ?><?php echo e(number_format($percent, 1)); ?>%
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- الأنشطة الأخيرة -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- سجل النشاطات -->
        <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="px-6 py-4 section-header rounded-t-2xl" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 50%, rgba(2, 132, 199, 0.08) 100%); border-bottom: 2px solid rgba(59, 130, 246, 0.3);">
                <h3 class="text-xl font-black bg-gradient-to-r from-blue-700 via-blue-600 to-sky-600 bg-clip-text text-transparent">
                    <i class="fas fa-history text-blue-600 ml-2"></i>
                    آخر النشاطات
                </h3>
            </div>
            <div class="p-6">
                <?php if(isset($stats['recent_activities']) && $stats['recent_activities']->count() > 0): ?>
                    <div class="space-y-4">
                        <?php $__currentLoopData = $stats['recent_activities']->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center space-x-3 space-x-reverse p-3 rounded-xl hover:bg-blue-50/80 transition-all duration-300 border border-blue-100/50">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 via-blue-600 to-sky-600 rounded-full flex items-center justify-center shadow-md">
                                        <i class="fas fa-history text-white text-xs"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-blue-900">
                                        <?php echo e($activity->user->name ?? 'مستخدم محذوف'); ?>

                                    </p>
                                    <p class="text-xs text-blue-700/70">
                                        <?php echo e($activity->action); ?> - <?php echo e($activity->created_at->diffForHumans()); ?>

                                    </p>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div class="mt-6 pt-4 border-t border-blue-200/50">
                        <a href="<?php echo e(route('admin.activity-log')); ?>" class="text-blue-600 hover:text-blue-800 text-sm font-bold inline-flex items-center gap-2 transition-colors">
                            عرض جميع النشاطات
                            <i class="fas fa-arrow-left text-xs"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <p class="text-blue-600/60 text-center py-8">لا توجد أنشطة بعد</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- آخر محاولات الامتحانات -->
        <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-indigo-200/50 hover:border-indigo-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(238, 242, 255, 0.95) 50%, rgba(224, 231, 255, 0.9) 100%);">
            <div class="px-6 py-4 section-header rounded-t-2xl" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.15) 0%, rgba(79, 70, 229, 0.1) 50%, rgba(67, 56, 202, 0.08) 100%); border-bottom: 2px solid rgba(99, 102, 241, 0.3);">
                <h3 class="text-xl font-black bg-gradient-to-r from-indigo-700 via-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    <i class="fas fa-clipboard-check text-indigo-600 ml-2"></i>
                    آخر محاولات الامتحانات
                </h3>
            </div>
            <div class="p-6">
                <?php if(isset($stats['recent_exam_attempts']) && $stats['recent_exam_attempts']->count() > 0): ?>
                    <div class="space-y-4">
                        <?php $__currentLoopData = $stats['recent_exam_attempts']->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attempt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center justify-between p-3 rounded-xl hover:bg-indigo-50/80 transition-all duration-300 border border-indigo-100/50">
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-indigo-900"><?php echo e($attempt->student->name ?? 'طالب محذوف'); ?></p>
                                    <p class="text-xs text-indigo-700/70"><?php echo e($attempt->exam->title ?? 'امتحان محذوف'); ?></p>
                                </div>
                                <div class="text-left">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border
                                        <?php echo e($attempt->score >= 80 ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : ($attempt->score >= 60 ? 'bg-yellow-100 text-yellow-700 border-yellow-200' : 'bg-rose-100 text-rose-700 border-rose-200')); ?>">
                                        <?php echo e($attempt->score); ?>%
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <p class="text-indigo-600/60 text-center py-8">لا توجد محاولات امتحانات بعد</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- آخر المستخدمين والكورسات -->
    <?php if(isset($recent_users) || isset($recent_courses)): ?>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- آخر المستخدمين -->
        <?php if(isset($recent_users)): ?>
        <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="p-6 section-header rounded-t-2xl" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 50%, rgba(2, 132, 199, 0.08) 100%); border-bottom: 2px solid rgba(59, 130, 246, 0.3);">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-black bg-gradient-to-r from-blue-700 via-blue-600 to-sky-600 bg-clip-text text-transparent">
                        <i class="fas fa-users text-blue-600 ml-2"></i>
                        آخر المستخدمين
                    </h3>
                    <a href="<?php echo e(route('admin.users.index')); ?>" class="text-sm font-bold text-blue-600 hover:text-blue-700 transition-colors inline-flex items-center gap-2">
                        عرض الكل
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <?php $__currentLoopData = $recent_users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="list-item-card flex items-center gap-4 p-3 rounded-xl group border border-blue-100/50 hover:border-blue-200/70" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(240, 249, 255, 0.85) 100%);">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 via-blue-600 to-sky-600 rounded-full flex items-center justify-center text-white font-bold shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <?php echo e(substr($user->name, 0, 1)); ?>

                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-blue-900 truncate"><?php echo e($user->name); ?></p>
                            <p class="text-xs text-blue-700/70"><?php echo e($user->phone ?? $user->email); ?></p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border
                                <?php if($user->role === 'student'): ?> bg-emerald-100 text-emerald-700 border-emerald-200
                                <?php elseif($user->role === 'instructor'): ?> bg-indigo-100 text-indigo-700 border-indigo-200
                                <?php elseif($user->role === 'super_admin'): ?> bg-rose-100 text-rose-700 border-rose-200
                                <?php else: ?> bg-gray-100 text-gray-700 border-gray-200 <?php endif; ?>">
                                <?php if($user->role === 'student'): ?> طالب
                                <?php elseif($user->role === 'instructor'): ?> مدرب
                                <?php elseif($user->role === 'super_admin'): ?> مدير عام
                                <?php else: ?> غير محدد <?php endif; ?>
                            </span>
                            <p class="text-xs text-blue-600/60 mt-1"><?php echo e($user->created_at->diffForHumans()); ?></p>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- آخر الكورسات -->
        <?php if(isset($recent_courses)): ?>
        <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-amber-200/50 hover:border-amber-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 251, 235, 0.95) 50%, rgba(254, 243, 199, 0.9) 100%);">
            <div class="p-6 section-header rounded-t-2xl" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(217, 119, 6, 0.1) 50%, rgba(180, 83, 9, 0.08) 100%); border-bottom: 2px solid rgba(245, 158, 11, 0.3);">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-black bg-gradient-to-r from-amber-700 via-orange-600 to-yellow-600 bg-clip-text text-transparent">
                        <i class="fas fa-book text-amber-600 ml-2"></i>
                        آخر الكورسات
                    </h3>
                    <a href="<?php echo e(route('admin.advanced-courses.index')); ?>" class="text-sm font-bold text-amber-600 hover:text-amber-700 transition-colors inline-flex items-center gap-2">
                        عرض الكل
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <?php $__empty_1 = true; $__currentLoopData = $recent_courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="list-item-card flex items-start gap-4 p-3 rounded-xl group border border-amber-100/50 hover:border-amber-200/70" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 251, 235, 0.85) 100%);">
                        <div class="w-14 h-14 bg-gradient-to-br from-amber-500 via-orange-500 to-yellow-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-book text-white text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-amber-900 truncate"><?php echo e($course->title); ?></p>
                            <p class="text-xs text-amber-700/70"><?php echo e($course->academicSubject->name ?? 'غير محدد'); ?></p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border
                                <?php if($course->is_active): ?> bg-emerald-100 text-emerald-700 border-emerald-200
                                <?php else: ?> bg-gray-100 text-gray-700 border-gray-200 <?php endif; ?>">
                                <?php if($course->is_active): ?> نشط
                                <?php else: ?> غير نشط <?php endif; ?>
                            </span>
                            <p class="text-xs text-amber-600/60 mt-1"><?php echo e($course->created_at->diffForHumans()); ?></p>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-8 text-amber-600/60">
                        <i class="fas fa-book text-3xl mb-2"></i>
                        <p>لا توجد كورسات بعد</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- الفواتير والمدفوعات -->
    <?php if((isset($pending_invoices) && $pending_invoices->count() > 0) || (isset($recent_payments) && $recent_payments->count() > 0)): ?>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- الفواتير المعلقة -->
        <?php if(isset($pending_invoices) && $pending_invoices->count() > 0): ?>
        <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-yellow-200/50 hover:border-yellow-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 251, 235, 0.95) 50%, rgba(254, 243, 199, 0.9) 100%);">
            <div class="p-6 section-header rounded-t-2xl" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(217, 119, 6, 0.1) 50%, rgba(180, 83, 9, 0.08) 100%); border-bottom: 2px solid rgba(245, 158, 11, 0.3);">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-black bg-gradient-to-r from-yellow-700 via-amber-600 to-orange-600 bg-clip-text text-transparent">
                        <i class="fas fa-file-invoice text-yellow-600 ml-2"></i>
                        الفواتير المعلقة
                    </h3>
                    <a href="#" class="text-sm font-bold text-yellow-600 hover:text-yellow-700 transition-colors inline-flex items-center gap-2">
                        عرض الكل
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <?php $__currentLoopData = $pending_invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="list-item-card flex items-start gap-4 p-3 rounded-xl group border border-yellow-100/50 hover:border-yellow-200/70" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 251, 235, 0.85) 100%);">
                        <div class="w-14 h-14 bg-gradient-to-br from-yellow-500 via-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-file-invoice text-white text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-yellow-900 truncate"><?php echo e($invoice->invoice_number ?? 'غير محدد'); ?></p>
                            <p class="text-xs text-yellow-700/70"><?php echo e($invoice->user->name ?? 'غير محدد'); ?></p>
                            <p class="text-xs font-bold text-yellow-700"><?php echo e(number_format($invoice->total_amount ?? 0, 2)); ?> ج.م</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">
                                معلق
                            </span>
                            <p class="text-xs text-yellow-600/60 mt-1"><?php echo e($invoice->created_at->diffForHumans()); ?></p>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- المدفوعات الأخيرة -->
        <?php if(isset($recent_payments) && $recent_payments->count() > 0): ?>
        <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-emerald-200/50 hover:border-emerald-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(236, 253, 245, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
            <div class="p-6 section-header rounded-t-2xl" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.1) 50%, rgba(4, 120, 87, 0.08) 100%); border-bottom: 2px solid rgba(16, 185, 129, 0.3);">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-black bg-gradient-to-r from-emerald-700 via-green-600 to-teal-600 bg-clip-text text-transparent">
                        <i class="fas fa-money-bill-wave text-emerald-600 ml-2"></i>
                        المدفوعات الأخيرة
                    </h3>
                    <a href="#" class="text-sm font-bold text-emerald-600 hover:text-emerald-700 transition-colors inline-flex items-center gap-2">
                        عرض الكل
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <?php $__currentLoopData = $recent_payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="list-item-card flex items-start gap-4 p-3 rounded-xl group border border-emerald-100/50 hover:border-emerald-200/70" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(236, 253, 245, 0.85) 100%);">
                        <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 via-green-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-money-bill-wave text-white text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-emerald-900 truncate"><?php echo e($payment->payment_number ?? 'غير محدد'); ?></p>
                            <p class="text-xs text-emerald-700/70"><?php echo e($payment->user->name ?? 'غير محدد'); ?></p>
                            <p class="text-xs font-bold text-emerald-700"><?php echo e(number_format($payment->amount ?? 0, 2)); ?> ج.م</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                مكتمل
                            </span>
                            <p class="text-xs text-emerald-600/60 mt-1"><?php echo e($payment->paid_at ? $payment->paid_at->diffForHumans() : $payment->created_at->diffForHumans()); ?></p>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- أزرار سريعة -->
    <div class="dashboard-card rounded-2xl p-6 card-hover-effect border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
        <div class="pb-4 mb-6 border-b-2 border-blue-200/50 flex items-center justify-between">
            <h3 class="text-xl font-black bg-gradient-to-r from-blue-700 via-blue-600 to-sky-600 bg-clip-text text-transparent">
                <i class="fas fa-bolt text-blue-600 ml-2"></i>
                إجراءات سريعة
            </h3>
            <p class="text-xs font-medium text-blue-700/70">روابط مباشرة للمهام اليومية بناءً على بيانات النظام الحالية</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
            <?php $__currentLoopData = ($quickActions ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($action['route']); ?>"
                   class="flex flex-col items-center gap-4 p-5 bg-gradient-to-br <?php echo e($action['background']); ?> rounded-2xl border-2 border-blue-200/30 hover:border-blue-300/50 shadow-lg hover:shadow-2xl transition-all duration-300 card-hover-effect group" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(240, 249, 255, 0.9) 100%);">
                    <div class="w-14 h-14 bg-gradient-to-br <?php echo e($action['icon_background']); ?> rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300" style="box-shadow: 0 8px 20px 0 rgba(59, 130, 246, 0.3);">
                        <i class="<?php echo e($action['icon']); ?> text-white text-xl"></i>
                    </div>
                    <div class="text-center space-y-2">
                        <p class="text-sm font-bold text-blue-900"><?php echo e($action['title']); ?></p>
                        <?php
                            $actionCount = $action['count'] ?? 0;
                        ?>
                        <p class="text-3xl font-black <?php echo e($action['count_class'] ?? 'bg-gradient-to-r from-blue-700 via-blue-600 to-sky-600 bg-clip-text text-transparent'); ?>">
                            <?php echo e(number_format($actionCount)); ?>

                        </p>
                        <?php if(!empty($action['meta'])): ?>
                            <p class="text-xs font-medium <?php echo e($action['meta_class'] ?? 'text-blue-700/70'); ?>"><?php echo e($action['meta']); ?></p>
                        <?php endif; ?>
                        <span class="inline-flex items-center justify-center gap-2 text-xs font-bold text-blue-600 group-hover:text-blue-700 transition-colors">
                            <?php echo e($action['cta']); ?>

                            <i class="fas fa-arrow-left text-[10px]"></i>
                        </span>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if(empty($quickActions)): ?>
                <div class="col-span-full text-center text-blue-600/60 text-sm py-4">
                    لا توجد مهام عاجلة حالياً.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>