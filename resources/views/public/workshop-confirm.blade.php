@php
    $__pageLocale = app()->getLocale();
    $__pageRtl = $__pageLocale === 'ar';
    $confirmed = session('confirmed_registration');
@endphp
<!DOCTYPE html>
<html lang="{{ $__pageLocale }}" dir="{{ $__pageRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title>تأكيد الحضور — {{ $workshop->title }} | Mindlytics</title>

    @include('components.favicon-meta')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * { font-family: 'Tajawal', 'Cairo', sans-serif; box-sizing: border-box; }
        html { scroll-behavior: smooth; overflow-x: hidden; }
        body {
            margin: 0;
            min-height: 100vh;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }
        .navbar-gradient {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 45%, #1d4ed8 100%);
            box-shadow: 0 1px 0 rgba(255, 255, 255, 0.08);
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .hero-section {
            background: linear-gradient(to bottom, #eff6ff, #dbeafe 40%, #f8fafc 100%);
            position: relative;
            overflow: hidden;
        }
        .form-input {
            width: 100%;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            color: #0f172a;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }
        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 50%, #1e40af 100%);
            transition: transform 0.15s, box-shadow 0.2s;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.35);
        }
        main { flex: 1 0 auto; padding-top: 4.5rem; }
        @if(request()->boolean('embed'))
        .navbar-gradient { display: none !important; }
        main { padding-top: 1rem !important; }
        @endif
    </style>
</head>
<body>
    @unless(request()->boolean('embed'))
        @include('components.unified-navbar')
    @endunless

    <main>
        <section class="hero-section pt-10 pb-8 sm:pt-14 sm:pb-10 px-4">
            <div class="max-w-3xl mx-auto text-center relative z-10">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-xl shadow-blue-500/30 mb-5">
                    <i class="fas fa-certificate text-2xl"></i>
                </div>
                <h1 class="text-3xl sm:text-4xl font-black text-slate-900 mb-3">تأكيد حضور الورشة</h1>
                <p class="text-slate-600 text-sm sm:text-base max-w-xl mx-auto leading-relaxed">
                    أدخل اسمك ورقم هاتفك لتأكيد حضورك واستحقاقك لشهادة الورشة.
                </p>
            </div>
        </section>

        <section class="px-4 pb-16 -mt-2">
            <div class="max-w-4xl mx-auto grid grid-cols-1 lg:grid-cols-5 gap-6">
                <div class="lg:col-span-2">
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6 h-full">
                        <p class="text-xs font-bold text-blue-600 uppercase tracking-wide mb-2">الورشة</p>
                        <h2 class="text-xl font-black text-slate-900 mb-4 leading-snug">{{ $workshop->title }}</h2>
                        <div class="space-y-3 text-sm text-slate-600">
                            @if($workshop->starts_at)
                                <div class="flex items-start gap-2">
                                    <i class="fas fa-calendar-alt text-blue-500 mt-0.5"></i>
                                    <span>{{ $workshop->starts_at->locale('ar')->translatedFormat('l j F Y — H:i') }}</span>
                                </div>
                            @endif
                            @if($workshop->location)
                                <div class="flex items-start gap-2">
                                    <i class="fas fa-location-dot text-rose-500 mt-0.5"></i>
                                    <span>{{ $workshop->location }}</span>
                                </div>
                            @endif
                            <div class="flex items-start gap-2">
                                <i class="fas fa-{{ $workshop->mode === 'online' ? 'globe' : ($workshop->mode === 'offline' ? 'building' : 'people-arrows') }} text-emerald-500 mt-0.5"></i>
                                <span>
                                    @if($workshop->mode === 'online') حضور أونلاين
                                    @elseif($workshop->mode === 'offline') حضور في المكان
                                    @else أونلاين أو في المكان
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-3">
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-lg p-6 sm:p-8">
                        @if(session('success'))
                            <div class="p-5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm text-center">
                                <i class="fas fa-check-circle text-emerald-600 text-3xl mb-3 block"></i>
                                <p class="font-bold text-lg mb-2">تم التأكيد</p>
                                <p>{{ session('success') }}</p>
                                @if($confirmed)
                                    <p class="text-xs text-emerald-700 mt-3">{{ $confirmed->name }} — {{ $confirmed->checked_in_at?->format('Y-m-d H:i') }}</p>
                                @endif
                                @unless(request()->boolean('embed'))
                                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 mt-5 text-sm font-semibold text-blue-600 hover:text-blue-700">
                                        <i class="fas fa-home"></i> العودة للرئيسية
                                    </a>
                                @endunless
                            </div>
                        @elseif(session('info'))
                            <div class="p-5 rounded-xl bg-sky-50 border border-sky-200 text-sky-800 text-sm text-center">
                                <i class="fas fa-circle-info text-sky-600 text-3xl mb-3 block"></i>
                                <p class="font-bold text-lg mb-2">سبق التأكيد</p>
                                <p>{{ session('info') }}</p>
                                @unless(request()->boolean('embed'))
                                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 mt-5 text-sm font-semibold text-blue-600 hover:text-blue-700">
                                        <i class="fas fa-home"></i> العودة للرئيسية
                                    </a>
                                @endunless
                            </div>
                        @else
                            @if(session('error'))
                                <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3">
                                    <i class="fas fa-exclamation-circle text-rose-600 mt-0.5"></i>
                                    <span>{{ session('error') }}</span>
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('public.workshops.confirm.store', $workshop->slug) }}" class="space-y-5">
                                @csrf

                                <div>
                                    <label for="name" class="block text-sm font-bold text-slate-800 mb-2">
                                        الاسم الكامل <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                                           class="form-input" placeholder="اسمك كما تريد أن يظهر في الشهادة" required autocomplete="name">
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-bold text-slate-800 mb-2">
                                        رقم الهاتف / واتساب <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                           class="form-input" placeholder="01xxxxxxxxx" required autocomplete="tel" dir="ltr">
                                </div>

                                <button type="submit"
                                        class="btn-primary w-full inline-flex items-center justify-center gap-2 rounded-xl px-6 py-3.5 text-sm font-bold text-white shadow-lg">
                                    <i class="fas fa-circle-check"></i>
                                    <span>تأكيد حضوري</span>
                                </button>

                                <p class="text-[11px] text-slate-500 text-center leading-relaxed">
                                    بالضغط على «تأكيد حضوري» أنت تؤكد مشاركتك في الورشة وتوافق على استخدام بياناتك لإصدار الشهادة.
                                </p>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
