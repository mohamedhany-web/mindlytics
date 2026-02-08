<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyProgram;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    public function index()
    {
        $programs = LoyaltyProgram::withCount('users')
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total' => LoyaltyProgram::count(),
            'active' => LoyaltyProgram::where('is_active', true)->count(),
        ];

        return view('admin.loyalty.index', compact('programs', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'points_per_purchase' => 'required|numeric|min:0',
            'points_per_referral' => 'nullable|numeric|min:0',
            'redemption_rules' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        LoyaltyProgram::create($validated);

        return redirect()->route('admin.loyalty.index')
            ->with('success', 'تم إنشاء برنامج الولاء بنجاح');
    }

    public function show(LoyaltyProgram $loyaltyProgram)
    {
        // تحميل المستخدمين إن أمكن
        try {
            $loyaltyProgram->load(['users']);
        } catch (\Exception $e) {
            // في حال عدم وجود علاقة users
        }
        return view('admin.loyalty.show', compact('loyaltyProgram'));
    }

    public function update(Request $request, LoyaltyProgram $loyaltyProgram)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'points_per_purchase' => 'required|numeric|min:0',
            'points_per_referral' => 'nullable|numeric|min:0',
            'redemption_rules' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $loyaltyProgram->update($validated);

        return redirect()->route('admin.loyalty.index')
            ->with('success', 'تم تحديث برنامج الولاء بنجاح');
    }
}
