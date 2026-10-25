@extends('layouts.admin')

@section('title', 'طلبات السحب - Mindlytics')
@section('header', 'طلبات السحب')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-money-bill-wave text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">إدارة طلبات السحب</h2>
                    <p class="text-sm text-slate-600 mt-1">إدارة طلبات سحب الماديات من المدربين</p>
                </div>
            </div>
        </div>
    </section>

    <!-- إحصائيات -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                    <i class="fas fa-money-bill-wave text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">إجمالي الطلبات</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($stats['total']) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                    <i class="fas fa-clock text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">قيد الانتظار</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($stats['pending']) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                    <i class="fas fa-check-circle text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">موافق عليها</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($stats['approved']) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white border border-slate-200 shadow-md p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <i class="fas fa-check-double text-base"></i>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-slate-600">إجمالي المكتملة</p>
                    <p class="text-xl font-black text-slate-900">{{ number_format($stats['completed'], 2) }} ج.م</p>
                </div>
            </div>
        </div>
    </section>

    <!-- البحث والفلترة -->
    <section class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-filter text-blue-600"></i>
                البحث والفلترة
            </h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('admin.withdrawals.index') }}" id="filterForm" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">البحث</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" value="{{ htmlspecialchars(request('search') ?? '', ENT_QUOTES, 'UTF-8') }}" maxlength="255" placeholder="رقم الطلب، اسم المدرب" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 pr-10 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">المدرب</label>
                    <select name="instructor_id" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">جميع المدربين</option>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}" {{ request('instructor_id') == $instructor->id ? 'selected' : '' }}>{{ htmlspecialchars($instructor->name, ENT_QUOTES, 'UTF-8') }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">الحالة</label>
                    <select name="status" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">جميع الحالات</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                    </select>
                </div>
                <div class="flex items-end gap-2 md:col-span-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-search"></i>
                        بحث
                    </button>
                    @if(request()->anyFilled(['search', 'instructor_id', 'status']))
                    <a href="{{ route('admin.withdrawals.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors" title="مسح الفلتر">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    <!-- قائمة طلبات السحب -->
    <section class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-list text-blue-600"></i>
                    قائمة طلبات السحب
                </h3>
                <p class="text-sm text-slate-600 mt-1">
                    <span class="font-semibold text-blue-600">{{ $withdrawals->total() }}</span> طلب
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">رقم الطلب</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">المدرب</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">المبلغ</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">طريقة الدفع</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">تاريخ الطلب</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse ($withdrawals as $withdrawal)
                        <tr class="hover:bg-slate-50 transition-colors duration-200">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ htmlspecialchars($withdrawal->request_number ?? '#' . $withdrawal->id, ENT_QUOTES, 'UTF-8') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-sm shadow-md">
                                        {{ mb_substr($withdrawal->instructor->name ?? '', 0, 1, 'UTF-8') }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ htmlspecialchars($withdrawal->instructor->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}</p>
                                        <p class="text-xs text-slate-500">{{ htmlspecialchars($withdrawal->instructor->phone ?? '-', ENT_QUOTES, 'UTF-8') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-lg">{{ number_format($withdrawal->amount ?? 0, 2) }} ج.م</div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $paymentMethodBadges = [
                                        'bank_transfer' => ['label' => 'تحويل بنكي', 'classes' => 'bg-blue-100 text-blue-700 border-blue-200', 'icon' => 'fas fa-university'],
                                        'wallet' => ['label' => 'محفظة', 'classes' => 'bg-purple-100 text-purple-700 border-purple-200', 'icon' => 'fas fa-wallet'],
                                        'cash' => ['label' => 'نقدي', 'classes' => 'bg-emerald-100 text-emerald-700 border-emerald-200', 'icon' => 'fas fa-money-bill'],
                                    ];
                                    $method = $paymentMethodBadges[$withdrawal->payment_method] ?? ['label' => 'أخرى', 'classes' => 'bg-slate-100 text-slate-700 border-slate-200', 'icon' => 'fas fa-ellipsis-h'];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1 text-xs font-semibold border {{ $method['classes'] }}">
                                    <i class="{{ $method['icon'] }} text-xs"></i>
                                    {{ $method['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusBadges = [
                                        'pending' => ['label' => 'قيد الانتظار', 'classes' => 'bg-slate-100 text-slate-700 border-slate-200'],
                                        'approved' => ['label' => 'موافق عليه', 'classes' => 'bg-blue-100 text-blue-700 border-blue-200'],
                                        'processing' => ['label' => 'قيد المعالجة', 'classes' => 'bg-amber-100 text-amber-700 border-amber-200'],
                                        'completed' => ['label' => 'مكتمل', 'classes' => 'bg-emerald-100 text-emerald-700 border-emerald-200'],
                                        'rejected' => ['label' => 'مرفوض', 'classes' => 'bg-rose-100 text-rose-700 border-rose-200'],
                                        'cancelled' => ['label' => 'ملغي', 'classes' => 'bg-slate-100 text-slate-700 border-slate-200'],
                                    ];
                                    $status = $statusBadges[$withdrawal->status] ?? ['label' => $withdrawal->status, 'classes' => 'bg-slate-100 text-slate-700 border-slate-200'];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1 text-xs font-semibold border {{ $status['classes'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-700">
                                <div class="font-medium">{{ $withdrawal->created_at->format('Y-m-d') }}</div>
                                <div class="text-slate-500">{{ $withdrawal->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.withdrawals.show', $withdrawal) }}" 
                                       class="w-9 h-9 flex items-center justify-center bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition-colors duration-200"
                                       title="عرض">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center">
                                        <i class="fas fa-money-bill-wave text-slate-400 text-2xl"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900">لا توجد طلبات سحب</p>
                                        <p class="text-sm text-slate-600 mt-1">لم يتم تقديم أي طلبات سحب بعد</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($withdrawals->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $withdrawals->appends(request()->query())->links() }}
            </div>
        @endif
    </section>
</div>

@push('scripts')
<script>
// حماية من XSS في البحث
const filterForm = document.getElementById('filterForm');
if (filterForm) {
    filterForm.addEventListener('submit', function(e) {
        // Sanitization يتم في الخادم
    });
}

// Sanitization للبحث
function sanitizeInput(input) {
    return input.replace(/[<>]/g, '');
}
</script>
@endpush
@endsection
