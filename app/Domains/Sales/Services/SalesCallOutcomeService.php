<?php

namespace App\Domains\Sales\Services;

use App\Domains\Sales\Models\SalesLead;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * نتيجة المكالمة ← المرحلة التالية للعميل (لم يرد، مهتم، غير مهتم…).
 * مستخدمة من تسجيل النشاط في شاشات المبيعات ومن تنفيذ مكالمة مخططة في CRM.
 */
class SalesCallOutcomeService
{
    public function apply(SalesLead $lead, ?string $outcome, User $actor): void
    {
        if (! $outcome) {
            return;
        }

        $pipeline = app(SalesPipelineService::class);

        try {
            match ($outcome) {
                'no_answer' => $pipeline->transition($lead, 'no_answer', [
                    'notes' => 'تسجيل تلقائي: لم يرد على المكالمة.',
                ], $actor),
                'interested' => $pipeline->transition($lead, 'connected', [
                    'connected_disposition' => 'interested',
                    'notes' => 'تسجيل تلقائي: مهتم بعد المكالمة.',
                ], $actor),
                'follow_up' => $pipeline->transition(
                    $lead,
                    in_array($lead->stage, ['new_lead', 'first_contact', 'no_answer'], true) ? 'connected' : 'follow_up_scheduled',
                    array_merge(
                        ['notes' => 'تسجيل تلقائي: يحتاج متابعة بعد المكالمة.'],
                        in_array($lead->stage, ['new_lead', 'first_contact', 'no_answer'], true)
                            ? ['connected_disposition' => 'callback']
                            : [
                                'next_follow_up_at' => now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i'),
                                'follow_up_channel' => 'whatsapp',
                            ]
                    ),
                    $actor
                ),
                'not_interested' => $pipeline->transition($lead, 'lost', [
                    'lost_reason' => 'no_need',
                    'notes' => 'تسجيل تلقائي: غير مهتم.',
                ], $actor),
                'wrong_number' => $pipeline->transition($lead, 'lost', [
                    'lost_reason' => 'wrong_number',
                    'notes' => 'تسجيل تلقائي: رقم خطأ.',
                ], $actor),
                'closed_lost' => $pipeline->transition($lead, 'lost', [
                    'lost_reason' => 'other',
                    'notes' => 'تسجيل تلقائي: إغلاق خسارة.',
                ], $actor),
                'closed_won' => $pipeline->transition($lead, SalesLead::WON_STAGE, [
                    'expected_value' => $lead->expected_value ?: $lead->offer_price ?: $lead->payment_amount ?: 1,
                    'notes' => 'تسجيل تلقائي: إغلاق ناجح من المكالمة.',
                ], $actor),
                default => null,
            };
        } catch (\Illuminate\Validation\ValidationException $e) {
            // لا نفشل تسجيل المكالمة إن تعذّر الانتقال التلقائي — يحدّث الموظف المرحلة يدوياً
            Log::info('sales.outcome_stage_skipped', [
                'lead_id' => $lead->id,
                'outcome' => $outcome,
                'errors' => $e->errors(),
            ]);
        }
    }
}
