@extends('layouts.admin')

@section('title', 'هيكل قسم الميديا')
@section('header', 'قسم الميديا — الهرم الوظيفي')

@section('content')
<div class="space-y-8">
    <section class="rounded-2xl border border-slate-200 bg-white shadow-lg overflow-hidden">
        <div class="px-5 py-5 bg-gradient-to-l from-slate-900 via-slate-800 to-cyan-900 text-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-cyan-200/90 mb-1">Media Department</p>
                    <h2 class="text-2xl font-black tracking-tight">الهرم الوظيفي — قسم الميديا</h2>
                    <p class="text-sm text-slate-300 mt-1 max-w-2xl">
                        مشرف المحتوى في القمة، وتحته مسار المصمم ومسار محرر الفيديو.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.design-task-cycles.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-white/10 hover:bg-white/20 px-3 py-2 text-xs font-semibold border border-white/15">
                        <i class="fas fa-palette"></i> دورات التصميم
                    </a>
                    <a href="{{ route('admin.montage-requests.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-white/10 hover:bg-white/20 px-3 py-2 text-xs font-semibold border border-white/15">
                        <i class="fas fa-film"></i> طلبات الفيديو
                    </a>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 p-4">
            @foreach([
                ['label' => 'مشرفو محتوى', 'value' => $stats['moderators'], 'tone' => 'text-fuchsia-700 bg-fuchsia-50 border-fuchsia-100'],
                ['label' => 'مصممون', 'value' => $stats['designers'], 'tone' => 'text-violet-700 bg-violet-50 border-violet-100'],
                ['label' => 'محررو فيديو', 'value' => $stats['editors'], 'tone' => 'text-cyan-700 bg-cyan-50 border-cyan-100'],
                ['label' => 'طلبات تصميم مفتوحة', 'value' => $stats['open_design'], 'tone' => 'text-amber-700 bg-amber-50 border-amber-100'],
                ['label' => 'طلبات فيديو مفتوحة', 'value' => $stats['open_montage'], 'tone' => 'text-sky-700 bg-sky-50 border-sky-100'],
            ] as $card)
                <div class="rounded-xl border p-3 {{ $card['tone'] }}">
                    <p class="text-[11px] font-semibold opacity-80">{{ $card['label'] }}</p>
                    <p class="text-2xl font-black tabular-nums mt-0.5">{{ number_format($card['value']) }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- الهرم البصري --}}
    <section class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 sm:p-8 overflow-x-auto">
        <div class="min-w-[720px] max-w-5xl mx-auto">
            {{-- المستوى 0: القسم --}}
            <div class="flex justify-center mb-2">
                <div class="rounded-2xl bg-slate-900 text-white px-8 py-4 shadow-xl text-center border border-slate-700">
                    <div class="text-2xl mb-1"><i class="fas fa-photo-video text-cyan-300"></i></div>
                    <p class="text-lg font-black">قسم الميديا</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Content · Design · Video</p>
                </div>
            </div>

            <div class="flex justify-center">
                <div class="w-px h-8 bg-slate-300"></div>
            </div>

            {{-- المستوى 1: مشرف المحتوى --}}
            <div class="flex justify-center mb-2">
                <div class="rounded-2xl border-2 border-fuchsia-300 bg-white px-6 py-4 shadow-md text-center min-w-[240px]">
                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-fuchsia-100 text-fuchsia-700 mb-2">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <p class="text-base font-black text-slate-900">مشرف المحتوى</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">ينزل تاسكات للمصمم ومحرر الفيديو</p>
                    <p class="mt-2 text-xs font-bold text-fuchsia-700">{{ $moderators->count() }} نشط</p>
                </div>
            </div>

            <div class="flex justify-center">
                <div class="w-px h-6 bg-slate-300"></div>
            </div>

            {{-- خط التفرع الأفقي --}}
            <div class="relative mx-auto" style="width: 70%; max-width: 520px;">
                <div class="h-px bg-slate-300 w-full"></div>
                <div class="absolute top-0 right-0 w-px h-6 bg-slate-300"></div>
                <div class="absolute top-0 left-0 w-px h-6 bg-slate-300"></div>
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-px h-0"></div>
            </div>

            <div class="grid grid-cols-2 gap-6 mt-6">
                {{-- فرع المصمم --}}
                <div class="flex flex-col items-center">
                    <div class="w-px h-4 bg-slate-300 mb-0"></div>
                    <div class="rounded-2xl border-2 border-violet-300 bg-white px-5 py-4 shadow-md text-center w-full max-w-xs">
                        <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-violet-100 text-violet-700 mb-2">
                            <i class="fas fa-palette"></i>
                        </div>
                        <p class="text-base font-black text-slate-900">المصمم</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">تنفيذ التصاميم والتسليم</p>
                        <p class="mt-2 text-xs font-bold text-violet-700">{{ $designers->count() }} نشط</p>
                    </div>
                </div>

                {{-- فرع محرر الفيديو --}}
                <div class="flex flex-col items-center">
                    <div class="w-px h-4 bg-slate-300 mb-0"></div>
                    <div class="rounded-2xl border-2 border-cyan-300 bg-white px-5 py-4 shadow-md text-center w-full max-w-xs">
                        <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-cyan-100 text-cyan-700 mb-2">
                            <i class="fas fa-film"></i>
                        </div>
                        <p class="text-base font-black text-slate-900">محرر الفيديو</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">المونتاج والتسليم بالمواعيد</p>
                        <p class="mt-2 text-xs font-bold text-cyan-700">{{ $editors->count() }} نشط</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- تفاصيل الأشخاص --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
        {{-- مشرفو المحتوى --}}
        <section class="rounded-2xl border border-fuchsia-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-fuchsia-100 bg-fuchsia-50/80 flex items-center justify-between">
                <h3 class="font-black text-fuchsia-950 flex items-center gap-2">
                    <i class="fas fa-user-tie text-fuchsia-600"></i>
                    مشرفو المحتوى
                </h3>
                <span class="text-xs font-bold text-fuchsia-700">{{ $moderators->count() }}</span>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($moderators as $user)
                    <div class="p-4">
                        <p class="font-bold text-slate-900">{{ $user->name }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $user->email }}</p>
                        <div class="mt-2 flex flex-wrap gap-2 text-[11px] font-semibold">
                            <span class="rounded-lg bg-violet-50 text-violet-800 px-2 py-0.5 border border-violet-100">
                                تصميم مفتوح: {{ (int) ($openDesignByModerator[$user->id] ?? 0) }}
                            </span>
                            <span class="rounded-lg bg-cyan-50 text-cyan-800 px-2 py-0.5 border border-cyan-100">
                                فيديو مفتوح: {{ (int) ($openMontageByModerator[$user->id] ?? 0) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="p-6 text-sm text-slate-500 text-center">لا يوجد مشرف محتوى نشط. عيّن وظيفة <code class="text-xs bg-slate-100 px-1 rounded">moderator</code>.</p>
                @endforelse
            </div>
        </section>

        {{-- المصممون --}}
        <section class="rounded-2xl border border-violet-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-violet-100 bg-violet-50/80 flex items-center justify-between">
                <h3 class="font-black text-violet-950 flex items-center gap-2">
                    <i class="fas fa-palette text-violet-600"></i>
                    المصممون
                </h3>
                <span class="text-xs font-bold text-violet-700">{{ $designers->count() }}</span>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($designers as $user)
                    <div class="p-4">
                        <p class="font-bold text-slate-900">{{ $user->name }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $user->email }}</p>
                        <div class="mt-2">
                            <span class="rounded-lg bg-violet-50 text-violet-800 px-2 py-0.5 border border-violet-100 text-[11px] font-semibold">
                                طلبات مفتوحة: {{ (int) ($openDesignByDesigner[$user->id] ?? 0) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="p-6 text-sm text-slate-500 text-center">لا يوجد مصمم نشط. عيّن وظيفة <code class="text-xs bg-slate-100 px-1 rounded">designer</code>.</p>
                @endforelse
            </div>
        </section>

        {{-- محررو الفيديو --}}
        <section class="rounded-2xl border border-cyan-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-cyan-100 bg-cyan-50/80 flex items-center justify-between">
                <h3 class="font-black text-cyan-950 flex items-center gap-2">
                    <i class="fas fa-film text-cyan-600"></i>
                    محررو الفيديو
                </h3>
                <span class="text-xs font-bold text-cyan-700">{{ $editors->count() }}</span>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($editors as $user)
                    <div class="p-4">
                        <p class="font-bold text-slate-900">{{ $user->name }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $user->email }}</p>
                        <div class="mt-2">
                            <span class="rounded-lg bg-cyan-50 text-cyan-800 px-2 py-0.5 border border-cyan-100 text-[11px] font-semibold">
                                طلبات مفتوحة: {{ (int) ($openMontageByEditor[$user->id] ?? 0) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="p-6 text-sm text-slate-500 text-center">لا يوجد محرر فيديو نشط. عيّن وظيفة <code class="text-xs bg-slate-100 px-1 rounded">video_editing</code>.</p>
                @endforelse
            </div>
        </section>
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 text-sm text-slate-600">
        <p class="font-bold text-slate-900 mb-2">كيف يعمل الهرم؟</p>
        <ul class="list-disc list-inside space-y-1 text-xs leading-relaxed">
            <li><strong>مشرف المحتوى</strong> ينشئ طلب تصميم للمصمم وطلب فيديو لمحرر الفيديو بمواعيد وتسليمات.</li>
            <li><strong>المصمم / محرر الفيديو</strong> يستلم المهمة في «مهامي» ويرفع التسليمات.</li>
            <li>بعد التسليم، المشرف يراجع ثم ينشئ <strong>التسليم النهائي</strong> إن لزم.</li>
        </ul>
    </section>
</div>
@endsection
