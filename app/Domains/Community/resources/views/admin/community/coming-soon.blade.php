@extends('layouts.admin')

@php
    $sectionTitles = [
        'competitions' => __('admin.community_competitions'),
        'datasets' => __('admin.community_datasets'),
        'submissions' => __('admin.community_submissions'),
        'discussions' => __('admin.community_discussions'),
        'settings' => __('admin.community_settings'),
    ];
    $sectionIcons = [
        'competitions' => 'fa-trophy',
        'datasets' => 'fa-database',
        'submissions' => 'fa-paper-plane',
        'discussions' => 'fa-comments',
        'settings' => 'fa-cog',
    ];
    $title = $sectionTitles[$section] ?? $section;
    $icon = $sectionIcons[$section] ?? 'fa-cog';
@endphp

@section('title', $title)
@section('header', $title)

@section('content')
<div class="w-full max-w-2xl">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 md:p-12 text-center">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-green-500 text-white flex items-center justify-center mx-auto mb-6">
            <i class="fas {{ $icon }} text-2xl"></i>
        </div>
        <h2 class="text-xl font-black text-gray-900 mb-4">{{ $title }}</h2>
        <p class="text-gray-600 mb-8">هذا القسم قيد التطوير. سيتم إضافته قريباً ضمن مجتمع البيانات والذكاء الاصطناعي.</p>
        <a href="{{ route('admin.community.dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors">
            <i class="fas fa-arrow-right"></i>
            <span>العودة للوحة المجتمع</span>
        </a>
    </div>
</div>
@endsection
