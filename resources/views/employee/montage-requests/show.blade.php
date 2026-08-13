@extends('layouts.employee')

@section('title', 'طلب مونتاج: ' . $montageRequest->title)
@section('header', 'تفاصيل طلب المونتاج')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-800 text-sm font-semibold">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-rose-800 text-sm font-semibold">{{ session('error') }}</div>
    @endif

    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <a href="{{ route('employee.montage-requests.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-cyan-700 hover:underline mb-2">
                <i class="fas fa-arrow-right text-xs"></i>
                كل طلبات المونتاج
            </a>
            <h1 class="text-2xl font-black text-slate-900">{{ $montageRequest->title }}</h1>
            <p class="text-sm text-slate-500 mt-1">الحالة: <strong>{{ $montageRequest->statusLabel() }}</strong></p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if($montageRequest->isOpen())
                <form method="post" action="{{ route('employee.montage-requests.cancel', $montageRequest) }}" onsubmit="return confirm('إلغاء طلب المونتاج؟')">
                    @csrf
                    <button class="inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-semibold text-rose-700 hover:bg-rose-100">
                        <i class="fas fa-ban"></i>
                        إلغاء الطلب
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 mb-1">موظف المونتاج</div>
            <div class="font-bold text-slate-900">{{ $montageRequest->montageEmployee->name ?? '—' }}</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 mb-1">حد التسليم</div>
            <div class="font-bold {{ $montageRequest->deadline_at && $montageRequest->deadline_at->isPast() && $montageRequest->isOpen() ? 'text-rose-600' : 'text-slate-900' }}">
                {{ $montageRequest->deadline_at?->format('Y-m-d H:i') }}
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-xs font-semibold text-slate-500 mb-1">الأولوية</div>
            <div class="font-bold text-slate-900">{{ $montageRequest->priority }}</div>
        </div>
    </div>

    @if($montageRequest->description)
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="font-bold text-slate-900 mb-2">الوصف</h2>
            <p class="text-sm text-slate-600 whitespace-pre-line">{{ $montageRequest->description }}</p>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="font-bold text-slate-900 mb-2">متطلبات الفيديو</h2>
        <p class="text-sm text-slate-600 whitespace-pre-line leading-relaxed">{{ $montageRequest->requirements }}</p>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/80 flex items-center justify-between">
            <h2 class="font-bold text-slate-900">تسليمات موظف المونتاج</h2>
            <span class="text-xs font-semibold text-slate-500">{{ $montageRequest->employeeTask?->deliverables?->count() ?? 0 }} تسليم</span>
        </div>
        <div class="p-5 space-y-3">
            @forelse(($montageRequest->employeeTask?->deliverables ?? collect()) as $deliverable)
                <div class="rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-3">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div>
                            <div class="font-bold text-slate-900">{{ $deliverable->title }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">{{ $deliverable->submitted_at?->format('Y-m-d H:i') }} · {{ $deliverable->delivery_type }}</div>
                        </div>
                        @if($deliverable->delivery_type === 'link' && $deliverable->link_url)
                            <a href="{{ $deliverable->link_url }}" target="_blank" class="text-sm font-semibold text-cyan-700 hover:underline">
                                <i class="fas fa-external-link-alt ml-1"></i>فتح الرابط
                            </a>
                        @elseif($deliverable->file_path)
                            <span class="text-xs font-semibold text-slate-600">{{ $deliverable->file_name ?: 'ملف مرفوع' }}</span>
                        @endif
                    </div>
                    @if($deliverable->description)
                        <p class="text-sm text-slate-600 mt-2 whitespace-pre-line">{{ $deliverable->description }}</p>
                    @endif
                </div>
            @empty
                <p class="text-sm text-slate-500 text-center py-6">لم يُسلَّم فيديو بعد.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
