@extends('layouts.admin')

@section('title', 'عرض المستخدم - ' . $user->name)
@section('header', 'عرض المستخدم')

@section('content')
@php
    $roles = [
        'super_admin' => ['label' => 'مدير عام', 'badge' => 'bg-rose-100 text-rose-700 border border-rose-200'],
        'admin' => ['label' => 'إداري', 'badge' => 'bg-indigo-100 text-indigo-700 border border-indigo-200'],
        'instructor' => ['label' => 'مدرب', 'badge' => 'bg-sky-100 text-sky-700 border border-sky-200'],
        'teacher' => ['label' => 'مدرس', 'badge' => 'bg-sky-100 text-sky-700 border border-sky-200'],
        'student' => ['label' => 'طالب', 'badge' => 'bg-emerald-100 text-emerald-700 border border-emerald-200'],
        'parent' => ['label' => 'ولي أمر', 'badge' => 'bg-amber-100 text-amber-700 border border-amber-200'],
        'employee' => ['label' => 'موظف', 'badge' => 'bg-amber-100 text-amber-700 border border-amber-200'],
    ];
    $roleKey = $user->is_employee ? 'employee' : $user->role;
    $roleMeta = $roles[$roleKey] ?? $roles['student'];
@endphp
<div class="space-y-6">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-user text-lg"></i>
                </div>
                <div>
                    <nav class="text-xs font-medium text-slate-500 flex flex-wrap items-center gap-2 mb-1">
                        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-700">لوحة التحكم</a>
                        <span>/</span>
                        <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-700">إدارة المستخدمين</a>
                        <span>/</span>
                        <span class="text-slate-600">عرض المستخدم</span>
                    </nav>
                    <h2 class="text-2xl font-black text-slate-900 mt-1">{{ $user->name }}</h2>
                    <p class="text-sm text-slate-600 mt-1">تفاصيل الحساب والحالة</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 text-sm font-semibold transition-colors">
                    <i class="fas fa-edit"></i>
                    تعديل
                </a>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-user text-blue-600"></i>
                        البيانات الأساسية
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        <div class="flex-shrink-0">
                            @if($user->profile_image)
                                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-2xl object-cover border-2 border-slate-200">
                            @else
                                <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-3xl font-bold">
                                    {{ mb_substr($user->name, 0, 1, 'UTF-8') }}
                                </div>
                            @endif
                        </div>
                        <div class="space-y-2 flex-1">
                            <p class="text-sm text-slate-600 font-medium">الاسم</p>
                            <p class="text-lg font-bold text-slate-900">{{ $user->name }}</p>
                            <p class="text-sm text-slate-600 font-medium mt-3">البريد الإلكتروني</p>
                            <p class="text-slate-900">{{ $user->email ?: '—' }}</p>
                            <p class="text-sm text-slate-600 font-medium mt-2">رقم الهاتف</p>
                            <p class="text-slate-900" dir="ltr">{{ $user->phone ?: '—' }}</p>
                        </div>
                    </div>
                    @if($user->bio)
                        <div class="pt-4 border-t border-slate-200">
                            <p class="text-sm text-slate-600 font-medium mb-2">النبذة التعريفية</p>
                            <p class="text-slate-800 whitespace-pre-wrap">{{ $user->bio }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="space-y-6">
            <div class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        الحالة والدور
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-xs font-semibold text-slate-600 mb-1">الدور</p>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold {{ $roleMeta['badge'] }}">
                            {{ $roleMeta['label'] }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-600 mb-1">حالة الحساب</p>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold {{ $user->is_active ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200' }}">
                            <span class="h-2 w-2 rounded-full bg-current"></span>
                            {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-600 mb-1">رقم العضوية</p>
                        <p class="font-bold text-slate-900">#{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-600 mb-1">تاريخ التسجيل</p>
                        <p class="text-slate-900">{{ $user->created_at ? $user->created_at->format('Y-m-d H:i') : '—' }}</p>
                    </div>
                    @if($user->last_login_at)
                        <div>
                            <p class="text-xs font-semibold text-slate-600 mb-1">آخر تسجيل دخول</p>
                            <p class="text-slate-900">{{ $user->last_login_at->format('Y-m-d H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
