@extends('layouts.admin')

@section('title', 'إضافات الموظفين')
@section('header', 'إضافات خارجية للموظفين')

@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold">{{ session('success') }}</div>@endif

    <section class="rounded-2xl bg-white border shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center"><i class="fas fa-plus-circle"></i></div>
                <div><h2 class="text-xl font-black">إضافات خارجية</h2><p class="text-xs text-slate-600">مكافآت، بدلات، حوافز — تُضاف لحساب الموظف</p></div>
            </div>
            <a href="{{ route('admin.employee-additions.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-sm font-semibold"><i class="fas fa-plus ml-1"></i> إضافة جديدة</a>
        </div>
        <div class="grid grid-cols-3 gap-3 p-4">
            <div class="rounded-xl border p-4"><p class="text-xs">الإجمالي</p><p class="text-xl font-black">{{ $stats['total'] }}</p></div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4"><p class="text-xs">مطبقة</p><p class="text-xl font-black">{{ $stats['applied'] }}</p></div>
            <div class="rounded-xl border p-4"><p class="text-xs">مجموع المبالغ</p><p class="text-xl font-black">{{ number_format($stats['total_amount'], 2) }} ج.م</p></div>
        </div>
    </section>

    <div class="rounded-2xl bg-white border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="text-right px-4 py-2">الرقم</th>
                <th class="text-right px-4 py-2">الموظف</th>
                <th class="text-right px-4 py-2">العنوان</th>
                <th class="text-right px-4 py-2">المبلغ</th>
                <th class="text-right px-4 py-2">النوع</th>
                <th class="text-right px-4 py-2">الحالة</th>
            </tr></thead>
            <tbody class="divide-y">
                @foreach($additions as $a)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-2"><a href="{{ route('admin.employee-additions.show', $a) }}" class="text-emerald-700 font-mono text-xs">{{ $a->addition_number }}</a></td>
                    <td class="px-4 py-2">{{ $a->employee->name ?? '—' }}</td>
                    <td class="px-4 py-2">{{ $a->title }}</td>
                    <td class="px-4 py-2 font-bold text-emerald-700">{{ number_format($a->amount, 2) }}</td>
                    <td class="px-4 py-2">{{ \App\Models\EmployeeSalaryAddition::typeLabels()[$a->type] ?? $a->type }}</td>
                    <td class="px-4 py-2">{{ $a->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $additions->links() }}</div>
    </div>
</div>
@endsection
