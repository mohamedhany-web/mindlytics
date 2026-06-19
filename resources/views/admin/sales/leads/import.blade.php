@extends('layouts.admin')

@section('title', 'استيراد عملاء')
@section('header', 'المبيعات — استيراد دفعة عملاء')

@section('content')
@php
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500';
    $selectClass = $inputClass;
    $statCards = [
        ['label' => 'موظفو مبيعات', 'value' => number_format($stats['reps'] ?? 0), 'icon' => 'fas fa-user-tie', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => 'نشطون للإسناد'],
        ['label' => 'تصنيفات', 'value' => number_format($stats['categories'] ?? 0), 'icon' => 'fas fa-tags', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'description' => 'لتنظيم الدفعة'],
        ['label' => 'دفعات سابقة', 'value' => number_format($stats['import_batches'] ?? 0), 'icon' => 'fas fa-layer-group', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'description' => 'استيرادات منجزة'],
        ['label' => 'عملاء مستوردون', 'value' => number_format($stats['imported_leads'] ?? 0), 'icon' => 'fas fa-file-import', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'description' => 'Leads من Excel'],
    ];
    $columns = [
        ['name' => 'الاسم', 'required' => true, 'aliases' => 'name, اسم, العميل'],
        ['name' => 'الهاتف', 'required' => false, 'aliases' => 'phone, تليفون, موبايل'],
        ['name' => 'البريد', 'required' => false, 'aliases' => 'email, ايميل'],
        ['name' => 'الشركة', 'required' => false, 'aliases' => 'company'],
        ['name' => 'الاهتمام', 'required' => false, 'aliases' => 'interest, منتج'],
        ['name' => 'القيمة', 'required' => false, 'aliases' => 'expected_value, value'],
        ['name' => 'ملاحظات', 'required' => false, 'aliases' => 'notes'],
        ['name' => 'الأولوية', 'required' => false, 'aliases' => 'priority — عادي، مرتفع، عاجل'],
    ];
    $oldRepIds = collect(old('assigned_to_ids', []))->map(fn ($id) => (int) $id)->all();
    $groupOptions = $groups->map(fn ($g) => [
        'id' => $g->id,
        'assigned_to' => $g->assigned_to,
        'label' => $g->name.' — '.$g->assignee?->name,
        'admin' => (bool) $g->is_admin_managed,
    ])->values();
@endphp

<div class="space-y-6" x-data="{
    fileName: '',
    dragOver: false,
    selectedGroupId: '{{ old('group_id', '') }}',
    groups: @json($groupOptions),
    applyGroupAssignee() {
        if (!this.selectedGroupId) return;
        const group = this.groups.find(g => String(g.id) === String(this.selectedGroupId));
        if (!group) return;
        document.querySelectorAll('.rep-checkbox').forEach(c => {
            c.checked = String(c.value) === String(group.assigned_to);
        });
    }
}" x-init="if (selectedGroupId) applyGroupAssignee()">
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-exclamation-circle ml-1"></i>{{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">
            <p class="font-semibold mb-1"><i class="fas fa-exclamation-circle ml-1"></i> يوجد أخطاء:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- الهيدر --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-file-upload"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">استيراد دفعة عملاء</h2>
                    <p class="text-xs text-slate-600">رفع Excel/CSV، تصنيف الدفعة، توزيع Round-Robin على موظفي المبيعات، وإشعار فوري.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.sales.leads.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-user-tag text-emerald-600"></i>
                    العملاء المحتملون
                </a>
                <a href="{{ route('admin.sales.categories.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-tags text-teal-600"></i>
                    التصنيفات
                </a>
                <a href="{{ route('admin.sales.groups.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-layer-group text-indigo-600"></i>
                    مجموعات العملاء
                </a>
                <a href="{{ route('admin.sales.leads.import.template') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl bg-emerald-600 hover:bg-emerald-700">
                    <i class="fas fa-download"></i>
                    تحميل القالب
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 p-4">
            @foreach($statCards as $card)
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-600 truncate">{{ $card['label'] }}</p>
                            <p class="text-xl font-black text-slate-900 truncate tabular-nums">{{ $card['value'] }}</p>
                        </div>
                        <div class="w-9 h-9 rounded-lg {{ $card['bg'] }} flex items-center justify-center {{ $card['text'] }} flex-shrink-0">
                            <i class="{{ $card['icon'] }} text-sm"></i>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1 truncate">{{ $card['description'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
        {{-- النموذج --}}
        <div class="xl:col-span-7 space-y-6">
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-cloud-upload-alt text-violet-600"></i>
                        رفع الملف وإعدادات الدفعة
                    </h3>
                    <p class="text-xs text-slate-600 mt-0.5">عمود <strong>الاسم</strong> إلزامي — باقي الأعمدة اختيارية.</p>
                </div>
                <div class="p-4 sm:p-5">
                    <form method="post" action="{{ route('admin.sales.leads.import.store') }}" enctype="multipart/form-data" class="space-y-5">
                        @csrf

                        {{-- منطقة رفع الملف --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-2">ملف Excel / CSV *</label>
                            <label
                                class="relative flex flex-col items-center justify-center w-full min-h-[140px] rounded-2xl border-2 border-dashed cursor-pointer transition-colors"
                                :class="dragOver ? 'border-violet-500 bg-violet-50' : 'border-slate-300 bg-slate-50 hover:border-violet-400 hover:bg-violet-50/50'"
                                @dragover.prevent="dragOver = true"
                                @dragleave.prevent="dragOver = false"
                                @drop.prevent="dragOver = false; $refs.fileInput.files = $event.dataTransfer.files; fileName = $event.dataTransfer.files[0]?.name || ''"
                            >
                                <input type="file" name="file" x-ref="fileInput" accept=".xlsx,.xls,.csv" required class="sr-only"
                                       @change="fileName = $event.target.files[0]?.name || ''">
                                <div class="text-center px-4 py-6 pointer-events-none">
                                    <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-violet-100 text-violet-600 flex items-center justify-center text-2xl">
                                        <i class="fas fa-file-excel"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-800" x-show="!fileName">اسحب الملف هنا أو انقر للاختيار</p>
                                    <p class="text-sm font-bold text-violet-700" x-show="fileName" x-text="fileName"></p>
                                    <p class="text-xs text-slate-500 mt-1">.xlsx · .xls · .csv — حد أقصى 10 MB</p>
                                </div>
                            </label>
                            @error('file')<p class="text-xs text-rose-600 mt-1.5">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">التصنيف *</label>
                                <select name="category_id" required class="{{ $selectClass }}">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                                @if($categories->isEmpty())
                                    <p class="text-xs text-amber-700 mt-1">
                                        <a href="{{ route('admin.sales.categories.index') }}" class="underline">أضف تصنيفاً</a> أولاً.
                                    </p>
                                @endif
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">المصدر</label>
                                <select name="source" class="{{ $selectClass }}">
                                    @foreach(\App\Models\SalesLead::SOURCES as $k => $label)
                                        <option value="{{ $k }}" @selected(old('source', 'other') === $k)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">أولوية افتراضية</label>
                                <select name="default_priority" class="{{ $selectClass }}">
                                    @foreach(\App\Models\SalesLead::PRIORITIES as $k => $label)
                                        <option value="{{ $k }}" @selected(old('default_priority', 'normal') === $k)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <label class="block text-xs font-semibold text-slate-700">مجموعة العملاء <span class="font-normal text-slate-500">(اختياري)</span></label>
                                    <a href="{{ route('admin.sales.groups.create') }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:underline">+ مجموعة جديدة</a>
                                </div>
                                <select name="group_id" class="{{ $selectClass }}"
                                        x-model="selectedGroupId"
                                        @change="applyGroupAssignee()">
                                    <option value="">— بدون مجموعة —</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}" @selected(old('group_id') == $group->id)>
                                            {{ $group->name }} — {{ $group->assignee?->name }}
                                            @if($group->is_admin_managed) (إدارة) @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('group_id')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                                <p class="text-xs text-slate-500 mt-1.5 flex items-start gap-1.5" x-show="selectedGroupId">
                                    <i class="fas fa-info-circle text-indigo-500 mt-0.5"></i>
                                    <span>عند اختيار مجموعة، تُسند كل الدفعة لموظف المجموعة ويظهر لها في لوحة الموظف.</span>
                                </p>
                                @if($groups->isEmpty())
                                    <p class="text-xs text-amber-700 mt-1">
                                        <a href="{{ route('admin.sales.groups.create') }}" class="underline">أنشئ مجموعة</a> وخصّصها لموظف مبيعات، ثم ارجع للاستيراد.
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- اختيار الموظفين --}}
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <label class="text-xs font-semibold text-slate-700">موظفو المبيعات * <span class="font-normal text-slate-500">(Round-Robin)</span></label>
                                @if($salesReps->count() > 1)
                                    <button type="button" onclick="document.querySelectorAll('.rep-checkbox').forEach(c => c.checked = true)" class="text-xs font-semibold text-violet-600 hover:underline">تحديد الكل</button>
                                @endif
                            </div>
                            @if($salesReps->isEmpty())
                                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                                    لا يوجد موظفو مبيعات نشطون. أضف موظفاً بدور مبيعات أولاً.
                                </div>
                            @else
                                <div class="grid sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto rounded-xl border border-slate-200 p-3 bg-slate-50/50">
                                    @foreach($salesReps as $rep)
                                        <label class="flex items-center gap-3 px-3 py-2.5 rounded-xl border border-slate-200 bg-white hover:border-violet-300 hover:bg-violet-50/30 cursor-pointer transition-colors has-[:checked]:border-violet-400 has-[:checked]:bg-violet-50 has-[:checked]:ring-1 has-[:checked]:ring-violet-200">
                                            <input type="checkbox" name="assigned_to_ids[]" value="{{ $rep->id }}" class="rep-checkbox rounded border-slate-300 text-violet-600 focus:ring-violet-500"
                                                   @checked(in_array($rep->id, $oldRepIds, true) || ($salesReps->count() === 1 && empty($oldRepIds)))>
                                            <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                                {{ mb_substr($rep->name, 0, 1) }}
                                            </span>
                                            <span class="text-sm font-semibold text-slate-800 truncate">{{ $rep->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                            @error('assigned_to_ids')<p class="text-xs text-rose-600 mt-1.5">{{ $message }}</p>@enderror
                            <p class="text-xs text-slate-500 mt-2 flex items-start gap-1.5">
                                <i class="fas fa-info-circle text-violet-500 mt-0.5"></i>
                                <span>كل عميل يُسند للموظف التالي بالتناوب. موظف واحد = كل الدفعة له. عدة موظفين = توزيع متساوٍ.</span>
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 pt-2 border-t border-slate-100">
                            <button type="submit" @disabled($salesReps->isEmpty() || $categories->isEmpty())
                                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-violet-600 hover:bg-violet-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-xl text-sm font-semibold shadow-sm">
                                <i class="fas fa-upload"></i>
                                استيراد وإرسال إشعار
                            </button>
                            <a href="{{ route('admin.sales.leads.import.template') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-emerald-700 rounded-xl border border-emerald-200 bg-emerald-50 hover:bg-emerald-100">
                                <i class="fas fa-download"></i>
                                قالب Excel
                            </a>
                        </div>
                    </form>
                </div>
            </section>
        </div>

        {{-- الشريط الجانبي: تعليمات --}}
        <aside class="xl:col-span-5 space-y-6">
            {{-- خطوات --}}
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-list-ol text-emerald-600"></i>
                        خطوات الاستيراد
                    </h3>
                </div>
                <ol class="p-4 space-y-3">
                    <li class="flex gap-3">
                        <span class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0 text-sm"><i class="fas fa-download"></i></span>
                        <div><p class="text-sm font-bold text-slate-900">حمّل القالب</p><p class="text-xs text-slate-600">ملف Excel جاهز بالأعمدة العربية الصحيحة.</p></div>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-9 h-9 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0 text-sm"><i class="fas fa-table"></i></span>
                        <div><p class="text-sm font-bold text-slate-900">املأ البيانات</p><p class="text-xs text-slate-600">الاسم إلزامي — الهاتف والبريد يساعدان في تجنب التكرار.</p></div>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-9 h-9 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center flex-shrink-0 text-sm"><i class="fas fa-tags"></i></span>
                        <div><p class="text-sm font-bold text-slate-900">اختر التصنيف</p><p class="text-xs text-slate-600">يُطبَّق على كل صف في الدفعة.</p></div>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0 text-sm"><i class="fas fa-layer-group"></i></span>
                        <div><p class="text-sm font-bold text-slate-900">مجموعة اختيارية</p><p class="text-xs text-slate-600">أنشئ مجموعة من الإدارة وخصّصها لموظف — العملاء المستوردون ينضمون إليها تلقائياً.</p></div>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-9 h-9 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center flex-shrink-0 text-sm"><i class="fas fa-random"></i></span>
                        <div><p class="text-sm font-bold text-slate-900">وزّع على الموظفين</p><p class="text-xs text-slate-600">Round-Robin: عميل 1 → أ، عميل 2 → ب، عميل 3 → أ...</p></div>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0 text-sm"><i class="fas fa-bell"></i></span>
                        <div><p class="text-sm font-bold text-slate-900">إشعار تلقائي</p><p class="text-xs text-slate-600">كل موظف يستلم إشعاراً بعدد العملاء المسندين له.</p></div>
                    </li>
                </ol>
            </section>

            {{-- الأعمدة --}}
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-columns text-sky-600"></i>
                        أعمدة الملف
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                                <th class="px-4 py-2 text-right text-xs font-semibold">العمود</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold w-16">إلزامي</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold">أسماء بديلة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($columns as $col)
                                <tr class="hover:bg-slate-50/80">
                                    <td class="px-4 py-2.5 font-semibold text-slate-800">{{ $col['name'] }}</td>
                                    <td class="px-4 py-2.5 text-center">
                                        @if($col['required'])
                                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700">نعم</span>
                                        @else
                                            <span class="text-slate-400 text-xs">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 text-xs text-slate-500">{{ $col['aliases'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- دفعات حديثة --}}
            @if($recentBatches->isNotEmpty())
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-history text-violet-600"></i>
                        آخر الدفعات
                    </h3>
                </div>
                <ul class="divide-y divide-slate-100">
                    @foreach($recentBatches as $batch)
                        <li class="px-4 py-3 flex items-center justify-between gap-3 hover:bg-slate-50/80">
                            <div class="min-w-0">
                                <p class="text-xs font-mono font-semibold text-violet-700 truncate">{{ $batch->import_batch }}</p>
                                <p class="text-[11px] text-slate-500">{{ \Carbon\Carbon::parse($batch->imported_at)->diffForHumans() }}</p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="text-xs font-bold text-slate-700 tabular-nums">{{ $batch->leads_count }} عميل</span>
                                <a href="{{ route('admin.sales.leads.index', ['import_batch' => $batch->import_batch]) }}"
                                   class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100">
                                    عرض
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
            @endif
        </aside>
    </div>
</div>
@endsection
