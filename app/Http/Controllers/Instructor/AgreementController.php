<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\InstructorAgreement;
use App\Models\AgreementPayment;
use Illuminate\Http\Request;

class AgreementController extends Controller
{
    public function index()
    {
        $instructor = auth()->user();
        
        $agreements = InstructorAgreement::where('instructor_id', $instructor->id)
            ->with(['payments.course', 'payments.lecture'])
            ->orderBy('created_at', 'desc')
            ->get();

        $activeAgreement = $agreements->where('status', InstructorAgreement::STATUS_ACTIVE)->first();
        
        $stats = [
            'total_earned' => AgreementPayment::where('instructor_id', $instructor->id)
                ->where('status', AgreementPayment::STATUS_PAID)
                ->sum('amount'),
            'pending_amount' => AgreementPayment::where('instructor_id', $instructor->id)
                ->where('status', AgreementPayment::STATUS_APPROVED)
                ->sum('amount'),
            'total_payments' => AgreementPayment::where('instructor_id', $instructor->id)->count(),
        ];

        return view('instructor.agreements.index', compact('agreements', 'activeAgreement', 'stats'));
    }

    public function show(InstructorAgreement $agreement)
    {
        if ($agreement->instructor_id !== auth()->id()) {
            abort(403);
        }

        $agreement->load(['payments.course', 'payments.lecture', 'instructor']);
        
        $stats = [
            'total_earned' => $agreement->paidPayments()->sum('amount'),
            'pending_amount' => $agreement->approvedPayments()->sum('amount'),
            'total_payments' => $agreement->payments()->count(),
            'paid_payments' => $agreement->paidPayments()->count(),
        ];

        return view('instructor.agreements.show', compact('agreement', 'stats'));
    }
}
