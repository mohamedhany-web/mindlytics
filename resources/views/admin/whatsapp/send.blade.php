@extends('layouts.admin')

@section('title', 'إرسال واتساب - Mindlytics')
@section('header', 'قسم الواتساب')

@section('content')
@php
    $connectionBadges = [
        'ready' => ['label' => 'متصل', 'class' => 'bg-emerald-100 text-emerald-800 border-emerald-200'],
        'qr' => ['label' => 'بانتظار QR', 'class' => 'bg-amber-100 text-amber-800 border-amber-200'],
        'unreachable' => ['label' => 'الجسر غير متاح', 'class' => 'bg-rose-100 text-rose-800 border-rose-200'],
    ];
    $connBadge = $connectionBadges[$connectionStatus] ?? ['label' => $connectionStatus, 'class' => 'bg-slate-100 text-slate-700 border-slate-200'];
    $templatesJson = $templates->map(fn ($t) => [
        'id' => $t->id,
        'title' => $t->title,
        'content' => $t->content,
    ])->values();
    $studentsJson = $students->map(fn ($s) => [
        'id' => $s->id,
        'name' => $s->name,
        'phone' => $s->phone,
    ])->values();
@endphp

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.whatsapp._alerts')
    @include('admin.whatsapp._nav', ['active' => 'send'])

    @include('admin.whatsapp._page-header', [
        'title' => 'إرسال رسالة واتساب',
        'subtitle' => 'رسالة فردية، طالب، كورس، أو إرسال جماعي — مع معاينة حية وقوالب جاهزة.',
        'icon' => 'fas fa-paper-plane',
        'actions' => '
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border ' . $connBadge['class'] . '">
                <span class="w-2 h-2 rounded-full ' . ($connectionStatus === 'ready' ? 'bg-emerald-500 animate-pulse' : 'bg-current opacity-60') . '"></span>
                ' . $connBadge['label'] . '
            </span>
            <a href="' . route('admin.whatsapp.index') . '" class="' . $waBtnSecondary . '"><i class="fas fa-qrcode"></i> الاتصال</a>
        ',
    ])

    @if($connectionStatus !== 'ready')
        <div class="rounded-2xl border-2 border-amber-200 bg-amber-50 px-5 py-4 flex flex-col sm:flex-row sm:items-center gap-3 text-sm text-amber-900">
            <div class="flex items-start gap-3 flex-1">
                <i class="fas fa-exclamation-triangle text-amber-600 text-lg mt-0.5"></i>
                <div>
                    <p class="font-bold">الواتساب غير متصل بالكامل</p>
                    <p class="mt-0.5 text-amber-800/90">تأكد من Bridge على VPS وامسح QR قبل الإرسال — وإلا ستُسجَّل الرسائل كفاشلة.</p>
                </div>
            </div>
            <a href="{{ route('admin.whatsapp.index') }}" class="{{ $waBtnPrimary }} text-sm shrink-0">فتح لوحة الاتصال</a>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 sm:gap-6"
         x-data="whatsappSendForm({
            templates: @json($templatesJson),
            students: @json($studentsJson),
            oldRecipient: @json(old('recipient_type', 'manual')),
            oldMessage: @json(old('message', '')),
            oldPhone: @json(old('phone', '')),
            oldUserId: @json(old('user_id', '')),
            oldCourseId: @json(old('course_id', '')),
            oldTemplateId: @json(old('template_id', '')),
         })">

        {{-- النموذج الرئيسي --}}
        <div class="xl:col-span-8 space-y-4">
            <section class="{{ $waSectionClass }}">
                <div class="px-5 py-4 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-users text-emerald-600"></i>
                        المستلمون
                    </h3>
                    <p class="text-sm text-slate-500 mt-1">اختر طريقة تحديد من تريد مراسلته</p>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                        @foreach([
                            'manual' => ['icon' => 'fas fa-phone', 'label' => 'رقم يدوي', 'desc' => 'إدخال رقم مباشرة'],
                            'single_student' => ['icon' => 'fas fa-user-graduate', 'label' => 'طالب', 'desc' => 'اختر من القائمة'],
                            'course_students' => ['icon' => 'fas fa-book', 'label' => 'طلاب كورس', 'desc' => 'إرسال جماعي'],
                            'all_students' => ['icon' => 'fas fa-users', 'label' => 'كل الطلاب', 'desc' => 'كل من لديه رقم'],
                        ] as $type => $meta)
                            <label class="cursor-pointer group">
                                <input type="radio" name="recipient_type_radio" value="{{ $type }}" x-model="recipientType" class="sr-only peer">
                                <div class="h-full p-4 rounded-xl border-2 border-slate-200 bg-white transition-all
                                            peer-checked:border-emerald-500 peer-checked:bg-emerald-50/80 peer-checked:shadow-md peer-checked:shadow-emerald-500/10
                                            hover:border-emerald-300 group-hover:shadow-sm">
                                    <i class="{{ $meta['icon'] }} text-emerald-600 text-lg mb-2 block"></i>
                                    <p class="font-bold text-slate-900 text-sm">{{ $meta['label'] }}</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $meta['desc'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="{{ $waSectionClass }}">
                <div class="px-5 py-4 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fab fa-whatsapp text-emerald-600"></i>
                        محتوى الرسالة
                    </h3>
                </div>
                <div class="p-5 sm:p-6">
                    <form method="POST" action="{{ route('admin.whatsapp.send.post') }}" @submit="onSubmit">
                        @csrf
                        <input type="hidden" name="recipient_type" :value="recipientType">

                        {{-- رقم يدوي --}}
                        <div x-show="recipientType === 'manual'" x-cloak class="mb-5">
                            <label class="{{ $waLabelClass }}">رقم الهاتف</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400"><i class="fas fa-phone"></i></span>
                                <input type="text" name="phone" x-model="phone"
                                       placeholder="01012345678 أو 201012345678"
                                       :required="recipientType === 'manual'"
                                       class="{{ $waInputClass }} pl-10 dir-ltr text-right font-mono">
                            </div>
                            <p class="text-xs text-slate-500 mt-1.5">يُضاف رمز مصر +20 تلقائياً</p>
                            @error('phone')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- طالب واحد --}}
                        <div x-show="recipientType === 'single_student'" x-cloak class="mb-5">
                            <label class="{{ $waLabelClass }}">اختر الطالب</label>
                            <select name="user_id" x-model="userId" :required="recipientType === 'single_student'"
                                    class="{{ $waSelectClass }}" @change="onStudentPick">
                                <option value="">— اختر طالباً —</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" data-phone="{{ $student->phone }}">
                                        {{ $student->name }} — {{ $student->phone }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- كورس --}}
                        <div x-show="recipientType === 'course_students'" x-cloak class="mb-5">
                            <label class="{{ $waLabelClass }}">اختر الكورس</label>
                            <select name="course_id" x-model="courseId" :required="recipientType === 'course_students'"
                                    class="{{ $waSelectClass }}">
                                <option value="">— اختر كورساً —</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                            @error('course_id')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- تحذير جماعي --}}
                        <div x-show="recipientType === 'all_students'" x-cloak
                             class="mb-5 rounded-xl bg-violet-50 border border-violet-200 px-4 py-3 text-sm text-violet-900 flex items-start gap-2">
                            <i class="fas fa-info-circle mt-0.5"></i>
                            <span>سيتم الإرسال لـ <strong>{{ $students->count() }}</strong> طالب لديهم رقم هاتف — مع فاصل 0.5 ثانية بين كل رسالة.</span>
                        </div>

                        {{-- قالب --}}
                        <div class="mb-5">
                            <label class="{{ $waLabelClass }}">قالب رسالة (اختياري)</label>
                            <select name="template_id" x-model="templateId" @change="applyTemplate" class="{{ $waSelectClass }}">
                                <option value="">— رسالة مخصصة —</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}">{{ $template->title }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-slate-500 mt-1.5">
                                مع القالب في الإرسال الجماعي تُستبدل المتغيرات لكل طالب تلقائياً
                                · <a href="{{ route('admin.messages.templates') }}" class="text-emerald-600 hover:underline">إدارة القوالب</a>
                            </p>
                        </div>

                        {{-- نص الرسالة --}}
                        <div class="mb-5">
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="{{ $waLabelClass }} mb-0">نص الرسالة</label>
                                <span class="text-xs font-mono tabular-nums"
                                      :class="charCount > 4000 ? 'text-rose-600 font-bold' : 'text-slate-500'"
                                      x-text="charCount + ' / 4096'"></span>
                            </div>
                            <textarea name="message" rows="8" required maxlength="4096" x-model="message"
                                      class="{{ $waTextareaClass }}"
                                      placeholder="اكتب رسالتك هنا...&#10;&#10;يمكنك استخدام *نص عريض* و _مائل_ كما في واتساب"></textarea>
                            @error('message')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- أزرار --}}
                        <div class="flex flex-wrap items-center gap-2 pt-4 border-t border-slate-100">
                            <button type="submit" class="{{ $waBtnPrimary }}" :disabled="submitting">
                                <i class="fas fa-paper-plane" :class="submitting && 'fa-spinner fa-spin'"></i>
                                <span x-text="submitting ? 'جاري الإرسال...' : submitLabel()"></span>
                            </button>
                            <button type="button" @click="resetForm()" class="{{ $waBtnSecondary }}">
                                <i class="fas fa-undo"></i> مسح
                            </button>
                            <a href="{{ route('admin.whatsapp.messages') }}" class="{{ $waBtnSecondary }} mr-auto">
                                <i class="fas fa-history"></i> السجل
                            </a>
                        </div>
                    </form>
                </div>
            </section>
        </div>

        {{-- الشريط الجانبي --}}
        <div class="xl:col-span-4 space-y-4">
            {{-- معاينة واتساب --}}
            <section class="{{ $waSectionClass }} overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 bg-gradient-to-r from-emerald-600 to-green-600">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fab fa-whatsapp"></i>
                        معاينة الرسالة
                    </h3>
                </div>
                <div class="p-4 bg-[#e5ddd5] min-h-[320px]" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23d4cdc4\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
                    <div class="max-w-[280px] mr-auto">
                        <div class="bg-[#dcf8c6] rounded-lg rounded-tr-none shadow-md p-3 relative">
                            <p class="text-[10px] font-bold text-emerald-800 mb-1">{{ config('app.name', 'Mindlytics') }}</p>
                            <p class="text-sm text-slate-900 whitespace-pre-wrap break-words leading-relaxed min-h-[60px]"
                               x-text="previewText"></p>
                            <div class="flex items-center justify-end gap-1 mt-1">
                                <span class="text-[10px] text-slate-500">{{ now()->format('H:i') }}</span>
                                <i class="fas fa-check-double text-[10px] text-sky-500"></i>
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-600 mt-2 text-center" x-show="recipientType !== 'manual'" x-text="recipientHint"></p>
                    </div>
                </div>
            </section>

            {{-- متغيرات --}}
            <section class="{{ $waSectionClass }}">
                <div class="px-5 py-3 border-b border-slate-200">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-code text-violet-600"></i>
                        متغيرات القوالب
                    </h3>
                </div>
                <div class="p-4 grid grid-cols-1 gap-1.5 text-xs">
                    @foreach([
                        '{{student_name}}' => 'اسم الطالب',
                        '{{courses_count}}' => 'عدد الكورسات',
                        '{{avg_score}}' => 'متوسط الدرجات',
                        '{{month_name}}' => 'الشهر الحالي',
                        '{{date}}' => 'التاريخ',
                        '{{platform_name}}' => 'اسم المنصة',
                    ] as $var => $desc)
                        <button type="button" @click="insertVariable(@json($var))"
                                class="flex items-center justify-between gap-2 px-3 py-2 rounded-lg hover:bg-emerald-50 border border-transparent hover:border-emerald-200 text-right transition-colors group">
                            <code class="bg-slate-100 group-hover:bg-white px-1.5 py-0.5 rounded font-mono text-emerald-700">{{ $var }}</code>
                            <span class="text-slate-500">{{ $desc }}</span>
                        </button>
                    @endforeach
                </div>
            </section>

            {{-- آخر الرسائل --}}
            @if($recentMessages->isNotEmpty())
                <section class="{{ $waSectionClass }}">
                    <div class="px-5 py-3 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-slate-900">آخر الإرسالات</h3>
                        <a href="{{ route('admin.whatsapp.messages') }}" class="text-xs text-emerald-600 hover:underline">الكل</a>
                    </div>
                    <ul class="divide-y divide-slate-100 max-h-[220px] overflow-y-auto">
                        @foreach($recentMessages as $msg)
                            <li class="px-4 py-3 hover:bg-slate-50/80 transition-colors">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-mono text-slate-600 dir-ltr text-right">+{{ $msg->phone_number }}</p>
                                        <p class="text-xs text-slate-700 truncate mt-0.5">{{ Str::limit($msg->message, 45) }}</p>
                                    </div>
                                    <span class="shrink-0 inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold
                                        {{ $msg->status === 'sent' ? 'bg-emerald-100 text-emerald-700' : ($msg->status === 'failed' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                                        {{ $msg->status_text }}
                                    </span>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1">{{ $msg->created_at?->diffForHumans() }}</p>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function whatsappSendForm(config) {
    return {
        recipientType: config.oldRecipient || 'manual',
        message: config.oldMessage || '',
        phone: config.oldPhone || '',
        userId: config.oldUserId || '',
        courseId: config.oldCourseId || '',
        templateId: config.oldTemplateId || '',
        templates: config.templates || [],
        students: config.students || [],
        submitting: false,

        get charCount() {
            return (this.message || '').length;
        },

        get previewText() {
            if (!this.message || !this.message.trim()) {
                return 'اكتب رسالتك لرؤية المعاينة هنا...';
            }
            return this.message;
        },

        get recipientHint() {
            if (this.recipientType === 'single_student' && this.userId) {
                const s = this.students.find(x => String(x.id) === String(this.userId));
                return s ? `إلى: ${s.name}` : '';
            }
            if (this.recipientType === 'all_students') {
                return `إرسال جماعي — ${this.students.length} طالب`;
            }
            if (this.recipientType === 'course_students') {
                return 'إرسال لجميع طلاب الكورس المسجّلين';
            }
            return '';
        },

        applyTemplate() {
            const t = this.templates.find(x => String(x.id) === String(this.templateId));
            if (t && t.content) {
                this.message = t.content;
            }
        },

        onStudentPick() {
            const s = this.students.find(x => String(x.id) === String(this.userId));
            if (s) this.phone = s.phone;
        },

        insertVariable(v) {
            this.message = (this.message || '') + v;
        },

        submitLabel() {
            const map = {
                manual: 'إرسال الآن',
                single_student: 'إرسال للطالب',
                course_students: 'إرسال لطلاب الكورس',
                all_students: 'إرسال جماعي',
            };
            return map[this.recipientType] || 'إرسال';
        },

        onSubmit(e) {
            if (this.recipientType === 'all_students' || this.recipientType === 'course_students' || this.recipientType === 'single_student') {
                const n = this.recipientType === 'single_student' ? 1
                    : (this.recipientType === 'course_students' ? 'طلاب الكورس' : this.students.length);
                if (!confirm('بدء إرسال ' + n + ' رسالة في الخلفية؟\n\nسيتم توجيهك لصفحة متابعة — من تم ومن فشل.')) {
                    e.preventDefault();
                    return;
                }
            }
            this.submitting = true;
        },

        resetForm() {
            this.message = '';
            this.phone = '';
            this.userId = '';
            this.courseId = '';
            this.templateId = '';
            this.recipientType = 'manual';
        },
    };
}
</script>
@endpush
@endsection
