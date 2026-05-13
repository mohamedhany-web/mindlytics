@extends('layouts.admin')

@section('title', 'لوحة الفرع')
@section('header', 'لوحة الفرع — ' . $branch->name)

@section('content')
<div class="p-6 lg:p-8 space-y-8 max-w-6xl mx-auto">
    <div class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-6 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900 mb-1">مرحباً، {{ auth()->user()->name }}</h2>
        <p class="text-sm text-slate-600">هذه اللوحة تعرض أرقاماً وملخصات لـ <strong>{{ $branch->name }}</strong> فقط. الدخول من الرابط الثابت: <code class="text-xs bg-slate-100 px-1 rounded" dir="ltr">{{ url('/branch-office') }}</code></p>
        @if($branch->suggestedSubdomainUrl())
            <p class="text-xs text-slate-500 mt-2">يمكن للطلاب زيارة النطاق الفرعي: <a class="text-emerald-700 underline break-all" dir="ltr" href="{{ $branch->suggestedSubdomainUrl() }}">{{ $branch->suggestedSubdomainUrl() }}</a></p>
        @endif
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-sm">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-semibold text-slate-500">المستخدمون</span>
                <i class="fas fa-users text-blue-500"></i>
            </div>
            <p class="text-2xl font-black text-slate-900 mt-2 tabular-nums">{{ number_format($stats['users']) }}</p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-sm">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-semibold text-slate-500">طلاب</span>
                <i class="fas fa-user-graduate text-indigo-500"></i>
            </div>
            <p class="text-2xl font-black text-slate-900 mt-2 tabular-nums">{{ number_format($stats['students']) }}</p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-sm">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-semibold text-slate-500">مدربون</span>
                <i class="fas fa-chalkboard-teacher text-violet-500"></i>
            </div>
            <p class="text-2xl font-black text-slate-900 mt-2 tabular-nums">{{ number_format($stats['instructors']) }}</p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-sm">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-semibold text-slate-500">كورسات أونلاين</span>
                <i class="fas fa-laptop-code text-emerald-500"></i>
            </div>
            <p class="text-2xl font-black text-slate-900 mt-2 tabular-nums">{{ number_format($stats['advanced_courses']) }}</p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-sm">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-semibold text-slate-500">كورسات أوفلاين</span>
                <i class="fas fa-school text-teal-500"></i>
            </div>
            <p class="text-2xl font-black text-slate-900 mt-2 tabular-nums">{{ number_format($stats['offline_courses']) }}</p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-sm">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-semibold text-slate-500">تسجيلات</span>
                <i class="fas fa-book-reader text-amber-500"></i>
            </div>
            <p class="text-2xl font-black text-slate-900 mt-2 tabular-nums">{{ number_format($stats['enrollments']) }}</p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-4 shadow-sm">
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-semibold text-slate-500">طلبات</span>
                <i class="fas fa-shopping-cart text-orange-500"></i>
            </div>
            <p class="text-2xl font-black text-slate-900 mt-2 tabular-nums">{{ number_format($stats['orders']) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900">آخر المستخدمين المضافين للفرع</h3>
            <a href="{{ route('branch.office.users') }}" class="text-sm font-semibold text-emerald-700 hover:underline">عرض الكل</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="text-right px-4 py-3 font-semibold">الاسم</th>
                        <th class="text-right px-4 py-3 font-semibold">البريد</th>
                        <th class="text-right px-4 py-3 font-semibold">الدور</th>
                        <th class="text-right px-4 py-3 font-semibold">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentUsers as $u)
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $u->name }}</td>
                            <td class="px-4 py-3 text-slate-600" dir="ltr">{{ $u->email }}</td>
                            <td class="px-4 py-3"><span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs">{{ $u->role }}</span></td>
                            <td class="px-4 py-3">{{ $u->is_active ? 'نشط' : 'موقوف' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">لا يوجد مستخدمون بعد في هذا الفرع.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
