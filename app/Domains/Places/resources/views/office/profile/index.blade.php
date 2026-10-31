@extends('layouts.place-manager')

@section('title', 'الملف الشخصي')
@section('header', 'الملف الشخصي')

@push('styles')
<style>
    .profile-header-card {
        background: linear-gradient(135deg, rgba(44, 169, 189, 0.1) 0%, rgba(101, 219, 228, 0.05) 100%);
        border: 2px solid rgba(44, 169, 189, 0.2);
    }
    .info-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 2px solid rgba(44, 169, 189, 0.1);
    }
    .form-input:focus {
        box-shadow: 0 8px 20px rgba(44, 169, 189, 0.15);
    }
</style>
@endpush

@section('content')
@php
    $memberSince = $user->created_at ? $user->created_at->copy()->locale('ar')->translatedFormat('d F Y') : null;
    $lastLogin = $user->last_login_at ? $user->last_login_at->copy()->locale('ar')->diffForHumans() : null;
@endphp

<div class="space-y-6">
    <div class="profile-header-card rounded-2xl p-6 sm:p-8 shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center gap-5">
            <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white overflow-hidden flex items-center justify-center mx-auto sm:mx-0">
                @if($user->profile_image)
                    <img src="{{ $user->profile_image_url }}" alt="" class="w-full h-full object-cover">
                @else
                    <span class="text-4xl font-bold">{{ mb_substr($user->name, 0, 1, 'UTF-8') }}</span>
                @endif
            </div>
            <div class="text-center sm:text-right">
                <h2 class="text-2xl font-black text-gray-900">{{ $user->name }}</h2>
                <p class="text-gray-600">مدير مكان — {{ $location->name ?? '' }}</p>
                <p class="text-sm text-gray-500 mt-1">عضو منذ {{ $memberSince }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl p-5 border-2 border-blue-200/50 shadow-lg">
            <p class="text-sm text-gray-600">البريد</p>
            <p class="font-bold text-gray-900 mt-1" dir="ltr">{{ $user->email }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 border-2 border-green-200/50 shadow-lg">
            <p class="text-sm text-gray-600">آخر تسجيل دخول</p>
            <p class="font-bold text-gray-900 mt-1">{{ $lastLogin ?? '—' }}</p>
        </div>
    </div>

    <div class="info-card rounded-2xl p-6 sm:p-8 shadow-lg">
        <h3 class="text-xl font-black text-gray-900 mb-6">تحديث البيانات وكلمة المرور</h3>
        <form method="POST" action="{{ route('place.office.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">الاسم الكامل *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="form-input w-full rounded-xl border-2 border-gray-200 px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">رقم الهاتف *</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required
                           class="form-input w-full rounded-xl border-2 border-gray-200 px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-900 mb-2">البريد الإلكتروني *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="form-input w-full rounded-xl border-2 border-gray-200 px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-900 mb-2">العنوان</label>
                    <input type="text" name="address" value="{{ old('address', $user->address) }}"
                           class="form-input w-full rounded-xl border-2 border-gray-200 px-4 py-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">صورة الملف الشخصي</label>
                <input type="file" name="profile_image" accept="image/*"
                       class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700">
            </div>

            <div class="border-t pt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">كلمة المرور الحالية</label>
                    <input type="password" name="current_password" class="form-input w-full rounded-xl border-2 border-gray-200 px-4 py-3">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">كلمة مرور جديدة</label>
                    <input type="password" name="password" class="form-input w-full rounded-xl border-2 border-gray-200 px-4 py-3">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" class="form-input w-full rounded-xl border-2 border-gray-200 px-4 py-3">
                </div>
            </div>

            <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold">
                <i class="fas fa-save ml-2"></i>حفظ التغييرات
            </button>
        </form>
    </div>
</div>
@endsection
