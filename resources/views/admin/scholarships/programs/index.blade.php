@extends('layouts.admin')

@section('title', 'المنح الدراسية - Mindlytics')
@section('header', 'قسم المنح')

@section('content')
@include('admin.scholarships._styles')

<div class="space-y-6">
    @include('admin.scholarships._alerts')

    @include('admin.scholarships._header', [
        'title' => 'المنح الدراسية',
        'subtitle' => 'كل منحة لها رابط تسجيل خاص، كورس معزول، ومدرب مخصص',
        'icon' => 'fas fa-award',
        'actions' => '<a href="' . route('admin.scholarships.programs.create') . '" class="' . $schBtnPrimary . '"><i class="fas fa-plus"></i><span>منحة جديدة</span></a>',
    ])

    @include('admin.scholarships._stats-grid', ['cards' => [
        ['label' => 'إجمالي المنح', 'value' => number_format($overview['programs_total'] ?? 0), 'icon' => 'fas fa-award', 'description' => number_format($overview['programs_active'] ?? 0) . ' نشطة'],
        ['label' => 'مسجّلون', 'value' => number_format($overview['registrations_total'] ?? 0), 'icon' => 'fas fa-users', 'description' => 'كل التسجيلات'],
        ['label' => 'بانتظار التفعيل', 'value' => number_format($overview['registered'] ?? 0), 'icon' => 'fas fa-hourglass-half', 'description' => 'طلبات معلّقة'],
        ['label' => 'مفعّلون', 'value' => number_format($overview['activated'] ?? 0), 'icon' => 'fas fa-user-check', 'description' => 'طلاب نشطون'],
    ]])

    @include('admin.scholarships._nav', ['active' => 'programs'])

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md"><i class="fas fa-filter text-lg"></i></div>
                <div><h3 class="text-lg font-black text-slate-900">البحث والفلترة</h3></div>
            </div>
        </div>
        <div class="px-6 py-5">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="{{ $schLabelClass }}"><i class="fas fa-search text-blue-600 text-sm"></i> البحث</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم المنحة أو الرابط" class="{{ $schInputClass }}">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 {{ $schBtnPrimary }}"><i class="fas fa-search"></i><span>بحث</span></button>
                    @if(request()->filled('search'))
                        <a href="{{ route('admin.scholarships.programs.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold"><i class="fas fa-times"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md"><i class="fas fa-award text-lg"></i></div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">قائمة المنح</h3>
                    <p class="text-xs text-slate-600 font-medium mt-1"><span class="font-bold text-blue-600">{{ $programs->total() }}</span> منحة</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-700">
                        <th class="px-6 py-4 text-right">المنحة</th>
                        <th class="px-6 py-4 text-right">المدرب</th>
                        <th class="px-6 py-4 text-center">مسجّل</th>
                        <th class="px-6 py-4 text-center">مفعّل</th>
                        <th class="px-6 py-4 text-center">بانتظار</th>
                        <th class="px-6 py-4 text-center">الحالة</th>
                        <th class="px-6 py-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-sm">
                    @forelse($programs as $program)
                        <tr class="sch-table-row">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900">{{ $program->name }}</p>
                                <p class="text-xs text-slate-500 font-mono" dir="ltr">{{ $program->slug }}</p>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $program->instructor?->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-center font-bold tabular-nums">{{ $program->registrations_count ?? 0 }}</td>
                            <td class="px-6 py-4 text-center font-bold text-emerald-600 tabular-nums">{{ $program->activated_count ?? 0 }}</td>
                            <td class="px-6 py-4 text-center font-bold text-amber-600 tabular-nums">{{ $program->pending_count ?? 0 }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold {{ $program->is_active ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $program->is_active ? 'نشطة' : 'متوقفة' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.scholarships.programs.show', $program) }}" class="w-9 h-9 flex items-center justify-center bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-lg shadow-sm" title="عرض"><i class="fas fa-eye text-sm"></i></a>
                                    <a href="{{ route('admin.scholarships.programs.edit', $program) }}" class="w-9 h-9 flex items-center justify-center bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg shadow-sm" title="تعديل"><i class="fas fa-edit text-sm"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-16 text-center text-slate-500 font-medium">لا توجد منح بعد</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($programs->hasPages())<div class="px-6 py-4 border-t border-slate-200 bg-slate-50">{{ $programs->links() }}</div>@endif
    </section>
</div>
@endsection
