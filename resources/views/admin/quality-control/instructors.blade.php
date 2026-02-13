@extends('layouts.admin')

@section('title', 'رقابة المدربين')
@section('header', 'رقابة المدربين')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-3xl shadow-lg p-6 border border-slate-200 overflow-hidden">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold text-slate-900">جميع المدربين — رقابة شاملة</h1>
            <p class="text-slate-500 text-sm">اضغط على اسم المدرب لعرض كل بياناته وتقاريره، أو صدّر تقرير Excel.</p>
        </div>
        <form method="GET" class="mb-6 flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم، البريد، الهاتف..." class="rounded-2xl border border-slate-200 px-4 py-2.5 text-sm w-64">
            <button type="submit" class="rounded-2xl bg-sky-600 hover:bg-sky-700 text-white px-4 py-2.5 text-sm font-semibold">بحث</button>
        </form>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-right">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase text-slate-500">
                        <th class="px-6 py-3">المدرب</th>
                        <th class="px-6 py-3">البريد / الهاتف</th>
                        <th class="px-6 py-3">الكورسات</th>
                        <th class="px-6 py-3">الاتفاقيات</th>
                        <th class="px-6 py-3">آخر نشاط</th>
                        <th class="px-6 py-3">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @foreach($instructors as $instructor)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.quality-control.instructors.show', $instructor) }}" class="font-semibold text-sky-600 hover:text-sky-700">{{ $instructor->name }}</a>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $instructor->email ?? '—' }}<br><span class="text-slate-500">{{ $instructor->phone ?? '—' }}</span></td>
                        <td class="px-6 py-4">{{ $instructor->courses_count }}</td>
                        <td class="px-6 py-4">{{ $instructor->agreements_count }}</td>
                        <td class="px-6 py-4 text-sm">{{ $instructor->last_activity ? $instructor->last_activity->diffForHumans() : '—' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('admin.quality-control.instructors.show', $instructor) }}" class="inline-flex items-center gap-1 rounded-xl bg-sky-100 text-sky-700 px-3 py-1.5 text-xs font-semibold hover:bg-sky-200">عرض التفاصيل</a>
                                <a href="{{ route('admin.quality-control.instructors.export', $instructor) }}" class="inline-flex items-center gap-1 rounded-xl bg-emerald-100 text-emerald-700 px-3 py-1.5 text-xs font-semibold hover:bg-emerald-200"><i class="fas fa-file-excel"></i> Excel</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 px-2">{{ $instructors->links() }}</div>
    </div>
</div>
@endsection
