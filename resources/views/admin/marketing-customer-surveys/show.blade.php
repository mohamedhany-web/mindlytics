@extends('layouts.admin')

@section('title', 'رد استبيان — ' . $survey->name)
@section('header', 'رد استبيان العملاء')

@section('content')
@php
    $answers = [
        ['label' => 'مهتم بإيه الفترة الجاية؟', 'value' => $survey->interested_in, 'icon' => 'fa-compass'],
        ['label' => 'رأيه في الكورس والأكاديمية', 'value' => $survey->opinion, 'icon' => 'fa-comment-dots'],
        ['label' => 'كورسات يحتاجها', 'value' => $survey->needed_courses, 'icon' => 'fa-list-check'],
        ['label' => 'توصيات للتحسين', 'value' => $survey->recommendations, 'icon' => 'fa-lightbulb'],
    ];
@endphp

<div class="w-full space-y-6">
    <a href="{{ route('admin.marketing-customer-surveys.index') }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-slate-800">
        <i class="fas fa-arrow-right"></i>
        كل ردود الاستبيان
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-3xl bg-white border border-slate-200 shadow-lg p-6 sm:p-8">
                <div class="flex flex-wrap items-start justify-between gap-4 pb-6 border-b border-slate-200">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">{{ $survey->name }}</h1>
                        <p class="text-slate-500 mt-1" dir="ltr">{{ $survey->email }}</p>
                        @if($survey->phone)
                            <p class="text-slate-500" dir="ltr">{{ $survey->phone }}</p>
                        @endif
                    </div>
                    <span class="text-xs text-slate-400 font-semibold">
                        {{ $survey->created_at?->format('Y-m-d H:i') }}
                    </span>
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-5 py-6 border-b border-slate-200">
                    <div>
                        <dt class="text-xs font-semibold text-slate-500 mb-1">الكورس</dt>
                        <dd class="text-slate-900 font-semibold">{{ $survey->course->title ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-slate-500 mb-1">المحافظة</dt>
                        <dd class="text-slate-900 font-semibold">{{ $survey->governorate_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-slate-500 mb-1">الوظيفة / المجال</dt>
                        <dd class="text-slate-900 font-semibold">{{ $survey->job_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-slate-500 mb-1">عرفنا من</dt>
                        <dd class="text-slate-900 font-semibold">{{ $survey->heard_from_label }}</dd>
                    </div>
                </dl>

                <div class="space-y-5 pt-6">
                    @foreach($answers as $answer)
                        @if(filled($answer['value']))
                            <div>
                                <p class="text-sm font-bold text-slate-700 mb-2">
                                    <i class="fas {{ $answer['icon'] }} text-sky-500 me-1.5"></i>
                                    {{ $answer['label'] }}
                                </p>
                                <p class="text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50 border border-slate-200 rounded-xl p-4">{{ $answer['value'] }}</p>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-3xl border-2 border-dashed border-emerald-300 bg-gradient-to-br from-emerald-50 to-sky-50 p-6">
                <h2 class="text-sm font-bold text-emerald-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-gift"></i>
                    الخصم الممنوح
                </h2>

                @if($survey->rewardCoupon)
                    <p class="font-mono text-lg font-black tracking-widest text-emerald-800 bg-white border border-emerald-200 rounded-xl px-4 py-3 text-center">
                        {{ $survey->rewardCoupon->code }}
                    </p>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <dt class="text-emerald-800/80">قيمة الخصم</dt>
                            <dd class="font-bold text-emerald-900">{{ (int) $survey->rewardCoupon->discount_value }}%</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-emerald-800/80">الحالة</dt>
                            <dd class="font-bold {{ $survey->rewardCoupon->used_count > 0 ? 'text-slate-600' : 'text-emerald-900' }}">
                                {{ $survey->rewardCoupon->used_count > 0 ? 'مُستخدم' : 'متاح' }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-emerald-800/80">ينتهي في</dt>
                            <dd class="font-bold text-emerald-900">{{ $survey->rewardCoupon->expires_at?->format('Y-m-d') ?? 'بدون' }}</dd>
                        </div>
                    </dl>

                    @if($survey->rewardCoupon->usages->count() > 0)
                        <div class="mt-5 pt-4 border-t border-emerald-200">
                            <p class="text-xs font-bold text-emerald-900 mb-2">سجل الاستخدام</p>
                            @foreach($survey->rewardCoupon->usages as $usage)
                                <p class="text-xs text-emerald-800/90">
                                    {{ $usage->created_at?->format('Y-m-d') }} —
                                    خصم {{ number_format($usage->discount_amount, 2) }} ج.م
                                    من {{ number_format($usage->order_amount, 2) }} ج.م
                                </p>
                            @endforeach
                        </div>
                    @endif

                    <a href="{{ route('admin.coupons.show', $survey->rewardCoupon) }}"
                       class="mt-5 inline-flex w-full items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-emerald-300 text-emerald-800 hover:bg-emerald-50 text-sm font-bold transition-colors">
                        <i class="fas fa-ticket-alt"></i>
                        إدارة الكوبون
                    </a>
                @else
                    <p class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-xl p-4">
                        لم يُمنح كوبون لهذا الرد. راجع سجلات النظام أو أضف كوبوناً يدوياً للعميل.
                    </p>
                @endif
            </div>

            <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6 space-y-3 text-xs text-slate-500">
                @if($survey->user)
                    <p>
                        <span class="font-semibold text-slate-700">حساب المنصة:</span>
                        {{ $survey->user->name }} (#{{ $survey->user->id }})
                    </p>
                @endif
                @if($survey->ip_address)
                    <p><span class="font-semibold text-slate-700">IP:</span> <span dir="ltr">{{ $survey->ip_address }}</span></p>
                @endif
            </div>

            <form method="POST" action="{{ route('admin.marketing-customer-surveys.destroy', $survey) }}"
                  onsubmit="return confirm('حذف رد الاستبيان؟ الكوبون الممنوح لن يُحذف تلقائياً.');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 text-sm font-bold transition-colors">
                    <i class="fas fa-trash"></i>
                    حذف الرد
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
