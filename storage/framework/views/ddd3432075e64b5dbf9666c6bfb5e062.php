<?php $__env->startSection('title', 'تصنيفات العملاء'); ?>
<?php $__env->startSection('header', 'المبيعات — تصنيفات العملاء'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500';
    $statCards = [
        ['label' => 'إجمالي التصنيفات', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-tags', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => 'كل التصنيفات'],
        ['label' => 'نشطة', 'value' => number_format($stats['active'] ?? 0), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'description' => 'متاحة للاستخدام'],
        ['label' => 'عملاء مرتبطون', 'value' => number_format($stats['leads_total'] ?? 0), 'icon' => 'fas fa-users', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'description' => 'Leads مصنّفة'],
        ['label' => 'بدون عملاء', 'value' => number_format($stats['empty_categories'] ?? 0), 'icon' => 'fas fa-folder-open', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'description' => 'قابلة للحذف'],
    ];
?>

<div class="space-y-6" x-data="{ editingId: null }">
    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-check-circle ml-1"></i><?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-exclamation-circle ml-1"></i><?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">
            <p class="font-semibold mb-1"><i class="fas fa-exclamation-circle ml-1"></i> يوجد أخطاء:</p>
            <ul class="list-disc list-inside space-y-0.5">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($e); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-tags"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">تصنيفات العملاء</h2>
                    <p class="text-xs text-slate-600">تنظيم Leads والاستيراد حسب نوع العميل — دورات، B2B، فعاليات، وغيرها.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('admin.sales.leads.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-user-tag text-emerald-600"></i>
                    العملاء المحتملون
                </a>
                <a href="<?php echo e(route('admin.sales.leads.import')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-file-upload text-violet-600"></i>
                    استيراد دفعة
                </a>
                <a href="<?php echo e(route('admin.sales.kpi.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-white rounded-xl bg-emerald-600 hover:bg-emerald-700">
                    <i class="fas fa-chart-line"></i>
                    KPIs
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 p-4">
            <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-600 truncate"><?php echo e($card['label']); ?></p>
                            <p class="text-xl font-black text-slate-900 truncate tabular-nums"><?php echo e($card['value']); ?></p>
                        </div>
                        <div class="w-9 h-9 rounded-lg <?php echo e($card['bg']); ?> flex items-center justify-center <?php echo e($card['text']); ?> flex-shrink-0">
                            <i class="<?php echo e($card['icon']); ?> text-sm"></i>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1 truncate"><?php echo e($card['description']); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-plus-circle text-emerald-600"></i>
                إضافة تصنيف جديد
            </h3>
            <p class="text-xs text-slate-600 mt-0.5">يُستخدم عند إنشاء عميل أو استيراد دفعة Excel — إلزامي لتنظيم البيانات.</p>
        </div>
        <div class="p-4">
            <form method="post" action="<?php echo e(route('admin.sales.categories.store')); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
                <?php echo csrf_field(); ?>
                <div class="lg:col-span-3">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">اسم التصنيف *</label>
                    <input type="text" name="name" value="<?php echo e(old('name')); ?>" required placeholder="مثال: دورات أونلاين" class="<?php echo e($inputClass); ?>">
                </div>
                <div class="lg:col-span-3">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">وصف مختصر</label>
                    <input type="text" name="description" value="<?php echo e(old('description')); ?>" placeholder="اختياري" class="<?php echo e($inputClass); ?>">
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">اللون</label>
                    <div class="flex items-center gap-2">
                        <input type="color" name="color" value="<?php echo e(old('color', '#059669')); ?>" class="h-10 w-14 rounded-xl border border-slate-300 cursor-pointer">
                        <span class="text-xs text-slate-500">للشارة في القوائم</span>
                    </div>
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">ترتيب العرض</label>
                    <input type="number" name="sort_order" value="<?php echo e(old('sort_order', 0)); ?>" min="0" max="9999" class="<?php echo e($inputClass); ?>">
                </div>
                <div class="lg:col-span-2">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold shadow-sm">
                        <i class="fas fa-plus"></i>
                        إضافة التصنيف
                    </button>
                </div>
            </form>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-black text-slate-900">التصنيفات الحالية</h3>
                <p class="text-xs text-slate-600">اضغط «تعديل» لتحديث الاسم أو اللون أو تعطيل التصنيف.</p>
            </div>
            <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200"><?php echo e($categories->count()); ?> تصنيف</span>
        </div>

        <?php if($categories->isEmpty()): ?>
            <div class="text-center py-16 px-4">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400">
                    <i class="fas fa-tags text-2xl"></i>
                </div>
                <p class="text-sm font-semibold text-slate-900">لا توجد تصنيفات بعد</p>
                <p class="text-xs text-slate-500 mt-1">أضف أول تصنيف باستخدام النموذج أعلاه.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-[760px] w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-700 border-b border-slate-200">
                            <th class="px-4 py-3 text-right font-semibold w-12">#</th>
                            <th class="px-4 py-3 text-right font-semibold">التصنيف</th>
                            <th class="px-4 py-3 text-center font-semibold">العملاء</th>
                            <th class="px-4 py-3 text-center font-semibold">الترتيب</th>
                            <th class="px-4 py-3 text-center font-semibold">الحالة</th>
                            <th class="px-4 py-3 text-center font-semibold w-44">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3 text-slate-400 tabular-nums text-xs"><?php echo e($loop->iteration); ?></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-start gap-3">
                                        <span class="mt-1 w-4 h-4 rounded-full flex-shrink-0 ring-2 ring-white shadow-sm" style="background: <?php echo e($cat->color); ?>"></span>
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-900"><?php echo e($cat->name); ?></p>
                                            <?php if($cat->description): ?>
                                                <p class="text-xs text-slate-500 mt-0.5 truncate max-w-xs"><?php echo e($cat->description); ?></p>
                                            <?php endif; ?>
                                            <p class="text-[10px] text-slate-400 mt-0.5 font-mono"><?php echo e($cat->slug); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="<?php echo e(route('admin.sales.leads.index', ['category_id' => $cat->id])); ?>"
                                       class="inline-flex items-center justify-center min-w-[2.5rem] px-2.5 py-1 rounded-lg text-sm font-bold tabular-nums transition-colors
                                       <?php echo e($cat->leads_count > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-slate-50 text-slate-400 border border-slate-200'); ?>">
                                        <?php echo e(number_format($cat->leads_count)); ?>

                                    </a>
                                </td>
                                <td class="px-4 py-3 text-center tabular-nums text-slate-600"><?php echo e($cat->sort_order); ?></td>
                                <td class="px-4 py-3 text-center">
                                    <?php if($cat->is_active): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            نشط
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            معطّل
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                        <a href="<?php echo e(route('admin.sales.leads.index', ['category_id' => $cat->id])); ?>"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-sky-700 bg-sky-50 border border-sky-200 hover:bg-sky-100"
                                           title="عرض العملاء">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button"
                                                @click="editingId = editingId === <?php echo e($cat->id); ?> ? null : <?php echo e($cat->id); ?>"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-violet-700 bg-violet-50 border border-violet-200 hover:bg-violet-100"
                                                title="تعديل">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <?php if(!$cat->leads_count): ?>
                                            <form action="<?php echo e(route('admin.sales.categories.destroy', $cat)); ?>" method="post" class="inline" onsubmit="return confirm('حذف التصنيف «<?php echo e($cat->name); ?>»؟');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-rose-700 bg-rose-50 border border-rose-200 hover:bg-rose-100" title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr x-show="editingId === <?php echo e($cat->id); ?>" x-cloak class="bg-violet-50/40 border-b border-violet-100">
                                <td colspan="6" class="px-4 py-4">
                                    <form method="post" action="<?php echo e(route('admin.sales.categories.update', $cat)); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <div class="lg:col-span-3">
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">الاسم</label>
                                            <input type="text" name="name" value="<?php echo e($cat->name); ?>" required class="<?php echo e($inputClass); ?>">
                                        </div>
                                        <div class="lg:col-span-3">
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">الوصف</label>
                                            <input type="text" name="description" value="<?php echo e($cat->description); ?>" class="<?php echo e($inputClass); ?>">
                                        </div>
                                        <div class="lg:col-span-2">
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">اللون</label>
                                            <input type="color" name="color" value="<?php echo e($cat->color); ?>" class="h-10 w-full rounded-xl border border-slate-300 cursor-pointer">
                                        </div>
                                        <div class="lg:col-span-1">
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">ترتيب</label>
                                            <input type="number" name="sort_order" value="<?php echo e($cat->sort_order); ?>" min="0" class="<?php echo e($inputClass); ?>">
                                        </div>
                                        <div class="lg:col-span-1 flex items-end pb-1">
                                            <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                                                <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" <?php if($cat->is_active): echo 'checked'; endif; ?>>
                                                نشط
                                            </label>
                                        </div>
                                        <div class="lg:col-span-2 flex gap-2">
                                            <button type="submit" class="flex-1 px-3 py-2 bg-violet-600 hover:bg-violet-700 text-white rounded-xl text-xs font-semibold">
                                                <i class="fas fa-save ml-1"></i> حفظ
                                            </button>
                                            <button type="button" @click="editingId = null" class="px-3 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-600 hover:bg-white">
                                                إلغاء
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    
    <?php if($categories->isNotEmpty()): ?>
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-palette text-teal-600"></i>
                معاينة الشارات
            </h3>
            <p class="text-xs text-slate-600 mt-0.5">كيف تظهر التصنيفات في قوائم العملاء ولوحة الموظف.</p>
        </div>
        <div class="p-4 flex flex-wrap gap-2">
            <?php $__currentLoopData = $categories->where('is_active', true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-sm font-semibold border shadow-sm"
                      style="color: <?php echo e($cat->color); ?>; border-color: <?php echo e($cat->color); ?>44; background: <?php echo e($cat->color); ?>12">
                    <span class="w-2.5 h-2.5 rounded-full" style="background: <?php echo e($cat->color); ?>"></span>
                    <?php echo e($cat->name); ?>

                    <span class="text-xs opacity-70 tabular-nums">(<?php echo e($cat->leads_count); ?>)</span>
                </span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if($categories->where('is_active', false)->isNotEmpty()): ?>
                <span class="text-xs text-slate-400 self-center mr-2">معطّلة:</span>
                <?php $__currentLoopData = $categories->where('is_active', false); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-sm font-semibold bg-slate-100 text-slate-500 border border-slate-200 line-through opacity-70">
                        <?php echo e($cat->name); ?>

                    </span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

<style>[x-cloak]{display:none!important}</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/sales/categories/index.blade.php ENDPATH**/ ?>