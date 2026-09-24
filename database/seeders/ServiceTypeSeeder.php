<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ServiceRequestHandler;
use App\Models\ServiceRequirement;
use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class ServiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $serviceTypes = [
            [
                'code' => 'DTSEN',
                'name' => 'Surat Keterangan DTSEN',
                'category' => 'Kesejahteraan Sosial',
                'description' => 'Penerbitan surat keterangan status seseorang/keluarga dalam Data Tunggal Sosial Ekonomi Nasional (DTSEN) beserta peringkat desil untuk keperluan afirmasi pendidikan (SPMB/PIP/KIPK), bansos, dan kesehatan.',
                'handler' => ServiceRequestHandler::Dtsen,
                'needs_assessment' => false,
                'sla_days' => 1,
                'is_active' => true,
                'requirements' => [
                    [
                        'name' => 'Kartu Tanda Penduduk (KTP) Pemohon',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Kartu Keluarga (KK)',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Surat Pengantar dari Desa / Kelurahan',
                        'is_mandatory' => false,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 3,
                    ],
                ],
            ],
            [
                'code' => 'PBI',
                'name' => 'Reaktivasi KIS / PBI-JK',
                'category' => 'Jaminan Kesehatan',
                'description' => 'Fasilitasi penerbitan rekomendasi pengaktifan kembali kepesertaan JKN-KIS Penerima Bantuan Iuran Jaminan Kesehatan (PBI-JK) yang dinonaktifkan oleh Kementerian Sosial.',
                'handler' => ServiceRequestHandler::Pbi,
                'needs_assessment' => false,
                'sla_days' => 3,
                'is_active' => true,
                'requirements' => [
                    [
                        'name' => 'Kartu Tanda Penduduk (KTP) Peserta',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Kartu Keluarga (KK)',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Kartu BPJS Kesehatan / KIS Peserta',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 3,
                    ],
                    [
                        'name' => 'Surat Keterangan / Resume Medis dari Fasilitas Kesehatan',
                        'is_mandatory' => false,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'code' => 'REHSOS',
                'name' => 'Permohonan Pelayanan Rehabilitasi Sosial',
                'category' => 'Rehabilitasi Sosial',
                'description' => 'Pelayanan penanganan masalah sosial perseorangan atau keluarga pemerlu pelayanan kesejahteraan sosial (lansia terlantar, disabilitas, ODGJ, anak terlantar, korban kekerasan).',
                'handler' => ServiceRequestHandler::Generic,
                'needs_assessment' => true,
                'sla_days' => 7,
                'is_active' => true,
                'requirements' => [
                    [
                        'name' => 'KTP Pemohon / Wali / Pelapor',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Kartu Keluarga (KK)',
                        'is_mandatory' => false,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Surat Keterangan / Rekomendasi Desa',
                        'is_mandatory' => false,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 3,
                    ],
                    [
                        'name' => 'Foto Kondisi Calon Klien',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'jpg,jpeg,png',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'code' => 'ATENSI',
                'name' => 'Rekomendasi Bantuan Asistensi Rehabilitasi Sosial (ATENSI)',
                'category' => 'Asistensi Sosial',
                'description' => 'Pengajuan usulan asistensi rehabilitasi sosial (kebutuhan dasar, nutrisi, kewirausahaan, perawatan) bagi keluarga rentan dan pemerlu pelayanan sosial.',
                'handler' => ServiceRequestHandler::Generic,
                'needs_assessment' => true,
                'sla_days' => 5,
                'is_active' => true,
                'requirements' => [
                    [
                        'name' => 'KTP Calon Penerima Bantuan / Wali',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Kartu Keluarga (KK)',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Surat Permohonan / Rekomendasi Desa/Kelurahan',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 3,
                    ],
                    [
                        'name' => 'Foto Kondisi Calon Penerima Bantuan',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'jpg,jpeg,png',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'code' => 'ALAT_BANTU',
                'name' => 'Rekomendasi Bantuan Alat Bantu Penyandang Disabilitas',
                'category' => 'Rehabilitasi Sosial',
                'description' => 'Fasilitasi rekomendasi pemberian alat bantu (kursi roda, kruk, walker, alat bantu dengar, tongkat netra) bagi penyandang disabilitas.',
                'handler' => ServiceRequestHandler::Generic,
                'needs_assessment' => true,
                'sla_days' => 5,
                'is_active' => true,
                'requirements' => [
                    [
                        'name' => 'KTP Pemohon / Penyandang Disabilitas',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Kartu Keluarga (KK)',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Surat Keterangan Medis Ragam Disabilitas dari Puskesmas/RSUD',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,jpeg,png',
                        'sort_order' => 3,
                    ],
                    [
                        'name' => 'Foto Seluruh Badan Pemohon',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'jpg,jpeg,png',
                        'sort_order' => 4,
                    ],
                ],
            ],
        ];

        foreach ($serviceTypes as $serviceData) {
            $requirements = $serviceData['requirements'];
            unset($serviceData['requirements']);

            /** @var ServiceType $serviceType */
            $serviceType = ServiceType::updateOrCreate(
                ['code' => $serviceData['code']],
                $serviceData
            );

            foreach ($requirements as $requirementData) {
                ServiceRequirement::updateOrCreate(
                    [
                        'service_type_id' => $serviceType->id,
                        'name' => $requirementData['name'],
                    ],
                    $requirementData
                );
            }
        }
    }
}
