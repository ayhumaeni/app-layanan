<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class NumberSequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'prefix',
        'period',
        'last_number',
    ];

    protected function casts(): array
    {
        return [
            'last_number' => 'integer',
        ];
    }

    /**
     * Generate the next formatted sequence number atomically using lockForUpdate.
     * Example output: DTSEN-202609-00001
     */
    public static function nextNumber(string $prefix, ?string $period = null, int $padding = 5): string
    {
        $period = $period ?? Carbon::now('Asia/Jakarta')->format('Ym');

        return DB::transaction(function () use ($prefix, $period, $padding): string {
            /** @var self $sequence */
            $sequence = self::lockForUpdate()->firstOrCreate(
                [
                    'prefix' => $prefix,
                    'period' => $period,
                ],
                [
                    'last_number' => 0,
                ]
            );

            $sequence->last_number++;
            $sequence->save();

            $formattedNumber = str_pad((string) $sequence->last_number, $padding, '0', STR_PAD_LEFT);

            return "{$prefix}-{$period}-{$formattedNumber}";
        });
    }
}
