<?php

namespace App\Services;

use App\Mail\PlatformNotificationMail;
use App\Models\EmployeeSalaryDeduction;
use App\Models\Notification;
use App\Models\SalesDailyReport;
use App\Models\SalesLead;
use App\Models\SalesLeadCategory;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppConversationMessage;
use App\Models\Workshop;
use App\Support\SalesDailyReportSettings;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class SalesNotificationService
{
    public function notifyLeadAssigned(SalesLead $lead, ?int $previousAssigneeId = null): void
    {
        $lead->loadMissing(['assignee', 'category']);
        $rep = $lead->assignee;
        if (! $rep) {
            return;
        }

        $isReassign = $previousAssigneeId && (int) $previousAssigneeId !== (int) $lead->assigned_to;
        $title = $isReassign ? 'إعادة إسناد عميل محتمل' : 'عميل محتمل جديد مسند إليك';
        $categoryLabel = $lead->category?->name ?? 'بدون تصنيف';
        $message = ($isReassign ? 'تم إسناد' : 'أُضيف').' العميل «'.$lead->name.'» إليك'
            .' — التصنيف: '.$categoryLabel
            .($lead->priority === 'urgent' ? ' — أولوية عاجلة!' : '');

        $this->sendOnceToday(
            (int) $rep->id,
            $title,
            route('employee.sales.leads.show', $lead),
            fn () => Notification::create([
                'user_id' => $rep->id,
                'sender_id' => auth()->id(),
                'title' => $title,
                'message' => $message,
                'type' => $isReassign ? 'warning' : 'employee',
                'priority' => in_array($lead->priority, ['urgent', 'high'], true) ? 'high' : 'normal',
                'audience' => 'employee',
                'action_url' => route('employee.sales.leads.show', $lead),
                'action_text' => 'فتح العميل',
                'data' => [
                    'kind' => 'sales_lead_assigned',
                    'lead_id' => $lead->id,
                    'category_id' => $lead->category_id,
                    'reassigned' => $isReassign,
                ],
            ])
        );

        if ($isReassign && $previousAssigneeId) {
            $this->sendOnceToday(
                (int) $previousAssigneeId,
                'تم نقل عميل من قائمتك',
                route('employee.sales.leads.index'),
                fn () => Notification::create([
                    'user_id' => $previousAssigneeId,
                    'sender_id' => auth()->id(),
                    'title' => 'تم نقل عميل من قائمتك',
                    'message' => 'العميل «'.$lead->name.'» أُعيد إسناده لموظف آخر.',
                    'type' => 'employee',
                    'priority' => 'normal',
                    'audience' => 'employee',
                    'action_url' => route('employee.sales.leads.index'),
                    'action_text' => 'عرض القائمة',
                    'data' => ['kind' => 'sales_lead_unassigned', 'lead_id' => $lead->id],
                ])
            );
        }
    }

    /**
     * @param  array{new: list<string>, existing: list<string>}  $repSummary
     */
    public function notifyWorkshopLeadsTransferred(User $rep, Workshop $workshop, array $repSummary, string $batchId): void
    {
        $newNames = $repSummary['new'] ?? [];
        $existingNames = $repSummary['existing'] ?? [];
        $newCount = count($newNames);
        $existingCount = count($existingNames);

        if ($newCount === 0 && $existingCount === 0) {
            return;
        }

        $parts = [];
        if ($newCount > 0) {
            $preview = implode('، ', array_slice($newNames, 0, 6));
            if ($newCount > 6) {
                $preview .= ' … و'.($newCount - 6).' آخرين';
            }
            $parts[] = $newCount.' عميل جديد: '.$preview;
        }
        if ($existingCount > 0) {
            $preview = implode('، ', array_slice($existingNames, 0, 6));
            if ($existingCount > 6) {
                $preview .= ' … و'.($existingCount - 6).' آخرين';
            }
            $parts[] = $existingCount.' موجود مسبقاً (تم ربطهم بالورشة): '.$preview;
        }

        $title = 'ترحيل من ورشة: '.$workshop->title;
        $message = implode(' | ', $parts);

        $actionUrl = $newCount > 0
            ? route('employee.sales.leads.index', ['import_batch' => $batchId])
            : route('employee.sales.leads.index');

        Notification::create([
            'user_id' => $rep->id,
            'sender_id' => auth()->id(),
            'title' => $title,
            'message' => $message,
            'type' => 'employee',
            'priority' => 'high',
            'audience' => 'employee',
            'action_url' => $actionUrl,
            'action_text' => $newCount > 0 ? 'عرض العملاء الجدد' : 'عرض العملاء',
            'data' => [
                'kind' => 'workshop_leads_transferred',
                'workshop_id' => $workshop->id,
                'import_batch' => $batchId,
                'new_count' => $newCount,
                'existing_count' => $existingCount,
                'new_names' => $newNames,
                'existing_names' => $existingNames,
            ],
        ]);
    }

    public function notifyBulkImport(User $rep, int $count, string $batchId, ?SalesLeadCategory $category = null): void
    {
        if ($count <= 0) {
            return;
        }

        $title = 'دفعة عملاء جديدة — '.$count.' عميل';
        $message = 'أُسندت إليك '.$count.' عميلاً محتملاً من الإدارة'
            .($category ? ' — التصنيف: '.$category->name : '')
            .'. يجب البدء بالمتابعة فوراً.';

        Notification::create([
            'user_id' => $rep->id,
            'sender_id' => auth()->id(),
            'title' => $title,
            'message' => $message,
            'type' => 'warning',
            'priority' => 'high',
            'audience' => 'employee',
            'action_url' => route('employee.sales.leads.index', ['import_batch' => $batchId]),
            'action_text' => 'عرض الدفعة',
            'data' => [
                'kind' => 'sales_bulk_import',
                'count' => $count,
                'import_batch' => $batchId,
                'category_id' => $category?->id,
            ],
        ]);
    }

    public function notifyDailyReportPenalty(User $rep, EmployeeSalaryDeduction $deduction, string $dateLabel): void
    {
        $title = 'غرامة — تقرير يومي مبيعات لم يُرسل';
        $message = 'لم تُسلّم التقرير اليومي لتاريخ '.$dateLabel
            .'. تم تسجيل خصم بقيمة '.number_format((float) $deduction->amount, 2).' ج.م.';

        $this->sendOnceToday(
            (int) $rep->id,
            $title,
            route('employee.sales.penalties.index'),
            fn () => Notification::create([
                'user_id' => $rep->id,
                'sender_id' => null,
                'title' => $title,
                'message' => $message,
                'type' => 'warning',
                'priority' => 'high',
                'audience' => 'employee',
                'action_url' => route('employee.sales.penalties.index'),
                'action_text' => 'خصوماتي',
                'data' => [
                    'kind' => 'sales_daily_report_penalty',
                    'deduction_id' => $deduction->id,
                    'amount' => (float) $deduction->amount,
                    'date' => $dateLabel,
                ],
            ])
        );
    }

    public function notifyDailyKpiPenalty(User $rep, EmployeeSalaryDeduction $deduction, string $dateLabel, string $metricLabel): void
    {
        $title = 'غرامة KPI يومي — '.$metricLabel;
        $message = 'لم يُحقَّق هدف «'.$metricLabel.'» بتاريخ '.$dateLabel
            .'. خصم '.number_format((float) $deduction->amount, 2).' ج.م. راجع نشاطك في الـ CRM.';

        $this->sendOnceToday(
            (int) $rep->id,
            $title.' '.$dateLabel,
            route('employee.sales.penalties.index'),
            fn () => Notification::create([
                'user_id' => $rep->id,
                'sender_id' => null,
                'title' => $title,
                'message' => $message,
                'type' => 'warning',
                'priority' => 'high',
                'audience' => 'employee',
                'action_url' => route('employee.sales.penalties.index'),
                'action_text' => 'خصوماتي',
                'data' => [
                    'kind' => 'sales_daily_kpi_penalty',
                    'deduction_id' => $deduction->id,
                    'amount' => (float) $deduction->amount,
                    'date' => $dateLabel,
                    'metric' => $metricLabel,
                ],
            ])
        );
    }

    public function notifyCommissionPaid(User $rep, SalesLead $lead, Transaction $txn): void
    {
        Notification::create([
            'user_id' => $rep->id,
            'sender_id' => auth()->id(),
            'title' => 'تم صرف عمولة مبيعات',
            'message' => 'عمولة '.number_format((float) $txn->amount, 2).' ج.م للعميل «'.$lead->name.'».',
            'type' => 'employee',
            'priority' => 'normal',
            'audience' => 'employee',
            'action_url' => route('employee.sales.leads.show', $lead),
            'action_text' => 'عرض العميل',
            'data' => [
                'kind' => 'sales_commission_paid',
                'lead_id' => $lead->id,
                'transaction_id' => $txn->id,
                'amount' => (float) $txn->amount,
            ],
        ]);
    }

    public function notifyWinPendingApproval(SalesLead $lead): void
    {
        $lead->loadMissing(['assignee']);
        $rep = $lead->assignee;
        $repName = $rep?->name ?? 'موظف';
        $value = number_format((float) ($lead->expected_value ?? 0), 2);
        $estCommission = $rep
            ? number_format($rep->calculateSalesCommissionAmount((float) ($lead->expected_value ?? 0)), 2)
            : '0.00';

        if ($rep) {
            Notification::create([
                'user_id' => $rep->id,
                'sender_id' => auth()->id(),
                'title' => 'طلب اعتماد فوز — في انتظار الإدارة',
                'message' => 'تم تسجيل فوز «'.$lead->name.'» بقيمة '.$value.' ج.م — الكوميشن المقدّر '.$estCommission.' ج.م بعد موافقة الإدارة.',
                'type' => 'employee',
                'priority' => 'normal',
                'audience' => 'employee',
                'action_url' => route('employee.sales.leads.show', $lead),
                'action_text' => 'عرض العميل',
                'data' => ['kind' => 'sales_win_pending', 'lead_id' => $lead->id],
            ]);
        }

        $title = 'طلب اعتماد صفقة Win';
        $message = "الموظف {$repName} — العميل «{$lead->name}» — قيمة {$value} ج.م — كوميشن مقدّر {$estCommission} ج.م";

        $admins = User::query()->whereIn('role', ['admin', 'super_admin'])->where('is_active', true)->get();
        foreach ($admins as $admin) {
            $this->sendOnceToday(
                (int) $admin->id,
                $title.' #'.$lead->id,
                route('admin.sales.win-approvals.index'),
                fn () => Notification::create([
                    'user_id' => $admin->id,
                    'sender_id' => $rep?->id,
                    'title' => $title,
                    'message' => $message,
                    'type' => 'warning',
                    'priority' => 'high',
                    'audience' => 'employee',
                    'action_url' => route('admin.sales.win-approvals.index'),
                    'action_text' => 'مراجعة الطلبات',
                    'data' => [
                        'kind' => 'sales_win_pending_admin',
                        'lead_id' => $lead->id,
                        'rep_id' => $rep?->id,
                    ],
                ])
            );
        }
    }

    public function notifyWinRejected(User $rep, SalesLead $lead, string $reason): void
    {
        Notification::create([
            'user_id' => $rep->id,
            'sender_id' => auth()->id(),
            'title' => 'تم رفض اعتماد الفوز',
            'message' => 'صفقة «'.$lead->name.'» — '.$reason,
            'type' => 'warning',
            'priority' => 'high',
            'audience' => 'employee',
            'action_url' => route('employee.sales.leads.show', $lead),
            'action_text' => 'عرض العميل',
            'data' => ['kind' => 'sales_win_rejected', 'lead_id' => $lead->id],
        ]);
    }

    public function notifyKpiAlert(User $rep, float $composite, array $flags, bool $critical = false): void
    {
        $title = $critical ? 'تنبيه حرج — أداء المبيعات' : 'تنبيه — أداء المبيعات';
        $message = 'مؤشرك المركّب للشهر: '.number_format($composite, 1).'%'
            .($flags ? ' — '.implode(' | ', array_slice($flags, 0, 2)) : '');

        $this->sendOnceToday(
            (int) $rep->id,
            $title,
            route('employee.sales.kpi.index'),
            fn () => Notification::create([
                'user_id' => $rep->id,
                'sender_id' => null,
                'title' => $title,
                'message' => $message,
                'type' => $critical ? 'warning' : 'reminder',
                'priority' => $critical ? 'urgent' : 'high',
                'audience' => 'employee',
                'action_url' => route('employee.sales.kpi.index'),
                'action_text' => 'مراجعة KPIs',
                'data' => [
                    'kind' => 'sales_kpi_alert',
                    'composite' => $composite,
                    'flags' => $flags,
                    'critical' => $critical,
                ],
            ])
        );
    }

    public function notifyImportBatchStale(User $rep, int $count, ?string $batchId, int $days): void
    {
        if ($count <= 0) {
            return;
        }

        $title = 'تنبيه — دفعة مستوردة بلا تواصل';
        $message = 'لديك '.$count.' عميلاً من دفعة استيراد لم يُسجَّل لهم تواصل خلال '.$days.' يوماً. ابدأ المتابعة فوراً.';

        $params = $batchId ? ['import_batch' => $batchId] : [];

        $this->sendOnceToday(
            (int) $rep->id,
            $title,
            route('employee.sales.leads.index', $params),
            fn () => Notification::create([
                'user_id' => $rep->id,
                'sender_id' => null,
                'title' => $title,
                'message' => $message,
                'type' => 'warning',
                'priority' => 'urgent',
                'audience' => 'employee',
                'action_url' => route('employee.sales.leads.index', $params),
                'action_text' => 'عرض الدفعة',
                'data' => [
                    'kind' => 'sales_import_stale',
                    'count' => $count,
                    'import_batch' => $batchId,
                    'days' => $days,
                ],
            ])
        );
    }

    public function notifyDataTransferred(User $fromRep, User $toRep, array $summary): void
    {
        $this->notifyDataTransferredMulti($fromRep, collect([$toRep]), $summary);
    }

    /**
     * @param  Collection<int, User>|iterable<int, User>  $toReps
     * @param  array<string, mixed>  $summary
     */
    public function notifyDataTransferredMulti(User $fromRep, iterable $toReps, array $summary): void
    {
        $toReps = collect($toReps)->filter()->values();
        if ($toReps->isEmpty()) {
            return;
        }

        $totalLeads = (int) ($summary['leads_assigned'] ?? 0);
        $perRep = $summary['per_rep'] ?? [];
        $names = $toReps->pluck('name')->implode('، ');

        foreach ($toReps as $toRep) {
            $leadsCount = (int) ($perRep[$toRep->id]['leads'] ?? ($toReps->count() === 1 ? $totalLeads : 0));

            Notification::create([
                'user_id' => $toRep->id,
                'sender_id' => auth()->id(),
                'title' => 'بيانات مبيعات منقولة إليك',
                'message' => 'نُقلت إليك '.$leadsCount.' عميلاً محتملاً من «'.$fromRep->name.'»'
                    .($toReps->count() > 1 ? ' (توزيع بين عدة موظفين).' : '. راجع قائمتك وابدأ المتابعة.'),
                'type' => 'employee',
                'priority' => 'high',
                'audience' => 'employee',
                'action_url' => route('employee.sales.leads.index'),
                'action_text' => 'عرض العملاء',
                'data' => [
                    'kind' => 'sales_data_transferred_in',
                    'from_user_id' => $fromRep->id,
                    'summary' => $summary,
                    'my_leads' => $leadsCount,
                ],
            ]);
        }

        Notification::create([
            'user_id' => $fromRep->id,
            'sender_id' => auth()->id(),
            'title' => 'تم نقل بيانات مبيعاتك',
            'message' => 'نُقلت بياناتك ('.$totalLeads.' عميل) إلى: '.$names.' بقرار من الإدارة.',
            'type' => 'employee',
            'priority' => 'normal',
            'audience' => 'employee',
            'action_url' => route('employee.sales.dashboard'),
            'action_text' => 'مركز المبيعات',
            'data' => [
                'kind' => 'sales_data_transferred_out',
                'to_user_ids' => $toReps->pluck('id')->all(),
                'summary' => $summary,
            ],
        ]);
    }

    public function notifyDailyReportReminder(User $rep): void
    {
        $title = 'تذكير: التقرير اليومي للمبيعات';
        $settings = SalesDailyReportSettings::all();
        $deadline = (string) ($settings['deadline_time'] ?? '23:59');
        $message = 'لم تُسلّم تقرير اليوم بعد. آخر موعد للتسليم: '.$deadline
            .' — يُطبَّق خصم تلقائي عند التأخير.';

        $actionUrl = route('employee.sales.daily-reports.edit');

        $this->sendOnceToday(
            (int) $rep->id,
            $title,
            $actionUrl,
            fn () => Notification::create([
                'user_id' => $rep->id,
                'sender_id' => null,
                'title' => $title,
                'message' => $message,
                'type' => 'reminder',
                'priority' => 'high',
                'audience' => 'employee',
                'action_url' => $actionUrl,
                'action_text' => 'إرسال التقرير الآن',
                'data' => ['kind' => 'sales_daily_report_reminder'],
            ])
        );

        if ($rep->email) {
            $this->sendReminderEmailOnceToday(
                $rep,
                $title,
                $message.' راجع التقرير المعبّأ تلقائياً من نشاطك ثم سلّمه.',
                $actionUrl,
                'تسليم التقرير الآن'
            );
        }
    }

    /**
     * @param  array{status: string, status_label: string, overall_pct: float, lines: list<array<string, mixed>>}  $comparison
     */
    public function notifyDailyReportSubmitted(User $rep, SalesDailyReport $report, array $comparison): void
    {
        $dateLabel = $report->report_date->format('Y-m-d');
        $status = $comparison['status'] ?? 'behind';
        $overall = $comparison['overall_pct'] ?? 0;
        $statusLabel = $comparison['status_label'] ?? '';

        Notification::create([
            'user_id' => $rep->id,
            'sender_id' => null,
            'title' => 'تم تسليم التقرير اليومي',
            'message' => "تقرير {$dateLabel} — {$statusLabel} ({$overall}% من أهداف KPI اليوم).",
            'type' => $status === 'met' ? 'success' : 'warning',
            'priority' => 'normal',
            'audience' => 'employee',
            'action_url' => route('employee.sales.daily-reports.index', ['date' => $dateLabel]),
            'action_text' => 'عرض التقرير',
            'data' => ['kind' => 'sales_daily_report_submitted', 'overall_pct' => $overall, 'status' => $status],
        ]);

        if ($status !== 'behind') {
            return;
        }

        $title = 'تقرير يومي — أداء أقل من هدف KPI';
        $message = "الموظف {$rep->name} — تقرير {$dateLabel} — تحقيق {$overall}% من أهداف اليوم.";

        $admins = User::query()->whereIn('role', ['admin', 'super_admin'])->where('is_active', true)->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'sender_id' => $rep->id,
                'title' => $title,
                'message' => $message,
                'type' => 'warning',
                'priority' => 'high',
                'audience' => 'employee',
                'action_url' => route('admin.sales.daily-reports.show', $report->id),
                'action_text' => 'عرض التقرير',
                'data' => [
                    'kind' => 'sales_daily_report_kpi_behind',
                    'report_id' => $report->id,
                    'rep_id' => $rep->id,
                    'overall_pct' => $overall,
                ],
            ]);
        }
    }

    private function sendReminderEmailOnceToday(User $rep, string $title, string $message, string $actionUrl, string $actionText): void
    {
        $cacheKey = 'sales_daily_report_email_reminder_'.today()->format('Y-m-d').'_'.$rep->id;
        if (cache()->has($cacheKey)) {
            return;
        }

        try {
            Mail::to($rep->email)->send(new PlatformNotificationMail($title, $message, $actionUrl, $actionText));
            cache()->put($cacheKey, true, now()->endOfDay());
        } catch (\Throwable) {
            // لا نوقف التذكير داخل المنصة إذا فشل البريد
        }
    }

    public function notifyWhatsAppInboundMessage(User $rep, WhatsAppConversation $conversation, WhatsAppConversationMessage $message): void
    {
        if (! $rep->isSalesStaff()) {
            return;
        }

        if (Notification::query()
            ->where('user_id', $rep->id)
            ->where('data->kind', 'whatsapp_inbound')
            ->where('data->message_id', $message->id)
            ->exists()) {
            return;
        }

        $inboxUrl = app(WhatsAppQueueService::class)->inboxUrlFor($rep, $conversation);
        $name = $conversation->displayName();
        $preview = mb_substr($message->displayBody() ?: 'رسالة جديدة', 0, 120);

        Notification::create([
            'user_id' => $rep->id,
            'sender_id' => null,
            'title' => 'رسالة واتساب جديدة',
            'message' => 'من «'.$name.'»: '.$preview,
            'type' => 'employee',
            'priority' => 'high',
            'audience' => 'employee',
            'action_url' => $inboxUrl,
            'action_text' => 'فتح المحادثة',
            'data' => [
                'kind' => 'whatsapp_inbound',
                'conversation_id' => $conversation->id,
                'message_id' => $message->id,
            ],
        ]);
    }

    public function notifyWhatsAppQueueRequest(WhatsAppConversation $conversation, WhatsAppConversationMessage $message): void
    {
        $managers = app(WhatsAppAssignmentService::class)->eligibleSalesManagers();
        $queueUrl = route('employee.sales-manager.whatsapp.queue.index');
        $name = $conversation->displayName();
        $preview = mb_substr($message->displayBody() ?: 'رسالة جديدة', 0, 120);

        foreach ($managers as $manager) {
            $recent = Notification::query()
                ->where('user_id', $manager->id)
                ->where('data->kind', 'whatsapp_queue')
                ->where('data->conversation_id', $conversation->id)
                ->where('created_at', '>=', now()->subMinutes(30))
                ->exists();

            if ($recent) {
                continue;
            }

            Notification::create([
                'user_id' => $manager->id,
                'sender_id' => null,
                'title' => 'طلب واتساب جديد — للتوزيع',
                'message' => '«'.$name.'» — '.$preview,
                'type' => 'employee',
                'priority' => 'high',
                'audience' => 'employee',
                'action_url' => $queueUrl,
                'action_text' => 'توزيع الطلب',
                'data' => [
                    'kind' => 'whatsapp_queue',
                    'conversation_id' => $conversation->id,
                    'message_id' => $message->id,
                ],
            ]);
        }
    }

    private function sendOnceToday(int $userId, string $title, string $actionUrl, callable $create): void
    {
        $exists = Notification::query()
            ->where('user_id', $userId)
            ->where('title', $title)
            ->where('action_url', $actionUrl)
            ->whereDate('created_at', today())
            ->exists();

        if (! $exists) {
            $create();
        }
    }
}
