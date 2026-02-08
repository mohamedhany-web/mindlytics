<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::where('user_id', auth()->id())
            ->with('course')
            ->where(function($q) {
                $q->where('status', 'issued')->orWhere('is_verified', true);
            })
            ->orderByRaw('COALESCE(issued_at, issue_date) DESC')
            ->paginate(15);

        $stats = [
            'total' => $certificates->total(),
            'issued' => Certificate::where('user_id', auth()->id())
                ->where(function($q) {
                    $q->where('status', 'issued')->orWhere('is_verified', true);
                })->count(),
        ];

        return view('student.certificates.index', compact('certificates', 'stats'));
    }

    public function show(Certificate $certificate)
    {
        if ($certificate->user_id !== auth()->id()) {
            abort(403);
        }

        $certificate->load(['course']);
        return view('student.certificates.show', compact('certificate'));
    }
}
