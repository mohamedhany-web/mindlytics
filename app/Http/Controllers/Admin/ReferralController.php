<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        $query = Referral::with(['referrer', 'referred', 'referralProgram', 'autoCoupon'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('program_id')) {
            $query->where('referral_program_id', $request->program_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('referrer', function($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                })->orWhereHas('referred', function($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                })->orWhere('referral_code', 'like', "%{$search}%");
            });
        }

        $referrals = $query->paginate(20);

        $stats = [
            'total' => Referral::count(),
            'completed' => Referral::where('status', 'completed')->count(),
            'pending' => Referral::where('status', 'pending')->count(),
            'total_rewards' => Referral::where('status', 'completed')->sum('reward_amount'),
            'total_discounts' => Referral::sum('discount_amount'),
        ];

        $programs = \App\Models\ReferralProgram::all();

        return view('admin.referrals.index', compact('referrals', 'stats', 'programs'));
    }

    public function show(Referral $referral)
    {
        $referral->load(['referrer', 'referred', 'referralProgram', 'autoCoupon', 'invoice']);
        return view('admin.referrals.show', compact('referral'));
    }
}
