<?php
    $welcome = $welcome ?? session('workshop_promo_welcome', []);
    $name = $welcome['name'] ?? auth()->user()?->name ?? '';
    $firstName = trim(explode(' ', $name)[0] ?: $name);
    $code = $welcome['code'] ?? '';
    $discount = $welcome['discount'] ?? '';
    $workshop = $welcome['workshop'] ?? null;
    $expires = $welcome['expires'] ?? null;
    $coursesUrl = url('/courses');

    $messageSeed = crc32(($code ?: 'x').'|'.($name ?: 'guest'));
    $headlines = [
        "يا {$firstName}… إنت رسمياً main character دلوقتي",
        "{$firstName} دخل الـ VIP zone — مفيش كلام",
        "باشمهندس {$firstName}؟ الكود اشتغل… والسعر بيتألم",
        "{$firstName}، النظام قال: ده واحد ليه taste",
        "مبروك {$firstName} — إنت مش مجرد طالب، إنت plot twist",
    ];
    $subtexts = [
        'الحساب اتعمل، الكود اتفعّل، والخصم جاهز يشتغل على الكورسات. إحنا شايفينك.',
        'يعني إيه؟ إنك حضرت الورشة ودخلت الكود صح — فالدنيا بقت تمشي لصالحك شوية.',
        'الكود شغال، الخصم locked in، وإنت officially واحد من الناس اللي بتعرف تستغل الفرصة.',
        'مش هنقولك بطل خارق… بس الخصم بتاعك فعلاً هيخلّي السعر يقول: أنا مصدوم.',
        'تمام، إنت دلوقتي عندك superpower اسمها «خصم الورشة». استخدمه بحرارة.',
    ];
    $badgeLines = [
        'W — الكود شغال',
        'verified flex',
        'no cap، الخصم حقيقي',
        'main character energy',
        'الكود اتعمله unlock',
    ];
    $discountQuips = [
        'السعر هيقولك: باي باي',
        'الفلوس بتتألم في صمت',
        'خصم مش طبيعي fr',
        'ده مش عرض… ده respect',
        'الحساب هيفرق معاك هنا',
    ];
    $ctaPrimary = ['يلا نشوف الكورسات 🔥', 'هات الكورسات — الخصم معانا', 'نروح نشتري بذكاء', 'فتح قائمة الكورسات'];
    $ctaSecondary = ['هنضل هنا الأول', 'تمام، فهمت القوة', 'هتفرج من اللوحة'];

    $headline = $headlines[$messageSeed % count($headlines)];
    $subtext = $subtexts[($messageSeed >> 3) % count($subtexts)];
    $badgeLine = $badgeLines[($messageSeed >> 5) % count($badgeLines)];
    $discountQuip = $discountQuips[($messageSeed >> 7) % count($discountQuips)];
    $btnExplore = $ctaPrimary[($messageSeed >> 9) % count($ctaPrimary)];
    $btnStay = $ctaSecondary[($messageSeed >> 11) % count($ctaSecondary)];
?><div
    x-data="{ open: true }"
    x-show="open"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-cloak
    class="fixed inset-0 z-[10050] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
    role="dialog"
    aria-modal="true"
    aria-labelledby="workshop-promo-welcome-title"
    @keydown.escape.window="open = false"
>
    <div class="workshop-promo-confetti" aria-hidden="true">
        <?php $__currentLoopData = range(1, 24); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <span class="workshop-promo-confetti-piece" style="--i: <?php echo e($i); ?>; --hue: <?php echo e(($i * 37) % 360); ?>;"></span>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div
        class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl border border-slate-200 overflow-hidden"
        x-show="open"
        x-transition:enter="transition ease-out duration-300 delay-75"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
    >
        <div class="absolute top-0 right-0 w-1 h-full bg-gradient-to-b from-sky-500 to-sky-600 rounded-r-2xl" aria-hidden="true"></div>
        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-l from-violet-500 via-sky-500 to-sky-400"></div>

        <button
            type="button"
            @click="open = false"
            class="absolute top-4 left-4 z-10 w-9 h-9 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-700 flex items-center justify-center transition-colors"
            aria-label="إغلاق"
        >
            <i class="fas fa-times text-sm"></i>
        </button>

        <div class="p-6 sm:p-8 text-center">
            <div class="flex flex-wrap items-center justify-center gap-2 mb-4">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-900 text-[10px] font-black uppercase tracking-wide border border-amber-200">
                    ✨ VIP unlocked
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-sky-100 text-sky-800 text-[10px] font-bold border border-sky-200">
                    Gen Z approved
                </span>
            </div>

            <div class="mx-auto mb-4 relative">
                <div class="w-20 h-20 mx-auto rounded-2xl bg-gradient-to-br from-sky-100 to-violet-100 flex items-center justify-center shadow-inner border border-sky-100 animate-[bounce_1s_ease-in-out_2]">
                    <span class="text-4xl leading-none select-none" aria-hidden="true">👑</span>
                </div>
                <div class="absolute -top-1 -right-2 sm:right-8 w-10 h-10 rounded-xl bg-violet-600 text-white flex items-center justify-center shadow-lg rotate-12">
                    <i class="fas fa-ticket-alt text-sm"></i>
                </div>
            </div>

            <p class="text-xs font-bold text-violet-600 mb-2">Mindlytics × Workshop pass</p>
            <h2 id="workshop-promo-welcome-title" class="text-xl sm:text-2xl font-black text-gray-900 mb-3 leading-snug px-1">
                <?php echo e($headline); ?>

            </h2>
            <p class="text-sm text-gray-600 leading-relaxed max-w-md mx-auto mb-6">
                <?php echo e($subtext); ?>

            </p>

            <div class="rounded-2xl border border-violet-200 bg-gradient-to-br from-violet-50 via-white to-sky-50 p-5 mb-6 text-right">
                <div class="flex items-center justify-center gap-2 mb-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold border border-emerald-200">
                        <i class="fas fa-bolt"></i>
                        <?php echo e($badgeLine); ?>

                    </span>
                </div>
                <?php if($code): ?>
                    <p class="text-xs font-semibold text-slate-500 mb-1">الكود اللي دخلته (واشتغل فعلاً)</p>
                    <p class="font-mono text-2xl font-black text-violet-700 tracking-wide mb-1"><?php echo e($code); ?></p>
                    <p class="text-[11px] text-violet-600/80 font-semibold mb-3 text-center">مش مجرد حروف — ده مفتاح الخصم بتاعك</p>
                <?php endif; ?>
                <?php if($discount): ?>
                    <div class="inline-flex flex-col items-center gap-1 rounded-xl bg-white border border-slate-200 px-4 py-3 shadow-sm w-full sm:w-auto">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-fire text-orange-500"></i>
                            <span class="text-sm text-slate-600">خصمك:</span>
                            <span class="text-xl font-black text-sky-600"><?php echo e($discount); ?></span>
                        </div>
                        <span class="text-[11px] font-bold text-slate-500"><?php echo e($discountQuip); ?></span>
                    </div>
                <?php endif; ?>
                <?php if($workshop): ?>
                    <p class="text-xs text-slate-600 mt-3">
                        <i class="fas fa-chalkboard-teacher text-violet-500 ml-1"></i>
                        الورشة اللي خلّتك كده: <span class="font-semibold text-slate-800"><?php echo e($workshop); ?></span>
                    </p>
                <?php endif; ?>
                <?php if($expires && $expires !== 'بدون انتهاء'): ?>
                    <p class="text-[11px] text-slate-500 mt-2">
                        <i class="fas fa-clock text-slate-400 ml-1"></i>
                        الخصم صالح لحد <?php echo e($expires); ?> — متسيبوش يضيع
                    </p>
                <?php endif; ?>
            </div>

            <p class="text-[11px] text-slate-400 mb-4 max-w-sm mx-auto">
                إحنا فخورين إنك معانا. جدّاً. مش كلام رسمي — إنت فعلاً واحد من الناس اللي بتتحرك.
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a
                    href="<?php echo e($coursesUrl); ?>"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white px-6 py-3 text-sm font-bold shadow-md shadow-sky-600/20 transition-colors"
                >
                    <i class="fas fa-graduation-cap"></i>
                    <?php echo e($btnExplore); ?>

                </a>
                <button
                    type="button"
                    @click="open = false"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-6 py-3 text-sm font-bold transition-colors"
                >
                    <i class="fas fa-couch"></i>
                    <?php echo e($btnStay); ?>

                </button>
            </div>
        </div>    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    .workshop-promo-confetti {
        position: fixed;
        inset: 0;
        pointer-events: none;
        overflow: hidden;
        z-index: 10049;
    }
    .workshop-promo-confetti-piece {
        position: absolute;
        top: -12px;
        left: calc((var(--i) * 4.2%) + 2%);
        width: 10px;
        height: 14px;
        border-radius: 2px;
        background: hsl(var(--hue), 75%, 58%);
        opacity: 0;
        animation: workshopPromoConfettiFall 3.2s ease-in forwards;
        animation-delay: calc(var(--i) * 0.08s);
    }
    .workshop-promo-confetti-piece:nth-child(3n) {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
    .workshop-promo-confetti-piece:nth-child(5n) {
        width: 6px;
        height: 16px;
    }
    @keyframes workshopPromoConfettiFall {
        0% {
            opacity: 0;
            transform: translateY(0) rotate(0deg) scale(0.6);
        }
        10% {
            opacity: 1;
        }
        100% {
            opacity: 0;
            transform: translateY(100vh) rotate(720deg) scale(1);
        }
    }
</style>
<?php /**PATH /Users/cityphone/Documents/all Mindlytics Project/Mindlytics/resources/views/components/workshop-promo-welcome-modal.blade.php ENDPATH**/ ?>