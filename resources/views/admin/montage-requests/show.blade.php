@extends('layouts.admin')

@section('title', 'طلب فيديو #'.$montageRequest->id)
@section('header', 'طلب فيديو #'.$montageRequest->id)

@php
    use App\Models\ModeratorMontageRequest;
@endphp

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold">{{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.montage-requests.index') }}" class="text-slate-500 hover:text-slate-800"><i class="fas fa-arrow-right"></i></a>
        <h2 class="text-xl font-black text-slate-900">{{ $montageRequest->title }}</h2>
        <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-cyan-50 text-cyan-800">{{ $montageRequest->statusLabel() }}</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-xs text-slate-500">المشرف</p>
            <p class="font-bold">{{ $montageRequest->moderator->name ?? '—' }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-xs text-slate-500">محرر الفيديو</p>
            <p class="font-bold">{{ $montageRequest->montageEmployee->name ?? '—' }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-xs text-slate-500">حد التسليم</p>
            <p class="font-bold">{{ $montageRequest->deadline_at?->format('Y-m-d H:i') }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-xs text-slate-500">الأولوية</p>
            <p class="font-bold">{{ ModeratorMontageRequest::priorityLabel($montageRequest->priority) }}</p>
        </div>
    </div>

    @if($montageRequest->requirements)
        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <h3 class="font-bold mb-2">متطلبات الفيديو</h3>
            <p class="text-sm whitespace-pre-line text-slate-700">{{ $montageRequest->requirements }}</p>
        </div>
    @endif

    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden">
        <div class="px-4 py-3 border-b bg-slate-50 font-bold">التسليمات ({{ $deliverablesTimeline->count() }})</div>
        <div class="p-4 space-y-3">
            @forelse($deliverablesTimeline as $row)
                @php $d = $row['deliverable']; @endphp
                <div class="rounded-lg border border-slate-200 px-4 py-3">
                    <div class="flex flex-wrap justify-between gap-2">
                        <div>
                            <span class="text-xs font-bold px-2 py-0.5 rounded bg-slate-100">{{ $row['source_label'] }}</span>
                            <span class="font-bold mr-2">{{ $d->title }}</span>
                        </div>
                        <span class="text-xs text-slate-500">{{ $d->created_at?->format('Y-m-d H:i') }}</span>
                    </div>
                    @if($d->link_url)
                        <a href="{{ $d->link_url }}" target="_blank" class="text-sm text-cyan-700 hover:underline">فتح الرابط</a>
                    @elseif($d->file_path)
                        <a href="{{ $d->publicFileUrl() }}" target="_blank" class="text-sm text-violet-700 hover:underline">تحميل الملف</a>
                    @endif
                </div>
            @empty
                <p class="text-sm text-slate-500 text-center py-6">لا توجد تسليمات بعد.</p>
            @endforelse
        </div>
    </div>

    <div class="flex flex-wrap gap-3">
        @if($montageRequest->employeeTask)
            <a href="{{ route('admin.employee-tasks.show', $montageRequest->employeeTask) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-cyan-50 text-cyan-800 text-sm font-semibold hover:bg-cyan-100">
                <i class="fas fa-film"></i> مهمة المحرر
            </a>
        @endif
        @if($montageRequest->moderatorDeliveryTask)
            <a href="{{ route('admin.employee-tasks.show', $montageRequest->moderatorDeliveryTask) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-50 text-emerald-800 text-sm font-semibold hover:bg-emerald-100">
                <i class="fas fa-upload"></i> مهمة تسليم المشرف
            </a>
        @endif
        @if($montageRequest->isOpen())
            <form method="post" action="{{ route('admin.montage-requests.cancel', $montageRequest) }}" onsubmit="return confirm('إلغاء هذا الطلب؟');">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-50 text-rose-700 text-sm font-semibold hover:bg-rose-100">
                    <i class="fas fa-ban"></i> إلغاء الطلب
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
