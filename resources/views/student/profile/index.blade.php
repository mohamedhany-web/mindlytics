@extends('layouts.app')

@section('title', 'الملف الشخصي')
@section('header', 'الملف الشخصي')

@push('styles')
<style>
    .info-card {
        background: white;
        border: 1px solid rgb(229 231 235);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
</style>
@endpush

@section('content')
@php
    use Illuminate\Support\Str;
    $roleLabels = [
        'student' => ['label' => 'طالب', 'color' => 'from-sky-500 to-sky-400', 'chip' => 'bg-gradient-to-r from-sky-500/15 to-sky-400/15 text-sky-500 border-2 border-sky-500/30'],
        'teacher' => ['label' => 'معلم', 'color' => 'from-emerald-500 to-green-600', 'chip' => 'bg-gradient-to-r from-emerald-500/15 to-green-600/15 text-emerald-600 border-2 border-emerald-500/30'],
        'admin' => ['label' => 'إداري', 'color' => 'from-indigo-500 to-violet-600', 'chip' => 'bg-gradient-to-r from-indigo-500/15 to-violet-600/15 text-indigo-600 border-2 border-indigo-500/30'],
        'super_admin' => ['label' => 'مدير عام', 'color' => 'from-blue-600 to-indigo-700', 'chip' => 'bg-gradient-to-r from-blue-600/15 to-indigo-700/15 text-blue-600 border-2 border-blue-600/30'],
    ];

    $roleMeta = $roleLabels[$user->role] ?? ['label' => 'مستخدم', 'color' => 'from-slate-500 to-slate-600', 'chip' => 'bg-slate-500/15 text-gray-200 border border-slate-500/40'];

    $memberSince = null;
    if ($user && $user->created_at instanceof \Carbon\CarbonInterface) {
        $memberSince = $user->created_at->copy()->locale('ar')->translatedFormat('d F Y');
    }

    $coursesCount = method_exists($user, 'courseEnrollments') ? $user->courseEnrollments()->count() : 0;
    $notificationsCount = method_exists($user, 'notifications') ? $user->notifications()->count() : 0;

    $lastLogin = null;
    if ($user && $user->last_login_at instanceof \Carbon\CarbonInterface) {
        $lastLogin = $user->last_login_at->copy()->locale('ar')->diffForHumans();
    }

    $stats = [
        ['icon' => 'fa-calendar-week', 'label' => 'تاريخ الانضمام', 'value' => $memberSince ?: '—', 'color' => 'from-sky-500 to-sky-400'],
        ['icon' => 'fa-layer-group', 'label' => 'الكورسات النشطة', 'value' => $coursesCount, 'color' => 'from-purple-500 to-indigo-600'],
        ['icon' => 'fa-bell', 'label' => 'الإشعارات', 'value' => $notificationsCount, 'color' => 'from-emerald-500 to-emerald-600'],
        ['icon' => 'fa-clock-rotate-left', 'label' => 'آخر تسجيل دخول', 'value' => $lastLogin ?: '—', 'color' => 'from-amber-400 to-amber-500'],
    ];
@endphp

<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 sm:p-6">
        <div class="flex flex-col lg:flex-row items-start lg:items-center gap-6 lg:justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center gap-5 w-full lg:w-auto">
                <div class="profile-avatar flex items-center justify-center h-24 w-24 sm:h-28 sm:w-28 rounded-2xl bg-gradient-to-br {{ $roleMeta['color'] }} text-white overflow-hidden mx-auto sm:mx-0">
                    @if($user->profile_image)
                        <img src="{{ $user->profile_image_url }}" alt="صورة الملف الشخصي" class="w-full h-full object-cover">
                    @else
                        <span class="text-4xl sm:text-5xl font-black leading-none">{{ mb_substr($user->name, 0, 1) }}</span>
                    @endif
                </div>
                <div class="flex-1 text-center sm:text-right">
                    <div class="mb-3">
                        <span class="inline-flex items-center gap-2 rounded-xl {{ $roleMeta['chip'] }} px-4 py-2 text-xs font-bold mb-3">
                            <i class="fas fa-user-shield"></i>
                            {{ $roleMeta['label'] }}
                        </span>
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-gray-900 mb-2">{{ $user->name }}</h1>
                        <p class="text-sm sm:text-base text-gray-600 font-medium">إدارة بياناتك وإعدادات حسابك الشخصي</p>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:justify-end gap-3 text-sm">
                        <span class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-500/10 to-sky-400/10 text-gray-700 px-4 py-2 font-bold border-2 border-sky-500/20">
                            <i class="fas fa-phone text-sky-500"></i>
                            {{ $user->phone ?? '—' }}
                        </span>
                        @if($user->email)
                            <span class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-500/10 to-sky-400/10 text-gray-700 px-4 py-2 font-bold border-2 border-sky-500/20">
                                <i class="fas fa-envelope text-sky-500"></i>
                                {{ $user->email }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 w-full lg:w-auto">
                @foreach ($stats as $stat)
                    <div class="stats-mini-card rounded-xl p-4 text-center">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $stat['color'] }} flex items-center justify-center text-white mx-auto mb-2 shadow-md">
                            <i class="fas {{ $stat['icon'] }} text-sm"></i>
                        </div>
                        <div class="text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">{{ $stat['label'] }}</div>
                        <div class="text-base sm:text-lg font-black text-gray-900">
                            {{ $stat['value'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- المحتوى الرئيسي -->
    <div class="grid grid-cols-1 gap-6 lg:gap-8 lg:grid-cols-3">
        <!-- البطاقات الجانبية -->
        <div class="space-y-6">
            <div class="info-card rounded-2xl p-6 shadow-lg">
                <h2 class="text-lg sm:text-xl font-black text-gray-900 mb-5 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-sky-500 to-sky-400 flex items-center justify-center text-white">
                        <i class="fas fa-info-circle text-sm"></i>
                    </div>
                    <span>معلومات الاتصال</span>
                </h2>
                <div class="space-y-4 text-sm">
                    <div class="flex items-center justify-between gap-4 p-3 bg-gradient-to-r from-sky-500/5 to-sky-400/5 rounded-xl">
                        <div class="flex items-center gap-3 text-gray-600">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-sky-400 text-white shadow-md"><i class="fas fa-id-badge"></i></span>
                            <span class="font-bold">رقم العضوية</span>
                        </div>
                        <span class="text-gray-900 font-black text-base">#{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 p-3 bg-gradient-to-r from-purple-500/5 to-indigo-500/5 rounded-xl">
                        <div class="flex items-center gap-3 text-gray-600">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 text-white shadow-md"><i class="fas fa-user-shield"></i></span>
                            <span class="font-bold">نوع الحساب</span>
                        </div>
                        <span class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $roleMeta['chip'] }}">{{ $roleMeta['label'] }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 p-3 bg-gradient-to-r from-green-500/5 to-emerald-500/5 rounded-xl">
                        <div class="flex items-center gap-3 text-gray-600">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 text-white shadow-md"><i class="fas fa-signal"></i></span>
                            <span class="font-bold">الحالة</span>
                        </div>
                        <span class="inline-flex items-center gap-2 rounded-xl px-3 py-1.5 text-xs font-bold {{ $user->is_active ? 'bg-gradient-to-r from-green-500/15 to-emerald-600/15 text-green-700 border-2 border-green-500/30' : 'bg-gradient-to-r from-red-500/15 to-rose-600/15 text-red-700 border-2 border-red-500/30' }}">
                            <span class="relative flex h-2 w-2">
                                <span class="absolute inline-flex h-full w-full rounded-full opacity-75 {{ $user->is_active ? 'bg-green-500 animate-ping' : 'bg-red-500' }}"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                            </span>
                            {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                    <div class="flex items-start gap-3 rounded-xl bg-gradient-to-r from-sky-500/10 to-sky-400/10 border-2 border-sky-500/20 px-4 py-3 text-gray-600">
                        <span class="mt-1 text-sky-500"><i class="fas fa-shield-halved"></i></span>
                        <p class="text-sm font-medium">يمكنك تحسين أمان حسابك بتفعيل التحقق بخطوتين من الإعدادات المتقدمة (قريباً).</p>
                    </div>
                </div>
            </div>

            <div class="info-card rounded-2xl p-6 shadow-lg">
                <h2 class="text-lg sm:text-xl font-black text-gray-900 mb-5 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-amber-400 to-amber-500 flex items-center justify-center text-white">
                        <i class="fas fa-lightbulb text-sm"></i>
                    </div>
                    <span>نصائح سريعة</span>
                </h2>
                <ul class="space-y-4 text-sm text-gray-600">
                    <li class="flex items-start gap-3 p-3 bg-gradient-to-r from-sky-500/5 to-sky-400/5 rounded-xl">
                        <span class="mt-1 text-sky-500"><i class="fas fa-check-circle"></i></span>
                        <div>
                            <p class="font-bold text-gray-900">حدّث معلومات التواصل</p>
                            <p class="mt-1 text-xs text-gray-600">احرص على أن يكون بريدك الإلكتروني ورقم هاتفك محدثين لاستقبال كل الإشعارات المهمة.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3 p-3 bg-gradient-to-r from-green-500/5 to-emerald-500/5 rounded-xl">
                        <span class="mt-1 text-green-600"><i class="fas fa-lock"></i></span>
                        <div>
                            <p class="font-bold text-gray-900">أنشئ كلمة مرور قوية</p>
                            <p class="mt-1 text-xs text-gray-600">استخدم مزيجاً من الأحرف والأرقام والرموز، وقم بتغيير كلمة المرور بشكل دوري.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3 p-3 bg-gradient-to-r from-purple-500/5 to-indigo-500/5 rounded-xl">
                        <span class="mt-1 text-purple-600"><i class="fas fa-bell"></i></span>
                        <div>
                            <p class="font-bold text-gray-900">فعّل الإشعارات</p>
                            <p class="mt-1 text-xs text-gray-600">ابقَ على اطلاع بالمستجدات من خلال متابعة جديد الكورسات والتنبيهات.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- النماذج -->
        <div class="lg:col-span-2 space-y-6">
            <div class="info-card rounded-2xl p-6 sm:p-8 shadow-lg">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
                    <div>
                        <h3 class="text-xl sm:text-2xl font-black text-gray-900 mb-2">تحديث البيانات الأساسية</h3>
                        <p class="text-sm sm:text-base text-gray-600 font-medium">قم بمراجعة معلوماتك وتحديثها في أي وقت</p>
                    </div>
                    <span class="inline-flex items-center gap-2 text-xs font-bold rounded-xl bg-gradient-to-r from-sky-500/10 to-sky-400/10 text-sky-500 border-2 border-sky-500/20 px-4 py-2">
                        <i class="fas fa-shield-check"></i>
                        بياناتك مشفرة وآمنة
                    </span>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-8" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="group">
                            <label class="block text-sm font-bold text-gray-900 mb-2">الاسم الكامل</label>
                            <div class="relative">
                                <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-600 group-focus-within:text-sky-500 transition-colors"></i>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                       class="form-input w-full rounded-xl border-2 border-sky-500/20 bg-white px-11 py-3.5 text-gray-900 font-medium shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20">
                            </div>
                            @error('name')
                                <p class="text-red-600 text-xs mt-2 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="group">
                            <label class="block text-sm font-bold text-gray-900 mb-2">رقم الهاتف</label>
                            <div class="relative">
                                <i class="fas fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-gray-600 group-focus-within:text-sky-500 transition-colors"></i>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required
                                       class="form-input w-full rounded-xl border-2 border-sky-500/20 bg-white px-11 py-3.5 text-gray-900 font-medium shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20">
                            </div>
                            @error('phone')
                                <p class="text-red-600 text-xs mt-2 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2 group">
                            <label class="block text-sm font-bold text-gray-900 mb-2">البريد الإلكتروني (اختياري)</label>
                            <div class="relative">
                                <i class="fas fa-at absolute left-4 top-1/2 -translate-y-1/2 text-gray-600 group-focus-within:text-sky-500 transition-colors"></i>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                       class="form-input w-full rounded-xl border-2 border-sky-500/20 bg-white px-11 py-3.5 text-gray-900 font-medium shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20">
                            </div>
                            @error('email')
                                <p class="text-red-600 text-xs mt-2 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="block text-sm font-bold text-gray-900 mb-3">صورة الملف الشخصي</label>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-2xl overflow-hidden border-2 border-dashed border-sky-500/30 bg-gradient-to-br from-sky-500/5 to-sky-400/5 flex items-center justify-center">
                                @if($user->profile_image)
                                    <img src="{{ $user->profile_image_url }}" alt="صورة الملف الشخصي" class="w-full h-full object-cover">
                                @else
                                    <i class="fas fa-camera text-sky-500 text-3xl"></i>
                                @endif
                            </div>
                            <div class="flex-1">
                                <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border-2 border-dashed border-sky-500/30 bg-gradient-to-r from-sky-500/10 to-sky-400/10 px-6 py-3 text-sm font-bold text-gray-900 hover:from-sky-500/20 hover:to-sky-400/20 transition-all">
                                    <i class="fas fa-upload text-sky-500"></i>
                                    <span>اختر صورة جديدة (PNG أو JPG)</span>
                                    <input type="file" name="profile_image" accept="image/*" class="hidden">
                                </label>
                                <p class="mt-2 text-xs text-gray-600 font-medium">الحد الأقصى لحجم الملف 2 ميجابايت.</p>
                                @error('profile_image')
                                    <p class="text-red-600 text-xs mt-2 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6 rounded-2xl border-2 border-dashed border-sky-500/20 bg-gradient-to-r from-sky-500/5 to-sky-400/5 p-6">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h4 class="text-base sm:text-lg font-black text-gray-900 mb-1">تغيير كلمة المرور</h4>
                                <p class="text-xs text-gray-600 font-medium">اترك الحقول فارغة إذا لم ترغب في التغيير الآن</p>
                            </div>
                            <span class="inline-flex items-center gap-2 text-xs font-bold text-sky-500 bg-white/50 px-3 py-1.5 rounded-xl border border-sky-500/20">
                                <i class="fas fa-key"></i> 
                                نصيحة: استخدم كلمة مرور مكونة من 12 حرفًا على الأقل
                            </span>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div class="group">
                                <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 mb-2">كلمة المرور الحالية</label>
                                <input type="password" name="current_password"
                                       class="form-input w-full rounded-xl border-2 border-sky-500/20 bg-white px-4 py-3 text-sm text-gray-900 font-medium focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20">
                                @error('current_password')
                                    <p class="text-red-600 text-xs mt-2 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="group">
                                <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 mb-2">كلمة المرور الجديدة</label>
                                <input type="password" name="password"
                                       class="form-input w-full rounded-xl border-2 border-green-500/20 bg-white px-4 py-3 text-sm text-gray-900 font-medium focus:border-green-500 focus:ring-4 focus:ring-green-500/20">
                                @error('password')
                                    <p class="text-red-600 text-xs mt-2 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="group">
                                <label class="block text-xs font-bold uppercase tracking-wide text-gray-600 mb-2">تأكيد كلمة المرور</label>
                                <input type="password" name="password_confirmation"
                                       class="form-input w-full rounded-xl border-2 border-green-500/20 bg-white px-4 py-3 text-sm text-gray-900 font-medium focus:border-green-500 focus:ring-4 focus:ring-green-500/20">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between pt-4 border-t-2 border-sky-500/10">
                        <div class="text-xs text-gray-600 flex items-center gap-2 font-medium">
                            <i class="fas fa-info-circle text-sky-500"></i>
                            <span>سيتم إرسال إشعار إلى بريدك في حال تغيير كلمة المرور.</span>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-sky-500/20 bg-white px-6 py-3 text-sm font-bold text-gray-900 hover:border-sky-500/40 hover:bg-sky-500/5 transition-all">
                                <i class="fas fa-arrow-right"></i>
                                رجوع إلى اللوحة
                            </a>
                            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-500 via-sky-400 to-sky-500 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-sky-500/30 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-sky-500/30 transition-all transform hover:scale-105">
                                <i class="fas fa-save"></i>
                                حفظ التعديلات
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="info-card rounded-2xl p-6 sm:p-8 shadow-lg">
                <h3 class="text-lg sm:text-xl font-black text-gray-900 mb-5 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-sky-500 to-sky-400 flex items-center justify-center text-white">
                        <i class="fas fa-history text-sm"></i>
                    </div>
                    <span>نشاط الحساب الأخير</span>
                </h3>
                <div class="space-y-4 text-sm">
                    <div class="flex items-center justify-between rounded-xl border-2 border-sky-500/10 bg-gradient-to-r from-sky-500/5 to-sky-400/5 px-4 py-4">
                        <div class="flex items-center gap-3">
                            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-sky-400 text-white shadow-md"><i class="fas fa-desktop"></i></span>
                            <div>
                                <p class="font-bold text-gray-900">آخر نشاط للنظام</p>
                                <p class="text-xs text-gray-600 font-medium">تم تسجيل الدخول بنجاح باستخدام متصفح آمن</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-600 font-bold">{{ $lastLogin ?: 'قبل قليل' }}</span>
                    </div>

                    <div class="flex items-center justify-between rounded-xl border-2 border-green-500/10 bg-gradient-to-r from-green-500/5 to-emerald-500/5 px-4 py-4">
                        <div class="flex items-center gap-3">
                            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 text-white shadow-md"><i class="fas fa-shield-heart"></i></span>
                            <div>
                                <p class="font-bold text-gray-900">أمان الحساب</p>
                                <p class="text-xs text-gray-600 font-medium">ننصح بتحديث كلمة المرور كل 90 يومًا للحفاظ على أعلى درجات الحماية.</p>
                            </div>
                        </div>
                        <a href="#" class="text-xs font-bold text-sky-500 hover:text-gray-600 transition-colors">تعلم المزيد</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

