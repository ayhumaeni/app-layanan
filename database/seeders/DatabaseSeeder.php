<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DistrictVillageSeeder::class,
            WorkUnitSeeder::class,
            UserSeeder::class,
            ServiceTypeSeeder::class,
            DtsenPurposeSeeder::class,
            ComplaintCategorySeeder::class,
            ClientCategorySeeder::class,
            ReferralInstitutionSeeder::class,
            InformationPageSeeder::class,
            NumberSequenceSeeder::class,
            RehabilitationCaseSeeder::class,
            ServiceRequestSeeder::class,
            ComplaintSeeder::class,
        ]);
    }
}
