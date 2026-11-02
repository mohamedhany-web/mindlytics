{{-- CRM المبيعات الموحّد بأنماط SuiteCRM 8 (docs/SALES_CRM.md). نفس الـ layout للموظف والمدير والأدمن. --}}
@php
    $crmUser = auth()->user();
    $crmIsAdmin = $crm->isAdmin();
    try {
        $crmUnread = (int) $crmUser->notifications()->whereNull('read_at')->count();
    } catch (\Throwable $e) {
        $crmUnread = 0;
    }
    $crmNotifUrl = $crmIsAdmin
        ? (Route::has('admin.notifications.index') ? route('admin.notifications.index') : null)
        : (Route::has('employee.notifications') ? route('employee.notifications') : null);
    $crmProfileUrl = $crmIsAdmin
        ? (Route::has('admin.profile') ? route('admin.profile') : null)
        : (Route::has('employee.profile') ? route('employee.profile') : null);
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CRM المبيعات') - {{ config('app.name') }}</title>
    @include('components.favicon-meta')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {{-- نفس مكتبات بوابة الموظف: مكوّنات الحضور والشات تعتمد عليها --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root {
            --crm-nav: #534D64; --crm-nav-2: #6E6880; --crm-accent: #F08377; --crm-link: #B8492F; --crm-link-hover: #8F3622;
            --crm-slate: #627587; --crm-muted: #8A97A4; --crm-bg: #F5F5F5; --crm-ink: #212529; --crm-line: #E5E7EB;
            --crm-good: #0E6B44; --crm-bad: #B42318; --crm-warn: #8A5300;
        }
        body { margin: 0; background: var(--crm-bg); color: var(--crm-ink); font-family: 'Cairo', sans-serif; }
        [x-cloak] { display: none !important; }
        .crm-nav { background: var(--crm-nav); height: 56px; display: flex; align-items: stretch; gap: 2px; padding: 0 14px; position: relative; z-index: 40; }
        .crm-tab { display: flex; align-items: center; gap: 4px; padding: 0 12px; color: #ECEAF2; font-size: 15px; white-space: nowrap; border-top: 3px solid transparent; border-bottom: 3px solid transparent; text-decoration: none; height: 100%; background: transparent; cursor: pointer; }
        .crm-tab:hover { color: #fff; background: var(--crm-nav-2); }
        .crm-tab.is-active { color: #fff; font-weight: 700; border-top-color: var(--crm-accent); }
        .crm-menu { position: absolute; top: 56px; min-width: 230px; background: var(--crm-nav); box-shadow: 0 4px 12px rgba(0,0,0,.3); padding: 6px 0; z-index: 50; }
        .crm-menu a { display: block; padding: 8px 16px; color: #fff; font-size: 13px; text-decoration: none; }
        .crm-menu a:hover { background: var(--crm-nav-2); }
        .crm-icon-btn { width: 40px; display: flex; align-items: center; justify-content: center; color: #fff; background: transparent; border: 0; cursor: pointer; position: relative; }
        .crm-badge { position: absolute; top: 10px; left: 4px; min-width: 16px; height: 16px; border-radius: 99px; background: var(--crm-accent); color: #2b1a17; font-size: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center; padding: 0 3px; }
        .crm-search { display: flex; align-items: center; gap: 8px; margin: 10px 6px; padding: 0 10px; width: 240px; background: var(--crm-nav-2); border-radius: 3px; }
        .crm-search input { flex: 1; min-width: 0; border: 0; background: transparent; color: #fff; font: inherit; font-size: 13px; outline: none; }
        .crm-search input::placeholder { color: #D9D5E2; }
        .crm-page { padding: 16px 20px 40px; }
        .crm-title { margin: 0; font-size: 29px; font-weight: 300; color: #5F5F5F; letter-spacing: .03em; }
        .crm-btn { display: inline-flex; align-items: center; gap: 6px; height: 32px; padding: 0 14px; border: 0; border-radius: 3px; font: inherit; font-size: 13px; font-weight: 700; color: #fff; cursor: pointer; text-decoration: none; white-space: nowrap; }
        .crm-btn-primary { background: var(--crm-link); } .crm-btn-primary:hover { background: var(--crm-link-hover); color: #fff; }
        .crm-btn-dark { background: var(--crm-nav); } .crm-btn-slate { background: var(--crm-slate); } .crm-btn-muted { background: var(--crm-muted); }
        .crm-btn-outline { background: #fff; color: var(--crm-ink); border: 1px solid #C9CDD2; font-weight: 400; }
        .crm-btn-sm { height: 28px; padding: 0 10px; font-size: 12px; }
        .crm-card { background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,.25); }
        .crm-dashlet-head { display: flex; align-items: center; gap: 8px; padding: 9px 14px; background: var(--crm-nav); color: #fff; }
        .crm-dashlet-head h2 { margin: 0; font-size: 14px; font-weight: 700; flex: 1; }
        .crm-dashlet-foot { display: flex; justify-content: flex-end; align-items: center; gap: 8px; padding: 7px 12px; background: var(--crm-muted); color: #fff; font-size: 12px; }
        .crm-dashlet-foot a { color: #fff; font-weight: 700; text-decoration: underline; }
        .crm-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .crm-table thead tr { background: var(--crm-slate); color: #fff; }
        .crm-table th { text-align: start; padding: 8px 10px; font-weight: 700; font-size: 12.5px; white-space: nowrap; }
        .crm-table th a { color: #fff; text-decoration: none; }
        .crm-table td { padding: 8px 10px; border-bottom: 1px solid var(--crm-line); vertical-align: middle; }
        .crm-table tbody tr:hover { background: #FAFAFB; }
        .crm-table-plain thead tr { background: transparent; color: #374151; }
        .crm-link { color: var(--crm-link); font-weight: 700; text-decoration: none; } .crm-link:hover { color: var(--crm-link-hover); text-decoration: underline; }
        .crm-toolbar { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; padding: 7px 10px; background: var(--crm-slate); color: #fff; }
        .crm-pager a, .crm-pager span.crm-pg { display: inline-flex; align-items: center; justify-content: center; height: 24px; min-width: 24px; padding: 0 7px; border: 1px solid #fff; color: #fff; font-size: 11px; text-decoration: none; }
        .crm-pager span.crm-pg { opacity: .45; }
        .crm-chip { display: inline-flex; align-items: center; height: 28px; padding: 0 12px; border: 1px solid #C9CDD2; border-radius: 3px; background: #fff; color: var(--crm-ink); font-size: 12px; text-decoration: none; }
        .crm-chip.is-active { background: var(--crm-nav); border-color: var(--crm-nav); color: #fff; }
        .crm-chip .n { margin-inline-start: 5px; font-weight: 700; }
        .crm-input, .crm-select { height: 34px; border: 1px solid #C9CDD2; border-radius: 3px; padding: 0 8px; font: inherit; font-size: 13px; background: #fff; color: var(--crm-ink); }
        .crm-label { display: flex; flex-direction: column; gap: 4px; font-size: 12px; font-weight: 700; color: #374151; }
        .crm-field { display: flex; gap: 10px; padding: 9px 0; border-bottom: 1px solid var(--crm-line); font-size: 13px; align-items: flex-start; }
        .crm-field > .k { width: 140px; flex: none; font-size: 12px; font-weight: 700; color: #4B5563; }
        .crm-field > .v { flex: 1; min-width: 0; overflow-wrap: anywhere; }
        .crm-field > .e { color: var(--crm-muted); text-decoration: none; }
        .crm-rtabs button { height: 30px; padding: 0 14px; border: 0; background: #E4E7EB; color: #374151; font: inherit; font-size: 12px; cursor: pointer; }
        .crm-rtabs button.is-active { background: var(--crm-nav); color: #fff; font-weight: 700; }
        .crm-rel button { height: 30px; padding: 0 10px; border: 0; border-bottom: 2px solid #C9CDD2; background: transparent; font: inherit; font-size: 12px; cursor: pointer; }
        .crm-rel button.is-active { border-bottom-color: var(--crm-nav); font-weight: 700; }
        .crm-subpanel { margin: 12px; border: 1px solid #DADDE1; }
        .crm-subpanel-head { display: flex; align-items: center; gap: 8px; padding: 8px 10px; border-bottom: 3px solid var(--crm-nav); }
        .crm-subpanel-head h3 { margin: 0; font-size: 13px; font-weight: 700; flex: 1; }
        .crm-stat { padding: 14px 8px; display: flex; flex-direction: column; gap: 2px; text-align: center; }
        .crm-stat b { font-size: 22px; } .crm-stat span { font-size: 11px; font-weight: 700; color: #4B5563; }
        .crm-bar { display: block; height: 14px; background: #EEF0F2; } .crm-bar > span { display: block; height: 14px; }
        .crm-good { color: var(--crm-good); font-weight: 700; } .crm-bad { color: var(--crm-bad); font-weight: 700; } .crm-warn { color: var(--crm-warn); font-weight: 700; }
        .crm-alert { padding: 10px 14px; font-size: 13px; border: 1px solid; }
        .crm-alert-ok { background: #E8F4EE; border-color: #B9DCC9; color: #0E4F33; }
        .crm-alert-err { background: #FDECEA; border-color: #F2C9C4; color: #7A271A; }
        .crm-muted { color: #4B5563; }
        .crm-sr { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); }
        @media (max-width: 900px) { .crm-search { width: 140px; } .crm-hide-sm { display: none; } }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="crm-nav" aria-label="وحدات CRM" x-data="{ open: null, x: 0, place(el) { const nav = el.closest('nav').getBoundingClientRect(); this.x = Math.max(0, nav.right - el.getBoundingClientRect().right); } }" @keydown.escape.window="open = null" @click.outside="open = null">
        <a href="{{ route($crm->route('home')) }}" class="crm-tab" aria-label="الرئيسية" style="padding: 0 10px"><i class="fas fa-house"></i></a>
        <div style="display: flex; align-items: stretch; overflow-x: auto; overflow-y: visible; flex: 0 1 auto">
            @foreach($crmTabs as $tab)
                @if($tab['key'] === 'home')
                    @continue
                @endif
                <div style="position: relative; display: flex">
                    @if(count($tab['items']))
                        <button type="button" class="crm-tab {{ $crmModule === $tab['key'] ? 'is-active' : '' }}" @click="place($el); open = open === '{{ $tab['key'] }}' ? null : '{{ $tab['key'] }}'" :aria-expanded="open === '{{ $tab['key'] }}'">
                            {{ $tab['label'] }} <i class="fas fa-caret-down" style="font-size: 10px; opacity: .8"></i>
                        </button>
                    @else
                        <a href="{{ $tab['url'] }}" class="crm-tab {{ $crmModule === $tab['key'] ? 'is-active' : '' }}">{{ $tab['label'] }}</a>
                    @endif
                </div>
            @endforeach
        </div>
        @foreach($crmTabs as $tab)
            @if(count($tab['items']))
                <div class="crm-menu" x-cloak x-show="open === '{{ $tab['key'] }}'" role="menu" aria-label="{{ $tab['label'] }}" :style="{ right: x + 'px' }">
                    @foreach($tab['items'] as $item)
                        <a role="menuitem" href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                    @endforeach
                </div>
            @endif
        @endforeach
        <span style="flex: 1 1 auto"></span>
        @if(count($crmQuickCreate))
            <div style="position: relative; display: flex">
                <button type="button" class="crm-icon-btn" aria-label="إنشاء سريع" @click="open = open === 'qc' ? null : 'qc'"><i class="fas fa-plus"></i></button>
                <div class="crm-menu" x-cloak x-show="open === 'qc'" role="menu" style="left: 0">
                    @foreach($crmQuickCreate as $qc)
                        <a role="menuitem" href="{{ $qc['url'] }}">＋ {{ $qc['label'] }}</a>
                    @endforeach
                </div>
            </div>
        @endif
        <form method="get" action="{{ route($crm->route('leads.index')) }}" class="crm-search crm-hide-sm" role="search">
            <label class="crm-sr" for="crm-global-search">بحث شامل</label>
            <input id="crm-global-search" type="search" name="q" placeholder="ابحث باسم أو رقم أو رقم طلب…" value="{{ request('q') }}">
            <button type="submit" aria-label="بحث" style="border: 0; background: transparent; color: #E8E6EE"><i class="fas fa-magnifying-glass"></i></button>
        </form>
        @if($crmNotifUrl)
            <a href="{{ $crmNotifUrl }}" class="crm-icon-btn" aria-label="الإشعارات"><i class="fas fa-bell"></i>@if($crmUnread > 0)<span class="crm-badge">{{ $crmUnread > 99 ? '99+' : $crmUnread }}</span>@endif</a>
        @endif
        <div style="position: relative; display: flex">
            <button type="button" class="crm-icon-btn" style="width: 46px" aria-label="قائمة المستخدم" @click="open = open === 'user' ? null : 'user'">
                <span style="width: 30px; height: 30px; border-radius: 99px; border: 2px solid #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px">{{ mb_substr($crmUser->name ?? '؟', 0, 1) }}</span>
            </button>
            <div class="crm-menu" x-cloak x-show="open === 'user'" role="menu" style="left: 0">
                <span style="display: block; padding: 8px 16px 4px; color: #D9D5E2; font-size: 12px">{{ $crmUser->name }} · {{ $crm->isAdmin() ? 'أدمن' : ($crm->isManager() ? 'مدير مبيعات' : 'مبيعات') }}</span>
                @if($crmProfileUrl)<a role="menuitem" href="{{ $crmProfileUrl }}">الملف الشخصي</a>@endif
                @if($crmIsAdmin && Route::has('admin.dashboard'))<a role="menuitem" href="{{ route('admin.dashboard') }}">لوحة الإدارة</a>@endif
                @if(! $crmIsAdmin && Route::has('employee.dashboard'))<a role="menuitem" href="{{ route('employee.dashboard') }}">لوحة الموظف</a>@endif
                @if(Route::has('logout'))
                    <form method="post" action="{{ route('logout') }}">@csrf<button type="submit" style="display: block; width: 100%; text-align: start; padding: 8px 16px; color: #fff; font-size: 13px; background: transparent; border: 0; cursor: pointer">تسجيل الخروج</button></form>
                @endif
            </div>
        </div>
    </nav>

    @unless($crmIsAdmin)
        <div style="display: flex; justify-content: flex-end; padding: 8px 20px 0">@include('components.employee-attendance-bar')</div>
    @endunless

    <main class="crm-page">
        @if(session('success'))
            <div class="crm-alert crm-alert-ok" role="status" style="margin-bottom: 12px"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="crm-alert crm-alert-err" role="alert" style="margin-bottom: 12px"><i class="fas fa-circle-exclamation"></i> {{ session('error') }}</div>
        @endif
        @if(session('warning'))
            <div class="crm-alert" role="status" style="margin-bottom: 12px; background: #FFF6E5; border-color: #F1D49A; color: #6B4100"><i class="fas fa-triangle-exclamation"></i> {{ session('warning') }}</div>
        @endif
        @if($errors->any())
            <div class="crm-alert crm-alert-err" role="alert" style="margin-bottom: 12px">
                @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
            </div>
        @endif
        @yield('crm_page_head')
        <div class="crm-content">@yield('content')</div>
    </main>

    @stack('scripts')
    @unless($crmIsAdmin)
        @include('components.employee-presence-heartbeat')
        @include('components.sales-team-chat-widget')
    @endunless
</body>
</html>
