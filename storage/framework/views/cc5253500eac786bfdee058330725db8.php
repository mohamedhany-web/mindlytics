<?php $registration = $registration ?? null; ?>
<?php if($registration): ?>
    <div class="flex flex-wrap gap-2 justify-end">
        <?php if($registration->status === \App\Models\ScholarshipRegistration::STATUS_REGISTERED): ?>
            <form method="POST" action="<?php echo e(route('instructor.scholarships.registrations.activate', $registration)); ?>"><?php echo csrf_field(); ?>
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold transition-colors">
                    <i class="fas fa-check"></i>
                    <span>تفعيل</span>
                </button>
            </form>
            <form method="POST" action="<?php echo e(route('instructor.scholarships.registrations.reject', $registration)); ?>"><?php echo csrf_field(); ?>
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-500 hover:bg-rose-600 text-white text-xs font-semibold transition-colors">
                    <i class="fas fa-times"></i>
                    <span>رفض</span>
                </button>
            </form>
        <?php elseif($registration->status === \App\Models\ScholarshipRegistration::STATUS_ACTIVATED): ?>
            <form method="POST" action="<?php echo e(route('instructor.scholarships.registrations.deactivate', $registration)); ?>"><?php echo csrf_field(); ?>
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-500 hover:bg-slate-600 text-white text-xs font-semibold transition-colors">
                    <i class="fas fa-ban"></i>
                    <span>إلغاء التفعيل</span>
                </button>
            </form>
        <?php elseif($registration->status === \App\Models\ScholarshipRegistration::STATUS_DEACTIVATED): ?>
            <form method="POST" action="<?php echo e(route('instructor.scholarships.registrations.activate', $registration)); ?>"><?php echo csrf_field(); ?>
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold transition-colors">
                    <i class="fas fa-redo"></i>
                    <span>إعادة التفعيل</span>
                </button>
            </form>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\instructor\scholarships\_registration-actions.blade.php ENDPATH**/ ?>