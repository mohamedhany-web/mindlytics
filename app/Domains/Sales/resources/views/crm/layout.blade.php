{{-- SuiteCRM-style Sales CRM shell — الواجهة الوحيدة للمبيعات --}}
@php
    if (! isset($crm)) {
        $crm = \App\Domains\Sales\Support\CrmScope::for(auth()->user(), request()->is('admin/*') || str_starts_with((string) request()->route()?->getName(), 'admin.'));
        $crmTabs = \App\Domains\Sales\Support\CrmNavigation::tabs($crm);
        $crmQuickCreate = \App\Domains\Sales\Support\CrmNavigation::quickCreate($crm);
        $crmModule = $crmModule ?? 'home';
    }
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
    @php
        $crmCssPath = public_path('css/sales-crm.css');
        $crmCssVer = is_file($crmCssPath) ? (string) filemtime($crmCssPath) : '1';
    @endphp
    {{-- Critical shell styles: لو ملف الـ CSS اتأخر أو الكاش قديم، الواجهة متتكسرش --}}
    <style id="crm-critical">
      :root{--crm-nav:#534D64;--crm-nav-2:#6E6880;--crm-accent:#F08377;--crm-link:#B8492F;--crm-slate:#627587;--crm-muted:#8A97A4;--crm-bg:#F5F5F5;--crm-ink:#212529;--crm-line:#E5E7EB;--crm-panel:#fff;--crm-good:#0E6B44;--crm-bad:#B42318;--crm-warn:#8A5300}
      *,*::before,*::after{box-sizing:border-box}
      body.crm-body{margin:0;min-height:100vh;background:var(--crm-bg)!important;color:var(--crm-ink);font-family:"Cairo","Segoe UI",Tahoma,sans-serif;line-height:1.5}
      [x-cloak]{display:none!important}
      .crm-nav{background:var(--crm-nav)!important;height:56px;display:flex;align-items:stretch;gap:2px;padding:0 14px;position:relative;z-index:40;color:#fff;box-shadow:0 1px 0 rgba(0,0,0,.18)}
      .crm-brand{display:flex;align-items:center;gap:8px;padding:0 12px;color:#fff;font-weight:700;font-size:14px;text-decoration:none;white-space:nowrap}
      .crm-tab{display:flex;align-items:center;gap:4px;padding:0 12px;color:#ECEAF2;font-size:14px;white-space:nowrap;border-top:3px solid transparent;border-bottom:3px solid transparent;text-decoration:none;height:100%;background:transparent;cursor:pointer;font-family:inherit}
      .crm-tab:hover{color:#fff;background:var(--crm-nav-2)}
      .crm-tab.is-active{color:#fff;font-weight:700;border-top-color:var(--crm-accent)}
      .crm-menu{position:absolute;top:56px;min-width:240px;background:var(--crm-nav);box-shadow:0 4px 12px rgba(0,0,0,.3);padding:6px 0;z-index:50}
      .crm-menu a,.crm-menu button{display:block;width:100%;text-align:start;padding:8px 16px;color:#fff;font-size:13px;text-decoration:none;background:transparent;border:0;cursor:pointer}
      .crm-menu a:hover,.crm-menu button:hover{background:var(--crm-nav-2)}
      .crm-icon-btn{display:flex;align-items:center;justify-content:center;width:40px;height:100%;color:#fff;background:transparent;border:0;cursor:pointer;position:relative;text-decoration:none}
      .crm-icon-btn:hover{background:var(--crm-nav-2)}
      .crm-search{display:flex;align-items:center;gap:6px;margin:10px 8px;padding:0 10px;height:32px;border-radius:3px;background:var(--crm-nav-2);width:200px}
      .crm-search input{flex:1;min-width:0;border:0;background:transparent;color:#fff;font:inherit;font-size:13px;outline:none}
      .crm-page{padding:18px 20px 48px;max-width:1400px;margin:0 auto;width:100%}
      .crm-title{margin:0;font-size:28px;font-weight:300;color:#5F5F5F}
      .crm-card{background:var(--crm-panel)!important;border:1px solid #E2E5EA;border-radius:6px;box-shadow:0 2px 8px rgba(32,41,54,.08);overflow:hidden}
      .crm-dashlet-head{display:flex;align-items:center;gap:8px;padding:10px 14px;background:var(--crm-nav)!important;color:#fff!important}
      .crm-dashlet-head h2{margin:0;font-size:14px;font-weight:700;flex:1;color:inherit}
      .crm-table{width:100%;border-collapse:collapse;font-size:13px;background:#fff}
      .crm-table thead tr{background:var(--crm-slate)!important;color:#fff!important}
      .crm-table th{text-align:start;padding:9px 10px;font-weight:700;font-size:12.5px;white-space:nowrap;color:#fff!important;border:0}
      .crm-table td{padding:9px 10px;border-bottom:1px solid var(--crm-line);vertical-align:middle;background:#fff}
      .crm-link{color:var(--crm-link);font-weight:700;text-decoration:none}
      .crm-stat{padding:16px 12px;display:flex;flex-direction:column;gap:4px;text-align:center;min-height:88px;justify-content:center}
      .crm-stat b{font-size:24px;font-weight:700;line-height:1.15}
      .crm-stat span{font-size:11px;font-weight:700;color:#4B5563}
      .crm-good{color:var(--crm-good);font-weight:700}.crm-bad{color:var(--crm-bad);font-weight:700}.crm-warn{color:var(--crm-warn);font-weight:700}.crm-muted{color:#4B5563}
      .crm-btn{display:inline-flex;align-items:center;gap:6px;height:32px;padding:0 14px;border:0;border-radius:3px;font:inherit;font-size:13px;font-weight:700;color:#fff;cursor:pointer;text-decoration:none;white-space:nowrap}
      .crm-btn-primary{background:var(--crm-link)}.crm-btn-dark{background:var(--crm-nav)}.crm-btn-slate{background:var(--crm-slate)}
    </style>
    <link rel="stylesheet" href="{{ asset('css/sales-crm.css') }}?v={{ $crmCssVer }}">
    {{-- Preflight بتاع Tailwind بيمسح خلفيات الجداول/الكروت ويخلي الواجهة باينة «متهالكة» --}}
    <script>
      window.tailwind = { config: { corePlugins: { preflight: false } } };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
</head>
<body class="crm-body">
    <nav class="crm-nav" aria-label="وحدات CRM" x-data="{ open: null, x: 0, place(el) { const nav = el.closest('nav').getBoundingClientRect(); this.x = Math.max(0, nav.right - el.getBoundingClientRect().right); } }" @keydown.escape.window="open = null" @click.outside="open = null">
        <a href="{{ route($crm->route('home')) }}" class="crm-brand" aria-label="الرئيسية">
            <i class="fas fa-house"></i>
            <span class="crm-hide-sm">Mindlytics CRM</span>
        </a>
        <div style="display: flex; align-items: stretch; overflow-x: auto; overflow-y: visible; flex: 0 1 auto">
            @foreach($crmTabs as $tab)
                @if($tab['key'] === 'home')
                    @continue
                @endif
                <div style="position: relative; display: flex">
                    @if(count($tab['items']))
                        <button type="button" class="crm-tab {{ ($crmModule ?? '') === $tab['key'] ? 'is-active' : '' }}" @click="place($el); open = open === '{{ $tab['key'] }}' ? null : '{{ $tab['key'] }}'" :aria-expanded="open === '{{ $tab['key'] }}'">
                            {{ $tab['label'] }} <i class="fas fa-caret-down" style="font-size: 10px; opacity: .8"></i>
                        </button>
                    @else
                        <a href="{{ $tab['url'] }}" class="crm-tab {{ ($crmModule ?? '') === $tab['key'] ? 'is-active' : '' }}">{{ $tab['label'] }}</a>
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
            <input id="crm-global-search" type="search" name="q" placeholder="ابحث باسم أو رقم…" value="{{ request('q') }}">
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
                @if(! $crmIsAdmin && Route::has('employee.crm.home'))<a role="menuitem" href="{{ route('employee.crm.home') }}">الرئيسية</a>@endif
                @if(! $crmIsAdmin && Route::has('employee.leaves.index'))<a role="menuitem" href="{{ route('employee.leaves.index') }}">الإجازات</a>@endif
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
        @if(isset($errors) && $errors->any())
            <div class="crm-alert crm-alert-err" role="alert" style="margin-bottom: 12px">
                @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
            </div>
        @endif
        @yield('content')
    </main>

    @stack('scripts')
    @unless($crmIsAdmin)
        @include('components.employee-presence-heartbeat')
        @include('components.sales-team-chat-widget')
    @endunless
</body>
</html>
