<div class="flex flex-col h-full bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 shadow-2xl border-l border-slate-700/50" style="margin: 0 !important; padding: 0 !important; margin-top: 0 !important; padding-top: 0 !important; position: relative !important; isolation: isolate !important; contain: layout style paint !important;">
    <!-- شعار المنصة -->
    <div class="p-6 border-b-2 border-slate-700/50 bg-gradient-to-br from-slate-800/90 via-slate-800/80 to-slate-900/90 flex-shrink-0 backdrop-blur-sm" style="margin-top: 0 !important; padding-top: 1.5rem !important;">
        <div class="flex items-center gap-4">
            <div class="relative">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center shadow-xl bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 border-2 border-blue-400/30">
                    <span class="text-3xl font-black text-white drop-shadow-lg">M</span>
                </div>
                <div class="absolute -inset-1 bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 rounded-2xl blur opacity-30 animate-pulse"></div>
            </div>
            <div>
                <h2 class="text-xl font-black bg-gradient-to-r from-blue-300 via-blue-200 to-blue-100 bg-clip-text text-transparent tracking-tight">Mindlytics</h2>
                <p class="text-xs text-slate-300/80 font-bold">لوحة الإدارة</p>
            </div>
        </div>
    </div>

    <!-- القائمة الرئيسية -->
    <nav class="flex-1 p-4 overflow-y-auto sidebar bg-transparent" style="flex: 1 1 auto !important; min-height: 0 !important; overflow-y: auto !important; scrollbar-width: thin; scrollbar-color: rgba(59, 130, 246, 0.5) transparent;">
        <style>
            .sidebar::-webkit-scrollbar {
                width: 6px;
            }
            .sidebar::-webkit-scrollbar-track {
                background: rgba(15, 23, 42, 0.5);
            }
            .sidebar::-webkit-scrollbar-thumb {
                background: linear-gradient(180deg, #3b82f6, #2563eb);
                border-radius: 10px;
            }
            .sidebar::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(180deg, #2563eb, #1d4ed8);
            }
        </style>
        <ul class="space-y-2">
            <!-- لوحة التحكم -->
            <?php
                $dashboardActive = request()->routeIs('admin.dashboard');
            ?>
            <li>
                <a href="<?php echo e(route('admin.dashboard')); ?>" 
                   @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 relative overflow-hidden group
                          <?php echo e($dashboardActive ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-xl shadow-blue-600/40' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-blue-400/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <i class="fas fa-chart-line w-5 relative z-10 <?php echo e($dashboardActive ? 'text-white' : 'text-slate-400 group-hover:text-white'); ?>"></i>
                    <span class="relative z-10 font-semibold">لوحة التحكم</span>
                    <?php if($dashboardActive): ?>
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-blue-400 to-blue-500 rounded-r shadow-lg"></div>
                    <?php endif; ?>
                </a>
            </li>

            <!-- الملف الشخصي -->
            <?php
                $profileActive = request()->routeIs('admin.profile*');
            ?>
            <li>
                <a href="<?php echo e(route('admin.profile')); ?>" 
                   @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 relative overflow-hidden group
                          <?php echo e($profileActive ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-xl shadow-blue-600/40' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-blue-400/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <i class="fas fa-user w-5 relative z-10 <?php echo e($profileActive ? 'text-white' : 'text-slate-400 group-hover:text-white'); ?>"></i>
                    <span class="relative z-10 font-semibold">الملف الشخصي</span>
                    <?php if($profileActive): ?>
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-blue-400 to-blue-500 rounded-r shadow-lg"></div>
                    <?php endif; ?>
                </a>
            </li>

            <!-- إدارة النظام -->
            <?php
                $systemManagementOpen = request()->routeIs('admin.users.*') || request()->routeIs('admin.orders.*') || request()->routeIs('admin.notifications.*') || request()->routeIs('admin.employee-notifications.*') || request()->routeIs('admin.activity-log*') || request()->routeIs('admin.statistics.*') || request()->routeIs('admin.performance.*');
            ?>
            <li x-data="{ open: <?php echo e($systemManagementOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-cogs w-5 text-slate-400 group-hover:text-white"></i>
                        <span class="font-medium">إدارة النظام</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-2 mr-4 space-y-1 border-r-2 border-slate-600/50 pr-2">
                    <li>
                        <a href="<?php echo e(route('admin.users.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.users*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-users w-4"></i>
                            <span>المستخدمين</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.orders.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.orders.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-shopping-cart w-4"></i>
                            <span>الطلبات</span>
                            <?php
                                $pendingOrders = \App\Models\Order::where('status', 'pending')->count();
                            ?>
                            <?php if($pendingOrders > 0): ?>
                                <span class="mr-auto bg-blue-500 text-white text-xs font-bold rounded-full px-2 py-1 shadow-lg"><?php echo e($pendingOrders); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.notifications.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.notifications.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-bell w-4"></i>
                            <span>الإشعارات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.employee-notifications.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.employee-notifications.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-user-tie w-4"></i>
                            <span>إشعارات الموظفين</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.activity-log')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.activity-log*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-history w-4"></i>
                            <span>سجل النشاطات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.statistics.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.statistics*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-chart-bar w-4"></i>
                            <span>الإحصائيات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.performance.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.performance.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-tachometer-alt w-4"></i>
                            <span>أداء الموقع</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- نظام الاتفاقيات -->
            <?php
                $agreementsOpen = request()->routeIs('admin.agreements.*') || request()->routeIs('admin.withdrawals.*') || request()->routeIs('admin.employee-agreements.*');
            ?>
            <li x-data="{ open: <?php echo e($agreementsOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-handshake w-5 text-blue-400 group-hover:text-white"></i>
                        <span class="font-medium">نظام الاتفاقيات</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-2 mr-4 space-y-1 border-r-2 border-slate-600/50 pr-2">
                    <?php if(Route::has('admin.agreements.index')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.agreements.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.agreements.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-file-contract w-4"></i>
                            <span>اتفاقيات المدربين</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if(Route::has('admin.employee-agreements.index')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.employee-agreements.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.employee-agreements.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-user-tie w-4"></i>
                            <span>اتفاقيات الموظفين</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if(Route::has('admin.withdrawals.index')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.withdrawals.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.withdrawals.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-money-bill-wave w-4"></i>
                            <span>طلبات السحب</span>
                            <?php
                                try {
                                    $pendingWithdrawals = \App\Models\WithdrawalRequest::where('status', 'pending')->count();
                                } catch (\Exception $e) {
                                    $pendingWithdrawals = 0;
                                }
                            ?>
                            <?php if($pendingWithdrawals > 0): ?>
                                <span class="mr-auto bg-yellow-400 text-yellow-900 text-xs font-bold rounded-full px-2 py-1 shadow-lg"><?php echo e($pendingWithdrawals); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </li>

            <!-- إدارة المحاسبة -->
            <?php
                $accountingOpen = request()->routeIs('admin.invoices.*') || request()->routeIs('admin.payments.*') || request()->routeIs('admin.transactions.*') || request()->routeIs('admin.wallets.*') || request()->routeIs('admin.expenses.*') || request()->routeIs('admin.subscriptions.*') || request()->routeIs('admin.installments.*') || request()->routeIs('admin.accounting.*') || request()->routeIs('admin.salaries.*');
            ?>
            <li x-data="{ open: <?php echo e($accountingOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-money-bill-wave w-5 text-slate-400 group-hover:text-white"></i>
                        <span class="font-medium">إدارة المحاسبة</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-2 mr-4 space-y-1 border-r-2 border-slate-600/50 pr-2">
                    <li>
                        <a href="<?php echo e(route('admin.invoices.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.invoices.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-file-invoice w-4"></i>
                            <span>الفواتير</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.payments.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.payments.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-credit-card w-4"></i>
                            <span>المدفوعات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.transactions.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.transactions.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-exchange-alt w-4"></i>
                            <span>المعاملات المالية</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.wallets.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.wallets.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-wallet w-4"></i>
                            <span>المحافظ</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.salaries.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.salaries.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-money-check-alt w-4"></i>
                            <span>الماليات الخاصة بالمدربين</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.expenses.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.expenses.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-receipt w-4"></i>
                            <span>المصروفات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.subscriptions.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.subscriptions.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-calendar-check w-4"></i>
                            <span>الاشتراكات</span>
                        </a>
                    </li>
                    <?php
                        $installmentsOpen = request()->routeIs('admin.installments.*');
                    ?>
                    <li x-data="{ open: <?php echo e($installmentsOpen ? 'true' : 'false'); ?> }">
                        <button @click="open = !open"
                                class="flex items-center justify-between w-full px-4 py-2.5 rounded-lg transition-all duration-300 text-slate-400 hover:bg-slate-700/50 hover:text-white">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-calendar-check w-4 text-slate-400"></i>
                                <span class="font-medium text-sm">إدارة التقسيط</span>
                            </span>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <ul x-show="open" x-transition class="mt-2 mr-3 space-y-1 border-r border-slate-600/50 pr-2">
                            <li>
                                <a href="<?php echo e(route('admin.installments.plans.index')); ?>"
                                   @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                                   class="flex items-center gap-2 px-4 py-2 text-xs rounded-lg transition-all duration-300 <?php echo e(request()->routeIs('admin.installments.plans.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : 'text-slate-400 hover:bg-slate-700/50 hover:text-white'); ?>">
                                    <i class="fas fa-layer-group w-3.5"></i>
                                    <span>خطط التقسيط</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo e(route('admin.installments.agreements.index')); ?>"
                                   @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                                   class="flex items-center gap-2 px-4 py-2 text-xs rounded-lg transition-all duration-300 <?php echo e(request()->routeIs('admin.installments.agreements.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : 'text-slate-400 hover:bg-slate-700/50 hover:text-white'); ?>">
                                    <i class="fas fa-handshake w-3.5"></i>
                                    <span>اتفاقيات السداد</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.accounting.reports')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.accounting.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-chart-pie w-4"></i>
                            <span>التقارير المحاسبية</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- إدارة التسويق -->
            <?php
                $marketingOpen = request()->routeIs('admin.coupons.*') || request()->routeIs('admin.referral-programs.*') || request()->routeIs('admin.referrals.*') || request()->routeIs('admin.loyalty.*') || request()->routeIs('admin.personal-branding.*');
            ?>
            <li x-data="{ open: <?php echo e($marketingOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-tags w-5 text-slate-400 group-hover:text-white"></i>
                        <span class="font-medium">إدارة التسويق</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-2 mr-4 space-y-1 border-r-2 border-slate-600/50 pr-2">
                    <li>
                        <a href="<?php echo e(route('admin.personal-branding.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.personal-branding.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-user-tie w-4"></i>
                            <span>التسويق الشخصي (المدربين)</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.coupons.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.coupons.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-ticket-alt w-4"></i>
                            <span>الكوبونات والخصومات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.referral-programs.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.referral-programs.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-gift w-4"></i>
                            <span>برامج الإحالات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.referrals.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.referrals.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-user-friends w-4"></i>
                            <span>الإحالات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.loyalty.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.loyalty.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-star w-4"></i>
                            <span>برامج الولاء</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- إدارة التسجيلات -->
            <?php
                $enrollmentsOpen = request()->routeIs('admin.online-enrollments.*') || request()->routeIs('admin.offline-enrollments.*') || request()->routeIs('admin.learning-path-enrollments.*');
            ?>
            <li x-data="{ open: <?php echo e($enrollmentsOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-user-graduate w-5 text-emerald-400 group-hover:text-white"></i>
                        <span class="font-medium">إدارة التسجيلات</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-2 mr-4 space-y-1 border-r-2 border-slate-600/50 pr-2">
                    <li>
                        <a href="<?php echo e(route('admin.online-enrollments.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.online-enrollments.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-laptop w-4"></i>
                            <span>التسجيلات الأونلاين</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.offline-enrollments.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.offline-enrollments.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-chalkboard-teacher w-4"></i>
                            <span>التسجيلات الأوفلاين</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.learning-path-enrollments.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.learning-path-enrollments.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-route w-4"></i>
                            <span>تسجيلات المسارات التعليمية</span>
                            <?php
                                try {
                                    $pendingEnrollments = \App\Models\LearningPathEnrollment::where('status', 'pending')->count();
                                } catch (\Exception $e) {
                                    $pendingEnrollments = 0;
                                }
                            ?>
                            <?php if($pendingEnrollments > 0): ?>
                                <span class="mr-auto bg-yellow-500 text-white text-xs font-bold rounded-full px-2 py-0.5 shadow-lg"><?php echo e($pendingEnrollments); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- إدارة المحتوى -->
            <?php
                $contentManagementOpen = request()->routeIs('admin.academic-years.*') || request()->routeIs('admin.learning-paths.*') || request()->routeIs('admin.academic-subjects.*') || request()->routeIs('admin.advanced-courses.*') || request()->routeIs('admin.exams.*') || request()->routeIs('admin.question-bank.*') || request()->routeIs('admin.question-categories.*') || request()->routeIs('admin.lectures.*') || request()->routeIs('admin.groups.*') || request()->routeIs('admin.assignments.*');
            ?>
            <li x-data="{ open: <?php echo e($contentManagementOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-folder w-5 text-slate-400 group-hover:text-white"></i>
                        <span class="font-medium">إدارة المحتوى</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-2 mr-4 space-y-1 border-r-2 border-slate-600/50 pr-2">
                    <li class="pt-2 pb-1">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-400 px-4 py-1 uppercase tracking-wider">
                            <i class="fas fa-route"></i>
                            كتالوج التعلم
                        </div>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.academic-years.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.academic-years.*') && !request()->routeIs('admin.learning-paths.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-compass w-4"></i>
                            <span>مسارات التعلم</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.learning-paths.courses.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.learning-paths.courses.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-graduation-cap w-4"></i>
                            <span>تسجيل الكورسات للمسارات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.learning-paths.instructors.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.learning-paths.instructors.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-user-tie w-4"></i>
                            <span>توصيف المدربين للمسارات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.academic-subjects.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.academic-subjects.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-layer-group w-4"></i>
                            <span>مجموعات المهارات</span>
                        </a>
                    </li>
                    <li>
                        <?php
                            $advancedCoursesActive = request()->routeIs('admin.advanced-courses.*') || request()->routeIs('admin.courses.lessons.*');
                        ?>
                        <a href="<?php echo e(route('admin.advanced-courses.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e($advancedCoursesActive ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-graduation-cap w-4"></i>
                            <span>إدارة الكورسات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.lectures.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.lectures.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-video w-4"></i>
                            <span>المحاضرات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.groups.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.groups.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-users-cog w-4"></i>
                            <span>المجموعات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.assignments.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.assignments.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-tasks w-4"></i>
                            <span>الواجبات والمشاريع</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.exams.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.exams.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-clipboard-check w-4"></i>
                            <span>الامتحانات</span>
                        </a>
                    </li>
                    <li>
                        <?php
                            $questionBankActive = request()->routeIs('admin.question-bank.*') || request()->routeIs('admin.question-categories.*');
                        ?>
                        <a href="<?php echo e(route('admin.question-bank.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e($questionBankActive ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-database w-4"></i>
                            <span>بنك الأسئلة</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- إدارة الكورسات الأوفلاين -->
            <?php
                $offlineCoursesOpen = request()->routeIs('admin.offline-courses.*') || request()->routeIs('admin.offline-groups.*') || request()->routeIs('admin.offline-enrollments.*') || request()->routeIs('admin.offline-activities.*') || request()->routeIs('admin.offline-agreements.*') || request()->routeIs('admin.offline-locations.*');
            ?>
            <li x-data="{ open: <?php echo e($offlineCoursesOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-chalkboard-teacher w-5 text-purple-400 group-hover:text-white"></i>
                        <span class="font-medium">الكورسات الأوفلاين</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-2 mr-4 space-y-1 border-r-2 border-slate-600/50 pr-2">
                    <li>
                        <a href="<?php echo e(route('admin.offline-locations.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.offline-locations.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-map-marker-alt w-4"></i>
                            <span>إدارة الأماكن</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.offline-courses.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.offline-courses.index') || request()->routeIs('admin.offline-courses.show') || request()->routeIs('admin.offline-courses.create') || request()->routeIs('admin.offline-courses.edit') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-book-reader w-4"></i>
                            <span>الكورسات الأوفلاين</span>
                            <?php
                                try {
                                    $activeOfflineCourses = \App\Models\OfflineCourse::where('status', 'active')->count();
                                } catch (\Exception $e) {
                                    $activeOfflineCourses = 0;
                                }
                            ?>
                            <?php if($activeOfflineCourses > 0): ?>
                                <span class="mr-auto bg-purple-500 text-white text-xs font-bold rounded-full px-2 py-0.5 shadow-lg"><?php echo e($activeOfflineCourses); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.offline-agreements.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.offline-agreements.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-file-contract w-4"></i>
                            <span>اتفاقيات المدربين</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- إدارة الموظفين -->
            <?php
                $employeesOpen = request()->routeIs('admin.employees.*') || request()->routeIs('admin.employee-jobs.*') || request()->routeIs('admin.employee-tasks.*') || request()->routeIs('admin.leaves.*') || request()->routeIs('admin.tasks.*') || request()->routeIs('admin.instructor-requests.*');
            ?>
            <li x-data="{ open: <?php echo e($employeesOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-users-cog w-5 text-emerald-400 group-hover:text-white"></i>
                        <span class="font-medium">الإدارة</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-2 mr-4 space-y-1 border-r-2 border-slate-600/50 pr-2">
                    <li>
                        <a href="<?php echo e(route('admin.employees.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.employees.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-user-tie w-4"></i>
                            <span>الموظفين</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.employee-jobs.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.employee-jobs.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-briefcase w-4"></i>
                            <span>الوظائف</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.employee-tasks.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.employee-tasks.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-tasks w-4"></i>
                            <span>مهام الموظفين</span>
                            <?php
                                try {
                                    $pendingTasks = \App\Models\EmployeeTask::where('status', 'pending')->count();
                                } catch (\Exception $e) {
                                    $pendingTasks = 0;
                                }
                            ?>
                            <?php if($pendingTasks > 0): ?>
                                <span class="mr-auto bg-yellow-500 text-white text-xs font-bold rounded-full px-2 py-0.5 shadow-lg"><?php echo e($pendingTasks); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.tasks.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.tasks.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-chalkboard-teacher w-4"></i>
                            <span>مهام المدربين</span>
                            <?php
                                try {
                                    $pendingInstructorTasks = \App\Models\Task::whereIn('user_id', \App\Models\User::whereIn('role', ['instructor', 'teacher'])->pluck('id'))->where('status', 'pending')->count();
                                } catch (\Exception $e) {
                                    $pendingInstructorTasks = 0;
                                }
                            ?>
                            <?php if($pendingInstructorTasks > 0): ?>
                                <span class="mr-auto bg-amber-500 text-white text-xs font-bold rounded-full px-2 py-0.5 shadow-lg"><?php echo e($pendingInstructorTasks); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.instructor-requests.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.instructor-requests.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-inbox w-4"></i>
                            <span>طلبات المدربين</span>
                            <?php
                                try {
                                    $pendingInstructorRequests = \App\Models\InstructorRequest::where('status', 'pending')->count();
                                } catch (\Exception $e) {
                                    $pendingInstructorRequests = 0;
                                }
                            ?>
                            <?php if($pendingInstructorRequests > 0): ?>
                                <span class="mr-auto bg-amber-500 text-white text-xs font-bold rounded-full px-2 py-0.5 shadow-lg"><?php echo e($pendingInstructorRequests); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.leaves.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.leaves.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-calendar-alt w-4"></i>
                            <span>الإجازات</span>
                            <?php
                                try {
                                    $pendingLeaves = \App\Models\LeaveRequest::where('status', 'pending')->count();
                                } catch (\Exception $e) {
                                    $pendingLeaves = 0;
                                }
                            ?>
                            <?php if($pendingLeaves > 0): ?>
                                <span class="mr-auto bg-yellow-500 text-white text-xs font-bold rounded-full px-2 py-0.5 shadow-lg"><?php echo e($pendingLeaves); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- الرقابة والجودة -->
            <?php
                $qualityControlOpen = request()->routeIs('admin.quality-control.*');
            ?>
            <li x-data="{ open: <?php echo e($qualityControlOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-shield-alt w-5 text-red-400 group-hover:text-white"></i>
                        <span class="font-medium">الرقابة والجودة</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-2 mr-4 space-y-1 border-r-2 border-slate-600/50 pr-2">
                    <li>
                        <a href="<?php echo e(route('admin.quality-control.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.quality-control.index') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-tachometer-alt w-4"></i>
                            <span>لوحة الرقابة</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.quality-control.students')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.quality-control.students') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-user-graduate w-4"></i>
                            <span>رقابة الطلاب</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.quality-control.instructors')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.quality-control.instructors') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-chalkboard-teacher w-4"></i>
                            <span>رقابة المدربين</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.quality-control.employees')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.quality-control.employees') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-user-tie w-4"></i>
                            <span>رقابة الموظفين</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.quality-control.operations')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.quality-control.operations') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-cogs w-4"></i>
                            <span>متابعة العمليات</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- التحكم في الشهادات -->
            <?php
                $certificatesManagementOpen = request()->routeIs('admin.certificates.*');
            ?>
            <li x-data="{ open: <?php echo e($certificatesManagementOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-certificate w-5 text-slate-400 group-hover:text-white"></i>
                        <span class="font-medium">التحكم في الشهادات</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-2 mr-4 space-y-1 border-r-2 border-slate-600/50 pr-2">
                    <li>
                        <a href="<?php echo e(route('admin.certificates.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.certificates.index') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-list w-4"></i>
                            <span>قائمة الشهادات</span>
                            <?php
                                $totalCertificates = \App\Models\Certificate::count();
                            ?>
                            <?php if($totalCertificates > 0): ?>
                                <span class="mr-auto bg-blue-400 text-white text-xs font-bold rounded-full px-2 py-0.5 shadow-lg"><?php echo e($totalCertificates); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.certificates.create')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.certificates.create') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-plus-circle w-4"></i>
                            <span>إصدار شهادة جديدة</span>
                        </a>
                    </li>
                    <?php
                        $pendingCertificates = \App\Models\Certificate::where(function($q) {
                            $q->where('status', 'pending')->orWhere('is_verified', false);
                        })->count();
                    ?>
                    <?php if($pendingCertificates > 0): ?>
                    <li>
                        <a href="<?php echo e(route('admin.certificates.index', ['status' => 'pending'])); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->get('status') == 'pending' ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-clock w-4"></i>
                            <span>الشهادات المعلقة</span>
                            <span class="mr-auto bg-yellow-400 text-yellow-900 text-xs font-bold rounded-full px-2 py-0.5 shadow-lg"><?php echo e($pendingCertificates); ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </li>

            <!-- الإنجازات والشارات -->
            <?php
                $achievementsOpen = request()->routeIs('admin.achievements.*') || request()->routeIs('admin.badges.*') || request()->routeIs('admin.reviews.*');
            ?>
            <li x-data="{ open: <?php echo e($achievementsOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-trophy w-5 text-slate-400 group-hover:text-white"></i>
                        <span class="font-medium">الإنجازات والشارات</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-2 mr-4 space-y-1 border-r-2 border-slate-600/50 pr-2">
                    <li>
                        <a href="<?php echo e(route('admin.achievements.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.achievements.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-medal w-4"></i>
                            <span>الإنجازات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.badges.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.badges.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-award w-4"></i>
                            <span>الشارات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.reviews.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.reviews.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-star-half-alt w-4"></i>
                            <span>التقييمات والمراجعات</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- إدارة الصلاحيات والأدوار -->
            <?php
                $permissionsOpen = request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') || request()->routeIs('admin.user-permissions.*');
            ?>
            <li x-data="{ open: <?php echo e($permissionsOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-shield-alt w-5 text-slate-400 group-hover:text-white"></i>
                        <span class="font-medium">الصلاحيات والأدوار</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-2 mr-4 space-y-1 border-r-2 border-slate-600/50 pr-2">
                    <li>
                        <a href="<?php echo e(route('admin.roles.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.roles.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-user-tag w-4"></i>
                            <span>الأدوار</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.permissions.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.permissions.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-key w-4"></i>
                            <span>الصلاحيات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.user-permissions.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.user-permissions.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-user-shield w-4"></i>
                            <span>صلاحيات المستخدمين</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- إدارة الصفحات الخارجية -->
            <?php
                $blogOpen = request()->routeIs('admin.blog.*') || request()->routeIs('admin.contact-messages.*') || request()->routeIs('admin.packages.*');
            ?>
            <li x-data="{ open: <?php echo e($blogOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-globe w-5 text-slate-400 group-hover:text-white"></i>
                        <span class="font-medium">الصفحات الخارجية</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-2 mr-4 space-y-1 border-r-2 border-slate-600/50 pr-2">
                    <li>
                        <a href="<?php echo e(route('admin.blog.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.blog.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-newspaper w-4"></i>
                            <span>المدونة</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.portfolio.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.portfolio.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-briefcase w-4"></i>
                            <span>البورتفوليو</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.contact-messages.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.contact-messages.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-envelope w-4"></i>
                            <span>رسائل التواصل</span>
                            <?php
                                $unreadCount = \App\Models\ContactMessage::whereNull('read_at')->count();
                            ?>
                            <?php if($unreadCount > 0): ?>
                                <span class="mr-auto bg-[#FFD34E] text-[#1F3A56] text-xs font-bold rounded-full px-2 py-1 shadow-lg"><?php echo e($unreadCount); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.packages.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.packages.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-tags w-4"></i>
                            <span>الأسعار والباقات</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- إدارة المهام -->
            <?php
                $tasksActive = request()->routeIs('admin.tasks.*');
            ?>
            <li>
                <a href="<?php echo e(route('admin.tasks.index')); ?>" 
                   @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 relative overflow-hidden group <?php echo e($tasksActive ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-xl shadow-blue-600/40' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-200/40 to-blue-100/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <i class="fas fa-list-check w-5 relative z-10 <?php echo e($tasksActive ? 'text-white' : 'text-slate-400 group-hover:text-white'); ?>"></i>
                    <span class="relative z-10 font-semibold">المهام</span>
                    <?php if($tasksActive): ?>
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-blue-400 to-blue-500 rounded-r shadow-lg"></div>
                    <?php endif; ?>
                </a>
            </li>

            <!-- الرسائل -->
            <?php
                $messagesActive = request()->routeIs('admin.messages.*');
            ?>
            <li>
                <a href="<?php echo e(route('admin.messages.index')); ?>" 
                   @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 relative overflow-hidden group <?php echo e($messagesActive ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-xl shadow-blue-600/40' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-200/40 to-blue-100/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <i class="fas fa-envelope w-5 relative z-10 <?php echo e($messagesActive ? 'text-white' : 'text-slate-400 group-hover:text-white'); ?>"></i>
                    <span class="relative z-10 font-semibold">الرسائل</span>
                    <?php if($messagesActive): ?>
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-blue-400 to-blue-500 rounded-r shadow-lg"></div>
                    <?php endif; ?>
                </a>
            </li>

            <!-- التقارير الشاملة -->
            <?php
                $reportsOpen = request()->routeIs('admin.reports.*');
            ?>
            <li x-data="{ open: <?php echo e($reportsOpen ? 'true' : 'false'); ?> }">
                <button @click="open = !open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-file-excel w-5 text-emerald-400 group-hover:text-white"></i>
                        <span class="font-medium">التقارير الشاملة</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition class="mt-2 mr-4 space-y-1 border-r-2 border-slate-600/50 pr-2">
                    <li>
                        <a href="<?php echo e(route('admin.reports.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.reports.index') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-chart-pie w-4"></i>
                            <span>لوحة التقارير</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.reports.users')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.reports.users') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-users w-4"></i>
                            <span>تقارير المستخدمين</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.reports.courses')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.reports.courses') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-graduation-cap w-4"></i>
                            <span>تقارير الكورسات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.reports.financial')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.reports.financial') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-money-bill-wave w-4"></i>
                            <span>التقارير المالية</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.reports.academic')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.reports.academic') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-book w-4"></i>
                            <span>التقارير الأكاديمية</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.reports.activities')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.reports.activities') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-history w-4"></i>
                            <span>تقارير النشاطات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.reports.comprehensive')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.reports.comprehensive') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-file-alt w-4"></i>
                            <span>التقرير الشامل</span>
                        </a>
                    </li>
                </ul>
            </li>

        </ul>
    </nav>

    <!-- معلومات المستخدم -->
    <div class="p-4 border-t-2 border-slate-700/50 bg-gradient-to-br from-slate-800/90 via-slate-800/80 to-slate-900/90 flex-shrink-0 backdrop-blur-sm">
        <div class="flex items-center gap-3 p-3 bg-slate-700/40 backdrop-blur-sm rounded-xl shadow-xl border border-slate-600/50 hover:bg-slate-700/60 transition-all duration-300 group">
            <?php if(auth()->user()->profile_image): ?>
                <img src="<?php echo e(asset('storage/' . auth()->user()->profile_image)); ?>" alt="<?php echo e(auth()->user()->name); ?>" class="w-12 h-12 rounded-full object-cover shadow-lg ring-2 ring-slate-600/50 flex-shrink-0" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 rounded-full hidden flex items-center justify-center text-white font-black text-lg shadow-lg relative overflow-hidden flex-shrink-0">
                    <div class="absolute inset-0 bg-gradient-to-br from-white/30 to-transparent"></div>
                    <span class="relative z-10"><?php echo e(substr(auth()->user()->name, 0, 1)); ?></span>
                </div>
            <?php else: ?>
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 rounded-full flex items-center justify-center text-white font-black text-lg shadow-lg relative overflow-hidden flex-shrink-0">
                    <div class="absolute inset-0 bg-gradient-to-br from-white/30 to-transparent"></div>
                    <span class="relative z-10"><?php echo e(substr(auth()->user()->name, 0, 1)); ?></span>
                </div>
            <?php endif; ?>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-black bg-gradient-to-r from-blue-300 via-blue-200 to-blue-100 bg-clip-text text-transparent truncate"><?php echo e(auth()->user()->name); ?></p>
                <p class="text-xs text-slate-300/80 font-bold"><?php echo e(auth()->user()->phone); ?></p>
            </div>
            <div class="w-3 h-3 bg-blue-500 rounded-full shadow-lg ring-2 ring-blue-400/50 ring-offset-2 ring-offset-slate-800 animate-pulse"></div>
        </div>
    </div>
</div>

<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/layouts/admin-sidebar.blade.php ENDPATH**/ ?>