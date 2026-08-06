@php
    $__pageLocale = app()->getLocale();
    $__pageRtl = $__pageLocale === 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ $__pageLocale }}" dir="{{ $__pageRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>حجز مجموعة {{ $bookingModeLabel ?? 'أوفلاين' }} — {{ $group->name }} | Mindlytics</title>
    <x-tracking-tags placement="head" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&family=Noto+Sans+Arabic:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Cairo', 'Noto Sans Arabic', sans-serif; }
        body { overflow-x: hidden; background: #f8fafc; padding-top: 80px; min-height: 100vh; }
        .hero-section {
            background: linear-gradient(135deg, #eff6ff 0%, #ffffff 50%, #ecfdf5 100%);
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(circle at 20% 30%, rgba(59, 130, 246, 0.08) 0%, transparent 50%),
                        radial-gradient(circle at 80% 70%, rgba(16, 185, 129, 0.06) 0%, transparent 50%);
            pointer-events: none;
        }
        .course-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 2px solid rgba(226, 232, 240, 0.8);
            transition: all 0.3s ease;
        }
        .course-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); }
        .navbar-gradient, nav#navbar {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 50%, #3b82f6 100%) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: fixed !important; top: 0; left: 0; right: 0; z-index: 1000 !important;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900" x-data="{ mobileMenu: false, method: '{{ old('payment_method', 'bank_transfer') }}' }" :class="{ 'overflow-hidden': mobileMenu }">
    <x-tracking-tags placement="body" />

    @php
        $__mlAnalytics = app(\App\Services\MarketingAnalyticsService::class);
        $__mlBeginCheckout = $__mlAnalytics->beginCheckout([
            $__mlAnalytics->itemFromOfflineCourse($course),
        ], (float) ($course->price ?? 0));
    @endphp
    <x-ecommerce-datalayer :payload="$__mlBeginCheckout" />

    @include('components.unified-navbar')

    <main>
        <section class="hero-section relative py-12 lg:py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <nav class="mb-6 text-gray-600 text-sm flex flex-wrap items-center gap-1">
                    <a href="{{ url('/') }}" class="hover:text-blue-600 transition-colors">الرئيسية</a>
                    <span class="text-gray-400">/</span>
                    <span class="text-gray-900 font-medium">حجز مجموعة {{ $bookingModeLabel ?? 'أوفلاين' }}</span>
                </nav>
                <div class="text-center mb-4">
                    <h1 class="text-3xl md:text-4xl font-black text-gray-900 mb-2">حجز مجموعة {{ $bookingModeLabel ?? 'أوفلاين' }}</h1>
                    <p class="text-lg text-gray-600">{{ $course->title }}</p>
                    <p class="text-blue-700 font-bold mt-2 text-xl">{{ $group->name }}</p>
                </div>
            </div>
        </section>

        <section class="py-8 md:py-12 bg-gradient-to-b from-gray-50 via-white to-gray-50 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                    <div class="lg:col-span-1">
                        <div class="course-card p-6 sticky top-24">
                            <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center gap-2">
                                <i class="fas fa-users text-blue-600"></i>
                                ملخص الحجز
                            </h3>
                            <div class="mb-6 pb-6 border-b border-gray-200 space-y-3 text-sm">
                                <div class="flex items-center gap-2 text-gray-700">
                                    <i class="fas fa-chalkboard-teacher text-purple-500"></i>
                                    <span>{{ $course->instructor->name ?? '—' }}</span>
                                </div>
                                @if($group->locationModel || $group->location)
                                    <div class="flex items-start gap-2 text-gray-700">
                                        <i class="fas fa-map-marker-alt text-red-500 mt-0.5"></i>
                                        <span>{{ $group->locationModel->name ?? $group->location ?? '—' }}</span>
                                    </div>
                                @endif
                                @if($group->start_date)
                                    <div class="flex items-center gap-2 text-gray-700">
                                        <i class="fas fa-calendar text-indigo-500"></i>
                                        <span>{{ $group->start_date->format('Y-m-d') }} @if($group->end_date) → {{ $group->end_date->format('Y-m-d') }} @endif</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-gray-600 text-sm">سعر الكورس:</span>
                                <span class="text-xl font-black text-blue-600">{{ number_format((float) $course->price, 2) }} <span class="text-sm text-gray-600">ج.م</span></span>
                            </div>
                            <div class="flex items-center justify-between pt-4 border-t-2 border-gray-200">
                                <span class="text-gray-900 font-bold">الإجمالي:</span>
                                <span class="text-2xl font-black text-green-600">{{ number_format((float) $course->price, 2) }} <span class="text-base text-gray-600">ج.م</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <div class="course-card p-6 md:p-8">
                            <h2 class="text-2xl font-black text-gray-900 mb-6 flex items-center gap-3">
                                <i class="fas fa-money-check-alt text-blue-600"></i>
                                إتمام طلب الحجز والدفع
                            </h2>

                            @if(session('success'))
                                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm flex items-center gap-2">
                                    <i class="fas fa-check-circle"></i>{{ session('success') }}
                                </div>
                            @endif

                            @if(!$courseScheduleOpen)
                                <div class="p-5 bg-amber-50 border-2 border-amber-200 rounded-xl text-amber-900 text-sm font-medium space-y-2">
                                    @if(($scheduleBlockReason ?? null) === 'not_started')
                                        <p><i class="fas fa-hourglass-start ml-2"></i> <strong>لم يبدأ الحجز بعد.</strong> موعد فتح الحجز المضبوط على الكورس:
                                            <span class="font-mono bg-white/80 px-2 py-0.5 rounded">{{ $course->booking_opens_at->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</span>
                                        </p>
                                        <p class="text-xs text-amber-800/90">عدّل «بداية الحجز» في تعديل الكورس الأوفلاين إلى تاريخ/وقت سابق، أو اتركه فارغاً ليكون الحجز مفتوحاً دائماً ضمن حدود «نهاية الحجز» فقط.</p>
                                    @elseif(($scheduleBlockReason ?? null) === 'ended')
                                        <p><i class="fas fa-door-closed ml-2"></i> <strong>انتهت فترة الحجز.</strong> نهاية الحجز كانت:
                                            <span class="font-mono bg-white/80 px-2 py-0.5 rounded">{{ $course->bookingClosesAtEffective()?->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</span>
                                        </p>
                                    @else
                                        <p><i class="fas fa-clock ml-2"></i> فترة الحجز غير مفتوحة حالياً (تحقق من حالة الكورس: يجب أن يكون <strong>نشطاً</strong> وليس مسودة).</p>
                                    @endif
                                    <p class="text-xs text-amber-800/90">إن اخترت «نهاية الحجز» بوقت 12:00 ص، تُحسب نهاية <strong>ذلك اليوم كاملاً</strong>.</p>
                                </div>
                            @elseif(!$groupHasRoom)
                                <div class="p-5 bg-rose-50 border-2 border-rose-200 rounded-xl text-rose-800 text-sm font-semibold">
                                    <i class="fas fa-users-slash ml-2"></i>
                                    تم اكتمال عدد هذه المجموعة (بما في ذلك الطلبات قيد المراجعة)، ولا يمكن استقبال حجوزات جديدة عبر هذا الرابط حالياً.
                                </div>
                            @elseif(!auth()->check())
                                <div class="p-6 bg-slate-50 rounded-xl border-2 border-slate-200 text-center space-y-4">
                                    <p class="text-gray-700">لإرسال طلب الحجز ورفع إيصال التحويل، يرجى تسجيل الدخول بحساب طالب.</p>
                                    <a href="{{ route('login', ['redirect' => url()->current()]) }}"
                                       class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-green-500 text-white px-8 py-3 rounded-full font-bold shadow-lg hover:shadow-xl transition-all">
                                        <i class="fas fa-sign-in-alt"></i>
                                        تسجيل الدخول للحجز
                                    </a>
                                </div>
                            @else
                                @if($errors->any())
                                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                                        <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="mb-6 p-5 bg-slate-50 rounded-xl border-2 border-slate-200">
                                    <p class="text-sm font-bold text-gray-900 mb-2">طرق الدفع المعتمدة</p>
                                    <p class="text-xs text-gray-600">تحويل بنكي أو محافظ إلكترونية (فودافون كاش، إنستاباي، …) حسب القنوات المفعّلة في المنصة. ارفع إيصال التحويل بعد الدفع.</p>
                                </div>

                                <form action="{{ $formRoute ?? route('public.offline-groups.book', $group->public_slug) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                                    @csrf
                                    <div>
                                        <label class="block text-sm font-bold text-gray-800 mb-2">طريقة التحويل <span class="text-rose-500">*</span></label>
                                        <select name="payment_method" x-model="method" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                                            <option value="bank_transfer">تحويل بنكي</option>
                                            @if($walletChannelsExist)
                                                <option value="wallet">محفظة إلكترونية</option>
                                            @endif
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-gray-800 mb-2">الاسم <span class="text-rose-500">*</span></label>
                                        <input type="text" name="transfer_name" value="{{ old('transfer_name') }}" required
                                               class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500"
                                               placeholder="اكتب اسمك / اسم صاحب التحويل كما يظهر في الإيصال">
                                    </div>

                                    @if($wallets->isNotEmpty())
                                        <div x-show="method === 'wallet' || method === 'bank_transfer'" x-cloak class="space-y-2">
                                            <label class="block text-sm font-bold text-gray-800">
                                                حساب التحويل / المحفظة
                                                <span class="text-rose-500" x-show="method === 'wallet'">*</span>
                                                <span class="text-gray-400 font-normal text-xs" x-show="method === 'bank_transfer'">(اختياري للتحويل البنكي)</span>
                                            </label>
                                            <select name="wallet_id" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500" x-bind:required="method === 'wallet'">
                                                <option value="">— اختر —</option>
                                                @foreach($wallets as $w)
                                                    <option value="{{ $w->id }}" @selected(old('wallet_id') == $w->id)>
                                                        {{ \App\Models\Wallet::typeLabel($w->type) }}
                                                        @if($w->name) — {{ $w->name }} @endif
                                                        @if($w->account_number) ({{ $w->account_number }}) @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    @if((float) $course->price > 0)
                                        <div>
                                            <label class="block text-sm font-bold text-gray-800 mb-2">صورة إيصال التحويل <span class="text-rose-500">*</span></label>
                                            <input type="file" name="payment_proof" accept="image/jpeg,image/png,image/jpg" required class="block w-full text-sm text-gray-600">
                                            <p class="text-xs text-gray-500 mt-1">حد أقصى 2 ميجابايت — jpg أو png</p>
                                        </div>
                                    @else
                                        <div>
                                            <label class="block text-sm font-bold text-gray-800 mb-2">صورة إيصال (اختياري)</label>
                                            <input type="file" name="payment_proof" accept="image/jpeg,image/png,image/jpg" class="block w-full text-sm text-gray-600">
                                        </div>
                                    @endif

                                    <div>
                                        <label class="block text-sm font-bold text-gray-800 mb-2">ملاحظات (اختياري)</label>
                                        <textarea name="student_notes" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500" placeholder="اسمك كما في التحويل أو أي تفاصيل">{{ old('student_notes') }}</textarea>
                                    </div>

                                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 via-blue-500 to-green-500 text-white px-8 py-4 rounded-full font-bold text-lg shadow-xl hover:shadow-2xl transition-all">
                                        <i class="fas fa-paper-plane"></i>
                                        إرسال طلب الحجز
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('components.unified-footer')
</body>
</html>
