@extends('layouts.app')

@section('title', 'رفع مشروع - رحلتي')
@section('header', 'رفع مشروع جديد')

@section('content')
<div class="w-full max-w-7xl mx-auto">
    <div class="mb-6 p-4 rounded-2xl bg-[#2CA9BD]/10 border border-[#2CA9BD]/20">
        <h3 class="text-sm font-bold text-[#1F3A56] mb-1">Mindlytics Journey</h3>
        <p class="text-xs text-gray-600">اربط المشروع بكورس مسجّل أو دبلوم (مسار / أونلاين / أوفلاين). بعد مراجعة المدرب يصبح Mindlytics Verified ويعرض للشركات.</p>
    </div>

    @if($errors->any())
        <div class="rounded-2xl bg-red-50 border-2 border-red-200 px-6 py-4 mb-6">
            <ul class="list-disc list-inside text-red-800 text-sm">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-[#2CA9BD] to-[#65DBE4] px-6 py-4">
            <h2 class="text-lg font-black text-white">إضافة مشروع للرحلة</h2>
        </div>

        <form action="{{ route('student.portfolio.store') }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8">
            @csrf
            @include('student.portfolio._form_fields')

            <div class="flex flex-col sm:flex-row gap-3 justify-end pt-4 border-t border-gray-200">
                <a href="{{ route('student.portfolio.index') }}" class="inline-flex items-center justify-center gap-2 border-2 border-gray-300 text-gray-700 px-6 py-3 rounded-xl font-bold hover:bg-gray-50">إلغاء</a>
                <button type="submit" name="action" value="draft" class="inline-flex items-center justify-center gap-2 border-2 border-[#2CA9BD] text-[#1F3A56] px-6 py-3 rounded-xl font-bold hover:bg-[#2CA9BD]/10">حفظ كمسودة</button>
                <button type="submit" name="action" value="submit" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#2CA9BD] to-[#65DBE4] text-white px-6 py-3 rounded-xl font-bold hover:shadow-lg">إرسال للمراجعة</button>
            </div>
        </form>
    </div>
</div>
@endsection
