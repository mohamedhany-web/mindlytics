@extends('layouts.student-dashboard')

@section('title', __('student.invoices_title'))
@section('header', __('student.invoices_title'))

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('student.invoices_title') }}</h1>
        <p class="text-gray-600 mt-1">{{ __('student.invoices_subtitle') }}</p>
    </div>

    @if(isset($invoices) && $invoices->count() > 0)
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('student.invoice_number') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('student.amount_label') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('common.status') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('student.actions_label') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($invoices as $invoice)
                    <tr>
                        <td class="px-6 py-4">{{ $invoice->invoice_number }}</td>
                        <td class="px-6 py-4">{{ number_format($invoice->total_amount, 2) }} {{ __('public.currency_egp') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($invoice->status == 'paid') bg-green-100 text-green-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ $invoice->status == 'paid' ? __('student.paid_status') : __('student.pending_status_label') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('student.invoices.show', $invoice) }}" class="text-sky-600 hover:text-sky-900">{{ __('common.view') }}</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">{{ $invoices->links() }}</div>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-lg p-12 text-center">
        <p class="text-gray-600">{{ __('student.no_invoices') }}</p>
    </div>
    @endif
</div>
@endsection
