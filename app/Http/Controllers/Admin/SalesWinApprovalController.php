<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesLead;
use App\Models\User;
use App\Services\SalesWinCommissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SalesWinApprovalController extends Controller
{
    public function __construct(
        private SalesWinCommissionService $winCommission,
    ) {}

    public function index(Request $request)
    {
        $query = SalesLead::query()
            ->pendingWinApproval()
            ->with(['assignee', 'category'])
            ->orderByDesc('closed_at')
            ->orderByDesc('updated_at');

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', (int) $request->assigned_to);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('company', 'like', "%{$s}%");
            });
        }

        $leads = $query->paginate(25)->withQueryString();
        $reps = User::salesEmployees()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        $stats = [
            'pending' => SalesLead::pendingWinApproval()->count(),
            'pending_value' => (float) SalesLead::pendingWinApproval()->sum('expected_value'),
            'pending_commission_est' => SalesLead::pendingWinApproval()
                ->with('assignee')
                ->get()
                ->sum(fn (SalesLead $l) => SalesWinCommissionService::defaultCommissionForLead($l)),
        ];

        return view('admin.sales.win-approvals.index', compact('leads', 'reps', 'stats'));
    }

    public function approve(Request $request, SalesLead $lead): RedirectResponse
    {
        $validated = $request->validate([
            'commission_amount' => 'nullable|numeric|min:0',
            'commission_notes' => 'nullable|string|max:2000',
        ]);

        $result = $this->winCommission->approveAndPayCommission(
            $lead,
            array_key_exists('commission_amount', $validated) && $validated['commission_amount'] !== null
                ? (float) $validated['commission_amount']
                : null,
            $validated['commission_notes'] ?? null,
        );

        if (! ($result['success'] ?? false)) {
            return back()->withErrors(['error' => $result['error'] ?? 'فشل الاعتماد']);
        }

        return back()->with('success', 'تم اعتماد الصفقة وصرف الكوميشن: ' . number_format((float) ($result['commission'] ?? 0), 2) . ' ج.م');
    }

    public function reject(Request $request, SalesLead $lead): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:2000',
        ]);

        $result = $this->winCommission->rejectWin($lead, $validated['rejection_reason']);

        if (! ($result['success'] ?? false)) {
            return back()->withErrors(['error' => $result['error'] ?? 'فشل الرفض']);
        }

        return back()->with('success', 'تم رفض اعتماد الفوز وإعادة الصفقة لمرحلة «عرض سعر».');
    }
}
