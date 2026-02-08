@extends('layouts.app')

@section('title', 'موارد الكورس - ' . $offlineCourse->title)
@section('header', 'موارد الكورس الأوفلاين')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="mb-4">
        <a href="{{ route('student.offline-courses.show', $offlineCourse) }}" class="inline-flex items-center text-sky-600 hover:text-sky-700 text-sm font-medium">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة لصفحة الكورس
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-gray-100">
            <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-file-alt text-sky-500"></i>
                موارد الكورس (أوفلاين) — {{ $offlineCourse->title }}
            </h1>
        </div>
        @if($resources->isEmpty())
            <div class="p-12 text-center text-gray-500">
                <i class="fas fa-folder-open text-4xl mb-3 opacity-50"></i>
                <p>لا توجد موارد متاحة حالياً.</p>
            </div>
        @else
            <ul class="divide-y divide-gray-100">
                @foreach($resources as $resource)
                    <li class="p-4 sm:p-5 hover:bg-gray-50/50">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $resource->title }}</h3>
                                @if($resource->description)
                                    <p class="text-sm text-gray-600 mt-1">{{ Str::limit($resource->description, 150) }}</p>
                                @endif
                            </div>
                            <div class="flex-shrink-0 flex flex-wrap gap-2 justify-end">
                                @if($resource->type === 'link' && $resource->url)
                                    <a href="{{ $resource->url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-600 text-white rounded-lg font-medium hover:bg-sky-700">
                                        <i class="fas fa-external-link-alt"></i>
                                        فتح الرابط
                                    </a>
                                @else
                                    @foreach($resource->getAllFiles() as $file)
                                        <a href="{{ asset('storage/' . $file['path']) }}" download="{{ $file['name'] ?? 'download' }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-sky-100 text-sky-700 rounded-lg text-sm font-medium hover:bg-sky-200">
                                            <i class="fas fa-download"></i>
                                            {{ Str::limit($file['name'] ?? 'تحميل', 25) }}
                                        </a>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
