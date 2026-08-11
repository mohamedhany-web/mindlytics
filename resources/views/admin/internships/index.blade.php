@extends('layouts.admin')

@section('title', 'التدريب - Internships')
@section('header', 'قسم التدريب')

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
                <i class="fas fa-user-graduate text-cyan-600"></i>
                <span>فرص التدريب</span>
            </h1>
            <p class="text-sm text-slate-500 mt-1">إدارة فرص التدريب وطلبات التقديم من الصفحة العامة.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.internship-applications.index') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 shadow-sm">
                <i class="fas fa-inbox"></i>
                طلبات التقديم
                @if(($stats['pending'] ?? 0) > 0)
                    <span class="rounded-full bg-amber-100 text-amber-800 text-xs font-bold px-2 py-0.5">{{ $stats['pending'] }}</span>
                @endif
            </a>
            <a href="{{ route('public.internships.index') }}" target="_blank"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-900 shadow-sm">
                <i class="fas fa-external-link-alt"></i>
                الصفحة العامة
            </a>
            <a href="{{ route('admin.internships.create') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 px-4 py-2.5 text-sm font-semibold text-white shadow-lg">
                <i class="fas fa-plus"></i>
                فرصة جديدة
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-xs font-semibold text-slate-500">الكل</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
            <div class="text-xs font-semibold text-emerald-700">مفتوحة</div>
            <div class="text-2xl font-black text-emerald-900 mt-1">{{ number_format($stats['open']) }}</div>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
            <div class="text-xs font-semibold text-amber-700">مسودات</div>
            <div class="text-2xl font-black text-amber-900 mt-1">{{ number_format($stats['draft']) }}</div>
        </div>
        <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4 shadow-sm">
            <div class="text-xs font-semibold text-sky-700">كل الطلبات</div>
            <div class="text-2xl font-black text-sky-900 mt-1">{{ number_format($stats['applications']) }}</div>
        </div>
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 shadow-sm">
            <div class="text-xs font-semibold text-rose-700">قيد الانتظار</div>
            <div class="text-2xl font-black text-rose-900 mt-1">{{ number_format($stats['pending']) }}</div>
        </div>
    </div>

    <form method="GET" class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-3">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="بحث بالعنوان / القسم / الموقع"
               class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500/70 focus:border-cyan-500">
        <select name="status" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500/70 focus:border-cyan-500">
            <option value="">كل الحالات</option>
            @foreach(\App\Models\Internship::statuses() as $key => $label)
                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="published" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500/70 focus:border-cyan-500">
            <option value="">النشر: الكل</option>
            <option value="1" @selected(request('published') === '1')>منشور</option>
            <option value="0" @selected(request('published') === '0')>غير منشور</option>
        </select>
        <button class="rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold px-4 py-2.5">تصفية</button>
    </form>

    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 font-bold text-slate-700">العنوان</th>
                        <th class="px-4 py-3 font-bold text-slate-700">النوع</th>
                        <th class="px-4 py-3 font-bold text-slate-700">الحالة</th>
                        <th class="px-4 py-3 font-bold text-slate-700">الطلبات</th>
                        <th class="px-4 py-3 font-bold text-slate-700">الموعد النهائي</th>
                        <th class="px-4 py-3 font-bold text-slate-700">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($internships as $item)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900">{{ $item->title }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    {{ $item->department ?: '—' }}
                                    @if($item->is_featured)
                                        <span class="ms-1 inline-flex items-center rounded-md bg-amber-50 text-amber-700 px-1.5 py-0.5 font-bold">Featured</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $item->typeLabel() }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700">{{ $item->statusLabel() }}</span>
                                @if($item->is_published)
                                    <span class="px-2 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700">منشور</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-900">{{ $item->applications_count }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $item->application_deadline?->format('Y-m-d') ?: '—' }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.internships.edit', $item) }}"
                                   class="inline-flex items-center gap-1.5 text-cyan-700 font-semibold hover:text-cyan-900">
                                    <i class="fas fa-pen text-xs"></i>
                                    تعديل
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-14 text-center">
                                <div class="text-slate-400 mb-2"><i class="fas fa-user-graduate text-3xl"></i></div>
                                <div class="text-slate-500 font-medium">لا توجد فرص تدريب بعد.</div>
                                <a href="{{ route('admin.internships.create') }}" class="inline-flex mt-3 text-cyan-700 font-semibold hover:underline">إنشاء أول فرصة</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($internships->hasPages())
            <div class="p-4 border-t border-slate-100">{{ $internships->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
