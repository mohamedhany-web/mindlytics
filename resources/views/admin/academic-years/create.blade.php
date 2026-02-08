@extends('layouts.admin')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="bg-gradient-to-br from-sky-500 via-sky-600 to-indigo-700 rounded-3xl p-6 sm:p-8 shadow-xl text-white relative overflow-hidden">
            <div class="absolute inset-y-0 left-0 w-40 bg-white/10 blur-3xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-4">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/15 text-sm font-semibold">
                        <i class="fas fa-route"></i>
                        إنشاء مسار تعلّم جديد
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-bold">أضف رحلة تعليمية متكاملة</h1>
                    <p class="text-sm text-white/80 max-w-2xl">
                        اجمع تحت هذا المسار مجموعات المهارات والكورسات التي تخدم هدفاً تعليمياً واحداً. اختر رمزاً ولوناً معبّرين وحدد ترتيب الظهور للطلاب وفريق المحتوى.
                    </p>
                </div>
                <a href="{{ route('admin.academic-years.index') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/40 px-5 py-2 text-sm font-semibold hover:bg-white/10 transition">
                    <i class="fas fa-arrow-right"></i>
                    العودة للمسارات
                </a>
            </div>
        </div>

        <div class="dashboard-card rounded-2xl card-hover-effect border-2 border-gray-200/50 hover:border-sky-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);">
            <div class="border-b border-gray-100 px-6 sm:px-8 py-5">
                <h2 class="text-xl font-semibold text-gray-900">بيانات المسار</h2>
                <p class="text-sm text-gray-500 mt-1">
                    أدخل الاسم، الرمز، الوصف، واختر الأيقونة واللون. يمكنك تعيين ترتيب الظهور وحالة المسار أثناء الإنشاء.
                </p>
            </div>
            <form method="POST" action="{{ route('admin.academic-years.store') }}" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-8">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                            اسم المسار <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition"
                               placeholder="مثال: مسار تطوير الواجهة الأمامية">
                        @error('name')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">
                            رمز المسار <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="code" id="code" value="{{ old('code') }}" required
                               class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition"
                               placeholder="مثال: FE-TRACK أو AI-PATH">
                        <p class="mt-1 text-xs text-gray-500">
                            رمز مختصر باللغة الإنجليزية لربط المسار مع الكورسات المرتبطة.
                        </p>
                        @error('code')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                            الوصف
                        </label>
                        <textarea name="description" id="description" rows="4"
                                  class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition"
                                  placeholder="اشرح الهدف من المسار، الجمهور المستهدف، والنواتج التعليمية المتوقعة.">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label for="video_url" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-video text-sky-600 ml-1"></i>
                            رابط فيديو المقدمة
                        </label>
                        <input type="url" name="video_url" id="video_url" value="{{ old('video_url') }}"
                               class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition"
                               placeholder="https://www.youtube.com/watch?v=... أو https://youtu.be/...">
                        <p class="mt-1 text-xs text-gray-500">
                            رابط فيديو مقدمة المسار التعليمي الذي سيتم عرضه في صفحة المسار. يدعم روابط YouTube و Vimeo.
                        </p>
                        @error('video_url')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label for="thumbnail" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-image text-sky-600 ml-1"></i>
                            صورة مصغرة للمسار
                        </label>
                        <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                               class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
                        <p class="mt-1 text-xs text-gray-500">
                            صورة مصغرة للمسار التعليمي. سيتم عرضها في قوائم المسارات.
                        </p>
                        @error('thumbnail')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-tag text-sky-600 ml-1"></i>
                            سعر المسار (ج.م)
                        </label>
                        <input type="number" name="price" id="price" value="{{ old('price', 0) }}" min="0" step="0.01"
                               class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition"
                               placeholder="0">
                        <p class="mt-1 text-xs text-gray-500">
                            اتركه 0 إذا كان المسار مجانياً.
                        </p>
                        @error('price')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="icon" class="block text-sm font-semibold text-gray-700 mb-2">
                            الأيقونة
                        </label>
                        <select name="icon" id="icon"
                                class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition">
                            @php
                                $icons = [
                                    'fas fa-compass' => '🧭 مسار',
                                    'fas fa-graduation-cap' => '🎓 تعليم أكاديمي',
                                    'fas fa-laptop-code' => '💻 برمجة',
                                    'fas fa-robot' => '🤖 ذكاء اصطناعي',
                                    'fas fa-briefcase' => '💼 مسار مهني',
                                    'fas fa-vial' => '🧪 علوم وتجارب',
                                    'fas fa-globe' => '🌍 لغات ومهارات',
                                ];
                            @endphp
                            @foreach($icons as $value => $label)
                                <option value="{{ $value }}" {{ old('icon') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('icon')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="color" class="block text-sm font-semibold text-gray-700 mb-2">
                            اللون
                        </label>
                        <input type="color" name="color" id="color" value="{{ old('color', '#0ea5e9') }}"
                               class="w-full h-12 rounded-2xl border border-gray-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500/40">
                        <p class="mt-1 text-xs text-gray-500">
                            يستخدم لتلوين البطاقة في لوحة التحكم.
                        </p>
                        @error('color')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="order" class="block text-sm font-semibold text-gray-700 mb-2">
                            ترتيب الظهور
                        </label>
                        <input type="number" name="order" id="order" value="{{ old('order', 0) }}" min="0"
                               class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-4 focus:ring-sky-500/20 transition"
                               placeholder="0">
                        <p class="mt-1 text-xs text-gray-500">
                            0 تعني أن المسار يظهر أولاً ضمن القائمة.
                        </p>
                        @error('order')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 px-4 py-3 rounded-2xl bg-slate-100 border border-slate-200">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-5 h-5 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                    <div>
                        <label for="is_active" class="text-sm font-semibold text-gray-800">المسار نشط</label>
                        <p class="text-xs text-gray-500">
                            المسارات النشطة متاحة لإضافة مجموعات مهارية وكورسات جديدة.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify_between gap-4 pt-4 border-t border-gray-100">
                    <span class="text-xs text-gray-500">
                        بعد حفظ المسار يمكنك إضافة مجموعات مهارية وربط الكورسات ضمنه.
                    </span>
                    <div class="flex flex-col md:flex-row md:items-center gap-3">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-sky-600 via-blue-600 to-sky-600 hover:from-sky-700 hover:via-blue-700 hover:to-sky-700 text-white px-6 py-3 text-sm font-bold shadow-lg shadow-sky-600/30 hover:shadow-xl hover:shadow-sky-600/40 hover:-translate-y-0.5 transition-all duration-300">
                            <i class="fas fa-save"></i>
                            حفظ المسار
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="text-sm font-medium text-blue-800 mb-2">أمثلة على المسارات التعليمية:</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm text-blue-700">
                <span>• مسار تطوير الواجهة الأمامية</span>
                <span>• مسار الذكاء الاصطناعي</span>
                <span>• مسار الأمن السيبراني</span>
                <span>• مسار تحليل البيانات</span>
                <span>• الصف الأول الثانوي</span>
                <span>• الصف الثاني الثانوي</span>
                <span>• الصف الثالث الثانوي</span>
                <span>• الصف الأول الإعدادي</span>
            </div>
        </div>
    </div>
</div>
@endsection