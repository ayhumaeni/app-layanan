<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ReferralInstitution;
use Illuminate\Database\Seeder;

class ReferralInstitutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $institutions = [
            [
                'name' => 'UPT Pelayanan Sosial Tresna Werdha (PSTW) Blitar',
                'type' => 'panti',
                'address' => 'Jl. Merdeka No. 12, Blitar',
                'contact' => '0342-801234',
                'is_active' => true,
            ],
            [
                'name' => 'RSUD Ngudi Waluyo Wlingi (Instalasi Jiwa)',
                'type' => 'RS',
                'address' => 'Jl. Dokter Sucipto No. 5, Wlingi, Kabupaten Blitar',
                'contact' => '0342-691006',
                'is_active' => true,
            ],
            [
                'name' => 'RSUD Srengat Kabupaten Blitar',
                'type' => 'RS',
                'address' => 'Jl. Raya Dandong No. 1, Srengat, Kabupaten Blitar',
                'contact' => '0342-551122',
                'is_active' => true,
            ],
            [
                'name' => 'Balai Rehabilitasi Sosial Anak Membutuhkan Perlindungan Khusus (BRPAMPK)',
                'type' => 'balai',
                'address' => 'Jl. Raya Tlogomas No. 45, Malang',
                'contact' => '0341-491234',
                'is_active' => true,
            ],
            [
                'name' => 'Lembaga Kesejahteraan Sosial (LKS) Disabilitas Harapan Mulia',
                'type' => 'LKS',
                'address' => 'Jl. Raya Garum No. 88, Garum, Kabupaten Blitar',
                'contact' => '085732109876',
                'is_active' => true,
            ],
            [
                'name' => 'UPT Rehabilitasi Sosial Bina Netra / Bina Daksa Malang',
                'type' => 'panti',
                'address' => 'Jl. Simpang Sulfat No. 10, Malang',
                'contact' => '0341-362001',
                'is_active' => true,
            ],
        ];

        foreach ($institutions as $inst) {
            ReferralInstitution::updateOrCreate(
                ['name' => $inst['name']],
                $inst
            );
        }
    }
}
