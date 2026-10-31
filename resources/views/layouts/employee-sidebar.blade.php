@php
    $sidebarUser = auth()->user();
    $hasSalesManagerPortal = $sidebarUser?->hasSalesManagerPortalAccess() ?? false;
    $hasModeratorPortal = $sidebarUser?->hasModeratorPortalAccess() ?? false;
    $isBusinessDeveloper = $sidebarUser?->isBusinessDeveloper() ?? false;
    $salesHome = ($sidebarUser?->isSalesStaff() ?? false) && Route::has('employee.crm.home') ? route('employee.crm.home') : route('employee.dashboard');
@endphp
<div class="flex flex-col h-full bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 shadow-2xl border-l border-slate-700/50 text-white">
    <!-- شعار المنصة -->
    <div class="p-6 border-b-2 border-slate-700/50 bg-slate-900/90 flex-shrink-0 emp-sidebar-logo-wrap">
        <a href="{{ $salesHome }}" class="flex items-center gap-4" title="Mindlytics">
            <div class="relative">
                <div class="w-16 h-16 rounded-full flex items-center justify-center shadow-xl overflow-hidden p-1">
                    <img src="{{ $platformLogoUrl ?? asset('logo-fallback.svg') }}"
                         alt="{{ config('app.name') }}"
                         class="w-full h-full object-cover object-center rounded-full"
                         onerror="this.onerror=null;this.src='{{ asset('logo-fallback.svg') }}';">
                </div>
            </div>
            <div class="emp-sidebar-logo-text">
                <h2 class="text-xl font-black bg-gradient-to-r from-blue-300 via-blue-200 to-blue-100 bg-clip-text text-transparent tracking-tight">Mindlytics</h2>
                <p class="text-xs text-slate-300/80 font-bold emp-sidebar-label">لوحة الموظف</p>
            </div>
        </a>
    </div>

    <!-- Navigation (التمرير يعمل لكن شريط التمرير مخفي) -->
    <nav class="flex-1 overflow-y-auto overflow-x-hidden px-4 py-6 space-y-2 employee-sidebar-nav">
        @if(($sidebarUser?->isSalesStaff() ?? false) && Route::has('employee.crm.home'))
        <a href="{{ route('employee.crm.home') }}"
           title="CRM المبيعات"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.crm.*') || request()->routeIs('employee.sales.*') || request()->routeIs('employee.sales-manager.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-200 bg-slate-700/40 hover:bg-slate-700/70 hover:text-white' }}"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-table-cells-large text-base"></i>
            <span>CRM المبيعات</span>
        </a>
        @else
        <a href="{{ route('employee.dashboard') }}" 
           title="لوحة التحكم"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.dashboard') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-home text-base"></i>
            <span>لوحة التحكم</span>
        </a>
        @endif

        <a href="{{ route('employee.tasks.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.tasks.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-tasks text-base"></i>
            <span>مهامي</span>
        </a>

        @if($isBusinessDeveloper)
        <div class="border-t border-slate-700/50 my-2 pt-2">
            <p class="px-4 text-xs font-semibold text-violet-400/90 uppercase tracking-wider mb-1">Business Developer</p>
            <a href="{{ route('employee.business-developer.guide') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.business-developer.guide') ? 'bg-violet-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-compass text-base"></i>
                <span>دليل النظام</span>
            </a>
            <a href="{{ route('employee.business-developer.marketing') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.business-developer.marketing') ? 'bg-teal-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-bullhorn text-base"></i>
                <span>مركز التسويق</span>
            </a>
            <a href="{{ route('employee.marketing-plans.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.marketing-plans.*') ? 'bg-pink-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-diagram-project text-base"></i>
                <span>إدارة خطط التسويق</span>
            </a>
        </div>
        @endif

        @if($hasModeratorPortal)
        <div class="border-t border-slate-700/50 my-2 pt-2">
            <p class="px-4 text-xs font-semibold text-fuchsia-400/90 uppercase tracking-wider mb-1">{{ $isBusinessDeveloper ? 'Business Developer — الماركتينغ' : 'المشرف — التصميم والفيديو' }}</p>
            <a href="{{ route('employee.design-cycles.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.design-cycles.*') ? 'bg-fuchsia-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-palette text-base"></i>
                <span>طلبات التصميم</span>
            </a>
            <a href="{{ route('employee.montage-requests.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.montage-requests.*') ? 'bg-cyan-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-film text-base"></i>
                <span>طلبات محرر الفيديو</span>
            </a>
            <a href="{{ route('employee.marketing-plans.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.marketing-plans.*') && ! request()->routeIs('employee.business-developer.marketing') ? 'bg-pink-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-bullhorn text-base"></i>
                <span>{{ $isBusinessDeveloper ? 'خطط التسويق (تفصيلي)' : 'التسويق والمنصات' }}</span>
            </a>
        </div>
        @endif

        @if(auth()->user()->isSalesEmployee() || $hasSalesManagerPortal)
        <div class="border-t border-slate-700/50 my-2 pt-2">
            <p class="px-4 text-xs font-semibold text-emerald-400/90 uppercase tracking-wider mb-1">أدوات المبيعات</p>
            @if(Route::has('employee.crm.leads.index'))
            <a href="{{ route('employee.crm.leads.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.crm.leads.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}" @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"><i class="fas fa-user-plus text-base"></i><span>العملاء المحتملون</span></a>
            <a href="{{ route('employee.crm.opportunities.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.crm.opportunities.*') || request()->routeIs('employee.crm.deals.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}" @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"><i class="fas fa-handshake text-base"></i><span>الفرص</span></a>
            <a href="{{ route('employee.crm.inbox.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.crm.inbox.*') || request()->routeIs('*.whatsapp.*') || request()->routeIs('*.meta-social.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}" @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"><i class="fas fa-comments text-base"></i><span>المحادثات</span></a>
            <a href="{{ route('employee.crm.reports.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.crm.reports.*') || request()->routeIs('employee.sales.kpi.*') || request()->routeIs('employee.sales-manager.kpi.*') || request()->routeIs('employee.sales-manager.scorecard.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}" @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"><i class="fas fa-chart-line text-base"></i><span>التقارير و KPI</span></a>
            @endif
            @if(auth()->user()->isSalesEmployee())
            <a href="{{ route('employee.sales.groups.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.sales.groups.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}" @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"><i class="fas fa-layer-group text-base"></i><span>مجموعات العملاء</span></a>
            <a href="{{ route('employee.sales.daily-reports.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.sales.daily-reports.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}" @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"><i class="fas fa-clipboard-list text-base"></i><span>تقريري اليومي</span></a>
            <a href="{{ route('employee.sales.shifts.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.sales.shifts.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}" @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"><i class="fas fa-calendar-week text-base"></i><span>شيفتاتي</span></a>
            <a href="{{ route('employee.sales.course-board.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.sales.course-board.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}" @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"><i class="fas fa-book-open text-base"></i><span>معلومات الكورسات</span></a>
            <a href="{{ route('employee.sales.policy.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.sales.policy.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}" @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"><i class="fas fa-scale-balanced text-base"></i><span>قواعد المبيعات</span></a>
            @endif
            @if($hasSalesManagerPortal)
            <a href="{{ route('employee.sales-manager.scorecard.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.sales-manager.scorecard.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}" @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"><i class="fas fa-shield-halved text-base"></i><span>Scorecard</span></a>
            <a href="{{ route('employee.sales-manager.distribution.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.sales-manager.distribution.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}" @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"><i class="fas fa-share-alt text-base"></i><span>التوزيع</span></a>
            <a href="{{ route('employee.sales-manager.attendance.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.sales-manager.attendance.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}" @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"><i class="fas fa-clock text-base"></i><span>حضور الفريق</span></a>
            <a href="{{ route('employee.sales-manager.live-board') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.sales-manager.live-board') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}" @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"><i class="fas fa-tv text-base"></i><span>اللوحة الحية</span></a>
            <a href="{{ route('employee.sales-manager.ops-board') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.sales-manager.ops-board') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}" @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"><i class="fas fa-broadcast-tower text-base"></i><span>متابعة الفريق</span></a>
            <a href="{{ route('employee.sales-manager.whatsapp.queue.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.sales-manager.whatsapp.queue.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}" @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"><i class="fas fa-bell text-base"></i><span>طابور واتساب</span></a>
            <a href="{{ route('employee.sales-manager.org-chart.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.sales-manager.org-chart.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}" @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"><i class="fas fa-sitemap text-base"></i><span>هيكل الفريق</span></a>
            <a href="{{ route('employee.sales-manager.shift-swaps.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.sales-manager.shift-swaps.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}" @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }"><i class="fas fa-right-left text-base"></i><span>تبديل الشيفتات</span></a>
            @endif
        </div>
        @endif

        @if(!auth()->user()->isSalesEmployee() && !$hasSalesManagerPortal)
        <a href="{{ route('employee.daily-reports.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.daily-reports.*') ? 'bg-sky-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-clipboard-list text-base"></i>
            <span>التقرير اليومي</span>
        </a>
        @endif

        <a href="{{ route('employee.leaves.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.leaves.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-calendar-alt text-base"></i>
            <span>الإجازات</span>
        </a>

        <div class="border-t border-slate-700/50 my-4"></div>

        <!-- قسم المحاسبة -->
        <div class="space-y-2">
            <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">المحاسبة</p>
            
            <a href="{{ route('employee.accounting.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.accounting.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-calculator text-base"></i>
                <span>الراتب والمحاسبة</span>
            </a>

            <a href="{{ route('employee.agreements.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.agreements.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-file-contract text-base"></i>
                <span>اتفاقياتي</span>
            </a>
        </div>

        <div class="border-t border-slate-700/50 my-4"></div>

        <a href="{{ route('employee.profile') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.profile*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-user text-base"></i>
            <span>الملف الشخصي</span>
        </a>

        <a href="{{ route('employee.notifications') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.notifications*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-bell text-base"></i>
            <span>الإشعارات</span>
            @php
                try {
                    $unreadCount = auth()->user()->notifications()->whereNull('read_at')->count();
                } catch (\Exception $e) {
                    $unreadCount = 0;
                }
            @endphp
            @if($unreadCount > 0)
                <span class="mr-auto bg-red-500 text-white text-xs font-bold rounded-full px-2 py-0.5 min-w-[20px] text-center">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
            @endif
        </a>

        @if(!auth()->user()->isSalesEmployee())
        <a href="{{ route('employee.marketing-today.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.marketing-today.*') ? 'bg-pink-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-bullhorn text-base"></i>
            <span>تسويق اليوم</span>
        </a>
        @endif

        <a href="{{ route('employee.calendar') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.calendar*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-calendar text-base"></i>
            <span>التقويم</span>
        </a>

        <a href="{{ route('employee.reports') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.reports*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-chart-bar text-base"></i>
            <span>التقارير والإحصائيات</span>
        </a>

        @if(auth()->user()->isSalesStaff())
        <a href="{{ route('employee.documentation') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.documentation') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-book text-base"></i>
            <span>دليل المبيعات</span>
        </a>
        @endif

        <div class="border-t border-slate-700/50 my-4"></div>

        <a href="{{ route('employee.settings') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.settings*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-cog text-base"></i>
            <span>الإعدادات</span>
        </a>
    </nav>

    <!-- User Info -->
    <div class="border-t border-slate-700/50 p-4 emp-sidebar-footer">
        <div class="flex items-center gap-3 mb-3 emp-sidebar-user-row">
            @php
                $user = auth()->user();
                $profileImage = $user->profile_image_url;
            @endphp
            @if($profileImage)
                <img src="{{ $profileImage }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover border-2 border-blue-400 flex-shrink-0">
            @else
                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                    {{ mb_substr($user->name, 0, 1, 'UTF-8') }}
                </div>
            @endif
            <div class="flex-1 min-w-0 emp-sidebar-user-meta">
                <p class="text-sm font-semibold text-white truncate">{{ $user->name }}</p>
                <p class="text-xs text-slate-400 truncate">موظف</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" title="تسجيل الخروج" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-slate-700/50 hover:bg-slate-700 text-white text-sm font-semibold rounded-xl transition-colors">
                <i class="fas fa-sign-out-alt"></i>
                <span class="emp-sidebar-logout-text">تسجيل الخروج</span>
            </button>
        </form>
    </div>
</div>
