@extends('layouts.admin')

@section('title', 'رقابة الموظفين')
@section('header', 'رقابة الموظفين')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">رقابة الموظفين</h1>
        <form method="GET" class="mb-6">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="البحث..." class="px-4 py-2 border border-gray-300 rounded-lg">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">بحث</button>
        </form>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الموظف</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجمالي المهام</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">مكتملة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">معلقة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">متأخرة</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($employees as $employee)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $employee->name }}</td>
                        <td class="px-6 py-4">{{ $employee->tasks_count }}</td>
                        <td class="px-6 py-4">{{ $employee->completed_tasks }}</td>
                        <td class="px-6 py-4">{{ $employee->pending_tasks }}</td>
                        <td class="px-6 py-4 text-red-600 font-semibold">{{ $employee->overdue_tasks }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $employees->links() }}</div>
    </div>
</div>
@endsection
