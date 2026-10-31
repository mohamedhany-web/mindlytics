@extends('layouts.admin')

@section('title', 'دورة تصميم جديدة')
@section('header', 'إنشاء دورة تصميم')

@section('content')
@php
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-fuchsia-500 focus:border-fuchsia-500';
    $textareaClass = $inputClass.' resize-y';
@endphp

<div class="space-y-6">
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">
            <p class="font-semibold mb-1"><i class="fas fa-exclamation-circle ml-1"></i> يوجد أخطاء:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    @if($moderators->isEmpty() || $designers->isEmpty())
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            <p class="font-bold mb-1"><i class="fas fa-exclamation-triangle ml-1"></i> تنبيه</p>
            @if($moderators->isEmpty())<p>لا يوجد مشرفون نشطون — أنشئ وظيفة مشرف وعيّنها لموظف.</p>@endif
            @if($designers->isEmpty())<p>لا يوجد مصممون نشطون — أنشئ وظيفة مصمم وعيّنها لموظف.</p>@endif
        </div>
    @endif

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-gradient-to-l from-fuchsia-50 to-white border-b flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.design-task-cycles.index') }}" class="text-slate-500 hover:text-slate-800"><i class="fas fa-arrow-right"></i></a>
                <div>
                    <h2 class="text-xl font-black text-slate-900">دورة تصميم جديدة</h2>
                    <p class="text-xs text-slate-600">اختر المشرف والمصمم — ستُنشأ مهمة تلقائياً للمصمم مع إشعار.</p>
                </div>
            </div>
        </div>

        <form method="post" action="{{ route('admin.design-task-cycles.store') }}" class="p-4 sm:p-6">
            @csrf
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
                <div class="xl:col-span-8 space-y-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">عنوان الطلب *</label>
                        <input type="text" name="title" value="{{ old('title') }}" required maxlength="255" placeholder="مثال: غلاف إعلان لكورس X" class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">وصف مختصر</label>
                        <textarea name="description" rows="3" placeholder="سياق الطلب..." class="{{ $textareaClass }}">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">تفاصيل التصميم المطلوب *</label>
                        <textarea name="specifications" rows="12" required placeholder="الأبعاد، الألوان، الخطوط، النصوص، المراجع..." class="{{ $textareaClass }} min-h-[16rem]">{{ old('specifications') }}</textarea>
                    </div>
                </div>

                <div class="xl:col-span-4 space-y-5">
                    <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4 space-y-4">
                        <h3 class="text-sm font-black text-slate-900 flex items-center gap-2"><i class="fas fa-users text-fuchsia-600"></i> الإسناد</h3>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">المشرف *</label>
                            <select name="moderator_id" required class="{{ $inputClass }}" {{ $moderators->isEmpty() ? 'disabled' : '' }}>
                                <option value="">— اختر المشرف —</option>
                                @foreach($moderators as $m)
                                    <option value="{{ $m->id }}" @selected(old('moderator_id') == $m->id)>{{ $m->name }}@if($m->employeeJob) — {{ $m->employeeJob->name }}@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">المصمم *</label>
                            <select name="designer_employee_id" required class="{{ $inputClass }}" {{ $designers->isEmpty() ? 'disabled' : '' }}>
                                <option value="">— اختر المصمم —</option>
                                @foreach($designers as $d)
                                    <option value="{{ $d->id }}" @selected(old('designer_employee_id') == $d->id)>{{ $d->name }}@if($d->employeeJob) — {{ $d->employeeJob->name }}@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">الأولوية *</label>
                            <select name="priority" required class="{{ $inputClass }}">
                                @foreach(['low' => 'منخفضة', 'medium' => 'متوسطة', 'high' => 'عالية', 'urgent' => 'عاجلة'] as $v => $l)
                                    <option value="{{ $v }}" @selected(old('priority', 'medium') === $v)>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">حد تسليم المصمم *</label>
                            <input type="datetime-local" name="deadline_at" value="{{ old('deadline_at') }}" required class="{{ $inputClass }}">
                        </div>
                    </div>

                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-fuchsia-600 hover:bg-fuchsia-700 text-white rounded-xl text-sm font-bold shadow-sm disabled:opacity-50"
                        {{ ($moderators->isEmpty() || $designers->isEmpty()) ? 'disabled' : '' }}>
                        <i class="fas fa-paper-plane"></i>
                        إنشاء الدورة وإسناد المهمة
                    </button>
                </div>
            </div>
        </form>
    </section>
</div>
@endsection
