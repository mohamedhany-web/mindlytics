@extends('layouts.admin')

@section('title', $plan->title)
@section('header', 'خطة تسويق: '.$plan->title)

@section('content')
@php
    $statusLabels = ['draft' => 'مسودة', 'active' => 'نشط', 'paused' => 'متوقف', 'completed' => 'مكتمل'];
    $evtStatus = fn ($s) => \App\Models\ModeratorMarketingCalendarEvent::statusLabel($s);
@endphp
<div class="space-y-6" x-data="{ isGeneralEvent: false }">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold">{{ session('success') }}</div>
    @endif

    <section class="rounded-2xl bg-white border shadow-lg overflow-hidden">
        <div class="px-5 py-4 bg-slate-50 border-b flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-black text-slate-900">{{ $plan->title }}</h2>
                <p class="text-xs text-slate-600 mt-1">المشرف: <strong>{{ $plan->moderator->name ?? '—' }}</strong> · {{ $statusLabels[$plan->status] ?? $plan->status }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.moderator-marketing-plans.index') }}" class="px-3 py-2 rounded-xl border text-sm font-semibold">القائمة</a>
                <a href="{{ route('admin.moderator-marketing-plans.edit', $plan) }}" class="px-3 py-2 rounded-xl bg-amber-500 text-white text-sm font-semibold">تعديل</a>
                <a href="{{ route('admin.moderator-marketing-plans.settings') }}" class="px-3 py-2 rounded-xl border text-sm font-semibold"><i class="fas fa-cog"></i> الإعدادات</a>
            </div>
        </div>
        <div class="p-5 grid md:grid-cols-2 gap-4 text-sm">
            @if($plan->summary)<div><p class="text-xs font-bold text-slate-500 mb-1">الملخص</p><p class="whitespace-pre-wrap">{{ $plan->summary }}</p></div>@endif
            @if($plan->goals)<div><p class="text-xs font-bold text-slate-500 mb-1">الأهداف</p><p class="whitespace-pre-wrap">{{ $plan->goals }}</p></div>@endif
        </div>
    </section>

    <div class="rounded-xl border border-violet-200 bg-violet-50 px-4 py-3 text-sm text-violet-900">
        <i class="fas fa-robot ml-1"></i>
        <strong>أتمتة:</strong> نوع «تصميم جرافيك» → مهمة للمصمم · «مونتاج فيديو» → مهمة مونتاج · البوست المجدول يتطلب تأكيد «تم التنفيذ» وإلا غرامة تلقائية.
    </div>

    {{-- منصات --}}
    <section class="rounded-2xl border bg-white shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b bg-pink-50 font-bold text-pink-900"><i class="fas fa-share-alt ml-1"></i> المنصات والتوصيف</div>
        <div class="p-4 space-y-4">
            <form method="post" action="{{ route('admin.moderator-marketing-plans.platforms.store', $plan) }}" class="grid md:grid-cols-12 gap-3 rounded-xl border border-dashed border-pink-200 bg-pink-50/30 p-4">
                @csrf
                <div class="md:col-span-6">
                    <label class="text-xs font-semibold block mb-2">المنصات (متعدد)</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach($platformLabels as $key => $label)
                            <label class="inline-flex items-center gap-2 rounded-lg border bg-white px-2 py-1.5 text-xs">
                                <input type="checkbox" name="platform_keys[]" value="{{ $key }}" class="rounded">
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="md:col-span-2"><label class="text-xs font-semibold">اسم مخصص (أخرى)</label><input type="text" name="custom_label" class="w-full rounded-xl border px-3 py-2 text-sm"></div>
                <div class="md:col-span-2"><label class="text-xs font-semibold">لون</label><input type="text" name="color_hex" value="#6366f1" pattern="#[0-9A-Fa-f]{6}" class="w-full rounded-xl border px-3 py-2 text-sm font-mono"></div>
                <div class="md:col-span-12">
                    <label class="text-xs font-semibold block mb-2">التوصيف / الوظائف المسؤولة</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($employeeJobs as $job)
                            <label class="inline-flex items-center gap-2 rounded-lg border border-violet-100 bg-violet-50 px-2 py-1 text-xs">
                                <input type="checkbox" name="employee_job_ids[]" value="{{ $job->id }}" class="rounded">
                                {{ $job->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="md:col-span-6"><label class="text-xs font-semibold">إيقاع النشر</label><textarea name="cadence_notes" rows="1" class="w-full rounded-xl border px-3 py-2 text-sm"></textarea></div>
                <div class="md:col-span-6"><label class="text-xs font-semibold">استراتيجية</label><textarea name="strategy_notes" rows="1" class="w-full rounded-xl border px-3 py-2 text-sm"></textarea></div>
                <div class="md:col-span-12"><button type="submit" class="px-4 py-2 bg-pink-600 text-white rounded-xl text-sm font-bold">إضافة منصة</button></div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50"><tr>
                        <th class="text-right px-3 py-2">المنصة</th>
                        <th class="text-right px-3 py-2">التوصيف</th>
                        <th class="text-right px-3 py-2">إيقاع</th>
                        <th class="text-right px-3 py-2"></th>
                    </tr></thead>
                    <tbody class="divide-y">
                        @forelse($plan->platforms as $plat)
                            <tr class="align-top">
                                <td class="px-3 py-2 font-semibold"><span class="w-2 h-2 rounded-full inline-block ml-1" style="background:{{ $plat->color_hex }}"></span>{{ $plat->displayName() }}</td>
                                <td class="px-3 py-2">@foreach($plat->employeeJobs as $j)<span class="text-xs bg-violet-100 text-violet-800 px-2 py-0.5 rounded ml-1">{{ $j->name }}</span>@endforeach</td>
                                <td class="px-3 py-2 text-xs text-slate-600">{{ $plat->cadence_notes ?: '—' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <details>
                                        <summary class="cursor-pointer text-xs font-semibold text-pink-700">تعديل</summary>
                                        <form method="post" action="{{ route('admin.moderator-marketing-plans.platforms.update', [$plan, $plat]) }}" class="mt-2 space-y-2 p-3 border rounded-lg min-w-[220px]">
                                            @csrf @method('PUT')
                                            <select name="platform_key" class="w-full rounded border px-2 py-1 text-xs">
                                                @foreach($platformLabels as $key => $label)
                                                    <option value="{{ $key }}" @selected($plat->platform_key === $key)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="custom_label" value="{{ $plat->custom_label }}" placeholder="اسم مخصص" class="w-full rounded border px-2 py-1 text-xs">
                                            <input type="text" name="color_hex" value="{{ $plat->color_hex }}" class="w-full rounded border px-2 py-1 text-xs font-mono">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($employeeJobs as $job)
                                                    <label class="text-[10px]"><input type="checkbox" name="employee_job_ids[]" value="{{ $job->id }}" @checked($plat->employeeJobs->contains('id', $job->id))> {{ $job->name }}</label>
                                                @endforeach
                                            </div>
                                            <textarea name="cadence_notes" rows="1" class="w-full rounded border px-2 py-1 text-xs">{{ $plat->cadence_notes }}</textarea>
                                            <button class="w-full py-1 rounded bg-slate-800 text-white text-xs font-bold">حفظ</button>
                                        </form>
                                    </details>
                                    <form method="post" action="{{ route('admin.moderator-marketing-plans.platforms.destroy', [$plan, $plat]) }}" onsubmit="return confirm('حذف المنصة؟');" class="mt-1">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-600 font-semibold">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-center text-slate-500">لا منصات — أضف منصة واربط التوصيف (مصمم / مونتاج).</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- إضافة حدث --}}
    <section class="rounded-2xl border bg-white shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b bg-slate-50 font-bold">إضافة حدث للجدول</div>
        <form method="post" action="{{ route('admin.moderator-marketing-plans.events.store', $plan) }}" class="p-4 grid md:grid-cols-12 gap-3">
            @csrf
            <div class="md:col-span-4"><label class="text-xs font-semibold">العنوان *</label><input type="text" name="title" required class="w-full rounded-xl border px-3 py-2 text-sm"></div>
            <div class="md:col-span-2"><label class="text-xs font-semibold">النوع *</label>
                <select name="content_type" required class="w-full rounded-xl border px-3 py-2 text-sm">
                    @foreach($contentTypes as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach
                </select>
            </div>
            <div class="md:col-span-2"><label class="text-xs font-semibold">البداية *</label><input type="datetime-local" name="starts_at" required class="w-full rounded-xl border px-3 py-2 text-sm"></div>
            <div class="md:col-span-2"><label class="text-xs font-semibold">الحالة</label>
                <select name="status" class="w-full rounded-xl border px-3 py-2 text-sm">
                    @foreach(['idea','draft','scheduled','published','skipped'] as $v)<option value="{{ $v }}" @selected($v==='scheduled')>{{ $evtStatus($v) }}</option>@endforeach
                </select>
            </div>
            <div class="md:col-span-2"><label class="text-xs font-semibold">مسؤول (اختياري)</label>
                <select name="assigned_employee_id" class="w-full rounded-xl border px-3 py-2 text-sm">
                    <option value="">تلقائي حسب المنصة/النوع</option>
                    @foreach($employees as $emp)<option value="{{ $emp->id }}">{{ $emp->name }}</option>@endforeach
                </select>
            </div>
            <div class="md:col-span-4"><label class="text-xs font-semibold">منصة</label>
                <select name="platform_id" class="w-full rounded-xl border px-3 py-2 text-sm">
                    <option value="">— عام —</option>
                    @foreach($plan->platforms as $plat)<option value="{{ $plat->id }}">{{ $plat->displayName() }}</option>@endforeach
                </select>
            </div>
            <div class="md:col-span-6"><label class="text-xs font-semibold">تفاصيل</label><textarea name="body" rows="2" class="w-full rounded-xl border px-3 py-2 text-sm"></textarea></div>
            <div class="md:col-span-2 flex items-end"><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="requires_confirmation" value="1" checked class="rounded"> يتطلب تأكيد تنفيذ</label></div>
            <div class="md:col-span-12"><button type="submit" class="px-5 py-2.5 bg-pink-600 text-white rounded-xl text-sm font-bold">إضافة + توجيه تلقائي</button></div>
        </form>
    </section>

    {{-- أحداث --}}
    <section class="rounded-2xl border bg-white shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b font-bold">جدول المحتوى ({{ $plan->calendarEvents->count() }})</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="text-right px-3 py-2">الوقت</th>
                        <th class="text-right px-3 py-2">العنوان</th>
                        <th class="text-right px-3 py-2">النوع</th>
                        <th class="text-right px-3 py-2">المنصة</th>
                        <th class="text-right px-3 py-2">المسؤول</th>
                        <th class="text-right px-3 py-2">مهمة</th>
                        <th class="text-right px-3 py-2">التأكيد</th>
                        <th class="text-right px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($plan->calendarEvents as $ev)
                        <tr class="hover:bg-slate-50">
                            <td class="px-3 py-2 whitespace-nowrap">{{ $ev->starts_at->format('Y-m-d H:i') }}</td>
                            <td class="px-3 py-2 font-semibold">{{ $ev->title }}</td>
                            <td class="px-3 py-2">{{ $contentTypes[$ev->content_type] ?? $ev->content_type ?? 'post' }}</td>
                            <td class="px-3 py-2">{{ $ev->platform?->displayName() ?? '—' }}</td>
                            <td class="px-3 py-2">{{ $ev->assignee?->name ?? '—' }}</td>
                            <td class="px-3 py-2">@if($ev->employeeTask)<a href="{{ route('admin.employee-tasks.show', $ev->employeeTask) }}" class="text-violet-700 font-bold">#{{ $ev->employeeTask->id }}</a>@else — @endif</td>
                            <td class="px-3 py-2">
                                @if($ev->isConfirmed())
                                    <span class="text-emerald-700 font-bold"><i class="fas fa-check"></i> {{ $ev->execution_confirmed_at?->format('m-d H:i') }}</span>
                                @elseif($ev->execution_penalty_deduction_id)
                                    <span class="text-rose-700 font-bold">غرامة</span>
                                @elseif($ev->requires_confirmation)
                                    <span class="text-amber-700 font-bold">معلق</span>
                                @else —
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                @if(!$ev->isConfirmed() && $ev->requires_confirmation)
                                    <form method="post" action="{{ route('admin.moderator-marketing-plans.events.confirm', [$plan, $ev]) }}">@csrf
                                        <button class="text-emerald-700 font-semibold">تأكيد</button>
                                    </form>
                                @endif
                                <details class="mt-1">
                                    <summary class="cursor-pointer text-xs text-pink-700 font-semibold">تعديل</summary>
                                    <form method="post" action="{{ route('admin.moderator-marketing-plans.events.update', [$plan, $ev]) }}" class="mt-2 space-y-2 p-3 border rounded-lg min-w-[240px]">
                                        @csrf @method('PUT')
                                        <input type="text" name="title" value="{{ $ev->title }}" required class="w-full rounded border px-2 py-1 text-xs">
                                        <select name="content_type" class="w-full rounded border px-2 py-1 text-xs">
                                            @foreach($contentTypes as $k => $l)
                                                <option value="{{ $k }}" @selected($ev->content_type === $k)>{{ $l }}</option>
                                            @endforeach
                                        </select>
                                        <input type="datetime-local" name="starts_at" value="{{ $ev->starts_at->format('Y-m-d\TH:i') }}" required class="w-full rounded border px-2 py-1 text-xs">
                                        <select name="platform_id" class="w-full rounded border px-2 py-1 text-xs">
                                            <option value="">— عام —</option>
                                            @foreach($plan->platforms as $plat)
                                                <option value="{{ $plat->id }}" @selected((int)$ev->platform_id === (int)$plat->id)>{{ $plat->displayName() }}</option>
                                            @endforeach
                                        </select>
                                        <select name="status" class="w-full rounded border px-2 py-1 text-xs">
                                            @foreach(['idea','draft','scheduled','published','skipped'] as $v)
                                                <option value="{{ $v }}" @selected($ev->status === $v)>{{ $evtStatus($v) }}</option>
                                            @endforeach
                                        </select>
                                        <label class="flex items-center gap-1 text-xs"><input type="checkbox" name="requires_confirmation" value="1" @checked($ev->requires_confirmation) class="rounded"> يتطلب تأكيد</label>
                                        <button class="w-full py-1 rounded bg-slate-800 text-white text-xs font-bold">حفظ</button>
                                    </form>
                                </details>
                                <form method="post" action="{{ route('admin.moderator-marketing-plans.events.destroy', [$plan, $ev]) }}" onsubmit="return confirm('حذف الحدث؟');" class="mt-1">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-red-600 font-semibold">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
