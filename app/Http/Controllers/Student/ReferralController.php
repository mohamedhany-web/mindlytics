<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Services\ReferralService;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    protected $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * عرض صفحة الإحالات للمستخدم
     */
    public function index()
    {
        $user = auth()->user();
        
        // الحصول على كود الإحالة
        $referralCode = $this->referralService->getUserReferralCode($user);
        
        // الإحالات الخاصة بالمستخدم (كمحيل)
        $referrals = Referral::where('referrer_id', $user->id)
            ->with(['referred', 'referralProgram', 'autoCoupon'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // الإحصائيات
        $stats = [
            'total_referrals' => $user->total_referrals ?? 0,
            'completed_referrals' => $user->completed_referrals ?? 0,
            'pending_referrals' => Referral::where('referrer_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'total_rewards' => Referral::where('referrer_id', $user->id)
                ->where('status', 'completed')
                ->sum('reward_amount'),
            'total_discount_given' => Referral::where('referrer_id', $user->id)
                ->sum('discount_amount'),
        ];

        // رابط الإحالة
        $referralLink = url('/register?ref=' . $referralCode);

        return view('student.referrals.index', compact('referralCode', 'referralLink', 'referrals', 'stats'));
    }

    /**
     * نسخ رابط الإحالة
     */
    public function copyLink(Request $request)
    {
        $user = auth()->user();
        $referralCode = $this->referralService->getUserReferralCode($user);
        $referralLink = url('/register?ref=' . $referralCode);

        return response()->json([
            'success' => true,
            'link' => $referralLink,
            'code' => $referralCode,
        ]);
    }
}