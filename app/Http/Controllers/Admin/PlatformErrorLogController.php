<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformErrorLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlatformErrorLogController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->filteredQuery($request);

        $statsBase = PlatformErrorLog::query();
        $stats = [
            'open' => (clone $statsBase)->open()->count(),
            'today' => (clone $statsBase)->whereDate('created_at', today())->count(),
            'critical' => (clone $statsBase)->unresolved()->where('level', 'critical')->count(),
            'resolved_week' => (clone $statsBase)->where('status', 'resolved')
                ->where('resolved_at', '>=', now()->subDays(7))
                ->count(),
        ];

        $errors = (clone $query)
            ->with(['user:id,name,email'])
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        $userOptions = User::query()
            ->whereIn('id', PlatformErrorLog::query()->whereNotNull('user_id')->distinct()->pluck('user_id'))
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $topFingerprints = PlatformErrorLog::query()
            ->unresolved()
            ->selectRaw('fingerprint, COUNT(*) as hits, MAX(message) as sample_message, MAX(exception_class) as sample_class')
            ->groupBy('fingerprint')
            ->orderByDesc('hits')
            ->limit(5)
            ->get();

        return view('admin.platform-errors.index', compact(
            'errors',
            'stats',
            'userOptions',
            'topFingerprints'
        ));
    }

    public function show(PlatformErrorLog $platformError)
    {
        $platformError->load(['user:id,name,email', 'resolver:id,name']);

        $similar = PlatformErrorLog::query()
            ->where('fingerprint', $platformError->fingerprint)
            ->whereKeyNot($platformError->id)
            ->with('user:id,name,email')
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        $sameUserRecent = $platformError->user_id
            ? PlatformErrorLog::query()
                ->where('user_id', $platformError->user_id)
                ->whereKeyNot($platformError->id)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get()
            : collect();

        return view('admin.platform-errors.show', compact('platformError', 'similar', 'sameUserRecent'));
    }

    public function updateStatus(Request $request, PlatformErrorLog $platformError)
    {
        $data = $request->validate([
            'status' => 'required|in:open,acknowledged,resolved',
            'admin_notes' => 'nullable|string|max:5000',
        ]);

        $updates = [
            'status' => $data['status'],
            'admin_notes' => $data['admin_notes'] ?? $platformError->admin_notes,
        ];

        if ($data['status'] === 'acknowledged' && ! $platformError->acknowledged_at) {
            $updates['acknowledged_at'] = now();
        }

        if ($data['status'] === 'resolved') {
            $updates['resolved_at'] = now();
            $updates['resolved_by'] = Auth::id();
        } elseif ($data['status'] !== 'resolved') {
            $updates['resolved_at'] = null;
            $updates['resolved_by'] = null;
        }

        $platformError->update($updates);

        return back()->with('success', 'تم تحديث حالة الخطأ.');
    }

    public function bulkUpdate(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:platform_error_logs,id',
            'status' => 'required|in:acknowledged,resolved',
        ]);

        $updates = ['status' => $data['status']];

        if ($data['status'] === 'resolved') {
            $updates['resolved_at'] = now();
            $updates['resolved_by'] = Auth::id();
        }

        PlatformErrorLog::query()
            ->whereIn('id', $data['ids'])
            ->update($updates);

        return back()->with('success', 'تم تحديث '.count($data['ids']).' سجل.');
    }

    private function filteredQuery(Request $request): Builder
    {
        $query = PlatformErrorLog::query();

        if ($request->filled('quick')) {
            match ($request->quick) {
                'open' => $query->open(),
                'unresolved' => $query->unresolved(),
                'today' => $query->whereDate('created_at', today()),
                'critical' => $query->unresolved()->where('level', 'critical'),
                default => null,
            };
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->boolean('guest_only')) {
            $query->whereNull('user_id');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function (Builder $q) use ($s) {
                $q->where('message', 'like', "%{$s}%")
                    ->orWhere('exception_class', 'like', "%{$s}%")
                    ->orWhere('url', 'like', "%{$s}%")
                    ->orWhere('file', 'like', "%{$s}%")
                    ->orWhereHas('user', function (Builder $uq) use ($s) {
                        $uq->where('name', 'like', "%{$s}%")
                            ->orWhere('email', 'like', "%{$s}%");
                    });
            });
        }

        return $query;
    }
}
