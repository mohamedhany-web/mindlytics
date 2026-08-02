@extends('layouts.admin')

@section('title', 'ربط يوزرز Meta Business Suite')
@section('header', 'ربط موظفي Meta')

@section('content')
@php
    $smBtnPrimary = 'inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold';
    $smBtnSecondary = 'inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50';
@endphp

<div class="space-y-5">
    @include('admin.meta-social._alerts')

    <div class="rounded-2xl border border-sky-200 bg-sky-50/80 p-4 sm:p-5">
        <h1 class="text-lg font-black text-slate-900">ربط يوزرز أكسس Meta ↔ موظفي النظام</h1>
        <p class="text-sm text-slate-600 mt-1 leading-relaxed">
            اللي بتديهم أكسس على الصفحة من <strong>Meta Business Suite</strong> بيظهروا هنا بعد المزامنة.
            اختار مقابل كل يوزر Meta الموظف عندك في Mindlytics — عشان التعيين والتقارير في الإنبوكس.
        </p>
        <p class="text-xs text-amber-800 mt-2 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
            ملاحظة: الربط هنا للمطابقة داخل نظامنا. الرد من تطبيق Business Suite نفسه غالبًا مش بيرجع اسم الموظف من Meta API —
            عشان التقارير الدقيقة خلّيهم يردّوا من إنبوكس Mindlytics بعد الربط.
        </p>
    </div>

    @if(! ($ready ?? false))
        <div class="rounded-xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900">
            شغّل على السيرفر: <code class="bg-white px-2 py-1 rounded">php artisan migrate --force</code>
        </div>
    @else
        <div class="flex flex-wrap gap-2 items-end">
            <form method="post" action="{{ route('admin.meta-social.agents.sync') }}" class="flex flex-wrap gap-2 items-end">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">الصفحة</label>
                    <select name="page_id" class="rounded-xl border border-slate-200 px-3 py-2 text-sm bg-white min-w-[12rem]">
                        <option value="">كل الصفحات النشطة</option>
                        @foreach($pages as $p)
                            <option value="{{ $p->id }}">{{ $p->page_name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="{{ $smBtnPrimary }}"><i class="fab fa-meta"></i> مزامنة من Meta</button>
            </form>
            <a href="{{ route('admin.meta-social.inbox.index') }}" class="{{ $smBtnSecondary }}"><i class="fas fa-inbox"></i> Inbox</a>
            <a href="{{ route('admin.employees.index') }}" class="{{ $smBtnSecondary }}"><i class="fas fa-users"></i> الموظفين</a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <h2 class="font-black text-slate-900 text-sm">يوزرز الأكسس</h2>
                <span class="text-xs font-bold text-slate-500">{{ $links->count() }} يوزر</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                    <tr>
                        <th class="text-start px-4 py-3">يوزر Meta</th>
                        <th class="text-start px-4 py-3">Meta ID</th>
                        <th class="text-start px-4 py-3">الصلاحيات</th>
                        <th class="text-start px-4 py-3">الموظف في النظام</th>
                        <th class="text-start px-4 py-3"></th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse($links as $link)
                        <tr class="{{ $link->isLinked() ? 'bg-emerald-50/40' : '' }}">
                            <td class="px-4 py-3">
                                <p class="font-bold text-slate-900">{{ $link->meta_user_name ?: '—' }}</p>
                                @if($link->meta_user_email)
                                    <p class="text-xs text-slate-500" dir="ltr">{{ $link->meta_user_email }}</p>
                                @endif
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $link->source }} · {{ $link->last_synced_at?->diffForHumans() ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs" dir="ltr">{{ $link->meta_user_id }}</td>
                            <td class="px-4 py-3">
                                @php $tasks = is_array($link->tasks) ? $link->tasks : []; @endphp
                                @if($tasks === [])
                                    <span class="text-slate-400">—</span>
                                @else
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($tasks as $t)
                                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-sky-100 text-sky-800">{{ $t }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3" colspan="2">
                                <form method="post" action="{{ route('admin.meta-social.agents.update', $link) }}" class="flex flex-wrap gap-2 items-center">
                                    @csrf
                                    @method('PUT')
                                    <select name="user_id" class="rounded-xl border border-slate-200 px-3 py-2 text-sm bg-white min-w-[14rem]">
                                        <option value="">— غير مربوط —</option>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}" @selected((int)$link->user_id === (int)$emp->id)>
                                                {{ $emp->name }}
                                                @if($emp->employeeJob) — {{ $emp->employeeJob->name }} @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="px-3 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-black">حفظ الربط</button>
                                    @if($link->isLinked())
                                        <span class="text-[10px] font-bold text-emerald-700"><i class="fas fa-check-circle"></i> مربوط</span>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                                لا يوجد يوزرز بعد — اضغط «مزامنة من Meta» لجلب اللي عندهم أكسس على الصفحة.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 space-y-3">
            <h2 class="font-black text-slate-900 text-sm">إضافة يدوية</h2>
            <p class="text-xs text-slate-500">لو المزامنة مرجعتش يوزر معيّن، ضيف Meta User ID يدويًا (من Business Suite أو من إعدادات الصفحة).</p>
            <form method="post" action="{{ route('admin.meta-social.agents.store') }}" class="grid sm:grid-cols-4 gap-3 items-end">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Meta User ID</label>
                    <input type="text" name="meta_user_id" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" dir="ltr" placeholder="1000…">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">الاسم (اختياري)</label>
                    <input type="text" name="meta_user_name" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="اسمه في Meta">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">الموظف</label>
                    <select name="user_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-white">
                        <option value="">— لاحقًا —</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="{{ $smBtnPrimary }} justify-center">إضافة</button>
            </form>
        </div>
    @endif
</div>
@endsection
