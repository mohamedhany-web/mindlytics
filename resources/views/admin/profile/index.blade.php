@extends('layouts.admin')

@section('title', 'الملف الشخصي - لوحة الإدارة')
@section('header', 'الملف الشخصي')

@section('content')
@php
    $roleLabels = [
        'admin' => ['label' => 'إداري', 'color' => 'from-indigo-500 to-violet-600', 'chip' => 'bg-gradient-to-r from-indigo-500/15 to-violet-600/15 text-indigo-600 border-2 border-indigo-500/30'],
        'super_admin' => ['label' => 'مدير عام', 'color' => 'from-blue-600 to-indigo-700', 'chip' => 'bg-gradient-to-r from-blue-600/15 to-indigo-700/15 text-blue-600 border-2 border-blue-600/30'],
    ];
    $roleMeta = $roleLabels[$user->role] ?? ['label' => 'إداري', 'color' => 'from-slate-500 to-slate-600', 'chip' => 'bg-slate-500/15 text-slate-700 border border-slate-500/40'];

    $memberSince = $user->created_at ? $user->created_at->copy()->locale('ar')->translatedFormat('d F Y') : '—';
    $lastLogin = $user->last_login_at ? $user->last_login_at->copy()->locale('ar')->diffForHumans() : '—';

    $stats = [
        ['icon' => 'fa-calendar-week', 'label' => 'تاريخ الانضمام', 'value' => $memberSince, 'color' => 'from-blue-500 to-blue-400'],
        ['icon' => 'fa-user-shield', 'label' => 'نوع الحساب', 'value' => $roleMeta['label'], 'color' => 'from-indigo-500 to-violet-600'],
        ['icon' => 'fa-clock-rotate-left', 'label' => 'آخر تسجيل دخول', 'value' => $lastLogin, 'color' => 'from-amber-400 to-amber-500'],
    ];
@endphp

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6">
    @if(session('recovery_codes'))
        <div class="rounded-2xl border-2 border-amber-200 bg-amber-50 p-6">
            <h3 class="font-black text-amber-900 mb-2 flex items-center gap-2"><i class="fas fa-key"></i> رموز الاسترداد — احفظها في مكان آمن</h3>
            <p class="text-sm text-amber-800 mb-4">استخدم أحد هذه الرموز للدخول إذا لم يكن معك جهاز المصادقة. كل رمز يُستخدم مرة واحدة فقط.</p>
            <div class="grid grid-cols-2 gap-2 font-mono text-sm">
                @foreach(session('recovery_codes') as $code)
                    <span class="bg-white px-3 py-2 rounded-lg border border-amber-200">{{ $code }}</span>
                @endforeach
            </div>
            @php session()->forget('recovery_codes'); @endphp
        </div>
    @endif
    <!-- الهيدر -->
    <div class="rounded-2xl border-2 border-slate-200/50 shadow-xl bg-white overflow-hidden">
        <div class="p-5 sm:p-6">
            <div class="flex flex-col lg:flex-row items-start lg:items-center gap-6 lg:justify-between">
                <div class="flex flex-col sm:flex-row sm:items-center gap-5 w-full lg:w-auto">
                    <div class="profile-avatar flex items-center justify-center h-24 w-24 sm:h-28 sm:w-28 rounded-2xl bg-gradient-to-br {{ $roleMeta['color'] }} text-white overflow-hidden mx-auto sm:mx-0 shadow-lg">
                        @if($user->profile_image)
                            <img src="{{ asset($user->profile_image) }}" alt="صورة الملف الشخصي" class="w-full h-full object-cover">
                        @else
                            <span class="text-4xl sm:text-5xl font-black leading-none">{{ mb_substr($user->name, 0, 1) }}</span>
                        @endif
                    </div>
                    <div class="flex-1 text-center sm:text-right">
                        <span class="inline-flex items-center gap-2 rounded-xl {{ $roleMeta['chip'] }} px-4 py-2 text-xs font-bold mb-3">
                            <i class="fas fa-user-shield"></i>
                            {{ $roleMeta['label'] }}
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-800 mb-2">{{ $user->name }}</h1>
                        <p class="text-sm text-slate-600 font-medium mb-3">إدارة بياناتك وإعدادات حسابك الشخصي</p>
                        <div class="flex flex-wrap justify-center sm:justify-end gap-2 text-sm">
                            <span class="inline-flex items-center gap-2 rounded-xl bg-blue-50 text-slate-700 px-4 py-2 font-bold border border-blue-200/50">
                                <i class="fas fa-phone text-blue-600"></i>
                                {{ $user->phone ?? '—' }}
                            </span>
                            @if($user->email)
                                <span class="inline-flex items-center gap-2 rounded-xl bg-blue-50 text-slate-700 px-4 py-2 font-bold border border-blue-200/50">
                                    <i class="fas fa-envelope text-blue-600"></i>
                                    {{ $user->email }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 w-full lg:w-auto">
                    @foreach ($stats as $stat)
                        <div class="rounded-xl p-4 bg-gradient-to-br from-slate-50 to-white border border-slate-200/50 text-center">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $stat['color'] }} flex items-center justify-center text-white mx-auto mb-2 shadow-md">
                                <i class="fas {{ $stat['icon'] }} text-sm"></i>
                            </div>
                            <div class="text-xs font-semibold text-slate-600 mb-1">{{ $stat['label'] }}</div>
                            <div class="text-sm font-bold text-slate-800">{{ $stat['value'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- المحتوى -->
    <div class="grid grid-cols-1 gap-6 lg:gap-8 lg:grid-cols-3">
        <!-- البطاقة الجانبية -->
        <div class="space-y-6">
            <div class="rounded-2xl border-2 border-slate-200/50 shadow-lg bg-white p-6">
                <h2 class="text-lg font-black text-slate-800 mb-5 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white">
                        <i class="fas fa-info-circle text-sm"></i>
                    </div>
                    <span>معلومات الحساب</span>
                </h2>
                <div class="space-y-4 text-sm">
                    <div class="flex items-center justify-between gap-4 p-3 bg-blue-50/50 rounded-xl border border-blue-100">
                        <span class="font-bold text-slate-600">رقم العضوية</span>
                        <span class="text-slate-800 font-black">#{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="font-bold text-slate-600">نوع الحساب</span>
                        <span class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $roleMeta['chip'] }}">{{ $roleMeta['label'] }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4 p-3 rounded-xl border {{ $user->is_active ? 'bg-emerald-50/50 border-emerald-200' : 'bg-rose-50/50 border-rose-200' }}">
                        <span class="font-bold text-slate-600">الحالة</span>
                        <span class="inline-flex items-center gap-2 text-xs font-bold {{ $user->is_active ? 'text-emerald-700' : 'text-rose-700' }}">
                            <span class="relative flex h-2 w-2">
                                <span class="absolute inline-flex h-full w-full rounded-full opacity-75 {{ $user->is_active ? 'bg-emerald-500 animate-ping' : 'bg-rose-500' }}"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full {{ $user->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                            </span>
                            {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- المصادقة الثنائية (2FA) -->
            <div class="rounded-2xl border-2 border-slate-200/50 shadow-lg bg-white p-6">
                <h2 class="text-lg font-black text-slate-800 mb-5 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white">
                        <i class="fas fa-shield-alt text-sm"></i>
                    </div>
                    <span>المصادقة الثنائية</span>
                </h2>
                @if($user->hasTwoFactorEnabled())
                    <p class="text-sm text-slate-600 mb-4">مفعّلة — يتم طلب رمز التحقق عند كل تسجيل دخول.</p>
                    <form action="{{ route('two-factor.disable') }}" method="POST" class="space-y-3" onsubmit="return confirm('هل تريد تعطيل المصادقة الثنائية؟ ستحتاج إدخال كلمة المرور.');">
                        @csrf
                        <input type="password" name="password" required placeholder="كلمة المرور للتأكيد" class="w-full rounded-xl border-2 border-slate-200 px-4 py-2.5 text-sm">
                        @error('password')
                            <p class="text-red-600 text-xs">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="w-full py-2.5 rounded-xl border-2 border-red-200 text-red-700 font-bold text-sm hover:bg-red-50 transition-colors">
                            تعطيل المصادقة الثنائية
                        </button>
                    </form>
                @else
                    <p class="text-sm text-slate-600 mb-4">تفعيل المصادقة الثنائية يزيد أمان دخولك للمنصة.</p>
                    <a href="{{ route('two-factor.setup') }}" class="flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 text-white font-bold text-sm shadow-md hover:shadow-lg transition-all">
                        <i class="fas fa-mobile-alt"></i>
                        تفعيل المصادقة الثنائية
                    </a>
                @endif
            </div>
        </div>

        <!-- نموذج التحديث -->
        <div class="lg:col-span-2">
            <div class="rounded-2xl border-2 border-slate-200/50 shadow-lg bg-white p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
                    <div>
                        <h3 class="text-xl font-black text-slate-800 mb-2">تحديث البيانات الأساسية</h3>
                        <p class="text-sm text-slate-600 font-medium">قم بمراجعة معلوماتك وتحديثها في أي وقت</p>
                    </div>
                    <span class="inline-flex items-center gap-2 text-xs font-bold rounded-xl bg-blue-50 text-blue-600 border border-blue-200 px-4 py-2">
                        <i class="fas fa-shield-check"></i>
                        بياناتك مشفرة وآمنة
                    </span>
                </div>

                <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-6" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-2">الاسم الكامل</label>
                            <div class="relative">
                                <i class="fas fa-user absolute right-4 top-1/2 -translate-y-1/2 text-slate-500"></i>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                       class="w-full rounded-xl border-2 border-slate-200 bg-white pr-11 pl-4 py-3 text-slate-800 font-medium focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                            </div>
                            @error('name')
                                <p class="text-red-600 text-xs mt-2 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-2">رقم الهاتف</label>
                            <div class="relative">
                                <i class="fas fa-phone absolute right-4 top-1/2 -translate-y-1/2 text-slate-500"></i>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required
                                       class="w-full rounded-xl border-2 border-slate-200 bg-white pr-11 pl-4 py-3 text-slate-800 font-medium focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                            </div>
                            @error('phone')
                                <p class="text-red-600 text-xs mt-2 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-slate-800 mb-2">البريد الإلكتروني (اختياري)</label>
                            <div class="relative">
                                <i class="fas fa-at absolute right-4 top-1/2 -translate-y-1/2 text-slate-500"></i>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                       class="w-full rounded-xl border-2 border-slate-200 bg-white pr-11 pl-4 py-3 text-slate-800 font-medium focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                            </div>
                            @error('email')
                                <p class="text-red-600 text-xs mt-2 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-3">صورة الملف الشخصي</label>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-2xl overflow-hidden border-2 border-dashed border-slate-200 bg-slate-50 flex items-center justify-center flex-shrink-0">
                                @if($user->profile_image)
                                    <img src="{{ asset($user->profile_image) }}" alt="صورة الملف الشخصي" class="w-full h-full object-cover">
                                @else
                                    <i class="fas fa-camera text-slate-400 text-3xl"></i>
                                @endif
                            </div>
                            <div class="flex-1">
                                <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 px-6 py-3 text-sm font-bold text-slate-700 hover:bg-slate-100 transition-all">
                                    <i class="fas fa-upload text-blue-600"></i>
                                    <span>اختر صورة جديدة (PNG أو JPG - حد أقصى 2 ميجابايت)</span>
                                    <input type="file" name="profile_image" accept="image/*" class="hidden">
                                </label>
                                @error('profile_image')
                                    <p class="text-red-600 text-xs mt-2 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 p-6 space-y-4">
                        <h4 class="text-base font-black text-slate-800">تغيير كلمة المرور</h4>
                        <p class="text-xs text-slate-600">اترك الحقول فارغة إذا لم ترغب في التغيير</p>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-2">كلمة المرور الحالية</label>
                                <input type="password" name="current_password"
                                       class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                                @error('current_password')
                                    <p class="text-red-600 text-xs mt-2 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-2">كلمة المرور الجديدة</label>
                                <input type="password" name="password"
                                       class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                                @error('password')
                                    <p class="text-red-600 text-xs mt-2 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-2">تأكيد كلمة المرور</label>
                                <input type="password" name="password_confirmation"
                                       class="w-full rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between pt-4 border-t border-slate-200">
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50 transition-all">
                            <i class="fas fa-arrow-right"></i>
                            رجوع للوحة التحكم
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-3 text-sm font-bold text-white shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all">
                            <i class="fas fa-save"></i>
                            حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
