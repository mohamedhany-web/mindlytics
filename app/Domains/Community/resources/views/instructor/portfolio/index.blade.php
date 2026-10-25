@extends('layouts.app')

@section('title', __('instructor.review_projects') . ' - Mindlytics')
@section('header', __('instructor.portfolio'))

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
        <a href="{{ route('instructor.portfolio.index') }}" class="px-4 py-2 rounded-xl text-sm font-bold {{ !request('status') ? 'bg-[#2CA9BD] text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">{{ __('instructor.all') }}</a>
        <a href="{{ route('instructor.portfolio.index', ['status' => 'pending_review']) }}" class="px-4 py-2 rounded-xl text-sm font-bold {{ request('status') === 'pending_review' ? 'bg-amber-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">{{ __('instructor.pending_review') }}</a>
        <a href="{{ route('instructor.portfolio.index', ['status' => 'resubmitted']) }}" class="px-4 py-2 rounded-xl text-sm font-bold {{ request('status') === 'resubmitted' ? 'bg-indigo-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">أُعيد إرساله</a>
        <a href="{{ route('instructor.portfolio.index', ['status' => 'changes_requested']) }}" class="px-4 py-2 rounded-xl text-sm font-bold {{ request('status') === 'changes_requested' ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">يحتاج تعديلات</a>
        <a href="{{ route('instructor.portfolio.index', ['status' => 'approved']) }}" class="px-4 py-2 rounded-xl text-sm font-bold {{ request('status') === 'approved' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">{{ __('instructor.approved') }}</a>
        <a href="{{ route('instructor.portfolio.index', ['status' => 'published']) }}" class="px-4 py-2 rounded-xl text-sm font-bold {{ request('status') === 'published' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">{{ __('instructor.published') }}</a>
    </div>

    @if($projects->count() > 0)
        <div class="bg-white rounded-2xl border-2 border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-sm font-bold text-gray-900">{{ __('instructor.project') }}</th>
                            <th class="px-4 py-3 text-sm font-bold text-gray-900">{{ __('instructor.student') }}</th>
                            <th class="px-4 py-3 text-sm font-bold text-gray-900">{{ __('instructor.path_name') }}</th>
                            <th class="px-4 py-3 text-sm font-bold text-gray-900">{{ __('common.status') }}</th>
                            <th class="px-4 py-3 text-sm font-bold text-gray-900">{{ __('instructor.action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($projects as $project)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <a href="{{ route('instructor.portfolio.show', $project) }}" class="font-bold text-[#2CA9BD] hover:underline">{{ $project->title }}</a>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $project->user->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $project->programContextLabel() ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusLabels = [
                                            'pending_review' => __('instructor.pending_review'),
                                            'resubmitted' => 'أُعيد إرساله',
                                            'changes_requested' => 'يحتاج تعديلات',
                                            'approved' => __('instructor.approved'),
                                            'rejected' => __('instructor.rejected'),
                                            'published' => __('instructor.published'),
                                            'draft' => 'مسودة',
                                        ];
                                        $statusClass = [
                                            'pending_review' => 'bg-amber-100 text-amber-800',
                                            'resubmitted' => 'bg-indigo-100 text-indigo-800',
                                            'changes_requested' => 'bg-orange-100 text-orange-800',
                                            'approved' => 'bg-blue-100 text-blue-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            'published' => 'bg-green-100 text-green-800',
                                            'draft' => 'bg-gray-100 text-gray-800',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $statusClass[$project->status] ?? 'bg-gray-100' }}">{{ $statusLabels[$project->status] ?? $project->status }}</span>
                                    @if($project->program_type)
                                        <div class="text-[10px] text-gray-500 mt-1">{{ $project->programTypeLabel() }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('instructor.portfolio.show', $project) }}" class="text-[#2CA9BD] hover:underline text-sm font-bold">{{ __('common.view') }}</a>
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
            <p class="text-gray-600 text-lg">{{ __('instructor.no_projects_in_category') }}</p>
        </div>
    @endif
</div>
@endsection
