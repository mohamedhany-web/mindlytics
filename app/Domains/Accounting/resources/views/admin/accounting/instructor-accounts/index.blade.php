@extends('layouts.admin')

@section('title', 'حسابات المدربين - المحاسبة')
@section('header', 'حسابات المدربين')

@section('content')
<div class="p-4 md:p-6 space-y-6" style="background: #f8fafc;">
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">حسابات المدربين</h1>
                <p class="text-slate-600 mt-1">رؤية كاملة: اتفاقيات، رواتب، مدفوعات، وأرباح نسبة الكورس لكل مدرب</p>
            </div>
            <a href="{{ route('admin.accounting.reports') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-medium">
                <i class="fas fa-chart-pie"></i>
                التقارير المحاسبية
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow p-6 border-2 border-slate-200">
            <p class="text-sm font-semibold text-slate-600 mb-1">عدد المدربين</p>
            <p class="text-2xl font-black text-slate-900">{{ $globalStats['instructors_count'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow p-6 border-2 border-amber-200">
            <p class="text-sm font-semibold text-slate-600 mb-1">إجمالي مطلوب الدفع</p>
            <p class="text-2xl font-black text-amber-700">{{ number_format($globalStats['pending_total'], 2) }} ج.م</p>
        </div>
        <div class="bg-white rounded-2xl shadow p-6 border-2 border-emerald-200">
            <p class="text-sm font-semibold text-slate-600 mb-1">إجمالي تم الدفع</p>
            <p class="text-2xl font-black text-emerald-700">{{ number_format($globalStats['paid_total'], 2) }} ج.م</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h2 class="text-lg font-bold text-slate-900">قائمة المدربين</h2>
            <form method="GET" action="{{ route('admin.accounting.instructor-accounts.index') }}" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو البريد أو الهاتف..." class="rounded-xl border border-slate-200 px-4 py-2 text-sm w-64">
                <button type="submit" class="px-4 py-2 bg-slate-700 text-white rounded-xl text-sm font-medium hover:bg-slate-800"><i class="fas fa-search ml-1"></i> بحث</button>
            </form>
        </div>
        @if($instructors->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-slate-100 text-sm text-slate-700">
                    <tr>
                        <th class="px-6 py-3">المدرب</th>
                        <th class="px-6 py-3">عدد الاتفاقيات</th>
                        <th class="px-6 py-3">مطلوب الدفع</th>
                        <th class="px-6 py-3">تم الدفع</th>
                        <th class="px-6 py-3">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($instructors as $instructor)
                    @php $stats = $statsByInstructor[$instructor->id] ?? []; @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <span class="font-medium text-slate-900">{{ $instructor->name }}</span>
                            @if($instructor->email)<span class="block text-xs text-slate-500">{{ $instructor->email }}</span>@endif
                            @if($instructor->phone)<span class="block text-xs text-slate-500">{{ $instructor->phone }}</span>@endif
                        </td>
                        <td class="px-6 py-4">{{ $stats['agreements_count'] ?? 0 }}</td>
                        <td class="px-6 py-4">
                            @if(($stats['pending_total'] ?? 0) > 0)
                                <span class="font-bold text-amber-700">{{ number_format($stats['pending_total'], 2) }} ج.م</span>
                                <span class="block text-xs text-slate-500">{{ $stats['pending_count'] ?? 0 }} مدفوعة</span>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if(($stats['paid_total'] ?? 0) > 0)
                                <span class="font-bold text-emerald-700">{{ number_format($stats['paid_total'], 2) }} ج.م</span>
                                <span class="block text-xs text-slate-500">{{ $stats['paid_count'] ?? 0 }} مدفوعة</span>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.accounting.instructor-accounts.show', $instructor) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-xl font-medium">
                                <i class="fas fa-file-invoice-dollar"></i>
                                عرض الحساب الكامل
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-12 text-center text-slate-500">
            <i class="fas fa-users text-4xl text-slate-300 mb-4"></i>
            <p class="font-medium">لا يوجد مدربون لديهم اتفاقيات أو مدفوعات.</p>
        </div>
        @endif
    </div>
</div>
@endsection
