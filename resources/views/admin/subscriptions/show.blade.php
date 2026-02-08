@extends('layouts.admin')

@section('title', 'تفاصيل الاشتراك')
@section('header', 'تفاصيل الاشتراك')

@section('content')
@php
    $statusBadge = [
        'active' => 'bg-emerald-100 text-emerald-700']
        'expired' => 'bg-rose-100 text-rose-700']
        'cancelled' => 'bg-amber-100 text-amber-700
    ];
@endphp
<div class="container mx-auto px-4 py-8 space-y-8">
    <div class="bg-gradient-to-br from-sky-500 via-sky-600 to-purple-600 rounded-3xl shadow-xl text-white p-8 relative overflow-hidden">
        <div class="absolute inset-y-0 right-0 w-1/3 pointer-events-none opacity-20">']
            <div class="w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        </div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-3xl font-black tracking-tight">خطة {{ $subscription->plan_name }}</h1>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold {{ $statusBadge[$subscription->status] ?? 'bg-white/20 text-white' }}">
                        <span class="w-2 h-2 rounded-full {{ $subscription->status === 'active' ? 'bg-emerald-300' : ($subscription->status === 'expired' ? 'bg-rose-300' : 'bg-amber-300') }}"></span>
                        {{ $subscription->status === 'active' ? 'نشط' : ($subscription->status === 'expired' ? 'منتهي' : 'ملغي') }}
                    </span>
                    @if($subscription->auto_renew)
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/20">
                            <i class="fas fa-sync"></i>
                            تجديد تلقائي
                        </span>
                    @endif
                </div>
                <p class="mt-3 text-white/70 max-w-2xl">
                    نوع الاشتراك: {{ \App\Models\Subscription::typeLabel($subscription->subscription_type) }} · دورة الفوترة: {{ \App\Models\Subscription::billingCycleLabel($subscription->billing_cycle) }}
                </p>
                <div class="mt-6 flex flex-wrap items-center gap-4 text-xs font-semibold">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15">
                        <i class="fas fa-user text-xs"></i>
                        {{ $subscription->user->name ?? 'غير معروف' }}
                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15">
                        <i class="fas fa-phone text-xs"></i>
                        {{ $subscription->user->phone ?? 'غير متوفر' }}
                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15">
                        <i class="fas fa-calendar-alt text-xs"></i>
                        {{ optional($subscription->created_at)->diffForHumans() }}
                    </span>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 justify-end">
                <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white text-sky-700 font-semibold shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-edit"></i>
                    تعديل الاشتراك
                </a>
                <a href="{{ route('admin.subscriptions.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white/20 text-white font-semibold border border-white/30 hover:bg-white/30 transition-all">
                    <i class="fas fa-arrow-right"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-lg font-black text-gray-900 mb-4">معلومات الاشتراك</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">نوع الاشتراك</p>
                        <p class="mt-2 text-base font-semibold text-gray-900">{{ \App\Models\Subscription::typeLabel($subscription->subscription_type) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">دورة الفوترة</p>
                        <p class="mt-2 text-base font-semibold text-gray-900">{{ \App\Models\Subscription::billingCycleLabel($subscription->billing_cycle) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">السعر</p>
                        <p class="mt-2 text-2xl font-black text-gray-900">{{ number_format($subscription->price, 2) }} ج.م</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">حالة التجديد التلقائي</p>
                        <p class="mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $subscription->auto_renew ? 'bg-emerald-100 text-emerald-700 ': ''bg-gray-100 text-gray-700 }}">']
                                {{ $subscription->auto_renew ? 'مفعل' : 'غير مفعل' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 space-y-6">
                <h2 class="text-lg font-black text-gray-900">مسار الاشتراك</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                    <div class="p-4 rounded-2xl border border-emerald-100 bg-emerald-50/80">
                        <p class="text-xs text-emerald-600 font-semibold uppercase tracking-wide">تاريخ البداية</p>
                        <p class="mt-2 text-base font-bold text-gray-900">{{ $subscription->start_date?->format('Y-m-d') ?? 'غير متوفر' }}</p>
                    </div>
                    <div class="p-4 rounded-2xl border border-sky-100 bg-sky-50/80">
                        <p class="text-xs text-sky-600 font-semibold uppercase tracking-wide">المدة الكلية</p>
                        <p class="mt-2 text-base font-bold text-gray-900">
                            @if($subscription->start_date && $subscription->end_date)
                                {{ $subscription->start_date->diffInDays($subscription->end_date) }} يوم
                            @else
                                غير محدد
                            @endif
                        </p>
                    </div>
                    <div class="p-4 rounded-2xl border border-rose-100 bg-rose-50/80">
                        <p class="text-xs text-rose-600 font-semibold uppercase tracking-wide">تاريخ الانتهاء</p>
                        <p class="mt-2 text-base font-bold text-gray-900">{{ $subscription->end_date?->format('Y-m-d') ?? 'غير متوفر' }}</p>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <span class="w-full border-t border-dashed border-gray-200"></span>
                    </div>
                    <div class="relative flex justify-between text-xs text-gray-500">
                        <span>{{ $subscription->start_date?->diffForHumans() ?? '—' }}</span>
                        <span>{{ $subscription->end_date?->diffForHumans() ?? '—' }}</span>
                    </div>
                </div>
            </div>

            @if($subscription->invoice)
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <h2 class="text-lg font-black text-gray-900 mb-4">الفاتورة المرتبطة</h2>
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">فاتورة رقم {{ $subscription->invoice->invoice_number }}</p>
                            <p class="text-xs text-gray-500 mt-1">أُنشئت بتاريخ {{ optional($subscription->invoice->created_at)->format('Y-m-d') }}</p>
                        </div>
                        <a href="{{ route('admin.invoices.show', $subscription->invoice) }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-sky-100 text-sky-600 font-semibold hover:bg-sky-200 transition-all">
                            <i class="fas fa-file-invoice"></i>
                            عرض الفاتورة
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-lg font-black text-gray-900 mb-4">بيانات المستخدم</h2>
                <div class="space-y-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">الاسم الكامل</p>
                        <p class="mt-1 text-base font-semibold text-gray-900">{{ $subscription->user->name ?? 'غير معروف' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">رقم الهاتف</p>
                        <p class="mt-1 text-base font-semibold text-gray-900">{{ $subscription->user->phone ?? 'غير متوفر' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">البريد الإلكتروني</p>
                        <p class="mt-1 text-base font-semibold text-gray-900">{{ $subscription->user->email ?? 'غير متوفر' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-lg font-black text-gray-900 mb-4">إجراءات سريعة</h2>
                <div class="space-y-3">
                    <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="flex items-center justify-between px-4 py-3 rounded-2xl border border-sky-100 bg-sky-50/70 text-sky-600 hover:border-sky-200 transition-all">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-sky-100 text-sky-600">
                                <i class="fas fa-pen"></i>
                            </span>
                            <span class="text-sm font-semibold">تعديل بيانات الاشتراك</span>
                        </div>
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                    <a href="{{ route('admin.subscriptions.index') }}" class="flex items-center justify-between px-4 py-3 rounded-2xl border border-gray-100 bg-gray-50/70 text-gray-600 hover:border-gray-200 transition-all">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gray-100 text-gray-600">
                                <i class="fas fa-list"></i>
                            </span>
                            <span class="text-sm font-semibold">العودة إلى جميع الاشتراكات</span>
                        </div>
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

