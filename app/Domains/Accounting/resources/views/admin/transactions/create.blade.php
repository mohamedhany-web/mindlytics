@extends('layouts.admin')

@section('title', 'إضافة معاملة جديدة')
@section('header', 'إضافة معاملة جديدة')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">إضافة معاملة جديدة</h1>
        
        <form action="{{ route('admin.transactions.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2 space-y-2">
                    <label for="transaction-client-search" class="block text-sm font-medium text-gray-700 mb-2">بحث عن عميل</label>
                    <input type="search"
                           id="transaction-client-search"
                           autocomplete="off"
                           placeholder="البريد الإلكتروني، الاسم، أو الهاتف…"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 placeholder:text-gray-400">
                    <p class="text-xs text-gray-500">يُصفّي قائمة العملاء أدناه دون إعادة تحميل الصفحة.</p>
                </div>
                <div>
                    <label for="transaction-user-select" class="block text-sm font-medium text-gray-700 mb-2">العميل *</label>
                    <select name="user_id" id="transaction-user-select" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">اختر العميل</option>
                        @foreach($users as $user)
                        @php
                            $searchBlob = \Illuminate\Support\Str::lower(trim(implode(' ', array_filter([
                                $user->name ?? '',
                                $user->email ?? '',
                                $user->phone ?? '',
                            ]))));
                        @endphp
                        <option value="{{ $user->id }}" data-search="{{ e($searchBlob) }}">
                            {{ $user->name }} — {{ $user->email }}@if(!empty($user->phone)) — {{ $user->phone }}@endif
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">النوع *</label>
                    <select name="type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="deposit">إيداع</option>
                        <option value="withdrawal">سحب</option>
                        <option value="payment">دفع</option>
                        <option value="refund">استرداد</option>
                        <option value="commission">عمولة</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المبلغ *</label>
                    <input type="number" name="amount" step="0.01" min="0" required value="{{ old('amount') }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة *</label>
                    <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="pending">معلقة</option>
                        <option value="completed" selected>مكتملة</option>
                        <option value="failed">فاشلة</option>
                        <option value="cancelled">ملغاة</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                <textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">{{ old('description') }}</textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                    إنشاء المعاملة
                </button>
                <a href="{{ route('admin.transactions.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var search = document.getElementById('transaction-client-search');
    var select = document.getElementById('transaction-user-select');
    if (!search || !select) return;

    var pool = Array.prototype.slice.call(select.options, 1).map(function (o) {
        return {
            v: o.value,
            t: o.text,
            s: (o.getAttribute('data-search') || o.text || '').toLowerCase()
        };
    });

    function applyFilter() {
        var q = search.value.trim().toLowerCase().replace(/\s+/g, ' ');
        var prev = select.value;

        while (select.options.length > 1) {
            select.remove(1);
        }

        pool.forEach(function (p) {
            if (!q || p.s.indexOf(q) !== -1) {
                var opt = document.createElement('option');
                opt.value = p.v;
                opt.textContent = p.t;
                opt.setAttribute('data-search', p.s);
                select.appendChild(opt);
            }
        });

        var stillThere = Array.prototype.some.call(select.options, function (o) {
            return o.value === prev;
        });
        select.value = stillThere ? prev : '';
    }

    search.addEventListener('input', applyFilter);
});
</script>
@endpush

