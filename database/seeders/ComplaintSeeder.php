<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use App\Models\ComplaintAttachment;
use App\Models\ComplaintCategory;
use App\Models\Disposition;
use App\Models\RehabilitationCase;
use App\Models\StatusHistory;
use App\Models\User;
use App\Models\Village;
use App\Models\WorkUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ComplaintSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $petugasPengaduan = User::where('email', 'petugas.pengaduan@blitarkab.go.id')->first();
        $petugasRehsos = User::where('email', 'petugas.rehsos@blitarkab.go.id')->first();
        $budi = User::where('email', 'budi.santoso@gmail.com')->first();
        $siti = User::where('email', 'siti.aminah@gmail.com')->first();

        $kanigoroVillage = Village::where('code', '35.05.06.1001')->first();
        $satreyanVillage = Village::where('code', '35.05.06.1002')->first();
        $talunVillage = Village::where('code', '35.05.10.1001')->first();
        $beruVillage = Village::where('code', '35.05.13.1003')->first();

        $catPpks = ComplaintCategory::where('name', 'like', '%ODGJ%')->first();
        $catBansos = ComplaintCategory::where('name', 'like', '%Bantuan Sosial%')->first();
        $catLansia = ComplaintCategory::where('name', 'like', '%Lanjut Usia%')->first();
        $catAnak = ComplaintCategory::where('name', 'like', '%Anak%')->first();

        $rehsosUnit = WorkUnit::where('name', 'like', '%Rehabilitasi%')->first();

        // ============================================================
        // 1. ADU-202609-00001 (InHandling -> Linked to Case RHS-202609-00001)
        // ============================================================
        /** @var Complaint $c1 */
        $c1 = Complaint::updateOrCreate(
            ['complaint_number' => 'ADU-202609-00001'],
            [
                'complaint_category_id' => $catPpks->id,
                'reporter_id' => $budi?->id,
                'reporter_name' => 'Budi Santoso',
                'reporter_phone' => '085712345678',
                'location_detail' => 'Kios Blok Barat Pasar Kanigoro, Jl. Kusuma Bangsa',
                'village_id' => $kanigoroVillage->id,
                'description' => 'Ada seorang pria terlantar tampak mengalami gangguan jiwa berkeliaran di Pasar Kanigoro, tidak memakai baju layak dan meresahkan pengunjung pasar.',
                'reported_at' => Carbon::now()->subDays(13),
                'officer_id' => $petugasRehsos?->id,
                'status' => ComplaintStatus::InHandling,
                'verification_result' => 'Laporan diverifikasi valid melalui konfirmasi pengelola pasar dan Satpol PP Kecamatan Kanigoro.',
                'action_taken' => 'Tim Reaksi Cepat (TRC) Dinsos bersama Satpol PP telah mengevakuasi klien ke RSUD Wlingi dan membuka kasus rehabilitasi sosial.',
            ]
        );

        // Attachment
        ComplaintAttachment::firstOrCreate(
            [
                'complaint_id' => $c1->id,
                'file_path' => 'complaints/foto_odgj_pasar_kanigoro.jpg',
            ],
            [
                'type' => 'photo',
            ]
        );

        // Disposition
        Disposition::create([
            'dispositionable_type' => Complaint::class,
            'dispositionable_id' => $c1->id,
            'from_user_id' => $petugasPengaduan->id,
            'to_work_unit_id' => $rehsosUnit->id,
            'to_user_id' => $petugasRehsos?->id,
            'instructions' => 'Mohon tim TRC Rehsos segera lakukan penjangkauan lapangan dan evakuasi ke faskes.',
            'disposed_at' => Carbon::now()->subDays(12),
        ]);

        // Link with Rehab Case
        $rehabCase1 = RehabilitationCase::where('case_number', 'RHS-202609-00001')->first();
        if ($rehabCase1) {
            $rehabCase1->update(['complaint_id' => $c1->id]);
        }

        // Status history
        StatusHistory::create([
            'statusable_type' => Complaint::class,
            'statusable_id' => $c1->id,
            'from_status' => 'received',
            'to_status' => 'in_handling',
            'notes' => 'Laporan didisposisikan dan sedang ditangani TRC Rehsos.',
            'user_id' => $petugasPengaduan->id,
            'created_at' => Carbon::now()->subDays(12),
        ]);

        // ============================================================
        // 2. ADU-202609-00002 (Resolved - Bansos PKH)
        // ============================================================
        Complaint::updateOrCreate(
            ['complaint_number' => 'ADU-202609-00002'],
            [
                'complaint_category_id' => $catBansos->id,
                'reporter_id' => $siti?->id,
                'reporter_name' => 'Siti Aminah',
                'reporter_phone' => '085812345679',
                'location_detail' => 'RT 03 RW 01 Lingkungan Satreyan, Kanigoro',
                'village_id' => $satreyanVillage->id,
                'description' => 'Tetangga saya lansia tidak mampu Mbah Darmi sejak tahap 2 kemarin bansos PKH lansianya tidak cair, padahal kartu KKS masih aktif.',
                'reported_at' => Carbon::now()->subDays(7),
                'officer_id' => $petugasPengaduan?->id,
                'status' => ComplaintStatus::Resolved,
                'verification_result' => 'Pengecekan di SIKS-NG menemukan anomali NIK belum padan dengan data Capil terbaru karena pembaruan KK.',
                'action_taken' => 'Petugas telah berkoordinasi dengan Pendamping PKH Kanigoro untuk pemadanan NIK ke Dispendukcapil. Rekening bansos telah aktif dan bantuan disalurkan pada termin berikutnya.',
                'resolved_at' => Carbon::now()->subDays(2),
            ]
        );

        // ============================================================
        // 3. ADU-202609-00003 (Dispatched - Lansia Sakit)
        // ============================================================
        Complaint::updateOrCreate(
            ['complaint_number' => 'ADU-202609-00003'],
            [
                'complaint_category_id' => $catLansia->id,
                'reporter_id' => null,
                'reporter_name' => 'Kuswanto (Ketua RT)',
                'reporter_phone' => '081233445577',
                'location_detail' => 'Dusun Duren RT 04 RW 02, Desa Talun',
                'village_id' => $talunVillage->id,
                'description' => 'Warga lansia terlantar Mbah Kerto (80 th) sakit menahun dan tinggal sendirian di gubuk, butuh pertolongan medis dan perawatan panti.',
                'reported_at' => Carbon::now()->subDays(3),
                'officer_id' => $petugasPengaduan?->id,
                'status' => ComplaintStatus::Dispatched,
                'verification_result' => 'Laporan telah divalidasi dengan aparat Desa Talun.',
            ]
        );

        // ============================================================
        // 4. ADU-202609-00004 (Received - Laporan Baru)
        // ============================================================
        Complaint::updateOrCreate(
            ['complaint_number' => 'ADU-202609-00004'],
            [
                'complaint_category_id' => $catAnak->id,
                'reporter_id' => null,
                'reporter_name' => 'Warga Wlingi',
                'reporter_phone' => '082199887711',
                'location_detail' => 'Perempatan Lampu Merah Wlingi',
                'village_id' => $beruVillage->id,
                'description' => 'Ada anak kecil usia sekitar 7 tahun mengamen dan mengemis sampai larut malam di perempatan jalan.',
                'reported_at' => Carbon::now()->subHours(6),
                'status' => ComplaintStatus::Received,
            ]
        );

        // ============================================================
        // 5. ADU-202609-00005 (Duplicate of ADU-202609-00001)
        // ============================================================
        Complaint::updateOrCreate(
            ['complaint_number' => 'ADU-202609-00005'],
            [
                'complaint_category_id' => $catPpks->id,
                'reporter_id' => null,
                'reporter_name' => 'Agung Wibowo',
                'reporter_phone' => '087812345678',
                'location_detail' => 'Pasar Kanigoro',
                'village_id' => $kanigoroVillage->id,
                'description' => 'Lapor ada orang terlantar tidak berbusana di pasar kanigoro.',
                'reported_at' => Carbon::now()->subDays(11),
                'officer_id' => $petugasPengaduan?->id,
                'status' => ComplaintStatus::Duplicate,
                'duplicate_of_id' => $c1->id,
                'verification_result' => 'Laporan duplikat atas subjek yang sama dengan tiket ADU-202609-00001.',
            ]
        );
    }
}
