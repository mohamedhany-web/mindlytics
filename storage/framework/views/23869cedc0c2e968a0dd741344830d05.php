<?php
    $active = $active ?? '';
?>
<nav class="flex flex-wrap gap-2 p-1.5 rounded-2xl bg-white border-2 border-slate-200/50 shadow-sm">
    <a href="<?php echo e(route('admin.whatsapp.index')); ?>"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all <?php echo e($active === 'dashboard' ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
        <i class="fab fa-whatsapp"></i>
        لوحة الواتساب
    </a>
    <a href="<?php echo e(route('admin.whatsapp.send')); ?>"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all <?php echo e($active === 'send' ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
        <i class="fas fa-paper-plane"></i>
        إرسال رسالة
    </a>
    <a href="<?php echo e(route('admin.whatsapp.messages')); ?>"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all <?php echo e($active === 'messages' ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
        <i class="fas fa-list"></i>
        سجل الرسائل
    </a>
    <a href="<?php echo e(route('admin.whatsapp.settings')); ?>"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all <?php echo e($active === 'settings' ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'); ?>">
        <i class="fas fa-plug"></i>
        إعدادات الربط
    </a>
</nav>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/whatsapp/_nav.blade.php ENDPATH**/ ?>