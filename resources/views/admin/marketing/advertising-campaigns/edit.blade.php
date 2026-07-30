@extends('layouts.admin')

@section('title', 'تعديل الحملة الإعلانية')
@section('header', 'تعديل الحملة الإعلانية')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="text-sm text-gray-500 mb-1">
                    <a href="{{ route('admin.advertising-campaigns.index') }}" class="hover:text-blue-600">الحملات الإعلانية</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-700 font-semibold">تعديل</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900">تعديل: {{ $campaign->name }}</h1>
                <p class="text-gray-600 mt-1">حدّث تفاصيل الحملة أو موظفي السيلز المسؤولين.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.advertising-campaigns.reports', ['campaign_id' => $campaign->id]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg font-medium border border-indigo-100">
                    <i class="fas fa-chart-column"></i> تقارير هذه الحملة
                </a>
                <a href="{{ route('admin.advertising-campaigns.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg font-medium border border-gray-200">
                    <i class="fas fa-arrow-right"></i> القائمة
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.advertising-campaigns.update', $campaign) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.marketing.advertising-campaigns._form')

        <div class="bg-white rounded-xl shadow-lg border border-gray-200 px-5 py-4 flex flex-wrap items-center gap-3">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-colors">
                <i class="fas fa-check"></i> حفظ التعديلات
            </button>
            <a href="{{ route('admin.advertising-campaigns.index') }}"
               class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg font-semibold border border-gray-200">
                إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
