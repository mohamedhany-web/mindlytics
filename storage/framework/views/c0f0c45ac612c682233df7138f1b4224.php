<?php $__env->startSection('title', 'بنك الأسئلة'); ?>
<?php $__env->startSection('header', 'بنك الأسئلة'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-green-100 text-green-800 px-4 py-3 font-medium"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl bg-red-100 text-red-800 px-4 py-3 font-medium"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <!-- الهيدر -->
    <div class="bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <nav class="text-sm text-white/80 mb-2">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <span class="text-white">بنك الأسئلة</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold mt-1">بنك الأسئلة</h1>
                <p class="text-sm text-white/90 mt-1">إدارة وتنظيم الأسئلة للامتحانات</p>
            </div>
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                <a href="<?php echo e(route('admin.question-categories.index')); ?>" class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-tags"></i>
                    إدارة التصنيفات
                </a>
                <a href="<?php echo e(route('admin.question-bank.create')); ?>" class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-gray-100 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-plus"></i>
                    إضافة سؤال جديد
                </a>
            </div>
        </div>
    </div>

    <!-- إحصائيات -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-question-circle text-xl text-indigo-600"></i>
                </div>
                <div><p class="text-2xl font-bold text-gray-900"><?php echo e($stats['total_questions']); ?></p><p class="text-sm text-gray-500">إجمالي الأسئلة</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check text-xl text-green-600"></i>
                </div>
                <div><p class="text-2xl font-bold text-gray-900"><?php echo e($stats['active_questions']); ?></p><p class="text-sm text-gray-500">أسئلة نشطة</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-folder text-xl text-purple-600"></i>
                </div>
                <div><p class="text-2xl font-bold text-gray-900"><?php echo e($stats['categories_count']); ?></p><p class="text-sm text-gray-500">تصنيفات</p></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-cyan-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-layer-group text-xl text-cyan-600"></i>
                </div>
                <div><p class="text-2xl font-bold text-gray-900"><?php echo e(count($stats['by_type'])); ?></p><p class="text-sm text-gray-500">أنواع أسئلة</p></div>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">البحث</label>
                <input type="text" name="search" id="search" value="<?php echo e(request('search')); ?>"
                       placeholder="نص السؤال..."
                       class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
            </div>
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">التصنيف</label>
                <select name="category_id" id="category_id" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    <option value="">جميع التصنيفات</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($category->id); ?>" <?php echo e(request('category_id') == $category->id ? 'selected' : ''); ?>>
                            <?php echo e($category->full_path ?? $category->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">نوع السؤال</label>
                <select name="type" id="type" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    <option value="">جميع الأنواع</option>
                    <?php $__currentLoopData = $questionTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e(request('type') == $key ? 'selected' : ''); ?>><?php echo e($type); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label for="difficulty" class="block text-sm font-medium text-gray-700 mb-1">مستوى الصعوبة</label>
                <select name="difficulty" id="difficulty" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    <option value="">جميع المستويات</option>
                    <?php $__currentLoopData = $difficultyLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e(request('difficulty') == $key ? 'selected' : ''); ?>><?php echo e($level); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-search"></i>
                    بحث
                </button>
            </div>
        </form>
    </div>

    <!-- قائمة الأسئلة -->
    <?php if($questions->count() > 0): ?>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex flex-wrap items-center justify-between gap-3">
                <h4 class="text-lg font-bold text-gray-900">الأسئلة (<?php echo e($questions->total()); ?>)</h4>
                <div class="flex items-center gap-2">
                    <a href="<?php echo e(route('admin.question-bank.create')); ?>" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-xl text-sm font-medium transition-colors">
                        <i class="fas fa-plus"></i>
                        إضافة سؤال
                    </a>
                </div>
            </div>

            <div class="divide-y divide-gray-200">
                <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $typeClass = 'bg-gray-100 text-gray-800';
                        if ($question->type == 'multiple_choice') $typeClass = 'bg-blue-100 text-blue-800';
                        elseif ($question->type == 'true_false') $typeClass = 'bg-green-100 text-green-800';
                        elseif ($question->type == 'fill_blank') $typeClass = 'bg-yellow-100 text-yellow-800';
                        elseif ($question->type == 'essay') $typeClass = 'bg-purple-100 text-purple-800';

                        $difficultyClass = 'bg-red-100 text-red-800';
                        if ($question->difficulty_level == 'easy') $difficultyClass = 'bg-green-100 text-green-800';
                        elseif ($question->difficulty_level == 'medium') $difficultyClass = 'bg-yellow-100 text-yellow-800';
                    ?>
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold <?php echo e($typeClass); ?>">
                                        <?php echo e($question->type_text); ?>

                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold <?php echo e($difficultyClass); ?>">
                                        <?php echo e($question->difficulty_text); ?>

                                    </span>
                                    <span class="text-sm text-gray-500"><?php echo e($question->points); ?> نقطة</span>
                                    <?php if($question->hasMedia()): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            <i class="fas fa-paperclip ml-1"></i>
                                            وسائط
                                        </span>
                                    <?php endif; ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium <?php echo e($question->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                        <?php echo e($question->is_active ? 'نشط' : 'غير نشط'); ?>

                                    </span>
                                </div>

                                <h4 class="text-base font-semibold text-gray-900 mb-2">
                                    <?php echo e(Str::limit(strip_tags($question->question), 120)); ?>

                                </h4>

                                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                    <?php if($question->category): ?>
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-tag"></i>
                                            <?php echo e($question->category->full_path ?? $question->category->name ?? '—'); ?>

                                        </span>
                                    <?php endif; ?>
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-clock"></i>
                                        <?php echo e($question->created_at->diffForHumans()); ?>

                                    </span>
                                    <?php if($question->tags && is_array($question->tags)): ?>
                                        <div class="flex items-center gap-1 flex-wrap">
                                            <?php $__currentLoopData = array_slice($question->tags, 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="bg-gray-100 px-2 py-0.5 rounded text-xs"><?php echo e(is_string($tag) ? $tag : ''); ?></span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0">
                                <a href="<?php echo e(route('admin.question-bank.show', $question)); ?>" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors" title="عرض"><i class="fas fa-eye"></i></a>
                                <a href="<?php echo e(route('admin.question-bank.edit', $question)); ?>" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-100 transition-colors" title="تعديل"><i class="fas fa-edit"></i></a>
                                <form action="<?php echo e(route('admin.question-bank.duplicate', $question)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-green-50 text-green-600 hover:bg-green-100 transition-colors" title="نسخ"><i class="fas fa-copy"></i></button>
                                </form>
                                <form action="<?php echo e(route('admin.question-bank.destroy', $question)); ?>" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا السؤال؟');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition-colors" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                <?php echo e($questions->links()); ?>

            </div>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-12 text-center">
            <div class="w-20 h-20 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-4xl mx-auto mb-4">
                <i class="fas fa-question-circle"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد أسئلة</h3>
            <p class="text-gray-500 mb-6">ابدأ ببناء بنك الأسئلة أو أنشئ تصنيفات أولاً</p>
            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="<?php echo e(route('admin.question-categories.index')); ?>" class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-tags"></i>
                    التصنيفات
                </a>
                <a href="<?php echo e(route('admin.question-bank.create')); ?>" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-plus"></i>
                    إضافة أول سؤال
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- توزيع الأسئلة حسب النوع -->
    <?php if(!empty($stats['by_type'])): ?>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h4 class="text-lg font-bold text-gray-900">توزيع الأسئلة حسب النوع</h4>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    <?php $__currentLoopData = $stats['by_type']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-100">
                            <div class="text-2xl font-bold text-gray-900"><?php echo e($count); ?></div>
                            <div class="text-sm text-gray-500 mt-0.5"><?php echo e($questionTypes[$type] ?? $type); ?></div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/admin/question-bank/index.blade.php ENDPATH**/ ?>