@extends('layouts.app')

@section('title', 'برنامج الإحالات - Mindlytics')
@section('header', 'برنامج الإحالات')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-users text-sky-600 ml-3"></i>
                    برنامج الإحالات
                </h1>
                <p class="text-gray-600">أحِل أصدقائك واحصل على مكافآت</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- إجمالي الإحالات -->
        <div class="bg-gradient-to-br from-sky-50 to-slate-50 rounded-2xl shadow-xl p-6 border border-sky-200 hover:shadow-2xl transition-all duration-300 card-hover-effect relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-sky-100/50 to-slate-100/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي الإحالات</p>
                        <p class="text-4xl font-black bg-gradient-to-r from-sky-600 via-sky-700 to-slate-600 bg-clip-text text-transparent">{{ number_format($stats['total_referrals']) }}</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-user-plus text-white text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <i class="fas fa-users text-sky-500 ml-2"></i>
                    <span class="text-gray-600 font-medium">جميع الإحالات المسجلة</span>
                </div>
            </div>
        </div>

        <!-- إحالات مكتملة -->
        <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-2xl shadow-xl p-6 border border-emerald-200 hover:shadow-2xl transition-all duration-300 card-hover-effect relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-100/50 to-green-100/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 mb-1">إحالات مكتملة</p>
                        <p class="text-4xl font-black text-emerald-600">{{ number_format($stats['completed_referrals']) }}</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <i class="fas fa-gift text-emerald-500 ml-2"></i>
                    <span class="text-gray-600 font-medium">تم الحصول على المكافأة</span>
                </div>
            </div>
        </div>

        <!-- إحالات قيد الانتظار -->
        <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-2xl shadow-xl p-6 border border-amber-200 hover:shadow-2xl transition-all duration-300 card-hover-effect relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-100/50 to-yellow-100/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 mb-1">قيد الانتظار</p>
                        <p class="text-4xl font-black text-amber-600">{{ number_format($stats['pending_referrals']) }}</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-hourglass-half text-white text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <i class="fas fa-clock text-amber-500 ml-2"></i>
                    <span class="text-gray-600 font-medium">في انتظار الشراء</span>
                </div>
            </div>
        </div>

        <!-- إجمالي المكافآت -->
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl shadow-xl p-6 border border-purple-200 hover:shadow-2xl transition-all duration-300 card-hover-effect relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-100/50 to-pink-100/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي المكافآت</p>
                        <p class="text-3xl font-black text-purple-600">{{ number_format($stats['total_rewards'], 2) }} ج.م</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-gift text-white text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <i class="fas fa-coins text-purple-500 ml-2"></i>
                    <span class="text-gray-600 font-medium">مكافآت من الإحالات</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Referral Code Card -->
    <div class="bg-gradient-to-br from-sky-500 via-sky-600 to-purple-600 rounded-2xl shadow-2xl p-8 mb-8 text-white relative overflow-hidden border border-sky-400/30 hover:shadow-2xl transition-all duration-300 card-hover-effect group">
        <div class="absolute top-0 right-0 w-80 h-80 bg-white/10 rounded-full -mr-40 -mt-40 blur-3xl group-hover:bg-white/15 transition-all duration-500"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/10 rounded-full -ml-32 -mb-32 blur-3xl group-hover:bg-white/15 transition-all duration-500"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent"></div>
        
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-link text-2xl text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black mb-1 flex items-center gap-2">
                                كود الإحالة الخاص بك
                            </h2>
                            <p class="text-sky-100 text-sm">شارك هذا الكود واحصل على مكافآت</p>
                        </div>
                    </div>
                    
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-5 mb-4 border border-white/20 shadow-lg">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex-1">
                                <p class="text-sm text-sky-100 mb-2 font-medium flex items-center gap-2">
                                    <i class="fas fa-tag"></i>
                                    كود الإحالة
                                </p>
                                <p class="text-3xl font-black tracking-wider bg-white/10 px-4 py-2 rounded-lg inline-block">{{ $referralCode }}</p>
                            </div>
                            <button onclick="copyReferralCode('{{ $referralCode }}')" 
                                    class="bg-white text-sky-600 px-6 py-3 rounded-xl font-bold hover:bg-sky-50 hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2 shadow-lg transform hover:scale-105">
                                <i class="fas fa-copy"></i>
                                نسخ الكود
                            </button>
                        </div>
                    </div>

                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-5 border border-white/20 shadow-lg">
                        <p class="text-sm text-sky-100 mb-3 font-medium flex items-center gap-2">
                            <i class="fas fa-link"></i>
                            رابط الإحالة
                        </p>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <input type="text" 
                                   id="referralLink" 
                                   value="{{ $referralLink }}" 
                                   readonly
                                   class="flex-1 bg-white/30 backdrop-blur-sm border border-white/30 rounded-lg px-4 py-3 text-white font-medium focus:outline-none focus:ring-2 focus:ring-white/50 text-sm">
                            <button onclick="copyReferralLink()" 
                                    class="bg-white text-sky-600 px-6 py-3 rounded-lg font-bold hover:bg-sky-50 hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2 shadow-lg transform hover:scale-105">
                                <i class="fas fa-copy"></i>
                                نسخ الرابط
                            </button>
                        </div>
                    </div>
                </div>

                <div class="text-center md:text-right flex-shrink-0">
                    <div class="w-36 h-36 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto md:mx-0 mb-4 border border-white/20 shadow-lg group-hover:scale-105 transition-transform duration-300">
                        <i class="fas fa-qrcode text-7xl text-white opacity-80"></i>
                    </div>
                    <p class="text-sm text-sky-100 font-medium">شارك الكود عبر وسائل التواصل</p>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works -->
    <div class="bg-white rounded-2xl shadow-xl p-6 mb-8 border border-gray-200 hover:shadow-2xl transition-all duration-300 card-hover-effect">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200 bg-gradient-to-r from-sky-50 to-slate-50 -m-6 p-6 rounded-t-2xl">
            <h3 class="text-xl font-black text-gray-900 flex items-center gap-2">
                <i class="fas fa-info-circle text-sky-600 ml-2"></i>
                كيف يعمل برنامج الإحالات؟
            </h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center p-6 bg-gradient-to-br from-sky-50 to-sky-100 rounded-xl border border-sky-200 hover:shadow-lg transition-all duration-300 group">
                <div class="w-20 h-20 bg-gradient-to-br from-sky-500 to-sky-600 rounded-full flex items-center justify-center mx-auto mb-4 text-white text-3xl font-bold shadow-lg group-hover:scale-110 transition-transform duration-300">1</div>
                <h4 class="font-bold text-gray-900 mb-2 text-lg">شارك كود الإحالة</h4>
                <p class="text-sm text-gray-600">انسخ كود الإحالة أو الرابط وشاركه مع أصدقائك</p>
            </div>
            <div class="text-center p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border border-purple-200 hover:shadow-lg transition-all duration-300 group">
                <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 text-white text-3xl font-bold shadow-lg group-hover:scale-110 transition-transform duration-300">2</div>
                <h4 class="font-bold text-gray-900 mb-2 text-lg">صديقك يسجل</h4>
                <p class="text-sm text-gray-600">صديقك يسجل باستخدام كودك ويحصل على خصم خاص</p>
            </div>
            <div class="text-center p-6 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl border border-emerald-200 hover:shadow-lg transition-all duration-300 group">
                <div class="w-20 h-20 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 text-white text-3xl font-bold shadow-lg group-hover:scale-110 transition-transform duration-300">3</div>
                <h4 class="font-bold text-gray-900 mb-2 text-lg">احصل على المكافأة</h4>
                <p class="text-sm text-gray-600">عند شراء صديقك لأي كورس، تحصل أنت على مكافأة</p>
            </div>
        </div>
    </div>

    <!-- Referrals List -->
    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-200 hover:shadow-2xl transition-all duration-300 card-hover-effect">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200 bg-gradient-to-r from-sky-50 to-slate-50 -m-6 p-6 rounded-t-2xl">
            <h3 class="text-xl font-black text-gray-900 flex items-center gap-2">
                <i class="fas fa-list text-sky-600 ml-2"></i>
                قائمة الإحالات
            </h3>
        </div>

        @if($referrals->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المستخدم المحال</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الإحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الخصم المطبق</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المكافأة</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($referrals as $referral)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-sky-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                        {{ substr($referral->referred->name ?? 'N', 0, 1) }}
                                    </div>
                                </div>
                                <div class="mr-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $referral->referred->name ?? 'غير معروف' }}</div>
                                    <div class="text-sm text-gray-500">{{ $referral->referred->phone ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $referral->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($referral->status == 'completed') bg-emerald-100 text-emerald-800
                                @elseif($referral->status == 'pending') bg-amber-100 text-amber-800
                                @else bg-red-100 text-red-800
                                @endif">
                                @if($referral->status == 'completed') مكتملة
                                @elseif($referral->status == 'pending') قيد الانتظار
                                @else ملغاة
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ number_format($referral->discount_amount ?? 0, 2) }} ج.م
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-emerald-600">
                            {{ number_format($referral->reward_amount ?? 0, 2) }} ج.م
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $referrals->links() }}</div>
        @else
        <div class="text-center py-16">
            <div class="w-32 h-32 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                <i class="fas fa-user-friends text-5xl text-gray-400"></i>
            </div>
            <p class="text-gray-600 text-xl font-semibold mb-2">لا توجد إحالات حتى الآن</p>
            <p class="text-gray-500 text-sm mb-6">ابدأ بمشاركة كود الإحالة مع أصدقائك واحصل على مكافآت</p>
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-sky-50 border border-sky-200 rounded-lg">
                <i class="fas fa-info-circle text-sky-600"></i>
                <span class="text-sm text-gray-700">كلما زادت الإحالات، زادت المكافآت!</span>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function copyReferralCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
        Toast.fire({
            icon: 'success',
            title: 'تم نسخ كود الإحالة: ' + code
        });
    });
}

function copyReferralLink() {
    const link = document.getElementById('referralLink').value;
    navigator.clipboard.writeText(link).then(() => {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
        Toast.fire({
            icon: 'success',
            title: 'تم نسخ رابط الإحالة'
        });
    });
}
</script>
@endsection
