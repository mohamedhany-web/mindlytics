<div class="flex flex-col h-full bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 shadow-2xl border-l border-slate-700/50 text-white">
    <!-- شعار المنصة -->
    <div class="p-6 border-b-2 border-slate-700/50 bg-slate-900/90 flex-shrink-0">
        <a href="<?php echo e(route('employee.dashboard')); ?>" class="flex items-center gap-4">
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
                <p class="text-xs text-slate-300/80 font-bold">لوحة الموظف</p>
            </div>
        </a>
    </div>

    <!-- Navigation (التمرير يعمل لكن شريط التمرير مخفي) -->
    <nav class="flex-1 overflow-y-auto overflow-x-hidden px-4 py-6 space-y-2 employee-sidebar-nav">
        <a href="<?php echo e(route('employee.dashboard')); ?>" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('employee.dashboard') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-home text-base"></i>
            <span>لوحة التحكم</span>
        </a>

        <a href="<?php echo e(route('employee.tasks.index')); ?>" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('employee.tasks.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-tasks text-base"></i>
            <span>مهامي</span>
        </a>

        <?php if(auth()->user()->isModeratorEmployee()): ?>
        <div class="border-t border-slate-700/50 my-2 pt-2">
            <p class="px-4 text-xs font-semibold text-fuchsia-400/90 uppercase tracking-wider mb-1">المشرف — التصميم</p>
            <a href="<?php echo e(route('employee.design-cycles.index')); ?>"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('employee.design-cycles.*') ? 'bg-fuchsia-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-palette text-base"></i>
                <span>طلبات التصميم</span>
            </a>
            <a href="<?php echo e(route('employee.marketing-plans.index')); ?>"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('employee.marketing-plans.*') ? 'bg-pink-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-bullhorn text-base"></i>
                <span>التسويق والمنصات</span>
            </a>
        </div>
        <?php endif; ?>

        <?php if(auth()->user()->isSalesEmployee()): ?>
        <div class="border-t border-slate-700/50 my-2 pt-2">
            <p class="px-4 text-xs font-semibold text-emerald-400/90 uppercase tracking-wider mb-1">المبيعات</p>
            <a href="<?php echo e(route('employee.sales.dashboard')); ?>"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('employee.sales.dashboard') ? 'bg-emerald-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-chart-line text-base"></i>
                <span>مركز المبيعات</span>
            </a>
            <a href="<?php echo e(route('employee.sales.daily-reports.index')); ?>"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('employee.sales.daily-reports.*') ? 'bg-emerald-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-clipboard-check text-base"></i>
                <span>التقرير اليومي</span>
            </a>
            <a href="<?php echo e(route('employee.marketing-today.index')); ?>"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('employee.marketing-today.*') ? 'bg-emerald-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-bullhorn text-base"></i>
                <span>تسويق اليوم</span>
            </a>
            <a href="<?php echo e(route('employee.sales.kpi.index')); ?>"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('employee.sales.kpi.*') ? 'bg-emerald-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-bullseye text-base"></i>
                <span>KPIs والأداء</span>
            </a>
            <a href="<?php echo e(route('employee.sales.commissions.index')); ?>"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('employee.sales.commissions.*') ? 'bg-emerald-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-coins text-base"></i>
                <span>العمولات</span>
            </a>
            <a href="<?php echo e(route('employee.sales.reports.index')); ?>"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('employee.sales.reports.*') ? 'bg-emerald-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-chart-bar text-base"></i>
            <span>تقارير الأداء</span>
            </a>
            <a href="<?php echo e(route('employee.sales.leads.index')); ?>"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('employee.sales.leads.*') ? 'bg-emerald-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-user-plus text-base"></i>
                <span>العملاء المحتملون</span>
            </a>
            <a href="<?php echo e(route('employee.sales.groups.index')); ?>"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('employee.sales.groups.*') ? 'bg-emerald-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-layer-group text-base"></i>
                <span>مجموعات العملاء</span>
            </a>
        </div>
        <?php endif; ?>

        <?php if(!auth()->user()->isSalesEmployee()): ?>
        <a href="<?php echo e(route('employee.daily-reports.index')); ?>"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('employee.daily-reports.*') ? 'bg-sky-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-clipboard-list text-base"></i>
            <span>التقرير اليومي</span>
        </a>
        <?php endif; ?>

        <a href="<?php echo e(route('employee.leaves.index')); ?>" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('employee.leaves.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-calendar-alt text-base"></i>
            <span>الإجازات</span>
        </a>

        <div class="border-t border-slate-700/50 my-4"></div>

        <!-- قسم المحاسبة -->
        <div class="space-y-2">
            <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">المحاسبة</p>
            
            <a href="<?php echo e(route('employee.accounting.index')); ?>" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('employee.accounting.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-calculator text-base"></i>
                <span>الراتب والمحاسبة</span>
            </a>

            <a href="<?php echo e(route('employee.agreements.index')); ?>" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('employee.agreements.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-file-contract text-base"></i>
                <span>اتفاقياتي</span>
            </a>
        </div>

        <div class="border-t border-slate-700/50 my-4"></div>

        <a href="<?php echo e(route('employee.profile')); ?>" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('employee.profile*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-user text-base"></i>
            <span>الملف الشخصي</span>
        </a>

        <a href="<?php echo e(route('employee.notifications')); ?>" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('employee.notifications*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-bell text-base"></i>
            <span>الإشعارات</span>
            <?php
                try {
                    $unreadCount = auth()->user()->notifications()->whereNull('read_at')->count();
                } catch (\Exception $e) {
                    $unreadCount = 0;
                }
            ?>
            <?php if($unreadCount > 0): ?>
                <span class="mr-auto bg-red-500 text-white text-xs font-bold rounded-full px-2 py-0.5 min-w-[20px] text-center"><?php echo e($unreadCount > 99 ? '99+' : $unreadCount); ?></span>
            <?php endif; ?>
        </a>

        <?php if(!auth()->user()->isSalesEmployee()): ?>
        <a href="<?php echo e(route('employee.marketing-today.index')); ?>"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('employee.marketing-today.*') ? 'bg-pink-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-bullhorn text-base"></i>
            <span>تسويق اليوم</span>
        </a>
        <?php endif; ?>

        <a href="<?php echo e(route('employee.calendar')); ?>" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('employee.calendar*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-calendar text-base"></i>
            <span>التقويم</span>
        </a>

        <a href="<?php echo e(route('employee.reports')); ?>" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('employee.reports*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-chart-bar text-base"></i>
            <span>التقارير والإحصائيات</span>
        </a>

        <a href="<?php echo e(route('employee.documentation')); ?>"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('employee.documentation') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-book text-base"></i>
            <span>Documentation</span>
        </a>

        <div class="border-t border-slate-700/50 my-4"></div>

        <a href="<?php echo e(route('employee.settings')); ?>" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 <?php echo e(request()->routeIs('employee.settings*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white'); ?>"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-cog text-base"></i>
            <span>الإعدادات</span>
        </a>
    </nav>

    <!-- User Info -->
    <div class="border-t border-slate-700/50 p-4">
        <div class="flex items-center gap-3 mb-3">
            <?php
                $user = auth()->user();
                $profileImage = $user->profile_image_url;
            ?>
            <?php if($profileImage): ?>
                <img src="<?php echo e($profileImage); ?>" alt="<?php echo e($user->name); ?>" class="w-10 h-10 rounded-full object-cover border-2 border-blue-400 flex-shrink-0">
            <?php else: ?>
                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                    <?php echo e(mb_substr($user->name, 0, 1, 'UTF-8')); ?>

                </div>
            <?php endif; ?>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white truncate"><?php echo e($user->name); ?></p>
                <p class="text-xs text-slate-400 truncate">موظف</p>
            </div>
        </div>
        <form method="POST" action="<?php echo e(route('logout')); ?>" class="w-full">
            <?php echo csrf_field(); ?>
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-slate-700/50 hover:bg-slate-700 text-white text-sm font-semibold rounded-xl transition-colors">
                <i class="fas fa-sign-out-alt"></i>
                <span>تسجيل الخروج</span>
            </button>
        </form>
    </div>
</div>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/layouts/employee-sidebar.blade.php ENDPATH**/ ?>