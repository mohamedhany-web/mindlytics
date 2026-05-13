<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\OfflineCourse;
use App\Models\Order;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use Illuminate\View\View;

class BranchDashboardController extends Controller
{
    public function index(): View
    {
        $branch = auth()->user()->branch;
        abort_unless($branch, 404);

        $branchId = (int) $branch->id;

        $stats = [
            'users' => User::query()->where('branch_id', $branchId)->count(),
            'students' => User::query()->where('branch_id', $branchId)->where('role', 'student')->count(),
            'instructors' => User::query()->where('branch_id', $branchId)->whereIn('role', ['instructor', 'teacher'])->count(),
            'advanced_courses' => AdvancedCourse::query()->where('branch_id', $branchId)->count(),
            'offline_courses' => OfflineCourse::query()->where('branch_id', $branchId)->count(),
            'enrollments' => StudentCourseEnrollment::query()->where('branch_id', $branchId)->count(),
            'orders' => Order::query()->where('branch_id', $branchId)->count(),
        ];

        $recentUsers = User::query()
            ->where('branch_id', $branchId)
            ->orderByDesc('created_at')
            ->limit(8)
            ->get(['id', 'name', 'email', 'role', 'is_active', 'created_at']);

        return view('branch-office.dashboard', compact('branch', 'stats', 'recentUsers'));
    }
}
