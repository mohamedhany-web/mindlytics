@extends('layouts.app')

@section('title', 'ملف الرحلة')
@section('header', 'ملف رحلة التعلم')

@section('content')
<div class="w-full max-w-3xl mx-auto space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-800 font-semibold">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-red-800 text-sm">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <p class="text-xs font-semibold text-sky-600 mb-1">Public hiring profile</p>
        <h1 class="text-xl font-bold text-gray-900 mb-2">إعداد ملفك للشركات</h1>
        <p class="text-sm text-gray-500 mb-4">الرابط: <span class="font-mono text-gray-800">{{ url('/j/'.$profile->slug) }}</span></p>
        <p class="text-sm text-gray-500 mb-6">اكتمال الملف: <strong>{{ $profile->profile_completion }}%</strong> — المشاريع المنشورة فقط تظهر للعامة.</p>

        <form method="POST" action="{{ route('student.portfolio.journey.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-bold mb-1">الاسم المعروض</label>
                <input type="text" name="display_name" value="{{ old('display_name', $profile->display_name) }}" class="w-full rounded-xl border border-gray-200 px-4 py-2.5">
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">الرابط المختصر (slug)</label>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-400">/j/</span>
                    <input type="text" name="slug" value="{{ old('slug', $profile->slug) }}" class="flex-1 rounded-xl border border-gray-200 px-4 py-2.5 font-mono text-sm" dir="ltr">
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">العنوان المهني</label>
                <input type="text" name="headline" value="{{ old('headline', $profile->headline) }}" placeholder="Frontend Engineer · Building real projects" class="w-full rounded-xl border border-gray-200 px-4 py-2.5">
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">نبذة</label>
                <textarea name="bio" rows="4" class="w-full rounded-xl border border-gray-200 px-4 py-2.5">{{ old('bio', $profile->bio) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">الهدف المهني</label>
                <input type="text" name="career_goal" value="{{ old('career_goal', $profile->career_goal) }}" class="w-full rounded-xl border border-gray-200 px-4 py-2.5">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-bold mb-1">GitHub</label>
                    <input type="url" name="github_url" value="{{ old('github_url', $profile->github_url) }}" class="w-full rounded-xl border border-gray-200 px-4 py-2.5" dir="ltr">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">LinkedIn</label>
                    <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $profile->linkedin_url) }}" class="w-full rounded-xl border border-gray-200 px-4 py-2.5" dir="ltr">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Website</label>
                    <input type="url" name="website_url" value="{{ old('website_url', $profile->website_url) }}" class="w-full rounded-xl border border-gray-200 px-4 py-2.5" dir="ltr">
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">الظهور</label>
                <select name="visibility" class="w-full rounded-xl border border-gray-200 px-4 py-2.5">
                    <option value="private" {{ old('visibility', $profile->visibility) === 'private' ? 'selected' : '' }}>خاص — لا يظهر للعامة</option>
                    <option value="unlisted" {{ old('visibility', $profile->visibility) === 'unlisted' ? 'selected' : '' }}>غير مدرج — يظهر لمن معه الرابط فقط</option>
                    <option value="public" {{ old('visibility', $profile->visibility) === 'public' ? 'selected' : '' }}>عام — يظهر في دليل المواهب</option>
                </select>
            </div>
            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_open_to_work" value="1" class="rounded border-gray-300 text-sky-600" {{ old('is_open_to_work', $profile->is_open_to_work) ? 'checked' : '' }}>
                متاح لفرص العمل (Open to work)
            </label>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('student.portfolio.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 font-semibold text-gray-700">رجوع</a>
                <button class="px-5 py-2.5 rounded-xl bg-sky-500 text-white font-semibold hover:bg-sky-600">حفظ الملف</button>
            </div>
        </form>
    </div>
</div>
@endsection
