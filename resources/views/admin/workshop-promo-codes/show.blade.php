@extends('layouts.admin')

@section('title', 'كود '.$workshopPromoCode->code)
@section('header', 'تفاصيل كود الورشة')

@section('content')
<div class="p-3 sm:p-6 space-y-6" style="background:#f8fafc;min-height:100vh;">
    @include('admin.marketing._flash')
    @include('admin.marketing._tabs', ['active' => 'promo'])

    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-bold text-violet-600 uppercase tracking-wide mb-1">كود الورشة</p>
                <h1 class="text-3xl font-black font-mono text-slate-900">{{ $workshopPromoCode->code }}</h1>
                <p class="text-slate-600 mt-1">{{ $workshopPromoCode->title }}</p>
                @if($workshopPromoCode->workshop)
                    <p class="text-sm text-sky-700 mt-2"><i class="fas fa-chalkboard-teacher ml-1"></i> {{ $workshopPromoCode->workshop->title }}</p>
                @endif
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.workshop-promo-codes.edit', $workshopPromoCode) }}" class="px-4 py-2 rounded-xl bg-amber-500 text-white text-sm font-bold">تعديل</a>
                <a href="{{ route('admin.workshop-promo-codes.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold">رجوع</a>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-6 bg-slate-50/50">
            <div><p class="text-xs text-slate-500">الخصم</p><p class="text-lg font-black">{{ $workshopPromoCode->discountLabel() }}</p></div>
            <div><p class="text-xs text-slate-500">ينتهي</p><p class="text-lg font-bold">{{ $workshopPromoCode->expiryLabel() }}</p></div>
            <div><p class="text-xs text-slate-500">التفعيلات</p><p class="text-lg font-bold">{{ $stats['activations'] }} @if($workshopPromoCode->max_activations)/ {{ $workshopPromoCode->max_activations }}@endif</p></div>
            <div><p class="text-xs text-slate-500">استُخدم</p><p class="text-lg font-bold text-emerald-700">{{ $stats['used'] }}</p></div>
        </div>
    </section>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-black text-slate-900"><i class="fas fa-user-check text-violet-600 ml-2"></i>من فعّل الكود</h2>
            <a href="{{ route('admin.workshop-promo-codes.export-activations', $workshopPromoCode) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition-colors">
                <i class="fas fa-file-excel"></i>
                <span>تصدير Excel</span>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-right">الطالب</th>
                        <th class="px-4 py-3 text-right">رقم الهاتف</th>
                        <th class="px-4 py-3 text-right">تاريخ التفعيل</th>
                        <th class="px-4 py-3 text-right">الحالة</th>
                        <th class="px-4 py-3 text-right">كوبون مرتبط</th>
                        <th class="px-4 py-3 text-right">مسند إلى</th>
                        <th class="px-4 py-3 text-right">متابعة</th>
                        <th class="px-4 py-3 text-right">المبيعات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($workshopPromoCode->activations as $act)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-semibold">{{ $act->user->name ?? '—' }}</div>
                                <div class="text-xs text-slate-500">{{ $act->user->email ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-700 whitespace-nowrap" dir="ltr">{{ $act->user->phone ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $act->activated_at?->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3">
                                @if($act->status === 'active')
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">مفعّل</span>
                                @elseif($act->status === 'used')
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-sky-100 text-sky-700">استُخدم</span>
                                @elseif($act->status === 'expired')
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600">منتهي</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">ملغي</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-xs">{{ $act->coupon?->code ?? '—' }}</td>
                            @include('admin.workshop-promo-codes._activation_sales_cells', ['act' => $act])
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-6 py-12 text-center text-slate-500">لم يفعّل أحد هذا الكود بعد</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
