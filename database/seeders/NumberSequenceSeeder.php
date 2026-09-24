<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\NumberSequence;
use Illuminate\Database\Seeder;

class NumberSequenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sequences = [
            ['prefix' => 'DTSEN', 'period' => '202609', 'last_number' => 5],
            ['prefix' => 'PBI', 'period' => '202609', 'last_number' => 3],
            ['prefix' => 'ADU', 'period' => '202609', 'last_number' => 5],
            ['prefix' => 'RHS', 'period' => '202609', 'last_number' => 3],
            ['prefix' => 'RJK', 'period' => '202609', 'last_number' => 2],

            // Upcoming period
            ['prefix' => 'DTSEN', 'period' => '202610', 'last_number' => 0],
            ['prefix' => 'PBI', 'period' => '202610', 'last_number' => 0],
            ['prefix' => 'ADU', 'period' => '202610', 'last_number' => 0],
            ['prefix' => 'RHS', 'period' => '202610', 'last_number' => 0],
            ['prefix' => 'RJK', 'period' => '202610', 'last_number' => 0],
        ];

        foreach ($sequences as $seq) {
            NumberSequence::updateOrCreate(
                [
                    'prefix' => $seq['prefix'],
                    'period' => $seq['period'],
                ],
                [
                    'last_number' => $seq['last_number'],
                ]
            );
        }
    }
}
