@extends('layouts.admin')

@section('title', 'تسجيل طالب — أونلاين')
@section('header', 'تسجيل طالب بالقناة الأونلاين')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">تسجيل طالب — أونلاين</h1>
                <p class="text-gray-600 mt-1">بريد الطالب المسجّل في المنصة؛ يُنشأ تسجيل نشط على المجموعة الأونلاين</p>
            </div>
            <a href="{{ route('admin.online-management.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-right mr-2"></i>العودة لكورسات الأونلاين
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        @if($courses->isEmpty())
            <div class="rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-sm px-4 py-4 mb-6">
                لا يوجد كورس نشط بمجموعة أونلاين مفعّلة. أنشئ كورس أونلاين فقط أو فعّل «الحجز الأونلاين» من
                <a href="{{ route('admin.offline-courses.index') }}" class="font-bold text-blue-700 underline">قائمة الكورسات الأوفلاين</a>.
            </div>
        @endif

        <form action="{{ route('admin.online-management.enroll.store') }}" method="post" class="space-y-6" @if($courses->isEmpty()) aria-disabled="true" @endif>
            @csrf

            @if($errors->has('error'))
                <div class="rounded-lg bg-red-50 text-red-800 text-sm px-4 py-3 border border-red-100">{{ $errors->first('error') }}</div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">بريد الطالب *</label>
                    <input type="email" name="student_email" value="{{ old('student_email') }}" required autocomplete="off" dir="ltr"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    @error('student_email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الكورس *</label>
                    <select name="offline_course_id" id="offline_course_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="">اختر الكورس</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" @selected(old('offline_course_id', $selectedCourseId) == $c->id)>{{ $c->title }} @if($c->online_only) (أونلاين فقط) @endif</option>
                        @endforeach
                    </select>
                    @error('offline_course_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المجموعة *</label>
                    <select name="group_id" id="group_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="">اختر الكورس أولاً</option>
                        @foreach($courses as $c)
                            @foreach($c->groups as $g)
                                @if($g->online_booking_enabled && $g->is_active && $g->status === 'active')
                                    <option value="{{ $g->id }}" data-course="{{ $c->id }}" @selected(old('group_id', $selectedGroupId) == $g->id)>
                                        [{{ $c->title }}] {{ $g->name }} ({{ $g->current_students_online }}/{{ $g->max_students_online }})
                                    </option>
                                @endif
                            @endforeach
                        @endforeach
                    </select>
                    @error('group_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="mark_fully_paid" value="1" {{ old('mark_fully_paid') ? 'checked' : '' }} class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="text-sm text-gray-700">تسجيل كمدفوع بالكامل (يُنشئ سجلات مالية عند وجود سعر للكورس)</span>
            </label>

            <div id="online-payment-box" class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 rounded-lg border border-gray-200 bg-gray-50">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">طريقة الدفع (عند الدفع الكامل)</label>
                    <select name="payment_method" id="online_payment_method" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="cash" @selected(old('payment_method', 'cash') === 'cash')>نقدي</option>
                        <option value="wallet" @selected(old('payment_method') === 'wallet')>تحويل على محفظة</option>
                    </select>
                    @error('payment_method')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div id="online_wallet_wrapper">
                    <label class="block text-sm font-medium text-gray-700 mb-2">المحفظة</label>
                    <select name="wallet_id" id="online_wallet_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="">اختر المحفظة</option>
                        @foreach($wallets as $wallet)
                            <option value="{{ $wallet->id }}" @selected((string) old('wallet_id') === (string) $wallet->id)>
                                {{ $wallet->name }} — {{ \App\Models\Wallet::typeLabel($wallet->type) }}
                            </option>
                        @endforeach
                    </select>
                    @error('wallet_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات للتسجيل</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">{{ old('notes') }}</textarea>
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" @disabled($courses->isEmpty()) class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:pointer-events-none">
                    <i class="fas fa-check"></i>
                    تفعيل التسجيل الأونلاين
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const courseEl = document.getElementById('offline_course_id');
    const groupEl = document.getElementById('group_id');
    const fullPaidEl = document.querySelector('input[name="mark_fully_paid"]');
    const paymentMethodEl = document.getElementById('online_payment_method');
    const walletWrapEl = document.getElementById('online_wallet_wrapper');
    const walletSelectEl = document.getElementById('online_wallet_id');
    const paymentBoxEl = document.getElementById('online-payment-box');
    if (!courseEl || !groupEl) return;

    function filterGroups() {
        const cid = courseEl.value;
        const opts = groupEl.querySelectorAll('option[data-course]');
        let firstVisible = null;
        opts.forEach(function (opt) {
            const show = !cid || opt.getAttribute('data-course') === cid;
            opt.hidden = !show;
            opt.disabled = !show;
            if (show && !firstVisible) firstVisible = opt;
        });
        const placeholder = groupEl.querySelector('option:not([data-course])');
        if (placeholder) {
            placeholder.hidden = false;
            placeholder.disabled = false;
        }
        if (cid) {
            const current = groupEl.querySelector('option[value="' + groupEl.value + '"]');
            if (!current || current.disabled || current.hidden) {
                groupEl.value = firstVisible ? firstVisible.value : '';
            }
        }
    }
    courseEl.addEventListener('change', filterGroups);
    filterGroups();

    function togglePaymentFields() {
        const isFullyPaid = !!(fullPaidEl && fullPaidEl.checked);
        const isWallet = paymentMethodEl && paymentMethodEl.value === 'wallet';

        if (paymentBoxEl) {
            paymentBoxEl.style.display = isFullyPaid ? '' : 'none';
        }
        if (walletWrapEl) {
            walletWrapEl.style.display = isFullyPaid && isWallet ? '' : 'none';
        }
        if (walletSelectEl) {
            walletSelectEl.required = isFullyPaid && isWallet;
            if (!isFullyPaid || !isWallet) {
                walletSelectEl.value = '';
            }
        }
    }

    if (fullPaidEl) fullPaidEl.addEventListener('change', togglePaymentFields);
    if (paymentMethodEl) paymentMethodEl.addEventListener('change', togglePaymentFields);
    togglePaymentFields();
});
</script>
@endpush
@endsection
