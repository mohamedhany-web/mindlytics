@extends('layouts.public')

@section('title', 'انتهت الورشة — '.$workshop->title.' | Mindlytics')

@push('styles')
@include('careers._styles')
@endpush

@section('content')
@include('careers._hero', [
    'badge' => 'ورشة Mindlytics',
    'title' => 'انتهت الورشة',
    'subtitle' => $workshop->title,
    'backUrl' => route('home'),
    'backLabel' => 'العودة للرئيسية',
])

<section class="py-12 md:py-16 bg-white">
    <div class="container mx-auto px-4 max-w-lg text-center">
        <div class="content-panel p-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-amber-100 text-amber-600 mb-5">
                <i class="fas fa-flag-checkered text-2xl"></i>
            </div>
            <p class="text-slate-600 text-sm leading-relaxed mb-6">
                لم يعد التسجيل في هذه الورشة متاحاً. نفس الرابط يعرض هذه الصفحة ليعرف الزائر أن الحجز أُغلق.
                @if($workshop->starts_at)
                    <span class="block mt-3 text-slate-500">كانت مجدولة: {{ $workshop->starts_at->format('Y-m-d H:i') }}</span>
                @endif
            </p>
            <a href="{{ route('home') }}" class="careers-btn-submit">
                <i class="fas fa-home"></i>
                العودة للرئيسية
            </a>
        </div>
    </div>
</section>
@endsection
