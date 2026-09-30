<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ComplaintStatus;
use App\Enums\RehabilitationCaseStatus;
use App\Enums\ServiceRequestStatus;
use App\Models\Complaint;
use App\Models\District;
use App\Models\DtsenCertificate;
use App\Models\DtsenPurpose;
use App\Models\RehabilitationCase;
use App\Models\ServiceRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class DashboardStatisticsService
{
    /**
     * Terapkan filter dan batasan hak akses wilayah pada query ServiceRequest.
     */
    public function applyServiceRequestFilters(Builder $query, ?array $filters, ?User $user): Builder
    {
        $filters ??= [];

        if ($user && $user->hasRole('operator_kecamatan_desa')) {
            if ($user->village_id) {
                $query->where('village_id', $user->village_id);
            } elseif ($user->district_id) {
                $query->whereHas('village', fn ($q) => $q->where('district_id', $user->district_id));
            }
        } elseif ($user && $user->hasRole('masyarakat')) {
            $query->where('submitter_id', $user->id);
        }

        if (! empty($filters['startDate'])) {
            $query->whereDate('submitted_at', '>=', Carbon::parse($filters['startDate']));
        }

        if (! empty($filters['endDate'])) {
            $query->whereDate('submitted_at', '<=', Carbon::parse($filters['endDate']));
        }

        if (! empty($filters['service_type_id'])) {
            $query->where('service_type_id', $filters['service_type_id']);
        }

        if (! empty($filters['district_id'])) {
            $query->whereHas('village', fn ($q) => $q->where('district_id', $filters['district_id']));
        }

        if (! empty($filters['village_id'])) {
            $query->where('village_id', $filters['village_id']);
        }

        return $query;
    }

    /**
     * Terapkan filter dan batasan hak akses wilayah pada query Complaint.
     */
    public function applyComplaintFilters(Builder $query, ?array $filters, ?User $user): Builder
    {
        $filters ??= [];

        if ($user && $user->hasRole('operator_kecamatan_desa')) {
            if ($user->village_id) {
                $query->where('village_id', $user->village_id);
            } elseif ($user->district_id) {
                $query->whereHas('village', fn ($q) => $q->where('district_id', $user->district_id));
            }
        } elseif ($user && $user->hasRole('masyarakat')) {
            $query->where('reporter_id', $user->id);
        }

        if (! empty($filters['startDate'])) {
            $query->whereDate('reported_at', '>=', Carbon::parse($filters['startDate']));
        }

        if (! empty($filters['endDate'])) {
            $query->whereDate('reported_at', '<=', Carbon::parse($filters['endDate']));
        }

        if (! empty($filters['district_id'])) {
            $query->whereHas('village', fn ($q) => $q->where('district_id', $filters['district_id']));
        }

        if (! empty($filters['village_id'])) {
            $query->where('village_id', $filters['village_id']);
        }

        return $query;
    }

    /**
     * Terapkan filter dan batasan hak akses penugasan pada query RehabilitationCase.
     */
    public function applyRehabilitationFilters(Builder $query, ?array $filters, ?User $user): Builder
    {
        $filters ??= [];

        if ($user && $user->hasRole('petugas_dinsos') && ! $user->hasAnyRole(['administrator', 'pimpinan', 'pejabat_penandatangan'])) {
            $query->where(function ($q) use ($user) {
                $q->where('officer_id', $user->id)
                    ->orWhereNull('officer_id');
            });
        }

        if (! empty($filters['startDate'])) {
            $query->whereDate('received_at', '>=', Carbon::parse($filters['startDate']));
        }

        if (! empty($filters['endDate'])) {
            $query->whereDate('received_at', '<=', Carbon::parse($filters['endDate']));
        }

        return $query;
    }

    /**
     * Data angka ringkasan kartu KPI utama (Overview Stats).
     */
    public function getOverviewStats(?array $filters = [], ?User $user = null): array
    {
        $filters ??= [];
        // 1. SK DTSEN Terbit
        $srQuery = ServiceRequest::query();
        $this->applyServiceRequestFilters($srQuery, $filters, $user);
        $dtsenIssued = (clone $srQuery)
            ->whereHas('serviceType', fn ($q) => $q->where('code', 'like', '%DTSEN%'))
            ->whereIn('status', [ServiceRequestStatus::Issued, ServiceRequestStatus::Completed])
            ->count();

        // 2. SK DTSEN Menunggu Tanda Tangan
        $pendingQuery = ServiceRequest::query();
        $this->applyServiceRequestFilters($pendingQuery, $filters, $user);
        $dtsenPending = (clone $pendingQuery)
            ->where('status', ServiceRequestStatus::AwaitingApproval)
            ->count();

        // 3. PBI-JK Tindak Lanjut (Diusulkan ke Kemensos atau Prioritas Darurat belum selesai)
        $pbiQuery = ServiceRequest::query();
        $this->applyServiceRequestFilters($pbiQuery, $filters, $user);
        $pbiFollowUp = (clone $pbiQuery)
            ->whereHas('serviceType', fn ($q) => $q->where('code', 'like', '%PBI%'))
            ->where(function ($q) {
                $q->where('status', ServiceRequestStatus::ProposedToMinistry)
                    ->orWhere(function ($sub) {
                        $sub->where('is_priority', true)
                            ->whereNotIn('status', [ServiceRequestStatus::Completed, ServiceRequestStatus::Rejected, ServiceRequestStatus::MinistryRejected]);
                    });
            })
            ->count();

        // 4. Kasus Rehabilitasi Aktif
        $rehabQuery = RehabilitationCase::query();
        $this->applyRehabilitationFilters($rehabQuery, $filters, $user);
        $rehabActive = (clone $rehabQuery)
            ->where('status', '!=', RehabilitationCaseStatus::Closed)
            ->count();

        // 5. Total Layanan & Pengaduan Dalam Proses (Aktif)
        $activeSrQuery = ServiceRequest::query();
        $this->applyServiceRequestFilters($activeSrQuery, $filters, $user);
        $activeSr = (clone $activeSrQuery)
            ->whereNotIn('status', [ServiceRequestStatus::Completed, ServiceRequestStatus::Rejected, ServiceRequestStatus::MinistryRejected])
            ->count();

        $activeComplaintQuery = Complaint::query();
        $this->applyComplaintFilters($activeComplaintQuery, $filters, $user);
        $activeComplaints = (clone $activeComplaintQuery)
            ->whereNotIn('status', [ComplaintStatus::Resolved, ComplaintStatus::Duplicate, ComplaintStatus::Invalid])
            ->count();

        return [
            'dtsen_issued' => $dtsenIssued,
            'dtsen_pending' => $dtsenPending,
            'pbi_follow_up' => $pbiFollowUp,
            'rehab_active' => $rehabActive,
            'active_in_process' => $activeSr + $activeComplaints,
        ];
    }

    /**
     * SK DTSEN Diterbitkan per Tujuan Penggunaan.
     */
    public function getDtsenByPurpose(?array $filters = [], ?User $user = null): array
    {
        $filters ??= [];
        $query = DtsenCertificate::query()->whereNotNull('issued_at');

        if (! empty($filters['startDate'])) {
            $query->whereDate('issued_at', '>=', Carbon::parse($filters['startDate']));
        }
        if (! empty($filters['endDate'])) {
            $query->whereDate('issued_at', '<=', Carbon::parse($filters['endDate']));
        }

        if ($user && $user->hasRole('operator_kecamatan_desa')) {
            $query->whereHas('serviceRequest', function ($sr) use ($user) {
                if ($user->village_id) {
                    $sr->where('village_id', $user->village_id);
                } elseif ($user->district_id) {
                    $sr->whereHas('village', fn ($q) => $q->where('district_id', $user->district_id));
                }
            });
        }

        $purposes = DtsenPurpose::all();
        $labels = [];
        $counts = [];

        foreach ($purposes as $purpose) {
            $labels[] = $purpose->name;
            $counts[] = (clone $query)->where('dtsen_purpose_id', $purpose->id)->count();
        }

        return [
            'labels' => $labels,
            'counts' => $counts,
        ];
    }

    /**
     * Rekap Reaktivasi PBI-JK per Tahap.
     */
    public function getPbiStages(?array $filters = [], ?User $user = null): array
    {
        $filters ??= [];
        $query = ServiceRequest::query()
            ->whereHas('serviceType', fn ($q) => $q->where('code', 'like', '%PBI%'));

        $this->applyServiceRequestFilters($query, $filters, $user);

        $stages = [
            'Pemeriksaan & Verifikasi' => [
                ServiceRequestStatus::Submitted,
                ServiceRequestStatus::DocumentCheck,
                ServiceRequestStatus::Verification,
                ServiceRequestStatus::EligibilityVerification,
            ],
            'Menunggu Persetujuan' => [
                ServiceRequestStatus::AwaitingApproval,
            ],
            'Diusulkan ke Kemensos' => [
                ServiceRequestStatus::ProposedToMinistry,
            ],
            'Disetujui / Aktif' => [
                ServiceRequestStatus::MinistryApproved,
                ServiceRequestStatus::Reactivated,
                ServiceRequestStatus::Completed,
            ],
            'Ditolak / Perbaikan' => [
                ServiceRequestStatus::RevisionRequested,
                ServiceRequestStatus::MinistryRejected,
                ServiceRequestStatus::Rejected,
            ],
        ];

        $labels = [];
        $counts = [];

        foreach ($stages as $label => $statuses) {
            $labels[] = $label;
            $counts[] = (clone $query)->whereIn('status', $statuses)->count();
        }

        return [
            'labels' => $labels,
            'counts' => $counts,
        ];
    }

    /**
     * Kasus Rehabilitasi per Status.
     */
    public function getRehabilitationStatusCounts(?array $filters = [], ?User $user = null): array
    {
        $filters ??= [];
        $query = RehabilitationCase::query();
        $this->applyRehabilitationFilters($query, $filters, $user);

        $statuses = [
            RehabilitationCaseStatus::Received,
            RehabilitationCaseStatus::Assessment,
            RehabilitationCaseStatus::ServicePlanning,
            RehabilitationCaseStatus::InService,
            RehabilitationCaseStatus::Monitoring,
            RehabilitationCaseStatus::Closed,
        ];

        $labels = [];
        $counts = [];

        foreach ($statuses as $status) {
            $labels[] = $status->label();
            $counts[] = (clone $query)->where('status', $status)->count();
        }

        return [
            'labels' => $labels,
            'counts' => $counts,
        ];
    }

    /**
     * Tren harian pengajuan layanan vs laporan pengaduan (14 hari terakhir).
     */
    public function getTrendData(?array $filters = [], ?User $user = null, int $days = 14): array
    {
        $filters ??= [];
        $endDate = ! empty($filters['endDate']) ? Carbon::parse($filters['endDate']) : now();
        $startDate = ! empty($filters['startDate']) ? Carbon::parse($filters['startDate']) : $endDate->copy()->subDays($days - 1);

        $labels = [];
        $requestData = [];
        $complaintData = [];

        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $dateString = $current->toDateString();
            $labels[] = $current->format('d M');

            // Pengajuan di tanggal ini
            $srQ = ServiceRequest::query()->whereDate('submitted_at', $dateString);
            $this->applyServiceRequestFilters($srQ, $filters, $user);
            $requestData[] = $srQ->count();

            // Pengaduan di tanggal ini
            $cmpQ = Complaint::query()->whereDate('reported_at', $dateString);
            $this->applyComplaintFilters($cmpQ, $filters, $user);
            $complaintData[] = $cmpQ->count();

            $current->addDay();
        }

        return [
            'labels' => $labels,
            'requests' => $requestData,
            'complaints' => $complaintData,
        ];
    }

    /**
     * Sebaran pengajuan per kecamatan di Kabupaten Blitar.
     */
    public function getRegionalDistribution(?array $filters = [], ?User $user = null): array
    {
        $filters ??= [];
        $districtsQuery = District::query()->orderBy('name');

        if ($user && $user->hasRole('operator_kecamatan_desa') && $user->district_id) {
            $districtsQuery->where('id', $user->district_id);
        }

        $districts = $districtsQuery->get();

        $labels = [];
        $counts = [];

        foreach ($districts as $district) {
            $srQ = ServiceRequest::query()
                ->whereHas('village', fn ($q) => $q->where('district_id', $district->id));

            if (! empty($filters['startDate'])) {
                $srQ->whereDate('submitted_at', '>=', Carbon::parse($filters['startDate']));
            }
            if (! empty($filters['endDate'])) {
                $srQ->whereDate('submitted_at', '<=', Carbon::parse($filters['endDate']));
            }
            if (! empty($filters['service_type_id'])) {
                $srQ->where('service_type_id', $filters['service_type_id']);
            }

            $labels[] = $district->name;
            $counts[] = $srQ->count();
        }

        return [
            'labels' => $labels,
            'counts' => $counts,
        ];
    }
}
