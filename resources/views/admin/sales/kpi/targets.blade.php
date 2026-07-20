@extends('layouts.admin')

@section('title', 'أهداف KPIs المبيعات')
@section('header', 'أهداف KPIs المبيعات')

@php
    $labels = [
        'leads_daily' => 'Leads جديدة / يوم',
        'leads_weekly' => 'Leads جديدة / أسبوع',
        'deals_weekly' => 'صفقات مغلقة / أسبوع',
        'revenue_monthly' => 'إيراد شهري (ج.م) — قيمة متوقعة مكتملة',
        'calls_daily' => 'مكالمات / يوم',
        'meetings_daily' => 'اجتماعات أو ديمو / يوم',
        'followups_daily' => 'متابعات مسجّلة / يوم',
        'response_minutes_max' => 'أقصى متوسط أول رد (دقيقة)',
        'closing_ratio_pct_min' => 'أدنى نسبة إغلاق won/(won+lost) %',
        'csat_min' => 'أدنى متوسط CSAT (1–5)',
        'loss_ratio_max_pct' => 'أقصى نسبة خسارة مقبولة %',
        'open_opportunities_min' => 'أدنى فرص مفتوحة في الأنبوب',
        'sales_cycle_max_days' => 'أقصى متوسط دورة بيع (يوم)',
        'crm_activities_daily_min' => 'أدنى أنشطة CRM / يوم',
        'data_fresh_open_pct_min' => 'أدنى % فرص محدّثة خلال 7 أيام',
        'engagement_days_pct_min' => 'أدنى % أيام بتفاعل مسجّل',
        'conversion_pct_target' => 'هدف نسبة تحويل % (شهري)',
    ];
    $groups = [
        'النشاط اليومي' => ['icon' => 'fas fa-bolt text-amber-600', 'keys' => ['leads_daily', 'leads_weekly', 'calls_daily', 'meetings_daily', 'followups_daily', 'crm_activities_daily_min']],
        'النتائج والإيراد' => ['icon' => 'fas fa-coins text-emerald-600', 'keys' => ['deals_weekly', 'revenue_monthly', 'closing_ratio_pct_min', 'conversion_pct_target']],
        'الجودة والالتزام' => ['icon' => 'fas fa-star text-sky-600', 'keys' => ['response_minutes_max', 'csat_min', 'loss_ratio_max_pct', 'sales_cycle_max_days', 'engagement_days_pct_min']],
        'الأنبوب والبيانات' => ['icon' => 'fas fa-filter text-violet-600', 'keys' => ['open_opportunities_min', 'data_fresh_open_pct_min']],
    ];
    $filledCount = collect($labels)->filter(fn ($_, $key) => ($targets[$key] ?? '') !== '' && ($targets[$key] ?? null) !== null)->count();
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500';
    $statCards = [
        ['label' => 'موظفو مبيعات', 'value' => number_format($salesReps->count()), 'icon' => 'fas fa-users', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => 'نشطون'],
        ['label' => 'حقول الأهداف', 'value' => number_format(count($labels)), 'icon' => 'fas fa-sliders-h', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'description' => 'مؤشر KPI'],
        ['label' => 'أهداف مُعبّأة', 'value' => number_format($filledCount), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'description' => 'للموظف والشهر'],
        ['label' => 'الشهر', 'value' => $yearMonth, 'icon' => 'fas fa-calendar-alt', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'description' => $rep?->name ?? '—'],
    ];
@endphp

@section('content')
<div class="space-y-6">
    {{-- الهيدر --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-sliders-h"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">أهداف KPIs المبيعات</h2>
                    <p class="text-xs text-slate-600">ضبط الأهداف الشهرية وإعدادات الكوميشن لكل موظف.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.sales.kpi.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-chart-line text-emerald-600"></i>
                    لوحة المراقبة
                </a>
                <a href="{{ route('admin.sales.leads.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-user-tag text-emerald-600"></i>
                    العملاء المحتملون
                </a>
            </div>
        </div>
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 p-4">
            @foreach($statCards as $card)
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-600 truncate">{{ $card['label'] }}</p>
                            <p class="text-xl font-black text-slate-900 truncate tabular-nums">{{ $card['value'] }}</p>
                        </div>
                        <div class="w-9 h-9 rounded-lg {{ $card['bg'] }} flex items-center justify-center {{ $card['text'] }} flex-shrink-0">
                            <i class="{{ $card['icon'] }} text-sm"></i>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1 truncate">{{ $card['description'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900 text-sm font-semibold flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-600"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-900 text-sm">
            <p class="font-semibold mb-1 flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> تحقق من البيانات</p>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($salesReps->isEmpty())
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-12 text-center">
                <div class="w-14 h-14 mx-auto mb-3 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <p class="text-sm font-semibold text-slate-900">لا موظفو مبيعات</p>
                <p class="text-xs text-slate-500 mt-1">لا يوجد موظفو مبيعات نشطون لضبط الأهداف.</p>
            </div>
        </section>
    @else
        {{-- اختيار الموظف والشهر --}}
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-filter text-sky-600"></i>
                    اختيار الموظف والشهر
                </h3>
            </div>
            <div class="p-4">
                <form method="get" action="{{ route('admin.sales.kpi.targets') }}" class="flex flex-col gap-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">الموظف</label>
                            <select name="user_id" class="{{ $inputClass }}">
                                @foreach($salesReps as $sr)
                                    <option value="{{ $sr->id }}" @selected((int) $userId === (int) $sr->id)>{{ $sr->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">الشهر</label>
                            <input type="month" name="year_month" value="{{ $yearMonth }}" class="{{ $inputClass }}">
                        </div>
                        <div>
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-700 px-4 py-2 text-sm font-semibold text-white">
                                <i class="fas fa-search"></i>
                                عرض
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        <form method="post" action="{{ route('admin.sales.kpi.targets.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="user_id" value="{{ $userId }}">
            <input type="hidden" name="year_month" value="{{ $yearMonth }}">

            {{-- الكوميشن --}}
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                            <i class="fas fa-coins text-emerald-600"></i>
                            إعدادات الكوميشن
                        </h3>
                        <p class="text-xs text-slate-600">لكل موظف — تُستخدم عند اعتماد wins من الإدارة.</p>
                    </div>
                    <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">{{ $rep?->name ?? '—' }}</span>
                </div>
                <div class="p-4 space-y-4">
                    @php
                        $commMode = old('sales_commission_mode', $rep?->sales_commission_mode ?? 'none');
                        $tierRows = old('tier_min')
                            ? collect(old('tier_min', []))->keys()->map(fn ($i) => [
                                'min' => old('tier_min.'.$i),
                                'max' => old('tier_max.'.$i),
                                'rate' => old('tier_rate.'.$i),
                                'bonus' => old('tier_bonus.'.$i),
                                'bonus_at' => old('tier_bonus_at.'.$i),
                            ])->all()
                            : \App\Services\SalesCommissionTierService::normalizeTiers($rep?->sales_commission_tiers);
                        $tierPeriod = old('sales_commission_tier_period', $rep?->sales_commission_tier_period ?? 'month');
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">النظام</label>
                            <select name="sales_commission_mode" id="sales_commission_mode" class="{{ $inputClass }}">
                                <option value="none" @selected($commMode === 'none')>بدون</option>
                                <option value="percent" @selected($commMode === 'percent')>نسبة % من expected value</option>
                                <option value="fixed" @selected($commMode === 'fixed')>مبلغ ثابت لكل win</option>
                                <option value="tier" @selected($commMode === 'tier')>Tier System (شرائح + بونص)</option>
                            </select>
                        </div>
                        <div id="commission_value_wrap">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">القيمة</label>
                            <input type="number" step="0.01" min="0" name="sales_commission_value"
                                   value="{{ old('sales_commission_value', $rep?->sales_commission_value) }}"
                                   class="{{ $inputClass }}">
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <p class="text-xs font-semibold text-slate-600">الإعداد الحالي</p>
                            <p class="text-sm font-black text-emerald-700 mt-0.5">{{ $rep?->salesCommissionLabel() ?? '—' }}</p>
                        </div>
                    </div>

                    <div id="tier_system_wrap" class="space-y-3 {{ $commMode === 'tier' ? '' : 'hidden' }}">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-end">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">فترة عدّ الـ Wins</label>
                                <select name="sales_commission_tier_period" class="{{ $inputClass }}">
                                    <option value="month" @selected($tierPeriod === 'month')>شهري (يُعاد من أول كل شهر)</option>
                                    <option value="all" @selected($tierPeriod === 'all')>تراكمي (كل الوقت)</option>
                                </select>
                            </div>
                            <p class="text-xs text-slate-600">
                                البيع رقم N يأخذ عمولة شريحته، والبونص يُصرف مرة عند الوصول لـ 10 / 20 / 30 / 40.
                            </p>
                        </div>
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-700 border-b border-slate-200">
                                        <th class="px-3 py-2 text-right font-semibold">من (بيع #)</th>
                                        <th class="px-3 py-2 text-right font-semibold">إلى</th>
                                        <th class="px-3 py-2 text-right font-semibold">عمولة / كورس</th>
                                        <th class="px-3 py-2 text-right font-semibold">بونص عند الوصول</th>
                                        <th class="px-3 py-2 text-right font-semibold">عند بيع #</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($tierRows as $i => $tier)
                                        <tr>
                                            <td class="px-3 py-2">
                                                <input type="number" min="1" name="tier_min[]" value="{{ $tier['min'] ?? '' }}" class="{{ $inputClass }}">
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="number" min="1" name="tier_max[]" value="{{ $tier['max'] ?? '' }}" placeholder="∞" class="{{ $inputClass }}">
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="number" step="0.01" min="0" name="tier_rate[]" value="{{ $tier['rate'] ?? '' }}" class="{{ $inputClass }}">
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="number" step="0.01" min="0" name="tier_bonus[]" value="{{ $tier['bonus'] ?? 0 }}" class="{{ $inputClass }}">
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="number" min="1" name="tier_bonus_at[]" value="{{ $tier['bonus_at'] ?? '' }}" placeholder="—" class="{{ $inputClass }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            {{-- الأهداف --}}
            @foreach($groups as $groupTitle => $group)
                <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                        <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                            <i class="{{ $group['icon'] }}"></i>
                            {{ $groupTitle }}
                        </h3>
                    </div>
                    <div class="p-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach($group['keys'] as $key)
                            <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                                <label class="block text-sm font-semibold text-slate-700 mb-2" for="t_{{ $key }}">{{ $labels[$key] ?? $key }}</label>
                                <input type="number" step="any" name="{{ $key }}" id="t_{{ $key }}"
                                       value="{{ old($key, $targets[$key] ?? '') }}"
                                       class="{{ $inputClass }}">
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach

            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-4 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <p class="text-xs text-slate-600">
                        <i class="fas fa-info-circle text-sky-600 ml-1"></i>
                        الحفظ يطبّق على <strong>{{ $rep?->name }}</strong> — {{ $yearMonth }}
                    </p>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold">
                        <i class="fas fa-save"></i>
                        حفظ الأهداف
                    </button>
                </div>
            </section>
        </form>

            {{-- اتفاقيات كوميشن حسب الكورس --}}
            @php
                $agreements = $agreements ?? collect();
                $defaultTiers = $defaultTiers ?? \App\Services\SalesCommissionTierService::defaultTiers();
            @endphp
            <section class="rounded-2xl bg-white border border-violet-200 shadow-lg overflow-hidden">
                <div class="px-4 py-3 border-b border-violet-200 bg-violet-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h3 class="text-base font-black text-violet-950 flex items-center gap-2">
                            <i class="fas fa-file-contract text-violet-600"></i>
                            اتفاقيات كوميشن حسب الكورس
                        </h3>
                        <p class="text-xs text-violet-800">لكل موظف × كورس: اختر وضع الحساب (Tier كورس / ثابت / نسبة / Tier عام…).</p>
                    </div>
                    <span class="text-xs font-semibold text-violet-800 bg-white px-2.5 py-1 rounded-lg border border-violet-200">{{ $agreements->count() }} اتفاقية</span>
                </div>
                <div class="p-4 space-y-4">
                    @if($agreements->isNotEmpty())
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-700 border-b border-slate-200">
                                        <th class="px-3 py-2 text-right">النوع</th>
                                        <th class="px-3 py-2 text-right">الكورس</th>
                                        <th class="px-3 py-2 text-center">السعر</th>
                                        <th class="px-3 py-2 text-right">وضع الحساب</th>
                                        <th class="px-3 py-2 text-center">حالة</th>
                                        <th class="px-3 py-2 text-center">حذف</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($agreements as $agr)
                                        <tr>
                                            <td class="px-3 py-2">{{ $agr->courseTypeLabel() }}</td>
                                            <td class="px-3 py-2 font-semibold">{{ $agr->courseTitle() }}</td>
                                            <td class="px-3 py-2 text-center tabular-nums">{{ $agr->coursePrice() !== null ? number_format($agr->coursePrice(), 2) : '—' }}</td>
                                            <td class="px-3 py-2 text-xs">{{ $agr->calcModeLabel() }}</td>
                                            <td class="px-3 py-2 text-center">
                                                <span class="text-[11px] font-bold {{ $agr->is_active ? 'text-emerald-700' : 'text-slate-500' }}">
                                                    {{ $agr->is_active ? 'نشطة' : 'موقوفة' }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                <form method="post" action="{{ route('admin.sales.course-commission-agreements.destroy', $agr) }}" onsubmit="return confirm('حذف الاتفاقية؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="year_month" value="{{ $yearMonth }}">
                                                    <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-semibold">حذف</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-slate-600">لا اتفاقيات بعد — أضف كورساً أدناه.</p>
                    @endif

                    <form method="post" action="{{ route('admin.sales.course-commission-agreements.store') }}" class="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-3" id="agr_create_form">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $userId }}">
                        <input type="hidden" name="year_month" value="{{ $yearMonth }}">
                        <p class="text-sm font-black text-slate-900">إضافة اتفاقية جديدة</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">نوع الكورس</label>
                                <select name="course_type" id="agr_course_type" class="{{ $inputClass }}" required>
                                    @foreach(\App\Models\SalesCourseCommissionAgreement::COURSE_TYPES as $k => $label)
                                        <option value="{{ $k }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="xl:col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">الكورس</label>
                                <select name="course_ref_id" id="agr_course_ref" class="{{ $inputClass }}" required>
                                    <option value="">— اختر —</option>
                                </select>
                                <p class="text-[11px] text-slate-500 mt-1" id="agr_course_price_hint"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">وضع الحساب</label>
                                <select name="calc_mode" id="agr_calc_mode" class="{{ $inputClass }}" required>
                                    @foreach(\App\Models\SalesCourseCommissionAgreement::CALC_MODES as $k => $label)
                                        <option value="{{ $k }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3" id="agr_value_wrap">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">القيمة (ثابت أو %)</label>
                                <input type="number" step="0.01" min="0" name="commission_value" class="{{ $inputClass }}" value="0">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">فترة الـ Tier</label>
                                <select name="tier_period" class="{{ $inputClass }}">
                                    <option value="month">شهري</option>
                                    <option value="all">تراكمي</option>
                                </select>
                            </div>
                        </div>
                        <div id="agr_tiers_wrap" class="hidden overflow-x-auto rounded-xl border border-slate-200 bg-white">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200">
                                        <th class="px-3 py-2 text-right">من</th>
                                        <th class="px-3 py-2 text-right">إلى</th>
                                        <th class="px-3 py-2 text-right">عمولة</th>
                                        <th class="px-3 py-2 text-right">بونص</th>
                                        <th class="px-3 py-2 text-right">عند #</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($defaultTiers as $tier)
                                        <tr>
                                            <td class="px-2 py-1"><input type="number" name="agr_tier_min[]" value="{{ $tier['min'] }}" class="{{ $inputClass }}"></td>
                                            <td class="px-2 py-1"><input type="number" name="agr_tier_max[]" value="{{ $tier['max'] ?? '' }}" placeholder="∞" class="{{ $inputClass }}"></td>
                                            <td class="px-2 py-1"><input type="number" step="0.01" name="agr_tier_rate[]" value="{{ $tier['rate'] }}" class="{{ $inputClass }}"></td>
                                            <td class="px-2 py-1"><input type="number" step="0.01" name="agr_tier_bonus[]" value="{{ $tier['bonus'] }}" class="{{ $inputClass }}"></td>
                                            <td class="px-2 py-1"><input type="number" name="agr_tier_bonus_at[]" value="{{ $tier['bonus_at'] ?? '' }}" class="{{ $inputClass }}"></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 hover:bg-violet-700 px-4 py-2 text-sm font-semibold text-white">
                            <i class="fas fa-plus"></i>
                            إضافة الاتفاقية
                        </button>
                    </form>
                </div>
            </section>
    @endif
</div>

@push('scripts')
<script>
(function () {
    const modeSel = document.getElementById('sales_commission_mode');
    const valueWrap = document.getElementById('commission_value_wrap');
    const tierWrap = document.getElementById('tier_system_wrap');
    if (modeSel) {
        function sync() {
            const isTier = modeSel.value === 'tier';
            const hideValue = modeSel.value === 'none' || isTier;
            if (valueWrap) valueWrap.classList.toggle('hidden', hideValue);
            if (tierWrap) tierWrap.classList.toggle('hidden', !isTier);
        }
        modeSel.addEventListener('change', sync);
        sync();
    }

    const typeSel = document.getElementById('agr_course_type');
    const courseSel = document.getElementById('agr_course_ref');
    const priceHint = document.getElementById('agr_course_price_hint');
    const calcMode = document.getElementById('agr_calc_mode');
    const agrValueWrap = document.getElementById('agr_value_wrap');
    const agrTiersWrap = document.getElementById('agr_tiers_wrap');
    const coursesUrl = @json(route('admin.sales.course-commission.courses'));
    let coursesCache = {};

    async function loadCourses() {
        if (!typeSel || !courseSel) return;
        const type = typeSel.value;
        courseSel.innerHTML = '<option value="">… جاري التحميل</option>';
        try {
            if (!coursesCache[type]) {
                const res = await fetch(coursesUrl + '?type=' + encodeURIComponent(type), { headers: { 'Accept': 'application/json' } });
                const json = await res.json();
                coursesCache[type] = json.data || [];
            }
            const list = coursesCache[type];
            courseSel.innerHTML = '<option value="">— اختر —</option>';
            list.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = c.title + ' (' + Number(c.price).toFixed(2) + ' ج.م)';
                opt.dataset.price = c.price;
                courseSel.appendChild(opt);
            });
        } catch (e) {
            courseSel.innerHTML = '<option value="">تعذّر التحميل</option>';
        }
        if (priceHint) priceHint.textContent = '';
    }

    function syncAgrMode() {
        if (!calcMode) return;
        const mode = calcMode.value;
        const needsValue = mode === 'fixed' || mode === 'percent';
        const needsTiers = mode === 'tier_course' || mode === 'tier_course_global_count';
        if (agrValueWrap) agrValueWrap.classList.toggle('hidden', !needsValue && mode === 'tier_global');
        if (agrTiersWrap) agrTiersWrap.classList.toggle('hidden', !needsTiers);
    }

    function syncPriceHint() {
        if (!courseSel || !priceHint) return;
        const opt = courseSel.selectedOptions[0];
        priceHint.textContent = opt && opt.dataset.price ? ('سعر الكورس في النظام: ' + Number(opt.dataset.price).toFixed(2) + ' ج.م') : '';
    }

    if (typeSel) {
        typeSel.addEventListener('change', loadCourses);
        loadCourses();
    }
    if (courseSel) courseSel.addEventListener('change', syncPriceHint);
    if (calcMode) {
        calcMode.addEventListener('change', syncAgrMode);
        syncAgrMode();
    }
})();
</script>
@endpush
@endsection
