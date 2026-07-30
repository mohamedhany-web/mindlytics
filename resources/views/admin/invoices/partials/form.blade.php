@php
    $invoice = $invoice ?? null;
    $isEdit = $invoice !== null;
    $formAction = $formAction ?? ($isEdit ? route('admin.invoices.update', $invoice) : route('admin.invoices.store'));
    $formMethod = $isEdit ? 'PUT' : 'POST';
    $defaultClientType = old('client_type', $invoice->client_type ?? (($invoice && $invoice->isCompanyClient()) ? 'company' : 'student'));
@endphp

<form action="{{ $formAction }}" method="POST" class="space-y-6" id="invoiceForm">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
            <p class="font-bold mb-1">يرجى تصحيح الأخطاء التالية:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- نوع العميل --}}
    <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4 space-y-3">
        <p class="text-xs font-bold text-slate-700">نوع العميل *</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 cursor-pointer hover:border-blue-300 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50/50">
                <input type="radio" name="client_type" value="student" class="text-blue-600 focus:ring-blue-500"
                       @checked($defaultClientType === 'student') data-client-type-radio>
                <span>
                    <span class="block text-sm font-bold text-slate-900">طالب</span>
                    <span class="block text-[11px] text-slate-500">فاتورة مرتبطة بحساب طالب مسجّل</span>
                </span>
            </label>
            <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 cursor-pointer hover:border-blue-300 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50/50">
                <input type="radio" name="client_type" value="company" class="text-blue-600 focus:ring-blue-500"
                       @checked($defaultClientType === 'company') data-client-type-radio>
                <span>
                    <span class="block text-sm font-bold text-slate-900">شركة / جهة خارجية</span>
                    <span class="block text-[11px] text-slate-500">إيراد من جهة خارجية باسم الشركة</span>
                </span>
            </label>
        </div>
        @error('client_type')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div id="invoice-student-fields" class="md:col-span-2 space-y-3">
            @if(!$isEdit)
            <div class="space-y-2">
                <label for="invoice-client-search" class="block text-xs font-bold text-slate-700">بحث عن طالب</label>
                <input type="search" id="invoice-client-search" autocomplete="off" placeholder="البريد، الاسم، أو الهاتف…"
                       class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                <p class="text-[11px] text-slate-500">يُصفّي قائمة الطلاب دون إعادة تحميل الصفحة.</p>
            </div>
            @endif

            <div>
                <label for="invoice-user-select" class="block text-xs font-bold text-slate-700 mb-1">الطالب *</label>
                <select name="user_id" id="invoice-user-select"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 @error('user_id') border-rose-400 @enderror">
                    <option value="">اختر الطالب</option>
                    @foreach($users as $user)
                        @php
                            $searchBlob = \Illuminate\Support\Str::lower(trim(implode(' ', array_filter([
                                $user->name ?? '',
                                $user->email ?? '',
                                $user->phone ?? '',
                            ]))));
                            $sel = (int) old('user_id', $invoice->user_id ?? 0) === (int) $user->id;
                        @endphp
                        <option value="{{ $user->id }}" data-search="{{ e($searchBlob) }}" @selected($sel)>
                            {{ $user->name }} — {{ $user->email }}@if(!empty($user->phone)) — {{ $user->phone }}@endif
                        </option>
                    @endforeach
                </select>
                @error('user_id')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div id="invoice-company-fields" class="md:col-span-2" style="display:none;">
            <label for="invoice-company-name" class="block text-xs font-bold text-slate-700 mb-1">اسم الشركة / الجهة *</label>
            <input type="text" name="company_name" id="invoice-company-name" maxlength="255"
                   value="{{ old('company_name', $invoice->company_name ?? '') }}"
                   placeholder="مثال: شركة النور للتجارة"
                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 @error('company_name') border-rose-400 @enderror">
            <p class="text-[11px] text-slate-500 mt-1">يُستخدم لتسجيل إيراد من جهة خارجية غير مرتبطة بطالب.</p>
            @error('company_name')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">نوع الفاتورة *</label>
            <select name="type" required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 @error('type') border-rose-400 @enderror">
                @include('admin.invoices.partials.type-options', ['selected' => old('type', $invoice->type ?? 'other')])
            </select>
            @error('type')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">المبلغ الفرعي *</label>
            <input type="number" name="subtotal" step="0.01" min="0" required data-invoice-field="subtotal"
                   value="{{ old('subtotal', $invoice->subtotal ?? '') }}"
                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 @error('subtotal') border-rose-400 @enderror">
            @error('subtotal')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">الضريبة</label>
            <input type="number" name="tax_amount" step="0.01" min="0" data-invoice-field="tax"
                   value="{{ old('tax_amount', $invoice->tax_amount ?? 0) }}"
                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">الخصم</label>
            <input type="number" name="discount_amount" step="0.01" min="0" data-invoice-field="discount"
                   value="{{ old('discount_amount', $invoice->discount_amount ?? 0) }}"
                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
        </div>

        @if($isEdit)
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">الحالة *</label>
            <select name="status" required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 @error('status') border-rose-400 @enderror">
                @include('admin.invoices.partials.status-options', ['selected' => old('status', $invoice->status ?? 'pending')])
            </select>
            @error('status')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>
        @endif

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">تاريخ الاستحقاق</label>
            <input type="date" name="due_date"
                   value="{{ old('due_date', $invoice && $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '') }}"
                   @if(!$isEdit) min="{{ now()->format('Y-m-d') }}" @endif
                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
        </div>
    </div>

    <div class="rounded-xl border border-blue-100 bg-blue-50/60 px-4 py-3 flex flex-wrap items-center justify-between gap-2">
        <span class="text-xs font-bold text-blue-900">الإجمالي التقديري</span>
        <span id="invoice-total-preview" class="text-lg font-black text-blue-800 tabular-nums">0.00 ج.م</span>
    </div>

    <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">الوصف</label>
        <textarea name="description" rows="3" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('description', $invoice->description ?? '') }}</textarea>
    </div>

    <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">ملاحظات</label>
        <textarea name="notes" rows="3" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('notes', $invoice->notes ?? '') }}</textarea>
    </div>

    <div class="flex flex-wrap gap-3 pt-2">
        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-5 py-2.5 text-sm font-bold text-white">
            <i class="fas fa-save"></i>
            {{ $isEdit ? 'تحديث الفاتورة' : 'إنشاء الفاتورة' }}
        </button>
        <a href="{{ $isEdit ? route('admin.invoices.show', $invoice) : route('admin.invoices.index') }}"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
            إلغاء
        </a>
    </div>
</form>

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function num(el, name) {
        var n = parseFloat(el.querySelector('[name="' + name + '"]')?.value || '0');
        return isNaN(n) ? 0 : n;
    }
    function updateTotal(form) {
        var sub = num(form, 'subtotal');
        var tax = num(form, 'tax_amount');
        var disc = num(form, 'discount_amount');
        var total = Math.max(0, sub + tax - disc);
        var out = form.querySelector('#invoice-total-preview');
        if (out) out.textContent = total.toFixed(2) + ' ج.م';
    }
    document.querySelectorAll('#invoiceForm').forEach(function (form) {
        form.querySelectorAll('[data-invoice-field]').forEach(function (el) {
            el.addEventListener('input', function () { updateTotal(form); });
        });
        updateTotal(form);
    });

    var search = document.getElementById('invoice-client-search');
    var select = document.getElementById('invoice-user-select');
    if (search && select) {
        var pool = Array.prototype.slice.call(select.options, 1).map(function (o) {
            return { v: o.value, t: o.text, s: (o.getAttribute('data-search') || o.text || '').toLowerCase() };
        });
        search.addEventListener('input', function () {
            var q = search.value.trim().toLowerCase().replace(/\s+/g, ' ');
            var prev = select.value;
            while (select.options.length > 1) select.remove(1);
            pool.forEach(function (p) {
                if (!q || p.s.indexOf(q) !== -1) {
                    var opt = document.createElement('option');
                    opt.value = p.v;
                    opt.textContent = p.t;
                    opt.setAttribute('data-search', p.s);
                    select.appendChild(opt);
                }
            });
            var still = Array.prototype.some.call(select.options, function (o) { return o.value === prev; });
            select.value = still ? prev : '';
        });
    }

    function syncClientType() {
        var checked = document.querySelector('[data-client-type-radio]:checked');
        var type = checked ? checked.value : 'student';
        var studentBox = document.getElementById('invoice-student-fields');
        var companyBox = document.getElementById('invoice-company-fields');
        var userSelect = document.getElementById('invoice-user-select');
        var companyInput = document.getElementById('invoice-company-name');
        var isCompany = type === 'company';

        if (studentBox) studentBox.style.display = isCompany ? 'none' : '';
        if (companyBox) companyBox.style.display = isCompany ? '' : 'none';

        if (userSelect) {
            userSelect.required = !isCompany;
            userSelect.disabled = isCompany;
            if (isCompany) userSelect.value = '';
        }
        if (companyInput) {
            companyInput.required = isCompany;
            companyInput.disabled = !isCompany;
            if (!isCompany) companyInput.value = companyInput.value; // keep for old() redisplay if switching back
        }
    }

    document.querySelectorAll('[data-client-type-radio]').forEach(function (radio) {
        radio.addEventListener('change', syncClientType);
    });
    syncClientType();
});
</script>
@endpush
@endonce
