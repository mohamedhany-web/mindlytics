<?php

namespace App\Domains\Advising\Models;

use Illuminate\Database\Eloquent\Model;

/** يوم مقفول للإرشاد (إجازة، حدث…): مفيش مواعيد فيه حتى لو فيه توافر أسبوعي. */
class AdvisingBlockedDate extends Model
{
    protected $table = 'advising_blocked_dates';

    protected $fillable = ['date', 'reason'];

    protected function casts(): array
    {
        return ['date' => 'date'];
    }
}
