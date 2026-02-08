@extends('layouts.admin')

@section('title', 'تفاصيل الإشعار')
@section('header', 'تفاصيل الإشعار: ' . htmlspecialchars($notification->title, ENT_QUOTES, 'UTF-8'))

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-bell text-lg"></i>
                </div>
                <div>
                    <nav class="text-xs font-medium text-slate-500 flex flex-wrap items-center gap-2 mb-1">
                        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-700">لوحة التحكم</a>
                        <span>/</span>
                        <a href="{{ route('admin.notifications.index') }}" class="text-blue-600 hover:text-blue-700">الإشعارات</a>
                        <span>/</span>
                        <span class="text-slate-600">{{ htmlspecialchars(Str::limit($notification->title, 30), ENT_QUOTES, 'UTF-8') }}</span>
                    </nav>
                    <h1 class="text-2xl font-black text-slate-900 mt-1">تفاصيل الإشعار</h1>
                </div>
            </div>
            <a href="{{ route('admin.notifications.index') }}" 
               class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                <i class="fas fa-arrow-right"></i>
                العودة
            </a>
        </div>
    </div>

    <!-- تفاصيل الإشعار -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- المحتوى الرئيسي -->
        <div class="xl:col-span-2 space-y-6">
            <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                            <i class="fas fa-bell text-lg"></i>
                        </div>
                        تفاصيل الإشعار
                    </h3>
                    <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1 text-xs font-semibold {{ $notification->is_read ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-blue-100 text-blue-700 border border-blue-200' }}">
                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                        {{ $notification->is_read ? 'مقروء' : 'غير مقروء' }}
                    </span>
                </div>
                <div class="p-6">
                    <div class="space-y-5">
                        <!-- العنوان -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-heading text-blue-600 text-sm"></i>
                                العنوان
                            </label>
                            <div class="font-bold text-xl text-slate-900">{{ htmlspecialchars($notification->title, ENT_QUOTES, 'UTF-8') }}</div>
                        </div>

                        <!-- النص -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-align-right text-blue-600 text-sm"></i>
                                النص
                            </label>
                            <div class="text-slate-900 bg-slate-50 p-4 rounded-lg whitespace-pre-wrap border border-slate-200">{{ htmlspecialchars($notification->message, ENT_QUOTES, 'UTF-8') }}</div>
                        </div>

                        <!-- معلومات التصنيف -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-4 rounded-lg border border-slate-200 bg-slate-50">
                                <label class="block text-xs font-semibold text-slate-700 mb-2">نوع الإشعار</label>
                                <div class="text-sm font-bold text-slate-900">{{ htmlspecialchars(\App\Models\Notification::getTypes()[$notification->type] ?? $notification->type, ENT_QUOTES, 'UTF-8') }}</div>
                            </div>
                            
                            <div class="p-4 rounded-lg border border-slate-200 bg-slate-50">
                                <label class="block text-xs font-semibold text-slate-700 mb-2">الأولوية</label>
                                <div class="text-sm font-bold text-slate-900">{{ htmlspecialchars(\App\Models\Notification::getPriorities()[$notification->priority] ?? $notification->priority, ENT_QUOTES, 'UTF-8') }}</div>
                            </div>
                        </div>

                        <!-- معلومات الاستهداف -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-4 rounded-lg border border-slate-200 bg-slate-50">
                                <label class="block text-xs font-semibold text-slate-700 mb-2">نوع الاستهداف</label>
                                <div class="text-sm font-bold text-slate-900">{{ htmlspecialchars(\App\Models\Notification::getTargetTypes()[$notification->target_type] ?? $notification->target_type, ENT_QUOTES, 'UTF-8') }}</div>
                            </div>
                            
                            @if($notification->target_id)
                                <div class="p-4 rounded-lg border border-slate-200 bg-slate-50">
                                    <label class="block text-xs font-semibold text-slate-700 mb-2">الهدف المحدد</label>
                                    <div class="text-sm font-bold text-slate-900">ID: {{ htmlspecialchars($notification->target_id, ENT_QUOTES, 'UTF-8') }}</div>
                                </div>
                            @endif
                        </div>

                        <!-- رابط الإجراء -->
                        @if($notification->action_url && $notification->action_text)
                            <div class="p-4 rounded-lg border border-blue-200 bg-blue-50">
                                <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                    <i class="fas fa-link text-blue-600 text-sm"></i>
                                    رابط الإجراء
                                </label>
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                    <a href="{{ htmlspecialchars($notification->action_url, ENT_QUOTES, 'UTF-8') }}" 
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-colors">
                                        {{ htmlspecialchars($notification->action_text, ENT_QUOTES, 'UTF-8') }}
                                        <i class="fas fa-external-link-alt text-xs"></i>
                                    </a>
                                    <code class="text-xs bg-white px-3 py-2 rounded-lg border border-slate-200 text-slate-600 break-all">{{ htmlspecialchars($notification->action_url, ENT_QUOTES, 'UTF-8') }}</code>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- الشريط الجانبي -->
        <div class="space-y-6">
            <!-- معلومات المستقبل -->
            <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-user text-blue-600"></i>
                        المستقبل
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-md">
                            {{ mb_substr(htmlspecialchars($notification->user->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8'), 0, 1) }}
                        </div>
                        <div>
                            <div class="font-bold text-slate-900">{{ htmlspecialchars($notification->user->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}</div>
                            <div class="text-xs text-slate-600 mt-0.5">{{ htmlspecialchars($notification->user->email ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}</div>
                            @if($notification->user->phone)
                                <div class="text-xs text-slate-600 mt-0.5">{{ htmlspecialchars($notification->user->phone, ENT_QUOTES, 'UTF-8') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- إحصائيات -->
            <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-chart-bar text-blue-600"></i>
                        إحصائيات
                    </h3>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
                        <span class="text-xs font-semibold text-slate-700">تاريخ الإرسال</span>
                        <span class="text-sm font-bold text-slate-900">{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
                        <span class="text-xs font-semibold text-slate-700">تاريخ القراءة</span>
                        <span class="text-sm font-bold text-slate-900">
                            {{ $notification->read_at ? $notification->read_at->format('d/m/Y H:i') : 'لم يُقرأ بعد' }}
                        </span>
                    </div>

                    @if($notification->read_at)
                        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
                            <span class="text-xs font-semibold text-slate-700">وقت الاستجابة</span>
                            <span class="text-sm font-bold text-slate-900">{{ $notification->read_at->diffInMinutes($notification->created_at) }} دقيقة</span>
                        </div>
                    @endif

                    @if($notification->expires_at)
                        <div class="flex items-center justify-between p-3 rounded-lg {{ $notification->isExpired() ? 'bg-rose-50 border border-rose-200' : 'bg-slate-50' }}">
                            <span class="text-xs font-semibold text-slate-700">ينتهي في</span>
                            <span class="text-sm font-bold {{ $notification->isExpired() ? 'text-rose-600' : 'text-slate-900' }}">
                                {{ $notification->expires_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
                        <span class="text-xs font-semibold text-slate-700">ID الإشعار</span>
                        <span class="text-sm font-bold text-slate-900">{{ $notification->id }}</span>
                    </div>
                </div>
            </div>

            <!-- إجراءات الأدمن -->
            <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-cog text-blue-600"></i>
                        إجراءات
                    </h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('admin.notifications.create') }}" 
                       class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-plus"></i>
                        إرسال إشعار جديد
                    </a>
                    
                    <form action="{{ route('admin.notifications.destroy', $notification) }}" method="POST" class="delete-form" onsubmit="return confirmDelete(event);">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-700 hover:to-rose-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-trash"></i>
                            حذف الإشعار
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // حماية من Double Submit
    let formSubmitting = false;

    function confirmDelete(event) {
        if (formSubmitting) {
            event.preventDefault();
            return false;
        }
        const confirmed = confirm('هل أنت متأكد من حذف هذا الإشعار؟');
        if (confirmed) {
            formSubmitting = true;
            const btn = event.target.closest('form').querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحذف...';
            }
        }
        return confirmed;
    }

    // حماية من XSS في الروابط الخارجية
    document.querySelectorAll('a[target="_blank"]').forEach(link => {
        link.setAttribute('rel', 'noopener noreferrer');
    });

    // منع الإرسال المتكرر
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (formSubmitting) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
@endpush
@endsection
