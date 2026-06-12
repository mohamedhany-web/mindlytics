<?php

namespace App\Support;

use App\Models\Branch;

/**
 * الفرع المُستنتج من الطلب (الدومين / الـ Host) لاستخدامه في الواجهات والخدمات لاحقاً.
 */
final class BranchContext
{
    public function __construct(
        public readonly ?Branch $branch = null,
    ) {}

    public function id(): ?int
    {
        return $this->branch?->id;
    }

    public function hasBranch(): bool
    {
        return $this->branch !== null;
    }
}
