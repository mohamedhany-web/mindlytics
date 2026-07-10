<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesFollowUpController extends Controller
{
    public function __construct()
    {
        $this->middleware('sales.employee');
    }

    public function index(Request $request): View
    {
        $userId = (int) Auth::id();
        $filter = $request->get('filter', 'today');
        if (! in_array($filter, ['overdue', 'today', 'week', 'none', 'stale', 'all'], true)) {
            $filter = 'today';
        }

        $base = SalesLead::query()
            ->forAssignee($userId)
            ->openPipeline()
            ->with(['category']);

        $staleDays = SalesLead::STALE_CONTACT_DAYS;
        $counts = [
            'overdue' => (clone $base)->whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<', now())->count(),
            'today' => (clone $base)->whereNotNull('next_follow_up_at')->whereDate('next_follow_up_at', today())->count(),
            'week' => (clone $base)->whereNotNull('next_follow_up_at')
                ->where('next_follow_up_at', '>=', now()->startOfDay())
                ->where('next_follow_up_at', '<=', now()->addDays(7)->endOfDay())
                ->count(),
            'none' => (clone $base)->whereNull('next_follow_up_at')->count(),
            'stale' => (clone $base)->where(function ($q) use ($staleDays) {
                $q->where(function ($q2) use ($staleDays) {
                    $q2->whereNull('last_contacted_at')->where('created_at', '<', now()->subDays($staleDays));
                })->orWhere('last_contacted_at', '<', now()->subDays($staleDays));
            })->count(),
            'all' => (clone $base)->count(),
        ];

        $query = clone $base;

        match ($filter) {
            'overdue' => $query->whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<', now())
                ->orderBy('next_follow_up_at'),
            'today' => $query->whereNotNull('next_follow_up_at')->whereDate('next_follow_up_at', today())
                ->orderBy('next_follow_up_at'),
            'week' => $query->whereNotNull('next_follow_up_at')
                ->where('next_follow_up_at', '>=', now()->startOfDay())
                ->where('next_follow_up_at', '<=', now()->addDays(7)->endOfDay())
                ->orderBy('next_follow_up_at'),
            'none' => $query->whereNull('next_follow_up_at')->orderByDesc('updated_at'),
            'stale' => $query->where(function ($q) use ($staleDays) {
                $q->where(function ($q2) use ($staleDays) {
                    $q2->whereNull('last_contacted_at')->where('created_at', '<', now()->subDays($staleDays));
                })->orWhere('last_contacted_at', '<', now()->subDays($staleDays));
            })->orderByRaw('last_contacted_at IS NULL DESC')->orderBy('last_contacted_at'),
            default => $query->orderByRaw('next_follow_up_at IS NULL ASC')->orderBy('next_follow_up_at'),
        };

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $leads = $query->paginate(30)->withQueryString();

        return view('employee.sales.follow-ups.index', compact('leads', 'counts', 'filter', 'staleDays'));
    }
}
