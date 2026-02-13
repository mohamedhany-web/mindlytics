@extends('layouts.admin')

@section('title', 'إدارة الطلبات')
@section('header', 'إدارة الطلبات')

@section('content')
@php
    $statCards = [
        [
            'label' => 'إجمالي الطلبات',
            'value' => number_format($stats['total']),
            'icon' => 'fas fa-shopping-cart',
            'bg' => 'bg-blue-100',
            'text' => 'text-blue-600',
            'description' => 'كل الطلبات المسجلة في المنصة',
        ],
        [
            'label' => 'طلبات قيد المراجعة',
            'value' => number_format($stats['pending']),
            'icon' => 'fas fa-hourglass-half',
            'bg' => 'bg-amber-100',
            'text' => 'text-amber-600',
            'description' => 'بإنتظار الموافقة أو الرفض',
        ],
        [
            'label' => 'طلبات مكتملة',
            'value' => number_format($stats['approved']),
            'icon' => 'fas fa-check-circle',
            'bg' => 'bg-emerald-100',
            'text' => 'text-emerald-600',
            'description' => 'تمت الموافقة عليها بنجاح',
        ],
        [
            'label' => 'طلبات مرفوضة',
            'value' => number_format($stats['rejected']),
            'icon' => 'fas fa-times-circle',
            'bg' => 'bg-rose-100',
            'text' => 'text-rose-600',
            'description' => 'تم رفضها بعد المراجعة',
        ],
    ];

    $statusBadges = [
        'pending' => ['label' => 'في الانتظار', 'classes' => 'bg-amber-100 text-amber-700 border border-amber-200'],
        'approved' => ['label' => 'مقبولة', 'classes' => 'bg-emerald-100 text-emerald-700 border border-emerald-200'],
        'rejected' => ['label' => 'مرفوضة', 'classes' => 'bg-rose-100 text-rose-700 border border-rose-200'],
    ];

    $paymentMethodLabels = [
        'bank_transfer' => 'تحويل بنكي',
        'wallet' => 'محفظة إلكترونية',
        'online' => 'دفع إلكتروني',
        'cash' => 'نقدي',
        'other' => 'أخرى',
    ];
@endphp

<div class="space-y-6">
    {{-- 1. الهيدر + كاردات الإحصائيات --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-shopping-cart text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">لوحة إدارة الطلبات</h2>
                    <p class="text-sm text-slate-600 mt-1">متابعة حركة التسجيلات والطلبات المالية عبر المسارات التعليمية.</p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-6">
            @foreach ($statCards as $card)
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-slate-600 mb-1">{{ $card['label'] }}</p>
                            <p class="text-2xl font-black text-slate-900">{{ $card['value'] }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-lg {{ $card['bg'] }} flex items-center justify-center {{ $card['text'] }} shadow-sm">
                            <i class="{{ $card['icon'] }} text-lg"></i>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600">{{ $card['description'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- 2. الفلترة أسفل الكاردات --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-filter text-blue-600"></i>
                البحث والفلترة
            </h3>
            <p class="text-xs text-slate-600 mt-1">فلترة الطلبات حسب الحالة، طريقة الدفع، أو بيانات الطالب.</p>
        </div>
        <div class="p-6">
            <form method="GET" id="filterForm" class="flex flex-col gap-4 lg:flex-row lg:items-end lg:flex-wrap">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-slate-700 mb-2">
                        <i class="fas fa-search text-blue-600 ml-1"></i>
                        البحث
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 right-3 flex items-center text-blue-500 pointer-events-none"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" value="{{ old('search', request('search')) }}" maxlength="255" placeholder="اسم الطالب، البريد، الهاتف، أو اسم الكورس" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 pl-10 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
                    </div>
                </div>
                <div class="w-full sm:w-auto min-w-[180px]">
                    <label class="block text-xs font-semibold text-slate-700 mb-2">
                        <i class="fas fa-toggle-on text-blue-600 ml-1"></i>
                        الحالة
                    </label>
                    <select name="status" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">جميع الحالات</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>مقبولة</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوضة</option>
                    </select>
                </div>
                <div class="w-full sm:w-auto min-w-[180px]">
                    <label class="block text-xs font-semibold text-slate-700 mb-2">
                        <i class="fas fa-wallet text-blue-600 ml-1"></i>
                        طريقة الدفع
                    </label>
                    <select name="payment_method" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">جميع الطرق</option>
                        <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                        <option value="online" {{ request('payment_method') == 'online' ? 'selected' : '' }}>دفع إلكتروني</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                        <option value="other" {{ request('payment_method') == 'other' ? 'selected' : '' }}>أخرى</option>
                    </select>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-filter"></i>
                        تطبيق
                    </button>
                    @if(request()->anyFilled(['search', 'status', 'payment_method']))
                    <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors" title="مسح الفلتر">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    {{-- 3. قائمة الطلبات بعرض كامل --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-black text-slate-900">الطلبات</h3>
                <p class="text-xs text-slate-600">آخر الطلبات مرتبة من الأحدث إلى الأقدم.</p>
            </div>
            <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-200">{{ $orders->total() }} طلب</span>
        </div>
        <div class="overflow-x-auto">
            <div class="p-3 space-y-1.5">
                @forelse ($orders as $order)
                    <div class="rounded-lg border border-slate-200 bg-slate-50/50 hover:border-blue-200 hover:bg-white transition-all duration-200 overflow-hidden">
                        <div class="px-3 py-2.5">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                                {{-- أيقونة الحالة --}}
                                <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center rounded-lg
                                    @if($order->status === 'pending') bg-amber-100 text-amber-600
                                    @elseif($order->status === 'approved') bg-emerald-100 text-emerald-600
                                    @else bg-rose-100 text-rose-600
                                    @endif">
                                    <i class="{{ $order->status === 'approved' ? 'fas fa-check' : ($order->status === 'pending' ? 'fas fa-clock' : 'fas fa-times') }} text-sm"></i>
                                </div>
                                {{-- بيانات الطلب --}}
                                <div class="flex-1 min-w-0 space-y-1">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <div>
                                            <p class="text-sm font-bold text-slate-900">{{ htmlspecialchars($order->user->name ?? '—') }}</p>
                                            <p class="text-xs text-slate-600">{{ htmlspecialchars($order->user->email ?? $order->user->phone ?? '—') }}</p>
                                        </div>
                                        @php $badge = $statusBadges[$order->status] ?? null; @endphp
                                        @if($badge)
                                            <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-semibold {{ $badge['classes'] }}">
                                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                                {{ $badge['label'] }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap gap-x-3 gap-y-0.5 text-xs text-slate-600">
                                        @if($order->academic_year_id && $order->learningPath)
                                            <span class="font-semibold text-slate-800">{{ htmlspecialchars($order->learningPath->name ?? 'مسار تعليمي') }}</span>
                                            <span class="text-blue-600"><i class="fas fa-route ml-0.5"></i> مسار تعليمي</span>
                                        @elseif($order->course)
                                            <span class="font-semibold text-slate-800">{{ htmlspecialchars($order->course->title ?? 'كورس') }}</span>
                                            @if($order->course->academicYear || $order->course->academicSubject)
                                                <span>{{ optional($order->course->academicYear)->name }}{{ $order->course->academicSubject ? ' • ' . $order->course->academicSubject->name : '' }}</span>
                                            @endif
                                        @else
                                            <span class="text-slate-500">—</span>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-600">
                                        <span class="inline-flex items-center gap-1">
                                            <i class="fas fa-money-bill-wave text-blue-500 text-[10px]"></i>
                                            <strong>{{ number_format($order->amount, 2) }}</strong> ج.م
                                        </span>
                                        <span class="inline-flex items-center gap-1">
                                            <i class="fas fa-calendar text-blue-500 text-[10px]"></i>
                                            {{ $order->created_at->format('d/m/Y H:i') }}
                                        </span>
                                        <span class="inline-flex items-center gap-1">
                                            <i class="fas fa-wallet text-blue-500 text-[10px]"></i>
                                            {{ $paymentMethodLabels[$order->payment_method] ?? $order->payment_method ?? '—' }}
                                        </span>
                                    </div>
                                </div>
                                {{-- أزرار الإجراءات --}}
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-300 bg-white text-blue-600 hover:bg-blue-50 hover:border-blue-400 transition-colors text-sm" title="عرض التفاصيل">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if ($order->status === 'pending')
                                        <button type="button" class="approve-btn inline-flex h-8 w-8 items-center justify-center rounded-lg border border-emerald-300 bg-white text-emerald-600 hover:bg-emerald-50 hover:border-emerald-400 transition-colors text-sm" title="موافقة" data-order-id="{{ $order->id }}" data-url="{{ route('admin.orders.approve', $order) }}">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="reject-btn inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-300 bg-white text-rose-600 hover:bg-rose-50 hover:border-rose-400 transition-colors text-sm" title="رفض" data-order-id="{{ $order->id }}" data-url="{{ route('admin.orders.reject', $order) }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-slate-200 bg-white p-12 text-center">
                        <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                            <i class="fas fa-shopping-cart text-3xl"></i>
                        </div>
                        <p class="text-lg font-bold text-slate-900 mb-1">لا توجد طلبات</p>
                        <p class="text-sm text-slate-600">لا توجد طلبات مطابقة لخيارات البحث والفلترة الحالية.</p>
                    </div>
                @endforelse
            </div>

            @if ($orders->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </section>

    {{-- 4. تحليل سريع --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900">مؤشرات سريعة</h3>
            <p class="text-xs text-slate-600 mt-1">معدل القبول وطلبات الشهر ومتوسط قيمة الطلب.</p>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-xl border border-emerald-200 bg-emerald-50/80 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                        <i class="fas fa-percentage text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-emerald-700 mb-1">معدل القبول</p>
                        <p class="text-xl font-black text-emerald-700">{{ $stats['total'] > 0 ? round(($stats['approved'] / $stats['total']) * 100, 1) : 0 }}%</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-blue-200 bg-blue-50/80 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                        <i class="fas fa-calendar text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-blue-700 mb-1">طلبات هذا الشهر</p>
                        <p class="text-xl font-black text-blue-700">{{ \App\Models\Order::whereMonth('created_at', now()->month)->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-purple-200 bg-purple-50/80 p-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-purple-100 text-purple-600">
                        <i class="fas fa-coins text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-purple-700 mb-1">متوسط قيمة الطلب</p>
                        <p class="text-xl font-black text-purple-700">{{ $stats['total'] > 0 ? number_format(\App\Models\Order::avg('amount'), 2) : 0 }} ج.م</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
(function() {
    var csrfToken = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    if (!csrfToken) return;

    function sendRequest(url, isApprove, btn) {
        var formData = new FormData();
        formData.append('_token', csrfToken);
        var originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(function(response) {
            var contentType = response.headers.get('content-type');
            if (contentType && contentType.indexOf('application/json') !== -1) {
                return response.json();
            }
            if (response.ok) {
                window.location.reload();
                return;
            }
            return response.text().then(function(text) { throw new Error(text || 'حدث خطأ أثناء المعالجة'); });
        })
        .then(function(data) {
            if (data && data.success) {
                if (data.message) alert(data.message);
                window.location.reload();
            } else {
                var msg = (data && (data.error || data.message)) || 'حدث خطأ أثناء المعالجة';
                alert(msg);
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        })
        .catch(function(err) {
            var msg = err.message || 'حدث خطأ أثناء المعالجة';
            alert(msg);
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    }

    document.querySelectorAll('.approve-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (!confirm('هل أنت متأكد من الموافقة على هذا الطلب؟\nسيتم تفعيل الكورس للطالب تلقائياً.')) return;
            sendRequest(btn.getAttribute('data-url'), true, btn);
        });
    });

    document.querySelectorAll('.reject-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (!confirm('هل أنت متأكد من رفض هذا الطلب؟')) return;
            sendRequest(btn.getAttribute('data-url'), false, btn);
        });
    });

    var filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function() {
            var searchInput = this.querySelector('input[name="search"]');
            if (searchInput) searchInput.value = searchInput.value.replace(/<[^>]*>/g, '').trim();
        });
    }
})();
</script>
@endpush
@endsection
