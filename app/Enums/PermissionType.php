<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Daftar permission sistem SAPA SOSIAL.
 *
 * Konvensi:
 * - CRUD → satu permission "kelola_*" (mencakup create, read, update, delete)
 * - Lihat saja → "lihat_*" (read-only, untuk Pimpinan)
 * - Aksi bisnis (verifikasi, tanda tangan, disposisi) → permission tersendiri
 *
 * Case names use English; string values remain in Indonesian.
 */
enum PermissionType: string
{
    // ─── Users & Access Control (UserResource) ───────────────────
    case ManageUsers = 'kelola_user';

    // ─── Master Data ─────────────────────────────────────────────
    case ManageRegions = 'kelola_wilayah';                     // DistrictResource + VillageResource
    case ManageWorkUnits = 'kelola_unit_kerja';                // WorkUnitResource
    case ManageServiceTypes = 'kelola_jenis_layanan';          // ServiceTypeResource
    case ManageDtsenPurposes = 'kelola_tujuan_dtsen';          // DtsenPurposeResource
    case ManageComplaintCategories = 'kelola_kategori_pengaduan'; // ComplaintCategoryResource
    case ManageClientCategories = 'kelola_kategori_klien';     // ClientCategoryResource
    case ManageReferralInstitutions = 'kelola_lembaga_rujukan'; // ReferralInstitutionResource

    // ─── Service Requests (ServiceRequestResource) ───────────────
    case ManageServiceRequests = 'kelola_pengajuan';
    case ViewServiceRequests = 'lihat_pengajuan';
    case VerifyServiceRequests = 'verifikasi_pengajuan';

    // ─── DTSEN & PBI-JK (business actions) ───────────────────────
    case SignDocuments = 'tanda_tangani_dokumen';               // paraf & approval berjenjang
    case VerifySiksng = 'verifikasi_siksng';                   // cek SIKS-NG & catat desil

    // ─── Rehabilitation (RehabilitationCaseResource + ClientResource) ──
    case ManageRehabilitation = 'kelola_rehabilitasi';
    case ViewRehabilitation = 'lihat_rehabilitasi';
    case ManageClients = 'kelola_klien';                       // ClientResource

    // ─── Complaints (ComplaintResource) ──────────────────────────
    case ManageComplaints = 'kelola_pengaduan';
    case ViewComplaints = 'lihat_pengaduan';
    case DispatchComplaints = 'disposisi_pengaduan';

    // ─── Information Pages (InformationPageResource — belum ada Resource) ──
    case ManageInformation = 'kelola_informasi';

    // ─── Dashboard & Reports ─────────────────────────────────────
    case ViewDashboard = 'lihat_dashboard';
    case ViewReports = 'lihat_laporan';

    // ─── Public Portal (citizen & operator frontend) ─────────────
    case SubmitServiceRequests = 'ajukan_layanan';
    case SubmitComplaints = 'ajukan_pengaduan';

    /**
     * Label bahasa Indonesia untuk tampilan UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::ManageUsers => 'Kelola Pengguna & Hak Akses',
            self::ManageRegions => 'Kelola Data Wilayah (Kecamatan & Desa)',
            self::ManageWorkUnits => 'Kelola Unit Kerja',
            self::ManageServiceTypes => 'Kelola Jenis Layanan & Persyaratan',
            self::ManageDtsenPurposes => 'Kelola Tujuan Penggunaan SK DTSEN',
            self::ManageComplaintCategories => 'Kelola Kategori Pengaduan',
            self::ManageClientCategories => 'Kelola Kategori Klien Rehabilitasi',
            self::ManageReferralInstitutions => 'Kelola Lembaga Tujuan Rujukan',
            self::ManageServiceRequests => 'Kelola Pengajuan Layanan (CRUD & proses)',
            self::ViewServiceRequests => 'Lihat Pengajuan Layanan (hanya baca)',
            self::VerifyServiceRequests => 'Verifikasi Berkas Pengajuan',
            self::SignDocuments => 'Paraf & Tanda Tangan Dokumen',
            self::VerifySiksng => 'Verifikasi Data SIKS-NG',
            self::ManageRehabilitation => 'Kelola Kasus Rehabilitasi Sosial',
            self::ViewRehabilitation => 'Lihat Kasus Rehabilitasi (ringkasan)',
            self::ManageClients => 'Kelola Data Klien Rehabilitasi',
            self::ManageComplaints => 'Kelola Pengaduan & Laporan Sosial',
            self::ViewComplaints => 'Lihat Pengaduan (hanya baca)',
            self::DispatchComplaints => 'Disposisi Pengaduan ke Unit/Petugas',
            self::ManageInformation => 'Kelola Konten Informasi Layanan',
            self::ViewDashboard => 'Lihat Dashboard',
            self::ViewReports => 'Lihat & Ekspor Laporan',
            self::SubmitServiceRequests => 'Mengajukan Layanan (portal publik)',
            self::SubmitComplaints => 'Mengajukan Pengaduan (portal publik)',
        };
    }

    /**
     * Kelompok permission untuk pengelompokan di UI.
     */
    public function group(): string
    {
        return match ($this) {
            self::ManageUsers => 'Pengaturan Pengguna',
            self::ManageRegions, self::ManageWorkUnits, self::ManageServiceTypes,
            self::ManageDtsenPurposes, self::ManageComplaintCategories,
            self::ManageClientCategories, self::ManageReferralInstitutions => 'Data Master',
            self::ManageServiceRequests, self::ViewServiceRequests,
            self::VerifyServiceRequests, self::SignDocuments,
            self::VerifySiksng => 'Pelayanan',
            self::ManageRehabilitation, self::ViewRehabilitation,
            self::ManageClients => 'Rehabilitasi Sosial',
            self::ManageComplaints, self::ViewComplaints,
            self::DispatchComplaints => 'Pengaduan',
            self::ManageInformation => 'Informasi Layanan',
            self::ViewDashboard, self::ViewReports => 'Dashboard & Laporan',
            self::SubmitServiceRequests, self::SubmitComplaints => 'Portal Publik',
        };
    }

    /**
     * Semua nilai permission sebagai array string (untuk seeder).
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
