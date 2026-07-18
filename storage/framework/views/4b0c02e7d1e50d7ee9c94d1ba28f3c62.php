

<?php $__env->startSection('title', 'مجموعات ووصول المنح - Mindlytics'); ?>
<?php $__env->startSection('header', 'قسم المنح'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.scholarships._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php
    $o = $overview ?? [];
?>

<div class="w-full space-y-6">
    <?php echo $__env->make('admin.scholarships._alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.scholarships._header', [
        'title' => 'المجموعات والوصول',
        'subtitle' => 'رقابة مجموعات طلبة المنح وإدارة الأعضاء — إعدادات الوصول تظهر داخل كورس المنحة',
        'icon' => 'fas fa-layer-group',
        'actions' => '<button type="button" onclick="openScholarshipGroupModal()" class="' . $schBtnPrimary . '"><i class="fas fa-plus"></i><span>إنشاء مجموعة</span></button>',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.scholarships._stats-grid', ['cards' => [
        ['label' => 'المجموعات', 'value' => number_format($o['groups_total'] ?? 0), 'icon' => 'fas fa-layer-group', 'description' => 'كل مجموعات المنح'],
        ['label' => 'أقسام مقيّدة', 'value' => number_format($o['restricted_sections'] ?? 0), 'icon' => 'fas fa-user-lock', 'description' => 'وصول محدود للأقسام'],
        ['label' => 'عناصر مقيّدة', 'value' => number_format($o['restricted_items'] ?? 0), 'icon' => 'fas fa-lock', 'description' => 'محاضرات/واجبات محدودة'],
        ['label' => 'طلاب مفعّلون', 'value' => number_format($o['activated'] ?? 0), 'icon' => 'fas fa-user-check', 'description' => 'يمكن ضمهم للمجموعات'],
    ]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md"><i class="fas fa-filter text-lg"></i></div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">البحث والفلترة</h3>
                    <p class="text-xs text-slate-600 font-medium mt-1">ابحث في المجموعات وفلتر حسب المنحة</p>
                </div>
            </div>
        </div>
        <div class="px-6 py-5">
            <form method="GET" action="<?php echo e(route('admin.scholarships.groups.index')); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="sm:col-span-2">
                    <label class="<?php echo e($schLabelClass); ?>"><i class="fas fa-search text-blue-600 text-sm"></i> البحث</label>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم المجموعة أو المنحة" class="<?php echo e($schInputClass); ?>">
                </div>
                <div>
                    <label class="<?php echo e($schLabelClass); ?>"><i class="fas fa-award text-blue-600 text-sm"></i> المنحة</label>
                    <select name="program_id" class="<?php echo e($schSelectClass); ?>">
                        <option value="">كل المنح</option>
                        <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($program->id); ?>" <?php if((string) request('program_id') === (string) $program->id): echo 'selected'; endif; ?>><?php echo e($program->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 <?php echo e($schBtnPrimary); ?>"><i class="fas fa-search"></i><span>بحث</span></button>
                    <?php if(request()->anyFilled(['search', 'program_id'])): ?>
                        <a href="<?php echo e(route('admin.scholarships.groups.index')); ?>" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors" title="مسح الفلتر"><i class="fas fa-times"></i></a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </section>

    <section class="<?php echo e($schSectionClass); ?>">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="text-lg font-black text-slate-900">مجموعات الطلبة</h3>
            <a href="<?php echo e(route('admin.scholarships.courses.index')); ?>" class="<?php echo e($schBtnSecondary); ?>">
                <i class="fas fa-user-lock"></i>
                <span>رقابة وصول الكورسات</span>
            </a>
        </div>
        <div class="p-6">
            <?php if($groups->isEmpty()): ?>
                <div class="text-center py-12 text-slate-500">
                    <i class="fas fa-layer-group text-3xl text-slate-300 mb-3"></i>
                    <p class="font-medium">لا توجد مجموعات بعد — أنشئ مجموعة وقسّم الطلبة المفعّلين.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div class="min-w-0">
                                    <h3 class="font-bold text-slate-800 truncate"><?php echo e($group->name); ?></h3>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        <?php echo e($group->program?->name); ?>

                                        <?php if($group->program?->instructor): ?>
                                            — <?php echo e($group->program->instructor->name); ?>

                                        <?php endif; ?>
                                    </p>
                                </div>
                                <span class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 text-[11px] font-semibold">
                                    <i class="fas fa-users"></i> <?php echo e($group->members_count); ?>

                                </span>
                            </div>
                            <?php if($group->description): ?>
                                <p class="text-xs text-slate-600 mb-3 line-clamp-2"><?php echo e($group->description); ?></p>
                            <?php endif; ?>
                            <div class="flex flex-wrap gap-1.5 mb-3 max-h-16 overflow-hidden">
                                <?php $__empty_1 = true; $__currentLoopData = $group->members->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <span class="inline-flex px-2 py-0.5 rounded-lg bg-white border border-slate-200 text-[11px] text-slate-700"><?php echo e($member->name); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <span class="text-[11px] text-slate-400">لا أعضاء بعد</span>
                                <?php endif; ?>
                                <?php if($group->members->count() > 6): ?>
                                    <span class="text-[11px] text-slate-500">+<?php echo e($group->members->count() - 6); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="flex gap-2">
                                <button type="button"
                                        onclick="editScholarshipGroup(<?php echo e(\Illuminate\Support\Js::from([
                                            'id' => $group->id,
                                            'name' => $group->name,
                                            'description' => $group->description,
                                            'program_id' => $group->scholarship_program_id,
                                            'member_ids' => $group->members->pluck('id')->values()->all(),
                                        ])); ?>)"
                                        class="flex-1 px-3 py-1.5 rounded-lg bg-sky-100 hover:bg-sky-200 text-sky-700 text-xs font-semibold transition-colors">
                                    <i class="fas fa-edit ml-1"></i> تعديل
                                </button>
                                <?php if($group->program?->course): ?>
                                    <a href="<?php echo e(route('admin.scholarships.courses.show', $group->program->course)); ?>"
                                       class="px-3 py-1.5 rounded-lg bg-indigo-100 hover:bg-indigo-200 text-indigo-700 text-xs font-semibold transition-colors"
                                       title="رقابة الوصول">
                                        <i class="fas fa-user-lock"></i>
                                    </a>
                                <?php endif; ?>
                                <form method="POST" action="<?php echo e(route('admin.scholarships.groups.destroy', $group)); ?>"
                                      onsubmit="return confirm('حذف هذه المجموعة؟')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-100 hover:bg-rose-200 text-rose-700 text-xs font-semibold transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php if($groups->hasPages()): ?>
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex justify-center"><?php echo e($groups->links()); ?></div>
        <?php endif; ?>
    </section>
</div>


<div id="scholarshipGroupModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-6 max-w-lg w-full shadow-xl border border-slate-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-800" id="scholarshipGroupModalTitle">إنشاء مجموعة</h3>
            <button type="button" onclick="closeScholarshipGroupModal()" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100"><i class="fas fa-times"></i></button>
        </div>
        <form id="scholarshipGroupForm" method="POST" action="<?php echo e(route('admin.scholarships.groups.store')); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_method" id="scholarshipGroupMethod" value="POST">
            <div class="mb-4">
                <label class="<?php echo e($schLabelClass); ?>">المنحة <span class="text-red-500">*</span></label>
                <select name="scholarship_program_id" id="scholarshipGroupProgram" required onchange="renderGroupMemberCheckboxes()"
                        class="<?php echo e($schSelectClass); ?>">
                    <option value="">اختر المنحة</option>
                    <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($program->id); ?>"><?php echo e($program->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="mb-4">
                <label class="<?php echo e($schLabelClass); ?>">اسم المجموعة <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="scholarshipGroupName" required maxlength="255"
                       class="<?php echo e($schInputClass); ?>" placeholder="مثال: مجموعة أ">
            </div>
            <div class="mb-4">
                <label class="<?php echo e($schLabelClass); ?>">الوصف (اختياري)</label>
                <textarea name="description" id="scholarshipGroupDescription" rows="2"
                          class="<?php echo e($schInputClass); ?>" placeholder="وصف مختصر..."></textarea>
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
                <button type="submit" class="flex-1 <?php echo e($schBtnPrimary); ?>">حفظ</button>
                <button type="button" onclick="closeScholarshipGroupModal()" class="<?php echo e($schBtnSecondary); ?>">إلغاء</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const activatedByProgram = <?php echo json_encode(($activatedByProgram ?? collect())->map(fn ($users) => $users->map(fn ($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email ?? ''])->values())->toArray()) ?>;
let editingGroupMemberIds = [];

function openScholarshipGroupModal() {
    document.getElementById('scholarshipGroupModalTitle').textContent = 'إنشاء مجموعة';
    document.getElementById('scholarshipGroupForm').action = <?php echo json_encode(route('admin.scholarships.groups.store'), 15, 512) ?>;
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
    document.getElementById('scholarshipGroupForm').action = `/admin/scholarships/groups/${data.id}`;
    document.getElementById('scholarshipGroupMethod').value = 'PUT';
    document.getElementById('scholarshipGroupProgram').value = String(data.program_id);
    document.getElementById('scholarshipGroupProgram').disabled = true;
    document.getElementById('scholarshipGroupName').value = data.name || '';
    document.getElementById('scholarshipGroupDescription').value = data.description || '';
    editingGroupMemberIds = (data.member_ids || []).map(String);
    renderGroupMemberCheckboxes();
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\scholarships\groups\index.blade.php ENDPATH**/ ?>