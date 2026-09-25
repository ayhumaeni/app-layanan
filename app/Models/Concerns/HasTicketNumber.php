<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\NumberSequence;

trait HasTicketNumber
{
    public static function generateTicketNumber(string $prefix, ?string $period = null): string
    {
        return NumberSequence::nextNumber($prefix, $period);
    }
}
