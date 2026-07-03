<?php

namespace App\Http\Controllers\Admin\Investment;

use App\Http\Controllers\Controller;
use App\Models\InvestmentInquiry;
use App\Models\InvestmentPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InquiryController extends Controller
{
    public function index(Request $request): View
    {
        $query = InvestmentInquiry::query()
            ->with('plan:id,title')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $query->where(function ($q) use ($s) {
                $q->where('full_name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('company_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('plan_id')) {
            $query->where('investment_plan_id', $request->plan_id);
        }

        $inquiries = $query->paginate(25)->withQueryString();
        $plans = InvestmentPlan::orderBy('title')->get(['id', 'title']);
        $statusLabels = InvestmentInquiry::statusLabels();

        return view('admin.investment.inquiries.index', compact('inquiries', 'plans', 'statusLabels'));
    }

    public function show(InvestmentInquiry $inquiry): View
    {
        $inquiry->load(['plan', 'reviewer:id,name']);
        $statusLabels = InvestmentInquiry::statusLabels();

        return view('admin.investment.inquiries.show', compact('inquiry', 'statusLabels'));
    }

    public function update(Request $request, InvestmentInquiry $inquiry): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,under_review,meeting_scheduled,approved,rejected,withdrawn',
            'admin_notes' => 'nullable|string|max:5000',
        ]);

        $inquiry->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? $inquiry->admin_notes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'تم تحديث حالة الطلب.');
    }

    public function destroy(InvestmentInquiry $inquiry): RedirectResponse
    {
        $inquiry->delete();

        return redirect()->route('admin.investment.inquiries.index')
            ->with('success', 'تم حذف الطلب.');
    }
}
