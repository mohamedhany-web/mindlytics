<?php

/**
 * اختبار يدوي: إعادة إرسال الرسائل الفاشلة فقط دون المساس بالمرسلة.
 *
 * الاستخدام: php scripts/test-whatsapp-batch-retry.php
 */

use App\Models\User;
use App\Models\WhatsAppBatch;
use App\Models\WhatsAppBatchItem;
use App\Services\WhatsAppBatchService;
use Illuminate\Support\Facades\Bus;

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

if (! WhatsAppBatchService::isReady()) {
    fwrite(STDERR, "جداول الواتساب غير موجودة — نفّذ php artisan migrate\n");
    exit(1);
}

Bus::fake();

$userId = User::query()->value('id');
if (! $userId) {
    fwrite(STDERR, "لا يوجد مستخدم في قاعدة البيانات.\n");
    exit(1);
}

$service = app(WhatsAppBatchService::class);

$batch = WhatsAppBatch::create([
    'title' => 'اختبار إعادة فاشلة فقط #' . now()->format('His'),
    'source_type' => 'admin_bulk',
    'status' => 'completed',
    'total_count' => 3,
    'sent_count' => 1,
    'failed_count' => 2,
    'created_by' => $userId,
    'completed_at' => now(),
]);

foreach ([
    ['name' => 'أحمد (نجح)', 'phone' => '201111111101', 'status' => 'sent', 'error' => null],
    ['name' => 'سارة (فشل)', 'phone' => '201111111102', 'status' => 'failed', 'error' => 'غير متصل'],
    ['name' => 'محمد (فشل)', 'phone' => '201111111103', 'status' => 'failed', 'error' => 'timeout'],
] as $i => $row) {
    WhatsAppBatchItem::create([
        'batch_id' => $batch->id,
        'recipient_name' => $row['name'],
        'phone' => $row['phone'],
        'message' => 'رسالة اختبار',
        'status' => $row['status'],
        'error_message' => $row['error'],
        'send_attempts' => $row['status'] === 'failed' ? 2 : 0,
        'sort_order' => $i,
        'sent_at' => $row['status'] === 'sent' ? now() : null,
    ]);
}

echo "قبل إعادة الإرسال:\n";
echo "  دفعة #{$batch->id} — مرسلة: {$batch->sent_count}, فاشلة: {$batch->failed_count}\n";

$result = $service->retryBatch($batch->fresh());
$batch->refresh();

echo "\nبعد retryBatch:\n";
echo "  أُعيدت فاشلة: {$result['failed_reset']}\n";
echo "  حالة الدفعة: {$batch->status}\n";
echo "  مرسلة: {$batch->sent_count}, فاشلة: {$batch->failed_count}, معلّقة: {$batch->pendingCount()}\n";

$statuses = $batch->items()->orderBy('sort_order')->pluck('status', 'recipient_name');
foreach ($statuses as $name => $status) {
    echo "  - {$name}: {$status}\n";
}

$stillSent = $batch->items()->where('status', 'sent')->count();
$nowPending = $batch->items()->where('status', 'pending')->count();

if ($stillSent === 1 && $nowPending === 2 && $batch->sent_count === 1) {
    echo "\n✓ نجح الاختبار: المرسلة بقيت مرسلة، الفاشلتان أصبحتا معلّقتين فقط.\n";
    echo "  افتح: /admin/whatsapp/batches/{$batch->id}?filter=failed (بعد إعادة فشل يدوي للاختبار)\n";
    exit(0);
}

echo "\n✗ فشل الاختبار — راجع الأرقام أعلاه.\n";
exit(1);
