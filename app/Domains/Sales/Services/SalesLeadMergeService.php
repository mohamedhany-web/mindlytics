<?php

namespace App\Domains\Sales\Services;

use App\Domains\Sales\Models\SalesActivity;
use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Support\CrmScope;
use App\Domains\Sales\Support\PhoneKey;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

/**
 * دمج عميلين مكررين (docs/SALES_CRM.md): كل اللي مربوط بالمكرر بيتنقل للعميل اللي هيفضل، والخانات الفاضية بتتكمل منه،
 * والمكرر بيتمسح soft-delete ومتعلّم عليه اتدمج في مين. مش بيدمج صفقة معتمدة ولا عميلين كل واحد مربوط بطلب مختلف.
 */
class SalesLeadMergeService
{
    /** خانات ما بتتنقلش من المكرر (هوية العميل اللي هيفضل ومرحلته وصاحبه وفلوسه). */
    private const KEEP = ['id', 'name', 'phone', 'stage', 'assigned_to', 'created_by', 'created_at', 'updated_at', 'deleted_at', 'merged_into_id',
        'stage_entered_at', 'won_confirmed_at', 'won_confirmed_by', 'commission_amount', 'commission_transaction_id', 'commission_notes', 'commission_settled_at', 'closed_at'];

    /** عملاء ممكن يكونوا نفس الشخص: نفس الرقم بأي صيغة، أو نفس الإيميل، أو اسم شبهه. */
    public function candidates(SalesLead $lead, CrmScope $scope, ?string $search = null, int $limit = 15): Collection
    {
        $query = $scope->leads()->whereKeyNot($lead->id);
        $search = trim((string) $search);

        if ($search !== '') {
            $tail = PhoneKey::likeTail($search);
            $query->where(function ($q) use ($search, $tail) {
                $q->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%');
                if ($tail) {
                    $q->orWhere('phone', 'like', $tail);
                }
                if (ctype_digit($search)) {
                    $q->orWhere('id', (int) $search);
                }
            });

            return $query->with('assignee:id,name')->latest('id')->limit($limit)->get();
        }

        $tail = PhoneKey::likeTail($lead->phone);
        $email = filled($lead->email) ? mb_strtolower(trim((string) $lead->email)) : null;
        $first = trim((string) strtok((string) $lead->name, ' '));
        $query->where(function ($q) use ($tail, $email, $lead, $first) {
            $q->whereRaw('1 = 0');
            if ($tail) {
                $q->orWhere('phone', 'like', $tail);
            }
            if ($email) {
                $q->orWhereRaw('LOWER(TRIM(email)) = ?', [$email]);
            }
            if (mb_strlen((string) $lead->name) >= 4) {
                $q->orWhere('name', $lead->name);
                if (mb_strlen($first) >= 3) {
                    $q->orWhere('name', 'like', $first.'%');
                }
            }
        });

        return $query->with('assignee:id,name')->latest('id')->limit(50)->get()
            ->sortByDesc(fn (SalesLead $c) => (PhoneKey::same($c->phone, $lead->phone) ? 4 : 0)
                + ($email && mb_strtolower(trim((string) $c->email)) === $email ? 3 : 0)
                + ($c->name === $lead->name ? 2 : 0))
            ->take($limit)->values();
    }

    /** ممكن المستخدم ده يدمج العميلين دول؟ (null = أيوه، وإلا سبب الرفض) */
    public function blockReason(SalesLead $keep, SalesLead $dupe, CrmScope $scope): ?string
    {
        if ($keep->id === $dupe->id) {
            return 'اختار عميل تاني غير نفس العميل.';
        }
        if (! $scope->canSee($keep) || ! $scope->canSee($dupe)) {
            return 'العميلين لازم يكونوا ضمن عملائك.';
        }
        if ($scope->isRep() && ((int) $keep->assigned_to !== (int) $scope->user->id || (int) $dupe->assigned_to !== (int) $scope->user->id)) {
            return 'تقدر تدمج عملاءك إنت بس. لو المكرر عند موظف تاني اطلب من المدير.';
        }
        if ($dupe->won_confirmed_at || $dupe->commission_transaction_id) {
            return 'العميل المكرر عليه صفقة معتمدة وعمولة، فما ينفعش يتدمج في غيره. خلّيه هو اللي يفضل.';
        }
        if ($keep->order_id && $dupe->order_id && (int) $keep->order_id !== (int) $dupe->order_id) {
            return 'كل عميل مربوط بطلب مدفوع مختلف، فدول غالباً شخصين مختلفين.';
        }

        return null;
    }

    /** @return array<string, int> عدد السجلات المربوطة بالعميل في كل جدول (للعرض قبل الدمج) */
    public function linkedCounts(SalesLead $lead): array
    {
        $out = [];
        foreach ($this->leadTables() as $table) {
            $n = DB::table($table)->where('sales_lead_id', $lead->id)->count();
            if ($n > 0) {
                $out[$table] = $n;
            }
        }
        $docs = Schema::hasTable('crm_documents') ? DB::table('crm_documents')->whereIn('parent_type', $this->leadMorphTypes())->where('parent_id', $lead->id)->count() : 0;
        if ($docs > 0) {
            $out['crm_documents'] = $docs;
        }

        return $out;
    }

    public function merge(SalesLead $keep, SalesLead $dupe, CrmScope $scope, User $actor): SalesLead
    {
        if ($reason = $this->blockReason($keep, $dupe, $scope)) {
            throw ValidationException::withMessages(['merge' => $reason]);
        }

        return DB::transaction(function () use ($keep, $dupe, $actor) {
            $keep = SalesLead::query()->lockForUpdate()->findOrFail($keep->id);
            $dupe = SalesLead::query()->lockForUpdate()->findOrFail($dupe->id);
            $moved = $this->linkedCounts($dupe);

            // الخانات الفاضية في العميل اللي هيفضل بتتكمل من المكرر
            $fill = [];
            foreach ($dupe->getAttributes() as $column => $value) {
                if (in_array($column, self::KEEP, true) || $value === null || $value === '' || ! $keep->isFillable($column)) {
                    continue;
                }
                $current = $keep->getAttribute($column);
                if ($current === null || $current === '') {
                    $fill[$column] = $value;
                }
            }
            if (filled($dupe->notes) && filled($keep->notes) && $dupe->notes !== $keep->notes) {
                $fill['notes'] = trim($keep->notes."\n— من العميل المدموج: ".$dupe->notes);
            }
            if ($fill !== []) {
                $keep->forceFill($fill)->save();
            }

            // كل المربوط بالمكرر يتنقل (الصفوف اللي بتتعارض مع قيد unique بتفضل مكانها من غير ما تضيع)
            foreach ($this->leadTables() as $table) {
                try {
                    DB::table($table)->where('sales_lead_id', $dupe->id)->update(['sales_lead_id' => $keep->id]);
                } catch (QueryException) {
                    foreach (DB::table($table)->where('sales_lead_id', $dupe->id)->pluck('id') as $rowId) {
                        try {
                            DB::table($table)->where('id', $rowId)->update(['sales_lead_id' => $keep->id]);
                        } catch (QueryException) {
                        }
                    }
                }
            }
            if (Schema::hasTable('crm_documents')) {
                DB::table('crm_documents')->whereIn('parent_type', $this->leadMorphTypes())->where('parent_id', $dupe->id)->update(['parent_id' => $keep->id]);
            }

            $dupe->forceFill(['merged_into_id' => $keep->id])->save();
            $dupe->delete();

            SalesActivity::create([
                'sales_lead_id' => $keep->id,
                'user_id' => $actor->id,
                'type' => 'note',
                'title' => 'دمج عميل مكرر',
                'body' => 'اتدمج فيه «'.$dupe->name.'» ('.$dupe->phone.', #'.$dupe->id.')، واتنقل معاه: '.($moved === [] ? 'مفيش سجلات' : collect($moved)->map(fn ($n, $t) => self::label($t).' '.$n)->implode('، ')).'.',
            ]);
            SalesAuditService::log('sales_lead_merged', $keep, ['merged_lead_id' => $dupe->id], ['moved' => $moved, 'filled' => array_keys($fill)],
                'دمج العميل #'.$dupe->id.' في #'.$keep->id.' — '.$keep->name);

            return $keep->fresh();
        });
    }

    public static function label(string $table): string
    {
        return [
            'sales_activities' => 'أنشطة', 'sales_planned_activities' => 'أنشطة مخططة', 'sales_lead_transfers' => 'تحويلات', 'crm_cases' => 'تذاكر',
            'crm_contacts' => 'جهات اتصال', 'crm_documents' => 'مستندات', 'whatsapp_conversations' => 'محادثات واتساب', 'meta_social_conversations' => 'محادثات ماسنجر/إنستجرام',
            'advising_bookings' => 'حجوزات إرشاد', 'sales_daily_report_contacts' => 'مكالمات في تقارير يومية', 'workshop_registrations' => 'تسجيلات ورش',
        ][$table] ?? $table;
    }

    /** نوع العميل في الأعمدة polymorphic (*_type): الـ alias، والاسم الكامل اللي اتخزن قبل كده. */
    private function leadMorphTypes(): array
    {
        return array_values(array_unique([Relation::getMorphAlias(SalesLead::class), (new SalesLead)->getMorphClass(), ltrim(SalesLead::class, '\\')]));
    }

    /** كل جدول فيه sales_lead_id (عشان أي جدول جديد يتنقل تلقائي). */
    private function leadTables(): array
    {
        static $tables = null;
        if ($tables !== null) {
            return $tables;
        }
        $db = DB::connection()->getDatabaseName();
        $tables = collect(Schema::getTables())
            ->filter(fn ($t) => ! isset($t['schema']) || $t['schema'] === null || $t['schema'] === $db || $t['schema'] === 'main')
            ->pluck('name')->unique()
            ->filter(fn ($t) => $t !== 'sales_leads' && Schema::hasColumn($t, 'sales_lead_id'))
            ->values()->all();

        return $tables;
    }
}
