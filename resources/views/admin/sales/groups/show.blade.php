@extends('layouts.admin')

@section('title', $group->name)
@section('header', 'المبيعات — مجموعة: '.$group->name)

@section('content')
@php
    $selectedMemberIds = collect(old('member_ids', $group->memberIds()->all()))->filter()->map(fn ($id) => (int) $id);
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500';
    $memberCount = $group->members->isNotEmpty() ? $group->members->count() : ($group->assignee ? 1 : 0);
    $leadsCount = $group->leads->count();
    $phoneCount = ($leadsWithPhone ?? collect())->count();
@endphp

<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-check-circle ml-1"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-exclamation-circle ml-1"></i>{{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">
            <p class="font-semibold mb-1"><i class="fas fa-exclamation-circle ml-1"></i> يوجد أخطاء:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="min-w-0">
                    <h2 class="text-xl font-black text-slate-900 truncate">{{ $group->name }}</h2>
                    <p class="text-xs text-slate-600">إدارة الموظفين والعملاء وإرسال واتساب جماعي للمجموعة.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.sales.groups.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-arrow-right"></i>
                    القائمة
                </a>
                <a href="{{ route('admin.sales.leads.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-user-tag text-emerald-600"></i>
                    العملاء
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 p-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-xs font-semibold text-slate-600">الموظفون</p>
                        <p class="text-xl font-black text-slate-900 tabular-nums">{{ number_format($memberCount) }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center"><i class="fas fa-users text-sm"></i></div>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-xs font-semibold text-slate-600">العملاء</p>
                        <p class="text-xl font-black text-slate-900 tabular-nums">{{ number_format($leadsCount) }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center"><i class="fas fa-user-tag text-sm"></i></div>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-xs font-semibold text-slate-600">بأرقام هاتف</p>
                        <p class="text-xl font-black text-slate-900 tabular-nums">{{ number_format($phoneCount) }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-teal-100 text-teal-600 flex items-center justify-center"><i class="fab fa-whatsapp text-sm"></i></div>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-xs font-semibold text-slate-600">آخر تحديث</p>
                        <p class="text-sm font-bold text-slate-900 mt-1">{{ $group->updated_at?->diffForHumans() ?? '—' }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center"><i class="fas fa-clock text-sm"></i></div>
                </div>
            </div>
        </div>
    </section>

    @if($group->members->isNotEmpty() || $group->assigned_to)
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-sky-600"></i>
                    تقارير أداء الموظفين في هذه المجموعة
                </h3>
            </div>
            <div class="p-4 flex flex-wrap gap-2">
                @foreach(($group->members->isNotEmpty() ? $group->members : collect([$group->assignee])) as $member)
                    @if($member)
                        <a href="{{ route('admin.sales.reports.employee', ['user_id' => $member->id, 'group_id' => $group->id, 'lead_scope' => 'in_groups']) }}"
                           class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-sky-50 border border-sky-200 text-sm font-semibold text-sky-800 hover:bg-sky-100 transition-colors">
                            <i class="fas fa-user text-xs"></i>
                            {{ $member->name }}
                        </a>
                    @endif
                @endforeach
            </div>
        </section>
    @endif

    @include('admin.sales.groups._whatsapp_bulk', [
        'group' => $group,
        'leadsWithPhone' => $leadsWithPhone ?? collect(),
        'formAction' => route('admin.sales.groups.whatsapp.store', $group),
        'latestBatch' => $latestBatch ?? null,
        'latestBatchUrl' => isset($latestBatch) ? route('admin.whatsapp.batches.show', $latestBatch) : null,
    ])

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-pen text-teal-600"></i>
                تعديل المجموعة والعملاء
            </h3>
            <p class="text-xs text-slate-600 mt-0.5">حدّث الاسم والموظفين ثم اختر العملاء من محافظهم.</p>
        </div>

        <form method="post" action="{{ route('admin.sales.groups.update', $group) }}" class="p-5 space-y-5">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">اسم المجموعة *</label>
                    <input type="text" name="name" value="{{ old('name', $group->name) }}" required maxlength="120" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">وصف</label>
                    <input type="text" name="description" value="{{ old('description', $group->description) }}" maxlength="2000" class="{{ $inputClass }}">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-2">
                    موظفو المبيعات في المجموعة
                    <span class="text-[11px] font-bold text-sky-700 bg-sky-50 border border-sky-200 rounded-lg px-2 py-0.5 mr-1">اختياري</span>
                </label>
                <div class="max-h-48 overflow-y-auto rounded-xl border border-slate-200 bg-slate-50/50 p-3 space-y-1.5">
                    @foreach($reps as $rep)
                        <label class="flex items-center gap-2.5 text-sm rounded-lg px-2.5 py-2 hover:bg-white cursor-pointer border border-transparent hover:border-slate-200 transition-colors">
                            <input type="checkbox" name="member_ids[]" value="{{ $rep->id }}" class="rounded text-teal-600 focus:ring-teal-500"
                                @checked($selectedMemberIds->contains($rep->id))>
                            <span class="font-medium text-slate-800">{{ $rep->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('member_ids')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                <p class="text-[11px] text-slate-500 mt-1">ليس إلزامياً. عند الإسناد: كل موظف يرى عملاءه المسندين إليه داخل هذه المجموعة فقط.</p>
            </div>

            <div>
                <div class="flex items-center justify-between gap-2 mb-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-0">اختر العملاء (من محافظ الموظفين المحددين)</label>
                    <span class="text-[11px] text-slate-500 tabular-nums">{{ $availableLeads->count() }} متاح</span>
                </div>
                <div class="max-h-80 overflow-y-auto rounded-xl border border-slate-200 bg-slate-50/50 p-3 space-y-1 text-sm">
                    @forelse($availableLeads as $lead)
                        <label class="flex flex-wrap items-center gap-2 rounded-lg px-2.5 py-2 hover:bg-white cursor-pointer border border-transparent hover:border-slate-200 transition-colors">
                            <input type="checkbox" name="lead_ids[]" value="{{ $lead->id }}" class="rounded text-teal-600 focus:ring-teal-500"
                                @checked(old('lead_ids') ? in_array($lead->id, old('lead_ids', [])) : (int) $lead->sales_lead_group_id === (int) $group->id)>
                            <span class="font-medium text-slate-800">{{ $lead->name }}</span>
                            <span class="text-slate-400 text-xs font-mono" dir="ltr">{{ $lead->phone }}</span>
                            @if($lead->assignee)
                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-sky-50 text-sky-700 border border-sky-100">{{ $lead->assignee->name }}</span>
                            @endif
                        </label>
                    @empty
                        <p class="text-sm text-slate-500 text-center py-8">لا يوجد عملاء في محافظ الموظفين المحددين.</p>
                    @endforelse
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl bg-emerald-600 hover:bg-emerald-700">
                    <i class="fas fa-save"></i>
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </section>

    @if($group->leads->isNotEmpty())
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-list text-emerald-600"></i>
                    عملاء المجموعة حالياً
                </h3>
                <span class="text-xs text-slate-500 tabular-nums">{{ $group->leads->count() }} عميل</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                            <th class="px-4 py-3 text-right font-semibold">العميل</th>
                            <th class="px-4 py-3 text-right font-semibold">الهاتف</th>
                            <th class="px-4 py-3 text-right font-semibold">الموظف</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($group->leads as $lead)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $lead->name }}</td>
                                <td class="px-4 py-3 text-xs font-mono text-slate-500" dir="ltr">{{ $lead->phone ?: '—' }}</td>
                                <td class="px-4 py-3 text-xs text-slate-600">{{ $lead->assignee->name ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    <section class="rounded-2xl bg-white border border-rose-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-rose-100 bg-rose-50">
            <h3 class="text-sm font-black text-rose-900 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i>
                منطقة خطر
            </h3>
        </div>
        <div class="p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <p class="text-sm text-slate-600">حذف المجموعة يزيل الربط عن العملاء دون حذف العملاء أنفسهم.</p>
            <form method="post" action="{{ route('admin.sales.groups.destroy', $group) }}" onsubmit="return confirm('حذف المجموعة؟')">
                @csrf @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-rose-700 rounded-xl border border-rose-300 bg-white hover:bg-rose-50">
                    <i class="fas fa-trash"></i>
                    حذف المجموعة
                </button>
            </form>
        </div>
    </section>
</div>
@endsection
