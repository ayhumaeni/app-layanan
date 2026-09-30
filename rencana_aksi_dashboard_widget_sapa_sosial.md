# Rencana Aksi: Dashboard Widget SAPA SOSIAL

> **Status Eksekusi**: ✅ **SELESAI DIIMPLEMENTASIKAN**
> Seluruh widget, query service, custom dashboard page dengan filter global, batasan hak akses wilayah/peran, serta feature test telah berhasil dibuat dan lulus pengujian (36 tests, 147 assertions).

Sumber: `PRD_SAPA_SOSIAL.md` (Bagian 3: Laporan & Dashboard, dan Bagian 4: Rancangan Database)
Stack: Laravel 13 · Filament v5 · Livewire v4 · PostgreSQL

Widget berasal dari **Dashboard Utama** di Bagian 3 PRD (9 informasi). Data diambil dari tabel di Bagian 4.

---

## Daftar Widget yang Muncul dari PRD

| # | Widget | Tipe Filament | Sumber data | Peran yang melihat |
|---|--------|---------------|-------------|--------------------|
| 1 | SK DTSEN diterbitkan (per tujuan & per desil) | `StatsOverviewWidget` + `ChartWidget` (bar) | `dtsen_certificates` (`issued_at`, `dtsen_purpose_id`, `decile`) | Admin, Pimpinan, Petugas |
| 2 | SK DTSEN menunggu tanda tangan | `TableWidget` | `service_requests` status `awaiting_approval` + `approvals` (`decision = pending`) | Pejabat Penandatangan, Pimpinan, Admin |
| 3 | Reaktivasi PBI-JK per tahap | `ChartWidget` (bar/doughnut) + stats "tertahan" | `service_requests` (status) + `pbi_reactivations.proposed_to_ministry_at` | Admin, Pimpinan, Petugas |
| 4 | Reaktivasi darurat medis (prioritas belum selesai) | `TableWidget` | `service_requests.is_priority` + `pbi_reactivations.reason = emergency` | Petugas, Pimpinan |
| 5 | Kasus rehabilitasi aktif + rujukan per lembaga | `StatsOverviewWidget` + `ChartWidget` | `rehabilitation_cases.status`, `referrals` + `referral_institutions` | Petugas rehabilitasi, Pimpinan (ringkasan) |
| 6 | Pengajuan & pengaduan masuk | `ChartWidget` (line, tren) | `service_requests.submitted_at`, `complaints.reported_at` | Semua peran admin panel |
| 7 | Dalam proses vs selesai | `StatsOverviewWidget` + `ChartWidget` | `service_requests.status`, `complaints.status` | Semua peran admin panel |
| 8 | Sebaran per wilayah | `ChartWidget` (bar) atau `TableWidget` | join `villages` → `districts` | Semua; Operator hanya wilayahnya |
| 9 | Informasi paling sering diakses *(opsional)* | `TableWidget` | `page_visits`, `search_logs` | Admin, Pimpinan |

Semua widget difilter berdasarkan **periode, jenis layanan, status, kecamatan, dan desa/kelurahan**. Operator Kecamatan/Desa hanya melihat data wilayahnya.

---

## Fase 1 — Fondasi (sebelum widget pertama)

1. **Panel & dashboard.** Buat halaman `Dashboard` kustom di panel `/admin` dengan `HasFiltersForm` (filter halaman dari Filament) agar satu filter berlaku untuk semua widget.
2. **Filter global** sesuai PRD: periode, jenis layanan, status, kecamatan, desa/kelurahan. Kecamatan dan desa dibuat dependent select (desa mengikuti kecamatan).
3. **Enum status.** Pastikan PHP Enum status (`ServiceRequestStatus`, `ComplaintStatus`, `RehabilitationCaseStatus`, `ReferralStatus`) sudah ada, lengkap dengan `label()` bahasa Indonesia. Widget memakainya untuk label dan warna.
4. **Pengelompokan status per tahap.** Buat method di Enum (mis. `stage()`) yang memetakan status ke kelompok dashboard: "verifikasi", "menunggu Kemensos", "aktif kembali", "ditolak". Kelompok ini dipakai widget 3 dan 7.
5. **Pembatasan data per peran/wilayah.** Buat satu trait/scope, mis. `scopeForCurrentUser()`, yang membatasi query untuk Operator Kecamatan/Desa (`district_id`/`village_id` di `users`) dan Petugas Rehabilitasi (hanya kasus yang ditugaskan). Semua widget wajib memakainya agar aturan hak akses tidak bocor lewat dashboard.
6. **Pengaturan admin.** Siapkan pengaturan "batas hari tertahan di `proposed_to_ministry`" (PRD: diatur admin, tidak boleh hardcode). Widget 3 membacanya dari sini.
7. **Index database.** Tambahkan index pada `status`, `village_id`, `service_type_id`, `submitted_at`, `issued_at`, `reported_at`, `received_at` (sesuai konvensi 4.1).

## Fase 2 — Query Layer Bersama

Pisahkan logika hitung dari widget supaya bisa dipakai ulang oleh laporan Excel/PDF (Bagian 3 "Laporan Berkala") dan mudah diuji.

- Buat kelas per domain, mis. `DtsenStatistics`, `PbiStatistics`, `RehabilitationStatistics`, `ComplaintStatistics`, `TicketStatistics`.
- Setiap kelas menerima objek filter (periode, layanan, status, kecamatan, desa) dan mengembalikan koleksi/angka.
- Gunakan agregasi SQL (`groupBy`, `count`, `avg` selisih tanggal), jangan hitung di PHP.
- Beri **cache pendek** (mis. 1–5 menit) dengan kunci yang memuat filter dan `user_id`/wilayah, supaya dashboard cepat tanpa mengorbankan "real-time" PRD.

## Fase 3 — Widget Prioritas Layanan Utama

Kerjakan sesuai tiga layanan prioritas PRD.

### 3.1 Widget SK DTSEN (nomor 1 dan 2)

- Stats: total terbit, ditolak, menunggu tanda tangan pada periode.
- Chart: terbit per tujuan penggunaan (`dtsen_purposes`) dan per desil (1–10).
- Tabel antrean: draf yang menunggu paraf/persetujuan; kolom nomor tiket, pemohon, tujuan, tahap (Kabid/Kadis), lama menunggu. Aksi cepat "Buka" ke Resource.
- Aturan: "terbit" dihitung dari `issued_at`; "ditolak" dari status `rejected`.

### 3.2 Widget Reaktivasi PBI-JK (nomor 3 dan 4)

- Chart per tahap (verifikasi, menunggu Kemensos, aktif kembali, ditolak, termasuk `ministry_rejected`).
- Stat "Perlu ditindaklanjuti": tiket berstatus `proposed_to_ministry` yang `proposed_to_ministry_at` melewati batas hari.
- Tabel darurat medis: `is_priority = true` dan belum `completed`/`rejected`, urut terlama dahulu.

### 3.3 Widget Rehabilitasi Sosial (nomor 5)

- Stats: kasus aktif per status (`assessment`, `in_service`, `monitoring`; `service_planning` dan `received` ikut dihitung sebagai aktif).
- Chart: rujukan per lembaga tujuan (`referral_institutions`), dipecah per status rujukan.
- Kepatuhan privasi: untuk Pimpinan tampilkan **ringkasan angka saja**, tanpa nama klien (PRD: identitas klien adalah data sensitif).

## Fase 4 — Widget Lintas Layanan

- **Nomor 6**: tren harian/mingguan/bulanan, dua garis: pengajuan (`service_requests`) dan pengaduan (`complaints`); bisa dipecah per jenis layanan/kategori.
- **Nomor 7**: stats "belum selesai" per status dan "selesai" (`completed`, `resolved`). Pisahkan pengajuan dan pengaduan karena status berbeda.
- **Nomor 8**: sebaran per kecamatan (bar); klik/filter untuk turun ke desa. Gabungkan `service_requests.village_id` dan `complaints.village_id` lewat `villages` → `districts`. Untuk Operator, otomatis satu kecamatan/desa.
- **Nomor 9 (opsional)**: top 10 konten dari `page_visits` (jumlahkan `visit_count` per periode) dan top kata kunci dari `search_logs`. Kerjakan terakhir dan konfirmasi dulu apakah masuk ruang lingkup.

## Fase 5 — Hak Akses & Tampilan per Peran

1. Gunakan `canView()` pada tiap widget untuk menyembunyikan widget yang tidak relevan (mis. widget 2 hanya untuk Pejabat Penandatangan, Pimpinan, Admin; widget 5 untuk petugas rehabilitasi, admin, pimpinan).
2. Permission usulan per widget, mis. `view_widget_dtsen`, `view_widget_pbi`, `view_widget_rehabilitation`, `view_widget_complaints`, `view_widget_regional`, `view_widget_information`. Dengan begitu peran gabungan (Kadis = Pimpinan + Penandatangan) otomatis mendapat gabungan widget.
3. Pimpinan hanya baca: pastikan tabel antrean di widget tidak menampilkan aksi ubah status.
4. Uji: tiap peran hanya melihat angka dari data yang boleh diakses.

## Fase 6 — Ekspor & Laporan Terkait Dashboard

- Tambahkan tombol ekspor Excel/PDF pada laporan berkala yang memakai kelas query yang sama dari Fase 2: Rekap SK DTSEN, Rekap Reaktivasi PBI-JK, Laporan Rehabilitasi, Laporan Pelayanan, Laporan Pengaduan.
- Ekspor besar dijalankan lewat queue (driver `database`), sesuai Konteks Teknis PRD.

## Fase 7 — Pengujian & Penyelesaian

1. **Feature test (Pest, PostgreSQL)** untuk tiap kelas statistik dengan data seed yang angkanya diketahui.
2. **Test hak akses:** Operator hanya wilayahnya, Petugas Rehabilitasi hanya kasusnya, Pimpinan tanpa aksi tulis.
3. **Test filter:** kombinasi periode + layanan + status + wilayah menghasilkan angka konsisten antar widget dan laporan.
4. **Performa:** cek `EXPLAIN` untuk query terberat (sebaran wilayah, tren) dengan data uji besar.
5. **Seeder demo** agar dashboard terisi saat demo ke pimpinan.

---

## Urutan Kerja yang Disarankan

1. Fase 1 dan 2 (fondasi + query layer)
2. Widget 1, 2 (SK DTSEN) → widget 3, 4 (PBI-JK) → widget 5 (rehabilitasi)
3. Widget 6, 7, 8 (lintas layanan)
4. Fase 5 (hak akses per widget) dikerjakan bersamaan tiap widget selesai, bukan di akhir
5. Fase 6 dan 7, lalu widget 9 jika disetujui

---

## Versi Ringkas: 5 Kartu Angka

Jika dashboard ingin diringkas, cukup satu `StatsOverviewWidget` berisi 5 kartu di bagian atas:

| # | Kartu | Isi | Sumber |
|---|-------|-----|--------|
| 1 | SK DTSEN terbit | Jumlah surat terbit pada periode terpilih | `dtsen_certificates.issued_at` |
| 2 | Menunggu tanda tangan | Draf SK DTSEN yang menunggu paraf/persetujuan | `approvals` (`decision = pending`) |
| 3 | PBI-JK perlu tindak lanjut | Tertahan di `proposed_to_ministry` melebihi batas hari, plus darurat medis prioritas yang belum selesai | `pbi_reactivations`, `service_requests.is_priority` |
| 4 | Kasus rehabilitasi aktif | Kasus yang belum `closed` | `rehabilitation_cases.status` |
| 5 | Tiket belum selesai | Pengajuan dan pengaduan yang masih diproses | `service_requests`, `complaints` |

Detail seperti per desil, per tujuan, per tahap, dan sebaran wilayah dapat dipindah ke halaman laporan atau muncul saat kartu diklik.

---

## Hal yang Perlu Dikonfirmasi Lebih Dulu

- **Batas hari tertahan** untuk `proposed_to_ministry`, serta batas lama nonaktif PBI-JK: PRD menyerahkannya ke admin dan nilai awalnya perlu dicek ke SOP Dinsos.
---
 
 ## Realisasi Implementasi
 
 | Komponen | File / Kelas | Keterangan |
 |---|---|---|
 | **Dashboard Page & Filter Global** | `app/Filament/Pages/Dashboard.php` | Page `HasFiltersForm` dengan filter: `startDate`, `endDate`, `service_type_id`, `district_id`, `village_id` (dependent dropdown). |
 | **Statistik & Query Service** | `app/Services/DashboardStatisticsService.php` | Aggregation service untuk overview stats, DTSEN per purpose, PBI stages, rehab cases, trend, regional distribution, dengan territory & role scoping. |
 | **Widget 1: KPI Overview** | `app/Filament/Widgets/SapaSosialOverviewWidget.php` | `StatsOverviewWidget` 5 kartu metrik: DTSEN terbit, menunggu tanda tangan, PBI-JK tindak lanjut, rehab aktif, tiket dalam proses. |
 | **Widget 2: Antrean SK DTSEN** | `app/Filament/Widgets/DtsenPendingApprovalWidget.php` | `TableWidget` antrean berkas status `awaiting_approval` untuk pejabat penandatangan / pimpinan. |
 | **Widget 3: Prioritas PBI Darurat** | `app/Filament/Widgets/PbiEmergencyPriorityWidget.php` | `TableWidget` darurat medis prioritas BPJS PBI-JK yang belum tuntas. |
 | **Widget 4: Chart DTSEN Tujuan** | `app/Filament/Widgets/DtsenPurposeChartWidget.php` | `ChartWidget` (doughnut) peruntukan SK DTSEN. |
 | **Widget 5: Chart PBI per Tahap** | `app/Filament/Widgets/PbiStageChartWidget.php` | `ChartWidget` (bar) per tahap verifikasi, kementerian, aktif, dan penolakan. |
 | **Widget 6: Chart Kasus Rehab** | `app/Filament/Widgets/RehabilitationCasesChartWidget.php` | `ChartWidget` (doughnut) status penanganan rehabilitasi sosial. |
 | **Widget 7: Tren Harian** | `app/Filament/Widgets/ServiceAndComplaintTrendChartWidget.php` | `ChartWidget` (line) tren pengajuan layanan vs laporan pengaduan harian. |
 | **Widget 8: Sebaran Wilayah** | `app/Filament/Widgets/RegionalDistributionChartWidget.php` | `ChartWidget` (bar) sebaran per kecamatan Kabupaten Blitar (ter-scope untuk operator). |
 | **Feature Test Suite** | `tests/Feature/DashboardWidgetsTest.php` | 7 test methods, 51 assertions (akses, hak akses peran, scoping operator, filter tanggal, dan render Livewire semua widget). |

