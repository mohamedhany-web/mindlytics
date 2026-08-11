@extends('layouts.app')

@section('title', 'تعديل مشروع - رحلتي')
@section('header', 'تعديل المشروع')

@section('content')
<div class="w-full max-w-7xl mx-auto">
    @if($errors->any())
        <div class="rounded-2xl bg-red-50 border-2 border-red-200 px-6 py-4 mb-6">
            <ul class="list-disc list-inside text-red-800 text-sm">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($project->instructor_notes || $project->rejected_reason)
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            <strong>ملاحظات المدرب:</strong>
            <p class="mt-1 whitespace-pre-line">{{ $project->instructor_notes ?: $project->rejected_reason }}</p>
        </div>
    @endif

    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-[#2CA9BD] to-[#65DBE4] px-6 py-4">
            <h2 class="text-lg font-black text-white">تعديل: {{ $project->title }}</h2>
        </div>

        <form action="{{ route('student.portfolio.update', $project) }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8">
            @csrf
            @method('PUT')
            @include('student.portfolio._form_fields')

            <div class="flex flex-col sm:flex-row gap-3 justify-end pt-4 border-t border-gray-200">
                <a href="{{ route('student.portfolio.index') }}" class="inline-flex items-center justify-center gap-2 border-2 border-gray-300 text-gray-700 px-6 py-3 rounded-xl font-bold">رجوع</a>
                <button type="submit" name="action" value="draft" class="inline-flex items-center justify-center gap-2 border-2 border-[#2CA9BD] text-[#1F3A56] px-6 py-3 rounded-xl font-bold">حفظ كمسودة</button>
                <button type="submit" name="action" value="submit" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#2CA9BD] to-[#65DBE4] text-white px-6 py-3 rounded-xl font-bold">إرسال للمراجعة</button>
            </div>
        </form>
    </div>
</div>
@endsection
