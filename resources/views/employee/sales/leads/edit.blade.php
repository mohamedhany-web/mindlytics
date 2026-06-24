@extends('layouts.employee')

@section('title', 'تعديل عميل محتمل')
@section('header', 'تعديل عميل محتمل')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <a href="{{ route('employee.sales.leads.show', $lead) }}" class="text-sm text-gray-600 hover:text-emerald-600"><i class="fas fa-arrow-right ml-1"></i> العودة للتفاصيل</a>
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
            <p class="font-bold mb-2"><i class="fas fa-exclamation-circle ml-1"></i> لم يتم الحفظ — راجع الحقول التالية:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <form method="post" action="{{ route('employee.sales.leads.update', $lead) }}">
            @csrf
            @method('PUT')
            @include('employee.sales._lead_fields', ['lead' => $lead, 'groups' => $groups ?? collect()])
            <div class="mt-6 flex gap-3">
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold">حفظ</button>
                <a href="{{ route('employee.sales.leads.show', $lead) }}" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
