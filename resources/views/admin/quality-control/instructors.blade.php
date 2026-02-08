@extends('layouts.admin')

@section('title', 'رقابة المدربين')
@section('header', 'رقابة المدربين')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">رقابة المدربين</h1>
        <form method="GET" class="mb-6">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="البحث..." class="px-4 py-2 border border-gray-300 rounded-lg">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">بحث</button>
        </form>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المدرب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الكورسات</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الاتفاقيات</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">آخر نشاط</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($instructors as $instructor)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $instructor->name }}</td>
                        <td class="px-6 py-4">{{ $instructor->courses_count }}</td>
                        <td class="px-6 py-4">{{ $instructor->agreements_count }}</td>
                        <td class="px-6 py-4">{{ $instructor->last_activity ? $instructor->last_activity->diffForHumans() : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $instructors->links() }}</div>
    </div>
</div>
@endsection
