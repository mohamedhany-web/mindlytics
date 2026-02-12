@extends('layouts.public')

@section('title', 'الشروط والأحكام - Mindlytics')

@push('styles')
<style>
    .hero-legal {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 45%, #1d4ed8 100%);
        position: relative;
        overflow: hidden;
    }
    .hero-legal::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M0 0h40v40H0V0zm2 2h36v36H2V2z'/%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.6;
    }
    .legal-card {
        transition: all 0.2s ease;
        border: 2px solid #e2e8f0;
    }
    .legal-card:hover {
        border-color: rgba(59, 130, 246, 0.25);
        box-shadow: 0 8px 24px rgba(59, 130, 246, 0.08);
    }
</style>
@endpush

@section('content')
<section class="hero-legal min-h-[38vh] flex items-center relative pt-24 pb-14 lg:pt-28 lg:pb-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-tight mb-4" style="text-shadow: 0 2px 12px rgba(0,0,0,0.3);">
            الشروط والأحكام
        </h1>
        <p class="text-lg md:text-xl text-blue-100 max-w-2xl mx-auto" style="text-shadow: 0 1px 4px rgba(0,0,0,0.2);">
            يرجى قراءة الشروط والأحكام التالية بعناية قبل استخدام الخدمة
        </p>
    </div>
</section>

<section class="py-12 md:py-16 bg-gradient-to-b from-slate-50 to-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl">
        <p class="text-slate-700 text-lg leading-relaxed mb-10">
            مرحباً بك في منصة Mindlytics. باستخدامك للمنصة فإنك توافق على الالتزام بهذه الشروط والأحكام.
        </p>

        <div class="space-y-6">
            <article class="legal-card bg-white rounded-2xl shadow-md p-6 md:p-8 border-r-4 border-blue-500">
                <h2 class="text-xl font-bold text-slate-800 mb-3 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0"><i class="fas fa-check-circle"></i></span>
                    1. القبول
                </h2>
                <p class="text-slate-600 leading-relaxed">
                    باستخدامك لهذه المنصة، فإنك توافق على الالتزام بهذه الشروط والأحكام. إذا كنت لا توافق على أي جزء منها، يرجى عدم استخدام الخدمة.
                </p>
            </article>

            <article class="legal-card bg-white rounded-2xl shadow-md p-6 md:p-8 border-r-4 border-blue-500">
                <h2 class="text-xl font-bold text-slate-800 mb-3 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0"><i class="fas fa-shield-alt"></i></span>
                    2. استخدام الخدمة
                </h2>
                <p class="text-slate-600 leading-relaxed">
                    يجب استخدام الخدمة لأغراض قانونية وتعليمية فقط. لا يجوز استخدام المنصة لأي غرض غير قانوني أو محظور أو مخالف للآداب العامة.
                </p>
            </article>

            <article class="legal-card bg-white rounded-2xl shadow-md p-6 md:p-8 border-r-4 border-blue-500">
                <h2 class="text-xl font-bold text-slate-800 mb-3 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0"><i class="fas fa-user-shield"></i></span>
                    3. الحسابات
                </h2>
                <p class="text-slate-600 leading-relaxed">
                    أنت مسؤول عن الحفاظ على سرية معلومات حسابك وكلمة المرور. توافق على إبلاغنا فوراً بأي استخدام غير مصرح به لحسابك.
                </p>
            </article>

            <article class="legal-card bg-white rounded-2xl shadow-md p-6 md:p-8 border-r-4 border-blue-500">
                <h2 class="text-xl font-bold text-slate-800 mb-3 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0"><i class="fas fa-copyright"></i></span>
                    4. الملكية الفكرية
                </h2>
                <p class="text-slate-600 leading-relaxed">
                    جميع المحتويات والمواد المتاحة على المنصة (دروس، فيديوهات، نصوص، شعارات) محمية بحقوق الطبع والنشر والملكية الفكرية. لا يجوز نسخها أو إعادة نشرها دون إذن كتابي.
                </p>
            </article>

            <article class="legal-card bg-white rounded-2xl shadow-md p-6 md:p-8 border-r-4 border-blue-500">
                <h2 class="text-xl font-bold text-slate-800 mb-3 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0"><i class="fas fa-edit"></i></span>
                    5. التعديلات
                </h2>
                <p class="text-slate-600 leading-relaxed">
                    نحتفظ بالحق في تعديل هذه الشروط في أي وقت. سيتم نشر أي تغييرات على هذه الصفحة، وننصح بمراجعتها دورياً.
                </p>
            </article>
        </div>
    </div>
</section>

<section class="py-14 bg-white border-t border-slate-200">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center max-w-2xl">
        <h3 class="text-2xl font-bold text-slate-800 mb-2">هل لديك استفسار؟</h3>
        <p class="text-slate-600 mb-6">نحن هنا لمساعدتك في أي وقت</p>
        <a href="{{ route('public.contact') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl hover:from-blue-700 hover:to-blue-600 transition-all">
            <i class="fas fa-envelope"></i>
            تواصل معنا
        </a>
    </div>
</section>
@endsection
