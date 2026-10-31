@extends('layouts.admin')

@section('title', 'إرسال إشعارات للمجتمع')
@section('header', 'إرسال إشعارات للمجتمع')

@section('content')
<div class="p-4 md:p-6 space-y-6">
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-red-100 border border-red-300 text-red-800">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="p-4 rounded-xl bg-red-100 border border-red-300 text-red-800">
            <ul class="list-disc list-inside">{{ $errors->first() }}</ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-cyan-50 to-blue-50">
            <h2 class="text-lg font-black text-slate-800 flex items-center gap-2">
                <span class="w-10 h-10 rounded-xl bg-cyan-500 text-white flex items-center justify-center"><i class="fas fa-bell"></i></span>
                إرسال إشعار بالبريد الإلكتروني (Gmail)
            </h2>
            <p class="text-sm text-slate-600 mt-1">يُرسل الإشعار إلى مساهمي المجتمع عبر Gmail. التصميم يظهر بشكل جميل داخل صندوق الوارد.</p>
            <p class="text-sm text-slate-500 mt-1"><strong>{{ $contributorsCount ?? 0 }}</strong> مساهم نشط سيستلم الإشعار.</p>
        </div>

        <form action="{{ route('admin.community.notifications.send') }}" method="POST" class="p-6 space-y-6" x-data="{ audience: '{{ old('audience', 'contributors') }}' }">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">إرسال إلى <span class="text-red-500">*</span></label>
                <div class="space-y-3">
                    <label class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all"
                           :class="audience === 'contributors' ? 'border-cyan-500 bg-cyan-50' : 'border-slate-200 hover:border-slate-300'">
                        <input type="radio" name="audience" value="contributors" class="w-4 h-4 text-cyan-600 focus:ring-cyan-500"
                               x-model="audience">
                        <span class="font-semibold text-slate-800">مساهمون المجتمع فقط</span>
                        <span class="text-slate-500 text-sm">({{ $contributorsCount ?? 0 }} مساهم)</span>
                    </label>
                    <label class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all"
                           :class="audience === 'specific' ? 'border-cyan-500 bg-cyan-50' : 'border-slate-200 hover:border-slate-300'">
                        <input type="radio" name="audience" value="specific" class="w-4 h-4 text-cyan-600 focus:ring-cyan-500"
                               x-model="audience">
                        <span class="font-semibold text-slate-800">شخص معين أو قائمة بريدية</span>
                    </label>
                </div>
            </div>
            <div x-show="audience === 'specific'" x-transition>
                <label for="emails" class="block text-sm font-bold text-slate-700 mb-2">البريد الإلكتروني (أو عدة عناوين) <span class="text-red-500">*</span></label>
                <textarea name="emails" id="emails" rows="4" placeholder="example@email.com&#10;آخر@email.com (سطر واحد لكل بريد)"
                          class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 resize-y">{{ old('emails') }}</textarea>
                <p class="mt-1 text-xs text-slate-500">ضع بريداً واحداً في كل سطر، أو افصل بينها بفاصلة.</p>
            </div>
            <div>
                <label for="subject" class="block text-sm font-bold text-slate-700 mb-2">عنوان الإشعار <span class="text-red-500">*</span></label>
                <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required maxlength="255"
                       placeholder="مثال: تحديثات جديدة في مجتمع الذكاء الاصطناعي"
                       class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
            </div>
            <div>
                <label for="body" class="block text-sm font-bold text-slate-700 mb-2">نص الإشعار <span class="text-red-500">*</span></label>
                <textarea name="body" id="body" rows="10" required maxlength="10000" placeholder="اكتب محتوى الإشعار هنا. سيظهر بتصميم منسق في البريد..."
                          class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 resize-y">{{ old('body') }}</textarea>
                <p class="mt-1 text-xs text-slate-500">سيتم إضافة تحية بالاسم تلقائياً عند الإمكان. النص يدعم الأسطر الجديدة.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-cyan-600 text-white font-bold hover:bg-cyan-700 transition-colors shadow-md">
                    <i class="fas fa-paper-plane"></i>
                    <span>إرسال الإشعار</span>
                </button>
                <a href="{{ route('admin.community.dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    <span>إلغاء</span>
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
