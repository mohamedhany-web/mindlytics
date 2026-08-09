@extends('layouts.admin')

@section('title', 'الشهادات')
@section('header', 'الشهادات')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">الشهادات</h1>
                <p class="text-gray-600 mt-1">إدارة شهادات الطلاب وتصميمات الشهادات</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <a href="{{ route('admin.certificates.branding') }}"
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center gap-2">
                    <i class="fas fa-image"></i>
                    <span>هوية الشهادات</span>
                </a>
                <a href="{{ route('admin.certificates.create') }}"
                   class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span>إصدار شهادة جديدة</span>
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden" x-data="{ activeTab: 'list' }">
        <div class="border-b border-gray-200 flex">
            <button type="button" @click="activeTab = 'list'"
                    :class="activeTab === 'list' ? 'border-b-2 border-sky-600 text-sky-700 font-semibold' : 'text-gray-600 hover:text-gray-900'"
                    class="px-6 py-4 text-sm font-medium transition-colors">
                <i class="fas fa-list ml-2"></i>
                قائمة الشهادات
            </button>
            <button type="button" @click="activeTab = 'templates'"
                    :class="activeTab === 'templates' ? 'border-b-2 border-sky-600 text-sky-700 font-semibold' : 'text-gray-600 hover:text-gray-900'"
                    class="px-6 py-4 text-sm font-medium transition-colors">
                <i class="fas fa-palette ml-2"></i>
                تصميمات الشهادات
            </button>
        </div>

        <div x-show="activeTab === 'list'" x-cloak class="p-6">
            @if(isset($stats))
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="rounded-xl p-5 border border-sky-100 bg-sky-50">
                        <p class="text-sm text-sky-700 font-medium">إجمالي الشهادات</p>
                        <p class="text-3xl font-black text-sky-900 mt-1">{{ $stats['total'] ?? 0 }}</p>
                    </div>
                    <div class="rounded-xl p-5 border border-emerald-100 bg-emerald-50">
                        <p class="text-sm text-emerald-700 font-medium">المُصدرة</p>
                        <p class="text-3xl font-black text-emerald-900 mt-1">{{ $stats['issued'] ?? 0 }}</p>
                    </div>
                    <div class="rounded-xl p-5 border border-amber-100 bg-amber-50">
                        <p class="text-sm text-amber-700 font-medium">المعلقة</p>
                        <p class="text-3xl font-black text-amber-900 mt-1">{{ $stats['pending'] ?? 0 }}</p>
                    </div>
                </div>
            @endif

            @if(isset($certificates) && $certificates->count() > 0)
                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3 text-right font-semibold">رقم الشهادة</th>
                                <th class="px-4 py-3 text-right font-semibold">السيريال</th>
                                <th class="px-4 py-3 text-right font-semibold">الطالب</th>
                                <th class="px-4 py-3 text-right font-semibold">العنوان</th>
                                <th class="px-4 py-3 text-right font-semibold">الكورس</th>
                                <th class="px-4 py-3 text-right font-semibold">الحالة</th>
                                <th class="px-4 py-3 text-right font-semibold">تاريخ الإصدار</th>
                                <th class="px-4 py-3 text-right font-semibold">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($certificates as $certificate)
                                @php
                                    $status = $certificate->status ?? ($certificate->is_verified ? 'issued' : 'pending');
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-mono text-gray-900">{{ $certificate->certificate_number }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-slate-700">{{ $certificate->serial_number ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $certificate->user->name ?? 'غير معروف' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $certificate->title ?? $certificate->course_name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $certificate->course->title ?? ($certificate->course_name ?? '-') }}</td>
                                    <td class="px-4 py-3">
                                        <span @class([
                                            'inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium',
                                            'bg-emerald-100 text-emerald-800' => $status === 'issued',
                                            'bg-amber-100 text-amber-800' => $status === 'pending',
                                            'bg-rose-100 text-rose-800' => $status === 'revoked',
                                            'bg-gray-100 text-gray-800' => ! in_array($status, ['issued', 'pending', 'revoked'], true),
                                        ])>
                                            @if($status === 'issued') مُصدرة
                                            @elseif($status === 'pending') معلقة
                                            @elseif($status === 'revoked') ملغاة
                                            @else {{ $status }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ $certificate->issued_at?->format('Y-m-d') ?? $certificate->issue_date?->format('Y-m-d') ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('admin.certificates.show', $certificate) }}"
                                           class="inline-flex items-center gap-1 text-sky-600 hover:text-sky-800 font-semibold">
                                            <i class="fas fa-eye"></i> عرض
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $certificates->links() }}</div>
            @else
                <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-12 text-center">
                    <i class="fas fa-certificate text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">لا توجد شهادات</h3>
                    <p class="text-sm text-gray-500 mb-5">لم يتم إصدار أي شهادات حتى الآن</p>
                    <a href="{{ route('admin.certificates.create') }}"
                       class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold">
                        <i class="fas fa-plus"></i> إصدار شهادة جديدة
                    </a>
                </div>
            @endif
        </div>

        <div x-show="activeTab === 'templates'" x-cloak class="p-6">
            <div class="mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-1">تصاميم الشهادات ({{ count($templates) }})</h2>
                    <p class="text-sm text-gray-600">معاينة خفيفة — الضغط يفتح التصميم كامل في نافذة منفصلة بدون تخريب الصفحة.</p>
                </div>
                <a href="{{ route('admin.certificates.branding') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold text-sm">
                    <i class="fas fa-stamp"></i> هوية + الختم الضريبي
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($templates as $key => $tpl)
                    @php
                        $isDesign = isset(\App\Services\CertificateIssueService::designCatalog()[$key]);
                        $previewUrl = $isDesign
                            ? route('admin.certificates.design-preview', $key)
                            : null;
                    @endphp
                    <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm">
                        <div class="h-44 bg-slate-900 relative overflow-hidden">
                            @if($previewUrl)
                                <iframe src="{{ $previewUrl }}"
                                        class="pointer-events-none absolute inset-0 w-[1122px] h-[793px] origin-top-left border-0"
                                        style="transform: scale(0.28);"
                                        loading="lazy"
                                        tabindex="-1"></iframe>
                            @else
                                <div class="absolute inset-0 flex items-center justify-center text-slate-300 text-sm font-semibold px-4 text-center">
                                    {{ $tpl['name'] }}
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <p class="font-bold text-gray-900">{{ $tpl['name'] }}</p>
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $tpl['description'] ?? '' }}</p>
                            <div class="mt-3 flex gap-2">
                                @if($previewUrl)
                                    <a href="{{ $previewUrl }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 text-white text-xs font-bold px-3 py-2 hover:bg-slate-800">
                                        <i class="fas fa-expand"></i> معاينة كاملة
                                    </a>
                                @endif
                                <span class="inline-flex items-center rounded-lg bg-emerald-50 text-emerald-800 text-xs font-bold px-2.5 py-2">
                                    {{ $key }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
