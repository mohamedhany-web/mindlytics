<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::withCount('usages')
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true)
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>=', now());
                    });
            } elseif ($request->status === 'expired') {
                $query->where(function($q) {
                    $q->where('is_active', false)
                      ->orWhere('expires_at', '<', now());
                });
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        $coupons = $query->paginate(20);

        $stats = [
            'total' => Coupon::count(),
            'active' => Coupon::where('is_active', true)->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })->count(),
            'expired' => Coupon::where(function($q) {
                $q->where('is_active', false)->orWhere('expires_at', '<', now());
            })->count(),
        ];

        return view('admin.coupons.index', compact('coupons', 'stats'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'is_active' => 'boolean',
        ]);

        Coupon::create([
            'code' => strtoupper($validated['code']),
            'name' => $validated['title'],
            'title' => $validated['title'], // إضافة title إذا كان موجوداً في الجدول
            'description' => $validated['description'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'minimum_amount' => $validated['minimum_amount'] ?? null,
            'usage_limit' => $validated['max_uses'] ?? null,
            'starts_at' => $validated['valid_from'] ?? null,
            'expires_at' => $validated['valid_until'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'تم إنشاء الكوبون بنجاح');
    }

    public function show(Coupon $coupon)
    {
        $coupon->load(['usages.user']);
        return view('admin.coupons.show', compact('coupon'));
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code,' . $coupon->id,
            'title' => 'required|string',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'is_active' => 'boolean',
        ]);

        $coupon->update([
            'code' => strtoupper($validated['code']),
            'name' => $validated['title'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'minimum_amount' => $validated['minimum_amount'] ?? null,
            'usage_limit' => $validated['max_uses'] ?? null,
            'starts_at' => $validated['valid_from'] ?? null,
            'expires_at' => $validated['valid_until'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'تم تحديث الكوبون بنجاح');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')
            ->with('success', 'تم حذف الكوبون بنجاح');
    }
}
