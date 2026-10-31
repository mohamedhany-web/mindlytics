<div class="flex flex-col h-full bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 shadow-2xl border-l border-slate-700/50 text-white">
    <!-- شعار المنصة -->
    <div class="p-6 border-b-2 border-slate-700/50 bg-slate-900/90 flex-shrink-0 emp-sidebar-logo-wrap">
        <a href="{{ route('employee.dashboard') }}" class="flex items-center gap-4" title="Mindlytics">
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
        @php
            $sidebarUser = auth()->user();
            $hasSalesManagerPortal = $sidebarUser?->hasSalesManagerPortalAccess() ?? false;
            $hasModeratorPortal = $sidebarUser?->hasModeratorPortalAccess() ?? false;
            $isBusinessDeveloper = $sidebarUser?->isBusinessDeveloper() ?? false;
        @endphp
        <a href="{{ route('employee.dashboard') }}" 
           title="لوحة التحكم"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.dashboard') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
           @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
            <i class="fas fa-home text-base"></i>
            <span>لوحة التحكم</span>
        </a>

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

        @if($hasSalesManagerPortal)
        @php $waQueueCount = app(\App\Domains\Messaging\Services\WhatsAppQueueService::class)->pendingCount(); @endphp
        <div class="border-t border-slate-700/50 my-2 pt-2">
            <p class="px-4 text-xs font-semibold text-amber-400/90 uppercase tracking-wider mb-1">واتساب — طلبات</p>
            <a href="{{ route('employee.sales-manager.whatsapp.queue.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.sales-manager.whatsapp.queue.*') ? 'bg-amber-500 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-bell text-base"></i>
                <span class="flex-1">طلبات جديدة</span>
                <span id="wa-queue-badge" class="min-w-[1.25rem] h-5 px-1.5 rounded-full text-xs font-bold flex items-center justify-center {{ $waQueueCount > 0 ? 'bg-amber-500 text-white' : 'hidden' }}">{{ $waQueueCount }}</span>
            </a>
        </div>
        @endif

        @if(auth()->user()->isSalesEmployee())
        <div class="border-t border-slate-700/50 my-2 pt-2">
            <p class="px-4 text-xs font-semibold text-emerald-400/90 uppercase tracking-wider mb-1">المبيعات</p>
            @if(Route::has('employee.crm.home'))
            <a href="{{ route('employee.crm.home') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.crm.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-200 bg-slate-700/40 hover:bg-slate-700/70 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-table-cells-large text-base"></i>
                <span>CRM المبيعات</span>
            </a>
            <a href="{{ route('employee.crm.leads.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.crm.leads.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-user-plus text-base"></i>
                <span>العملاء المحتملون</span>
            </a>
            <a href="{{ route('employee.crm.opportunities.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.crm.opportunities.*') || request()->routeIs('employee.crm.deals.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-handshake text-base"></i>
                <span>الفرص</span>
            </a>
            <a href="{{ route('employee.crm.activities.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.crm.activities.*') || request()->routeIs('employee.crm.calls.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-phone text-base"></i>
                <span>الأنشطة</span>
            </a>
            <a href="{{ route('employee.crm.calendar.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.crm.calendar.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-calendar-week text-base"></i>
                <span>التقويم</span>
            </a>
            <a href="{{ route('employee.crm.reports.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.crm.reports.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-chart-bar text-base"></i>
                <span>التقارير</span>
            </a>
            @endif
            @if(Route::has('employee.marketing-today.index'))
            <a href="{{ route('employee.marketing-today.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.marketing-today.*') ? 'bg-emerald-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-bullhorn text-base"></i>
                <span>تسويق اليوم</span>
            </a>
            @endif
            @if(Route::has('employee.sales.course-board.index'))
            <a href="{{ route('employee.sales.course-board.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.sales.course-board.*') ? 'bg-emerald-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-book-open text-base"></i>
                <span>معلومات الكورسات</span>
            </a>
            @endif
            @if(Route::has('employee.sales.policy.index'))
            <a href="{{ route('employee.sales.policy.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.sales.policy.*') ? 'bg-emerald-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-scale-balanced text-base"></i>
                <span>قواعد المبيعات</span>
            </a>
            @endif
            <a href="{{ route('employee.sales.whatsapp.inbox.index') }}"
               title="محادثات الواتساب"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.sales.whatsapp.inbox.*') ? 'bg-emerald-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fab fa-whatsapp text-base"></i>
                <span>محادثات الواتساب</span>
            </a>
            <a href="{{ route('employee.sales.meta-social.inbox.index') }}"
               title="Messenger & Instagram"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.sales.meta-social.inbox.*') ? 'bg-emerald-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fab fa-facebook-messenger text-base"></i>
                <span>Messenger & Instagram</span>
            </a>
            <a href="{{ route('employee.sales.groups.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.sales.groups.*') ? 'bg-emerald-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-layer-group text-base"></i>
                <span>مجموعات العملاء</span>
            </a>
        </div>
        @endif

        @if($hasSalesManagerPortal)
        <div class="border-t border-slate-700/50 my-2 pt-2">
            <p class="px-4 text-xs font-semibold text-teal-400/90 uppercase tracking-wider mb-1">{{ $isBusinessDeveloper ? 'Business Developer — المبيعات' : 'مدير المبيعات' }}</p>
            @if(Route::has('employee.crm.home'))
            <a href="{{ route('employee.crm.home') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.crm.home') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-200 bg-slate-700/40 hover:bg-slate-700/70 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-table-cells-large text-base"></i>
                <span>CRM المبيعات</span>
            </a>
            <a href="{{ route('employee.crm.admin.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.crm.admin.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-users-cog text-base"></i>
                <span>إدارة الفريق</span>
            </a>
            <a href="{{ route('employee.crm.leads.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.crm.leads.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-user-plus text-base"></i>
                <span>العملاء المحتملون</span>
            </a>
            <a href="{{ route('employee.crm.opportunities.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.crm.opportunities.*') || request()->routeIs('employee.crm.deals.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-project-diagram text-base"></i>
                <span>الفرص / Pipeline</span>
            </a>
            <a href="{{ route('employee.crm.calendar.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.crm.calendar.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-calendar-alt text-base"></i>
                <span>التقويم</span>
            </a>
            <a href="{{ route('employee.crm.reports.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.crm.reports.*') ? 'bg-[#534D64] text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-chart-line text-base"></i>
                <span>التقارير و KPI</span>
            </a>
            @endif
            @if(Route::has('employee.sales.course-board.index'))
            <a href="{{ route('employee.sales.course-board.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.sales.course-board.*') ? 'bg-teal-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-book-open text-base"></i>
                <span>معلومات الكورسات</span>
            </a>
            @endif
            <a href="{{ route('employee.sales-manager.whatsapp.inbox.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.sales-manager.whatsapp.inbox.*') ? 'bg-teal-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fab fa-whatsapp text-base"></i>
                <span>محادثات الفريق</span>
            </a>
            <a href="{{ route('employee.sales-manager.attendance.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('employee.sales-manager.attendance.*') ? 'bg-teal-600 text-white shadow-lg' : 'text-slate-300 hover:bg-slate-700/50 hover:text-white' }}"
               @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                <i class="fas fa-clock text-base"></i>
                <span>حضور الفريق</span>
            </a>
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
