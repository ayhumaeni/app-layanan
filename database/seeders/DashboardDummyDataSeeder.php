<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ApprovalDecision;
use App\Enums\ComplaintStatus;
use App\Enums\HandlingType;
use App\Enums\MinistryDecision;
use App\Enums\PbiReactivationReason;
use App\Enums\ReferralStatus;
use App\Enums\RehabilitationCaseStatus;
use App\Enums\ServiceRequestStatus;
use App\Models\Approval;
use App\Models\Assessment;
use App\Models\Client;
use App\Models\ClientCategory;
use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\Disposition;
use App\Models\DtsenCertificate;
use App\Models\DtsenPurpose;
use App\Models\InformationPage;
use App\Models\MonitoringRecord;
use App\Models\NumberSequence;
use App\Models\PageVisit;
use App\Models\PbiReactivation;
use App\Models\Referral;
use App\Models\ReferralInstitution;
use App\Models\RehabilitationCase;
use App\Models\SearchLog;
use App\Models\ServiceRequest;
use App\Models\ServiceType;
use App\Models\User;
use App\Models\Village;
use App\Models\WorkUnit;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardDummyDataSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command?->info('Memulai seeding data dummy dashboard SAPA SOSIAL (> 1000 data)...');

        // Master data lookups
        $dtsenType = ServiceType::where('code', 'DTSEN')->firstOrFail();
        $pbiType = ServiceType::where('code', 'PBI')->firstOrFail();
        $atensiType = ServiceType::where('code', 'ATENSI')->first();
        $alatBantuType = ServiceType::where('code', 'ALAT_BANTU')->first();

        $linjamsos = WorkUnit::where('name', 'like', '%Linjamsos%')->first();
        $rehsosUnit = WorkUnit::where('name', 'like', '%Rehabilitasi%')->first();
        $puskesosUnit = WorkUnit::where('name', 'like', '%Front Office%')->first();

        $kadis = User::where('email', 'kadis@blitarkab.go.id')->first();
        $kabidLinjamsos = User::where('email', 'kabid.linjamsos@blitarkab.go.id')->first();
        $petugasDtsen = User::where('email', 'petugas.dtsen@blitarkab.go.id')->first();
        $petugasPbi = User::where('email', 'petugas.pbi@blitarkab.go.id')->first();
        $petugasRehsos = User::where('email', 'petugas.rehsos@blitarkab.go.id')->first();
        $petugasPengaduan = User::where('email', 'petugas.pengaduan@blitarkab.go.id')->first();

        $dtsenPurposes = DtsenPurpose::all();
        $complaintCategories = ComplaintCategory::all();
        $clientCategories = ClientCategory::all();
        $referralInstitutions = ReferralInstitution::all();
        $informationPages = InformationPage::all();
        $villages = Village::with('district')->get();
        $communityUsers = User::role('masyarakat')->get();

        if ($villages->isEmpty()) {
            $this->command?->error('Data Desa / Kelurahan belum ada. Silakan jalankan DistrictVillageSeeder terlebih dahulu.');

            return;
        }

        // Sequence generator per prefix and period (YYYYMM)
        $sequenceCounters = [];
        $getNextNumber = function (string $prefix, string $period) use (&$sequenceCounters): string {
            $key = "{$prefix}-{$period}";
            if (! isset($sequenceCounters[$key])) {
                $pattern = "{$prefix}-{$period}-%";
                $srMax = DB::table('service_requests')->where('request_number', 'like', $pattern)->max('request_number');
                $cmpMax = DB::table('complaints')->where('complaint_number', 'like', $pattern)->max('complaint_number');
                $rhsMax = DB::table('rehabilitation_cases')->where('case_number', 'like', $pattern)->max('case_number');
                $rjkMax = DB::table('referrals')->where('referral_number', 'like', $pattern)->max('referral_number');
                $seqMax = DB::table('number_sequences')->where('prefix', $prefix)->where('period', $period)->value('last_number') ?? 0;

                $maxVal = (int) $seqMax;
                foreach ([$srMax, $cmpMax, $rhsMax, $rjkMax] as $val) {
                    if ($val) {
                        $parts = explode('-', (string) $val);
                        $num = (int) end($parts);
                        if ($num > $maxVal) {
                            $maxVal = $num;
                        }
                    }
                }
                $sequenceCounters[$key] = $maxVal;
            }
            $sequenceCounters[$key]++;

            return sprintf('%s-%s-%05d', $prefix, $period, $sequenceCounters[$key]);
        };

        // Helper: Generate realistic NIK for Blitar (3505...)
        $generateNik = function (Village $village, string $gender, Carbon $birthDate): string {
            $districtCode = str_replace('.', '', (string) $village->district?->code);
            if (strlen($districtCode) < 6) {
                $districtCode = '350506';
            }
            $day = (int) $birthDate->format('d');
            if ($gender === 'female') {
                $day += 40;
            }
            $dayStr = str_pad((string) $day, 2, '0', STR_PAD_LEFT);
            $monthStr = $birthDate->format('m');
            $yearStr = $birthDate->format('y');
            $seq = str_pad((string) mt_rand(1, 999), 4, '0', STR_PAD_LEFT);

            return substr($districtCode, 0, 6).$dayStr.$monthStr.$yearStr.$seq;
        };

        $blitarDusuns = [
            'Krajan', 'Sumberrejo', 'Genengan', 'Kebonsari', 'Turi', 'Sendang',
            'Sukorejo', 'Centong', 'Rejoso', 'Ringinanyar', 'Boro', 'Precet',
            'Sawahan', 'Wonorejo', 'Bantengan', 'Kalisapu', 'Tegalrejo', 'Plumpung',
        ];

        $generateAddress = function (Village $village) use ($blitarDusuns): string {
            $dusun = $blitarDusuns[array_rand($blitarDusuns)];
            $rt = str_pad((string) mt_rand(1, 12), 2, '0', STR_PAD_LEFT);
            $rw = str_pad((string) mt_rand(1, 6), 2, '0', STR_PAD_LEFT);

            return "RT {$rt} RW {$rw} Dusun {$dusun}, Desa {$village->name}, Kec. {$village->district?->name}";
        };

        $generatePhone = function (): string {
            $prefixes = ['0812', '0813', '0821', '0822', '0852', '0853', '0857', '0858', '0878', '0881'];

            return $prefixes[array_rand($prefixes)].mt_rand(10000000, 99999999);
        };

        // Current application context time: Sep 30, 2026
        $now = Carbon::create(2026, 9, 30, 16, 0, 0);

        // Pre-generate 900 dates for ServiceRequests:
        // - 150 dates in the last 14 days (Sep 17, 2026 - Sep 30, 2026) for active daily trend lines
        // - 750 dates spread from Jan 1, 2026 to Sep 16, 2026
        $srDates = [];
        for ($day = 17; $day <= 30; $day++) {
            $countForDay = mt_rand(9, 14);
            for ($k = 0; $k < $countForDay; $k++) {
                $hour = mt_rand(8, 16);
                $minute = mt_rand(0, 59);
                $second = mt_rand(0, 59);
                $srDates[] = Carbon::create(2026, 9, $day, $hour, $minute, $second);
            }
        }
        $remainingSrCount = 900 - count($srDates);
        for ($i = 0; $i < $remainingSrCount; $i++) {
            $randomMonth = mt_rand(1, 9);
            $maxDay = ($randomMonth === 9) ? 16 : 28;
            $randomDay = mt_rand(1, $maxDay);
            $hour = mt_rand(8, 16);
            $minute = mt_rand(0, 59);
            $srDates[] = Carbon::create(2026, $randomMonth, $randomDay, $hour, $minute, mt_rand(0, 59));
        }
        shuffle($srDates);

        // Pre-generate 320 dates for Complaints:
        // - 70 dates in the last 14 days (Sep 17 - Sep 30)
        // - 250 dates spread from Jan 1 to Sep 16
        $complaintDates = [];
        for ($day = 17; $day <= 30; $day++) {
            $countForDay = mt_rand(4, 7);
            for ($k = 0; $k < $countForDay; $k++) {
                $complaintDates[] = Carbon::create(2026, 9, $day, mt_rand(8, 17), mt_rand(0, 59), mt_rand(0, 59));
            }
        }
        $remainingComplaintCount = 320 - count($complaintDates);
        for ($i = 0; $i < $remainingComplaintCount; $i++) {
            $randomMonth = mt_rand(1, 9);
            $maxDay = ($randomMonth === 9) ? 16 : 28;
            $complaintDates[] = Carbon::create(2026, $randomMonth, mt_rand(1, $maxDay), mt_rand(8, 17), mt_rand(0, 59), mt_rand(0, 59));
        }
        shuffle($complaintDates);

        DB::beginTransaction();
        try {
            // =========================================================================
            // 1. SEEDING 900 SERVICE REQUESTS (520 DTSEN, 280 PBI, 50 ATENSI, 50 ALAT_BANTU)
            // =========================================================================
            $this->command?->info('1. Membuat 900 Service Requests beserta dokumen & relasi terkait...');

            $certSeq = (int) (DB::table('dtsen_certificates')->count() + 100);
            $recSeq = (int) (DB::table('pbi_reactivations')->count() + 100);

            for ($i = 0; $i < 900; $i++) {
                $village = $villages->random();
                $submittedAt = $srDates[$i];
                $period = $submittedAt->format('Ym');
                $isRecent = $submittedAt->diffInDays($now) <= 14;

                $gender = (mt_rand(0, 1) === 1) ? 'male' : 'female';
                $birthDate = (clone $submittedAt)->subYears(mt_rand(19, 65))->subDays(mt_rand(0, 365));
                $applicantNik = $generateNik($village, $gender, $birthDate);
                $familyCardNumber = '3505'.mt_rand(10, 99).$birthDate->format('dmY').mt_rand(10, 99);
                $applicantName = fake('id_ID')->name($gender);
                $address = $generateAddress($village);
                $phone = $generatePhone();
                $submitter = (mt_rand(1, 10) <= 4 && $communityUsers->isNotEmpty()) ? $communityUsers->random() : null;

                // Determine Service Type
                if ($i < 520) {
                    // -----------------------------------------------------------------
                    // DTSEN Service Request (520 records)
                    // -----------------------------------------------------------------
                    $requestNumber = $getNextNumber('DTSEN', $period);

                    // Determine Status Distribution
                    if ($i < 360) {
                        // 360 Completed / Issued
                        $status = ServiceRequestStatus::Completed;
                    } elseif ($i < 420) {
                        // 60 Awaiting Approval
                        $status = ServiceRequestStatus::AwaitingApproval;
                    } elseif ($i < 460) {
                        // 40 In Verification / Document Check / Data Verification
                        $status = [
                            ServiceRequestStatus::DocumentCheck,
                            ServiceRequestStatus::DataVerification,
                            ServiceRequestStatus::Verification,
                        ][mt_rand(0, 2)];
                    } elseif ($i < 495) {
                        // 35 Revision Requested
                        $status = ServiceRequestStatus::RevisionRequested;
                    } else {
                        // 25 Rejected
                        $status = ServiceRequestStatus::Rejected;
                    }

                    $completedAt = ($status === ServiceRequestStatus::Completed)
                        ? (clone $submittedAt)->addDays(mt_rand(1, 3))->addHours(mt_rand(1, 8))
                        : null;
                    if ($completedAt && $completedAt->gt($now)) {
                        $completedAt = clone $now;
                    }

                    $sr = ServiceRequest::create([
                        'request_number' => $requestNumber,
                        'service_type_id' => $dtsenType->id,
                        'submitter_id' => $submitter?->id,
                        'applicant_name' => $applicantName,
                        'applicant_nik' => $applicantNik,
                        'family_card_number' => $familyCardNumber,
                        'address' => $address,
                        'village_id' => $village->id,
                        'phone' => $phone,
                        'submitted_at' => $submittedAt,
                        'officer_id' => $petugasDtsen?->id,
                        'work_unit_id' => $linjamsos?->id,
                        'status' => $status,
                        'is_priority' => (mt_rand(1, 100) <= 6),
                        'verification_result' => ($status === ServiceRequestStatus::Rejected)
                            ? 'Data NIK pemohon tidak ditemukan dalam basis data DTKS/DTSEN Kabupaten Blitar.'
                            : 'Berkas identitas dan kesesuaian data keluarga telah divalidasi dengan SIKS-NG.',
                        'officer_notes' => 'Pemeriksaan berkas pemohon dan kriteria desil terpenuhi sesuai ketentuan.',
                        'service_result' => ($status === ServiceRequestStatus::Completed)
                            ? 'Surat Keterangan Terdaftar DTSEN telah diterbitkan dan ditandatangani secara elektronik.'
                            : null,
                        'rejection_reason' => ($status === ServiceRequestStatus::Rejected)
                            ? 'Pemohon tidak terdaftar dalam pangkalan data DTSEN / tingkat desil melebihi batas ketentuan bansos.'
                            : null,
                        'completed_at' => $completedAt,
                    ]);

                    // Create DtsenCertificate for DTSEN requests
                    $purpose = $dtsenPurposes->random();
                    $relationships = ['Diri Sendiri', 'Anak', 'Istri', 'Suami', 'Orang Tua'];
                    $relationship = $relationships[array_rand($relationships)];
                    $subjectName = ($relationship === 'Diri Sendiri') ? $applicantName : fake('id_ID')->name();
                    $subjectNik = ($relationship === 'Diri Sendiri') ? $applicantNik : $generateNik($village, 'male', (clone $birthDate)->addYears(mt_rand(-10, 10)));

                    // Realistic Decile distribution: Desil 1-4 more frequent
                    $decileRoll = mt_rand(1, 100);
                    if ($decileRoll <= 35) {
                        $decile = 1;
                    } elseif ($decileRoll <= 60) {
                        $decile = 2;
                    } elseif ($decileRoll <= 80) {
                        $decile = 3;
                    } elseif ($decileRoll <= 90) {
                        $decile = 4;
                    } else {
                        $decile = mt_rand(5, 8);
                    }

                    $isIssued = ($status === ServiceRequestStatus::Completed || $status === ServiceRequestStatus::Issued);
                    $issuedAt = $isIssued ? ($completedAt ?? (clone $submittedAt)->addDays(2)) : null;
                    if ($issuedAt && $issuedAt->gt($now)) {
                        $issuedAt = clone $now;
                    }

                    $certSeq++;
                    $certificateNumber = $isIssued
                        ? sprintf('400.9/%04d/409.105/%s', $certSeq, $submittedAt->format('Y'))
                        : null;

                    DtsenCertificate::create([
                        'service_request_id' => $sr->id,
                        'dtsen_purpose_id' => $purpose->id,
                        'purpose_description' => 'Persyaratan pengajuan '.$purpose->name.' bagi keluarga prasejahtera.',
                        'subject_name' => $subjectName,
                        'subject_nik' => $subjectNik,
                        'relationship_to_applicant' => $relationship,
                        'is_registered' => ($status !== ServiceRequestStatus::Rejected),
                        'decile' => $decile,
                        'checked_at' => (clone $submittedAt)->addHours(mt_rand(2, 24)),
                        'checker_id' => $petugasDtsen?->id,
                        'certificate_number' => $certificateNumber,
                        'issued_at' => $issuedAt,
                        'valid_until' => $issuedAt ? (clone $issuedAt)->addMonths(6)->toDateString() : null,
                        'signer_id' => (mt_rand(0, 1) === 1) ? $kadis?->id : $kabidLinjamsos?->id,
                        'file_path' => $isIssued ? "certificates/dtsen_{$sr->id}.pdf" : null,
                        'verification_code' => 'DTSEN-'.$submittedAt->format('Y').'-'.strtoupper(Str::random(8)),
                    ]);

                    // Create Approvals for AwaitingApproval or Completed
                    if ($status === ServiceRequestStatus::AwaitingApproval) {
                        $step = (mt_rand(1, 10) <= 6) ? 1 : 2;
                        $approver = ($step === 1) ? $kabidLinjamsos : $kadis;
                        Approval::create([
                            'approvable_type' => ServiceRequest::class,
                            'approvable_id' => $sr->id,
                            'step' => $step,
                            'approver_id' => $approver?->id ?? $kadis->id,
                            'decision' => ApprovalDecision::Pending->value,
                            'notes' => 'Menunggu verifikasi akhir dan tanda tangan basah / TTE pejabat.',
                            'decided_at' => null,
                        ]);
                    } elseif ($isIssued) {
                        Approval::create([
                            'approvable_type' => ServiceRequest::class,
                            'approvable_id' => $sr->id,
                            'step' => 1,
                            'approver_id' => $kabidLinjamsos?->id ?? $kadis->id,
                            'decision' => ApprovalDecision::Approved->value,
                            'notes' => 'Diparaf dan disetujui Kabid Linjamsos.',
                            'decided_at' => (clone $submittedAt)->addDays(1),
                        ]);
                        Approval::create([
                            'approvable_type' => ServiceRequest::class,
                            'approvable_id' => $sr->id,
                            'step' => 2,
                            'approver_id' => $kadis?->id,
                            'decision' => ApprovalDecision::Approved->value,
                            'notes' => 'Ditandatangani secara elektronik oleh Kepala Dinas.',
                            'decided_at' => $issuedAt,
                        ]);
                    }
                } elseif ($i < 800) {
                    // -----------------------------------------------------------------
                    // PBI Reaktivasi Service Request (280 records, index 520 to 799)
                    // -----------------------------------------------------------------
                    $pbiIndex = $i - 520;
                    $requestNumber = $getNextNumber('PBI', $period);

                    $hospitals = [
                        'RSUD Ngudi Waluyo Wlingi',
                        'RSUD Srengat Kabupaten Blitar',
                        'RSUD Mardi Waluyo Kota Blitar',
                        'Puskesmas Kanigoro',
                        'Puskesmas Wlingi',
                        'Puskesmas Srengat',
                        'Puskesmas Talun',
                        'Puskesmas Garum',
                    ];

                    $isEmergencyMedical = false;
                    $pbiReason = PbiReactivationReason::Other->value;

                    // Distribution for PBI
                    if ($pbiIndex < 35) {
                        // 35 Emergency Priority in-process (for PbiEmergencyPriorityWidget!)
                        $isEmergencyMedical = true;
                        $isPriority = true;
                        $pbiReason = (mt_rand(1, 10) <= 6) ? PbiReactivationReason::Emergency->value : PbiReactivationReason::Catastrophic->value;
                        $status = [
                            ServiceRequestStatus::Submitted,
                            ServiceRequestStatus::DocumentCheck,
                            ServiceRequestStatus::Verification,
                            ServiceRequestStatus::EligibilityVerification,
                            ServiceRequestStatus::AwaitingApproval,
                            ServiceRequestStatus::ProposedToMinistry,
                        ][mt_rand(0, 5)];
                    } elseif ($pbiIndex < 90) {
                        // 55 Proposed to Ministry (Some > 14 days ago for KPI "Tertahan di Kemensos")
                        $isPriority = (mt_rand(1, 10) <= 2);
                        $pbiReason = [PbiReactivationReason::Emergency->value, PbiReactivationReason::Catastrophic->value, PbiReactivationReason::Chronic->value, PbiReactivationReason::Newborn->value][mt_rand(0, 3)];
                        $status = ServiceRequestStatus::ProposedToMinistry;
                    } elseif ($pbiIndex < 130) {
                        // 40 In Process (Verification / Awaiting Approval)
                        $isPriority = false;
                        $pbiReason = [PbiReactivationReason::Chronic->value, PbiReactivationReason::Newborn->value, PbiReactivationReason::Other->value][mt_rand(0, 2)];
                        $status = [
                            ServiceRequestStatus::DocumentCheck,
                            ServiceRequestStatus::Verification,
                            ServiceRequestStatus::EligibilityVerification,
                            ServiceRequestStatus::AwaitingApproval,
                        ][mt_rand(0, 3)];
                    } elseif ($pbiIndex < 240) {
                        // 110 Approved / Reactivated / Completed
                        $isPriority = (mt_rand(1, 10) <= 3);
                        $pbiReason = [PbiReactivationReason::Emergency->value, PbiReactivationReason::Catastrophic->value, PbiReactivationReason::Chronic->value, PbiReactivationReason::Newborn->value][mt_rand(0, 3)];
                        $status = [
                            ServiceRequestStatus::Completed,
                            ServiceRequestStatus::Reactivated,
                            ServiceRequestStatus::MinistryApproved,
                        ][mt_rand(0, 2)];
                    } else {
                        // 40 Rejected / Revision
                        $isPriority = false;
                        $pbiReason = [PbiReactivationReason::Other->value, PbiReactivationReason::Chronic->value][mt_rand(0, 1)];
                        $status = (mt_rand(0, 1) === 1)
                            ? ServiceRequestStatus::MinistryRejected
                            : ServiceRequestStatus::Rejected;
                    }

                    $completedAt = ($status === ServiceRequestStatus::Completed || $status === ServiceRequestStatus::Reactivated)
                        ? (clone $submittedAt)->addDays(mt_rand(7, 21))
                        : null;
                    if ($completedAt && $completedAt->gt($now)) {
                        $completedAt = clone $now;
                    }

                    $sr = ServiceRequest::create([
                        'request_number' => $requestNumber,
                        'service_type_id' => $pbiType->id,
                        'submitter_id' => $submitter?->id,
                        'applicant_name' => $applicantName,
                        'applicant_nik' => $applicantNik,
                        'family_card_number' => $familyCardNumber,
                        'address' => $address,
                        'village_id' => $village->id,
                        'phone' => $phone,
                        'submitted_at' => $submittedAt,
                        'officer_id' => $petugasPbi?->id,
                        'work_unit_id' => $linjamsos?->id,
                        'status' => $status,
                        'is_priority' => $isPriority,
                        'verification_result' => ($status === ServiceRequestStatus::Rejected)
                            ? 'Peserta tidak memenuhi kriteria non-aktif karena premi mandiri tertunggak / tidak masuk DTKS.'
                            : 'Kelayakan medis dan data kepesertaan BPJS telah terverifikasi dengan Dinas Kesehatan & Rumah Sakit.',
                        'officer_notes' => 'Pasien memerlukan reaktivasi segera untuk penjaminan pengobatan faskes rujukan.',
                        'service_result' => ($completedAt)
                            ? 'Rekomendasi disetujui Kemensos, kartu BPJS PBI-JK telah aktif kembali.'
                            : null,
                        'rejection_reason' => ($status === ServiceRequestStatus::Rejected || $status === ServiceRequestStatus::MinistryRejected)
                            ? 'Ditolak oleh sistem Kemensos karena kuota kepesertaan daerah atau data anomali.'
                            : null,
                        'completed_at' => $completedAt,
                    ]);

                    // PBI Reactivation Details
                    $recSeq++;
                    $hasRecommendation = in_array($status, [
                        ServiceRequestStatus::ProposedToMinistry,
                        ServiceRequestStatus::MinistryApproved,
                        ServiceRequestStatus::Reactivated,
                        ServiceRequestStatus::Completed,
                        ServiceRequestStatus::MinistryRejected,
                    ], true);

                    $recommendationNumber = $hasRecommendation
                        ? sprintf('440/REC-%04d/409.105/%s', $recSeq, $submittedAt->format('Y'))
                        : null;
                    $recommendationIssuedAt = $hasRecommendation
                        ? (clone $submittedAt)->addDays(mt_rand(1, 2))
                        : null;

                    // Proposed to Ministry timestamp logic:
                    $proposedToMinistryAt = null;
                    $ministryDecision = MinistryDecision::Pending->value;
                    $ministryDecidedAt = null;
                    $reactivatedDate = null;

                    if ($hasRecommendation) {
                        if ($status === ServiceRequestStatus::ProposedToMinistry) {
                            // Split: some older than 14 days ("Tertahan di Kemensos"), some fresh
                            if ($pbiIndex >= 35 && $pbiIndex < 65) {
                                // 30 records proposed 18 - 45 days ago!
                                $proposedToMinistryAt = (clone $now)->subDays(mt_rand(18, 45));
                            } else {
                                $proposedToMinistryAt = (clone $now)->subDays(mt_rand(1, 12));
                            }
                            $ministryDecision = MinistryDecision::Pending->value;
                        } elseif (in_array($status, [ServiceRequestStatus::MinistryApproved, ServiceRequestStatus::Reactivated, ServiceRequestStatus::Completed], true)) {
                            $proposedToMinistryAt = (clone $submittedAt)->addDays(mt_rand(2, 4));
                            $ministryDecision = MinistryDecision::Approved->value;
                            $ministryDecidedAt = (clone $proposedToMinistryAt)->addDays(mt_rand(5, 14));
                            if ($ministryDecidedAt->gt($now)) {
                                $ministryDecidedAt = clone $now;
                            }
                            $reactivatedDate = (clone $ministryDecidedAt)->addDays(1)->toDateString();
                        } elseif ($status === ServiceRequestStatus::MinistryRejected) {
                            $proposedToMinistryAt = (clone $submittedAt)->addDays(mt_rand(2, 4));
                            $ministryDecision = MinistryDecision::Rejected->value;
                            $ministryDecidedAt = (clone $proposedToMinistryAt)->addDays(mt_rand(5, 12));
                        }
                    }

                    PbiReactivation::create([
                        'service_request_id' => $sr->id,
                        'participant_name' => $applicantName,
                        'participant_nik' => $applicantNik,
                        'bpjs_card_number' => '000'.mt_rand(1000000000, 9999999999),
                        'deactivated_date' => (clone $submittedAt)->subMonths(mt_rand(2, 6))->toDateString(),
                        'reason' => $pbiReason,
                        'health_facility_name' => $hospitals[array_rand($hospitals)],
                        'health_letter_number' => sprintf('SKD/%d/RSUD/%s', mt_rand(100, 999), $submittedAt->format('Y')),
                        'decile' => mt_rand(1, 4),
                        'eligibility_notes' => 'Pasien berdomisili di Blitar dan memenuhi kriteria darurat medis / desil PBI-JK.',
                        'recommendation_number' => $recommendationNumber,
                        'recommendation_issued_at' => $recommendationIssuedAt,
                        'signer_id' => $kabidLinjamsos?->id ?? $kadis?->id,
                        'proposed_to_ministry_at' => $proposedToMinistryAt,
                        'ministry_decision' => $ministryDecision,
                        'ministry_decided_at' => $ministryDecidedAt,
                        'reactivated_date' => $reactivatedDate,
                    ]);
                } elseif ($i < 850) {
                    // -----------------------------------------------------------------
                    // ATENSI Service Request (50 records, index 800 to 849)
                    // -----------------------------------------------------------------
                    $requestNumber = $getNextNumber('ATENSI', $period);
                    $status = [
                        ServiceRequestStatus::Submitted,
                        ServiceRequestStatus::Verification,
                        ServiceRequestStatus::Assessment,
                        ServiceRequestStatus::InProcess,
                        ServiceRequestStatus::Completed,
                    ][mt_rand(0, 4)];

                    ServiceRequest::create([
                        'request_number' => $requestNumber,
                        'service_type_id' => $atensiType?->id ?? $dtsenType->id,
                        'submitter_id' => $submitter?->id,
                        'applicant_name' => $applicantName,
                        'applicant_nik' => $applicantNik,
                        'family_card_number' => $familyCardNumber,
                        'address' => $address,
                        'village_id' => $village->id,
                        'phone' => $phone,
                        'submitted_at' => $submittedAt,
                        'officer_id' => $petugasRehsos?->id,
                        'work_unit_id' => $rehsosUnit?->id,
                        'status' => $status,
                        'is_priority' => (mt_rand(1, 10) <= 2),
                        'verification_result' => 'Pemeriksaan kebutuhan asistensi rehabilitasi sosial (nutrisi / sembako / perawatan).',
                        'officer_notes' => 'Telah dilakukan kunjungan rumah (home visit) oleh pendamping sosial.',
                        'service_result' => ($status === ServiceRequestStatus::Completed) ? 'Paket bantuan ATENSI telah diserahkan kepada penerima manfaat.' : null,
                        'completed_at' => ($status === ServiceRequestStatus::Completed) ? (clone $submittedAt)->addDays(mt_rand(3, 10)) : null,
                    ]);
                } else {
                    // -----------------------------------------------------------------
                    // ALAT_BANTU Service Request (50 records, index 850 to 899)
                    // -----------------------------------------------------------------
                    $requestNumber = $getNextNumber('ALAT_BANTU', $period);
                    $tools = ['Kursi Roda Standar', 'Kursi Roda CP', 'Alat Bantu Dengar', 'Tongkat Ketiak', 'Walker Lansia', 'Kaki Palsu'];
                    $chosenTool = $tools[array_rand($tools)];
                    $status = [
                        ServiceRequestStatus::Submitted,
                        ServiceRequestStatus::Assessment,
                        ServiceRequestStatus::InProcess,
                        ServiceRequestStatus::Completed,
                    ][mt_rand(0, 3)];

                    ServiceRequest::create([
                        'request_number' => $requestNumber,
                        'service_type_id' => $alatBantuType?->id ?? $dtsenType->id,
                        'submitter_id' => $submitter?->id,
                        'applicant_name' => $applicantName,
                        'applicant_nik' => $applicantNik,
                        'family_card_number' => $familyCardNumber,
                        'address' => $address,
                        'village_id' => $village->id,
                        'phone' => $phone,
                        'submitted_at' => $submittedAt,
                        'officer_id' => $petugasRehsos?->id,
                        'work_unit_id' => $rehsosUnit?->id,
                        'status' => $status,
                        'is_priority' => (mt_rand(1, 10) <= 2),
                        'verification_result' => "Pemohon membutuhkan alat bantu jenis {$chosenTool} berdasarkan asesmen fisik.",
                        'officer_notes' => "Pengukuran spesifikasi alat bantu {$chosenTool} telah dicatat.",
                        'service_result' => ($status === ServiceRequestStatus::Completed) ? "Bantuan {$chosenTool} telah diserahterimakan." : null,
                        'completed_at' => ($status === ServiceRequestStatus::Completed) ? (clone $submittedAt)->addDays(mt_rand(5, 14)) : null,
                    ]);
                }
            }

            // =========================================================================
            // 2. SEEDING 320 COMPLAINTS (PENGADUAN MASYARAKAT)
            // =========================================================================
            $this->command?->info('2. Membuat 320 Pengaduan Masyarakat (Complaints)...');

            $complaintDescriptions = [
                'Bantuan Sosial' => [
                    'Keluarga kurang mampu di RT ini belum terdaftar di DTKS/PBI padahal kondisi rumah tidak layak huni.',
                    'Penerima PKH lansia tidak kunjung menerima kartu KKS padahal sudah ada pemberitahuan dari pendamping.',
                    'Bantuan sembako BLT tahap ini belum sampai ke warga rentan di dusun kami, mohon pengecekan data.',
                    'Warga miskin ekstrem di lingkungan kami belum mendapatkan bansos jenis apapun selama tahun 2026.',
                ],
                'ODGJ' => [
                    'Ada ODGJ terlantar di pinggir jalan raya desa, sering membahayakan pengguna jalan dan perlu penanganan.',
                    'Seorang pria tanpa identitas tampak mengalami gangguan jiwa berkeliaran di dekat pasar dan warung warga.',
                    'Warga melapor adanya ODGJ mengamuk di pemukiman, mohon tim TRC Dinsos segera melakukan penjangkauan.',
                ],
                'Lanjut Usia' => [
                    'Lansia sebatang kara hidup sendirian dalam kondisi sakit di gubuk reyot, membutuhkan perawatan darurat.',
                    'Nenek lansia terlantar tidak ada keluarga yang mengurus, tetangga bergantian memberi makan, butuh rujukan panti.',
                    'Warga lansia membutuhkan bantuan kursi roda dan tempat tidur medis karena stroke menahun.',
                ],
                'Anak' => [
                    'Anak usia sekolah terlantar putus sekolah karena orang tua meninggal dunia dan diasuh kerabat tidak mampu.',
                    'Dugaan penelantaran anak di bawah umur yang ditinggal orang tuanya bekerja keluar kota tanpa kabar.',
                    'Anak membutuhkan pendampingan sosial dan rujukan ke balai rehabilitasi anak.',
                ],
                'Disabilitas' => [
                    'Penyandang disabilitas ganda membutuhkan bantuan kursi roda dan terapi motorik di faskes rehabilitasi.',
                    'Warga difabel membutuhkan akses pelatihan kerja dan bantuan alat penunjang usaha mandiri.',
                ],
                'Umum' => [
                    'Laporan warga terkait dugaan penyaluran bantuan sosial yang salah sasaran di tingkat RT.',
                    'Permohonan klarifikasi prosedur pendaftaran surat keterangan miskin untuk pengobatan rumah sakit.',
                ],
            ];

            for ($j = 0; $j < 320; $j++) {
                $reportedAt = $complaintDates[$j];
                $period = $reportedAt->format('Ym');
                $complaintNumber = $getNextNumber('ADU', $period);

                $village = $villages->random();
                $category = $complaintCategories->random();
                $reporterGender = (mt_rand(0, 1) === 1) ? 'male' : 'female';
                $reporterName = fake('id_ID')->name($reporterGender);
                $reporterPhone = $generatePhone();
                $submitter = (mt_rand(1, 10) <= 3 && $communityUsers->isNotEmpty()) ? $communityUsers->random() : null;

                // Status distribution
                if ($j < 30) {
                    $cStatus = ComplaintStatus::Received;
                } elseif ($j < 55) {
                    $cStatus = ComplaintStatus::Verification;
                } elseif ($j < 70) {
                    $cStatus = ComplaintStatus::ClarificationRequested;
                } elseif ($j < 100) {
                    $cStatus = ComplaintStatus::Dispatched;
                } elseif ($j < 150) {
                    $cStatus = ComplaintStatus::InHandling;
                } elseif ($j < 295) {
                    $cStatus = ComplaintStatus::Resolved;
                } elseif ($j < 310) {
                    $cStatus = ComplaintStatus::Duplicate;
                } else {
                    $cStatus = ComplaintStatus::Invalid;
                }

                $resolvedAt = ($cStatus === ComplaintStatus::Resolved)
                    ? (clone $reportedAt)->addDays(mt_rand(1, 5))->addHours(mt_rand(1, 10))
                    : null;
                if ($resolvedAt && $resolvedAt->gt($now)) {
                    $resolvedAt = clone $now;
                }

                // Pick a realistic description based on category name
                $catName = $category->name;
                $descList = $complaintDescriptions['Umum'];
                if (str_contains($catName, 'Bantuan Sosial') || str_contains($catName, 'Bansos')) {
                    $descList = $complaintDescriptions['Bantuan Sosial'];
                } elseif (str_contains($catName, 'ODGJ') || str_contains($catName, 'PPKS')) {
                    $descList = $complaintDescriptions['ODGJ'];
                } elseif (str_contains($catName, 'Lanjut Usia') || str_contains($catName, 'Lansia')) {
                    $descList = $complaintDescriptions['Lanjut Usia'];
                } elseif (str_contains($catName, 'Anak')) {
                    $descList = $complaintDescriptions['Anak'];
                } elseif (str_contains($catName, 'Disabilitas')) {
                    $descList = $complaintDescriptions['Disabilitas'];
                }
                $description = $descList[array_rand($descList)];

                $complaint = Complaint::create([
                    'complaint_number' => $complaintNumber,
                    'complaint_category_id' => $category->id,
                    'reporter_id' => $submitter?->id,
                    'reporter_name' => $reporterName,
                    'reporter_phone' => $reporterPhone,
                    'location_detail' => 'Sekitar '.$blitarDusuns[array_rand($blitarDusuns)].', Desa '.$village->name,
                    'village_id' => $village->id,
                    'description' => $description,
                    'reported_at' => $reportedAt,
                    'officer_id' => $petugasPengaduan?->id,
                    'status' => $cStatus,
                    'verification_result' => ($cStatus !== ComplaintStatus::Invalid && $cStatus !== ComplaintStatus::Received)
                        ? 'Pengaduan telah diverifikasi melalui koordinasi dengan perangkat desa / pemantauan lapangan.'
                        : null,
                    'action_taken' => ($cStatus === ComplaintStatus::Resolved || $cStatus === ComplaintStatus::InHandling)
                        ? 'Tim TRC Dinsos telah menindaklanjuti ke lokasi dan mengkoordinasikan intervensi lanjutan.'
                        : null,
                    'resolved_at' => $resolvedAt,
                ]);

                // Dispositions for dispatched / in_handling complaints
                if (in_array($cStatus, [ComplaintStatus::Dispatched, ComplaintStatus::InHandling, ComplaintStatus::Resolved], true)) {
                    Disposition::create([
                        'dispositionable_type' => Complaint::class,
                        'dispositionable_id' => $complaint->id,
                        'from_user_id' => $petugasPengaduan?->id ?? $kadis->id,
                        'to_work_unit_id' => (str_contains($category->name, 'ODGJ') || str_contains($category->name, 'PPKS')) ? $rehsosUnit?->id : $linjamsos?->id,
                        'to_user_id' => (str_contains($category->name, 'ODGJ') || str_contains($category->name, 'PPKS')) ? $petugasRehsos?->id : $petugasPbi?->id,
                        'instructions' => 'Mohon segera lakukan penjangkauan lapangan dan koordinasikan dengan faskes / pendamping wilayah.',
                        'disposed_at' => (clone $reportedAt)->addHours(mt_rand(2, 24)),
                    ]);
                }
            }

            // =========================================================================
            // 3. SEEDING 160 CLIENTS & REHABILITATION CASES
            // =========================================================================
            $this->command?->info('3. Membuat 160 Klien & Kasus Rehabilitasi Sosial...');

            $caseStatuses = [
                RehabilitationCaseStatus::Received,
                RehabilitationCaseStatus::Assessment,
                RehabilitationCaseStatus::ServicePlanning,
                RehabilitationCaseStatus::InService,
                RehabilitationCaseStatus::Monitoring,
                RehabilitationCaseStatus::Closed,
            ];

            for ($m = 0; $m < 160; $m++) {
                $village = $villages->random();
                $clientCategory = $clientCategories->random();
                $cGender = (mt_rand(0, 1) === 1) ? 'male' : 'female';
                $clientName = fake('id_ID')->name($cGender);
                $birthDate = (clone $now)->subYears(mt_rand(12, 75))->subDays(mt_rand(0, 365));
                $clientNik = $generateNik($village, $cGender, $birthDate);

                $client = Client::create([
                    'name' => $clientName,
                    'client_category_id' => $clientCategory->id,
                    'nik' => (mt_rand(1, 10) <= 8) ? $clientNik : null, // some PPKS may lack NIK initially
                    'birth_date' => $birthDate->toDateString(),
                    'gender' => $cGender,
                    'address' => $generateAddress($village),
                    'village_id' => $village->id,
                    'phone' => (mt_rand(1, 10) <= 6) ? $generatePhone() : null,
                ]);

                // Case Dates across 2026
                $caseReceivedAt = (clone $now)->subDays(mt_rand(2, 260))->addHours(mt_rand(8, 16));
                $period = $caseReceivedAt->format('Ym');
                $caseNumber = $getNextNumber('RHS', $period);

                // Status distribution
                if ($m < 25) {
                    $rStatus = RehabilitationCaseStatus::Received;
                } elseif ($m < 50) {
                    $rStatus = RehabilitationCaseStatus::Assessment;
                } elseif ($m < 75) {
                    $rStatus = RehabilitationCaseStatus::ServicePlanning;
                } elseif ($m < 110) {
                    $rStatus = RehabilitationCaseStatus::InService;
                } elseif ($m < 135) {
                    $rStatus = RehabilitationCaseStatus::Monitoring;
                } else {
                    $rStatus = RehabilitationCaseStatus::Closed;
                }

                $handlingTypes = [HandlingType::Direct, HandlingType::Referral, HandlingType::Both];
                $handlingType = $handlingTypes[array_rand($handlingTypes)];

                $closedAt = ($rStatus === RehabilitationCaseStatus::Closed)
                    ? (clone $caseReceivedAt)->addDays(mt_rand(20, 60))
                    : null;
                if ($closedAt && $closedAt->gt($now)) {
                    $closedAt = clone $now;
                }

                $case = RehabilitationCase::create([
                    'case_number' => $caseNumber,
                    'client_id' => $client->id,
                    'service_request_id' => null,
                    'complaint_id' => null,
                    'officer_id' => $petugasRehsos?->id,
                    'handling_type' => $handlingType,
                    'status' => $rStatus,
                    'handling_result' => ($rStatus === RehabilitationCaseStatus::Closed)
                        ? 'Klien telah pulih dan direunifikasi kembali ke pihak keluarga / menjalani kehidupan mandiri.'
                        : null,
                    'received_at' => $caseReceivedAt,
                    'closed_at' => $closedAt,
                ]);

                // Create Assessment for cases beyond 'Received'
                if ($rStatus !== RehabilitationCaseStatus::Received) {
                    $assessmentDate = (clone $caseReceivedAt)->addDays(mt_rand(1, 3));
                    $needsReferral = ($handlingType === HandlingType::Referral || $handlingType === HandlingType::Both);

                    $assessment = Assessment::create([
                        'rehabilitation_case_id' => $case->id,
                        'officer_id' => $petugasRehsos?->id,
                        'assessment_date' => $assessmentDate->toDateString(),
                        'result' => "Klien {$clientName} menunjukkan kebutuhan intervensi sosial terpadu terkait {$clientCategory->name}.",
                        'service_needs' => 'Perawatan kebersihan diri, pendampingan psikososial, dan rehabilitasi berkala.',
                        'recommendation' => $needsReferral ? 'Rujuk segera ke balai/lembaga rujukan mitra Dinsos.' : 'Penanganan langsung di rumah aman / lingkungan keluarga.',
                        'needs_referral' => $needsReferral,
                    ]);

                    // Create Referral if needed
                    if ($needsReferral && $referralInstitutions->isNotEmpty()) {
                        $refPeriod = $assessmentDate->format('Ym');
                        $referralNumber = $getNextNumber('RJK', $refPeriod);
                        $institution = $referralInstitutions->random();

                        $refStatus = ($rStatus === RehabilitationCaseStatus::Closed)
                            ? ReferralStatus::Completed->value
                            : (($rStatus === RehabilitationCaseStatus::InService || $rStatus === RehabilitationCaseStatus::Monitoring)
                                ? ReferralStatus::InService->value
                                : ReferralStatus::Accepted->value);

                        $completedRefAt = ($refStatus === ReferralStatus::Completed->value)
                            ? (clone $assessmentDate)->addDays(mt_rand(14, 45))
                            : null;
                        if ($completedRefAt && $completedRefAt->gt($now)) {
                            $completedRefAt = clone $now;
                        }

                        Referral::create([
                            'referral_number' => $referralNumber,
                            'rehabilitation_case_id' => $case->id,
                            'assessment_id' => $assessment->id,
                            'referral_institution_id' => $institution->id,
                            'officer_id' => $petugasRehsos?->id,
                            'referral_date' => (clone $assessmentDate)->addDays(1)->toDateString(),
                            'status' => $refStatus,
                            'service_result' => "Klien diterima di {$institution->name} dan mendapatkan pelayanan rehabilitasi sosial sesuai standar operasional.",
                            'completed_at' => $completedRefAt,
                        ]);
                    }

                    // Create Monitoring records for active cases in service or monitoring
                    if (in_array($rStatus, [RehabilitationCaseStatus::InService, RehabilitationCaseStatus::Monitoring, RehabilitationCaseStatus::Closed], true)) {
                        MonitoringRecord::create([
                            'rehabilitation_case_id' => $case->id,
                            'officer_id' => $petugasRehsos?->id,
                            'monitoring_date' => (clone $assessmentDate)->addDays(mt_rand(7, 14))->toDateString(),
                            'progress' => 'Kondisi fisik dan emosional klien menunjukkan perkembangan positif dan stabil.',
                            'result_notes' => 'Lanjutkan program monitoring berkala dua pekan ke depan.',
                        ]);
                    }
                }
            }

            // =========================================================================
            // 4. SEEDING PAGE VISITS & SEARCH LOGS (DASHBOARD WIDGET 9 SUPPORT)
            // =========================================================================
            $this->command?->info('4. Membuat data Page Visits & Search Logs...');

            if ($informationPages->isNotEmpty()) {
                // Generate daily visits for the past 90 days for each info page
                for ($d = 90; $d >= 0; $d--) {
                    $visitDate = (clone $now)->subDays($d)->toDateString();
                    foreach ($informationPages as $page) {
                        $baseVisits = match ($page->slug) {
                            'surat-keterangan-dtsen' => mt_rand(40, 180),
                            'reaktivasi-kis-pbi-jk' => mt_rand(30, 150),
                            'tata-cara-pengaduan-sosial' => mt_rand(20, 90),
                            default => mt_rand(10, 60),
                        };

                        PageVisit::updateOrCreate(
                            [
                                'information_page_id' => $page->id,
                                'visit_date' => $visitDate,
                            ],
                            [
                                'visit_count' => $baseVisits,
                            ]
                        );
                    }
                }
            }

            // Search logs
            $keywords = [
                'surat keterangan dtsen', 'syarat sk dtsen', 'reaktivasi kis bpjs', 'pbi jk nonaktif',
                'cara aktifkan bpjs gratis', 'bansos pkh blitar', 'cek desil dtsen', 'daftar odgj terlantar',
                'bantuan kursi roda', 'panti jompo blitar', 'lapor bansos tidak cair', 'jadwal siks ng',
                'kartu indonesia sehat', 'bantuan anak terlantar', 'rehab narkoba',
            ];

            for ($s = 0; $s < 180; $s++) {
                $searchedAt = (clone $now)->subDays(mt_rand(0, 90))->subHours(mt_rand(1, 23))->subMinutes(mt_rand(1, 59));
                $kw = $keywords[array_rand($keywords)];
                SearchLog::create([
                    'keyword' => $kw,
                    'result_count' => mt_rand(1, 25),
                    'searched_at' => $searchedAt,
                ]);
            }

            // =========================================================================
            // 5. UPDATE NUMBER SEQUENCES TABLE WITH HIGHEST GENERATED VALUES
            // =========================================================================
            $this->command?->info('5. Memperbarui tabel NumberSequence...');

            foreach ($sequenceCounters as $key => $lastNum) {
                [$prefix, $period] = explode('-', $key);
                $existing = NumberSequence::where('prefix', $prefix)->where('period', $period)->first();
                if ($existing) {
                    if ($lastNum > $existing->last_number) {
                        $existing->update(['last_number' => $lastNum]);
                    }
                } else {
                    NumberSequence::create([
                        'prefix' => $prefix,
                        'period' => $period,
                        'last_number' => $lastNum,
                    ]);
                }
            }

            DB::commit();
            $this->command?->info('✓ Seeder Dummy Data Dashboard SAPA SOSIAL berhasil diselesaikan!');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command?->error('Gagal menjalankan seeder: '.$e->getMessage());
            throw $e;
        }
    }
}
