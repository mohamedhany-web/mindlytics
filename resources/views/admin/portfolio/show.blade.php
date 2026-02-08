@extends('layouts.admin')

@section('title', $project->title . ' - البورتفوليو')
@section('header', 'عرض المشروع')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-2xl bg-green-50 border-2 border-green-200 px-6 py-4 flex items-center gap-3">
            <i class="fas fa-check-circle text-green-600 text-xl"></i>
            <span class="font-bold text-green-800">{{ session('success') }}</span>
        </div>
    @endif

    <a href="{{ route('admin.portfolio.index') }}" class="inline-flex items-center gap-2 text-blue-600 hover:underline font-bold">
        <i class="fas fa-arrow-right"></i>
        العودة للقائمة
    </a>

    <div class="bg-white rounded-2xl border-2 border-gray-200 overflow-hidden shadow-lg">
        @if($project->image_path)
            <div class="aspect-video bg-gray-100">
                <img src="{{ asset($project->image_path) }}" alt="{{ $project->title }}" class="w-full h-full object-cover">
            </div>
        @endif
        <div class="p-8">
            <h1 class="text-2xl font-black text-gray-900 mb-4">{{ $project->title }}</h1>
            @if($project->description)
                <div class="prose text-gray-600 mb-6">{!! nl2br(e($project->description)) !!}</div>
            @endif
            @if($project->project_url)
                <p class="mb-4"><a href="{{ $project->project_url }}" target="_blank" rel="noopener" class="text-blue-600 hover:underline font-bold">{{ $project->project_url }}</a></p>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 text-sm">
                <p><strong>الطالب:</strong> {{ $project->user->name ?? '—' }}</p>
                <p><strong>المسار:</strong> {{ $project->academicYear->name ?? '—' }}</p>
                <p><strong>الحالة:</strong> {{ $project->status }}</p>
                <p><strong>ظاهر في المعرض:</strong> {{ $project->is_visible ? 'نعم' : 'لا' }}</p>
                @if($project->reviewer)
                    <p><strong>راجع من:</strong> {{ $project->reviewer->name }}</p>
                @endif
            </div>

            <form action="{{ route('admin.portfolio.toggle-visibility', $project) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 {{ $project->is_visible ? 'bg-amber-600 hover:bg-amber-700' : 'bg-green-600 hover:bg-green-700' }} text-white px-6 py-2.5 rounded-xl font-bold">
                    {{ $project->is_visible ? 'إخفاء من المعرض' : 'إظهار في المعرض' }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
