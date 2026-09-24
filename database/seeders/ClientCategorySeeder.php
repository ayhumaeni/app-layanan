<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ClientCategory;
use Illuminate\Database\Seeder;

class ClientCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Lanjut Usia Terlantar',
            'Penyandang Disabilitas Mental (ODGJ Terlantar)',
            'Penyandang Disabilitas Fisik / Sensorik / Intelektual',
            'Anak Terlantar / AMPK',
            'Korban Tindak Kekerasan / PMI Bermasalah',
            'Gelandangan dan Pengemis (Gepeng)',
            'Tuna Sosial Lainnya',
        ];

        foreach ($categories as $categoryName) {
            ClientCategory::firstOrCreate([
                'name' => $categoryName,
            ]);
        }
    }
}
