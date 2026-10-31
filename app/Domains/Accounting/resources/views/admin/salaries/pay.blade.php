@extends('layouts.admin')

@section('title', 'تنفيذ الدفع للمدرب')
@section('header', 'تنفيذ الدفع للمدرب')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">تنفيذ الدفع وتحويل المبلغ</h1>
                <p class="text-gray-600 mt-1">رفع إيصال التحويل وتسجيل الدفع للمدرب</p>
            </div>
            <a href="{{ route('admin.salaries.instructor', $payment->instructor) }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-right mr-2"></i>العودة لماليات المدرب
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <h2 class="text-lg font-bold text-gray-900 mb-4">تفاصيل المدفوعة</h2>
            <dl class="space-y-3">
                <div><dt class="text-sm text-gray-500">رقم المدفوعة</dt><dd class="font-mono font-semibold">{{ $payment->payment_number }}</dd></div>
                <div><dt class="text-sm text-gray-500">المدرب</dt><dd class="font-semibold">{{ $payment->instructor->name ?? '—' }}</dd></div>
                <div><dt class="text-sm text-gray-500">الاتفاقية</dt><dd>{{ $payment->agreement->title ?? '—' }}</dd></div>
                <div><dt class="text-sm text-gray-500">المبلغ</dt><dd class="text-xl font-bold text-green-700">{{ number_format($payment->amount, 2) }} ج.م</dd></div>
            </dl>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <h2 class="text-lg font-bold text-gray-900 mb-4">حساب التحويل (بيانات المدرب)</h2>
            @if($payment->instructor && $payment->instructor->payoutDetail && $payment->instructor->payoutDetail->hasAnyDetails())
                @php $d = $payment->instructor->payoutDetail; @endphp
                <dl class="space-y-2 text-sm">
                    @if($d->bank_name)<div><dt class="text-gray-500">البنك</dt><dd class="font-medium">{{ $d->bank_name }}</dd></div>@endif
                    @if($d->account_holder_name)<div><dt class="text-gray-500">اسم صاحب الحساب</dt><dd>{{ $d->account_holder_name }}</dd></div>@endif
                    @if($d->account_number)<div><dt class="text-gray-500">رقم الحساب</dt><dd class="font-mono">{{ $d->account_number }}</dd></div>@endif
                    @if($d->iban)<div><dt class="text-gray-500">الآيبان</dt><dd class="font-mono">{{ $d->iban }}</dd></div>@endif
                    @if($d->branch_name)<div><dt class="text-gray-500">الفرع</dt><dd>{{ $d->branch_name }}</dd></div>@endif
                </dl>
            @else
                <p class="text-amber-600 bg-amber-50 p-4 rounded-lg">المدرب لم يضف بعد بيانات حساب التحويل. يمكنك تنفيذ الدفع ورفع الإيصال، وننصح بإخبار المدرب بإضافة بيانات التحويل من صفحة «حساب التحويل» في لوحته.</p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-lg font-bold text-gray-900 mb-4">تسجيل الدفع ورفع إيصال التحويل</h2>
        <form action="{{ route('admin.salaries.mark-paid', $payment) }}" method="POST" enctype="multipart/form-data" class="space-y-4 max-w-xl">
            @csrf
            <div>
                <label for="transfer_receipt" class="block text-sm font-medium text-gray-700 mb-1">إيصال التحويل (مطلوب) *</label>
                <input type="file" name="transfer_receipt" id="transfer_receipt" accept=".pdf,.jpg,.jpeg,.png" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">PDF أو صورة، حجم أقصى 5 ميجا</p>
                @error('transfer_receipt')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">ملاحظات (اختياري)</label>
                <textarea name="notes" id="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                    <i class="fas fa-check mr-2"></i>تسجيل الدفع ورفع الإيصال
                </button>
                <a href="{{ route('admin.salaries.instructor', $payment->instructor) }}" class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
