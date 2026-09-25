<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\District;
use App\Models\User;
use App\Models\Village;
use App\Models\WorkUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('password');

        $sekretariat = WorkUnit::where('name', 'like', '%Sekretariat%')->first();
        $linjamsos = WorkUnit::where('name', 'like', '%Linjamsos%')->first();
        $rehsos = WorkUnit::where('name', 'like', '%Rehabilitasi%')->first();
        $puskesos = WorkUnit::where('name', 'like', '%Front Office%')->first();

        $kanigoroDistrict = District::where('code', '35.05.06')->first();
        $satreyanVillage = Village::where('code', '35.05.06.1002')->first();
        $kanigoroVillage = Village::where('code', '35.05.06.1001')->first();
        $wlingiDistrict = District::where('code', '35.05.13')->first();
        $beruVillage = Village::where('code', '35.05.13.1003')->first();
        $srengatDistrict = District::where('code', '35.05.22')->first();

        $users = [
            // 1. Administrator
            [
                'name' => 'Administrator SAPA SOSIAL',
                'email' => 'admin@blitarkab.go.id',
                'phone' => '081234567890',
                'nik' => '3505060101850001',
                'work_unit_id' => $sekretariat?->id,
                'district_id' => null,
                'village_id' => null,
                'is_active' => true,
                'roles' => ['administrator'],
            ],
            // 2. Pejabat Penandatangan & Pimpinan
            [
                'name' => 'Dra. Hj. Sri Wahyuni, M.Si',
                'email' => 'kadis@blitarkab.go.id',
                'phone' => '081234567891',
                'nik' => '3505064501700001',
                'work_unit_id' => $sekretariat?->id,
                'district_id' => null,
                'village_id' => null,
                'is_active' => true,
                'roles' => ['pimpinan', 'pejabat_penandatangan'],
            ],
            [
                'name' => 'Bambang Suryanto, S.Sos',
                'email' => 'kabid.linjamsos@blitarkab.go.id',
                'phone' => '081234567892',
                'nik' => '3505061203750002',
                'work_unit_id' => $linjamsos?->id,
                'district_id' => null,
                'village_id' => null,
                'is_active' => true,
                'roles' => ['pejabat_penandatangan'],
            ],
            [
                'name' => 'Dra. Endang Purwanti',
                'email' => 'kabid.rehsos@blitarkab.go.id',
                'phone' => '081234567893',
                'nik' => '3505065508780003',
                'work_unit_id' => $rehsos?->id,
                'district_id' => null,
                'village_id' => null,
                'is_active' => true,
                'roles' => ['pejabat_penandatangan'],
            ],
            [
                'name' => 'Drs. Mohamad Arifin',
                'email' => 'sekdin@blitarkab.go.id',
                'phone' => '081234567894',
                'nik' => '3505061405740004',
                'work_unit_id' => $sekretariat?->id,
                'district_id' => null,
                'village_id' => null,
                'is_active' => true,
                'roles' => ['pimpinan'],
            ],
            // 3. Petugas Teknis Pelayanan Dinsos
            [
                'name' => 'Agus Prasetyo, S.AP',
                'email' => 'petugas.dtsen@blitarkab.go.id',
                'phone' => '081234567895',
                'nik' => '3505062002880005',
                'work_unit_id' => $linjamsos?->id,
                'district_id' => null,
                'village_id' => null,
                'is_active' => true,
                'roles' => ['petugas_dinsos'],
            ],
            [
                'name' => 'Rina Kartikasari, S.Tr.Sos',
                'email' => 'petugas.pbi@blitarkab.go.id',
                'phone' => '081234567896',
                'nik' => '3505066107900006',
                'work_unit_id' => $linjamsos?->id,
                'district_id' => null,
                'village_id' => null,
                'is_active' => true,
                'roles' => ['petugas_dinsos'],
            ],
            [
                'name' => 'Dwi Handoko, S.Sos',
                'email' => 'petugas.rehsos@blitarkab.go.id',
                'phone' => '081234567897',
                'nik' => '3505061809860007',
                'work_unit_id' => $rehsos?->id,
                'district_id' => null,
                'village_id' => null,
                'is_active' => true,
                'roles' => ['petugas_dinsos'],
            ],
            [
                'name' => 'Siti Nurhaliza, S.Kom',
                'email' => 'petugas.pengaduan@blitarkab.go.id',
                'phone' => '081234567898',
                'nik' => '3505064903930008',
                'work_unit_id' => $puskesos?->id,
                'district_id' => null,
                'village_id' => null,
                'is_active' => true,
                'roles' => ['petugas_dinsos'],
            ],
            // 4. Operator Kecamatan & Desa
            [
                'name' => 'Hadi Wijaya',
                'email' => 'operator.kanigoro@blitarkab.go.id',
                'phone' => '082134567801',
                'nik' => '3505061010890009',
                'work_unit_id' => null,
                'district_id' => $kanigoroDistrict?->id,
                'village_id' => null,
                'is_active' => true,
                'roles' => ['operator_kecamatan_desa'],
            ],
            [
                'name' => 'Anisa Fitri',
                'email' => 'operator.satreyan@blitarkab.go.id',
                'phone' => '082134567802',
                'nik' => '3505065406950010',
                'work_unit_id' => null,
                'district_id' => $kanigoroDistrict?->id,
                'village_id' => $satreyanVillage?->id,
                'is_active' => true,
                'roles' => ['operator_kecamatan_desa'],
            ],
            [
                'name' => 'Tri Wahyudi',
                'email' => 'operator.wlingi@blitarkab.go.id',
                'phone' => '082134567803',
                'nik' => '3505132512870011',
                'work_unit_id' => null,
                'district_id' => $wlingiDistrict?->id,
                'village_id' => null,
                'is_active' => true,
                'roles' => ['operator_kecamatan_desa'],
            ],
            [
                'name' => 'Nurul Huda',
                'email' => 'operator.srengat@blitarkab.go.id',
                'phone' => '082134567804',
                'nik' => '3505221908840012',
                'work_unit_id' => null,
                'district_id' => $srengatDistrict?->id,
                'village_id' => null,
                'is_active' => true,
                'roles' => ['operator_kecamatan_desa'],
            ],
            // 5. Masyarakat Pemohon / Pelapor
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@gmail.com',
                'phone' => '085712345678',
                'nik' => '3505061507880005',
                'work_unit_id' => null,
                'district_id' => $kanigoroDistrict?->id,
                'village_id' => $satreyanVillage?->id,
                'is_active' => true,
                'roles' => ['masyarakat'],
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti.aminah@gmail.com',
                'phone' => '085812345679',
                'nik' => '3505065204920006',
                'work_unit_id' => null,
                'district_id' => $kanigoroDistrict?->id,
                'village_id' => $kanigoroVillage?->id,
                'is_active' => true,
                'roles' => ['masyarakat'],
            ],
            [
                'name' => 'Joko Susilo',
                'email' => 'joko.susilo@gmail.com',
                'phone' => '085912345680',
                'nik' => '3505131002800007',
                'work_unit_id' => null,
                'district_id' => $wlingiDistrict?->id,
                'village_id' => $beruVillage?->id,
                'is_active' => true,
                'roles' => ['masyarakat'],
            ],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $defaultPassword,
                    'phone' => $userData['phone'],
                    'nik' => $userData['nik'],
                    'work_unit_id' => $userData['work_unit_id'],
                    'district_id' => $userData['district_id'],
                    'village_id' => $userData['village_id'],
                    'is_active' => $userData['is_active'],
                    'email_verified_at' => now(),
                ]
            );

            if (isset($userData['roles'])) {
                $user->syncRoles($userData['roles']);
            }
        }
    }
}
