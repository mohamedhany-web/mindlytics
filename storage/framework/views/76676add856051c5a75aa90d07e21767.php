<?php $__env->startSection('title', 'استيراد عملاء'); ?>
<?php $__env->startSection('header', 'المبيعات — استيراد دفعة عملاء'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500';
    $selectClass = $inputClass;
    $statCards = [
        ['label' => 'موظفو مبيعات', 'value' => number_format($stats['reps'] ?? 0), 'icon' => 'fas fa-user-tie', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => 'نشطون للإسناد'],
        ['label' => 'تصنيفات', 'value' => number_format($stats['categories'] ?? 0), 'icon' => 'fas fa-tags', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'description' => 'لتنظيم الدفعة'],
        ['label' => 'دفعات سابقة', 'value' => number_format($stats['import_batches'] ?? 0), 'icon' => 'fas fa-layer-group', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'description' => 'استيرادات منجزة'],
        ['label' => 'عملاء مستوردون', 'value' => number_format($stats['imported_leads'] ?? 0), 'icon' => 'fas fa-file-import', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'description' => 'Leads من Excel'],
    ];
    $columns = [
        ['name' => 'الاسم', 'required' => true, 'aliases' => 'name, اسم, العميل'],
        ['name' => 'الهاتف', 'required' => false, 'aliases' => 'phone, تليفون, موبايل'],
        ['name' => 'البريد', 'required' => false, 'aliases' => 'email, ايميل'],
        ['name' => 'الشركة', 'required' => false, 'aliases' => 'company'],
        ['name' => 'الاهتمام', 'required' => false, 'aliases' => 'interest, منتج'],
        ['name' => 'القيمة', 'required' => false, 'aliases' => 'expected_value, value'],
        ['name' => 'ملاحظات', 'required' => false, 'aliases' => 'notes'],
        ['name' => 'الأولوية', 'required' => false, 'aliases' => 'priority — عادي، مرتفع، عاجل'],
    ];
    $oldRepIds = collect(old('assigned_to_ids', []))->map(fn ($id) => (int) $id)->all();
    $groupOptions = $groups->map(function ($g) {
        $memberIds = $g->memberIds()->map(fn ($id) => (int) $id)->values()->all();
        $memberNames = $g->members->isNotEmpty()
            ? $g->members->pluck('name')->implode('، ')
            : ($g->assignee?->name ?? '—');

        return [
            'id' => $g->id,
            'assigned_to' => $g->assigned_to,
            'member_ids' => $memberIds,
            'label' => $g->name.' — '.$memberNames,
            'admin' => (bool) $g->is_admin_managed,
        ];
    })->values();
?>

<div class="space-y-6" x-data="salesLeadImportPage">
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
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-file-upload"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">استيراد دفعة عملاء</h2>
                    <p class="text-xs text-slate-600">رفع Excel/CSV، تصنيف الدفعة، توزيع Round-Robin على موظفي المبيعات، وإشعار فوري.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('admin.sales.leads.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-user-tag text-emerald-600"></i>
                    العملاء المحتملون
                </a>
                <a href="<?php echo e(route('admin.sales.categories.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-tags text-teal-600"></i>
                    التصنيفات
                </a>
                <a href="<?php echo e(route('admin.sales.groups.index')); ?>" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-layer-group text-indigo-600"></i>
                    مجموعات العملاء
                </a>
                <a href="<?php echo e(route('admin.sales.leads.import.template')); ?>" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl bg-emerald-600 hover:bg-emerald-700">
                    <i class="fas fa-download"></i>
                    تحميل القالب
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

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
        
        <div class="xl:col-span-7 space-y-6">
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-cloud-upload-alt text-violet-600"></i>
                        رفع الملف وإعدادات الدفعة
                    </h3>
                    <p class="text-xs text-slate-600 mt-0.5">عمود <strong>الاسم</strong> إلزامي — باقي الأعمدة اختيارية.</p>
                </div>
                <div class="p-4 sm:p-5">
                    <form method="post" action="<?php echo e(route('admin.sales.leads.import.store')); ?>" enctype="multipart/form-data" class="space-y-5">
                        <?php echo csrf_field(); ?>

                        
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-2">ملف Excel / CSV *</label>
                            <label
                                class="relative flex flex-col items-center justify-center w-full min-h-[140px] rounded-2xl border-2 border-dashed cursor-pointer transition-colors"
                                :class="dragOver ? 'border-violet-500 bg-violet-50' : 'border-slate-300 bg-slate-50 hover:border-violet-400 hover:bg-violet-50/50'"
                                @dragover.prevent="dragOver = true"
                                @dragleave.prevent="dragOver = false"
                                @drop.prevent="dragOver = false; $refs.fileInput.files = $event.dataTransfer.files; fileName = $event.dataTransfer.files[0]?.name || ''"
                            >
                                <input type="file" name="file" x-ref="fileInput" accept=".xlsx,.xls,.csv" required class="sr-only"
                                       @change="fileName = $event.target.files[0]?.name || ''">
                                <div class="text-center px-4 py-6 pointer-events-none">
                                    <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-violet-100 text-violet-600 flex items-center justify-center text-2xl">
                                        <i class="fas fa-file-excel"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-800" x-show="!fileName">اسحب الملف هنا أو انقر للاختيار</p>
                                    <p class="text-sm font-bold text-violet-700" x-show="fileName" x-text="fileName"></p>
                                    <p class="text-xs text-slate-500 mt-1">.xlsx · .xls · .csv — حد أقصى 10 MB</p>
                                </div>
                            </label>
                            <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1.5"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">التصنيف *</label>
                                <select name="category_id" required class="<?php echo e($selectClass); ?>">
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($cat->id); ?>" <?php if(old('category_id') == $cat->id): echo 'selected'; endif; ?>><?php echo e($cat->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <?php if($categories->isEmpty()): ?>
                                    <p class="text-xs text-amber-700 mt-1">
                                        <a href="<?php echo e(route('admin.sales.categories.index')); ?>" class="underline">أضف تصنيفاً</a> أولاً.
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">المصدر</label>
                                <select name="source" class="<?php echo e($selectClass); ?>">
                                    <?php $__currentLoopData = \App\Models\SalesLead::SOURCES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($k); ?>" <?php if(old('source', 'other') === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">أولوية افتراضية</label>
                                <select name="default_priority" class="<?php echo e($selectClass); ?>">
                                    <?php $__currentLoopData = \App\Models\SalesLead::PRIORITIES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($k); ?>" <?php if(old('default_priority', 'normal') === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <label class="block text-xs font-semibold text-slate-700">مجموعة العملاء <span class="font-normal text-slate-500">(اختياري)</span></label>
                                    <a href="<?php echo e(route('admin.sales.groups.create')); ?>" target="_blank" class="text-xs font-semibold text-indigo-600 hover:underline">+ مجموعة جديدة</a>
                                </div>
                                <select name="group_id" class="<?php echo e($selectClass); ?>"
                                        x-model="selectedGroupId"
                                        @change="applyGroupAssignee()">
                                    <option value="">— بدون مجموعة —</option>
                                    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $groupMemberLabel = $group->members->isNotEmpty()
                                                ? $group->members->pluck('name')->implode('، ')
                                                : ($group->assignee?->name ?? '—');
                                        ?>
                                        <option value="<?php echo e($group->id); ?>" <?php if(old('group_id') == $group->id): echo 'selected'; endif; ?>>
                                            <?php echo e($group->name); ?> — <?php echo e($groupMemberLabel); ?>

                                            <?php if($group->is_admin_managed): ?> (إدارة) <?php endif; ?>
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['group_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <p class="text-xs text-slate-500 mt-1.5 flex items-start gap-1.5" x-show="selectedGroupId">
                                    <i class="fas fa-info-circle text-indigo-500 mt-0.5"></i>
                                    <span>عند اختيار مجموعة، يُحدَّد موظفوها تلقائياً ويُوزَّع العملاء عليهم بالتناوب داخل نفس المجموعة.</span>
                                </p>
                                <?php if($groups->isEmpty()): ?>
                                    <p class="text-xs text-amber-700 mt-1">
                                        <a href="<?php echo e(route('admin.sales.groups.create')); ?>" class="underline">أنشئ مجموعة</a> وخصّصها لموظف مبيعات، ثم ارجع للاستيراد.
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>

                        
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <label class="text-xs font-semibold text-slate-700">موظفو المبيعات * <span class="font-normal text-slate-500">(Round-Robin)</span></label>
                                <?php if($salesReps->count() > 1): ?>
                                    <button type="button" onclick="document.querySelectorAll('.rep-checkbox').forEach(c => c.checked = true)" class="text-xs font-semibold text-violet-600 hover:underline">تحديد الكل</button>
                                <?php endif; ?>
                            </div>
                            <?php if($salesReps->isEmpty()): ?>
                                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                                    لا يوجد موظفو مبيعات نشطون. أضف موظفاً بدور مبيعات أولاً.
                                </div>
                            <?php else: ?>
                                <div class="grid sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto rounded-xl border border-slate-200 p-3 bg-slate-50/50">
                                    <?php $__currentLoopData = $salesReps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="flex items-center gap-3 px-3 py-2.5 rounded-xl border border-slate-200 bg-white hover:border-violet-300 hover:bg-violet-50/30 cursor-pointer transition-colors has-[:checked]:border-violet-400 has-[:checked]:bg-violet-50 has-[:checked]:ring-1 has-[:checked]:ring-violet-200">
                                            <input type="checkbox" name="assigned_to_ids[]" value="<?php echo e($rep->id); ?>" class="rep-checkbox rounded border-slate-300 text-violet-600 focus:ring-violet-500"
                                                   <?php if(in_array($rep->id, $oldRepIds, true) || ($salesReps->count() === 1 && empty($oldRepIds))): echo 'checked'; endif; ?>>
                                            <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                                <?php echo e(mb_substr($rep->name, 0, 1)); ?>

                                            </span>
                                            <span class="text-sm font-semibold text-slate-800 truncate"><?php echo e($rep->name); ?></span>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                            <?php $__errorArgs = ['assigned_to_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1.5"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <p class="text-xs text-slate-500 mt-2 flex items-start gap-1.5">
                                <i class="fas fa-info-circle text-violet-500 mt-0.5"></i>
                                <span>كل عميل يُسند للموظف التالي بالتناوب. موظف واحد = كل الدفعة له. عدة موظفين = توزيع متساوٍ.</span>
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 pt-2 border-t border-slate-100">
                            <button type="submit" <?php if($salesReps->isEmpty() || $categories->isEmpty()): echo 'disabled'; endif; ?>
                                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-violet-600 hover:bg-violet-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-xl text-sm font-semibold shadow-sm">
                                <i class="fas fa-upload"></i>
                                استيراد وإرسال إشعار
                            </button>
                            <a href="<?php echo e(route('admin.sales.leads.import.template')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-emerald-700 rounded-xl border border-emerald-200 bg-emerald-50 hover:bg-emerald-100">
                                <i class="fas fa-download"></i>
                                قالب Excel
                            </a>
                        </div>
                    </form>
                </div>
            </section>
        </div>

        
        <aside class="xl:col-span-5 space-y-6">
            
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-list-ol text-emerald-600"></i>
                        خطوات الاستيراد
                    </h3>
                </div>
                <ol class="p-4 space-y-3">
                    <li class="flex gap-3">
                        <span class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0 text-sm"><i class="fas fa-download"></i></span>
                        <div><p class="text-sm font-bold text-slate-900">حمّل القالب</p><p class="text-xs text-slate-600">ملف Excel جاهز بالأعمدة العربية الصحيحة.</p></div>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-9 h-9 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0 text-sm"><i class="fas fa-table"></i></span>
                        <div><p class="text-sm font-bold text-slate-900">املأ البيانات</p><p class="text-xs text-slate-600">الاسم إلزامي — الهاتف والبريد يساعدان في تجنب التكرار.</p></div>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-9 h-9 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center flex-shrink-0 text-sm"><i class="fas fa-tags"></i></span>
                        <div><p class="text-sm font-bold text-slate-900">اختر التصنيف</p><p class="text-xs text-slate-600">يُطبَّق على كل صف في الدفعة.</p></div>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0 text-sm"><i class="fas fa-layer-group"></i></span>
                        <div><p class="text-sm font-bold text-slate-900">مجموعة اختيارية</p><p class="text-xs text-slate-600">أنشئ مجموعة من الإدارة وخصّصها لموظف — العملاء المستوردون ينضمون إليها تلقائياً.</p></div>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-9 h-9 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center flex-shrink-0 text-sm"><i class="fas fa-random"></i></span>
                        <div><p class="text-sm font-bold text-slate-900">وزّع على الموظفين</p><p class="text-xs text-slate-600">Round-Robin: عميل 1 → أ، عميل 2 → ب، عميل 3 → أ...</p></div>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0 text-sm"><i class="fas fa-bell"></i></span>
                        <div><p class="text-sm font-bold text-slate-900">إشعار تلقائي</p><p class="text-xs text-slate-600">كل موظف يستلم إشعاراً بعدد العملاء المسندين له.</p></div>
                    </li>
                </ol>
            </section>

            
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-columns text-sky-600"></i>
                        أعمدة الملف
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                                <th class="px-4 py-2 text-right text-xs font-semibold">العمود</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold w-16">إلزامي</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold">أسماء بديلة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-slate-50/80">
                                    <td class="px-4 py-2.5 font-semibold text-slate-800"><?php echo e($col['name']); ?></td>
                                    <td class="px-4 py-2.5 text-center">
                                        <?php if($col['required']): ?>
                                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700">نعم</span>
                                        <?php else: ?>
                                            <span class="text-slate-400 text-xs">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-2.5 text-xs text-slate-500"><?php echo e($col['aliases']); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </section>

            
            <?php if($recentBatches->isNotEmpty()): ?>
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-history text-violet-600"></i>
                        آخر الدفعات
                    </h3>
                </div>
                <ul class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $recentBatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="px-4 py-3 flex items-center justify-between gap-3 hover:bg-slate-50/80">
                            <div class="min-w-0">
                                <p class="text-xs font-mono font-semibold text-violet-700 truncate"><?php echo e($batch->import_batch); ?></p>
                                <p class="text-[11px] text-slate-500"><?php echo e(\Carbon\Carbon::parse($batch->imported_at)->diffForHumans()); ?></p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="text-xs font-bold text-slate-700 tabular-nums"><?php echo e($batch->leads_count); ?> عميل</span>
                                <a href="<?php echo e(route('admin.sales.leads.index', ['import_batch' => $batch->import_batch])); ?>"
                                   class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100">
                                    عرض
                                </a>
                            </div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </section>
            <?php endif; ?>
        </aside>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('alpine:init', function () {
    Alpine.data('salesLeadImportPage', function () {
        return {
            fileName: '',
            dragOver: false,
            selectedGroupId: <?php echo json_encode((string) old('group_id', ''), 512) ?>,
            groups: <?php echo json_encode($groupOptions, 15, 512) ?>,
            init() {
                if (this.selectedGroupId) {
                    this.applyGroupAssignee();
                }
            },
            applyGroupAssignee() {
                if (!this.selectedGroupId) {
                    return;
                }
                const group = this.groups.find(function (g) {
                    return String(g.id) === String(this.selectedGroupId);
                }.bind(this));
                if (!group) {
                    return;
                }
                const memberIds = (group.member_ids && group.member_ids.length)
                    ? group.member_ids.map(String)
                    : [String(group.assigned_to)];
                document.querySelectorAll('.rep-checkbox').forEach(function (checkbox) {
                    checkbox.checked = memberIds.includes(String(checkbox.value));
                });
            },
        };
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\sales\leads\import.blade.php ENDPATH**/ ?>