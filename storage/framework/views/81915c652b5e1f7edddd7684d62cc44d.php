<div class="admin-sidebar-root flex flex-col h-full bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 shadow-2xl border-l border-slate-700/50" style="margin: 0 !important; padding: 0 !important; margin-top: 0 !important; padding-top: 0 !important; position: relative !important;">
    <!-- شعار المنصة -->
    <div class="p-6 border-b-2 border-slate-700/50 bg-slate-900/90 flex-shrink-0" style="margin-top: 0 !important; padding-top: 1.5rem !important;">
        <div class="flex items-center gap-4">
            <div class="relative">
                <div class="w-16 h-16 rounded-full flex items-center justify-center shadow-xl overflow-hidden p-1">
                    <img src="<?php echo e($platformLogoUrl ?? asset('logo-fallback.svg')); ?>"
                         alt="<?php echo e(config('app.name')); ?>"
                         class="w-full h-full object-cover object-center rounded-full"
                         onerror="this.onerror=null;this.src='<?php echo e(asset('logo-fallback.svg')); ?>';">
                </div>
            </div>
            <div>
                <h2 class="text-xl font-black bg-gradient-to-r from-blue-300 via-blue-200 to-blue-100 bg-clip-text text-transparent tracking-tight">Mindlytics</h2>
                <p class="text-xs text-slate-300/80 font-bold"><?php echo e(__('admin.admin_panel')); ?></p>
            </div>
        </div>
    </div>

    <!-- القائمة الرئيسية -->
    <nav class="admin-sidebar-nav sidebar flex-1 p-4 overflow-y-auto bg-transparent" style="flex: 1 1 auto !important; min-height: 0 !important;">
        <ul class="space-y-2">
            <!-- لوحة التحكم -->
            <?php
                $dashboardActive = request()->routeIs('admin.dashboard');
            ?>
            <li>
                <a href="<?php echo e(route('admin.dashboard')); ?>" 
                   @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 relative group
                          <?php echo e($dashboardActive ? 'admin-nav-dashboard-active' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white overflow-hidden'); ?>">
                    <?php if (! ($dashboardActive)): ?>
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-blue-400/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <?php endif; ?>
                    <i class="fas fa-chart-line w-5 <?php echo e($dashboardActive ? 'bridge-icon' : 'relative z-10 text-slate-400 group-hover:text-white'); ?>"></i>
                    <span class="relative z-10 font-semibold"><?php echo e(__('admin.dashboard')); ?></span>
                </a>
            </li>

            <?php
                $branchesMenuOpen = request()->routeIs('admin.branches.*') || request()->routeIs('admin.branch-managers.*');
            ?>
            <li x-data="{ open: <?php echo e($branchesMenuOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="open = !open" :aria-expanded="open"
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-code-branch w-5 text-amber-400 group-hover:text-white"></i>
                        <span class="font-semibold text-start leading-snug">الفروع والامتداد<br><span class="text-xs font-normal text-slate-500 group-hover:text-slate-300">فروع الأكاديمية والدومينات</span></span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400 shrink-0" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <li>
                        <a href="<?php echo e(route('admin.branches.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.branches.index') || request()->routeIs('admin.branches.show') || request()->routeIs('admin.branches.edit') ? 'bg-amber-600/30 text-white font-semibold border-r-2 border-amber-400' : ''); ?>">
                            <i class="fas fa-list w-4"></i>
                            <span>قائمة الفروع</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.branches.create')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.branches.create') ? 'bg-amber-600/30 text-white font-semibold border-r-2 border-amber-400' : ''); ?>">
                            <i class="fas fa-plus w-4"></i>
                            <span>إضافة فرع</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.branch-managers.create')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.branch-managers.create') ? 'bg-amber-600/30 text-white font-semibold border-r-2 border-amber-400' : ''); ?>">
                            <i class="fas fa-user-tie w-4"></i>
                            <span>إضافة مدير فرع</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.branches.rollout-plan')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.branches.rollout-plan') ? 'bg-amber-600/30 text-white font-semibold border-r-2 border-amber-400' : ''); ?>">
                            <i class="fas fa-book-open w-4"></i>
                            <span>خطة التوسع (المستند)</span>
                        </a>
                    </li>
                </ul>
            </li>

            <?php
                $mobileAppMenuOpen = request()->routeIs('admin.mobile-app.*') || request()->routeIs('admin.practice.*');
            ?>
            <li x-data="{ open: <?php echo e($mobileAppMenuOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="open = !open" :aria-expanded="open"
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-mobile-alt w-5 text-violet-400 group-hover:text-white"></i>
                        <span class="font-semibold text-start leading-snug">Mindlytics Community<br><span class="text-xs font-normal text-slate-500 group-hover:text-slate-300">تطبيق الطلاب</span></span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400 shrink-0" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <li>
                        <a href="<?php echo e(route('admin.mobile-app.edit')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.mobile-app.edit') ? 'bg-violet-600/30 text-white font-semibold border-r-2 border-violet-400' : ''); ?>">
                            <i class="fas fa-home w-4"></i>
                            <span>محتوى الصفحة الرئيسية</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.practice.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.practice.*') ? 'bg-violet-600/30 text-white font-semibold border-r-2 border-violet-400' : ''); ?>">
                            <i class="fas fa-dumbbell w-4"></i>
                            <span>Practice (التمارين)</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.mobile-app.course-community.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.mobile-app.course-community.*') ? 'bg-violet-600/30 text-white font-semibold border-r-2 border-violet-400' : ''); ?>">
                            <i class="fas fa-comments w-4"></i>
                            <span>مجتمع الكورسات (مراقبة ونشر)</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.mobile-app.notifications')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.mobile-app.notifications') ? 'bg-violet-600/30 text-white font-semibold border-r-2 border-violet-400' : ''); ?>">
                            <i class="fas fa-bell w-4"></i>
                            <span>إشعارات التطبيق</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.mobile-app.maintenance')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.mobile-app.maintenance') ? 'bg-violet-600/30 text-white font-semibold border-r-2 border-violet-400' : ''); ?>">
                            <i class="fas fa-tools w-4"></i>
                            <span>الصيانة والرسائل العامة</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.mobile-app.links')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.mobile-app.links') ? 'bg-violet-600/30 text-white font-semibold border-r-2 border-violet-400' : ''); ?>">
                            <i class="fas fa-link w-4"></i>
                            <span>الروابط والمسارات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.mobile-app.appearance')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.mobile-app.appearance') ? 'bg-violet-600/30 text-white font-semibold border-r-2 border-violet-400' : ''); ?>">
                            <i class="fas fa-palette w-4"></i>
                            <span>المظهر والعلامة</span>
                        </a>
                    </li>
                </ul>
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
                    <span class="relative z-10 font-semibold"><?php echo e(__('admin.profile')); ?></span>
                    <?php if($profileActive): ?>
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-blue-400 to-blue-500 rounded-r shadow-lg"></div>
                    <?php endif; ?>
                </a>
            </li>

            <?php
                $salesMenuOpen = request()->routeIs('admin.sales.*');
            ?>
            <li x-data="{ open: <?php echo e($salesMenuOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="open = !open" :aria-expanded="open"
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-handshake w-5 text-emerald-400 group-hover:text-white"></i>
                        <span class="font-medium">المبيعات</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <li>
                        <a href="<?php echo e(route('admin.sales.reports.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.sales.reports.*') ? 'bg-emerald-600/30 text-white font-semibold border-r-2 border-emerald-400' : ''); ?>">
                            <i class="fas fa-chart-line w-4"></i>
                            <span>تقارير المبيعات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.sales.daily-reports.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.sales.daily-reports.*') ? 'bg-emerald-600/30 text-white font-semibold border-r-2 border-emerald-400' : ''); ?>">
                            <i class="fas fa-clipboard-list w-4"></i>
                            <span>التقارير اليومية (موظفين)</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.sales.categories.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.sales.categories.*') ? 'bg-emerald-600/30 text-white font-semibold border-r-2 border-emerald-400' : ''); ?>">
                            <i class="fas fa-tags w-4"></i>
                            <span>تصنيفات العملاء</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.sales.leads.import')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.sales.leads.import*') ? 'bg-emerald-600/30 text-white font-semibold border-r-2 border-emerald-400' : ''); ?>">
                            <i class="fas fa-file-upload w-4"></i>
                            <span>استيراد دفعة عملاء</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.sales.leads.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.sales.leads.*') ? 'bg-emerald-600/30 text-white font-semibold border-r-2 border-emerald-400' : ''); ?>">
                            <i class="fas fa-users w-4"></i>
                            <span>العملاء المحتملون</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.sales.groups.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.sales.groups.*') ? 'bg-emerald-600/30 text-white font-semibold border-r-2 border-emerald-400' : ''); ?>">
                            <i class="fas fa-layer-group w-4"></i>
                            <span>مجموعات العملاء</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.sales.transfer.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.sales.transfer.*') ? 'bg-emerald-600/30 text-white font-semibold border-r-2 border-emerald-400' : ''); ?>">
                            <i class="fas fa-random w-4"></i>
                            <span>تحويل بيانات موظف</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.sales.audit-log.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.sales.audit-log.*') ? 'bg-emerald-600/30 text-white font-semibold border-r-2 border-emerald-400' : ''); ?>">
                            <i class="fas fa-clipboard-list w-4"></i>
                            <span>سجل أنشطة المبيعات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.sales.kpi.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.sales.kpi.index') && !request()->routeIs('admin.sales.kpi.targets*') ? 'bg-emerald-600/30 text-white font-semibold border-r-2 border-emerald-400' : ''); ?>">
                            <i class="fas fa-bullseye w-4"></i>
                            <span>مراقبة KPIs المبيعات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.sales.kpi.targets')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.sales.kpi.targets*') ? 'bg-emerald-600/30 text-white font-semibold border-r-2 border-emerald-400' : ''); ?>">
                            <i class="fas fa-sliders-h w-4"></i>
                            <span>أهداف المبيعات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.sales.insights.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.sales.insights.*') ? 'bg-emerald-600/30 text-white font-semibold border-r-2 border-emerald-400' : ''); ?>">
                            <i class="fas fa-lightbulb w-4"></i>
                            <span>Insights الموظفين</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.sales.commissions.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.sales.commissions.*') ? 'bg-emerald-600/30 text-white font-semibold border-r-2 border-emerald-400' : ''); ?>">
                            <i class="fas fa-coins w-4"></i>
                            <span>كوميشن المبيعات</span>
                        </a>
                    </li>
                </ul>
            </li>

            <?php
                $whatsappMenuOpen = request()->routeIs('admin.whatsapp.*');
            ?>
            <li x-data="{ open: <?php echo e($whatsappMenuOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="open = !open" :aria-expanded="open"
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fab fa-whatsapp w-5 text-emerald-400 group-hover:text-white"></i>
                        <span class="font-medium">قسم الواتساب</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <li>
                        <a href="<?php echo e(route('admin.whatsapp.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.whatsapp.index') ? 'bg-emerald-600/30 text-white font-semibold border-r-2 border-emerald-400' : ''); ?>">
                            <i class="fas fa-tachometer-alt w-4"></i>
                            <span>لوحة الواتساب</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.whatsapp.settings')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.whatsapp.settings') || request()->routeIs('admin.whatsapp.test-connection') || request()->routeIs('admin.whatsapp.disconnect') ? 'bg-emerald-600/30 text-white font-semibold border-r-2 border-emerald-400' : ''); ?>">
                            <i class="fas fa-plug w-4"></i>
                            <span>ربط Meta</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.whatsapp.send')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.whatsapp.send*') ? 'bg-emerald-600/30 text-white font-semibold border-r-2 border-emerald-400' : ''); ?>">
                            <i class="fas fa-paper-plane w-4"></i>
                            <span>إرسال رسالة</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.whatsapp.messages')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.whatsapp.messages*') ? 'bg-emerald-600/30 text-white font-semibold border-r-2 border-emerald-400' : ''); ?>">
                            <i class="fas fa-list w-4"></i>
                            <span>سجل الرسائل</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.whatsapp.templates.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.whatsapp.templates.*') ? 'bg-emerald-600/30 text-white font-semibold border-r-2 border-emerald-400' : ''); ?>">
                            <i class="fas fa-file-alt w-4"></i>
                            <span>قوالب Meta</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.whatsapp.inbox')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.whatsapp.inbox*') ? 'bg-emerald-600/30 text-white font-semibold border-r-2 border-emerald-400' : ''); ?>">
                            <i class="fas fa-inbox w-4"></i>
                            <span>المحادثات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.whatsapp.batches.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.whatsapp.batches.*') ? 'bg-emerald-600/30 text-white font-semibold border-r-2 border-emerald-400' : ''); ?>">
                            <i class="fas fa-layer-group w-4"></i>
                            <span>دفعات الإرسال</span>
                        </a>
                    </li>
                </ul>
            </li>

            <?php
                $hrMenuOpen = request()->routeIs('admin.hr.*');
            ?>
            <li x-data="{ open: <?php echo e($hrMenuOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="open = !open" :aria-expanded="open"
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-user-friends w-5 text-pink-400 group-hover:text-white"></i>
                        <span class="font-medium">HR</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <li>
                        <a href="<?php echo e(route('admin.hr.jobs.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.hr.jobs.*') ? 'bg-pink-600/30 text-white font-semibold border-r-2 border-pink-400' : ''); ?>">
                            <i class="fas fa-briefcase w-4"></i>
                            <span>الوظائف</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.hr.applications.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.hr.applications.*') ? 'bg-pink-600/30 text-white font-semibold border-r-2 border-pink-400' : ''); ?>">
                            <i class="fas fa-inbox w-4"></i>
                            <span>المتقدمون والسكور</span>
                        </a>
                    </li>
                </ul>
            </li>

            <?php
                $scholarshipsMenuOpen = request()->routeIs('admin.scholarships.*');
            ?>
            <li x-data="{ open: <?php echo e($scholarshipsMenuOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="open = !open" :aria-expanded="open"
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-graduation-cap w-5 text-violet-400 group-hover:text-white"></i>
                        <span class="font-medium">قسم المنح</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <li>
                        <a href="<?php echo e(route('admin.scholarships.dashboard')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.scholarships.dashboard') ? 'bg-violet-600/30 text-white font-semibold border-r-2 border-violet-400' : ''); ?>">
                            <i class="fas fa-tachometer-alt w-4"></i>
                            <span>لوحة المنح</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.scholarships.programs.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.scholarships.programs.*') ? 'bg-violet-600/30 text-white font-semibold border-r-2 border-violet-400' : ''); ?>">
                            <i class="fas fa-award w-4"></i>
                            <span>المنح الدراسية</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.scholarships.courses.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.scholarships.courses.*') ? 'bg-violet-600/30 text-white font-semibold border-r-2 border-violet-400' : ''); ?>">
                            <i class="fas fa-book w-4"></i>
                            <span>كورسات المنح</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.scholarships.instructors.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.scholarships.instructors.*') ? 'bg-violet-600/30 text-white font-semibold border-r-2 border-violet-400' : ''); ?>">
                            <i class="fas fa-chalkboard-teacher w-4"></i>
                            <span>مدربو المنح</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.scholarships.students.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.scholarships.students.*') ? 'bg-violet-600/30 text-white font-semibold border-r-2 border-violet-400' : ''); ?>">
                            <i class="fas fa-user-graduate w-4"></i>
                            <span>طلاب المنح</span>
                        </a>
                    </li>
                </ul>
            </li>

            <?php
                $investmentMenuOpen = request()->routeIs('admin.investment.*');
            ?>
            <li x-data="{ open: <?php echo e($investmentMenuOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="open = !open" :aria-expanded="open"
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-chart-pie w-5 text-amber-400 group-hover:text-white"></i>
                        <span class="font-medium">قسم الاستثمار</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <li>
                        <a href="<?php echo e(route('admin.investment.dashboard')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.investment.dashboard') ? 'bg-amber-600/30 text-white font-semibold border-r-2 border-amber-400' : ''); ?>">
                            <i class="fas fa-tachometer-alt w-4"></i>
                            <span>لوحة الاستثمار</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.investment.plans.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.investment.plans.*') ? 'bg-amber-600/30 text-white font-semibold border-r-2 border-amber-400' : ''); ?>">
                            <i class="fas fa-layer-group w-4"></i>
                            <span>الخطط الاستثمارية</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.investment.inquiries.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.investment.inquiries.*') ? 'bg-amber-600/30 text-white font-semibold border-r-2 border-amber-400' : ''); ?>">
                            <i class="fas fa-handshake w-4"></i>
                            <span>طلبات المستثمرين</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.investment.policies.edit')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.investment.policies.*') ? 'bg-amber-600/30 text-white font-semibold border-r-2 border-amber-400' : ''); ?>">
                            <i class="fas fa-gavel w-4"></i>
                            <span>الإطار القانوني</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- إدارة النظام -->
            <?php
                $systemManagementOpen = request()->routeIs('admin.system-settings.*') || request()->routeIs('admin.users.*') || request()->routeIs('admin.orders.*') || request()->routeIs('admin.notifications.*') || request()->routeIs('admin.employee-notifications.*') || request()->routeIs('admin.activity-log*') || request()->routeIs('admin.platform-errors.*') || request()->routeIs('admin.two-factor-logs.*') || request()->routeIs('admin.statistics.*') || request()->routeIs('admin.performance.*');
            ?>
            <li x-data="{ open: <?php echo e($systemManagementOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="open = !open" :aria-expanded="open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-cogs w-5 text-slate-400 group-hover:text-white"></i>
                        <span class="font-medium"><?php echo e(__('admin.system_management')); ?></span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <li>
                        <a href="<?php echo e(route('admin.system-settings.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.system-settings.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-palette w-4"></i>
                            <span>إعدادات النظام</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.users.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.users*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-users w-4"></i>
                            <span><?php echo e(__('admin.users')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.orders.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.orders.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-shopping-cart w-4"></i>
                            <span><?php echo e(__('admin.orders')); ?></span>
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
                            <span><?php echo e(__('admin.notifications')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.employee-notifications.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.employee-notifications.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-user-tie w-4"></i>
                            <span><?php echo e(__('admin.employee_notifications')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.platform-errors.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.platform-errors.*') ? 'bg-rose-600/30 text-white font-semibold shadow-md border-r-2 border-rose-400' : ''); ?>">
                            <i class="fas fa-bug w-4"></i>
                            <span>أخطاء المنصة</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.activity-log')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.activity-log*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-history w-4"></i>
                            <span><?php echo e(__('admin.activity_log')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.two-factor-logs.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.two-factor-logs.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-shield-alt w-4"></i>
                            <span><?php echo e(__('admin.two_factor_logs')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.statistics.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.statistics*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-chart-bar w-4"></i>
                            <span><?php echo e(__('admin.statistics')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.performance.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.performance.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-tachometer-alt w-4"></i>
                            <span><?php echo e(__('admin.performance')); ?></span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- الاجتماعات / الورش -->
            <?php
                $workshopsOpen = request()->routeIs('admin.workshops.*');
            ?>
            <li x-data="{ open: <?php echo e($workshopsOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="open = !open" :aria-expanded="open"
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-people-arrows w-5 text-blue-400 group-hover:text-white"></i>
                        <span class="font-medium">الاجتماعات / الورش</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <li>
                        <a href="<?php echo e(route('admin.workshops.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.workshops.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-list w-4"></i>
                            <span>كل الورش</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- إدارة المحتوى / مصادر الفيديو -->
            <?php
                $contentOpen = request()->routeIs('admin.video-providers.*');
            ?>
            <li x-data="{ open: <?php echo e($contentOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="open = !open" :aria-expanded="open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-photo-video w-5 text-blue-400 group-hover:text-white"></i>
                        <span class="font-medium">إدارة المحتوى</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <li>
                        <a href="<?php echo e(route('admin.video-providers.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.video-providers.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-server w-4"></i>
                            <span>مصادر الفيديو</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- نظام الاتفاقيات -->
            <?php
                $agreementsOpen = request()->routeIs('admin.agreements.*') || request()->routeIs('admin.withdrawals.*') || request()->routeIs('admin.employee-agreements.*');
            ?>
            <li x-data="{ open: <?php echo e($agreementsOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="open = !open" :aria-expanded="open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-handshake w-5 text-blue-400 group-hover:text-white"></i>
                        <span class="font-medium"><?php echo e(__('admin.agreements_system')); ?></span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <?php if(Route::has('admin.agreements.index')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.agreements.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.agreements.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-file-contract w-4"></i>
                            <span><?php echo e(__('admin.instructor_agreements')); ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if(Route::has('admin.employee-agreements.index')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.employee-agreements.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.employee-agreements.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-user-tie w-4"></i>
                            <span><?php echo e(__('admin.employee_agreements')); ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if(Route::has('admin.withdrawals.index')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.withdrawals.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.withdrawals.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-money-bill-wave w-4"></i>
                            <span><?php echo e(__('admin.withdrawal_requests')); ?></span>
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
                $accountingOpen = request()->routeIs('admin.invoices.*') || request()->routeIs('admin.payments.*') || request()->routeIs('admin.transactions.*') || request()->routeIs('admin.wallets.*') || request()->routeIs('admin.expenses.*') || request()->routeIs('admin.subscriptions.*') || request()->routeIs('admin.installments.*') || request()->routeIs('admin.accounting.*') || request()->routeIs('admin.salaries.*') || request()->routeIs('admin.employee-salaries.*') || request()->routeIs('admin.employee-agreements.*') || request()->routeIs('admin.accounting.hub') || request()->routeIs('admin.accounting.chart') || request()->routeIs('admin.accounting.gateway-operations');
            ?>
            <li x-data="{ open: <?php echo e($accountingOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="open = !open" :aria-expanded="open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-money-bill-wave w-5 text-slate-400 group-hover:text-white"></i>
                        <span class="font-medium"><?php echo e(__('admin.accounting')); ?></span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <li>
                        <a href="<?php echo e(route('admin.accounting.hub')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.accounting.hub') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-th-large w-4"></i>
                            <span>مركز المحاسبة</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.accounting.insights')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.accounting.insights*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-chart-area w-4"></i>
                            <span>مؤشرات الشركة (Real‑time)</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.accounting.receivables')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.accounting.receivables*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-hand-holding-usd w-4"></i>
                            <span>المديونية والذمم</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.accounting.chart')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.accounting.chart') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-sitemap w-4"></i>
                            <span>شجرة الحسابات</span>
                        </a>
                    </li>
                    <?php if(Route::has('admin.accounting.installments')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.accounting.installments')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.accounting.installments') ? 'bg-violet-600/35 text-white font-semibold shadow-md border-r-2 border-violet-400' : ''); ?>">
                            <i class="fas fa-percentage w-4"></i>
                            <span>لوحة التقسيط</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if(Route::has('admin.accounting.gateway-operations')): ?>
                    <li>
                        <a href="<?php echo e(route('admin.accounting.gateway-operations')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.accounting.gateway-operations') ? 'bg-indigo-600/35 text-white font-semibold shadow-md border-r-2 border-indigo-400' : ''); ?>">
                            <i class="fas fa-plug w-4"></i>
                            <span>بوابات الدفع</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <li>
                        <a href="<?php echo e(route('admin.invoices.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.invoices.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-file-invoice w-4"></i>
                            <span><?php echo e(__('admin.invoices')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.payments.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.payments.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-credit-card w-4"></i>
                            <span><?php echo e(__('admin.payments')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.transactions.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.transactions.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-exchange-alt w-4"></i>
                            <span><?php echo e(__('admin.transactions')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.wallets.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.wallets.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-wallet w-4"></i>
                            <span><?php echo e(__('admin.wallets')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.salaries.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.salaries.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-money-check-alt w-4"></i>
                            <span><?php echo e(__('admin.instructor_finances')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.accounting.instructor-accounts.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.accounting.instructor-accounts.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-user-tie w-4"></i>
                            <span>حسابات المدربين</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.employee-agreements.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.employee-agreements.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-users-cog w-4"></i>
                            <span>اتفاقيات الموظفين ورواتبهم</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.employee-salaries.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.employee-salaries.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-money-check-alt w-4"></i>
                            <span>رواتب الموظفين</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.expenses.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.expenses.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-receipt w-4"></i>
                            <span><?php echo e(__('admin.expenses')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.subscriptions.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.subscriptions.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-calendar-check w-4"></i>
                            <span><?php echo e(__('admin.subscriptions')); ?></span>
                        </a>
                    </li>
                    <?php
                        $installmentsOpen = request()->routeIs('admin.installments.*');
                    ?>
                    <li x-data="{ open: <?php echo e($installmentsOpen ? 'true' : 'false'); ?> }">
                        <button type="button" @click="open = !open" :aria-expanded="open"
                                class="flex items-center justify-between w-full px-4 py-2.5 rounded-lg transition-all duration-300 text-slate-400 hover:bg-slate-700/50 hover:text-white">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-calendar-check w-4 text-slate-400"></i>
                                <span class="font-medium text-sm"><?php echo e(__('admin.installment_management')); ?></span>
                            </span>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                            <li>
                                <a href="<?php echo e(route('admin.installments.plans.index')); ?>"
                                   @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                                   class="flex items-center gap-2 px-4 py-2 text-xs rounded-lg transition-all duration-300 <?php echo e(request()->routeIs('admin.installments.plans.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : 'text-slate-400 hover:bg-slate-700/50 hover:text-white'); ?>">
                                    <i class="fas fa-layer-group w-3.5"></i>
                                    <span><?php echo e(__('admin.installment_plans')); ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo e(route('admin.installments.agreements.index')); ?>"
                                   @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                                   class="flex items-center gap-2 px-4 py-2 text-xs rounded-lg transition-all duration-300 <?php echo e(request()->routeIs('admin.installments.agreements.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : 'text-slate-400 hover:bg-slate-700/50 hover:text-white'); ?>">
                                    <i class="fas fa-handshake w-3.5"></i>
                                    <span><?php echo e(__('admin.payment_agreements')); ?></span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.accounting.reports')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.accounting.reports') || request()->routeIs('admin.accounting.reports.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-chart-pie w-4"></i>
                            <span><?php echo e(__('admin.accounting_reports')); ?></span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- إدارة التسويق -->
            <?php
                $marketingOpen = request()->routeIs('admin.coupons.*') || request()->routeIs('admin.referral-programs.*') || request()->routeIs('admin.workshop-promo-codes.*') || request()->routeIs('admin.referrals.*') || request()->routeIs('admin.loyalty.*') || request()->routeIs('admin.personal-branding.*') || request()->routeIs('admin.popup-ads.*');
            ?>
            <li x-data="{ open: <?php echo e($marketingOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="open = !open" :aria-expanded="open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-tags w-5 text-slate-400 group-hover:text-white"></i>
                        <span class="font-medium"><?php echo e(__('admin.marketing')); ?></span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <li>
                        <a href="<?php echo e(route('admin.popup-ads.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.popup-ads.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-bullhorn w-4"></i>
                            <span><?php echo e(__('admin.popup_ads')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.personal-branding.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.personal-branding.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-user-tie w-4"></i>
                            <span><?php echo e(__('admin.personal_branding')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.coupons.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.coupons.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-ticket-alt w-4"></i>
                            <span><?php echo e(__('admin.coupons_discounts')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.referral-programs.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.referral-programs.*') || request()->routeIs('admin.workshop-promo-codes.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-gift w-4"></i>
                            <span><?php echo e(__('admin.referral_programs')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.referrals.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.referrals.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-user-friends w-4"></i>
                            <span><?php echo e(__('admin.referrals')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.loyalty.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.loyalty.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-star w-4"></i>
                            <span><?php echo e(__('admin.loyalty_programs')); ?></span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- إدارة التسجيلات -->
            <?php
                $enrollmentsOpen = request()->routeIs('admin.online-enrollments.*') || request()->routeIs('admin.learning-path-enrollments.*');
            ?>
            <li x-data="{ open: <?php echo e($enrollmentsOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="open = !open" :aria-expanded="open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-user-graduate w-5 text-emerald-400 group-hover:text-white"></i>
                        <span class="font-medium"><?php echo e(__('admin.enrollments')); ?></span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <li>
                        <a href="<?php echo e(route('admin.online-enrollments.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.online-enrollments.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-laptop w-4"></i>
                            <span><?php echo e(__('admin.online_enrollments')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.learning-path-enrollments.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.learning-path-enrollments.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-route w-4"></i>
                            <span><?php echo e(__('admin.learning_path_enrollments')); ?></span>
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
                $contentManagementOpen = request()->routeIs('admin.academic-years.*') || request()->routeIs('admin.learning-paths.*') || request()->routeIs('admin.academic-subjects.*') || request()->routeIs('admin.advanced-courses.*') || request()->routeIs('admin.exams.*') || request()->routeIs('admin.practice.*') || request()->routeIs('admin.question-bank.*') || request()->routeIs('admin.question-categories.*') || request()->routeIs('admin.lectures.*') || request()->routeIs('admin.groups.*') || request()->routeIs('admin.assignments.*');
            ?>
            <li x-data="{ open: <?php echo e($contentManagementOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="open = !open" :aria-expanded="open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-folder w-5 text-slate-400 group-hover:text-white"></i>
                        <span class="font-medium"><?php echo e(__('admin.content_management')); ?></span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <li class="pt-2 pb-1">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-400 px-4 py-1 uppercase tracking-wider">
                            <i class="fas fa-route"></i>
                            <?php echo e(__('admin.learning_catalog')); ?>

                        </div>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.academic-years.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.academic-years.*') && !request()->routeIs('admin.learning-paths.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-compass w-4"></i>
                            <span><?php echo e(__('admin.learning_paths')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.learning-paths.courses.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.learning-paths.courses.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-graduation-cap w-4"></i>
                            <span><?php echo e(__('admin.register_courses_paths')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.learning-paths.instructors.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.learning-paths.instructors.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-user-tie w-4"></i>
                            <span><?php echo e(__('admin.assign_instructors_paths')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.academic-subjects.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.academic-subjects.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-layer-group w-4"></i>
                            <span><?php echo e(__('admin.skill_groups')); ?></span>
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
                            <span><?php echo e(__('admin.courses_management')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.lectures.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.lectures.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-video w-4"></i>
                            <span><?php echo e(__('admin.lectures')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.groups.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.groups.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-users-cog w-4"></i>
                            <span><?php echo e(__('admin.groups')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.assignments.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.assignments.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-tasks w-4"></i>
                            <span><?php echo e(__('admin.assignments_projects')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.exams.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.exams.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-clipboard-check w-4"></i>
                            <span><?php echo e(__('admin.exams')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.practice.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.practice.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-dumbbell w-4"></i>
                            <span>Practice (التمارين)</span>
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
                            <span><?php echo e(__('admin.question_bank')); ?></span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- مجتمع البيانات والذكاء الاصطناعي (مسابقات، داتاسيت، مناقشات) -->
            <?php
                $communityOpen = request()->routeIs('admin.community.*');
            ?>
            <li x-data="{ open: <?php echo e($communityOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="open = !open" :aria-expanded="open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-users-cog w-5 text-cyan-400 group-hover:text-white"></i>
                        <span class="font-medium"><?php echo e(__('admin.community_section')); ?></span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <li class="pt-1">
                        <p class="px-4 py-1 text-xs font-bold text-cyan-400/90 uppercase tracking-wide">مراقبة عامة</p>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.community.dashboard')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.community.dashboard') || request()->routeIs('admin.community') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-tachometer-alt w-4"></i>
                            <span>لوحة المراقبة</span>
                        </a>
                    </li>
                    <li class="pt-2">
                        <p class="px-4 py-1 text-xs font-bold text-cyan-400/90 uppercase tracking-wide">المحتوى</p>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.community.competitions.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.community.competitions.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-trophy w-4"></i>
                            <span><?php echo e(__('admin.community_competitions')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.community.datasets.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.community.datasets.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-database w-4"></i>
                            <span><?php echo e(__('admin.community_datasets')); ?></span>
                        </a>
                    </li>
                    <li class="pt-2">
                        <p class="px-4 py-1 text-xs font-bold text-cyan-400/90 uppercase tracking-wide">المساهمون والمراجعة</p>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.community.submissions.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.community.submissions.index') || request()->routeIs('admin.community.submissions.dataset.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-paper-plane w-4"></i>
                            <span>مراجعة تقديمات البيانات</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.community.submissions.models.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.community.submissions.models.*') || request()->routeIs('admin.community.submissions.model.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-brain w-4"></i>
                            <span>تقديمات النماذج (Model Zoo)</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.community.contributors.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.community.contributors.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-user-plus w-4"></i>
                            <span>إنشاء وإدارة المساهمين</span>
                        </a>
                    </li>
                    <li class="pt-2">
                        <p class="px-4 py-1 text-xs font-bold text-cyan-400/90 uppercase tracking-wide">أخرى</p>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.community.discussions.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.community.discussions.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-comments w-4"></i>
                            <span><?php echo e(__('admin.community_discussions')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.community.notifications.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.community.notifications.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-bell w-4"></i>
                            <span>إرسال إشعارات للمجتمع</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.community.settings.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.community.settings.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-cog w-4"></i>
                            <span><?php echo e(__('admin.community_settings')); ?></span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- إدارة الأونلاين (كورسات وجداول حضوري/أونلاين) -->
            <?php
                $onlineManagementOpen = request()->routeIs('admin.online-management.*') || request()->routeIs('admin.online-course-bookings.*');
            ?>
            <li x-data="{ open: <?php echo e($onlineManagementOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="open = !open" :aria-expanded="open"
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-laptop-house w-5 text-indigo-400 group-hover:text-white"></i>
                        <span class="font-medium">إدارة الأونلاين</span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <li>
                        <a href="<?php echo e(route('admin.online-management.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.online-management.index') || request()->routeIs('admin.online-management.courses.*') ? 'bg-indigo-600/35 text-white font-semibold border-r-2 border-indigo-400' : ''); ?>">
                            <i class="fas fa-book-open w-4"></i>
                            <span>كورسات الأونلاين</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.online-management.courses.create')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.online-management.courses.create') ? 'bg-indigo-600/35 text-white font-semibold border-r-2 border-indigo-400' : ''); ?>">
                            <i class="fas fa-plus-circle w-4"></i>
                            <span>كورس أونلاين فقط (جديد)</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.online-management.enroll')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.online-management.enroll') ? 'bg-emerald-600/35 text-white font-semibold border-r-2 border-emerald-400' : ''); ?>">
                            <i class="fas fa-user-check w-4"></i>
                            <span>تسجيل طالب (إيميل)</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.online-course-bookings.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.online-course-bookings.*') ? 'bg-indigo-600/35 text-white font-semibold border-r-2 border-indigo-400' : ''); ?>">
                            <i class="fas fa-inbox w-4"></i>
                            <span>حجوزات الأونلاين</span>
                            <?php
                                try {
                                    $pendingOnlineBookingsMenu2 = \App\Models\OfflineCourseBooking::where('status', 'pending')->where('booking_channel', 'online')->count();
                                } catch (\Exception $e) {
                                    $pendingOnlineBookingsMenu2 = 0;
                                }
                            ?>
                            <?php if($pendingOnlineBookingsMenu2 > 0): ?>
                                <span class="mr-auto bg-amber-500 text-white text-xs font-bold rounded-full px-2 py-0.5"><?php echo e($pendingOnlineBookingsMenu2); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- إدارة الكورسات الأوفلاين -->
            <?php
                $offlineCoursesOpen = request()->routeIs('admin.offline-courses.*') || request()->routeIs('admin.offline-groups.*') || request()->routeIs('admin.offline-enrollments.*') || request()->routeIs('admin.offline-course-bookings.*') || request()->routeIs('admin.offline-activities.*') || request()->routeIs('admin.offline-agreements.*') || request()->routeIs('admin.offline-locations.*') || request()->routeIs('admin.place-usage-logs.*') || request()->routeIs('admin.place-settlements.*');
            ?>
            <li x-data="{ open: <?php echo e($offlineCoursesOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="open = !open" :aria-expanded="open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-chalkboard-teacher w-5 text-purple-400 group-hover:text-white"></i>
                        <span class="font-medium"><?php echo e(__('admin.offline_courses')); ?></span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <li>
                        <a href="<?php echo e(route('admin.offline-locations.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.offline-locations.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-map-marker-alt w-4"></i>
                            <span><?php echo e(__('admin.manage_locations')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.place-usage-logs.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.place-usage-logs.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-clock w-4"></i>
                            <span>ساعات الأماكن</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.place-settlements.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.place-settlements.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-calendar-check w-4"></i>
                            <span>مخالصات الأماكن</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.offline-courses.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.offline-courses.index') || request()->routeIs('admin.offline-courses.show') || request()->routeIs('admin.offline-courses.create') || request()->routeIs('admin.offline-courses.edit') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-book-reader w-4"></i>
                            <span><?php echo e(__('admin.offline_courses_list')); ?></span>
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
                        <a href="<?php echo e(route('admin.offline-enrollments.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.offline-enrollments.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-user-check w-4"></i>
                            <span><?php echo e(__('admin.offline_enrollments')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.offline-course-bookings.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.offline-course-bookings.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-calendar-check w-4"></i>
                            <span>حجوزات أوفلاين</span>
                            <?php
                                try {
                                    $pendingOfflineBookingsMenu = \App\Models\OfflineCourseBooking::where('status', 'pending')->where('booking_channel', 'offline')->count();
                                } catch (\Exception $e) {
                                    $pendingOfflineBookingsMenu = 0;
                                }
                            ?>
                            <?php if($pendingOfflineBookingsMenu > 0): ?>
                                <span class="mr-auto bg-amber-500 text-white text-xs font-bold rounded-full px-2 py-0.5"><?php echo e($pendingOfflineBookingsMenu); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.offline-agreements.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.offline-agreements.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-file-contract w-4"></i>
                            <span><?php echo e(__('admin.instructor_agreements')); ?></span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- إدارة الموظفين -->
            <?php
                $employeesOpen = request()->routeIs('admin.employees.*') || request()->routeIs('admin.employee-jobs.*') || request()->routeIs('admin.employee-tasks.*') || request()->routeIs('admin.design-task-cycles.*') || request()->routeIs('admin.moderator-marketing-plans.*') || request()->routeIs('admin.employee-deductions.*') || request()->routeIs('admin.employee-additions.*') || request()->routeIs('admin.employee-daily-reports.*') || request()->routeIs('admin.leaves.*') || request()->routeIs('admin.tasks.*') || request()->routeIs('admin.instructor-requests.*');
            ?>
            <li x-data="{ open: <?php echo e($employeesOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="open = !open" :aria-expanded="open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-users-cog w-5 text-emerald-400 group-hover:text-white"></i>
                        <span class="font-medium"><?php echo e(__('admin.management')); ?></span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <li>
                        <a href="<?php echo e(route('admin.employees.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.employees.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-user-tie w-4"></i>
                            <span><?php echo e(__('admin.employees')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.employee-jobs.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.employee-jobs.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-briefcase w-4"></i>
                            <span><?php echo e(__('admin.jobs')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.employee-tasks.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.employee-tasks.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-tasks w-4"></i>
                            <span><?php echo e(__('admin.employee_tasks')); ?></span>
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
                        <a href="<?php echo e(route('admin.design-task-cycles.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.design-task-cycles.index') || request()->routeIs('admin.design-task-cycles.show') || request()->routeIs('admin.design-task-cycles.create') || request()->routeIs('admin.design-task-cycles.edit') ? 'bg-fuchsia-600/30 text-white font-semibold border-r-2 border-fuchsia-400' : ''); ?>">
                            <i class="fas fa-palette w-4"></i>
                            <span>دورات التصميم (مشرف/مصمم)</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.design-task-cycles.performance-report')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.design-task-cycles.performance-report') ? 'bg-fuchsia-600/30 text-white font-semibold border-r-2 border-fuchsia-400' : ''); ?>">
                            <i class="fas fa-chart-pie w-4"></i>
                            <span>تقرير أداء التصميم (شهري)</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.moderator-marketing-plans.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.moderator-marketing-plans.*') ? 'bg-pink-600/30 text-white font-semibold border-r-2 border-pink-400' : ''); ?>">
                            <i class="fas fa-bullhorn w-4"></i>
                            <span>خطط تسويق المشرفين</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.employee-deductions.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.employee-deductions.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-minus-circle w-4"></i>
                            <span><?php echo e(__('admin.employee_deductions')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.employee-additions.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.employee-additions.*') ? 'bg-emerald-600/30 text-white font-semibold border-r-2 border-emerald-400' : ''); ?>">
                            <i class="fas fa-plus-circle w-4"></i>
                            <span>إضافات الموظفين</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.employee-daily-reports.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.employee-daily-reports.*') ? 'bg-sky-600/30 text-white font-semibold border-r-2 border-sky-400' : ''); ?>">
                            <i class="fas fa-clipboard-check w-4"></i>
                            <span>التقارير اليومية</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.tasks.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.tasks.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-chalkboard-teacher w-4"></i>
                            <span><?php echo e(__('admin.instructor_tasks')); ?></span>
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
                            <span><?php echo e(__('admin.instructor_requests_join')); ?></span>
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
                            <span><?php echo e(__('admin.leaves')); ?></span>
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
                <button type="button" @click="open = !open" :aria-expanded="open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-shield-alt w-5 text-red-400 group-hover:text-white"></i>
                        <span class="font-medium"><?php echo e(__('admin.quality_supervision')); ?></span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <li>
                        <a href="<?php echo e(route('admin.quality-control.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.quality-control.index') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-tachometer-alt w-4"></i>
                            <span><?php echo e(__('admin.control_panel')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.quality-control.students')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.quality-control.students') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-user-graduate w-4"></i>
                            <span><?php echo e(__('admin.student_control')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.quality-control.instructors')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.quality-control.instructors') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-chalkboard-teacher w-4"></i>
                            <span><?php echo e(__('admin.instructor_control')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.quality-control.employees')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.quality-control.employees') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-user-tie w-4"></i>
                            <span><?php echo e(__('admin.employee_control')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.quality-control.operations')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.quality-control.operations') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-cogs w-4"></i>
                            <span><?php echo e(__('admin.operations_followup')); ?></span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- التحكم في الشهادات -->
            <?php
                $certificatesManagementOpen = request()->routeIs('admin.certificates.*');
            ?>
            <li x-data="{ open: <?php echo e($certificatesManagementOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="open = !open" :aria-expanded="open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-certificate w-5 text-slate-400 group-hover:text-white"></i>
                        <span class="font-medium"><?php echo e(__('admin.certificates_control')); ?></span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <li>
                        <a href="<?php echo e(route('admin.certificates.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.certificates.index') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-list w-4"></i>
                            <span><?php echo e(__('admin.certificates_list')); ?></span>
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
                            <span><?php echo e(__('admin.issue_certificate')); ?></span>
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
                            <span><?php echo e(__('admin.pending_certificates')); ?></span>
                            <span class="mr-auto bg-yellow-400 text-yellow-900 text-xs font-bold rounded-full px-2 py-0.5 shadow-lg"><?php echo e($pendingCertificates); ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </li>

            <!-- الإنجازات والشارات -->
            <?php
                $achievementsOpen = request()->routeIs('admin.achievements.*') || request()->routeIs('admin.badges.*') || request()->routeIs('admin.reviews.*') || request()->routeIs('admin.learning-path-reviews.*');
            ?>
            <li x-data="{ open: <?php echo e($achievementsOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="open = !open" :aria-expanded="open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-trophy w-5 text-slate-400 group-hover:text-white"></i>
                        <span class="font-medium"><?php echo e(__('admin.achievements_badges')); ?></span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <li>
                        <a href="<?php echo e(route('admin.achievements.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.achievements.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-medal w-4"></i>
                            <span><?php echo e(__('admin.achievements')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.badges.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.badges.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-award w-4"></i>
                            <span><?php echo e(__('admin.badges')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.reviews.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.reviews.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-star-half-alt w-4"></i>
                            <span><?php echo e(__('admin.reviews_ratings')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.learning-path-reviews.index')); ?>"
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.learning-path-reviews.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-route w-4"></i>
                            <span>تقييمات المسارات</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- إدارة الصلاحيات والأدوار -->
            <?php
                $permissionsOpen = request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') || request()->routeIs('admin.user-permissions.*');
            ?>
            <li x-data="{ open: <?php echo e($permissionsOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="open = !open" :aria-expanded="open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-shield-alt w-5 text-slate-400 group-hover:text-white"></i>
                        <span class="font-medium"><?php echo e(__('admin.permissions_roles')); ?></span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <li>
                        <a href="<?php echo e(route('admin.roles.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.roles.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-user-tag w-4"></i>
                            <span><?php echo e(__('admin.roles')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.permissions.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.permissions.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-key w-4"></i>
                            <span><?php echo e(__('admin.permissions')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.user-permissions.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.user-permissions.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-user-shield w-4"></i>
                            <span><?php echo e(__('admin.user_permissions')); ?></span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- إدارة الصفحات الخارجية -->
            <?php
                $blogOpen = request()->routeIs('admin.blog.*') || request()->routeIs('admin.contact-messages.*') || request()->routeIs('admin.packages.*');
            ?>
            <li x-data="{ open: <?php echo e($blogOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="open = !open" :aria-expanded="open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-globe w-5 text-slate-400 group-hover:text-white"></i>
                        <span class="font-medium"><?php echo e(__('admin.external_pages')); ?></span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <li>
                        <a href="<?php echo e(route('admin.blog.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.blog.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-newspaper w-4"></i>
                            <span><?php echo e(__('admin.blog')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.portfolio.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.portfolio.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-briefcase w-4"></i>
                            <span><?php echo e(__('admin.portfolio')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.contact-messages.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.contact-messages.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-envelope w-4"></i>
                            <span><?php echo e(__('admin.contact_messages')); ?></span>
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
                            <span><?php echo e(__('admin.pricing_packages')); ?></span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- الإدارة العليا (من نحن) -->
            <?php
                $topManagementOpen = request()->routeIs('admin.about.*');
            ?>
            <li x-data="{ open: <?php echo e($topManagementOpen ? 'true' : 'false'); ?> }">
                <button type="button" @click="open = !open" :aria-expanded="open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-building w-5 text-amber-400 group-hover:text-white"></i>
                        <span class="font-medium"><?php echo e(__('admin.top_management')); ?></span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <li>
                        <a href="<?php echo e(route('admin.about.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.about.*') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-info-circle w-4"></i>
                            <span><?php echo e(__('admin.about_page')); ?></span>
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
                    <span class="relative z-10 font-semibold"><?php echo e(__('admin.tasks')); ?></span>
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
                    <span class="relative z-10 font-semibold"><?php echo e(__('admin.messages')); ?></span>
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
                <button type="button" @click="open = !open" :aria-expanded="open" 
                        class="flex items-center justify-between w-full px-4 py-3 rounded-xl hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white group">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-file-excel w-5 text-emerald-400 group-hover:text-white"></i>
                        <span class="font-medium"><?php echo e(__('admin.comprehensive_reports')); ?></span>
                    </div>
                    <i class="fas fa-chevron-down transition-transform duration-300 text-slate-400" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-transition x-cloak class="admin-sidebar-sub">
                    <li>
                        <a href="<?php echo e(route('admin.reports.index')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.reports.index') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-chart-pie w-4"></i>
                            <span><?php echo e(__('admin.reports_dashboard')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.reports.users')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.reports.users') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-users w-4"></i>
                            <span><?php echo e(__('admin.user_reports')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.reports.courses')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.reports.courses') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-graduation-cap w-4"></i>
                            <span><?php echo e(__('admin.course_reports')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.reports.financial')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.reports.financial') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-money-bill-wave w-4"></i>
                            <span><?php echo e(__('admin.financial_reports')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.reports.academic')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.reports.academic') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-book w-4"></i>
                            <span><?php echo e(__('admin.academic_reports')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.reports.activities')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.reports.activities') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-history w-4"></i>
                            <span><?php echo e(__('admin.activity_reports')); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.reports.comprehensive')); ?>" 
                           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"
                           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg hover:bg-slate-700/50 transition-all duration-300 text-slate-300 hover:text-white <?php echo e(request()->routeIs('admin.reports.comprehensive') ? 'bg-blue-600/30 text-white font-semibold shadow-md border-r-2 border-blue-500' : ''); ?>">
                            <i class="fas fa-file-alt w-4"></i>
                            <span><?php echo e(__('admin.comprehensive_report')); ?></span>
                        </a>
                    </li>
                </ul>
            </li>

        </ul>
    </nav>

    <!-- معلومات المستخدم -->
    <div class="p-4 border-t-2 border-slate-700/50 bg-gradient-to-br from-slate-800/90 via-slate-800/80 to-slate-900/90 flex-shrink-0 backdrop-blur-sm">
        <div class="flex items-center gap-3 p-3 bg-slate-700/40 backdrop-blur-sm rounded-xl shadow-xl border border-slate-600/50 hover:bg-slate-700/60 transition-all duration-300 group">
            <?php if(auth()->user()?->profile_image): ?>
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