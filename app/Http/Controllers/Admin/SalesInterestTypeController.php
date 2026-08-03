<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesInterestType;
use Illuminate\Http\Request;

class SalesInterestTypeController extends Controller
{
    public function index()
    {
        $types = SalesInterestType::query()
            ->withCount(['leads', 'specialists'])
            ->ordered()
            ->get();

        $stats = [
            'total' => $types->count(),
            'active' => $types->where('is_active', true)->count(),
            'leads_total' => (int) $types->sum('leads_count'),
            'specialists' => (int) $types->sum('specialists_count'),
        ];

        return view('admin.sales.interest-types.index', compact('types', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:120',
            'name_en' => 'nullable|string|max:120',
            'color' => 'nullable|string|max:16',
            'sort_order' => 'nullable|integer|min:0|max:9999',
        ]);

        SalesInterestType::create([
            'name_ar' => $validated['name_ar'],
            'name_en' => $validated['name_en'] ?? null,
            'slug' => SalesInterestType::generateSlug($validated['name_ar']),
            'color' => $validated['color'] ?? '#059669',
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', 'تم إنشاء نوع الاهتمام.');
    }

    public function update(Request $request, SalesInterestType $interestType)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:120',
            'name_en' => 'nullable|string|max:120',
            'color' => 'nullable|string|max:16',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'boolean',
        ]);

        $interestType->update([
            'name_ar' => $validated['name_ar'],
            'name_en' => $validated['name_en'] ?? null,
            'slug' => SalesInterestType::generateSlug($validated['name_ar'], $interestType->id),
            'color' => $validated['color'] ?? $interestType->color,
            'sort_order' => $validated['sort_order'] ?? $interestType->sort_order,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'تم تحديث نوع الاهتمام.');
    }

    public function destroy(SalesInterestType $interestType)
    {
        if ($interestType->leads()->exists()) {
            return back()->with('error', 'لا يمكن حذف نوع مرتبط بعملاء. عطّله بدلاً من ذلك.');
        }

        $interestType->delete();

        return back()->with('success', 'تم حذف نوع الاهتمام.');
    }
}
