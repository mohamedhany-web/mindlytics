{{--
    طبقة توحيد للصفحات العامة القديمة المكتوبة بـ Tailwind (docs/SITE_DESIGN.md):
    بتحوّل الألوان والتدرجات والزوايا والأزرار والخانات لهوية الموقع، من غير ما نلمس منطق الصفحة.
    كل القواعد تحت .h-legacy (الـ <main> بتاع layouts.public) عشان ما تأثرش على الهيدر والفوتر.
--}}
<style>
    .h-legacy { font-family: var(--h-font); color: var(--h-ink); }
    .h-legacy h1, .h-legacy h2, .h-legacy h3 { font-family: var(--h-display); letter-spacing: 0; }
    .h-legacy :is(.font-black, .font-extrabold) { font-weight: 700; }

    /* التدرجات: الغامقة (نص أبيض) بلون الخلفية الغامقة، والفاتحة رمادي فاتح */
    .h-legacy [class*="bg-gradient-to-"] { background-image: none; background-color: var(--h-mist); }
    .h-legacy [class*="bg-gradient-to-"].text-white,
    .h-legacy section[class*="bg-gradient-to-"]:has(h1.text-white, h2.text-white, .text-white > h1) { background-color: var(--h-ink); color: #FFFFFF; }
    .h-legacy [class*="bg-clip-text"] { background: none; -webkit-text-fill-color: currentColor; color: var(--h-ink); }
    .h-legacy :is(.text-white) [class*="bg-clip-text"] { color: #FFFFFF; }

    /* الزوايا والظلال: حادة وهادية زي الرئيسية (الشارات الدائرية الصغيرة تفضل) */
    .h-legacy :is(.rounded-lg, .rounded-xl, .rounded-2xl, .rounded-3xl) { border-radius: 2px; }
    .h-legacy :is(a, button).rounded-full { border-radius: 0; }
    .h-legacy :is(.shadow, .shadow-md, .shadow-lg, .shadow-xl, .shadow-2xl) { box-shadow: none; }
    .h-legacy :is(.shadow-lg, .shadow-xl, .shadow-2xl):not([class*="border"]) { box-shadow: 0 1px 0 var(--h-line), 0 0 0 1px var(--h-line); }
    .h-legacy [class*="hover:scale-"]:hover, .h-legacy [class*="hover:-translate-y"]:hover, .h-legacy [class*="group-hover:scale-"] { transform: none; }
    .h-legacy :is(.animate-pulse, .animate-bounce, .animate-spin):not([role="status"]) { animation: none; }

    /* الأزرار */
    .h-legacy :is(a, button, input[type="submit"]):is([class*="bg-gradient-to-"], .bg-blue-500, .bg-blue-600, .bg-blue-700, .bg-sky-500, .bg-sky-600, .bg-indigo-600, .bg-violet-600, .bg-purple-600, .bg-emerald-600, .bg-green-500, .bg-green-600, .bg-teal-600, .btn-primary) {
        background: var(--h-brand); background-image: none; color: #FFFFFF; border-radius: 0; box-shadow: none;
    }
    .h-legacy :is(a, button, input[type="submit"]):is([class*="bg-gradient-to-"], .bg-blue-500, .bg-blue-600, .bg-blue-700, .bg-sky-500, .bg-sky-600, .bg-indigo-600, .bg-violet-600, .bg-purple-600, .bg-emerald-600, .bg-green-500, .bg-green-600, .bg-teal-600, .btn-primary):hover { background: var(--h-ink); color: #FFFFFF; }
    .h-legacy :is(a, button).btn-secondary { background: var(--h-accent); background-image: none; color: var(--h-ink); border-radius: 0; box-shadow: none; }
    .h-legacy :is(a, button).btn-outline { background: transparent; color: var(--h-brand); border: 1px solid var(--h-brand); border-radius: 0; }
    .h-legacy .btn-primary::before { display: none; }
    .h-legacy :is(a, button):is(.bg-white):is(.text-blue-600, .text-sky-600, .text-indigo-600, .text-purple-600) { color: var(--h-brand); border-color: var(--h-brand); }

    /* الروابط والألوان المميزة */
    .h-legacy a:is(.text-blue-500, .text-blue-600, .text-blue-700, .text-sky-600, .text-sky-700, .text-indigo-600, .text-indigo-700, .text-violet-600, .text-purple-600) { color: var(--h-brand); }
    .h-legacy :is(.text-blue-600, .text-sky-600, .text-indigo-600, .text-purple-600, .text-violet-600):not(a) { color: var(--h-brand); }
    .h-legacy :is(.bg-blue-50, .bg-sky-50, .bg-indigo-50, .bg-purple-50, .bg-violet-50, .bg-cyan-50) { background-color: var(--h-mist); }
    .h-legacy :is(.bg-blue-100, .bg-sky-100, .bg-indigo-100, .bg-purple-100) { background-color: #E8E5F1; }
    .h-legacy :is(.border-blue-200, .border-sky-200, .border-indigo-200, .border-purple-200, .border-blue-100, .border-sky-100) { border-color: var(--h-line); }

    .h-legacy :is(.text-blue-900, .text-blue-800, .text-indigo-900, .text-indigo-800, .text-sky-900):not(a) { color: var(--h-ink); }
    .h-legacy .section-title::after { background: var(--h-brand) !important; }
    .h-legacy .careers-badge { background: var(--h-mist) !important; border-color: var(--h-line) !important; color: var(--h-brand) !important; }

    /* بطاقة السعر والأزرار في صفحات توصيف الكورسات (course-info) */
    .h-legacy .price-panel { background: var(--h-ink) !important; background-image: radial-gradient(60% 50% at 0% 0%, rgba(126, 104, 214, .3), rgba(126, 104, 214, 0) 70%) !important; border-radius: 0 !important; box-shadow: none !important; color: #FFFFFF; }
    .h-legacy .price-panel .text-blue-100 { color: var(--h-on-dark-2) !important; }
    .h-legacy .price-tag { background: transparent !important; border: 1px solid var(--h-line-dark) !important; border-radius: 0 !important; }
    .h-legacy .cta-primary { background: var(--h-accent) !important; color: var(--h-ink) !important; border-radius: 0 !important; box-shadow: none !important; }
    .h-legacy .cta-outline { background: transparent !important; color: #FFFFFF !important; border: 1px solid #FFFFFF !important; border-radius: 0 !important; }
    .h-legacy :is(.stat-card, .content-panel) { border-radius: 2px !important; box-shadow: none !important; border: 1px solid var(--h-line) !important; background: #FFFFFF; }

    /* الخانات */
    .h-legacy input:where(:not([type="checkbox"], [type="radio"], [type="file"], [type="range"], [type="color"], [type="hidden"], [type="submit"], [type="button"])),
    .h-legacy select, .h-legacy textarea { border-radius: 0; border-color: var(--h-field); font-family: inherit; }
    .h-legacy input:focus, .h-legacy select:focus, .h-legacy textarea:focus { outline: none; border-color: var(--h-brand); box-shadow: 0 0 0 3px rgba(63, 51, 86, .15); --tw-ring-shadow: 0 0 #0000; }
    .h-legacy input[type="checkbox"], .h-legacy input[type="radio"] { accent-color: var(--h-brand); }

    /* أبطال الصفحات القديمة (hero-gradient / hero-faq / hero-legal / hero-help) = رأس الصفحة الموحّد */
    .h-legacy > section:first-of-type:is(.hero-gradient, .hero-faq, .hero-legal, .hero-help) {
        min-height: 0 !important; padding: 56px 0 !important; margin-top: 0 !important; display: block;
        background: var(--h-ink) !important; background-image: radial-gradient(40% 90% at 0% 0%, rgba(126, 104, 214, .32), rgba(126, 104, 214, 0) 70%), radial-gradient(30% 80% at 8% 100%, rgba(242, 182, 48, .18), rgba(242, 182, 48, 0) 70%) !important;
    }
    .h-legacy > section:first-of-type:is(.hero-gradient, .hero-faq, .hero-legal, .hero-help)::before,
    .h-legacy > section:first-of-type:is(.hero-gradient, .hero-faq, .hero-legal, .hero-help) .particles { display: none; }
    .h-legacy > section:first-of-type:is(.hero-gradient, .hero-faq, .hero-legal, .hero-help) > .container { max-width: 1200px; text-align: start; }
    .h-legacy > section:first-of-type:is(.hero-gradient, .hero-faq, .hero-legal, .hero-help) h1 { font-size: 38px !important; line-height: 1.4; color: #FFFFFF; text-shadow: none !important; margin-bottom: 10px; }
    .h-legacy > section:first-of-type:is(.hero-gradient, .hero-faq, .hero-legal, .hero-help) p { color: var(--h-on-dark) !important; text-shadow: none !important; font-size: 17px !important; margin-inline: 0; }
    .h-legacy > section:first-of-type:is(.hero-gradient, .hero-faq, .hero-legal, .hero-help) .mx-auto { margin-inline: 0; }

    /* أبطال صفحات الكتالوج والمسارات وعن الأكاديمية وصفحة الكورس (.hero-section) */
    .h-legacy .hero-section { min-height: 0 !important; padding: 56px 0 48px !important; background: var(--h-ink) !important;
        background-image: radial-gradient(40% 90% at 0% 0%, rgba(126, 104, 214, .32), rgba(126, 104, 214, 0) 70%), radial-gradient(30% 80% at 8% 100%, rgba(242, 182, 48, .18), rgba(242, 182, 48, 0) 70%) !important; color: #FFFFFF; }
    .h-legacy .hero-section::before, .h-legacy .hero-section::after, .h-legacy .hero-section .animated-background, .h-legacy .hero-section .hero-glow { display: none !important; }
    .h-legacy .hero-section > div > .text-center, .h-legacy .hero-section > div > div > .text-center { text-align: start; }
    .h-legacy .hero-section > div > .text-center > :is(p, div), .h-legacy .hero-section .max-w-3xl.mx-auto, .h-legacy .hero-section .max-w-4xl.mx-auto { margin-inline: 0; }
    .h-legacy .hero-section h1 { font-size: 38px !important; line-height: 1.4 !important; color: #FFFFFF !important; margin-bottom: 10px !important; }
    .h-legacy .hero-section h1 * { color: #FFFFFF !important; -webkit-text-fill-color: #FFFFFF; background: none !important; animation: none !important; }
    .h-legacy .hero-section h1 + p { color: var(--h-on-dark) !important; font-size: 17px !important; margin-bottom: 24px !important; }
    .h-legacy .hero-section :is(.rounded-full, .rounded-2xl, .rounded-3xl) { border-radius: 0; }
    .h-legacy .hero-section .fade-in-up { animation: none; opacity: 1; transform: none; }
    .h-legacy .hero-section span[class*="bg-white"] { background: transparent !important; border: 1px solid var(--h-line-dark) !important; color: var(--h-accent) !important; }
    .h-legacy .hero-section nav :is(a, span, li) { color: var(--h-on-dark-2) !important; }

    /* الهيدر بقى sticky مش fixed: شيل المسافة اللي كانت متسابة له فوق الصفحات */
    .h-legacy > :first-child:is(.pt-24, .pt-28, .pt-32) { padding-top: 40px !important; }
    .h-legacy > :first-child[style*="padding-top"] { padding-top: 40px !important; }
    .h-legacy > :is(section, div):first-child.min-h-screen { min-height: 0; }
    @media (max-width: 960px) {
        .h-legacy > section:first-of-type:is(.hero-gradient, .hero-faq, .hero-legal, .hero-help) { padding: 36px 0 !important; }
        .h-legacy > section:first-of-type:is(.hero-gradient, .hero-faq, .hero-legal, .hero-help) h1, .h-legacy .hero-section h1 { font-size: 28px !important; }
        .h-legacy .hero-section { padding: 36px 0 32px !important; }
        .h-legacy > :first-child:is(.pt-24, .pt-28, .pt-32), .h-legacy > :first-child[style*="padding-top"] { padding-top: 24px !important; }
    }
</style>
