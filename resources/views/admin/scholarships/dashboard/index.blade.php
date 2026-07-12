@extends('layouts.admin')

@section('title', 'إدارة المنح الدراسية - Mindlytics')
@section('header', 'قسم المنح')

@section('content')
@include('admin.scholarships._styles')

@php
    $o = $overview;
    $statusBadges = [
        'registered' => 'bg-amber-100 text-amber-700 border border-amber-200',
        'activated' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
        'rejected' => 'bg-rose-100 text-rose-700 border border-rose-200',
        'deactivated' => 'bg-slate-100 text-slate-700 border border-slate-200',
    ];
@endphp

<div class="w-full space-y-6">
    @include('admin.scholarships._alerts')

    @include('admin.scholarships._header', [
        'title' => 'لوحة المنح',
        'subtitle' => 'رقابة شاملة للمنح والكورسات والمدربين والطلاب والمجموعات والوصول',
        'icon' => 'fas fa-tachometer-alt',
        'actions' => '<a href="' . route('admin.scholarships.programs.create') . '" class="' . $schBtnPrimary . '"><i class="fas fa-plus"></i><span>منحة جديدة</span></a>',
    ])

    @include('admin.scholarships._stats-grid', ['cards' => [
        ['label' => 'المنح الدراسية', 'value' => number_format($o['programs_total'] ?? 0), 'icon' => 'fas fa-award', 'description' => number_format($o['programs_active'] ?? 0) . ' منحة نشطة'],
        ['label' => 'طلاب مفعّلون', 'value' => number_format($o['activated'] ?? 0), 'icon' => 'fas fa-user-check', 'description' => 'لديهم وصول للكورس'],
        ['label' => 'المجموعات', 'value' => number_format($o['groups_total'] ?? 0), 'icon' => 'fas fa-layer-group', 'description' => 'تقسيم الطلبة'],
        ['label' => 'محتوى مقيّد', 'value' => number_format(($o['restricted_sections'] ?? 0) + ($o['restricted_items'] ?? 0)), 'icon' => 'fas fa-user-lock', 'description' => (number_format($o['restricted_sections'] ?? 0) . ' أقسام / ' . number_format($o['restricted_items'] ?? 0) . ' عناصر')],
    ]])

    {{-- أقسام الرقابة — صفحات مستقلة --}}
    <section class="w-full grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach([
            ['route' => 'admin.scholarships.programs.index', 'icon' => 'fas fa-award', 'title' => 'المنح الدراسية', 'desc' => 'إنشاء ومتابعة كل المنح وروابط التسجيل'],
            ['route' => 'admin.scholarships.courses.index', 'icon' => 'fas fa-book', 'title' => 'كورسات المنح', 'desc' => 'رقابة الكورسات المعزولة ووصول المنهج'],
            ['route' => 'admin.scholarships.instructors.index', 'icon' => 'fas fa-chalkboard-teacher', 'title' => 'مدربو المنح', 'desc' => 'متابعة المدربين وطلاب كل منحة'],
            ['route' => 'admin.scholarships.students.index', 'icon' => 'fas fa-user-graduate', 'title' => 'طلاب المنح', 'desc' => 'تفعيل ورفض وإلغاء تفعيل المسجّلين'],
            ['route' => 'admin.scholarships.groups.index', 'icon' => 'fas fa-layer-group', 'title' => 'المجموعات والوصول', 'desc' => 'تقسيم الطلبة ورقابة المحتوى المقيّد'],
        ] as $link)
            <a href="{{ route($link['route']) }}" class="sch-card rounded-2xl p-5 flex items-start gap-4 hover:border-blue-300 transition-all">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md shrink-0">
                    <i class="{{ $link['icon'] }}"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="font-black text-slate-900">{{ $link['title'] }}</h3>
                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">{{ $link['desc'] }}</p>
                </div>
            </a>
        @endforeach
    </section>

    {{-- البحث والفلترة --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-filter text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">البحث والفلترة</h3>
                    <p class="text-xs text-slate-600 font-medium mt-1">ابحث في المنح وفلتر حسب الحالة</p>
                </div>
            </div>
        </div>
        <div class="px-6 py-5">
            <form method="GET" action="{{ route('admin.scholarships.dashboard') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="sm:col-span-2">
                    <label class="{{ $schLabelClass }}"><i class="fas fa-search text-blue-600 text-sm"></i> البحث</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم المنحة أو الرابط (slug)" class="{{ $schInputClass }}">
                </div>
                <div>
                    <label class="{{ $schLabelClass }}"><i class="fas fa-toggle-on text-blue-600 text-sm"></i> الحالة</label>
                    <select name="status" class="{{ $schSelectClass }}">
                        <option value="">جميع الحالات</option>
                        <option value="active" @selected(request('status') === 'active')>نشطة</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>متوقفة</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 {{ $schBtnPrimary }}"><i class="fas fa-search"></i><span>بحث</span></button>
                    @if(request()->anyFilled(['search', 'status']))
                        <a href="{{ route('admin.scholarships.dashboard') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors" title="مسح الفلتر"><i class="fas fa-times"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    {{-- قائمة المنح --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-award text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">قائمة المنح الدراسية</h3>
                    <p class="text-xs text-slate-600 font-medium mt-1"><span class="font-bold text-blue-600">{{ $programs->total() }}</span> منحة</p>
                </div>
            </div>
            <a href="{{ route('admin.scholarships.students.index', ['status' => 'registered']) }}" class="{{ $schBtnSecondary }}">
                <i class="fas fa-user-clock text-amber-600"></i>
                <span>طلبات التفعيل ({{ number_format($o['registered'] ?? 0) }})</span>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-700">
                        <th class="px-6 py-4 text-right"><div class="flex items-center gap-2"><i class="fas fa-award text-blue-600"></i><span>المنحة</span></div></th>
                        <th class="px-6 py-4 text-right"><div class="flex items-center gap-2"><i class="fas fa-chalkboard-teacher text-blue-600"></i><span>المدرب</span></div></th>
                        <th class="px-6 py-4 text-center"><div class="flex items-center justify-center gap-2"><i class="fas fa-users text-blue-600"></i><span>مسجّل</span></div></th>
                        <th class="px-6 py-4 text-center"><div class="flex items-center justify-center gap-2"><i class="fas fa-check text-blue-600"></i><span>مفعّل</span></div></th>
                        <th class="px-6 py-4 text-center"><div class="flex items-center justify-center gap-2"><i class="fas fa-clock text-blue-600"></i><span>بانتظار</span></div></th>
                        <th class="px-6 py-4 text-center"><div class="flex items-center justify-center gap-2"><i class="fas fa-toggle-on text-blue-600"></i><span>الحالة</span></div></th>
                        <th class="px-6 py-4 text-center"><div class="flex items-center justify-center gap-2"><i class="fas fa-cog text-blue-600"></i><span>الإجراءات</span></div></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-sm">
                    @forelse($programs as $program)
                        <tr class="sch-table-row">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900 text-base">{{ $program->name }}</p>
                                <p class="text-xs text-slate-500 font-mono mt-0.5" dir="ltr">{{ $program->slug }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($program->instructor)
                                    <a href="{{ route('admin.scholarships.instructors.show', $program->instructor) }}" class="font-semibold text-slate-900 hover:text-blue-600 hover:underline">{{ $program->instructor->name }}</a>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-slate-800 tabular-nums">{{ $program->registrations_count ?? 0 }}</td>
                            <td class="px-6 py-4 text-center font-bold text-emerald-600 tabular-nums">{{ $program->activated_count ?? 0 }}</td>
                            <td class="px-6 py-4 text-center font-bold text-amber-600 tabular-nums">{{ $program->pending_count ?? 0 }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold {{ $program->is_active ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    {{ $program->is_active ? 'نشطة' : 'متوقفة' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.scholarships.programs.show', $program) }}" class="w-9 h-9 flex items-center justify-center bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-lg transition-colors shadow-sm hover:shadow-md" title="عرض"><i class="fas fa-eye text-sm"></i></a>
                                    <a href="{{ route('admin.scholarships.programs.edit', $program) }}" class="w-9 h-9 flex items-center justify-center bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition-colors shadow-sm hover:shadow-md" title="تعديل"><i class="fas fa-edit text-sm"></i></a>
                                    <a href="{{ route('admin.scholarships.students.index', ['program_id' => $program->id]) }}" class="w-9 h-9 flex items-center justify-center bg-emerald-50 hover:bg-emerald-100 text-emerald-600 rounded-lg transition-colors shadow-sm hover:shadow-md" title="الطلاب"><i class="fas fa-user-graduate text-sm"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center"><i class="fas fa-award text-3xl text-blue-600"></i></div>
                                    <div>
                                        <p class="font-bold text-slate-900 text-lg mb-1">لا توجد منح بعد</p>
                                        <p class="text-sm text-slate-600 font-medium">أنشئ أول منحة دراسية لبدء التسجيل</p>
                                    </div>
                                    <a href="{{ route('admin.scholarships.programs.create') }}" class="{{ $schBtnPrimary }}"><i class="fas fa-plus"></i><span>منحة جديدة</span></a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($programs->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">{{ $programs->links() }}</div>
        @endif
    </section>

    {{-- طلبات التفعيل + أحدث المنح --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <section class="sch-card rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center text-white shadow-md"><i class="fas fa-hourglass-half text-lg"></i></div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">بانتظار التفعيل</h3>
                        <p class="text-xs text-slate-600 font-medium mt-1">آخر طلبات التسجيل</p>
                    </div>
                </div>
                <a href="{{ route('admin.scholarships.students.index', ['status' => 'registered']) }}" class="text-sm font-semibold text-blue-600 hover:underline">عرض الكل</a>
            </div>
            <div class="p-4 space-y-2 max-h-96 overflow-y-auto">
                @forelse($pendingRegistrations as $registration)
                    <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-colors">
                        <div class="sch-avatar-gradient w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold shadow-md flex-shrink-0">{{ mb_substr($registration->user?->name ?? '?', 0, 1, 'UTF-8') }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-900 truncate">{{ $registration->user?->name }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ $registration->program?->name }}</p>
                        </div>
                        @include('admin.scholarships._registration-actions', ['registration' => $registration])
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-500 font-medium">لا يوجد طلاب بانتظار التفعيل</div>
                @endforelse
            </div>
        </section>

        <section class="sch-card rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md"><i class="fas fa-award text-lg"></i></div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">أحدث المنح</h3>
                        <p class="text-xs text-slate-600 font-medium mt-1">آخر المنح المُنشأة</p>
                    </div>
                </div>
                <a href="{{ route('admin.scholarships.programs.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">عرض الكل</a>
            </div>
            <div class="p-4 space-y-2 max-h-96 overflow-y-auto">
                @forelse($recentPrograms as $program)
                    <div class="flex items-center justify-between gap-3 p-3 rounded-lg hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-colors">
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 truncate">{{ $program->name }}</p>
                            <p class="text-xs text-slate-500">{{ $program->instructor?->name }} — {{ $program->pending_count ?? 0 }} بانتظار / {{ $program->activated_count ?? 0 }} مفعّل</p>
                        </div>
                        <a href="{{ route('admin.scholarships.programs.show', $program) }}" class="text-sm font-semibold text-blue-600 hover:underline shrink-0">إدارة</a>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-500 font-medium">لا توجد منح بعد</div>
                @endforelse
            </div>
        </section>
    </div>

    {{-- إجراءات سريعة --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md"><i class="fas fa-bolt text-lg"></i></div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">إجراءات سريعة</h3>
                    <p class="text-xs text-slate-600 font-medium mt-1">الوصول السريع لأقسام المنح</p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 p-6">
            <a href="{{ route('admin.scholarships.programs.create') }}" class="group rounded-xl border border-slate-200 bg-white p-5 hover:border-blue-300 hover:shadow-md transition-all sch-card">
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 shadow-sm mb-3"><i class="fas fa-plus text-lg"></i></div>
                <h4 class="text-sm font-bold text-slate-900 mb-1">منحة جديدة</h4>
                <p class="text-xs text-slate-600">إنشاء منحة ورابط تسجيل خاص</p>
            </a>
            <a href="{{ route('admin.scholarships.students.index') }}" class="group rounded-xl border border-slate-200 bg-white p-5 hover:border-blue-300 hover:shadow-md transition-all sch-card">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shadow-sm mb-3"><i class="fas fa-user-graduate text-lg"></i></div>
                <h4 class="text-sm font-bold text-slate-900 mb-1">طلاب المنح</h4>
                <p class="text-xs text-slate-600">تفعيل ورفض وإدارة المسجّلين</p>
            </a>
            <a href="{{ route('admin.scholarships.courses.index') }}" class="group rounded-xl border border-slate-200 bg-white p-5 hover:border-blue-300 hover:shadow-md transition-all sch-card">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600 shadow-sm mb-3"><i class="fas fa-book text-lg"></i></div>
                <h4 class="text-sm font-bold text-slate-900 mb-1">كورسات المنح</h4>
                <p class="text-xs text-slate-600">الكورسات المعزولة لكل منحة</p>
            </a>
            <a href="{{ route('admin.scholarships.instructors.index') }}" class="group rounded-xl border border-slate-200 bg-white p-5 hover:border-blue-300 hover:shadow-md transition-all sch-card">
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 shadow-sm mb-3"><i class="fas fa-chalkboard-teacher text-lg"></i></div>
                <h4 class="text-sm font-bold text-slate-900 mb-1">مدربو المنح</h4>
                <p class="text-xs text-slate-600">المدربون المعيّنون للمنح</p>
            </a>
        </div>
    </section>
</div>
@endsection
