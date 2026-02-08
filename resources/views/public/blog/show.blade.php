@extends('layouts.public')

@section('title', $post->title . ' - Mindlytics')
@section('content')
<!-- Hero Section -->
<section class="hero-gradient min-h-[40vh] flex items-center relative overflow-hidden pt-28">
    <div class="container mx-auto px-4 text-center relative z-10">
        <h1 class="text-4xl md:text-5xl font-extrabold text-white leading-tight mb-4 animate-fade-in">
            {{ $post->title }}
        </h1>
    </div>
</section>

<!-- Post Content -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Back Button -->
            <a href="{{ route('public.blog.index') }}" class="inline-flex items-center text-sky-600 hover:text-sky-700 mb-6 btn-outline">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة للمدونة
            </a>

            <!-- Post Header -->
            <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg p-8 mb-8 border border-gray-200 card-hover">
                <div class="flex items-center text-gray-600 mb-6 flex-wrap gap-4">
                    <i class="fas fa-user ml-2"></i>
                    <span class="ml-2">{{ $post->author->name ?? 'غير معروف' }}</span>
                    <i class="fas fa-calendar mr-4 ml-6"></i>
                    <span>{{ $post->published_at->format('Y-m-d') }}</span>
                    <i class="fas fa-eye mr-4 ml-6"></i>
                    <span>{{ $post->views_count }} مشاهدة</span>
                </div>
                @if($post->tags)
                <div class="flex flex-wrap gap-2">
                    @foreach($post->tags as $tag)
                    <span class="px-3 py-1 bg-sky-100 dark:bg-sky-900 text-sky-700 dark:text-sky-300 rounded-full text-sm">{{ $tag }}</span>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Featured Image -->
            @if($post->featured_image)
            <div class="mb-8">
                <img src="{{ asset($post->featured_image) }}" alt="{{ $post->title }}" class="w-full rounded-xl shadow-lg max-h-96 object-cover" loading="lazy" decoding="async" onerror="this.style.display='none'">
            </div>
            @else
            <div class="mb-8 w-full h-64 bg-gradient-to-br from-sky-100 to-slate-100 rounded-xl shadow-lg flex items-center justify-center">
                <i class="fas fa-image text-6xl text-gray-400"></i>
            </div>
            @endif

            <!-- Post Content -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8 border border-gray-200 card-hover">
                <div class="prose prose-lg max-w-none">
                    {!! $post->content !!}
                </div>
            </div>

            <!-- Related Posts -->
            @if($relatedPosts->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-200 card-hover">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">مقالات ذات صلة</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relatedPosts as $relatedPost)
                    <a href="{{ route('public.blog.show', $relatedPost->slug) }}" class="block hover:shadow-lg transition-shadow">
                        @if($relatedPost->featured_image)
                        <img src="{{ asset($relatedPost->featured_image) }}" alt="{{ $relatedPost->title }}" class="w-full h-32 object-cover rounded-lg mb-3" onerror="this.style.display='none'">
                        @else
                        <div class="w-full h-32 bg-gradient-to-br from-sky-100 to-slate-100 rounded-lg mb-3 flex items-center justify-center">
                            <i class="fas fa-image text-2xl text-gray-400"></i>
                        </div>
                        @endif
                        <h3 class="font-bold text-gray-900 mb-2">{{ $relatedPost->title }}</h3>
                        <p class="text-sm text-gray-600">{{ Str::limit($relatedPost->excerpt ?? strip_tags($relatedPost->content), 80) }}</p>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection


