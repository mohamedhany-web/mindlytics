@extends('layouts.app')

@section('title', 'طلباتي')
@section('header', 'طلباتي')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">طلباتي</h1>
                <p class="text-sm text-gray-500">تتبع حالة طلباتك وعمليات الشراء</p>
            </div>
            <a href="{{ route('academic-years') }}" class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                <i class="fas fa-search"></i>
                تصفح الكورسات
            </a>
        </div>
    </div>

    @if($orders->count() > 0)
        <div class="space-y-4">
            @foreach($orders as $order)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-4 sm:p-5">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-3">
                                <h3 class="text-base sm:text-lg font-bold text-gray-900">
                                    @if($order->academic_year_id && $order->learningPath)
                                        {{ $order->learningPath->name ?? 'مسار تعليمي' }}
                                    @else
                                        {{ $order->course->title ?? 'كورس غير محدد' }}
                                    @endif
                                </h3>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $order->status == 'pending' ? 'bg-amber-100 text-amber-800' : ($order->status == 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800') }}">
                                    @if($order->status == 'pending')<i class="fas fa-clock"></i>
                                    @elseif($order->status == 'approved')<i class="fas fa-check-circle"></i>
                                    @else<i class="fas fa-times-circle"></i>
                                    @endif
                                    {{ $order->status_text }}
                                </span>
                            </div>

                            <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500 mb-3">
                                @if($order->academic_year_id && $order->learningPath)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-gray-100 text-gray-600 text-xs font-medium">مسار تعليمي</span>
                                    @if($order->learningPath->price)
                                        <span>{{ number_format($order->learningPath->price, 2) }} ج.م</span>
                                    @endif
                                @elseif($order->course && ($order->course->academicYear || $order->course->academicSubject))
                                    @if($order->course->academicYear)
                                        <span>{{ $order->course->academicYear->name }}</span>
                                    @endif
                                    @if($order->course->academicSubject)
                                        <span>· {{ $order->course->academicSubject->name }}</span>
                                    @endif
                                @endif
                                <span>· {{ $order->created_at->diffForHumans() }}</span>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3 mb-3">
                                <div class="py-2 px-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <p class="text-xs font-medium text-gray-500">المبلغ</p>
                                    <p class="text-sm font-bold text-gray-900">{{ number_format($order->amount, 2) }} ج.م</p>
                                </div>
                                <div class="py-2 px-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <p class="text-xs font-medium text-gray-500">طريقة الدفع</p>
                                    <p class="text-xs font-semibold text-gray-900">
                                        @if($order->payment_method == 'bank_transfer') تحويل بنكي
                                        @elseif($order->payment_method == 'cash') نقدي
                                        @else أخرى
                                        @endif
                                    </p>
                                </div>
                                <div class="py-2 px-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <p class="text-xs font-medium text-gray-500">تاريخ الطلب</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $order->created_at->format('d/m/Y') }}</p>
                                </div>
                                @if($order->approved_at)
                                <div class="py-2 px-3 bg-emerald-50 rounded-lg border border-emerald-100">
                                    <p class="text-xs font-medium text-gray-500">تاريخ الموافقة</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $order->approved_at->format('d/m/Y') }}</p>
                                </div>
                                @endif
                            </div>

                            @if($order->notes)
                                <div class="p-3 bg-sky-50 rounded-lg border border-sky-100 mb-3">
                                    <p class="text-xs font-medium text-gray-500 mb-1">ملاحظاتك</p>
                                    <p class="text-sm text-gray-700">{{ $order->notes }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-row sm:flex-col gap-2 flex-shrink-0 w-full sm:w-auto">
                            <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center justify-center gap-2 flex-1 sm:flex-none bg-sky-500 hover:bg-sky-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                                <i class="fas fa-eye"></i>
                                عرض التفاصيل
                            </a>
                            @if($order->status == 'approved' && $order->course)
                                <a href="{{ route('courses.show', $order->course) }}" class="inline-flex items-center justify-center gap-2 flex-1 sm:flex-none bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                                    <i class="fas fa-play"></i>
                                    ادخل للكورس
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($orders->hasPages())
            <div class="flex justify-center mt-6">
                {{ $orders->links() }}
            </div>
        @endif
    @else
        <div class="rounded-xl p-10 sm:p-12 text-center bg-gray-50 border border-dashed border-gray-200">
            <div class="w-16 h-16 bg-sky-100 rounded-2xl flex items-center justify-center mx-auto mb-4 text-sky-600">
                <i class="fas fa-shopping-cart text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">لا توجد طلبات</h3>
            <p class="text-sm text-gray-500 mb-6 max-w-sm mx-auto">لم تقم بتقديم أي طلبات بعد. يمكنك تصفح الكورسات المتاحة وطلب تفعيلها.</p>
            <a href="{{ route('academic-years') }}" class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                <i class="fas fa-plus"></i>
                تصفح الكورسات
            </a>
        </div>
    @endif
</div>
@endsection
