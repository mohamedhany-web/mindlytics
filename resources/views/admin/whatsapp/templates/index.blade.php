@extends('layouts.admin')

@section('title', 'قوالب الواتساب — Meta Templates')
@section('header', 'قسم الواتساب')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.whatsapp._alerts')

    @include('admin.whatsapp._page-header', [
        'title' => 'قوالب الواتساب',
        'subtitle' => 'قوالب Meta الرسمية + مكتبة رسائل مقترحة جاهزة لموظفي المبيعات.',
        'icon' => 'fas fa-file-alt',
        'actions' => '
            <form method="POST" action="' . route('admin.whatsapp.templates.sync') . '" class="inline">' . csrf_field() . '
                <button type="submit" class="' . $waBtnSecondary . '"><i class="fas fa-sync"></i> مزامنة Meta</button>
            </form>
            <a href="' . route('admin.whatsapp.templates.create') . '" class="' . $waBtnPrimary . '"><i class="fas fa-plus"></i> قالب Meta جديد</a>
        ',
        'statCards' => [
            ['label' => 'قوالب Meta', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-layer-group', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600'],
            ['label' => 'Meta معتمد', 'value' => number_format($stats['approved'] ?? 0), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
            ['label' => 'مقترحة للسيلز', 'value' => number_format($suggestedStats['total'] ?? 0), 'icon' => 'fas fa-wand-magic-sparkles', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600'],
            ['label' => 'Meta قيد المراجعة', 'value' => number_format($stats['pending'] ?? 0), 'icon' => 'fas fa-clock', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600'],
        ],
    ])

    <nav class="flex flex-wrap gap-2">
        <a href="{{ route('admin.whatsapp.templates.index', ['tab' => 'meta']) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold border transition-colors
           @if(($activeTab ?? 'meta') === 'meta') bg-emerald-600 text-white border-emerald-600 shadow-sm @else bg-white text-slate-700 border-slate-200 hover:border-slate-300 @endif">
            <i class="fas fa-cloud"></i>
            قوالب Meta الرسمية
            <span class="text-[10px] px-2 py-0.5 rounded-full @if(($activeTab ?? 'meta') === 'meta') bg-white/20 @else bg-slate-100 @endif">{{ number_format($stats['total'] ?? 0) }}</span>
        </a>
        <a href="{{ route('admin.whatsapp.templates.index', ['tab' => 'suggested']) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold border transition-colors
           @if(($activeTab ?? 'meta') === 'suggested') bg-violet-600 text-white border-violet-600 shadow-sm @else bg-white text-slate-700 border-slate-200 hover:border-slate-300 @endif">
            <i class="fas fa-wand-magic-sparkles"></i>
            مكتبة مقترحة للسيلز
            <span class="text-[10px] px-2 py-0.5 rounded-full @if(($activeTab ?? 'meta') === 'suggested') bg-white/20 @else bg-slate-100 @endif">{{ number_format($suggestedStats['total'] ?? 0) }}</span>
        </a>
    </nav>

    @if(!($connectionMeta['can_send'] ?? false) && ($activeTab ?? 'meta') === 'meta')
        <div class="rounded-2xl border-2 border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900">
            <i class="fas fa-exclamation-triangle ml-1"></i>
            أكمل <a href="{{ route('admin.whatsapp.settings') }}" class="font-bold underline">ربط Meta</a> (Access Token + WABA ID) قبل إرسال القوالب.
        </div>
    @endif

    @if(($activeTab ?? 'meta') === 'suggested')
        <div class="rounded-2xl border border-violet-200 bg-violet-50 px-5 py-4 text-sm text-violet-900">
            <p class="font-bold flex items-center gap-2">
                <i class="fas fa-info-circle"></i>
                مكتبة رسائل جاهزة لموظفي المبيعات
            </p>
            <p class="mt-2 text-violet-800/90 leading-relaxed">
                هذه <strong>مسودات نصوص</strong> للنسخ والتعديل داخل المحادثة. موظف السيلز يجدها أيضاً في صندوق الوارد (زر العصا السحرية).
                لإرسال رسالة <strong>خارج نافذة 24 ساعة</strong> تحتاج <strong>قالب Meta معتمد</strong> من التبويب الأول.
            </p>
            <p class="mt-2 text-xs text-violet-700">
                <i class="fas fa-clock ml-1"></i>
                إرسال Template Message من Meta يفتح نافذة محادثة لمدة 24 ساعة — بعدها قد تحتاج إرسال قالب مرة أخرى.
            </p>
        </div>

        @if(!($suggestedReady ?? false))
            <section class="{{ $waSectionClass }} p-8 text-center">
                <i class="fas fa-database text-4xl text-amber-500 mb-3"></i>
                <p class="font-bold text-slate-800 mb-2">جدول المكتبة غير موجود على السيرفر</p>
                <p class="text-sm text-slate-600">نفّذ <code class="bg-slate-100 px-1 rounded">php artisan migrate</code> ثم اضغط تحميل المكتبة.</p>
            </section>
        @elseif(($suggestedStats['total'] ?? 0) === 0)
            <section class="{{ $waSectionClass }} p-10 text-center">
                <i class="fas fa-wand-magic-sparkles text-5xl text-violet-400 mb-4"></i>
                <p class="font-bold text-slate-800 text-lg mb-2">المكتبة فارغة — لم تُحمَّل بعد</p>
                <p class="text-sm text-slate-600 mb-6 max-w-lg mx-auto">اضغط الزر لتحميل أكثر من 20 قالباً جاهزاً (ترحيب، متابعة، تسعير، دفع، إغلاق...).</p>
                <form method="POST" action="{{ route('admin.whatsapp.templates.seed-suggested') }}">
                    @csrf
                    <button type="submit" class="{{ $waBtnPrimary }}">
                        <i class="fas fa-download"></i>
                        تحميل المكتبة الافتراضية
                    </button>
                </form>
            </section>
        @else
            <section class="{{ $waSectionClass }}" x-data="suggestedTemplatesLibrary(@js($suggestedTemplates->map(fn ($t) => [
                'key' => $t->key,
                'title' => $t->title,
                'category' => $t->category,
                'category_label' => $t->categoryLabel(),
                'language' => $t->language,
                'body' => $t->body,
                'help' => $t->help,
                'variables' => $t->variables ?? [],
            ])->values()->all()))">
                <div class="px-5 py-4 border-b border-slate-200 space-y-3">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                        <input type="hidden" name="tab" value="suggested">
                        <div class="md:col-span-2">
                            <input type="search" name="suggested_search" value="{{ request('suggested_search') }}"
                                   placeholder="بحث في العنوان أو النص..."
                                   class="{{ $waInputClass }}">
                        </div>
                        <select name="suggested_category" class="{{ $waSelectClass }}">
                            <option value="">كل التصنيفات</option>
                            @foreach(\App\Models\WhatsAppSuggestedTemplate::categoryLabels() as $val => $lbl)
                                <option value="{{ $val }}" @selected(request('suggested_category') === $val)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        <select name="suggested_language" class="{{ $waSelectClass }}">
                            <option value="">كل اللغات</option>
                            <option value="ar" @selected(request('suggested_language') === 'ar')>العربية ({{ $suggestedStats['ar'] ?? 0 }})</option>
                            <option value="en" @selected(request('suggested_language') === 'en')>English ({{ $suggestedStats['en'] ?? 0 }})</option>
                        </select>
                        <div class="flex gap-2">
                            <button type="submit" class="{{ $waBtnDark }} flex-1"><i class="fas fa-search"></i></button>
                            @if(request()->anyFilled(['suggested_search','suggested_category','suggested_language']))
                                <a href="{{ route('admin.whatsapp.templates.index', ['tab' => 'suggested']) }}" class="{{ $waBtnSecondary }}"><i class="fas fa-times"></i></a>
                            @endif
                        </div>
                    </form>
                    <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-slate-600">
                        <span>عرض {{ $suggestedTemplates->count() }} من {{ $suggestedStats['total'] ?? 0 }} قالب</span>
                        <form method="POST" action="{{ route('admin.whatsapp.templates.seed-suggested') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-violet-700 hover:text-violet-900 font-semibold">
                                <i class="fas fa-redo ml-1"></i> إعادة تحميل المكتبة الافتراضية
                            </button>
                        </form>
                    </div>
                </div>

                <div class="grid lg:grid-cols-3 gap-0 divide-y lg:divide-y-0 lg:divide-x divide-slate-200 rtl:lg:divide-x-reverse">
                    <div class="lg:col-span-1 max-h-[70vh] overflow-auto">
                        @forelse($suggestedTemplates as $tpl)
                            <button type="button"
                                    @click="select(@js([
                                        'id' => $tpl->id,
                                        'key' => $tpl->key,
                                        'title' => $tpl->title,
                                        'category_label' => $tpl->categoryLabel(),
                                        'language' => $tpl->language,
                                        'body' => $tpl->body,
                                        'help' => $tpl->help,
                                        'variables' => $tpl->variables ?? [],
                                        'meta_status' => $tpl->metaTemplate?->status,
                                    ]))"
                                    class="w-full text-right px-5 py-4 border-b border-slate-100 hover:bg-violet-50/50 transition-colors"
                                    :class="selected?.key === @js($tpl->key) ? 'bg-violet-50 border-r-4 border-r-violet-500' : ''">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 text-sm">{{ $tpl->title }}</p>
                                        <div class="flex flex-wrap gap-1.5 mt-2">
                                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-bold">{{ $tpl->categoryLabel() }}</span>
                                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-sky-50 text-sky-700 font-bold uppercase">{{ $tpl->language }}</span>
                                            @if($tpl->metaTemplate)
                                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold">Meta: {{ $tpl->metaTemplate->statusLabel() }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </button>
                        @empty
                            <div class="p-8 text-center text-slate-500 text-sm">لا توجد نتائج للبحث الحالي.</div>
                        @endforelse
                    </div>
                    <div class="lg:col-span-2 p-5 min-h-[320px]">
                        <template x-if="selected">
                            <div class="space-y-4">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <h3 class="font-bold text-lg text-slate-900" x-text="selected.title"></h3>
                                        <p class="text-xs text-slate-500 mt-1">
                                            <span x-text="selected.category_label"></span>
                                            ·
                                            <span class="uppercase" x-text="selected.language"></span>
                                        </p>
                                    </div>
                                    <button type="button" @click="copyBody()"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold">
                                        <i class="fas fa-copy"></i>
                                        <span x-text="copied ? 'تم النسخ!' : 'نسخ النص'"></span>
                                    </button>
                                </div>
                                <div class="flex flex-wrap gap-2" x-show="selected?.id">
                                    <a :href="'{{ url('admin/whatsapp/templates/suggested') }}/' + selected.id + '/edit'"
                                       class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50">
                                        <i class="fas fa-edit"></i> تعديل
                                    </a>
                                </div>
                                <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                                    <p class="text-[11px] font-bold text-slate-500 mb-2">معاينة الرسالة</p>
                                    <pre class="whitespace-pre-wrap text-sm text-slate-800 leading-relaxed" x-text="selected.body"></pre>
                                </div>
                                <div class="rounded-xl bg-amber-50 border border-amber-100 p-4" x-show="selected.help">
                                    <p class="text-[11px] font-bold text-amber-800 mb-2"><i class="fas fa-lightbulb ml-1"></i> شرح الاستخدام</p>
                                    <pre class="whitespace-pre-wrap text-xs text-amber-900/90 leading-relaxed" x-text="selected.help"></pre>
                                </div>
                                <div x-show="selected.variables && selected.variables.length" class="text-xs text-slate-600">
                                    <span class="font-bold">المتغيرات:</span>
                                    <template x-for="v in selected.variables" :key="v">
                                        <code class="mx-1 bg-slate-100 px-1.5 py-0.5 rounded" x-text="'@{{' + v + '}}'"></code>
                                    </template>
                                </div>
                                <div class="flex flex-wrap gap-2 pt-2 border-t border-slate-100" x-show="selected?.id">
                                    <form method="POST" x-bind:action="'{{ url('admin/whatsapp/templates/suggested') }}/' + selected.id + '/meta-draft'">
                                        @csrf
                                        <button type="submit" class="{{ $waBtnSecondary }} text-xs"><i class="fas fa-file-export"></i> مسودة Meta</button>
                                    </form>
                                    <form method="POST" x-bind:action="'{{ url('admin/whatsapp/templates/suggested') }}/' + selected.id + '/submit-meta'">
                                        @csrf
                                        <button type="submit" class="{{ $waBtnPrimary }} text-xs"><i class="fab fa-meta"></i> إرسال لـ Meta</button>
                                    </form>
                                </div>
                            </div>
                        </template>
                        <div x-show="!selected" class="h-full flex flex-col items-center justify-center text-slate-400 py-16">
                            <i class="fas fa-hand-pointer text-4xl mb-3"></i>
                            <p class="font-semibold text-slate-600">اختر قالباً من القائمة لعرض المعاينة</p>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    @else

    <section class="{{ $waSectionClass }} p-5">
        <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
            <div>
                <h3 class="font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-user-shield text-violet-600"></i>
                    صلاحيات القوالب للموظفين
                </h3>
                <p class="text-xs text-slate-600 mt-1 max-w-2xl">
                    حدّد هل يرى موظفو المبيعات كل القوالب المعتمدة، أم قوالباً محددة تُعيَّن لكل قالب على حدة.
                </p>
            </div>
            <span class="text-[10px] px-2.5 py-1 rounded-full font-bold
                @if(($templateAccessMode ?? 'all') === 'restricted') bg-violet-100 text-violet-800 @else bg-emerald-100 text-emerald-800 @endif">
                {{ $templateAccessLabels[$templateAccessMode ?? 'all'] ?? '—' }}
            </span>
        </div>
        <form method="POST" action="{{ route('admin.whatsapp.templates.access-mode') }}" class="space-y-3">
            @csrf
            <div class="grid sm:grid-cols-2 gap-3">
                @foreach($templateAccessLabels ?? [] as $mode => $label)
                    <label class="flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition-colors
                        @if(($templateAccessMode ?? 'all') === $mode) border-violet-300 bg-violet-50/60 @else border-slate-200 hover:border-slate-300 @endif">
                        <input type="radio" name="template_access_mode" value="{{ $mode }}"
                               @checked(($templateAccessMode ?? 'all') === $mode)
                               class="mt-1 text-violet-600">
                        <span>
                            <span class="block text-sm font-bold text-slate-900">{{ $label }}</span>
                            <span class="block text-[11px] text-slate-500 mt-1">
                                @if($mode === 'all')
                                    كل موظفي المبيعات ومديري المبيعات يرون كل القوالب المعتمدة في المحادثات.
                                @else
                                    يظهر لكل موظف فقط القوالب التي تحدّدها له من صفحة كل قالب.
                                @endif
                            </span>
                        </span>
                    </label>
                @endforeach
            </div>
            <button type="submit" class="{{ $waBtnPrimary }} text-sm">
                <i class="fas fa-save"></i> حفظ إعداد الصلاحيات
            </button>
        </form>
        @if(($templateAccessMode ?? 'all') === 'restricted')
            <p class="text-[11px] text-violet-800 bg-violet-50 border border-violet-100 rounded-lg px-3 py-2 mt-3">
                <i class="fas fa-info-circle ml-1"></i>
                افتح أي قالب من الجدول أدناه وحدّد الموظفين المسموح لهم باستخدامه.
                @if(($salesStaff ?? collect())->isEmpty())
                    <strong class="block mt-1 text-amber-800">لا يوجد موظفو مبيعات نشطون حالياً.</strong>
                @endif
            </p>
        @endif
    </section>

    <section class="{{ $waSectionClass }}">
        <div class="px-5 py-4 border-b border-slate-200">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <div class="md:col-span-2">
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو المحتوى..."
                           class="{{ $waInputClass }}">
                </div>
                <select name="status" class="{{ $waSelectClass }}">
                    <option value="">كل الحالات</option>
                    @foreach(\App\Models\WhatsAppMetaTemplate::statusLabels() as $val => $lbl)
                        <option value="{{ $val }}" @selected(request('status') === $val)>{{ $lbl }}</option>
                    @endforeach
                </select>
                <select name="category" class="{{ $waSelectClass }}">
                    <option value="">كل الفئات</option>
                    @foreach(\App\Models\WhatsAppMetaTemplate::categoryLabels() as $val => $lbl)
                        <option value="{{ $val }}" @selected(request('category') === $val)>{{ $lbl }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="{{ $waBtnDark }} flex-1"><i class="fas fa-search"></i></button>
                    @if(request()->anyFilled(['search','status','category','language']))
                        <a href="{{ route('admin.whatsapp.templates.index') }}" class="{{ $waBtnSecondary }}"><i class="fas fa-times"></i></a>
                    @endif
                </div>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 border-b">
                    <tr>
                        <th class="px-5 py-3 text-right font-semibold">القالب</th>
                        <th class="px-5 py-3 text-right font-semibold">الفئة</th>
                        <th class="px-5 py-3 text-right font-semibold">الحالة</th>
                        @if(($templateAccessMode ?? 'all') === 'restricted')
                            <th class="px-5 py-3 text-right font-semibold">الموظفون</th>
                        @endif
                        <th class="px-5 py-3 text-right font-semibold">المحتوى</th>
                        <th class="px-5 py-3 text-right font-semibold">آخر مزامنة</th>
                        <th class="px-5 py-3 text-center font-semibold">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($templates as $tpl)
                        @php
                            $statusClass = match($tpl->status) {
                                'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'rejected' => 'bg-rose-100 text-rose-800 border-rose-200',
                                'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                                default => 'bg-slate-100 text-slate-600 border-slate-200',
                            };
                        @endphp
                        <tr class="hover:bg-emerald-50/30">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('admin.whatsapp.templates.show', $tpl) }}" class="font-bold text-slate-900 hover:text-emerald-700 font-mono dir-ltr text-right block">{{ $tpl->name }}</a>
                                <span class="text-xs text-slate-500">{{ $tpl->language }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-xs">{{ $tpl->categoryLabel() }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold border {{ $statusClass }}">{{ $tpl->statusLabel() }}</span>
                                @if($tpl->displayRejectionReason())
                                    <p class="text-[10px] text-rose-600 mt-1 max-w-xs truncate" title="{{ $tpl->displayRejectionReason() }}">{{ Str::limit($tpl->displayRejectionReason(), 40) }}</p>
                                @endif
                            </td>
                            @if(($templateAccessMode ?? 'all') === 'restricted')
                                <td class="px-5 py-3.5 text-xs">
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-violet-50 text-violet-800 font-semibold">
                                        <i class="fas fa-users text-[10px]"></i>
                                        {{ $tpl->assigned_users_count ?? 0 }}
                                    </span>
                                </td>
                            @endif
                            <td class="px-5 py-3.5 max-w-xs">
                                <p class="truncate text-slate-600" title="{{ $tpl->body_text }}">{{ Str::limit($tpl->body_text, 60) }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-500 whitespace-nowrap">{{ $tpl->meta_synced_at?->diffForHumans() ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-center">
                                <a href="{{ route('admin.whatsapp.templates.show', $tpl) }}" class="inline-flex w-8 h-8 items-center justify-center rounded-lg bg-slate-50 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700" title="عرض">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ ($templateAccessMode ?? 'all') === 'restricted' ? 7 : 6 }}" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center text-slate-400">
                                    <i class="fas fa-file-alt text-4xl mb-3"></i>
                                    <p class="font-semibold text-slate-600 mb-1">لا توجد قوالب Meta بعد</p>
                                    <p class="text-sm mb-4">زامن مع Meta أو أنشئ قالباً جديداً. للرسائل الجاهة للسيلز، افتح تبويب «مكتبة مقترحة للسيلز».</p>
                                    <div class="flex flex-wrap justify-center gap-2">
                                        <a href="{{ route('admin.whatsapp.templates.index', ['tab' => 'suggested']) }}" class="{{ $waBtnPrimary }} text-sm">
                                            <i class="fas fa-wand-magic-sparkles"></i> المكتبة المقترحة
                                        </a>
                                        <a href="{{ route('admin.whatsapp.templates.create') }}" class="{{ $waBtnSecondary }} text-sm">قالب Meta جديد</a>
                                        <form method="POST" action="{{ route('admin.whatsapp.templates.sync') }}">@csrf
                                            <button type="submit" class="{{ $waBtnSecondary }} text-sm">مزامنة Meta</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($templates->hasPages())
            <div class="px-5 py-4 border-t bg-slate-50/50">{{ $templates->links() }}</div>
        @endif
    </section>
    @endif
</div>

@push('scripts')
<script>
function suggestedTemplatesLibrary(items) {
    return {
        items: items || [],
        selected: (items && items.length) ? items[0] : null,
        copied: false,
        select(tpl) {
            this.selected = tpl;
            this.copied = false;
        },
        copyBody() {
            if (!this.selected?.body) return;
            navigator.clipboard.writeText(this.selected.body).then(() => {
                this.copied = true;
                setTimeout(() => { this.copied = false; }, 2000);
            });
        },
    };
}
</script>
@endpush
@endsection
