@extends('layouts.admin')

@section('title', 'طلب سحب #' . ($withdrawal->request_number ?? $withdrawal->id) . ' - Mindlytics')
@section('header', 'تفاصيل طلب السحب')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-money-bill-wave text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">طلب سحب {{ $withdrawal->request_number ?? '#' . $withdrawal->id }}</h2>
                    <p class="text-sm text-slate-600 mt-1">{{ $withdrawal->created_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>
            <a href="{{ route('admin.withdrawals.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                <i class="fas fa-arrow-right"></i>
                العودة للقائمة
            </a>
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-800 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-red-800 text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- تفاصيل الطلب والمدرب -->
        <div class="lg:col-span-2 space-y-6">
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        تفاصيل الطلب
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                            <p class="text-xs font-semibold text-slate-500 mb-1">المبلغ</p>
                            <p class="text-xl font-black text-slate-900">{{ number_format($withdrawal->amount, 2) }} ج.م</p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                            <p class="text-xs font-semibold text-slate-500 mb-1">الحالة</p>
                            @php
                                $statusBadges = [
                                    'pending' => ['label' => 'قيد المراجعة', 'classes' => 'bg-slate-100 text-slate-700 border-slate-200'],
                                    'approved' => ['label' => 'موافق عليه', 'classes' => 'bg-amber-100 text-amber-700 border-amber-200'],
                                    'rejected' => ['label' => 'مرفوض', 'classes' => 'bg-red-100 text-red-700 border-red-200'],
                                    'processing' => ['label' => 'قيد المعالجة', 'classes' => 'bg-blue-100 text-blue-700 border-blue-200'],
                                    'completed' => ['label' => 'مكتمل', 'classes' => 'bg-emerald-100 text-emerald-700 border-emerald-200'],
                                    'cancelled' => ['label' => 'ملغي', 'classes' => 'bg-slate-100 text-slate-600 border-slate-200'],
                                ];
                                $status = $statusBadges[$withdrawal->status] ?? ['label' => $withdrawal->status, 'classes' => 'bg-slate-100 text-slate-700 border-slate-200'];
                            @endphp
                            <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-semibold border {{ $status['classes'] }}">
                                <span class="h-2 w-2 rounded-full bg-current"></span>
                                {{ $status['label'] }}
                            </span>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 sm:col-span-2">
                            <p class="text-xs font-semibold text-slate-500 mb-1">طريقة الاستلام</p>
                            <p class="font-semibold text-slate-900">{{ $withdrawal->payment_method_label }}</p>
                        </div>
                    </div>
                    @if($withdrawal->notes)
                        <div class="p-4 rounded-xl bg-amber-50/50 border border-amber-100">
                            <p class="text-xs font-semibold text-amber-800 mb-1">ملاحظات المدرب</p>
                            <p class="text-sm text-slate-700">{{ $withdrawal->notes }}</p>
                        </div>
                    @endif
                </div>
            </section>

            @if($withdrawal->payment_method === 'bank_transfer' && ($withdrawal->bank_name || $withdrawal->account_number || $withdrawal->account_holder_name || $withdrawal->iban))
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-university text-blue-600"></i>
                        بيانات التحويل البنكي
                    </h3>
                </div>
                <div class="p-6 space-y-3">
                    @if($withdrawal->bank_name)
                        <p class="text-sm"><span class="font-semibold text-slate-600">البنك:</span> <span class="text-slate-900">{{ $withdrawal->bank_name }}</span></p>
                    @endif
                    @if($withdrawal->account_holder_name)
                        <p class="text-sm"><span class="font-semibold text-slate-600">صاحب الحساب:</span> <span class="text-slate-900">{{ $withdrawal->account_holder_name }}</span></p>
                    @endif
                    @if($withdrawal->account_number)
                        <p class="text-sm"><span class="font-semibold text-slate-600">رقم الحساب:</span> <span class="text-slate-900 font-mono">{{ $withdrawal->account_number }}</span></p>
                    @endif
                    @if($withdrawal->iban)
                        <p class="text-sm"><span class="font-semibold text-slate-600">الآيبان:</span> <span class="text-slate-900 font-mono">{{ $withdrawal->iban }}</span></p>
                    @endif
                </div>
            </section>
            @endif

            @if($withdrawal->admin_notes || $withdrawal->processed_by)
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-user-cog text-slate-600"></i>
                        معالجة الإدارة
                    </h3>
                </div>
                <div class="p-6 space-y-3">
                    @if($withdrawal->admin_notes)
                        <p class="text-sm"><span class="font-semibold text-slate-600">ملاحظات الإدارة:</span></p>
                        <p class="text-sm text-slate-700 bg-slate-50 p-3 rounded-lg">{{ $withdrawal->admin_notes }}</p>
                    @endif
                    @if($withdrawal->processedBy)
                        <p class="text-sm"><span class="font-semibold text-slate-600">تمت المعالجة بواسطة:</span> <span class="text-slate-900">{{ $withdrawal->processedBy->name }}</span></p>
                    @endif
                    @if($withdrawal->processed_at)
                        <p class="text-sm"><span class="font-semibold text-slate-600">تاريخ المعالجة:</span> <span class="text-slate-900">{{ $withdrawal->processed_at->format('Y-m-d H:i') }}</span></p>
                    @endif
                </div>
            </section>
            @endif
        </div>

        <!-- المدرب + إجراءات -->
        <div class="space-y-6">
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-chalkboard-teacher text-blue-600"></i>
                        المدرب
                    </h3>
                </div>
                <div class="p-6">
                    @if($withdrawal->instructor)
                        <p class="font-bold text-slate-900">{{ $withdrawal->instructor->name }}</p>
                        @if($withdrawal->instructor->phone)
                            <p class="text-sm text-slate-600 mt-1"><i class="fas fa-phone ml-1"></i> {{ $withdrawal->instructor->phone }}</p>
                        @endif
                        @if($withdrawal->instructor->email)
                            <p class="text-sm text-slate-600 mt-1"><i class="fas fa-envelope ml-1"></i> {{ $withdrawal->instructor->email }}</p>
                        @endif
                    @else
                        <p class="text-slate-500">—</p>
                    @endif
                </div>
            </section>

            <!-- إجراءات -->
            @if($withdrawal->status === 'pending')
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-tasks text-amber-600"></i>
                        إجراءات
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <form action="{{ route('admin.withdrawals.approve', $withdrawal) }}" method="POST" class="space-y-3">
                        @csrf
                        <label class="block text-xs font-semibold text-slate-700">ملاحظات (اختياري)</label>
                        <textarea name="admin_notes" rows="2" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500" placeholder="ملاحظات عند الموافقة"></textarea>
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm transition-colors">
                            <i class="fas fa-check"></i>
                            موافقة
                        </button>
                    </form>
                    <form action="{{ route('admin.withdrawals.reject', $withdrawal) }}" method="POST" class="space-y-3">
                        @csrf
                        <label class="block text-xs font-semibold text-slate-700">ملاحظات (اختياري)</label>
                        <textarea name="admin_notes" rows="2" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="سبب الرفض إن أردت"></textarea>
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-sm transition-colors"
                                onclick="return confirm('هل أنت متأكد من رفض طلب السحب؟');">
                            <i class="fas fa-times"></i>
                            رفض
                        </button>
                    </form>
                </div>
            </section>
            @endif

            @if($withdrawal->status === 'approved')
            <section class="rounded-2xl bg-white border border-amber-200 shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-amber-200 bg-amber-50">
                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-check-double text-amber-600"></i>
                        إكمال الطلب
                    </h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.withdrawals.complete', $withdrawal) }}" method="POST" class="space-y-3">
                        @csrf
                        <label class="block text-xs font-semibold text-slate-700">ملاحظات (اختياري)</label>
                        <textarea name="admin_notes" rows="2" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500" placeholder="ملاحظات عند الإكمال"></textarea>
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-sm transition-colors">
                            <i class="fas fa-check-double"></i>
                            تم التحويل / إكمال
                        </button>
                    </form>
                </div>
            </section>
            @endif
        </div>
    </div>
</div>
@endsection
