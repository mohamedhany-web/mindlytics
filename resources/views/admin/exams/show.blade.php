@extends('layouts.admin')

@section('title', 'تفاصيل الامتحان')
@section('header', 'تفاصيل الامتحان: ' . $exam->title)

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="flex items-center justify-between">
        <div>
            <nav class="text-sm text-gray-500 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600">لوحة التحكم</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.exams.index') }}" class="hover:text-primary-600">الامتحانات</a>
                <span class="mx-2">/</span>
                <span>{{ $exam->title }}</span>
            </nav>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.exams.edit', $exam) }}" 
               class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-edit ml-2"></i>
                تعديل
            </a>
            <a href="{{ route('admin.exams.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة
            </a>
        </div>
    </div>

    <!-- معلومات الامتحان والإحصائيات -->
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- معلومات الامتحان -->
        <div class="xl:col-span-3">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">معلومات الامتحان</h3>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ $exam->is_active ? 'bg-green-100 text-green-800 ': ''bg-red-100 text-red-800 }}">']
                            {{ $exam->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                        @if($exam->is_published)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-globe ml-1"></i>
                                منشور
                            </span>
                        @endif
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">العنوان</label>
                                <div class="font-semibold text-gray-900">{{ $exam->title }}</div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">الكورس</label>
                                <div class="text-gray-900">{{ $exam->course->title ?? 'غير محدد' }}</div>
                                <div class="text-sm text-gray-500">{{ $exam->course->academicSubject->name ?? 'غير محدد' }}</div>
                            </div>
                            @if($exam->lesson)
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-500 mb-1">الدرس</label>
                                    <div class="text-gray-900">{{ $exam->lesson->title }}</div>
                                </div>
                            @endif
                        </div>
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">مدة الامتحان</label>
                                <div class="text-gray-900">{{ $exam->duration_minutes }} دقيقة</div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">درجة النجاح</label>
                                <div class="text-gray-900">{{ $exam->passing_marks }}%</div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">عدد المحاولات المسموحة</label>
                                <div class="text-gray-900">{{ $exam->attempts_allowed == 0 ? 'غير محدود' : $exam->attempts_allowed }}</div>
                            </div>
                        </div>
                    </div>

                    @if($exam->description)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-500 mb-2">الوصف</label>
                            <div class="text-gray-900 bg-gray-50 p-3 rounded-lg">
                                {{ $exam->description }}
                            </div>
                        </div>
                    @endif

                    @if($exam->instructions)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-500 mb-2">التعليمات</label>
                            <div class="text-gray-900 bg-gray-50 p-3 rounded-lg whitespace-pre-wrap">{{ $exam->instructions }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- إحصائيات سريعة -->
        <div class="space-y-4">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-blue-100">
                        <i class="fas fa-question-circle text-blue-600"></i>
                    </div>
                    <div class="mr-3">
                        <p class="text-2xl font-bold text-gray-900">{{ $exam->examQuestions->count() }}</p>
                        <p class="text-sm text-gray-500">أسئلة</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-green-100">
                        <i class="fas fa-users text-green-600"></i>
                    </div>
                    <div class="mr-3">
                        <p class="text-2xl font-bold text-gray-900">{{ $exam->attempts->count() }}</p>
                        <p class="text-sm text-gray-500">محاولات</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-yellow-100">
                        <i class="fas fa-star text-yellow-600"></i>
                    </div>
                    <div class="mr-3">
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($exam->stats['average_score'], 1) }}</p>
                        <p class="text-sm text-gray-500">متوسط الدرجات</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-purple-100">
                        <i class="fas fa-percentage text-purple-600"></i>
                    </div>
                    <div class="mr-3">
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($exam->stats['pass_rate'], 1) }}%</p>
                        <p class="text-sm text-gray-500">معدل النجاح</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- تبويبات المحتوى -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 space-x-reverse px-6" x-data="{ activeTab: 'questions' }">
                <button @click="activeTab = 'questions'" 
                        :class="activeTab === 'questions' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fas fa-question-circle ml-2"></i>
                    الأسئلة ({{ $exam->examQuestions->count() }})
                </button>
                <button @click="activeTab = 'attempts'" 
                        :class="activeTab === 'attempts' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fas fa-users ml-2"></i>
                    المحاولات ({{ $exam->attempts->count() }})
                </button>
                <button @click="activeTab = 'settings'" 
                        :class="activeTab === 'settings' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fas fa-cogs ml-2"></i>
                    الإعدادات
                </button>
                <button @click="activeTab = 'actions'" 
                        :class="activeTab === 'actions' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fas fa-tools ml-2"></i>
                    الإجراءات
                </button>
            </nav>
        </div>

        <div class="p-6">
            <!-- تبويب الأسئلة -->
            <div x-show="activeTab === 'questions'">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-900">أسئلة الامتحان</h4>
                    <a href="{{ route('admin.exams.questions.manage', $exam) }}" 
                       class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-cogs ml-2"></i>
                        إدارة الأسئلة
                    </a>
                </div>

                @if($exam->examQuestions->count() > 0)
                    <div class="space-y-3">
                        @foreach($exam->examQuestions as $examQuestion)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-4 space-x-reverse">
                                    <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                                        <span class="text-primary-600 font-medium text-sm">{{ $examQuestion->order }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ Str::limit($examQuestion->question->question, 80) }}
                                        </p>
                                        <div class="flex items-center gap-4 text-xs text-gray-500 mt-1">
                                            <span>{{ $examQuestion->question->getTypeLabel() }}</span>
                                            <span>{{ $examQuestion->marks }} نقطة</span>
                                            @if($examQuestion->question->category)
                                                <span>{{ $examQuestion->question->category->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        @if($examQuestion->question->difficulty_level == 'easy') bg-green-100 text-green-800
                                        @elseif($examQuestion->question->difficulty_level == 'medium') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ $examQuestion->question->getDifficultyLabel() }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-question-circle text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد أسئلة</h3>
                        <p class="text-gray-500 mb-4">ابدأ بإضافة الأسئلة لهذا الامتحان</p>
                        <a href="{{ route('admin.exams.questions.manage', $exam) }}" 
                           class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            إضافة أسئلة
                        </a>
                    </div>
                @endif
            </div>

            <!-- تبويب المحاولات -->
            <div x-show="activeTab === 'attempts'">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-900">محاولات الطلاب</h4>
                    <a href="{{ route('admin.exams.statistics', $exam) }}" 
                       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-chart-bar ml-2"></i>
                        إحصائيات مفصلة
                    </a>
                </div>

                @if($exam->attempts->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الطالب</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النتيجة</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الوقت المستغرق</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ المحاولة</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($exam->attempts->take(10) as $attempt)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                                    <span class="text-primary-600 font-medium">
                                                        {{ substr($attempt->user->name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div class="mr-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $attempt->user->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $attempt->user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($attempt->status === 'completed')
                                                <div class="text-sm font-medium text-gray-900">{{ number_format($attempt->score, 1) }} / {{ $exam->total_marks }}</div>
                                                <div class="text-sm text-gray-500">{{ number_format($attempt->percentage, 1) }}%</div>
                                            @else
                                                <span class="text-sm text-gray-500">لم يكتمل</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $attempt->formatted_time }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $attempt->result_color == 'green' ? 'bg-green-100 text-green-800 : ']
                                                   ($attempt->result_color == 'red' ? 'bg-red-100 text-red-800 ': ''bg-gray-100 text-gray-800 }}">
                                                {{ $attempt->result_status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">']
                                            {{ $attempt->created_at->format('Y-m-d H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-users text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد محاولات</h3>
                        <p class="text-gray-500">لم يقم أي طالب بأداء هذا الامتحان بعد</p>
                    </div>
                @endif
            </div>

            <!-- تبويب الإعدادات -->
            <div x-show="activeTab === 'settings'">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">إعدادات الامتحان</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <h5 class="font-medium text-gray-900">إعدادات العرض</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">خلط الأسئلة</span>
                                <span class="font-medium {{ $exam->randomize_questions ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $exam->randomize_questions ? 'مفعل' : 'معطل' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">خلط الخيارات</span>
                                <span class="font-medium {{ $exam->randomize_options ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $exam->randomize_options ? 'مفعل' : 'معطل' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">عرض النتائج فوراً</span>
                                <span class="font-medium {{ $exam->show_results_immediately ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $exam->show_results_immediately ? 'مفعل' : 'معطل' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">عرض الإجابات الصحيحة</span>
                                <span class="font-medium {{ $exam->show_correct_answers ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $exam->show_correct_answers ? 'مفعل' : 'معطل' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h5 class="font-medium text-gray-900">إعدادات الأمان</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">منع تبديل التبويبات</span>
                                <span class="font-medium {{ $exam->prevent_tab_switch ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $exam->prevent_tab_switch ? 'مفعل' : 'معطل' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">تسليم تلقائي</span>
                                <span class="font-medium {{ $exam->auto_submit ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $exam->auto_submit ? 'مفعل' : 'معطل' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">تتطلب كاميرا</span>
                                <span class="font-medium {{ $exam->require_camera ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $exam->require_camera ? 'مطلوبة' : 'غير مطلوبة' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">تتطلب مايكروفون</span>
                                <span class="font-medium {{ $exam->require_microphone ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $exam->require_microphone ? 'مطلوب' : 'غير مطلوب' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- تبويب الإجراءات -->
            <div x-show="activeTab === 'actions'">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- تفعيل/إيقاف -->
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <h5 class="font-medium text-gray-900 mb-2">حالة الامتحان</h5>
                        <p class="text-sm text-gray-500 mb-4">تفعيل أو إيقاف الامتحان</p>
                        <button onclick="toggleExamStatus({{ $exam->id }})" 
                                class="w-full {{ $exam->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            {{ $exam->is_active ? 'إيقاف الامتحان' : 'تفعيل الامتحان' }}
                        </button>
                    </div>

                    <!-- نشر/إلغاء نشر -->
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <h5 class="font-medium text-gray-900 mb-2">حالة النشر</h5>
                        <p class="text-sm text-gray-500 mb-4">نشر الامتحان للطلاب</p>
                        <button onclick="toggleExamPublish({{ $exam->id }})" 
                                class="w-full {{ $exam->is_published ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            {{ $exam->is_published ? 'إلغاء النشر' : 'نشر الامتحان' }}
                        </button>
                    </div>

                    <!-- معاينة -->
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <h5 class="font-medium text-gray-900 mb-2">معاينة الامتحان</h5>
                        <p class="text-sm text-gray-500 mb-4">اختبر الامتحان كطالب</p>
                        <a href="{{ route('admin.exams.preview', $exam) }}" 
                           class="w-full bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg font-medium transition-colors block text-center">
                            معاينة الامتحان
                        </a>
                    </div>

                    <!-- نسخ الامتحان -->
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <h5 class="font-medium text-gray-900 mb-2">نسخ الامتحان</h5>
                        <p class="text-sm text-gray-500 mb-4">إنشاء نسخة من الامتحان</p>
                        <form action="{{ route('admin.exams.duplicate', $exam) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('هل تريد إنشاء نسخة من هذا الامتحان؟')"
                                    class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                نسخ الامتحان
                            </button>
                        </form>
                    </div>

                    <!-- حذف الامتحان -->
                    <div class="p-4 border border-red-200 rounded-lg bg-red-50">
                        <h5 class="font-medium text-red-900 mb-2">حذف الامتحان</h5>
                        <p class="text-sm text-red-700 mb-4">حذف نهائي (لا يمكن التراجع)</p>
                        <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    onclick="return confirm('هل أنت متأكد من حذف هذا الامتحان؟ هذا الإجراء لا يمكن التراجع عنه!')"
                                    class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                حذف الامتحان
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleExamStatus(examId) {
    if (confirm('هل تريد تغيير حالة هذا الامتحان؟')) {
        fetch(`/admin/exams/${examId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('حدث خطأ في تغيير حالة الامتحان', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('حدث خطأ في تغيير حالة الامتحان', 'error');
        });
    }
}

function toggleExamPublish(examId) {
    if (confirm('هل تريد تغيير حالة نشر هذا الامتحان؟')) {
        fetch(`/admin/exams/${examId}/toggle-publish`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('حدث خطأ في تغيير حالة النشر', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('حدث خطأ في تغيير حالة النشر', 'error');
        });
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg text-white z-50 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} ml-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// تحسين التبويبات - إضافة تفعيل التبويب عند النقر على الرابط
document.addEventListener('DOMContentLoaded', function() {
    // التأكد من أن Alpine.js محمل
    document.addEventListener('alpine:init', () => {
        console.log('Alpine.js loaded and tabs initialized');
    });
});
</script>
@endpush
@endsection
