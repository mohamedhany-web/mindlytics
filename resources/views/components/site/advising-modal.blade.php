{{--
    بوب أب حجز جلسة الإرشاد المهني (docs/ADVISING.md). موجود في كل الصفحات العامة (بيتضمّن من components/site/footer).
    بيفتح من أي رابط ينتهي بـ #consult أو أي عنصر عليه data-advising-open (و data-advising-interest يختار المجال).
    من غير JS، الروابط بتروح لصفحة /advising.
--}}
@php
    $siteAr = app()->getLocale() === 'ar';
    $t = fn (string $ar, string $en) => $siteAr ? $ar : $en;
    $goals = \App\Domains\Advising\Models\AdvisingBooking::GOALS;
    $levels = \App\Domains\Advising\Models\AdvisingBooking::LEVELS;
    try {
        $advisingInterests = \Illuminate\Support\Facades\Cache::remember('advising.interests.'.app()->getLocale(), 600, function () {
            $paths = \App\Domains\Learning\Models\AcademicYear::query()->where('is_active', true)->orderBy('order')->limit(12)->pluck('name');
            $boards = \App\Domains\Sales\Models\SalesDiplomaBoardEntry::query()->active()->ordered()->limit(8)->pluck('name')
                ->concat(\App\Domains\Sales\Models\SalesCourseBoardEntry::query()->active()->ordered()->limit(8)->pluck('name'));

            return $boards->concat($paths)->filter()->unique()->values()->all();
        });
    } catch (\Throwable $e) {
        $advisingInterests = [];
    }
@endphp
<dialog id="h-advising" class="h-adv" aria-labelledby="h-adv-title"
        data-slots-url="{{ route('advising.slots') }}" data-store-url="{{ route('advising.store') }}"
        data-l-error="{{ $t('حصلت مشكلة، حاول تاني.', 'Something went wrong, please try again.') }}"
        data-l-pick="{{ $t('اختار ميعاد الأول.', 'Please pick a time first.') }}"
        data-l-goal="{{ $t('اختار هدفك الأول.', 'Please choose your goal first.') }}"
        data-l-sending="{{ $t('جارِ الحجز…', 'Booking…') }}">
    <form method="dialog" class="h-adv-close-row">
        <button class="h-adv-x" aria-label="{{ $t('إغلاق', 'Close') }}">
            <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M2 2L14 14M14 2L2 14" stroke="currentColor" stroke-width="2"/></svg>
        </button>
    </form>

    <div class="h-adv-head">
        <span class="h-eyebrow" style="margin: 0">{{ $t('جلسة إرشاد مهني مجانية', 'Free career guidance session') }}</span>
        <h2 id="h-adv-title">{{ $t('خلّينا نساعدك تعرف تبدأ منين', 'Let us help you find where to start') }}</h2>
        <p>{{ $t('في جلسة قصيرة مع مرشد من الأكاديمية: نفهم هدفك ومستواك، ونرشّحلك المسار المناسب، ونرسم معاك خطة دخولك لسوق العمل.', 'A short session with one of our advisors: we understand your goal and level, recommend the right path, and map your way into the job market.') }}</p>
        <ol class="h-adv-steps" aria-label="{{ $t('خطوات الحجز', 'Booking steps') }}">
            <li data-step-dot="1" class="is-on">{{ $t('هدفك', 'Your goal') }}</li>
            <li data-step-dot="2">{{ $t('الميعاد', 'Time') }}</li>
            <li data-step-dot="3">{{ $t('بياناتك', 'Your details') }}</li>
        </ol>
    </div>

    <form class="h-adv-body" data-advising-form novalidate>
        @csrf
        <div class="a-honeypot" aria-hidden="true" style="position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0)"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>

        {{-- 1) الهدف والمستوى --}}
        <section data-step="1">
            <fieldset class="h-adv-set">
                <legend>{{ $t('إيه اللي عايز توصله؟', 'What do you want to achieve?') }}</legend>
                <div class="h-adv-options">
                    @foreach($goals as $k => $l)
                        <label class="h-adv-option"><input type="radio" name="goal" value="{{ $k }}" required><span>{{ $l[$siteAr ? 0 : 1] }}</span></label>
                    @endforeach
                </div>
            </fieldset>
            <fieldset class="h-adv-set">
                <legend>{{ $t('مستواك دلوقتي', 'Your current level') }}</legend>
                <div class="h-adv-options h-adv-options-row">
                    @foreach($levels as $k => $l)
                        <label class="h-adv-option"><input type="radio" name="level" value="{{ $k }}"><span>{{ $l[$siteAr ? 0 : 1] }}</span></label>
                    @endforeach
                </div>
            </fieldset>
            <label class="h-field">{{ $t('المجال أو البرنامج اللي مهتم بيه (اختياري)', 'Field or program you are interested in (optional)') }}
                <select name="interest" data-advising-interest>
                    <option value="">{{ $t('لسه مش عارف، عايز ترشيح', 'Not sure, I want a recommendation') }}</option>
                    @foreach($advisingInterests as $name)<option value="{{ $name }}">{{ $name }}</option>@endforeach
                </select>
            </label>
            <p class="h-field-error" data-step-msg role="alert" hidden></p>
            <div class="h-adv-actions">
                <button type="button" class="h-btn h-btn-primary h-btn-lg" data-next="2">{{ $t('التالي: اختار الميعاد', 'Next: pick a time') }}</button>
            </div>
        </section>

        {{-- 2) الميعاد --}}
        <section data-step="2" hidden>
            <p class="h-adv-label">{{ $t('اختار اليوم', 'Pick a day') }}</p>
            <div class="h-adv-days" data-days role="group" aria-label="{{ $t('الأيام المتاحة', 'Available days') }}"></div>
            <p class="h-adv-label">{{ $t('اختار الساعة', 'Pick a time') }}</p>
            <div class="h-adv-times" data-times role="group" aria-label="{{ $t('المواعيد المتاحة', 'Available times') }}"></div>
            <p class="h-adv-empty" data-slots-empty hidden>{{ $t('مفيش مواعيد متاحة للحجز دلوقتي. كلّمنا على واتساب ونحدد معاك ميعاد.', 'No times are open right now. Message us on WhatsApp and we will set one with you.') }}</p>
            <p class="h-adv-empty" data-slots-loading>{{ $t('جارِ تحميل المواعيد…', 'Loading times…') }}</p>
            <input type="hidden" name="slot" value="">
            <p class="h-field-error" data-step-msg role="alert" hidden></p>
            <div class="h-adv-actions">
                <button type="button" class="h-btn h-btn-outline" data-back="1">{{ $t('رجوع', 'Back') }}</button>
                <button type="button" class="h-btn h-btn-primary h-btn-lg" data-next="3">{{ $t('التالي: بياناتك', 'Next: your details') }}</button>
            </div>
        </section>

        {{-- 3) البيانات --}}
        <section data-step="3" hidden>
            <p class="h-adv-chosen" data-chosen></p>
            <div class="h-adv-grid">
                <label class="h-field">{{ $t('الاسم', 'Name') }}<input type="text" name="name" required maxlength="120" autocomplete="name" value="{{ auth()->user()?->name }}"></label>
                <label class="h-field">{{ $t('رقم الموبايل (واتساب)', 'Mobile (WhatsApp)') }}<input type="tel" name="phone" required maxlength="40" autocomplete="tel" inputmode="tel" dir="ltr" style="text-align: {{ $siteAr ? 'right' : 'left' }}" value="{{ auth()->user()?->phone }}"></label>
            </div>
            <label class="h-field">{{ $t('البريد الإلكتروني (اختياري)', 'Email (optional)') }}<input type="email" name="email" maxlength="190" autocomplete="email" dir="ltr" style="text-align: {{ $siteAr ? 'right' : 'left' }}" value="{{ auth()->user()?->email }}"></label>
            <label class="h-field">{{ $t('احكيلنا عن وضعك (اختياري)', 'Tell us about your situation (optional)') }}
                <textarea name="notes" rows="3" maxlength="2000" placeholder="{{ $t('مثلاً: خريج تجارة وعايز أشتغل Data Analyst، عندي ساعتين في اليوم', 'e.g. business graduate aiming for a data analyst role, 2 hours a day available') }}"></textarea>
            </label>
            <p class="h-field-error" data-form-error role="alert" hidden></p>
            <div class="h-adv-actions">
                <button type="button" class="h-btn h-btn-outline" data-back="2">{{ $t('رجوع', 'Back') }}</button>
                <button type="submit" class="h-btn h-btn-primary h-btn-lg" data-submit>{{ $t('احجز الجلسة', 'Book the session') }}</button>
            </div>
        </section>

        {{-- تم --}}
        <section data-step="done" hidden>
            <div class="h-adv-done" role="status">
                <b>{{ $t('اتحجزت جلستك', 'Your session is booked') }}</b>
                <p data-done-summary></p>
                <p>{{ $t('هنكلمك على واتساب قبل الجلسة نأكّد الميعاد ونبعتلك اللينك أو العنوان. فكّر في هدفك والوقت اللي تقدر تذاكره في اليوم، عشان نطلع بخطة واضحة.', 'We will message you on WhatsApp before the session to confirm and send the link or address. Think about your goal and how much time you can study each day, so we leave with a clear plan.') }}</p>
            </div>
            <div class="h-adv-actions"><button type="button" class="h-btn h-btn-primary" data-close>{{ $t('تمام', 'Done') }}</button></div>
        </section>
    </form>
</dialog>

<style>
    .h-adv { width: min(640px, calc(100vw - 24px)); max-height: calc(100vh - 32px); padding: 0; border: 0; background: #FFFFFF; color: var(--h-ink); box-shadow: 0 24px 60px rgba(23, 20, 42, .35); overflow: auto; font-family: var(--h-font); }
    .h-adv::backdrop { background: rgba(23, 20, 42, .62); }
    .h-adv-close-row { position: sticky; top: 0; display: flex; justify-content: flex-end; padding: 10px 10px 0; z-index: 2; height: 0; overflow: visible; }
    .h-adv-x { width: 40px; height: 40px; border: 0; background: rgba(255, 255, 255, .14); color: #FFFFFF; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; }
    .h-adv-head { background: var(--h-ink); color: #FFFFFF; padding: 28px 28px 20px; background-image: radial-gradient(50% 90% at 0% 0%, rgba(126, 104, 214, .38), rgba(126, 104, 214, 0) 70%), radial-gradient(40% 80% at 100% 100%, rgba(242, 182, 48, .18), rgba(242, 182, 48, 0) 70%); }
    .h-adv-head .h-eyebrow { color: var(--h-accent); }
    .h-adv-head h2 { margin: 6px 0 8px; font-family: var(--h-display); font-size: 24px; line-height: 1.45; color: #FFFFFF; }
    .h-adv-head p { margin: 0; color: var(--h-on-dark); font-size: 15px; }
    .h-adv-steps { list-style: none; margin: 18px 0 0; padding: 0; display: flex; gap: 6px; font-size: 13px; }
    .h-adv-steps li { flex: 1; padding-top: 8px; border-top: 3px solid var(--h-line-dark); color: var(--h-on-dark-2); }
    .h-adv-steps li.is-on { border-top-color: var(--h-accent); color: #FFFFFF; font-weight: 600; }
    .h-adv-body { padding: 22px 28px 26px; }
    .h-adv-body section { display: flex; flex-direction: column; gap: 16px; }
    .h-adv-body section[hidden] { display: none; }
    .h-adv-set { border: 0; margin: 0; padding: 0; }
    .h-adv-set legend, .h-adv-label { font-weight: 700; font-size: 15px; margin: 0 0 10px; padding: 0; }
    .h-adv-options { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 240px), 1fr)); gap: 8px; }
    .h-adv-options-row { grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); }
    .h-adv-option { position: relative; display: flex; cursor: pointer; }
    .h-adv-option input { position: absolute; opacity: 0; pointer-events: none; }
    .h-adv-option span { flex: 1; border: 1px solid var(--h-field); padding: 12px 14px; font-size: 15px; min-height: 48px; display: flex; align-items: center; }
    .h-adv-option input:checked + span { border-color: var(--h-brand); box-shadow: inset 0 0 0 1px var(--h-brand); background: var(--h-mist); font-weight: 600; }
    .h-adv-option input:focus-visible + span { outline: 3px solid var(--h-accent); outline-offset: 2px; }
    .h-adv-days { display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px; }
    .h-adv-day { flex: none; min-width: 84px; min-height: 60px; border: 1px solid var(--h-field); background: #FFFFFF; font: inherit; cursor: pointer; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px; padding: 6px 10px; color: var(--h-ink); }
    .h-adv-day small { font-size: 12px; color: var(--h-muted); }
    .h-adv-day[aria-pressed="true"] { background: var(--h-ink); border-color: var(--h-ink); color: #FFFFFF; }
    .h-adv-day[aria-pressed="true"] small { color: var(--h-on-dark-2); }
    .h-adv-times { display: grid; grid-template-columns: repeat(auto-fill, minmax(118px, 1fr)); gap: 8px; }
    .h-adv-time { min-height: 52px; border: 1px solid var(--h-field); background: #FFFFFF; font: inherit; font-weight: 600; cursor: pointer; color: var(--h-ink); display: flex; flex-direction: column; align-items: center; justify-content: center; }
    .h-adv-time small { font-size: 11px; font-weight: 400; color: var(--h-muted); }
    .h-adv-time[aria-pressed="true"] { background: var(--h-brand); border-color: var(--h-brand); color: #FFFFFF; }
    .h-adv-time[aria-pressed="true"] small { color: var(--h-on-dark); }
    .h-adv-empty { margin: 0; color: var(--h-text-2); font-size: 14px; }
    .h-adv-chosen { margin: 0; padding: 12px 14px; background: var(--h-mist); border: 1px solid var(--h-line); font-weight: 600; }
    .h-adv-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; }
    .h-adv-actions { display: flex; flex-wrap: wrap; gap: 10px; justify-content: space-between; align-items: center; padding-top: 6px; }
    .h-adv-actions .h-btn-lg { flex: 1 1 220px; }
    .h-adv-done { border: 1px solid #B9DCC9; background: #E8F4EE; color: #0E4F33; padding: 18px; display: flex; flex-direction: column; gap: 8px; }
    .h-adv-done b { font-family: var(--h-display); font-size: 20px; }
    .h-adv-done p { margin: 0; }
    @media (max-width: 600px) {
        .h-adv { width: 100vw; max-width: 100vw; height: 100%; max-height: 100%; margin: 0; }
        .h-adv-head { padding: 22px 16px 16px; }
        .h-adv-head h2 { font-size: 21px; }
        .h-adv-body { padding: 18px 16px 24px; }
    }
</style>

<script>
(function () {
    var dlg = document.getElementById('h-advising');
    if (! dlg || typeof dlg.showModal !== 'function') { return; } // متصفح قديم: الروابط هتفتح صفحة /advising
    var form = dlg.querySelector('[data-advising-form]');
    var daysBox = dlg.querySelector('[data-days]');
    var timesBox = dlg.querySelector('[data-times]');
    var slotInput = form.querySelector('[name="slot"]');
    var chosen = dlg.querySelector('[data-chosen]');
    var errorBox = dlg.querySelector('[data-form-error]');
    var days = null, chosenLabel = '';

    var show = function (step) {
        form.querySelectorAll('[data-step]').forEach(function (s) { s.hidden = s.dataset.step !== String(step); });
        dlg.querySelectorAll('[data-step-dot]').forEach(function (d) { d.classList.toggle('is-on', step === 'done' || Number(d.dataset.stepDot) <= Number(step)); });
        var first = form.querySelector('[data-step="' + step + '"] input:not([type=hidden]), [data-step="' + step + '"] button');
        if (first) { first.focus({ preventScroll: true }); }
        dlg.scrollTop = 0;
    };
    var pressed = function (box, btn) { box.querySelectorAll('button').forEach(function (b) { b.setAttribute('aria-pressed', b === btn ? 'true' : 'false'); }); };
    var el = function (tag, cls, html) { var e = document.createElement(tag); if (cls) { e.className = cls; } if (html !== undefined) { e.innerHTML = html; } return e; };
    var esc = function (s) { return String(s).replace(/[&<>"']/g, function (c) { return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c]; }); };

    var renderTimes = function (day) {
        timesBox.innerHTML = '';
        day.slots.forEach(function (s) {
            var b = el('button', 'h-adv-time', esc(s.time_label) + '<small>' + esc(s.mode_label) + '</small>');
            b.type = 'button'; b.setAttribute('aria-pressed', slotInput.value === s.key ? 'true' : 'false');
            b.addEventListener('click', function () {
                slotInput.value = s.key; pressed(timesBox, b);
                chosenLabel = day.label + ' ' + day.short + ' — ' + s.time_label + ' · ' + s.mode_label + ' · ' + s.minutes + (document.documentElement.lang === 'ar' ? ' دقيقة' : ' min');
            });
            timesBox.appendChild(b);
        });
    };
    var loadSlots = function () {
        if (days) { return; }
        fetch(dlg.dataset.slotsUrl, { headers: { 'Accept': 'application/json' } }).then(function (r) { return r.json(); }).then(function (data) {
            days = data.days || [];
            dlg.querySelector('[data-slots-loading]').hidden = true;
            dlg.querySelector('[data-slots-empty]').hidden = days.length > 0;
            days.forEach(function (day, i) {
                var b = el('button', 'h-adv-day', '<span>' + esc(day.label) + '</span><small>' + esc(day.short) + '</small>');
                b.type = 'button'; b.setAttribute('aria-pressed', i === 0 ? 'true' : 'false');
                b.addEventListener('click', function () { pressed(daysBox, b); renderTimes(day); });
                daysBox.appendChild(b);
            });
            if (days.length) { renderTimes(days[0]); }
        }).catch(function () { dlg.querySelector('[data-slots-loading]').textContent = dlg.dataset.lError; });
    };

    var open = function (interest, goal) {
        if (goal) {
            var g = form.querySelector('[name="goal"][value="' + goal + '"]');
            if (g) { g.checked = true; }
        }
        if (interest) {
            var sel = form.querySelector('[data-advising-interest]');
            Array.prototype.forEach.call(sel.options, function (o) { if (o.value === interest) { sel.value = interest; } });
        }
        if (! dlg.open) { dlg.showModal(); }
        show(form.dataset.done ? 'done' : 1);
        loadSlots();
    };
    document.addEventListener('click', function (e) {
        var trigger = e.target.closest('[data-advising-open], a[href$="#consult"]');
        if (! trigger) { return; }
        e.preventDefault();
        open(trigger.dataset.advisingInterest || trigger.dataset.program || '', trigger.dataset.advisingGoal || '');
    });
    if (location.hash === '#book') { open('', ''); }

    form.addEventListener('click', function (e) {
        var next = e.target.closest('[data-next]'), back = e.target.closest('[data-back]'), close = e.target.closest('[data-close]');
        if (close) { dlg.close(); return; }
        if (back) { show(back.dataset.back); return; }
        if (! next) { return; }
        var msg = next.closest('section').querySelector('[data-step-msg]');
        var fail = function (text) { if (msg) { msg.textContent = text; msg.hidden = false; } };
        if (msg) { msg.hidden = true; }
        if (next.dataset.next === '2' && ! form.querySelector('[name="goal"]:checked')) { fail(dlg.dataset.lGoal); return; }
        if (next.dataset.next === '3') {
            if (! slotInput.value) { fail(dlg.dataset.lPick); return; }
            chosen.textContent = chosenLabel;
        }
        show(next.dataset.next);
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (! form.reportValidity()) { return; }
        var btn = form.querySelector('[data-submit]'), label = btn.textContent;
        btn.disabled = true; btn.textContent = dlg.dataset.lSending; errorBox.hidden = true;
        fetch(dlg.dataset.storeUrl, { method: 'POST', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body: new FormData(form) })
            .then(function (r) { return r.json().then(function (body) { return { ok: r.ok, body: body }; }); })
            .then(function (res) {
                if (! res.ok) {
                    var errs = res.body.errors || {};
                    errorBox.textContent = (Object.values(errs)[0] || [res.body.message || dlg.dataset.lError])[0];
                    errorBox.hidden = false;
                    if (errs.slot) { days = null; daysBox.innerHTML = ''; timesBox.innerHTML = ''; slotInput.value = ''; dlg.querySelector('[data-slots-loading]').hidden = false; loadSlots(); }
                    return;
                }
                var s = res.body.summary || {};
                dlg.querySelector('[data-done-summary]').textContent = [s.date, s.time, s.mode].filter(Boolean).join(' — ');
                form.dataset.done = '1';
                show('done');
            })
            .catch(function () { errorBox.textContent = dlg.dataset.lError; errorBox.hidden = false; })
            .finally(function () { btn.disabled = false; btn.textContent = label; });
    });
})();
</script>
