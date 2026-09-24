<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ComplaintCategory;
use Illuminate\Database\Seeder;

class ComplaintCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Bantuan Sosial (PKH, BPNT / Sembako, BLT)',
            'Pemerlu Pelayanan Kesejahteraan Sosial (PPKS) / ODGJ Terlantar',
            'Lanjut Usia Terlantar / Butuh Bantuan',
            'Anak Terlantar / Perlindungan Anak & Kekerasan',
            'Penyandang Disabilitas Terlantar',
            'Bencana Alam & Perlindungan Korban Bencana',
            'Pelayanan Petugas & Maladministrasi Pelayanan Sosial',
            'Lain-lain',
        ];

        foreach ($categories as $categoryName) {
            ComplaintCategory::firstOrCreate(
                ['name' => $categoryName],
                ['is_active' => true]
            );
        }
    }
}
