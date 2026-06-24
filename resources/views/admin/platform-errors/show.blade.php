@extends('layouts.admin')

@section('title', 'تفاصيل الخطأ #'.$platformError->id)
@section('header', 'تفاصيل الخطأ')

@section('content')
@php
    $statusClass = match($platformError->status) {
        'resolved' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'acknowledged' => 'bg-sky-100 text-sky-800 border-sky-200',
        default => 'bg-rose-100 text-rose-800 border-rose-200',
    };
@endphp

<div class="space-y-6 max-w-6xl mx-auto">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.platform-errors.index', request()->only(['user_id'])) }}" class="text-sm text-slate-600 hover:text-rose-600">
            <i class="fas fa-arrow-right ml-1"></i> العودة لسجل الأخطاء
        </a>
        @if($platformError->user_id)
            <a href="{{ route('admin.platform-errors.index', ['user_id' => $platformError->user_id]) }}"
               class="text-sm font-semibold text-sky-700 hover:underline">
                <i class="fas fa-user ml-1"></i> كل أخطاء {{ $platformError->user?->name }}
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 font-medium">{{ session('success') }}</div>
    @endif

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 sm:px-8 py-6 border-b border-slate-200 flex flex-wrap items-start justify-between gap-4">
            <div class="space-y-2 min-w-0 flex-1">
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold border {{ $statusClass }}">
                        {{ \App\Models\PlatformErrorLog::statusLabel($platformError->status) }}
                    </span>
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-800">
                        {{ \App\Models\PlatformErrorLog::levelLabel($platformError->level) }}
                    </span>
                    <span class="text-xs text-slate-500 tabular-nums">{{ $platformError->created_at->format('Y-m-d H:i:s') }}</span>
                </div>
                <h2 class="text-xl font-black text-slate-900 break-words">{{ $platformError->message }}</h2>
                @if($platformError->exception_class)
                    <p class="text-sm font-mono text-slate-600 break-all">{{ $platformError->exception_class }}</p>
                @endif
                @if($platformError->shortLocation())
                    <p class="text-xs font-mono text-rose-700 bg-rose-50 inline-block px-2 py-1 rounded">{{ $platformError->shortLocation() }}</p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-0 lg:divide-x lg:divide-x-reverse divide-slate-200">
            <div class="lg:col-span-2 p-5 sm:p-8 space-y-6">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 mb-2">سياق الطلب</h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                        <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
                            <dt class="text-xs text-slate-500 mb-1">المستخدم</dt>
                            <dd class="font-semibold text-slate-900">{{ $platformError->user?->name ?? 'زائر' }}</dd>
                            @if($platformError->user?->email)<dd class="text-xs text-slate-500">{{ $platformError->user->email }}</dd>@endif
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
                            <dt class="text-xs text-slate-500 mb-1">IP</dt>
                            <dd class="font-mono text-slate-800">{{ $platformError->ip_address ?? '—' }}</dd>
                        </div>
                        <div class="sm:col-span-2 rounded-xl bg-slate-50 p-3 border border-slate-100">
                            <dt class="text-xs text-slate-500 mb-1">الرابط</dt>
                            <dd class="font-mono text-xs text-slate-800 break-all">{{ $platformError->url ?? '—' }}</dd>
                            @if($platformError->method)<dd class="text-xs text-slate-500 mt-1">Method: <strong>{{ $platformError->method }}</strong></dd>@endif
                        </div>
                    </dl>
                </div>

                @if($platformError->request_input)
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 mb-2">مدخلات الطلب (مُنقّاة)</h3>
                        <pre class="text-xs bg-slate-900 text-emerald-100 rounded-xl p-4 overflow-x-auto max-h-64">{{ json_encode($platformError->request_input, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                @endif

                @if($platformError->context)
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 mb-2">سياق إضافي</h3>
                        <pre class="text-xs bg-slate-900 text-sky-100 rounded-xl p-4 overflow-x-auto max-h-64">{{ json_encode($platformError->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                @endif

                @if($platformError->trace)
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 mb-2">Stack Trace</h3>
                        <pre class="text-[11px] leading-relaxed bg-slate-950 text-slate-200 rounded-xl p-4 overflow-x-auto max-h-[480px] whitespace-pre-wrap font-mono">{{ $platformError->trace }}</pre>
                    </div>
                @endif
            </div>

            <aside class="p-5 sm:p-6 space-y-5 bg-slate-50/50">
                <form method="POST" action="{{ route('admin.platform-errors.update-status', $platformError) }}" class="space-y-3 rounded-xl border border-slate-200 bg-white p-4">
                    @csrf
                    @method('PATCH')
                    <h3 class="text-sm font-bold text-slate-900">تحديث الحالة</h3>
                    <select name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm">
                        @foreach(\App\Models\PlatformErrorLog::STATUSES as $k => $label)
                            <option value="{{ $k }}" @selected($platformError->status === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <textarea name="admin_notes" rows="4" placeholder="ملاحظات المعالجة…" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm">{{ old('admin_notes', $platformError->admin_notes) }}</textarea>
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold">حفظ</button>
                    @if($platformError->resolved_at)
                        <p class="text-[11px] text-emerald-700">حُلّ في {{ $platformError->resolved_at->format('Y-m-d H:i') }}@if($platformError->resolver) — {{ $platformError->resolver->name }}@endif</p>
                    @endif
                </form>

                @if($similar->isNotEmpty())
                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <h3 class="text-sm font-bold text-slate-900 mb-2">نفس الخطأ ({{ $similar->count() }})</h3>
                        <ul class="space-y-2 max-h-48 overflow-y-auto text-xs">
                            @foreach($similar as $s)
                                <li>
                                    <a href="{{ route('admin.platform-errors.show', $s) }}" class="text-sky-700 hover:underline">
                                        #{{ $s->id }} — {{ $s->created_at->format('m-d H:i') }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($sameUserRecent->isNotEmpty())
                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <h3 class="text-sm font-bold text-slate-900 mb-2">أخطاء أخرى لنفس المستخدم</h3>
                        <ul class="space-y-2 max-h-48 overflow-y-auto text-xs">
                            @foreach($sameUserRecent as $s)
                                <li class="flex items-start justify-between gap-2">
                                    <a href="{{ route('admin.platform-errors.show', $s) }}" class="text-sky-700 hover:underline truncate flex-1">
                                        {{ \Illuminate\Support\Str::limit($s->message, 40) }}
                                    </a>
                                    <span class="text-slate-400 shrink-0">{{ $s->created_at->format('m-d') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </aside>
        </div>
    </section>
</div>
@endsection
