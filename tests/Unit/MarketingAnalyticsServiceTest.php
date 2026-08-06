<?php

namespace Tests\Unit;

use App\Services\MarketingAnalyticsService;
use Tests\TestCase;

class MarketingAnalyticsServiceTest extends TestCase
{
    public function test_item_from_course_array_uses_stable_id_and_category(): void
    {
        $service = new MarketingAnalyticsService;
        $item = $service->itemFromCourseArray([
            'id' => 42,
            'title' => 'Laravel Mastery',
            'price' => 199.5,
            'academic_subject' => ['name' => 'Backend'],
        ], 3);

        $this->assertSame('course:42', $item['item_id']);
        $this->assertSame('Laravel Mastery', $item['item_name']);
        $this->assertSame(199.5, $item['price']);
        $this->assertSame('online_course', $item['item_category']);
        $this->assertSame('Backend', $item['item_category2']);
        $this->assertSame(3, $item['index']);
        $this->assertSame('Mindlytics', $item['item_brand']);
    }

    public function test_purchase_payload_includes_transaction_and_items(): void
    {
        $service = new MarketingAnalyticsService;
        $items = [
            $service->normalizeItem([
                'item_id' => 'course:7',
                'item_name' => 'AI Course',
                'price' => 500,
                'quantity' => 1,
                'item_category' => 'online_course',
            ]),
        ];

        $payload = $service->purchasePayload('order:99', 500.0, $items, 'SAVE10');

        $this->assertSame('purchase', $payload['event']);
        $this->assertSame('order:99', $payload['ecommerce']['transaction_id']);
        $this->assertSame(500.0, $payload['ecommerce']['value']);
        $this->assertSame('EGP', $payload['ecommerce']['currency']);
        $this->assertSame('SAVE10', $payload['ecommerce']['coupon']);
        $this->assertCount(1, $payload['ecommerce']['items']);
        $this->assertSame('course:7', $payload['ecommerce']['items'][0]['item_id']);
    }

    public function test_view_item_list_and_begin_checkout_events(): void
    {
        $service = new MarketingAnalyticsService;
        $item = $service->itemFromCourseArray(['id' => 1, 'title' => 'A', 'price' => 10], 0);

        $list = $service->viewItemList([$item]);
        $this->assertSame('view_item_list', $list['event']);
        $this->assertSame('courses', $list['ecommerce']['item_list_id']);

        $checkout = $service->beginCheckout([$item], 10.0);
        $this->assertSame('begin_checkout', $checkout['event']);
        $this->assertSame(10.0, $checkout['ecommerce']['value']);
    }
}
