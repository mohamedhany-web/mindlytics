@extends('layouts.admin')

@section('title', 'كورسات المنح - Mindlytics')
@section('header', 'قسم المنح')

@section('content')
@include('admin.scholarships._styles')

<div class="w-full space-y-6">
    @include('admin.scholarships._alerts')
    @include('admin.scholarships._header', [
        'title' => 'كورسات المنح',
        'subtitle' => 'كورسات معزولة تُنشأ تلقائياً لكل منحة — لا تظهر في الكتالوج العام',
        'icon' => 'fas fa-book',
    ])
    @include('admin.scholarships._stats-grid', ['cards' => [
        ['label' => 'إجمالي الكورسات', 'value' => number_format($overview['courses_total'] ?? 0), 'icon' => 'fas fa-book', 'description' => number_format($overview['courses_active'] ?? 0) . ' نشطة'],
        ['label' => 'أقسام مقيّدة', 'value' => number_format($overview['restricted_sections'] ?? 0), 'icon' => 'fas fa-user-lock', 'description' => 'وصول محدود'],
        ['label' => 'عناصر مقيّدة', 'value' => number_format($overview['restricted_items'] ?? 0), 'icon' => 'fas fa-lock', 'description' => 'محاضرات/واجبات'],
        ['label' => 'المجموعات', 'value' => number_format($overview['groups_total'] ?? 0), 'icon' => 'fas fa-layer-group', 'description' => 'تقسيم الطلبة'],
    ]])

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md"><i class="fas fa-filter text-lg"></i></div>
                <div><h3 class="text-lg font-black text-slate-900">البحث والفلترة</h3></div>
            </div>
        </div>
        <div class="px-6 py-5">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="{{ $schLabelClass }}"><i class="fas fa-search text-blue-600 text-sm"></i> البحث</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="عنوان الكورس أو المنحة" class="{{ $schInputClass }}">
                </div>
                <div>
                    <label class="{{ $schLabelClass }}"><i class="fas fa-toggle-on text-blue-600 text-sm"></i> الحالة</label>
                    <select name="status" class="{{ $schSelectClass }}">
                        <option value="">كل الحالات</option>
                        <option value="active" @selected(request('status') === 'active')>نشط</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>غير نشط</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 {{ $schBtnPrimary }}"><i class="fas fa-search"></i><span>بحث</span></button>
                    @if(request()->anyFilled(['search', 'status']))
                        <a href="{{ route('admin.scholarships.courses.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold"><i class="fas fa-times"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md"><i class="fas fa-book text-lg"></i></div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">قائمة كورسات المنح</h3>
                    <p class="text-xs text-slate-600 font-medium mt-1"><span class="font-bold text-blue-600">{{ $courses->total() }}</span> كورس</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-700">
                        <th class="px-6 py-4 text-right">الكورس</th>
                        <th class="px-6 py-4 text-right">المنحة</th>
                        <th class="px-6 py-4 text-right">المدرب</th>
                        <th class="px-6 py-4 text-center">طلاب مفعّلون</th>
                        <th class="px-6 py-4 text-center">الحالة</th>
                        <th class="px-6 py-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-sm">
                    @forelse($courses as $course)
                        <tr class="sch-table-row">
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $course->title }}</td>
                            <td class="px-6 py-4">
                                @if($course->scholarshipProgram)
                                    <a href="{{ route('admin.scholarships.programs.show', $course->scholarshipProgram) }}" class="font-semibold hover:text-blue-600 hover:underline">{{ $course->scholarshipProgram->name }}</a>
                                @else — @endif
                            </td>
                            <td class="px-6 py-4 text-slate-700">{{ $course->instructor?->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-center font-bold text-emerald-600 tabular-nums">{{ $course->active_enrollments_count ?? 0 }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold {{ $course->is_active ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $course->is_active ? 'نشط' : 'متوقف' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.scholarships.courses.show', $course) }}" class="w-9 h-9 inline-flex items-center justify-center bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-lg shadow-sm" title="عرض"><i class="fas fa-eye text-sm"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-16 text-center text-slate-500">لا توجد كورسات منح</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($courses->hasPages())<div class="px-6 py-4 border-t border-slate-200 bg-slate-50">{{ $courses->links() }}</div>@endif
    </section>
</div>
@endsection
