@extends('layouts.admin')

@section('title', 'إدارة المدفوعات - Mindlytics')
@section('header', 'إدارة المدفوعات')

@section('content')
@php
    $statCards = [
        [
            'label' => 'إجمالي المدفوعات',
            'value' => number_format($stats['total'] ?? 0),
            'icon' => 'fas fa-money-bill-wave',
            'color' => 'blue',
            'description' => 'كل المدفوعات المسجلة في المنصة',
        ],
        [
            'label' => 'مدفوعات مكتملة',
            'value' => number_format($stats['completed'] ?? 0),
            'icon' => 'fas fa-check-circle',
            'color' => 'emerald',
            'description' => 'تمت بنجاح',
        ],
        [
            'label' => 'مدفوعات معلقة',
            'value' => number_format($stats['pending'] ?? 0),
            'icon' => 'fas fa-hourglass-half',
            'color' => 'amber',
            'description' => 'في انتظار المعالجة',
        ],
        [
            'label' => 'إجمالي المبلغ',
            'value' => number_format($stats['total_amount'] ?? 0, 2) . ' ج.م',
            'icon' => 'fas fa-coins',
            'color' => 'purple',
            'description' => 'قيمة المدفوعات المكتملة',
        ],
    ];

    $statusBadges = [
        'completed' => ['label' => 'مكتملة', 'classes' => 'bg-emerald-100 text-emerald-700 border border-emerald-200'],
        'pending' => ['label' => 'معلقة', 'classes' => 'bg-amber-100 text-amber-700 border border-amber-200'],
        'processing' => ['label' => 'قيد المعالجة', 'classes' => 'bg-blue-100 text-blue-700 border border-blue-200'],
        'failed' => ['label' => 'فاشلة', 'classes' => 'bg-rose-100 text-rose-700 border border-rose-200'],
        'cancelled' => ['label' => 'ملغاة', 'classes' => 'bg-slate-100 text-slate-700 border border-slate-200'],
        'refunded' => ['label' => 'مستردة', 'classes' => 'bg-orange-100 text-orange-700 border border-orange-200'],
    ];

    $paymentMethodLabels = [
        'cash' => 'نقدي',
        'card' => 'بطاقة',
        'bank_transfer' => 'تحويل بنكي',
        'online' => 'دفع إلكتروني',
        'wallet' => 'محفظة',
        'other' => 'أخرى',
    ];
@endphp

<div class="space-y-6">
    <!-- الهيدر -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-money-bill-wave text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">لوحة إدارة المدفوعات</h2>
                    <p class="text-sm text-slate-600 mt-1">متابعة المدفوعات، طرق الدفع، وحالة المعالجة عبر المنصة.</p>
                </div>
            </div>
            <a href="{{ route('admin.payments.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-500 rounded-xl shadow hover:from-blue-700 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                <i class="fas fa-plus"></i>
                إضافة دفعة جديدة
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-6">
            @foreach ($statCards as $card)
                @php
                    $colorClasses = [
                        'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600'],
                        'amber' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-600'],
                        'emerald' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
                        'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600'],
                    ];
                    $colors = $colorClasses[$card['color']] ?? $colorClasses['blue'];
                @endphp
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-slate-600 mb-1">{{ htmlspecialchars($card['label']) }}</p>
                            <p class="text-2xl font-black text-slate-900">{{ htmlspecialchars($card['value']) }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-lg {{ $colors['bg'] }} flex items-center justify-center {{ $colors['text'] }} shadow-sm">
                            <i class="{{ $card['icon'] }} text-lg"></i>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600">{{ htmlspecialchars($card['description']) }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="grid grid-cols-1 xl:grid-cols-3">
            <div class="border-b border-slate-200 xl:border-b-0 xl:border-l px-6 py-5 bg-slate-50">
                <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                    <i class="fas fa-filter text-blue-600"></i>
                    البحث والفلترة
                </h3>
                <p class="text-xs text-slate-600 mb-5">فلترة المدفوعات حسب الحالة أو بيانات العميل.</p>
                <form method="GET" action="{{ route('admin.payments.index') }}" id="filterForm" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-search text-blue-600 text-sm"></i>
                            البحث
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-blue-500"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" value="{{ htmlspecialchars(request('search') ?? '') }}" maxlength="255" placeholder="رقم الدفعة، اسم العميل، أو رقم الهاتف" oninput="sanitizeInput(this)" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 pr-10 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-tag text-blue-600 text-sm"></i>
                            الحالة
                        </label>
                        <select name="status" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            <option value="">جميع الحالات</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فاشلة</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 px-4 py-2.5 text-sm font-semibold text-white shadow hover:from-blue-700 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <i class="fas fa-search"></i>
                        بحث متقدم
                    </button>
                </form>
            </div>
            <div class="xl:col-span-2">
                <div class="border-b border-slate-200 px-6 py-5 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-black text-slate-900">المدفوعات الحديثة</h3>
                        <p class="text-xs text-slate-600 mt-1">آخر المدفوعات مرتبة من الأحدث إلى الأقدم.</p>
                    </div>
                    <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-3 py-1 rounded-lg">{{ $payments->total() }} دفعة</span>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @forelse ($payments as $payment)
                            <div class="rounded-xl border border-slate-200 bg-white p-5 hover:border-blue-300 hover:shadow-md transition-all duration-200">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 flex">
                                        <i class="fas fa-money-bill-wave text-lg"></i>
                                    </div>
                                    <div class="flex-1 space-y-2">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <p class="text-sm font-bold text-slate-900">{{ htmlspecialchars($payment->payment_number) }}</p>
                                                <p class="text-xs text-slate-600 mt-0.5">{{ htmlspecialchars($payment->user->name ?? 'غير معروف') }} - {{ htmlspecialchars($payment->user->phone ?? '-') }}</p>
                                                @if($payment->reference_number)
                                                    <p class="text-xs text-slate-500 mt-1">مرجع: {{ htmlspecialchars($payment->reference_number) }}</p>
                                                @endif
                                            </div>
                                            @php $badge = $statusBadges[$payment->status] ?? null; @endphp
                                            @if($badge)
                                                <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1 text-xs font-semibold {{ $badge['classes'] }}">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                                    {{ htmlspecialchars($badge['label']) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center justify-between gap-4 pt-2 border-t border-slate-100">
                                            <div class="flex items-center gap-4 text-xs text-slate-600">
                                                <span><i class="fas fa-coins text-emerald-600 ml-1"></i> {{ number_format($payment->amount, 2) }} ج.م</span>
                                                <span><i class="fas fa-credit-card text-blue-600 ml-1"></i> {{ htmlspecialchars($paymentMethodLabels[$payment->payment_method] ?? $payment->payment_method) }}</span>
                                                <span><i class="fas fa-calendar text-slate-500 ml-1"></i> {{ $payment->paid_at ? $payment->paid_at->format('Y-m-d') : $payment->created_at->format('Y-m-d') }}</span>
                                            </div>
                                            <a href="{{ route('admin.payments.show', $payment) }}" class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-600 hover:bg-blue-100 transition-colors duration-200">
                                                <i class="fas fa-eye"></i>
                                                عرض
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl border border-slate-200 bg-white p-12 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100">
                                        <i class="fas fa-money-bill-wave text-2xl text-slate-400"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">لا توجد مدفوعات</p>
                                        <p class="text-xs text-slate-500 mt-1">لم يتم تسجيل أي مدفوعات بعد</p>
                                    </div>
                                    <a href="{{ route('admin.payments.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 px-4 py-2 text-sm font-semibold text-white shadow hover:from-blue-700 hover:to-blue-600 transition-all duration-200">
                                        <i class="fas fa-plus"></i>
                                        إضافة دفعة جديدة
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    @if(isset($payments) && $payments->hasPages())
                    <div class="px-0 py-4 border-t border-slate-200 mt-4">
                        {{ $payments->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function sanitizeInput(input) {
    input.value = input.value.replace(/[<>'"&]/g, '');
}
</script>
@endsection
