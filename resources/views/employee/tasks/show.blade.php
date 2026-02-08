@extends('layouts.employee')

@section('title', 'تفاصيل المهمة')
@section('header', 'تفاصيل المهمة')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $task->title }}</h1>
                <p class="text-gray-600 mt-1">عرض تفاصيل المهمة</p>
            </div>
            <a href="{{ route('employee.tasks.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-right mr-2"></i>العودة
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-sm text-gray-600 mb-1">المكلف</p>
                <p class="font-semibold text-gray-900 text-lg">{{ $task->assigner->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">الأولوية</p>
                <span class="px-3 py-1 rounded-full text-sm font-semibold
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
            <div>
                <p class="text-sm text-gray-600 mb-1">الموعد النهائي</p>
                <p class="font-semibold text-gray-900 text-lg {{ $task->deadline < now() && !in_array($task->status, ['completed', 'cancelled']) ? 'text-red-600' : '' }}">
                    {{ $task->deadline->format('Y-m-d') }}
                </p>
            </div>
            @endif
            <div>
                <p class="text-sm text-gray-600 mb-1">التقدم</p>
                <div class="flex items-center gap-2">
                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $task->progress }}%"></div>
                    </div>
                    <span class="text-sm font-semibold">{{ $task->progress }}%</span>
                </div>
            </div>
        </div>

        @if($task->description)
        <div class="mb-6 pt-6 border-t border-gray-200">
            <p class="text-sm text-gray-600 mb-2">الوصف</p>
            <p class="text-gray-900 leading-relaxed">{{ $task->description }}</p>
        </div>
        @endif

        <!-- تحديث الحالة -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">تحديث حالة المهمة</h3>
            <form action="{{ route('employee.tasks.update-status', $task) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                        <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>معلقة</option>
                            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                            <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>مكتملة</option>
                            <option value="on_hold" {{ $task->status == 'on_hold' ? 'selected' : '' }}>معلقة مؤقتاً</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">التقدم (%)</label>
                        <input type="number" name="progress" value="{{ $task->progress }}" min="0" max="100" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <button type="submit" class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i>تحديث الحالة
                </button>
            </form>
        </div>

        <!-- التسليمات -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">التسليمات</h3>
            @if($task->deliverables->count() > 0)
                <div class="space-y-3 mb-4">
                    @foreach($task->deliverables as $deliverable)
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h4 class="font-semibold text-gray-900">{{ $deliverable->title }}</h4>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if($deliverable->delivery_type === 'image') bg-pink-100 text-pink-800
                                            @elseif($deliverable->delivery_type === 'link') bg-purple-100 text-purple-800
                                            @else bg-blue-100 text-blue-800
                                            @endif">
                                            @if($deliverable->delivery_type === 'image')
                                                <i class="fas fa-image"></i> صورة
                                            @elseif($deliverable->delivery_type === 'link')
                                                <i class="fas fa-link"></i> رابط
                                            @else
                                                <i class="fas fa-file"></i> ملف
                                            @endif
                                        </span>
                                    </div>
                                    
                                    @if($deliverable->description)
                                        <p class="text-sm text-gray-600 mb-2">{{ $deliverable->description }}</p>
                                    @endif

                                    @if($deliverable->delivery_type === 'link' && $deliverable->link_url)
                                        <a href="{{ $deliverable->link_url }}" target="_blank" class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800 mb-2">
                                            <i class="fas fa-external-link-alt"></i>
                                            {{ $deliverable->link_url }}
                                        </a>
                                    @elseif($deliverable->file_name)
                                        <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                                            <i class="fas fa-file"></i>
                                            <span>{{ $deliverable->file_name }}</span>
                                            @if($deliverable->file_path)
                                                <a href="{{ Storage::url($deliverable->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                                    <i class="fas fa-download"></i> تحميل
                                                </a>
                                            @endif
                                        </div>
                                        @if($deliverable->delivery_type === 'image' && $deliverable->file_path)
                                            <div class="mt-2">
                                                <img src="{{ Storage::url($deliverable->file_path) }}" alt="{{ $deliverable->title }}" class="max-w-xs rounded-lg border border-gray-200">
                                            </div>
                                        @endif
                                    @endif

                                    @if($deliverable->feedback)
                                        <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                            <p class="text-sm font-semibold text-gray-700 mb-1">ملاحظات المراجع:</p>
                                            <p class="text-sm text-gray-900">{{ $deliverable->feedback }}</p>
                                        </div>
                                    @endif
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full flex-shrink-0
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
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- إضافة تسليم جديد -->
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
                    <!-- حقل الملف/الصورة -->
                    <div id="file_field">
                        <label class="block text-sm font-medium text-gray-700 mb-2" id="file_label">الملف *</label>
                        <input type="file" name="file" id="file_input" accept="" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1" id="file_hint">حدد ملف للتسليم</p>
                    </div>
                    <!-- حقل الرابط -->
                    <div id="link_field" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">الرابط *</label>
                        <input type="url" name="link_url" id="link_input" placeholder="https://example.com" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">أدخل رابط التسليم</p>
                    </div>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-upload mr-2"></i>تسليم المهمة
                    </button>
                </div>
            </form>

            <script>
                document.getElementById('delivery_type').addEventListener('change', function() {
                    const type = this.value;
                    const fileField = document.getElementById('file_field');
                    const linkField = document.getElementById('link_field');
                    const fileInput = document.getElementById('file_input');
                    const linkInput = document.getElementById('link_input');
                    const fileLabel = document.getElementById('file_label');
                    const fileHint = document.getElementById('file_hint');

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
                        
                        if (type === 'image') {
                            fileLabel.textContent = 'الصورة *';
                            fileInput.setAttribute('accept', 'image/*');
                            fileHint.textContent = 'حدد صورة للتسليم (JPG, PNG, GIF, etc.)';
                        } else {
                            fileLabel.textContent = 'الملف *';
                            fileInput.removeAttribute('accept');
                            fileHint.textContent = 'حدد ملف للتسليم';
                        }
                    }
                });
            </script>
        </div>
    </div>
</div>
@endsection
