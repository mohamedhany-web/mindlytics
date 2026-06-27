<?php

namespace Tests\Unit;

use App\Jobs\ProcessWhatsAppBatchJob;
use App\Models\User;
use App\Models\WhatsAppBatch;
use App\Models\WhatsAppBatchItem;
use App\Services\WhatsAppBatchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class WhatsAppBatchRetryTest extends TestCase
{
    use RefreshDatabase;

    private WhatsAppBatchService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(WhatsAppBatchService::class);
    }

    public function test_retry_batch_resets_only_failed_items_not_sent(): void
    {
        Bus::fake();

        $user = User::factory()->create();
        $batch = WhatsAppBatch::create([
            'title' => 'اختبار إعادة الإرسال',
            'source_type' => 'admin_bulk',
            'source_id' => null,
            'status' => 'completed',
            'total_count' => 3,
            'sent_count' => 1,
            'failed_count' => 2,
            'created_by' => $user->id,
            'completed_at' => now(),
        ]);

        $sent = WhatsAppBatchItem::create([
            'batch_id' => $batch->id,
            'recipient_name' => 'نجح',
            'phone' => '201000000001',
            'message' => 'مرحباً',
            'status' => 'sent',
            'sort_order' => 0,
            'sent_at' => now(),
        ]);

        $failedA = WhatsAppBatchItem::create([
            'batch_id' => $batch->id,
            'recipient_name' => 'فشل 1',
            'phone' => '201000000002',
            'message' => 'مرحباً',
            'status' => 'failed',
            'error_message' => 'غير متصل',
            'send_attempts' => 3,
            'sort_order' => 1,
        ]);

        $failedB = WhatsAppBatchItem::create([
            'batch_id' => $batch->id,
            'recipient_name' => 'فشل 2',
            'phone' => '201000000003',
            'message' => 'مرحباً',
            'status' => 'failed',
            'error_message' => 'timeout',
            'send_attempts' => 2,
            'sort_order' => 2,
        ]);

        $result = $this->service->retryBatch($batch->fresh());

        $this->assertSame(2, $result['failed_reset']);
        $this->assertTrue($result['dispatched']);

        $batch->refresh();
        $this->assertSame('pending', $batch->status);
        $this->assertSame(0, $batch->failed_count);
        $this->assertSame(1, $batch->sent_count);
        $this->assertNull($batch->completed_at);

        $this->assertSame('sent', $sent->fresh()->status);
        $this->assertSame('pending', $failedA->fresh()->status);
        $this->assertSame('pending', $failedB->fresh()->status);
        $this->assertNull($failedA->fresh()->error_message);
        $this->assertSame(0, $failedA->fresh()->send_attempts);

        Bus::assertDispatched(ProcessWhatsAppBatchJob::class, fn ($job) => $job->batchId === $batch->id);
    }

    public function test_retry_single_item_leaves_other_failed_unchanged(): void
    {
        Bus::fake();

        $user = User::factory()->create();
        $batch = WhatsAppBatch::create([
            'title' => 'اختبار رسالة واحدة',
            'source_type' => 'admin_bulk',
            'status' => 'completed',
            'total_count' => 2,
            'sent_count' => 0,
            'failed_count' => 2,
            'created_by' => $user->id,
            'completed_at' => now(),
        ]);

        $first = WhatsAppBatchItem::create([
            'batch_id' => $batch->id,
            'phone' => '201000000011',
            'message' => 'أ',
            'status' => 'failed',
            'error_message' => 'فشل',
            'sort_order' => 0,
        ]);

        $second = WhatsAppBatchItem::create([
            'batch_id' => $batch->id,
            'phone' => '201000000012',
            'message' => 'ب',
            'status' => 'failed',
            'error_message' => 'فشل',
            'sort_order' => 1,
        ]);

        $this->service->retryItem($batch, $first);

        $batch->refresh();
        $this->assertSame('pending', $batch->status);
        $this->assertSame(1, $batch->failed_count);
        $this->assertSame('pending', $first->fresh()->status);
        $this->assertSame('failed', $second->fresh()->status);

        Bus::assertDispatched(ProcessWhatsAppBatchJob::class);
    }

    public function test_cannot_retry_non_failed_item(): void
    {
        $user = User::factory()->create();
        $batch = WhatsAppBatch::create([
            'title' => 'اختبار',
            'source_type' => 'admin_bulk',
            'status' => 'completed',
            'total_count' => 1,
            'sent_count' => 1,
            'failed_count' => 0,
            'created_by' => $user->id,
        ]);

        $sent = WhatsAppBatchItem::create([
            'batch_id' => $batch->id,
            'phone' => '201000000099',
            'message' => 'مرحباً',
            'status' => 'sent',
            'sort_order' => 0,
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('يمكن إعادة إرسال الرسائل الفاشلة فقط');

        $this->service->retryItem($batch, $sent);
    }
}
