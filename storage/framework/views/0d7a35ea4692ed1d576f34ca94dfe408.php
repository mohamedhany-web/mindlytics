<?php
    $template = $template ?? null;
    $buttons = old('buttons', $template?->buttons ?? [['type' => 'QUICK_REPLY', 'text' => '', 'url' => '', 'phone' => '']]);
    if (! is_array($buttons) || $buttons === []) {
        $buttons = [['type' => 'QUICK_REPLY', 'text' => '', 'url' => '', 'phone' => '']];
    }
?>

<div class="space-y-6" x-data="{ headerType: '<?php echo e(old('header_type', $template?->header_type ?? '')); ?>', buttons: <?php echo \Illuminate\Support\Js::from($buttons)->toHtml() ?> }">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="<?php echo e($waLabelClass); ?>">اسم القالب (Name) *</label>
            <input type="text" name="name" value="<?php echo e(old('name', $template?->name)); ?>" required
                   pattern="[a-z0-9_]+" dir="ltr"
                   placeholder="order_confirmation"
                   class="<?php echo e($waInputClass); ?> font-mono">
            <p class="text-xs text-slate-500 mt-1">أحرف إنجليزية صغيرة، أرقام، و _ فقط — يُستخدم في Meta API</p>
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div>
            <label class="<?php echo e($waLabelClass); ?>">اللغة *</label>
            <select name="language" required class="<?php echo e($waSelectClass); ?>">
                <?php $__currentLoopData = \App\Models\WhatsAppMetaTemplate::languageOptions(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($code); ?>" <?php if(old('language', $template?->language ?? 'ar') === $code): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="<?php echo e($waLabelClass); ?>">الفئة *</label>
            <select name="category" required class="<?php echo e($waSelectClass); ?>">
                <?php $__currentLoopData = \App\Models\WhatsAppMetaTemplate::categoryLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($val); ?>" <?php if(old('category', $template?->category ?? 'UTILITY') === $val): echo 'selected'; endif; ?>><?php echo e($lbl); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <p class="text-xs text-slate-500 mt-1">Marketing يتطلب موافقة أطول — Utility للإشعارات والمعاملات</p>
        </div>
        <div>
            <label class="<?php echo e($waLabelClass); ?>">Header (اختياري)</label>
            <select name="header_type" x-model="headerType" class="<?php echo e($waSelectClass); ?>">
                <?php $__currentLoopData = \App\Models\WhatsAppMetaTemplate::headerTypeLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($val); ?>"><?php echo e($lbl); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </div>

    <div x-show="headerType !== ''" x-cloak>
        <label class="<?php echo e($waLabelClass); ?>">
            <span x-text="headerType === 'text' ? 'نص الـ Header' : 'رابط مثال للوسائط (Example URL)'"></span>
        </label>
        <input type="text" name="header_content" value="<?php echo e(old('header_content', $template?->header_content)); ?>"
               class="<?php echo e($waInputClass); ?>" dir="ltr"
               :placeholder="headerType === 'text' ? 'عنوان الرسالة' : 'https://example.com/image.jpg'">
        <p class="text-xs text-slate-500 mt-1" x-show="headerType !== 'text'">Meta تطلب رابط مثال للصورة/فيديو/مستند عند المراجعة</p>
    </div>

    <div>
        <label class="<?php echo e($waLabelClass); ?>">محتوى الرسالة (Body) *</label>
        <textarea name="body_text" rows="6" required class="<?php echo e($waTextareaClass); ?>"
                  placeholder="مرحباً {{1}}، طلبك رقم {{2}} تم تأكيده."><?php echo e(old('body_text', $template?->body_text)); ?></textarea>
        <p class="text-xs text-slate-500 mt-1">استخدم متغيرات Meta: <code class="bg-slate-100 px-1 rounded" dir="ltr">{{1}}</code> <code class="bg-slate-100 px-1 rounded" dir="ltr">{{2}}</code> …</p>
        <?php $__errorArgs = ['body_text'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div>
        <label class="<?php echo e($waLabelClass); ?>">Footer (اختياري — 60 حرف)</label>
        <input type="text" name="footer_text" maxlength="60" value="<?php echo e(old('footer_text', $template?->footer_text)); ?>"
               class="<?php echo e($waInputClass); ?>" placeholder="شكراً لثقتك بنا">
    </div>

    <div>
        <div class="flex items-center justify-between mb-3">
            <label class="<?php echo e($waLabelClass); ?> mb-0">الأزرار (Buttons)</label>
            <button type="button" @click="buttons.push({type:'QUICK_REPLY',text:'',url:'',phone:''})"
                    class="text-xs font-bold text-emerald-700 hover:underline">+ إضافة زر</button>
        </div>
        <div class="space-y-3">
            <template x-for="(btn, i) in buttons" :key="i">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 p-3 rounded-xl border border-slate-200 bg-slate-50/50">
                    <div class="sm:col-span-3">
                        <select :name="'buttons['+i+'][type]'" x-model="btn.type" class="<?php echo e($waSelectClass); ?> !text-xs">
                            <option value="QUICK_REPLY">رد سريع</option>
                            <option value="URL">رابط URL</option>
                            <option value="PHONE_NUMBER">اتصال</option>
                        </select>
                    </div>
                    <div class="sm:col-span-4">
                        <input type="text" :name="'buttons['+i+'][text]'" x-model="btn.text" placeholder="نص الزر" class="<?php echo e($waInputClass); ?> !text-xs">
                    </div>
                    <div class="sm:col-span-4" x-show="btn.type === 'URL'">
                        <input type="url" :name="'buttons['+i+'][url]'" x-model="btn.url" placeholder="https://..." dir="ltr" class="<?php echo e($waInputClass); ?> !text-xs">
                    </div>
                    <div class="sm:col-span-4" x-show="btn.type === 'PHONE_NUMBER'">
                        <input type="text" :name="'buttons['+i+'][phone]'" x-model="btn.phone" placeholder="+2010..." dir="ltr" class="<?php echo e($waInputClass); ?> !text-xs">
                    </div>
                    <div class="sm:col-span-1 flex items-center justify-end">
                        <button type="button" @click="buttons.splice(i,1)" class="text-rose-600 hover:text-rose-800 p-1" title="حذف">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/admin/whatsapp/templates/_form.blade.php ENDPATH**/ ?>