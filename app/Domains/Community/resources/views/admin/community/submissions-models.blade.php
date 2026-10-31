@extends('layouts.admin')

@section('title', 'تقديمات النماذج — مراجعة ونشر')
@section('header', 'تقديمات مكتبة النماذج (Model Zoo)')

@section('content')
<div class="p-4 md:p-6 space-y-6">
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-red-100 border border-red-300 text-red-800">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-2xl shadow border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-amber-50/80">
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-brain text-amber-600"></i>
                النماذج المعلقة (من المساهمين)
            </h2>
            <p class="text-sm text-slate-600 mt-1">مراجعة النماذج والموافقة عليها للنشر في مكتبة النماذج أو الرفض.</p>
        </div>
        <div class="p-6">
            @if($pendingModels->isEmpty())
                <div class="text-center py-12 text-slate-500">
                    <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-400 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-brain text-3xl"></i>
                    </div>
                    <p>لا توجد تقديمات نماذج معلقة حالياً.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($pendingModels as $m)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-slate-900">{{ $m->title }}</h3>
                                @if($m->description)
                                    <p class="text-sm text-slate-600 mt-1 line-clamp-2">{{ Str::limit($m->description, 120) }}</p>
                                @endif
                                <p class="text-xs text-slate-500 mt-2">
                                    من: {{ $m->creator->name ?? '—' }} ({{ $m->creator->email ?? '—' }}) — {{ $m->created_at->diffForHumans() }}
                                </p>
                                @if($m->dataset)
                                    <p class="text-xs text-cyan-600 mt-1"><i class="fas fa-database ml-1"></i> مرتبط بداتاسيت: {{ Str::limit($m->dataset->title, 40) }}</p>
                                @endif
                                <div class="flex flex-wrap gap-3 mt-2">
                                    <a href="{{ route('admin.community.submissions.model.show', $m) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-amber-600 hover:text-amber-700">
                                        <i class="fas fa-eye"></i>
                                        <span>عرض النموذج والمنهجية</span>
                                    </a>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <form action="{{ route('admin.community.submissions.model.approve', $m) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition-colors">
                                        <i class="fas fa-check ml-1"></i> موافقة ونشر
                                    </button>
                                </form>
                                <form action="{{ route('admin.community.submissions.model.reject', $m) }}" method="POST" class="inline" onsubmit="return confirm('رفض هذا النموذج؟');">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700 transition-colors">
                                        <i class="fas fa-times ml-1"></i> رفض
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="flex flex-wrap gap-3">
        <a href="{{ route('admin.community.submissions.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200">
            <i class="fas fa-database"></i>
            <span>تقديمات البيانات</span>
        </a>
        <a href="{{ route('admin.community.dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-cyan-100 text-cyan-800 font-bold hover:bg-cyan-200">
            <i class="fas fa-home"></i>
            <span>لوحة المجتمع</span>
        </a>
    </div>
</div>
@endsection
