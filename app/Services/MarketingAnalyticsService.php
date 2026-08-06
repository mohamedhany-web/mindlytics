<?php

namespace App\Services;

use App\Models\AdvancedCourse;
use App\Models\OfflineCourse;
use App\Models\Order;
use App\Models\Package;
use App\Models\Workshop;
use App\Support\MarketingWebAnalyticsSettings;
use Illuminate\Support\Arr;

/**
 * Builds GA4 ecommerce payloads for dataLayer (via GTM).
 *
 * @see https://developers.google.com/analytics/devguides/collection/ga4/ecommerce
 */
class MarketingAnalyticsService
{
    public function enabled(): bool
    {
        return MarketingWebAnalyticsSettings::enabled();
    }

    public function currency(): string
    {
        return MarketingWebAnalyticsSettings::currency();
    }

    public function brand(): string
    {
        return MarketingWebAnalyticsSettings::itemBrand();
    }

    /**
     * @param  array<string, mixed>  $course  Array shape used on courses listing pages
     * @return array<string, mixed>
     */
    public function itemFromCourseArray(array $course, int $index = 0): array
    {
        $id = (int) ($course['id'] ?? 0);
        $price = (float) ($course['price'] ?? $course['original_price'] ?? 0);
        $category = (string) ($course['academic_subject']['name'] ?? 'online_course');

        return $this->normalizeItem([
            'item_id' => 'course:'.$id,
            'item_name' => (string) ($course['title'] ?? 'Course #'.$id),
            'price' => $price,
            'quantity' => 1,
            'index' => $index,
            'item_category' => 'online_course',
            'item_category2' => $category,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function itemFromCourse(AdvancedCourse $course, int $index = 0, ?float $priceOverride = null): array
    {
        $course->loadMissing('academicSubject');

        return $this->normalizeItem([
            'item_id' => 'course:'.$course->id,
            'item_name' => $course->localized('title') ?: ('Course #'.$course->id),
            'price' => $priceOverride ?? $course->effectivePrice(),
            'quantity' => 1,
            'index' => $index,
            'item_category' => 'online_course',
            'item_category2' => $course->academicSubject?->name,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function itemFromOfflineCourse(OfflineCourse $course, int $index = 0): array
    {
        return $this->normalizeItem([
            'item_id' => 'offline_course:'.$course->id,
            'item_name' => (string) ($course->title ?? 'Offline #'.$course->id),
            'price' => (float) ($course->price ?? 0),
            'quantity' => 1,
            'index' => $index,
            'item_category' => 'offline_course',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function itemFromPackage(Package $package, int $index = 0): array
    {
        return $this->normalizeItem([
            'item_id' => 'package:'.$package->id,
            'item_name' => (string) ($package->name ?? 'Package #'.$package->id),
            'price' => (float) ($package->price ?? 0),
            'quantity' => 1,
            'index' => $index,
            'item_category' => 'package',
        ]);
    }

    /**
     * Workshops are registration-led (no list price on the model).
     *
     * @return array<string, mixed>
     */
    public function itemFromWorkshop(Workshop $workshop, int $index = 0, float $price = 0): array
    {
        return $this->normalizeItem([
            'item_id' => 'workshop:'.$workshop->id,
            'item_name' => (string) ($workshop->title ?? 'Workshop #'.$workshop->id),
            'price' => $price,
            'quantity' => 1,
            'index' => $index,
            'item_category' => 'workshop',
        ]);
    }

    /**
     * @param  list<array<string, mixed>>  $items
     * @return array{event: string, ecommerce: array<string, mixed>}
     */
    public function viewItemList(array $items, string $listId = 'courses', string $listName = 'Courses'): array
    {
        $items = array_values(array_map(fn ($item, $i) => array_merge($item, [
            'item_list_id' => $listId,
            'item_list_name' => $listName,
            'index' => $item['index'] ?? $i,
        ]), $items, array_keys($items)));

        return [
            'event' => 'view_item_list',
            'ecommerce' => [
                'currency' => $this->currency(),
                'item_list_id' => $listId,
                'item_list_name' => $listName,
                'items' => $items,
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array{event: string, ecommerce: array<string, mixed>}
     */
    public function selectItem(array $item, string $listId = 'courses', string $listName = 'Courses'): array
    {
        $item = array_merge($item, [
            'item_list_id' => $listId,
            'item_list_name' => $listName,
        ]);

        return [
            'event' => 'select_item',
            'ecommerce' => [
                'currency' => $this->currency(),
                'item_list_id' => $listId,
                'item_list_name' => $listName,
                'items' => [$item],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>|list<array<string, mixed>>  $items
     * @return array{event: string, ecommerce: array<string, mixed>}
     */
    public function viewItem(array $items): array
    {
        $items = array_is_list($items) && isset($items[0]) && is_array($items[0])
            ? $items
            : [$items];

        $value = array_sum(array_map(fn ($i) => (float) ($i['price'] ?? 0) * (int) ($i['quantity'] ?? 1), $items));

        return [
            'event' => 'view_item',
            'ecommerce' => [
                'currency' => $this->currency(),
                'value' => round($value, 2),
                'items' => array_values($items),
            ],
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $items
     * @return array{event: string, ecommerce: array<string, mixed>}
     */
    public function beginCheckout(array $items, ?float $value = null, ?string $coupon = null): array
    {
        $value ??= array_sum(array_map(fn ($i) => (float) ($i['price'] ?? 0) * (int) ($i['quantity'] ?? 1), $items));

        $ecommerce = [
            'currency' => $this->currency(),
            'value' => round($value, 2),
            'items' => array_values($items),
        ];
        if ($coupon) {
            $ecommerce['coupon'] = $coupon;
        }

        return [
            'event' => 'begin_checkout',
            'ecommerce' => $ecommerce,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $items
     * @return array{event: string, ecommerce: array<string, mixed>}
     */
    public function purchasePayload(
        string $transactionId,
        float $value,
        array $items,
        ?string $coupon = null,
        ?float $tax = null,
        ?float $shipping = null,
    ): array {
        $ecommerce = [
            'transaction_id' => $transactionId,
            'currency' => $this->currency(),
            'value' => round($value, 2),
            'items' => array_values($items),
        ];
        if ($coupon) {
            $ecommerce['coupon'] = $coupon;
        }
        if ($tax !== null) {
            $ecommerce['tax'] = round($tax, 2);
        }
        if ($shipping !== null) {
            $ecommerce['shipping'] = round($shipping, 2);
        }

        return [
            'event' => 'purchase',
            'ecommerce' => $ecommerce,
        ];
    }

    /**
     * @return array{event: string, ecommerce: array<string, mixed>}|null
     */
    public function purchaseFromOrder(Order $order): ?array
    {
        $order->loadMissing(['course', 'learningPath', 'coupon']);

        $items = [];
        if ($order->course) {
            $items[] = $this->itemFromCourse(
                $order->course,
                0,
                (float) ($order->amount ?? $order->course->effectivePrice())
            );
        } elseif ($order->learningPath) {
            $items[] = $this->normalizeItem([
                'item_id' => 'learning_path:'.$order->academic_year_id,
                'item_name' => (string) ($order->learningPath->name ?? 'Learning Path'),
                'price' => (float) ($order->amount ?? 0),
                'quantity' => 1,
                'item_category' => 'learning_path',
            ]);
        }

        if ($items === []) {
            return null;
        }

        $value = (float) ($order->amount ?? array_sum(array_column($items, 'price')));
        $coupon = $order->coupon?->code;

        return $this->purchasePayload(
            'order:'.$order->id,
            $value,
            $items,
            $coupon
        );
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    public function normalizeItem(array $item): array
    {
        $out = [
            'item_id' => (string) ($item['item_id'] ?? ''),
            'item_name' => (string) ($item['item_name'] ?? ''),
            'affiliation' => (string) ($item['affiliation'] ?? $this->brand()),
            'item_brand' => (string) ($item['item_brand'] ?? $this->brand()),
            'price' => round((float) ($item['price'] ?? 0), 2),
            'quantity' => (int) ($item['quantity'] ?? 1),
        ];

        foreach (['index', 'discount', 'item_category', 'item_category2', 'item_category3', 'item_list_id', 'item_list_name', 'item_variant', 'coupon'] as $key) {
            if (Arr::has($item, $key) && $item[$key] !== null && $item[$key] !== '') {
                $out[$key] = $item[$key];
            }
        }

        return $out;
    }

    /**
     * Encode a dataLayer push payload as JSON for Blade.
     *
     * @param  array<string, mixed>  $payload
     */
    public function toJson(array $payload): string
    {
        return json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}';
    }
}
