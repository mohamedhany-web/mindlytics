@extends('layouts.app')

@section('title', 'مشاريعي - البورتفوليو')
@section('header', 'مشاريعي')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    @if(session('success'))
    <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-600"></i>
        <span class="font-semibold text-emerald-800">{{ session('success') }}</span>
    </div>
    @endif

    <!-- الهيدر -->
    <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">مشاريعي</h1>
                <p class="text-sm text-gray-500">ارفع مشاريعك بعد إتمام الكورسات ليعرضها المدرب ثم تنشر في معرض Mindlytics Portfolio.</p>
            </div>
            <a href="{{ route('student.portfolio.create') }}" class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                <i class="fas fa-plus"></i>
                رفع مشروع جديد
            </a>
        </div>
    </div>

    @if($projects->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($projects as $project)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
            @if($project->image_path)
            <div class="aspect-video bg-gray-100">
                <img src="{{ asset($project->image_path) }}" alt="{{ $project->title }}" class="w-full h-full object-cover">
            </div>
            @else
            <div class="aspect-video bg-sky-50 flex items-center justify-center">
                <i class="fas fa-code text-3xl text-sky-300"></i>
            </div>
            @endif
            <div class="p-4">
                <h3 class="text-base font-bold text-gray-900 mb-2 line-clamp-2">{{ $project->title }}</h3>
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($project->description, 80) }}</p>
                @php
                    $statusLabels = [
                        'pending_review' => ['label' => 'قيد المراجعة', 'class' => 'bg-amber-100 text-amber-800'],
                        'approved' => ['label' => 'معتمد', 'class' => 'bg-sky-100 text-sky-800'],
                        'rejected' => ['label' => 'مرفوض', 'class' => 'bg-red-100 text-red-800'],
                        'published' => ['label' => 'منشور', 'class' => 'bg-emerald-100 text-emerald-800'],
                    ];
                    $s = $statusLabels[$project->status] ?? ['label' => $project->status, 'class' => 'bg-gray-100 text-gray-800'];
                @endphp
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $s['class'] }}">{{ $s['label'] }}</span>
                @if($project->academicYear)
                <p class="text-xs text-gray-500 mt-2">{{ $project->academicYear->name }}</p>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @if($projects->hasPages())
    <div class="flex justify-center mt-6">{{ $projects->links() }}</div>
    @endif
    @else
    <div class="bg-white rounded-xl border border-dashed border-gray-200 p-10 sm:p-12 text-center">
        <div class="w-16 h-16 bg-sky-100 rounded-2xl flex items-center justify-center mx-auto mb-4 text-sky-600">
            <i class="fas fa-briefcase text-2xl"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">لا توجد مشاريع بعد</h3>
        <p class="text-sm text-gray-500 mb-6 max-w-sm mx-auto">ارفع مشروعك الأول بعد إتمام أي كورس، وسيراجعه المدرب ثم ينشر في المعرض.</p>
        <a href="{{ route('student.portfolio.create') }}" class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors">
            <i class="fas fa-plus"></i>
            رفع مشروع
        </a>
    </div>
    @endif
</div>
@endsection
