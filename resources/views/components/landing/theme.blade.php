{{-- Public site tokens — Mindlytics brand: light purple + cream (labani) --}}
<style>
    :root {
        /* Ink */
        --lp-primary: #3f3356;
        --lp-text: #8a8199;
        /* Surfaces — cool light blue + soft purple (no warm yellow/beige) */
        --lp-white: #ffffff;
        --lp-cream: #eef2f9;
        --lp-secondary: #ebe6f6;
        --lp-grey: #e8ecf5;
        /* Accents (light purple family — legacy names kept for components) */
        --lp-yellow: #c9b0eb;
        --lp-yellow-ink: #3f3356;
        --lp-blue: #7e8fd4;
        --lp-blue-hover: #6a7bc4;
        --lp-green: #8b7cc8;
        --lp-green-soft: #ebe6f6;
        --lp-orange: #a894d4;
        --lp-peach: #dde3f5;
        /* Stages / heroes (light — never near-black) */
        --lp-stage: #e4e0f2;
        --lp-stage-ink: #3f3356;
        --lp-stage-mute: rgba(63, 51, 86, 0.72);
        --lp-radius: 12px;
        --lp-radius-sm: 8px;
        --lp-radius-pill: 40px;
        --lp-max: 1140px;
        --lp-canvas: 1440px;
    }

    .lp-promo {
        background: var(--lp-cream);
        min-height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 12px 16px;
        text-align: center;
        border-bottom: 1px solid rgba(63, 51, 86, 0.06);
    }
    .lp-promo p {
        margin: 0;
        font-size: 12px;
        line-height: 16px;
        color: var(--lp-primary);
    }
    .lp-promo a {
        color: var(--lp-blue-hover);
        text-decoration: underline;
        font-weight: 600;
    }
    .lp-promo img {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }

    .lp-section {
        position: relative;
        width: 100%;
        overflow: hidden;
    }

    .lp-container {
        width: 100%;
        max-width: calc(var(--lp-max) + 60px);
        margin-inline: auto;
        padding-inline: 1rem;
    }
    @media (min-width: 640px) {
        .lp-container { padding-inline: 1.5rem; }
    }
    @media (min-width: 1280px) {
        .lp-container {
            max-width: var(--lp-max);
            padding-inline: 0;
        }
    }

    .lp-eyebrow {
        font-weight: 600;
        font-size: 17px;
        letter-spacing: 1.36px;
        text-transform: uppercase;
        color: var(--lp-green);
        margin: 0;
        line-height: 1;
    }

    .lp-h1 {
        font-weight: 700;
        font-size: clamp(2rem, 4.5vw, 48px);
        line-height: 1.15;
        color: var(--lp-primary);
        margin: 0;
    }

    .lp-display {
        font-weight: 800;
        font-size: clamp(2.5rem, 6.5vw, 76px);
        line-height: 1.05;
        color: var(--lp-primary);
        margin: 0;
    }

    .lp-lead {
        font-size: 16px;
        line-height: 26px;
        color: var(--lp-text);
        margin: 0;
    }

    .lp-btn-yellow {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--lp-yellow);
        color: var(--lp-yellow-ink);
        font-weight: 600;
        font-size: 14px;
        line-height: 1;
        padding: 24px 40px;
        border-radius: var(--lp-radius-pill);
        text-decoration: none;
        border: 0;
        transition: filter 0.15s, transform 0.15s;
        white-space: nowrap;
    }
    .lp-btn-yellow:hover {
        filter: brightness(0.96);
        color: var(--lp-yellow-ink);
        transform: translateY(-1px);
    }

    .lp-link-underline {
        font-weight: 600;
        font-size: 14px;
        line-height: 1;
        color: var(--lp-primary);
        text-decoration: underline;
        white-space: nowrap;
    }

    .lp-peach {
        background: var(--lp-peach);
    }

    .lp-media {
        background: var(--lp-secondary);
        overflow: hidden;
        border-radius: var(--lp-radius);
        position: relative;
    }
    .lp-media--peach {
        background: var(--lp-peach);
    }
    .lp-media--grey {
        background: var(--lp-grey);
    }

    /* ——— Hero (Figma 5:105) ——— */
    .lp-hero {
        background: var(--lp-cream);
        padding-block: 3rem 2.5rem;
    }
    @media (min-width: 1280px) {
        .lp-hero {
            padding-block: 0;
            min-height: 780px;
        }
        .lp-hero__stage {
            position: relative;
            width: 1220px;
            max-width: 100%;
            height: 539px;
            margin: 120px auto 0;
        }
        .lp-hero__mobile { display: none !important; }
        .lp-hero__desktop { display: block; }
    }
    @media (max-width: 1279px) {
        .lp-hero__desktop { display: none !important; }
        .lp-hero__mobile { display: block; }
    }

    .lp-hero__title {
        position: absolute;
        left: 213px;
        top: 0;
        width: 794px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 24px;
        text-align: center;
    }
    [dir="rtl"] .lp-hero__title {
        left: auto;
        right: 213px;
    }
    .lp-hero__title-inner {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }
    .lp-hero__lead {
        width: 604px;
        max-width: 100%;
    }
    .lp-hero__cta {
        position: absolute;
        left: 475px;
        top: 356px;
        display: flex;
        align-items: center;
        gap: 32px;
    }
    [dir="rtl"] .lp-hero__cta {
        left: auto;
        right: 475px;
        flex-direction: row-reverse;
    }
    .lp-hero__box1 {
        position: absolute;
        left: 28px;
        top: 82px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
    }
    [dir="rtl"] .lp-hero__box1 {
        left: auto;
        right: 28px;
        align-items: flex-end;
    }
    .lp-hero__video {
        width: 158px;
        height: 158px;
        border-radius: 12px;
        background: var(--lp-secondary);
        overflow: hidden;
        position: relative;
    }
    .lp-hero__video-fill {
        position: absolute;
        inset: -5px -44px auto;
        width: 253px;
        height: 177px;
        background: var(--lp-peach);
    }
    .lp-hero__media-img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .lp-hero__play {
        position: absolute;
        left: 50%;
        top: 50%;
        width: 36px;
        height: 36px;
        transform: translate(-50%, -50%);
        z-index: 2;
        filter: drop-shadow(0 4px 10px rgba(63, 51, 86, 0.25));
    }
    .lp-hero__dots {
        position: absolute;
        left: 0;
        top: 63px;
        width: 100px;
        height: 100px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    [dir="rtl"] .lp-hero__dots {
        left: auto;
        right: 0;
    }
    .lp-hero__dots img {
        width: 100px;
        height: 9px;
        display: block;
    }
    .lp-hero__testimonial {
        position: absolute;
        left: 29px;
        top: 367px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
        max-width: 281px;
    }
    [dir="rtl"] .lp-hero__testimonial {
        left: auto;
        right: 29px;
        align-items: flex-end;
        text-align: end;
    }
    .lp-hero__stars {
        width: 128px;
        height: 24px;
        display: block;
    }
    .lp-hero__avatar {
        width: 48px;
        height: 48px;
        border-radius: 40px;
        background: var(--lp-secondary);
        overflow: hidden;
        position: relative;
        flex-shrink: 0;
    }
    .lp-hero__avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .lp-hero__avatar-fill {
        position: absolute;
        left: -19px;
        top: -2px;
        width: 85px;
        height: 56px;
        background: var(--lp-peach);
    }
    .lp-hero__box2 {
        position: absolute;
        left: 1008px;
        top: 240px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        align-items: flex-end;
    }
    [dir="rtl"] .lp-hero__box2 {
        left: auto;
        right: 1008px;
        align-items: flex-start;
    }
    .lp-hero__instructor-img {
        width: 212px;
        height: 244px;
        border-radius: 12px;
        background: var(--lp-secondary);
        overflow: hidden;
        position: relative;
    }
    .lp-hero__instructor-fill {
        position: absolute;
        left: -113px;
        top: -20px;
        width: 424px;
        height: 283px;
        background: var(--lp-peach);
    }
    .lp-hero__arrow {
        position: absolute;
        left: 1027px;
        top: 90px;
        width: 96px;
        height: 90px;
    }
    [dir="rtl"] .lp-hero__arrow {
        left: auto;
        right: 1027px;
        transform: scaleX(-1);
    }
    .lp-hero__scroll {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 2.5rem;
        padding-bottom: 1.5rem;
    }
    @media (min-width: 1280px) {
        .lp-hero__scroll {
            position: absolute;
            left: 50%;
            top: 723px;
            transform: translateX(-50%);
            margin: 0;
            padding: 0;
        }
    }
    .lp-hero__scroll span {
        font-weight: 600;
        font-size: 16px;
        letter-spacing: 0.32px;
        color: var(--lp-primary);
    }

    /* ——— Stats (9:500) ——— */
    .lp-stats {
        background: var(--lp-blue-hover);
        min-height: 220px;
        display: flex;
        align-items: center;
    }
    .lp-stats__row {
        width: 100%;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
    }
    .lp-stats__item {
        flex: 1 1 140px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
    }
    .lp-stats__num {
        font-weight: 800;
        font-size: clamp(2.5rem, 5vw, 64px);
        line-height: 1;
        color: #fff;
        margin: 0;
    }
    .lp-stats__label {
        font-weight: 700;
        font-size: 18px;
        color: var(--lp-cream);
        margin: 0;
    }
    .lp-stats__div {
        display: none;
        width: 0;
        height: 90px;
        align-items: center;
        justify-content: center;
    }
    @media (min-width: 1024px) {
        .lp-stats__div { display: flex; }
        .lp-stats__div img {
            width: 90px;
            height: 8px;
            transform: rotate(90deg);
        }
    }

    /* ——— Who (9:521) ——— */
    .lp-who {
        background: var(--lp-cream);
        padding-block: 96px;
    }
    .lp-who__top {
        display: grid;
        gap: 2.5rem;
        margin-bottom: 3rem;
    }
    @media (min-width: 1024px) {
        .lp-who__top {
            grid-template-columns: 483px 1fr;
            gap: 77px;
            align-items: start;
        }
    }
    .lp-who__copy {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .lp-who__heading {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .lp-who__bullets {
        margin: 0;
        padding-inline-start: 1.15rem;
        max-width: 483px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        color: var(--lp-text);
        font-size: 16px;
        line-height: 26px;
    }
    .lp-who__aside {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .lp-who__photo {
        border-radius: 16px;
        overflow: hidden;
        background: var(--lp-grey);
        aspect-ratio: 16 / 10;
        max-height: 280px;
    }
    .lp-who__photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .lp-who__video {
        background: var(--lp-grey);
        border-radius: 16px;
        min-height: 266px;
        padding: 32px 44px;
        position: relative;
    }
    .lp-who__video-head {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 23px;
        font-weight: 700;
        font-size: 20px;
        color: var(--lp-primary);
    }
    .lp-who__row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .lp-who__row--active {
        background: var(--lp-blue-hover);
        color: #fff;
    }
    .lp-who__row--locked {
        background: #fff;
        color: var(--lp-text);
    }
    .lp-who__row-left {
        display: flex;
        align-items: center;
        gap: 21px;
    }
    .lp-who__lock-wrap {
        background: var(--lp-secondary);
        border-radius: 100px;
        padding: 10px;
        display: inline-flex;
    }
    .lp-who__cards {
        display: grid;
        gap: 1.25rem;
    }
    @media (min-width: 1024px) {
        .lp-who__cards {
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
    }
    .lp-who__card {
        background: var(--lp-secondary);
        border-radius: 12px;
        padding: 36px 40px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .lp-who__icon {
        width: 60px;
        height: 60px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .lp-who__icon img {
        width: 24px;
        height: 24px;
    }
    .lp-who__card h3 {
        margin: 0;
        font-weight: 700;
        font-size: 24px;
        color: var(--lp-primary);
    }

    /* ——— Courses (17:651) ——— */
    .lp-courses {
        background: var(--lp-secondary);
        padding-block: 96px;
    }
    .lp-courses__head {
        text-align: center;
        max-width: 676px;
        margin: 0 auto 3rem;
        display: flex;
        flex-direction: column;
        gap: 24px;
        align-items: center;
    }
    .lp-courses__heading {
        display: flex;
        flex-direction: column;
        gap: 20px;
        align-items: center;
    }
    .lp-courses__grid {
        display: grid;
        gap: 24px;
        grid-template-columns: 1fr;
    }
    @media (min-width: 768px) {
        .lp-courses__grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (min-width: 1100px) {
        .lp-courses__grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 24px 20px;
        }
    }
    .lp-course-card {
        background: #fff;
        border-radius: 12px;
        padding: 16px 16px 20px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        overflow: hidden;
        height: 100%;
        text-decoration: none;
        color: inherit;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .lp-course-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(63, 51, 86, 0.08);
    }
    .lp-course-card__img {
        height: 226px;
        border-radius: 8px;
        background: var(--lp-peach);
        overflow: hidden;
        position: relative;
    }
    .lp-course-card__img img.lp-course-card__thumb {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        z-index: 1;
    }
    .lp-course-card__img img.lp-course-card__thumb.is-broken {
        display: none;
    }
    .lp-course-card__placeholder {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 3rem;
        color: rgba(63, 51, 86, 0.28);
        background:
            radial-gradient(circle at 30% 20%, rgba(201, 176, 235, 0.5), transparent 55%),
            linear-gradient(160deg, var(--lp-secondary), var(--lp-peach));
        z-index: 0;
    }
    .lp-course-card__placeholder.is-hidden {
        display: none;
    }
    .lp-course-card__body {
        display: flex;
        flex-direction: column;
        gap: 20px;
        padding-inline: 8px;
        flex: 1;
    }
    .lp-course-card__meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .lp-course-card__badge {
        background: var(--lp-green-soft);
        color: var(--lp-green);
        font-weight: 600;
        font-size: 14px;
        line-height: 1;
        padding: 10px 14px;
        border-radius: 30px;
        white-space: nowrap;
        max-width: 60%;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .lp-course-card__rating {
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--lp-text);
        font-size: 14px;
        font-weight: 600;
        white-space: nowrap;
    }
    .lp-course-card__rating img {
        width: 12px;
        height: 12px;
    }
    .lp-course-card__rating small {
        font-weight: 400;
        font-size: 12px;
        line-height: 16px;
    }
    .lp-course-card__title {
        margin: 0;
        font-weight: 700;
        font-size: 18px;
        line-height: 1.35;
        color: var(--lp-primary);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.7em;
    }
    .lp-course-card__stats {
        display: flex;
        flex-wrap: wrap;
        gap: 16px 32px;
        color: var(--lp-text);
        font-size: 16px;
        line-height: 26px;
    }
    .lp-course-card__stats span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .lp-course-card__stats img {
        width: 16px;
        height: 16px;
    }
    .lp-course-card__foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: auto;
    }
    .lp-course-card__teacher {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }
    .lp-course-card__avatar {
        width: 46px;
        height: 46px;
        border-radius: 100px;
        background: var(--lp-peach);
        overflow: hidden;
        flex-shrink: 0;
    }
    .lp-course-card__avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .lp-course-card__teacher strong {
        font-weight: 700;
        font-size: 16px;
        color: var(--lp-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .lp-course-card__price {
        font-weight: 700;
        font-size: 18px;
        color: var(--lp-blue);
        white-space: nowrap;
    }
    .lp-path-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        text-decoration: none;
        color: inherit;
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(126, 143, 212, 0.14);
        border-radius: var(--lp-radius);
        overflow: hidden;
        box-shadow: 0 10px 28px rgba(63, 51, 86, 0.04);
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }
    .lp-path-card:hover {
        transform: translateY(-4px);
        border-color: rgba(126, 143, 212, 0.35);
        box-shadow: 0 16px 36px rgba(63, 51, 86, 0.1);
    }
    .lp-path-card__media {
        position: relative;
        height: 200px;
        background:
            radial-gradient(circle at 28% 20%, rgba(201, 176, 235, 0.55), transparent 55%),
            linear-gradient(160deg, var(--lp-secondary), var(--lp-peach));
        overflow: hidden;
    }
    .lp-path-card__media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .lp-path-card__media.is-empty .lp-path-card__initial,
    .lp-path-card__initial:not([hidden]) {
        display: flex;
    }
    .lp-path-card__initial {
        display: none;
        position: absolute;
        inset: 0;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 3rem;
        color: rgba(63, 51, 86, 0.28);
    }
    .lp-path-card__step {
        position: absolute;
        inset-inline-start: 0.85rem;
        top: 0.85rem;
        z-index: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.25rem;
        height: 2.25rem;
        padding: 0 0.55rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(126, 143, 212, 0.28);
        color: var(--lp-blue-hover);
        font-size: 0.8rem;
        font-weight: 800;
    }
    .lp-path-card__body {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
        flex: 1;
        padding: 1.15rem 1.15rem 1.25rem;
    }
    .lp-path-card__title {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 800;
        line-height: 1.35;
        color: var(--lp-primary);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .lp-path-card__desc {
        margin: 0;
        color: var(--lp-text);
        font-size: 0.9rem;
        line-height: 1.55;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex: 1;
    }
    .lp-path-card__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem 0.9rem;
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--lp-text);
    }
    .lp-path-card__meta strong { color: var(--lp-primary); }
    .lp-path-card__foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-top: 0.25rem;
        padding-top: 0.85rem;
        border-top: 1px solid rgba(126, 143, 212, 0.14);
    }
    .lp-path-card__price {
        font-weight: 800;
        font-size: 1.05rem;
        color: var(--lp-blue-hover);
    }
    .lp-path-card__cta {
        font-size: 0.8rem;
        font-weight: 800;
        color: var(--lp-primary);
        text-decoration: underline;
        text-underline-offset: 3px;
    }
    .lp-paths__grid {
        display: grid;
        gap: 1.25rem;
        grid-template-columns: 1fr;
    }
    @media (min-width: 640px) {
        .lp-paths__grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (min-width: 1024px) {
        .lp-paths__grid { grid-template-columns: repeat(3, 1fr); }
    }
    .lp-courses__cta {
        text-align: center;
        margin-top: 3rem;
    }

    /* ——— Why (25:1068) ——— */
    .lp-why {
        background: var(--lp-cream);
        padding-block: 96px;
    }
    .lp-why__grid {
        display: grid;
        gap: 2.5rem;
    }
    @media (min-width: 1024px) {
        .lp-why__grid {
            grid-template-columns: 463px 1fr;
            gap: 77px;
            align-items: start;
        }
    }
    .lp-why__visual {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .lp-why__photo {
        height: 510px;
        max-height: 60vw;
        border-radius: 12px;
        background: var(--lp-peach);
        overflow: hidden;
    }
    .lp-why__photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .lp-why__note {
        background: var(--lp-grey);
        border-radius: 12px;
        min-height: 108px;
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 24px;
    }
    .lp-why__note img {
        width: 60px;
        height: 60px;
        flex-shrink: 0;
    }
    .lp-why__note p {
        margin: 0;
        font-size: 16px;
        line-height: 26px;
        color: var(--lp-primary);
    }
    .lp-why__copy {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    .lp-why__features {
        display: flex;
        flex-direction: column;
        gap: 20px;
        margin-top: 1.5rem;
    }
    .lp-why__feature-head {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .lp-why__feature-icon {
        width: 44px;
        height: 44px;
        border-radius: 30px;
        background: var(--lp-blue-hover);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .lp-why__feature-icon img {
        width: 24px;
        height: 24px;
    }
    .lp-why__feature-head strong {
        font-weight: 700;
        font-size: 18px;
        color: var(--lp-primary);
    }
    .lp-why__feature-body {
        padding-inline-start: 58px;
        margin: 0;
        font-size: 16px;
        line-height: 26px;
        color: var(--lp-text);
        max-width: 522px;
    }

    /* ——— Instructors (25:1331) ——— */
    .lp-instructors {
        background: var(--lp-secondary);
        padding-block: 96px;
    }
    .lp-instructors__head {
        text-align: center;
        max-width: 676px;
        margin: 0 auto 3rem;
        display: flex;
        flex-direction: column;
        gap: 24px;
        align-items: center;
    }
    .lp-instructors__grid {
        display: grid;
        gap: 20px;
        grid-template-columns: 1fr;
        align-items: stretch;
    }
    @media (min-width: 640px) {
        .lp-instructors__grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (min-width: 1100px) {
        .lp-instructors__grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }
    .lp-instructor-card {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        height: 389px;
        max-width: 100%;
        position: relative;
        display: flex;
        flex-direction: column;
        text-decoration: none;
        color: inherit;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .lp-instructor-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 28px rgba(63, 51, 86, 0.08);
    }
    .lp-instructor-card__photo {
        height: 270px;
        flex-shrink: 0;
        background: linear-gradient(160deg, #efe6f8 0%, var(--lp-peach) 55%, #e0d0ef 100%);
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .lp-instructor-card__photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .lp-instructor-card__initials {
        font-weight: 800;
        font-size: 42px;
        line-height: 1;
        color: rgba(63, 51, 86, 0.35);
        letter-spacing: 0.02em;
        user-select: none;
    }
    .lp-instructor-card__badge {
        position: absolute;
        inset-inline-start: 24px;
        bottom: 24px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        max-width: calc(100% - 48px);
        padding: 6px 10px;
        border-radius: 4px;
        background: rgba(255, 255, 255, 0.72);
        backdrop-filter: blur(2px);
        border: 1px solid var(--lp-secondary);
        color: var(--lp-green);
        font-size: 12px;
        line-height: 16px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .lp-instructor-card__badge img {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
    }
    .lp-instructor-card__body {
        flex: 1;
        min-height: 0;
        padding: 14px 24px 18px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .lp-instructor-card__role {
        margin: 0;
        font-size: 12px;
        line-height: 16px;
        color: var(--lp-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .lp-instructor-card__name {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
    }
    .lp-instructor-card__name strong {
        font-weight: 700;
        font-size: 22px;
        color: var(--lp-primary);
        line-height: 1.15;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        min-width: 0;
        flex: 1;
    }
    .lp-instructor-card__name img {
        width: 26px;
        height: 26px;
        flex-shrink: 0;
    }
    .lp-instructor-card__meta {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 14px;
        margin-top: auto;
        font-size: 12px;
        line-height: 16px;
        color: var(--lp-text);
        min-width: 0;
    }
    .lp-instructor-card__meta span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        min-width: 0;
    }
    .lp-instructor-card__meta img {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }
    .lp-instructor-card__skills {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        min-height: 1.5rem;
    }
    .lp-instructor-card__skills span {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.55rem;
        border-radius: 999px;
        background: var(--lp-green-soft);
        color: var(--lp-blue-hover);
        font-size: 11px;
        font-weight: 700;
        line-height: 1.2;
        max-width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .lp-instructor-card__photo.is-empty .lp-instructor-card__initials {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }
    .lp-instructors__empty {
        text-align: center;
        padding: 2rem 1rem;
    }
    .lp-instructors__cta {
        text-align: center;
        margin-top: 3rem;
    }

    /* ——— How (25:1473) ——— */
    .lp-how {
        background: var(--lp-cream);
        padding-block: 96px;
    }
    .lp-how__copy {
        max-width: 677px;
        display: flex;
        flex-direction: column;
        gap: 24px;
        margin-bottom: 3.5rem;
    }
    .lp-how__heading {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .lp-how__steps {
        display: grid;
        gap: 2rem;
    }
    @media (min-width: 1024px) {
        .lp-how__steps {
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
    }
    .lp-how__step {
        display: flex;
        flex-direction: column;
        gap: 30px;
    }
    .lp-how__numline {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .lp-how__num {
        width: 60px;
        height: 60px;
        border-radius: 100px;
        background: var(--lp-blue-hover);
        color: #fff;
        font-weight: 700;
        font-size: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .lp-how__line {
        flex: 1;
        min-width: 0;
        height: 2px;
        display: none;
    }
    @media (min-width: 1024px) {
        .lp-how__line { display: block; }
        .lp-how__line img {
            width: 100%;
            height: 2px;
            display: block;
        }
    }
    .lp-how__step h3 {
        margin: 0 0 12px;
        font-weight: 700;
        font-size: 24px;
        color: var(--lp-primary);
    }
    .lp-how__step p {
        margin: 0;
        max-width: 332px;
        font-size: 16px;
        line-height: 26px;
        color: var(--lp-text);
    }

    /* ——— Header (40:1898) ——— */
    .lp-header { position: relative; z-index: 50; background: #fff; }
    .lp-header__nav {
        border-bottom: 1px solid rgba(63, 51, 86, 0.06);
        background: #fff;
    }
    .lp-header__inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        min-height: 106px;
        padding-block: 1rem;
    }
    .lp-header__brand {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        color: var(--lp-primary);
        flex-shrink: 0;
    }
    .lp-header__logo {
        width: 32px;
        height: 32px;
        object-fit: contain;
    }
    .lp-header__name {
        font-weight: 700;
        font-size: 32px;
        line-height: 1;
    }
    @media (max-width: 640px) {
        .lp-header__name { font-size: 22px; }
        .lp-header__inner { min-height: 72px; }
    }
    .lp-header__links {
        display: none;
        align-items: center;
        gap: 20px;
        font-weight: 600;
        font-size: 16px;
        letter-spacing: 0.32px;
    }
    .lp-header__links a {
        color: var(--lp-text);
        text-decoration: none;
        white-space: nowrap;
    }
    .lp-header__links a.is-active,
    .lp-header__links a:hover {
        color: var(--lp-primary);
    }
    .lp-header__actions {
        display: none;
        align-items: center;
        gap: 22px;
    }
    .lp-header__lang,
    .lp-header__login {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        font-size: 16px;
        letter-spacing: 0.32px;
        color: var(--lp-primary);
        text-decoration: none;
    }
    .lp-header__signup {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--lp-yellow);
        color: var(--lp-yellow-ink) !important;
        font-weight: 600;
        font-size: 14px;
        line-height: 1;
        padding: 18px 32px;
        border-radius: 40px;
        text-decoration: none;
        white-space: nowrap;
    }
    .lp-header__signup:hover { filter: brightness(0.96); color: var(--lp-yellow-ink) !important; }
    .lp-header__burger {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border: 0;
        background: var(--lp-secondary);
        border-radius: 12px;
        color: var(--lp-primary);
        cursor: pointer;
        z-index: 62;
        position: relative;
    }
    .lp-header__backdrop {
        position: fixed;
        inset: 0;
        z-index: 60;
        background: rgba(63, 51, 86, 0.45);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.25s ease, visibility 0.25s ease;
    }
    .lp-header__backdrop.is-open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }
    .lp-header__drawer {
        position: fixed;
        inset-block: 0;
        inset-inline-end: 0;
        z-index: 61;
        width: min(320px, 88vw);
        display: flex;
        flex-direction: column;
        background: #fff;
        box-shadow: -12px 0 40px rgba(63, 51, 86, 0.12);
        transform: translate3d(100%, 0, 0);
        transition: transform 0.28s cubic-bezier(0.22, 1, 0.36, 1);
        pointer-events: none;
        padding: 1rem 1.1rem 1.5rem;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }
    [dir="rtl"] .lp-header__drawer {
        transform: translate3d(-100%, 0, 0);
        box-shadow: 12px 0 40px rgba(63, 51, 86, 0.12);
    }
    .lp-header__drawer.is-open {
        transform: translate3d(0, 0, 0);
        pointer-events: auto;
    }
    .lp-header__drawer-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding-bottom: 1rem;
        margin-bottom: 0.5rem;
        border-bottom: 1px solid rgba(63, 51, 86, 0.06);
    }
    .lp-header__drawer-head .lp-header__name {
        font-size: 20px;
    }
    .lp-header__drawer-close {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border: 0;
        border-radius: 12px;
        background: var(--lp-secondary);
        color: var(--lp-primary);
        cursor: pointer;
        flex-shrink: 0;
    }
    .lp-header__drawer-links {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1 1 auto;
    }
    .lp-header__drawer-links a {
        padding: 12px 14px;
        border-radius: 10px;
        color: var(--lp-primary);
        text-decoration: none;
        font-weight: 600;
        font-size: 1rem;
    }
    .lp-header__drawer-links a:hover {
        background: var(--lp-secondary);
    }
    .lp-header__drawer-foot {
        margin-top: auto;
        padding-top: 1rem;
        border-top: 1px solid rgba(63, 51, 86, 0.06);
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .lp-header__mobile-cta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }
    .lp-header__mobile-cta .lp-header__login,
    .lp-header__mobile-cta .lp-header__signup {
        flex: 1 1 auto;
        justify-content: center;
        text-align: center;
        padding-block: 14px;
    }
    @media (prefers-reduced-motion: reduce) {
        .lp-header__backdrop,
        .lp-header__drawer {
            transition: none;
        }
    }
    @media (min-width: 1100px) {
        .lp-header__links,
        .lp-header__actions { display: flex; }
        .lp-header__burger,
        .lp-header__backdrop,
        .lp-header__drawer { display: none !important; }
    }

    /* ——— Footer (40:1966) ——— */
    .lp-footer {
        background: var(--lp-grey);
        padding-top: 80px;
        margin-top: auto;
    }
    .lp-footer__grid {
        display: grid;
        gap: 2.5rem;
        padding-bottom: 64px;
    }
    @media (min-width: 768px) {
        .lp-footer__grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (min-width: 1100px) {
        .lp-footer__grid {
            grid-template-columns: 1.4fr 1fr 1fr 1fr;
            gap: 40px;
        }
    }
    .lp-footer__about {
        max-width: 280px;
        margin-bottom: 1.5rem;
    }
    .lp-footer__cta {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: var(--lp-yellow);
        color: var(--lp-yellow-ink);
        font-weight: 600;
        font-size: 14px;
        padding: 16px 28px;
        border-radius: 40px;
        text-decoration: none;
    }
    .lp-footer__cta:hover { color: var(--lp-yellow-ink); filter: brightness(0.96); }
    .lp-footer__cta img { filter: none; opacity: 0.85; }
    .lp-footer__title {
        margin: 0 0 1.25rem;
        font-weight: 700;
        font-size: 18px;
        color: var(--lp-primary);
    }
    .lp-footer__list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .lp-footer__list a {
        color: var(--lp-text);
        text-decoration: none;
        font-size: 16px;
        line-height: 26px;
    }
    .lp-footer__list a:hover { color: var(--lp-primary); }
    .lp-footer__bottom {
        border-top: 1px solid rgba(63, 51, 86, 0.08);
        padding-block: 24px;
    }
    .lp-footer__bottom-inner {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 12px;
    }
    .lp-footer__bottom p {
        margin: 0;
        font-size: 14px;
        color: var(--lp-text);
    }

    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0,0,0,0);
        border: 0;
    }

    /* ——— Motion ——— */
    .lp-reveal {
        opacity: 0;
        transform: translate3d(0, 22px, 0);
        transition:
            opacity 0.7s cubic-bezier(0.22, 1, 0.36, 1),
            transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
        transition-delay: var(--lp-delay, 0ms);
        will-change: opacity, transform;
    }
    .lp-reveal.is-in {
        opacity: 1;
        transform: none;
    }
    .lp-course-card,
    .lp-instructor-card,
    .lp-who__card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .lp-who__card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(63, 51, 86, 0.08);
    }
    .lp-btn-yellow {
        transition: filter 0.15s, transform 0.2s ease;
    }
    @keyframes lp-float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }
    @keyframes lp-float-soft {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
    @keyframes lp-bounce {
        0%, 100% { transform: translateX(-50%) translateY(0); opacity: 0.85; }
        50% { transform: translateX(-50%) translateY(6px); opacity: 1; }
    }
    @keyframes lp-draw {
        0%, 100% { opacity: 0.55; transform: translateY(0); }
        50% { opacity: 1; transform: translateY(3px); }
    }
    .lp-anim--float { animation: lp-float 5.5s ease-in-out infinite; }
    .lp-anim--float-soft { animation: lp-float-soft 4.5s ease-in-out infinite; }
    .lp-anim--bounce { animation: lp-bounce 2.2s ease-in-out infinite; }
    .lp-anim--draw { animation: lp-draw 3.2s ease-in-out infinite; }
    @media (max-width: 1279px) {
        .lp-anim--bounce { animation-name: lp-float-soft; }
        .lp-hero__scroll.lp-anim--bounce { animation: lp-float-soft 2.2s ease-in-out infinite; }
    }
    @media (prefers-reduced-motion: reduce) {
        .lp-reveal {
            opacity: 1 !important;
            transform: none !important;
            transition: none !important;
        }
        .lp-anim--float,
        .lp-anim--float-soft,
        .lp-anim--bounce,
        .lp-anim--draw {
            animation: none !important;
        }
    }
</style>
