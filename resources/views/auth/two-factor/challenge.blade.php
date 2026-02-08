<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>المصادقة الثنائية - Mindlytics</title>
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
        .input-2fa {
            font-size: 1.5rem;
            letter-spacing: 0.5rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="w-full max-w-md">
        <div class="bg-white/90 backdrop-blur rounded-2xl shadow-xl border-2 border-blue-100 p-6 sm:p-8">
            <div class="text-center mb-6">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-blue-600 to-blue-500 flex items-center justify-center text-white shadow-lg mb-4">
                    <i class="fas fa-shield-alt text-2xl"></i>
                </div>
                <h1 class="text-xl font-black text-gray-900">المصادقة الثنائية</h1>
                <p class="text-sm text-gray-600 mt-2">أدخل الرمز المكون من 6 أرقام من تطبيق Google Authenticator</p>
            </div>

            @if($errors->has('code'))
                <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-medium">
                    {{ $errors->first('code') }}
                </div>
            @endif

            <form action="{{ route('two-factor.verify') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="code" class="block text-sm font-bold text-gray-800 mb-2">رمز التحقق</label>
                    <input type="text"
                           name="code"
                           id="code"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           maxlength="6"
                           autocomplete="one-time-code"
                           autofocus
                           required
                           class="input-2fa w-full px-4 py-4 rounded-xl border-2 border-blue-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                           placeholder="000000"
                           dir="ltr">
                </div>
                <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 text-white font-bold shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-check ml-2"></i>
                    تحقق
                </button>
            </form>

            <p class="text-xs text-gray-500 mt-4 text-center">
                إذا فقدت جهازك، استخدم أحد رموز الاسترداد التي حصلت عليها عند التفعيل.
            </p>

            <a href="{{ route('login') }}" class="block text-center text-sm text-blue-600 hover:text-blue-800 font-medium mt-4">
                <i class="fas fa-arrow-right ml-1"></i>
                العودة لتسجيل الدخول
            </a>
        </div>
    </div>
</body>
</html>
