{{-- صفحات البرامج والكورسات (الدبلومة، البرنامج التدريبي، القوائم): بتتحمّل بعد components.site.styles. docs/SITE_DESIGN.md --}}
    <style>
        .dip-body { padding: 56px 0 88px; }
        .dip-block + .dip-block { margin-top: 48px; }
        .dip-block > h2 { margin: 0 0 18px; font-family: var(--h-display); font-size: 24px; line-height: 1.5; }
        .dip-facts { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 230px), 1fr)); margin: 0; border-top: 2px solid var(--h-ink); }
        .dip-facts div { display: flex; gap: 14px; align-items: flex-start; padding: 18px 0; border-bottom: 1px solid var(--h-line); padding-inline-end: 16px; }
        .dip-facts i { width: 40px; height: 40px; flex: none; display: flex; align-items: center; justify-content: center; background: var(--h-mist); color: var(--h-brand); }
        .dip-facts dt { font-size: 13px; color: var(--h-muted); }
        .dip-facts dd { margin: 2px 0 0; font-weight: 700; overflow-wrap: anywhere; unicode-bidi: plaintext; text-align: right; }
        .dip-highlights { list-style: none; margin: 0; padding: 0; display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 300px), 1fr)); gap: 12px; }
        .dip-highlights li { display: flex; gap: 12px; align-items: flex-start; padding: 16px 18px; border: 1px solid var(--h-line); background: #FFFFFF; line-height: 1.65; }
        .dip-highlights li::before { content: ''; flex: none; width: 8px; height: 8px; margin-top: 9px; background: var(--h-accent); }
        .dip-lectures { display: flex; flex-direction: column; gap: 10px; }
        .dip-lecture { display: flex; align-items: center; gap: 14px; padding: 14px 18px; border: 1px solid var(--h-line); color: var(--h-ink); text-decoration: none; }
        .dip-lecture:hover { border-color: var(--h-brand); }
        .dip-lecture .dip-play { width: 44px; height: 44px; flex: none; display: flex; align-items: center; justify-content: center; background: var(--h-ink); color: var(--h-accent); }
        .dip-lecture b { display: block; }
        .dip-lecture small { color: var(--h-muted); font-size: 13px; }
        .dip-lecture .fa-arrow-up-right-from-square { margin-inline-start: auto; color: var(--h-muted); }
        .dip-details { color: var(--h-text-2); white-space: pre-line; margin: 0; }
        .dip-side { position: sticky; top: 100px; display: flex; flex-direction: column; gap: 16px; }
        .dip-card { border: 1px solid #D5D2E0; background: #FFFFFF; }
        .dip-price { background: var(--h-ink); color: #FFFFFF; padding: 24px 26px; background-image: radial-gradient(60% 80% at 0% 0%, rgba(126, 104, 214, .38), rgba(126, 104, 214, 0) 72%), radial-gradient(50% 70% at 100% 100%, rgba(242, 182, 48, .2), rgba(242, 182, 48, 0) 70%); }
        .dip-price small { display: block; font-size: 13px; color: var(--h-on-dark-2); }
        .dip-price b { display: block; font-family: var(--h-display); font-size: 34px; line-height: 1.4; }
        .dip-price b span { font-size: 16px; font-weight: 600; color: var(--h-on-dark); }
        .dip-price p { margin: 6px 0 0; font-size: 14px; color: var(--h-on-dark); }
        .dip-card-body { padding: 22px 26px; display: flex; flex-direction: column; gap: 12px; }
        .dip-card-body .h-link { text-align: center; font-size: 15px; }
        .dip-start { display: flex; justify-content: space-between; gap: 12px; font-size: 14px; color: var(--h-text-2); padding-bottom: 12px; border-bottom: 1px solid var(--h-line); }
        .dip-start b { color: var(--h-ink); }
        .dip-pay { padding: 20px 26px; }
        .dip-pay h3 { margin: 0 0 10px; font-size: 16px; }
        .dip-pay ul { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 8px; font-size: 15px; }
        .dip-pay li { display: flex; gap: 10px; align-items: baseline; overflow-wrap: anywhere; }
        .dip-pay li i { color: var(--h-brand); }
        .dip-mobile-bar { display: none; }
        /* كروت البرامج والكورسات في صفحات القوائم */
        .pg-list { display: flex; flex-direction: column; gap: 20px; }
        .pg-card { display: grid; grid-template-columns: minmax(0, 1fr) 280px; border: 1px solid var(--h-line); background: #FFFFFF; }
        .pg-card:hover { border-color: var(--h-brand); }
        .pg-card-main { padding: 28px; display: flex; flex-direction: column; gap: 14px; min-width: 0; }
        .pg-card-main h2, .pg-card-main h3 { margin: 0; font-family: var(--h-display); font-size: 24px; line-height: 1.45; }
        .pg-card-main h2 a, .pg-card-main h3 a { color: var(--h-ink); text-decoration: none; }
        .pg-card-main h2 a:hover, .pg-card-main h3 a:hover { color: var(--h-brand); }
        .pg-card-main > p { margin: 0; color: var(--h-text-2); }
        .pg-meta { list-style: none; margin: 0; padding: 0; display: flex; flex-wrap: wrap; gap: 8px; }
        .pg-meta li { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; padding: 4px 10px; background: var(--h-mist); color: var(--h-text-2); unicode-bidi: plaintext; }
        .pg-meta i { color: var(--h-brand); }
        .pg-card-side { border-inline-start: 1px solid var(--h-line); background: var(--h-mist); padding: 24px; display: flex; flex-direction: column; justify-content: center; gap: 10px; }
        .pg-card-side small { display: block; font-size: 13px; color: var(--h-muted); }
        .pg-card-side b { font-family: var(--h-display); font-size: 26px; line-height: 1.3; }
        .pg-card-side b span { font-size: 14px; font-weight: 600; color: var(--h-text-2); }
        .pg-card-side .h-link { text-align: center; font-size: 15px; }
        .pg-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 320px), 1fr)); gap: 16px; }
        .pg-tile { border: 1px solid var(--h-line); background: #FFFFFF; display: flex; flex-direction: column; color: var(--h-ink); text-decoration: none; }
        .pg-tile:hover { border-color: var(--h-brand); }
        .pg-tile-img { aspect-ratio: 16 / 9; background: var(--h-ink); overflow: hidden; display: flex; align-items: center; justify-content: center; color: var(--h-accent); font-family: var(--h-display); font-size: 30px; }
        .pg-tile-img img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .pg-tile-body { padding: 20px 22px; display: flex; flex-direction: column; gap: 10px; flex: 1; }
        .pg-tile-body h3 { margin: 0; font-size: 18px; line-height: 1.5; }
        .pg-tile-body p { margin: 0; color: var(--h-text-2); font-size: 15px; }
        .pg-tile-foot { margin-top: auto; padding-top: 12px; border-top: 1px solid var(--h-line); display: flex; justify-content: space-between; align-items: center; gap: 12px; }
        .pg-tile-foot b { font-family: var(--h-display); font-size: 19px; }
        .pg-help { background: var(--h-ink); color: #FFFFFF; padding: 32px; display: flex; flex-wrap: wrap; gap: 20px; align-items: center; justify-content: space-between;
            background-image: radial-gradient(50% 120% at 0% 0%, rgba(126, 104, 214, .38), rgba(126, 104, 214, 0) 72%), radial-gradient(40% 100% at 100% 100%, rgba(242, 182, 48, .2), rgba(242, 182, 48, 0) 70%); }
        .pg-help h2 { margin: 0; font-family: var(--h-display); font-size: 22px; line-height: 1.5; color: #FFFFFF; }
        .pg-help p { margin: 6px 0 0; color: var(--h-on-dark); max-width: 560px; }
        .pg-video { position: relative; aspect-ratio: 16 / 9; background: var(--h-ink); }
        .pg-video iframe, .pg-video video { position: absolute; inset: 0; width: 100%; height: 100%; border: 0; }
        .pg-curriculum { border-top: 2px solid var(--h-ink); }
        .pg-curriculum a { display: flex; align-items: center; gap: 16px; padding: 16px 0; border-bottom: 1px solid var(--h-line); color: var(--h-ink); text-decoration: none; }
        .pg-curriculum a:hover b { color: var(--h-brand); }
        .pg-curriculum .pg-num { width: 36px; height: 36px; flex: none; display: flex; align-items: center; justify-content: center; background: var(--h-mist); font-weight: 700; color: var(--h-brand); }
        .pg-curriculum small { display: block; color: var(--h-muted); font-size: 13px; }
        .pg-curriculum .pg-price { margin-inline-start: auto; font-weight: 700; white-space: nowrap; }
        .pg-review { border-bottom: 1px solid var(--h-line); padding: 16px 0; }
        .pg-review header { display: flex; justify-content: space-between; gap: 12px; font-weight: 700; }
        .pg-review p { margin: 6px 0 0; color: var(--h-text-2); white-space: pre-line; }
        .pg-stars { color: var(--h-accent); letter-spacing: 2px; }
        .pg-note { padding: 14px 16px; background: var(--h-mist); border: 1px solid var(--h-line); font-size: 15px; color: var(--h-text-2); }
        .pg-note.is-good { background: #ECF8F1; border-color: #B7E2C8; color: var(--h-good); font-weight: 600; }
        .pg-note.is-bad { background: #FDECEA; border-color: #F5C2BC; color: var(--h-bad); }
        @media (max-width: 960px) {
            .dip-body { padding: 32px 0 48px; }
            .dip-side { position: static; }
            .dip-block + .dip-block { margin-top: 36px; }
            .dip-block > h2 { font-size: 21px; }
            .dip-facts { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .dip-facts div { flex-direction: column; gap: 8px; padding: 14px 0; padding-inline-end: 12px; }
            .dip-facts i { width: 32px; height: 32px; font-size: 13px; }
            .dip-facts dd { font-size: 15px; }
            .dip-mobile-bar { display: flex; align-items: center; gap: 12px; position: fixed; inset-inline: 0; bottom: 0; z-index: 50; padding: 10px 16px calc(10px + env(safe-area-inset-bottom)); background: #FFFFFF; border-top: 1px solid #D5D2E0; }
            .dip-mobile-bar div { display: flex; flex-direction: column; line-height: 1.3; }
            .dip-mobile-bar small { font-size: 12px; color: var(--h-muted); }
            .dip-mobile-bar b { font-family: var(--h-display); font-size: 18px; }
            .dip-mobile-bar .h-btn { flex: 1; }
            body.h-has-mobile-bar { padding-bottom: 76px; }
            .pg-card { grid-template-columns: minmax(0, 1fr); }
            .pg-card-main { padding: 20px; }
            .pg-card-main h2, .pg-card-main h3 { font-size: 20px; }
            .pg-card-side { border-inline-start: 0; border-top: 1px solid var(--h-line); padding: 18px 20px; flex-direction: row; flex-wrap: wrap; align-items: center; justify-content: space-between; }
            .pg-card-side .h-btn { flex: 1 1 100%; }
            .pg-help { padding: 24px 20px; }
        }
    </style>
