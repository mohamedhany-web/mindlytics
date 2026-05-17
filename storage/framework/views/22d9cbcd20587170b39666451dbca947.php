<?php $__env->startSection('title', 'تفاصيل المجموعة'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-green-100 text-green-800 px-4 py-3 font-medium"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl bg-red-100 text-red-800 px-4 py-3 font-medium"><?php echo e(session('error')); ?></div>
    <?php endif; ?>
    <!-- هيدر الصفحة -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.groups.index')); ?>" class="hover:text-white">المجموعات</a>
                    <span class="mx-2">/</span>
                    <span class="text-white truncate"><?php echo e(Str::limit($group->name, 35)); ?></span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1 truncate"><?php echo e($group->name); ?></h1>
                <p class="text-sm text-white/90 mt-1"><?php echo e($group->course->title ?? ''); ?> · مدرب: <?php echo e($group->course->instructor->name ?? '—'); ?></p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="<?php echo e(route('admin.groups.edit', $group)); ?>" class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <form action="<?php echo e(route('admin.groups.destroy', $group)); ?>" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه المجموعة؟ سيتم حذف ربط الأعضاء أيضاً.');">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="inline-flex items-center gap-2 bg-red-500/90 hover:bg-red-600 text-white px-4 py-2.5 rounded-xl font-semibold transition-colors border border-white/30">
                        <i class="fas fa-trash"></i> حذف
                    </button>
                </form>
                <a href="<?php echo e(route('admin.groups.index')); ?>" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2.5 rounded-xl font-medium transition-colors border border-white/30">
                    <i class="fas fa-arrow-right"></i> رجوع
                </a>
            </div>
        </div>
    </div>

    <!-- معلومات المجموعة -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                <p class="text-xs font-medium text-gray-500 mb-1">قائد المجموعة</p>
                <p class="text-sm font-semibold text-gray-900"><?php echo e($group->leader->name ?? 'غير محدد'); ?></p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                <p class="text-xs font-medium text-gray-500 mb-1">عدد الأعضاء</p>
                <p class="text-sm font-semibold text-gray-900"><?php echo e($group->members->count()); ?> / <?php echo e($group->max_members); ?></p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                <p class="text-xs font-medium text-gray-500 mb-1">الحالة</p>
                <?php
                    $statusClass = $group->status == 'active' ? 'bg-green-100 text-green-800' : ($group->status == 'inactive' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800');
                    $statusText = $group->status == 'active' ? 'نشط' : ($group->status == 'inactive' ? 'غير نشط' : 'مؤرشف');
                ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?php echo e($statusClass); ?>"><?php echo e($statusText); ?></span>
            </div>
        </div>

        <?php if($group->description): ?>
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-2">الوصف</h3>
                <p class="text-gray-700 text-sm"><?php echo e($group->description); ?></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- أعضاء المجموعة -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-lg font-bold text-gray-900">أعضاء المجموعة (<?php echo e($group->members->count()); ?>)</h2>
            <?php if($group->members->count() < $group->max_members): ?>
                <button type="button" onclick="document.getElementById('addMemberModal').classList.remove('hidden'); document.getElementById('addMemberModal').classList.add('flex');" 
                        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-user-plus"></i> إضافة عضو
                </button>
            <?php endif; ?>
        </div>

        <?php if($group->members->count() > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">الاسم</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">البريد</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">الدور</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">تاريخ الانضمام</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $group->members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo e($member->name); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo e($member->email); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($member->pivot->role == 'leader'): ?>
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">قائد</span>
                                    <?php else: ?>
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">عضو</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo e($member->pivot->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('Y-m-d') : '—'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($member->pivot->role != 'leader'): ?>
                                        <form action="<?php echo e(route('admin.groups.remove-member', [$group, $member->id])); ?>" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من إزالة هذا العضو؟');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition-colors" title="إزالة"><i class="fas fa-user-minus"></i></button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-500 py-8">لا يوجد أعضاء في هذه المجموعة. استخدم زر «إضافة عضو» لإضافة طلاب مسجلين في الكورس.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Modal إضافة عضو -->
<div id="addMemberModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">إضافة عضو جديد</h3>
        </div>
        <form action="<?php echo e(route('admin.groups.add-member', $group)); ?>" method="POST" class="p-6">
            <?php echo csrf_field(); ?>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">الطالب (المسجلون في الكورس)</label>
                <select name="user_id" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">اختر الطالب</option>
                    <?php $__currentLoopData = $availableStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($student->id); ?>"><?php echo e($student->name); ?> (<?php echo e($student->email); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php if($availableStudents->isEmpty()): ?>
                    <p class="mt-2 text-sm text-amber-600">لا يوجد طلاب مسجلون في الكورس غير مضافين للمجموعة.</p>
                <?php endif; ?>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addMemberModal').classList.add('hidden'); document.getElementById('addMemberModal').classList.remove('flex');" 
                        class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors">إلغاء</button>
                <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition-colors">إضافة</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/groups/show.blade.php ENDPATH**/ ?>