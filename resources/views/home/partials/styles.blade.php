{{-- ستايل الصفحة الرئيسية الجديدة (docs/HOMEPAGE.md): ألوان البراند، بدون مكتبات CSS خارجية. --}}
<style>
    :root {
        --h-ink: #17142A; --h-ink-2: #231D36; --h-brand: #3F3356; --h-accent: #F2B630;
        --h-mist: #F4F3F8; --h-line: #E3E1EA; --h-line-dark: #3A3550;
        --h-text-2: #4A4560; --h-muted: #5B5670; --h-on-dark: #D9D6E4; --h-on-dark-2: #C9C5D6;
    }
    *, *::before, *::after { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body { margin: 0; background: #FFFFFF; color: var(--h-ink); font-family: 'IBM Plex Sans Arabic', system-ui, sans-serif; font-size: 16px; line-height: 1.75; -webkit-font-smoothing: antialiased; }
    img { max-width: 100%; }
    a { color: var(--h-brand); }
    a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible, summary:focus-visible { outline: 3px solid var(--h-accent); outline-offset: 2px; }
    .h-wrap { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
    .h-kufi { font-family: 'Noto Kufi Arabic', 'IBM Plex Sans Arabic', sans-serif; }
    .h-eyebrow { display: block; font-size: 14px; font-weight: 600; color: var(--h-brand); margin-bottom: 8px; }
    .h-eyebrow-accent { color: var(--h-accent); }
    .h-h2 { margin: 0; font-family: 'Noto Kufi Arabic', sans-serif; font-size: 32px; line-height: 1.45; font-weight: 700; }
    .h-lead { margin: 12px 0 0; color: var(--h-text-2); font-size: 17px; max-width: 640px; }
    .h-section { padding: 88px 0; }
    .h-section-mist { background: var(--h-mist); border-top: 1px solid var(--h-line); border-bottom: 1px solid var(--h-line); }
    .h-section-dark { background: var(--h-ink); color: #FFFFFF; }
    .h-section-head { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-end; gap: 16px; margin-bottom: 32px; }
    .h-link { color: var(--h-ink); text-decoration: none; font-weight: 600; }
    .h-link:hover { color: var(--h-brand); text-decoration: underline; text-underline-offset: 4px; }

    /* أزرار */
    .h-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 48px; padding: 0 24px; font: inherit; font-weight: 600; font-size: 15px; text-decoration: none; border: 1px solid transparent; cursor: pointer; transition: background-color .15s, color .15s, border-color .15s; }
    .h-btn-primary { background: var(--h-brand); color: #FFFFFF; }
    .h-btn-primary:hover { background: var(--h-ink); color: #FFFFFF; }
    .h-btn-accent { background: var(--h-accent); color: var(--h-ink); font-weight: 700; }
    .h-btn-accent:hover { background: #FFC94D; color: var(--h-ink); }
    .h-btn-outline { border-color: var(--h-brand); color: var(--h-brand); background: transparent; }
    .h-btn-outline:hover { background: var(--h-brand); color: #FFFFFF; }
    .h-btn-ghost-light { border-color: #FFFFFF; color: #FFFFFF; background: transparent; }
    .h-btn-ghost-light:hover { background: #FFFFFF; color: var(--h-ink); }
    .h-btn-lg { min-height: 54px; padding: 0 30px; font-size: 16px; }

    /* الشريط العلوي والهيدر */
    .h-topbar { background: var(--h-ink); color: var(--h-on-dark); font-size: 13px; }
    .h-topbar .h-wrap { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 8px; min-height: 40px; }
    .h-topbar a { color: #FFFFFF; text-decoration: none; }
    .h-topbar a:hover { text-decoration: underline; }
    .h-topbar-links { display: flex; flex-wrap: wrap; gap: 20px; }
    .h-header { position: sticky; top: 0; z-index: 40; background: #FFFFFF; border-bottom: 1px solid var(--h-line); }
    .h-header .h-wrap { display: flex; justify-content: space-between; align-items: center; gap: 16px; min-height: 76px; }
    .h-logo { display: flex; align-items: center; gap: 12px; color: var(--h-ink); text-decoration: none; }
    .h-logo-mark { width: 40px; height: 40px; background: var(--h-brand); color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-family: 'Noto Kufi Arabic', sans-serif; font-weight: 700; font-size: 20px; }
    .h-logo-text { display: flex; flex-direction: column; line-height: 1.25; }
    .h-logo-text b { font-family: 'Noto Kufi Arabic', sans-serif; font-size: 19px; }
    .h-logo-text span { font-size: 12px; color: var(--h-muted); }
    .h-nav { display: flex; gap: 28px; font-size: 15px; font-weight: 500; }
    .h-nav a { color: var(--h-ink); text-decoration: none; }
    .h-nav a:hover { color: var(--h-brand); text-decoration: underline; text-underline-offset: 6px; }
    .h-menu-btn { display: none; width: 46px; height: 46px; border: 1px solid #C9C5D6; background: #FFFFFF; cursor: pointer; align-items: center; justify-content: center; padding: 0; }
    .h-mobile-menu { display: none; border-top: 1px solid var(--h-line); background: #FFFFFF; }
    .h-mobile-menu a { display: block; padding: 16px 24px; border-bottom: 1px solid var(--h-line); color: var(--h-ink); text-decoration: none; font-size: 18px; font-weight: 600; }
    .h-mobile-menu.is-open { display: block; }

    /* البطل: الصورة الحقيقية مع ألوان خفيفة على الأطراف بتتداخل مع الصورة */
    .h-hero { display: flex; flex-wrap: wrap; background: var(--h-ink); color: #FFFFFF; }
    .h-hero-copy { flex: 1 1 520px; min-width: 0; display: flex; padding: 88px 0; position: relative; z-index: 1; }
    .h-hero-inner { max-width: 580px; padding: 0 48px; margin-inline-start: auto; display: flex; flex-direction: column; gap: 22px; }
    .h-hero h1 { margin: 0; font-family: 'Noto Kufi Arabic', sans-serif; font-size: 46px; line-height: 1.35; font-weight: 700; color: #FFFFFF; }
    .h-hero p { margin: 0; font-size: 18px; color: var(--h-on-dark); }
    .h-hero-actions { display: flex; flex-wrap: wrap; gap: 18px; align-items: center; }
    .h-hero-actions .h-link { color: #FFFFFF; }
    .h-hero-facts { list-style: none; margin: 14px 0 0; padding: 20px 0 0; border-top: 1px solid var(--h-line-dark); display: flex; flex-wrap: wrap; gap: 10px 28px; font-size: 14px; color: var(--h-on-dark-2); }
    .h-hero-facts li { display: flex; align-items: center; gap: 8px; }
    .h-hero-facts li::before { content: ''; width: 6px; height: 6px; background: var(--h-accent); }
    .h-photo { flex: 1 1 520px; min-width: 0; margin: 0; position: relative; min-height: 520px; overflow: hidden; isolation: isolate; }
    .h-photo img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: block; z-index: -2; }
    /* الطرف الملاصق للنص بيذوب في لون الخلفية الغامق */
    .h-photo::before { content: ''; position: absolute; inset: 0; z-index: -1; pointer-events: none;
        background:
            linear-gradient(to left, var(--h-ink) 0%, rgba(23, 20, 42, .72) 9%, rgba(23, 20, 42, .18) 26%, rgba(23, 20, 42, 0) 38%),
            linear-gradient(to top, rgba(23, 20, 42, .55) 0%, rgba(23, 20, 42, 0) 22%); }
    /* لمسات لون خفيفة من البراند في الأركان */
    .h-photo::after { content: ''; position: absolute; inset: 0; z-index: -1; pointer-events: none;
        background:
            radial-gradient(48% 46% at 0% 100%, rgba(242, 182, 48, .46) 0%, rgba(242, 182, 48, .16) 45%, rgba(242, 182, 48, 0) 72%),
            radial-gradient(52% 44% at 0% 0%, rgba(126, 104, 214, .52) 0%, rgba(126, 104, 214, .18) 48%, rgba(126, 104, 214, 0) 74%),
            radial-gradient(34% 40% at 72% 100%, rgba(126, 104, 214, .42) 0%, rgba(126, 104, 214, 0) 70%),
            radial-gradient(30% 34% at 70% 0%, rgba(242, 182, 48, .22) 0%, rgba(242, 182, 48, 0) 70%); }
    .h-photo figcaption { position: absolute; bottom: 16px; inset-inline-end: 16px; background: rgba(255, 255, 255, .94); color: var(--h-ink); font-size: 13px; padding: 6px 12px; }

    /* الأرقام */
    .h-stats { border-bottom: 1px solid var(--h-line); }
    .h-stats .h-wrap { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); }
    .h-stat { padding: 26px 22px; border-inline-start: 1px solid var(--h-line); display: flex; flex-direction: column; }
    .h-stat b { font-family: 'Noto Kufi Arabic', sans-serif; font-size: 30px; }
    .h-stat span { font-size: 14px; color: var(--h-muted); }

    /* الدفعات */
    .h-filters { display: flex; flex-wrap: wrap; gap: 24px; align-items: center; margin-bottom: 24px; }
    .h-filter-group { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
    .h-filter-group > span { font-size: 13px; font-weight: 600; color: var(--h-text-2); margin-inline-end: 4px; }
    .h-chip { font: inherit; font-size: 14px; font-weight: 600; min-height: 44px; padding: 0 16px; cursor: pointer; border: 1px solid #C9C5D6; background: #FFFFFF; color: var(--h-ink); }
    .h-chip[aria-pressed="true"] { background: var(--h-ink); border-color: var(--h-ink); color: #FFFFFF; }
    .h-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 330px), 1fr)); gap: 16px; }
    .h-intake { background: #FFFFFF; border: 1px solid var(--h-line); padding: 24px; display: flex; flex-direction: column; gap: 14px; }
    .h-intake:hover { border-color: var(--h-brand); }
    .h-intake[hidden] { display: none; }
    .h-intake-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; }
    .h-intake-type { font-size: 13px; color: var(--h-muted); }
    .h-intake h3 { margin: 0; font-size: 19px; line-height: 1.5; }
    .h-tag { font-size: 12px; font-weight: 600; border: 1px solid var(--h-ink); padding: 2px 10px; white-space: nowrap; }
    .h-facts { margin: 0; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px 16px; font-size: 14px; }
    .h-facts dt { color: var(--h-muted); font-size: 12px; }
    .h-facts dd { margin: 0; font-weight: 600; }
    .h-intake-actions { display: flex; gap: 14px; align-items: center; margin-top: auto; padding-top: 6px; }
    .h-empty { background: #FFFFFF; border: 1px dashed #C9C5D6; padding: 26px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 16px; }
    .h-empty[hidden] { display: none; }

    /* البرامج */
    .h-programs { display: flex; flex-wrap: wrap; gap: 16px; align-items: stretch; }
    .h-flagship { flex: 2 1 560px; min-width: 0; border: 2px solid var(--h-ink); padding: 32px; display: flex; flex-direction: column; gap: 18px; }
    .h-flagship-head { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 12px; align-items: flex-start; }
    .h-flagship h3 { margin: 0; font-family: 'Noto Kufi Arabic', sans-serif; font-size: 26px; line-height: 1.4; }
    .h-price { font-family: 'Noto Kufi Arabic', sans-serif; font-weight: 700; font-size: 22px; }
    .h-highlights { list-style: none; margin: 0; padding: 14px 0 0; border-top: 1px solid var(--h-line); display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 8px 20px; color: var(--h-text-2); font-size: 15px; }
    .h-highlights li { display: flex; gap: 10px; }
    .h-highlights li::before { content: ''; flex: none; width: 6px; height: 6px; margin-top: 11px; background: var(--h-brand); }
    .h-side-programs { flex: 1 1 300px; min-width: 0; display: flex; flex-direction: column; gap: 16px; }
    .h-program { flex: 1; border: 1px solid var(--h-line); padding: 22px 24px; display: flex; flex-direction: column; gap: 6px; color: var(--h-ink); text-decoration: none; }
    .h-program:hover { border-color: var(--h-brand); }
    .h-program small { font-size: 13px; color: var(--h-muted); }
    .h-program b { font-size: 18px; }
    .h-program span { color: var(--h-text-2); font-size: 15px; }

    /* الأهداف */
    .h-tabs { display: flex; flex-wrap: wrap; border-bottom: 1px solid var(--h-line); margin-bottom: 24px; }
    .h-tab { background: transparent; border: 0; border-bottom: 3px solid transparent; margin-bottom: -1px; margin-inline-end: 28px; font: inherit; font-size: 16px; font-weight: 500; color: var(--h-muted); padding: 0 2px; min-height: 52px; cursor: pointer; }
    .h-tab[aria-selected="true"] { border-bottom-color: var(--h-brand); font-weight: 700; color: var(--h-ink); }
    .h-panel { display: flex; flex-wrap: wrap; gap: 28px; align-items: center; justify-content: space-between; }
    .h-panel[hidden] { display: none; }
    .h-panel p { margin: 0; font-size: 19px; line-height: 1.7; flex: 1 1 520px; min-width: 0; }

    /* نظام الدراسة والتحقق */
    .h-split { display: flex; flex-wrap: wrap; gap: 48px; align-items: flex-start; }
    .h-split > .h-grow { flex: 2 1 560px; min-width: 0; }
    .h-split > .h-side { flex: 1 1 320px; min-width: 0; }
    .h-system { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); border-top: 2px solid var(--h-ink); margin-top: 28px; }
    .h-system div { padding: 20px 0 20px 24px; border-bottom: 1px solid var(--h-line); }
    .h-system div:nth-last-child(-n + 2) { border-bottom: 0; }
    .h-system b { display: block; }
    .h-system span { color: var(--h-text-2); font-size: 15px; }
    .h-boxform { background: #FFFFFF; border: 1px solid #D5D2E0; padding: 28px; display: flex; flex-direction: column; gap: 14px; }
    .h-boxform h2, .h-boxform h3 { margin: 0; font-size: 21px; line-height: 1.5; }
    .h-boxform p { margin: 0; color: var(--h-text-2); font-size: 15px; }
    .h-field { display: flex; flex-direction: column; gap: 6px; font-size: 14px; font-weight: 600; }
    .h-field input, .h-field select, .h-field textarea { height: 48px; border: 1px solid #C9C5D6; background: #FFFFFF; font: inherit; font-size: 15px; font-weight: 400; color: var(--h-ink); padding: 0 12px; width: 100%; }
    .h-field textarea { height: auto; min-height: 96px; padding: 10px 12px; resize: vertical; }

    /* المحاضرون */
    .h-faculty { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 24px; }
    .h-person { display: flex; flex-direction: column; gap: 6px; color: var(--h-ink); text-decoration: none; }
    .h-person-photo { aspect-ratio: 4 / 5; background: #DCD8E6; overflow: hidden; display: flex; align-items: center; justify-content: center; margin-bottom: 6px; }
    .h-person-photo img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .h-person-photo span { font-family: 'Noto Kufi Arabic', sans-serif; font-size: 44px; font-weight: 700; color: var(--h-brand); }
    .h-person b { font-size: 18px; }
    .h-person small { font-size: 14px; color: var(--h-text-2); }

    /* الرسوم */
    .h-fees { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); border: 1px solid var(--h-line-dark); }
    .h-fees div { padding: 24px; border-inline-start: 1px solid var(--h-line-dark); display: flex; flex-direction: column; gap: 6px; }
    .h-fees small { font-size: 13px; color: var(--h-on-dark-2); }
    .h-fees b { font-family: 'Noto Kufi Arabic', sans-serif; font-size: 23px; }
    .h-fees span { font-size: 14px; color: var(--h-on-dark); }

    /* الشركات */
    .h-business { background: var(--h-brand); color: #FFFFFF; padding: 64px 0; }
    .h-business .h-wrap { display: flex; flex-wrap: wrap; gap: 28px; align-items: center; justify-content: space-between; }
    .h-business p { margin: 8px 0 0; color: #E4E0F2; }

    /* الأسئلة */
    .h-faq { border-top: 1px solid var(--h-line); }
    .h-faq details { border-bottom: 1px solid var(--h-line); }
    .h-faq summary { list-style: none; cursor: pointer; display: flex; justify-content: space-between; align-items: center; gap: 16px; min-height: 58px; padding: 14px 0; font-weight: 600; }
    .h-faq summary::-webkit-details-marker { display: none; }
    .h-faq summary::after { content: '+'; font-size: 22px; color: var(--h-brand); }
    .h-faq details[open] summary::after { content: '−'; }
    .h-faq p { margin: 0 0 16px; color: var(--h-text-2); }

    /* الفوتر */
    .h-footer { background: var(--h-ink); color: var(--h-on-dark); padding: 56px 0 28px; font-size: 14px; }
    .h-footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 28px; }
    .h-footer nav { display: flex; flex-direction: column; gap: 6px; }
    .h-footer b { color: #FFFFFF; }
    .h-footer a { color: var(--h-on-dark); text-decoration: none; }
    .h-footer a:hover { color: #FFFFFF; text-decoration: underline; }
    .h-footer-bottom { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 12px; border-top: 1px solid var(--h-line-dark); margin-top: 36px; padding-top: 18px; font-size: 13px; }

    /* شريط الإجراء الثابت على الموبايل */
    .h-mobile-bar { display: none; }

    @media (max-width: 960px) {
        .h-nav, .h-header-cta, .h-hide-sm { display: none; }
        .h-menu-btn { display: inline-flex; }
        .h-hero { flex-direction: column-reverse; flex-wrap: nowrap; }
        .h-stats .h-wrap { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .h-stat { padding: 18px 14px; }
        .h-stat b { font-size: 24px; }
        .h-hero-copy { padding: 32px 0 40px; }
        .h-hero-inner { padding: 0 20px; margin: 0; }
        .h-hero h1 { font-size: 30px; line-height: 1.45; }
        .h-hero p { font-size: 16px; }
        .h-photo { flex: none; min-height: 0; aspect-ratio: 3 / 2; }
        /* على الموبايل الصورة فوق، فالطرف اللي تحت هو اللي بيذوب في الخلفية */
        .h-photo::before { background: linear-gradient(to top, var(--h-ink) 0%, rgba(23, 20, 42, .6) 12%, rgba(23, 20, 42, 0) 40%); }
        .h-photo::after { background:
            radial-gradient(60% 55% at 0% 0%, rgba(126, 104, 214, .5) 0%, rgba(126, 104, 214, 0) 72%),
            radial-gradient(55% 50% at 100% 0%, rgba(242, 182, 48, .38) 0%, rgba(242, 182, 48, 0) 70%),
            radial-gradient(60% 40% at 50% 100%, rgba(126, 104, 214, .4) 0%, rgba(126, 104, 214, 0) 70%); }
        .h-photo figcaption { display: none; }
        .h-section { padding: 56px 0; }
        .h-h2 { font-size: 24px; }
        .h-wrap { padding: 0 16px; }
        .h-flagship { padding: 22px; }
        .h-system { grid-template-columns: minmax(0, 1fr); }
        .h-system div:nth-last-child(-n + 2) { border-bottom: 1px solid var(--h-line); }
        .h-system div:last-child { border-bottom: 0; }
        .h-filters { gap: 14px; }
        .h-filter-group { flex-wrap: nowrap; overflow-x: auto; margin: 0 -16px; padding: 0 16px 4px; width: calc(100% + 32px); }
        .h-chip { flex: none; white-space: nowrap; }
        .h-mobile-bar { display: flex; position: fixed; inset-inline: 0; bottom: 0; z-index: 50; gap: 8px; padding: 10px 12px calc(10px + env(safe-area-inset-bottom)); background: #FFFFFF; border-top: 1px solid #D5D2E0; }
        .h-mobile-bar .h-btn { min-height: 48px; }
        .h-mobile-bar .h-btn-primary { flex: 1; }
        body { padding-bottom: 76px; }
    }
    @media (prefers-reduced-motion: reduce) { html { scroll-behavior: auto; } }
</style>
