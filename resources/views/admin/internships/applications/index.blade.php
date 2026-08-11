@extends('layouts.admin')

@section('title', 'طلبات التدريب')
@section('header', 'طلبات التقديم على التدريب')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-6" style="background: #f8fafc; min-height: 100%;">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-800 text-sm font-semibold flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-inbox text-cyan-600"></i>
                <span>طلبات التدريب</span>
            </h1>
            <p class="text-sm text-slate-500 mt-1">مراجعة طلبات التقديم على فرص الـ Internships.</p>
        </div>
        <a href="{{ route('admin.internships.index') }}"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 shadow-sm">
            <i class="fas fa-list"></i>
            فرص التدريب
        </a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><div class="text-xs font-semibold text-slate-500">الكل</div><div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total']) }}</div></div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm"><div class="text-xs font-semibold text-amber-700">معلّق</div><div class="text-2xl font-black text-amber-900 mt-1">{{ number_format($stats['pending']) }}</div></div>
        <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4 shadow-sm"><div class="text-xs font-semibold text-sky-700">مراجَع</div><div class="text-2xl font-black text-sky-900 mt-1">{{ number_format($stats['reviewed']) }}</div></div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm"><div class="text-xs font-semibold text-emerald-700">مقبول</div><div class="text-2xl font-black text-emerald-900 mt-1">{{ number_format($stats['accepted']) }}</div></div>
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 shadow-sm"><div class="text-xs font-semibold text-rose-700">مرفوض</div><div class="text-2xl font-black text-rose-900 mt-1">{{ number_format($stats['rejected']) }}</div></div>
    </div>

    <form method="GET" class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-3">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="اسم / إيميل / هاتف / جامعة"
               class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500/70 focus:border-cyan-500">
        <select name="status" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500/70 focus:border-cyan-500">
            <option value="">كل الحالات</option>
            @foreach(\App\Models\InternshipApplication::statuses() as $key => $label)
                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="internship_id" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500/70 focus:border-cyan-500">
            <option value="">كل الفرص</option>
            @foreach($internships as $item)
                <option value="{{ $item->id }}" @selected((string) request('internship_id') === (string) $item->id)>{{ $item->title }}</option>
            @endforeach
        </select>
        <button class="rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold px-4 py-2.5">تصفية</button>
    </form>

    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 font-bold text-slate-700">المتقدم</th>
                        <th class="px-4 py-3 font-bold text-slate-700">الفرصة</th>
                        <th class="px-4 py-3 font-bold text-slate-700">الحالة</th>
                        <th class="px-4 py-3 font-bold text-slate-700">التاريخ</th>
                        <th class="px-4 py-3 font-bold text-slate-700">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($applications as $app)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900">{{ $app->name }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $app->email }} @if($app->phone)· {{ $app->phone }}@endif</div>
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $app->internship->title ?? '—' }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700">{{ $app->statusLabel() }}</span></td>
                            <td class="px-4 py-3 text-slate-600">{{ $app->created_at?->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.internship-applications.show', $app) }}"
                                   class="inline-flex items-center gap-1.5 text-cyan-700 font-semibold hover:text-cyan-900">
                                    <i class="fas fa-eye text-xs"></i>
                                    عرض
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-14 text-center text-slate-500">لا توجد طلبات بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($applications->hasPages())
            <div class="p-4 border-t border-slate-100">{{ $applications->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
