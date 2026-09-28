# Prompt Google Stitch — Portal Publik SAPA SOSIAL

Prompt untuk https://stitch.withgoogle.com/ berdasarkan PRD *SAPA SOSIAL — Satu Pintu Layanan Sosial Kabupaten Blitar*. Cakupan: **Portal Publik** (`/`) saja. Panel admin Filament tidak disertakan.

## Cara Pakai

1. Buka Stitch, pilih mode **Web** (responsif, mobile-first).
2. Tempel **Prompt 1** lebih dulu untuk menetapkan gaya visual dan halaman Beranda.
3. Lanjutkan **Prompt 2–7** satu per satu di proyek yang sama agar desain konsisten.
4. Revisi per bagian dengan kalimat pendek, misalnya: *"Perbesar tombol CTA hero dan buat warna sekunder lebih hangat."*
5. Ekspor ke HTML/CSS atau Figma sebagai acuan visual, lalu terjemahkan ke komponen Blade/Livewire + Tailwind CSS v4.

> Prompt ditulis dalam bahasa Inggris agar hasil Stitch lebih konsisten. Semua teks antarmuka tetap dalam bahasa Indonesia.

---

## Prompt 1 — Design System + Beranda

```
Design a mobile-first, fully responsive public service portal website for "SAPA SOSIAL — Satu Pintu Layanan Sosial Kabupaten Blitar", the social services one-stop portal of Dinas Sosial Kabupaten Blitar (Indonesian local government). Citizens use it to apply for social services, file complaints, and track their ticket status online. All UI text must be in Bahasa Indonesia.

AUDIENCE: ordinary Indonesian citizens, including elderly people and low-digital-literacy users, many on cheap Android phones. Design for clarity, large tap targets (min 48px), high contrast (WCAG AA), plain language, no jargon.

VISUAL STYLE:
- Trustworthy, warm, modern government look — not corporate, not childish
- Primary: deep teal-blue (#0F5C7A); Secondary: warm amber (#F5A623) for key CTAs and priority highlights; Success: green (#1E9E5A); Warning: orange; Error: red; Background: soft off-white (#F7FAFB); text: dark slate
- Font: Inter or Plus Jakarta Sans, base size 16px+, generous line height
- Rounded corners (12px), soft shadows, friendly line icons, plenty of whitespace
- Status badges with color + icon + text (never color alone)

BUILD THE HOME PAGE ("Beranda") with:
1. Top navbar: logo placeholder + "SAPA SOSIAL / Dinas Sosial Kab. Blitar"; menu: Beranda, Layanan, Pengaduan, Cek Status, Verifikasi Surat, Informasi; buttons "Masuk" and "Daftar". Hamburger menu on mobile.
2. Hero: headline "Satu Pintu Layanan Sosial Kabupaten Blitar", subheadline "Ajukan layanan, sampaikan pengaduan, dan pantau prosesnya dengan nomor tiket." Two prominent CTAs: "Ajukan Layanan" (primary) and "Sampaikan Pengaduan" (amber). Below them a compact "Cek Status Tiket" input box (ticket number field + button "Lacak").
3. "Layanan Prioritas" — 3 large cards with icon, short description, and "Ajukan" button:
   - Surat Keterangan DTSEN — "Untuk syarat SPMB jalur afirmasi, PIP, KIP Kuliah, bantuan sosial, dan layanan kesehatan."
   - Reaktivasi KIS / PBI-JK — "Aktifkan kembali kepesertaan JKN-KIS yang dinonaktifkan."
   - Pelayanan Rehabilitasi Sosial — "Untuk lansia terlantar, penyandang disabilitas, ODGJ, anak, dan korban kekerasan."
4. Secondary quick-action row: "Layanan Sosial Lainnya", "Pengaduan Sosial", "Verifikasi Keaslian Surat DTSEN", "Informasi & Formulir Unduhan".
5. "Cara Kerja" — 4-step horizontal/vertical stepper: Pilih Layanan → Isi Data & Unggah Dokumen → Dapatkan Nomor Tiket → Pantau Status Sampai Selesai.
6. Search bar for service information ("Cari layanan atau informasi...").
7. FAQ accordion (4 items), then a "Butuh bantuan langsung?" block with address, office hours, phone, and a note "Operator Kecamatan/Desa/Puskesos siap membantu Anda mengajukan".
8. Footer: contact, quick links, "© Dinas Sosial Kabupaten Blitar".

Keep the page clean and scannable; do not add features beyond those listed.
```

---

## Prompt 2 — Daftar Layanan & Detail Layanan (Informasi)

```
Using the same design system, create two pages:

A) "Layanan & Informasi" listing page: search bar with keyword search, category filter chips (Semua, Program Sosial, Rehabilitasi Sosial, Disabilitas, Lansia, Pengaduan), and a grid of service cards (title, one-line description, "Lihat Detail"). Include a sidebar or section "Formulir Unduhan" with a list of downloadable files (name, version badge "Versi terbaru", PDF icon, "Unduh" button).

B) Service detail page for "Surat Keterangan DTSEN" with sections: Deskripsi; Persyaratan (checklist: KTP, KK); Tujuan Penggunaan (chips: SPMB, PIP, KIP Kuliah, Bantuan Sosial, Kesehatan, Lainnya); Alur Pelayanan (vertical numbered timeline); Waktu & Lokasi Pelayanan; Kontak; Formulir Unduhan; FAQ accordion. Add a sticky bottom bar on mobile (and a right-side sticky card on desktop) with primary button "Ajukan Layanan Ini" and secondary "Sampaikan Pengaduan". Include a breadcrumb and "Terakhir diperbarui" date.
```

---

## Prompt 3 — Form Pengajuan Surat Keterangan DTSEN (Multi-step)

```
Using the same design system, create a multi-step application form page for "Surat Keterangan DTSEN" with a progress stepper at the top (4 steps) and one clear task per screen, big inputs, inline helper text, and a persistent "Kembali" / "Lanjut" button bar.

Step 1 "Pilih Tujuan": radio cards for tujuan penggunaan (SPMB, PIP, KIP Kuliah, Bantuan Sosial, Kesehatan, Lainnya) with a text field "Keterangan tujuan" if Lainnya.
Step 2 "Data Pemohon": Nama Lengkap, NIK (16 digit), No. KK (16 digit), Alamat, Kecamatan (dropdown), Desa/Kelurahan (dependent dropdown), No. HP. Then section "Data Orang yang Diterangkan": Nama, NIK, Hubungan dengan pemohon (dropdown, e.g. Anak, Diri Sendiri).
Step 3 "Unggah Dokumen": two upload dropzones — "KTP" and "KK" (required marker, file type and max size hint, preview thumbnail, replace/remove, upload success state).
Step 4 "Tinjau & Kirim": read-only summary cards with "Ubah" links, consent checkbox "Saya menyatakan data yang diisi benar", button "Kirim Pengajuan".

Also design the validation error state (inline red messages with icon) and a friendly note banner: "Pengajuan dapat dibantu oleh Operator Kecamatan/Desa."
Additionally, design the success screen: big check icon, "Pengajuan Berhasil Dikirim", the ticket number in a large copyable box (example: DTSEN-202610-00012), a note "Simpan nomor tiket ini untuk memantau status", and buttons "Cek Status" and "Kembali ke Beranda".
```

---

## Prompt 4 — Form Reaktivasi KIS/PBI-JK

```
Using the same design system, create the application form page for "Reaktivasi KIS / PBI-JK" as a multi-step form: Data Peserta (nama, NIK, No. KK, alamat, kecamatan, desa, No. HP, nomor kartu BPJS/KIS, perkiraan tanggal nonaktif); Alasan Reaktivasi (radio cards: Penyakit Kronis/Katastropik, Kondisi Darurat Medis, Bayi Baru Lahir dari Ibu Peserta PBI, Lainnya); Unggah Dokumen (KTP, KK, Kartu BPJS/KIS, and "Surat Keterangan Fasilitas Kesehatan" — field for nama faskes and nomor surat — that becomes REQUIRED when a medical reason is selected, with a dynamic required-state indicator); Tinjau & Kirim.
When "Kondisi Darurat Medis" is selected, show an amber priority banner: "Pengajuan darurat medis akan diprioritaskan oleh petugas."
```

---

## Prompt 5 — Form Pengaduan Sosial

```
Using the same design system, create the "Pengaduan Sosial" form page. Fields: Kategori Permasalahan (dropdown/cards), Lokasi Kejadian (Kecamatan, Desa/Kelurahan required; optional detail alamat text), Deskripsi Permasalahan (textarea with character hint), Unggah Foto/Dokumen (optional, multiple, preview grid), Data Pelapor (Nama, No. HP — both required, with a note that contact is needed for follow-up/clarification). Short intro text explaining what kinds of issues can be reported. Submit button "Kirim Laporan". Include the success screen with ticket number format ADU-202610-00004 and a "Cek Status" button.
```

---

## Prompt 6 — Cek Status Tiket (Pelacakan)

```
Using the same design system, create the "Cek Status Tiket" page. A simple verification form: Nomor Tiket + "4 digit terakhir NIK atau No. HP" + button "Lacak". Show the result state for a ticket "PBI-202610-00007 — Reaktivasi KIS/PBI-JK": a header summary card (ticket number, service, date submitted, current status badge "Menunggu Keputusan Kemensos"), and a vertical progress timeline with these steps and per-step state (selesai / sedang berjalan / belum): Diajukan → Pemeriksaan Berkas → Verifikasi Kelayakan → Menunggu Persetujuan → Surat Rekomendasi Terbit → Diusulkan ke Kemensos → Disetujui Kemensos → Aktif Kembali → Selesai. Each completed step shows date/time and a short note.
Also design alternate states as small variants: "Perlu Perbaikan Berkas" (orange alert card with petugas note and button "Perbaiki Pengajuan"), "Ditolak" (red card with reason and next-step guidance), "Selesai" (green card with "Unduh Surat" button), and "Tiket tidak ditemukan" (empty/error state).
```

---

## Prompt 7 — Verifikasi Keaslian Surat & Akun Masyarakat

```
Using the same design system, create two pages:

A) "Verifikasi Keaslian Surat DTSEN" (public, no login): input for kode verifikasi + button "Periksa" (also mention the QR on the letter leads here). Design three result states: VALID (green shield check, showing nomor surat, nama, tujuan penggunaan, tanggal terbit, masa berlaku, pejabat penandatangan), KEDALUWARSA (orange, "Surat sudah melewati masa berlaku"), and TIDAK DITEMUKAN (red, "Kode verifikasi tidak ditemukan"). Mask personal data partially (e.g. NIK 3507********1234).

B) "Akun Saya" citizen dashboard (after login): greeting, buttons "Ajukan Layanan" and "Sampaikan Pengaduan", and a "Riwayat Pengajuan & Pengaduan" table/list (on mobile: cards) with ticket number, service, date, status badge, and "Lihat Detail". Include tabs: Semua, Dalam Proses, Selesai. Also design the simple Login and Daftar pages (email/No. HP + password, "Lupa kata sandi?").
```

---

## Pemetaan ke PRD

| Prompt | Halaman | Layanan PRD |
|--------|---------|-------------|
| 1 | Beranda | Portal publik (semua layanan) |
| 2 | Daftar & detail layanan, formulir unduhan, FAQ | Layanan 6 |
| 3 | Form + sukses tiket SK DTSEN | Layanan 1 |
| 4 | Form Reaktivasi KIS/PBI-JK | Layanan 2 |
| 5 | Form Pengaduan | Layanan 5 |
| 6 | Cek status tiket (nomor tiket + 4 digit NIK/HP) | Layanan 1, 2, 4, 5 |
| 7 | Verifikasi keaslian surat, akun masyarakat | Layanan 1, Portal publik |

**Belum tercakup:** layanan Rehabilitasi Sosial (Layanan 3) dan Pengajuan Lainnya (Layanan 4) tidak punya form publik sendiri di prompt ini. Rehabilitasi ditangani petugas dan berasal dari pengajuan/pengaduan, sedangkan Pengajuan Lainnya bisa memakai pola form yang sama dengan Prompt 3.
