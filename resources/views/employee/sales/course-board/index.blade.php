@extends('layouts.employee')

@section('title', 'معلومات الكورسات')
@section('header', 'معلومات الكورسات')

@push('styles')
@include('employee.sales._styles')
<style>
    .course-board-table th { white-space: nowrap; }
    .course-board-table td { vertical-align: top; }
    .copy-btn.copied { background: #059669; color: #fff; border-color: #059669; }
</style>
@endpush

@section('content')
<div class="space-y-6 sales-hub">
    <div class="dashboard-card flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Course board</h2>
            <p class="text-slate-600 text-sm mt-1">
                {{ $entries->count() }} courses
                @if($updatedAt)
                    · updated {{ \Carbon\Carbon::parse($updatedAt)->format('M j, Y') }}
                @endif
            </p>
            <p class="text-slate-500 text-sm mt-2">مرجع سريع للأسعار والمواعيد — انسخ رابط Landing وأرسله للعميل.</p>
        </div>
    </div>

    <div class="panel-card overflow-hidden">
        <div class="panel-card-head px-4 py-3 font-bold text-slate-800">
            <i class="fas fa-graduation-cap ml-2 text-emerald-600"></i> جدول الكورسات
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm course-board-table">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th class="px-3 py-3 text-right font-bold">Course</th>
                        <th class="px-3 py-3 text-right font-bold">Audience</th>
                        <th class="px-3 py-3 text-right font-bold">Instructor</th>
                        <th class="px-3 py-3 text-right font-bold">Starts</th>
                        <th class="px-3 py-3 text-right font-bold">Days</th>
                        <th class="px-3 py-3 text-right font-bold">Duration</th>
                        <th class="px-3 py-3 text-right font-bold">Hours</th>
                        <th class="px-3 py-3 text-right font-bold">Price (EGP)</th>
                        <th class="px-3 py-3 text-right font-bold">Format</th>
                        <th class="px-3 py-3 text-right font-bold">Landing page</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($entries as $entry)
                        <tr class="hover:bg-slate-50/70 {{ ! $entry->isComplete() ? 'bg-amber-50/40' : '' }}">
                            <td class="px-3 py-3 font-bold text-slate-900">{{ $entry->name }}</td>
                            <td class="px-3 py-3 text-slate-600">{{ $entry->audience ?: '—' }}</td>
                            <td class="px-3 py-3 text-slate-600">{{ $entry->instructor_name ?: '—' }}</td>
                            <td class="px-3 py-3 text-slate-600">{{ $entry->start_label ?: '—' }}</td>
                            <td class="px-3 py-3 text-slate-600">{{ $entry->schedule_days ?: '—' }}</td>
                            <td class="px-3 py-3 text-slate-600">{{ $entry->duration ?: '—' }}</td>
                            <td class="px-3 py-3 text-slate-600">{{ $entry->hours ?: '—' }}</td>
                            <td class="px-3 py-3 text-slate-800 whitespace-nowrap">
                                @if($entry->price_online)
                                    <div>online {{ number_format((float) $entry->price_online, 0) }}</div>
                                @endif
                                @if($entry->price_recorded)
                                    <div class="text-slate-500">recorded {{ number_format((float) $entry->price_recorded, 0) }}</div>
                                @endif
                                @if(! $entry->price_online && ! $entry->price_recorded)
                                    —
                                @endif
                            </td>
                            <td class="px-3 py-3 text-slate-600">{{ $entry->format ?: '—' }}</td>
                            <td class="px-3 py-3 whitespace-nowrap">
                                @if($url = $entry->landingUrl())
                                    <div class="flex flex-col gap-1.5">
                                        <a href="{{ $url }}" target="_blank" class="text-emerald-700 hover:underline font-semibold text-xs">
                                            <i class="fas fa-external-link-alt ml-1"></i> فتح
                                        </a>
                                        <button type="button"
                                                class="copy-btn inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                                data-copy-url="{{ $url }}">
                                            <i class="fas fa-link"></i> نسخ الرابط
                                        </button>
                                    </div>
                                @else
                                    <span class="text-xs text-amber-700 font-semibold">Needs details</span>
                                @endif
                            </td>
                        </tr>
                        @if(filled($entry->summary))
                            <tr class="bg-slate-50/50">
                                <td colspan="10" class="px-3 py-2 text-xs text-slate-500">{{ $entry->summary }}</td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-12 text-center text-slate-500">لا توجد كورسات بعد — تواصل مع الإدارة.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.copy-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var url = btn.getAttribute('data-copy-url');
        if (!url) return;
        navigator.clipboard.writeText(url).then(function () {
            btn.classList.add('copied');
            var old = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> تم النسخ';
            setTimeout(function () {
                btn.classList.remove('copied');
                btn.innerHTML = old;
            }, 1800);
        });
    });
});
</script>
@endpush
