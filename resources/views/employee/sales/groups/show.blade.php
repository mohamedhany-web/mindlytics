@extends('layouts.employee')

@section('title', $group->name)
@section('header', 'مجموعة: '.$group->name)

@section('content')
@include('employee.sales.groups._styles')

<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <h2 class="text-xl font-bold text-slate-900">{{ $group->name }}</h2>
                @if($group->is_admin_managed)
                    <span class="text-xs px-2 py-0.5 rounded-md bg-sky-100 text-sky-800 font-semibold">من الإدارة</span>
                @endif
            </div>
            @if($group->description)
                <p class="text-sm text-slate-500 mt-1">{{ $group->description }}</p>
            @endif
            @if(($group->members ?? collect())->count() > 1)
                <p class="text-xs text-sky-700 mt-1">
                    <i class="fas fa-user-friends ml-1"></i>
                    مجموعة مشتركة مع: {{ $group->members->where('id', '!=', auth()->id())->pluck('name')->implode('، ') ?: 'فريق المبيعات' }}
                    — تظهر لك عملاؤك فقط ({{ $group->leads->count() }}).
                </p>
            @endif
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('employee.sales.groups.index') }}" class="px-4 py-2 text-sm border border-slate-200 rounded-lg text-slate-700">المجموعات</a>
            <a href="{{ route('employee.sales.leads.index', ['group_id' => $group->id]) }}" class="px-4 py-2 text-sm border border-slate-200 rounded-lg text-slate-700">عرض العملاء</a>
            <a href="{{ route('employee.sales.leads.create') }}?group={{ $group->id }}" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold">+ عميل في المجموعة</a>
        </div>
    </div>

    @if(session('success'))<div class="rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-2 text-sm">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="rounded-lg border border-rose-200 bg-rose-50 text-rose-800 px-4 py-2 text-sm">{{ session('error') }}</div>@endif

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="xl:col-span-8 space-y-4">
            <form method="post" action="{{ route('employee.sales.groups.update', $group) }}" class="sales-panel p-5 space-y-4">
                @csrf
                @method('PUT')
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">الاسم</label>
                        <input type="text" name="name" value="{{ old('name', $group->name) }}" required class="px-3 py-2.5" @disabled($group->is_admin_managed)>
                        @if($group->is_admin_managed)<p class="text-xs text-slate-500 mt-1">اسم مجموعات الإدارة لا يُعدَّل</p>@endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">وصف</label>
                        <input type="text" name="description" value="{{ old('description', $group->description) }}" class="px-3 py-2.5">
                    </div>
                </div>

                <div>
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                        <label class="block text-sm font-bold text-slate-800">اختر العملاء</label>
                        <span class="text-xs text-slate-500">{{ $group->leads->count() }} محدّد حالياً</span>
                    </div>
                    <div class="max-h-80 overflow-y-auto border border-slate-200 rounded-lg divide-y divide-slate-100">
                        @forelse($availableLeads as $lead)
                            <label class="flex items-center gap-3 px-3 py-2.5 hover:bg-slate-50 cursor-pointer text-sm">
                                <input type="checkbox" name="lead_ids[]" value="{{ $lead->id }}" class="rounded border-slate-300"
                                    @checked(old('lead_ids') ? in_array($lead->id, old('lead_ids', [])) : (int)$lead->sales_lead_group_id === (int)$group->id)>
                                <span class="font-medium text-slate-900 flex-1">{{ $lead->name }}</span>
                                @if($lead->phone)<span class="text-slate-500 text-xs" dir="ltr">{{ $lead->phone }}</span>@endif
                                @if($lead->sales_lead_group_id && (int)$lead->sales_lead_group_id !== (int)$group->id)
                                    <span class="text-[10px] text-amber-700">مجموعة أخرى</span>
                                @endif
                            </label>
                        @empty
                            <p class="p-4 text-sm text-slate-500 text-center">لا يوجد عملاء متاحون — <a href="{{ route('employee.sales.leads.create') }}" class="underline">سجّل عميلاً</a></p>
                        @endforelse
                    </div>
                </div>

                <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold">حفظ التغييرات</button>
            </form>

            @if(!$group->is_admin_managed)
                <form method="post" action="{{ route('employee.sales.groups.destroy', $group) }}" onsubmit="return confirm('حذف المجموعة؟ العملاء يبقون بدون مجموعة.')" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-sm text-rose-700 border border-rose-200 rounded-lg hover:bg-rose-50">حذف المجموعة</button>
                </form>
            @endif
        </div>

        <aside class="xl:col-span-4 space-y-4">
            @include('admin.sales.groups._whatsapp_bulk', [
                'group' => $group,
                'leadsWithPhone' => $leadsWithPhone ?? collect(),
                'formAction' => route('employee.sales.groups.whatsapp.store', $group),
                'latestBatch' => $latestBatch ?? null,
                'latestBatchUrl' => isset($latestBatch) ? route('employee.sales.groups.whatsapp-batches.show', [$group, $latestBatch]) : null,
                'panelClass' => 'sales-panel p-4 space-y-4',
            ])

            <div class="sales-panel p-4">
                <h3 class="font-bold text-slate-900 text-sm mb-3">عملاء المجموعة ({{ $group->leads->count() }})</h3>
                <ul class="space-y-2 max-h-96 overflow-y-auto text-sm">
                    @forelse($group->leads as $lead)
                        <li class="flex justify-between gap-2 border-b border-slate-100 pb-2">
                            <a href="{{ route('employee.sales.leads.show', $lead) }}" class="font-medium text-slate-800 hover:underline">{{ $lead->name }}</a>
                            <span class="text-xs text-slate-500">{{ \App\Models\SalesLead::stageLabel($lead->stage) }}</span>
                        </li>
                    @empty
                        <li class="text-slate-500 text-sm">لا يوجد عملاء بعد</li>
                    @endforelse
                </ul>
            </div>
            <div class="sales-panel p-4 text-xs text-slate-600">
                <p class="font-semibold text-slate-800 mb-1">اختصار</p>
                <p>عند تسجيل عميل جديد اختر هذه المجموعة من القائمة — أو استخدم زر «+ عميل في المجموعة».</p>
            </div>
        </aside>
    </div>
</div>
@endsection
