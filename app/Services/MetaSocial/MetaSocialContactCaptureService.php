<?php

namespace App\Services\MetaSocial;

use App\Models\MetaSocialConversation;
use App\Models\SalesLead;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * Meta لا توفّر رقم الهاتف/الإيميل من Messenger/Instagram عبر Graph API
 * حتى للحسابات الموثّقة — نحفظ البيانات عندما يشاركها العميل في الرسالة
 * أو عبر Quick Reply لطلب الرقم (Messenger فقط).
 */
class MetaSocialContactCaptureService
{
    public static function bumpInboxVersion(): void
    {
        Cache::put('meta_social:inbox_version', (string) now()->timestamp.'_'.uniqid('', true), now()->addDays(7));
    }

    public static function inboxVersion(): string
    {
        return (string) Cache::get('meta_social:inbox_version', '0');
    }

    /**
     * @return array{phone: ?string, email: ?string}
     */
    public function extractFromText(string $text): array
    {
        $phone = $this->extractPhone($text);
        $email = $this->extractEmail($text);

        return ['phone' => $phone, 'email' => $email];
    }

    public function extractPhone(string $text): ?string
    {
        $text = trim($text);
        if ($text === '') {
            return null;
        }

        $patterns = [
            // +20 / 0020 / 20 ثم موبايل مصري
            '/(?:\+|00)?20[\s\-.]?0?1[0125](?:[\s\-.]?\d){8}\b/u',
            // 01xxxxxxxxx
            '/(?<!\d)01[0125](?:[\s\-.]?\d){8}\b/u',
            // دولي صريح
            '/\+(?:[1-9]\d{7,14})\b/u',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                return $this->normalizePhone($m[0]);
            }
        }

        return null;
    }

    public function extractEmail(string $text): ?string
    {
        if (preg_match('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/iu', $text, $m)) {
            return strtolower(trim($m[0]));
        }

        return null;
    }

    public function normalizePhone(string $raw): string
    {
        $raw = trim($raw);
        $digits = preg_replace('/\D+/', '', $raw) ?? '';

        if (str_starts_with($digits, '0020')) {
            $digits = substr($digits, 2);
        }
        if (str_starts_with($digits, '20') && strlen($digits) >= 12) {
            return '+'.$digits;
        }
        if (str_starts_with($digits, '01') && strlen($digits) === 11) {
            return '+20'.substr($digits, 1);
        }
        if (str_starts_with($digits, '1') && strlen($digits) === 10) {
            return '+20'.$digits;
        }
        if (str_starts_with($raw, '+')) {
            return '+'.$digits;
        }

        return $digits !== '' ? $digits : $raw;
    }

    /**
     * @return array{phone?: string, email?: string}
     */
    public function captureFromInboundText(MetaSocialConversation $conversation, string $text): array
    {
        if (! Schema::hasColumn('meta_social_conversations', 'phone')) {
            return [];
        }

        $extracted = $this->extractFromText($text);
        $updates = [];

        if ($extracted['phone'] && $this->shouldStorePhone($conversation->phone, $extracted['phone'])) {
            $updates['phone'] = $extracted['phone'];
        }
        if ($extracted['email'] && blank($conversation->email)) {
            $updates['email'] = $extracted['email'];
        }

        if ($updates === []) {
            return [];
        }

        $conversation->update($updates);
        $this->syncCapturedToLead($conversation->fresh(), $updates);
        self::bumpInboxVersion();

        return $updates;
    }

    public function scanConversationHistory(MetaSocialConversation $conversation, int $limit = 80): array
    {
        if (! Schema::hasColumn('meta_social_conversations', 'phone')) {
            return [];
        }

        $captured = [];
        $messages = $conversation->messages()
            ->where('direction', 'inbound')
            ->whereNotNull('body')
            ->orderByDesc('id')
            ->limit($limit)
            ->get(['id', 'body']);

        foreach ($messages as $message) {
            $got = $this->captureFromInboundText($conversation->fresh(), (string) $message->body);
            if ($got !== []) {
                $captured = array_merge($captured, $got);
                $conversation->refresh();
                if (filled($conversation->phone) && filled($conversation->email)) {
                    break;
                }
            }
        }

        return $captured;
    }

    /**
     * @param  array{phone?: string, email?: string}  $updates
     */
    private function syncCapturedToLead(MetaSocialConversation $conversation, array $updates): void
    {
        if (! $conversation->sales_lead_id || ! Schema::hasTable('sales_leads')) {
            // محاولة ربط Lead موجود بنفس الرقم
            if (! empty($updates['phone'])) {
                $lead = SalesLead::query()
                    ->where(function ($q) use ($updates) {
                        $digits = preg_replace('/\D+/', '', $updates['phone']);
                        $q->where('phone', $updates['phone']);
                        if ($digits) {
                            $q->orWhere('phone', 'like', '%'.$digits.'%');
                        }
                    })
                    ->orderByDesc('updated_at')
                    ->first();
                if ($lead && ! $conversation->sales_lead_id) {
                    $conversation->update(['sales_lead_id' => $lead->id]);
                }
            }

            return;
        }

        $lead = SalesLead::query()->find($conversation->sales_lead_id);
        if (! $lead) {
            return;
        }

        $leadUpdates = [];
        if (! empty($updates['phone']) && $this->shouldStorePhone($lead->phone, $updates['phone'])) {
            $leadUpdates['phone'] = $updates['phone'];
        }
        if (! empty($updates['email']) && blank($lead->email)) {
            $leadUpdates['email'] = $updates['email'];
        }
        if ($leadUpdates !== []) {
            $lead->update($leadUpdates);
        }
    }

    private function shouldStorePhone(?string $existing, string $new): bool
    {
        $existing = trim((string) $existing);
        if ($existing === '') {
            return true;
        }
        // استبدال placeholder الداخلي meta_messenger_xxx
        if (str_starts_with($existing, 'meta_')) {
            return true;
        }

        return false;
    }
}
