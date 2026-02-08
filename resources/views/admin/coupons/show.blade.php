@extends('layouts.admin')

@section('title', 'تفاصيل الكوبون')
@section('header', 'تفاصيل الكوبون')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">كوبون: {{ $coupon->code }}</h1>
                <p class="text-gray-600 mt-1">{{ $coupon->title }}</p>
            </div>
            <div>
                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg font-medium transition-colors mr-2">
                    تعديل
                </a>
                <a href="{{ route('admin.coupons.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg font-medium transition-colors">
                    رجوع
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-4">معلومات الكوبون</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-gray-600">الكود:</span>
                        <span class="font-bold text-lg text-gray-900 mr-2">{{ $coupon->code }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">نوع الخصم:</span>
                        <span class="font-medium text-gray-900 mr-2">{{ $coupon->discount_type == 'percentage' ? 'نسبة مئوية' : 'مبلغ ثابت' }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">قيمة الخصم:</span>
                        <span class="font-medium text-gray-900 text-lg mr-2">
                            {{ $coupon->discount_type == 'percentage' ? $coupon->discount_value . '%' : number_format($coupon->discount_value, 2) . ' ج.م' }}
                        </span>
                    </div>
                    @if($coupon->minimum_amount)
                    <div>
                        <span class="text-sm text-gray-600">الحد الأدنى:</span>
                        <span class="font-medium text-gray-900 mr-2">{{ number_format($coupon->minimum_amount, 2) }} ج.م</span>
                    </div>
                    @endif
                </div>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-4">الاستخدام</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-gray-600">عدد الاستخدامات:</span>
                        <span class="font-medium text-gray-900 mr-2">{{ $coupon->usages->count() ?? 0 }} / {{ $coupon->usage_limit ?? ($coupon->max_uses ?? '∞') }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">الحالة:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ ($coupon->is_active && (!$coupon->expires_at || $coupon->expires_at >= now())) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} mr-2">
                            {{ ($coupon->is_active && (!$coupon->expires_at || $coupon->expires_at >= now())) ? 'نشط' : 'منتهي' }}
                        </span>
                    </div>
                    @if($coupon->starts_at)
                    <div>
                        <span class="text-sm text-gray-600">من:</span>
                        <span class="font-medium text-gray-900 mr-2">{{ $coupon->starts_at->format('Y-m-d') }}</span>
                    </div>
                    @endif
                    @if($coupon->expires_at)
                    <div>
                        <span class="text-sm text-gray-600">إلى:</span>
                        <span class="font-medium text-gray-900 mr-2">{{ $coupon->expires_at->format('Y-m-d') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($coupon->description)
        <div class="border-t border-gray-200 pt-6 mt-6">
            <h3 class="text-lg font-bold text-gray-900 mb-2">الوصف</h3>
            <p class="text-gray-600">{{ $coupon->description }}</p>
        </div>
        @endif

        @if($coupon->usages && $coupon->usages->count() > 0)
        <div class="border-t border-gray-200 pt-6 mt-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">سجل الاستخدامات</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">المستخدم</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($coupon->usages as $usage)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $usage->user->name ?? 'غير معروف' }}</td>
                            <td class="px-4 py-2 text-sm text-gray-600">{{ $usage->used_at ? $usage->used_at->format('Y-m-d') : '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

