<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesCourseCommissionAgreement;
use App\Models\User;
use App\Services\SalesAuditService;
use App\Services\SalesCommissionTierService;
use App\Services\SalesCourseCommissionResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SalesCourseCommissionAgreementController extends Controller
{
    public function store(Request $request)
    {
        $validated = $this->validatedAgreement($request);
        $rep = User::query()->findOrFail($validated['user_id']);
        if (! $rep->isSalesEmployee()) {
            return back()->withErrors(['user_id' => 'المستخدم ليس موظف مبيعات.'])->withInput();
        }

        $courseId = (int) $validated['course_ref_id'];
        $type = (string) $validated['course_type'];
        $key = SalesCourseCommissionAgreement::makeCourseKey($type, $courseId);

        if (SalesCourseCommissionAgreement::query()->where('user_id', $rep->id)->where('course_key', $key)->exists()) {
            return back()->withErrors(['course_ref_id' => 'توجد اتفاقية لهذا الكورس مسبقاً.'])->withInput();
        }

        $agreement = new SalesCourseCommissionAgreement([
            'user_id' => $rep->id,
            'calc_mode' => $validated['calc_mode'],
            'commission_value' => in_array($validated['calc_mode'], ['fixed', 'percent'], true)
                ? (float) ($validated['commission_value'] ?? 0)
                : null,
            'tier_period' => $validated['tier_period'] ?? 'month',
            'tiers' => $this->tiersFromRequest($request, $validated['calc_mode']),
            'is_active' => true,
        ]);
        SalesCourseCommissionAgreement::applyCourseSelection($agreement, $type, $courseId);
        $agreement->save();

        SalesAuditService::log(
            'sales_course_commission_agreement_created',
            $agreement,
            null,
            $agreement->toArray(),
            'إنشاء اتفاقية كوميشن كورس لـ '.($rep->name ?? '')
        );

        return redirect()
            ->route('admin.sales.kpi.targets', [
                'user_id' => $rep->id,
                'year_month' => $request->input('year_month', now()->format('Y-m')),
            ])
            ->with('success', 'تم إضافة اتفاقية الكورس.');
    }

    public function update(Request $request, SalesCourseCommissionAgreement $agreement)
    {
        $validated = $this->validatedAgreement($request, false);
        $type = (string) $validated['course_type'];
        $courseId = (int) $validated['course_ref_id'];
        $key = SalesCourseCommissionAgreement::makeCourseKey($type, $courseId);

        $dup = SalesCourseCommissionAgreement::query()
            ->where('user_id', $agreement->user_id)
            ->where('course_key', $key)
            ->where('id', '!=', $agreement->id)
            ->exists();
        if ($dup) {
            return back()->withErrors(['course_ref_id' => 'توجد اتفاقية لهذا الكورس مسبقاً.'])->withInput();
        }

        $before = $agreement->toArray();
        SalesCourseCommissionAgreement::applyCourseSelection($agreement, $type, $courseId);
        $agreement->forceFill([
            'calc_mode' => $validated['calc_mode'],
            'commission_value' => in_array($validated['calc_mode'], ['fixed', 'percent'], true)
                ? (float) ($validated['commission_value'] ?? 0)
                : null,
            'tier_period' => $validated['tier_period'] ?? 'month',
            'tiers' => $this->tiersFromRequest($request, $validated['calc_mode']),
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ])->save();

        SalesAuditService::log(
            'sales_course_commission_agreement_updated',
            $agreement,
            $before,
            $agreement->fresh()->toArray(),
            'تحديث اتفاقية كوميشن كورس بواسطة '.(Auth::user()->name ?? '')
        );

        return redirect()
            ->route('admin.sales.kpi.targets', [
                'user_id' => $agreement->user_id,
                'year_month' => $request->input('year_month', now()->format('Y-m')),
            ])
            ->with('success', 'تم تحديث اتفاقية الكورس.');
    }

    public function destroy(Request $request, SalesCourseCommissionAgreement $agreement)
    {
        $userId = $agreement->user_id;
        $agreement->delete();

        return redirect()
            ->route('admin.sales.kpi.targets', [
                'user_id' => $userId,
                'year_month' => $request->input('year_month', now()->format('Y-m')),
            ])
            ->with('success', 'تم حذف اتفاقية الكورس.');
    }

    public function courses(Request $request)
    {
        $type = (string) $request->query('type', 'advanced');
        if (! array_key_exists($type, SalesCourseCommissionAgreement::COURSE_TYPES)) {
            return response()->json(['data' => []]);
        }

        return response()->json(['data' => SalesCourseCommissionResolver::listCourses($type)]);
    }

    private function validatedAgreement(Request $request, bool $requireUser = true): array
    {
        return $request->validate([
            'user_id' => [$requireUser ? 'required' : 'nullable', 'integer', Rule::exists('users', 'id')],
            'course_type' => ['required', Rule::in(array_keys(SalesCourseCommissionAgreement::COURSE_TYPES))],
            'course_ref_id' => ['required', 'integer', 'min:1'],
            'calc_mode' => ['required', Rule::in(array_keys(SalesCourseCommissionAgreement::CALC_MODES))],
            'commission_value' => ['nullable', 'numeric', 'min:0'],
            'tier_period' => ['nullable', Rule::in(['month', 'all'])],
            'is_active' => ['nullable', 'boolean'],
            'agr_tier_min' => ['nullable', 'array'],
            'agr_tier_max' => ['nullable', 'array'],
            'agr_tier_rate' => ['nullable', 'array'],
            'agr_tier_bonus' => ['nullable', 'array'],
            'agr_tier_bonus_at' => ['nullable', 'array'],
        ]);
    }

    private function tiersFromRequest(Request $request, string $calcMode): ?array
    {
        if (! in_array($calcMode, ['tier_course', 'tier_course_global_count'], true)) {
            return null;
        }

        $tiers = [];
        $mins = $request->input('agr_tier_min', []);
        $maxs = $request->input('agr_tier_max', []);
        $rates = $request->input('agr_tier_rate', []);
        $bonuses = $request->input('agr_tier_bonus', []);
        $bonusAts = $request->input('agr_tier_bonus_at', []);
        $count = max(count($mins), count($rates));
        for ($i = 0; $i < $count; $i++) {
            if (! isset($mins[$i]) || $mins[$i] === '' || $mins[$i] === null) {
                continue;
            }
            $tiers[] = [
                'min' => (int) $mins[$i],
                'max' => (isset($maxs[$i]) && $maxs[$i] !== '' && $maxs[$i] !== null) ? (int) $maxs[$i] : null,
                'rate' => (float) ($rates[$i] ?? 0),
                'bonus' => (float) ($bonuses[$i] ?? 0),
                'bonus_at' => (isset($bonusAts[$i]) && $bonusAts[$i] !== '' && $bonusAts[$i] !== null) ? (int) $bonusAts[$i] : null,
            ];
        }

        return SalesCommissionTierService::normalizeTiers($tiers);
    }
}
