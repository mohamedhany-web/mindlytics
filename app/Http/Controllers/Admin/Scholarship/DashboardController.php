<?php

namespace App\Http\Controllers\Admin\Scholarship;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipProgram;
use App\Models\ScholarshipRegistration;
use App\Services\Scholarship\ScholarshipStatsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, ScholarshipStatsService $stats): View
    {
        $overview = $stats->overview();
        $pendingRegistrations = $stats->recentPending(10);
        $recentPrograms = $stats->recentPrograms(6);

        $query = ScholarshipProgram::query()
            ->with(['instructor', 'course'])
            ->withCount([
                'registrations',
                'registrations as activated_count' => fn ($q) => $q->where('status', ScholarshipRegistration::STATUS_ACTIVATED),
                'registrations as pending_count' => fn ($q) => $q->where('status', ScholarshipRegistration::STATUS_REGISTERED),
            ])
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('slug', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $programs = $query->paginate(15)->withQueryString();

        return view('admin.scholarships.dashboard.index', compact(
            'overview',
            'pendingRegistrations',
            'recentPrograms',
            'programs',
        ));
    }
}
