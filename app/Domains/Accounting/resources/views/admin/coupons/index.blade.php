@extends('layouts.admin')

@section('title', 'الكوبونات والخصومات')
@section('header', 'الكوبونات والخصومات')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">الكوبونات والخصومات</h1>
                <p class="text-gray-600 mt-1">إدارة كوبونات الخصم</p>
            </div>
            <a href="{{ route('admin.coupons.create') }}" 
               class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                <i class="fas fa-plus mr-2"></i>
                إضافة كوبون جديد
            </a>
        </div>
    </div>

    <!-- الإحصائيات -->
    @if(isset($stats))
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <div class="text-sm text-gray-600">إجمالي الكوبونات</div>
            <div class="text-2xl font-bold text-gray-900 mt-2">{{ $stats['total'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <div class="text-sm text-gray-600">النشطة</div>
            <div class="text-2xl font-bold text-green-600 mt-2">{{ $stats['active'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <div class="text-sm text-gray-600">المنتهية</div>
            <div class="text-2xl font-bold text-red-600 mt-2">{{ $stats['expired'] ?? 0 }}</div>
        </div>
    </div>
    @endif

    <!-- قائمة الكوبونات -->
    @if(isset($coupons) && $coupons->count() > 0)
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الكود</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">العنوان</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">نوع الخصم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">قيمة الخصم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">عدد الاستخدامات</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($coupons as $coupon)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $coupon->code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $coupon->title ?? $coupon->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $coupon->discount_type == 'percentage' ? 'نسبة مئوية' : 'مبلغ ثابت' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $coupon->discount_type == 'percentage' ? $coupon->discount_value . '%' : number_format($coupon->discount_value, 2) . ' ج.م' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $coupon->usages_count ?? 0 }} / {{ $coupon->usage_limit ?? ($coupon->max_uses ?? '∞') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($coupon->is_active && (!$coupon->expires_at || $coupon->expires_at >= now())) bg-green-100 text-green-800
                                @else bg-red-100 text-red-800
                                @endif">
                                @if($coupon->is_active && (!$coupon->expires_at || $coupon->expires_at >= now())) نشط
                                @else منتهي
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.coupons.show', $coupon) }}" class="text-sky-600 hover:text-sky-900">عرض</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $coupons->links() }}
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-lg p-12 text-center border border-gray-200">
        <i class="fas fa-ticket-alt text-gray-400 text-6xl mb-4"></i>
        <p class="text-gray-600 text-lg">لا توجد كوبونات</p>
    </div>
    @endif
</div>
@endsection
