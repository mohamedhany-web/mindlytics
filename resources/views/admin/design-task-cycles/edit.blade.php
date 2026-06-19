@extends('layouts.admin')

@section('title', 'تعديل دورة #'.$designTaskCycle->id)
@section('header', 'تعديل دورة تصميم #'.$designTaskCycle->id)

@section('content')
@php
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-fuchsia-500 focus:border-fuchsia-500';
    $textareaClass = $inputClass.' resize-y';
    $locked = in_array($designTaskCycle->status, [\App\Models\DesignTaskCycle::STATUS_COMPLETED, \App\Models\DesignTaskCycle::STATUS_CANCELLED], true);
@endphp

<div class="space-y-6">
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm font-semibold">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    @if($locked)
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            <i class="fas fa-lock ml-1"></i> هذه الدورة <strong>{{ \App\Models\DesignTaskCycle::statusLabel($designTaskCycle->status) }}</strong> — يمكن تعديل المحتوى والملاحظات فقط، وليس تغيير المشرف أو المصمم.
        </div>
    @endif

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.design-task-cycles.show', $designTaskCycle) }}" class="text-slate-500 hover:text-slate-800"><i class="fas fa-arrow-right"></i></a>
                <div>
                    <h2 class="text-xl font-black text-slate-900">تعديل: {{ $designTaskCycle->title }}</h2>
                    <p class="text-xs text-slate-600">#{{ $designTaskCycle->id }} — {{ \App\Models\DesignTaskCycle::statusLabel($designTaskCycle->status) }}</p>
                </div>
            </div>
        </div>

        <form method="post" action="{{ route('admin.design-task-cycles.update', $designTaskCycle) }}" class="p-4 sm:p-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
                <div class="xl:col-span-8 space-y-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">عنوان الطلب *</label>
                        <input type="text" name="title" value="{{ old('title', $designTaskCycle->title) }}" required class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">وصف مختصر</label>
                        <textarea name="description" rows="3" class="{{ $textareaClass }}">{{ old('description', $designTaskCycle->description) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">تفاصيل التصميم *</label>
                        <textarea name="specifications" rows="12" required class="{{ $textareaClass }} min-h-[16rem]">{{ old('specifications', $designTaskCycle->specifications) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">ملاحظات الإدارة</label>
                        <textarea name="admin_notes" rows="3" class="{{ $textareaClass }}">{{ old('admin_notes', $designTaskCycle->admin_notes) }}</textarea>
                    </div>
                </div>

                <div class="xl:col-span-4 space-y-5">
                    <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4 space-y-4">
                        <h3 class="text-sm font-black text-slate-900">الإسناد والحالة</h3>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">المشرف *</label>
                            <select name="moderator_id" {{ $locked ? 'disabled' : 'required' }} class="{{ $inputClass }} {{ $locked ? 'bg-slate-100' : '' }}">
                                @foreach($moderators as $m)
                                    <option value="{{ $m->id }}" @selected(old('moderator_id', $designTaskCycle->moderator_id) == $m->id)>{{ $m->name }}</option>
                                @endforeach
                            </select>
                            @if($locked)<input type="hidden" name="moderator_id" value="{{ $designTaskCycle->moderator_id }}">@endif
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">المصمم *</label>
                            <select name="designer_employee_id" {{ $locked ? 'disabled' : 'required' }} class="{{ $inputClass }} {{ $locked ? 'bg-slate-100' : '' }}">
                                @foreach($designers as $d)
                                    <option value="{{ $d->id }}" @selected(old('designer_employee_id', $designTaskCycle->designer_employee_id) == $d->id)>{{ $d->name }}</option>
                                @endforeach
                            </select>
                            @if($locked)<input type="hidden" name="designer_employee_id" value="{{ $designTaskCycle->designer_employee_id }}">@endif
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">الأولوية *</label>
                            <select name="priority" required class="{{ $inputClass }}">
                                @foreach(['low', 'medium', 'high', 'urgent'] as $v)
                                    <option value="{{ $v }}" @selected(old('priority', $designTaskCycle->priority) === $v)>{{ \App\Models\DesignTaskCycle::priorityLabel($v) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">حد التسليم *</label>
                            <input type="datetime-local" name="deadline_at" value="{{ old('deadline_at', $designTaskCycle->deadline_at?->format('Y-m-d\TH:i')) }}" required class="{{ $inputClass }}">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">الحالة *</label>
                            <select name="status" required class="{{ $inputClass }}">
                                @foreach(['pending_design','design_in_progress','design_submitted','moderator_delivery_pending','completed','cancelled'] as $st)
                                    <option value="{{ $st }}" @selected(old('status', $designTaskCycle->status) === $st)>{{ \App\Models\DesignTaskCycle::statusLabel($st) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-fuchsia-600 hover:bg-fuchsia-700 text-white rounded-xl text-sm font-bold">
                        <i class="fas fa-save"></i> حفظ التعديلات
                    </button>
                    <a href="{{ route('admin.design-task-cycles.show', $designTaskCycle) }}" class="block text-center text-sm text-slate-600 hover:text-slate-900">إلغاء</a>
                </div>
            </div>
        </form>
    </section>
</div>
@endsection
