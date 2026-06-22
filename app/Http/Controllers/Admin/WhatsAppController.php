<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppMessage;
use App\Services\WhatsAppBridgeService;
use App\Services\WhatsAppService;
use App\Support\WhatsAppBridgeSettings;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    public function __construct(
        private WhatsAppBridgeService $bridge,
        private WhatsAppService $whatsapp
    ) {}

    public function index()
    {
        $settings = WhatsAppBridgeSettings::all();
        $statusResult = $this->bridge->getStatus();
        $status = $statusResult['success'] ? ($statusResult['data'] ?? []) : [];
        $bridgeError = $statusResult['success'] ? null : ($statusResult['error'] ?? null);

        $stats = [
            'total' => WhatsAppMessage::count(),
            'sent_today' => WhatsAppMessage::where('status', 'sent')->whereDate('created_at', today())->count(),
            'failed' => WhatsAppMessage::where('status', 'failed')->count(),
        ];

        return view('admin.whatsapp.index', compact('settings', 'status', 'bridgeError', 'stats'));
    }

    public function sendForm()
    {
        return view('admin.whatsapp.send');
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:30',
            'message' => 'required|string|max:4096',
        ], [
            'phone.required' => 'رقم الهاتف مطلوب',
            'message.required' => 'نص الرسالة مطلوب',
        ]);

        $result = $this->whatsapp->sendMessage($request->phone, $request->message);

        if ($result['success'] ?? false) {
            return redirect()
                ->route('admin.whatsapp.messages')
                ->with('success', 'تم إرسال الرسالة بنجاح.');
        }

        return back()
            ->withInput()
            ->with('error', $result['error'] ?? 'فشل إرسال الرسالة.');
    }

    public function messages(Request $request)
    {
        $messages = WhatsAppMessage::with('user')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, function ($q, $search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('phone_number', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.whatsapp.messages', compact('messages'));
    }

    public function settings()
    {
        return view('admin.whatsapp.settings', [
            'settings' => WhatsAppBridgeSettings::all(),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'service_type' => 'required|in:disabled,wwebjs,local,official,custom',
            'bridge_url' => 'nullable|url|max:500',
            'bridge_token' => 'nullable|string|max:500',
        ], [
            'bridge_url.url' => 'رابط الجسر غير صالح',
        ]);

        if (in_array($request->service_type, ['wwebjs', 'local'], true)) {
            $request->validate([
                'bridge_url' => 'required|url',
                'bridge_token' => 'required|string|min:8',
            ], [
                'bridge_url.required' => 'رابط سيرفر Node.js Bridge مطلوب',
                'bridge_token.required' => 'توكن الأمان مطلوب',
                'bridge_token.min' => 'التوكن يجب أن يكون 8 أحرف على الأقل',
            ]);
        }

        WhatsAppBridgeSettings::save([
            'service_type' => $request->service_type,
            'bridge_url' => $request->bridge_url ?? '',
            'bridge_token' => $request->bridge_token ?? '',
        ]);

        return back()->with('success', 'تم حفظ إعدادات الواتساب.');
    }

    public function statusJson()
    {
        return response()->json($this->bridge->getStatus());
    }

    public function qrJson()
    {
        return response()->json($this->bridge->getQr());
    }

    public function startBridge()
    {
        $result = $this->bridge->start();

        if ($result['success'] ?? false) {
            return back()->with('success', 'تم بدء تهيئة الاتصال — امسح QR خلال ثوانٍ.');
        }

        return back()->with('error', $result['error'] ?? 'فشل بدء الجسر.');
    }

    public function logoutBridge()
    {
        $result = $this->bridge->logout();

        if ($result['success'] ?? false) {
            return back()->with('success', 'تم قطع اتصال الواتساب.');
        }

        return back()->with('error', $result['error'] ?? 'فشل قطع الاتصال.');
    }
}
