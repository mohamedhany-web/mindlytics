@extends('layouts.admin')

@section('title', $course->title)
@section('header', 'كورس المنحة')

@section('content')
@include('admin.scholarships._styles')

@php
    $scopeLabels = [
        'all' => ['label' => 'الكل', 'class' => 'bg-emerald-100 text-emerald-700 border-emerald-200'],
        'selected' => ['label' => 'طلبة محددون', 'class' => 'bg-sky-100 text-sky-700 border-sky-200'],
        'groups' => ['label' => 'مجموعات', 'class' => 'bg-indigo-100 text-indigo-700 border-indigo-200'],
    ];
    $itemTypeLabel = function ($item) {
        if (! $item?->item) return 'عنصر';
        if ($item->item instanceof \App\Models\Lecture) return 'محاضرة';
        if ($item->item instanceof \App\Models\Assignment) return 'واجب';
        if ($item->item instanceof \App\Models\AdvancedExam || $item->item instanceof \App\Models\Exam) return 'امتحان';
        if ($item->item instanceof \App\Models\LearningPattern) return 'نمط تعليمي';
        return class_basename($item->item_type);
    };
@endphp

<div class="w-full space-y-6">
    @include('admin.scholarships._alerts')

    @include('admin.scholarships._header', [
        'title' => $course->title,
        'subtitle' => 'منحة: ' . ($course->scholarshipProgram?->name ?? '—') . ' | المدرب: ' . ($course->instructor?->name ?? '—'),
        'icon' => 'fas fa-book-open',
        'actions' => trim(
            ($course->scholarshipProgram ? '<a href="' . route('admin.scholarships.programs.show', $course->scholarshipProgram) . '" class="' . $schBtnSecondary . '"><i class="fas fa-award"></i><span>صفحة المنحة</span></a>' : '')
            . ' <a href="' . route('admin.scholarships.groups.index', array_filter(['program_id' => $course->scholarship_program_id])) . '" class="' . $schBtnSecondary . '"><i class="fas fa-layer-group"></i><span>المجموعات</span></a>'
        ),
    ])

    @include('admin.scholarships._stats-grid', ['cards' => [
        ['label' => 'طلاب مفعّلون', 'value' => number_format($course->active_enrollments_count ?? 0), 'icon' => 'fas fa-user-check', 'description' => 'تسجيلات نشطة'],
        ['label' => 'المجموعات', 'value' => number_format($visibilityStats['groups_total'] ?? 0), 'icon' => 'fas fa-layer-group', 'description' => 'مجموعات المنحة'],
        ['label' => 'أقسام مقيّدة', 'value' => number_format($visibilityStats['sections_restricted'] ?? 0) . ' / ' . number_format($visibilityStats['sections_total'] ?? 0), 'icon' => 'fas fa-user-lock', 'description' => 'من إجمالي الأقسام'],
        ['label' => 'عناصر مقيّدة', 'value' => number_format($visibilityStats['items_restricted'] ?? 0) . ' / ' . number_format($visibilityStats['items_total'] ?? 0), 'icon' => 'fas fa-lock', 'description' => 'من عناصر المنهج'],
    ]])

    {{-- رقابة الوصول --}}
    <section class="{{ $schSectionClass }}">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-user-lock text-indigo-600"></i>
                    رقابة وصول المنهج
                </h3>
                <p class="text-xs text-slate-600 mt-1">عرض من يظهر له كل قسم وعنصر (كل الطلبة / طلبة محددون / مجموعات)</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-700">
                        <th class="px-6 py-4 text-right">العنصر</th>
                        <th class="px-6 py-4 text-center">النوع</th>
                        <th class="px-6 py-4 text-center">نطاق الظهور</th>
                        <th class="px-6 py-4 text-right">المستهدفون</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($curriculumSections as $section)
                        @php
                            $sectionScope = $section->visibility_scope ?? 'all';
                            $sectionMeta = $scopeLabels[$sectionScope] ?? $scopeLabels['all'];
                        @endphp
                        <tr class="sch-table-row bg-slate-50/80">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 flex items-center gap-2">
                                    <i class="fas fa-folder text-amber-500"></i>
                                    {{ $section->title }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center text-slate-600">قسم</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold border {{ $sectionMeta['class'] }}">{{ $sectionMeta['label'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                @if($sectionScope === 'selected')
                                    {{ $section->visibleStudents->pluck('name')->join('، ') ?: '—' }}
                                @elseif($sectionScope === 'groups')
                                    {{ $section->visibleGroups->pluck('name')->join('، ') ?: '—' }}
                                @else
                                    كل طلبة المنحة
                                @endif
                            </td>
                        </tr>
                        @foreach($section->items as $item)
                            @php
                                $itemScope = $item->visibility_scope ?? 'all';
                                $itemMeta = $scopeLabels[$itemScope] ?? $scopeLabels['all'];
                            @endphp
                            <tr class="sch-table-row">
                                <td class="px-6 py-3 pr-10">
                                    <div class="text-slate-800 flex items-center gap-2">
                                        <i class="fas fa-chevron-left text-[10px] text-slate-400"></i>
                                        {{ $item->item->title ?? ('#' . $item->id) }}
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-center text-slate-500 text-xs">{{ $itemTypeLabel($item) }}</td>
                                <td class="px-6 py-3 text-center">
                                    <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold border {{ $itemMeta['class'] }}">{{ $itemMeta['label'] }}</span>
                                </td>
                                <td class="px-6 py-3 text-slate-600 text-xs">
                                    @if($itemScope === 'selected')
                                        {{ $item->visibleStudents->pluck('name')->join('، ') ?: '—' }}
                                    @elseif($itemScope === 'groups')
                                        {{ $item->visibleGroups->pluck('name')->join('، ') ?: '—' }}
                                    @else
                                        يتبع القسم / الكل
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr><td colspan="4" class="px-6 py-12 text-center text-slate-500">لا يوجد منهج مبني بعد لهذا الكورس</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- مجموعات المنحة --}}
    <section class="{{ $schSectionClass }}">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="text-lg font-black text-slate-900">مجموعات طلبة هذه المنحة</h3>
            <a href="{{ route('admin.scholarships.groups.index', array_filter(['program_id' => $course->scholarship_program_id])) }}" class="{{ $schBtnSecondary }}">
                <i class="fas fa-external-link-alt"></i>
                <span>إدارة المجموعات</span>
            </a>
        </div>
        <div class="p-6">
            @if(($groups ?? collect())->isEmpty())
                <p class="text-sm text-slate-500 text-center py-6">لا توجد مجموعات لهذه المنحة بعد.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($groups as $group)
                        <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <h4 class="font-bold text-slate-800 truncate">{{ $group->name }}</h4>
                                <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 text-[11px] font-semibold">
                                    <i class="fas fa-users"></i> {{ $group->members_count }}
                                </span>
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                @forelse($group->members->take(8) as $member)
                                    <span class="inline-flex px-2 py-0.5 rounded-lg bg-white border border-slate-200 text-[11px] text-slate-700">{{ $member->name }}</span>
                                @empty
                                    <span class="text-[11px] text-slate-400">لا أعضاء</span>
                                @endforelse
                                @if($group->members->count() > 8)
                                    <span class="text-[11px] text-slate-500">+{{ $group->members->count() - 8 }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="{{ $schSectionClass }}">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900">الطلاب المفعّلون في هذا الكورس</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-700">
                        <th class="px-6 py-4 text-right">الطالب</th>
                        <th class="px-6 py-4 text-right">البريد</th>
                        <th class="px-6 py-4 text-right">الهاتف</th>
                        <th class="px-6 py-4 text-center">تاريخ التفعيل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($registrations as $registration)
                        <tr class="sch-table-row">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="sch-avatar-gradient w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold shadow-md">{{ mb_substr($registration->user?->name ?? '?', 0, 1, 'UTF-8') }}</div>
                                    <span class="font-bold text-slate-900">{{ $registration->user?->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $registration->user?->email }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $registration->user?->phone ?: '—' }}</td>
                            <td class="px-6 py-4 text-center text-slate-700">{{ $registration->activated_at?->format('Y-m-d H:i') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-12 text-center text-slate-500">لا يوجد طلاب مفعّلون بعد</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($registrations->hasPages())<div class="px-6 py-4 border-t border-slate-200 bg-slate-50">{{ $registrations->links() }}</div>@endif
    </section>
</div>
@endsection
