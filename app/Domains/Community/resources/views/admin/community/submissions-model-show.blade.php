@extends('layouts.admin')

@section('title', 'عرض تقديم نموذج')
@section('header', 'عرض تقديم نموذج')

@section('content')
<div class="p-4 md:p-6 space-y-6">
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-red-100 border border-red-300 text-red-800">{{ session('error') }}</div>
    @endif

    <div class="flex flex-wrap gap-3 mb-6">
        <a href="{{ route('admin.community.submissions.models.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 font-semibold">
            <i class="fas fa-arrow-right"></i>
            <span>العودة لقائمة التقديمات</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-xl font-black text-slate-900">{{ $model->title }}</h1>
            <div class="flex items-center gap-2">
                @if($model->status === \App\Domains\Community\Models\CommunityModel::STATUS_PENDING)
                    <form action="{{ route('admin.community.submissions.model.approve', $model) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700">
                            <i class="fas fa-check ml-1"></i> موافقة ونشر
                        </button>
                    </form>
                    <form action="{{ route('admin.community.submissions.model.reject', $model) }}" method="POST" class="inline" onsubmit="return confirm('رفض هذا النموذج؟');">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700">
                            <i class="fas fa-times ml-1"></i> رفض
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="p-6 space-y-6">
            <div>
                <h2 class="text-sm font-bold text-slate-500 mb-1">المساهم</h2>
                <p class="text-slate-800 font-semibold">{{ $model->creator->name ?? '—' }}</p>
                <p class="text-sm text-slate-500">{{ $model->creator->email ?? '—' }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ $model->created_at->format('Y-m-d H:i') }}</p>
            </div>

            @if($model->description)
                <div>
                    <h2 class="text-sm font-bold text-slate-500 mb-2">الوصف</h2>
                    <div class="text-slate-700 whitespace-pre-line rounded-xl bg-slate-50 p-4">{{ $model->description }}</div>
                </div>
            @endif

            @if($model->dataset)
                <div>
                    <h2 class="text-sm font-bold text-slate-500 mb-2">مجموعة البيانات المرتبطة</h2>
                    <p class="text-cyan-700 font-semibold">{{ $model->dataset->title }}</p>
                </div>
            @endif

            <div>
                <h2 class="text-sm font-bold text-slate-500 mb-2">شرح الخطوات والمنهجية (من المساهم)</h2>
                <div class="text-slate-700 whitespace-pre-line rounded-xl bg-amber-50/80 border border-amber-100 p-4">{{ $model->methodology_steps ?: '—' }}</div>
            </div>

            @if($model->performance_metrics && is_array($model->performance_metrics) && count($model->performance_metrics) > 0)
                <div>
                    <h2 class="text-sm font-bold text-slate-500 mb-2">مقاييس الأداء</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($model->performance_metrics as $key => $value)
                            <span class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-800 font-bold text-sm">{{ $key }}: {{ is_numeric($value) ? number_format((float)$value, 4) : $value }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($model->license)
                <div>
                    <h2 class="text-sm font-bold text-slate-500 mb-1">الترخيص</h2>
                    <p class="text-slate-800 font-semibold">{{ $model->license }}</p>
                </div>
            @endif

            @if($model->usage_instructions)
                <div>
                    <h2 class="text-sm font-bold text-slate-500 mb-2">طريقة الاستخدام أو الاستدعاء</h2>
                    <pre class="text-sm text-slate-700 bg-slate-900 text-slate-100 p-4 rounded-xl overflow-x-auto whitespace-pre-wrap font-mono">{{ $model->usage_instructions }}</pre>
                </div>
            @endif

            @php $filesList = $model->files_list; @endphp
            @if(!empty($filesList))
                <div>
                    <h2 class="text-sm font-bold text-slate-500 mb-2">ملفات النموذج</h2>
                    <ul class="space-y-2">
                        @foreach($filesList as $idx => $file)
                            @php
                                $path = is_array($file) ? ($file['path'] ?? null) : null;
                                $name = is_array($file) ? ($file['original_name'] ?? basename($path)) : basename($path);
                                $size = is_array($file) ? ($file['size'] ?? '') : '';
                            @endphp
                            <li class="flex items-center justify-between gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="font-mono text-sm text-slate-800 truncate">{{ $name }}</span>
                                @if($size)<span class="text-xs text-slate-500">{{ $size }}</span>@endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
