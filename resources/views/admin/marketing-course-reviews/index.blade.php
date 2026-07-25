@extends('layouts.admin')

@section('title', 'ريفيوهات الكورسات')
@section('header', 'ريفيوهات الكورسات')

@section('content')
<div class="w-full space-y-6">
    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">ريفيوهات الكورسات</h1>
                <p class="text-slate-500 mt-1">صور ونصوص تقييمات الطلاب لعرضها في صفحة الكورس العامة.</p>
            </div>
            <a href="{{ route('admin.marketing-course-reviews.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white rounded-xl font-semibold shadow-lg shadow-sky-500/30 transition-all">
                <i class="fas fa-plus"></i>
                <span>ريفيو جديد</span>
            </a>
        </div>

        <div class="p-5 sm:p-8 space-y-6">
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800">{{ session('success') }}</div>
            @endif

            <form method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">الكورس</label>
                    <select name="course_id" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500/30">
                        <option value="">الكل</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" @selected((string) request('course_id') === (string) $course->id)>{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">الحالة</label>
                    <select name="status" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500/30">
                        <option value="">الكل</option>
                        <option value="approved" @selected(request('status') === 'approved')>منشور</option>
                        <option value="pending" @selected(request('status') === 'pending')>مخفي</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">النوع</label>
                    <select name="type" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500/30">
                        <option value="">الكل</option>
                        <option value="image" @selected(request('type') === 'image')>بصورة</option>
                        <option value="text" @selected(request('type') === 'text')>نصي</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button class="w-full px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-semibold">تصفية</button>
                </div>
            </form>

            @if($reviews->count() === 0)
                <div class="rounded-2xl border border-dashed border-slate-300 p-12 text-center text-slate-500">
                    <i class="fas fa-star text-4xl text-slate-300 mb-3 block"></i>
                    <p>لا توجد ريفيوهات بعد.
                        <a href="{{ route('admin.marketing-course-reviews.create') }}" class="text-sky-600 hover:underline font-semibold">أضف أول ريفيو</a>
                    </p>
                </div>
            @else
                <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($reviews as $review)
                        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden flex flex-col">
                            <div class="aspect-[4/5] bg-slate-100 relative">
                                @if($review->image_url)
                                    <img src="{{ $review->image_url }}" alt="ريفيو" class="absolute inset-0 w-full h-full object-cover">
                                @else
                                    <div class="absolute inset-0 flex flex-col items-center justify-center gap-2 p-6 text-center bg-gradient-to-br from-slate-50 to-sky-50">
                                        <i class="fas fa-quote-right text-2xl text-sky-400"></i>
                                        <p class="text-sm text-slate-600 line-clamp-6 leading-relaxed">{{ $review->body_text ?: 'رأي نصي' }}</p>
                                    </div>
                                @endif
                                <div class="absolute top-3 right-3 flex flex-col gap-1.5 items-end">
                                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full {{ $review->is_approved ? 'bg-emerald-500 text-white' : 'bg-amber-400 text-slate-900' }}">
                                        {{ $review->is_approved ? 'منشور' : 'مخفي' }}
                                    </span>
                                    @if($review->is_featured)
                                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-amber-500 text-white">مميز</span>
                                    @endif
                                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-white/90 text-slate-700">
                                        {{ $review->image_url ? 'صورة' : 'نص' }}
                                    </span>
                                </div>
                            </div>
                            <div class="p-4 space-y-2 flex-1">
                                <div class="font-bold text-slate-900 text-sm">{{ $review->course->title ?? '—' }}</div>
                                <div class="text-xs text-slate-500">{{ $review->display_name }} · {{ $review->created_at?->format('Y-m-d') }}</div>
                                <div class="flex items-center gap-0.5 text-xs">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= (int) $review->rating ? 'text-amber-400' : 'text-slate-200' }}"></i>
                                    @endfor
                                </div>
                                @if($review->image_url && $review->body_text !== '')
                                    <p class="text-sm text-slate-600 line-clamp-2">{{ $review->body_text }}</p>
                                @endif
                            </div>
                            <div class="p-4 pt-0 flex gap-2">
                                <form method="POST" action="{{ route('admin.marketing-course-reviews.toggle', $review) }}" class="flex-1">
                                    @csrf
                                    <button class="w-full rounded-xl border border-slate-200 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">
                                        {{ $review->is_approved ? 'إخفاء' : 'نشر' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.marketing-course-reviews.destroy', $review) }}" onsubmit="return confirm('حذف هذا الريفيو؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-xl border border-rose-200 text-rose-600 px-3 py-2 text-xs font-bold hover:bg-rose-50">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-2">{{ $reviews->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
