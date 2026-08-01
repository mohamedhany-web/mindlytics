<?php

namespace App\Services;

use App\Models\SalesActivity;
use App\Models\SalesDayBlock;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class SalesDayBlockService
{
    public function tablesReady(): bool
    {
        return Schema::hasTable('sales_day_blocks');
    }

    /**
     * @return Collection<int, SalesDayBlock>
     */
    public function activeBlocks(): Collection
    {
        if (! $this->tablesReady()) {
            return collect();
        }

        return SalesDayBlock::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('start_time')
            ->get();
    }

    public function currentBlock(?Carbon $at = null): ?SalesDayBlock
    {
        $at = $at ?? now();
        $hm = $at->format('H:i:s');

        return $this->activeBlocks()->first(function (SalesDayBlock $block) use ($hm) {
            return $block->start_time <= $hm && $block->end_time > $hm;
        });
    }

    public function nextBlock(?Carbon $at = null): ?SalesDayBlock
    {
        $at = $at ?? now();
        $hm = $at->format('H:i:s');

        return $this->activeBlocks()->first(fn (SalesDayBlock $block) => $block->start_time > $hm);
    }

    /**
     * @return array{
     *   current: ?SalesDayBlock,
     *   next: ?SalesDayBlock,
     *   blocks: Collection<int, SalesDayBlock>,
     *   minutes_left: ?int,
     *   label: string
     * }
     */
    public function snapshot(?Carbon $at = null): array
    {
        $at = $at ?? now();
        $current = $this->currentBlock($at);
        $next = $this->nextBlock($at);
        $minutesLeft = null;

        if ($current) {
            $end = Carbon::parse($at->toDateString().' '.$current->end_time);
            $minutesLeft = max(0, (int) $at->diffInMinutes($end, false));
        }

        return [
            'current' => $current,
            'next' => $next,
            'blocks' => $this->activeBlocks(),
            'minutes_left' => $minutesLeft,
            'label' => $current
                ? $current->name
                : ($next ? 'قبل '.$next->name : 'خارج جدول اليوم'),
        ];
    }

    /**
     * هل النشاط المسجّل في آخر ساعتين كافٍ حسب نوع البلوك الحالي؟
     */
    public function isOnPace(User $user, ?Carbon $at = null): bool
    {
        $at = $at ?? now();
        $block = $this->currentBlock($at);
        if (! $block) {
            return true;
        }

        // الاستراحات لا تحتاج نشاط
        if (in_array($block->activity_type, ['break', 'lunch'], true)) {
            return true;
        }

        $since = $at->copy()->subHours(2);
        $q = SalesActivity::query()
            ->where('user_id', $user->id)
            ->where('created_at', '>=', $since)
            ->where('type', '!=', 'stage_change');

        $count = match ($block->activity_type) {
            'calls' => (clone $q)->where('type', 'call')->count(),
            'followup' => (clone $q)->whereIn('type', ['follow_up', 'call', 'meeting'])->count(),
            'whatsapp_closing' => (clone $q)->whereIn('type', ['whatsapp', 'call', 'note', 'meeting'])->count(),
            'report' => true,
            default => (clone $q)->count(),
        };

        if ($block->activity_type === 'report') {
            return true;
        }

        $minRequired = match ($block->activity_type) {
            'calls' => 4,
            'followup' => 3,
            'whatsapp_closing' => 3,
            'brief' => 0,
            default => 2,
        };

        return $count >= $minRequired;
    }
}
