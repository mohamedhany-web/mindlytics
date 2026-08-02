@extends('layouts.admin')

@section('title', 'تفاصيل الاتفاقية - Mindlytics')
@section('header', 'تفاصيل الاتفاقية')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif
    <!-- Header -->
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
                    <i class="fas fa-file-contract text-sky-600"></i>
                    {{ $agreement->title }}
                </h2>
                <p class="text-sm text-slate-500 mt-2">رقم الاتفاقية: <span class="font-semibold">{{ $agreement->agreement_number }}</span></p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if($agreement->isCoursePercentageType())
                    <form method="POST" action="{{ route('admin.agreements.apply-percentage', $agreement) }}"
                          onsubmit="return confirm('تطبيق النسبة الحالية ({{ number_format($agreement->course_percentage ?? 0, 2) }}%) على كل مدفوعات التفعيل غير المدفوعة؟\nالمدفوعات اللي اتدفعت للمدرب هتتساب زي ما هي.');"
                          class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-emerald-600 rounded-xl shadow hover:bg-emerald-700 transition-all">
                            <i class="fas fa-percentage"></i>
                            تطبيق النسبة الجديدة
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.agreements.edit', $agreement) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-amber-600 rounded-xl shadow hover:bg-amber-700 transition-all">
                    <i class="fas fa-edit"></i>
                    تعديل
                </a>
                <form method="POST" action="{{ route('admin.agreements.destroy', $agreement) }}"
                      onsubmit="return confirm('حذف هذه الاتفاقية نهائيًا؟\nلن يُسمح بالحذف إذا وُجدت مدفوعات مكتملة.');"
                      class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-rose-600 rounded-xl shadow hover:bg-rose-700 transition-all">
                        <i class="fas fa-trash"></i>
                        حذف
                    </button>
                </form>
                <a href="{{ route('admin.agreements.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition-all">
                    <i class="fas fa-arrow-right"></i>
                    رجوع
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-5 sm:p-8">
            <div class="rounded-2xl border border-slate-200 bg-white/70 p-5">
                <p class="text-xs font-semibold text-slate-500 mb-2">إجمالي المدفوعات</p>
                <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['total_earned'], 2) }} ج.م</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white/70 p-5">
                <p class="text-xs font-semibold text-slate-500 mb-2">معلق</p>
                <p class="text-2xl font-bold text-amber-600">{{ number_format($stats['pending_amount'], 2) }} ج.م</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white/70 p-5">
                <p class="text-xs font-semibold text-slate-500 mb-2">إجمالي المدفوعات</p>
                <p class="text-2xl font-bold text-slate-900">{{ $stats['total_payments'] }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white/70 p-5">
                <p class="text-xs font-semibold text-slate-500 mb-2">مدفوع</p>
                <p class="text-2xl font-bold text-emerald-600">{{ $stats['paid_payments'] }}</p>
            </div>
        </div>
    </section>

    <!-- Agreement Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <!-- Basic Info -->
            <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900">معلومات الاتفاقية</h3>
                </div>
                <div class="px-5 py-6 sm:px-8 lg:px-12 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">المدرب</p>
                            <p class="text-sm font-semibold text-slate-900">{{ $agreement->instructor->name }}</p>
                            <p class="text-xs text-slate-500">{{ $agreement->instructor->phone }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">نوع الاتفاقية</p>
                            <p class="text-sm font-semibold text-slate-900">{{ $agreement->type_label }}</p>
                        </div>
                        @if(($agreement->billing_type ?? '') === 'course_percentage')
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">الكورس الأونلاين</p>
                            <p class="text-sm font-semibold text-slate-900">{{ $agreement->advancedCourse?->title ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">نسبة المدرب</p>
                            <p class="text-sm font-semibold text-slate-900">{{ number_format($agreement->course_percentage ?? 0, 2) }}%</p>
                            <p class="text-[11px] text-slate-500 mt-1">لو غيّرت النسبة بعد تسجيلات قديمة، استخدم زر «تطبيق النسبة الجديدة» فوق.</p>
                        </div>
                        @else
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">السعر/المعدل</p>
                            <p class="text-sm font-semibold text-slate-900">{{ number_format($agreement->rate, 2) }} ج.م</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">الحالة</p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $agreement->status == 'active' ? 'bg-emerald-100 text-emerald-700' : ($agreement->status == 'draft' ? 'bg-gray-100 text-gray-700' : 'bg-rose-100 text-rose-700') }}">
                                {{ $agreement->status_label }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">تاريخ البدء</p>
                            <p class="text-sm font-semibold text-slate-900">{{ $agreement->start_date->format('Y-m-d') }}</p>
                        </div>
                        @if($agreement->end_date)
                        <div>
                            <p class="text-xs font-semibold text-slate-500 mb-1">تاريخ الانتهاء</p>
                            <p class="text-sm font-semibold text-slate-900">{{ $agreement->end_date->format('Y-m-d') }}</p>
                        </div>
                        @endif
                    </div>
                    @if($agreement->description)
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">الوصف</p>
                        <p class="text-sm text-slate-700">{{ $agreement->description }}</p>
                    </div>
                    @endif
                    @if($agreement->terms)
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">شروط العقد</p>
                        <div class="text-sm text-slate-700 whitespace-pre-line">{{ $agreement->terms }}</div>
                    </div>
                    @endif
                </div>
            </section>

            <!-- Payments -->
            <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900">سجل المدفوعات</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr class="text-xs font-semibold uppercase tracking-widest text-slate-500">
                                <th class="px-6 py-4 text-right">رقم الدفعة</th>
                                <th class="px-6 py-4 text-right">النوع</th>
                                <th class="px-6 py-4 text-right">المبلغ</th>
                                <th class="px-6 py-4 text-right">تفاصيل التفعيل</th>
                                <th class="px-6 py-4 text-right">الحالة</th>
                                <th class="px-6 py-4 text-right">التاريخ</th>
                                <th class="px-6 py-4 text-right">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white/80 text-sm">
                            @forelse($agreement->payments as $payment)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-4">{{ $payment->payment_number }}</td>
                                    <td class="px-6 py-4">
                                        {{ $payment->type_label ?? $payment->type }}
                                        @if($payment->type === 'course_activation' && $payment->enrollment)
                                            <span class="block text-xs text-slate-500 mt-1">الطالب: {{ $payment->enrollment->student->name ?? '—' }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-semibold">{{ number_format($payment->amount, 2) }} ج.م</td>
                                    <td class="px-6 py-4 text-xs text-slate-600">
                                        @if($payment->type === 'course_activation' && $payment->enrollment)
                                            @php $enr = $payment->enrollment; @endphp
                                            <div>المدفوع: {{ number_format((float)($enr->final_price ?? 0), 2) }} ج.م</div>
                                            @if((float)($enr->discount_amount ?? 0) > 0)
                                                <div class="text-emerald-700">خصم: {{ number_format((float)$enr->discount_amount, 2) }} ج.م</div>
                                                <div class="text-slate-500">قبل الخصم: {{ number_format((float)($enr->original_price ?? 0), 2) }} ج.م</div>
                                            @endif
                                            @if($enr->invoice_id)
                                                <div class="mt-1">فاتورة: #{{ $enr->invoice_id }}</div>
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $payment->status == 'paid' ? 'bg-emerald-100 text-emerald-700' : ($payment->status == 'approved' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700') }}">
                                            {{ $payment->status_label ?? $payment->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500">{{ $payment->created_at->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            @if($payment->status !== 'paid')
                                                <button type="button"
                                                        onclick="document.getElementById('edit-payment-{{ $payment->id }}').classList.toggle('hidden')"
                                                        class="text-xs px-2.5 py-1 rounded-lg bg-sky-100 text-sky-800 hover:bg-sky-200 font-semibold">
                                                    تعديل
                                                </button>
                                                <form method="POST" action="{{ route('admin.agreements.payments.destroy', $payment) }}"
                                                      onsubmit="return confirm('حذف هذه المدفوعة من سجل المدرب؟');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs px-2.5 py-1 rounded-lg bg-rose-100 text-rose-800 hover:bg-rose-200 font-semibold">
                                                        حذف
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-slate-400">مدفوع</span>
                                            @endif
                                        </div>
                                        <div id="edit-payment-{{ $payment->id }}" class="hidden mt-3 p-3 rounded-xl border border-slate-200 bg-slate-50 text-right">
                                            <form method="POST" action="{{ route('admin.agreements.payments.update', $payment) }}" class="space-y-2">
                                                @csrf
                                                @method('PUT')
                                                <div>
                                                    <label class="text-[11px] font-semibold text-slate-600">المبلغ (ج.م)</label>
                                                    <input type="number" name="amount" step="0.01" min="0" value="{{ $payment->amount }}"
                                                           class="w-full mt-1 px-2 py-1.5 text-sm border border-slate-300 rounded-lg">
                                                </div>
                                                <div>
                                                    <label class="text-[11px] font-semibold text-slate-600">الوصف</label>
                                                    <input type="text" name="description" value="{{ $payment->description }}"
                                                           class="w-full mt-1 px-2 py-1.5 text-sm border border-slate-300 rounded-lg">
                                                </div>
                                                <div>
                                                    <label class="text-[11px] font-semibold text-slate-600">ملاحظات</label>
                                                    <input type="text" name="notes" value="{{ $payment->notes }}"
                                                           class="w-full mt-1 px-2 py-1.5 text-sm border border-slate-300 rounded-lg">
                                                </div>
                                                <button type="submit" class="text-xs px-3 py-1.5 rounded-lg bg-emerald-600 text-white font-semibold">حفظ</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">لا توجد مدفوعات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 sm:space-y-6">
            <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900">إجراءات سريعة</h3>
                </div>
                <div class="px-5 py-6 sm:px-8 lg:px-12 space-y-3">
                    <a href="{{ route('admin.agreements.edit', $agreement) }}" class="block w-full text-center px-4 py-2.5 bg-amber-600 text-white rounded-xl hover:bg-amber-700 transition-all">
                        <i class="fas fa-edit ml-2"></i>
                        تعديل الاتفاقية
                    </a>
                    @if($agreement->status == 'active')
                        <form method="POST" action="{{ route('admin.agreements.update', $agreement) }}" class="inline-block w-full">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="suspended">
                            <button type="submit" class="block w-full text-center px-4 py-2.5 bg-amber-100 text-amber-700 rounded-xl hover:bg-amber-200 transition-all">
                                <i class="fas fa-pause ml-2"></i>
                                تعليق الاتفاقية
                            </button>
                        </form>
                    @endif
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
