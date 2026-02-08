@extends('layouts.public')

@section('title', 'آراء العملاء - Mindlytics')

@section('content')
<!-- Hero Section -->
<section class="hero-gradient min-h-[50vh] flex items-center relative overflow-hidden pt-28" style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.85) 25%, rgba(14, 165, 233, 0.7) 50%, rgba(14, 165, 233, 0.75) 75%, rgba(2, 132, 199, 0.8) 100%);">
    <div class="container mx-auto px-4 text-center relative z-10">
        <h1 class="text-5xl md:text-6xl font-black text-white leading-tight mb-6 fade-in" style="text-shadow: 0 4px 16px rgba(0,0,0,0.8), 0 2px 8px rgba(0,0,0,0.6), 0 0 12px rgba(14, 165, 233, 0.4);">
            آراء عملائنا
        </h1>
        <p class="text-xl md:text-2xl text-white mb-10 fade-in font-semibold" style="text-shadow: 0 3px 12px rgba(0,0,0,0.7), 0 1px 6px rgba(0,0,0,0.5), 0 0 8px rgba(14, 165, 233, 0.3);">
            ماذا يقول طلابنا عنا
        </p>
    </div>
</section>

<!-- Testimonials -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
            @for($i = 1; $i <= 6; $i++)
            <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                <div class="flex items-center mb-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-sky-500 to-sky-700 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg">
                        {{ $i }}
                    </div>
                    <div class="mr-4">
                        <h4 class="font-bold text-gray-900">اسم الطالب {{ $i }}</h4>
                        <p class="text-sm text-gray-600">مطور برمجيات</p>
                    </div>
                </div>
                <div class="flex mb-3">
                    @for($j = 1; $j <= 5; $j++)
                    <i class="fas fa-star text-yellow-400"></i>
                    @endfor
                </div>
                <p class="text-gray-700 leading-relaxed">
                    "تجربة رائعة! الكورسات شاملة والمحتوى ممتاز. استفدت كثيراً وأصبحت قادراً على العمل في مجال البرمجة."
                </p>
            </div>
            @endfor
        </div>
    </div>
</section>
@endsection
