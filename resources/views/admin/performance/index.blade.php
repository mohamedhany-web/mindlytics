@extends('layouts.admin')

@section('title', 'أداء الموقع - Mindlytics')
@section('header', 'أداء الموقع')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-tachometer-alt text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">لوحة أداء الموقع</h2>
                    <p class="text-sm text-slate-600 mt-1">متابعة وتحسين أداء الموقع</p>
                </div>
            </div>
        </div>
    </section>

    <!-- معلومات النظام -->
    <section class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-server text-blue-600"></i>
                معلومات النظام
            </h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
            <div class="rounded-xl border border-slate-200 bg-white p-5 flex items-center justify-between hover:shadow-md transition-shadow duration-200">
                <div>
                    <p class="text-xs font-semibold text-slate-600 mb-2">إصدار PHP</p>
                    <p class="text-2xl font-black text-slate-900">{{ htmlspecialchars($systemInfo['php_version'] ?? 'N/A', ENT_QUOTES, 'UTF-8') }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                    <i class="fab fa-php text-xl"></i>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 flex items-center justify-between hover:shadow-md transition-shadow duration-200">
                <div>
                    <p class="text-xs font-semibold text-slate-600 mb-2">إصدار Laravel</p>
                    <p class="text-2xl font-black text-slate-900">{{ htmlspecialchars($systemInfo['laravel_version'] ?? 'N/A', ENT_QUOTES, 'UTF-8') }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                    <i class="fab fa-laravel text-xl"></i>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 flex items-center justify-between hover:shadow-md transition-shadow duration-200">
                <div>
                    <p class="text-xs font-semibold text-slate-600 mb-2">حد الذاكرة</p>
                    <p class="text-2xl font-black text-slate-900">{{ htmlspecialchars($systemInfo['memory_limit'] ?? 'N/A', ENT_QUOTES, 'UTF-8') }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                    <i class="fas fa-memory text-xl"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- معلومات الأداء -->
    <section class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-chart-line text-blue-600"></i>
                معلومات الأداء
            </h3>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
            <!-- استخدام الذاكرة -->
            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <h4 class="text-lg font-black text-slate-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-microchip text-blue-600"></i>
                    استخدام الذاكرة
                </h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200">
                        <span class="text-sm font-semibold text-slate-700">الاستخدام الحالي:</span>
                        <span class="text-lg font-black text-slate-900">{{ htmlspecialchars($performanceInfo['memory_usage'] ?? 'N/A', ENT_QUOTES, 'UTF-8') }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200">
                        <span class="text-sm font-semibold text-slate-700">الحد الأقصى:</span>
                        <span class="text-lg font-black text-slate-900">{{ htmlspecialchars($performanceInfo['memory_peak'] ?? 'N/A', ENT_QUOTES, 'UTF-8') }}</span>
                    </div>
                </div>
            </div>

            <!-- مساحة القرص -->
            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <h4 class="text-lg font-black text-slate-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-hdd text-blue-600"></i>
                    مساحة القرص
                </h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200">
                        <span class="text-sm font-semibold text-slate-700">المساحة المتاحة:</span>
                        <span class="text-lg font-black text-slate-900">{{ htmlspecialchars($performanceInfo['disk_free_space'] ?? 'N/A', ENT_QUOTES, 'UTF-8') }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200">
                        <span class="text-sm font-semibold text-slate-700">إجمالي المساحة:</span>
                        <span class="text-lg font-black text-slate-900">{{ htmlspecialchars($performanceInfo['disk_total_space'] ?? 'N/A', ENT_QUOTES, 'UTF-8') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- إدارة الكاش -->
    <section class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-database text-blue-600"></i>
                إدارة الكاش
            </h3>
            <p class="text-sm text-slate-600 mt-2">إدارة وتنظيف الكاش لتحسين الأداء</p>
        </div>

        <!-- أحجام الكاش -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-6">
            <div class="rounded-xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-4">
                <div class="text-xs font-semibold text-slate-600 mb-2">كاش الإعدادات</div>
                <div class="text-xl font-black text-slate-900">{{ htmlspecialchars($cacheSizes['config'] ?? '0 KB', ENT_QUOTES, 'UTF-8') }}</div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-4">
                <div class="text-xs font-semibold text-slate-600 mb-2">كاش المسارات</div>
                <div class="text-xl font-black text-slate-900">{{ htmlspecialchars($cacheSizes['route'] ?? '0 KB', ENT_QUOTES, 'UTF-8') }}</div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-4">
                <div class="text-xs font-semibold text-slate-600 mb-2">كاش العروض</div>
                <div class="text-xl font-black text-slate-900">{{ htmlspecialchars($cacheSizes['view'] ?? '0 KB', ENT_QUOTES, 'UTF-8') }}</div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-4">
                <div class="text-xs font-semibold text-slate-600 mb-2">كاش التطبيق</div>
                <div class="text-xl font-black text-slate-900">{{ htmlspecialchars($cacheSizes['application'] ?? '0 KB', ENT_QUOTES, 'UTF-8') }}</div>
            </div>
        </div>

        <!-- إجراءات الكاش -->
        <div class="px-6 py-6 border-t border-slate-200">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <button onclick="clearCache('config')" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white px-4 py-3 text-sm font-semibold shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-trash"></i>
                    مسح كاش الإعدادات
                </button>
                <button onclick="clearCache('route')" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white px-4 py-3 text-sm font-semibold shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-trash"></i>
                    مسح كاش المسارات
                </button>
                <button onclick="clearCache('view')" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white px-4 py-3 text-sm font-semibold shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-trash"></i>
                    مسح كاش العروض
                </button>
                <button onclick="clearCache('application')" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white px-4 py-3 text-sm font-semibold shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-trash"></i>
                    مسح كاش التطبيق
                </button>
                <button onclick="clearCache('all')" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white px-4 py-3 text-sm font-semibold shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-broom"></i>
                    مسح جميع الكاش
                </button>
                <button onclick="optimizeCache()" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 text-white px-4 py-3 text-sm font-semibold shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-rocket"></i>
                    تحسين الأداء
                </button>
            </div>
        </div>
    </section>

    <!-- أدوات التحسين -->
    <section class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-tools text-blue-600"></i>
                أدوات التحسين
            </h3>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
            <!-- تنظيف الملفات المؤقتة -->
            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <h4 class="text-lg font-black text-slate-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-file-archive text-blue-600"></i>
                    تنظيف الملفات المؤقتة
                </h4>
                <p class="text-slate-600 mb-4 text-sm">
                    حذف الملفات المؤقتة القديمة لتحرير مساحة القرص
                </p>
                <button onclick="clearTempFiles()" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white px-4 py-3 text-sm font-semibold shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-broom"></i>
                    تنظيف الملفات المؤقتة
                </button>
            </div>

            <!-- تحسين قاعدة البيانات -->
            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <h4 class="text-lg font-black text-slate-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-database text-blue-600"></i>
                    تحسين قاعدة البيانات
                </h4>
                <p class="text-slate-600 mb-4 text-sm">
                    تحسين الجداول وتحسين الأداء
                </p>
                <button onclick="optimizeDatabase()" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white px-4 py-3 text-sm font-semibold shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-tools"></i>
                    تحسين قاعدة البيانات
                </button>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
let isSubmitting = false;

function clearCache(type) {
    if (isSubmitting) return;
    
    const sanitizedType = type.replace(/[^a-z_]/gi, '');
    if (!['config', 'route', 'view', 'application', 'compiled', 'all'].includes(sanitizedType)) {
        alert('نوع غير صالح');
        return;
    }

    if (confirm('هل أنت متأكد من مسح الكاش؟')) {
        isSubmitting = true;
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري المعالجة...';
        
        fetch(`{{ route('admin.performance.clear-cache') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            },
            body: JSON.stringify({ type: sanitizedType })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('حدث خطأ: ' + data.message);
                button.disabled = false;
                button.innerHTML = originalText;
                isSubmitting = false;
            }
        })
        .catch(error => {
            alert('حدث خطأ أثناء الاتصال');
            button.disabled = false;
            button.innerHTML = originalText;
            isSubmitting = false;
        });
    }
}

function optimizeCache() {
    if (isSubmitting) return;
    
    if (confirm('هل تريد تحسين الأداء بإنشاء الكاش؟')) {
        isSubmitting = true;
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحسين...';
        
        fetch(`{{ route('admin.performance.optimize-cache') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('حدث خطأ: ' + data.message);
                button.disabled = false;
                button.innerHTML = originalText;
                isSubmitting = false;
            }
        })
        .catch(error => {
            alert('حدث خطأ أثناء الاتصال');
            button.disabled = false;
            button.innerHTML = originalText;
            isSubmitting = false;
        });
    }
}

function clearTempFiles() {
    if (isSubmitting) return;
    
    if (confirm('هل تريد حذف الملفات المؤقتة؟')) {
        isSubmitting = true;
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التنظيف...';
        
        fetch(`{{ route('admin.performance.clear-temp-files') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
            } else {
                alert('حدث خطأ: ' + data.message);
            }
            button.disabled = false;
            button.innerHTML = originalText;
            isSubmitting = false;
        })
        .catch(error => {
            alert('حدث خطأ أثناء الاتصال');
            button.disabled = false;
            button.innerHTML = originalText;
            isSubmitting = false;
        });
    }
}

function optimizeDatabase() {
    if (isSubmitting) return;
    
    if (confirm('هل تريد تحسين قاعدة البيانات؟ قد يستغرق هذا الأمر بعض الوقت.')) {
        isSubmitting = true;
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحسين...';
        
        fetch(`{{ route('admin.performance.optimize-database') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
            } else {
                alert('حدث خطأ: ' + data.message);
            }
            button.disabled = false;
            button.innerHTML = originalText;
            isSubmitting = false;
        })
        .catch(error => {
            alert('حدث خطأ أثناء الاتصال');
            button.disabled = false;
            button.innerHTML = originalText;
            isSubmitting = false;
        });
    }
}
</script>
@endpush
@endsection
