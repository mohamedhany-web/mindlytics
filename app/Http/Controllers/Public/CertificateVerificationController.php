<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateVerificationController extends Controller
{
    public function verify(Request $request, $code = null)
    {
        $verificationCode = $code ?? $request->input('code');
        
        if (!$verificationCode) {
            return view('public.certificates.verify', [
                'certificate' => null,
                'error' => 'الرجاء إدخال رمز التحقق'
            ]);
        }

        $certificate = Certificate::where('verification_code', $verificationCode)
            ->orWhere('serial_number', $verificationCode)
            ->with(['user', 'course', 'instructor'])
            ->first();

        if (!$certificate) {
            return view('public.certificates.verify', [
                'certificate' => null,
                'error' => 'الشهادة غير موجودة أو رمز التحقق غير صحيح'
            ]);
        }

        // Verify hash if exists
        $isValid = true;
        if ($certificate->certificate_hash) {
            $isValid = $certificate->verifyHash();
        }

        return view('public.certificates.verify', [
            'certificate' => $certificate,
            'isValid' => $isValid,
            'error' => $isValid ? null : 'تم اكتشاف تلاعب في الشهادة'
        ]);
    }
}
