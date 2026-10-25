@extends('layouts.admin')

@section('title', 'تتبع التسويق')
@section('header', 'تتبع التسويق — إعدادات + تواجد مصر')

@section('content')
@php
    $s = $settings ?? [];
    $st = $trackingStatus ?? [];
    $egypt = $egypt ?? ['summary' => [], 'governorates' => [], 'cities' => [], 'top' => [], 'samples' => []];
    $sum = $egypt['summary'] ?? [];
@endphp
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-5 py-4 font-semibold text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <nav class="text-sm text-gray-500 mb-1">
                    <span class="font-semibold text-gray-700">التسويق</span>
                    <span class="mx-2">/</span>
                    <span class="font-semibold text-gray-700">تتبع التسويق</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-chart-line text-sky-600"></i>
                    تتبع التسويق وتواجد الجمهور في مصر
                </h1>
                <p class="text-sm text-gray-600 mt-1">إعدادات GTM / GA4 / Clarity / Meta Pixel + تحليل تلقائي للمحافظات من البيانات الحالية.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.marketing-regions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700">
                    <i class="fas fa-globe-africa"></i> خريطة المناطق
                </a>
                <a href="{{ route('admin.marketing-customer-surveys.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <i class="fas fa-clipboard-question"></i> الاستبيانات
                </a>
            </div>
        </div>
    </div>

    {{-- Tracking status cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
        @foreach([
            ['key' => 'enabled', 'label' => 'التتبع', 'icon' => 'fa-power-off'],
            ['key' => 'gtm', 'label' => 'GTM', 'icon' => 'fa-tags'],
            ['key' => 'ga4', 'label' => 'GA4', 'icon' => 'fab fa-google'],
            ['key' => 'clarity', 'label' => 'Clarity', 'icon' => 'fa-eye'],
            ['key' => 'meta', 'label' => 'Meta Pixel', 'icon' => 'fab fa-meta'],
        ] as $card)
            @php $on = (bool) ($st[$card['key']] ?? false); @endphp
            <div class="rounded-2xl p-4 border-2 bg-white shadow {{ $on ? 'border-emerald-200' : 'border-slate-200' }}">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-xs font-semibold text-slate-500">{{ $card['label'] }}</p>
                        <p class="text-sm font-black {{ $on ? 'text-emerald-700' : 'text-slate-500' }}">{{ $on ? 'مفعّل' : 'غير مضبوط' }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $on ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-400' }}">
                        <i class="{{ str_starts_with($card['icon'], 'fab') ? $card['icon'] : 'fas '.$card['icon'] }}"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Egypt auto insights --}}
    <section class="rounded-2xl bg-white border-2 border-emerald-100 shadow-xl overflow-hidden">
        <div class="px-6 py-5 border-b bg-gradient-to-r from-emerald-50 to-sky-50 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-emerald-600"></i>
                    تواجد الناس داخل مصر (تلقائي)
                </h2>
                <p class="text-xs text-slate-600 mt-1">
                    من الاستبيانات + عناوين المستخدمين + أرقام +20 + مواقع دخول IP —
                    الفترة: {{ $egypt['from'] ?? '' }} → {{ $egypt['to'] ?? '' }}
                </p>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs text-slate-500 font-semibold">محافظات ظاهرة</p>
                    <p class="text-2xl font-black text-slate-900">{{ number_format($sum['governorates'] ?? 0) }}</p>
                </div>
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-4">
                    <p class="text-xs text-emerald-700 font-semibold">من الاستبيان</p>
                    <p class="text-2xl font-black text-emerald-800">{{ number_format($sum['survey_people'] ?? 0) }}</p>
                </div>
                <div class="rounded-xl border border-sky-200 bg-sky-50/50 p-4">
                    <p class="text-xs text-sky-700 font-semibold">هواتف مصرية (+20)</p>
                    <p class="text-2xl font-black text-sky-800">{{ number_format($sum['egyptian_phones'] ?? 0) }}</p>
                </div>
                <div class="rounded-xl border border-violet-200 bg-violet-50/50 p-4">
                    <p class="text-xs text-violet-700 font-semibold">مدن من لوجات الدخول</p>
                    <p class="text-2xl font-black text-violet-800">{{ number_format($sum['eg_login_cities'] ?? 0) }}</p>
                </div>
            </div>

            <div class="grid lg:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-black text-slate-800 mb-3">أعلى المحافظات</h3>
                    @if(!empty($egypt['top']))
                        <canvas id="egyptGovChart" height="240"></canvas>
                    @else
                        <p class="text-sm text-slate-500 py-10 text-center rounded-xl border border-dashed border-slate-200">
                            لا بيانات محافظة بعد — ستظهر تلقائيًا من الاستبيانات والعناوين ولوجات الدخول داخل مصر.
                        </p>
                    @endif
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-800 mb-3">تفاصيل المحافظات</h3>
                    <div class="overflow-y-auto max-h-[320px] rounded-xl border border-slate-200">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-slate-500 sticky top-0">
                                <tr>
                                    <th class="text-right px-3 py-2">المحافظة</th>
                                    <th class="text-right px-3 py-2">العدد</th>
                                    <th class="text-right px-3 py-2">المصدر</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse(($egypt['governorates'] ?? []) as $g)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-3 py-2 font-bold text-slate-900">{{ $g['name'] }}</td>
                                        <td class="px-3 py-2 font-semibold">{{ number_format($g['count']) }}</td>
                                        <td class="px-3 py-2 text-xs text-slate-500">
                                            @php
                                                $srcMap = ['survey' => 'استبيان', 'address' => 'عنوان', 'login_ip' => 'دخول IP'];
                                            @endphp
                                            {{ collect($g['sources'] ?? [])->map(fn ($s) => $srcMap[$s] ?? $s)->implode(' · ') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="px-3 py-8 text-center text-slate-500">لا بيانات</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="grid lg:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-black text-slate-800 mb-3">مدن من IP تسجيل الدخول (مصر)</h3>
                    <div class="flex flex-wrap gap-2">
                        @forelse(($egypt['cities'] ?? []) as $city)
                            <span class="inline-flex items-center gap-2 rounded-full bg-violet-50 border border-violet-100 px-3 py-1.5 text-xs font-bold text-violet-800">
                                {{ $city['name'] }}
                                <span class="text-violet-500">{{ number_format($city['count']) }}</span>
                            </span>
                        @empty
                            <p class="text-sm text-slate-500">لا مدن مستنتجة بعد من اللوجات.</p>
                        @endforelse
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-800 mb-3">عيّنات دخول مصرية حديثة</h3>
                    <div class="overflow-y-auto max-h-[200px] rounded-xl border border-slate-200 text-xs">
                        <table class="min-w-full">
                            <tbody class="divide-y">
                                @forelse(($egypt['samples'] ?? []) as $row)
                                    <tr>
                                        <td class="px-3 py-2 font-semibold">{{ $row['user_name'] }}</td>
                                        <td class="px-3 py-2">{{ $row['city'] ?? $row['region_name'] ?? '—' }}</td>
                                        <td class="px-3 py-2 font-mono" dir="ltr">{{ $row['phone_country'] ?? '' }}</td>
                                    </tr>
                                @empty
                                    <tr><td class="px-3 py-6 text-center text-slate-500">لا عينات</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Settings form --}}
    <section class="rounded-2xl bg-white border-2 border-slate-200 shadow-xl overflow-hidden">
        <div class="px-6 py-5 border-b bg-gradient-to-r from-sky-50 via-indigo-50 to-violet-50">
            <h2 class="text-xl font-black text-slate-900">إعدادات التتبع والحملات</h2>
            <p class="text-sm text-slate-600 mt-1">Google Tag Manager · GA4 · Microsoft Clarity · Meta Pixel</p>
        </div>

        <div class="p-6">
            <form method="post" action="{{ route('admin.marketing-web-analytics.settings.update') }}" class="space-y-8 max-w-3xl">
                @csrf
                @method('PUT')

                <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50/60 p-4 cursor-pointer">
                    <input type="checkbox" name="enabled" value="1" @checked(old('enabled', $s['enabled'] ?? true)) class="mt-1 rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                    <span>
                        <span class="block text-sm font-bold text-slate-900">تفعيل التتبع على الصفحات العامة</span>
                        <span class="block text-xs text-slate-500 mt-0.5">لا يُحقَن على لوحات الموظفين أو الأدمن.</span>
                    </span>
                </label>

                <div class="rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="px-4 py-3 bg-slate-50 border-b flex items-center gap-2">
                        <i class="fab fa-google text-red-500"></i>
                        <h3 class="text-sm font-black text-slate-800">Google — GTM &amp; GA4</h3>
                    </div>
                    <div class="p-4 space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">GTM Container ID</label>
                            <input type="text" name="gtm_container_id" value="{{ old('gtm_container_id', $s['gtm_container_id'] ?? '') }}"
                                   placeholder="GTM-XXXXXXX" dir="ltr"
                                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-sky-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">GA4 Measurement ID</label>
                            <input type="text" name="ga4_measurement_id" value="{{ old('ga4_measurement_id', $s['ga4_measurement_id'] ?? '') }}"
                                   placeholder="G-XXXXXXXXXX" dir="ltr"
                                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-sky-500">
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="px-4 py-3 bg-slate-50 border-b flex items-center gap-2">
                        <i class="fab fa-microsoft text-sky-600"></i>
                        <h3 class="text-sm font-black text-slate-800">Microsoft Clarity</h3>
                    </div>
                    <div class="p-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Clarity Project ID</label>
                        <input type="text" name="clarity_project_id" value="{{ old('clarity_project_id', $s['clarity_project_id'] ?? '') }}"
                               placeholder="xxxxxxxxxx" dir="ltr"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-sky-500">
                    </div>
                </div>

                <div class="rounded-2xl border-2 border-blue-100 overflow-hidden">
                    <div class="px-4 py-3 bg-gradient-to-r from-blue-50 to-indigo-50 border-b flex items-center gap-2">
                        <i class="fab fa-meta text-blue-600"></i>
                        <h3 class="text-sm font-black text-slate-800">Meta Pixel</h3>
                    </div>
                    <div class="p-4 space-y-4">
                        <label class="flex items-start gap-3 rounded-xl border border-blue-100 bg-blue-50/40 p-4 cursor-pointer">
                            <input type="checkbox" name="meta_pixel_enabled" value="1" @checked(old('meta_pixel_enabled', $s['meta_pixel_enabled'] ?? true)) class="mt-1 rounded border-blue-300 text-blue-600">
                            <span class="text-sm font-bold text-slate-900">تفعيل Meta Pixel</span>
                        </label>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Meta Pixel ID</label>
                            <input type="text" name="meta_pixel_id" value="{{ old('meta_pixel_id', $s['meta_pixel_id'] ?? '') }}"
                                   placeholder="123456789012345" dir="ltr" inputmode="numeric"
                                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="px-4 py-3 bg-slate-50 border-b flex items-center gap-2">
                        <i class="fas fa-store text-emerald-600"></i>
                        <h3 class="text-sm font-black text-slate-800">إعدادات الكتالوج</h3>
                    </div>
                    <div class="p-4 grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">العملة</label>
                            <input type="text" name="currency" value="{{ old('currency', $s['currency'] ?? 'EGP') }}" dir="ltr"
                                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">العلامة التجارية</label>
                            <input type="text" name="item_brand" value="{{ old('item_brand', $s['item_brand'] ?? 'Mindlytics') }}"
                                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm">
                        </div>
                    </div>
                </div>

                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-sky-600 to-violet-600 text-white text-sm font-bold shadow-lg">
                    <i class="fas fa-save"></i> حفظ الإعدادات
                </button>
            </form>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    const labels = @json(collect($egypt['top'] ?? [])->pluck('name'));
    const values = @json(collect($egypt['top'] ?? [])->pluck('count'));
    const el = document.getElementById('egyptGovChart');
    if (!el || !window.Chart || !labels.length) return;
    new Chart(el, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'التواجد',
                data: values,
                backgroundColor: 'rgba(16, 185, 129, 0.75)',
                borderRadius: 8
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
})();
</script>
@endpush
