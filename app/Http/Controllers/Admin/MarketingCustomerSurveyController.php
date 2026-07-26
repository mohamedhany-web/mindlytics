<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\MarketingCustomerSurvey;
use App\Services\CustomerSurveyRewardService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MarketingCustomerSurveyController extends Controller
{
    public function index(Request $request)
    {
        $query = MarketingCustomerSurvey::query()
            ->with(['course:id,title', 'user:id,name,email', 'rewardCoupon'])
            ->latest();

        if ($request->filled('course_id')) {
            $query->where('advanced_course_id', (int) $request->course_id);
        }

        if ($request->filled('governorate')) {
            $query->where('governorate', $request->governorate);
        }

        if ($request->filled('heard_from')) {
            $query->where('heard_from', $request->heard_from);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $surveys = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => MarketingCustomerSurvey::count(),
            'this_month' => MarketingCustomerSurvey::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'rewarded' => MarketingCustomerSurvey::whereNotNull('reward_coupon_id')->count(),
        ];

        $heardFromBreakdown = MarketingCustomerSurvey::selectRaw('heard_from, count(*) as total')
            ->groupBy('heard_from')
            ->orderByDesc('total')
            ->pluck('total', 'heard_from');

        return view('admin.marketing-customer-surveys.index', [
            'surveys' => $surveys,
            'stats' => $stats,
            'heardFromBreakdown' => $heardFromBreakdown,
            'courses' => AdvancedCourse::orderBy('title')->get(['id', 'title']),
            'governorates' => MarketingCustomerSurvey::governorates(),
            'heardFromOptions' => MarketingCustomerSurvey::heardFromOptions(),
            'discountPercentage' => CustomerSurveyRewardService::DISCOUNT_PERCENTAGE,
        ]);
    }

    public function show(MarketingCustomerSurvey $marketing_customer_survey)
    {
        $marketing_customer_survey->load(['course:id,title', 'user:id,name,email,phone', 'rewardCoupon.usages']);

        return view('admin.marketing-customer-surveys.show', [
            'survey' => $marketing_customer_survey,
        ]);
    }

    public function destroy(MarketingCustomerSurvey $marketing_customer_survey)
    {
        $marketing_customer_survey->delete();

        return redirect()->route('admin.marketing-customer-surveys.index')
            ->with('success', 'تم حذف رد الاستبيان. ملاحظة: كوبون الخصم الممنوح يبقى صالحاً حتى تعطّله من صفحة الكوبونات.');
    }

    public function export(Request $request): StreamedResponse
    {
        $surveys = MarketingCustomerSurvey::with(['course:id,title', 'rewardCoupon:id,code'])
            ->latest()
            ->get();

        $filename = 'customer-surveys-'.now()->format('Y-m-d-His').'.csv';

        return response()->stream(function () use ($surveys) {
            $handle = fopen('php://output', 'wb');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'التاريخ', 'الاسم', 'البريد', 'الهاتف', 'الكورس', 'المحافظة',
                'الوظيفة', 'عرفنا من', 'مهتم بإيه', 'الرأي', 'كورسات مطلوبة',
                'توصيات', 'كود الخصم',
            ]);

            foreach ($surveys as $survey) {
                fputcsv($handle, [
                    $survey->created_at?->format('Y-m-d H:i'),
                    $survey->name,
                    $survey->email,
                    $survey->phone,
                    $survey->course->title ?? '—',
                    $survey->governorate_label,
                    $survey->job_label,
                    $survey->heard_from_label,
                    $survey->interested_in,
                    $survey->opinion,
                    $survey->needed_courses,
                    $survey->recommendations,
                    $survey->rewardCoupon->code ?? '—',
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
