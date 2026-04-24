@extends('layouts.public')

@section('title', 'حجوزاتي - Mindlytics')

@section('content')
<section class="pt-28 pb-14 bg-gradient-to-b from-slate-100 to-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <div>
                <h1 class="text-3xl md:text-4xl font-black text-slate-900">صفحة الحجوزات</h1>
                <p class="text-slate-600 mt-2">تابع كل طلبات الحجز الخاصة بك لحظياً.</p>
            </div>
            <a href="{{ route('public.groups') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 transition">
                <i class="fas fa-arrow-right"></i>
                حجز مجموعة جديدة
            </a>
        </div>

        @if($bookings->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center">
                <i class="fas fa-inbox text-4xl text-slate-300 mb-3"></i>
                <h2 class="text-xl font-bold text-slate-800 mb-1">لا توجد حجوزات حتى الآن</h2>
                <p class="text-slate-600 mb-4">ابدأ باختيار مجموعة أوفلاين أو أونلاين ثم أرسل طلب الحجز.</p>
                <a href="{{ route('public.groups') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition">
                    <i class="fas fa-users"></i>
                    تصفح الجروبات
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach($bookings as $booking)
                    @php
                        $statusColor = match($booking->status) {
                            'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                            default => 'bg-amber-50 text-amber-700 border-amber-200',
                        };
                        $statusLabel = match($booking->status) {
                            'approved' => 'مقبول',
                            'rejected' => 'مرفوض',
                            default => 'قيد المراجعة',
                        };
                    @endphp
                    <div class="bg-white rounded-2xl border border-slate-200 p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-black text-slate-900">{{ $booking->course->title ?? 'كورس غير متاح' }}</h3>
                            <span class="text-xs font-bold px-3 py-1 rounded-full border {{ $statusColor }}">{{ $statusLabel }}</span>
                        </div>
                        <ul class="space-y-2 text-sm text-slate-700">
                            <li><i class="fas fa-users text-blue-600 ml-1"></i> المجموعة: {{ $booking->requestedGroup->name ?? 'غير محددة' }}</li>
                            <li><i class="fas fa-signal text-violet-600 ml-1"></i> نوع الحجز: {{ $booking->booking_channel === 'online' ? 'أونلاين' : 'أوفلاين' }}</li>
                            <li><i class="fas fa-credit-card text-emerald-600 ml-1"></i> طريقة الدفع: {{ $booking->payment_method === 'wallet' ? 'محفظة إلكترونية' : 'تحويل بنكي' }}</li>
                            <li><i class="fas fa-calendar text-slate-500 ml-1"></i> تاريخ الطلب: {{ $booking->created_at?->format('Y-m-d h:i A') }}</li>
                        </ul>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
