@extends('layouts.admin')

@section('title', 'مجموعات العملاء')
@section('header', 'مجموعات العملاء — المبيعات')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <p class="text-sm text-slate-600">إنشاء مجموعات مشتركة لموظف واحد أو أكثر — كل موظف يرى عملاءه ضمن المجموعة</p>
        <a href="{{ route('admin.sales.groups.create') }}" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold">+ مجموعة</a>
    </div>
    @if(session('success'))<div class="text-sm text-emerald-700">{{ session('success') }}</div>@endif
    <div class="bg-white border rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="text-right p-3">المجموعة</th>
                <th class="text-right p-3">الموظفون</th>
                <th class="text-right p-3">عملاء</th>
                <th class="text-right p-3"></th>
            </tr></thead>
            <tbody>
                @forelse($groups as $g)
                <tr class="border-t">
                    <td class="p-3 font-semibold">{{ $g->name }}</td>
                    <td class="p-3 text-xs text-slate-600">
                        @if($g->members->isNotEmpty())
                            {{ $g->members->pluck('name')->implode('، ') }}
                        @else
                            {{ $g->assignee->name ?? '—' }}
                        @endif
                    </td>
                    <td class="p-3">{{ $g->leads_count }}</td>
                    <td class="p-3"><a href="{{ route('admin.sales.groups.show', $g) }}" class="text-sky-700 font-semibold">إدارة</a></td>
                </tr>
                @empty
                <tr><td colspan="4" class="p-6 text-center text-slate-500">لا توجد مجموعات</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3">{{ $groups->links() }}</div>
    </div>
</div>
@endsection
