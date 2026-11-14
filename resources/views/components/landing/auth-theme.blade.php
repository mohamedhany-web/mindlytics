{{-- Auth form styles on top of --lp-* (public-site) --}}
<style>
    .lp-auth-body {
        margin: 0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        background: var(--lp-cream);
        color: var(--lp-primary);
        font-family: 'Tajawal', 'Cairo', sans-serif;
    }
    .lp-auth-main {
        flex: 1 0 auto;
        width: 100%;
        background: var(--lp-secondary);
    }
    .lp-auth {
        width: 100%;
        max-width: calc(var(--lp-max) + 60px);
        margin-inline: auto;
        padding: 2.5rem 1rem 4rem;
    }
    @media (min-width: 640px) {
        .lp-auth { padding-inline: 1.5rem; }
    }
    @media (min-width: 1280px) {
        .lp-auth { max-width: var(--lp-max); padding-inline: 0; padding-block: 3.5rem 5rem; }
    }
    .lp-auth__grid {
        display: grid;
        gap: 1.75rem;
        align-items: stretch;
    }
    @media (min-width: 1024px) {
        .lp-auth__grid {
            grid-template-columns: 1fr 1.05fr;
            gap: 2rem;
            min-height: 560px;
        }
    }
    .lp-auth__panel {
        background: #fff;
        border-radius: var(--lp-radius);
        padding: 2rem 1.5rem;
        box-shadow: 0 10px 40px rgba(63, 51, 86, 0.06);
    }
    @media (min-width: 640px) {
        .lp-auth__panel { padding: 2.5rem 2.25rem; }
    }
    .lp-auth__visual {
        border-radius: var(--lp-radius);
        overflow: hidden;
        position: relative;
        min-height: 220px;
        background: var(--lp-grey);
        display: flex;
        align-items: flex-end;
    }
    @media (min-width: 1024px) {
        .lp-auth__visual { min-height: 100%; }
    }
    .lp-auth__visual img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .lp-auth__visual-overlay {
        position: relative;
        z-index: 1;
        width: 100%;
        padding: 1.75rem 1.5rem;
        background: linear-gradient(180deg, transparent 0%, rgba(63, 51, 86, 0.72) 100%);
        color: #fff;
    }
    .lp-auth__visual-overlay h2 {
        margin: 0 0 0.5rem;
        font-size: clamp(1.35rem, 3vw, 1.85rem);
        font-weight: 800;
        line-height: 1.25;
    }
    .lp-auth__visual-overlay p {
        margin: 0;
        font-size: 0.95rem;
        line-height: 1.55;
        color: rgba(255, 255, 255, 0.9);
    }
    .lp-auth__eyebrow {
        margin: 0 0 0.75rem;
    }
    .lp-auth__title {
        margin: 0 0 0.5rem;
        font-weight: 800;
        font-size: clamp(1.6rem, 3vw, 2rem);
        color: var(--lp-primary);
        line-height: 1.2;
    }
    .lp-auth__lead {
        margin: 0 0 1.75rem;
        color: var(--lp-text);
        font-size: 1rem;
        line-height: 1.6;
    }
    .lp-auth__label {
        display: block;
        margin-bottom: 0.45rem;
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--lp-primary);
    }
    .lp-auth__input {
        width: 100%;
        background: var(--lp-grey);
        border: 1px solid transparent;
        border-radius: var(--lp-radius);
        padding: 0.9rem 1rem;
        font-size: 1rem;
        color: var(--lp-primary);
        font-family: inherit;
        transition: border-color 0.15s, background 0.15s, box-shadow 0.15s;
    }
    .lp-auth__input:hover { background: #eee; }
    .lp-auth__input:focus {
        outline: none;
        background: #fff;
        border-color: var(--lp-blue);
        box-shadow: 0 0 0 3px rgba(155, 126, 201, 0.15);
    }
    .lp-auth__input.is-error {
        border-color: #ef4444;
    }
    .lp-auth__field { margin-bottom: 1.1rem; }
    .lp-auth__error {
        margin: 0.4rem 0 0;
        font-size: 0.75rem;
        color: #dc2626;
        font-weight: 600;
    }
    .lp-auth__hint {
        margin: 0.4rem 0 0;
        font-size: 0.75rem;
        color: var(--lp-text);
        font-weight: 500;
        line-height: 1.45;
    }
    [x-cloak] { display: none !important; }
    .lp-auth__phone {
        display: flex;
        border-radius: var(--lp-radius);
        overflow: hidden;
        background: var(--lp-grey);
        border: 1px solid transparent;
    }
    .lp-auth__phone:focus-within {
        background: #fff;
        border-color: var(--lp-blue);
        box-shadow: 0 0 0 3px rgba(155, 126, 201, 0.15);
    }
    .lp-auth__phone select,
    .lp-auth__phone input {
        border: 0;
        background: transparent;
        box-shadow: none;
        border-radius: 0;
    }
    .lp-auth__phone select {
        max-width: 9.5rem;
        border-inline-end: 1px solid rgba(63, 51, 86, 0.08);
        padding-inline: 0.65rem;
        font-size: 0.875rem;
    }
    .lp-auth__phone input { flex: 1; min-width: 0; }
    .lp-auth__row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
        font-size: 0.875rem;
    }
    .lp-auth__check {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--lp-text);
        cursor: pointer;
    }
    .lp-auth__check input {
        width: 1rem;
        height: 1rem;
        accent-color: var(--lp-blue);
    }
    .lp-auth__link {
        color: var(--lp-blue);
        font-weight: 700;
        text-decoration: underline;
    }
    .lp-auth__link:hover { color: var(--lp-blue-hover); }
    .lp-auth__submit {
        display: inline-flex;
        width: 100%;
        align-items: center;
        justify-content: center;
        background: var(--lp-blue);
        color: #fff;
        font-weight: 700;
        font-size: 0.95rem;
        padding: 1rem 1.5rem;
        border: 0;
        border-radius: var(--lp-radius-pill);
        cursor: pointer;
        transition: filter 0.15s, transform 0.15s;
    }
    .lp-auth__submit:hover {
        filter: brightness(1.05);
        transform: translateY(-1px);
        color: #fff;
    }
    .lp-auth__foot {
        margin-top: 1.5rem;
        text-align: center;
        font-size: 0.9rem;
        color: var(--lp-text);
    }
    .lp-auth__alert {
        margin-bottom: 1rem;
        padding: 0.85rem 1rem;
        border-radius: var(--lp-radius);
        font-size: 0.875rem;
        font-weight: 600;
    }
    .lp-auth__alert--ok {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    .lp-auth__alert--info {
        background: var(--lp-secondary);
        color: var(--lp-primary);
        border: 1px solid rgba(155, 126, 201, 0.15);
    }
    .lp-auth__alert--warn {
        background: var(--lp-cream);
        color: var(--lp-primary);
        border: 1px solid rgba(155, 126, 201, 0.28);
    }
    .lp-auth__grid-fields {
        display: grid;
        gap: 0 1rem;
    }
    @media (min-width: 768px) {
        .lp-auth__grid-fields {
            grid-template-columns: 1fr 1fr;
        }
        .lp-auth__grid-fields .lp-auth__span-2 {
            grid-column: 1 / -1;
        }
    }
    .lp-auth__toggle {
        position: absolute;
        inset-inline-end: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        border: 0;
        background: transparent;
        color: var(--lp-text);
        cursor: pointer;
        padding: 0.25rem;
    }
    .lp-auth__pass-wrap { position: relative; }
    .lp-auth__pass-wrap .lp-auth__input { padding-inline-end: 2.75rem; }
</style>
