<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfflineLocation;
use Illuminate\Http\Request;

class OfflineLocationController extends Controller
{
    /**
     * عرض قائمة الأماكن
     */
    public function index(Request $request)
    {
        $query = OfflineLocation::query();

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // فلترة حسب الحالة
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

    /**
     * عرض صفحة إنشاء مكان
     */
    public function create()
    {
        return view('admin.offline-locations.create');
    }

    /**
     * حفظ مكان جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        OfflineLocation::create($validated);

        return redirect()->route('admin.offline-locations.index')
                        ->with('success', 'تم إنشاء المكان بنجاح');
    }

    /**
     * عرض تفاصيل مكان
     */
    public function show(OfflineLocation $offlineLocation)
    {
        $offlineLocation->load('courses.instructor');
        
        $stats = [
            'total_courses' => $offlineLocation->courses()->count(),
            'active_courses' => $offlineLocation->courses()->where('status', 'active')->count(),
        ];

        return view('admin.offline-locations.show', compact('offlineLocation', 'stats'));
    }

    /**
     * عرض صفحة تعديل مكان
     */
    public function edit(OfflineLocation $offlineLocation)
    {
        return view('admin.offline-locations.edit', compact('offlineLocation'));
    }

    /**
     * تحديث مكان
     */
    public function update(Request $request, OfflineLocation $offlineLocation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $offlineLocation->update($validated);

        return redirect()->route('admin.offline-locations.show', $offlineLocation)
                        ->with('success', 'تم تحديث المكان بنجاح');
    }

    /**
     * حذف مكان
     */
    public function destroy(OfflineLocation $offlineLocation)
    {
        // التحقق من عدم وجود كورسات مرتبطة
        if ($offlineLocation->courses()->count() > 0) {
            return back()->withErrors(['error' => 'لا يمكن حذف المكان لأنه مرتبط بكورسات']);
        }

        $offlineLocation->delete();

        return redirect()->route('admin.offline-locations.index')
                        ->with('success', 'تم حذف المكان بنجاح');
    }
}
