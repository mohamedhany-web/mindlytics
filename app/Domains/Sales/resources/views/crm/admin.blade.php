{{--
    إطار CRM لصفحات إدارة المبيعات (admin/sales/*): نفس الشريط العلوي والألوان والخطوط بتاعة شاشات CRM.
    الصفحات بتعمل @extends('sales::crm.admin') وبتحتفظ بـ @section('title' / 'header' / 'content') زي ما هي،
    والستايل تحت بيوحّد الكروت والجداول والخانات والأزرار المكتوبة بـ Tailwind على تصميم CRM.
--}}
@extends('sales::crm.layout')

@use('App\Domains\Sales\Support\CrmNavigation')
@use('App\Domains\Sales\Support\CrmScope')
@php
    $crm ??= CrmScope::for(auth()->user(), true);
    $crmModule ??= CrmNavigation::moduleForAdminRoute(request()->route()?->getName());
    $crmTabs ??= CrmNavigation::tabs($crm);
    $crmQuickCreate ??= CrmNavigation::quickCreate($crm);
@endphp

@section('crm_page_head')
    <nav aria-label="مسار الصفحة" style="display: flex; flex-wrap: wrap; align-items: center; gap: 6px; margin-bottom: 12px; font-size: 12px; color: #5F6B76">
        @if(Route::has('admin.crm.settings.index'))
            <a href="{{ route('admin.crm.settings.index', ['tab' => 'setup']) }}" class="crm-link" style="font-weight: 600">إدارة المبيعات</a>
        @else
            <span>إدارة المبيعات</span>
        @endif
        <i class="fas fa-angle-left" style="font-size: 10px; opacity: .6"></i>
        <span style="font-weight: 700; color: var(--crm-ink)">@yield('header', 'المبيعات')</span>
    </nav>
@endsection

@push('styles')
<style>
    /* ── الكروت: زوايا وظلال CRM ── */
    .crm-page .rounded-md, .crm-page .rounded-lg, .crm-page .rounded-xl, .crm-page .rounded-2xl, .crm-page .rounded-3xl { border-radius: 3px; }
    .crm-page .shadow, .crm-page .shadow-lg, .crm-page .shadow-xl, .crm-page .shadow-2xl { box-shadow: 0 2px 4px rgba(0, 0, 0, .25); }
    .crm-page .shadow-sm, .crm-page .shadow-md { box-shadow: none; }

    /* التدرجات: الغامقة (نص أبيض) بلون الشريط، والفاتحة خلفية محايدة */
    .crm-page [class*="bg-gradient-to-"] { background-image: none; background-color: #F7F7F9; }
    .crm-page [class*="bg-gradient-to-"].text-white { background-color: var(--crm-nav); }

    /* ── الجداول: رأس رمادي غامق زي قوائم CRM ── */
    .crm-page table thead, .crm-page table thead tr { background: var(--crm-slate); }
    .crm-page table thead th { color: #fff; font-weight: 700; font-size: 12.5px; border-color: transparent; }
    .crm-page table thead th * { color: inherit; }
    .crm-page table thead th[class*="bg-"] { background: var(--crm-slate); }
    .crm-page table tbody td { border-color: var(--crm-line); }
    .crm-page table tbody tr:hover { background-color: #FAFAFB; }

    /* ── الخانات ── */
    .crm-page input:where(:not([type="checkbox"], [type="radio"], [type="file"], [type="range"], [type="color"], [type="hidden"], [type="submit"], [type="button"])),
    .crm-page select,
    .crm-page textarea {
        border: 1px solid #C9CDD2; border-radius: 3px; background-color: #fff; color: var(--crm-ink); font-size: 13px; line-height: 1.5; padding-inline: 10px;
    }
    .crm-page input:where(:not([type="checkbox"], [type="radio"], [type="file"], [type="range"], [type="color"], [type="hidden"], [type="submit"], [type="button"])),
    .crm-page select:not([multiple]) { height: 38px; padding-top: 0; padding-bottom: 0; }
    /* الخانات الصغيرة جوه الجداول والكروت (text-xs) */
    .crm-page :is(input, select:not([multiple])):is(.text-xs, .text-\[11px\], .text-\[10px\]) { height: 30px; font-size: 12px; }
    .crm-page input:focus, .crm-page select:focus, .crm-page textarea:focus {
        outline: none; border-color: var(--crm-nav); box-shadow: 0 0 0 2px rgba(83, 77, 100, .18); --tw-ring-shadow: 0 0 #0000;
    }
    .crm-page input[type="checkbox"], .crm-page input[type="radio"] { accent-color: var(--crm-link); }
    .crm-page input[type="file"] { font-size: 13px; }
    .crm-page label { color: #374151; }
    .crm-page .crm-label :is(input, select, textarea) { font-weight: 400; }

    /* ── الأزرار: الأساسي بلون CRM، الغامق بلون الشريط، الأحمر يفضل للحذف والرفض ── */
    .crm-page :is(button, a, input[type="submit"]):is(.bg-emerald-500, .bg-emerald-600, .bg-emerald-700, .bg-sky-600, .bg-sky-700, .bg-violet-600, .bg-violet-700, .bg-indigo-600, .bg-indigo-700, .bg-blue-600, .bg-blue-700, .bg-teal-600, .bg-amber-600, .bg-orange-600) {
        background-color: var(--crm-link); background-image: none; color: #fff; border-radius: 3px;
    }
    .crm-page :is(button, a, input[type="submit"]):is(.bg-emerald-500, .bg-emerald-600, .bg-emerald-700, .bg-sky-600, .bg-sky-700, .bg-violet-600, .bg-violet-700, .bg-indigo-600, .bg-indigo-700, .bg-blue-600, .bg-blue-700, .bg-teal-600, .bg-amber-600, .bg-orange-600):hover { background-color: var(--crm-link-hover); }
    .crm-page :is(button, a, input[type="submit"]):is(.bg-slate-700, .bg-slate-800, .bg-slate-900, .bg-gray-800, .bg-gray-900) { background-color: var(--crm-nav); color: #fff; }
    .crm-page :is(button, a, input[type="submit"]):is(.bg-slate-700, .bg-slate-800, .bg-slate-900, .bg-gray-800, .bg-gray-900):hover { background-color: var(--crm-nav-2); }
    .crm-page :is(button, a, input[type="submit"]):is(.bg-rose-600, .bg-red-600) { background-color: var(--crm-bad); color: #fff; }

    /* ── الروابط ── */
    .crm-page :is(a, button:not([class*="bg-"])):is(.text-sky-600, .text-sky-700, .text-violet-600, .text-violet-700, .text-indigo-600, .text-indigo-700, .text-blue-600, .text-blue-700), .crm-page a:is(.text-emerald-600, .text-emerald-700, .text-teal-700) { color: var(--crm-link); }
    .crm-page :is(a, button:not([class*="bg-"])):is(.text-sky-600, .text-sky-700, .text-violet-600, .text-violet-700, .text-indigo-600, .text-indigo-700, .text-blue-600, .text-blue-700):hover, .crm-page a:is(.text-emerald-600, .text-emerald-700, .text-teal-700):hover { color: var(--crm-link-hover); }

    /* ── بنمط SuiteCRM 8 ── */
    .crm-content { font-size: 13px; }

    /* رأس الصفحة (أول كارت): من غير كارت، والعنوان كبير وخفيف زي عناوين الوحدات في SuiteCRM */
    .crm-content > * > .overflow-hidden:first-child,
    .crm-content > * > .overflow-hidden:first-child > .border-b:first-child { background: transparent; border-color: transparent; box-shadow: none; }
    .crm-content > * > .overflow-hidden:first-child > .border-b:first-child { padding-inline: 0; padding-top: 0; }
    .crm-content > * > .overflow-hidden:first-child > .border-b:first-child :is(h1, h2):first-of-type { font-size: 29px; font-weight: 300; color: #5F5F5F; letter-spacing: .02em; line-height: 1.35; margin-bottom: 2px; }
    .crm-content > * > .overflow-hidden:first-child > .border-b:first-child > div > :is(.w-9, .w-10, .w-11, .w-12):first-child,
    .crm-content > * > .overflow-hidden:first-child > .border-b:first-child > div > div > :is(.w-9, .w-10, .w-11, .w-12):first-child { display: none; }
    .crm-content > * > .overflow-hidden:first-child > :not(.border-b:first-child) { padding-inline: 0; }

    /* رؤوس باقي الكروت = شريط Dashlet بنفسجي */
    .crm-content .overflow-hidden:not(.crm-content > * > .overflow-hidden:first-child) > .border-b:first-child {
        background: var(--crm-nav); border-color: var(--crm-nav); color: #fff; padding-top: 9px; padding-bottom: 9px;
    }
    .crm-content .overflow-hidden:not(.crm-content > * > .overflow-hidden:first-child) > .border-b:first-child :is(h2, h3, p, span, i, div, strong, b):not(a *, button *, .rounded-full, .rounded-full *, .border-b .border, .border-b .border *, .border-b [class*="bg-white"], .border-b [class*="bg-white"] *) {
        color: #fff;
    }
    .crm-content .overflow-hidden:not(.crm-content > * > .overflow-hidden:first-child) > .border-b:first-child :is(h2, h3) { font-size: 14px; font-weight: 700; }
    .crm-content .overflow-hidden:not(.crm-content > * > .overflow-hidden:first-child) > .border-b:first-child p { opacity: .85; }

    /* بلاطات الإحصائيات: من غير ألوان الأيقونات، زي Dashlet الأرقام */
    .crm-content :is(.w-9, .w-10, .w-11, .w-12):is(.h-9, .h-10, .h-11, .h-12):is([class*="bg-"]):not([class*="bg-gradient"]) { background: #EEF0F2; color: var(--crm-nav); }
    .crm-content :is(.w-9, .w-10, .w-11, .w-12):is(.h-9, .h-10, .h-11, .h-12):is([class*="bg-"]):not([class*="bg-gradient"]) i { color: var(--crm-nav); }

    /* الأزرار الثانوية بنمط crm-btn-outline */
    .crm-content a:is(.border-slate-200, .border-slate-300):not([class*="bg-"]):is(.inline-flex) { background: #fff; color: var(--crm-ink); border-color: #C9CDD2; border-radius: 3px; }
    .crm-content :is(a, button).inline-flex:is([class*="py-2"], [class*="py-2.5"]) { min-height: 34px; font-size: 13px; }


    .crm-content .overflow-hidden:not(.crm-content > * > .overflow-hidden:first-child) > .border-b:first-child a:not([class*="bg-"]):not([class*="border"]) { color: #fff; text-decoration: underline; }
    .crm-content .overflow-hidden:not(.crm-content > * > .overflow-hidden:first-child) > .border-b:first-child label { color: #fff; }
</style>
@endpush
