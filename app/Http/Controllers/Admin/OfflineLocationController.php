<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfflineLocation;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OfflineLocationController extends Controller
{
    public function index(Request $request)
    {
        $query = OfflineLocation::query()->with(['defaultWallet', 'manager']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $locations = $query->latest()->paginate(20);

        $stats = [
            'total' => OfflineLocation::count(),
            'active' => OfflineLocation::where('is_active', true)->count(),
        ];

        return view('admin.offline-locations.index', compact('locations', 'stats'));
    }

    public function create()
    {
        $wallets = Wallet::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.offline-locations.create', compact('wallets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->locationRules());

        OfflineLocation::create($validated);

        return redirect()->route('admin.offline-locations.index')
                        ->with('success', 'تم إنشاء المكان بنجاح');
    }

    public function show(OfflineLocation $offlineLocation)
    {
        $offlineLocation->load(['courses.instructor', 'defaultWallet', 'manager', 'monthlySettlements' => fn ($q) => $q->latest('period_month')->limit(6)]);

        $placeManagers = User::query()
            ->where('offline_location_id', $offlineLocation->id)
            ->where('role', 'place_manager')
            ->orderBy('name')
            ->get();

        $stats = [
            'total_courses' => $offlineLocation->courses()->count(),
            'active_courses' => $offlineLocation->courses()->where('status', 'active')->count(),
        ];

        return view('admin.offline-locations.show', compact('offlineLocation', 'stats', 'placeManagers'));
    }

    public function edit(OfflineLocation $offlineLocation)
    {
        $wallets = Wallet::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.offline-locations.edit', compact('offlineLocation', 'wallets'));
    }

    public function update(Request $request, OfflineLocation $offlineLocation)
    {
        $validated = $request->validate($this->locationRules());

        $offlineLocation->update($validated);

        return redirect()->route('admin.offline-locations.show', $offlineLocation)
                        ->with('success', 'تم تحديث المكان بنجاح');
    }

    public function destroy(OfflineLocation $offlineLocation)
    {
        if ($offlineLocation->courses()->count() > 0) {
            return back()->withErrors(['error' => 'لا يمكن حذف المكان لأنه مرتبط بكورسات']);
        }

        $offlineLocation->delete();

        return redirect()->route('admin.offline-locations.index')
                        ->with('success', 'تم حذف المكان بنجاح');
    }

    public function storePlaceManager(Request $request, OfflineLocation $offlineLocation): RedirectResponse
    {
        if ($offlineLocation->manager_user_id) {
            return back()->with('error', 'يوجد مدير مكان مرتبط بالفعل. يمكنك تعديل بياناته من قائمة المستخدمين.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|string|max:30|unique:users,phone',
            'password' => 'required|string|min:8|max:255',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'اسم مدير المكان مطلوب.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.unique' => 'هذا البريد مستخدم لمستخدم آخر.',
            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.unique' => 'رقم الهاتف مستخدم لمستخدم آخر.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.min' => 'كلمة المرور يجب ألا تقل عن 8 أحرف.',
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => $validated['password'],
            'address' => $validated['address'] ?? null,
            'role' => 'place_manager',
            'offline_location_id' => $offlineLocation->id,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $offlineLocation->update(['manager_user_id' => $user->id]);

        return redirect()
            ->route('admin.offline-locations.show', $offlineLocation)
            ->with('success', 'تم إنشاء حساب مدير المكان.')
            ->with('generated_place_manager_password', $validated['password'])
            ->with('generated_place_manager_email', $validated['email'])
            ->with('generated_place_manager_phone', $validated['phone']);
    }

    /**
     * @return array<string, mixed>
     */
    private function locationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'hourly_rate' => 'nullable|numeric|min:0',
            'default_wallet_id' => 'nullable|exists:wallets,id',
            'vendor_contact_name' => 'nullable|string|max:255',
            'vendor_tax_id' => 'nullable|string|max:64',
            'vendor_bank_details' => 'nullable|string|max:2000',
        ];
    }
}
