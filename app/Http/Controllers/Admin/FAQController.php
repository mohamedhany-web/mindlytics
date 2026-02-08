<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FAQ;
use Illuminate\Http\Request;

class FAQController extends Controller
{
    public function index(Request $request)
    {
        $query = FAQ::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('question', 'like', '%' . $request->search . '%')
                  ->orWhere('answer', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $faqs = $query->orderBy('order')->orderBy('created_at', 'desc')->paginate(20);
        $categories = FAQ::distinct()->pluck('category')->filter()->values();

        return view('admin.faq.index', compact('faqs', 'categories'));
    }

    public function create()
    {
        $categories = FAQ::distinct()->pluck('category')->filter()->values();
        return view('admin.faq.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:2000',
            'category' => 'nullable|string|max:100',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        FAQ::create($validated);

        return redirect()->route('admin.faq.index')
            ->with('success', 'تم إنشاء السؤال بنجاح');
    }

    public function show(FAQ $faq)
    {
        return view('admin.faq.show', compact('faq'));
    }

    public function edit(FAQ $faq)
    {
        $categories = FAQ::distinct()->pluck('category')->filter()->values();
        return view('admin.faq.edit', compact('faq', 'categories'));
    }

    public function update(Request $request, FAQ $faq)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:2000',
            'category' => 'nullable|string|max:100',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $faq->update($validated);

        return redirect()->route('admin.faq.index')
            ->with('success', 'تم تحديث السؤال بنجاح');
    }

    public function destroy(FAQ $faq)
    {
        $faq->delete();

        return redirect()->route('admin.faq.index')
            ->with('success', 'تم حذف السؤال بنجاح');
    }
}



