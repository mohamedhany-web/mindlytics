<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferralProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $programs = ReferralProgram::orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total' => ReferralProgram::count(),
            'active' => ReferralProgram::where('is_active', true)->count(),
            'inactive' => ReferralProgram::where('is_active', false)->count(),
        ];

        return view('admin.referral-programs.index', compact('programs', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.referral-programs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'referrer_reward_type' => 'required|in:percentage,fixed,points',
            'referrer_reward_value' => 'nullable|numeric|min:0',
            'discount_valid_days' => 'required|integer|min:1',
            'referral_code_valid_days' => 'nullable|integer|min:1',
            'max_referrals_per_user' => 'nullable|integer|min:1',
            'max_discount_uses_per_referred' => 'required|integer|min:1',
            'allow_self_referral' => 'boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        ReferralProgram::create($validated);

        return redirect()->route('admin.referral-programs.index')
            ->with('success', 'تم إنشاء برنامج الإحالات بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(ReferralProgram $referralProgram)
    {
        $referralProgram->load(['referrals.referrer', 'referrals.referred']);
        
        $stats = [
            'total_referrals' => $referralProgram->referrals()->count(),
            'completed_referrals' => $referralProgram->referrals()->where('status', 'completed')->count(),
            'pending_referrals' => $referralProgram->referrals()->where('status', 'pending')->count(),
            'total_discount_given' => $referralProgram->referrals()->sum('discount_amount'),
            'total_rewards_given' => $referralProgram->referrals()->where('status', 'completed')->sum('reward_amount'),
        ];

        return view('admin.referral-programs.show', compact('referralProgram', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReferralProgram $referralProgram)
    {
        return view('admin.referral-programs.edit', compact('referralProgram'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReferralProgram $referralProgram)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'referrer_reward_type' => 'required|in:percentage,fixed,points',
            'referrer_reward_value' => 'nullable|numeric|min:0',
            'discount_valid_days' => 'required|integer|min:1',
            'referral_code_valid_days' => 'nullable|integer|min:1',
            'max_referrals_per_user' => 'nullable|integer|min:1',
            'max_discount_uses_per_referred' => 'required|integer|min:1',
            'allow_self_referral' => 'boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        $referralProgram->update($validated);

        return redirect()->route('admin.referral-programs.index')
            ->with('success', 'تم تحديث برنامج الإحالات بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReferralProgram $referralProgram)
    {
        if ($referralProgram->referrals()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف برنامج الإحالات لأنه يحتوي على إحالات مرتبطة');
        }

        $referralProgram->delete();

        return redirect()->route('admin.referral-programs.index')
            ->with('success', 'تم حذف برنامج الإحالات بنجاح');
    }
}