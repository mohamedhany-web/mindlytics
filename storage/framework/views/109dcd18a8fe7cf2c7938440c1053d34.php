<?php $__env->startSection('title', 'تعديل مسار التعلم'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full px-4 py-6 space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <div class="w-full max-w-full space-y-6">
        <div class="bg-gradient-to-br from-sky-500 via-sky-600 to-indigo-700 rounded-3xl p-6 sm:p-8 shadow-xl text-white relative overflow-hidden">
            <div class="absolute inset-y-0 left-0 w-40 bg-white/10 blur-3xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-4">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/15 text-sm font-semibold">
                        <i class="fas fa-route"></i>
                        مسار التعلم
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-bold"><?php echo e($academicYear->name); ?></h1>
                    <div class="flex flex-wrap items-center gap-3 text-sm text-white/80">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15">
                            <i class="fas fa-barcode"></i>
                            <?php echo e($academicYear->code); ?>

                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10">
                            <i class="fas fa-layer-group"></i>
                            <?php echo e($academicYear->academicSubjects->count()); ?> مجموعة مهارية
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10">
                            <i class="fas fa-graduation-cap"></i>
                            <?php echo e($trackSummary['courses_count']); ?> كورس مرتبط
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full <?php echo e($academicYear->is_active ? 'bg-emerald-100/80 text-emerald-900' : 'bg-rose-100/80 text-rose-900'); ?>">
                            <i class="fas fa-circle"></i>
                            <?php echo e($academicYear->is_active ? 'نشط' : 'موقوف'); ?>

                        </span>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <form method="POST" action="<?php echo e(route('admin.academic-years.toggle-status', $academicYear)); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white/15 hover:bg-white/25 transition px-4 py-2 text-sm font-semibold">
                            <i class="fas fa-power-off"></i>
                            <?php echo e($academicYear->is_active ? 'إيقاف مؤقت' : 'تفعيل المسار'); ?>

                        </button>
                    </form>
                    <a href="<?php echo e(route('admin.academic-subjects.index', ['track' => $academicYear->id])); ?>" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white text-sky-700 px-5 py-2 text-sm font-semibold shadow-lg shadow-sky-600/20 hover:bg-slate-100 transition">
                        <i class="fas fa-layer-group"></i>
                        إدارة المجموعات
                    </a>
                    <a href="<?php echo e(route('admin.academic-years.index')); ?>" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/40 px-5 py-2 text-sm font-semibold hover:bg-white/10 transition">
                        <i class="fas fa-arrow-right"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-gray-200/50 hover:border-sky-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);">
            <div class="border-b border-gray-100 px-6 sm:px-8 py-5" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 50%, rgba(2, 132, 199, 0.08) 100%); border-bottom: 2px solid rgba(59, 130, 246, 0.3);">
                <h2 class="text-xl font-black bg-gradient-to-r from-sky-700 via-blue-600 to-sky-600 bg-clip-text text-transparent">
                    <i class="fas fa-edit text-sky-600 ml-2"></i>
                    بيانات المسار
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    حدّث بيانات المسار، اللون، الأيقونة وترتيب العرض. استخدم مربع الاختيار لتفعيل المسار أو إيقافه.
                </p>
            </div>
            <form action="<?php echo e(route('admin.academic-years.update', $academicYear)); ?>" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-8">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">اسم المسار *</label>
                        <input type="text" name="name" value="<?php echo e(old('name', $academicYear->name)); ?>" required
                               class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-500 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">رمز المسار *</label>
                        <input type="text" name="code" value="<?php echo e(old('code', $academicYear->code)); ?>" required
                               class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
                        <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-500 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">الوصف</label>
                        <textarea name="description" rows="4"
                                  class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition"><?php echo e(old('description', $academicYear->description)); ?></textarea>
                        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-500 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            <i class="fas fa-video text-sky-600 ml-1"></i>
                            رابط فيديو المقدمة
                        </label>
                        <input type="url" name="video_url" value="<?php echo e(old('video_url', $academicYear->video_url)); ?>"
                               class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition"
                               placeholder="https://www.youtube.com/watch?v=VIDEO_ID أو https://youtu.be/VIDEO_ID أو https://vimeo.com/VIDEO_ID">
                        <p class="mt-1 text-xs text-gray-500">
                            يُعرض في صفحة المسار على الموقع. الصيغ المدعومة: YouTube، Vimeo، أو رابط مباشر لملف .mp4
                        </p>
                        <?php $__errorArgs = ['video_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-500 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            <i class="fas fa-image text-sky-600 ml-1"></i>
                            صورة مصغرة للمسار
                        </label>
                        <?php if($academicYear->thumbnail): ?>
                            <div class="mb-3">
                                <img src="<?php echo e(asset('storage/' . $academicYear->thumbnail)); ?>" alt="Thumbnail" class="w-32 h-32 object-cover rounded-lg border border-gray-200">
                                <p class="text-xs text-gray-500 mt-1">الصورة الحالية</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="thumbnail" accept="image/*"
                               class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
                        <p class="mt-1 text-xs text-gray-500">
                            صورة مصغرة للمسار التعليمي. سيتم عرضها في قوائم المسارات.
                        </p>
                        <?php $__errorArgs = ['thumbnail'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-500 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            <i class="fas fa-tag text-sky-600 ml-1"></i>
                            سعر المسار (ج.م)
                        </label>
                        <input type="number" name="price" value="<?php echo e(old('price', $academicYear->price ?? 0)); ?>" min="0" step="0.01"
                               class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition"
                               placeholder="0">
                        <p class="mt-1 text-xs text-gray-500">
                            سعر المسار مستقل عن أسعار الكورسات. هذا السعر يظهر على الموقع للاشتراك في المسار. اتركه 0 للمسار المجاني.
                        </p>
                        <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-500 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">الأيقونة</label>
                        <select name="icon"
                                class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
                            <?php
                                $icons = [
                                    'fas fa-calendar-alt' => '📅 تقويم',
                                    'fas fa-graduation-cap' => '🎓 تخرج',
                                    'fas fa-school' => '🏫 مدرسة',
                                    'fas fa-book' => '📚 كتاب',
                                    'fas fa-user-graduate' => '👨‍🎓 طالب',
                                    'fas fa-compass' => '🧭 مسار',
                                    'fas fa-lightbulb' => '💡 مهارات'
                                ];
                            ?>
                            <?php $__currentLoopData = $icons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iconValue => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($iconValue); ?>" <?php echo e(old('icon', $academicYear->icon) === $iconValue ? 'selected' : ''); ?>>
                                    <?php echo e($label); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['icon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-500 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">اللون</label>
                        <input type="color" name="color" value="<?php echo e(old('color', $academicYear->color ?? '#0ea5e9')); ?>"
                               class="w-full h-12 rounded-2xl border border-gray-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500/40">
                        <?php $__errorArgs = ['color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-500 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">ترتيب الظهور</label>
                        <input type="number" name="order" value="<?php echo e(old('order', $academicYear->order)); ?>" min="0"
                               class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
                        <?php $__errorArgs = ['order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-500 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">حالة المسار</label>
                        <div class="flex items-center gap-2 px-4 py-3 rounded-2xl bg-slate-100 border border-slate-200">
                            <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $academicYear->is_active) ? 'checked' : ''); ?>

                                   class="w-5 h-5 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                            <span class="text-sm text-gray-700">المسار متاح للطلاب</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t border-gray-100">
                    <span class="text-xs text-gray-500">
                        آخر تعديل: <?php echo e($academicYear->updated_at?->diffForHumans() ?? 'غير متوفر'); ?>

                    </span>
                    <div class="flex flex-col md:flex-row md:items-center gap-3">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-600 via-blue-600 to-sky-600 hover:from-sky-700 hover:via-blue-700 hover:to-sky-700 text-white px-6 py-3 text-sm font-bold shadow-lg shadow-sky-600/30 hover:shadow-xl hover:shadow-sky-600/40 hover:-translate-y-0.5 transition-all duration-300">
                            <i class="fas fa-save"></i>
                            حفظ التغييرات
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2 space-y-6">
                <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-gray-200/50 hover:border-sky-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);">
                    <div class="border-b border-gray-100 px-6 sm:px-8 py-5 flex items-center justify-between" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 50%, rgba(2, 132, 199, 0.08) 100%); border-bottom: 2px solid rgba(59, 130, 246, 0.3);">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">المجموعات المهارية المرتبطة</h2>
                            <p class="text-sm text-gray-500 mt-1">
                                راجع المجموعات المرتبطة بالمسار وانتقل لإدارتها أو تعديلها.
                            </p>
                        </div>
                        <a href="<?php echo e(route('admin.academic-subjects.create', ['track' => $academicYear->id])); ?>"
                           class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-900 text-white px-4 py-2 text-sm font-semibold hover:bg-slate-700 transition">
                            <i class="fas fa-plus"></i>
                            إضافة مجموعة جديدة
                        </a>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $clusters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cluster): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $metrics = $cluster->cluster_metrics ?? [];
                            ?>
                            <div class="px-6 sm:px-8 py-5 flex flex-col gap-4 hover:bg-slate-50 transition">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <div class="space-y-1">
                                        <h3 class="text-lg font-semibold text-gray-900"><?php echo e($cluster->name); ?></h3>
                                        <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100">
                                                <i class="fas fa-barcode"></i><?php echo e($cluster->code); ?>

                                            </span>
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full <?php echo e($cluster->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'); ?>">
                                                <i class="fas fa-circle"></i><?php echo e($cluster->is_active ? 'نشطة' : 'موقوفة'); ?>

                                            </span>
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-sky-100 text-sky-700">
                                                <i class="fas fa-graduation-cap"></i><?php echo e($metrics['courses_count'] ?? 0); ?> كورس
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <?php if(!empty($metrics['languages']) || !empty($metrics['frameworks'])): ?>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs text-gray-600">
                                        <div class="flex flex-wrap items-center gap-1">
                                            <span class="font-semibold text-gray-500">اللغات:</span>
                                            <?php $__empty_2 = true; $__currentLoopData = $metrics['languages'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                                <span class="px-2 py-1 rounded-full bg-slate-100"><?php echo e($language); ?></span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                                <span>-</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-1">
                                            <span class="font-semibold text-gray-500">الأطر:</span>
                                            <?php $__empty_2 = true; $__currentLoopData = $metrics['frameworks'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $framework): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                                <span class="px-2 py-1 rounded-full bg-slate-100"><?php echo e($framework); ?></span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                                <span>-</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if(!empty($metrics['avg_duration']) || !empty($metrics['avg_rating'])): ?>
                                    <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500">
                                        <?php if(!empty($metrics['avg_duration'])): ?>
                                            <span><i class="fas fa-clock ml-1"></i>مدة متوسطة <?php echo e($metrics['avg_duration']); ?></span>
                                        <?php endif; ?>
                                        <?php if(!empty($metrics['avg_rating'])): ?>
                                            <span><i class="fas fa-star ml-1 text-amber-400"></i>تقييم <?php echo e($metrics['avg_rating']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if(($cluster->preview_courses ?? collect())->isNotEmpty()): ?>
                                    <div class="border border-slate-100 rounded-2xl px-4 py-3 bg-slate-50 space-y-2">
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">كورسات مرتبطة</p>
                                        <?php $__currentLoopData = $cluster->preview_courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="flex items-center justify-between text-xs text-gray-600">
                                                <span class="truncate flex-1"><?php echo e($course->title); ?></span>
                                                <div class="flex items-center gap-2 text-gray-400">
                                                    <?php if($course->programming_language): ?>
                                                        <span><i class="fas fa-code ml-1"></i><?php echo e($course->programming_language); ?></span>
                                                    <?php endif; ?>
                                                    <?php if($course->level): ?>
                                                        <span><i class="fas fa-signal ml-1"></i><?php echo e($course->level); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- أزرار الإجراءات في الجزء الأبيض السفلي -->
                                <div class="pt-4 border-t border-gray-200">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="<?php echo e(route('admin.academic-subjects.edit', $cluster)); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-slate-100 transition">
                                            <i class="fas fa-pen"></i> تعديل
                                        </a>
                                        <a href="<?php echo e(route('admin.academic-subjects.index', ['track' => $academicYear->id])); ?>" class="inline-flex items-center gap-2 rounded-xl border border-sky-200 px-4 py-2 text-xs font-semibold text-sky-700 hover:bg-sky-50 transition">
                                            <i class="fas fa-layer-group"></i> إدارة
                                        </a>
                                        <form method="POST" action="<?php echo e(route('admin.academic-subjects.destroy', $cluster)); ?>" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه المجموعة المهارية؟ سيتم فقدان أي ربط يدوي للكورسات مع هذا الاسم. هذا الإجراء لا يمكن التراجع عنه!');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-red-200 px-4 py-2 text-xs font-semibold text-red-700 hover:bg-red-50 transition">
                                                <i class="fas fa-trash"></i> حذف
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="px-6 py-10 text-center text-gray-500">
                                لم يتم إضافة مجموعات مهارية بعد. استخدم زر "إضافة مجموعة جديدة" للبدء.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-gray-200/50 hover:border-sky-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);">
                    <div class="border-b border-gray-100 px-6 sm:px-8 py-4" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 50%, rgba(2, 132, 199, 0.08) 100%); border-bottom: 2px solid rgba(59, 130, 246, 0.3);">
                        <h3 class="text-lg font-black bg-gradient-to-r from-sky-700 via-blue-600 to-sky-600 bg-clip-text text-transparent">
                            <i class="fas fa-chart-bar text-sky-600 ml-2"></i>
                            إحصائيات المسار
                        </h3>
                    </div>
                    <div class="px-6 sm:px-8 py-5 space-y-4 text-sm text-gray-600">
                        <div class="flex items-center justify-between">
                            <span>عدد الكورسات</span>
                            <span class="font-semibold text-gray-900"><?php echo e($trackSummary['courses_count']); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>متوسط المدة</span>
                            <span class="font-semibold text-gray-900"><?php echo e($trackSummary['avg_duration'] ?? 'غير محدد'); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>التقييم المتوسط</span>
                            <span class="font-semibold text-gray-900"><?php echo e($trackSummary['avg_rating'] ?? 'غير متوفر'); ?></span>
                        </div>
                        <div class="space-y-2">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">اللغات</p>
                            <div class="flex flex-wrap gap-1">
                                <?php $__empty_1 = true; $__currentLoopData = $trackSummary['languages']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <span class="px-2 py-1 rounded-full bg-slate-100 text-xs"><?php echo e($language); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <span class="text-xs text-gray-400">-</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">أطر العمل</p>
                            <div class="flex flex-wrap gap-1">
                                <?php $__empty_1 = true; $__currentLoopData = $trackSummary['frameworks']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $framework): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <span class="px-2 py-1 rounded-full bg-slate-100 text-xs"><?php echo e($framework); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <span class="text-xs text-gray-400">-</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-rose-200/50 hover:border-rose-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(254, 242, 242, 0.95) 100%);">
                    <div class="border-b border-rose-100 px-6 sm:px-8 py-4" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(220, 38, 38, 0.1) 50%, rgba(185, 28, 28, 0.08) 100%); border-bottom: 2px solid rgba(239, 68, 68, 0.3);">
                        <h3 class="text-lg font-black bg-gradient-to-r from-rose-700 via-red-600 to-rose-600 bg-clip-text text-transparent">
                            <i class="fas fa-exclamation-triangle text-rose-600 ml-2"></i>
                            منطقة خطرة
                        </h3>
                    </div>
                    <div class="px-6 sm:px-8 py-5 space-y-4 text-sm text-gray-600">
                        <p>حذف المسار سيزيله من لوحة التحكم. لا يمكن الحذف إذا كان يحتوي على مجموعات مهارية.</p>
                        <form action="<?php echo e(route('admin.academic-years.destroy', $academicYear)); ?>" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المسار؟');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" <?php echo e($academicYear->academicSubjects->isNotEmpty() ? 'disabled' : ''); ?>

                                    class="inline-flex items-center gap-2 rounded-2xl px-5 py-2 text-sm font-semibold <?php echo e($academicYear->academicSubjects->isNotEmpty() ? 'bg-rose-200 text-rose-700 cursor-not-allowed' : 'bg-rose-600 text-white hover:bg-rose-700'); ?>">
                                <i class="fas fa-trash"></i>
                                حذف المسار
                            </button>
                            <?php if($academicYear->academicSubjects->isNotEmpty()): ?>
                                <p class="mt-2 text-xs text-rose-500">قم بحذف أو نقل المجموعات المرتبطة قبل حذف المسار.</p>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- إدارة الكورسات المرتبطة بالمسار -->
        <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-gray-200/50 hover:border-sky-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);">
            <div class="border-b border-gray-100 px-6 sm:px-8 py-5" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 50%, rgba(2, 132, 199, 0.08) 100%); border-bottom: 2px solid rgba(59, 130, 246, 0.3);">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-black bg-gradient-to-r from-sky-700 via-blue-600 to-sky-600 bg-clip-text text-transparent">
                            <i class="fas fa-graduation-cap text-sky-600 ml-2"></i>
                            الكورسات المرتبطة بالمسار
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            أضف أو أزل الكورسات المرتبطة مباشرة بهذا المسار التعليمي.
                        </p>
                    </div>
                    <button type="button" onclick="showAddCourseModal()" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-600 via-blue-600 to-sky-600 hover:from-sky-700 hover:via-blue-700 hover:to-sky-700 text-white px-4 py-2 text-sm font-bold shadow-lg shadow-sky-600/30 hover:shadow-xl transition-all duration-300">
                        <i class="fas fa-plus"></i>
                        إضافة كورس
                    </button>
                </div>
            </div>
            <div class="p-6">
                <?php if($academicYear->linkedCourses->count() > 0): ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $academicYear->linkedCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200 hover:border-sky-300 transition-colors">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900"><?php echo e($course->title); ?></h4>
                                    <div class="flex flex-wrap items-center gap-2 mt-2 text-xs text-gray-600">
                                        <?php if($course->instructor): ?>
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-blue-100 text-blue-700">
                                                <i class="fas fa-user-tie"></i>
                                                <?php echo e($course->instructor->name); ?>

                                            </span>
                                        <?php endif; ?>
                                        <?php if($course->programming_language): ?>
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-purple-100 text-purple-700">
                                                <i class="fas fa-code"></i>
                                                <?php echo e($course->programming_language); ?>

                                            </span>
                                        <?php endif; ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                            <i class="fas fa-clock"></i>
                                            <?php echo e($course->duration_hours ?? 0); ?> ساعة
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full <?php echo e($course->price > 0 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'); ?>">
                                            <i class="fas fa-money-bill"></i>
                                            <?php echo e($course->price > 0 ? number_format($course->price) . ' ج.م' : 'مجاني'); ?>

                                        </span>
                                    </div>
                                </div>
                                <form method="POST" action="<?php echo e(route('admin.academic-years.remove-course', ['academicYear' => $academicYear->id, 'course' => $course->id])); ?>" class="inline" onsubmit="return confirm('هل أنت متأكد من إزالة هذا الكورس من المسار؟');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-100 text-red-700 hover:bg-red-200 transition-all duration-300">
                                        <i class="fas fa-times"></i>
                                        إزالة
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-graduation-cap text-4xl mb-3 text-gray-300"></i>
                        <p>لا توجد كورسات مرتبطة مباشرة بهذا المسار</p>
                        <p class="text-sm mt-1">استخدم زر "إضافة كورس" لإضافة كورسات للمسار</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- إدارة المدربين -->
        <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-gray-200/50 hover:border-sky-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);">
            <div class="border-b border-gray-100 px-6 sm:px-8 py-5" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.1) 50%, rgba(2, 132, 199, 0.08) 100%); border-bottom: 2px solid rgba(59, 130, 246, 0.3);">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-black bg-gradient-to-r from-sky-700 via-blue-600 to-sky-600 bg-clip-text text-transparent">
                            <i class="fas fa-user-tie text-sky-600 ml-2"></i>
                            المدربين في المسار
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            حدد المدربين المسؤولين عن هذا المسار والكورسات المخصصة لكل مدرب.
                        </p>
                    </div>
                    <button type="button" onclick="showAddInstructorModal()" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-600 via-blue-600 to-sky-600 hover:from-sky-700 hover:via-blue-700 hover:to-sky-700 text-white px-4 py-2 text-sm font-bold shadow-lg shadow-sky-600/30 hover:shadow-xl transition-all duration-300">
                        <i class="fas fa-plus"></i>
                        إضافة مدرب
                    </button>
                </div>
            </div>
            <div class="p-6">
                <?php if($academicYear->instructors->count() > 0): ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $academicYear->instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $assignedCourses = json_decode($instructor->pivot->assigned_courses ?? '[]', true);
                            ?>
                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 hover:border-sky-300 transition-colors">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900"><?php echo e($instructor->name); ?></h4>
                                        <p class="text-sm text-gray-600 mt-1"><?php echo e($instructor->email); ?></p>
                                        <?php if(!empty($assignedCourses) && count($assignedCourses) > 0): ?>
                                            <div class="mt-2">
                                                <p class="text-xs font-semibold text-gray-500 mb-1">الكورسات المخصصة:</p>
                                                <div class="flex flex-wrap gap-2">
                                                    <?php $__currentLoopData = $assignedCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $courseId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php
                                                            $course = $allCourses->firstWhere('id', $courseId);
                                                        ?>
                                                        <?php if($course): ?>
                                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-sky-100 text-sky-700 text-xs">
                                                                <i class="fas fa-graduation-cap"></i>
                                                                <?php echo e($course->title); ?>

                                                            </span>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-xs text-gray-500 mt-2">جميع الكورسات في المسار</p>
                                        <?php endif; ?>
                                    </div>
                                    <form method="POST" action="<?php echo e(route('admin.academic-years.remove-instructor', ['academicYear' => $academicYear->id, 'instructor' => $instructor->id])); ?>" class="inline" onsubmit="return confirm('هل أنت متأكد من إزالة هذا المدرب من المسار؟');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-100 text-red-700 hover:bg-red-200 transition-all duration-300">
                                            <i class="fas fa-times"></i>
                                            إزالة
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-user-tie text-4xl mb-3 text-gray-300"></i>
                        <p>لا يوجد مدربين مرتبطين بهذا المسار</p>
                        <p class="text-sm mt-1">استخدم زر "إضافة مدرب" لإضافة مدربين للمسار</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal إضافة كورس -->
<div id="addCourseModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[9999] hidden items-center justify-center" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto relative z-[10000]">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">إضافة كورس للمسار</h3>
                <button onclick="hideAddCourseModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <form method="POST" action="<?php echo e(route('admin.academic-years.add-course', $academicYear)); ?>" class="p-6 space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">اختر الكورس</label>
                <select name="course_id" required class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20">
                    <option value="">اختر الكورس</option>
                    <?php $__currentLoopData = $allCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(!$academicYear->linkedCourses->contains($course->id)): ?>
                            <option value="<?php echo e($course->id); ?>">
                                <?php echo e($course->title); ?> 
                                <?php if($course->instructor): ?>
                                    - <?php echo e($course->instructor->name); ?>

                                <?php endif; ?>
                                <?php if($course->price > 0): ?>
                                    (<?php echo e(number_format($course->price)); ?> ج.م)
                                <?php else: ?>
                                    (مجاني)
                                <?php endif; ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">ترتيب الكورس في المسار</label>
                <input type="number" name="order" value="0" min="0" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_required" value="1" checked id="is_required" class="w-5 h-5 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                <label for="is_required" class="text-sm text-gray-700">الكورس إجباري</label>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="hideAddCourseModal()" class="flex-1 px-4 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition-colors">
                    إلغاء
                </button>
                <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-700 hover:to-blue-700 text-white font-bold shadow-lg transition-all">
                    إضافة
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal إضافة مدرب -->
<div id="addInstructorModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[9999] hidden items-center justify-center" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto relative z-[10000]">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">إضافة مدرب للمسار</h3>
                <button onclick="hideAddInstructorModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <form method="POST" action="<?php echo e(route('admin.academic-years.add-instructor', $academicYear)); ?>" class="p-6 space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">اختر المدرب</label>
                <select name="instructor_id" required id="instructorSelect" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20">
                    <option value="">اختر المدرب</option>
                    <?php $__currentLoopData = $availableInstructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(!$academicYear->instructors->contains($instructor->id)): ?>
                            <option value="<?php echo e($instructor->id); ?>"><?php echo e($instructor->name); ?> - <?php echo e($instructor->email); ?></option>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">الكورسات المخصصة (اختياري)</label>
                <p class="text-xs text-gray-500 mb-2">اتركه فارغاً لتعيين جميع الكورسات في المسار</p>
                <div class="space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded-xl p-3">
                    <?php $__currentLoopData = $academicYear->linkedCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="flex items-center gap-2 p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                            <input type="checkbox" name="assigned_courses[]" value="<?php echo e($course->id); ?>" class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                            <span class="text-sm text-gray-700"><?php echo e($course->title); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($academicYear->linkedCourses->count() == 0): ?>
                        <p class="text-sm text-gray-500 text-center py-4">أضف كورسات للمسار أولاً</p>
                    <?php endif; ?>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">ملاحظات</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20" placeholder="ملاحظات حول هذا المدرب في المسار..."></textarea>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="hideAddInstructorModal()" class="flex-1 px-4 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition-colors">
                    إلغاء
                </button>
                <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-700 hover:to-blue-700 text-white font-bold shadow-lg transition-all">
                    إضافة
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showAddCourseModal() {
    const modal = document.getElementById('addCourseModal');
    if (modal) {
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function hideAddCourseModal() {
    const modal = document.getElementById('addCourseModal');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

function showAddInstructorModal() {
    const modal = document.getElementById('addInstructorModal');
    if (modal) {
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function hideAddInstructorModal() {
    const modal = document.getElementById('addInstructorModal');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

// إغلاق الـ Modal عند النقر خارجها
document.addEventListener('DOMContentLoaded', function() {
    const courseModal = document.getElementById('addCourseModal');
    const instructorModal = document.getElementById('addInstructorModal');
    
    if (courseModal) {
        courseModal.addEventListener('click', function(e) {
            if (e.target === this) {
                hideAddCourseModal();
            }
        });
    }
    
    if (instructorModal) {
        instructorModal.addEventListener('click', function(e) {
            if (e.target === this) {
                hideAddInstructorModal();
            }
        });
    }
    
    // إغلاق عند الضغط على ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideAddCourseModal();
            hideAddInstructorModal();
        }
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/academic-years/edit.blade.php ENDPATH**/ ?>