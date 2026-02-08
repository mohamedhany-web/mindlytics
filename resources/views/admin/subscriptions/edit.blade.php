@extends('layouts.admin')

@section('title', 'تعديل الاشتراك')
@section('header', 'تعديل الاشتراك')

@section('content')
@php
    $typeOptions = \App\Models\Subscription::typeLabels();
    $cycleOptions = \App\Models\Subscription::billingCycleLabels();
@endphp
<div class="container mx-auto px-4 py-8 space-y-6">
    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl">
            <strong class="font-semibold">حدثت أخطاء:</strong>
            <ul class="list-disc pr-6 mt-2 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-black text-gray-900">تعديل الاشتراك</h1>
                    <p class="text-sm text-gray-500 mt-1">قم بتحديث تفاصيل الخطة والفترة الزمنية للمستخدم.</p>
                </div>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-sky-100 text-sky-600">
                    <i class="fas fa-id-card"></i>
                    {{ $subscription->plan_name }}
                </span>
            </div>

            <form action="{{ route('admin.subscriptions.update', $subscription) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">المستخدم *</label>
                        <select name="user_id" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $subscription->user_id == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} — {{ $user->phone }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">نوع الاشتراك *</label>
                        <select name="subscription_type" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            @foreach($typeOptions as $value => $label)
                                <option value="{{ $value }}" {{ $subscription->subscription_type === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">اسم الخطة *</label>
                        <input type="text" name="plan_name" value="{{ old('plan_name', $subscription->plan_name) }}" required
                               class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">السعر *</label>
                        <div class="relative">
                            <input type="number" name="price" step="0.01" min="0" value="{{ old('price', $subscription->price) }}" required
                                   class="w-full pl-12 pr-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            <span class="absolute inset-y-0 left-4 flex items-center text-sm font-semibold text-gray-500">ج.م</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">تاريخ البداية *</label>
                        <input type="date" name="start_date" value="{{ old('start_date', optional($subscription->start_date)->format('Y-m-d')) }}" required
                               class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">تاريخ الانتهاء *</label>
                        <input type="date" name="end_date" value="{{ old('end_date', optional($subscription->end_date)->format('Y-m-d')) }}" required
                               class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">دورة الفوترة *</label>
                        <select name="billing_cycle" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            @foreach($cycleOptions as $value => $label)
                                <option value="{{ $value }}" {{ $subscription->billing_cycle === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">الحالة *</label>
                        <select name="status" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            <option value="active" {{ $subscription->status === 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="expired" {{ $subscription->status === 'expired' ? 'selected' : '' }}>منتهي</option>
                            <option value="cancelled" {{ $subscription->status === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between bg-gray-50 border border-gray-100 rounded-2xl px-4 py-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">تفعيل التجديد التلقائي</p>
                        <p class="text-xs text-gray-500">في حالة التفعيل سيتم تجديد الاشتراك تلقائياً بعد انتهاء المدة.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="auto_renew" value="1" class="sr-only peer" {{ old('auto_renew', $subscription->auto_renew) ? 'checked' : '' }}>
                        <div class="w-12 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-sky-500 rounded-full peer peer-checked:after:translate-x-6 peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sky-500"></div>
                    </label>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white font-semibold shadow-lg shadow-sky-500/30 transition-all">
                        <i class="fas fa-save"></i>
                        تحديث الاشتراك
                    </button>
                    <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold transition-all">
                        إلغاء والرجوع
                    </a>
                </div>
            </form>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-lg font-black text-gray-900 mb-4">ملخص الاشتراك الحالي</h2>
                <div class="space-y-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">المستخدم</p>
                        <p class="mt-1 text-base font-semibold text-gray-900">{{ $subscription->user->name ?? 'غير معروف' }}</p>
                        <p class="text-xs text-gray-500">{{ $subscription->user->phone ?? 'بدون رقم' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">الفترة الحالية</p>
                        <p class="mt-1 text-base font-semibold text-gray-900">{{ $subscription->start_date?->format('Y-m-d') ?? 'غير محدد' }} → {{ $subscription->end_date?->format('Y-m-d') ?? 'غير محدد' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">تاريخ التحديث الأخير</p>
                        <p class="mt-1 text-base font-semibold text-gray-900">{{ optional($subscription->updated_at)->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-lg font-black text-gray-900 mb-4">نصائح سريعة</h2>
                <ul class="space-y-3 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-info-circle mt-1 text-sky-500"></i>
                        تأكد من أن تاريخ الانتهاء بعد تاريخ البداية لتجنب رفض النموذج.
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-sync mt-1 text-sky-500"></i>
                        قم بتفعيل التجديد التلقائي للحسابات المستمرة لتوفير الوقت.
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-receipt mt-1 text-sky-500"></i>
                        اربط الاشتراك بالفاتورة المناسبة لضمان تتبع مالي دقيق.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

