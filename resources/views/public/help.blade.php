@extends('layouts.public')

@section('title', 'مركز المساعدة - Mindlytics')

@section('content')
<!-- Hero Section -->
<section class="hero-gradient min-h-[50vh] flex items-center relative overflow-hidden pt-28" style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.85) 25%, rgba(14, 165, 233, 0.7) 50%, rgba(14, 165, 233, 0.75) 75%, rgba(2, 132, 199, 0.8) 100%);">
    <div class="container mx-auto px-4 text-center relative z-10">
        <h1 class="text-5xl md:text-6xl font-black text-white leading-tight mb-6 fade-in" style="text-shadow: 0 4px 16px rgba(0,0,0,0.8), 0 2px 8px rgba(0,0,0,0.6), 0 0 12px rgba(14, 165, 233, 0.4);">
            مركز المساعدة
        </h1>
        <p class="text-xl md:text-2xl text-white mb-10 fade-in font-semibold" style="text-shadow: 0 3px 12px rgba(0,0,0,0.7), 0 1px 6px rgba(0,0,0,0.5), 0 0 8px rgba(14, 165, 233, 0.3);">
            كيف يمكننا مساعدتك؟
        </p>
    </div>
</section>

<!-- Help Options -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto mb-12">
            <a href="{{ route('public.faq') }}" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow text-center card-hover">
                <div class="text-5xl text-sky-500 mb-4">
                    <i class="fas fa-question-circle"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">الأسئلة الشائعة</h3>
                <p class="text-gray-600">إجابات على الأسئلة الأكثر شيوعاً</p>
            </a>

            <a href="{{ route('public.contact') }}" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow text-center card-hover">
                <div class="text-5xl text-blue-500 mb-4">
                    <i class="fas fa-envelope"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">تواصل معنا</h3>
                <p class="text-gray-600">أرسل استفسارك وسنرد عليك قريباً</p>
            </a>

            <a href="#" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow text-center card-hover">
                <div class="text-5xl text-indigo-500 mb-4">
                    <i class="fas fa-video"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">فيديوهات تعليمية</h3>
                <p class="text-gray-600">تعرف على كيفية استخدام المنصة</p>
            </a>
        </div>

        <!-- Common Topics -->
        <div class="bg-white rounded-xl shadow-lg p-8 max-w-4xl mx-auto">
            <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">مواضيع شائعة</h2>
            <div class="space-y-4">
                @for($i = 1; $i <= 5; $i++)
                <div class="border-b border-gray-200 pb-4 card-hover p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center">
                        <i class="fas fa-question-circle text-sky-500 ml-3"></i>
                        كيف يمكنني التسجيل في الكورسات؟
                    </h3>
                    <p class="text-gray-600">
                        يمكنك التسجيل في أي كورس من خلال صفحة الكورسات. اختر الكورس المناسب واضغط على "اشترك الآن".
                    </p>
                </div>
                @endfor
            </div>
        </div>
    </div>
</section>
@endsection
