<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ApprovalDecision;
use App\Enums\DocumentVerificationStatus;
use App\Enums\MinistryDecision;
use App\Enums\PbiReactivationReason;
use App\Enums\ServiceRequestStatus;
use App\Models\Approval;
use App\Models\DtsenCertificate;
use App\Models\DtsenPurpose;
use App\Models\PbiReactivation;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestDocument;
use App\Models\ServiceRequirement;
use App\Models\ServiceType;
use App\Models\StatusHistory;
use App\Models\User;
use App\Models\Village;
use App\Models\WorkUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ServiceRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dtsenType = ServiceType::where('code', 'DTSEN')->first();
        $pbiType = ServiceType::where('code', 'PBI')->first();
        $atensiType = ServiceType::where('code', 'ATENSI')->first();
        $alatBantuType = ServiceType::where('code', 'ALAT_BANTU')->first();

        $linjamsos = WorkUnit::where('name', 'like', '%Linjamsos%')->first();
        $rehsos = WorkUnit::where('name', 'like', '%Rehabilitasi%')->first();

        $kadis = User::where('email', 'kadis@blitarkab.go.id')->first();
        $kabidLinjamsos = User::where('email', 'kabid.linjamsos@blitarkab.go.id')->first();
        $petugasDtsen = User::where('email', 'petugas.dtsen@blitarkab.go.id')->first();
        $petugasPbi = User::where('email', 'petugas.pbi@blitarkab.go.id')->first();
        $petugasRehsos = User::where('email', 'petugas.rehsos@blitarkab.go.id')->first();
        $operatorKanigoro = User::where('email', 'operator.kanigoro@blitarkab.go.id')->first();

        $budi = User::where('email', 'budi.santoso@gmail.com')->first();
        $siti = User::where('email', 'siti.aminah@gmail.com')->first();
        $joko = User::where('email', 'joko.susilo@gmail.com')->first();

        $satreyan = Village::where('code', '35.05.06.1002')->first();
        $kanigoro = Village::where('code', '35.05.06.1001')->first();
        $beru = Village::where('code', '35.05.13.1003')->first();
        $talun = Village::where('code', '35.05.10.1001')->first();

        $spmbPurpose = DtsenPurpose::where('code', 'spmb')->first();
        $kipPurpose = DtsenPurpose::where('code', 'kip_kuliah')->first();
        $pipPurpose = DtsenPurpose::where('code', 'pip')->first();
        $bansosPurpose = DtsenPurpose::where('code', 'bansos')->first();

        // ==========================================
        // 1. DTSEN-202609-00001 (Completed - SPMB)
        // ==========================================
        /** @var ServiceRequest $sr1 */
        $sr1 = ServiceRequest::updateOrCreate(
            ['request_number' => 'DTSEN-202609-00001'],
            [
                'service_type_id' => $dtsenType->id,
                'submitter_id' => $budi?->id,
                'applicant_name' => 'Budi Santoso',
                'applicant_nik' => '3505061507880005',
                'family_card_number' => '3505061507880001',
                'address' => 'RT 02 RW 03 Lingkungan Satreyan, Kanigoro',
                'village_id' => $satreyan->id,
                'phone' => '085712345678',
                'submitted_at' => Carbon::now()->subDays(4),
                'officer_id' => $petugasDtsen?->id,
                'work_unit_id' => $linjamsos?->id,
                'status' => ServiceRequestStatus::Completed,
                'is_priority' => false,
                'verification_result' => 'Data pemohon dan orang yang diterangkan padan di SIKS-NG Kemensos (Desil 2). Berkas lengkap dan memenuhi syarat SPMB afirmasi.',
                'officer_notes' => 'Telah dilakukan validasi berkas KTP dan KK asli.',
                'service_result' => 'Surat Keterangan DTSEN diterbitkan dengan nomor 400.9/012/409.105/2026 bertanda QR Code verifikasi.',
                'completed_at' => Carbon::now()->subDays(1),
            ]
        );

        // Documents
        $reqsDtsen = ServiceRequirement::where('service_type_id', $dtsenType->id)->get();
        foreach ($reqsDtsen as $req) {
            ServiceRequestDocument::updateOrCreate(
                [
                    'service_request_id' => $sr1->id,
                    'service_requirement_id' => $req->id,
                ],
                [
                    'file_path' => 'documents/dtsen_req_'.$sr1->id.'_'.$req->id.'.pdf',
                    'original_name' => $req->name.' - Budi Santoso.pdf',
                    'verification_status' => DocumentVerificationStatus::Valid,
                    'notes' => 'Dokumen sesuai dan terbaca jelas',
                ]
            );
        }

        // DTSEN Certificate
        /** @var DtsenCertificate $cert1 */
        $cert1 = DtsenCertificate::updateOrCreate(
            ['service_request_id' => $sr1->id],
            [
                'dtsen_purpose_id' => $spmbPurpose->id,
                'purpose_description' => 'Persyaratan SPMB Jalur Afirmasi SMAN 1 Talun',
                'subject_name' => 'Ananda Rizky Pratama',
                'subject_nik' => '3505062005080001',
                'relationship_to_applicant' => 'Anak Kandung',
                'is_registered' => true,
                'decile' => 2,
                'checked_at' => Carbon::now()->subDays(3),
                'checker_id' => $petugasDtsen?->id,
                'certificate_number' => '400.9/012/409.105/2026',
                'issued_at' => Carbon::now()->subDays(1),
                'valid_until' => Carbon::now()->addDays(90),
                'signer_id' => $kadis?->id,
                'file_path' => 'certificates/sk_dtsen_202609_00001.pdf',
                'verification_code' => 'VRF-DTSEN-202609-001',
            ]
        );

        // Approvals (Kabid & Kadis)
        Approval::updateOrCreate(
            [
                'approvable_type' => DtsenCertificate::class,
                'approvable_id' => $cert1->id,
                'step' => 1,
            ],
            [
                'approver_id' => $kabidLinjamsos->id,
                'decision' => ApprovalDecision::Approved,
                'notes' => 'Data sesuai SIKS-NG desil 2, telah diparaf.',
                'decided_at' => Carbon::now()->subDays(2),
            ]
        );

        Approval::updateOrCreate(
            [
                'approvable_type' => DtsenCertificate::class,
                'approvable_id' => $cert1->id,
                'step' => 2,
            ],
            [
                'approver_id' => $kadis->id,
                'decision' => ApprovalDecision::Approved,
                'notes' => 'Disetujui dan ditandatangani.',
                'decided_at' => Carbon::now()->subDays(1),
            ]
        );

        // Status histories
        $statusesSr1 = [
            ['from' => null, 'to' => 'submitted', 'time' => Carbon::now()->subDays(4)],
            ['from' => 'submitted', 'to' => 'document_check', 'time' => Carbon::now()->subDays(3)->subHours(10)],
            ['from' => 'document_check', 'to' => 'data_verification', 'time' => Carbon::now()->subDays(3)],
            ['from' => 'data_verification', 'to' => 'awaiting_approval', 'time' => Carbon::now()->subDays(2)],
            ['from' => 'awaiting_approval', 'to' => 'issued', 'time' => Carbon::now()->subDays(1)],
            ['from' => 'issued', 'to' => 'completed', 'time' => Carbon::now()->subDays(1)->addHours(2)],
        ];
        foreach ($statusesSr1 as $st) {
            StatusHistory::create([
                'statusable_type' => ServiceRequest::class,
                'statusable_id' => $sr1->id,
                'from_status' => $st['from'],
                'to_status' => $st['to'],
                'notes' => 'Status otomatis diperbarui sistem',
                'user_id' => $petugasDtsen?->id,
                'created_at' => $st['time'],
            ]);
        }

        // ==========================================
        // 2. DTSEN-202609-00002 (AwaitingApproval - KIP Kuliah)
        // ==========================================
        /** @var ServiceRequest $sr2 */
        $sr2 = ServiceRequest::updateOrCreate(
            ['request_number' => 'DTSEN-202609-00002'],
            [
                'service_type_id' => $dtsenType->id,
                'submitter_id' => $siti?->id,
                'applicant_name' => 'Siti Aminah',
                'applicant_nik' => '3505065204920006',
                'family_card_number' => '3505065204920001',
                'address' => 'Jl. Merdeka No. 45 Kanigoro',
                'village_id' => $kanigoro->id,
                'phone' => '085812345679',
                'submitted_at' => Carbon::now()->subDays(2),
                'officer_id' => $petugasDtsen?->id,
                'work_unit_id' => $linjamsos?->id,
                'status' => ServiceRequestStatus::AwaitingApproval,
                'is_priority' => false,
                'verification_result' => 'Data pemohon terdaftar di SIKS-NG pada Desil 3. Persyaratan KIP Kuliah memenuhi syarat.',
                'officer_notes' => 'Telah diparaf oleh Kabid Linjamsos, menunggu tanda tangan Kepala Dinas.',
            ]
        );

        $cert2 = DtsenCertificate::updateOrCreate(
            ['service_request_id' => $sr2->id],
            [
                'dtsen_purpose_id' => $kipPurpose->id,
                'purpose_description' => 'Pendaftaran Beasiswa KIP Kuliah Universitas Brawijaya',
                'subject_name' => 'Siti Aminah',
                'subject_nik' => '3505065204920006',
                'relationship_to_applicant' => 'Diri Sendiri',
                'is_registered' => true,
                'decile' => 3,
                'checked_at' => Carbon::now()->subDays(1),
                'checker_id' => $petugasDtsen?->id,
                'signer_id' => $kadis?->id,
                'verification_code' => 'VRF-DTSEN-202609-002',
            ]
        );

        Approval::updateOrCreate(
            [
                'approvable_type' => DtsenCertificate::class,
                'approvable_id' => $cert2->id,
                'step' => 1,
            ],
            [
                'approver_id' => $kabidLinjamsos->id,
                'decision' => ApprovalDecision::Approved,
                'notes' => 'Desil 3 sesuai batas KIP Kuliah (maksimal Desil 4). Diparaf.',
                'decided_at' => Carbon::now()->subHours(6),
            ]
        );

        Approval::updateOrCreate(
            [
                'approvable_type' => DtsenCertificate::class,
                'approvable_id' => $cert2->id,
                'step' => 2,
            ],
            [
                'approver_id' => $kadis->id,
                'decision' => ApprovalDecision::Pending,
                'notes' => null,
            ]
        );

        // ==========================================
        // 3. DTSEN-202609-00003 (DataVerification - PIP)
        // ==========================================
        $sr3 = ServiceRequest::updateOrCreate(
            ['request_number' => 'DTSEN-202609-00003'],
            [
                'service_type_id' => $dtsenType->id,
                'submitter_id' => $operatorKanigoro?->id,
                'applicant_name' => 'Supardi',
                'applicant_nik' => '3505061108740001',
                'family_card_number' => '3505061108740005',
                'address' => 'Dusun Tlogo, Desa Tlogo, Kanigoro',
                'village_id' => $satreyan->id,
                'phone' => '082345678901',
                'submitted_at' => Carbon::now()->subHours(10),
                'officer_id' => $petugasDtsen?->id,
                'work_unit_id' => $linjamsos?->id,
                'status' => ServiceRequestStatus::DataVerification,
                'is_priority' => false,
                'officer_notes' => 'Petugas sedang mencocokkan NIK pemohon pada basis data SIKS-NG Kemensos.',
            ]
        );

        DtsenCertificate::updateOrCreate(
            ['service_request_id' => $sr3->id],
            [
                'dtsen_purpose_id' => $pipPurpose->id,
                'purpose_description' => 'Pengusulan Bantuan Program Indonesia Pintar (PIP) SMP',
                'subject_name' => 'Bagus Pratama',
                'subject_nik' => '3505061208110002',
                'relationship_to_applicant' => 'Anak Kandung',
                'is_registered' => false,
                'verification_code' => 'VRF-DTSEN-202609-003',
            ]
        );

        // ==========================================
        // 4. DTSEN-202609-00004 (RevisionRequested - Berkas KK Buram)
        // ==========================================
        ServiceRequest::updateOrCreate(
            ['request_number' => 'DTSEN-202609-00004'],
            [
                'service_type_id' => $dtsenType->id,
                'submitter_id' => $joko?->id,
                'applicant_name' => 'Joko Susilo',
                'applicant_nik' => '3505131002800007',
                'family_card_number' => '3505131002800001',
                'address' => 'Lingkungan Beru, Wlingi',
                'village_id' => $beru->id,
                'phone' => '085912345680',
                'submitted_at' => Carbon::now()->subDays(1),
                'officer_id' => $petugasDtsen?->id,
                'work_unit_id' => $linjamsos?->id,
                'status' => ServiceRequestStatus::RevisionRequested,
                'is_priority' => false,
                'officer_notes' => 'Foto Kartu Keluarga buram dan terpotong pada bagian NIK orang tua. Mohon unggah ulang foto dokumen KK asli yang jelas.',
            ]
        );

        // ==========================================
        // 5. DTSEN-202609-00005 (Rejected - Melebihi Batas Desil)
        // ==========================================
        $sr5 = ServiceRequest::updateOrCreate(
            ['request_number' => 'DTSEN-202609-00005'],
            [
                'service_type_id' => $dtsenType->id,
                'submitter_id' => null,
                'applicant_name' => 'Hendro Purnomo',
                'applicant_nik' => '3505101402820003',
                'family_card_number' => '3505101402820001',
                'address' => 'Desa Talun, Kecamatan Talun',
                'village_id' => $talun->id,
                'phone' => '081398765432',
                'submitted_at' => Carbon::now()->subDays(3),
                'officer_id' => $petugasDtsen?->id,
                'work_unit_id' => $linjamsos?->id,
                'status' => ServiceRequestStatus::Rejected,
                'is_priority' => false,
                'verification_result' => 'Terdaftar pada Desil 8 di basis data SIKS-NG Kemensos.',
                'rejection_reason' => 'Hasil verifikasi SIKS-NG menunjukkan posisi pemohon pada Desil 8. Ketentuan batas maksimal untuk keperluan usulan bantuan sosial adalah Desil 4.',
                'completed_at' => Carbon::now()->subDays(2),
            ]
        );

        DtsenCertificate::updateOrCreate(
            ['service_request_id' => $sr5->id],
            [
                'dtsen_purpose_id' => $bansosPurpose->id,
                'purpose_description' => 'Pengusulan Bantuan Sosial Sembako / PKH',
                'subject_name' => 'Hendro Purnomo',
                'subject_nik' => '3505101402820003',
                'relationship_to_applicant' => 'Diri Sendiri',
                'is_registered' => true,
                'decile' => 8,
                'checked_at' => Carbon::now()->subDays(2),
                'checker_id' => $petugasDtsen?->id,
                'verification_code' => 'VRF-DTSEN-202609-005',
            ]
        );

        // ==========================================
        // 6. PBI-202609-00001 (Completed - Darurat Medis Reaktivasi)
        // ==========================================
        /** @var ServiceRequest $pbi1 */
        $pbi1 = ServiceRequest::updateOrCreate(
            ['request_number' => 'PBI-202609-00001'],
            [
                'service_type_id' => $pbiType->id,
                'submitter_id' => $joko?->id,
                'applicant_name' => 'Joko Susilo',
                'applicant_nik' => '3505131002800007',
                'family_card_number' => '3505131002800001',
                'address' => 'Lingkungan Beru RT 01 RW 02, Wlingi',
                'village_id' => $beru->id,
                'phone' => '085912345680',
                'submitted_at' => Carbon::now()->subDays(14),
                'officer_id' => $petugasPbi?->id,
                'work_unit_id' => $linjamsos?->id,
                'status' => ServiceRequestStatus::Completed,
                'is_priority' => true,
                'verification_result' => 'Pasien rawat inap darurat di RSUD Ngudi Waluyo Wlingi. Masuk kategori darurat medis mendesak, desil 2.',
                'service_result' => 'Usulan disetujui Kemensos RI dan kepesertaan telah aktif kembali di BPJS Kesehatan per tanggal '.Carbon::now()->subDays(2)->format('d/m/Y'),
                'completed_at' => Carbon::now()->subDays(2),
            ]
        );

        $pbiDetail1 = PbiReactivation::updateOrCreate(
            ['service_request_id' => $pbi1->id],
            [
                'participant_name' => 'Joko Susilo',
                'participant_nik' => '3505131002800007',
                'bpjs_card_number' => '0001234567891',
                'deactivated_date' => Carbon::now()->subMonths(2)->toDateString(),
                'reason' => PbiReactivationReason::Emergency,
                'health_facility_name' => 'RSUD Ngudi Waluyo Wlingi',
                'health_letter_number' => '445/892/RSUD/2026',
                'decile' => 2,
                'eligibility_notes' => 'Kondisi gawat darurat bedah abdomen, dirawat di ruang ICU RSUD Wlingi.',
                'recommendation_number' => '440/045/409.105/2026',
                'recommendation_issued_at' => Carbon::now()->subDays(12),
                'signer_id' => $kadis?->id,
                'proposed_to_ministry_at' => Carbon::now()->subDays(11),
                'ministry_decision' => MinistryDecision::Approved,
                'ministry_decided_at' => Carbon::now()->subDays(4),
                'reactivated_date' => Carbon::now()->subDays(2)->toDateString(),
            ]
        );

        Approval::updateOrCreate(
            [
                'approvable_type' => PbiReactivation::class,
                'approvable_id' => $pbiDetail1->id,
                'step' => 1,
            ],
            [
                'approver_id' => $kadis->id,
                'decision' => ApprovalDecision::Approved,
                'notes' => 'Rekomendasi reaktivasi darurat medis disetujui.',
                'decided_at' => Carbon::now()->subDays(12),
            ]
        );

        // ==========================================
        // 7. PBI-202609-00002 (ProposedToMinistry - Penyakit Kronis)
        // ==========================================
        $pbi2 = ServiceRequest::updateOrCreate(
            ['request_number' => 'PBI-202609-00002'],
            [
                'service_type_id' => $pbiType->id,
                'submitter_id' => $budi?->id,
                'applicant_name' => 'Budi Santoso',
                'applicant_nik' => '3505061507880005',
                'family_card_number' => '3505061507880001',
                'address' => 'Satreyan, Kanigoro',
                'village_id' => $satreyan->id,
                'phone' => '085712345678',
                'submitted_at' => Carbon::now()->subDays(5),
                'officer_id' => $petugasPbi?->id,
                'work_unit_id' => $linjamsos?->id,
                'status' => ServiceRequestStatus::ProposedToMinistry,
                'is_priority' => false,
                'verification_result' => 'Pasien cuci darah rutin, berkas lengkap, rekomendasi diterbitkan.',
                'officer_notes' => 'Data usulan telah diinput pada aplikasi SIKS-NG Kemensos menu Usulan Reaktivasi PBI-JK.',
            ]
        );

        PbiReactivation::updateOrCreate(
            ['service_request_id' => $pbi2->id],
            [
                'participant_name' => 'Mardi Utomo',
                'participant_nik' => '3505060105550001',
                'bpjs_card_number' => '0001987654321',
                'deactivated_date' => Carbon::now()->subMonths(3)->toDateString(),
                'reason' => PbiReactivationReason::Chronic,
                'health_facility_name' => 'RSUD Srengat',
                'health_letter_number' => '440/123/RSUDS/2026',
                'decile' => 3,
                'eligibility_notes' => 'Pasien gagal ginjal kronis butuh hemodialisa rutin 2x seminggu.',
                'recommendation_number' => '440/058/409.105/2026',
                'recommendation_issued_at' => Carbon::now()->subDays(3),
                'signer_id' => $kadis?->id,
                'proposed_to_ministry_at' => Carbon::now()->subDays(2),
                'ministry_decision' => MinistryDecision::Pending,
            ]
        );

        // ==========================================
        // 8. PBI-202609-00003 (EligibilityVerification - Darurat Medis)
        // ==========================================
        $pbi3 = ServiceRequest::updateOrCreate(
            ['request_number' => 'PBI-202609-00003'],
            [
                'service_type_id' => $pbiType->id,
                'submitter_id' => null,
                'applicant_name' => 'Kusnan',
                'applicant_nik' => '3505061904700002',
                'family_card_number' => '3505061904700001',
                'address' => 'Desa Gaprang, Kanigoro',
                'village_id' => $satreyan->id,
                'phone' => '081299887766',
                'submitted_at' => Carbon::now()->subHours(8),
                'officer_id' => $petugasPbi?->id,
                'work_unit_id' => $linjamsos?->id,
                'status' => ServiceRequestStatus::EligibilityVerification,
                'is_priority' => true,
                'officer_notes' => 'Pengajuan prioritas darurat medis sedang diverifikasi data kepesertaan dan tanggal nonaktifnya.',
            ]
        );

        PbiReactivation::updateOrCreate(
            ['service_request_id' => $pbi3->id],
            [
                'participant_name' => 'Kusnan',
                'participant_nik' => '3505061904700002',
                'bpjs_card_number' => '0001445566778',
                'deactivated_date' => Carbon::now()->subMonths(1)->toDateString(),
                'reason' => PbiReactivationReason::Emergency,
                'health_facility_name' => 'RSUD Ngudi Waluyo Wlingi',
                'health_letter_number' => '445/102/IGD/2026',
            ]
        );

        // ==========================================
        // 9. Layanan 4: Bantuan ATENSI (Completed)
        // ==========================================
        ServiceRequest::updateOrCreate(
            ['request_number' => 'ATENSI-202609-00001'],
            [
                'service_type_id' => $atensiType->id,
                'submitter_id' => $siti?->id,
                'applicant_name' => 'Siti Aminah',
                'applicant_nik' => '3505065204920006',
                'family_card_number' => '3505065204920001',
                'address' => 'Kanigoro, Blitar',
                'village_id' => $kanigoro->id,
                'phone' => '085812345679',
                'submitted_at' => Carbon::now()->subDays(6),
                'officer_id' => $petugasRehsos?->id,
                'work_unit_id' => $rehsos?->id,
                'status' => ServiceRequestStatus::Completed,
                'is_priority' => false,
                'verification_result' => 'Verifikasi berkas dan kondisi keluarga memenuhi kriteria penerima ATENSI nutrisi anak.',
                'assessment_notes' => 'Keluarga rentan prasejahtera dengan balita resiko stunting.',
                'service_result' => 'Paket bantuan nutrisi ATENSI telah diserahkan langsung kepada keluarga penerima manfaat.',
                'completed_at' => Carbon::now()->subDays(1),
            ]
        );

        // ==========================================
        // 10. Layanan 4: Bantuan Kursi Roda Disabilitas (InProcess)
        // ==========================================
        ServiceRequest::updateOrCreate(
            ['request_number' => 'ALAT-202609-00001'],
            [
                'service_type_id' => $alatBantuType->id,
                'submitter_id' => null,
                'applicant_name' => 'Wahyudi Pratama',
                'applicant_nik' => '3505062207900003',
                'family_card_number' => '3505062207900001',
                'address' => 'Desa Satreyan, Kanigoro',
                'village_id' => $satreyan->id,
                'phone' => '085233445566',
                'submitted_at' => Carbon::now()->subDays(2),
                'officer_id' => $petugasRehsos?->id,
                'work_unit_id' => $rehsos?->id,
                'status' => ServiceRequestStatus::InProcess,
                'is_priority' => false,
                'verification_result' => 'Persyaratan medis dokter spesialis saraf terpenuhi (Disabilitas Fisik Paraplegia).',
                'assessment_notes' => 'Rekomendasi kebutuhan: Kursi Roda Standar Dewasa 1 unit.',
            ]
        );
    }
}
