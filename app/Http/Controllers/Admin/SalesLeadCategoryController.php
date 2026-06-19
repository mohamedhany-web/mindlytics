<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesLeadCategory;
use Illuminate\Http\Request;

class SalesLeadCategoryController extends Controller
{
    public function index()
    {
        $categories = SalesLeadCategory::query()
            ->withCount('leads')
            ->ordered()
            ->get();

        $stats = [
            'total' => $categories->count(),
            'active' => $categories->where('is_active', true)->count(),
            'leads_total' => (int) $categories->sum('leads_count'),
            'empty_categories' => $categories->where('leads_count', 0)->count(),
        ];

        return view('admin.sales.categories.index', compact('categories', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120|unique:sales_lead_categories,name',
            'color' => 'nullable|string|max:16',
            'description' => 'nullable|string|max:2000',
            'sort_order' => 'nullable|integer|min:0|max:9999',
        ]);

        SalesLeadCategory::create([
            'name' => $validated['name'],
            'slug' => SalesLeadCategory::generateSlug($validated['name']),
            'color' => $validated['color'] ?? '#059669',
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', 'تم إنشاء التصنيف.');
    }

    public function update(Request $request, SalesLeadCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120|unique:sales_lead_categories,name,'.$category->id,
            'color' => 'nullable|string|max:16',
            'description' => 'nullable|string|max:2000',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'boolean',
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => SalesLeadCategory::generateSlug($validated['name'], $category->id),
            'color' => $validated['color'] ?? $category->color,
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? $category->sort_order,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'تم تحديث التصنيف.');
    }

    public function destroy(SalesLeadCategory $category)
    {
        if ($category->leads()->exists()) {
            return back()->with('error', 'لا يمكن حذف تصنيف مرتبط بعملاء. عطّله بدلاً من ذلك.');
        }

        $category->delete();

        return back()->with('success', 'تم حذف التصنيف.');
    }
}
