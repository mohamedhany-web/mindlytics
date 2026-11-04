{{-- بيربط كل عنوان خانة (label) بالخانة اللي بعده لو مش مربوطين: الضغط على العنوان يروح للخانة، وقارئ الشاشة يقرا اسمها. --}}
<script>
(function () {
    var controls = 'input:not([type=hidden]):not([type=submit]):not([type=button]), select, textarea';
    var n = 0;
    var link = function (root) {
        (root || document).querySelectorAll('label:not([for])').forEach(function (label) {
            if (label.querySelector(controls)) { return; }
            var el = label.nextElementSibling, target = null;
            for (var i = 0; el && i < 2 && ! target; i++, el = el.nextElementSibling) {
                if (el.matches(controls)) { target = el; } else if (el.tagName !== 'LABEL') {
                    var inner = el.querySelectorAll(controls);
                    if (inner.length === 1) { target = inner[0]; } else if (inner.length > 1) { break; }
                }
            }
            if (! target || (target.id && document.querySelector('label[for="' + target.id + '"]'))) { return; }
            if (! target.id) { target.id = 'fl-' + (++n); }
            label.htmlFor = target.id;
        });
    };
    // خانات من غير عنوان خالص: في جدول بتاخد اسم العمود، وفي الفلاتر اسم مفهوم من اسم الخانة
    var names = { from: 'من تاريخ', date_from: 'من تاريخ', to: 'إلى تاريخ', date_to: 'إلى تاريخ', date: 'التاريخ', work_date: 'تاريخ العمل',
        period: 'الفترة', status: 'الحالة', employee_id: 'الموظف', user_id: 'الموظف', to_user_id: 'تحويل إلى موظف', from_user_id: 'من موظف',
        group_id: 'المجموعة', interest_type_id: 'نوع الاهتمام', filter: 'الفلتر', year_month: 'الشهر', month: 'الشهر', sales_reports_to_id: 'المدير المباشر' };
    var name = function (root) {
        (root || document).querySelectorAll(controls).forEach(function (el) {
            if (el.labels && el.labels.length) { return; }
            if (el.getAttribute('aria-label') || el.getAttribute('placeholder') || el.closest('label')) { return; }
            var cell = el.closest('td');
            if (cell) {
                var table = cell.closest('table'), th = table && table.querySelectorAll('thead th')[cell.cellIndex];
                var head = th ? th.textContent.trim() : '';
                if (head) { el.setAttribute('aria-label', head); return; }
            }
            var key = (el.name || '').replace(/\[\]$/, '');
            if (names[key]) { el.setAttribute('aria-label', names[key]); return; }
            if (el.tagName === 'SELECT' && el.options.length && el.options[0].text.trim()) { el.setAttribute('aria-label', el.options[0].text.trim()); }
        });
    };
    var run = function () { link(); name(); };
    document.addEventListener('DOMContentLoaded', function () { run(); setTimeout(run, 400); });
})();
</script>
