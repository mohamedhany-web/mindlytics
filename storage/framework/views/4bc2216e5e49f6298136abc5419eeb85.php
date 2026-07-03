
<?php
    $psMessage = $message ?? session('success', 'تم الدفع بنجاح!');
    $psRedirect = $redirectUrl ?? session('payment_success_redirect_url');
    $psSeconds = (int) ($seconds ?? 5);
    if ($psSeconds < 1) {
        $psSeconds = 5;
    }
?>
<div
    x-data="{
        open: true,
        seconds: <?php echo e($psSeconds); ?>,
        redirectUrl: <?php echo json_encode($psRedirect, 15, 512) ?>,
        _timer: null,
        init() {
            this._timer = setInterval(() => {
                this.seconds--;
                if (this.seconds <= 0) {
                    clearInterval(this._timer);
                    this._timer = null;
                    if (this.redirectUrl) window.location.href = this.redirectUrl;
                    this.open = false;
                }
            }, 1000);
        },
        dismiss() {
            if (this._timer) {
                clearInterval(this._timer);
                this._timer = null;
            }
            if (this.redirectUrl) window.location.href = this.redirectUrl;
            this.open = false;
        }
    }"
    x-show="open"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-cloak
    class="fixed inset-0 z-[10050] flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm"
    role="dialog"
    aria-modal="true"
    aria-labelledby="payment-success-title"
>
    <div class="relative w-full max-w-md rounded-3xl bg-white shadow-2xl border border-emerald-100 overflow-hidden">
        <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-l from-emerald-500 via-teal-500 to-sky-500"></div>
        <div class="p-8 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 mb-4 shadow-inner">
                <i class="fas fa-check-circle text-3xl"></i>
            </div>
            <h2 id="payment-success-title" class="text-xl font-black text-slate-900 mb-2">تم الدفع بنجاح</h2>
            <p class="text-sm text-slate-600 leading-relaxed mb-6"><?php echo e($psMessage); ?></p>
            <div class="rounded-2xl bg-slate-50 border border-slate-200 py-4 px-4 mb-6">
                <p class="text-xs font-semibold text-slate-500 mb-1">
                    <?php if($psRedirect): ?>
                        جاري تحويلك إلى صفحة الكورس خلال
                    <?php else: ?>
                        يمكنك البدء بالتعلم خلال
                    <?php endif; ?>
                </p>
                <p class="text-4xl font-black text-emerald-600 tabular-nums" x-text="seconds"></p>
                <p class="text-xs text-slate-400 mt-1">ثانية</p>
            </div>
            <button
                type="button"
                @click="dismiss()"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 text-white px-8 py-3 text-sm font-bold hover:bg-emerald-700 transition-colors"
            >
                <i class="fas fa-play-circle"></i>
                <?php if($psRedirect): ?>
                    انتقل الآن
                <?php else: ?>
                    ابدأ الآن
                <?php endif; ?>
            </button>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views\components\payment-success-modal.blade.php ENDPATH**/ ?>