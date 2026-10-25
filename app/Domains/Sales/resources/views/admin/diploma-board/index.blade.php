@extends('layouts.admin')

@section('title', 'توصيف الدبلومات')
@section('header', 'توصيف الدبلومات')

@section('content')
<div class="w-full space-y-6">
    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">توصيف الدبلومات</h1>
                <p class="text-slate-500 mt-1">{{ $total }} دبلومة · {{ $published }} صفحة منشورة</p>
            </div>
            <a href="{{ route('admin.sales-diploma-board.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white rounded-xl font-semibold shadow-lg shadow-emerald-500/25 transition-all">
                <i class="fas fa-plus"></i>
                <span>دبلومة جديدة</span>
            </a>
        </div>

        <div class="p-5 sm:p-8 space-y-6">
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800">{{ session('success') }}</div>
            @endif

            <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">بحث</label>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="اسم الدبلومة، المدرب، slug..."
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/30">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">الحالة</label>
                    <select name="status" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/30">
                        <option value="">الكل</option>
                        <option value="active" @selected(request('status') === 'active')>نشط</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>مخفي</option>
                    </select>
                </div>
                <div class="sm:col-span-3 flex gap-2">
                    <button class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-semibold">تصفية</button>
                    <a href="{{ route('admin.sales-diploma-board.index') }}" class="px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600">إعادة ضبط</a>
                </div>
            </form>

            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-right font-bold">#</th>
                            <th class="px-4 py-3 text-right font-bold">الدبلومة</th>
                            <th class="px-4 py-3 text-right font-bold">المدرب</th>
                            <th class="px-4 py-3 text-right font-bold">تاريخ البداية</th>
                            <th class="px-4 py-3 text-right font-bold">السعر</th>
                            <th class="px-4 py-3 text-right font-bold">الزوار</th>
                            <th class="px-4 py-3 text-right font-bold">Landing</th>
                            <th class="px-4 py-3 text-right font-bold">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($entries as $entry)
                            <tr class="hover:bg-slate-50/80 {{ ! $entry->is_active ? 'opacity-60' : '' }}">
                                <td class="px-4 py-3 text-slate-500">{{ $entry->sort_order }}</td>
                                <td class="px-4 py-3 font-bold text-slate-900">{{ $entry->name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $entry->instructor_name ?: '—' }}</td>
                                <td class="px-4 py-3 text-slate-600 whitespace-nowrap">{{ $entry->startDisplay() ?: '—' }}</td>
                                <td class="px-4 py-3 text-slate-800 whitespace-nowrap">{{ $entry->priceLabel() }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-700">{{ number_format($entry->visits_count) }}</td>
                                <td class="px-4 py-3">
                                    @if($entry->landingUrl())
                                        <a href="{{ $entry->landingUrl() }}" target="_blank" class="text-emerald-600 hover:underline font-semibold text-xs">فتح</a>
                                    @else
                                        <span class="text-xs text-amber-600 font-semibold">غير منشور</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <a href="{{ route('admin.sales-diploma-board.edit', $entry) }}" class="text-sky-600 hover:underline font-semibold">تعديل</a>
                                    <form method="POST" action="{{ route('admin.sales-diploma-board.destroy', $entry) }}" class="inline" onsubmit="return confirm('حذف توصيف هذه الدبلومة؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:underline font-semibold mr-2">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-slate-500">لا توجد دبلومات بعد. ابدأ بإضافة توصيف جديد.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $entries->links() }}
        </div>
    </div>
</div>
@endsection
