@extends('layouts.admin')

@section('title', 'تفاصيل الطلب #' . $order->id . ' - Mindlytics')
@section('header', 'تفاصيل الطلب #' . $order->id)

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-shopping-cart text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-slate-900">تفاصيل الطلب #{{ $order->id }}</h1>
                    <p class="text-sm text-slate-600 mt-1 flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-xs"></i>
                        {{ $order->created_at->format('d/m/Y - H:i') }}
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.orders.index') }}" 
               class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                <i class="fas fa-arrow-right"></i>
                العودة للطلبات
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- تفاصيل الطلب -->
        <div class="lg:col-span-2 space-y-6">
            <!-- معلومات الطالب -->
            <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                    <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                            <i class="fas fa-user-graduate text-lg"></i>
                        </div>
                        معلومات الطالب
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 rounded-lg border border-slate-200 bg-slate-50">
                            <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-user text-blue-600 text-sm"></i>
                                الاسم
                            </label>
                            <div class="text-base font-bold text-slate-900">{{ htmlspecialchars($order->user->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}</div>
                        </div>
                        
                        <div class="p-4 rounded-lg border border-slate-200 bg-slate-50">
                            <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-phone text-blue-600 text-sm"></i>
                                رقم الهاتف
                            </label>
                            <div class="text-base font-bold text-slate-900">{{ htmlspecialchars($order->user->phone ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}</div>
                        </div>
                        
                        <div class="p-4 rounded-lg border border-slate-200 bg-slate-50">
                            <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-envelope text-blue-600 text-sm"></i>
                                البريد الإلكتروني
                            </label>
                            <div class="text-base font-bold text-slate-900 break-all">{{ htmlspecialchars($order->user->email ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}</div>
                        </div>
                        
                        <div class="p-4 rounded-lg border border-slate-200 bg-slate-50">
                            <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-calendar-check text-blue-600 text-sm"></i>
                                تاريخ التسجيل
                            </label>
                            <div class="text-base font-bold text-slate-900">{{ $order->user->created_at ? $order->user->created_at->format('d/m/Y') : 'غير محدد' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- معلومات الكورس أو المسار التعليمي -->
            <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                    <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                            @if($order->academic_year_id)
                                <i class="fas fa-route text-lg"></i>
                            @else
                                <i class="fas fa-book-open text-lg"></i>
                            @endif
                        </div>
                        {{ $order->academic_year_id ? 'معلومات المسار التعليمي' : 'معلومات الكورس' }}
                    </h2>
                </div>
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="w-full sm:w-24 h-24 bg-gradient-to-br {{ $order->academic_year_id ? 'from-green-500 to-green-600' : 'from-blue-500 to-blue-600' }} rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                            @if($order->academic_year_id && $order->learningPath && $order->learningPath->thumbnail)
                                <img src="{{ asset('storage/' . $order->learningPath->thumbnail) }}" alt="{{ htmlspecialchars($order->learningPath->name ?? 'مسار تعليمي', ENT_QUOTES, 'UTF-8') }}" 
                                     class="w-full h-full object-cover rounded-xl" onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\'%3E%3Cpath fill=\'%23fff\' d=\'M8 5v14l11-7z\'/%3E%3C/svg%3E';">
                            @elseif($order->course && $order->course->thumbnail)
                                <img src="{{ asset('storage/' . $order->course->thumbnail) }}" alt="{{ htmlspecialchars($order->course->title ?? 'كورس', ENT_QUOTES, 'UTF-8') }}" 
                                     class="w-full h-full object-cover rounded-xl" onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\'%3E%3Cpath fill=\'%23fff\' d=\'M8 5v14l11-7z\'/%3E%3C/svg%3E';">
                            @else
                                <i class="fas {{ $order->academic_year_id ? 'fa-route' : 'fa-play-circle' }} text-white text-3xl"></i>
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            @if($order->academic_year_id && $order->learningPath)
                                <h3 class="text-lg font-bold text-slate-900 mb-2">{{ htmlspecialchars($order->learningPath->name ?? 'مسار تعليمي', ENT_QUOTES, 'UTF-8') }}</h3>
                                <div class="flex flex-wrap items-center gap-2 mb-3">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-green-100 text-green-700 border border-green-200">
                                        <i class="fas fa-route text-xs"></i>
                                        مسار تعليمي
                                    </span>
                                    @if($order->learningPath->price)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        <i class="fas fa-money-bill-wave text-xs"></i>
                                        {{ number_format($order->learningPath->price, 2) }} ج.م
                                    </span>
                                    @endif
                                </div>
                                @if($order->learningPath->description)
                                    <p class="text-sm text-slate-600">
                                        {{ htmlspecialchars(Str::limit($order->learningPath->description, 150), ENT_QUOTES, 'UTF-8') }}
                                    </p>
                                @endif
                            @elseif($order->course)
                                <h3 class="text-lg font-bold text-slate-900 mb-2">{{ htmlspecialchars($order->course->title ?? 'كورس غير محدد', ENT_QUOTES, 'UTF-8') }}</h3>
                                @if($order->course->academicYear || $order->course->academicSubject)
                                <div class="flex flex-wrap items-center gap-2 mb-3">
                                    @if($order->course->academicYear)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-200">
                                        <i class="fas fa-graduation-cap text-xs"></i>
                                        {{ htmlspecialchars($order->course->academicYear->name, ENT_QUOTES, 'UTF-8') }}
                                    </span>
                                    @endif
                                    @if($order->course->academicSubject)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-indigo-100 text-indigo-700 border border-indigo-200">
                                        <i class="fas fa-layer-group text-xs"></i>
                                        {{ htmlspecialchars($order->course->academicSubject->name, ENT_QUOTES, 'UTF-8') }}
                                    </span>
                                    @endif
                                </div>
                                @endif
                                @if($order->course->description)
                                    <p class="text-sm text-slate-600">
                                        {{ htmlspecialchars(Str::limit($order->course->description, 150), ENT_QUOTES, 'UTF-8') }}
                                    </p>
                                @endif
                            @else
                                <h3 class="text-lg font-bold text-slate-900 mb-2">غير محدد</h3>
                                <p class="text-sm text-slate-600">لا توجد معلومات متاحة</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- تفاصيل الدفع -->
            <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                    <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                            <i class="fas fa-credit-card text-lg"></i>
                        </div>
                        تفاصيل الدفع
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 rounded-lg border border-slate-200 bg-slate-50">
                            <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-money-bill-wave text-blue-600 text-sm"></i>
                                المبلغ
                            </label>
                            <div class="text-2xl font-black text-blue-600">
                                {{ number_format($order->amount, 2) }} <span class="text-base text-slate-600 font-semibold">ج.م</span>
                            </div>
                        </div>
                        
                        <div class="p-4 rounded-lg border border-slate-200 bg-slate-50">
                            <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-wallet text-blue-600 text-sm"></i>
                                طريقة الدفع
                            </label>
                            <div class="text-base font-bold text-slate-900">
                                @if($order->payment_method == 'bank_transfer')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-200">
                                        <i class="fas fa-university text-xs"></i>
                                        تحويل بنكي
                                    </span>
                                @elseif($order->payment_method == 'cash')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        <i class="fas fa-money-bill text-xs"></i>
                                        نقدي
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        <i class="fas fa-question-circle text-xs"></i>
                                        أخرى
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="p-4 rounded-lg border border-slate-200 bg-slate-50">
                            <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-calendar-alt text-blue-600 text-sm"></i>
                                تاريخ الطلب
                            </label>
                            <div class="text-base font-bold text-slate-900">
                                {{ $order->created_at->format('d/m/Y') }}
                                <span class="text-sm text-slate-600 font-normal">- {{ $order->created_at->format('H:i') }}</span>
                            </div>
                        </div>
                        
                        @if($order->approved_at)
                        <div class="p-4 rounded-lg border border-slate-200 bg-slate-50">
                            <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-check-circle text-blue-600 text-sm"></i>
                                تاريخ المراجعة
                            </label>
                            <div class="text-base font-bold text-slate-900">
                                {{ $order->approved_at->format('d/m/Y') }}
                                <span class="text-sm text-slate-600 font-normal">- {{ $order->approved_at->format('H:i') }}</span>
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($order->notes)
                        <div class="mt-4 p-4 rounded-lg border border-slate-200 bg-slate-50">
                            <label class="block text-sm font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-sticky-note text-blue-600"></i>
                                ملاحظات الطالب
                            </label>
                            <div class="text-sm text-slate-700">
                                {{ htmlspecialchars($order->notes, ENT_QUOTES, 'UTF-8') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- صورة الإيصال -->
            @if($order->payment_proof)
                <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                        <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600">
                                <i class="fas fa-receipt text-lg"></i>
                            </div>
                            إيصال الدفع
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="text-center">
                            <div class="inline-block p-3 rounded-xl border border-slate-200 bg-slate-50">
                                @php
                                    $fullPath = storage_path('app/public/' . $order->payment_proof);
                                    $imageExists = file_exists($fullPath);
                                    
                                    $imageUrl = null;
                                    if ($imageExists) {
                                        $imageUrl = asset('storage/' . $order->payment_proof);
                                        if (!file_exists(public_path('storage/' . $order->payment_proof))) {
                                            try {
                                                $imageUrl = route('storage.file', ['path' => htmlspecialchars($order->payment_proof, ENT_QUOTES, 'UTF-8')]);
                                            } catch (\Exception $e) {
                                                $imageUrl = url('/storage/' . htmlspecialchars($order->payment_proof, ENT_QUOTES, 'UTF-8'));
                                            }
                                        }
                                    }
                                @endphp
                                @if($imageExists)
                                <img src="{{ htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') }}" 
                                     alt="إيصال الدفع" 
                                     class="max-w-full h-auto rounded-lg shadow-md cursor-pointer hover:shadow-lg transition-all duration-200"
                                     onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';"
                                     onclick="openImageModal(this.src)">
                                <div class="hidden p-4 rounded-lg border border-amber-200 bg-amber-50">
                                    <p class="text-sm text-amber-800 flex items-center gap-2">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <span>الصورة غير متوفرة حالياً</span>
                                    </p>
                                </div>
                                @else
                                <div class="p-4 rounded-lg border border-amber-200 bg-amber-50">
                                    <p class="text-sm text-amber-800 flex items-center gap-2">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <span>الصورة غير موجودة في الخادم</span>
                                    </p>
                                </div>
                                @endif
                            </div>
                            <p class="text-xs text-slate-600 mt-4 flex items-center justify-center gap-2">
                                <i class="fas fa-info-circle"></i>
                                اضغط على الصورة لعرضها بحجم أكبر
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- حالة الطلب والإجراءات -->
        <div class="lg:col-span-1">
            <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden sticky top-8">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                    <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center
                            @if($order->status == 'pending') bg-amber-100 text-amber-600
                            @elseif($order->status == 'approved') bg-emerald-100 text-emerald-600
                            @else bg-rose-100 text-rose-600
                            @endif">
                            <i class="fas 
                                @if($order->status == 'pending') fa-clock
                                @elseif($order->status == 'approved') fa-check-circle
                                @else fa-times-circle
                                @endif text-lg"></i>
                        </div>
                        حالة الطلب
                    </h2>
                </div>
                
                <div class="p-6">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-xl flex items-center justify-center shadow-md
                            @if($order->status == 'pending') bg-amber-100
                            @elseif($order->status == 'approved') bg-emerald-100
                            @else bg-rose-100
                            @endif">
                            <i class="fas 
                                @if($order->status == 'pending') fa-clock text-amber-600 text-2xl
                                @elseif($order->status == 'approved') fa-check text-emerald-600 text-2xl
                                @else fa-times text-rose-600 text-2xl
                                @endif"></i>
                        </div>
                        
                        <div class="text-xl font-black mb-2
                            @if($order->status == 'pending') text-amber-600
                            @elseif($order->status == 'approved') text-emerald-600
                            @else text-rose-600
                            @endif">
                            {{ htmlspecialchars($order->status_text ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}
                        </div>
                        <p class="text-sm text-slate-600">
                            @if($order->status == 'pending')
                                جاري المراجعة
                            @elseif($order->status == 'approved')
                                تمت الموافقة
                            @else
                                تم الرفض
                            @endif
                        </p>
                    </div>

                    @if($order->status == 'pending')
                        <script>
                            // تعريف الدوال مباشرة قبل الأزرار لضمان توفرها
                            (function() {
                                let isProcessing = false;
                                
                                window.approveOrder = async function(orderId) {
                                    console.log('approveOrder called with orderId:', orderId);
                                    
                                    if (isProcessing) {
                                        console.log('Already processing, returning...');
                                        return;
                                    }

                                    const confirmed = confirm('هل أنت متأكد من الموافقة على هذا الطلب؟\nسيتم تفعيل الكورس للطالب تلقائياً.');
                                    if (!confirmed) {
                                        console.log('User cancelled approval');
                                        return;
                                    }
                                    
                                    console.log('Starting approval process...');

                                    const approveBtn = document.getElementById('approveBtn');
                                    const rejectBtn = document.getElementById('rejectBtn');
                                    const originalApproveText = approveBtn ? approveBtn.innerHTML : '';

                                    isProcessing = true;
                                    if (approveBtn) {
                                        approveBtn.disabled = true;
                                        approveBtn.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري المعالجة...';
                                    }
                                    if (rejectBtn) rejectBtn.disabled = true;

                                    try {
                                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                                        
                                        if (!csrfToken) {
                                            alert('خطأ: لم يتم العثور على CSRF token');
                                            if (approveBtn) {
                                                approveBtn.disabled = false;
                                                approveBtn.innerHTML = originalApproveText;
                                            }
                                            if (rejectBtn) rejectBtn.disabled = false;
                                            isProcessing = false;
                                            return;
                                        }
                                        
                                        console.log('CSRF Token found, making request...');
                                        
                                        const formData = new FormData();
                                        formData.append('_token', csrfToken);

                                        const controller = new AbortController();
                                        const timeoutId = setTimeout(() => controller.abort(), 120000);

                                        console.log('Fetching:', `/admin/orders/${orderId}/approve`);
                                        
                                        const response = await fetch(`/admin/orders/${orderId}/approve`, {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': csrfToken,
                                                'Accept': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest'
                                            },
                                            body: formData,
                                            signal: controller.signal
                                        });

                                        clearTimeout(timeoutId);

                                        const contentType = response.headers.get('content-type');
                                        let data;
                                        
                                        if (contentType && contentType.includes('application/json')) {
                                            data = await response.json();
                                        } else {
                                            if (response.redirected || response.status === 200) {
                                                window.location.reload();
                                                return;
                                            }
                                            const text = await response.text();
                                            throw new Error(text || 'حدث خطأ أثناء المعالجة');
                                        }

                                        if (response.ok) {
                                            if (data.redirect) {
                                                window.location.href = data.redirect;
                                            } else {
                                                window.location.reload();
                                            }
                                        } else {
                                            console.error('Error Response Data:', data);
                                            const errorMsg = data.error || data.message || 'حدث خطأ أثناء المعالجة';
                                            const errorDetails = data.file && data.line ? `\n\nالملف: ${data.file}\nالسطر: ${data.line}` : '';
                                            alert(errorMsg + errorDetails);
                                            
                                            if (approveBtn) {
                                                approveBtn.disabled = false;
                                                approveBtn.innerHTML = originalApproveText;
                                            }
                                            if (rejectBtn) rejectBtn.disabled = false;
                                            isProcessing = false;
                                        }
                                    } catch (error) {
                                        console.error('Error in approveOrder:', error);
                                        let errorMessage = '';
                                        
                                        if (error.name === 'AbortError') {
                                            errorMessage = 'انتهت مهلة الانتظار. العملية تستغرق وقتاً طويلاً. يرجى المحاولة مرة أخرى أو مراجعة السجلات.';
                                        } else if (error.message) {
                                            errorMessage = error.message;
                                        } else {
                                            errorMessage = 'حدث خطأ غير معروف أثناء الموافقة على الطلب: ' + error.toString();
                                        }
                                        
                                        alert(errorMessage);
                                        
                                        if (approveBtn) {
                                            approveBtn.disabled = false;
                                            approveBtn.innerHTML = originalApproveText;
                                        }
                                        if (rejectBtn) rejectBtn.disabled = false;
                                        isProcessing = false;
                                    }
                                };
                                
                                window.rejectOrder = async function(orderId) {
                                    if (isProcessing) return;

                                    const confirmed = confirm('هل أنت متأكد من رفض هذا الطلب؟');
                                    if (!confirmed) return;

                                    const approveBtn = document.getElementById('approveBtn');
                                    const rejectBtn = document.getElementById('rejectBtn');
                                    const originalRejectText = rejectBtn ? rejectBtn.innerHTML : '';

                                    isProcessing = true;
                                    if (approveBtn) approveBtn.disabled = true;
                                    if (rejectBtn) {
                                        rejectBtn.disabled = true;
                                        rejectBtn.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري المعالجة...';
                                    }

                                    try {
                                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                                        const formData = new FormData();
                                        formData.append('_token', csrfToken);

                                        const controller = new AbortController();
                                        const timeoutId = setTimeout(() => controller.abort(), 60000);

                                        const response = await fetch(`/admin/orders/${orderId}/reject`, {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': csrfToken,
                                                'Accept': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest'
                                            },
                                            body: formData,
                                            signal: controller.signal
                                        });

                                        clearTimeout(timeoutId);

                                        const contentType = response.headers.get('content-type');
                                        let data;
                                        
                                        if (contentType && contentType.includes('application/json')) {
                                            data = await response.json();
                                        } else {
                                            if (response.redirected || response.status === 200) {
                                                window.location.reload();
                                                return;
                                            }
                                            const text = await response.text();
                                            throw new Error(text || 'حدث خطأ أثناء المعالجة');
                                        }

                                        if (response.ok) {
                                            if (data.redirect) {
                                                window.location.href = data.redirect;
                                            } else {
                                                window.location.reload();
                                            }
                                        } else {
                                            console.error('Error Response Data:', data);
                                            const errorMsg = data.error || data.message || 'حدث خطأ أثناء المعالجة';
                                            const errorDetails = data.file && data.line ? `\n\nالملف: ${data.file}\nالسطر: ${data.line}` : '';
                                            alert(errorMsg + errorDetails);
                                            
                                            if (approveBtn) approveBtn.disabled = false;
                                            if (rejectBtn) {
                                                rejectBtn.disabled = false;
                                                rejectBtn.innerHTML = originalRejectText;
                                            }
                                            isProcessing = false;
                                        }
                                    } catch (error) {
                                        console.error('Error:', error);
                                        let errorMessage = '';
                                        
                                        if (error.name === 'AbortError') {
                                            errorMessage = 'انتهت مهلة الانتظار. يرجى المحاولة مرة أخرى.';
                                        } else if (error.message) {
                                            errorMessage = error.message;
                                        } else {
                                            errorMessage = 'حدث خطأ غير معروف أثناء رفض الطلب';
                                        }
                                        
                                        alert(errorMessage);
                                        
                                        if (approveBtn) approveBtn.disabled = false;
                                        if (rejectBtn) {
                                            rejectBtn.disabled = false;
                                            rejectBtn.innerHTML = originalRejectText;
                                        }
                                        isProcessing = false;
                                    }
                                };
                                
                                console.log('Order approval functions ready:', typeof window.approveOrder, typeof window.rejectOrder);
                            })();
                        </script>
                        <div class="space-y-3">
                            <button type="button" 
                                    id="approveBtn"
                                    onclick="window.approveOrder({{ $order->id }}); return false;"
                                    class="w-full bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 text-white py-3 px-4 rounded-xl font-semibold shadow-md hover:shadow-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-check ml-2"></i>
                                الموافقة على الطلب
                            </button>
                            
                            <button type="button" 
                                    id="rejectBtn"
                                    onclick="window.rejectOrder({{ $order->id }}); return false;"
                                    class="w-full bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-700 hover:to-rose-600 text-white py-3 px-4 rounded-xl font-semibold shadow-md hover:shadow-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-times ml-2"></i>
                                رفض الطلب
                            </button>
                        </div>
                    @elseif($order->status == 'approved')
                        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                            <p class="text-sm text-emerald-800 flex items-start gap-2">
                                <i class="fas fa-check-circle mt-0.5"></i>
                                <span>تمت الموافقة على الطلب وتم تفعيل الكورس للطالب.</span>
                            </p>
                        </div>
                    @else
                        <div class="rounded-lg border border-rose-200 bg-rose-50 p-4">
                            <p class="text-sm text-rose-800 flex items-start gap-2">
                                <i class="fas fa-exclamation-circle mt-0.5"></i>
                                <span>تم رفض هذا الطلب.</span>
                            </p>
                        </div>
                    @endif

                    @if($order->approver)
                        <div class="mt-6 pt-6 border-t border-slate-200">
                            <p class="text-xs font-semibold text-slate-600 mb-3">تمت المراجعة بواسطة:</p>
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-md">
                                    {{ mb_substr(htmlspecialchars($order->approver->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8'), 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900">{{ htmlspecialchars($order->approver->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}</p>
                                    @if($order->approved_at)
                                        <p class="text-xs text-slate-600">{{ $order->approved_at->format('d/m/Y - H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal لعرض الصورة -->
<div id="imageModal" class="fixed inset-0 bg-black/90 backdrop-blur-sm hidden items-center justify-center z-50" onclick="closeImageModal()">
    <div class="max-w-5xl max-h-[90vh] p-4 relative">
        <button onclick="closeImageModal()" class="absolute -top-12 left-0 text-white hover:text-slate-300 text-3xl font-bold transition-colors">
            <i class="fas fa-times-circle"></i>
        </button>
        <img id="modalImage" src="" alt="إيصال الدفع" class="max-w-full max-h-[90vh] object-contain rounded-xl shadow-2xl" onerror="closeImageModal();">
    </div>
</div>

@push('scripts')
<script>
    // الكود الرئيسي موجود في inline script قبل الأزرار
    // هذا القسم فقط للدوال المساعدة الأخرى
    
    function openImageModal(src) {
        // حماية من XSS - التحقق من URL
        if (!src || !src.startsWith('http') && !src.startsWith('/')) {
            return;
        }
        const img = document.getElementById('modalImage');
        if (img) {
            img.src = src;
            document.getElementById('imageModal').classList.remove('hidden');
            document.getElementById('imageModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
            const img = document.getElementById('modalImage');
            if (img) {
                img.src = '';
            }
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
        }
    });

</script>
@endpush
@endsection
