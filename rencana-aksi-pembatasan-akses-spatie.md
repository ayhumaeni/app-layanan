# Rencana Aksi: Pembatasan Akses SAPA SOSIAL dengan Spatie Laravel Permission v8

Referensi: PRD SAPA SOSIAL (Laravel · Filament v5 · Livewire v4 · PostgreSQL) dan https://spatie.be/docs/laravel-permission/v8/installation-laravel

## Latar Belakang

Di PRD, pengunjung (Masyarakat) dan staf sama-sama disimpan di tabel `users`. Pemisahan akses karenanya dibuat di lapisan otorisasi, bukan di struktur tabel. Tiga lapis kontrol:

1. **Role**: menentukan siapa yang boleh masuk panel `/admin`.
2. **Permission**: menentukan aksi apa yang boleh dilakukan.
3. **Policy dan query scope**: membatasi data yang boleh disentuh (wilayah, milik sendiri, petugas yang ditugaskan).

Role saja tidak cukup. Tanpa scope, Operator Kecamatan A tetap dapat melihat data Kecamatan B.

---

## Fase 1: Instalasi dan Konfigurasi Dasar

- [x] Baca halaman **Prerequisites** Spatie v8 untuk memastikan kompatibilitas versi PHP/Laravel dengan PRD (Laravel ≥ 11.28, PHP 8.3+) dan catatan tentang model `User`.
- [x] `composer require spatie/laravel-permission`
- [x] `php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"`
- [x] Atur `config/permission.php` sebelum migrate:
  - `teams => false` (pembatasan wilayah memakai kolom `district_id` dan `village_id` pada `users`, bukan fitur Teams)
  - Jika `CACHE_STORE=database`, pasang migrasi cache Laravel lebih dulu
- [x] `php artisan optimize:clear`, lalu `php artisan migrate` (PostgreSQL)
- [x] Tambahkan trait `HasRoles` pada model `User`
- [ ] Daftarkan alias middleware `role`, `permission`, `role_or_permission` di `bootstrap/app.php`

---

## Fase 2: Definisi Role dan Permission (Seeder)

Satu guard (`web`), satu tabel `users`. Role mengikuti Bagian 1 PRD:

| Role | Cakupan akses |
|---|---|
| `administrator` | Semua, termasuk kelola user/role dan data master |
| `petugas_dinsos` | Proses pengajuan, pengaduan, kasus rehabilitasi (sesuai penugasan) |
| `pejabat_penandatangan` | Paraf dan persetujuan SK DTSEN dan rekomendasi PBI-JK |
| `pimpinan` | Hanya `lihat_*`: dashboard, laporan, ringkasan kasus rehabilitasi |
| `operator_kecamatan_desa` | Buat dan pantau pengajuan di wilayahnya saja |
| `masyarakat` | **Tanpa akses `/admin`**; hanya data miliknya di portal publik (frontend Livewire) |

### Daftar Permission (Enum `App\Enums\PermissionType`)

Permission diturunkan dari **Filament Resources** yang ada. Untuk operasi CRUD, satu permission `kelola_*` mencakup create/read/update/delete agar tidak terlalu detail. Aksi bisnis (verifikasi, tanda tangan, disposisi) dipisah karena alur PRD berjenjang.

| Permission (value) | Label | Sumber / Filament Resource | Grup |
|---|---|---|---|
| `kelola_user` | Kelola Pengguna & Hak Akses | `UserResource` | Pengaturan Pengguna |
| `kelola_wilayah` | Kelola Data Wilayah | `DistrictResource` + `VillageResource` | Data Master |
| `kelola_unit_kerja` | Kelola Unit Kerja | `WorkUnitResource` | Data Master |
| `kelola_jenis_layanan` | Kelola Jenis Layanan & Persyaratan | `ServiceTypeResource` | Data Master |
| `kelola_tujuan_dtsen` | Kelola Tujuan Penggunaan SK DTSEN | `DtsenPurposeResource` | Data Master |
| `kelola_kategori_pengaduan` | Kelola Kategori Pengaduan | `ComplaintCategoryResource` | Data Master |
| `kelola_kategori_klien` | Kelola Kategori Klien Rehabilitasi | `ClientCategoryResource` | Data Master |
| `kelola_lembaga_rujukan` | Kelola Lembaga Tujuan Rujukan | `ReferralInstitutionResource` | Data Master |
| `kelola_pengajuan` | Kelola Pengajuan Layanan | `ServiceRequestResource` | Pelayanan |
| `lihat_pengajuan` | Lihat Pengajuan (hanya baca) | `ServiceRequestResource` | Pelayanan |
| `verifikasi_pengajuan` | Verifikasi Berkas Pengajuan | `ServiceRequestResource` (aksi) | Pelayanan |
| `tanda_tangani_dokumen` | Paraf & Tanda Tangan Dokumen | Approvals (aksi bisnis) | Pelayanan |
| `verifikasi_siksng` | Verifikasi Data SIKS-NG | DtsenCertificate (aksi bisnis) | Pelayanan |
| `kelola_rehabilitasi` | Kelola Kasus Rehabilitasi Sosial | `RehabilitationCaseResource` | Rehabilitasi Sosial |
| `lihat_rehabilitasi` | Lihat Kasus Rehabilitasi (ringkasan) | `RehabilitationCaseResource` | Rehabilitasi Sosial |
| `kelola_klien` | Kelola Data Klien Rehabilitasi | `ClientResource` | Rehabilitasi Sosial |
| `kelola_pengaduan` | Kelola Pengaduan & Laporan Sosial | `ComplaintResource` | Pengaduan |
| `lihat_pengaduan` | Lihat Pengaduan (hanya baca) | `ComplaintResource` | Pengaduan |
| `disposisi_pengaduan` | Disposisi Pengaduan ke Unit/Petugas | `ComplaintResource` (aksi) | Pengaduan |
| `kelola_informasi` | Kelola Konten Informasi Layanan | InformationPageResource (belum ada) | Informasi Layanan |
| `lihat_dashboard` | Lihat Dashboard | Dashboard widgets | Dashboard & Laporan |
| `lihat_laporan` | Lihat & Ekspor Laporan | Laporan / ekspor | Dashboard & Laporan |
| `ajukan_layanan` | Mengajukan Layanan (portal publik) | Frontend Livewire | Portal Publik |
| `ajukan_pengaduan` | Mengajukan Pengaduan (portal publik) | Frontend Livewire | Portal Publik |

### Matriks Role × Permission

| Permission | admin | petugas | pejabat | pimpinan | operator | masyarakat |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| `kelola_user` | ✅ | | | | | |
| `kelola_wilayah` | ✅ | | | | | |
| `kelola_unit_kerja` | ✅ | | | | | |
| `kelola_jenis_layanan` | ✅ | | | | | |
| `kelola_tujuan_dtsen` | ✅ | | | | | |
| `kelola_kategori_pengaduan` | ✅ | | | | | |
| `kelola_kategori_klien` | ✅ | | | | | |
| `kelola_lembaga_rujukan` | ✅ | | | | | |
| `kelola_pengajuan` | ✅ | ✅ | | | | |
| `lihat_pengajuan` | ✅ | | ✅ | ✅ | ✅ | |
| `verifikasi_pengajuan` | ✅ | ✅ | | | | |
| `tanda_tangani_dokumen` | ✅ | | ✅ | | | |
| `verifikasi_siksng` | ✅ | ✅ | | | | |
| `kelola_rehabilitasi` | ✅ | ✅ | | | | |
| `lihat_rehabilitasi` | ✅ | | | ✅ | | |
| `kelola_klien` | ✅ | ✅ | | | | |
| `kelola_pengaduan` | ✅ | ✅ | | | | |
| `lihat_pengaduan` | ✅ | | | ✅ | ✅ | |
| `disposisi_pengaduan` | ✅ | ✅ | | | | |
| `kelola_informasi` | ✅ | | | | | |
| `lihat_dashboard` | ✅ | ✅ | ✅ | ✅ | ✅ | |
| `lihat_laporan` | ✅ | ✅ | ✅ | ✅ | | |
| `ajukan_layanan` | ✅ | | | | ✅ | ✅ |
| `ajukan_pengaduan` | ✅ | | | | ✅ | ✅ |

Ketentuan:

- [x] Enum `App\Enums\PermissionType` mendefinisikan semua permission dengan method `label()` dan `group()`.
- [x] Seeder idempoten (`firstOrCreate`) dan memanggil `forgetCachedPermissions()`.
- [x] Administrator memakai `Gate::before` (pola Super-Admin Spatie) di `AppServiceProvider`. Perlu diingat pola ini melewati semua Policy.
- [x] Satu akun boleh memiliki banyak role (mis. Kadis = `pimpinan` + `pejabat_penandatangan`).

---

## Fase 3: Kunci Gerbang Utama (Panel Filament)

Ini titik kebocoran paling berisiko karena Masyarakat dan staf berada di tabel yang sama.

- [x] `User` mengimplementasikan `FilamentUser`; `canAccessPanel()` hanya `true` bila `is_active` **dan** user memiliki role staf (bukan `masyarakat`).
- [x] `canAccessPanel()` secara eksplisit menolak (`return false`) bila user memiliki role `masyarakat`, sebelum mengecek role staf.
- [x] Registrasi publik hanya memberi role `masyarakat`. Role tidak masuk `$fillable`.
- [x] Pemberian role staf hanya lewat `UserResource` yang dilindungi permission `kelola_user`.
- [x] Pembuatan `RoleResource` (`app/Filament/Resources/Roles`) untuk manajemen role dan penetapan permission berbasis `PermissionType`.
- [x] Pembuatan `PermissionResource` (`app/Filament/Resources/Permissions`) untuk melihat daftar 24 permission, pengelompokan grup, dan penetapan role.
- [x] Pembuatan `RolePolicy` dan `PermissionPolicy` terdaftar di `AppServiceProvider` serta perlindungan form role di `UserForm`.
- [ ] Portal publik: halaman akun dan riwayat pengajuan memakai `auth` + `role:masyarakat`. Cek tiket dan verifikasi SK tetap terbuka tanpa login.

### Filament Resources untuk Pengelolaan Role & Hak Akses

Tiga resource di navigasi **Pengaturan Pengguna** telah dikonfigurasi:

1. **`UserResource` (`Pengguna & Hak Akses`)**:
   - Pemilihan role menggunakan `Select::make('roles')` dengan label yang ramah pengguna (`Administrator`, `Petugas Dinsos`, dsb.).
   - Dilindungi agar hanya user dengan permission `kelola_user` / Administrator yang dapat mengubah role.
   - Sinkronisasi role menggunakan method Spatie `$record->syncRoles()` dan otomatis memanggil `forgetCachedPermissions()`.
   - Tampilan kolom tabel dan filter tabel dilengkapi badge warna dan pemformatan label yang rapi.

2. **`RoleResource` (`Peran (Roles)`)**:
   - Menampilkan daftar peran, jumlah permission, dan jumlah pengguna aktif per peran.
   - Form mengizinkan pemilihan permission menggunakan `CheckboxList` interaktif dengan pencarian, toggle massal (bulk toggleable), serta label dan grup dari `PermissionType`.
   - Proteksi peran bawaan sistem (`administrator`, `petugas_dinsos`, `pejabat_penandatangan`, `pimpinan`, `operator_kecamatan_desa`, `masyarakat`) agar tidak dapat dihapus atau diubah identifier-nya.
   - Setiap perubahan permission otomatis membersihkan cache permission Spatie.

3. **`PermissionResource` (`Izin Akses (Permissions)`)**:
   - Menampilkan 24 permission sistem yang diturunkan dari `PermissionType` enum beserta kategori/grup dan role yang memilikinya.
   - Kode permission dapat disalin langsung (copyable), dan dapat difilter berdasarkan peran.
   - Dibatasi secara policy agar kode permission tidak dapat dibuat sembarangan di luar enum sistem.

### Pengunjung (Masyarakat) vs Staf — Garis Pemisah

| Aspek | Masyarakat | Staf (semua role selain masyarakat) |
|---|---|---|
| Akses `/admin` (Filament) | ❌ **DITOLAK** via `canAccessPanel()` | ✅ Diizinkan |
| Portal publik (Livewire) | ✅ Semua fitur frontend | ✅ (bisa juga mengakses jika diperlukan) |
| Permission | `ajukan_layanan`, `ajukan_pengaduan` saja | Sesuai role masing-masing |
| Data yang terlihat | Hanya data milik sendiri | Sesuai scope wilayah/penugasan |

---

## Fase 4: Policy dan Scoping Data

Referensi: https://laravel.com/docs/12.x/authorization#creating-policies

### 4.1 Apa Itu Laravel Policy

Policy adalah kelas PHP yang mengelompokkan logika otorisasi berdasarkan model/resource tertentu. Setiap method di Policy menerima `User $user` (dan opsional instance model) lalu mengembalikan `bool` — apakah user diizinkan melakukan aksi tersebut.

**Method standar Policy (dipakai Filament secara otomatis):**

| Method | Kapan dipanggil | Menerima model? |
|---|---|---|
| `viewAny(User $user)` | Menampilkan daftar / list page | Tidak |
| `view(User $user, Model $model)` | Melihat detail satu record | Ya |
| `create(User $user)` | Membuat record baru | Tidak |
| `update(User $user, Model $model)` | Mengedit record | Ya |
| `delete(User $user, Model $model)` | Menghapus record (soft delete) | Ya |
| `restore(User $user, Model $model)` | Memulihkan soft-deleted record | Ya |
| `forceDelete(User $user, Model $model)` | Hapus permanen | Ya |

### 4.2 Pembuatan Policy

```bash
php artisan make:policy NamaModelPolicy --model=NamaModel
```

Hasil: `app/Policies/NamaModelPolicy.php`

### 4.3 Auto-Discovery (Konvensi Penamaan)

Laravel **otomatis menemukan** Policy tanpa perlu registrasi manual, selama:
- Policy berada di `app/Policies/`
- Nama Policy = nama Model + suffix `Policy` (contoh: `User` → `UserPolicy`)

Atau dapat didaftarkan manual via `Gate::policy()` di `AppServiceProvider`, atau via atribut `#[UsePolicy()]` pada model.

### 4.4 Interaksi Gate::before dengan Policy

Karena `Gate::before` sudah diatur di `AppServiceProvider` untuk role `administrator`, maka:
- **Administrator melewati semua Policy** — method Policy tidak pernah dipanggil untuk administrator.
- Policy hanya efektif untuk role lain (`petugas_dinsos`, `pejabat_penandatangan`, `pimpinan`, `operator_kecamatan_desa`, `masyarakat`).

### 4.5 Policy yang Sudah Ada

Proyek ini sudah memiliki **14 Policy** di `app/Policies/`:

| Policy | Model | Status | Catatan |
|---|---|---|---|
| `UserPolicy` | `User` | ⚠️ Perlu migrasi | Masih pakai `hasRole()` langsung |
| `ServiceRequestPolicy` | `ServiceRequest` | ⚠️ Perlu migrasi | Sudah ada scoping wilayah & kepemilikan, tapi pakai role langsung |
| `ComplaintPolicy` | `Complaint` | ⚠️ Perlu migrasi | Sudah ada scoping wilayah & kepemilikan |
| `RehabilitationCasePolicy` | `RehabilitationCase` | ⚠️ Perlu migrasi | Sudah ada scoping petugas yang ditugaskan |
| `ClientPolicy` | `Client` | ⚠️ Perlu migrasi | Data sensitif, sudah dibatasi role |
| `ReferralPolicy` | `Referral` | ⚠️ Perlu migrasi | Sudah dibatasi role |
| `DistrictPolicy` | `District` | ⚠️ Perlu migrasi | viewAny/view terbuka |
| `VillagePolicy` | `Village` | ⚠️ Perlu migrasi | viewAny/view terbuka |
| `WorkUnitPolicy` | `WorkUnit` | ⚠️ Perlu migrasi | |
| `ServiceTypePolicy` | `ServiceType` | ⚠️ Perlu migrasi | |
| `DtsenPurposePolicy` | `DtsenPurpose` | ⚠️ Perlu migrasi | |
| `ComplaintCategoryPolicy` | `ComplaintCategory` | ⚠️ Perlu migrasi | |
| `ClientCategoryPolicy` | `ClientCategory` | ⚠️ Perlu migrasi | |
| `ReferralInstitutionPolicy` | `ReferralInstitution` | ⚠️ Perlu migrasi | |

### 4.6 Masalah pada Policy Saat Ini

Policy saat ini menggunakan `$user->hasRole()` / `$user->hasAnyRole()` secara langsung alih-alih `$user->can()` (Spatie permission). Ini berarti:

1. **Permission di database tidak dipakai** — perubahan permission via UI tidak berpengaruh.
2. **Tidak konsisten** dengan enum `PermissionType` yang sudah dibuat.
3. **Gate::before untuk administrator redundan** — Policy sudah hardcode `hasRole('administrator')` di mana-mana.

### 4.7 Rencana Migrasi Policy → Permission-Based

Setiap Policy akan dimigrasikan dari pola role-based ke permission-based menggunakan `$user->can()` yang membaca dari tabel `permissions` Spatie. Karena `Gate::before` sudah menangani administrator, maka method Policy **tidak perlu lagi** mengecek role `administrator`.

#### Pola Migrasi

**Sebelum (role-based):**
```php
public function viewAny(User $user): bool
{
    return $user->hasAnyRole([
        'administrator',
        'petugas_dinsos',
        'pimpinan',
    ]);
}
```

**Sesudah (permission-based):**
```php
use App\Enums\PermissionType;

public function viewAny(User $user): bool
{
    // administrator sudah dihandle Gate::before
    return $user->can(PermissionType::ManageServiceRequests->value)
        || $user->can(PermissionType::ViewServiceRequests->value);
}
```

#### Contoh: ServiceRequestPolicy (Sesudah Migrasi)

```php
use App\Enums\PermissionType;

class ServiceRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionType::ManageServiceRequests->value)
            || $user->can(PermissionType::ViewServiceRequests->value);
    }

    public function view(User $user, ServiceRequest $sr): bool
    {
        if ($user->can(PermissionType::ManageServiceRequests->value)
            || $user->can(PermissionType::ViewServiceRequests->value)) {
            // Scope wilayah untuk operator
            if ($user->hasRole('operator_kecamatan_desa')) {
                return $this->isInUserTerritory($user, $sr);
            }
            return true;
        }

        // Masyarakat: hanya data milik sendiri (via portal publik)
        if ($user->hasRole('masyarakat')) {
            return $sr->submitter_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionType::ManageServiceRequests->value)
            || $user->can(PermissionType::SubmitServiceRequests->value);
    }

    public function update(User $user, ServiceRequest $sr): bool
    {
        if (! $user->can(PermissionType::ManageServiceRequests->value)) {
            return false;
        }

        // Scope wilayah untuk operator
        if ($user->hasRole('operator_kecamatan_desa')) {
            return $this->isInUserTerritory($user, $sr);
        }

        return true;
    }

    public function delete(User $user, ServiceRequest $sr): bool
    {
        // Hanya administrator (ditangani Gate::before)
        return false;
    }

    private function isInUserTerritory(User $user, ServiceRequest $sr): bool
    {
        if ($user->village_id && $sr->village_id === $user->village_id) {
            return true;
        }
        if ($user->district_id && $sr->village?->district_id === $user->district_id) {
            return true;
        }
        return false;
    }
}
```

### 4.8 Matriks Policy × Permission × Model

| Policy | `viewAny`/`view` | `create` | `update` | `delete` | Scoping tambahan |
|---|---|---|---|---|---|
| `UserPolicy` | `kelola_user` | `kelola_user` | `kelola_user` | `kelola_user` | Tidak bisa hapus diri sendiri |
| `ServiceRequestPolicy` | `kelola_pengajuan` / `lihat_pengajuan` | `kelola_pengajuan` / `ajukan_layanan` | `kelola_pengajuan` | Admin only | Wilayah (operator), milik sendiri (masyarakat) |
| `ComplaintPolicy` | `kelola_pengaduan` / `lihat_pengaduan` | `kelola_pengaduan` / `ajukan_pengaduan` | `kelola_pengaduan` | Admin only | Wilayah (operator), milik sendiri (masyarakat) |
| `RehabilitationCasePolicy` | `kelola_rehabilitasi` / `lihat_rehabilitasi` | `kelola_rehabilitasi` | `kelola_rehabilitasi` | Admin only | Petugas yang ditugaskan (`officer_id`) |
| `ClientPolicy` | `kelola_klien` / `lihat_rehabilitasi` | `kelola_klien` | `kelola_klien` | Admin only | Data sensitif |
| `ReferralPolicy` | `kelola_rehabilitasi` | `kelola_rehabilitasi` | `kelola_rehabilitasi` | Admin only | Petugas yang ditugaskan |
| `DistrictPolicy` | Semua (data publik) | `kelola_wilayah` | `kelola_wilayah` | `kelola_wilayah` | — |
| `VillagePolicy` | Semua (data publik) | `kelola_wilayah` | `kelola_wilayah` | `kelola_wilayah` | — |
| `WorkUnitPolicy` | `kelola_unit_kerja` | `kelola_unit_kerja` | `kelola_unit_kerja` | `kelola_unit_kerja` | — |
| `ServiceTypePolicy` | Semua (untuk form) | `kelola_jenis_layanan` | `kelola_jenis_layanan` | `kelola_jenis_layanan` | — |
| `DtsenPurposePolicy` | `kelola_tujuan_dtsen` | `kelola_tujuan_dtsen` | `kelola_tujuan_dtsen` | `kelola_tujuan_dtsen` | — |
| `ComplaintCategoryPolicy` | `kelola_kategori_pengaduan` | `kelola_kategori_pengaduan` | `kelola_kategori_pengaduan` | `kelola_kategori_pengaduan` | — |
| `ClientCategoryPolicy` | `kelola_kategori_klien` | `kelola_kategori_klien` | `kelola_kategori_klien` | `kelola_kategori_klien` | — |
| `ReferralInstitutionPolicy` | `kelola_lembaga_rujukan` | `kelola_lembaga_rujukan` | `kelola_lembaga_rujukan` | `kelola_lembaga_rujukan` | — |

### 4.9 Scoping Query di Filament Resource

Selain Policy, setiap Filament Resource perlu meng-*override* `getEloquentQuery()` untuk membatasi data yang muncul di tabel:

```php
// Contoh di ServiceRequestResource
public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    $user = auth()->user();

    if ($user->hasRole('operator_kecamatan_desa')) {
        if ($user->village_id) {
            $query->where('village_id', $user->village_id);
        } elseif ($user->district_id) {
            $query->whereHas('village', fn ($q) => $q->where('district_id', $user->district_id));
        }
    }

    if ($user->hasRole('masyarakat')) {
        $query->where('submitter_id', $user->id);
    }

    return $query;
}
```

### 4.10 Checklist Migrasi Policy

- [ ] Migrasi `ServiceRequestPolicy` → permission-based + scoping wilayah
- [ ] Migrasi `ComplaintPolicy` → permission-based + scoping wilayah
- [ ] Migrasi `RehabilitationCasePolicy` → permission-based + scoping petugas
- [ ] Migrasi `ClientPolicy` → permission-based (data sensitif)
- [ ] Migrasi `ReferralPolicy` → permission-based + scoping petugas
- [ ] Migrasi `UserPolicy` → permission-based
- [ ] Migrasi `DistrictPolicy` → permission-based
- [ ] Migrasi `VillagePolicy` → permission-based
- [ ] Migrasi `WorkUnitPolicy` → permission-based
- [ ] Migrasi `ServiceTypePolicy` → permission-based
- [ ] Migrasi `DtsenPurposePolicy` → permission-based
- [ ] Migrasi `ComplaintCategoryPolicy` → permission-based
- [ ] Migrasi `ClientCategoryPolicy` → permission-based
- [ ] Migrasi `ReferralInstitutionPolicy` → permission-based
- [ ] Tambahkan `getEloquentQuery()` scope di `ServiceRequestResource`
- [ ] Tambahkan `getEloquentQuery()` scope di `ComplaintResource`
- [ ] Tambahkan `getEloquentQuery()` scope di `RehabilitationCaseResource`
- Dokumen privat tetap lewat temporary signed URL; cek Policy dilakukan sebelum URL dibuat.

---

## Fase 5: Penguatan

- [ ] Audit log (activitylog) untuk perubahan role, permission, dan status `is_active`.
- [ ] Cegah admin menghapus role dirinya sendiri atau menonaktifkan admin terakhir.
- [ ] Staf yang keluar dinonaktifkan (`is_active = false`), bukan dihapus, agar riwayat `status_histories` tetap utuh.
- [ ] Rate limit pada login dan cek tiket.

---

## Fase 6: Pengujian (PHPUnit, PostgreSQL)

Test matriks role × resource × aksi. Kasus minimal:

- [x] Masyarakat mengakses `/admin` → 403 (diverifikasi di `AdminPanelResourcesTest`)
- [x] Staff tanpa permission `kelola_user` mengakses `/admin/roles` dan `/admin/permissions` → 403
- [x] Administrator mengakses `/admin/roles` dan `/admin/permissions` → 200
- [ ] Masyarakat membuka tiket milik orang lain → ditolak
- [ ] Operator wilayah A membuka data wilayah B → ditolak
- [ ] Pimpinan mencoba edit/hapus → ditolak
- [ ] Petugas yang tidak ditugaskan membuka kasus rehabilitasi → ditolak
- [ ] User dengan `is_active = false` → tidak bisa masuk
- [x] Cache permission ter-reset setelah perubahan role (otomatis lewat `forgetCachedPermissions()` di form & page hooks)

---

## Urutan Pengerjaan yang Disarankan

1. ~~Fase 1 dan 2~~ ✅ Selesai
2. ~~Fase 3 (Gerbang Panel Filament & Manajemen Role/Permission)~~ ✅ Selesai (UserResource, RoleResource, PermissionResource, Policies)
3. Fase 4, per modul sesuai prioritas PRD: DTSEN → PBI-JK → Rehabilitasi → Pengaduan
4. Fase 5 dan 6 berjalan paralel dengan Fase 4

---

## Keputusan yang Perlu Dikonfirmasi

- Apakah operator kecamatan dan operator desa memakai level akses yang sama? Jika operator desa harus lebih sempit, aturan scope dibedakan berdasarkan kolom yang terisi (`village_id` terisi = desa; hanya `district_id` = kecamatan).

---

## File yang Telah Dimodifikasi/Dibuat

| File | Status | Keterangan |
|---|---|---|
| `app/Enums/PermissionType.php` | ✅ Baru | Enum 24 permission dengan case bahasa Inggris, nilai string Indonesia, `label()`, dan `group()` |
| `database/seeders/RolePermissionSeeder.php` | ✅ Diperbarui | Menggunakan `PermissionType` enum untuk 6 peran sistem |
| `app/Models/User.php` | ✅ Sudah benar | `canAccessPanel()` menolak `masyarakat` |
| `app/Providers/AppServiceProvider.php` | ✅ Diperbarui | `Gate::before` super-admin + registrasi Policy untuk Role & Permission |
| `app/Policies/UserPolicy.php` | ✅ Diperbarui | Menggunakan `PermissionType::ManageUsers` (`kelola_user`) |
| `app/Policies/RolePolicy.php` | ✅ Baru | Proteksi akses RoleResource via permission `kelola_user` dan cegah hapus peran sistem |
| `app/Policies/PermissionPolicy.php` | ✅ Baru | Proteksi akses PermissionResource via permission `kelola_user` |
| `app/Filament/Resources/Users/Schemas/UserForm.php` | ✅ Diperbarui | Form role dengan label ramah, validasi required, proteksi akses, dan Spatie cache flush |
| `app/Filament/Resources/Users/Tables/UsersTable.php` | ✅ Diperbarui | Kolom peran dan filter peran diformat dengan label ramah dan warna badge |
| `app/Filament/Resources/Users/Pages/CreateUser.php` | ✅ Diperbarui | Hook `afterCreate` membersihkan cache permission Spatie |
| `app/Filament/Resources/Users/Pages/EditUser.php` | ✅ Diperbarui | Hook `afterSave` membersihkan cache permission Spatie |
| `app/Filament/Resources/Roles/RoleResource.php` | ✅ Baru | Resource Filament untuk mengelola Role |
| `app/Filament/Resources/Roles/Schemas/RoleForm.php` | ✅ Baru | Form Role dengan CheckboxList permission per grup dan label `PermissionType` |
| `app/Filament/Resources/Roles/Tables/RolesTable.php` | ✅ Baru | Tabel Role dengan jumlah permission, jumlah user, dan proteksi hapus role sistem |
| `app/Filament/Resources/Roles/Pages/ListRoles.php` | ✅ Baru | Halaman daftar Role |
| `app/Filament/Resources/Roles/Pages/CreateRole.php` | ✅ Baru | Halaman buat Role dengan cache flush Spatie |
| `app/Filament/Resources/Roles/Pages/EditRole.php` | ✅ Baru | Halaman edit Role dengan proteksi hapus role sistem |
| `app/Filament/Resources/Permissions/PermissionResource.php` | ✅ Baru | Resource Filament untuk melihat dan mengelola asosiasi Permission |
| `app/Filament/Resources/Permissions/Schemas/PermissionForm.php` | ✅ Baru | Form penetapan role pada permission |
| `app/Filament/Resources/Permissions/Tables/PermissionsTable.php` | ✅ Baru | Tabel Permission dengan pencarian deskripsi, filter grup/role, dan kode copyable |
| `app/Filament/Resources/Permissions/Pages/ListPermissions.php` | ✅ Baru | Halaman daftar Permission |
| `app/Filament/Resources/Permissions/Pages/EditPermission.php` | ✅ Baru | Halaman edit penetapan role pada Permission |
| `tests/Feature/AdminPanelResourcesTest.php` | ✅ Diperbarui | Test akses Role & Permission Resource serta proteksi non-admin & masyarakat |
