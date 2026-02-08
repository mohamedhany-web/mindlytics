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
            'color' => 'blue',
            'description' => 'كل الطلبات المسجلة في المنصة',
        ],
        [
            'label' => 'طلبات قيد المراجعة',
            'value' => number_format($stats['pending']),
            'icon' => 'fas fa-hourglass-half',
            'color' => 'amber',
            'description' => 'بإنتظار الموافقة أو الرفض',
        ],
        [
            'label' => 'طلبات مكتملة',
            'value' => number_format($stats['approved']),
            'icon' => 'fas fa-check-circle',
            'color' => 'emerald',
            'description' => 'تمت الموافقة عليها بنجاح',
        ],
        [
            'label' => 'طلبات مرفوضة',
            'value' => number_format($stats['rejected']),
            'icon' => 'fas fa-times-circle',
            'color' => 'rose',
            'description' => 'تم رفضها بعد المراجعة',
        ],
    ];

    $statusBadges = [
        'pending' => ['label' => 'في الانتظار', 'classes' => 'bg-amber-100 text-amber-700 border border-amber-200'],
        'approved' => ['label' => 'مقبولة', 'classes' => 'bg-emerald-100 text-emerald-700 border border-emerald-200'],
        'rejected' => ['label' => 'مرفوضة', 'classes' => 'bg-rose-100 text-rose-700 border border-rose-200'],
    ];
@endphp

<div class="space-y-6">
    <!-- الهيدر -->
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
                        <div class="w-12 h-12 rounded-lg bg-{{ $card['color'] }}-100 flex items-center justify-center text-{{ $card['color'] }}-600 shadow-sm">
                            <i class="{{ $card['icon'] }} text-lg"></i>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600">{{ $card['description'] }}</p>
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
                <p class="text-xs text-slate-600 mb-5">فلترة الطلبات حسب الحالة، طريقة الدفع، أو بيانات الطالب.</p>
                <form method="GET" id="filterForm" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-search text-blue-600 text-sm"></i>
                            البحث
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-blue-500"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" value="{{ old('search', request('search')) }}" maxlength="255" placeholder="اسم الطالب أو البريد أو الهاتف" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 pr-10 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-toggle-on text-blue-600 text-sm"></i>
                            الحالة
                        </label>
                        <select name="status" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            <option value="">جميع الحالات</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>مقبولة</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوضة</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-wallet text-blue-600 text-sm"></i>
                            طريقة الدفع
                        </label>
                        <select name="payment_method" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            <option value="">جميع الطرق</option>
                            <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                            <option value="other" {{ request('payment_method') == 'other' ? 'selected' : '' }}>أخرى</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2 pt-2">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200">
                            <i class="fas fa-filter"></i>
                            تطبيق الفلترة
                        </button>
                        @if(request()->anyFilled(['search', 'status', 'payment_method']))
                        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors" title="مسح الفلتر">
                            <i class="fas fa-times"></i>
                        </a>
                        @endif
                    </div>
                </form>
            </div>
            <div class="xl:col-span-2">
                <div class="border-b border-slate-200 px-6 py-5 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-black text-slate-900">الطلبات الحديثة</h3>
                        <p class="text-xs text-slate-600 mt-1">آخر التسجيلات مرتبة من الأحدث إلى الأقدم.</p>
                    </div>
                    <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-3 py-1 rounded-lg">{{ $orders->total() }} طلب</span>
                </div>
                <div class="p-6 space-y-3">
                    @forelse ($orders as $order)
                        <div class="rounded-xl border border-slate-200 bg-white p-5 hover:border-blue-300 hover:shadow-md transition-all duration-200">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 flex-shrink-0 items-center justify-center rounded-xl
                                    @if($order->status === 'pending') bg-amber-100 text-amber-600
                                    @elseif($order->status === 'approved') bg-emerald-100 text-emerald-600
                                    @else bg-rose-100 text-rose-600
                                    @endif flex">
                                    <i class="{{ $order->status === 'approved' ? 'fas fa-check' : ($order->status === 'pending' ? 'fas fa-clock' : 'fas fa-times') }} text-lg"></i>
                                </div>
                                <div class="flex-1 space-y-2">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="text-sm font-bold text-slate-900">{{ htmlspecialchars($order->user->name ?? 'غير محدد') }}</p>
                                            <p class="text-xs text-slate-600 mt-0.5">{{ htmlspecialchars($order->user->phone ?? 'غير محدد') }}</p>
                                        </div>
                                        @php $badge = $statusBadges[$order->status] ?? null; @endphp
                                        @if($badge)
                                            <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1 text-xs font-semibold {{ $badge['classes'] }}">
                                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                                {{ $badge['label'] }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="space-y-1 text-xs text-slate-600">
                                        @if($order->academic_year_id && $order->learningPath)
                                            <p class="font-semibold text-slate-900">{{ htmlspecialchars($order->learningPath->name ?? 'مسار تعليمي') }}</p>
                                            <p class="text-green-600">
                                                <i class="fas fa-route ml-1"></i>
                                                مسار تعليمي
                                            </p>
                                        @elseif($order->course)
                                            <p class="font-semibold text-slate-900">{{ htmlspecialchars($order->course->title ?? 'كورس غير محدد') }}</p>
                                            <p>{{ optional($order->course->academicYear)->name }} • {{ optional($order->course->academicSubject)->name }}</p>
                                        @else
                                            <p class="font-semibold text-slate-900">غير محدد</p>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-600">
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="fas fa-money-bill-wave text-blue-500"></i>
                                            {{ number_format($order->amount, 2) }} ج.م
                                        </span>
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="fas fa-calendar text-blue-500"></i>
                                            {{ $order->created_at->format('d/m/Y') }}
                                        </span>
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="fas fa-wallet text-blue-500"></i>
                                            {{ htmlspecialchars($order->payment_method_label ?? 'غير محدد') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 bg-white text-blue-600 hover:bg-blue-50 hover:border-blue-400 transition-colors" title="عرض التفاصيل">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    @if ($order->status === 'pending')
                                        <form method="POST" action="{{ route('admin.orders.approve', $order) }}" class="approve-form" onsubmit="return confirmApprove(event);">
                                            @csrf
                                            <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-emerald-300 bg-white text-emerald-600 hover:bg-emerald-50 hover:border-emerald-400 transition-colors" title="موافقة">
                                                <i class="fas fa-check text-sm"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.orders.reject', $order) }}" class="reject-form" onsubmit="return confirmReject(event);">
                                            @csrf
                                            <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-300 bg-white text-rose-600 hover:bg-rose-50 hover:border-rose-400 transition-colors" title="رفض">
                                                <i class="fas fa-times text-sm"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-slate-200 bg-white p-10 text-center">
                            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                                <i class="fas fa-shopping-cart text-2xl"></i>
                            </div>
                            <p class="font-bold text-slate-900 mb-1">لا توجد طلبات</p>
                            <p class="text-sm text-slate-600">لا توجد طلبات مطابقة لخيارات البحث الحالية.</p>
                        </div>
                    @endforelse

                    @if ($orders->hasPages())
                        <div class="border-t border-slate-200 pt-5">
                            {{ $orders->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-black text-slate-900">تحليل الأداء</h3>
                <p class="text-xs text-slate-600 mt-1">مؤشرات سريعة لمراقبة جودة عمليات القبول والتحصيل.</p>
            </div>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5">
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
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
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
            <div class="rounded-xl border border-purple-200 bg-purple-50 p-5">
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
    // حماية من Double Submit
    let formSubmitting = false;

    function confirmApprove(event) {
        if (formSubmitting) {
            event.preventDefault();
            return false;
        }
        const confirmed = confirm('هل أنت متأكد من الموافقة على هذا الطلب؟\nسيتم تفعيل الكورس للطالب تلقائياً.');
        if (confirmed) {
            formSubmitting = true;
            const btn = event.target.closest('form').querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }
        }
        return confirmed;
    }

    function confirmReject(event) {
        if (formSubmitting) {
            event.preventDefault();
            return false;
        }
        const confirmed = confirm('هل أنت متأكد من رفض هذا الطلب؟');
        if (confirmed) {
            formSubmitting = true;
            const btn = event.target.closest('form').querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }
        }
        return confirmed;
    }

    // حماية من XSS - تنقية بيانات البحث
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            const searchInput = this.querySelector('input[name="search"]');
            if (searchInput) {
                // إزالة HTML tags والتنقية
                searchInput.value = searchInput.value.replace(/<[^>]*>/g, '').trim();
            }
        });
    }

    // منع الإرسال المتكرر للنماذج
    document.querySelectorAll('.approve-form, .reject-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (formSubmitting) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
@endpush
@endsection