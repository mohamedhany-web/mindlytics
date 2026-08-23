@extends('layouts.employee')

@section('title', 'طلب فيديو جديد')
@section('header', 'طلب فيديو جديد لمحرر الفيديو')

@section('content')
<div class="w-full max-w-none space-y-6">
    <div class="relative overflow-hidden rounded-2xl border border-cyan-200/60 bg-gradient-to-br from-cyan-600 via-sky-600 to-blue-700 p-6 sm:p-8 text-white shadow-xl">
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/15 backdrop-blur-sm border border-white/20 shadow-lg">
                    <i class="fas fa-film text-2xl"></i>
                </div>
                <div>
                    <a href="{{ route('employee.montage-requests.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-white/90 hover:text-white mb-2 transition-colors">
                        <i class="fas fa-arrow-right text-xs opacity-80"></i>
                        العودة لطلبات محرر الفيديو
                    </a>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight">طلب فيديو لمحرر الفيديو</h1>
                    <p class="mt-2 text-sm sm:text-base text-white/85 max-w-2xl leading-relaxed">
                        اختر محرر الفيديو، حدّد الموعد النهائي، واكتب متطلبات الفيديو. ستُنشأ تلقائياً <strong class="text-white">مهمة</strong> في «مهامي» لمحرر الفيديو ويمكنه التسليم برابط Drive أو رفع ملف.
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if($editors->isEmpty())
        <div class="rounded-2xl border-2 border-amber-200 bg-gradient-to-r from-amber-50 to-orange-50 px-5 py-4 shadow-sm flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                <i class="fas fa-user-slash text-lg"></i>
            </div>
            <div class="text-sm text-amber-900">
                <p class="font-bold text-amber-950 mb-1">لا يوجد محررو فيديو متاحون</p>
                <p class="text-amber-800/90">اطلب من الإدارة إنشاء وظيفة برمز <code class="rounded-md bg-white/80 px-1.5 py-0.5 text-xs font-mono border border-amber-200">video_editing</code> وتعيينها لموظف نشط.</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-rose-800 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="post" action="{{ route('employee.montage-requests.store') }}" class="w-full">
        @csrf
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
            <div class="xl:col-span-8 space-y-6 min-w-0">
                <div class="rounded-2xl border border-gray-200/80 bg-white p-5 sm:p-8 shadow-lg shadow-gray-200/50 w-full">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-100 text-cyan-700">
                            <i class="fas fa-file-video"></i>
                        </span>
                        <div>
                            <h2 class="font-bold text-gray-900 text-lg">محتوى الطلب</h2>
                            <p class="text-xs text-gray-500">العنوان والمتطلبات التفصيلية للفيديو</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-bold text-gray-800 mb-2">عنوان الفيديو / الطلب <span class="text-red-500">*</span></label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" required maxlength="255"
                                   placeholder="مثال: ريلز إعلان كورس الذكاء الاصطناعي"
                                   class="w-full rounded-xl border border-gray-200 py-3 px-4 text-sm shadow-sm focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/30">
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-bold text-gray-800 mb-2">وصف مختصر <span class="text-gray-400 font-normal">(اختياري)</span></label>
                            <textarea id="description" name="description" rows="3"
                                      class="w-full rounded-xl border border-gray-200 py-3 px-4 text-sm shadow-sm focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/30"
                                      placeholder="سياق الطلب أو المنصة المستهدفة...">{{ old('description') }}</textarea>
                        </div>

                        <div>
                            <label for="requirements" class="block text-sm font-bold text-gray-800 mb-2">متطلبات الفيديو <span class="text-red-500">*</span></label>
                            <p class="text-xs text-gray-500 mb-2">المدة، النصوص، المقاطع، الموسيقى، الشعار، المقاسات، وأي ملفات مصدر مطلوبة.</p>
                            <textarea id="requirements" name="requirements" rows="14" required
                                      class="w-full rounded-xl border border-gray-200 py-4 px-4 text-sm shadow-sm focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/30 leading-relaxed min-h-[16rem]">{{ old('requirements') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="xl:col-span-4 space-y-6">
                <div class="rounded-2xl border border-gray-200/80 bg-white p-5 sm:p-6 shadow-lg shadow-gray-200/50 sticky top-24">
                    <h2 class="font-bold text-gray-900 text-lg mb-4 flex items-center gap-2">
                        <i class="fas fa-user-cog text-cyan-600"></i>
                        الإسناد والموعد
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label for="montage_employee_id" class="block text-sm font-bold text-gray-800 mb-2">محرر الفيديو <span class="text-red-500">*</span></label>
                            <select id="montage_employee_id" name="montage_employee_id" required
                                    class="w-full rounded-xl border border-gray-200 py-3 px-4 text-sm focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/30">
                                <option value="">اختر محرر الفيديو</option>
                                @foreach($editors as $editor)
                                    <option value="{{ $editor->id }}" @selected((string) old('montage_employee_id') === (string) $editor->id)>
                                        {{ $editor->name }} @if($editor->employeeJob)— {{ $editor->employeeJob->name }}@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="priority" class="block text-sm font-bold text-gray-800 mb-2">الأولوية <span class="text-red-500">*</span></label>
                            <select id="priority" name="priority" required class="w-full rounded-xl border border-gray-200 py-3 px-4 text-sm focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/30">
                                @foreach(['low' => 'منخفضة', 'medium' => 'متوسطة', 'high' => 'عالية', 'urgent' => 'عاجلة'] as $val => $label)
                                    <option value="{{ $val }}" @selected(old('priority', 'medium') === $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="deadline_at" class="block text-sm font-bold text-gray-800 mb-2">حد التسليم (Deadline) <span class="text-red-500">*</span></label>
                            <input type="datetime-local" id="deadline_at" name="deadline_at" value="{{ old('deadline_at') }}" required
                                   class="w-full rounded-xl border border-gray-200 py-3 px-4 text-sm focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/30">
                        </div>

                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-l from-cyan-600 to-blue-600 px-6 py-4 text-base font-black text-white shadow-lg disabled:opacity-45"
                                {{ $editors->isEmpty() ? 'disabled' : '' }}>
                            <i class="fas fa-paper-plane"></i>
                            إرسال لمحرر الفيديو كمهمة
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
