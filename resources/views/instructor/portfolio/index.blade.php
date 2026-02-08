@extends('layouts.app')

@section('title', 'مراجعة البورتفوليو')
@section('header', 'مراجعة مشاريع البورتفوليو')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-2xl bg-green-50 border-2 border-green-200 px-6 py-4 flex items-center gap-3">
            <i class="fas fa-check-circle text-green-600 text-xl"></i>
            <span class="font-bold text-green-800">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl bg-red-50 border-2 border-red-200 px-6 py-4 flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
            <span class="font-bold text-red-800">{{ session('error') }}</span>
        </div>
    @endif

    <div class="flex flex-wrap gap-2 mb-4">
        <a href="{{ route('instructor.portfolio.index') }}" class="px-4 py-2 rounded-xl text-sm font-bold {{ !request('status') ? 'bg-[#2CA9BD] text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">الكل</a>
        <a href="{{ route('instructor.portfolio.index', ['status' => 'pending_review']) }}" class="px-4 py-2 rounded-xl text-sm font-bold {{ request('status') === 'pending_review' ? 'bg-amber-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">قيد المراجعة</a>
        <a href="{{ route('instructor.portfolio.index', ['status' => 'approved']) }}" class="px-4 py-2 rounded-xl text-sm font-bold {{ request('status') === 'approved' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">معتمد</a>
        <a href="{{ route('instructor.portfolio.index', ['status' => 'published']) }}" class="px-4 py-2 rounded-xl text-sm font-bold {{ request('status') === 'published' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">منشور</a>
    </div>

    @if($projects->count() > 0)
        <div class="bg-white rounded-2xl border-2 border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-sm font-bold text-gray-900">المشروع</th>
                            <th class="px-4 py-3 text-sm font-bold text-gray-900">الطالب</th>
                            <th class="px-4 py-3 text-sm font-bold text-gray-900">المسار</th>
                            <th class="px-4 py-3 text-sm font-bold text-gray-900">الحالة</th>
                            <th class="px-4 py-3 text-sm font-bold text-gray-900">إجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($projects as $project)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <a href="{{ route('instructor.portfolio.show', $project) }}" class="font-bold text-[#2CA9BD] hover:underline">{{ $project->title }}</a>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $project->user->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $project->academicYear->name ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusLabels = ['pending_review' => 'قيد المراجعة', 'approved' => 'معتمد', 'rejected' => 'مرفوض', 'published' => 'منشور'];
                                        $statusClass = ['pending_review' => 'bg-amber-100 text-amber-800', 'approved' => 'bg-blue-100 text-blue-800', 'rejected' => 'bg-red-100 text-red-800', 'published' => 'bg-green-100 text-green-800'];
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $statusClass[$project->status] ?? 'bg-gray-100' }}">{{ $statusLabels[$project->status] ?? $project->status }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('instructor.portfolio.show', $project) }}" class="text-[#2CA9BD] hover:underline text-sm font-bold">عرض</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-200">{{ $projects->withQueryString()->links() }}</div>
        </div>
    @else
        <div class="bg-white rounded-2xl border-2 border-dashed border-gray-300 p-12 text-center">
            <i class="fas fa-folder-open text-5xl text-gray-400 mb-4"></i>
            <p class="text-gray-600 text-lg">لا توجد مشاريع في هذه الفئة.</p>
        </div>
    @endif
</div>
@endsection
