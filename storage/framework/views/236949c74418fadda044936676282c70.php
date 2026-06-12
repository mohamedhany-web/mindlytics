

<?php $__env->startSection('title', 'نبذة عنك'); ?>
<?php $__env->startSection('content'); ?>
<div class="w-full max-w-3xl mx-auto">
    <?php if(session('success')): ?>
        <div class="mb-6 p-4 rounded-2xl bg-emerald-100 border border-emerald-300 text-emerald-800 font-medium"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="mb-6 p-4 rounded-2xl bg-red-100 border border-red-300 text-red-800">
            <ul class="list-disc list-inside text-sm"><?php echo e($errors->first()); ?></ul>
        </div>
    <?php endif; ?>

    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mb-2">نبذة عنك</h1>
        <p class="text-slate-600">ستُعرض في صفحة المساهمين بعد مراجعة الإدارة. أضف صورة ونبذة وخبراتك وروابطك.</p>
    </div>

    <form action="<?php echo e(route('community.contributor.profile.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-8">
        <?php echo csrf_field(); ?>

        
        <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-cyan-500/10 via-slate-50 to-blue-500/10 border border-slate-200/80 shadow-lg">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiMwMDcyOTkiIGZpbGwtb3BhY2l0eT0iMC4wNCI+PHBhdGggZD0iTTM2IDM0djItSDI0di0yaDEyem0wLTR2MkgyNHYtMmgxMnoiLz48L2c+PC9nPjwvc3ZnPg==')] opacity-40"></div>
            <div class="relative p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row gap-6 items-start">
                    <div class="flex-shrink-0">
                        <label for="photo" class="block cursor-pointer group">
                            <div class="w-32 h-32 rounded-2xl border-2 border-dashed border-slate-300 group-hover:border-cyan-400 bg-white overflow-hidden flex items-center justify-center transition-all shadow-inner">
                                <?php if($profile->photo_path && $profile->photo_url): ?>
                                    <img src="<?php echo e($profile->photo_url); ?>" alt="" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="text-slate-400 group-hover:text-cyan-500 transition-colors">
                                        <i class="fas fa-camera text-4xl mb-1"></i>
                                        <span class="text-xs font-bold">صورة شخصية</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/jpg,image/webp" class="sr-only">
                        </label>
                        <p class="text-xs text-slate-500 mt-2">JPG, PNG أو WebP. 2 ميجا كحد أقصى</p>
                    </div>
                    <div class="flex-1 min-w-0">
                        <label for="bio" class="block text-sm font-bold text-slate-700 mb-2">نبذة عنك</label>
                        <textarea name="bio" id="bio" rows="4" placeholder="اكتب نبذة قصيرة تظهر في صفحة المساهمين..."
                                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 resize-y bg-white/80"><?php echo e(old('bio', $profile->bio)); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-lg font-black text-slate-900 mb-3 flex items-center gap-2">
                <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center"><i class="fas fa-briefcase"></i></span>
                الخبرات
            </h2>
            <textarea name="experience" id="experience" rows="5" placeholder="اذكر خبراتك في التحليل أو البرمجة أو الذكاء الاصطناعي..."
                      class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 resize-y"><?php echo e(old('experience', $profile->experience)); ?></textarea>
        </div>

        
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-lg font-black text-slate-900 mb-4 flex items-center gap-2">
                <span class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center"><i class="fas fa-link"></i></span>
                روابطك
            </h2>
            <div>
                <label for="linkedin_url" class="block text-sm font-bold text-slate-700 mb-1">LinkedIn</label>
                <input type="url" name="linkedin_url" id="linkedin_url" value="<?php echo e(old('linkedin_url', $profile->linkedin_url)); ?>"
                       placeholder="https://linkedin.com/in/..."
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20">
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-cyan-600 text-white font-bold hover:bg-cyan-700 transition-colors shadow-md">
                <i class="fas fa-paper-plane"></i>
                <span>إرسال للمراجعة</span>
            </button>
            <a href="<?php echo e(route('community.contributor.dashboard')); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors">إلغاء</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('community.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/community/contributor/profile/edit.blade.php ENDPATH**/ ?>