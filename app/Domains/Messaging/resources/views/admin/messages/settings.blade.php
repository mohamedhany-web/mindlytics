@extends('layouts.admin')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('إعداد واتساب API') }}</h1>
        <p class="text-gray-600">{{ __('أدخل بيانات الـ API الخاص بك وسيتم ربطه تلقائياً') }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fab fa-whatsapp text-green-500 ml-2"></i>
                {{ __('ربط WhatsApp API') }}
            </h3>
        </div>

        <form action="{{ route('admin.messages.save-api-settings') }}" method="POST" class="p-6">
            @csrf
            
            <div class="space-y-6">
                <!-- رابط API -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('رابط API') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="url" name="api_url" required
                           value="{{ old('api_url', config('services.whatsapp.api_url')) }}"
                           placeholder="https://api.whatsapp.com/send"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <p class="mt-1 text-xs text-gray-500">{{ __('مثال: https://your-api.com/whatsapp/send') }}</p>
                </div>

                <!-- API Token -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('API Token') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="api_token" required
                           value="{{ old('api_token', config('services.whatsapp.api_token')) }}"
                           placeholder="أدخل الـ Token الخاص بك"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- طريقة الإرسال -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('طريقة إرسال البيانات') }}
                    </label>
                    <select name="request_method" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="POST" {{ old('request_method', 'POST') === 'POST' ? 'selected' : '' }}>POST</option>
                        <option value="GET" {{ old('request_method') === 'GET' ? 'selected' : '' }}>GET</option>
                    </select>
                </div>

                <!-- أسماء المعاملات -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('اسم معامل رقم الهاتف') }}
                        </label>
                        <input type="text" name="phone_param" 
                               value="{{ old('phone_param', 'phone') }}"
                               placeholder="phone"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <p class="mt-1 text-xs text-gray-500">{{ __('مثال: phone, number, to') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('اسم معامل الرسالة') }}
                        </label>
                        <input type="text" name="message_param" 
                               value="{{ old('message_param', 'message') }}"
                               placeholder="message"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <p class="mt-1 text-xs text-gray-500">{{ __('مثال: message, text, msg') }}</p>
                    </div>
                </div>

                <!-- معاملات إضافية -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('معاملات إضافية (JSON)') }}
                    </label>
                    <textarea name="extra_params" rows="4"
                              placeholder='{"instance": "your_instance", "accessToken": "your_token"}'
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-mono text-sm">{{ old('extra_params', '{}') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">{{ __('معاملات إضافية مطلوبة من الـ API') }}</p>
                </div>

                <!-- تفعيل الخدمة -->
                <div class="flex items-center">
                    <input type="checkbox" name="enable_service" value="1" 
                           {{ old('enable_service', config('services.whatsapp.enabled')) ? 'checked' : '' }}
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label class="mr-2 text-sm text-gray-900">
                        {{ __('تفعيل إرسال الرسائل') }}
                    </label>
                </div>
            </div>

            <!-- اختبار الـ API -->
            <div class="mt-8 p-6 bg-gray-50 rounded-lg">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-vial ml-2"></i>
                    {{ __('اختبار الـ API') }}
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('رقم الاختبار') }}
                        </label>
                        <input type="tel" id="test_phone" 
                               placeholder="01234567890"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('رسالة الاختبار') }}
                        </label>
                        <input type="text" id="test_message" 
                               value="رسالة اختبار من منصة Mindlytics 🎓"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>
                
                <button type="button" onclick="testAPI()" 
                        class="mt-4 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    <i class="fas fa-paper-plane ml-2"></i>
                    {{ __('اختبار الإرسال') }}
                </button>
                
                <div id="test-result" class="mt-4 hidden">
                    <!-- نتيجة الاختبار ستظهر هنا -->
                </div>
            </div>

            <!-- أزرار الحفظ -->
            <div class="mt-8 flex justify-end space-x-2 space-x-reverse">
                <a href="{{ route('admin.messages.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    {{ __('إلغاء') }}
                </a>
                <button type="submit" 
                        class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    <i class="fas fa-save ml-2"></i>
                    {{ __('حفظ الإعدادات') }}
                </button>
            </div>
        </form>
    </div>

    <!-- أمثلة على APIs مجانية -->
    <div class="mt-8 bg-blue-50 rounded-xl p-6 border border-blue-200">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">
            <i class="fas fa-lightbulb ml-2"></i>
            {{ __('أمثلة على APIs مجانية') }}
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
            <div>
                <h4 class="font-medium text-blue-800 mb-2">WhatsApp Web API</h4>
                <div class="bg-white p-3 rounded border space-y-1">
                    <div><strong>URL:</strong> http://localhost:3001/send-message</div>
                    <div><strong>Method:</strong> POST</div>
                    <div><strong>Phone Param:</strong> phone</div>
                    <div><strong>Message Param:</strong> message</div>
                </div>
            </div>
            
            <div>
                <h4 class="font-medium text-blue-800 mb-2">CallMeBot API</h4>
                <div class="bg-white p-3 rounded border space-y-1">
                    <div><strong>URL:</strong> https://api.callmebot.com/whatsapp.php</div>
                    <div><strong>Method:</strong> GET</div>
                    <div><strong>Phone Param:</strong> phone</div>
                    <div><strong>Message Param:</strong> text</div>
                    <div><strong>Extra:</strong> {"apikey": "YOUR_API_KEY"}</div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
async function testAPI() {
    const phone = document.getElementById('test_phone').value;
    const message = document.getElementById('test_message').value;
    const resultDiv = document.getElementById('test-result');
    
    if (!phone || !message) {
        showResult('يرجى إدخال رقم الهاتف والرسالة', 'error');
        return;
    }
    
    // إظهار حالة التحميل
    showResult('جاري الاختبار...', 'loading');
    
    try {
        const response = await fetch('{{ route("admin.messages.test-api") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                phone: phone,
                message: message,
                // جمع بيانات النموذج للاختبار
                api_url: document.querySelector('input[name="api_url"]').value,
                api_token: document.querySelector('input[name="api_token"]').value,
                request_method: document.querySelector('select[name="request_method"]').value,
                phone_param: document.querySelector('input[name="phone_param"]').value,
                message_param: document.querySelector('input[name="message_param"]').value,
                extra_params: document.querySelector('textarea[name="extra_params"]').value
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showResult('✅ تم إرسال الرسالة بنجاح!', 'success');
        } else {
            showResult('❌ فشل الإرسال: ' + (result.error || 'خطأ غير معروف'), 'error');
        }
        
    } catch (error) {
        showResult('❌ خطأ في الاتصال: ' + error.message, 'error');
    }
}

function showResult(message, type) {
    const resultDiv = document.getElementById('test-result');
    const bgColor = {
        'success': 'bg-green-50 border-green-200 text-green-800']
        'error': 'bg-red-50 border-red-200 text-red-800']
        'loading': 'bg-blue-50 border-blue-200 text-blue-800
    };
    
    resultDiv.className = `mt-4 p-4 rounded-lg border ${bgColor[type] || bgColor.error}`;
    resultDiv.textContent = message;']
    resultDiv.classList.remove('hidden');
}

// عرض/إخفاء كلمة المرور
function togglePassword() {
    const input = document.querySelector('input[name="api_token"]');
    const icon = document.querySelector('#toggle-password-icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}
</script>
@endpush
@endsection
