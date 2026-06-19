@extends('layouts.admin')

@section('title', 'دورة تصميم #'.$designTaskCycle->id)
@section('header', 'دورة تصميم #'.$designTaskCycle->id)

@section('content')
@php
    $locked = in_array($designTaskCycle->status, [\App\Models\DesignTaskCycle::STATUS_COMPLETED, \App\Models\DesignTaskCycle::STATUS_CANCELLED], true);
    $canCancel = ! $locked;
@endphp

<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold"><i class="fas fa-check-circle ml-1"></i>{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">{{ $errors->first() }}</div>
    @endif

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.design-task-cycles.index') }}" class="text-slate-500 hover:text-slate-800"><i class="fas fa-arrow-right"></i></a>
                <div>
                    <h2 class="text-xl font-black text-slate-900">{{ $designTaskCycle->title }}</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-1">
                        <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-semibold border {{ \App\Models\DesignTaskCycle::statusBadgeClass($designTaskCycle->status) }}">
                            {{ \App\Models\DesignTaskCycle::statusLabel($designTaskCycle->status) }}
                        </span>
                        <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-semibold {{ \App\Models\DesignTaskCycle::priorityBadgeClass($designTaskCycle->priority) }}">
                            {{ \App\Models\DesignTaskCycle::priorityLabel($designTaskCycle->priority) }}
                        </span>
                        <span class="text-xs text-slate-500">#{{ $designTaskCycle->id }}</span>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.design-task-cycles.edit', $designTaskCycle) }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-edit text-amber-600"></i> تعديل
                </a>
                @if($designTaskCycle->designerTask)
                    <a href="{{ route('admin.employee-tasks.show', $designTaskCycle->designerTask) }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold rounded-xl bg-sky-50 text-sky-800 hover:bg-sky-100">
                        <i class="fas fa-tasks"></i> مهمة المصمم
                    </a>
                @endif
                @if($designTaskCycle->moderatorDeliveryTask)
                    <a href="{{ route('admin.employee-tasks.show', $designTaskCycle->moderatorDeliveryTask) }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold rounded-xl bg-emerald-50 text-emerald-800 hover:bg-emerald-100">
                        <i class="fas fa-truck"></i> تسليم المشرف
                    </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 p-4 border-b bg-white">
            <div class="rounded-xl border p-3">
                <p class="text-[11px] text-slate-500">المشرف</p>
                <p class="font-bold text-slate-900">{{ $designTaskCycle->moderator->name ?? '—' }}</p>
            </div>
            <div class="rounded-xl border p-3">
                <p class="text-[11px] text-slate-500">المصمم</p>
                <p class="font-bold text-slate-900">{{ $designTaskCycle->designer->name ?? '—' }}</p>
            </div>
            <div class="rounded-xl border p-3">
                <p class="text-[11px] text-slate-500">حد التسليم</p>
                <p class="font-bold text-slate-900">{{ $designTaskCycle->deadline_at?->format('Y-m-d H:i') ?? '—' }}</p>
            </div>
            <div class="rounded-xl border p-3">
                <p class="text-[11px] text-slate-500">اكتمال الدورة</p>
                <p class="font-bold text-slate-900">{{ $designTaskCycle->completed_at?->format('Y-m-d H:i') ?? '—' }}</p>
            </div>
        </div>

        <div class="p-4 sm:p-6 space-y-6">
            @if($designTaskCycle->description)
                <div>
                    <h3 class="text-xs font-bold text-slate-500 uppercase mb-2">الوصف</h3>
                    <p class="text-slate-800 whitespace-pre-wrap leading-relaxed">{{ $designTaskCycle->description }}</p>
                </div>
            @endif
            <div>
                <h3 class="text-xs font-bold text-slate-500 uppercase mb-2">المواصفات</h3>
                <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                    <p class="text-slate-800 whitespace-pre-wrap leading-relaxed text-sm">{{ $designTaskCycle->specifications }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="rounded-xl border border-slate-200 p-4">
                    <h3 class="font-bold text-slate-900 mb-3 flex items-center gap-2"><i class="fas fa-sticky-note text-fuchsia-600"></i> ملاحظات الإدارة</h3>
                    <form method="post" action="{{ route('admin.design-task-cycles.notes.update', $designTaskCycle) }}" class="space-y-3">
                        @csrf
                        <textarea name="admin_notes" rows="4" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">{{ old('admin_notes', $designTaskCycle->admin_notes) }}</textarea>
                        <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-xl text-sm font-semibold">حفظ الملاحظات</button>
                    </form>
                </div>

                <div class="rounded-xl border border-slate-200 p-4">
                    <h3 class="font-bold text-slate-900 mb-3 flex items-center gap-2"><i class="fas fa-clock text-sky-600"></i> الجدول الزمني</h3>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-slate-500">تاريخ الإنشاء</dt><dd class="font-semibold">{{ $designTaskCycle->created_at?->format('Y-m-d H:i') }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">تسليم المصمم</dt><dd class="font-semibold">{{ $designTaskCycle->designer_submitted_at?->format('Y-m-d H:i') ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">آخر تحديث</dt><dd class="font-semibold">{{ $designTaskCycle->updated_at?->format('Y-m-d H:i') }}</dd></div>
                    </dl>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 pt-2 border-t border-slate-100">
                @if($canCancel)
                    <form method="post" action="{{ route('admin.design-task-cycles.cancel', $designTaskCycle) }}" onsubmit="return confirm('إلغاء هذه الدورة؟');">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold">
                            <i class="fas fa-ban"></i> إلغاء الدورة
                        </button>
                    </form>
                @endif
                <form method="post" action="{{ route('admin.design-task-cycles.destroy', $designTaskCycle) }}" onsubmit="return confirm('حذف الدورة نهائياً مع المهام المرتبطة؟ لا يمكن التراجع.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold">
                        <i class="fas fa-trash"></i> حذف نهائي
                    </button>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
