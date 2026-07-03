@extends('layouts.admin')

@section('title', 'دفعات إرسال الواتساب - Mindlytics')
@section('header', 'قسم الواتساب')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.whatsapp._alerts')

    @include('admin.whatsapp._page-header', [
        'title' => 'دفعات الإرسال',
        'subtitle' => 'متابعة الإرسال الجماعي في الخلفية — من تم ومن لم يُرسل.',
        'icon' => 'fas fa-layer-group',
        'actions' => '<a href="' . route('admin.whatsapp.send') . '" class="' . $waBtnPrimary . '"><i class="fas fa-paper-plane"></i> إرسال جديد</a>',
    ])

    <section class="{{ $waSectionClass }}">
        <div class="px-5 py-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-lg font-bold text-slate-900">كل الدفعات</h3>
            <form method="GET" class="flex flex-wrap gap-2 text-sm">
                <select name="status" class="{{ $waSelectClass }} !py-1.5 !text-xs" onchange="this.form.submit()">
                    <option value="">كل الحالات</option>
                    @foreach(['pending' => 'في الانتظار', 'processing' => 'جاري الإرسال', 'completed' => 'اكتمل'] as $val => $label)
                        <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="source_type" class="{{ $waSelectClass }} !py-1.5 !text-xs" onchange="this.form.submit()">
                    <option value="">كل المصادر</option>
                    <option value="workshop" @selected(request('source_type') === 'workshop')>ورش</option>
                    <option value="admin_bulk" @selected(request('source_type') === 'admin_bulk')>إرسال جماعي</option>
                </select>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">#</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">العنوان</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">الحالة</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">النتيجة</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500">التاريخ</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($batches as $batch)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 text-sm text-slate-500">{{ $batch->id }}</td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-semibold text-slate-900">{{ $batch->title ?: 'دفعة #' . $batch->id }}</p>
                                <p class="text-[11px] text-slate-500">{{ $batch->source_type === 'workshop' ? 'ورشة' : 'إرسال جماعي' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $badge = match($batch->status) {
                                        'processing' => 'bg-sky-100 text-sky-800 border-sky-200',
                                        'completed' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold border {{ $badge }}">{{ $batch->statusLabel() }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm tabular-nums">
                                <span class="text-emerald-700 font-semibold">{{ $batch->sent_count }}</span>
                                <span class="text-slate-400">/</span>
                                <span class="text-rose-600 font-semibold">{{ $batch->failed_count }}</span>
                                <span class="text-slate-400">/</span>
                                <span class="text-slate-600">{{ $batch->total_count }}</span>
                                <p class="text-[10px] text-slate-400">نجح / فشل / إجمالي</p>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600">{{ $batch->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 text-left">
                                <a href="{{ route('admin.whatsapp.batches.show', $batch) }}" class="{{ $waBtnSecondary }} !text-xs !py-1.5">
                                    <i class="fas fa-eye"></i> التفاصيل
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-500">لا توجد دفعات بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($batches->hasPages())
            <div class="px-5 py-4 border-t border-slate-200">{{ $batches->links() }}</div>
        @endif
    </section>
</div>
@endsection
