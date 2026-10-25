@extends('layouts.admin')
@section('title', 'تعديل الإعلان المنبثق')
@section('header', 'تعديل الإعلان المنبثق')
@section('content')
<div class="w-full">
    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
            <h1 class="text-xl font-bold text-slate-900">تعديل الإعلان</h1>
        </div>
        <form action="{{ route('admin.popup-ads.update', $popupAd) }}" method="POST" class="p-5 sm:p-8 space-y-6">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">عنوان الإعلان <span class="text-rose-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $popupAd->title) }}" required
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                @error('title')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">نص الإعلان <span class="text-rose-500">*</span></label>
                <textarea name="body" rows="5" required
                          class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">{{ old('body', $popupAd->body) }}</textarea>
                @error('body')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">نص زر الدعوة (اختياري)</label>
                <input type="text" name="cta_text" value="{{ old('cta_text', $popupAd->cta_text) }}" placeholder="مثال: ابدأ الآن"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                @error('cta_text')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">رابط الزر (اختياري)</label>
                <input type="url" name="link_url" value="{{ old('link_url', $popupAd->link_url) }}" placeholder="https://..."
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                @error('link_url')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">تاريخ بدء العرض <span class="text-rose-500">*</span></label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $popupAd->starts_at->format('Y-m-d\TH:i')) }}" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30">
                    @error('starts_at')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">مدة العرض (أيام) <span class="text-rose-500">*</span></label>
                    <input type="number" name="duration_days" value="{{ old('duration_days', max(1, $popupAd->starts_at->diffInDays($popupAd->ends_at))) }}" min="1" max="365" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30">
                    @error('duration_days')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">عدد مرات ظهور الإعلان لكل زائر <span class="text-rose-500">*</span></label>
                <input type="number" name="max_views_per_visitor" value="{{ old('max_views_per_visitor', $popupAd->max_views_per_visitor) }}" min="1" max="100" required
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30">
                @error('max_views_per_visitor')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $popupAd->is_active) ? 'checked' : '' }}
                       class="rounded border-slate-300 text-sky-500 focus:ring-sky-500">
                <label for="is_active" class="text-sm font-medium text-slate-700">تفعيل الإعلان</label>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white rounded-xl font-semibold">حفظ التعديلات</button>
                <a href="{{ route('admin.popup-ads.index') }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
