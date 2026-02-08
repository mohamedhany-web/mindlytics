<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * عرض سجل النشاطات
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        // فلترة حسب النوع (action)
        if ($request->filled('type')) {
            $type = $request->type;
            $query->where(function($q) use ($type) {
                $q->where('action', 'like', '%' . $type . '%')
                  ->orWhere('action', $type);
            });
        }

        // فلترة حسب المستخدم
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // فلترة حسب التاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // البحث في الوصف
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('action', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%')
                                ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $activities = $query->paginate(20);
        
        // إحصائيات
        $stats = [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::whereDate('created_at', today())->count(),
            'this_week' => ActivityLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => ActivityLog::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
        ];

        return view('admin.activity-log.index', compact('activities', 'stats'));
    }

    /**
     * عرض تفاصيل نشاط معين
     */
    public function show(ActivityLog $activityLog)
    {
        return view('admin.activity-log.show', compact('activityLog'));
    }

    /**
     * مسح سجلات النشاط
     */
    public function destroy(Request $request)
    {
        try {
            $type = $request->input('delete_type', $request->input('type', 'all'));
            $deletedCount = 0;

            switch ($type) {
                case 'all':
                    // مسح جميع السجلات
                    $deletedCount = ActivityLog::count();
                    ActivityLog::truncate();
                    break;

                case 'old':
                    // مسح السجلات الأقدم من 3 أشهر
                    $date = now()->subMonths(3);
                    $deletedCount = ActivityLog::where('created_at', '<', $date)->count();
                    ActivityLog::where('created_at', '<', $date)->delete();
                    break;

                case 'older':
                    // مسح السجلات الأقدم من 6 أشهر
                    $date = now()->subMonths(6);
                    $deletedCount = ActivityLog::where('created_at', '<', $date)->count();
                    ActivityLog::where('created_at', '<', $date)->delete();
                    break;

                case 'filtered':
                    // مسح السجلات المطابقة للفلاتر الحالية
                    $query = ActivityLog::query();

                    if ($request->filled('type') && $request->input('type') !== 'filtered') {
                        $typeValue = $request->type;
                        $query->where(function($q) use ($typeValue) {
                            $q->where('action', 'like', '%' . $typeValue . '%')
                              ->orWhere('action', $typeValue);
                        });
                    }

                    if ($request->filled('user_id')) {
                        $query->where('user_id', $request->user_id);
                    }

                    if ($request->filled('date_from')) {
                        $query->whereDate('created_at', '>=', $request->date_from);
                    }

                    if ($request->filled('date_to')) {
                        $query->whereDate('created_at', '<=', $request->date_to);
                    }

                    if ($request->filled('search')) {
                        $query->where(function($q) use ($request) {
                            $q->where('description', 'like', '%' . $request->search . '%')
                              ->orWhere('action', 'like', '%' . $request->search . '%')
                              ->orWhereHas('user', function($userQuery) use ($request) {
                                  $userQuery->where('name', 'like', '%' . $request->search . '%')
                                            ->orWhere('email', 'like', '%' . $request->search . '%');
                              });
                        });
                    }

                    $deletedCount = $query->count();
                    $query->delete();
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'نوع المسح غير صحيح'
                    ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => "تم مسح {$deletedCount} سجل بنجاح",
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء مسح السجلات: ' . $e->getMessage()
            ], 500);
        }
    }
}









