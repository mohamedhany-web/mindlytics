<?php $ad = $ad ?? null; ?>
<?php if($ad): ?>
<div id="popup-ad-overlay" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/55 backdrop-blur-md" style="animation: popupOverlayIn 0.5s ease-out;">
    
    <div class="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
        <?php for($i = 0; $i < 32; $i++): ?>
        <div class="popup-fall absolute rounded-full" style="
            left: <?php echo e(rand(0, 100)); ?>%;
            top: -30px;
            width: <?php echo e(rand(8, 22)); ?>px;
            height: <?php echo e(rand(8, 22)); ?>px;
            background: linear-gradient(135deg, #0ea5e9, #38bdf8);
            opacity: 0.25;
            animation: shapeFall <?php echo e(4 + ($i % 4)); ?>s linear infinite;
            animation-delay: <?php echo e($i * 0.12); ?>s;
        "></div>
        <div class="popup-fall absolute rounded-lg" style="
            left: <?php echo e(rand(0, 100)); ?>%;
            top: -40px;
            width: <?php echo e(rand(10, 20)); ?>px;
            height: <?php echo e(rand(10, 20)); ?>px;
            background: linear-gradient(135deg, #6366f1, #818cf8);
            opacity: 0.2;
            animation: shapeFall <?php echo e(5 + ($i % 3)); ?>s linear infinite;
            animation-delay: <?php echo e($i * 0.1); ?>s;
        "></div>
        <?php endfor; ?>
    </div>
    
    <div class="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
        <?php for($j = 0; $j < 20; $j++): ?>
        <div class="popup-float absolute w-2 h-2 rounded-full bg-sky-400/40" style="
            left: <?php echo e(rand(5, 95)); ?>%;
            top: <?php echo e(rand(10, 90)); ?>%;
            animation: particleFloat <?php echo e(3 + ($j % 4)); ?>s ease-in-out infinite;
            animation-delay: <?php echo e($j * 0.2); ?>s;
        "></div>
        <?php endfor; ?>
    </div>

    
    <div id="popup-ad-box" class="relative z-10 w-full max-w-lg rounded-2xl overflow-hidden bg-white border-2 border-sky-200/80 shadow-2xl transition-all duration-500 popup-card-glow" style="
        box-shadow: 0 0 0 1px rgba(255,255,255,0.9), 0 25px 50px -12px rgba(0,0,0,0.2), 0 0 60px -15px rgba(14, 165, 233, 0.25);
        animation: popupCardIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    ">
        
        <div class="h-2 w-full bg-gradient-to-l from-sky-500 via-sky-400 to-blue-600 popup-top-bar"></div>

        
        <button type="button" id="popup-ad-close" class="absolute top-4 right-4 z-20 w-10 h-10 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-700 transition-all duration-200 hover:scale-110" aria-label="إغلاق">
            <i class="fas fa-times text-sm"></i>
        </button>

        <div class="p-8">
            
            <h3 class="text-2xl font-bold text-slate-800 mb-5 leading-tight text-center"><?php echo e($ad->title); ?></h3>

            
            <div class="text-slate-600 leading-relaxed text-base mb-6 whitespace-pre-wrap text-center"><?php echo e(nl2br(e($ad->body ?? ''))); ?></div>

            
            <?php if($ad->cta_text && $ad->link_url): ?>
            <div class="flex justify-center">
                <a href="<?php echo e($ad->link_url); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-l from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-sky-500/30 transition-all duration-300 hover:scale-105 hover:shadow-xl hover:shadow-sky-500/35">
                    <?php echo e($ad->cta_text); ?>

                    <i class="fas fa-arrow-left text-sm opacity-90"></i>
                </a>
            </div>
            <?php elseif($ad->cta_text): ?>
            <div class="flex justify-center">
                <span class="inline-flex items-center gap-2 px-6 py-3 bg-slate-100 text-slate-700 font-semibold rounded-xl"><?php echo e($ad->cta_text); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    @keyframes popupOverlayIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes popupCardIn {
        0% { opacity: 0; transform: scale(0.85) translateY(20px); }
        60% { transform: scale(1.02) translateY(-4px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes shapeFall {
        0% { transform: translateY(0) rotate(0deg); opacity: 0.2; }
        15% { opacity: 0.35; }
        85% { opacity: 0.2; }
        100% { transform: translateY(100vh) rotate(360deg); opacity: 0; }
    }
    @keyframes particleFloat {
        0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.35; }
        50% { transform: translate(8px, -12px) scale(1.15); opacity: 0.65; }
    }
    .popup-top-bar {
        animation: barShine 2.5s ease-in-out infinite;
    }
    @keyframes barShine {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.85; }
    }
    .popup-card-glow {
        animation: popupCardIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1), cardGlow 3s ease-in-out infinite;
    }
    @keyframes cardGlow {
        0%, 100% { box-shadow: 0 0 0 1px rgba(255,255,255,0.9), 0 25px 50px -12px rgba(0,0,0,0.2), 0 0 60px -15px rgba(14, 165, 233, 0.25); }
        50% { box-shadow: 0 0 0 1px rgba(255,255,255,0.9), 0 25px 50px -12px rgba(0,0,0,0.2), 0 0 80px -10px rgba(14, 165, 233, 0.35); }
    }
</style>
<script>
(function() {
    var overlay = document.getElementById('popup-ad-overlay');
    var closeBtn = document.getElementById('popup-ad-close');
    if (!overlay) return;
    function hide() {
        overlay.style.opacity = '0';
        overlay.style.pointerEvents = 'none';
        setTimeout(function() { overlay.style.display = 'none'; }, 350);
    }
    if (closeBtn) closeBtn.addEventListener('click', hide);
    overlay.addEventListener('click', function(e) { if (e.target === overlay) hide(); });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') hide(); });
})();
</script>
<?php endif; ?>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/partials/popup-ad.blade.php ENDPATH**/ ?>