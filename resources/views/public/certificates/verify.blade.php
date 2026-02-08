<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التحقق من الشهادة - Mindlytics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('components.certificate-styles')
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-12 px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-black text-gray-900 mb-2">التحقق من الشهادة</h1>
                <p class="text-gray-600">أدخل رمز التحقق أو السيريال للتحقق من صحة الشهادة</p>
            </div>

            <!-- Verification Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                <form method="GET" action="{{ route('public.certificates.verify') }}" class="flex gap-4">
                    <input type="text" 
                           name="code" 
                           value="{{ request('code') }}"
                           placeholder="أدخل رمز التحقق أو السيريال" 
                           class="flex-1 px-6 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg">
                    <button type="submit" 
                            class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-8 py-4 rounded-xl font-semibold transition-all duration-300 shadow-lg shadow-blue-500/30 hover:shadow-xl">
                        <i class="fas fa-search ml-2"></i>
                        التحقق
                    </button>
                </form>
            </div>

            <!-- Results -->
            @if(isset($certificate))
                @if($certificate && $isValid)
                <!-- Valid Certificate -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-green-500">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 text-white">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-3xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-black">شهادة صحيحة ومعتمدة</h2>
                                <p class="text-green-100">تم التحقق من صحة هذه الشهادة</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-4">معلومات الطالب</h3>
                                <div class="space-y-2 text-sm">
                                    <div><span class="text-gray-600">الاسم:</span> <span class="font-semibold text-gray-900">{{ $certificate->user->name ?? 'غير معروف' }}</span></div>
                                    <div><span class="text-gray-600">البريد:</span> <span class="font-semibold text-gray-900">{{ $certificate->user->email ?? '-' }}</span></div>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-4">معلومات الشهادة</h3>
                                <div class="space-y-2 text-sm">
                                    <div><span class="text-gray-600">رقم الشهادة:</span> <span class="font-semibold text-gray-900 font-mono">{{ $certificate->certificate_number }}</span></div>
                                    @if($certificate->serial_number)
                                    <div><span class="text-gray-600">السيريال:</span> <span class="font-semibold text-gray-900 font-mono">{{ $certificate->serial_number }}</span></div>
                                    @endif
                                    <div><span class="text-gray-600">تاريخ الإصدار:</span> <span class="font-semibold text-gray-900">{{ $certificate->issued_at ? $certificate->issued_at->format('Y-m-d') : '-' }}</span></div>
                                    <div><span class="text-gray-600">الحالة:</span> 
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            مُصدرة ومعتمدة
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Certificate Preview -->
                        <div class="border-t border-gray-200 pt-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">معاينة الشهادة</h3>
                            <div class="certificate-container">
                                @include('components.certificate-templates', [
                                    'certificate' => $certificate,
                                    'template' => $certificate->template ?? 'classic'
                                ])
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <!-- Invalid Certificate -->
                <div class="bg-white rounded-2xl shadow-xl p-8 border-2 border-red-500">
                    <div class="text-center">
                        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-times-circle text-red-600 text-4xl"></i>
                        </div>
                        <h2 class="text-2xl font-black text-gray-900 mb-2">شهادة غير صحيحة</h2>
                        <p class="text-red-600 font-semibold">{{ $error ?? 'تم اكتشاف تلاعب في الشهادة أو الشهادة غير موجودة' }}</p>
                    </div>
                </div>
                @endif
            @elseif(isset($error))
            <!-- Error Message -->
            <div class="bg-white rounded-2xl shadow-xl p-8 border-2 border-yellow-500">
                <div class="text-center">
                    <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-4xl"></i>
                    </div>
                    <h2 class="text-2xl font-black text-gray-900 mb-2">تنبيه</h2>
                    <p class="text-yellow-600 font-semibold">{{ $error }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
