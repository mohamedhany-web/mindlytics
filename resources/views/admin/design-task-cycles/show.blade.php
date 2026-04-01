@extends('layouts.admin')

@section('title', 'دورة تصميم #'.$designTaskCycle->id)
@section('header', 'دورة تصميم #'.$designTaskCycle->id)

@section('content')
<div class="space-y-6 w-full">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.design-task-cycles.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-800 text-sm font-semibold">القائمة</a>
        @if($designTaskCycle->designerTask)
            <a href="{{ route('admin.employee-tasks.show', $designTaskCycle->designerTask) }}" class="px-4 py-2 rounded-lg bg-sky-50 text-sky-800 text-sm font-semibold">مهمة المصمم</a>
        @endif
        @if($designTaskCycle->moderatorDeliveryTask)
            <a href="{{ route('admin.employee-tasks.show', $designTaskCycle->moderatorDeliveryTask) }}" class="px-4 py-2 rounded-lg bg-emerald-50 text-emerald-800 text-sm font-semibold">مهمة تسليم المشرف</a>
        @endif
    </div>

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm">{{ $errors->first() }}</div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
        <div class="flex flex-wrap justify-between gap-2">
            <h2 class="text-lg font-bold">{{ $designTaskCycle->title }}</h2>
            <span class="text-sm font-bold text-fuchsia-800">{{ \App\Models\DesignTaskCycle::statusLabel($designTaskCycle->status) }}</span>
        </div>
        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
            <div><dt class="text-gray-500">المشرف</dt><dd class="font-semibold">{{ $designTaskCycle->moderator->name ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">المصمم</dt><dd class="font-semibold">{{ $designTaskCycle->designer->name ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">حد تسليم المصمم</dt><dd>{{ $designTaskCycle->deadline_at?->format('Y-m-d H:i') }}</dd></div>
            <div><dt class="text-gray-500">تسليم المصمم</dt><dd>{{ $designTaskCycle->designer_submitted_at?->format('Y-m-d H:i') ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">اكتمال الدورة</dt><dd>{{ $designTaskCycle->completed_at?->format('Y-m-d H:i') ?? '—' }}</dd></div>
        </dl>
        @if($designTaskCycle->description)
            <div><p class="text-xs text-gray-500 mb-1">الوصف</p><p class="text-gray-800 whitespace-pre-wrap">{{ $designTaskCycle->description }}</p></div>
        @endif
        <div><p class="text-xs text-gray-500 mb-1">المواصفات</p><p class="text-gray-800 whitespace-pre-wrap">{{ $designTaskCycle->specifications }}</p></div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h3 class="font-bold mb-3">ملاحظات الإدارة</h3>
        <form method="post" action="{{ route('admin.design-task-cycles.notes.update', $designTaskCycle) }}" class="space-y-3">
            @csrf
            <textarea name="admin_notes" rows="4" class="w-full rounded-lg border-gray-300 text-sm">{{ old('admin_notes', $designTaskCycle->admin_notes) }}</textarea>
            <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold">حفظ</button>
        </form>
    </div>

    @if(! in_array($designTaskCycle->status, [\App\Models\DesignTaskCycle::STATUS_COMPLETED, \App\Models\DesignTaskCycle::STATUS_CANCELLED], true))
        <form method="post" action="{{ route('admin.design-task-cycles.cancel', $designTaskCycle) }}" onsubmit="return confirm('إلغاء الدورة؟');">
            @csrf
            <button type="submit" class="px-5 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold">إلغاء من الإدارة</button>
        </form>
    @endif
</div>
@endsection
