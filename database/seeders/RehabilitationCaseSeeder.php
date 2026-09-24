<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\HandlingType;
use App\Enums\ReferralStatus;
use App\Enums\RehabilitationCaseStatus;
use App\Models\Assessment;
use App\Models\Client;
use App\Models\ClientCategory;
use App\Models\MonitoringRecord;
use App\Models\Referral;
use App\Models\ReferralInstitution;
use App\Models\RehabilitationCase;
use App\Models\StatusHistory;
use App\Models\User;
use App\Models\Village;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class RehabilitationCaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $petugasRehsos = User::where('email', 'petugas.rehsos@blitarkab.go.id')->first();

        $kanigoroVillage = Village::where('code', '35.05.06.1001')->first();
        $satreyanVillage = Village::where('code', '35.05.06.1002')->first();
        $beruVillage = Village::where('code', '35.05.13.1003')->first();

        $catOdgj = ClientCategory::where('name', 'like', '%ODGJ%')->first();
        $catLansia = ClientCategory::where('name', 'like', '%Lanjut Usia%')->first();
        $catAnak = ClientCategory::where('name', 'like', '%Anak%')->first();

        $rsudWlingi = ReferralInstitution::where('name', 'like', '%Ngudi Waluyo%')->first();
        $pstwBlitar = ReferralInstitution::where('name', 'like', '%Tresna Werdha%')->first();

        // ============================================================
        // KASUS 1: ODGJ Terlantar (InService / Sedang dirawat di RS)
        // ============================================================
        /** @var Client $client1 */
        $client1 = Client::updateOrCreate(
            ['name' => 'Slamet (Mr. X)'],
            [
                'client_category_id' => $catOdgj->id,
                'nik' => '3505060101789999',
                'birth_date' => Carbon::now()->subYears(45)->toDateString(),
                'gender' => 'male',
                'address' => 'Ditemukan di Area Pasar Kanigoro',
                'village_id' => $kanigoroVillage?->id,
                'phone' => null,
            ]
        );

        /** @var RehabilitationCase $case1 */
        $case1 = RehabilitationCase::updateOrCreate(
            ['case_number' => 'RHS-202609-00001'],
            [
                'client_id' => $client1->id,
                'officer_id' => $petugasRehsos?->id,
                'handling_type' => HandlingType::Both,
                'status' => RehabilitationCaseStatus::InService,
                'received_at' => Carbon::now()->subDays(12),
            ]
        );

        /** @var Assessment $assessment1 */
        $assessment1 = Assessment::updateOrCreate(
            ['rehabilitation_case_id' => $case1->id],
            [
                'officer_id' => $petugasRehsos?->id,
                'assessment_date' => Carbon::now()->subDays(11)->toDateString(),
                'result' => 'Klien ditemukan oleh warga di jalan raya Kanigoro dalam keadaan linglung, tidak berpakaian layak, dan tidak mampu berkomunikasi runtut.',
                'service_needs' => 'Stabilisasi medis psikiatris, penanganan medis darurat, perawatan kebersihan diri, dan terapi psikososial.',
                'recommendation' => 'Rujuk segera ke RSUD Ngudi Waluyo Wlingi Instalasi Jiwa untuk pengobatan klinis dan observasi intensif.',
                'needs_referral' => true,
            ]
        );

        /** @var Referral $referral1 */
        $referral1 = Referral::updateOrCreate(
            ['referral_number' => 'RJK-202609-00001'],
            [
                'rehabilitation_case_id' => $case1->id,
                'assessment_id' => $assessment1->id,
                'referral_institution_id' => $rsudWlingi->id,
                'officer_id' => $petugasRehsos?->id,
                'referral_date' => Carbon::now()->subDays(10)->toDateString(),
                'status' => ReferralStatus::InService,
                'service_result' => 'Pasien dirawat inap di Bangsal Jiwa RSUD Ngudi Waluyo Wlingi.',
            ]
        );

        MonitoringRecord::updateOrCreate(
            [
                'rehabilitation_case_id' => $case1->id,
                'referral_id' => $referral1->id,
                'monitoring_date' => Carbon::now()->subDays(4)->toDateString(),
            ],
            [
                'officer_id' => $petugasRehsos?->id,
                'progress' => 'Telah dilakukan home visit ke RSUD. Pasien mulai merespon nama panggilan, halusinasi berkurang setelah terapi injeksi dan oral.',
                'result_notes' => 'Rencana perekaman biometrik Dispendukcapil minggu depan untuk penelusuran alamat keluarga asal.',
            ]
        );

        StatusHistory::create([
            'statusable_type' => RehabilitationCase::class,
            'statusable_id' => $case1->id,
            'from_status' => 'received',
            'to_status' => 'in_service',
            'notes' => 'Kasus masuk tahap pelayanan rujukan medis.',
            'user_id' => $petugasRehsos?->id,
            'created_at' => Carbon::now()->subDays(10),
        ]);

        // ============================================================
        // KASUS 2: Lansia Terlantar (Monitoring di Panti)
        // ============================================================
        /** @var Client $client2 */
        $client2 = Client::updateOrCreate(
            ['name' => 'Mbah Supartini'],
            [
                'client_category_id' => $catLansia->id,
                'nik' => '3505065204520002',
                'birth_date' => Carbon::now()->subYears(74)->toDateString(),
                'gender' => 'female',
                'address' => 'RT 01 RW 04 Dusun Krajan, Satreyan',
                'village_id' => $satreyanVillage?->id,
                'phone' => null,
            ]
        );

        /** @var RehabilitationCase $case2 */
        $case2 = RehabilitationCase::updateOrCreate(
            ['case_number' => 'RHS-202609-00002'],
            [
                'client_id' => $client2->id,
                'officer_id' => $petugasRehsos?->id,
                'handling_type' => HandlingType::Referral,
                'status' => RehabilitationCaseStatus::Monitoring,
                'received_at' => Carbon::now()->subDays(25),
            ]
        );

        /** @var Assessment $assessment2 */
        $assessment2 = Assessment::updateOrCreate(
            ['rehabilitation_case_id' => $case2->id],
            [
                'officer_id' => $petugasRehsos?->id,
                'assessment_date' => Carbon::now()->subDays(23)->toDateString(),
                'result' => 'Mbah Supartini berusia 74 tahun, hidup sendiri tanpa sanak keluarga. Rumah reot tidak layak, kesulitan memenuhi kebutuhan makan harian.',
                'service_needs' => 'Perawatan panti wreda jangka panjang, tempat tinggal layak, jaminan nutrisi dan kesehatan lansia.',
                'recommendation' => 'Direkomendasikan alih bina ke UPT Pelayanan Sosial Tresna Werdha (PSTW) Blitar.',
                'needs_referral' => true,
            ]
        );

        /** @var Referral $referral2 */
        $referral2 = Referral::updateOrCreate(
            ['referral_number' => 'RJK-202609-00002'],
            [
                'rehabilitation_case_id' => $case2->id,
                'assessment_id' => $assessment2->id,
                'referral_institution_id' => $pstwBlitar->id,
                'officer_id' => $petugasRehsos?->id,
                'referral_date' => Carbon::now()->subDays(20)->toDateString(),
                'status' => ReferralStatus::Completed,
                'service_result' => 'Klien telah resmi diterima sebagai penerima manfaat residensial PSTW Blitar.',
                'completed_at' => Carbon::now()->subDays(15),
            ]
        );

        MonitoringRecord::updateOrCreate(
            [
                'rehabilitation_case_id' => $case2->id,
                'referral_id' => $referral2->id,
                'monitoring_date' => Carbon::now()->subDays(5)->toDateString(),
            ],
            [
                'officer_id' => $petugasRehsos?->id,
                'progress' => 'Monitoring via koordinasi pekerja sosial PSTW Blitar. Mbah Supartini dalam keadaan sehat, aktif mengikuti kegiatan senam lansia dan kerohanian.',
                'result_notes' => 'Klien merasa nyaman dan terawat dengan baik.',
            ]
        );

        // ============================================================
        // KASUS 3: Anak Terlantar (Closed - Selesai Reunifikasi Keluarga)
        // ============================================================
        /** @var Client $client3 */
        $client3 = Client::updateOrCreate(
            ['name' => 'Bayu Prasetyo'],
            [
                'client_category_id' => $catAnak->id,
                'nik' => '3505131505180001',
                'birth_date' => Carbon::now()->subYears(8)->toDateString(),
                'gender' => 'male',
                'address' => 'Lingkungan Beru, Wlingi',
                'village_id' => $beruVillage?->id,
                'phone' => null,
            ]
        );

        RehabilitationCase::updateOrCreate(
            ['case_number' => 'RHS-202609-00003'],
            [
                'client_id' => $client3->id,
                'officer_id' => $petugasRehsos?->id,
                'handling_type' => HandlingType::Direct,
                'status' => RehabilitationCaseStatus::Closed,
                'handling_result' => 'Klien telah berhasil direunifikasikan kepada paman kandung dengan perjanjian pengasuhan tertulis bermaterai, serta difasilitasi paket bantuan perlengkapan sekolah dan pemutakhiran data bansos.',
                'received_at' => Carbon::now()->subDays(30),
                'closed_at' => Carbon::now()->subDays(3),
            ]
        );
    }
}
