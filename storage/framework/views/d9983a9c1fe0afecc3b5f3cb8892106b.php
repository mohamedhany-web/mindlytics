<?php $__env->startSection('title', 'لوحة الإدارة - Mindlytics'); ?>
<?php $__env->startSection('header', 'لوحة الإدارة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $direction = $chartDashboard['direction'] ?? [];
    $monthly = $chartDashboard['monthly'] ?? [];
    $dirMetrics = $direction['metrics'] ?? [];
    $dirStatus = $direction['status'] ?? 'neutral';
    $dirBannerClass = match ($dirStatus) {
        'growth' => 'border-emerald-200 bg-emerald-50',
        'stable' => 'border-sky-200 bg-sky-50',
        'decline' => 'border-rose-200 bg-rose-50',
        default => 'border-slate-200 bg-slate-50',
    };
    $dirIconClass = match ($dirStatus) {
        'growth' => 'text-emerald-600',
        'stable' => 'text-sky-600',
        'decline' => 'text-rose-600',
        default => 'text-slate-600',
    };
    $walletAvailable = (float) ($stats['total_wallet_balance'] ?? 0);
    $walletPending = (float) ($stats['total_wallet_pending'] ?? 0);
    $walletTotal = $walletAvailable + $walletPending;
    $revenueArr = $monthly['revenue'] ?? [];
    $latestRevenue = $revenueArr !== [] ? (float) $revenueArr[array_key_last($revenueArr)] : 0;
    $enrollArr = $monthly['totalEnrollments'] ?? [];
    $latestEnrollments = $enrollArr !== [] ? (int) $enrollArr[array_key_last($enrollArr)] : 0;

    $statCards = [
        ['label' => 'الطلاب', 'value' => number_format($metrics['students']['total'] ?? 0), 'icon' => 'fas fa-user-graduate', 'theme' => 'emerald', 'desc' => '+' . number_format($metrics['students']['new_this_month'] ?? 0) . ' هذا الشهر', 'trend' => $metrics['students']['trend'] ?? null],
        ['label' => 'تسجيلات نشطة', 'value' => number_format($metrics['enrollments']['total'] ?? 0), 'icon' => 'fas fa-user-check', 'theme' => 'violet', 'desc' => '+' . number_format($metrics['enrollments']['new_this_month'] ?? 0) . ' هذا الشهر', 'trend' => $metrics['enrollments']['trend'] ?? null],
        ['label' => 'إيراد الشهر', 'value' => number_format($metrics['monthly_revenue']['current'] ?? 0, 0), 'icon' => 'fas fa-chart-line', 'theme' => 'sky', 'desc' => 'ج.م — مدفوعات مكتملة', 'trend' => $metrics['monthly_revenue']['trend'] ?? null],
        ['label' => 'إجمالي الإيراد', 'value' => number_format($stats['total_revenue'] ?? 0, 0), 'icon' => 'fas fa-money-bill-wave', 'theme' => 'green', 'desc' => 'ج.م — تراكمي', 'trend' => null],
        ['label' => 'الكورسات', 'value' => number_format($metrics['courses']['total'] ?? 0), 'icon' => 'fas fa-book', 'theme' => 'amber', 'desc' => number_format($stats['published_courses'] ?? 0) . ' منشور', 'trend' => $metrics['courses']['trend'] ?? null],
        ['label' => 'المدربون', 'value' => number_format($metrics['instructors']['total'] ?? 0), 'icon' => 'fas fa-chalkboard-teacher', 'theme' => 'indigo', 'desc' => '+' . number_format($metrics['instructors']['new_this_month'] ?? 0) . ' هذا الشهر', 'trend' => $metrics['instructors']['trend'] ?? null],
        ['label' => 'المستخدمون', 'value' => number_format($metrics['users']['total'] ?? 0), 'icon' => 'fas fa-users', 'theme' => 'blue', 'desc' => '+' . number_format($metrics['users']['new_this_month'] ?? 0) . ' هذا الشهر', 'trend' => $metrics['users']['trend'] ?? null],
        ['label' => 'فواتير معلّقة', 'value' => number_format($metrics['pending_invoices']['total'] ?? 0), 'icon' => 'fas fa-file-invoice', 'theme' => 'rose', 'desc' => number_format($walletTotal, 0) . ' ج.م في المحافظ', 'trend' => $metrics['pending_invoices']['trend'] ?? null],
    ];

    $cardThemes = [
        'emerald' => ['border' => 'border-emerald-200/70', 'bg' => 'from-white via-white to-emerald-50/60', 'label' => 'text-emerald-800/80', 'value' => 'from-emerald-700 to-teal-600', 'icon' => 'from-emerald-500 to-teal-600', 'desc' => 'text-emerald-700/70'],
        'violet'  => ['border' => 'border-violet-200/70', 'bg' => 'from-white via-white to-violet-50/60', 'label' => 'text-violet-800/80', 'value' => 'from-violet-700 to-purple-600', 'icon' => 'from-violet-500 to-purple-600', 'desc' => 'text-violet-700/70'],
        'sky'     => ['border' => 'border-sky-200/70', 'bg' => 'from-white via-white to-sky-50/60', 'label' => 'text-sky-800/80', 'value' => 'from-sky-700 to-blue-600', 'icon' => 'from-sky-500 to-blue-600', 'desc' => 'text-sky-700/70'],
        'green'   => ['border' => 'border-green-200/70', 'bg' => 'from-white via-white to-green-50/60', 'label' => 'text-green-800/80', 'value' => 'from-green-700 to-emerald-600', 'icon' => 'from-green-500 to-emerald-600', 'desc' => 'text-green-700/70'],
        'amber'   => ['border' => 'border-amber-200/70', 'bg' => 'from-white via-white to-amber-50/60', 'label' => 'text-amber-800/80', 'value' => 'from-amber-700 to-orange-600', 'icon' => 'from-amber-500 to-orange-500', 'desc' => 'text-amber-700/70'],
        'indigo'  => ['border' => 'border-indigo-200/70', 'bg' => 'from-white via-white to-indigo-50/60', 'label' => 'text-indigo-800/80', 'value' => 'from-indigo-700 to-violet-600', 'icon' => 'from-indigo-500 to-violet-600', 'desc' => 'text-indigo-700/70'],
        'blue'    => ['border' => 'border-blue-200/70', 'bg' => 'from-white via-white to-blue-50/60', 'label' => 'text-blue-800/80', 'value' => 'from-blue-700 to-sky-600', 'icon' => 'from-blue-500 to-sky-600', 'desc' => 'text-blue-700/70'],
        'rose'    => ['border' => 'border-rose-200/70', 'bg' => 'from-white via-white to-rose-50/60', 'label' => 'text-rose-800/80', 'value' => 'from-rose-700 to-red-600', 'icon' => 'from-rose-500 to-red-500', 'desc' => 'text-rose-700/70'],
    ];
?>

<div class="space-y-6">
    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-university"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">لوحة الأكاديمية</h2>
                    <p class="text-xs text-slate-600">نظرة شاملة على الإيراد، التسجيلات، الطلاب، والاتجاهات.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('admin.accounting.insights')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-chart-bar text-sky-600"></i>
                    مؤشرات المحاسبة
                </a>
                <a href="<?php echo e(route('admin.sales.insights.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-chart-pie text-emerald-600"></i>
                    Insights المبيعات
                </a>
                <a href="<?php echo e(route('admin.wallets.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-wallet text-amber-600"></i>
                    المحافظ
                </a>
            </div>
        </div>
    </section>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $theme = $cardThemes[$card['theme'] ?? 'blue'] ?? $cardThemes['blue'];
                $trend = $card['trend'] ?? null;
                $pct = $trend['percent'] ?? null;
                $trendUp = $pct !== null && $pct >= 0;
            ?>
            <div class="dashboard-stat-card rounded-2xl border-2 <?php echo e($theme['border']); ?> bg-gradient-to-br <?php echo e($theme['bg']); ?> p-5 shadow-lg">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold <?php echo e($theme['label']); ?> mb-1"><?php echo e($card['label']); ?></p>
                        <p class="text-3xl font-black bg-gradient-to-r <?php echo e($theme['value']); ?> bg-clip-text text-transparent tabular-nums"><?php echo e($card['value']); ?></p>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br <?php echo e($theme['icon']); ?> flex items-center justify-center text-white shadow-md flex-shrink-0">
                        <i class="<?php echo e($card['icon']); ?> text-lg"></i>
                    </div>
                </div>
                <p class="text-xs font-medium <?php echo e($theme['desc']); ?> truncate"><?php echo e($card['desc']); ?></p>
                <?php if($pct !== null): ?>
                    <p class="text-xs font-bold mt-2 <?php echo e($trendUp ? 'text-emerald-700' : 'text-rose-700'); ?>">
                        <?php echo e($trendUp ? '↑' : '↓'); ?> <?php echo e(number_format(abs((float) $pct), 1)); ?>% عن الشهر السابق
                    </p>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <section class="rounded-2xl border shadow-lg overflow-hidden <?php echo e($dirBannerClass); ?>">
        <div class="px-4 py-4 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center <?php echo e($dirIconClass); ?>">
                    <i class="fas fa-compass text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-600">اتجاه الأكاديمية</p>
                    <p class="text-lg font-black text-slate-900"><?php echo e($direction['label'] ?? '—'); ?></p>
                    <p class="text-sm text-slate-700 mt-1 max-w-3xl"><?php echo e($direction['summary'] ?? ''); ?></p>
                    <p class="text-[11px] text-slate-500 mt-2">
                        <?php echo e($direction['previous_month_label'] ?? '—'); ?> ← <?php echo e($direction['current_month_label'] ?? '—'); ?>

                    </p>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 min-w-0">
                <?php $__currentLoopData = $dirMetrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $pct = $m['pct'] ?? null;
                        $isMoney = $key === 'revenue';
                        $trendUp = $pct !== null && $pct > 0;
                        $trendClass = $trendUp ? 'text-emerald-700' : ($pct < 0 ? 'text-rose-700' : 'text-slate-600');
                    ?>
                    <div class="rounded-xl bg-white/80 border border-white p-3 shadow-sm">
                        <p class="text-[10px] font-semibold text-slate-500 truncate"><?php echo e($m['label'] ?? ''); ?></p>
                        <p class="text-lg font-black text-slate-900 tabular-nums">
                            <?php echo e($isMoney ? number_format((float) ($m['current'] ?? 0), 0) : number_format((int) ($m['current'] ?? 0))); ?>

                        </p>
                        <?php if($pct !== null): ?>
                            <p class="text-[10px] font-bold <?php echo e($trendClass); ?>"><?php echo e($trendUp ? '↑' : ($pct < 0 ? '↓' : '→')); ?> <?php echo e(number_format(abs((float) $pct), 1)); ?>%</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>

    
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden xl:col-span-2">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">الإيراد والتسجيلات — آخر 6 أشهر</h3>
                <p class="text-xs text-slate-600">إيراد المدفوعات + تسجيلات أونلاين/أوفلاين.</p>
            </div>
            <div class="p-4">
                <div class="relative w-full" style="height: 360px;">
                    <canvas id="chartAcademyMain"></canvas>
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">الإيراد الشهري</h3>
            </div>
            <div class="p-4">
                <div class="relative w-full" style="height: 300px;">
                    <canvas id="chartRevenue"></canvas>
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">نمو الطلاب والطلبات</h3>
            </div>
            <div class="p-4">
                <div class="relative w-full" style="height: 300px;">
                    <canvas id="chartStudentsOrders"></canvas>
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">الإيراد اليومي — 14 يوم</h3>
            </div>
            <div class="p-4">
                <div class="relative w-full" style="height: 280px;">
                    <canvas id="chartDailyRevenue"></canvas>
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">توزيع التسجيلات</h3>
                <p class="text-xs text-slate-600">أونلاين vs أوفلاين (نشطة).</p>
            </div>
            <div class="p-4">
                <div class="relative w-full" style="height: 280px;">
                    <canvas id="chartEnrollmentMix"></canvas>
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">توزيع المستخدمين</h3>
            </div>
            <div class="p-4">
                <div class="relative w-full" style="height: 280px;">
                    <canvas id="chartUserRoles"></canvas>
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">نشاط النظام — 7 أيام</h3>
            </div>
            <div class="p-4">
                <div class="relative w-full" style="height: 280px;">
                    <canvas id="chartWeeklyActivity"></canvas>
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden xl:col-span-2">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">أكثر الكورسات تسجيلاً</h3>
            </div>
            <div class="p-4">
                <div class="relative w-full" style="height: 300px;">
                    <canvas id="chartTopCourses"></canvas>
                </div>
            </div>
        </section>
    </div>

    
    <?php if(!empty($quickActions)): ?>
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-bolt text-amber-500"></i>
                مهام تحتاج انتباه
            </h3>
        </div>
        <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
            <?php $__currentLoopData = $quickActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($action['route']); ?>" class="rounded-xl border border-slate-200 bg-white p-4 hover:bg-slate-50 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                            <i class="<?php echo e($action['icon']); ?> text-sm"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-800 truncate"><?php echo e($action['title']); ?></p>
                    </div>
                    <p class="text-2xl font-black text-slate-900 tabular-nums"><?php echo e(number_format($action['count'] ?? 0)); ?></p>
                    <?php if(!empty($action['meta'])): ?>
                        <p class="text-[10px] text-slate-500 mt-1 truncate"><?php echo e($action['meta']); ?></p>
                    <?php endif; ?>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>
    <?php endif; ?>

    
    <?php if(isset($branchesOperationalOverview) && $branchesOperationalOverview->isNotEmpty()): ?>
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-layer-group text-indigo-600"></i>
                نظرة على الفروع
            </h3>
            <p class="text-xs text-slate-600 mt-1">بيانات منفصلة لكل فرع عبر branch_id.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 border-b border-slate-200">
                        <th class="px-4 py-3 text-right font-semibold">الفرع</th>
                        <th class="px-4 py-3 text-center font-semibold">مستخدمون</th>
                        <th class="px-4 py-3 text-center font-semibold">كورسات</th>
                        <th class="px-4 py-3 text-center font-semibold">أوفلاين</th>
                        <th class="px-4 py-3 text-center font-semibold">طلبات</th>
                        <th class="px-4 py-3 text-center font-semibold">تسجيلات</th>
                        <th class="px-4 py-3 text-center font-semibold">حالة</th>
                        <th class="px-4 py-3 text-center font-semibold">عرض</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $branchesOperationalOverview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $isCentral = isset($centralAcademyBranchId) && (int) $bRow->id === (int) $centralAcademyBranchId; ?>
                        <tr class="hover:bg-slate-50 <?php echo e($isCentral ? 'bg-indigo-50/40' : ''); ?>">
                            <td class="px-4 py-3 font-semibold">
                                <?php echo e($bRow->name); ?>

                                <?php if($isCentral): ?>
                                    <span class="mr-1 text-[10px] font-bold px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-800">أساسي</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-center tabular-nums"><?php echo e(number_format($bRow->users_count)); ?></td>
                            <td class="px-4 py-3 text-center tabular-nums"><?php echo e(number_format($bRow->advanced_courses_count)); ?></td>
                            <td class="px-4 py-3 text-center tabular-nums"><?php echo e(number_format($bRow->offline_courses_count)); ?></td>
                            <td class="px-4 py-3 text-center tabular-nums"><?php echo e(number_format($bRow->orders_count)); ?></td>
                            <td class="px-4 py-3 text-center tabular-nums"><?php echo e(number_format($bRow->student_course_enrollments_count)); ?></td>
                            <td class="px-4 py-3 text-center">
                                <?php if($bRow->is_active): ?>
                                    <span class="text-xs font-semibold text-emerald-700">نشط</span>
                                <?php else: ?>
                                    <span class="text-xs text-slate-500">موقوف</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="<?php echo e(route('admin.branches.show', $bRow)); ?>" class="text-xs font-bold text-sky-600 hover:text-sky-800">تفاصيل</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php endif; ?>

    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h3 class="text-base font-black text-slate-900">آخر النشاطات</h3>
                <a href="<?php echo e(route('admin.activity-log')); ?>" class="text-xs font-semibold text-sky-600">عرض الكل</a>
            </div>
            <div class="p-4">
                <?php if(isset($stats['recent_activities']) && $stats['recent_activities']->count() > 0): ?>
                    <ul class="space-y-2">
                        <?php $__currentLoopData = $stats['recent_activities']->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 text-sm">
                                <div class="w-8 h-8 rounded-lg bg-sky-100 flex items-center justify-center text-sky-600 flex-shrink-0">
                                    <i class="fas fa-history text-xs"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-semibold text-slate-900 truncate"><?php echo e($activity->user->name ?? '—'); ?></p>
                                    <p class="text-xs text-slate-500 truncate"><?php echo e($activity->action); ?> — <?php echo e($activity->created_at->diffForHumans()); ?></p>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php else: ?>
                    <p class="text-sm text-slate-500 text-center py-6">لا توجد أنشطة.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">آخر محاولات الامتحانات</h3>
            </div>
            <div class="p-4">
                <?php if(isset($stats['recent_exam_attempts']) && $stats['recent_exam_attempts']->count() > 0): ?>
                    <ul class="space-y-2">
                        <?php $__currentLoopData = $stats['recent_exam_attempts']->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attempt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex items-center justify-between gap-3 p-2 rounded-lg hover:bg-slate-50 text-sm">
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-900 truncate"><?php echo e($attempt->student->name ?? '—'); ?></p>
                                    <p class="text-xs text-slate-500 truncate"><?php echo e($attempt->exam->title ?? '—'); ?></p>
                                </div>
                                <span class="inline-flex rounded-md px-2 py-0.5 text-xs font-bold <?php echo e($attempt->score >= 80 ? 'bg-emerald-100 text-emerald-700' : ($attempt->score >= 60 ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700')); ?>">
                                    <?php echo e($attempt->score); ?>%
                                </span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php else: ?>
                    <p class="text-sm text-slate-500 text-center py-6">لا توجد محاولات.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <?php if(isset($recent_users)): ?>
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h3 class="text-sm font-black text-slate-900">آخر المستخدمين</h3>
                <a href="<?php echo e(route('admin.users.index')); ?>" class="text-xs font-semibold text-sky-600">الكل</a>
            </div>
            <ul class="divide-y divide-slate-100">
                <?php $__currentLoopData = $recent_users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="px-4 py-3 hover:bg-slate-50">
                        <p class="text-sm font-semibold text-slate-900 truncate"><?php echo e($user->name); ?></p>
                        <p class="text-xs text-slate-500"><?php echo e($user->created_at->diffForHumans()); ?></p>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </section>
        <?php endif; ?>

        <?php if(isset($recent_courses)): ?>
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h3 class="text-sm font-black text-slate-900">آخر الكورسات</h3>
                <a href="<?php echo e(route('admin.advanced-courses.index')); ?>" class="text-xs font-semibold text-sky-600">الكل</a>
            </div>
            <ul class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $recent_courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li class="px-4 py-3 hover:bg-slate-50">
                        <p class="text-sm font-semibold text-slate-900 truncate"><?php echo e($course->title); ?></p>
                        <p class="text-xs text-slate-500"><?php echo e($course->created_at->diffForHumans()); ?></p>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="px-4 py-8 text-center text-sm text-slate-500">لا كورسات.</li>
                <?php endif; ?>
            </ul>
        </section>
        <?php endif; ?>

        <?php if(isset($recent_payments) && $recent_payments->count() > 0): ?>
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-sm font-black text-slate-900">آخر المدفوعات</h3>
            </div>
            <ul class="divide-y divide-slate-100">
                <?php $__currentLoopData = $recent_payments->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="px-4 py-3 hover:bg-slate-50 flex justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-900 truncate"><?php echo e($payment->user->name ?? '—'); ?></p>
                            <p class="text-xs text-slate-500"><?php echo e($payment->paid_at?->diffForHumans() ?? $payment->created_at->diffForHumans()); ?></p>
                        </div>
                        <span class="text-sm font-bold text-emerald-700 tabular-nums"><?php echo e(number_format($payment->amount ?? 0, 0)); ?></span>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </section>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    const dash = <?php echo json_encode($chartDashboard, 15, 512) ?>;
    const weekly = <?php echo json_encode($weeklyActivityChart ?? ['labels' => [], 'values' => []], 512) ?>;
    const monthly = dash.monthly || {};
    const daily = dash.daily_revenue || {};
    const mix = dash.enrollment_mix || {};
    const roles = dash.user_roles || {};
    const topCourses = dash.top_courses || {};

    const palette = {
        emerald: 'rgb(16, 185, 129)', emeraldSoft: 'rgba(16, 185, 129, 0.15)',
        sky: 'rgb(14, 165, 233)', skySoft: 'rgba(14, 165, 233, 0.15)',
        indigo: 'rgb(99, 102, 241)', indigoSoft: 'rgba(99, 102, 241, 0.15)',
        amber: 'rgb(245, 158, 11)', violet: 'rgb(139, 92, 246)',
        rose: 'rgb(244, 63, 94)', slate: 'rgb(100, 116, 139)',
    };
    const doughnutColors = [palette.emerald, palette.sky, palette.indigo, palette.amber, palette.violet, palette.rose, palette.slate];
    const baseOptions = {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 300 },
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } },
    };

    function hasData(arr) {
        return Array.isArray(arr) && arr.some(v => Number(v) > 0);
    }

    function emptyMsg(id, msg) {
        const c = document.getElementById(id);
        if (c) c.parentElement.innerHTML = '<p class="text-sm text-slate-500 text-center py-16">' + msg + '</p>';
    }

    if (hasData(monthly.revenue) || hasData(monthly.totalEnrollments)) {
        new Chart(document.getElementById('chartAcademyMain'), {
            type: 'line',
            data: {
                labels: monthly.labels || [],
                datasets: [
                    { label: 'الإيراد (ج.م)', data: monthly.revenue || [], borderColor: palette.emerald, backgroundColor: palette.emeraldSoft, tension: 0.35, fill: true, yAxisID: 'y1' },
                    { label: 'تسجيلات أونلاين', data: monthly.onlineEnrollments || [], borderColor: palette.sky, tension: 0.35, yAxisID: 'y' },
                    { label: 'تسجيلات أوفلاين', data: monthly.offlineEnrollments || [], borderColor: palette.indigo, tension: 0.35, yAxisID: 'y' },
                    { label: 'إجمالي التسجيلات', data: monthly.totalEnrollments || [], borderColor: palette.amber, borderDash: [5, 4], tension: 0.35, yAxisID: 'y' },
                ],
            },
            options: {
                ...baseOptions,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: { beginAtZero: true, position: 'right', title: { display: true, text: 'تسجيلات' } },
                    y1: { beginAtZero: true, position: 'left', grid: { drawOnChartArea: false }, title: { display: true, text: 'ج.م' } },
                },
            },
        });
    } else emptyMsg('chartAcademyMain', 'لا بيانات كافية لعرض الاتجاه.');

    if (hasData(monthly.revenue)) {
        new Chart(document.getElementById('chartRevenue'), {
            type: 'bar',
            data: { labels: monthly.labels || [], datasets: [{ label: 'الإيراد', data: monthly.revenue || [], backgroundColor: 'rgba(16, 185, 129, 0.75)', borderRadius: 8 }] },
            options: { ...baseOptions, plugins: { ...baseOptions.plugins, legend: { display: false } }, scales: { y: { beginAtZero: true } } },
        });
    } else emptyMsg('chartRevenue', 'لا إيراد مسجّل.');

    if (hasData(monthly.newStudents) || hasData(monthly.orders)) {
        new Chart(document.getElementById('chartStudentsOrders'), {
            type: 'line',
            data: {
                labels: monthly.labels || [],
                datasets: [
                    { label: 'طلاب جدد', data: monthly.newStudents || [], borderColor: palette.emerald, tension: 0.35, fill: false },
                    { label: 'طلبات', data: monthly.orders || [], borderColor: palette.sky, tension: 0.35, fill: false },
                ],
            },
            options: { ...baseOptions, scales: { y: { beginAtZero: true } } },
        });
    } else emptyMsg('chartStudentsOrders', 'لا بيانات طلاب/طلبات.');

    if (hasData(daily.revenue)) {
        new Chart(document.getElementById('chartDailyRevenue'), {
            type: 'bar',
            data: { labels: daily.labels || [], datasets: [{ label: 'إيراد يومي', data: daily.revenue || [], backgroundColor: 'rgba(14, 165, 233, 0.7)', borderRadius: 6 }] },
            options: { ...baseOptions, plugins: { ...baseOptions.plugins, legend: { display: false } }, scales: { y: { beginAtZero: true } } },
        });
    } else emptyMsg('chartDailyRevenue', 'لا مدفوعات في آخر 14 يوم.');

    if (hasData(mix.values)) {
        new Chart(document.getElementById('chartEnrollmentMix'), {
            type: 'doughnut',
            data: { labels: mix.labels || [], datasets: [{ data: mix.values || [], backgroundColor: doughnutColors, borderWidth: 2 }] },
            options: baseOptions,
        });
    } else emptyMsg('chartEnrollmentMix', 'لا تسجيلات نشطة.');

    if (hasData(roles.values)) {
        new Chart(document.getElementById('chartUserRoles'), {
            type: 'doughnut',
            data: { labels: roles.labels || [], datasets: [{ data: roles.values || [], backgroundColor: doughnutColors, borderWidth: 2 }] },
            options: baseOptions,
        });
    } else emptyMsg('chartUserRoles', 'لا مستخدمين.');

    if (hasData(weekly.values)) {
        new Chart(document.getElementById('chartWeeklyActivity'), {
            type: 'line',
            data: { labels: weekly.labels || [], datasets: [{ label: 'أحداث', data: weekly.values || [], borderColor: palette.indigo, backgroundColor: palette.indigoSoft, tension: 0.35, fill: true }] },
            options: { ...baseOptions, plugins: { ...baseOptions.plugins, legend: { display: false } }, scales: { y: { beginAtZero: true } } },
        });
    } else emptyMsg('chartWeeklyActivity', 'لا نشاط في آخر 7 أيام.');

    if (hasData(topCourses.values)) {
        new Chart(document.getElementById('chartTopCourses'), {
            type: 'bar',
            data: { labels: topCourses.labels || [], datasets: [{ label: 'تسجيلات نشطة', data: topCourses.values || [], backgroundColor: 'rgba(99, 102, 241, 0.75)', borderRadius: 6 }] },
            options: { indexAxis: 'y', ...baseOptions, plugins: { ...baseOptions.plugins, legend: { display: false } }, scales: { x: { beginAtZero: true } } },
        });
    } else emptyMsg('chartTopCourses', 'لا تسجيلات على الكورسات.');
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>