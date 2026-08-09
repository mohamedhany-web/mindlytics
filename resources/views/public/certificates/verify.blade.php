@php
    $__pageLocale = app()->getLocale();
    $__pageRtl = $__pageLocale === 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ $__pageLocale }}" dir="{{ $__pageRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التحقق من الشهادة - Mindlytics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50">
    <div class="min-h-screen py-12 px-4">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-3xl sm:text-4xl font-black text-slate-900 mb-2">التحقق من الشهادة</h1>
                <p class="text-slate-600">أدخل الرقم التسلسلي (Serial) المطبوع على الشهادة لعرض بيانات الحاصل عليها</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 mb-8 border border-slate-100">
                <form method="GET" action="{{ route('public.certificates.verify') }}" class="flex flex-col sm:flex-row gap-3">
                    <input type="text"
                           name="code"
                           value="{{ request('code') }}"
                           placeholder="مثال: MIND-2026-XXXXXXXX-1234"
                           class="flex-1 px-5 py-3.5 border-2 border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-base font-mono">
                    <button type="submit"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3.5 rounded-xl font-bold transition shadow-lg shadow-emerald-600/20">
                        <i class="fas fa-search ml-2"></i>
                        تحقق
                    </button>
                </form>
            </div>

            @if(isset($certificate) && $certificate && ($isValid ?? false))
                @php $user = $certificate->user; @endphp
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-emerald-500 mb-8">
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-6 text-white">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-2xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-black">شهادة صحيحة وموثّقة</h2>
                                <p class="text-emerald-50 font-mono text-sm mt-1">{{ $certificate->serial_number ?? $certificate->certificate_number }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-base font-black text-slate-900 mb-4 flex items-center gap-2">
                                <i class="fas fa-user text-emerald-600"></i> بيانات الحاصل على الشهادة
                            </h3>
                            <dl class="space-y-3 text-sm">
                                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                                    <dt class="text-slate-500">الاسم</dt>
                                    <dd class="font-bold text-slate-900 text-end">{{ data_get($certificate->metadata, 'display_name') ?: ($user->name ?? '—') }}</dd>
                                </div>
                                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                                    <dt class="text-slate-500">البريد الإلكتروني</dt>
                                    <dd class="font-semibold text-slate-900 text-end break-all">{{ $user->email ?? '—' }}</dd>
                                </div>
                                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                                    <dt class="text-slate-500">الهاتف</dt>
                                    <dd class="font-semibold text-slate-900 text-end" dir="ltr">{{ $user->phone ?? '—' }}</dd>
                                </div>
                                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                                    <dt class="text-slate-500">معرف المستخدم</dt>
                                    <dd class="font-mono text-slate-800">#{{ $user->id ?? '—' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-base font-black text-slate-900 mb-4 flex items-center gap-2">
                                <i class="fas fa-certificate text-emerald-600"></i> بيانات الشهادة والكورس
                            </h3>
                            <dl class="space-y-3 text-sm">
                                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                                    <dt class="text-slate-500">الكورس</dt>
                                    <dd class="font-bold text-slate-900 text-end">{{ $certificate->course?->title ?? $certificate->course_name ?? '—' }}</dd>
                                </div>
                                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                                    <dt class="text-slate-500">المدرب</dt>
                                    <dd class="font-semibold text-slate-900 text-end">{{ $certificate->instructor_signature_name ?? $certificate->instructor?->name ?? $certificate->course?->instructor?->name ?? '—' }}</dd>
                                </div>
                                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                                    <dt class="text-slate-500">رقم الشهادة</dt>
                                    <dd class="font-mono text-slate-800 text-end">{{ $certificate->certificate_number }}</dd>
                                </div>
                                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                                    <dt class="text-slate-500">الرقم التسلسلي</dt>
                                    <dd class="font-mono font-bold text-emerald-800 text-end">{{ $certificate->serial_number }}</dd>
                                </div>
                                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                                    <dt class="text-slate-500">تاريخ الإصدار</dt>
                                    <dd class="font-semibold text-slate-900">{{ optional($certificate->issued_at ?? $certificate->issue_date)->format('Y-m-d') ?? '—' }}</dd>
                                </div>
                                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                                    <dt class="text-slate-500">التصميم</dt>
                                    <dd class="font-semibold text-slate-900">{{ \App\Models\Certificate::availableTemplates()[$certificate->template]['name'] ?? $certificate->template }}</dd>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <dt class="text-slate-500">الحالة</dt>
                                    <dd><span class="inline-flex rounded-full bg-emerald-100 text-emerald-800 px-2.5 py-0.5 text-xs font-bold">مُصدرة ومعتمدة</span></dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 p-6 sm:p-8 bg-slate-50">
                        <h3 class="text-base font-black text-slate-900 mb-4">معاينة الشهادة</h3>
                        <div class="overflow-auto rounded-xl bg-slate-900 p-3">
                            @include('components.certificate-templates', [
                                'certificate' => $certificate,
                                'branding' => $branding ?? null,
                                'template' => $certificate->template ?? 'emerald-classic',
                                'templateDomId' => 'verify-certificate-template',
                            ])
                        </div>
                    </div>
                </div>
            @elseif(isset($certificate) && $certificate && !($isValid ?? false))
                <div class="bg-white rounded-2xl shadow-xl p-8 border-2 border-rose-500">
                    <div class="text-center">
                        <div class="w-20 h-20 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-times-circle text-rose-600 text-4xl"></i>
                        </div>
                        <h2 class="text-2xl font-black text-slate-900 mb-2">شهادة غير صالحة</h2>
                        <p class="text-rose-600 font-semibold">{{ $error ?? 'تعذر التحقق من الشهادة' }}</p>
                    </div>
                </div>
            @elseif(isset($error))
                <div class="bg-white rounded-2xl shadow-xl p-8 border-2 border-amber-400">
                    <div class="text-center">
                        <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-exclamation-triangle text-amber-600 text-4xl"></i>
                        </div>
                        <h2 class="text-2xl font-black text-slate-900 mb-2">تنبيه</h2>
                        <p class="text-amber-700 font-semibold">{{ $error }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
