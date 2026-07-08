<?php
    $waPending = $whatsappPendingCount ?? 0;
    $waContacted = $whatsappContactedCount ?? 0;
    $waTotal = $waPending + $waContacted;
    $defaultMessage = "مرحباً {{name}}،\n\nشكراً لتسجيلك في ورشة «{{workshop}}» ({{attendance}}).\n\n";
?>

<section class="rounded-xl border border-green-200 bg-gradient-to-br from-green-50/80 to-white p-4 sm:p-5 space-y-4">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="fab fa-whatsapp text-green-600"></i>
                إرسال واتساب يدوي (WhatsApp Web)
            </h3>
            <p class="text-xs text-slate-600 mt-1">
                اكتب الرسالة ثم افتح صفحة الإرسال — اضغط على كل رقم ليفتح واتساب ويب ويُسجَّل «تم التواصل».
            </p>
        </div>
        <div class="flex flex-wrap gap-2 text-[11px]">
            <span class="px-2 py-1 rounded-lg bg-white border border-slate-200 text-slate-700">
                <strong><?php echo e($waTotal); ?></strong> برقم
            </span>
            <span class="px-2 py-1 rounded-lg bg-amber-50 border border-amber-200 text-amber-800">
                <strong><?php echo e($waPending); ?></strong> لم يُتواصل
            </span>
            <span class="px-2 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800">
                <strong><?php echo e($waContacted); ?></strong> تم التواصل
            </span>
        </div>
    </div>

    <form method="POST" action="<?php echo e(route('admin.workshops.send-whatsapp', $workshop)); ?>" class="space-y-3">
        <?php echo csrf_field(); ?>

        <div class="grid sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-slate-700 mb-1">المستلمون</label>
                <select name="scope" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs">
                    <option value="all">كل المسجلين (برقم)</option>
                    <option value="pending">لم يُتواصل معهم فقط</option>
                    <option value="phone">رقم محدد</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-700 mb-1">نوع الحضور</label>
                <select name="attendance" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs">
                    <option value="all">الكل</option>
                    <option value="online">أونلاين</option>
                    <option value="offline">حضوري</option>
                </select>
            </div>
        </div>

        <input type="text" name="phone" placeholder="2010xxxxxxx (عند اختيار رقم محدد)"
               class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs dir-ltr">

        <div>
            <label class="block text-[11px] font-semibold text-slate-700 mb-1">نص الرسالة *</label>
            <textarea name="message" rows="4" required
                      class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs leading-relaxed"
                      placeholder="<?php echo e($defaultMessage); ?>"><?php echo e(old('message', $defaultMessage)); ?></textarea>
            <p class="text-[10px] text-slate-500 mt-1">
                متغيرات:
                <code class="bg-slate-100 px-1 rounded">{{name}}</code>
                <code class="bg-slate-100 px-1 rounded">{{workshop}}</code>
                <code class="bg-slate-100 px-1 rounded">{{attendance}}</code>
            </p>
        </div>

        <button type="submit"
                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white text-sm font-bold shadow-md">
            <i class="fab fa-whatsapp"></i>
            ابدأ — فتح صفحة الإرسال
        </button>
    </form>
</section>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\admin\workshops\_whatsapp_manual.blade.php ENDPATH**/ ?>