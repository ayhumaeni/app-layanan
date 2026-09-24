<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\District;
use App\Models\Village;
use Illuminate\Database\Seeder;

class DistrictVillageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $districtsWithVillages = [
            [
                'code' => '35.05.01',
                'name' => 'Wonodadi',
                'villages' => [
                    ['code' => '35.05.01.2001', 'name' => 'Wonodadi'],
                    ['code' => '35.05.01.2002', 'name' => 'Pikatan'],
                    ['code' => '35.05.01.2003', 'name' => 'Gandekan'],
                    ['code' => '35.05.01.2004', 'name' => 'Kolomayan'],
                    ['code' => '35.05.01.2005', 'name' => 'Tawangrejo'],
                ],
            ],
            [
                'code' => '35.05.02',
                'name' => 'Udanawu',
                'villages' => [
                    ['code' => '35.05.02.2001', 'name' => 'Bakung'],
                    ['code' => '35.05.02.2002', 'name' => 'Bendorejo'],
                    ['code' => '35.05.02.2003', 'name' => 'Besuki'],
                    ['code' => '35.05.02.2004', 'name' => 'Karanggondang'],
                    ['code' => '35.05.02.2005', 'name' => 'Mangunrejo'],
                ],
            ],
            [
                'code' => '35.05.03',
                'name' => 'Sanankulon',
                'villages' => [
                    ['code' => '35.05.03.2001', 'name' => 'Sanankulon'],
                    ['code' => '35.05.03.2002', 'name' => 'Bendowulung'],
                    ['code' => '35.05.03.2003', 'name' => 'Gledug'],
                    ['code' => '35.05.03.2004', 'name' => 'Kalipucung'],
                    ['code' => '35.05.03.2005', 'name' => 'Sumberjo'],
                ],
            ],
            [
                'code' => '35.05.04',
                'name' => 'Kademangan',
                'villages' => [
                    ['code' => '35.05.04.1001', 'name' => 'Kademangan'],
                    ['code' => '35.05.04.2002', 'name' => 'Dawuhan'],
                    ['code' => '35.05.04.2003', 'name' => 'Jimbe'],
                    ['code' => '35.05.04.2004', 'name' => 'Plosorejo'],
                    ['code' => '35.05.04.2005', 'name' => 'Rejowinangun'],
                ],
            ],
            [
                'code' => '35.05.05',
                'name' => 'Nglegok',
                'villages' => [
                    ['code' => '35.05.05.1001', 'name' => 'Nglegok'],
                    ['code' => '35.05.05.2002', 'name' => 'Banggle'],
                    ['code' => '35.05.05.2003', 'name' => 'Dayu'],
                    ['code' => '35.05.05.2004', 'name' => 'Jiwo'],
                    ['code' => '35.05.05.2005', 'name' => 'Penataran'],
                ],
            ],
            [
                'code' => '35.05.06',
                'name' => 'Kanigoro',
                'villages' => [
                    ['code' => '35.05.06.1001', 'name' => 'Kanigoro'],
                    ['code' => '35.05.06.1002', 'name' => 'Satreyan'],
                    ['code' => '35.05.06.2003', 'name' => 'Bangle'],
                    ['code' => '35.05.06.2004', 'name' => 'Gaprang'],
                    ['code' => '35.05.06.2005', 'name' => 'Gogodeso'],
                    ['code' => '35.05.06.2006', 'name' => 'Jatinom'],
                    ['code' => '35.05.06.2007', 'name' => 'Karangsono'],
                    ['code' => '35.05.06.2008', 'name' => 'Kuningan'],
                    ['code' => '35.05.06.2009', 'name' => 'Minggirsari'],
                    ['code' => '35.05.06.2010', 'name' => 'Papungan'],
                    ['code' => '35.05.06.2011', 'name' => 'Sawentar'],
                    ['code' => '35.05.06.2012', 'name' => 'Tlogo'],
                ],
            ],
            [
                'code' => '35.05.07',
                'name' => 'Garum',
                'villages' => [
                    ['code' => '35.05.07.1001', 'name' => 'Garum'],
                    ['code' => '35.05.07.1002', 'name' => 'Bence'],
                    ['code' => '35.05.07.1003', 'name' => 'Sumberdiren'],
                    ['code' => '35.05.07.1004', 'name' => 'Tawangsari'],
                    ['code' => '35.05.07.2005', 'name' => 'Karangrejo'],
                    ['code' => '35.05.07.2006', 'name' => 'Pojok'],
                    ['code' => '35.05.07.2007', 'name' => 'Sidodadi'],
                    ['code' => '35.05.07.2008', 'name' => 'Slorok'],
                    ['code' => '35.05.07.2009', 'name' => 'Tingal'],
                ],
            ],
            [
                'code' => '35.05.08',
                'name' => 'Sutojayan',
                'villages' => [
                    ['code' => '35.05.08.1001', 'name' => 'Sutojayan'],
                    ['code' => '35.05.08.1002', 'name' => 'Kalipang'],
                    ['code' => '35.05.08.1003', 'name' => 'Kembangarum'],
                    ['code' => '35.05.08.1004', 'name' => 'Kedungbunder'],
                    ['code' => '35.05.08.1005', 'name' => 'Sukorejo'],
                    ['code' => '35.05.08.2006', 'name' => 'Bacem'],
                    ['code' => '35.05.08.2007', 'name' => 'Pandanyoyo'],
                ],
            ],
            [
                'code' => '35.05.09',
                'name' => 'Panggungrejo',
                'villages' => [
                    ['code' => '35.05.09.2001', 'name' => 'Panggungrejo'],
                    ['code' => '35.05.09.2002', 'name' => 'Balerejo'],
                    ['code' => '35.05.09.2003', 'name' => 'Bumiayu'],
                    ['code' => '35.05.09.2004', 'name' => 'Kaligambir'],
                    ['code' => '35.05.09.2005', 'name' => 'Serang'],
                ],
            ],
            [
                'code' => '35.05.10',
                'name' => 'Talun',
                'villages' => [
                    ['code' => '35.05.10.1001', 'name' => 'Talun'],
                    ['code' => '35.05.10.1002', 'name' => 'Kamulan'],
                    ['code' => '35.05.10.1003', 'name' => 'Kaweron'],
                    ['code' => '35.05.10.1004', 'name' => 'Bajang'],
                    ['code' => '35.05.10.2005', 'name' => 'Bendosewu'],
                    ['code' => '35.05.10.2006', 'name' => 'Duren'],
                    ['code' => '35.05.10.2007', 'name' => 'Jeblog'],
                    ['code' => '35.05.10.2008', 'name' => 'Pasirharjo'],
                ],
            ],
            [
                'code' => '35.05.11',
                'name' => 'Gandusari',
                'villages' => [
                    ['code' => '35.05.11.2001', 'name' => 'Gandusari'],
                    ['code' => '35.05.11.2002', 'name' => 'Gadungan'],
                    ['code' => '35.05.11.2003', 'name' => 'Kotes'],
                    ['code' => '35.05.11.2004', 'name' => 'Ngaringan'],
                    ['code' => '35.05.11.2005', 'name' => 'Sukosewu'],
                ],
            ],
            [
                'code' => '35.05.12',
                'name' => 'Binangun',
                'villages' => [
                    ['code' => '35.05.12.2001', 'name' => 'Binangun'],
                    ['code' => '35.05.12.2002', 'name' => 'Kedungwungu'],
                    ['code' => '35.05.12.2003', 'name' => 'Ngembul'],
                    ['code' => '35.05.12.2004', 'name' => 'Rejoso'],
                    ['code' => '35.05.12.2005', 'name' => 'Sambigede'],
                ],
            ],
            [
                'code' => '35.05.13',
                'name' => 'Wlingi',
                'villages' => [
                    ['code' => '35.05.13.1001', 'name' => 'Wlingi'],
                    ['code' => '35.05.13.1002', 'name' => 'Babadan'],
                    ['code' => '35.05.13.1003', 'name' => 'Beru'],
                    ['code' => '35.05.13.1004', 'name' => 'Tangkil'],
                    ['code' => '35.05.13.1005', 'name' => 'Tembalang'],
                    ['code' => '35.05.13.2006', 'name' => 'Balerejo'],
                    ['code' => '35.05.13.2007', 'name' => 'Ngadirenggo'],
                    ['code' => '35.05.13.2008', 'name' => 'Tegalasri'],
                ],
            ],
            [
                'code' => '35.05.14',
                'name' => 'Doko',
                'villages' => [
                    ['code' => '35.05.14.2001', 'name' => 'Doko'],
                    ['code' => '35.05.14.2002', 'name' => 'Genengan'],
                    ['code' => '35.05.14.2003', 'name' => 'Kalimanis'],
                    ['code' => '35.05.14.2004', 'name' => 'Resapombo'],
                    ['code' => '35.05.14.2005', 'name' => 'Sumberurip'],
                ],
            ],
            [
                'code' => '35.05.15',
                'name' => 'Kesamben',
                'villages' => [
                    ['code' => '35.05.15.2001', 'name' => 'Kesamben'],
                    ['code' => '35.05.15.2002', 'name' => 'Bumi Pratama'],
                    ['code' => '35.05.15.2003', 'name' => 'Jugosari'],
                    ['code' => '35.05.15.2004', 'name' => 'Pagerwojo'],
                    ['code' => '35.05.15.2005', 'name' => 'Siraman'],
                ],
            ],
            [
                'code' => '35.05.16',
                'name' => 'Wates',
                'villages' => [
                    ['code' => '35.05.16.2001', 'name' => 'Wates'],
                    ['code' => '35.05.16.2002', 'name' => 'Mojorejo'],
                    ['code' => '35.05.16.2003', 'name' => 'Purworejo'],
                    ['code' => '35.05.16.2004', 'name' => 'Ringinrejo'],
                    ['code' => '35.05.16.2005', 'name' => 'Tugurejo'],
                ],
            ],
            [
                'code' => '35.05.17',
                'name' => 'Ponggok',
                'villages' => [
                    ['code' => '35.05.17.2001', 'name' => 'Ponggok'],
                    ['code' => '35.05.17.2002', 'name' => 'Bacem'],
                    ['code' => '35.05.17.2003', 'name' => 'Candirejo'],
                    ['code' => '35.05.17.2004', 'name' => 'Gembongan'],
                    ['code' => '35.05.17.2005', 'name' => 'Kebonduren'],
                ],
            ],
            [
                'code' => '35.05.18',
                'name' => 'Selorejo',
                'villages' => [
                    ['code' => '35.05.18.2001', 'name' => 'Selorejo'],
                    ['code' => '35.05.18.2002', 'name' => 'Banjarsari'],
                    ['code' => '35.05.18.2003', 'name' => 'Ngrendeng'],
                    ['code' => '35.05.18.2004', 'name' => 'Olok-Olok'],
                    ['code' => '35.05.18.2005', 'name' => 'Pohgajih'],
                ],
            ],
            [
                'code' => '35.05.19',
                'name' => 'Bakung',
                'villages' => [
                    ['code' => '35.05.19.2001', 'name' => 'Bakung'],
                    ['code' => '35.05.19.2002', 'name' => 'Bululawang'],
                    ['code' => '35.05.19.2003', 'name' => 'Kedungbanteng'],
                    ['code' => '35.05.19.2004', 'name' => 'Lorejo'],
                    ['code' => '35.05.19.2005', 'name' => 'Plandirejo'],
                ],
            ],
            [
                'code' => '35.05.20',
                'name' => 'Wonotirto',
                'villages' => [
                    ['code' => '35.05.20.2001', 'name' => 'Wonotirto'],
                    ['code' => '35.05.20.2002', 'name' => 'Gununggede'],
                    ['code' => '35.05.20.2003', 'name' => 'Kaligrenjeng'],
                    ['code' => '35.05.20.2004', 'name' => 'Pasiraman'],
                    ['code' => '35.05.20.2005', 'name' => 'Tambakrejo'],
                ],
            ],
            [
                'code' => '35.05.21',
                'name' => 'Selopuro',
                'villages' => [
                    ['code' => '35.05.21.2001', 'name' => 'Selopuro'],
                    ['code' => '35.05.21.2002', 'name' => 'Jambewangi'],
                    ['code' => '35.05.21.2003', 'name' => 'Jatitengah'],
                    ['code' => '35.05.21.2004', 'name' => 'Manding'],
                    ['code' => '35.05.21.2005', 'name' => 'Popoh'],
                ],
            ],
            [
                'code' => '35.05.22',
                'name' => 'Srengat',
                'villages' => [
                    ['code' => '35.05.22.1001', 'name' => 'Srengat'],
                    ['code' => '35.05.22.1002', 'name' => 'Dandong'],
                    ['code' => '35.05.22.1003', 'name' => 'Kauman'],
                    ['code' => '35.05.22.1004', 'name' => 'Togogan'],
                    ['code' => '35.05.22.2005', 'name' => 'Kendalrejo'],
                    ['code' => '35.05.22.2006', 'name' => 'Maron'],
                    ['code' => '35.05.22.2007', 'name' => 'Purwokerto'],
                    ['code' => '35.05.22.2008', 'name' => 'Selokajang'],
                    ['code' => '35.05.22.2009', 'name' => 'Wonorejo'],
                ],
            ],
        ];

        foreach ($districtsWithVillages as $districtData) {
            /** @var District $district */
            $district = District::firstOrCreate(
                ['code' => $districtData['code']],
                ['name' => $districtData['name']]
            );

            foreach ($districtData['villages'] as $villageData) {
                Village::firstOrCreate(
                    ['code' => $villageData['code']],
                    [
                        'district_id' => $district->id,
                        'name' => $villageData['name'],
                    ]
                );
            }
        }
    }
}
