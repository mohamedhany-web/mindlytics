<?php

namespace App\Domains\Messaging\Console\Commands;

use App\Domains\Messaging\Services\MetaSocialInboxService;
use Illuminate\Console\Command;

/**
 * مزامنة تاريخ محادثات/رسائل Meta من السيرفر (مفيد لو الـ HTTP timeout قصير).
 */
class SyncMetaSocialHistoryCommand extends Command
{
    protected $signature = 'meta-social:sync-history
                            {--page= : معرف الصفحة المحلي (meta_social_pages.id)}
                            {--conversation-pages=50 : أقصى عدد صفحات pagination للمحادثات}';

    protected $description = 'جلب المحادثات والرسائل القديمة من Meta Graph إلى الإنبوكس';

    public function handle(MetaSocialInboxService $inbox): int
    {
        $pageId = $this->option('page') !== null && $this->option('page') !== ''
            ? (int) $this->option('page')
            : null;
        $maxPages = max(1, (int) $this->option('conversation-pages'));

        $this->info('جاري المزامنة من Meta…');

        $result = $inbox->syncHistoryFromMeta($pageId, $maxPages);

        if (! ($result['success'] ?? false)) {
            $this->error($result['error'] ?? 'فشلت المزامنة');
            foreach ($result['errors'] ?? [] as $err) {
                $this->warn(' - '.$err);
            }

            return self::FAILURE;
        }

        $this->info(sprintf(
            'تمت: %d صفحة · %d محادثة · %d رسالة جديدة',
            (int) ($result['pages'] ?? 0),
            (int) ($result['synced'] ?? 0),
            (int) ($result['messages'] ?? 0)
        ));

        foreach ($result['errors'] ?? [] as $err) {
            $this->warn('تحذير: '.$err);
        }

        return self::SUCCESS;
    }
}
