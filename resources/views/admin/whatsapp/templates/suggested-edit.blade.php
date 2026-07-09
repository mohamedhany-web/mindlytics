@extends('layouts.admin')

@section('title', 'تعديل قالب مقترح — ' . $suggested->title)
@section('header', 'قسم الواتساب')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.whatsapp._alerts')

    @include('admin.whatsapp._page-header', [
        'title' => 'تعديل: ' . $suggested->title,
        'subtitle' => $suggested->categoryLabel() . ' · ' . strtoupper($suggested->language),
        'icon' => 'fas fa-wand-magic-sparkles',
        'actions' => '
            <a href="' . route('admin.whatsapp.templates.index', ['tab' => 'suggested']) . '" class="' . $waBtnSecondary . '"><i class="fas fa-arrow-right"></i> المكتبة</a>
        ',
    ])

    @if($suggested->metaTemplate)
        <div class="rounded-2xl border border-sky-200 bg-sky-50 px-5 py-4 text-sm text-sky-900 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="font-bold">مسودة Meta مرتبطة</p>
                <p class="text-xs mt-1 font-mono dir-ltr">{{ $suggested->metaTemplate->name }} · {{ $suggested->metaTemplate->statusLabel() }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.whatsapp.templates.show', $suggested->metaTemplate) }}" class="{{ $waBtnSecondary }} text-xs">عرض Meta</a>
                @if($suggested->metaTemplate->isEditable())
                    <a href="{{ route('admin.whatsapp.templates.edit', $suggested->metaTemplate) }}" class="{{ $waBtnSecondary }} text-xs">تعديل Meta</a>
                @endif
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.whatsapp.templates.suggested.update', $suggested) }}">
        @csrf
        @method('PUT')
        <section class="{{ $waSectionClass }} p-5 sm:p-6 space-y-5">
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="{{ $waLabelClass }}">العنوان</label>
                    <input type="text" name="title" value="{{ old('title', $suggested->title) }}" required class="{{ $waInputClass }}">
                </div>
                <div>
                    <label class="{{ $waLabelClass }}">التصنيف</label>
                    <select name="category" class="{{ $waSelectClass }}">
                        @foreach(\App\Models\WhatsAppSuggestedTemplate::categoryLabels() as $val => $lbl)
                            <option value="{{ $val }}" @selected(old('category', $suggested->category) === $val)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $waLabelClass }}">اللغة</label>
                    <select name="language" class="{{ $waSelectClass }}">
                        <option value="ar" @selected(old('language', $suggested->language) === 'ar')>العربية</option>
                        <option value="en" @selected(old('language', $suggested->language) === 'en')>English</option>
                    </select>
                </div>
                <div>
                    <label class="{{ $waLabelClass }}">ترتيب العرض</label>
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $suggested->sort_order) }}" class="{{ $waInputClass }}">
                </div>
            </div>

            <div>
                <label class="{{ $waLabelClass }}">نص الرسالة</label>
                <p class="text-[11px] text-slate-500 mb-2">استخدم متغيرات بصيغة <code>@{{name}}</code> — سيتم تحويلها تلقائياً إلى <code>@{{1}}</code> عند إرسال Meta.</p>
                <textarea name="body" rows="8" required class="{{ $waInputClass }} text-sm leading-relaxed">{{ old('body', $suggested->body) }}</textarea>
            </div>

            <div>
                <label class="{{ $waLabelClass }}">شرح الاستخدام (للسيلز)</label>
                <textarea name="help" rows="5" class="{{ $waInputClass }} text-sm leading-relaxed">{{ old('help', $suggested->help) }}</textarea>
            </div>

            <div>
                <label class="{{ $waLabelClass }}">المتغيرات (مفصولة بفاصلة)</label>
                <input type="text" name="variables_text"
                       value="{{ old('variables_text', implode(', ', $suggested->variables ?? [])) }}"
                       placeholder="name, agent, topic"
                       class="{{ $waInputClass }} dir-ltr text-left font-mono text-sm">
            </div>

            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $suggested->is_active)) class="rounded text-emerald-600">
                نشط في المكتبة
            </label>

            <div class="flex flex-wrap gap-3 pt-4 border-t border-slate-200">
                <button type="submit" class="{{ $waBtnPrimary }}"><i class="fas fa-save"></i> حفظ</button>
                @if($suggested->metaTemplate?->isEditable())
                    <button type="submit" name="sync_meta_draft" value="1" class="{{ $waBtnSecondary }}">
                        <i class="fas fa-sync"></i> حفظ + تحديث مسودة Meta
                    </button>
                @endif
            </div>
        </section>
    </form>

    <section class="{{ $waSectionClass }} p-5 space-y-3">
        <h3 class="font-bold text-slate-900">إرسال إلى Meta</h3>
        <p class="text-sm text-slate-600">حوّل هذا القالب إلى مسودة Meta (متغيرات مرقّمة) ثم أرسله للاعتماد.</p>
        <div class="flex flex-wrap gap-2">
            <form method="POST" action="{{ route('admin.whatsapp.templates.suggested.meta-draft', $suggested) }}">
                @csrf
                <button type="submit" class="{{ $waBtnSecondary }}">
                    <i class="fas fa-file-export"></i>
                    {{ $suggested->metaTemplate ? 'تحديث مسودة Meta' : 'إنشاء مسودة Meta' }}
                </button>
            </form>
            <form method="POST" action="{{ route('admin.whatsapp.templates.suggested.submit-meta', $suggested) }}">
                @csrf
                <button type="submit" class="{{ $waBtnPrimary }}">
                    <i class="fab fa-meta"></i> إرسال إلى Meta للاعتماد
                </button>
            </form>
        </div>
    </section>
</div>
@endsection
