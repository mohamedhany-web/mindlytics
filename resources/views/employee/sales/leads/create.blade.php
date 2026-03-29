@extends('layouts.employee')

@section('title', 'عميل محتمل جديد')
@section('header', 'عميل محتمل جديد')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <a href="{{ route('employee.sales.leads.index') }}" class="text-sm text-gray-600 hover:text-emerald-600"><i class="fas fa-arrow-right ml-1"></i> العودة</a>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form method="post" action="{{ route('employee.sales.leads.store') }}">
            @csrf
            @include('employee.sales._lead_fields', ['lead' => null])
            <div class="mt-6 flex gap-3">
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold">حفظ</button>
                <a href="{{ route('employee.sales.leads.index') }}" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
