<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateVerificationController extends Controller
{
    public function verify(Request $request, $code = null)
    {
        $verificationCode = trim((string) ($code ?? $request->input('code', '')));

        if ($verificationCode === '') {
            return view('public.certificates.verify', [
                'certificate' => null,
                'error' => 'الرجاء إدخال الرقم التسلسلي أو رمز التحقق',
            ]);
        }

        $certificate = Certificate::query()
            ->where(function ($q) use ($verificationCode) {
                $q->where('serial_number', $verificationCode)
                    ->orWhere('verification_code', $verificationCode)
                    ->orWhere('certificate_number', $verificationCode);
            })
            ->with(['user', 'course.instructor', 'instructor'])
            ->first();

        if (! $certificate) {
            return view('public.certificates.verify', [
                'certificate' => null,
                'error' => 'الشهادة غير موجودة — تأكد من الرقم التسلسلي',
            ]);
        }

        $isValid = true;
        if ($certificate->status === 'revoked') {
            $isValid = false;
        } elseif ($certificate->certificate_hash) {
            $isValid = $certificate->verifyHash();
        }

        return view('public.certificates.verify', [
            'certificate' => $certificate,
            'branding' => \App\Models\CertificateBranding::current(),
            'isValid' => $isValid,
            'error' => $isValid ? null : ($certificate->status === 'revoked'
                ? 'هذه الشهادة ملغاة'
                : 'تم اكتشاف تلاعب في بيانات الشهادة'),
        ]);
    }
}
