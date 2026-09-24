<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\InformationCategory;
use App\Enums\PublishStatus;
use App\Models\DownloadableForm;
use App\Models\Faq;
use App\Models\InformationPage;
use App\Models\PageVisit;
use App\Models\SearchLog;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class InformationPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@blitarkab.go.id')->first();
        $dtsenType = ServiceType::where('code', 'DTSEN')->first();
        $pbiType = ServiceType::where('code', 'PBI')->first();
        $rehsosType = ServiceType::where('code', 'REHSOS')->first();
        $atensiType = ServiceType::where('code', 'ATENSI')->first();
        $alatBantuType = ServiceType::where('code', 'ALAT_BANTU')->first();

        $pages = [
            [
                'title' => 'Penerbitan Surat Keterangan Data Tunggal Sosial Ekonomi Nasional (DTSEN)',
                'slug' => 'surat-keterangan-dtsen',
                'category' => InformationCategory::Program,
                'service_type_id' => $dtsenType?->id,
                'description' => 'Layanan penerbitan Surat Keterangan DTSEN untuk menerangkan posisi desil seseorang atau keluarga dalam basis data sosial ekonomi, digunakan sebagai syarat SPMB Afirmasi, KIP Kuliah, PIP, dan beasiswa.',
                'requirements' => "1. Foto/Scan KTP Pemohon (Asli)\n2. Foto/Scan Kartu Keluarga (KK) (Asli)\n3. Dokumen pendukung lain jika diperlukan (surat pengantar desa)",
                'procedure' => "1. Pemohon mendaftar atau mengisi formulir pengajuan online di portal SAPA SOSIAL.\n2. Mengunggah dokumen KTP dan KK.\n3. Petugas memverifikasi kelengkapan berkas dan mengecek data pada sistem SIKS-NG Kemensos.\n4. Apabila memenuhi kriteria batas desil, sistem menerbitkan draf surat dan diajukan paraf serta tanda tangan pejabat.\n5. Surat Keterangan DTSEN terbit bertanda QR Code verifikasi dan dapat diunduh langsung.",
                'service_hours' => 'Senin – Kamis: 08.00 – 15.00 WIB, Jumat: 08.00 – 14.30 WIB',
                'location' => 'Front Office Pelayanan Terpadu Dinas Sosial Kabupaten Blitar, Jl. Sudanco Supriyadi No. 17 Kanigoro',
                'contact' => 'WhatsApp Layanan: 0812-3456-7890 | Email: dinsos@blitarkab.go.id',
                'publish_status' => PublishStatus::Published,
                'published_at' => Carbon::now()->subMonths(2),
                'manager_id' => $admin?->id,
                'forms' => [
                    [
                        'name' => 'Formulir Permohonan Surat Keterangan DTSEN.pdf',
                        'file_path' => 'forms/formulir_sk_dtsen.pdf',
                        'version' => '1.0',
                    ],
                ],
                'faqs' => [
                    [
                        'question' => 'Apakah pembuatan Surat Keterangan DTSEN dipungut biaya?',
                        'answer' => 'Tidak dipungut biaya sama sekali (GRATIS). Seluruh pelayanan di Dinas Sosial Kabupaten Blitar bebas dari pungli.',
                        'sort_order' => 1,
                    ],
                    [
                        'question' => 'Berapa batas desil untuk mendaftar SPMB jalur afirmasi atau KIP Kuliah?',
                        'answer' => 'Sesuai ketentuan, batas desil maksimal untuk SPMB jalur afirmasi adalah Desil 1 sampai dengan Desil 5, sedangkan untuk beasiswa KIP Kuliah dan PIP maksimal Desil 4.',
                        'sort_order' => 2,
                    ],
                    [
                        'question' => 'Bagaimana jika nama saya belum terdaftar di basis data DTSEN / SIKS-NG?',
                        'answer' => 'Bila belum terdaftar, pengajuan surat belum dapat diterbitkan. Anda disarankan mengajukan usulan pendaftaran melalui Musyawarah Desa/Kelurahan (Musdes/Muskel) di desa/kelurahan setempat.',
                        'sort_order' => 3,
                    ],
                ],
            ],
            [
                'title' => 'Fasilitasi Reaktivasi Kepesertaan JKN-KIS / PBI-JK Dinonaktifkan',
                'slug' => 'reaktivasi-kis-pbi-jk',
                'category' => InformationCategory::Program,
                'service_type_id' => $pbiType?->id,
                'description' => 'Fasilitasi pengusulan reaktivasi kembali kartu Indonesia Sehat Penerima Bantuan Iuran Jaminan Kesehatan (PBI-JK) yang dinonaktifkan oleh Kementerian Sosial, khususnya bagi warga miskin atau kondisi medis mendesak.',
                'requirements' => "1. Foto/Scan KTP dan KK Peserta\n2. Foto Kartu KIS / BPJS Kesehatan yang dinonaktifkan\n3. Surat Keterangan Sakit / Resume Medis Fasilitas Kesehatan (Wajib untuk kondisi darurat/penyakit kronis)",
                'procedure' => "1. Pemohon mengajukan permohonan melalui portal SAPA SOSIAL atau melalui operator desa/kecamatan.\n2. Petugas memverifikasi kelayakan desil dan kriteria masa nonaktif.\n3. Pejabat menerbitkan surat rekomendasi reaktivasi PBI-JK.\n4. Petugas menginput usulan ke SIKS-NG Kemensos RI.\n5. Status aktif kembali dapat dipantau langsung via nomor tiket.",
                'service_hours' => 'Senin – Kamis: 08.00 – 15.00 WIB, Jumat: 08.00 – 14.30 WIB',
                'location' => 'Front Office Pelayanan Terpadu Dinas Sosial Kabupaten Blitar, Jl. Sudanco Supriyadi No. 17 Kanigoro',
                'contact' => 'WhatsApp Layanan: 0812-3456-7896 | Email: dinsos@blitarkab.go.id',
                'publish_status' => PublishStatus::Published,
                'published_at' => Carbon::now()->subMonths(2),
                'manager_id' => $admin?->id,
                'forms' => [
                    [
                        'name' => 'Formulir Pengusulan Reaktivasi KIS PBI-JK.pdf',
                        'file_path' => 'forms/formulir_reaktivasi_pbi.pdf',
                        'version' => '1.0',
                    ],
                ],
                'faqs' => [
                    [
                        'question' => 'Mengapa kartu BPJS PBI-JK saya tiba-tiba tidak aktif?',
                        'answer' => 'Penonaktifan dapat disebabkan oleh pemutakhiran data berkala dari Kemensos (SK Pencabutan), perbaikan data kependudukan NIK tidak padan, atau kuota kepesertaan daerah.',
                        'sort_order' => 1,
                    ],
                    [
                        'question' => 'Berapa lama proses reaktivasi kartu PBI-JK?',
                        'answer' => 'Rekomendasi dari Dinsos diterbitkan dalam 1-3 hari kerja. Proses persetujuan di Kemensos RI dan aktivasi di BPJS Kesehatan umumnya berlangsung 3 hingga 14 hari kerja tergantung jadwal cut-off pusat.',
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'title' => 'Alur dan Pelayanan Rehabilitasi Sosial bagi Pemerlu Pelayanan Kesejahteraan Sosial (PPKS)',
                'slug' => 'alur-pelayanan-rehabilitasi-sosial',
                'category' => InformationCategory::Rehabilitation,
                'service_type_id' => $rehsosType?->id,
                'description' => 'Mekanisme penanganan rehabilitasi sosial terpadu untuk lansia terlantar, penyandang disabilitas, ODGJ terlantar, anak berhadapan dengan hukum, dan korban tindak kekerasan.',
                'requirements' => "1. Laporan warga / rekomendasi pemerintah desa / laporan kepolisian\n2. Dokumen identitas klien (jika tersedia)\n3. Informasi lokasi keberadaan klien",
                'procedure' => "1. Penerimaan laporan atau klien oleh Pekerja Sosial / Tim Reaksi Cepat (TRC) Dinsos.\n2. Assessment komprehensif terhadap kondisi fisik, psikis, dan sosial klien.\n3. Penyusunan rencana pelayanan (pelayanan langsung atau rujukan ke panti/RS/balai).\n4. Pelaksanaan intervensi penanganan dan monitoring perkembangan berkala hingga terminasi kasus.",
                'service_hours' => 'Pelayanan Kantor: Hari Kerja 08.00 – 15.00 WIB | Respon Pengaduan Darurat: 24 Jam',
                'location' => 'Bidang Rehabilitasi Sosial, Dinas Sosial Kabupaten Blitar',
                'contact' => 'Hotline TRC Rehsos: 0812-3456-7897',
                'publish_status' => PublishStatus::Published,
                'published_at' => Carbon::now()->subMonths(1),
                'manager_id' => $admin?->id,
                'forms' => [],
                'faqs' => [
                    [
                        'question' => 'Bagaimana jika menemukan orang dengan gangguan jiwa (ODGJ) atau lansia terlantar di jalan?',
                        'answer' => 'Masyarakat dapat segera melapor melalui menu Pengaduan Sosial di SAPA SOSIAL dengan menyertakan foto dan titik lokasi, atau menghubungi hotline dinas.',
                        'sort_order' => 1,
                    ],
                ],
            ],
            [
                'title' => 'Layanan Bantuan Alat Bantu bagi Penyandang Disabilitas',
                'slug' => 'layanan-alat-bantu-disabilitas',
                'category' => InformationCategory::Disability,
                'service_type_id' => $alatBantuType?->id,
                'description' => 'Fasilitasi penyediaan dan rekomendasi bantuan alat bantu fisik bagi penyandang disabilitas di Kabupaten Blitar (kursi roda, kruk ketiak, walker, hearing aid, tongkat adaptif).',
                'requirements' => "1. Fotokopi KTP dan KK Penyandang Disabilitas\n2. Surat Keterangan Ragam Disabilitas dari Dokter Puskesmas/RSUD\n3. Surat Pengantar Desa/Kelurahan\n4. Foto seluruh badan pemohon",
                'procedure' => "1. Pemohon mengajukan permohonan melalui sistem SAPA SOSIAL.\n2. Petugas melakukan penelaahan berkas dan verifikasi lapangan (home visit).\n3. Verifikasi ketersediaan alokasi bantuan APBD / Balai Kemensos.\n4. Penyerahan alat bantu kepada penerima manfaat.",
                'service_hours' => 'Senin – Jumat: 08.00 – 15.00 WIB',
                'location' => 'Bidang Rehabilitasi Sosial Dinas Sosial Kabupaten Blitar',
                'contact' => 'Telepon: 0342-801234',
                'publish_status' => PublishStatus::Published,
                'published_at' => Carbon::now()->subMonths(1),
                'manager_id' => $admin?->id,
                'forms' => [],
                'faqs' => [],
            ],
            [
                'title' => 'Tata Cara dan Saluran Pengaduan Sosial Kabupaten Blitar',
                'slug' => 'tata-cara-pengaduan-sosial',
                'category' => InformationCategory::Complaint,
                'service_type_id' => null,
                'description' => 'Panduan masyarakat dalam menyampaikan laporan permasalahan sosial, keluhan bantuan sosial, atau penemuan warga terlantar di wilayah Kabupaten Blitar.',
                'requirements' => "1. Identitas pelapor (Nama & Nomor HP aktif untuk koordinasi)\n2. Lokasi kejadian jelas (Desa & Kecamatan)\n3. Deskripsi kronologi masalah\n4. Bukti foto pendukung (jika ada)",
                'procedure' => "1. Buka menu Pengaduan Sosial pada portal SAPA SOSIAL.\n2. Pilih kategori masalah sosial dan isi formulir laporan.\n3. Simpan nomor tiket pengaduan untuk memantau tindak lanjut.\n4. Petugas akan menghubungi pelapor jika membutuhkan klarifikasi lapangan.",
                'service_hours' => 'Online 24 Jam (Diproses pada jam kerja)',
                'location' => 'Sistem SAPA SOSIAL Dinas Sosial Kabupaten Blitar',
                'contact' => 'WhatsApp Pengaduan: 0812-3456-7898',
                'publish_status' => PublishStatus::Published,
                'published_at' => Carbon::now()->subMonths(2),
                'manager_id' => $admin?->id,
                'forms' => [],
                'faqs' => [
                    [
                        'question' => 'Apakah identitas pelapor pengaduan sosial dirahasiakan?',
                        'answer' => 'Ya, identitas pelapor dilindungi dan hanya digunakan oleh petugas untuk keperluan verifikasi dan koordinasi penanganan.',
                        'sort_order' => 1,
                    ],
                ],
            ],
        ];

        foreach ($pages as $pageData) {
            $forms = $pageData['forms'];
            $faqs = $pageData['faqs'];
            unset($pageData['forms'], $pageData['faqs']);

            /** @var InformationPage $page */
            $page = InformationPage::updateOrCreate(
                ['slug' => $pageData['slug']],
                $pageData
            );

            foreach ($forms as $formData) {
                DownloadableForm::updateOrCreate(
                    [
                        'information_page_id' => $page->id,
                        'name' => $formData['name'],
                    ],
                    [
                        'file_path' => $formData['file_path'],
                        'version' => $formData['version'],
                        'is_current' => true,
                    ]
                );
            }

            foreach ($faqs as $faqData) {
                Faq::updateOrCreate(
                    [
                        'information_page_id' => $page->id,
                        'question' => $faqData['question'],
                    ],
                    [
                        'answer' => $faqData['answer'],
                        'sort_order' => $faqData['sort_order'],
                        'is_active' => true,
                    ]
                );
            }

            // Sample Page Visits for the past 7 days
            for ($i = 0; $i < 7; $i++) {
                PageVisit::firstOrCreate(
                    [
                        'information_page_id' => $page->id,
                        'visit_date' => Carbon::today()->subDays($i)->toDateString(),
                    ],
                    [
                        'visit_count' => rand(15, 80),
                    ]
                );
            }
        }

        // Sample general FAQs (not linked to a specific information page)
        $generalFaqs = [
            [
                'question' => 'Apa itu aplikasi SAPA SOSIAL?',
                'answer' => 'SAPA SOSIAL adalah portal Satu Pintu Layanan Sosial Kabupaten Blitar yang mengintegrasikan pengajuan layanan SK DTSEN, reaktivasi KIS PBI-JK, rehabilitasi sosial, dan pengaduan sosial secara online dan transparan.',
                'sort_order' => 1,
            ],
            [
                'question' => 'Bagaimana cara mengecek status pengajuan atau pengaduan saya?',
                'answer' => 'Anda dapat membuka halaman Cek Tiket di portal publik dengan memasukkan nomor tiket (misal: DTSEN-202609-00001) dan 4 digit terakhir NIK atau Nomor HP pemohon.',
                'sort_order' => 2,
            ],
            [
                'question' => 'Apakah pemohon yang tidak memiliki HP/internet bisa mengajukan layanan?',
                'answer' => 'Bisa. Pemohon dapat mendatangi kantor desa/kelurahan atau kantor kecamatan setempat. Petugas operator desa/kecamatan akan membantu mendaftarkan dan memproses pengajuan melalui sistem SAPA SOSIAL.',
                'sort_order' => 3,
            ],
        ];

        foreach ($generalFaqs as $gfaq) {
            Faq::updateOrCreate(
                [
                    'information_page_id' => null,
                    'question' => $gfaq['question'],
                ],
                [
                    'answer' => $gfaq['answer'],
                    'sort_order' => $gfaq['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        // Sample search logs
        $searchTerms = [
            ['keyword' => 'DTSEN', 'result_count' => 12],
            ['keyword' => 'KIS PBI', 'result_count' => 8],
            ['keyword' => 'Syarat SPMB afirmasi', 'result_count' => 15],
            ['keyword' => 'Lapor ODGJ terlantar', 'result_count' => 5],
            ['keyword' => 'Bantuan kursi roda', 'result_count' => 9],
            ['keyword' => 'Cek desil bansos', 'result_count' => 18],
        ];

        foreach ($searchTerms as $term) {
            SearchLog::create([
                'keyword' => $term['keyword'],
                'result_count' => $term['result_count'],
                'searched_at' => Carbon::now()->subHours(rand(1, 48)),
            ]);
        }
    }
}
