@extends('layouts.admin')

@section('title', 'إرسال دعوة جروب من Excel')
@section('header', 'قسم الواتساب')

@section('content')
@php
    $isConnected = (bool) ($connectionMeta['can_send'] ?? false);
    $preview = session('excel_preview');
@endphp

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.whatsapp._alerts')

    @include('admin.whatsapp._page-header', [
        'title' => 'إرسال دعوة جروب واتساب من Excel',
        'subtitle' => 'ارفع الأرقام → رتّبها وصحّحها → أنشئ قالب الدعوة لـ Meta → أرسل دفعة تظهر في المحادثات',
        'icon' => 'fas fa-file-excel',
        'actions' => '
            <a href="' . route('admin.whatsapp.excel-campaign.sample') . '" class="' . $waBtnSecondary . '"><i class="fas fa-download"></i> نموذج Excel</a>
            <a href="' . route('admin.whatsapp.batches.index') . '" class="' . $waBtnSecondary . '"><i class="fas fa-layer-group"></i> دفعات الإرسال</a>
            <a href="' . route('admin.whatsapp.inbox') . '" class="' . $waBtnSecondary . '"><i class="fas fa-inbox"></i> المحادثات</a>
        ',
    ])

    @if(! $isOfficial || ! $isConnected)
        <div class="rounded-2xl border-2 border-amber-300 bg-amber-50 p-5 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-amber-900">الواتساب غير جاهز للإرسال</h3>
                <p class="text-sm text-amber-800 mt-1">أكمل ربط Meta Cloud API قبل إنشاء القالب أو الإرسال الجماعي.</p>
            </div>
            <a href="{{ route('admin.whatsapp.settings') }}" class="{{ $waBtnPrimary }}">إعدادات الربط</a>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <section class="{{ $waSectionClass }} xl:col-span-2">
            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
                <h3 class="font-bold text-slate-900 flex items-center gap-2"><i class="fas fa-users text-emerald-600"></i> بيانات الدعوة والجروب</h3>
            </div>
            <div class="p-5 space-y-5">
                <form method="POST" action="{{ route('admin.whatsapp.excel-campaign.template') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">اسم الجروب <span class="text-rose-500">*</span></label>
                            <input type="text" name="group_name" value="{{ old('group_name') }}" required maxlength="120"
                                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm" placeholder="مثال: جروب دفعة مارس">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">لينك جروب واتساب <span class="text-rose-500">*</span></label>
                            <input type="url" name="group_link" value="{{ old('group_link') }}" required maxlength="500"
                                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm" dir="ltr" placeholder="https://chat.whatsapp.com/XXXX">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">تسمية القالب (للعرض)</label>
                            <input type="text" name="display_name" value="{{ old('display_name') }}" maxlength="255"
                                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm" placeholder="مثال: دعوة جروب دفعة مارس">
                            <p class="text-xs text-slate-500 mt-1">اسم واضح للموظفين — يُملأ تلقائياً من اسم الجروب إن تركته فارغاً.</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">اسم القالب في Meta (اختياري)</label>
                            <input type="text" name="template_name" value="{{ old('template_name') }}" maxlength="512"
                                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono" dir="ltr" placeholder="group_invite_....">
                            <p class="text-xs text-slate-500 mt-1">حروف إنجليزية صغيرة وأرقام و _ فقط. يُولَّد تلقائياً إن تركته فارغاً.</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">نص قالب الدعوة</label>
                            <textarea name="body_text" rows="8" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm" placeholder="نص القالب">{{ old('body_text', $defaultBody) }}</textarea>
                            <div class="mt-2 flex flex-wrap gap-2 text-[11px]">
                                @foreach($variableLabels as $k => $label)
                                    <span class="inline-flex px-2 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100">@php echo '{{'.$k.'}}'; @endphp = {{ $label }}</span>
                                @endforeach
                            </div>
                            <p class="text-xs text-amber-700 mt-2">مهم: ضع كود الدعوة فقط داخل المتغير بعد <code dir="ltr">chat.whatsapp.com/</code> — Meta ترفض الرابط الكامل داخل المثال.</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 pt-2 border-t border-slate-200">
                        <button type="submit" name="submit_now" value="1" class="{{ $waBtnPrimary }}" {{ (! $isConnected) ? 'disabled' : '' }}>
                            <i class="fas fa-paper-plane"></i> إنشاء القالب وإرساله لـ Meta
                        </button>
                        <button type="submit" name="submit_now" value="0" class="{{ $waBtnSecondary }}" {{ (! $isConnected) ? 'disabled' : '' }}>
                            حفظ كمسودة فقط
                        </button>
                    </div>
                </form>
                <form method="POST" action="{{ route('admin.whatsapp.excel-campaign.sync') }}" class="pt-2">
                    @csrf
                    <button type="submit" class="{{ $waBtnSecondary }}"><i class="fas fa-sync"></i> مزامنة حالة القوالب</button>
                </form>
            </div>
        </section>

        <section class="{{ $waSectionClass }}">
            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
                <h3 class="font-bold text-slate-900">قوالب الدعوة الأخيرة</h3>
            </div>
            <div class="p-4 space-y-3 max-h-[28rem] overflow-y-auto">
                @forelse($recentDrafts as $tpl)
                    <div class="rounded-xl border border-slate-200 p-3 text-sm">
                        <div class="font-semibold text-slate-800 truncate">{{ $tpl->displayTitle() }}</div>
                        <div class="text-[11px] text-slate-500 font-mono truncate" dir="ltr">{{ $tpl->name }}</div>
                        <div class="mt-1 flex items-center justify-between gap-2">
                            <span class="text-xs px-2 py-0.5 rounded-full
                                {{ $tpl->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($tpl->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600') }}">
                                {{ $tpl->statusLabel() }}
                            </span>
                            <span class="text-[11px] text-slate-400">{{ $tpl->updated_at?->diffForHumans() }}</span>
                        </div>
                        @if($tpl->rejection_reason)
                            <p class="text-xs text-rose-600 mt-2">{{ $tpl->rejection_reason }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-500 text-center py-8">لا توجد قوالب دعوة بعد.</p>
                @endforelse
            </div>
        </section>
    </div>

    <section class="{{ $waSectionClass }}">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="font-bold text-slate-900 flex items-center gap-2"><i class="fas fa-file-excel text-emerald-600"></i> ملف الأرقام والإرسال</h3>
            <p class="text-xs text-slate-500">الأعمدة: الاسم (اختياري) + الهاتف (إلزامي). الأرقام تُطبَّع لصيغة مصر الدولية تلقائياً.</p>
        </div>
        <div class="p-5 space-y-5">
            <form method="POST" action="{{ route('admin.whatsapp.excel-campaign.preview') }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-3 items-end">
                @csrf
                <div class="flex-1 w-full">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">رفع Excel للمعاينة والترتيب</label>
                    <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required
                           class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm bg-white">
                </div>
                <button type="submit" class="{{ $waBtnSecondary }}"><i class="fas fa-list-check"></i> معاينة الأرقام</button>
            </form>

            @if($preview)
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/40 p-4">
                    <div class="flex flex-wrap gap-3 text-sm mb-3">
                        <span class="font-semibold text-emerald-800">صالح: {{ number_format($preview['valid_count'] ?? 0) }}</span>
                        <span class="text-amber-700">متخطى: {{ number_format($preview['skipped_count'] ?? 0) }}</span>
                        <span class="text-slate-500">صفوف: {{ number_format($preview['total_rows'] ?? 0) }}</span>
                        <span class="text-slate-400" dir="ltr">{{ $preview['file_name'] ?? '' }}</span>
                    </div>
                    <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-slate-600">
                                <tr>
                                    <th class="px-3 py-2 text-right">الاسم</th>
                                    <th class="px-3 py-2 text-right">الرقم الأصلي</th>
                                    <th class="px-3 py-2 text-right">بعد التطبيع</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach(($preview['valid'] ?? []) as $row)
                                    <tr>
                                        <td class="px-3 py-2">{{ $row['name'] ?? '—' }}</td>
                                        <td class="px-3 py-2 font-mono text-xs" dir="ltr">{{ $row['raw_phone'] ?? '—' }}</td>
                                        <td class="px-3 py-2 font-mono text-xs text-emerald-700" dir="ltr">{{ $row['phone'] ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(! empty($preview['skipped']))
                        <details class="mt-3 text-xs text-slate-600">
                            <summary class="cursor-pointer font-semibold">عرض المتخطّى (عينة)</summary>
                            <ul class="mt-2 space-y-1 list-disc pr-5">
                                @foreach($preview['skipped'] as $skip)
                                    <li>سطر {{ $skip['row'] ?? '?' }}: {{ $skip['reason'] ?? '' }} @if(!empty($skip['value'])) — <span dir="ltr">{{ $skip['value'] }}</span>@endif</li>
                                @endforeach
                            </ul>
                        </details>
                    @endif
                </div>
            @endif

            <form method="POST" action="{{ route('admin.whatsapp.excel-campaign.send') }}" enctype="multipart/form-data" class="space-y-4 border-t border-slate-200 pt-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">ملف Excel للإرسال <span class="text-rose-500">*</span></label>
                        <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">القالب المعتمد <span class="text-rose-500">*</span></label>
                        <select name="template_id" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm">
                            <option value="">اختر قالباً معتمداً من Meta</option>
                            @foreach($approvedTemplates as $tpl)
                                <option value="{{ $tpl->id }}" @selected((string) old('template_id', session('created_template_id')) === (string) $tpl->id)>
                                    {{ $tpl->displayLabel() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">اسم الجروب <span class="text-rose-500">*</span></label>
                        <input type="text" name="group_name" value="{{ old('group_name') }}" required maxlength="120"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">لينك الجروب <span class="text-rose-500">*</span></label>
                        <input type="url" name="group_link" value="{{ old('group_link') }}" required maxlength="500"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm" dir="ltr">
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="{{ $waBtnPrimary }}" {{ (! $isConnected) ? 'disabled' : '' }}
                            onclick="return confirm('بدء إرسال دعوات الجروب للمستلمين في الملف؟')">
                        <i class="fas fa-paper-plane"></i> إرسال والتحويل لدفعات الإرسال
                    </button>
                    <a href="{{ route('admin.whatsapp.templates.index') }}" class="{{ $waBtnSecondary }}">إدارة كل القوالب</a>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
