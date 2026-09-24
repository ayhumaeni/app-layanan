<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\WorkUnit;
use Illuminate\Database\Seeder;

class WorkUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            [
                'name' => 'Sekretariat Dinas Sosial',
                'is_active' => true,
            ],
            [
                'name' => 'Bidang Perlindungan dan Jaminan Sosial (Linjamsos)',
                'is_active' => true,
            ],
            [
                'name' => 'Bidang Rehabilitasi Sosial (Rehsos)',
                'is_active' => true,
            ],
            [
                'name' => 'Bidang Pemberdayaan Sosial dan Penanganan Fakir Miskin',
                'is_active' => true,
            ],
            [
                'name' => 'Front Office Pelayanan Terpadu & Puskesos',
                'is_active' => true,
            ],
        ];

        foreach ($units as $unit) {
            WorkUnit::firstOrCreate(
                ['name' => $unit['name']],
                ['is_active' => $unit['is_active']]
            );
        }
    }
}
