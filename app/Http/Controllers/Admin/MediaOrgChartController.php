<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DesignTaskCycle;
use App\Models\EmployeeJob;
use App\Models\ModeratorMontageRequest;
use App\Models\User;

class MediaOrgChartController extends Controller
{
    public function index()
    {
        EmployeeJob::ensureMediaJobs();

        $moderators = User::moderatorEmployees()
            ->where('is_active', true)
            ->with('employeeJob')
            ->orderBy('name')
            ->get();

        $designers = User::designerEmployees()
            ->where('is_active', true)
            ->with('employeeJob')
            ->orderBy('name')
            ->get();

        $editors = User::videoEditingEmployees()
            ->where('is_active', true)
            ->with('employeeJob')
            ->orderBy('name')
            ->get();

        $openDesignByModerator = DesignTaskCycle::query()
            ->whereNotIn('status', [
                DesignTaskCycle::STATUS_COMPLETED,
                DesignTaskCycle::STATUS_CANCELLED,
            ])
            ->selectRaw('moderator_id, count(*) as c')
            ->groupBy('moderator_id')
            ->pluck('c', 'moderator_id');

        $openMontageByModerator = ModeratorMontageRequest::query()
            ->whereNotIn('status', [
                ModeratorMontageRequest::STATUS_COMPLETED,
                ModeratorMontageRequest::STATUS_CANCELLED,
            ])
            ->selectRaw('moderator_id, count(*) as c')
            ->groupBy('moderator_id')
            ->pluck('c', 'moderator_id');

        $openDesignByDesigner = DesignTaskCycle::query()
            ->whereNotIn('status', [
                DesignTaskCycle::STATUS_COMPLETED,
                DesignTaskCycle::STATUS_CANCELLED,
            ])
            ->selectRaw('designer_employee_id, count(*) as c')
            ->groupBy('designer_employee_id')
            ->pluck('c', 'designer_employee_id');

        $openMontageByEditor = ModeratorMontageRequest::query()
            ->whereNotIn('status', [
                ModeratorMontageRequest::STATUS_COMPLETED,
                ModeratorMontageRequest::STATUS_CANCELLED,
            ])
            ->selectRaw('montage_employee_id, count(*) as c')
            ->groupBy('montage_employee_id')
            ->pluck('c', 'montage_employee_id');

        $stats = [
            'moderators' => $moderators->count(),
            'designers' => $designers->count(),
            'editors' => $editors->count(),
            'open_design' => (int) $openDesignByModerator->sum(),
            'open_montage' => (int) $openMontageByModerator->sum(),
        ];

        return view('admin.media.org-chart.index', compact(
            'moderators',
            'designers',
            'editors',
            'openDesignByModerator',
            'openMontageByModerator',
            'openDesignByDesigner',
            'openMontageByEditor',
            'stats'
        ));
    }
}
