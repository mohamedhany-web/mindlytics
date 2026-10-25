@extends('layouts.admin')

@section('title', 'قواعد فريق المبيعات')
@section('header', 'المبيعات — قواعد السيلز')

@section('content')
@php $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500'; @endphp
<div class="space-y-6">
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800">{{ session('success') }}</div>
    @endif

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-5 bg-gradient-to-l from-emerald-700 to-teal-700 text-white">
            <p class="text-xs uppercase tracking-widest opacity-80">Mindlytics Academy</p>
            <h2 class="text-2xl font-black mt-1">{{ $settings->document_title }}</h2>
            <p class="text-sm opacity-90 mt-1">{{ $settings->document_title_en }}</p>
            <p class="text-xs mt-2 opacity-75">Version {{ $settings->version }} · Applies To: Sales Team</p>
        </div>
        <form method="POST" action="{{ route('admin.sales.policy.settings') }}" class="p-5 space-y-4 border-b border-slate-200">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500">Version</label>
                    <input type="text" name="version" value="{{ old('version', $settings->version) }}" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500">Effective Date</label>
                    <input type="date" name="effective_date" value="{{ old('effective_date', $settings->effective_date?->format('Y-m-d')) }}" class="{{ $inputClass }}">
                </div>
                <div class="flex items-end">
                    <button class="w-full rounded-xl bg-slate-900 text-white px-4 py-2.5 text-sm font-semibold">حفظ بيانات الدليل</button>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500">العنوان بالعربي</label>
                    <input type="text" name="document_title" value="{{ old('document_title', $settings->document_title) }}" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500">English title</label>
                    <input type="text" name="document_title_en" value="{{ old('document_title_en', $settings->document_title_en) }}" class="{{ $inputClass }}">
                </div>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">المقدمة</label>
                <textarea name="intro_content" rows="4" class="{{ $inputClass }}">{{ old('intro_content', $settings->intro_content) }}</textarea>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">إقرار الاستلام والالتزام</label>
                <textarea name="acknowledgement_content" rows="5" class="{{ $inputClass }}">{{ old('acknowledgement_content', $settings->acknowledgement_content) }}</textarea>
            </div>
        </form>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg p-5">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <h3 class="font-black text-slate-900">الأقسام والقواعد</h3>
            <a href="{{ route('admin.sales.policy.rules.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-semibold">
                <i class="fas fa-plus"></i> قاعدة جديدة
            </a>
        </div>

        <details class="mb-4 rounded-xl border border-dashed border-slate-300 p-4">
            <summary class="font-semibold text-slate-700 cursor-pointer">+ إضافة قسم جديد</summary>
            <form method="POST" action="{{ route('admin.sales.policy.sections.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 mt-4 items-end">
                @csrf
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold">عنوان القسم</label>
                    <input type="text" name="title" required class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="text-xs font-semibold">Rules range</label>
                    <input type="text" name="rules_range" placeholder="9 – 14" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="text-xs font-semibold">الترتيب</label>
                    <input type="number" name="sort_order" value="0" class="{{ $inputClass }}">
                </div>
                <button class="md:col-span-4 rounded-xl bg-slate-800 text-white px-4 py-2.5 text-sm font-semibold w-fit">إضافة قسم</button>
            </form>
        </details>

        <div class="space-y-4">
            @foreach($sections as $section)
                <div class="rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="px-4 py-3 bg-slate-50 border-b flex flex-wrap items-center justify-between gap-2">
                        <form method="POST" action="{{ route('admin.sales.policy.sections.update', $section) }}" class="flex flex-wrap items-center gap-2 flex-1">
                            @csrf @method('PUT')
                            <input type="text" name="title" value="{{ $section->title }}" class="rounded-lg border px-2 py-1 text-sm font-bold min-w-[200px]">
                            <input type="text" name="rules_range" value="{{ $section->rules_range }}" placeholder="Rules" class="rounded-lg border px-2 py-1 text-xs w-24">
                            <input type="number" name="sort_order" value="{{ $section->sort_order }}" class="rounded-lg border px-2 py-1 text-xs w-16">
                            <label class="text-xs flex items-center gap-1"><input type="checkbox" name="is_active" value="1" @checked($section->is_active)> نشط</label>
                            <button class="text-xs font-semibold text-sky-700">حفظ</button>
                        </form>
                        <form method="POST" action="{{ route('admin.sales.policy.sections.destroy', $section) }}" onsubmit="return confirm('حذف القسم وكل قواعده؟');">
                            @csrf @method('DELETE')
                            <button class="text-xs font-semibold text-rose-600">حذف القسم</button>
                        </form>
                    </div>
                    <div class="divide-y">
                        @forelse($section->rules as $rule)
                            <div class="px-4 py-3 flex flex-wrap items-start justify-between gap-3 {{ ! $rule->is_active ? 'opacity-50' : '' }}">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-black">Rule {{ $rule->displayNumber() }}</span>
                                        <span class="font-bold text-slate-900">{{ $rule->title }}</span>
                                        @if(! $rule->is_active)<span class="text-xs text-amber-600 font-semibold">مخفي</span>@endif
                                    </div>
                                    <p class="text-sm text-slate-600 mt-1 line-clamp-2 whitespace-pre-line">{{ \Illuminate\Support\Str::limit($rule->content, 180) }}</p>
                                </div>
                                <div class="flex gap-2 shrink-0">
                                    <a href="{{ route('admin.sales.policy.rules.edit', $rule) }}" class="text-xs font-semibold text-sky-700">تعديل</a>
                                    <form method="POST" action="{{ route('admin.sales.policy.rules.destroy', $rule) }}" onsubmit="return confirm('حذف هذه القاعدة؟');">
                                        @csrf @method('DELETE')
                                        <button class="text-xs font-semibold text-rose-600">حذف</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-6 text-center text-slate-500 text-sm">لا توجد قواعد في هذا القسم.</div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
