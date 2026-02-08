@extends('layouts.admin')

@section('title', 'تعديل الدفعة')
@section('header', 'تعديل الدفعة')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">تعديل الدفعة #{{ $payment->payment_number }}</h1>
        
        <form action="{{ route('admin.payments.update', $payment) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العميل *</label>
                    <select name="user_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $payment->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }} - {{ $user->phone }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الفاتورة</label>
                    <select name="invoice_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        @forelse($invoices as $invoice)
                            <option value="{{ $invoice->id }}" {{ $payment->invoice_id == $invoice->id ? 'selected' : '' }}>
                                {{ $invoice->invoice_number }} · {{ $invoice->user->name }} · متبقي {{ number_format($invoice->remaining_amount + ($payment->invoice_id === $invoice->id ? $payment->amount : 0), 2) }} ج.م
                            </option>
                        @empty
                            <option value="" disabled selected>لا توجد فواتير متاحة</option>
                        @endforelse
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المبلغ *</label>
                    <input type="number" name="amount" step="0.01" min="0" required value="{{ old('amount', $payment->amount) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">طريقة الدفع</label>
                    <select name="payment_method" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="cash" {{ $payment->payment_method == 'cash' ? 'selected' : '' }}>نقدي</option>
                        <option value="card" {{ $payment->payment_method == 'card' ? 'selected' : '' }}>بطاقة</option>
                        <option value="bank_transfer" {{ $payment->payment_method == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                        <option value="online" {{ $payment->payment_method == 'online' ? 'selected' : '' }}>دفع إلكتروني</option>
                        <option value="wallet" {{ $payment->payment_method == 'wallet' ? 'selected' : '' }}>محفظة</option>
                        <option value="other" {{ $payment->payment_method == 'other' ? 'selected' : '' }}>أخرى</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة *</label>
                    <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="pending" {{ $payment->status == 'pending' ? 'selected' : '' }}>معلقة</option>
                        <option value="completed" {{ $payment->status == 'completed' ? 'selected' : '' }}>مكتملة</option>
                        <option value="failed" {{ $payment->status == 'failed' ? 'selected' : '' }}>فاشلة</option>
                        <option value="cancelled" {{ $payment->status == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">{{ old('notes', $payment->notes) }}</textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                    تحديث الدفعة
                </button>
                <a href="{{ route('admin.payments.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

