@extends('layouts.app')

@section('title', 'المسارات التعليمية')
@section('header', 'المسارات التعليمية')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-50 via-blue-50 to-green-50 rounded-2xl p-6 border-2 border-green-200 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-black text-gray-900 mb-2">المسارات التعليمية</h1>
                <p class="text-gray-600">إدارة وترتيب المسارات التعليمية التي تدرب فيها</p>
            </div>
        </div>
    </div>

    <!-- Learning Paths Grid -->
    @if($learningPaths->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($learningPaths as $path)
            <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-200 hover:border-green-300 transition-all duration-300 overflow-hidden group">
                <div class="h-48 bg-gradient-to-br from-green-500 via-blue-500 to-green-600 flex items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-black/10 group-hover:bg-black/20 transition-colors"></div>
                    @if($path->icon)
                        <i class="{{ $path->icon }} text-white text-6xl relative z-10 group-hover:scale-110 transition-transform"></i>
                    @else
                        <i class="fas fa-route text-white text-6xl relative z-10 group-hover:scale-110 transition-transform"></i>
                    @endif
                </div>
                
                <div class="p-6">
                    <h3 class="text-xl font-black text-gray-900 mb-2 group-hover:text-green-600 transition-colors">
                        {{ $path->name }}
                    </h3>
                    @if($path->description)
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ Str::limit($path->description, 100) }}</p>
                    @endif
                    
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-4 text-sm text-gray-600">
                            <span class="flex items-center gap-1">
                                <i class="fas fa-layer-group text-blue-600"></i>
                                {{ $path->academic_subjects_count }} مادة
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="fas fa-book text-green-600"></i>
                                {{ $path->linked_courses_count }} كورس
                            </span>
                        </div>
                    </div>
                    
                    <a href="{{ route('instructor.learning-path.show', $path->slug) }}" 
                       class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white px-4 py-3 rounded-xl font-bold transition-all duration-300 shadow-lg hover:shadow-xl">
                        <i class="fas fa-cog"></i>
                        <span>إدارة المسار</span>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-route text-gray-400 text-2xl"></i>
            </div>
            <p class="text-gray-600 text-lg">لا توجد مسارات تعليمية مخصصة لك حالياً</p>
        </div>
    @endif
</div>
@endsection
