@extends('layouts.admin')

@section('title', 'تعديل المستخدم - ' . $user->name)
@section('header', 'تعديل المستخدم')

@section('content')
<div class="space-y-6">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-user-edit text-lg"></i>
                </div>
                <div>
                    <nav class="text-xs font-medium text-slate-500 flex flex-wrap items-center gap-2 mb-1">
                        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-700">لوحة التحكم</a>
                        <span>/</span>
                        <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-700">إدارة المستخدمين</a>
                        <span>/</span>
                        <a href="{{ route('admin.users.show', $user->id) }}" class="text-blue-600 hover:text-blue-700">عرض المستخدم</a>
                        <span>/</span>
                        <span class="text-slate-600">تعديل</span>
                    </nav>
                    <h2 class="text-2xl font-black text-slate-900 mt-1">تعديل بيانات المستخدم</h2>
                    <p class="text-sm text-slate-600 mt-1">تحديث الاسم، التواصل، الدور وحالة الحساب.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.show', $user->id) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    <i class="fas fa-eye"></i>
                    عرض
                </a>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </section>

    @if(session('success') || request('updated') == '1')
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-emerald-800 text-sm font-medium flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-600"></i>
            {{ session('success', 'تم تحديث بيانات المستخدم بنجاح') }}
        </div>
    @endif

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}" id="editUserForm" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-5">
                        <div class="flex items-center gap-3 pb-4 border-b border-slate-200">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                                <i class="fas fa-user text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">المعلومات الأساسية</h3>
                                <p class="text-xs text-slate-600 mt-1">الاسم، البريد، رقم الهاتف.</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1">
                                <label for="name" class="block text-xs font-semibold text-slate-700 mb-2">الاسم الكامل <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required maxlength="255" class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
                                @error('name')<p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
                            </div>
                            <div class="space-y-1">
                                <label for="phone" class="block text-xs font-semibold text-slate-700 mb-2">رقم الهاتف <span class="text-rose-500">*</span></label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" required class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" dir="ltr" />
                                @error('phone')<p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
                            </div>
                            <div class="space-y-1 md:col-span-2">
                                <label for="email" class="block text-xs font-semibold text-slate-700 mb-2">البريد الإلكتروني</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" maxlength="255" class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
                                @error('email')<p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
                            </div>
                            <div class="space-y-1 md:col-span-2">
                                <label for="password" class="block text-xs font-semibold text-slate-700 mb-2">كلمة المرور الجديدة (اختياري)</label>
                                <input type="password" name="password" id="password" minlength="8" class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="اتركه فارغاً إذا لم ترغب بتغيير كلمة المرور" />
                                @error('password')<p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-3">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-200">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600">
                                <i class="fas fa-align-right text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">نبذة تعريفية (اختياري)</h3>
                            </div>
                        </div>
                        <textarea name="bio" id="bio" rows="4" maxlength="1000" class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">{{ old('bio', $user->bio ?? '') }}</textarea>
                        @error('bio')<p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="space-y-6">
                    <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-5">
                        <div class="flex items-center gap-3 pb-4 border-b border-slate-200">
                            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                                <i class="fas fa-user-shield text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">الدور والحالة</h3>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="space-y-1">
                                <label for="role" class="block text-xs font-semibold text-slate-700 mb-2">الدور <span class="text-rose-500">*</span></label>
                                <select name="role" id="role" required class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all cursor-pointer">
                                    <option value="super_admin" {{ old('role', $user->is_employee ? 'employee' : $user->role) == 'super_admin' ? 'selected' : '' }}>مدير عام</option>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>إداري</option>
                                    <option value="instructor" {{ old('role', $user->role) == 'instructor' ? 'selected' : '' }}>مدرب</option>
                                    <option value="teacher" {{ old('role', $user->role) == 'teacher' ? 'selected' : '' }}>مدرس</option>
                                    <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>طالب</option>
                                    <option value="parent" {{ old('role', $user->role) == 'parent' ? 'selected' : '' }}>ولي أمر</option>
                                    <option value="employee" {{ old('role', $user->is_employee ? 'employee' : $user->role) == 'employee' ? 'selected' : '' }}>موظف</option>
                                </select>
                                @error('role')<p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
                            </div>
                            <div class="space-y-1">
                                <label for="is_active" class="block text-xs font-semibold text-slate-700 mb-2">حالة الحساب <span class="text-rose-500">*</span></label>
                                <select name="is_active" id="is_active" required class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all cursor-pointer">
                                    <option value="1" {{ old('is_active', ($user->is_active ?? true) ? '1' : '0') == '1' ? 'selected' : '' }}>نشط</option>
                                    <option value="0" {{ old('is_active', ($user->is_active ?? true) ? '1' : '0') == '0' ? 'selected' : '' }}>غير نشط</option>
                                </select>
                                @error('is_active')<p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
                        <div class="flex flex-col gap-3">
                            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg hover:shadow-xl transition-all duration-200">
                                <i class="fas fa-save"></i>
                                <span>حفظ التعديلات</span>
                            </button>
                            <a href="{{ route('admin.users.show', $user->id) }}" class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-slate-300 px-6 py-3.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-all">
                                <i class="fas fa-times"></i>
                                <span>إلغاء</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>
@endsection
