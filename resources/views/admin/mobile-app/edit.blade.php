@extends('layouts.admin')

@section('title', 'Mindlytics Community — الصفحة الرئيسية')
@section('page_title', 'Mindlytics Community · المحتوى')

@section('content')
<div class="w-full min-h-screen p-3 sm:p-4 md:p-6 lg:p-8 space-y-4 sm:space-y-6" style="background: #f8fafc;">

    @if(session('success'))
        <div class="rounded-2xl border-2 border-emerald-200/80 bg-gradient-to-r from-emerald-50 to-white px-5 py-4 text-emerald-900 shadow-lg flex items-center gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg"><i class="fas fa-check text-lg"></i></span>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border-2 border-rose-200 bg-rose-50/95 px-5 py-4 shadow-lg">
            <p class="font-black text-rose-900 mb-2 flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> يرجى تصحيح ما يلي:</p>
            <ul class="list-disc list-inside space-y-1 text-sm text-rose-800">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- شريط بطول الصفحة — نفس أسلوب البطاقات في لوحة التحكم --}}
    <div class="rounded-2xl p-6 sm:p-8 relative overflow-hidden border-2 border-violet-200/60 shadow-xl hover:shadow-2xl transition-all duration-300 w-full group"
         style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(245,243,255,0.96) 45%, rgba(237,233,254,0.92) 100%);">
        <div class="absolute inset-0 bg-gradient-to-br from-violet-100/50 via-indigo-50/30 to-fuchsia-50/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
        <div class="absolute top-0 left-0 w-40 h-40 bg-gradient-to-br from-violet-400/15 to-transparent rounded-full blur-2xl pointer-events-none" aria-hidden="true"></div>
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex items-start gap-5 min-w-0">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl flex items-center justify-center shadow-xl shrink-0"
                     style="background: linear-gradient(135deg, #7c3aed 0%, #6366f1 50%, #4f46e5 100%); box-shadow: 0 10px 28px rgba(124, 58, 237, 0.35);">
                    <i class="fas fa-mobile-alt text-white text-2xl sm:text-3xl"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-violet-700/90 mb-1">Mindlytics Community</p>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight bg-gradient-to-r from-violet-800 via-indigo-700 to-violet-700 bg-clip-text text-transparent">
                        محتوى الصفحة الرئيسية للتطبيق
                    </h1>
                    <p class="mt-3 text-slate-600 text-sm sm:text-base leading-relaxed max-w-4xl">
                        النصوص أدناه تظهر للطلاب في التطبيق. أرقام التقدّم والكورسات تُجلب تلقائياً من اشتراكات الطالب النشطة — لا يُعرض محتوى تعليمي حقيقي بدون اشتراك فعّال في كورس.
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 shrink-0">
                <span class="inline-flex items-center gap-2 rounded-xl border-2 border-violet-200/80 bg-white/90 px-4 py-2.5 text-xs sm:text-sm font-bold text-violet-900 shadow-sm">
                    <i class="fas fa-sync text-violet-600"></i> يحدّث الطالب بالسحب للأسفل
                </span>
                <span class="inline-flex items-center gap-2 rounded-xl border-2 border-indigo-200/80 bg-white/90 px-4 py-2.5 text-xs sm:text-sm font-bold text-indigo-900 shadow-sm">
                    <i class="fas fa-graduation-cap text-indigo-600"></i> طلاب فقط
                </span>
            </div>
        </div>
    </div>

    {{-- بطاقات توضيحية بعرض الشبكة — نفس روح لوحة التحكم --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 w-full">
        <div class="rounded-2xl p-5 sm:p-6 relative overflow-hidden border-2 border-blue-200/50 shadow-lg hover:shadow-xl transition-all duration-300"
             style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(239,246,255,0.95) 100%);">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-bold text-blue-800/90">نصوص قابلة للتعديل</span>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-sky-600 flex items-center justify-center shadow-lg"><i class="fas fa-pen-fancy text-white"></i></div>
            </div>
            <p class="text-xs sm:text-sm text-blue-700/80 leading-relaxed">الترحيب، المهمة، ورسالة عدم الاشتراك تُدار من هنا وتنعكس فوراً على واجهة التطبيق.</p>
        </div>
        <div class="rounded-2xl p-5 sm:p-6 relative overflow-hidden border-2 border-emerald-200/50 shadow-lg hover:shadow-xl transition-all duration-300"
             style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(236,253,245,0.95) 100%);">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-bold text-emerald-800/90">بيانات حية</span>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg"><i class="fas fa-chart-line text-white"></i></div>
            </div>
            <p class="text-xs sm:text-sm text-emerald-800/80 leading-relaxed">التقدّم وقائمة الكورسات تُبنى من قاعدة البيانات حسب اشتراك كل طالب.</p>
        </div>
        <div class="rounded-2xl p-5 sm:p-6 relative overflow-hidden border-2 border-amber-200/50 shadow-lg hover:shadow-xl transition-all duration-300"
             style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(255,251,235,0.95) 100%);">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-bold text-amber-900/90">مسار الكتالوج</span>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg"><i class="fas fa-link text-white"></i></div>
            </div>
            <p class="text-xs sm:text-sm text-amber-900/80 leading-relaxed">يحدد أين يُوجَّه الطالب على الموقع عند تصفّح الكورسات أو الاشتراك.</p>
        </div>
    </div>

    <form action="{{ route('admin.mobile-app.update') }}" method="POST" class="space-y-6 w-full">
        @csrf
        @method('PUT')

        <div class="rounded-2xl border-2 border-slate-200/70 bg-white p-5 sm:p-8 shadow-lg w-full">
            <h2 class="text-lg sm:text-xl font-black text-slate-900 mb-6 flex items-center gap-3 pb-4 border-b border-slate-100">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-sky-600 text-white shadow-md"><i class="fas fa-heading"></i></span>
                ترحيب الصفحة الرئيسية
            </h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 lg:gap-6 w-full">
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-slate-700">العنوان (عربي)</label>
                    <input type="text" name="welcome_title_ar" value="{{ old('welcome_title_ar', $settings->welcome_title_ar) }}"
                           class="w-full rounded-xl border-2 border-slate-200/90 bg-slate-50/50 px-4 py-3 text-sm focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-shadow">
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-slate-700">العنوان (English)</label>
                    <input type="text" name="welcome_title_en" value="{{ old('welcome_title_en', $settings->welcome_title_en) }}" dir="ltr"
                           class="w-full rounded-xl border-2 border-slate-200/90 bg-slate-50/50 px-4 py-3 text-sm focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-shadow">
                </div>
                <div class="lg:col-span-2 space-y-2">
                    <label class="block text-sm font-bold text-slate-700">الوصف الفرعي (عربي)</label>
                    <input type="text" name="welcome_subtitle_ar" value="{{ old('welcome_subtitle_ar', $settings->welcome_subtitle_ar) }}"
                           class="w-full rounded-xl border-2 border-slate-200/90 bg-slate-50/50 px-4 py-3 text-sm focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-shadow">
                </div>
                <div class="lg:col-span-2 space-y-2">
                    <label class="block text-sm font-bold text-slate-700">الوصف الفرعي (English)</label>
                    <input type="text" name="welcome_subtitle_en" value="{{ old('welcome_subtitle_en', $settings->welcome_subtitle_en) }}" dir="ltr"
                           class="w-full rounded-xl border-2 border-slate-200/90 bg-slate-50/50 px-4 py-3 text-sm focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-shadow">
                </div>
            </div>
        </div>

        <div class="rounded-2xl border-2 border-slate-200/70 bg-white p-5 sm:p-8 shadow-lg w-full">
            <h2 class="text-lg sm:text-xl font-black text-slate-900 mb-6 flex items-center gap-3 pb-4 border-b border-slate-100">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 text-white shadow-md"><i class="fas fa-bullseye"></i></span>
                بطاقة المهمة والتشجيع
            </h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 lg:gap-6 w-full">
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-slate-700">عنوان المهمة (عربي)</label>
                    <input type="text" name="mission_headline_ar" value="{{ old('mission_headline_ar', $settings->mission_headline_ar) }}"
                           class="w-full rounded-xl border-2 border-slate-200/90 bg-slate-50/50 px-4 py-3 text-sm focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-shadow">
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-slate-700">عنوان المهمة (English)</label>
                    <input type="text" name="mission_headline_en" value="{{ old('mission_headline_en', $settings->mission_headline_en) }}" dir="ltr"
                           class="w-full rounded-xl border-2 border-slate-200/90 bg-slate-50/50 px-4 py-3 text-sm focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-shadow">
                </div>
                <div class="lg:col-span-2 space-y-2">
                    <label class="block text-sm font-bold text-slate-700">نص المهمة (عربي)</label>
                    <textarea name="mission_body_ar" rows="4"
                              class="w-full rounded-xl border-2 border-slate-200/90 bg-slate-50/50 px-4 py-3 text-sm focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-shadow">{{ old('mission_body_ar', $settings->mission_body_ar) }}</textarea>
                </div>
                <div class="lg:col-span-2 space-y-2">
                    <label class="block text-sm font-bold text-slate-700">نص المهمة (English)</label>
                    <textarea name="mission_body_en" rows="4" dir="ltr"
                              class="w-full rounded-xl border-2 border-slate-200/90 bg-slate-50/50 px-4 py-3 text-sm focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-shadow">{{ old('mission_body_en', $settings->mission_body_en) }}</textarea>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border-2 border-slate-200/70 bg-white p-5 sm:p-8 shadow-lg w-full">
            <h2 class="text-lg sm:text-xl font-black text-slate-900 mb-6 flex items-center gap-3 pb-4 border-b border-slate-100">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-md"><i class="fas fa-user-clock"></i></span>
                عند عدم وجود اشتراك نشط في كورس
            </h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 lg:gap-6 w-full">
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-slate-700">العنوان (عربي)</label>
                    <input type="text" name="no_subscription_title_ar" value="{{ old('no_subscription_title_ar', $settings->no_subscription_title_ar) }}"
                           class="w-full rounded-xl border-2 border-slate-200/90 bg-slate-50/50 px-4 py-3 text-sm focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-shadow">
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-slate-700">العنوان (English)</label>
                    <input type="text" name="no_subscription_title_en" value="{{ old('no_subscription_title_en', $settings->no_subscription_title_en) }}" dir="ltr"
                           class="w-full rounded-xl border-2 border-slate-200/90 bg-slate-50/50 px-4 py-3 text-sm focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-shadow">
                </div>
                <div class="lg:col-span-2 space-y-2">
                    <label class="block text-sm font-bold text-slate-700">النص (عربي)</label>
                    <textarea name="no_subscription_body_ar" rows="4"
                              class="w-full rounded-xl border-2 border-slate-200/90 bg-slate-50/50 px-4 py-3 text-sm focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-shadow">{{ old('no_subscription_body_ar', $settings->no_subscription_body_ar) }}</textarea>
                </div>
                <div class="lg:col-span-2 space-y-2">
                    <label class="block text-sm font-bold text-slate-700">النص (English)</label>
                    <textarea name="no_subscription_body_en" rows="4" dir="ltr"
                              class="w-full rounded-xl border-2 border-slate-200/90 bg-slate-50/50 px-4 py-3 text-sm focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-shadow">{{ old('no_subscription_body_en', $settings->no_subscription_body_en) }}</textarea>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border-2 border-indigo-200/60 p-5 sm:p-8 shadow-lg w-full"
             style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(238,242,255,0.9) 100%);">
            <div class="flex flex-col lg:flex-row lg:items-end gap-6">
                <div class="flex-1 min-w-0 space-y-2">
                    <label class="block text-sm font-bold text-indigo-900">مسار صفحة الكورسات على الموقع</label>
                    <p class="text-xs text-indigo-700/80 leading-relaxed">يفتحه التطبيق للطلاب لتصفّح الكورسات والاشتراك. يجب أن يبدأ بـ <code class="rounded bg-white/80 px-1.5 py-0.5 border border-indigo-200">/</code> — مثال: <code class="rounded bg-white/80 px-1.5 py-0.5 border border-indigo-200">/courses</code></p>
                    <input type="text" name="catalog_web_path" value="{{ old('catalog_web_path', $settings->catalog_web_path) }}" required dir="ltr"
                           placeholder="/courses"
                           class="w-full max-w-xl rounded-xl border-2 border-indigo-200/90 bg-white px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-indigo-500/35 focus:border-indigo-500 transition-shadow">
                </div>
                <div class="shrink-0">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-violet-600 via-indigo-600 to-violet-700 px-8 py-3.5 text-white font-black shadow-xl shadow-violet-600/35 hover:shadow-2xl hover:from-violet-500 hover:via-indigo-500 hover:to-violet-600 transition-all duration-300 w-full lg:w-auto min-w-[200px]">
                        <i class="fas fa-save"></i>
                        حفظ كل الإعدادات
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
