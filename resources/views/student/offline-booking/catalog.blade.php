@extends('layouts.app')

@section('title', 'حجز كورسات أوفلاين')
@section('header', 'حجز كورسات أوفلاين')

@push('styles')
<style>
    .offline-booking-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        transition: all 0.25s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .offline-booking-card:hover {
        box-shadow: 0 8px 20px rgba(14, 165, 233, 0.12);
        border-color: #bae6fd;
    }
</style>
@endpush

@section('content')
{{-- إلغاء حشوة layouts.app (p-6) لاستخدام عرض عمود المحتوى كاملاً --}}
<div class="-m-6 min-w-0 pb-6">
    <div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">الكورسات المتاحة للحجز</h1>
                <p class="text-sm text-gray-500">تصفّح الكورسات التي فُتح لها الحجز حالياً، ثم أرسل طلبك مع إيصال التحويل.</p>
            </div>
            <a href="{{ route('student.offline-courses.index') }}"
               class="inline-flex items-center justify-center gap-2 shrink-0 px-4 py-2.5 rounded-lg bg-sky-500 hover:bg-sky-600 text-white text-sm font-semibold transition-colors">
                <i class="fas fa-arrow-right text-xs"></i>
                كورساتي الأوفلاين
            </a>
        </div>

        @if(session('info'))
            <div class="rounded-xl border border-sky-200 bg-sky-50 text-sky-900 px-4 py-3 text-sm font-medium">{{ session('info') }}</div>
        @endif

        @if($courses->isEmpty())
            <div class="rounded-xl p-10 sm:p-12 text-center bg-gray-50 border border-dashed border-gray-200">
                <div class="w-16 h-16 bg-sky-100 rounded-2xl flex items-center justify-center mx-auto mb-4 text-sky-600">
                    <i class="fas fa-calendar-xmark text-2xl"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-900 mb-2">لا توجد كورسات مفتوحة للحجز الآن</h2>
                <p class="text-sm text-gray-500 max-w-md mx-auto">عندما تفتح الأكاديمية نافذة حجز لكورس أوفلاين، سيظهر هنا تلقائياً.</p>
            </div>
        @else
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-sm text-gray-600">
                <p>
                    <span class="font-bold text-gray-900">{{ $courses->total() }}</span>
                    كورس{{ $courses->total() === 1 ? '' : 'ات' }} متاح{{ $courses->total() === 1 ? '' : 'ة' }} للحجز
                    @if($courses->hasPages())
                        <span class="text-gray-400">· الصفحة {{ $courses->currentPage() }} من {{ $courses->lastPage() }}</span>
                    @endif
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($courses as $course)
                    <div class="offline-booking-card overflow-hidden flex flex-col">
                        <div class="h-32 bg-sky-100 flex items-center justify-center text-sky-600 flex-shrink-0">
                            <i class="fas fa-chalkboard-teacher text-3xl"></i>
                        </div>
                        <div class="p-4 flex flex-col flex-1">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <h2 class="text-base font-bold text-gray-900 line-clamp-2 leading-snug flex-1 min-w-0">{{ $course->title }}</h2>
                                <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-sky-100 text-sky-700 flex-shrink-0">أوفلاين</span>
                            </div>
                            @if($course->description)
                                <p class="text-xs text-gray-600 line-clamp-2 mb-2">{{ \Illuminate\Support\Str::limit(strip_tags($course->description), 80) }}</p>
                            @endif
                            <p class="text-xs text-gray-500 mb-2">
                                @if($course->instructor)
                                    <i class="fas fa-user-tie ml-1"></i>{{ $course->instructor->name }}
                                @endif
                                @if($course->locationModel || $course->location)
                                    @if($course->instructor) · @endif
                                    <i class="fas fa-location-dot ml-1"></i>{{ $course->locationModel->name ?? $course->location }}
                                @endif
                            </p>
                            <div class="text-xs text-gray-600 mb-2">
                                السعر: <span class="font-bold text-gray-900">{{ number_format((float) $course->price, 2) }}</span> ج.م
                            </div>
                            <div class="text-xs text-gray-500 mb-2">
                                <i class="fas fa-users ml-1"></i>{{ $course->current_students ?? 0 }} / {{ $course->max_students ?? '—' }} طالب
                            </div>
                            @if($course->booking_opens_at || $course->booking_closes_at)
                                <div class="text-xs rounded-lg bg-gray-50 border border-gray-100 p-2 mb-3 text-gray-600">
                                    <span class="font-semibold text-gray-800 block mb-0.5">نافذة الحجز</span>
                                    @if($course->booking_opens_at)
                                        من {{ $course->booking_opens_at->timezone(config('app.timezone'))->format('Y-m-d H:i') }}
                                    @endif
                                    @if($course->booking_closes_at)
                                        @if($course->booking_opens_at)<br>@endif
                                        إلى {{ $course->booking_closes_at->timezone(config('app.timezone'))->format('Y-m-d H:i') }}
                                    @endif
                                </div>
                            @endif
                            <div class="mt-auto pt-1">
                                <a href="{{ route('student.offline-courses.booking.create', $course) }}"
                                   class="inline-flex items-center justify-center gap-2 w-full py-2.5 rounded-lg bg-sky-500 hover:bg-sky-600 text-white text-sm font-semibold transition-colors">
                                    <i class="fas fa-pen-to-square text-xs"></i>
                                    طلب حجز
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($courses->hasPages())
                <div class="flex justify-center sm:justify-end pt-2">
                    {{ $courses->links() }}
                </div>
            @else
                {{ $courses->links() }}
            @endif
        @endif
    </div>
</div>
@endsection
