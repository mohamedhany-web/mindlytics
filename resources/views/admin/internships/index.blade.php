@extends('layouts.admin')

@section('title', 'التدريب - Internships')
@section('header', 'قسم التدريب')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-800 font-semibold">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">فرص التدريب (Internships)</h1>
            <p class="text-slate-500 text-sm mt-1">إدارة فرص التدريب وطلبات التقديم من الصفحة العامة.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.internship-applications.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-800 text-sm font-semibold">طلبات التقديم ({{ $stats['pending'] }} معلّق)</a>
            <a href="{{ route('public.internships.index') }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-slate-800 text-white text-sm font-semibold">الصفحة العامة</a>
            <a href="{{ route('admin.internships.create') }}" class="px-4 py-2.5 rounded-xl bg-sky-600 text-white text-sm font-semibold">+ فرصة جديدة</a>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="text-xs font-semibold text-slate-500">الكل</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
            <div class="text-xs font-semibold text-emerald-700">مفتوحة</div>
            <div class="text-2xl font-black text-emerald-900 mt-1">{{ number_format($stats['open']) }}</div>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
            <div class="text-xs font-semibold text-amber-700">مسودات</div>
            <div class="text-2xl font-black text-amber-900 mt-1">{{ number_format($stats['draft']) }}</div>
        </div>
        <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4">
            <div class="text-xs font-semibold text-sky-700">كل الطلبات</div>
            <div class="text-2xl font-black text-sky-900 mt-1">{{ number_format($stats['applications']) }}</div>
        </div>
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
            <div class="text-xs font-semibold text-rose-700">قيد الانتظار</div>
            <div class="text-2xl font-black text-rose-900 mt-1">{{ number_format($stats['pending']) }}</div>
        </div>
    </div>

    <form method="GET" class="bg-white border border-slate-200 rounded-2xl p-4 grid grid-cols-1 md:grid-cols-4 gap-3">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="بحث بالعنوان / القسم / الموقع" class="rounded-xl border-slate-200 text-sm">
        <select name="status" class="rounded-xl border-slate-200 text-sm">
            <option value="">كل الحالات</option>
            @foreach(\App\Models\Internship::statuses() as $key => $label)
                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="published" class="rounded-xl border-slate-200 text-sm">
            <option value="">النشر: الكل</option>
            <option value="1" @selected(request('published') === '1')>منشور</option>
            <option value="0" @selected(request('published') === '0')>غير منشور</option>
        </select>
        <button class="rounded-xl bg-slate-800 text-white text-sm font-semibold px-4 py-2">تصفية</button>
    </form>

    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 font-bold">العنوان</th>
                        <th class="px-4 py-3 font-bold">النوع</th>
                        <th class="px-4 py-3 font-bold">الحالة</th>
                        <th class="px-4 py-3 font-bold">الطلبات</th>
                        <th class="px-4 py-3 font-bold">الموعد النهائي</th>
                        <th class="px-4 py-3 font-bold">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($internships as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900">{{ $item->title }}</div>
                                <div class="text-xs text-slate-500">{{ $item->department }} @if($item->is_featured)· Featured @endif</div>
                            </td>
                            <td class="px-4 py-3">{{ $item->typeLabel() }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-lg text-xs font-bold bg-slate-100">{{ $item->statusLabel() }}</span>
                                @if($item->is_published)
                                    <span class="px-2 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700">منشور</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-bold">{{ $item->applications_count }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $item->application_deadline?->format('Y-m-d') ?: '—' }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.internships.edit', $item) }}" class="text-sky-700 font-semibold hover:underline">تعديل</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">لا توجد فرص تدريب بعد.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $internships->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
