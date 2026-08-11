@extends('layouts.public')

@section('title', $internship->title . ' | تدريب Mindlytics')
@section('meta_description', \Illuminate\Support\Str::limit($internship->summary ?: strip_tags($internship->description ?? ''), 160))

@section('content')
<section class="py-8 md:py-12 bg-slate-50" style="padding-top: 6rem;">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('public.internships.index') }}" class="inline-flex items-center gap-2 text-blue-700 font-medium mb-6">
            <i class="fas fa-arrow-right"></i> كل فرص التدريب
        </a>

        @if(session('success'))
            <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-800 font-semibold">{{ session('success') }}</div>
        @endif

        <article class="bg-white border border-gray-200 rounded-2xl p-6 md:p-8 mb-8">
            <div class="flex flex-wrap gap-2 mb-4">
                @if($internship->is_featured)
                    <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg">Featured</span>
                @endif
                <span class="text-xs font-bold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg">{{ $internship->typeLabel() }}</span>
                @if($internship->department)
                    <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg">{{ $internship->department }}</span>
                @endif
                @unless($internship->isOpenForApply())
                    <span class="text-xs font-bold text-rose-700 bg-rose-50 px-2.5 py-1 rounded-lg">التقديم مغلق</span>
                @endunless
            </div>

            <h1 class="text-2xl md:text-3xl font-black text-gray-900 mb-3">{{ $internship->title }}</h1>
            @if($internship->summary)
                <p class="text-gray-600 mb-6">{{ $internship->summary }}</p>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8 text-sm">
                <div class="rounded-xl bg-slate-50 border border-gray-100 p-3"><div class="text-xs text-gray-500">الموقع</div><div class="font-bold">{{ $internship->location ?: '—' }}</div></div>
                <div class="rounded-xl bg-slate-50 border border-gray-100 p-3"><div class="text-xs text-gray-500">المدة</div><div class="font-bold">{{ $internship->duration ?: '—' }}</div></div>
                <div class="rounded-xl bg-slate-50 border border-gray-100 p-3"><div class="text-xs text-gray-500">المقاعد</div><div class="font-bold">{{ $internship->seats ?: 'مفتوحة' }}</div></div>
                <div class="rounded-xl bg-slate-50 border border-gray-100 p-3"><div class="text-xs text-gray-500">آخر موعد</div><div class="font-bold">{{ $internship->application_deadline?->format('Y-m-d') ?: '—' }}</div></div>
            </div>

            @if($internship->description)
                <div class="mb-6">
                    <h2 class="font-bold text-gray-900 mb-2">عن الفرصة</h2>
                    <div class="text-gray-600 whitespace-pre-line">{{ $internship->description }}</div>
                </div>
            @endif
            @if($internship->requirements)
                <div class="mb-6">
                    <h2 class="font-bold text-gray-900 mb-2">المتطلبات</h2>
                    <div class="text-gray-600 whitespace-pre-line">{{ $internship->requirements }}</div>
                </div>
            @endif
            @if($internship->benefits)
                <div class="mb-6">
                    <h2 class="font-bold text-gray-900 mb-2">ماذا ستكتسب؟</h2>
                    <div class="text-gray-600 whitespace-pre-line">{{ $internship->benefits }}</div>
                </div>
            @endif
        </article>

        @if($internship->isOpenForApply())
            <div id="apply" class="bg-white border border-gray-200 rounded-2xl p-6 md:p-8">
                <h2 class="text-xl font-black text-gray-900 mb-2">قدّم الآن</h2>
                <p class="text-sm text-gray-500 mb-6">املأ النموذج وارفع سيرتك الذاتية. سنراجع طلبك ونتواصل معك.</p>

                @if($errors->any())
                    <div class="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-red-800 text-sm">
                        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('public.internships.apply', $internship->slug) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold mb-1">الاسم الكامل *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-xl border border-gray-200 px-4 py-2.5">
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1">البريد الإلكتروني *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-xl border border-gray-200 px-4 py-2.5" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1">الهاتف</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-xl border border-gray-200 px-4 py-2.5" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1">الجامعة</label>
                            <input type="text" name="university" value="{{ old('university') }}" class="w-full rounded-xl border border-gray-200 px-4 py-2.5">
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1">التخصص</label>
                            <input type="text" name="major" value="{{ old('major') }}" class="w-full rounded-xl border border-gray-200 px-4 py-2.5">
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1">السنة الدراسية</label>
                            <input type="text" name="year_of_study" value="{{ old('year_of_study') }}" class="w-full rounded-xl border border-gray-200 px-4 py-2.5" placeholder="ثالثة / خريج">
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1">Portfolio URL</label>
                            <input type="url" name="portfolio_url" value="{{ old('portfolio_url') }}" class="w-full rounded-xl border border-gray-200 px-4 py-2.5" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1">GitHub</label>
                            <input type="url" name="github_url" value="{{ old('github_url') }}" class="w-full rounded-xl border border-gray-200 px-4 py-2.5" dir="ltr">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold mb-1">LinkedIn</label>
                            <input type="url" name="linkedin_url" value="{{ old('linkedin_url') }}" class="w-full rounded-xl border border-gray-200 px-4 py-2.5" dir="ltr">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1">لماذا أنت مناسب لهذه الفرصة؟</label>
                        <textarea name="cover_letter" rows="4" class="w-full rounded-xl border border-gray-200 px-4 py-2.5">{{ old('cover_letter') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1">السيرة الذاتية (PDF/Word) *</label>
                        <input type="file" name="cv" accept=".pdf,.doc,.docx,application/pdf" required class="w-full text-sm">
                        <p class="text-xs text-gray-500 mt-1">حد أقصى 5 ميجابايت</p>
                    </div>
                    <button type="submit" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold">
                        <i class="fas fa-paper-plane"></i>
                        إرسال طلب التقديم
                    </button>
                </form>
            </div>
        @else
            <div class="bg-white border border-dashed border-gray-300 rounded-2xl p-8 text-center text-gray-600">
                التقديم على هذه الفرصة مغلق حالياً.
            </div>
        @endif
    </div>
</section>
@endsection
