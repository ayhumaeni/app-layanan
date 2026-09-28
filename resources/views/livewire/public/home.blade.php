<div class="flex flex-col w-full pb-16">
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-b from-surface-container-low via-surface to-surface pt-10 pb-12 px-4 sm:px-6 lg:px-8 border-b border-surface-container-high/40">
        <div class="max-w-6xl mx-auto flex flex-col gap-8">
            <!-- Header Headline & Badges -->
            <div class="flex flex-col gap-4 max-w-3xl">
                <div class="inline-flex items-center gap-2 self-start px-3.5 py-1.5 rounded-full bg-surface-container-high text-primary font-bold text-xs tracking-wide shadow-xs">
                    <span class="material-symbols-outlined text-[16px] text-primary" style="font-variation-settings: 'FILL' 1;">verified</span>
                    <span>Portal Resmi Pemerintah Kabupaten Blitar</span>
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-primary tracking-tight leading-tight">
                    Satu Pintu Layanan Sosial Kabupaten Blitar
                </h1>
                <p class="text-base sm:text-lg text-on-surface-variant leading-relaxed">
                    Ajukan layanan sosial, sampaikan laporan pengaduan, dan pantau proses verifikasi secara transparan dengan nomor tiket resmi Anda.
                </p>
            </div>

            <!-- Two Main Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-1 max-w-xl">
                <a href="{{ route('services.index') }}" class="min-h-[52px] px-6 rounded-xl bg-primary text-white font-bold text-base shadow-md hover:bg-primary-container transition-all flex items-center justify-center gap-2.5 active:scale-[0.99]">
                    <span class="material-symbols-outlined text-[22px]">assignment_add</span>
                    <span>Ajukan Layanan Online</span>
                </a>
                <a href="{{ route('complaints.create') }}" class="min-h-[52px] px-6 rounded-xl bg-secondary-container text-on-secondary-container font-bold text-base shadow-md hover:brightness-95 transition-all flex items-center justify-center gap-2.5 active:scale-[0.99]">
                    <span class="material-symbols-outlined text-[22px]">record_voice_over</span>
                    <span>Sampaikan Pengaduan</span>
                </a>
            </div>

            <!-- Interactive Quick Ticket Tracking Box -->
            <div class="w-full bg-surface-container-lowest rounded-2xl p-5 sm:p-6 shadow-sm border border-surface-container-high">
                <div class="flex items-center gap-3 pb-3 border-b border-surface-container">
                    <div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center text-primary shrink-0">
                        <span class="material-symbols-outlined text-[22px]">manage_search</span>
                    </div>
                    <div class="flex flex-col">
                        <h2 class="text-base sm:text-lg font-bold text-on-surface">Sudah punya nomor tiket pengajuan?</h2>
                        <span class="text-xs sm:text-sm text-on-surface-variant">Lacak perkembangan berkas atau unduh surat resmi Anda di sini</span>
                    </div>
                </div>

                <form wire:submit="trackQuickTicket" class="flex flex-col gap-3 pt-4">
                    <div class="flex flex-col sm:flex-row gap-2">
                        <div class="relative flex-1">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-[20px]">tag</span>
                            <input wire:model="quickTicketNumber" 
                                   type="text" 
                                   placeholder="Contoh: DTSEN-202610-00012 atau PBI-... atau ADU-..." 
                                   class="w-full h-[52px] pl-11 pr-4 bg-surface-container-low rounded-xl text-on-surface font-semibold text-sm placeholder:text-outline/70 focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary uppercase transition-all" />
                        </div>
                        <button type="submit" 
                                class="h-[52px] px-6 rounded-xl bg-primary text-white font-bold text-sm shadow-sm hover:bg-primary-container transition-all flex items-center justify-center gap-2 shrink-0 cursor-pointer active:scale-95">
                            <span class="material-symbols-outlined text-[20px]" wire:loading.remove wire:target="trackQuickTicket">search</span>
                            <span class="animate-spin material-symbols-outlined text-[20px]" wire:loading wire:target="trackQuickTicket">sync</span>
                            <span>Lacak Status</span>
                        </button>
                    </div>

                    <!-- Error Alert -->
                    @if ($quickTrackError)
                        <div class="p-3.5 rounded-xl bg-error-container/40 text-on-error-container border border-error/20 flex items-start gap-2.5 text-xs sm:text-sm animate-fade-in">
                            <span class="material-symbols-outlined text-error text-[20px] shrink-0 mt-0.5">error</span>
                            <div class="flex-1">
                                <p class="font-medium">{{ $quickTrackError }}</p>
                                <a href="{{ route('tracking') }}" class="font-bold text-primary underline mt-1 inline-block">Buka Halaman Pelacakan Lengkap &rarr;</a>
                            </div>
                        </div>
                    @endif

                    <!-- Success / Found Alert -->
                    @if ($quickTrackResult)
                        <div class="p-4 rounded-xl bg-surface-container-high/60 border border-primary/20 flex flex-col sm:flex-row sm:items-center justify-between gap-3 animate-fade-in">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[20px]">verified</span>
                                </div>
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-mono font-bold text-primary text-sm sm:text-base">{{ $quickTrackResult['number'] }}</span>
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary text-white">
                                            {{ $quickTrackResult['status_label'] }}
                                        </span>
                                    </div>
                                    <span class="text-xs font-semibold text-on-surface mt-0.5">{{ $quickTrackResult['service'] }} • Pemohon: {{ $quickTrackResult['applicant'] }}</span>
                                    <span class="text-xs text-on-surface-variant mt-0.5">Diajukan: {{ $quickTrackResult['submitted_at'] }}</span>
                                </div>
                            </div>
                            <a href="{{ route('tracking') }}?tiket={{ urlencode($quickTrackResult['number']) }}" class="self-start sm:self-auto px-4 py-2 rounded-lg bg-primary text-white text-xs font-bold hover:bg-primary-container shadow-xs transition-colors flex items-center gap-1">
                                <span>Lihat Detail Lengkap</span>
                                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                            </a>
                        </div>
                    @endif

                    <div class="flex items-center justify-between text-xs text-outline px-1">
                        <span>* Nomor tiket otomatis diterbitkan sistem saat permohonan atau pengaduan dikirim.</span>
                        <a href="{{ route('tracking') }}" class="text-primary font-bold hover:underline">Pusat Pelacakan &rarr;</a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Global Search Section -->
    <section class="max-w-6xl mx-auto w-full px-4 sm:px-6 lg:px-8 -mt-5 relative z-10">
        <form wire:submit="searchServices" class="w-full bg-surface-container-lowest rounded-2xl p-2.5 shadow-md border border-surface-container-high flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[24px] pl-3 shrink-0">search</span>
            <input wire:model="searchKeyword" 
                   type="search" 
                   placeholder="Cari jenis layanan bantuan sosial, formulir unduhan, atau panduan PPKS..." 
                   class="w-full h-11 bg-transparent text-on-surface text-sm sm:text-base placeholder:text-outline focus:outline-none" />
            <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-container transition-colors shrink-0">
                Cari
            </button>
        </form>
    </section>

    <!-- Layanan Prioritas Section -->
    <section class="max-w-6xl mx-auto w-full px-4 sm:px-6 lg:px-8 mt-12 flex flex-col gap-6">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-2">
            <div>
                <span class="text-xs font-bold text-secondary uppercase tracking-wider">Prioritas Warga Blitar</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-primary tracking-tight">3 Layanan Sosial Unggulan</h2>
                <p class="text-sm text-on-surface-variant mt-1">Layanan terpadu yang paling sering diakses masyarakat Kabupaten Blitar dengan alur cepat</p>
            </div>
            <a href="{{ route('services.index') }}" class="text-xs sm:text-sm font-bold text-primary hover:text-primary-container flex items-center gap-1 group">
                <span>Lihat Semua Layanan</span>
                <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1: SK DTSEN -->
            <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm hover:shadow-md border border-surface-container-high transition-all flex flex-col justify-between gap-5 relative overflow-hidden group">
                <div class="flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-secondary-fixed text-on-secondary-fixed inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px] text-secondary">star</span>
                            <span>Paling Banyak Diakses</span>
                        </span>
                        <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0 group-hover:bg-primary group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[26px]">description</span>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold text-on-surface group-hover:text-primary transition-colors">
                            Surat Keterangan DTSEN
                        </h3>
                        <p class="text-xs text-on-surface-variant mt-1.5 leading-relaxed">
                            Penerbitan surat status Data Tunggal Sosial Ekonomi Nasional (DTSEN) & peringkat desil untuk pendaftaran afirmasi SPMB, PIP, KIP Kuliah, bansos, dan Jamkesda.
                        </p>
                    </div>

                    <div class="p-3 rounded-xl bg-surface-container-low text-xs text-on-surface-variant flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[18px]">fact_check</span>
                        <span>Syarat: <strong>KTP & Kartu Keluarga (KK)</strong></span>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2 border-t border-surface-container">
                    <a href="{{ route('services.dtsen.apply') }}" class="flex-1 min-h-[46px] rounded-xl bg-primary text-white font-bold text-xs flex items-center justify-center gap-1.5 hover:bg-primary-container shadow-xs transition-colors">
                        <span class="material-symbols-outlined text-[18px]">add_circle</span>
                        <span>Ajukan Surat</span>
                    </a>
                    <a href="{{ route('services.show', 'surat-keterangan-dtsen') }}" class="px-3 min-h-[46px] rounded-xl bg-surface-container text-on-surface hover:bg-surface-container-high font-semibold text-xs flex items-center justify-center transition-colors" title="Informasi Lengkap">
                        <span class="material-symbols-outlined text-[18px]">info</span>
                    </a>
                </div>
            </div>

            <!-- Card 2: Reaktivasi KIS / PBI-JK -->
            <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm hover:shadow-md border border-surface-container-high transition-all flex flex-col justify-between gap-5 relative overflow-hidden group">
                <div class="flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-error-container/60 text-on-error-container inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px] text-error">emergency</span>
                            <span>Prioritas Medis 24 Jam</span>
                        </span>
                        <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0 group-hover:bg-primary group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[26px]">medical_services</span>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold text-on-surface group-hover:text-primary transition-colors">
                            Reaktivasi KIS / PBI-JK
                        </h3>
                        <p class="text-xs text-on-surface-variant mt-1.5 leading-relaxed">
                            Fasilitasi pengaktifan kembali kepesertaan JKN-KIS PBI yang nonaktif untuk warga berpenyakit kronis, darurat rawat inap RS, atau bayi baru lahir dari ibu peserta PBI.
                        </p>
                    </div>

                    <div class="p-3 rounded-xl bg-surface-container-low text-xs text-on-surface-variant flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[18px]">fact_check</span>
                        <span>Syarat: <strong>KTP, KK, Kartu KIS & Ket. Faskes</strong></span>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2 border-t border-surface-container">
                    <a href="{{ route('services.kis.apply') }}" class="flex-1 min-h-[46px] rounded-xl bg-secondary-container text-on-secondary-container font-bold text-xs flex items-center justify-center gap-1.5 hover:brightness-95 shadow-xs transition-colors">
                        <span class="material-symbols-outlined text-[18px]">health_and_safety</span>
                        <span>Ajukan Reaktivasi</span>
                    </a>
                    <a href="{{ route('services.show', 'reaktivasi-kis-pbi-jk') }}" class="px-3 min-h-[46px] rounded-xl bg-surface-container text-on-surface hover:bg-surface-container-high font-semibold text-xs flex items-center justify-center transition-colors" title="Informasi Lengkap">
                        <span class="material-symbols-outlined text-[18px]">info</span>
                    </a>
                </div>
            </div>

            <!-- Card 3: Rehabilitasi Sosial -->
            <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm hover:shadow-md border border-surface-container-high transition-all flex flex-col justify-between gap-5 relative overflow-hidden group">
                <div class="flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-tertiary-fixed text-on-tertiary-fixed inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px] text-tertiary">healing</span>
                            <span>Pendampingan Terpadu</span>
                        </span>
                        <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0 group-hover:bg-primary group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[26px]">accessible</span>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold text-on-surface group-hover:text-primary transition-colors">
                            Pelayanan Rehabilitasi Sosial
                        </h3>
                        <p class="text-xs text-on-surface-variant mt-1.5 leading-relaxed">
                            Penanganan terpadu untuk lansia terlantar, penyandang disabilitas, ODGJ terlantar, anak terlantar/korban kekerasan, termasuk assessment dan rujukan panti.
                        </p>
                    </div>

                    <div class="p-3 rounded-xl bg-surface-container-low text-xs text-on-surface-variant flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[18px]">fact_check</span>
                        <span>Alur: <strong>Laporan / Kasus &rarr; Assessment</strong></span>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2 border-t border-surface-container">
                    <a href="{{ route('complaints.create') }}" class="flex-1 min-h-[46px] rounded-xl bg-primary text-white font-bold text-xs flex items-center justify-center gap-1.5 hover:bg-primary-container shadow-xs transition-colors">
                        <span class="material-symbols-outlined text-[18px]">record_voice_over</span>
                        <span>Laporkan Kasus</span>
                    </a>
                    <a href="{{ route('services.show', 'alur-pelayanan-rehabilitasi-sosial') }}" class="px-3 min-h-[46px] rounded-xl bg-surface-container text-on-surface hover:bg-surface-container-high font-semibold text-xs flex items-center justify-center transition-colors" title="Informasi Lengkap">
                        <span class="material-symbols-outlined text-[18px]">info</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Secondary Quick Actions Row -->
    <section class="max-w-6xl mx-auto w-full px-4 sm:px-6 lg:px-8 mt-12">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('services.index') }}" class="p-4 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface transition-all flex flex-col items-center text-center gap-2 border border-outline-variant/30 group">
                <div class="w-12 h-12 rounded-xl bg-white text-primary flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[24px]">category</span>
                </div>
                <span class="font-bold text-xs sm:text-sm text-primary">Layanan Sosial Lainnya</span>
                <span class="text-[11px] text-on-surface-variant line-clamp-2">Katalog lengkap bansos & rekomendasi</span>
            </a>

            <a href="{{ route('complaints.create') }}" class="p-4 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface transition-all flex flex-col items-center text-center gap-2 border border-outline-variant/30 group">
                <div class="w-12 h-12 rounded-xl bg-white text-secondary flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[24px]">campaign</span>
                </div>
                <span class="font-bold text-xs sm:text-sm text-primary">Pengaduan Sosial</span>
                <span class="text-[11px] text-on-surface-variant line-clamp-2">Lapor PPKS & masalah bansos wilayah</span>
            </a>

            <a href="{{ route('certificate.verify') }}" class="p-4 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface transition-all flex flex-col items-center text-center gap-2 border border-outline-variant/30 group">
                <div class="w-12 h-12 rounded-xl bg-white text-tertiary flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[24px]">qr_code_scanner</span>
                </div>
                <span class="font-bold text-xs sm:text-sm text-primary">Verifikasi Keaslian Surat</span>
                <span class="text-[11px] text-on-surface-variant line-clamp-2">Cek barcode/QR SK DTSEN resmi</span>
            </a>

            <a href="{{ route('services.index') }}#formulir-unduhan" class="p-4 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface transition-all flex flex-col items-center text-center gap-2 border border-outline-variant/30 group">
                <div class="w-12 h-12 rounded-xl bg-white text-primary flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[24px]">download</span>
                </div>
                <span class="font-bold text-xs sm:text-sm text-primary">Formulir Unduhan</span>
                <span class="text-[11px] text-on-surface-variant line-clamp-2">Unduh template berkas & panduan PDF</span>
            </a>
        </div>
    </section>

    <!-- Cara Kerja Satu Pintu Stepper -->
    <section class="max-w-6xl mx-auto w-full px-4 sm:px-6 lg:px-8 mt-16">
        <div class="bg-surface-container-lowest rounded-2xl p-6 sm:p-10 border border-surface-container-high shadow-xs">
            <div class="text-center max-w-xl mx-auto mb-10">
                <span class="text-xs font-bold text-primary uppercase tracking-wider">Mudah & Transparan</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-on-surface tracking-tight mt-1">4 Langkah Mudah Pengajuan</h2>
                <p class="text-xs sm:text-sm text-on-surface-variant mt-1.5">Tidak perlu bolak-balik kantor dinas. Seluruh tahapan dapat Anda pantau langsung.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 relative">
                <!-- Step 1 -->
                <div class="flex flex-col items-center text-center gap-3 relative">
                    <div class="w-14 h-14 rounded-2xl bg-primary text-white flex items-center justify-center font-extrabold text-lg shadow-md shadow-primary/20">
                        1
                    </div>
                    <h3 class="font-bold text-base text-on-surface">Pilih Layanan</h3>
                    <p class="text-xs text-on-surface-variant leading-relaxed">
                        Pilih jenis layanan yang dibutuhkan seperti SK DTSEN, KIS PBI-JK, atau pengaduan.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="flex flex-col items-center text-center gap-3 relative">
                    <div class="w-14 h-14 rounded-2xl bg-primary text-white flex items-center justify-center font-extrabold text-lg shadow-md shadow-primary/20">
                        2
                    </div>
                    <h3 class="font-bold text-base text-on-surface">Isi Data & Dokumen</h3>
                    <p class="text-xs text-on-surface-variant leading-relaxed">
                        Lengkapi identitas NIK, KK, dan unggah foto/dokumen persyaratan melalui gawai Anda.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="flex flex-col items-center text-center gap-3 relative">
                    <div class="w-14 h-14 rounded-2xl bg-secondary-container text-on-secondary-container flex items-center justify-center font-extrabold text-lg shadow-md shadow-secondary/20">
                        3
                    </div>
                    <h3 class="font-bold text-base text-on-surface">Dapatkan Nomor Tiket</h3>
                    <p class="text-xs text-on-surface-variant leading-relaxed">
                        Sistem menerbitkan nomor tiket unik resmi sebagai tanda terima pengajuan Anda.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="flex flex-col items-center text-center gap-3 relative">
                    <div class="w-14 h-14 rounded-2xl bg-tertiary-container text-white flex items-center justify-center font-extrabold text-lg shadow-md shadow-tertiary/20">
                        4
                    </div>
                    <h3 class="font-bold text-base text-on-surface">Pantau Sampai Selesai</h3>
                    <p class="text-xs text-on-surface-variant leading-relaxed">
                        Pantau riwayat verifikasi petugas hingga surat resmi berbarcode siap diunduh.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Accordion Section -->
    <section class="max-w-4xl mx-auto w-full px-4 sm:px-6 lg:px-8 mt-16 flex flex-col gap-6" x-data="{ activeFaq: 1 }">
        <div class="text-center max-w-xl mx-auto">
            <span class="text-xs font-bold text-secondary uppercase tracking-wider">Tanya Jawab</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-primary tracking-tight mt-1">Pertanyaan yang Sering Diajukan</h2>
            <p class="text-xs sm:text-sm text-on-surface-variant mt-1">Jawaban cepat seputar persyaratan DTSEN, KIS, dan pengaduan sosial</p>
        </div>

        <div class="flex flex-col gap-3">
            @forelse ($faqs as $faq)
                <div class="bg-surface-container-lowest rounded-xl border border-surface-container-high overflow-hidden shadow-xs">
                    <button @click="activeFaq = (activeFaq === {{ $faq->id }} ? null : {{ $faq->id }})" 
                            type="button" 
                            class="w-full px-5 py-4 flex items-center justify-between text-left gap-4 hover:bg-surface-container-low transition-colors">
                        <span class="font-bold text-sm sm:text-base text-on-surface">{{ $faq->question }}</span>
                        <span class="material-symbols-outlined text-[20px] text-primary shrink-0 transition-transform duration-200" 
                              :class="{ 'rotate-180': activeFaq === {{ $faq->id }} }">
                            expand_more
                        </span>
                    </button>
                    <div x-show="activeFaq === {{ $faq->id }}" 
                         x-transition:enter="transition ease-out duration-150" 
                         x-transition:enter-start="opacity-0 -translate-y-2" 
                         x-transition:enter-end="opacity-100 translate-y-0" 
                         class="px-5 pb-5 pt-1 text-xs sm:text-sm text-on-surface-variant leading-relaxed border-t border-surface-container/60" 
                         style="display: none;">
                        {{ $faq->answer }}
                    </div>
                </div>
            @empty
                <div class="p-6 rounded-xl bg-surface-container text-center text-sm text-on-surface-variant">
                    Belum ada data tanya jawab.
                </div>
            @endforelse
        </div>
    </section>

    <!-- Puskesos & Assistance Banner -->
    <section class="max-w-6xl mx-auto w-full px-4 sm:px-6 lg:px-8 mt-16">
        <div class="bg-gradient-to-r from-primary-container to-primary text-white rounded-2xl p-6 sm:p-10 shadow-lg relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-start gap-4 relative z-10 max-w-2xl">
                <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center shrink-0 border border-white/20">
                    <span class="material-symbols-outlined text-[32px] text-white">support_agent</span>
                </div>
                <div class="flex flex-col gap-1.5">
                    <span class="text-xs font-bold text-secondary-fixed tracking-wider uppercase">Pendampingan Pengajuan</span>
                    <h3 class="text-xl sm:text-2xl font-bold leading-snug">
                        Terkendala Gawai atau Kuota Internet?
                    </h3>
                    <p class="text-xs sm:text-sm text-white/80 leading-relaxed">
                        Warga Kabupaten Blitar dapat mendatangi kantor Balai Desa / Kelurahan atau Kantor Kecamatan setempat. Petugas Puskesos (Pusat Kesejahteraan Sosial) siap membantu penginputan berkas Anda secara gratis.
                    </p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3 shrink-0 relative z-10 w-full sm:w-auto">
                <a href="https://wa.me/6281234567890" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto min-h-[48px] px-6 rounded-xl bg-secondary-container text-on-secondary-container font-bold text-sm shadow-md hover:brightness-95 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">chat</span>
                    <span>Hubungi WhatsApp</span>
                </a>
                <a href="{{ route('services.index') }}" class="w-full sm:w-auto min-h-[48px] px-6 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-sm border border-white/20 transition-all flex items-center justify-center gap-1.5">
                    <span>Panduan Wilayah</span>
                </a>
            </div>

            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        </div>
    </section>
</div>
