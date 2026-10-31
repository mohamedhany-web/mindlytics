@extends('layouts.admin')

@section('title', 'مصادر الفيديو')
@section('header', 'مصادر الفيديو (Bunny وغيرها)')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-200/80 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-sky-50 via-blue-50 to-sky-50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h3 class="text-lg font-black bg-gradient-to-r from-sky-800 via-blue-700 to-sky-600 bg-clip-text text-transparent flex items-center gap-2">
                    <i class="fas fa-photo-video text-sky-600"></i>
                    إدارة مصادر الفيديو
                </h3>
                <p class="text-xs sm:text-sm text-slate-500 max-w-xl">
                    هنا يمكنك ضبط بيانات الاتصال بمصادر الفيديو مثل Bunny.net (Video library ID, CDN hostname, API key، Token authentication key).
                </p>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- نموذج إضافة مصدر جديد -->
            <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                <h4 class="text-sm font-semibold text-slate-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-emerald-500"></i>
                    إضافة مصدر جديد
                </h4>
                <form method="POST" action="{{ route('admin.video-providers.store') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">الاسم الظاهر</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               placeholder="مثال: Bunny.net Stream"
                               class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500">
                        @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">الرمز (Slug)</label>
                        <input type="text" name="slug" value="{{ old('slug', 'bunny') }}" required
                               placeholder="bunny"
                               class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500">
                        @error('slug')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">المنصة (Platform)</label>
                        <input type="text" name="platform" value="{{ old('platform', 'bunny') }}" required
                               placeholder="bunny"
                               class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500">
                        @error('platform')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Video library ID</label>
                        <input type="text" name="library_id" value="{{ old('library_id') }}"
                               placeholder="Library ID من لوحة Bunny"
                               class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">CDN hostname</label>
                        <input type="text" name="cdn_hostname" value="{{ old('cdn_hostname') }}"
                               placeholder="مثال: iframe.mediadelivery.net أو video.mydomain.com"
                               class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Stream API key (AccessKey)</label>
                        <input type="password" name="api_key" value="{{ old('api_key') }}"
                               placeholder="YOUR_STREAM_API_KEY"
                               class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Token authentication key</label>
                        <input type="password" name="token_auth_key" value="{{ old('token_auth_key') }}"
                               placeholder="المفتاح المستخدم لتوليد الـ Token"
                               class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500">
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-sky-600 focus:ring-sky-500/20">
                            مصدر نشط
                        </label>
                    </div>
                    <div class="md:col-span-2 lg:col-span-3 flex justify-end">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-sm font-semibold shadow-md hover:shadow-lg transition-all">
                            <i class="fas fa-save"></i>
                            حفظ المصدر
                        </button>
                    </div>
                </form>
            </div>

            <!-- قائمة المصادر الحالية -->
            <div class="rounded-xl border border-slate-200 bg-white">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-database text-sky-500"></i>
                        المصادر المسجلة
                    </h4>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($providers as $provider)
                        <form method="POST" action="{{ route('admin.video-providers.update', $provider) }}" class="p-4 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-3 items-start">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">الاسم</label>
                                <input type="text" name="name" value="{{ old('name_'.$provider->id, $provider->name) }}"
                                       class="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Slug</label>
                                <input type="text" name="slug" value="{{ old('slug_'.$provider->id, $provider->slug) }}"
                                       class="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Platform</label>
                                <input type="text" name="platform" value="{{ old('platform_'.$provider->id, $provider->platform) }}"
                                       class="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Library ID</label>
                                <input type="text" name="library_id" value="{{ old('library_id_'.$provider->id, $provider->library_id) }}"
                                       class="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">CDN hostname</label>
                                <input type="text" name="cdn_hostname" value="{{ old('cdn_hostname_'.$provider->id, $provider->cdn_hostname) }}"
                                       class="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Stream API key</label>
                                <input type="password" name="api_key" value="{{ old('api_key_'.$provider->id, $provider->api_key) }}"
                                       class="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Token key</label>
                                <input type="password" name="token_auth_key" value="{{ old('token_auth_key_'.$provider->id, $provider->token_auth_key) }}"
                                       class="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500">
                            </div>
                            <div class="flex flex-col justify-between gap-2">
                                <label class="inline-flex items-center gap-2 text-[11px] font-semibold text-slate-700 mt-5">
                                    <input type="checkbox" name="is_active" value="1" {{ $provider->is_active ? 'checked' : '' }}
                                           class="rounded border-slate-300 text-sky-600 focus:ring-sky-500/20">
                                    مصدر نشط
                                </label>
                                <button type="submit"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold self-start">
                                    <i class="fas fa-save"></i>
                                    حفظ
                                </button>
                            </div>
                        </form>
                    @empty
                        <div class="p-4 text-center text-sm text-slate-500">
                            لا توجد مصادر فيديو مسجلة بعد. استخدم النموذج أعلاه لإضافة Bunny أو أي مصدر آخر.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

