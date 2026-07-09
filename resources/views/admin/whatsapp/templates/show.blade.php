@extends('layouts.admin')

@section('title', $template->name . ' — قالب واتساب')
@section('header', 'قسم الواتساب')

@section('content')
@php
    $statusClass = match($template->status) {
        'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
        'rejected' => 'bg-rose-100 text-rose-800 border-rose-200',
        default => 'bg-slate-100 text-slate-700 border-slate-200',
    };
@endphp

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.whatsapp._alerts')

    @include('admin.whatsapp._page-header', [
        'title' => $template->name,
        'subtitle' => $template->language . ' · ' . $template->categoryLabel(),
        'icon' => 'fas fa-file-alt',
        'actions' => '
            <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-bold border ' . $statusClass . '">' . $template->statusLabel() . '</span>
            ' . ($template->isEditable() ? '<a href="' . route('admin.whatsapp.templates.edit', $template) . '" class="' . $waBtnSecondary . '"><i class="fas fa-edit"></i> تعديل</a>' : '') . '
            <a href="' . route('admin.whatsapp.templates.index') . '" class="' . $waBtnSecondary . '"><i class="fas fa-list"></i> القائمة</a>
        ',
    ])

    @if($template->displayRejectionReason())
        <div class="rounded-2xl border-2 border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-900">
            <p class="font-bold mb-1"><i class="fas fa-times-circle ml-1"></i> سبب الرفض من Meta</p>
            <p>{{ $template->displayRejectionReason() }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <section class="{{ $waSectionClass }} p-5">
                <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2"><i class="fas fa-align-right text-emerald-600"></i> معاينة المحتوى</h3>
                @if($template->header_type && $template->header_content)
                    <div class="text-sm font-bold text-slate-800 mb-2 pb-2 border-b border-slate-100">
                        @if($template->header_type === 'text')
                            {{ $template->header_content }}
                        @else
                            <span class="text-xs text-slate-500 uppercase">{{ $template->header_type }}</span>
                            <span class="block font-mono text-xs dir-ltr">{{ $template->header_content }}</span>
                        @endif
                    </div>
                @endif
                <p class="text-slate-700 whitespace-pre-wrap leading-relaxed">{{ $template->body_text }}</p>
                @if($template->footer_text)
                    <p class="text-xs text-slate-500 mt-4 pt-2 border-t">{{ $template->footer_text }}</p>
                @endif
                @if(is_array($template->buttons) && $template->buttons !== [])
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach($template->buttons as $btn)
                            @if(! is_array($btn))
                                @continue
                            @endif
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-800">
                                @if(($btn['type'] ?? '') === 'URL')<i class="fas fa-link"></i>
                                @elseif(($btn['type'] ?? '') === 'PHONE_NUMBER')<i class="fas fa-phone"></i>
                                @else<i class="fas fa-reply"></i>@endif
                                {{ $btn['text'] ?? '' }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </section>

            @if(is_array($template->components) && $template->components !== [])
                <section class="{{ $waSectionClass }}">
                    <div class="px-5 py-3 border-b font-bold text-slate-900 text-sm">JSON المُرسل لـ Meta</div>
                    <pre class="p-5 text-xs overflow-x-auto dir-ltr text-left bg-slate-50 text-slate-700 max-h-64">{{ json_encode($template->components, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE) }}</pre>
                </section>
            @endif

            @if(($templateAccessMode ?? 'all') === 'restricted')
                <section class="{{ $waSectionClass }} p-5">
                    <h3 class="font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <i class="fas fa-user-shield text-violet-600"></i>
                        الموظفون المصرّح لهم
                    </h3>
                    @if(($salesStaff ?? collect())->isEmpty())
                        <p class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                            لا يوجد موظفو مبيعات نشطون لإسناد القالب إليهم.
                        </p>
                    @else
                        <form method="POST" action="{{ route('admin.whatsapp.templates.access', $template) }}" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <div class="grid sm:grid-cols-2 gap-2 max-h-56 overflow-y-auto border border-slate-100 rounded-xl p-3 bg-slate-50/50">
                                @php $assignedIds = ($template->assignedUsers ?? collect())->pluck('id')->all(); @endphp
                                @foreach($salesStaff as $staff)
                                    <label class="flex items-center gap-2 text-sm text-slate-800 cursor-pointer rounded-lg px-2 py-1.5 hover:bg-white">
                                        <input type="checkbox" name="user_ids[]" value="{{ $staff->id }}"
                                               @checked(in_array($staff->id, $assignedIds, true))
                                               class="rounded text-violet-600">
                                        <span>{{ $staff->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <button type="submit" class="{{ $waBtnPrimary }} text-sm w-full sm:w-auto justify-center">
                                <i class="fas fa-save"></i> حفظ الصلاحيات
                            </button>
                        </form>
                    @endif
                </section>
            @endif
        </div>

        <aside class="space-y-4">
            <section class="{{ $waSectionClass }} p-5 text-sm space-y-3">
                <p><span class="text-slate-500">Meta ID:</span> <span class="font-mono text-xs dir-ltr">{{ $template->meta_template_id ?? '—' }}</span></p>
                <p><span class="text-slate-500">المتغيرات:</span> <strong>{{ $template->body_variable_count }}</strong></p>
                <p><span class="text-slate-500">أُرسل إلى Meta:</span> {{ $template->submitted_at?->format('Y-m-d H:i') ?? '—' }}</p>
                <p><span class="text-slate-500">آخر مزامنة:</span> {{ $template->meta_synced_at?->diffForHumans() ?? '—' }}</p>
                <p><span class="text-slate-500">أنشأه:</span> {{ $template->creator?->name ?? '—' }}</p>
            </section>

            <div class="space-y-2">
                @if($template->isEditable())
                    <form method="POST" action="{{ route('admin.whatsapp.templates.submit', $template) }}">@csrf
                        <button type="submit" class="{{ $waBtnPrimary }} w-full justify-center">
                            <i class="fab fa-meta"></i> إرسال إلى Meta للاعتماد
                        </button>
                    </form>
                @elseif($template->canDuplicateForResubmit())
                    <form method="POST" action="{{ route('admin.whatsapp.templates.duplicate', $template) }}">@csrf
                        <button type="submit" class="{{ $waBtnPrimary }} w-full justify-center">
                            <i class="fas fa-copy"></i> نسخ للتعديل وإعادة الإرسال
                        </button>
                    </form>
                    <p class="text-[11px] text-slate-500 leading-relaxed px-1">
                        Meta لا يسمح بتعديل القالب المعتمد مباشرة — يُنشأ نسخة مسودة جديدة للتعديل ثم الإرسال.
                    </p>
                @endif

                @if($template->status === 'pending')
                    <form method="POST" action="{{ route('admin.whatsapp.templates.sync') }}">@csrf
                        <button type="submit" class="{{ $waBtnSecondary }} w-full justify-center">
                            <i class="fas fa-sync"></i> تحديث الحالة من Meta
                        </button>
                    </form>
                @endif

                @if($template->isSendable())
                    <a href="{{ route('admin.whatsapp.inbox') }}" class="{{ $waBtnPrimary }} w-full justify-center">
                        <i class="fas fa-paper-plane"></i> استخدام في المحادثات
                    </a>
                @endif

                <form method="POST" action="{{ route('admin.whatsapp.templates.destroy', $template) }}"
                      onsubmit="return confirm('حذف هذا القالب؟');">
                    @csrf @method('DELETE')
                    <input type="hidden" name="delete_from_meta" value="1">
                    <button type="submit" class="w-full text-sm text-rose-700 border border-rose-200 rounded-xl py-2.5 hover:bg-rose-50 bg-white">
                        حذف القالب
                    </button>
                </form>
            </div>
        </aside>
    </div>
</div>
@endsection
