@extends('layouts.employee')

@section('title', 'تفاصيل المهمة')
@section('header', 'تفاصيل المهمة')

@section('content')
<div class="space-y-6">
    @if(session('import_report') && $task->isVideoEditing())
        @php $rep = session('import_report'); @endphp
        <div class="bg-white rounded-xl border border-slate-200 p-4 text-sm text-gray-800 shadow-sm">
            <p class="font-semibold text-gray-900 mb-2">تقرير الاستيراد</p>
            <p>تم استيراد: <strong>{{ $rep['imported'] ?? 0 }}</strong></p>
            @if(!empty($rep['skipped_duplicates']))
                <p class="mt-2 text-amber-800">روابط مُتخطاة (مكررة): {{ count($rep['skipped_duplicates']) }}</p>
                <ul class="list-disc list-inside text-xs text-gray-600 max-h-32 overflow-y-auto mt-1">
                    @foreach(array_slice($rep['skipped_duplicates'], 0, 15) as $line)
                        <li>{{ $line }}</li>
                    @endforeach
                </ul>
            @endif
            @if(!empty($rep['row_errors']))
                <p class="mt-2 text-red-800">أخطاء في الصفوف: {{ count($rep['row_errors']) }}</p>
                <ul class="list-disc list-inside text-xs text-gray-600 max-h-32 overflow-y-auto mt-1">
                    @foreach(array_slice($rep['row_errors'], 0, 15, true) as $rowNum => $err)
                        <li>صف {{ $rowNum }}: {{ $err }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-wrap justify-between items-start gap-4 mb-6">
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $task->title }}</h1>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold
                        {{ $task->isVideoEditing() ? 'bg-violet-100 text-violet-800' : 'bg-slate-100 text-slate-700' }}">
                        @if($task->isVideoEditing())
                            <i class="fas fa-video"></i> مونتاج فيديو
                        @else
                            <i class="fas fa-tasks"></i> مهمة عامة
                        @endif
                    </span>
                </div>
                <p class="text-gray-600">عرض تفاصيل المهمة والتسليمات</p>
            </div>
            <a href="{{ route('employee.tasks.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors shrink-0">
                <i class="fas fa-arrow-right mr-2"></i>العودة
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">المكلف</p>
                <p class="font-semibold text-gray-900">{{ $task->assigner->name }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">الأولوية</p>
                <span class="inline-block px-2 py-1 rounded-lg text-sm font-semibold
                    @if($task->priority === 'urgent') bg-red-100 text-red-800
                    @elseif($task->priority === 'high') bg-orange-100 text-orange-800
                    @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    @if($task->priority === 'urgent') عاجل
                    @elseif($task->priority === 'high') عالي
                    @elseif($task->priority === 'medium') متوسط
                    @else منخفض
                    @endif
                </span>
            </div>
            @if($task->deadline)
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">الموعد النهائي</p>
                <p class="font-semibold {{ $task->deadline < now() && !in_array($task->status, ['completed', 'cancelled']) ? 'text-red-600' : 'text-gray-900' }}">
                    {{ $task->deadline->format('Y-m-d') }}
                </p>
            </div>
            @endif
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">التقدم</p>
                <div class="flex items-center gap-2">
                    <div class="flex-1 bg-gray-200 rounded-full h-2 min-w-[60px]">
                        <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: {{ $task->progress }}%"></div>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">{{ $task->progress }}%</span>
                </div>
            </div>
        </div>

        @if($task->description)
        <div class="mb-6 pt-6 border-t border-gray-200">
            <p class="text-sm font-medium text-gray-600 mb-2">الوصف</p>
            <p class="text-gray-900 leading-relaxed whitespace-pre-wrap">{{ $task->description }}</p>
        </div>
        @endif

        @if($task->isVideoEditing())
            @php
                $sumBeforeMin = (int) $task->deliverables->sum('duration_before_minutes');
                $sumAfterMin = (int) $task->deliverables->sum('duration_after_minutes');
            @endphp
            @if($task->deliverables->count() > 0)
                <div class="mb-6 flex flex-wrap gap-3 text-sm text-gray-700">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-violet-50 border border-violet-100">مجموع دقائق قبل: <strong class="mr-1">{{ $sumBeforeMin }}</strong></span>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-100">مجموع دقائق بعد: <strong class="mr-1">{{ $sumAfterMin }}</strong></span>
                </div>
            @endif
        @endif

        <!-- تحديث الحالة -->
        <div class="mb-6 p-5 bg-slate-50 rounded-xl border border-slate-200">
            <h3 class="text-base font-semibold text-gray-900 mb-3">تحديث حالة المهمة</h3>
            <form action="{{ route('employee.tasks.update-status', $task) }}" method="POST" class="flex flex-wrap items-end gap-4">
                @csrf
                @method('PUT')
                <div class="flex-1 min-w-[140px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                    <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>معلقة</option>
                        <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                        <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>مكتملة</option>
                        <option value="on_hold" {{ $task->status == 'on_hold' ? 'selected' : '' }}>معلقة مؤقتاً</option>
                    </select>
                </div>
                <div class="w-24">
                    <label class="block text-sm font-medium text-gray-700 mb-1">التقدم %</label>
                    <input type="number" name="progress" value="{{ $task->progress }}" min="0" max="100" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i>تحديث
                </button>
            </form>
        </div>

        <!-- زر التسليمات: عند النقر يفتح ويظهر كامل التسليمات -->
        <div class="border-t border-gray-200 pt-8 mt-8">
            <details class="group rounded-2xl border-2 border-slate-200 bg-white overflow-hidden" id="deliverables-section" {{ request()->has('open') ? 'open' : '' }}>
                <summary class="flex items-center justify-between gap-4 w-full cursor-pointer list-none px-6 py-4 bg-gradient-to-l from-slate-50 to-white hover:from-blue-50/50 hover:to-white transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded-2xl">
                    <span class="flex items-center gap-3 font-bold text-gray-900 text-lg">
                        @if($task->isVideoEditing())
                            <span class="w-12 h-12 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-film text-xl"></i>
                            </span>
                        @else
                            <span class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-inbox text-xl"></i>
                            </span>
                        @endif
                        <span>التسليمات</span>
                        @if($task->deliverables->count() > 0)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm font-semibold">
                                {{ $task->deliverables->count() }}
                            </span>
                        @endif
                    </span>
                    <span class="flex items-center gap-2 text-gray-500 group-open:rotate-180 transition-transform">
                        <i class="fas fa-chevron-down"></i>
                        <span class="text-sm font-medium">عرض الكل</span>
                    </span>
                </summary>
                <div class="px-6 pb-6 pt-2 bg-slate-50/50 border-t border-slate-100">
            <!-- نموذج التسليم (فوق) -->
            @if($task->isVideoEditing())
                <div class="bg-violet-50/30 border-2 border-violet-200 rounded-xl p-6 mb-6">
                    <h4 class="text-base font-semibold text-gray-900 mb-4">
                        <i class="fas fa-plus-circle text-violet-600 mr-2"></i>تسليم مونتاج جديد
                    </h4>
                    <form action="{{ route('employee.tasks.submit-deliverable', $task) }}" method="POST">
                        @csrf
                        <input type="hidden" name="task_type_context" value="video_editing">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">عنوان التسليم (اختياري)</label>
                                <input type="text" name="title" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500" placeholder="مثال: فيديو الحلقة ١">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">رابط الفيديو من Bunny <span class="text-red-500">*</span></label>
                                <input type="url" name="video_link_url" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500" placeholder="https://...bunny.net أو b-cdn.net أو mediadelivery.net">
                                <p class="text-xs text-gray-500 mt-1">رابط من Bunny: bunny.net أو b-cdn.net أو mediadelivery.net — لا يتم رفع ملفات</p>
                                @error('video_link_url')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ممن استلمته <span class="text-red-500">*</span></label>
                                <input type="text" name="received_from" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500" placeholder="اسم الشخص أو المصدر">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">مدة الفيديو قبل المونتاج (نص)</label>
                                <input type="text" name="duration_before" value="{{ old('duration_before') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500" placeholder="مثال: 10:30 أو 45 دقيقة">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">مدة الفيديو بعد المونتاج (نص)</label>
                                <input type="text" name="duration_after" value="{{ old('duration_after') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500" placeholder="مثال: 8:00 أو 35 دقيقة">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">دقائق قبل (رقم، اختياري)</label>
                                <input type="number" name="duration_before_minutes" value="{{ old('duration_before_minutes') }}" min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500" placeholder="يُفضَّل لحساب المجاميع">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">دقائق بعد (رقم، اختياري)</label>
                                <input type="number" name="duration_after_minutes" value="{{ old('duration_after_minutes') }}" min="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500" placeholder="يُفضَّل لحساب المجاميع">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات (اختياري)</label>
                                <textarea name="description" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500" placeholder="أي تفاصيل إضافية..."></textarea>
                            </div>
                        </div>
                        <button type="submit" class="mt-4 w-full md:w-auto px-6 py-2.5 bg-violet-600 hover:bg-violet-700 text-white rounded-lg font-medium transition-colors">
                            <i class="fas fa-upload mr-2"></i>تسليم المونتاج
                        </button>
                    </form>

                    <div class="mt-6 pt-6 border-t border-violet-200">
                        <h5 class="text-sm font-semibold text-gray-900 mb-3"><i class="fas fa-file-excel text-green-600 mr-2"></i>استيراد عدة تسليمات من Excel</h5>
                        <p class="text-xs text-gray-600 mb-3">عمود إلزامي: رابط الفيديو (Bunny). يُمنع تكرار نفس الرابط في الملف أو في التسليمات السابقة.</p>
                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            <a href="{{ route('employee.tasks.deliverables.montage-excel-template', $task) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-violet-300 text-violet-800 text-sm font-medium hover:bg-violet-50">
                                <i class="fas fa-download"></i> تنزيل القالب
                            </a>
                        </div>
                        <form action="{{ route('employee.tasks.deliverables.import-excel', $task) }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-end gap-3">
                            @csrf
                            <div class="flex-1 min-w-[200px]">
                                <label class="block text-xs font-medium text-gray-700 mb-1">ملف Excel (.xlsx)</label>
                                <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required class="w-full text-sm text-gray-700 file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-violet-100 file:text-violet-800">
                            </div>
                            <button type="submit" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium">
                                <i class="fas fa-file-import mr-1"></i> استيراد
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 mb-6">
                    <h4 class="text-base font-semibold text-gray-900 mb-4">إضافة تسليم جديد</h4>
                    <form action="{{ route('employee.tasks.submit-deliverable', $task) }}" method="POST" enctype="multipart/form-data" id="deliverableForm">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">عنوان التسليم *</label>
                                <input type="text" name="title" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                                <textarea name="description" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">نوع التسليم *</label>
                                <select name="delivery_type" id="delivery_type" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="file">ملف</option>
                                    <option value="image">صورة</option>
                                    <option value="link">رابط</option>
                                </select>
                            </div>
                            <div id="file_field">
                                <label class="block text-sm font-medium text-gray-700 mb-2" id="file_label">الملف *</label>
                                <input type="file" name="file" id="file_input" accept="" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <p class="text-xs text-gray-500 mt-1" id="file_hint">حدد ملف للتسليم</p>
                            </div>
                            <div id="link_field" style="display: none;">
                                <label class="block text-sm font-medium text-gray-700 mb-2">الرابط *</label>
                                <input type="url" name="link_url" id="link_input" placeholder="https://example.com" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-upload mr-2"></i>تسليم المهمة
                            </button>
                        </div>
                    </form>
                    <script>
                        document.getElementById('delivery_type').addEventListener('change', function() {
                            var type = this.value;
                            var fileField = document.getElementById('file_field');
                            var linkField = document.getElementById('link_field');
                            var fileInput = document.getElementById('file_input');
                            var linkInput = document.getElementById('link_input');
                            var fileLabel = document.getElementById('file_label');
                            var fileHint = document.getElementById('file_hint');
                            if (type === 'link') {
                                fileField.style.display = 'none';
                                linkField.style.display = 'block';
                                fileInput.removeAttribute('required');
                                linkInput.setAttribute('required', 'required');
                            } else {
                                fileField.style.display = 'block';
                                linkField.style.display = 'none';
                                fileInput.setAttribute('required', 'required');
                                linkInput.removeAttribute('required');
                                fileLabel.textContent = type === 'image' ? 'الصورة *' : 'الملف *';
                                fileInput.setAttribute('accept', type === 'image' ? 'image/*' : '');
                                fileHint.textContent = type === 'image' ? 'حدد صورة للتسليم' : 'حدد ملف للتسليم';
                            }
                        });
                    </script>
                </div>
            @endif

            <!-- جميع التسليمات (تحت) -->
            <h4 class="text-base font-semibold text-gray-900 mb-3 flex items-center gap-2">
                <i class="fas fa-list text-slate-500"></i>
                جميع التسليمات
            </h4>
            @if($task->deliverables->count() > 0)
                <div class="space-y-4" id="task-deliverables-list">
                    @foreach($task->deliverables as $index => $deliverable)
                        <div class="border border-gray-200 rounded-xl p-5 hover:border-blue-300 transition-colors bg-white shadow-sm">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                <div class="flex-1 space-y-3">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="font-semibold text-gray-900">
                                            {{ $deliverable->title ?: ($task->isVideoEditing() ? 'فيديو ' . ($index + 1) : 'تسليم ' . ($index + 1)) }}
                                        </h4>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if($deliverable->status === 'approved') bg-green-100 text-green-800
                                            @elseif($deliverable->status === 'rejected') bg-red-100 text-red-800
                                            @elseif($deliverable->status === 'submitted') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            @if($deliverable->status === 'approved') معتمد
                                            @elseif($deliverable->status === 'rejected') مرفوض
                                            @elseif($deliverable->status === 'submitted') مقدم
                                            @else معلق
                                            @endif
                                        </span>
                                        @if($task->isVideoEditing() && ($deliverable->received_from || $deliverable->duration_before || $deliverable->duration_after))
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-violet-50 text-violet-700">
                                                <i class="fas fa-video"></i> مونتاج
                                            </span>
                                        @endif
                                    </div>

                                    @if($task->isVideoEditing() && ($deliverable->received_from || $deliverable->duration_before || $deliverable->duration_after))
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                                            @if($deliverable->received_from)
                                                <div class="bg-violet-50/50 rounded-lg p-3">
                                                    <p class="text-xs font-medium text-violet-600 mb-0.5">ممن استلمته</p>
                                                    <p class="text-gray-900 font-medium">{{ $deliverable->received_from }}</p>
                                                </div>
                                            @endif
                                            @if($deliverable->duration_before)
                                                <div class="bg-amber-50/50 rounded-lg p-3">
                                                    <p class="text-xs font-medium text-amber-700 mb-0.5">مدة قبل المونتاج</p>
                                                    <p class="text-gray-900 font-medium">{{ $deliverable->duration_before }}</p>
                                                </div>
                                            @endif
                                            @if($deliverable->duration_after)
                                                <div class="bg-emerald-50/50 rounded-lg p-3">
                                                    <p class="text-xs font-medium text-emerald-700 mb-0.5">مدة بعد المونتاج</p>
                                                    <p class="text-gray-900 font-medium">{{ $deliverable->duration_after }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    @if($deliverable->description)
                                        <p class="text-sm text-gray-600">{{ $deliverable->description }}</p>
                                    @endif

                                    @if($deliverable->delivery_type === 'link' && $deliverable->link_url)
                                        <div class="flex flex-wrap items-center gap-2 text-sm">
                                            @if($task->isVideoEditing())
                                                <i class="fas fa-video text-violet-500"></i>
                                                <span class="text-gray-600">رابط الفيديو (Bunny):</span>
                                            @else
                                                <i class="fas fa-link text-gray-500"></i>
                                            @endif
                                            <a href="{{ $deliverable->link_url }}" target="_blank" rel="noopener" class="text-blue-600 hover:text-blue-800 font-medium break-all">
                                                {{ Str::limit($deliverable->link_url, 60) }}
                                                <i class="fas fa-external-link-alt text-xs mr-1"></i>
                                            </a>
                                        </div>
                                    @elseif($deliverable->file_name)
                                        <div class="flex items-center gap-2 text-sm">
                                            <i class="fas fa-file-video text-violet-500"></i>
                                            <span class="text-gray-700">{{ $deliverable->file_name }}</span>
                                            @if($deliverable->file_path)
                                                <a href="{{ Storage::url($deliverable->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                                    <i class="fas fa-download"></i> تحميل
                                                </a>
                                            @endif
                                        </div>
                                    @endif

                                    @if($deliverable->feedback)
                                        <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                            <p class="text-xs font-semibold text-amber-800 mb-1">ملاحظات المراجع</p>
                                            <p class="text-sm text-gray-900">{{ $deliverable->feedback }}</p>
                                        </div>
                                    @endif
                                    <p class="text-xs text-gray-400">{{ $deliverable->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2 shrink-0 justify-end md:flex-col md:items-stretch">
                                    <button type="button" onclick="document.getElementById('edit-form-{{ $deliverable->id }}').classList.toggle('hidden')" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium bg-white border border-gray-300 text-gray-800 hover:bg-gray-50 transition-colors">
                                        <i class="fas fa-edit"></i> تعديل
                                    </button>
                                    <form action="{{ route('employee.tasks.deliverables.destroy', [$task, $deliverable]) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا التسليم؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 transition-colors">
                                            <i class="fas fa-trash-alt"></i> حذف
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div id="edit-form-{{ $deliverable->id }}" class="hidden mt-4 pt-4 border-t border-gray-200">
                                @if($task->isVideoEditing())
                                    <p class="text-sm font-semibold text-violet-800 mb-3"><i class="fas fa-pen mr-1"></i> تعديل تسليم المونتاج</p>
                                    <form action="{{ route('employee.tasks.deliverables.update', [$task, $deliverable]) }}" method="POST" class="space-y-3">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="task_type_context" value="video_editing">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-medium text-gray-700 mb-1">عنوان التسليم</label>
                                                <input type="text" name="title" value="{{ old('title', $deliverable->title) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-medium text-gray-700 mb-1">رابط الفيديو من Bunny <span class="text-red-500">*</span></label>
                                                <input type="url" name="video_link_url" required value="{{ old('video_link_url', $deliverable->link_url) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">ممن استلمته <span class="text-red-500">*</span></label>
                                                <input type="text" name="received_from" required value="{{ old('received_from', $deliverable->received_from) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">مدة قبل المونتاج (نص)</label>
                                                <input type="text" name="duration_before" value="{{ old('duration_before', $deliverable->duration_before) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">مدة بعد المونتاج (نص)</label>
                                                <input type="text" name="duration_after" value="{{ old('duration_after', $deliverable->duration_after) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">دقائق قبل</label>
                                                <input type="number" name="duration_before_minutes" value="{{ old('duration_before_minutes', $deliverable->duration_before_minutes) }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">دقائق بعد</label>
                                                <input type="number" name="duration_after_minutes" value="{{ old('duration_after_minutes', $deliverable->duration_after_minutes) }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-medium text-gray-700 mb-1">ملاحظات</label>
                                                <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">{{ old('description', $deliverable->description) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white rounded-lg text-sm font-medium">حفظ التعديلات</button>
                                            <button type="button" onclick="document.getElementById('edit-form-{{ $deliverable->id }}').classList.add('hidden')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg text-sm">إلغاء</button>
                                        </div>
                                    </form>
                                @else
                                    <p class="text-sm font-semibold text-blue-800 mb-3"><i class="fas fa-pen mr-1"></i> تعديل التسليم</p>
                                    <form action="{{ route('employee.tasks.deliverables.update', [$task, $deliverable]) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">عنوان التسليم *</label>
                                            <input type="text" name="title" required value="{{ old('title', $deliverable->title) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">الوصف</label>
                                            <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">{{ old('description', $deliverable->description) }}</textarea>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">نوع التسليم *</label>
                                            <select name="delivery_type" id="edit_delivery_type_{{ $deliverable->id }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                                <option value="file" {{ old('delivery_type', $deliverable->delivery_type) === 'file' ? 'selected' : '' }}>ملف</option>
                                                <option value="image" {{ old('delivery_type', $deliverable->delivery_type) === 'image' ? 'selected' : '' }}>صورة</option>
                                                <option value="link" {{ old('delivery_type', $deliverable->delivery_type) === 'link' ? 'selected' : '' }}>رابط</option>
                                            </select>
                                        </div>
                                        <div id="edit_file_wrap_{{ $deliverable->id }}">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">استبدال الملف (اختياري)</label>
                                            <input type="file" name="file" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                            <p class="text-xs text-gray-500 mt-1">اتركه فارغاً للإبقاء على الملف الحالي</p>
                                        </div>
                                        <div id="edit_link_wrap_{{ $deliverable->id }}">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">الرابط *</label>
                                            <input type="url" name="link_url" value="{{ old('link_url', $deliverable->link_url) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="https://">
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">حفظ التعديلات</button>
                                            <button type="button" onclick="document.getElementById('edit-form-{{ $deliverable->id }}').classList.add('hidden')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg text-sm">إلغاء</button>
                                        </div>
                                    </form>
                                    <script>
                                        (function () {
                                            var sel = document.getElementById('edit_delivery_type_{{ $deliverable->id }}');
                                            var fw = document.getElementById('edit_file_wrap_{{ $deliverable->id }}');
                                            var lw = document.getElementById('edit_link_wrap_{{ $deliverable->id }}');
                                            function sync() {
                                                if (!sel || !fw || !lw) return;
                                                if (sel.value === 'link') { fw.classList.add('hidden'); lw.classList.remove('hidden'); }
                                                else { fw.classList.remove('hidden'); lw.classList.add('hidden'); }
                                            }
                                            if (sel) { sel.addEventListener('change', sync); sync(); }
                                        })();
                                    </script>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-xl border-2 border-dashed border-gray-200 bg-gray-50/50 p-8 text-center">
                    <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-inbox text-2xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-600 font-medium">لا توجد تسليمات حتى الآن</p>
                    <p class="text-sm text-gray-500 mt-1">التسليمات التي تقدمها من النموذج أعلاه ستظهر هنا</p>
                </div>
            @endif
                </div>
            </details>
        </div>
    </div>
</div>
@endsection
