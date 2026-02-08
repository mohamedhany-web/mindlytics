@extends('layouts.public')

@section('title', 'سياسة الخصوصية - Mindlytics')

@section('content')
<!-- Hero Section -->
<section class="hero-gradient min-h-[50vh] flex items-center relative overflow-hidden pt-28" style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.85) 25%, rgba(14, 165, 233, 0.7) 50%, rgba(14, 165, 233, 0.75) 75%, rgba(2, 132, 199, 0.8) 100%);">
    <div class="container mx-auto px-4 text-center relative z-10">
        <h1 class="text-5xl md:text-6xl font-black text-white leading-tight mb-6 fade-in" style="text-shadow: 0 4px 16px rgba(0,0,0,0.8), 0 2px 8px rgba(0,0,0,0.6), 0 0 12px rgba(14, 165, 233, 0.4);">
            سياسة الخصوصية
        </h1>
        <p class="text-xl md:text-2xl text-white mb-10 fade-in font-semibold" style="text-shadow: 0 3px 12px rgba(0,0,0,0.7), 0 1px 6px rgba(0,0,0,0.5), 0 0 8px rgba(14, 165, 233, 0.3);">
            نحن ملتزمون بحماية خصوصيتك
        </p>
    </div>
</section>

<!-- Content Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="bg-white rounded-xl shadow-lg p-8 md:p-12">
            <div class="prose prose-lg max-w-none">
                <p class="text-gray-700 text-lg leading-relaxed mb-8">
                    نحن في Mindlytics نلتزم بحماية خصوصيتك. توضح هذه السياسة كيفية جمع واستخدام معلوماتك الشخصية.
                </p>
                
                <div class="space-y-8">
                    <div class="card-hover p-6 rounded-xl bg-gradient-to-br from-sky-50 to-sky-100 border-r-4 border-sky-500">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-database text-sky-500 ml-3"></i>
                            1. المعلومات التي نجمعها
                        </h2>
                        <p class="text-gray-700 leading-relaxed">
                            نجمع المعلومات التي تقدمها لنا عند التسجيل واستخدام الخدمة، بما في ذلك الاسم والبريد الإلكتروني ورقم الهاتف.
                        </p>
                    </div>

                    <div class="card-hover p-6 rounded-xl bg-gradient-to-br from-sky-50 to-sky-100 border-r-4 border-sky-500">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-tasks text-sky-500 ml-3"></i>
                            2. كيفية استخدام المعلومات
                        </h2>
                        <p class="text-gray-700 leading-relaxed">
                            نستخدم المعلومات التي نجمعها لتقديم وتحسين خدماتنا، والاتصال بك، وإرسال الإشعارات المهمة.
                        </p>
                    </div>

                    <div class="card-hover p-6 rounded-xl bg-gradient-to-br from-sky-50 to-sky-100 border-r-4 border-sky-500">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-lock text-sky-500 ml-3"></i>
                            3. حماية المعلومات
                        </h2>
                        <p class="text-gray-700 leading-relaxed">
                            نتخذ إجراءات أمنية مناسبة لحماية معلوماتك الشخصية من الوصول غير المصرح به أو التغيير أو الكشف.
                        </p>
                    </div>

                    <div class="card-hover p-6 rounded-xl bg-gradient-to-br from-sky-50 to-sky-100 border-r-4 border-sky-500">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-share-alt text-sky-500 ml-3"></i>
                            4. مشاركة المعلومات
                        </h2>
                        <p class="text-gray-700 leading-relaxed">
                            لا نبيع أو نؤجر معلوماتك الشخصية لأطراف ثالثة. قد نشارك المعلومات فقط في الحالات المحددة في هذه السياسة.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
