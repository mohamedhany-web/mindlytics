<?php $__env->startSection('title', 'التقارير المحاسبية - Mindlytics'); ?>
<?php $__env->startSection('header', 'التقارير المحاسبية'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full space-y-6">
    <!-- فلترة الفترة الزمنية -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">التقارير المحاسبية</h2>
                    <p class="text-sm text-slate-500 mt-1">تقارير شاملة عن جميع العمليات المالية</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="<?php echo e(route('admin.accounting.reports.export', array_merge(request()->all(), ['type' => 'all']))); ?>" 
                       class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                        <i class="fas fa-file-excel"></i>
                        تصدير Excel شامل
                    </a>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                            <i class="fas fa-download"></i>
                            تصدير محدد
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition
                             class="absolute left-0 mt-2 w-56 rounded-2xl bg-white shadow-xl border border-slate-200 z-50">
                            <div class="p-2 space-y-1">
                                <a href="<?php echo e(route('admin.accounting.reports.export', array_merge(request()->all(), ['type' => 'summary']))); ?>" 
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-lg">
                                    <i class="fas fa-chart-pie w-4"></i>
                                    الملخص المالي
                                </a>
                                <a href="<?php echo e(route('admin.accounting.reports.export', array_merge(request()->all(), ['type' => 'invoices']))); ?>" 
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-lg">
                                    <i class="fas fa-file-invoice w-4"></i>
                                    الفواتير
                                </a>
                                <a href="<?php echo e(route('admin.accounting.reports.export', array_merge(request()->all(), ['type' => 'payments']))); ?>" 
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-lg">
                                    <i class="fas fa-money-bill-wave w-4"></i>
                                    المدفوعات
                                </a>
                                <a href="<?php echo e(route('admin.accounting.reports.export', array_merge(request()->all(), ['type' => 'transactions']))); ?>" 
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-lg">
                                    <i class="fas fa-exchange-alt w-4"></i>
                                    المعاملات
                                </a>
                                <a href="<?php echo e(route('admin.accounting.reports.export', array_merge(request()->all(), ['type' => 'expenses']))); ?>" 
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-lg">
                                    <i class="fas fa-receipt w-4"></i>
                                    المصروفات
                                </a>
                                <a href="<?php echo e(route('admin.accounting.reports.export', array_merge(request()->all(), ['type' => 'wallets']))); ?>" 
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-lg">
                                    <i class="fas fa-wallet w-4"></i>
                                    المحافظ
                                </a>
                                <a href="<?php echo e(route('admin.accounting.reports.export', array_merge(request()->all(), ['type' => 'orders']))); ?>" 
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-lg">
                                    <i class="fas fa-shopping-cart w-4"></i>
                                    الطلبات
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <form method="GET" action="<?php echo e(route('admin.accounting.reports')); ?>" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-2">الفترة الزمنية</label>
                        <select name="period" class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400" onchange="this.form.submit()">
                            <option value="day" <?php echo e($period == 'day' ? 'selected' : ''); ?>>اليوم</option>
                            <option value="week" <?php echo e($period == 'week' ? 'selected' : ''); ?>>هذا الأسبوع</option>
                            <option value="month" <?php echo e($period == 'month' ? 'selected' : ''); ?>>هذا الشهر</option>
                            <option value="year" <?php echo e($period == 'year' ? 'selected' : ''); ?>>هذه السنة</option>
                            <option value="all" <?php echo e($period == 'all' ? 'selected' : ''); ?>>الكل</option>
                            <option value="custom" <?php echo e($period == 'custom' ? 'selected' : ''); ?>>مخصص</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-2">من تاريخ</label>
                        <input type="date" name="start_date" value="<?php echo e($startDate ? $startDate->format('Y-m-d') : ''); ?>" class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-2">إلى تاريخ</label>
                        <input type="date" name="end_date" value="<?php echo e($endDate ? $endDate->format('Y-m-d') : ''); ?>" class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-400" />
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                            <i class="fas fa-filter"></i>
                            تطبيق الفلترة
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- الإحصائيات الرئيسية -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h2 class="text-2xl font-bold text-slate-900">ملخص مالي شامل</h2>
            <p class="text-sm text-slate-500 mt-1">من <?php echo e($startDate->format('Y-m-d')); ?> إلى <?php echo e($endDate->format('Y-m-d')); ?></p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-5 sm:p-8">
            <!-- إجمالي الإيرادات -->
            <div class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-green-50 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-emerald-600">إجمالي الإيرادات</p>
                        <p class="mt-3 text-3xl font-black text-emerald-700"><?php echo e(number_format($stats['total_revenue'], 2)); ?> ج.م</p>
                    </div>
                    <div class="w-14 h-14 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-arrow-down text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- إجمالي المصروفات -->
            <div class="rounded-2xl border border-rose-200 bg-gradient-to-br from-rose-50 to-red-50 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-rose-600">إجمالي المصروفات</p>
                        <p class="mt-3 text-3xl font-black text-rose-700"><?php echo e(number_format($stats['total_expenses'], 2)); ?> ج.م</p>
                    </div>
                    <div class="w-14 h-14 bg-rose-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-arrow-up text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- الربح الصافي -->
            <div class="rounded-2xl border border-sky-200 bg-gradient-to-br from-sky-50 to-blue-50 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-sky-600">الربح الصافي</p>
                        <p class="mt-3 text-3xl font-black <?php echo e($stats['net_profit'] >= 0 ? 'text-sky-700' : 'text-rose-700'); ?>">
                            <?php echo e(number_format($stats['net_profit'], 2)); ?> ج.م
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-sky-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- نسبة الربحية -->
            <div class="rounded-2xl border border-purple-200 bg-gradient-to-br from-purple-50 to-indigo-50 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-purple-600">نسبة الربحية</p>
                        <p class="mt-3 text-3xl font-black text-purple-700">
                            <?php if($stats['total_revenue'] > 0): ?>
                                <?php echo e(number_format(($stats['net_profit'] / $stats['total_revenue']) * 100, 2)); ?>%
                            <?php else: ?>
                                0%
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-purple-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-percentage text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- صف إضافي: محافظ المنصة + الطلبات -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-5 sm:p-8 pt-0">
            <div class="rounded-2xl border border-violet-200 bg-gradient-to-br from-violet-50 to-purple-50 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-violet-600">محافظ المنصة</p>
                        <p class="mt-3 text-2xl font-black text-violet-700"><?php echo e($stats['wallet_stats']['total_wallets']); ?></p>
                        <p class="text-xs text-slate-600 mt-1"><?php echo e($stats['wallet_stats']['active_wallets']); ?> نشطة</p>
                    </div>
                    <div class="w-12 h-12 bg-violet-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-wallet text-white"></i>
                    </div>
                </div>
                <p class="text-xs text-slate-500">إجمالي الأرصدة: <strong><?php echo e(number_format($stats['wallet_stats']['total_balance'], 2)); ?> ج.م</strong></p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-orange-50 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-amber-600">الطلبات (الفترة)</p>
                        <p class="mt-3 text-2xl font-black text-amber-700"><?php echo e($stats['order_stats']['total_orders']); ?></p>
                        <p class="text-xs text-slate-600 mt-1"><?php echo e($stats['order_stats']['approved_orders']); ?> معتمدة · <?php echo e($stats['order_stats']['pending_orders']); ?> معلقة</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-shopping-cart text-white"></i>
                    </div>
                </div>
                <p class="text-xs text-slate-500">مبالغ معتمدة: <strong><?php echo e(number_format($stats['order_stats']['approved_amount'], 2)); ?> ج.م</strong></p>
            </div>
        </div>
    </section>

    <!-- الإحصائيات التفصيلية -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- الفواتير -->
        <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
                <h3 class="text-lg font-bold text-slate-900">الفواتير</h3>
            </div>
            <div class="p-5 sm:p-8 space-y-4">
                <div class="flex items-center justify-between py-3 border-b border-slate-100">
                    <span class="text-sm text-slate-600">إجمالي الفواتير</span>
                    <span class="text-lg font-bold text-slate-900"><?php echo e(number_format($stats['total_invoices'])); ?></span>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-slate-100">
                    <span class="text-sm text-slate-600">مدفوعة</span>
                    <span class="text-lg font-bold text-emerald-600"><?php echo e(number_format($stats['paid_invoices'])); ?></span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-sm text-slate-600">معلقة</span>
                    <span class="text-lg font-bold text-amber-600"><?php echo e(number_format($stats['pending_invoices'])); ?></span>
                </div>
            </div>
        </section>

        <!-- المدفوعات -->
        <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
                <h3 class="text-lg font-bold text-slate-900">المدفوعات</h3>
            </div>
            <div class="p-5 sm:p-8 space-y-4">
                <div class="flex items-center justify-between py-3 border-b border-slate-100">
                    <span class="text-sm text-slate-600">إجمالي المدفوعات</span>
                    <span class="text-lg font-bold text-slate-900"><?php echo e(number_format($stats['total_payments'])); ?></span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-sm text-slate-600">مكتملة</span>
                    <span class="text-lg font-bold text-emerald-600"><?php echo e(number_format($stats['completed_payments'])); ?></span>
                </div>
            </div>
        </section>

        <!-- المعاملات -->
        <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
                <h3 class="text-lg font-bold text-slate-900">المعاملات</h3>
            </div>
            <div class="p-5 sm:p-8 space-y-4">
                <div class="flex items-center justify-between py-3">
                    <span class="text-sm text-slate-600">إجمالي المعاملات</span>
                    <span class="text-lg font-bold text-slate-900"><?php echo e(number_format($stats['total_transactions'])); ?></span>
                </div>
            </div>
        </section>
    </div>

    <!-- أقسام التقارير — عرض كل قسم في صفحة منفصلة -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900">عرض التفاصيل حسب القسم</h3>
            <p class="text-sm text-slate-500 mt-1">كل قسم في صفحة منفصلة مع ترقيم الصفحات وتصدير Excel</p>
        </div>
        <div class="p-5 sm:p-8">
            <?php $query = request()->only(['period', 'start_date', 'end_date']); ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="<?php echo e(route('admin.accounting.reports.invoices', $query)); ?>" class="flex items-center gap-4 p-4 rounded-2xl border border-slate-200 hover:border-sky-300 hover:bg-sky-50/50 transition-colors">
                    <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center"><i class="fas fa-file-invoice text-sky-600"></i></div>
                    <div>
                        <p class="font-semibold text-slate-900">الفواتير</p>
                        <p class="text-xs text-slate-500"><?php echo e($stats['total_invoices']); ?> فاتورة في الفترة</p>
                    </div>
                    <i class="fas fa-chevron-left text-slate-400 mr-auto"></i>
                </a>
                <a href="<?php echo e(route('admin.accounting.reports.payments', $query)); ?>" class="flex items-center gap-4 p-4 rounded-2xl border border-slate-200 hover:border-sky-300 hover:bg-sky-50/50 transition-colors">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center"><i class="fas fa-money-bill-wave text-emerald-600"></i></div>
                    <div>
                        <p class="font-semibold text-slate-900">المدفوعات</p>
                        <p class="text-xs text-slate-500"><?php echo e($stats['total_payments']); ?> دفعة في الفترة</p>
                    </div>
                    <i class="fas fa-chevron-left text-slate-400 mr-auto"></i>
                </a>
                <a href="<?php echo e(route('admin.accounting.reports.transactions', $query)); ?>" class="flex items-center gap-4 p-4 rounded-2xl border border-slate-200 hover:border-sky-300 hover:bg-sky-50/50 transition-colors">
                    <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center"><i class="fas fa-exchange-alt text-violet-600"></i></div>
                    <div>
                        <p class="font-semibold text-slate-900">المعاملات المالية</p>
                        <p class="text-xs text-slate-500"><?php echo e($stats['total_transactions']); ?> معاملة في الفترة</p>
                    </div>
                    <i class="fas fa-chevron-left text-slate-400 mr-auto"></i>
                </a>
                <a href="<?php echo e(route('admin.accounting.reports.expenses', $query)); ?>" class="flex items-center gap-4 p-4 rounded-2xl border border-slate-200 hover:border-sky-300 hover:bg-sky-50/50 transition-colors">
                    <div class="w-12 h-12 rounded-xl bg-rose-100 flex items-center justify-center"><i class="fas fa-receipt text-rose-600"></i></div>
                    <div>
                        <p class="font-semibold text-slate-900">المصروفات</p>
                        <p class="text-xs text-slate-500">عرض المصروفات في الفترة</p>
                    </div>
                    <i class="fas fa-chevron-left text-slate-400 mr-auto"></i>
                </a>
                <a href="<?php echo e(route('admin.accounting.reports.wallets', $query)); ?>" class="flex items-center gap-4 p-4 rounded-2xl border border-slate-200 hover:border-sky-300 hover:bg-sky-50/50 transition-colors">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center"><i class="fas fa-wallet text-amber-600"></i></div>
                    <div>
                        <p class="font-semibold text-slate-900">محافظ المنصة</p>
                        <p class="text-xs text-slate-500"><?php echo e($stats['wallet_stats']['total_wallets']); ?> محفظة</p>
                    </div>
                    <i class="fas fa-chevron-left text-slate-400 mr-auto"></i>
                </a>
                <a href="<?php echo e(route('admin.accounting.reports.orders', $query)); ?>" class="flex items-center gap-4 p-4 rounded-2xl border border-slate-200 hover:border-sky-300 hover:bg-sky-50/50 transition-colors">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center"><i class="fas fa-shopping-cart text-indigo-600"></i></div>
                    <div>
                        <p class="font-semibold text-slate-900">الطلبات</p>
                        <p class="text-xs text-slate-500"><?php echo e($stats['order_stats']['total_orders']); ?> طلب في الفترة</p>
                    </div>
                    <i class="fas fa-chevron-left text-slate-400 mr-auto"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- تقارير الإيرادات -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900">تفاصيل الإيرادات</h3>
        </div>
        <div class="p-5 sm:p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- الإيرادات من المدفوعات -->
                <div>
                    <h4 class="text-sm font-semibold text-slate-700 mb-4">حسب طريقة الدفع</h4>
                    <div class="space-y-3">
                        <?php $__empty_1 = true; $__currentLoopData = $revenueReports['from_payments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                            <div>
                                <p class="text-sm font-medium text-slate-900"><?php echo e($item->payment_method); ?></p>
                                <p class="text-xs text-slate-500"><?php echo e($item->count); ?> دفعة</p>
                            </div>
                            <p class="text-sm font-bold text-emerald-600"><?php echo e(number_format($item->total, 2)); ?> ج.م</p>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-sm text-slate-500 text-center py-4">لا توجد بيانات</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- الإيرادات من المعاملات -->
                <div>
                    <h4 class="text-sm font-semibold text-slate-700 mb-4">حسب الفئة</h4>
                    <div class="space-y-3">
                        <?php $__empty_1 = true; $__currentLoopData = $revenueReports['from_transactions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                            <div>
                                <p class="text-sm font-medium text-slate-900"><?php echo e($item->category ?? 'غير محدد'); ?></p>
                                <p class="text-xs text-slate-500"><?php echo e($item->count); ?> معاملة</p>
                            </div>
                            <p class="text-sm font-bold text-emerald-600"><?php echo e(number_format($item->total, 2)); ?> ج.م</p>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-sm text-slate-500 text-center py-4">لا توجد بيانات</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- تقارير المصروفات -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900">تفاصيل المصروفات</h3>
        </div>
        <div class="p-5 sm:p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- المصروفات من جدول المصروفات -->
                <div>
                    <h4 class="text-sm font-semibold text-slate-700 mb-4">حسب الفئة</h4>
                    <div class="space-y-3">
                        <?php $__empty_1 = true; $__currentLoopData = $expenseReports['from_expenses']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                            <div>
                                <p class="text-sm font-medium text-slate-900"><?php echo e(\App\Models\Expense::categoryLabel($item->category)); ?></p>
                                <p class="text-xs text-slate-500"><?php echo e($item->count); ?> مصروف</p>
                            </div>
                            <p class="text-sm font-bold text-rose-600"><?php echo e(number_format($item->total, 2)); ?> ج.م</p>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-sm text-slate-500 text-center py-4">لا توجد بيانات</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- المصروفات من المعاملات -->
                <div>
                    <h4 class="text-sm font-semibold text-slate-700 mb-4">من المعاملات</h4>
                    <div class="space-y-3">
                        <?php $__empty_1 = true; $__currentLoopData = $expenseReports['from_transactions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                            <div>
                                <p class="text-sm font-medium text-slate-900"><?php echo e($item->category ?? 'غير محدد'); ?></p>
                                <p class="text-xs text-slate-500"><?php echo e($item->count); ?> معاملة</p>
                            </div>
                            <p class="text-sm font-bold text-rose-600"><?php echo e(number_format($item->total, 2)); ?> ج.م</p>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-sm text-slate-500 text-center py-4">لا توجد بيانات</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- الرسم البياني الشهري -->
    <?php if(count($monthlyData['months']) > 0): ?>
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900">الإيرادات والمصروفات الشهرية</h3>
        </div>
        <div class="p-5 sm:p-8">
            <div class="space-y-4">
                <?php $__currentLoopData = $monthlyData['months']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-slate-700"><?php echo e(\Carbon\Carbon::parse($month . '-01')->format('Y-m')); ?></span>
                        <div class="flex items-center gap-4 text-xs">
                            <span class="text-emerald-600">إيرادات: <?php echo e(number_format($monthlyData['revenues'][$index], 2)); ?> ج.م</span>
                            <span class="text-rose-600">مصروفات: <?php echo e(number_format($monthlyData['expenses'][$index], 2)); ?> ج.م</span>
                            <span class="text-sky-600 font-semibold">صافي: <?php echo e(number_format($monthlyData['revenues'][$index] - $monthlyData['expenses'][$index], 2)); ?> ج.م</span>
                        </div>
                    </div>
                    <div class="relative h-8 bg-slate-100 rounded-full overflow-hidden">
                        <div class="absolute inset-0 flex">
                            <div class="bg-emerald-500" style="width: <?php echo e($monthlyData['revenues'][$index] > 0 ? min(($monthlyData['revenues'][$index] / max($monthlyData['revenues'][$index], $monthlyData['expenses'][$index])) * 100, 100) : 0); ?>%"></div>
                            <div class="bg-rose-500" style="width: <?php echo e($monthlyData['expenses'][$index] > 0 ? min(($monthlyData['expenses'][$index] / max($monthlyData['revenues'][$index], $monthlyData['expenses'][$index])) * 100, 100) : 0); ?>%"></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/accounting/reports.blade.php ENDPATH**/ ?>