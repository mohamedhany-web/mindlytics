@extends('layouts.admin')

@section('title', 'هيكل قسم المبيعات')
@section('header', 'قسم المبيعات — الهرم الوظيفي')

@section('content')
<div class="space-y-8">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-check-circle ml-1"></i>{{ session('success') }}
        </div>
    @endif

    <section class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-950">
        <p class="font-bold mb-1"><i class="fas fa-info-circle ml-1"></i> ربط الوظائف بالهرم</p>
        <p class="text-xs leading-relaxed">
            <strong>مدير مبيعات</strong> برمز <code class="bg-white px-1.5 py-0.5 rounded border border-emerald-200">sales_manager</code>
            · <strong>موظف مبيعات</strong> برمز <code class="bg-white px-1.5 py-0.5 rounded border border-emerald-200">sales</code>.
            من القائمة بالأسفل عيّن المدير المباشر لكل موظف لبناء شجرة التبعية.
        </p>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-lg overflow-hidden">
        <div class="px-5 py-5 bg-gradient-to-l from-slate-900 via-emerald-950 to-teal-900 text-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-200/90 mb-1">Sales Department</p>
                    <h2 class="text-2xl font-black tracking-tight">الهرم الوظيفي — قسم المبيعات</h2>
                    <p class="text-sm text-slate-300 mt-1 max-w-2xl">
                        مدير المبيعات في القمة، وتحته فريق موظفي المبيعات — مع Leads ومتابعات لكل فرد.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.sales.distribution.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-white/10 hover:bg-white/20 px-3 py-2 text-xs font-semibold border border-white/15">
                        <i class="fas fa-share-nodes"></i> توزيع Leads
                    </a>
                    <a href="{{ route('admin.employees.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-white/10 hover:bg-white/20 px-3 py-2 text-xs font-semibold border border-white/15">
                        <i class="fas fa-users"></i> الموظفون
                    </a>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 p-4">
            @foreach([
                ['label' => 'مديرو مبيعات', 'value' => $stats['managers'], 'tone' => 'text-teal-700 bg-teal-50 border-teal-100'],
                ['label' => 'موظفو مبيعات', 'value' => $stats['employees'], 'tone' => 'text-emerald-700 bg-emerald-50 border-emerald-100'],
                ['label' => 'Leads مفتوحة', 'value' => $stats['open_leads'], 'tone' => 'text-amber-700 bg-amber-50 border-amber-100'],
                ['label' => 'بدون مدير مباشر', 'value' => $stats['unlinked_reps'], 'tone' => 'text-rose-700 bg-rose-50 border-rose-100'],
            ] as $card)
                <div class="rounded-xl border p-3 {{ $card['tone'] }}">
                    <p class="text-[11px] font-semibold opacity-80">{{ $card['label'] }}</p>
                    <p class="text-2xl font-black tabular-nums mt-0.5">{{ number_format($card['value']) }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- الهرم البصري --}}
    <section class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 sm:p-8 overflow-x-auto">
        <div class="min-w-[520px] max-w-3xl mx-auto">
            <div class="flex justify-center mb-2">
                <div class="rounded-2xl bg-slate-900 text-white px-8 py-4 shadow-xl text-center border border-slate-700">
                    <div class="text-2xl mb-1"><i class="fas fa-handshake text-emerald-300"></i></div>
                    <p class="text-lg font-black">قسم المبيعات</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Sales · Leads · Follow-ups</p>
                </div>
            </div>

            <div class="flex justify-center"><div class="w-px h-8 bg-slate-300"></div></div>

            <div class="flex justify-center mb-2">
                <div class="rounded-2xl border-2 border-teal-300 bg-white px-6 py-4 shadow-md text-center min-w-[240px]">
                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-teal-100 text-teal-700 mb-2">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <p class="text-base font-black text-slate-900">مدير المبيعات</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">إدارة الفريق والأداء والتقارير</p>
                    <p class="mt-2 text-xs font-bold text-teal-700">{{ $managers->count() }} نشط</p>
                </div>
            </div>

            <div class="flex justify-center"><div class="w-px h-8 bg-slate-300"></div></div>

            <div class="flex justify-center">
                <div class="rounded-2xl border-2 border-emerald-300 bg-white px-6 py-4 shadow-md text-center min-w-[240px] max-w-sm">
                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 mb-2">
                        <i class="fas fa-headset"></i>
                    </div>
                    <p class="text-base font-black text-slate-900">موظف مبيعات</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">متابعة Leads والمتابعات وإغلاق الصفقات</p>
                    <p class="mt-2 text-xs font-bold text-emerald-700">{{ $employees->count() }} نشط</p>
                </div>
            </div>
        </div>
    </section>

    {{-- قوائم الأشخاص --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
        <section class="rounded-2xl border border-teal-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-teal-100 bg-teal-50/80 flex items-center justify-between">
                <h3 class="font-black text-teal-950 flex items-center gap-2">
                    <i class="fas fa-user-shield text-teal-600"></i>
                    مديرو المبيعات
                </h3>
                <span class="text-xs font-bold text-teal-700">{{ $managers->count() }}</span>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($managers as $user)
                    @php
                        $specs = $user->salesInterestTypes->pluck('name_ar')->implode(' · ') ?: '—';
                    @endphp
                    <div class="p-4">
                        <p class="font-bold text-slate-900">{{ $user->name }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $user->email }}</p>
                        <p class="text-[11px] text-slate-600 mt-1">تخصصات: {{ $specs }}</p>
                        <div class="mt-2 flex flex-wrap gap-2 text-[11px] font-semibold">
                            <span class="rounded-lg bg-teal-50 text-teal-800 px-2 py-0.5 border border-teal-100">
                                مرؤوسون مباشرون: {{ (int) ($directReportsCount[$user->id] ?? 0) }}
                            </span>
                            <span class="rounded-lg bg-amber-50 text-amber-800 px-2 py-0.5 border border-amber-100">
                                Leads مفتوحة: {{ (int) ($openCounts[$user->id] ?? 0) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="p-6 text-sm text-slate-500 text-center">لا يوجد مدير مبيعات. عيّن وظيفة <code class="text-xs bg-slate-100 px-1 rounded">sales_manager</code>.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-emerald-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-emerald-100 bg-emerald-50/80 flex items-center justify-between">
                <h3 class="font-black text-emerald-950 flex items-center gap-2">
                    <i class="fas fa-headset text-emerald-600"></i>
                    موظفو المبيعات
                </h3>
                <span class="text-xs font-bold text-emerald-700">{{ $employees->count() }}</span>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($employees as $user)
                    @php
                        $specs = $user->salesInterestTypes->pluck('name_ar')->implode(' · ') ?: '—';
                        $manager = $staff->firstWhere('id', $user->sales_reports_to_id);
                    @endphp
                    <div class="p-4">
                        <p class="font-bold text-slate-900">{{ $user->name }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $user->email }}</p>
                        <p class="text-[11px] text-slate-600 mt-1">
                            المدير المباشر:
                            <strong>{{ $manager?->name ?? '— غير محدد —' }}</strong>
                        </p>
                        <p class="text-[11px] text-slate-600">تخصصات: {{ $specs }}</p>
                        <div class="mt-2">
                            <span class="rounded-lg bg-emerald-50 text-emerald-800 px-2 py-0.5 border border-emerald-100 text-[11px] font-semibold">
                                Leads مفتوحة: {{ (int) ($openCounts[$user->id] ?? 0) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="p-6 text-sm text-slate-500 text-center">لا يوجد موظف مبيعات. عيّن وظيفة <code class="text-xs bg-slate-100 px-1 rounded">sales</code>.</p>
                @endforelse
            </div>
        </section>
    </div>

    {{-- شجرة التبعية الفعلية --}}
    <section class="rounded-2xl border border-slate-200 bg-white shadow-lg overflow-hidden">
        <div class="px-4 py-4 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900">شجرة التبعية (مدير مباشر)</h3>
            <p class="text-xs text-slate-600 mt-1">الهيكل الفعلي حسب «المدير المباشر» لكل موظف — يمكن تعديله من كل بطاقة أو من القائمة السريعة بالأسفل.</p>
        </div>
        <div class="p-4 bg-slate-50/80">
            @forelse($tree as $node)
                @include('admin.sales.org-chart._node', ['node' => $node, 'depth' => 0, 'staff' => $staff, 'openCounts' => $openCounts, 'readonly' => false])
            @empty
                <p class="text-sm text-slate-500 p-4 text-center">لا يوجد هيكل بعد — عيّن المدير المباشر من القائمة بالأسفل.</p>
            @endforelse
        </div>
    </section>

    {{-- ربط سريع --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b bg-slate-50 font-black flex items-center gap-2">
            <i class="fas fa-link text-emerald-600"></i>
            ربط سريع — المدير المباشر
        </div>
        <div class="divide-y">
            @forelse($staff as $user)
                <form method="post" action="{{ route('admin.sales.org-chart.update', $user) }}" class="p-4 flex flex-col sm:flex-row sm:items-center gap-3">
                    @csrf @method('PUT')
                    <div class="min-w-[180px]">
                        <p class="font-bold text-slate-900">{{ $user->name }}</p>
                        <p class="text-[11px] text-slate-500">{{ $user->isSalesManager() ? 'مدير مبيعات' : 'موظف مبيعات' }}</p>
                    </div>
                    <select name="sales_reports_to_id" class="rounded-xl border border-slate-300 px-3 py-2 text-sm flex-1 bg-white">
                        <option value="">— بدون مدير مباشر —</option>
                        @foreach($staff as $cand)
                            @if($cand->id !== $user->id)
                                <option value="{{ $cand->id }}" @selected((int) $user->sales_reports_to_id === (int) $cand->id)>
                                    {{ $cand->name }} ({{ $cand->isSalesManager() ? 'مدير' : 'موظف' }})
                                </option>
                            @endif
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-xs font-semibold shrink-0">تحديث</button>
                </form>
            @empty
                <p class="p-6 text-sm text-slate-500 text-center">لا يوجد موظفو مبيعات نشطون.</p>
            @endforelse
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 text-sm text-slate-600">
        <p class="font-bold text-slate-900 mb-2">كيف يعمل الهرم؟</p>
        <ul class="list-disc list-inside space-y-1 text-xs leading-relaxed">
            <li><strong>مدير المبيعات</strong> يتابع الفريق، الشيفتات، Leads، والتقارير اليومية.</li>
            <li><strong>موظف المبيعات</strong> يستلم Leads ويتابعها من مركز المبيعات.</li>
            <li>اربط كل موظف بمديره المباشر ليظهر في الشجرة وفي تقارير الفريق.</li>
        </ul>
    </section>
</div>
@endsection
