@extends('layouts.admin')

@section('title', 'تقديمات المجتمع - مراجعة ونشر')
@section('header', 'تقديمات المساهمين (مراجعة والموافقة)')

@section('content')
<div class="p-4 md:p-6 space-y-6">
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-red-100 border border-red-300 text-red-800">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-2xl shadow border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h2 class="text-lg font-bold text-slate-800">مجموعات البيانات المعلقة (من المساهمين)</h2>
            <p class="text-sm text-slate-600 mt-1">مراجعة البيانات والموافقة عليها للنشر أو الرفض.</p>
        </div>
        <div class="p-6">
            @if($pendingDatasets->isEmpty())
                <div class="text-center py-12 text-slate-500">
                    <i class="fas fa-inbox text-4xl mb-4"></i>
                    <p>لا توجد تقديمات معلقة حالياً.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($pendingDatasets as $dataset)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-slate-900">{{ $dataset->title }}</h3>
                                @if($dataset->description)
                                    <p class="text-sm text-slate-600 mt-1 line-clamp-2">{{ Str::limit($dataset->description, 120) }}</p>
                                @endif
                                <p class="text-xs text-slate-500 mt-2">
                                    من: {{ $dataset->creator->name ?? '—' }} ({{ $dataset->creator->email ?? '—' }}) — {{ $dataset->created_at->diffForHumans() }}
                                </p>
                                <div class="flex flex-wrap gap-3 mt-2">
                                    <a href="{{ route('admin.community.submissions.dataset.show', $dataset) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-cyan-600 hover:text-cyan-700">
                                        <i class="fas fa-eye"></i>
                                        <span>عرض البيانات</span>
                                    </a>
                                    @if($dataset->file_path)
                                        <a href="{{ route('admin.community.submissions.dataset.download', $dataset) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-600 hover:text-slate-800">
                                            <i class="fas fa-download"></i>
                                            <span>تحميل الملف</span>
                                        </a>
                                    @endif
                                    @if($dataset->file_url)
                                        <a href="{{ $dataset->file_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-700">
                                            <i class="fas fa-external-link-alt"></i>
                                            <span>رابط التحميل</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <form action="{{ route('admin.community.submissions.dataset.approve', $dataset) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition-colors">
                                        <i class="fas fa-check ml-1"></i> موافقة ونشر
                                    </button>
                                </form>
                                <form action="{{ route('admin.community.submissions.dataset.reject', $dataset) }}" method="POST" class="inline">
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
</div>
@endsection
