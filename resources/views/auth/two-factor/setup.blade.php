<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تفعيل المصادقة الثنائية - Mindlytics</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Cairo', sans-serif; }
        body {
            min-height: 100vh;
            background: linear-gradient(to bottom, #f0f9ff, #e0f2fe, #fff);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
    </style>
</head>
<body>
    <div class="w-full max-w-lg">
        <div class="bg-white/90 backdrop-blur rounded-2xl shadow-xl border-2 border-blue-100 p-6 sm:p-8">
            @if(session('warning'))
                <div class="mb-6 p-4 rounded-xl bg-amber-50 border-2 border-amber-200 text-amber-800 text-sm font-medium flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-amber-600"></i>
                    {{ session('warning') }}
                </div>
            @endif
            <div class="text-center mb-6">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-blue-600 to-blue-500 flex items-center justify-center text-white shadow-lg mb-4">
                    <i class="fas fa-mobile-alt text-2xl"></i>
                </div>
                <h1 class="text-xl font-black text-gray-900">تفعيل المصادقة الثنائية</h1>
                <p class="text-sm text-gray-600 mt-2">المصادقة الثنائية إلزامية للدخول. امسح رمز QR بتطبيق Google Authenticator ثم أدخل الرمز للتفعيل.</p>
            </div>

            <div class="flex justify-center my-6">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}" alt="QR Code" class="rounded-xl border-2 border-gray-200">
            </div>

            <p class="text-xs text-gray-600 text-center mb-4">
                أو أدخل المفتاح يدوياً: <code class="bg-gray-100 px-2 py-1 rounded font-mono text-xs" dir="ltr">{{ $secret }}</code>
            </p>

            @if($errors->has('code'))
                <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-medium">
                    {{ $errors->first('code') }}
                </div>
            @endif

            <form action="{{ route('two-factor.enable') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="code" class="block text-sm font-bold text-gray-800 mb-2">رمز التحقق (6 أرقام)</label>
                    <input type="text"
                           name="code"
                           id="code"
                           inputmode="numeric"
                           maxlength="6"
                           required
                           class="w-full px-4 py-3 rounded-xl border-2 border-blue-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none text-center text-lg tracking-widest font-mono"
                           placeholder="000000"
                           dir="ltr">
                </div>
                <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 text-white font-bold shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-check ml-2"></i>
                    تفعيل المصادقة الثنائية
                </button>
            </form>

            <a href="{{ route(auth()->user()->isEmployee() ? 'employee.dashboard' : (auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin' ? 'admin.dashboard' : 'instructor.courses.index')) }}" class="block text-center text-sm text-gray-500 hover:text-gray-700 mt-4">
                <i class="fas fa-arrow-right ml-1"></i>
                إلغاء والعودة
            </a>
        </div>
    </div>
</body>
</html>
