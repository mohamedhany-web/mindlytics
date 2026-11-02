{{-- سلوك الصفحة الرئيسية: قائمة الموبايل، فلترة الدفعات، تابات الأهداف، ورسالة الاستشارة. الصفحة شغالة كاملة من غير JS. --}}
<script>
(function () {
    // قائمة الموبايل
    var toggle = document.querySelector('[data-menu-toggle]');
    var menu = document.getElementById('h-mobile-menu');
    if (toggle && menu) {
        var setMenu = function (open) {
            menu.classList.toggle('is-open', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            toggle.setAttribute('aria-label', open ? 'إغلاق القائمة' : 'فتح القائمة');
        };
        toggle.addEventListener('click', function () { setMenu(! menu.classList.contains('is-open')); });
        menu.querySelectorAll('[data-menu-close]').forEach(function (a) { a.addEventListener('click', function () { setMenu(false); }); });
    }

    // فلترة الدفعات (النوع × الطريقة)
    var state = { kind: 'all', mode: 'all' };
    var cards = document.querySelectorAll('.h-intake');
    var counter = document.querySelector('[data-intake-count]');
    var empty = document.querySelector('[data-intake-empty]');
    var applyFilters = function () {
        var shown = 0;
        cards.forEach(function (c) {
            var ok = (state.kind === 'all' || c.dataset.kind === state.kind) && (state.mode === 'all' || c.dataset.mode === state.mode);
            c.hidden = ! ok;
            if (ok) { shown++; }
        });
        if (counter) { counter.textContent = shown === 0 ? counter.dataset.lNone : (shown === 1 ? counter.dataset.lOne : shown + ' ' + counter.dataset.lMany); }
        if (empty) { empty.hidden = shown !== 0; }
    };
    document.querySelectorAll('[data-filter]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var key = btn.dataset.filter;
            state[key] = btn.dataset.value;
            document.querySelectorAll('[data-filter="' + key + '"]').forEach(function (b) { b.setAttribute('aria-pressed', b === btn ? 'true' : 'false'); });
            applyFilters();
        });
    });

    // تابات «ابدأ من هدفك» (أسهم الكيبورد كمان)
    var tabs = Array.prototype.slice.call(document.querySelectorAll('[data-tab]'));
    var selectTab = function (tab) {
        tabs.forEach(function (t) {
            var on = t === tab;
            t.setAttribute('aria-selected', on ? 'true' : 'false');
            t.tabIndex = on ? 0 : -1;
            var panel = document.getElementById(t.getAttribute('aria-controls'));
            if (panel) { panel.hidden = ! on; }
        });
    };
    tabs.forEach(function (tab, i) {
        tab.addEventListener('click', function () { selectTab(tab); });
        tab.addEventListener('keydown', function (e) {
            // RTL: السهم الشمال = التالي، وLTR العكس
            var rtl = document.documentElement.dir === 'rtl';
            var next = e.key === (rtl ? 'ArrowLeft' : 'ArrowRight') ? i + 1 : (e.key === (rtl ? 'ArrowRight' : 'ArrowLeft') ? i - 1 : null);
            if (next === null) { return; }
            e.preventDefault();
            var t = tabs[(next + tabs.length) % tabs.length];
            selectTab(t);
            t.focus();
        });
    });

})();
</script>
