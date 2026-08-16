@extends('layouts.public')

@section('title', 'التسجيل في الورشة — '.$workshop->title.' | Mindlytics')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($workshop->description ?: $workshop->title), 160))

@push('styles')
@include('careers._styles')
@endpush

@section('content')
@php
    $total = $workshop->max_seats ?: null;
    $registeredCount = $workshop->registrations()->count();
    $modeLabel = match ($workshop->mode) {
        'online' => 'أونلاين (عن بُعد)',
        'offline' => 'في المكان (أوفلاين)',
        default => 'أونلاين أو في المكان',
    };
    $modeIcon = match ($workshop->mode) {
        'online' => 'fas fa-globe',
        'offline' => 'fas fa-building',
        default => 'fas fa-people-arrows',
    };
@endphp

@include('careers._hero', [
    'title' => $workshop->title,
    'subtitle' => 'املأ البيانات للحجز في الورشة، وبعد التقديم تقدر تنضم لجروب الواتساب مباشرة.',
    'backUrl' => route('home'),
    'backLabel' => 'العودة للرئيسية',
    'metaChips' => array_values(array_filter([
        ['label' => $modeLabel, 'icon' => $modeIcon, 'tone' => 'blue'],
        $workshop->location ? ['label' => $workshop->location, 'icon' => 'fas fa-map-marker-alt', 'tone' => 'green'] : null,
        $workshop->starts_at ? ['label' => $workshop->starts_at->format('Y-m-d H:i'), 'icon' => 'fas fa-calendar-alt', 'tone' => 'violet'] : null,
        $total
            ? ['label' => 'متبقي '.($remaining ?? max($total - $registeredCount, 0)).' مقعد', 'icon' => 'fas fa-users', 'tone' => 'green']
            : ['label' => 'مقاعد مفتوحة', 'icon' => 'fas fa-users', 'tone' => 'green'],
    ])),
])

<section class="py-10 md:py-16 bg-gradient-to-b from-white via-blue-50/30 to-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-6xl">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start">
            <aside class="lg:col-span-2 space-y-4">
                <div class="content-panel p-6">
                    <p class="form-section-title mb-4">
                        <span class="form-section-num"><i class="fas fa-info text-[10px]"></i></span>
                        تفاصيل الورشة
                    </p>
                    <ul class="space-y-3 text-sm text-slate-700">
                        @if($workshop->starts_at)
                            <li class="flex items-start gap-3">
                                <span class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0"><i class="fas fa-calendar-alt"></i></span>
                                <span>
                                    <span class="block text-xs font-bold text-slate-500">البداية</span>
                                    {{ $workshop->starts_at->locale(app()->getLocale())->translatedFormat('l j F Y — H:i') }}
                                </span>
                            </li>
                        @endif
                        @if($workshop->ends_at)
                            <li class="flex items-start gap-3">
                                <span class="w-9 h-9 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0"><i class="fas fa-clock"></i></span>
                                <span>
                                    <span class="block text-xs font-bold text-slate-500">النهاية</span>
                                    {{ $workshop->ends_at->locale(app()->getLocale())->translatedFormat('l j F Y — H:i') }}
                                </span>
                            </li>
                        @endif
                        @if($workshop->location)
                            <li class="flex items-start gap-3">
                                <span class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0"><i class="fas fa-location-dot"></i></span>
                                <span>
                                    <span class="block text-xs font-bold text-slate-500">المكان</span>
                                    {{ $workshop->location }}
                                </span>
                            </li>
                        @endif
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center flex-shrink-0"><i class="{{ $modeIcon }}"></i></span>
                            <span>
                                <span class="block text-xs font-bold text-slate-500">طريقة الحضور</span>
                                {{ $modeLabel }}
                            </span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0"><i class="fas fa-users"></i></span>
                            <span>
                                <span class="block text-xs font-bold text-slate-500">المقاعد</span>
                                @if($total)
                                    {{ $registeredCount }} / {{ $total }}
                                    @if(! is_null($remaining))
                                        <span class="text-emerald-700 font-bold">(متبقي {{ $remaining }})</span>
                                    @endif
                                @else
                                    غير محدودة
                                @endif
                            </span>
                        </li>
                    </ul>
                </div>

                @if($workshop->description)
                    <div class="content-panel p-6">
                        <p class="text-xs font-bold text-blue-700 mb-2">عن الورشة</p>
                        <div class="text-sm text-slate-700 whitespace-pre-line leading-relaxed">{{ $workshop->description }}</div>
                    </div>
                @endif
            </aside>

            <div class="lg:col-span-3">
                <div class="content-panel p-6 sm:p-8">
                    @if(session('error'))
                        <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3">
                            <i class="fas fa-exclamation-circle mt-0.5"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('workshop_registered') || session('success'))
                        <div class="text-center space-y-4 py-4">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600">
                                <i class="fas fa-check text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-extrabold text-blue-900">تم استلام طلبك</p>
                                <p class="text-sm text-slate-600 mt-2 leading-relaxed max-w-md mx-auto">{{ session('success') }}</p>
                            </div>
                            @if($workshop->publicWhatsappGroupUrl())
                                <a href="{{ $workshop->publicWhatsappGroupUrl() }}" target="_blank" rel="noopener noreferrer"
                                   class="w-full inline-flex items-center justify-center gap-2 rounded-full bg-[#25D366] hover:bg-[#1ebe5d] px-6 py-3.5 text-sm font-bold text-white shadow-lg">
                                    <i class="fab fa-whatsapp text-xl"></i>
                                    <span>انضم إلى جروب الواتساب</span>
                                </a>
                                <p class="text-xs text-slate-500">اضغط الزر للدخول مباشرة إلى جروب الورشة على واتساب.</p>
                            @endif
                            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-bold text-blue-700 hover:text-blue-900">
                                <i class="fas fa-home"></i>
                                العودة للرئيسية
                            </a>
                        </div>
                    @elseif(! is_null($remaining) && $remaining <= 0)
                        <div class="text-center py-8">
                            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-rose-100 text-rose-600 mb-4">
                                <i class="fas fa-user-slash text-xl"></i>
                            </div>
                            <p class="text-lg font-extrabold text-slate-900 mb-2">اكتمل العدد</p>
                            <p class="text-sm text-slate-600">لا يمكن استقبال تسجيلات جديدة حالياً.</p>
                        </div>
                    @else
                        <p class="form-section-title">
                            <span class="form-section-num">1</span>
                            بيانات التسجيل
                        </p>
                        <form method="POST" action="{{ route('public.workshops.register', $workshop->slug) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label for="name" class="careers-label">الاسم الكامل <span class="text-rose-500">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" class="careers-input" required autocomplete="name">
                            </div>
                            <div>
                                <label for="email" class="careers-label">البريد الإلكتروني</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" class="careers-input" autocomplete="email">
                            </div>
                            <div>
                                <label for="phone" class="careers-label">رقم الجوال / واتساب</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="careers-input" dir="ltr" autocomplete="tel">
                            </div>
                            <div>
                                <label class="careers-label">طريقة الحضور</label>
                                @if($workshop->mode === 'online')
                                    <input type="hidden" name="attendance_mode" value="online">
                                    <span class="job-meta-chip blue"><i class="fas fa-globe"></i> أونلاين (عن بُعد)</span>
                                @elseif($workshop->mode === 'offline')
                                    <input type="hidden" name="attendance_mode" value="offline">
                                    <span class="job-meta-chip green"><i class="fas fa-building"></i> في المكان (أوفلاين)</span>
                                @else
                                    <p class="text-xs text-slate-500 mb-2">اختر كيف تفضل حضور الورشة:</p>
                                    <div class="flex flex-col sm:flex-row gap-3 text-sm">
                                        <label class="flex-1 inline-flex items-center gap-2 rounded-2xl border-2 border-slate-200 px-4 py-3 cursor-pointer hover:border-sky-300 has-[:checked]:border-sky-500 has-[:checked]:bg-sky-50">
                                            <input type="radio" name="attendance_mode" value="online" class="text-sky-600 border-slate-300 focus:ring-sky-500"
                                                   {{ old('attendance_mode', 'online') === 'online' ? 'checked' : '' }}>
                                            <span class="font-bold text-slate-800">أونلاين (عن بُعد)</span>
                                        </label>
                                        <label class="flex-1 inline-flex items-center gap-2 rounded-2xl border-2 border-slate-200 px-4 py-3 cursor-pointer hover:border-sky-300 has-[:checked]:border-sky-500 has-[:checked]:bg-sky-50">
                                            <input type="radio" name="attendance_mode" value="offline" class="text-sky-600 border-slate-300 focus:ring-sky-500"
                                                   {{ old('attendance_mode') === 'offline' ? 'checked' : '' }}>
                                            <span class="font-bold text-slate-800">في المكان (أوفلاين)</span>
                                        </label>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <label for="notes" class="careers-label">ملاحظات إضافية</label>
                                <textarea id="notes" name="notes" rows="3" class="careers-input"
                                          placeholder="مستواك الحالي، أو أي متطلبات خاصة.">{{ old('notes') }}</textarea>
                            </div>
                            <button type="submit" class="careers-btn-submit w-full">
                                <i class="fas fa-paper-plane"></i>
                                إرسال طلب التسجيل
                            </button>
                            <p class="text-[11px] text-slate-500 text-center leading-relaxed">
                                بتعبئة هذا النموذج فأنت توافق على تواصل فريق Mindlytics معك بخصوص تفاصيل الورشة ومواعيدها.
                            </p>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
