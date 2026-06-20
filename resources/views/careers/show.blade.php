@extends('layouts.public')

@section('title', $job->title . ' — التقديم | Mindlytics')

@push('styles')
@include('careers._styles')
@endpush

@section('content')
@php
    $meta = array_filter([
        $job->department,
        $job->location,
        $job->employment_type,
    ]);
@endphp

<section class="hero-careers min-h-[38vh] flex items-center relative pt-24 pb-14 lg:pt-28 lg:pb-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <a href="{{ route('careers.index') }}" class="inline-flex items-center gap-2 text-blue-100 hover:text-white text-sm font-semibold mb-5 transition-colors">
            <i class="fas fa-arrow-right"></i>
            جميع الوظائف
        </a>
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-black text-white leading-tight mb-4 max-w-3xl" style="text-shadow: 0 2px 12px rgba(0,0,0,0.3);">
            {{ $job->title }}
        </h1>
        @if($meta !== [])
            <div class="flex flex-wrap gap-2">
                @if($job->department)
                    <span class="job-meta-pill"><i class="fas fa-building"></i>{{ $job->department }}</span>
                @endif
                @if($job->location)
                    <span class="job-meta-pill"><i class="fas fa-map-marker-alt"></i>{{ $job->location }}</span>
                @endif
                @if($job->employment_type)
                    <span class="job-meta-pill"><i class="fas fa-clock"></i>{{ $job->employment_type }}</span>
                @endif
            </div>
        @endif
    </div>
</section>

<section class="py-12 md:py-16 bg-gradient-to-b from-slate-50 to-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-6xl">
        @if(session('success'))
            <div class="mb-8 rounded-2xl border-2 border-emerald-200 bg-emerald-50 text-emerald-800 px-5 py-4 text-sm font-semibold flex items-start gap-3">
                <i class="fas fa-check-circle text-xl mt-0.5"></i>
                <div>
                    <p class="font-black text-base mb-1">تم إرسال طلبك بنجاح</p>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-8 rounded-2xl border-2 border-rose-200 bg-rose-50 text-rose-800 px-5 py-4 text-sm">
                <p class="font-bold mb-2 flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> يوجد أخطاء في النموذج:</p>
                <ul class="list-disc list-inside space-y-0.5 mr-1">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid lg:grid-cols-12 gap-8 items-start">
            {{-- نموذج التقديم --}}
            <div class="lg:col-span-7">
                <div class="flex items-center gap-3 mb-6">
                    <div class="section-bar rounded-full"></div>
                    <h2 class="text-2xl font-bold text-slate-800">نموذج التقديم</h2>
                </div>

                <div class="bg-white rounded-2xl shadow-md border-2 border-slate-100 overflow-hidden" x-data="{ cvName: '', attachCount: 0 }">
                    <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-blue-50 to-white">
                        <p class="text-sm text-slate-600 flex items-start gap-2">
                            <i class="fas fa-shield-alt text-blue-500 mt-0.5"></i>
                            <span>بياناتك وملفاتك محمية — تُخزَّن على Cloudflare (R2) ولا تُشارَك إلا مع فريق التوظيف.</span>
                        </p>
                    </div>

                    <form method="post" action="{{ route('careers.apply', $job) }}" enctype="multipart/form-data" class="p-6 space-y-5">
                        @csrf

                        <div>
                            <label class="careers-label">الاسم بالكامل <span class="text-rose-500">*</span></label>
                            <input name="full_name" value="{{ old('full_name') }}" required placeholder="مثال: أحمد محمد" class="careers-input">
                        </div>

                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="careers-label">البريد الإلكتروني</label>
                                <input name="email" type="email" value="{{ old('email') }}" placeholder="name@email.com" class="careers-input">
                            </div>
                            <div>
                                <label class="careers-label">رقم الهاتف</label>
                                <input name="phone" value="{{ old('phone') }}" placeholder="01xxxxxxxxx" class="careers-input" dir="ltr">
                            </div>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="careers-label">LinkedIn</label>
                                <input name="linkedin_url" type="url" value="{{ old('linkedin_url') }}" placeholder="https://linkedin.com/in/..." class="careers-input" dir="ltr">
                            </div>
                            <div>
                                <label class="careers-label">Portfolio / GitHub</label>
                                <input name="portfolio_url" type="url" value="{{ old('portfolio_url') }}" placeholder="https://..." class="careers-input" dir="ltr">
                            </div>
                        </div>

                        <div>
                            <label class="careers-label">رسالة تعريفية (اختياري)</label>
                            <textarea name="cover_letter" rows="4" placeholder="لماذا ترغب بالانضمام لهذه الوظيفة؟" class="careers-input resize-y min-h-[100px]">{{ old('cover_letter') }}</textarea>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="careers-label">السيرة الذاتية (CV) <span class="text-rose-500">*</span></label>
                                <label class="upload-zone block cursor-pointer">
                                    <input type="file" name="cv" required accept=".pdf,.doc,.docx" class="sr-only"
                                           @change="cvName = $event.target.files[0]?.name || ''">
                                    <div class="text-center">
                                        <div class="w-12 h-12 mx-auto mb-2 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl">
                                            <i class="fas fa-file-upload"></i>
                                        </div>
                                        <p class="text-sm font-bold text-slate-800" x-text="cvName || 'اختر ملف CV'"></p>
                                        <p class="text-xs text-slate-500 mt-1">PDF أو Word — حد أقصى 10 MB</p>
                                    </div>
                                </label>
                            </div>
                            <div>
                                <label class="careers-label">مرفقات إضافية (اختياري)</label>
                                <label class="upload-zone block cursor-pointer">
                                    <input type="file" name="attachments[]" multiple accept=".pdf,.doc,.docx,.png,.jpg,.jpeg,.zip" class="sr-only"
                                           @change="attachCount = $event.target.files?.length || 0">
                                    <div class="text-center">
                                        <div class="w-12 h-12 mx-auto mb-2 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl">
                                            <i class="fas fa-paperclip"></i>
                                        </div>
                                        <p class="text-sm font-bold text-slate-800" x-text="attachCount ? attachCount + ' ملف/ملفات' : 'إرفاق ملفات'"></p>
                                        <p class="text-xs text-slate-500 mt-1">حتى 5 ملفات</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="pt-2 flex flex-col sm:flex-row sm:items-center gap-4">
                            <button type="submit" class="careers-btn-primary w-full sm:w-auto">
                                <i class="fas fa-paper-plane"></i>
                                إرسال الطلب
                            </button>
                            <p class="text-xs text-slate-500">بالضغط على «إرسال» أنت توافق على مراجعة بياناتك من فريق HR.</p>
                        </div>
                    </form>
                </div>
            </div>

            {{-- الشريط الجانبي --}}
            <aside class="lg:col-span-5 space-y-6">
                @if($job->description || $job->requirements)
                    <div class="bg-white rounded-2xl shadow-md border-2 border-slate-100 p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <h3 class="text-lg font-black text-slate-900">تفاصيل الوظيفة</h3>
                        </div>
                        <div class="space-y-4 text-sm text-slate-700 leading-relaxed">
                            @if($job->description)
                                <div>
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">الوصف</p>
                                    <div class="whitespace-pre-line">{!! nl2br(e($job->description)) !!}</div>
                                </div>
                            @endif
                            @if($job->requirements)
                                <div class="pt-4 border-t border-slate-100">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">المتطلبات</p>
                                    <div class="whitespace-pre-line">{!! nl2br(e($job->requirements)) !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="bg-white rounded-2xl shadow-md border-2 border-slate-100 p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                            <i class="fas fa-route"></i>
                        </div>
                        <h3 class="text-lg font-black text-slate-900">خطوات التقديم</h3>
                    </div>
                    <ol class="space-y-4">
                        <li class="step-item">
                            <span class="step-num">1</span>
                            <div>
                                <p class="text-sm font-bold text-slate-900">املأ النموذج</p>
                                <p class="text-xs text-slate-500 mt-0.5">أدخل بياناتك وارفع CV</p>
                            </div>
                        </li>
                        <li class="step-item">
                            <span class="step-num">2</span>
                            <div>
                                <p class="text-sm font-bold text-slate-900">مراجعة HR</p>
                                <p class="text-xs text-slate-500 mt-0.5">فريق التوظيف يقيّم طلبك</p>
                            </div>
                        </li>
                        <li class="step-item">
                            <span class="step-num">3</span>
                            <div>
                                <p class="text-sm font-bold text-slate-900">التواصل معك</p>
                                <p class="text-xs text-slate-500 mt-0.5">في حال الملاءمة — مقابلة أو عرض</p>
                            </div>
                        </li>
                    </ol>
                </div>

                <div class="rounded-2xl border-2 border-blue-100 bg-gradient-to-br from-blue-50 to-white p-5">
                    <p class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-question-circle text-blue-600"></i>
                        استفسار؟
                    </p>
                    <p class="text-xs text-slate-600 mt-2">لأي سؤال عن الوظيفة أو عملية التقديم، تواصل معنا.</p>
                    <a href="{{ route('public.contact') }}" class="inline-flex items-center gap-2 mt-3 text-sm font-bold text-blue-700 hover:text-blue-900">
                        صفحة التواصل
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
