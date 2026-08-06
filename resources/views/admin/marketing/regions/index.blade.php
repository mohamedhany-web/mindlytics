@extends('layouts.admin')

@section('title', 'المناطق')
@section('header', 'التسويق — المناطق والزيارات')

@section('content')
@php
    $d = $data ?? [];
    $summary = $d['summary'] ?? [];
    $countries = $d['countries'] ?? [];
    $map = $d['map'] ?? [];
    $governorates = $d['governorates'] ?? [];
    $recent = $d['recent_logins'] ?? [];
    $metric = $d['metric'] ?? 'combined';
@endphp
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-4">
            <div>
                <nav class="text-sm text-gray-500 mb-1">
                    <span class="font-semibold text-gray-700">التسويق</span>
                    <span class="mx-2">/</span>
                    <span class="font-semibold text-gray-700">المناطق</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-globe-africa text-sky-600"></i>
                    من أين يأتي الجمهور؟
                </h1>
                <p class="text-sm text-gray-600 mt-1">
                    التسجيلات من كود هاتف المستخدم · تسجيلات الدخول من IP في اللوجات · الزيارات العامة تُحسب تلقائيًا بعد التفعيل.
                </p>
            </div>
            <form method="get" class="flex flex-wrap items-end gap-2">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">من</label>
                    <input type="date" name="from" value="{{ $d['from'] ?? '' }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">إلى</label>
                    <input type="date" name="to" value="{{ $d['to'] ?? '' }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">المقياس على الخريطة</label>
                    <select name="metric" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <option value="combined" @selected($metric === 'combined')>مجمّع</option>
                        <option value="registrations" @selected($metric === 'registrations')>تسجيلات</option>
                        <option value="logins" @selected($metric === 'logins')>دخول (لوجات)</option>
                        <option value="visits" @selected($metric === 'visits')>زيارات صفحات</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 rounded-lg bg-sky-600 text-white text-sm font-bold hover:bg-sky-700">تطبيق</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl p-5 border-2 border-sky-200/60 bg-white shadow">
            <p class="text-sm text-slate-600 font-semibold">دول ظاهرة</p>
            <p class="text-3xl font-black text-slate-900">{{ number_format($summary['countries'] ?? 0) }}</p>
        </div>
        <div class="rounded-2xl p-5 border-2 border-emerald-200/60 bg-white shadow">
            <p class="text-sm text-slate-600 font-semibold">تسجيلات (هاتف → دولة)</p>
            <p class="text-3xl font-black text-emerald-700">{{ number_format($summary['registrations'] ?? 0) }}</p>
        </div>
        <div class="rounded-2xl p-5 border-2 border-violet-200/60 bg-white shadow">
            <p class="text-sm text-slate-600 font-semibold">دخول من اللوجات</p>
            <p class="text-3xl font-black text-violet-700">{{ number_format($summary['logins'] ?? 0) }}</p>
        </div>
        <div class="rounded-2xl p-5 border-2 border-amber-200/60 bg-white shadow">
            <p class="text-sm text-slate-600 font-semibold">زيارات صفحات</p>
            <p class="text-3xl font-black text-amber-700">{{ number_format($summary['visits'] ?? 0) }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-5 gap-6">
        <section class="lg:col-span-3 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b bg-slate-50 flex items-center justify-between gap-2">
                <h2 class="text-sm font-black text-slate-800"><i class="fas fa-map-marked-alt text-sky-500 ml-1"></i> خريطة العالم</h2>
                <span class="text-xs text-slate-500">أغمق = أكثر نشاطًا حسب المقياس المختار</span>
            </div>
            <div class="p-4">
                <div id="regions-world-map" class="w-full rounded-xl border border-slate-100 bg-slate-50" style="height: 420px;"></div>
                <p id="regions-map-fallback" class="hidden text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 mt-3">
                    تعذر تحميل مكتبة الخريطة. الجدول أدناه يعرض نفس البيانات.
                </p>
            </div>
        </section>

        <section class="lg:col-span-2 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b bg-slate-50">
                <h2 class="text-sm font-black text-slate-800">أعلى الدول</h2>
            </div>
            <div class="overflow-y-auto max-h-[420px]">
                <table class="min-w-full text-sm">
                    <thead class="bg-white sticky top-0 text-slate-500">
                        <tr>
                            <th class="text-right px-3 py-2">الدولة</th>
                            <th class="text-right px-3 py-2">تسجيل</th>
                            <th class="text-right px-3 py-2">دخول</th>
                            <th class="text-right px-3 py-2">زيارة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($countries as $c)
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-2">
                                    <span class="font-bold text-slate-900">{{ $c['name_ar'] }}</span>
                                    <span class="text-xs text-slate-400 font-mono block" dir="ltr">{{ $c['country_code'] }}</span>
                                </td>
                                <td class="px-3 py-2 font-semibold">{{ number_format($c['registrations'] ?? 0) }}</td>
                                <td class="px-3 py-2 font-semibold">{{ number_format($c['logins'] ?? 0) }}</td>
                                <td class="px-3 py-2 font-semibold">{{ number_format($c['visits'] ?? 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-3 py-8 text-center text-slate-500">لا بيانات في الفترة المحددة</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <section class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b bg-slate-50">
                <h2 class="text-sm font-black text-slate-800">محافظات مصر (من استبيان العملاء)</h2>
            </div>
            <div class="p-4">
                @if(count($governorates))
                    <canvas id="chartGovernorates" height="220"></canvas>
                @else
                    <p class="text-sm text-slate-500 py-8 text-center">لا توجد إجابات استبيان بمحافظة في هذه الفترة.</p>
                @endif
            </div>
        </section>

        <section class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b bg-slate-50">
                <h2 class="text-sm font-black text-slate-800">آخر تسجيلات دخول + الموقع من اللوج</h2>
            </div>
            <div class="overflow-x-auto max-h-[360px] overflow-y-auto">
                <table class="min-w-full text-xs">
                    <thead class="bg-slate-50 text-slate-500 sticky top-0">
                        <tr>
                            <th class="text-right px-3 py-2">المستخدم</th>
                            <th class="text-right px-3 py-2">هاتف → دولة</th>
                            <th class="text-right px-3 py-2">IP → موقع</th>
                            <th class="text-right px-3 py-2">الوقت</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($recent as $row)
                            <tr>
                                <td class="px-3 py-2">
                                    <div class="font-semibold text-slate-800">{{ $row['user_name'] }}</div>
                                    <div class="text-slate-400 truncate max-w-[140px]">{{ $row['email'] }}</div>
                                </td>
                                <td class="px-3 py-2 font-mono" dir="ltr">{{ $row['phone_country'] ?? '—' }}</td>
                                <td class="px-3 py-2">
                                    <div class="font-semibold">{{ $row['country_name'] ?? ($row['country_code'] ?? '—') }}</div>
                                    <div class="text-slate-500">{{ $row['city'] ?? '' }} {{ $row['region_name'] ?? '' }}</div>
                                    <div class="text-slate-400 font-mono" dir="ltr">{{ $row['ip'] }}</div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap" dir="ltr">{{ $row['at'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-3 py-8 text-center text-slate-500">لا لوجات دخول في الفترة</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div class="rounded-xl border border-sky-100 bg-sky-50 px-4 py-3 text-xs text-sky-900 leading-relaxed">
        <strong>مصادر البيانات:</strong>
        التسجيلات = استنتاج الدولة من مقدمة رقم الهاتف (إعدادات phone_countries).
        الدخول = عناوين IP في جدول activity_logs مع تحديد جغرافي مخزَّن محليًا.
        الزيارات = عدّاد يومي لصفحات الموقع العامة (زيارة واحدة لكل جلسة متصفح يوميًا) يبدأ من بعد نشر هذه الميزة.
        للمحافظات داخل مصر يُستخدم استبيان العملاء إن وُجد.
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/js/jsvectormap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/maps/world.js"></script>
<script>
(function () {
    const mapData = @json($map);
    const govLabels = @json(collect($governorates)->pluck('name'));
    const govValues = @json(collect($governorates)->pluck('count'));

    try {
        if (window.jsVectorMap && document.getElementById('regions-world-map')) {
            new jsVectorMap({
                selector: '#regions-world-map',
                map: 'world',
                backgroundColor: 'transparent',
                zoomButtons: true,
                zoomOnScroll: false,
                series: {
                    regions: [{
                        attribute: 'fill',
                        scale: ['#e0f2fe', '#0369a1'],
                        values: mapData,
                        normalizeFunction: 'polynomial'
                    }]
                },
                regionStyle: {
                    initial: { fill: '#e2e8f0', stroke: '#fff', strokeWidth: 0.5 },
                    hover: { fill: '#0ea5e9' }
                },
                onRegionTooltipShow(event, tooltip, code) {
                    const value = mapData[code] || 0;
                    tooltip.text(code + ': ' + value);
                }
            });
        } else {
            document.getElementById('regions-map-fallback')?.classList.remove('hidden');
        }
    } catch (e) {
        document.getElementById('regions-map-fallback')?.classList.remove('hidden');
    }

    const govCanvas = document.getElementById('chartGovernorates');
    if (govCanvas && window.Chart && govLabels.length) {
        new Chart(govCanvas, {
            type: 'bar',
            data: {
                labels: govLabels,
                datasets: [{
                    label: 'استبيانات',
                    data: govValues,
                    backgroundColor: 'rgba(14, 165, 233, 0.7)',
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    }
})();
</script>
@endpush
