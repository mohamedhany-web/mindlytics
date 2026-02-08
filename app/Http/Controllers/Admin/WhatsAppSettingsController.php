<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class WhatsAppSettingsController extends Controller
{
    /**
     * عرض صفحة إعدادات واتساب
     */
    public function settings()
    {
        return view('admin.messages.settings');
    }

    /**
     * حفظ إعدادات API
     */
    public function saveApiSettings(Request $request)
    {
        $request->validate([
            'api_url' => 'required|url',
            'api_token' => 'required|string',
            'request_method' => 'required|in:GET,POST',
            'phone_param' => 'required|string',
            'message_param' => 'required|string',
            'extra_params' => 'nullable|json',
        ], [
            'api_url.required' => 'رابط API مطلوب',
            'api_url.url' => 'رابط API غير صالح',
            'api_token.required' => 'API Token مطلوب',
            'extra_params.json' => 'المعاملات الإضافية يجب أن تكون JSON صالح',
        ]);

        // قراءة ملف .env الحالي
        $envPath = base_path('.env');
        $envContent = File::exists($envPath) ? File::get($envPath) : '';

        // تحديث المتغيرات
        $envVars = [
            'WHATSAPP_TYPE' => 'custom',
            'WHATSAPP_API_URL' => $request->api_url,
            'WHATSAPP_API_TOKEN' => $request->api_token,
            'WHATSAPP_REQUEST_METHOD' => $request->request_method,
            'WHATSAPP_PHONE_PARAM' => $request->phone_param,
            'WHATSAPP_MESSAGE_PARAM' => $request->message_param,
            'WHATSAPP_EXTRA_PARAMS' => $request->extra_params ?: '{}',
            'WHATSAPP_ENABLED' => $request->has('enable_service') ? 'true' : 'false',
        ];

        foreach ($envVars as $key => $value) {
            $pattern = "/^{$key}=.*$/m";
            $replacement = "{$key}=\"{$value}\"";
            
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }

        // حفظ ملف .env
        File::put($envPath, $envContent);

        // مسح cache الإعدادات
        \Artisan::call('config:clear');
        \Artisan::call('cache:clear');

        return back()->with('success', 'تم حفظ إعدادات WhatsApp بنجاح! يمكنك الآن اختبار الإرسال.');
    }

    /**
     * اختبار الـ API
     */
    public function testApi(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
            'api_url' => 'required|url',
            'api_token' => 'required|string',
        ]);

        try {
            $phone = $this->formatPhoneNumber($request->phone);
            $apiUrl = $request->api_url;
            $method = $request->request_method ?: 'POST';
            $phoneParam = $request->phone_param ?: 'phone';
            $messageParam = $request->message_param ?: 'message';
            $extraParams = json_decode($request->extra_params ?: '{}', true);

            // إعداد البيانات
            $data = array_merge($extraParams, [
                $phoneParam => $phone,
                $messageParam => $request->message,
            ]);

            // إعداد Headers
            $headers = [
                'Content-Type' => 'application/json',
            ];

            // إضافة Authorization إذا كان Token موجود
            if ($request->api_token) {
                $headers['Authorization'] = 'Bearer ' . $request->api_token;
            }

            // إرسال الطلب
            if ($method === 'POST') {
                $response = Http::withHeaders($headers)
                    ->timeout(30)
                    ->post($apiUrl, $data);
            } else {
                $response = Http::withHeaders($headers)
                    ->timeout(30)
                    ->get($apiUrl, $data);
            }

            $responseData = $response->json();

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم اختبار API بنجاح',
                    'response' => $responseData
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'فشل في الاتصال بـ API: ' . $response->status(),
                    'response' => $responseData
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'خطأ في الاختبار: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * تنسيق رقم الهاتف
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // إزالة المسافات والرموز
        $phone = preg_replace('/[^0-9+]/', '', $phoneNumber);
        
        // إضافة رمز مصر إذا لم يكن موجوداً
        if (!str_starts_with($phone, '+') && !str_starts_with($phone, '20')) {
            if (str_starts_with($phone, '0')) {
                $phone = '20' . substr($phone, 1);
            } else {
                $phone = '20' . $phone;
            }
        }

        // إزالة + إذا كان موجوداً
        $phone = ltrim($phone, '+');

        return $phone;
    }
}
