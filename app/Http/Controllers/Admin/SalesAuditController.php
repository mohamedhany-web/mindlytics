<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class SalesAuditController extends Controller
{
    public const SALES_ACTIONS = [
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
        'sales_lead_won_confirmed',
        'sales_lead_won_rejected',
        'sales_leads_bulk_import',
        'sales_lead_csat_recorded',
        'sales_data_transferred',
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

        $stats = [
            'total' => (clone $query)->count(),
        ];

        $logs = $query->latest()->paginate(40)->withQueryString();

        $filterUsers = \App\Models\User::orderBy('name')->get(['id', 'name']);

        return view('admin.sales.audit.index', [
            'logs' => $logs,
            'stats' => $stats,
            'filterUsers' => $filterUsers,
            'actionLabels' => self::actionLabels(),
        ]);
    }

    public static function actionLabels(): array
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
            'sales_lead_won_confirmed' => 'اعتماد فوز + عمولة',
            'sales_lead_won_rejected' => 'رفض اعتماد فوز',
            'sales_leads_bulk_import' => 'استيراد دفعة عملاء',
            'sales_lead_csat_recorded' => 'تسجيل CSAT',
            'sales_data_transferred' => 'تحويل بيانات مبيعات',
        ];
    }
}
