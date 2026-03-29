<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class SalesAuditController extends Controller
{
    private const SALES_ACTIONS = [
        'sales_lead_created',
        'sales_lead_viewed',
        'sales_lead_updated',
        'sales_lead_deleted',
        'sales_activity_created',
        'sales_lead_created_admin',
        'sales_lead_viewed_admin',
        'sales_lead_updated_admin',
        'sales_lead_deleted_admin',
        'sales_lead_reassigned',
        'sales_activity_created_admin',
    ];

    public function index(Request $request)
    {
        $query = ActivityLog::query()->with('user')->whereIn('action', self::SALES_ACTIONS);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('description', 'like', "%{$s}%")
                    ->orWhere('url', 'like', "%{$s}%");
            });
        }

        $logs = $query->latest()->paginate(40)->withQueryString();

        $filterUsers = \App\Models\User::orderBy('name')->get(['id', 'name']);

        return view('admin.sales.audit.index', [
            'logs' => $logs,
            'filterUsers' => $filterUsers,
            'actionLabels' => $this->actionLabels(),
        ]);
    }

    private function actionLabels(): array
    {
        return [
            'sales_lead_created' => 'إنشاء عميل (موظف)',
            'sales_lead_viewed' => 'عرض عميل (موظف)',
            'sales_lead_updated' => 'تحديث عميل (موظف)',
            'sales_lead_deleted' => 'حذف عميل (موظف)',
            'sales_activity_created' => 'نشاط (موظف)',
            'sales_lead_created_admin' => 'إنشاء عميل (إدارة)',
            'sales_lead_viewed_admin' => 'عرض عميل (إدارة)',
            'sales_lead_updated_admin' => 'تحديث عميل (إدارة)',
            'sales_lead_deleted_admin' => 'حذف عميل (إدارة)',
            'sales_lead_reassigned' => 'إعادة إسناد',
            'sales_activity_created_admin' => 'نشاط (إدارة)',
        ];
    }
}
