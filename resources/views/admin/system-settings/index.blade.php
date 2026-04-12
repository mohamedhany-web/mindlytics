@extends('layouts.admin')

@section('title', 'إعدادات النظام')
@section('header', 'إعدادات النظام')

@section('content')
<div class="w-full min-h-screen bg-gradient-to-b from-slate-50 via-white to-slate-100">
    <div class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-10 space-y-8">

        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200/80 bg-emerald-50/90 text-emerald-900 px-5 py-4 text-sm font-semibold shadow-sm flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-white"><i class="fas fa-check"></i></span>
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="rounded-2xl border border-amber-200/80 bg-amber-50/90 text-amber-950 px-5 py-4 text-sm font-semibold shadow-sm flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-white"><i class="fas fa-exclamation-triangle"></i></span>
                {{ session('warning') }}
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-900 shadow-sm">
                <p class="font-bold mb-2">يرجى تصحيح ما يلي:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <div>
                <h2 class="text-2xl lg:text-3xl font-black text-slate-900 tracking-tight">لوحة التحكم الشاملة</h2>
                <p class="mt-2 text-slate-600 text-sm lg:text-base max-w-3xl leading-relaxed">الهوية البصرية للمنصة وطريقة استقبال المدفوعات (طلبات الكورسات/المسارات والفواتير). التغييرات تُطبَّق على الواجهات العامة فور الحفظ.</p>
            </div>
        </div>

        <form method="post" action="{{ route('admin.system-settings.update') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 lg:gap-8">

                {{-- الهوية البصرية --}}
                <section class="rounded-3xl border border-slate-200/80 bg-white shadow-xl shadow-slate-200/50 overflow-hidden">
                    <div class="px-6 py-5 lg:px-8 lg:py-6 border-b border-slate-100 bg-gradient-to-l from-blue-600 to-indigo-600 text-white">
                        <div class="flex items-center gap-3">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 backdrop-blur">
                                <i class="fas fa-palette text-xl"></i>
                            </span>
                            <div>
                                <h3 class="text-lg font-black">الهوية البصرية</h3>
                                <p class="text-sm text-blue-100/90 mt-0.5">الشعار والأيقونة تظهر في لوحة الإدارة وأنحاء الموقع</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 lg:p-8 space-y-8">
                        <div class="grid sm:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <label class="text-sm font-bold text-slate-800">شعار الموقع (Logo)</label>
                                <p class="text-xs text-slate-500 leading-relaxed">PNG, JPG, WebP, GIF — حتى 2 ميغابايت</p>
                                <div class="flex items-center gap-4">
                                    <div class="h-28 w-28 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 flex items-center justify-center p-2 overflow-hidden shrink-0">
                                        <img src="{{ $logoUrl }}" alt="" class="max-h-full max-w-full object-contain" id="logo-preview">
                                    </div>
                                    <input type="file" name="logo" accept="image/png,image/jpeg,image/jpg,image/webp,image/gif"
                                           class="block w-full min-w-0 text-sm text-slate-700 file:mr-0 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                                </div>
                            </div>
                            <div class="space-y-3">
                                <label class="text-sm font-bold text-slate-800">أيقونة المتصفح (Favicon)</label>
                                <p class="text-xs text-slate-500 leading-relaxed">ICO, PNG, SVG, WebP — حتى 512 كيلوبايت</p>
                                <div class="flex items-center gap-4">
                                    <div class="h-20 w-20 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 flex items-center justify-center p-2 shrink-0">
                                        <img src="{{ $faviconUrl }}" alt="" class="max-h-full max-w-full object-contain" id="favicon-preview" width="48" height="48">
                                    </div>
                                    <input type="file" name="favicon" accept=".ico,.png,.svg,.webp"
                                           class="block w-full min-w-0 text-sm text-slate-700 file:mr-0 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-slate-700 file:text-white hover:file:bg-slate-800 cursor-pointer">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- الدفع --}}
                <section class="rounded-3xl border border-slate-200/80 bg-white shadow-xl shadow-slate-200/50 overflow-hidden">
                    <div class="px-6 py-5 lg:px-8 lg:py-6 border-b border-slate-100 bg-gradient-to-l from-emerald-600 to-teal-600 text-white">
                        <div class="flex items-center gap-3">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 backdrop-blur">
                                <i class="fas fa-money-bill-wave text-xl"></i>
                            </span>
                            <div>
                                <h3 class="text-lg font-black">طريقة الدفع عبر المنصة</h3>
                                <p class="text-sm text-emerald-100/90 mt-0.5">تؤثر على صفحة إتمام الطلب (كورس/مسار) وصفحة تفاصيل فاتورتك</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 lg:p-8 space-y-5">
                        <p class="text-sm text-slate-600 leading-relaxed">اختر كيف يدفع العميل: تحويل يدوي مع رفع إيصال، أو بوابة كاشير، أو فواتيرك (iframe) لشراء الكورسات مع مفاتيح Vendor/Provider في ملف البيئة.</p>

                        <div class="grid gap-4">
                            <label class="relative flex cursor-pointer rounded-2xl border-2 p-4 transition-all has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50 border-slate-200 hover:border-slate-300">
                                <input type="radio" name="platform_payment_mode" value="manual" class="mt-1 text-emerald-600 focus:ring-emerald-500" @checked(old('platform_payment_mode', $platformPaymentMode) === 'manual')>
                                <div class="mr-4 flex-1">
                                    <span class="font-bold text-slate-900">دفع يدوي</span>
                                    <p class="text-sm text-slate-600 mt-1">صفحة الطلب تعرض بيانات التحويل ورفع صورة الإيصال. مناسب للمراجعة اليدوية قبل التفعيل.</p>
                                </div>
                            </label>

                            <label class="relative flex cursor-pointer rounded-2xl border-2 p-4 transition-all has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50/50 border-slate-200 hover:border-slate-300">
                                <input type="radio" name="platform_payment_mode" value="kashier" class="mt-1 text-blue-600 focus:ring-blue-500" @checked(old('platform_payment_mode', $platformPaymentMode) === 'kashier')>
                                <div class="mr-4 flex-1">
                                    <span class="font-bold text-slate-900">بوابة كاشير (الحالية)</span>
                                    <p class="text-sm text-slate-600 mt-1">دفع إلكتروني عبر iframe كاشير كما هو مُفعّل اليوم في النظام.</p>
                                </div>
                            </label>

                            <label class="relative flex cursor-pointer rounded-2xl border-2 p-4 transition-all has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/50 border-slate-200 hover:border-slate-300">
                                <input type="radio" name="platform_payment_mode" value="fawaterak" class="mt-1 text-indigo-600 focus:ring-indigo-500" @checked(old('platform_payment_mode', $platformPaymentMode) === 'fawaterak')>
                                <div class="mr-4 flex-1">
                                    <span class="font-bold text-slate-900">فواتيرك (iframe — الكورسات)</span>
                                    <p class="text-sm text-slate-600 mt-1">دفع إلكتروني عبر إضافة فواتيرك في صفحة إتمام طلب الكورس. يتطلب FAWATERAK_VENDOR_KEY و FAWATERAK_PROVIDER_KEY و FAWATERAK_INTEGRATION=iframe في .env وتطابق نطاق الـ HMAC مع لوحة فواتيرك.</p>
                                </div>
                            </label>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white p-4 space-y-2">
                            <label class="flex items-start gap-3 cursor-pointer text-sm text-slate-800">
                                <input type="checkbox" name="fawaterak_gateway_on" value="1" class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" @checked(old('fawaterak_gateway_on', $fawaterakGatewayEnabled ?? true))>
                                <span><span class="font-bold">تفعيل بوابة فواتيرك</span> — عند إلغاء التفعيل لن يظهر مسار الدفع الإلكتروني لفواتيرك حتى لو كان الوضع «فواتيرك» محدداً.</span>
                            </label>
                        </div>

                        <div id="gateway-fees" class="rounded-2xl border border-amber-200/80 bg-amber-50/40 p-5 space-y-4">
                            <h4 class="text-sm font-black text-amber-950 flex items-center gap-2">
                                <i class="fas fa-percentage text-amber-600"></i>
                                عمولة بوابة الدفع (محاسبية)
                            </h4>
                            <p class="text-xs text-amber-900/85 leading-relaxed">تُحسب عند إتمام دفع أونلاين عبر كاشير أو فواتيرك (وليس التحويل اليدوي). تُسجَّل في الدفعة وتُنشأ لها <strong>معاملة مدينة</strong> (فئة fee) في سجل المعاملات. يمكن أيضاً ضبط القيم عبر متغيرات البيئة <code class="bg-white/80 px-1 rounded">GATEWAY_FEE_MODE</code> و<code class="bg-white/80 px-1 rounded">GATEWAY_FEE_PERCENT</code> و<code class="bg-white/80 px-1 rounded">GATEWAY_FEE_FIXED_EGP</code> كافتراضي قبل دمج JSON.</p>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">طريقة احتساب العمولة</label>
                                <select name="gateway_fee_mode" class="w-full max-w-md rounded-xl border border-slate-200 px-3 py-2.5 text-sm">
                                    <option value="none" @selected(old('gateway_fee_mode', $gatewayFeeMode ?? 'none') === 'none')>بدون عمولة مسجّلة</option>
                                    <option value="percent" @selected(old('gateway_fee_mode', $gatewayFeeMode ?? 'none') === 'percent')>نسبة من إجمالي العملية (%)</option>
                                    <option value="fixed" @selected(old('gateway_fee_mode', $gatewayFeeMode ?? 'none') === 'fixed')>مبلغ ثابت لكل عملية (ج.م)</option>
                                </select>
                            </div>
                            <div class="grid sm:grid-cols-2 gap-4 max-w-2xl">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">النسبة % (عند اختيار نسبة)</label>
                                    <input type="number" name="gateway_fee_percent" step="0.01" min="0" max="100" value="{{ old('gateway_fee_percent', $gatewayFeePercent ?? '0') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">مبلغ ثابت ج.م (عند اختيار ثابت)</label>
                                    <input type="number" name="gateway_fee_fixed" step="0.01" min="0" value="{{ old('gateway_fee_fixed', $gatewayFeeFixed ?? '0') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-slate-50 border border-slate-200/80 p-4 text-xs text-slate-600 leading-relaxed">
                            <i class="fas fa-lightbulb text-amber-500 ml-1"></i>
                            تأكد من ضبط <strong>محافظ المنصة</strong> من الإدارة عند استخدام الدفع اليدوي حتى تظهر أرقام التحويل للطلاب.
                        </div>
                    </div>
                </section>
            </div>

            <div class="flex flex-wrap items-center gap-4 pt-2">
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl bg-gradient-to-l from-blue-600 to-indigo-600 text-white text-sm font-black shadow-lg shadow-blue-500/25 hover:from-blue-700 hover:to-indigo-700 transition-all">
                    <i class="fas fa-save"></i>
                    حفظ كل الإعدادات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
