@extends('layouts.admin')

@section('title', 'قوالب الواتساب — Meta Templates')
@section('header', 'قسم الواتساب')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.whatsapp._alerts')

    @include('admin.whatsapp._page-header', [
        'title' => 'قوالب Meta',
        'subtitle' => 'إنشاء وإدارة قوالب الرسائل وإرسالها للمراجعة — بدون الدخول إلى Meta Business Manager.',
        'icon' => 'fas fa-file-alt',
        'actions' => '
            <form method="POST" action="' . route('admin.whatsapp.templates.sync') . '" class="inline">' . csrf_field() . '
                <button type="submit" class="' . $waBtnSecondary . '"><i class="fas fa-sync"></i> مزامنة مع Meta</button>
            </form>
            <a href="' . route('admin.whatsapp.templates.create') . '" class="' . $waBtnPrimary . '"><i class="fas fa-plus"></i> قالب جديد</a>
        ',
        'statCards' => [
            ['label' => 'إجمالي القوالب', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-layer-group', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600'],
            ['label' => 'معتمد', 'value' => number_format($stats['approved'] ?? 0), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
            ['label' => 'قيد المراجعة', 'value' => number_format($stats['pending'] ?? 0), 'icon' => 'fas fa-clock', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600'],
            ['label' => 'مرفوض', 'value' => number_format($stats['rejected'] ?? 0), 'icon' => 'fas fa-times-circle', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600'],
        ],
    ])

    @if(!($connectionMeta['can_send'] ?? false))
        <div class="rounded-2xl border-2 border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900">
            <i class="fas fa-exclamation-triangle ml-1"></i>
            أكمل <a href="{{ route('admin.whatsapp.settings') }}" class="font-bold underline">ربط Meta</a> (Access Token + WABA ID) قبل إرسال القوالب.
        </div>
    @endif

    <section class="{{ $waSectionClass }} p-5">
        <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
            <div>
                <h3 class="font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-user-shield text-violet-600"></i>
                    صلاحيات القوالب للموظفين
                </h3>
                <p class="text-xs text-slate-600 mt-1 max-w-2xl">
                    حدّد هل يرى موظفو المبيعات كل القوالب المعتمدة، أم قوالباً محددة تُعيَّن لكل قالب على حدة.
                </p>
            </div>
            <span class="text-[10px] px-2.5 py-1 rounded-full font-bold
                @if(($templateAccessMode ?? 'all') === 'restricted') bg-violet-100 text-violet-800 @else bg-emerald-100 text-emerald-800 @endif">
                {{ $templateAccessLabels[$templateAccessMode ?? 'all'] ?? '—' }}
            </span>
        </div>
        <form method="POST" action="{{ route('admin.whatsapp.templates.access-mode') }}" class="space-y-3">
            @csrf
            <div class="grid sm:grid-cols-2 gap-3">
                @foreach($templateAccessLabels ?? [] as $mode => $label)
                    <label class="flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition-colors
                        @if(($templateAccessMode ?? 'all') === $mode) border-violet-300 bg-violet-50/60 @else border-slate-200 hover:border-slate-300 @endif">
                        <input type="radio" name="template_access_mode" value="{{ $mode }}"
                               @checked(($templateAccessMode ?? 'all') === $mode)
                               class="mt-1 text-violet-600">
                        <span>
                            <span class="block text-sm font-bold text-slate-900">{{ $label }}</span>
                            <span class="block text-[11px] text-slate-500 mt-1">
                                @if($mode === 'all')
                                    كل موظفي المبيعات ومديري المبيعات يرون كل القوالب المعتمدة في المحادثات.
                                @else
                                    يظهر لكل موظف فقط القوالب التي تحدّدها له من صفحة كل قالب.
                                @endif
                            </span>
                        </span>
                    </label>
                @endforeach
            </div>
            <button type="submit" class="{{ $waBtnPrimary }} text-sm">
                <i class="fas fa-save"></i> حفظ إعداد الصلاحيات
            </button>
        </form>
        @if(($templateAccessMode ?? 'all') === 'restricted')
            <p class="text-[11px] text-violet-800 bg-violet-50 border border-violet-100 rounded-lg px-3 py-2 mt-3">
                <i class="fas fa-info-circle ml-1"></i>
                افتح أي قالب من الجدول أدناه وحدّد الموظفين المسموح لهم باستخدامه.
                @if(($salesStaff ?? collect())->isEmpty())
                    <strong class="block mt-1 text-amber-800">لا يوجد موظفو مبيعات نشطون حالياً.</strong>
                @endif
            </p>
        @endif
    </section>

    <section class="{{ $waSectionClass }}">
        <div class="px-5 py-4 border-b border-slate-200">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <div class="md:col-span-2">
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو المحتوى..."
                           class="{{ $waInputClass }}">
                </div>
                <select name="status" class="{{ $waSelectClass }}">
                    <option value="">كل الحالات</option>
                    @foreach(\App\Models\WhatsAppMetaTemplate::statusLabels() as $val => $lbl)
                        <option value="{{ $val }}" @selected(request('status') === $val)>{{ $lbl }}</option>
                    @endforeach
                </select>
                <select name="category" class="{{ $waSelectClass }}">
                    <option value="">كل الفئات</option>
                    @foreach(\App\Models\WhatsAppMetaTemplate::categoryLabels() as $val => $lbl)
                        <option value="{{ $val }}" @selected(request('category') === $val)>{{ $lbl }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="{{ $waBtnDark }} flex-1"><i class="fas fa-search"></i></button>
                    @if(request()->anyFilled(['search','status','category','language']))
                        <a href="{{ route('admin.whatsapp.templates.index') }}" class="{{ $waBtnSecondary }}"><i class="fas fa-times"></i></a>
                    @endif
                </div>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 border-b">
                    <tr>
                        <th class="px-5 py-3 text-right font-semibold">القالب</th>
                        <th class="px-5 py-3 text-right font-semibold">الفئة</th>
                        <th class="px-5 py-3 text-right font-semibold">الحالة</th>
                        @if(($templateAccessMode ?? 'all') === 'restricted')
                            <th class="px-5 py-3 text-right font-semibold">الموظفون</th>
                        @endif
                        <th class="px-5 py-3 text-right font-semibold">المحتوى</th>
                        <th class="px-5 py-3 text-right font-semibold">آخر مزامنة</th>
                        <th class="px-5 py-3 text-center font-semibold">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($templates as $tpl)
                        @php
                            $statusClass = match($tpl->status) {
                                'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'rejected' => 'bg-rose-100 text-rose-800 border-rose-200',
                                'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                                default => 'bg-slate-100 text-slate-600 border-slate-200',
                            };
                        @endphp
                        <tr class="hover:bg-emerald-50/30">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('admin.whatsapp.templates.show', $tpl) }}" class="font-bold text-slate-900 hover:text-emerald-700 font-mono dir-ltr text-right block">{{ $tpl->name }}</a>
                                <span class="text-xs text-slate-500">{{ $tpl->language }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-xs">{{ $tpl->categoryLabel() }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold border {{ $statusClass }}">{{ $tpl->statusLabel() }}</span>
                                @if($tpl->rejection_reason)
                                    <p class="text-[10px] text-rose-600 mt-1 max-w-xs truncate" title="{{ $tpl->rejection_reason }}">{{ Str::limit($tpl->rejection_reason, 40) }}</p>
                                @endif
                            </td>
                            @if(($templateAccessMode ?? 'all') === 'restricted')
                                <td class="px-5 py-3.5 text-xs">
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-violet-50 text-violet-800 font-semibold">
                                        <i class="fas fa-users text-[10px]"></i>
                                        {{ $tpl->assigned_users_count ?? 0 }}
                                    </span>
                                </td>
                            @endif
                            <td class="px-5 py-3.5 max-w-xs">
                                <p class="truncate text-slate-600" title="{{ $tpl->body_text }}">{{ Str::limit($tpl->body_text, 60) }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-500 whitespace-nowrap">{{ $tpl->meta_synced_at?->diffForHumans() ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-center">
                                <a href="{{ route('admin.whatsapp.templates.show', $tpl) }}" class="inline-flex w-8 h-8 items-center justify-center rounded-lg bg-slate-50 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700" title="عرض">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ ($templateAccessMode ?? 'all') === 'restricted' ? 7 : 6 }}" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center text-slate-400">
                                    <i class="fas fa-file-alt text-4xl mb-3"></i>
                                    <p class="font-semibold text-slate-600 mb-1">لا توجد قوالب بعد</p>
                                    <p class="text-sm mb-4">أنشئ قالباً جديداً أو زامن مع Meta لجلب القوالب الموجودة</p>
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.whatsapp.templates.create') }}" class="{{ $waBtnPrimary }} text-sm">قالب جديد</a>
                                        <form method="POST" action="{{ route('admin.whatsapp.templates.sync') }}">@csrf
                                            <button type="submit" class="{{ $waBtnSecondary }} text-sm">مزامنة Meta</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($templates->hasPages())
            <div class="px-5 py-4 border-t bg-slate-50/50">{{ $templates->links() }}</div>
        @endif
    </section>
</div>
@endsection
