@extends('layouts.app')

@section('title', 'طلاب المنح - Mindlytics')
@section('header', 'طلاب المنح')

@section('content')
@php
    $statusBadges = [
        'registered' => 'bg-amber-100 text-amber-800',
        'activated' => 'bg-emerald-100 text-emerald-800',
        'rejected' => 'bg-rose-100 text-rose-800',
        'deactivated' => 'bg-slate-100 text-slate-700',
    ];
@endphp

<div class="space-y-6">
    @include('instructor.scholarships._alerts')

    <!-- الهيدر -->
    <div class="relative rounded-2xl border border-slate-200 bg-gradient-to-br from-white via-slate-50/40 to-white shadow-sm overflow-hidden">
        <div class="absolute top-0 right-0 w-28 h-28 rounded-full bg-sky-100/50 -translate-y-1/2 translate-x-1/2 pointer-events-none" aria-hidden="true"></div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 p-5 sm:p-6">
            <div class="flex items-center gap-4 min-w-0 flex-1">
                <div class="w-14 h-14 rounded-2xl bg-white border border-slate-100 shadow-sm flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user-graduate text-sky-600 text-2xl"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-sky-600 uppercase tracking-wider mb-1">المنح الدراسية</p>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800">طلاب المنح</h1>
                    <p class="text-sm text-slate-500 mt-0.5">جميع المسجّلين في منحك — يمكنك تفعيلهم أو رفضهم.</p>
                </div>
            </div>
            <a href="{{ route('instructor.scholarships.students.index', ['status' => 'registered']) }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 text-sm font-semibold shadow-sm transition-colors flex-shrink-0">
                <i class="fas fa-user-clock"></i>
                <span>بانتظار التفعيل ({{ $stats['registered'] }})</span>
            </a>
        </div>
    </div>

    @include('instructor.scholarships._nav', ['active' => 'students'])

    <!-- الإحصائيات -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">الإجمالي</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800">{{ $stats['total'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 flex items-center justify-center">
                <i class="fas fa-users text-sky-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">بانتظار التفعيل</p>
                <p class="text-2xl sm:text-3xl font-bold text-amber-600">{{ $stats['registered'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
                <i class="fas fa-clock text-amber-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">مفعّلون</p>
                <p class="text-2xl sm:text-3xl font-bold text-emerald-600">{{ $stats['activated'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
                <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">مرفوضون</p>
                <p class="text-2xl sm:text-3xl font-bold text-rose-600">{{ $stats['rejected'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center">
                <i class="fas fa-times-circle text-rose-600 text-lg"></i>
            </div>
        </div>
    </div>

    @include('instructor.scholarships._filters', [
        'programs' => $programs,
        'showProgramFilter' => true,
        'filterAction' => route('instructor.scholarships.students.index'),
    ])

    <!-- مجموعات الطلبة -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 sm:px-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="text-base sm:text-lg font-bold text-slate-800 flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-indigo-50 border border-slate-100 flex items-center justify-center">
                    <i class="fas fa-layer-group text-indigo-600 text-xs"></i>
                </span>
                مجموعات الطلبة
            </h2>
            <button type="button" onclick="openScholarshipGroupModal()"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 text-sm font-semibold transition-colors">
                <i class="fas fa-plus"></i>
                إنشاء مجموعة
            </button>
        </div>

        <div class="p-5">
            @if(($groups ?? collect())->isEmpty())
                <div class="text-center py-8 text-slate-500">
                    <i class="fas fa-layer-group text-2xl text-slate-300 mb-2"></i>
                    <p class="text-sm font-medium">لا توجد مجموعات بعد — أنشئ مجموعة وقسّم الطلبة المفعّلين.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($groups as $group)
                        <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div class="min-w-0">
                                    <h3 class="font-bold text-slate-800 truncate">{{ $group->name }}</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $group->program?->name }}</p>
                                </div>
                                <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 text-[11px] font-semibold">
                                    <i class="fas fa-users"></i> {{ $group->members_count }}
                                </span>
                            </div>
                            @if($group->description)
                                <p class="text-xs text-slate-600 mb-3 line-clamp-2">{{ $group->description }}</p>
                            @endif
                            <div class="flex flex-wrap gap-1.5 mb-3 max-h-16 overflow-hidden">
                                @forelse($group->members->take(6) as $member)
                                    <span class="inline-flex px-2 py-0.5 rounded-lg bg-white border border-slate-200 text-[11px] text-slate-700">{{ $member->name }}</span>
                                @empty
                                    <span class="text-[11px] text-slate-400">لا أعضاء بعد</span>
                                @endforelse
                                @if($group->members->count() > 6)
                                    <span class="text-[11px] text-slate-500">+{{ $group->members->count() - 6 }}</span>
                                @endif
                            </div>
                            <div class="flex gap-2">
                                <button type="button"
                                        onclick="editScholarshipGroup({{ \Illuminate\Support\Js::from([
                                            'id' => $group->id,
                                            'name' => $group->name,
                                            'description' => $group->description,
                                            'program_id' => $group->scholarship_program_id,
                                            'member_ids' => $group->members->pluck('id')->values()->all(),
                                        ]) }})"
                                        class="flex-1 px-3 py-1.5 rounded-lg bg-sky-100 hover:bg-sky-200 text-sky-700 text-xs font-semibold transition-colors">
                                    <i class="fas fa-edit ml-1"></i> تعديل
                                </button>
                                <form method="POST" action="{{ route('instructor.scholarships.groups.destroy', $group) }}"
                                      onsubmit="return confirm('حذف هذه المجموعة؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-100 hover:bg-rose-200 text-rose-700 text-xs font-semibold transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- جدول الطلاب -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 sm:px-6 border-b border-slate-200">
            <h2 class="text-base sm:text-lg font-bold text-slate-800 flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-sky-50 border border-slate-100 flex items-center justify-center">
                    <i class="fas fa-list text-sky-600 text-xs"></i>
                </span>
                قائمة الطلاب
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-5 py-3 text-right font-semibold">الطالب</th>
                        <th class="px-5 py-3 text-right font-semibold">المنحة</th>
                        <th class="px-5 py-3 text-center font-semibold">تاريخ التسجيل</th>
                        <th class="px-5 py-3 text-center font-semibold">الحالة</th>
                        <th class="px-5 py-3 text-left font-semibold">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($registrations as $registration)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-slate-800">{{ $registration->user?->name }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $registration->user?->email }}@if($registration->user?->phone) — {{ $registration->user->phone }}@endif</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <a href="{{ route('instructor.scholarships.show', $registration->program) }}" class="text-sky-600 hover:text-sky-700 font-medium transition-colors">
                                    {{ $registration->program?->name }}
                                </a>
                            </td>
                            <td class="px-5 py-3.5 text-center text-slate-600">{{ $registration->registered_at?->format('Y-m-d H:i') }}</td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold {{ $statusBadges[$registration->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $registration->status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">@include('instructor.scholarships._registration-actions', ['registration' => $registration])</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-user-graduate text-2xl text-slate-400"></i>
                                </div>
                                <p class="text-slate-500 font-medium">لا يوجد طلاب مسجّلون في منحك.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($registrations->hasPages())
            <div class="px-5 py-4 border-t border-slate-200 flex justify-center">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal مجموعة --}}
<div id="scholarshipGroupModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-6 max-w-lg w-full shadow-xl border border-slate-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-800" id="scholarshipGroupModalTitle">إنشاء مجموعة</h3>
            <button type="button" onclick="closeScholarshipGroupModal()" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100"><i class="fas fa-times"></i></button>
        </div>
        <form id="scholarshipGroupForm" method="POST" action="{{ route('instructor.scholarships.groups.store') }}">
            @csrf
            <input type="hidden" name="_method" id="scholarshipGroupMethod" value="POST">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-slate-700 mb-1">المنحة <span class="text-red-500">*</span></label>
                <select name="scholarship_program_id" id="scholarshipGroupProgram" required onchange="renderGroupMemberCheckboxes()"
                        class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-slate-800 text-sm">
                    <option value="">اختر المنحة</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}">{{ $program->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-slate-700 mb-1">اسم المجموعة <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="scholarshipGroupName" required maxlength="255"
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-slate-800 text-sm" placeholder="مثال: مجموعة أ">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-slate-700 mb-1">الوصف (اختياري)</label>
                <textarea name="description" id="scholarshipGroupDescription" rows="2"
                          class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-slate-800 text-sm" placeholder="وصف مختصر..."></textarea>
            </div>
            <div class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-semibold text-slate-700">أعضاء المجموعة (الطلبة المفعّلون)</label>
                    <button type="button" onclick="toggleAllGroupMembers()" class="text-xs font-semibold text-sky-600 hover:text-sky-700">تحديد الكل / إلغاء</button>
                </div>
                <div id="scholarshipGroupMembersWrap" class="max-h-48 overflow-y-auto rounded-xl border border-slate-200 p-3 space-y-2 bg-slate-50">
                    <p class="text-xs text-slate-500">اختر المنحة أولًا لعرض الطلبة المفعّلين.</p>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white rounded-xl font-semibold">حفظ</button>
                <button type="button" onclick="closeScholarshipGroupModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold">إلغاء</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const activatedByProgram = @json(($activatedByProgram ?? collect())->map(fn ($users) => $users->map(fn ($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email ?? ''])->values())->toArray());
let editingGroupMemberIds = [];

function openScholarshipGroupModal() {
    document.getElementById('scholarshipGroupModalTitle').textContent = 'إنشاء مجموعة';
    document.getElementById('scholarshipGroupForm').action = @json(route('instructor.scholarships.groups.store'));
    document.getElementById('scholarshipGroupMethod').value = 'POST';
    document.getElementById('scholarshipGroupProgram').value = '';
    document.getElementById('scholarshipGroupProgram').disabled = false;
    document.getElementById('scholarshipGroupName').value = '';
    document.getElementById('scholarshipGroupDescription').value = '';
    editingGroupMemberIds = [];
    renderGroupMemberCheckboxes();
    document.getElementById('scholarshipGroupModal').classList.remove('hidden');
    document.getElementById('scholarshipGroupModal').classList.add('flex');
}

function editScholarshipGroup(data) {
    document.getElementById('scholarshipGroupModalTitle').textContent = 'تعديل المجموعة';
    document.getElementById('scholarshipGroupForm').action = `/instructor/scholarships/groups/${data.id}`;
    document.getElementById('scholarshipGroupMethod').value = 'PUT';
    document.getElementById('scholarshipGroupProgram').value = String(data.program_id);
    document.getElementById('scholarshipGroupProgram').disabled = true;
    document.getElementById('scholarshipGroupName').value = data.name || '';
    document.getElementById('scholarshipGroupDescription').value = data.description || '';
    editingGroupMemberIds = (data.member_ids || []).map(String);
    renderGroupMemberCheckboxes();
    // program is disabled so ensure value is submitted via hidden if needed
    let hiddenProgram = document.getElementById('scholarshipGroupProgramHidden');
    if (!hiddenProgram) {
        hiddenProgram = document.createElement('input');
        hiddenProgram.type = 'hidden';
        hiddenProgram.name = 'scholarship_program_id';
        hiddenProgram.id = 'scholarshipGroupProgramHidden';
        document.getElementById('scholarshipGroupForm').appendChild(hiddenProgram);
    }
    hiddenProgram.value = String(data.program_id);
    document.getElementById('scholarshipGroupModal').classList.remove('hidden');
    document.getElementById('scholarshipGroupModal').classList.add('flex');
}

function closeScholarshipGroupModal() {
    const modal = document.getElementById('scholarshipGroupModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.getElementById('scholarshipGroupProgram').disabled = false;
    const hiddenProgram = document.getElementById('scholarshipGroupProgramHidden');
    if (hiddenProgram) hiddenProgram.remove();
}

function renderGroupMemberCheckboxes() {
    const programId = document.getElementById('scholarshipGroupProgram').value;
    const wrap = document.getElementById('scholarshipGroupMembersWrap');
    const students = activatedByProgram[programId] || activatedByProgram[String(programId)] || [];
    if (!programId) {
        wrap.innerHTML = '<p class="text-xs text-slate-500">اختر المنحة أولًا لعرض الطلبة المفعّلين.</p>';
        return;
    }
    if (!students.length) {
        wrap.innerHTML = '<p class="text-xs text-slate-500">لا يوجد طلبة مفعّلون في هذه المنحة بعد.</p>';
        return;
    }
    const selected = new Set(editingGroupMemberIds.map(String));
    wrap.innerHTML = students.map(s => `
        <label class="flex items-center gap-2 p-2 rounded-lg bg-white border border-slate-200 cursor-pointer hover:border-indigo-300">
            <input type="checkbox" name="member_ids[]" value="${s.id}" class="group-member-cb w-4 h-4 text-indigo-600 rounded border-slate-300" ${selected.has(String(s.id)) ? 'checked' : ''}>
            <span class="text-sm text-slate-800">${s.name || ''}${s.email ? ' <span class="text-xs text-slate-500">— ' + s.email + '</span>' : ''}</span>
        </label>
    `).join('');
}

function toggleAllGroupMembers() {
    const boxes = document.querySelectorAll('.group-member-cb');
    if (!boxes.length) return;
    const allChecked = Array.from(boxes).every(b => b.checked);
    boxes.forEach(b => { b.checked = !allChecked; });
}
</script>
@endpush

