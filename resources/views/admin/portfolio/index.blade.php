@extends('layouts.admin')

@section('title', 'البورتفوليو - الرقابة')
@section('header', 'البورتفوليو')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-2xl bg-green-50 border-2 border-green-200 px-6 py-4 flex items-center gap-3">
            <i class="fas fa-check-circle text-green-600 text-xl"></i>
            <span class="font-bold text-green-800">{{ session('success') }}</span>
        </div>
    @endif

    <p class="text-gray-600">مراقبة مشاريع البورتفوليو — يمكنك إخفاء أي مشروع من المعرض العام.</p>

    <div class="flex flex-wrap gap-2 mb-4">
        <a href="{{ route('admin.portfolio.index') }}" class="px-4 py-2 rounded-xl text-sm font-bold {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">الكل</a>
        <a href="{{ route('admin.portfolio.index', ['status' => 'published']) }}" class="px-4 py-2 rounded-xl text-sm font-bold {{ request('status') === 'published' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700' }}">منشور</a>
        <a href="{{ route('admin.portfolio.index', ['visible' => '1']) }}" class="px-4 py-2 rounded-xl text-sm font-bold {{ request('visible') === '1' ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-700' }}">ظاهر</a>
        <a href="{{ route('admin.portfolio.index', ['visible' => '0']) }}" class="px-4 py-2 rounded-xl text-sm font-bold {{ request('visible') === '0' ? 'bg-amber-600 text-white' : 'bg-gray-200 text-gray-700' }}">مخفي</a>
    </div>

    <div class="bg-white rounded-2xl border-2 border-gray-200 overflow-hidden shadow-lg">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-gray-50 border-b-2 border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-sm font-bold text-gray-900">المشروع</th>
                        <th class="px-4 py-3 text-sm font-bold text-gray-900">الطالب</th>
                        <th class="px-4 py-3 text-sm font-bold text-gray-900">المسار</th>
                        <th class="px-4 py-3 text-sm font-bold text-gray-900">الحالة</th>
                        <th class="px-4 py-3 text-sm font-bold text-gray-900">ظاهر</th>
                        <th class="px-4 py-3 text-sm font-bold text-gray-900">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($projects as $project)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.portfolio.show', $project) }}" class="font-bold text-blue-600 hover:underline">{{ $project->title }}</a>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $project->user->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $project->academicYear->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $statusLabels = ['pending_review' => 'قيد المراجعة', 'approved' => 'معتمد', 'rejected' => 'مرفوض', 'published' => 'منشور'];
                                @endphp
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-gray-100 text-gray-800">{{ $statusLabels[$project->status] ?? $project->status }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($project->is_visible)
                                    <span class="text-green-600 font-bold">نعم</span>
                                @else
                                    <span class="text-amber-600 font-bold">مخفي</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.portfolio.show', $project) }}" class="text-blue-600 hover:underline text-sm font-bold">عرض</a>
                                <form action="{{ route('admin.portfolio.toggle-visibility', $project) }}" method="POST" class="inline mr-2">
                                    @csrf
                                    <button type="submit" class="text-amber-600 hover:underline text-sm font-bold">{{ $project->is_visible ? 'إخفاء' : 'إظهار' }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-500">لا توجد مشاريع.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200">{{ $projects->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
