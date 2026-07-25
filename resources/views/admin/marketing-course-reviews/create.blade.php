@extends('layouts.admin')

@section('title', 'إضافة ريفيو كورس')
@section('header', 'إضافة ريفيو كورس')

@section('content')
@php
    $oldType = old('review_type', 'image');
@endphp
<div class="w-full">
    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900">ريفيو تسويقي جديد</h1>
                <p class="text-slate-500 mt-1">
                    الصور تُرفع على
                    <span class="font-semibold text-sky-700">Cloudflare R2</span>
                    وتظهر في سكرول آراء الطلاب بصفحة الكورس.
                </p>
            </div>
            <a href="{{ route('admin.marketing-course-reviews.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold">
                <i class="fas fa-arrow-right"></i>
                رجوع
            </a>
        </div>

        <form action="{{ route('admin.marketing-course-reviews.store') }}" method="POST" enctype="multipart/form-data"
              class="p-5 sm:p-8 space-y-6"
              x-data="marketingReviewForm(@js($oldType))"
              @submit="onSubmit">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">نوع الريفيو <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="review_type" value="image" class="sr-only peer" x-model="type">
                        <div class="h-full rounded-2xl border-2 border-slate-200 peer-checked:border-sky-500 peer-checked:bg-sky-50 p-4 transition">
                            <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center mb-3">
                                <i class="fas fa-image"></i>
                            </div>
                            <div class="font-bold text-slate-900 text-sm">صورة تقييم</div>
                            <p class="text-xs text-slate-500 mt-1">سكرين شوت واتساب / ريفيو بصري</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="review_type" value="text" class="sr-only peer" x-model="type">
                        <div class="h-full rounded-2xl border-2 border-slate-200 peer-checked:border-sky-500 peer-checked:bg-sky-50 p-4 transition">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-3">
                                <i class="fas fa-comment-dots"></i>
                            </div>
                            <div class="font-bold text-slate-900 text-sm">رأي نصي</div>
                            <p class="text-xs text-slate-500 mt-1">تقييم مكتوب بدون صورة</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="review_type" value="quote" class="sr-only peer" x-model="type">
                        <div class="h-full rounded-2xl border-2 border-slate-200 peer-checked:border-sky-500 peer-checked:bg-sky-50 p-4 transition">
                            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center mb-3">
                                <i class="fas fa-quote-right"></i>
                            </div>
                            <div class="font-bold text-slate-900 text-sm">اقتباس مميز</div>
                            <p class="text-xs text-slate-500 mt-1">نص بارز + صورة اختيارية</p>
                        </div>
                    </label>
                </div>
                @error('review_type')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">الكورس <span class="text-rose-500">*</span></label>
                <select name="course_id" required
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                    <option value="">اختر الكورس</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected((string) old('course_id', request('course_id')) === (string) $course->id)>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
                @error('course_id')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-1">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">اسم صاحب الرأي</label>
                    <input type="text" name="reviewer_name" value="{{ old('reviewer_name') }}" placeholder="مثال: أحمد محمد"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                    @error('reviewer_name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">المصدر</label>
                    <select name="source" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                        <option value="">بدون</option>
                        @foreach(['واتساب', 'فيسبوك', 'إنستجرام', 'تيليجرام', 'مراجعة داخل المنصة', 'أخرى'] as $src)
                            <option value="{{ $src }}" @selected(old('source') === $src)>{{ $src }}</option>
                        @endforeach
                    </select>
                    @error('source')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">التقييم</label>
                    <select name="rating" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" @selected((string) old('rating', '5') === (string) $i)>{{ $i }} / 5</option>
                        @endfor
                    </select>
                    @error('rating')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    نص التقييم
                    <span class="text-rose-500" x-show="type !== 'image'">*</span>
                    <span class="text-slate-400 font-normal" x-show="type === 'image'">(اختياري مع الصورة)</span>
                </label>
                <textarea name="comment" rows="4" placeholder="اكتب رأي الطالب هنا..."
                          class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500"
                          :required="type !== 'image'">{{ old('comment') }}</textarea>
                @error('comment')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div x-show="type === 'image' || type === 'quote'" x-cloak>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    صورة الريفيو
                    <span class="text-rose-500" x-show="type === 'image'">*</span>
                    <span class="text-slate-400 font-normal" x-show="type === 'quote'">(اختياري)</span>
                </label>
                <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/80 p-5">
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif"
                           class="w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-sky-600 file:text-white file:font-semibold file:cursor-pointer hover:file:bg-sky-700"
                           :required="type === 'image'"
                           x-ref="imageInput"
                           @change="onImageSelected($event)">
                    <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                        يتم ضغط الصورة تلقائياً في المتصفح قبل الرفع ثم تُخزَّن على Cloudflare R2.
                        الصيغ: JPG / PNG / WEBP / GIF — تجنّب HEIC من الآيفون.
                    </p>
                    <p class="mt-1 text-xs font-semibold text-slate-600" x-show="statusText" x-text="statusText"></p>
                    @error('image')
                        <p class="mt-2 text-sm text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                    <div x-show="preview" class="mt-4 max-w-sm rounded-2xl overflow-hidden border border-slate-200 bg-white">
                        <img :src="preview" alt="معاينة" class="w-full object-contain max-h-80">
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-6 pt-1">
                <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 cursor-pointer">
                    <input type="hidden" name="is_approved" value="0">
                    <input type="checkbox" name="is_approved" value="1" class="rounded border-slate-300 text-sky-500 focus:ring-sky-500"
                           @checked((string) old('is_approved', '1') === '1')>
                    نشر مباشرة على صفحة الكورس
                </label>
                <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 cursor-pointer">
                    <input type="hidden" name="is_featured" value="0">
                    <input type="checkbox" name="is_featured" value="1" class="rounded border-slate-300 text-amber-500 focus:ring-amber-500"
                           @checked((string) old('is_featured', '0') === '1')>
                    تمييز في أول القائمة
                </label>
            </div>

            <div class="flex gap-3 pt-4 border-t border-slate-100">
                <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white rounded-xl font-semibold shadow-lg shadow-sky-500/30 disabled:opacity-60"
                        :disabled="compressing">
                    <span x-show="!compressing">حفظ الريفيو</span>
                    <span x-show="compressing">جاري تجهيز الصورة...</span>
                </button>
                <a href="{{ route('admin.marketing-course-reviews.index') }}"
                   class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function marketingReviewForm(initialType) {
    return {
        type: initialType || 'image',
        preview: null,
        compressing: false,
        statusText: '',
        async onImageSelected(e) {
            const input = e.target;
            const file = input.files && input.files[0];
            if (!file) {
                this.preview = null;
                this.statusText = '';
                return;
            }

            this.compressing = true;
            this.statusText = 'جاري ضغط الصورة...';
            try {
                const originalMb = (file.size / (1024 * 1024)).toFixed(2);
                const compressed = await this.compressImage(file, 1600, 0.82, 1.6 * 1024 * 1024);
                const dt = new DataTransfer();
                dt.items.add(compressed);
                input.files = dt.files;
                if (this.preview) URL.revokeObjectURL(this.preview);
                this.preview = URL.createObjectURL(compressed);
                const newMb = (compressed.size / (1024 * 1024)).toFixed(2);
                this.statusText = 'تم الضغط: ' + originalMb + 'MB ← ' + newMb + 'MB — جاهز للرفع على Cloudflare';
            } catch (err) {
                console.error(err);
                this.statusText = 'تعذر الضغط التلقائي — سيتم رفع الملف كما هو إن سمح السيرفر.';
                if (this.preview) URL.revokeObjectURL(this.preview);
                this.preview = URL.createObjectURL(file);
            } finally {
                this.compressing = false;
            }
        },
        onSubmit(e) {
            if (this.compressing) {
                e.preventDefault();
                return;
            }
            if ((this.type === 'image') && this.$refs.imageInput && !this.$refs.imageInput.files.length) {
                e.preventDefault();
                this.statusText = 'اختر صورة الريفيو أولاً.';
            }
        },
        compressImage(file, maxWidth, quality, maxBytes) {
            return new Promise((resolve, reject) => {
                if (!file.type || !file.type.startsWith('image/')) {
                    resolve(file);
                    return;
                }
                // GIF قد يفقد الحركة — ارفعه كما هو إن كان صغيراً
                if (file.type === 'image/gif' && file.size <= maxBytes) {
                    resolve(file);
                    return;
                }

                const img = new Image();
                const url = URL.createObjectURL(file);
                img.onload = () => {
                    URL.revokeObjectURL(url);
                    let { width, height } = img;
                    if (width > maxWidth) {
                        height = Math.round(height * (maxWidth / width));
                        width = maxWidth;
                    }
                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    const tryEncode = (q) => new Promise((res) => {
                        canvas.toBlob((blob) => res(blob), 'image/jpeg', q);
                    });

                    (async () => {
                        let q = quality;
                        let blob = await tryEncode(q);
                        while (blob && blob.size > maxBytes && q > 0.45) {
                            q -= 0.08;
                            blob = await tryEncode(q);
                        }
                        if (!blob) {
                            reject(new Error('blob failed'));
                            return;
                        }
                        const name = (file.name || 'review').replace(/\.[^.]+$/, '') + '.jpg';
                        resolve(new File([blob], name, { type: 'image/jpeg', lastModified: Date.now() }));
                    })().catch(reject);
                };
                img.onerror = () => {
                    URL.revokeObjectURL(url);
                    reject(new Error('image load failed'));
                };
                img.src = url;
            });
        }
    };
}
</script>
@endpush
@endsection
