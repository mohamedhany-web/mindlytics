@extends('layouts.employee')

@section('title', 'طلب تصميم جديد')
@section('header', 'طلب تصميم جديد')

@section('content')
<div class="w-full max-w-none space-y-6">
    {{-- شريط علوي: تنقل + عنوان --}}
    <div class="relative overflow-hidden rounded-2xl border border-fuchsia-200/60 bg-gradient-to-br from-fuchsia-600 via-violet-600 to-indigo-700 p-6 sm:p-8 text-white shadow-xl">
        <div class="absolute inset-0 opacity-20 pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.35\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/15 backdrop-blur-sm border border-white/20 shadow-lg">
                    <i class="fas fa-palette text-2xl"></i>
                </div>
                <div>
                    <a href="{{ route('employee.design-cycles.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-white/90 hover:text-white mb-2 transition-colors">
                        <i class="fas fa-arrow-right text-xs opacity-80"></i>
                        العودة لطلبات التصميم
                    </a>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight">طلب تصميم جديد</h1>
                    <p class="mt-2 text-sm sm:text-base text-white/85 max-w-2xl leading-relaxed">
                        اختر المصمم، حدّد الموعد النهائي، واكتب المواصفات بوضوح. ستُنشأ تلقائياً <strong class="text-white">مهمة</strong> للمصمم في نظام المهام مع إشعار بالبريد عند توفره.
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if($designers->isEmpty())
        <div class="rounded-2xl border-2 border-amber-200 bg-gradient-to-r from-amber-50 to-orange-50 px-5 py-4 shadow-sm flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                <i class="fas fa-user-slash text-lg"></i>
            </div>
            <div class="text-sm text-amber-900">
                <p class="font-bold text-amber-950 mb-1">لا يوجد مصممون متاحون</p>
                <p class="text-amber-800/90">اطلب من الإدارة إنشاء وظيفة برمز <code class="rounded-md bg-white/80 px-1.5 py-0.5 text-xs font-mono border border-amber-200">designer</code> وتعيينها لموظف نشط.</p>
            </div>
        </div>
    @endif

    <form method="post" action="{{ route('employee.design-cycles.store') }}" class="w-full">
        @csrf
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
            {{-- المحتوى أولاً على الموبايل؛ على xl يأخذ الجزء الأوسع --}}
            <div class="xl:col-span-8 space-y-6 min-w-0 order-1">
                <div class="rounded-2xl border border-gray-200/80 bg-white p-5 sm:p-8 shadow-lg shadow-gray-200/50 w-full">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 text-violet-700">
                            <i class="fas fa-file-alt"></i>
                        </span>
                        <div>
                            <h2 class="font-bold text-gray-900 text-lg">محتوى الطلب</h2>
                            <p class="text-xs text-gray-500">العنوان والوصف والمواصفات التفصيلية — بعرض كامل</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-bold text-gray-800 mb-2">عنوان الطلب <span class="text-red-500">*</span></label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" required maxlength="255"
                                   placeholder="مثال: غلاف إعلان لكورس X — نسخة سوشيال"
                                   class="w-full rounded-xl border border-gray-200 py-3 px-4 text-sm text-gray-900 placeholder:text-gray-400 shadow-sm transition focus:border-fuchsia-500 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/30">
                            @error('title')
                                <p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-bold text-gray-800 mb-2">وصف مختصر <span class="text-gray-400 font-normal">(اختياري)</span></label>
                            <textarea id="description" name="description" rows="3" placeholder="سطر أو اثنان لسياق الطلب..."
                                      class="w-full rounded-xl border border-gray-200 py-3 px-4 text-sm text-gray-900 placeholder:text-gray-400 shadow-sm transition focus:border-fuchsia-500 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/30 resize-y min-h-[5rem]">{{ old('description') }}</textarea>
                        </div>

                        <div>
                            <label for="specifications" class="block text-sm font-bold text-gray-800 mb-2">
                                تفاصيل التصميم المطلوب <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-gray-500 mb-2">كلما كانت أدق، كان التسليم أقرب للمطلوب.</p>
                            <textarea id="specifications" name="specifications" rows="16" required
                                      placeholder="الأبعاد (بكسل أو سم)، الألوان (أكواد أو اسم الهوية)، الخطوط، النصوص الحرفية، أي شعارات يجب استخدامها، مراجع (روابط)، عدد النسخ أو المقاسات، وما يُسلَّم في النهاية."
                                      class="w-full rounded-xl border border-gray-200 py-4 px-4 text-sm text-gray-900 placeholder:text-gray-400 shadow-sm transition focus:border-fuchsia-500 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/30 resize-y min-h-[20rem] leading-relaxed">{{ old('specifications') }}</textarea>
                            @error('specifications')
                                <p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- إرسال على الموبايل والتابلت بعد تعبئة المحتوى --}}
                <div class="xl:hidden">
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-l from-fuchsia-600 to-violet-600 px-6 py-4 text-base font-black text-white shadow-lg shadow-fuchsia-500/20 disabled:cursor-not-allowed disabled:opacity-45"
                            {{ $designers->isEmpty() ? 'disabled' : '' }}>
                        <i class="fas fa-paper-plane"></i>
                        إرسال للمصمم كمهمة
                    </button>
                </div>
            </div>

            {{-- عمود الإسناد: sticky على الشاشات الكبيرة فقط --}}
            <div class="xl:col-span-4 space-y-6 order-2 w-full">
                <div class="xl:sticky xl:top-4 space-y-6">
                    <div class="rounded-2xl border border-gray-200/80 bg-white p-5 sm:p-6 shadow-lg shadow-gray-200/50 w-full">
                        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-fuchsia-100 text-fuchsia-700">
                                <i class="fas fa-user-check"></i>
                            </span>
                            <div>
                                <h2 class="font-bold text-gray-900">الإسناد والموعد</h2>
                                <p class="text-xs text-gray-500">من ينفّذ ومتى ينتهي</p>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <label for="designer_employee_id" class="block text-sm font-bold text-gray-800 mb-2">
                                    المصمم <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-400">
                                        <i class="fas fa-paint-brush text-sm"></i>
                                    </span>
                                    <select id="designer_employee_id" name="designer_employee_id" required
                                            class="w-full appearance-none rounded-xl border border-gray-200 bg-gray-50/50 py-3 pr-10 pl-4 text-sm font-medium text-gray-900 shadow-sm transition focus:border-fuchsia-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-fuchsia-500/30 disabled:opacity-50 disabled:cursor-not-allowed"
                                            {{ $designers->isEmpty() ? 'disabled' : '' }}>
                                        <option value="">— اختر المصمم —</option>
                                        @foreach($designers as $d)
                                            <option value="{{ $d->id }}" @selected(old('designer_employee_id') == $d->id)>
                                                {{ $d->name }}@if($d->employeeJob) — {{ $d->employeeJob->name }}@endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('designer_employee_id')
                                    <p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="priority" class="block text-sm font-bold text-gray-800 mb-2">
                                    الأولوية <span class="text-red-500">*</span>
                                </label>
                                <select id="priority" name="priority" required
                                        class="w-full rounded-xl border border-gray-200 bg-gray-50/50 py-3 px-4 text-sm font-medium text-gray-900 shadow-sm transition focus:border-fuchsia-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-fuchsia-500/30">
                                    @foreach(['low' => 'منخفضة', 'medium' => 'متوسطة', 'high' => 'عالية', 'urgent' => 'عاجلة'] as $v => $l)
                                        <option value="{{ $v }}" @selected(old('priority', 'medium') === $v)>{{ $l }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="deadline_at" class="block text-sm font-bold text-gray-800 mb-2">
                                    حد أقصى لتسليم المصمم <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" id="deadline_at" name="deadline_at" value="{{ old('deadline_at') }}" required
                                       class="w-full rounded-xl border border-gray-200 bg-gray-50/50 py-3 px-4 text-sm font-medium text-gray-900 shadow-sm transition focus:border-fuchsia-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-fuchsia-500/30">
                                @error('deadline_at')
                                    <p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-indigo-100 bg-indigo-50/60 p-4 sm:p-5">
                        <p class="text-xs font-bold text-indigo-900 mb-2 flex items-center gap-2">
                            <i class="fas fa-lightbulb text-amber-500"></i>
                            نصائح سريعة
                        </p>
                        <ul class="text-xs text-indigo-900/85 space-y-2 leading-relaxed list-none">
                            <li class="flex gap-2"><span class="text-fuchsia-600 font-bold">•</span> اذكر الصيغ النهائية (PNG، PDF، إلخ).</li>
                            <li class="flex gap-2"><span class="text-fuchsia-600 font-bold">•</span> أرفق روابط مرجعية في خانة التفاصيل.</li>
                            <li class="flex gap-2"><span class="text-fuchsia-600 font-bold">•</span> الموعد يظهر للمصمم كموعد نهائي للمهمة.</li>
                        </ul>
                    </div>

                    <div class="hidden xl:block">
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-l from-fuchsia-600 to-violet-600 px-6 py-4 text-base font-black text-white shadow-lg shadow-fuchsia-500/25 transition hover:from-fuchsia-700 hover:to-violet-700 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-45"
                                {{ $designers->isEmpty() ? 'disabled' : '' }}>
                            <i class="fas fa-paper-plane"></i>
                            إرسال للمصمم كمهمة
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
