<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col gap-6" x-data="{ copied: false }">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs font-medium text-on-surface-variant">
        <a href="{{ route('home') }}" class="hover:text-primary transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-[15px]">home</span>
            <span>Beranda</span>
        </a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-primary font-bold">Cek Status Tiket</span>
    </nav>

    <!-- Header Title -->
    <div class="flex flex-col gap-1">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-surface-container-high flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-[20px]">timeline</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-primary tracking-tight">
                Lacak Status Pengajuan & Tiket
            </h1>
        </div>
        <p class="text-xs sm:text-sm text-on-surface-variant">
            Pantau perkembangan permohonan surat keterangan, jaminan kesehatan, dan pengaduan sosial Anda secara transparan.
        </p>
    </div>

    <!-- Tracking Form -->
    <div class="bg-surface-container-lowest rounded-2xl p-6 sm:p-8 border border-surface-container-high shadow-xs flex flex-col gap-5">
        <div class="flex items-center justify-between pb-2 border-b border-surface-container">
            <div class="flex items-center gap-2">
                <div class="w-2 h-4 rounded-full bg-secondary"></div>
                <h2 class="text-sm font-bold text-on-surface">Pencarian Cepat Berkas</h2>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-surface-container text-primary text-xs font-medium">
                <span class="material-symbols-outlined text-[14px]">verified_user</span>
                <span>Aman & Terenkripsi</span>
            </span>
        </div>

        <form wire:submit="trackTicket" class="flex flex-col gap-4">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                <div class="sm:col-span-8 flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-on-surface flex items-center gap-1" for="ticketInput">
                        <span>Nomor Tiket Pengajuan</span>
                        <span class="text-error">*</span>
                    </label>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined absolute left-3.5 text-outline text-[20px]">tag</span>
                        <input wire:model="ticket_number" 
                               type="text" 
                               id="ticketInput"
                               placeholder="Contoh: PBI-202610-00007 atau DTSEN-..." 
                               class="w-full h-12 pl-11 pr-4 rounded-xl bg-surface-container-low text-on-surface text-sm font-mono uppercase tracking-wider focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary border border-surface-container transition-all" />
                    </div>
                    <span class="text-[11px] text-on-surface-variant">Tertera pada bukti tanda terima permohonan atau SMS/WA resmi.</span>
                </div>

                <div class="sm:col-span-4 flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-on-surface flex items-center gap-1" for="securityDigits">
                        <span>4 Digit Terakhir NIK / HP</span>
                        <span class="text-on-surface-variant font-normal">(Opsional)</span>
                    </label>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined absolute left-3.5 text-outline text-[20px]">fingerprint</span>
                        <input wire:model="security_digits" 
                               type="text" 
                               id="securityDigits"
                               maxlength="4"
                               placeholder="Contoh: 1234" 
                               class="w-full h-12 pl-11 pr-4 rounded-xl bg-surface-container-low text-on-surface text-sm font-mono tracking-widest focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary border border-surface-container transition-all" />
                    </div>
                    <span class="text-[11px] text-on-surface-variant">Verifikasi keamanan data pribadi.</span>
                </div>
            </div>

            <button type="submit" 
                    wire:loading.attr="disabled"
                    class="w-full min-h-[48px] bg-primary hover:bg-primary-container text-white rounded-xl font-bold text-sm flex items-center justify-center gap-2 shadow-sm transition-all cursor-pointer active:scale-95">
                <span class="material-symbols-outlined text-[20px]" wire:loading.remove wire:target="trackTicket">search</span>
                <span class="animate-spin material-symbols-outlined text-[20px]" wire:loading wire:target="trackTicket">sync</span>
                <span>Lacak Status Tiket</span>
            </button>
        </form>

        @if ($errorMessage)
            <div class="p-4 rounded-xl bg-error-container/40 text-on-error-container border border-error/20 flex items-start gap-3 text-xs sm:text-sm animate-fade-in">
                <span class="material-symbols-outlined text-error text-[20px] shrink-0 mt-0.5">error</span>
                <div class="flex-1">
                    <strong class="font-bold block mb-0.5">Tiket Belum Ditemukan</strong>
                    <p class="leading-relaxed">{{ $errorMessage }}</p>
                    <p class="text-xs text-on-error-container/80 mt-1">
                        Tips: Periksa kembali ejaan tanda strip (-), atau hubungi petugas kami jika Anda mendaftar melalui operator desa.
                    </p>
                </div>
            </div>
        @endif
    </div>

    <!-- TRACKING RESULT CARD -->
    @if ($result)
        <div class="bg-surface-container-lowest rounded-2xl border border-surface-container-high shadow-md overflow-hidden flex flex-col animate-fade-in">
            <!-- Ribbon Header -->
            <div class="bg-surface-container p-5 sm:p-6 flex flex-col gap-2 border-b border-surface-container-high">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-primary uppercase tracking-wider">Hasil Pelacakan Berkas</span>
                    <span class="inline-flex items-center gap-1.5 text-xs text-on-surface-variant bg-surface-container-lowest px-2.5 py-1 rounded-full font-medium">
                        <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                        <span>Terhubung Database Dinsos</span>
                    </span>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mt-1">
                    <div class="flex items-center gap-2">
                        <span class="text-xl sm:text-2xl font-extrabold font-mono text-on-surface tracking-wide">{{ $result['number'] }}</span>
                        <button @click="navigator.clipboard.writeText('{{ $result['number'] }}'); copied = true; setTimeout(() => copied = false, 2500)" 
                                type="button" 
                                class="p-1.5 rounded-lg bg-surface-container-high hover:bg-surface-container-lowest text-primary transition-colors cursor-pointer" 
                                title="Salin Kode Tiket">
                            <span class="material-symbols-outlined text-[18px]" x-show="!copied">content_copy</span>
                            <span class="material-symbols-outlined text-[18px] text-tertiary" x-show="copied" style="display: none;">check</span>
                        </button>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-primary text-white self-start sm:self-auto">
                        {{ $result['status_label'] }}
                    </span>
                </div>
            </div>

            <!-- Overview Content -->
            <div class="p-6 sm:p-8 flex flex-col gap-6">
                <!-- Service Name & Emergency Tag -->
                <div class="flex flex-col gap-2">
                    <span class="text-xs text-on-surface-variant uppercase tracking-wider font-semibold">Jenis Layanan / Permohonan</span>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <h2 class="text-lg sm:text-xl font-bold text-on-surface">{{ $result['service_name'] }}</h2>
                        @if ($result['is_priority'])
                            <span class="inline-flex items-center gap-1 bg-error-container text-on-error-container px-2.5 py-0.5 rounded-full text-xs font-bold self-start sm:self-auto">
                                <span class="material-symbols-outlined text-[15px]">emergency</span>
                                <span>Prioritas Darurat Medis 24 Jam</span>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Meta Details Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-surface-container-low p-4 rounded-xl text-xs">
                    <div><span class="text-on-surface-variant">Nama Pemohon/Pelapor:</span> <strong class="text-on-surface block text-sm">{{ $result['applicant_name'] }}</strong></div>
                    <div><span class="text-on-surface-variant">Tanggal Pengajuan:</span> <span class="text-on-surface font-semibold block text-sm">{{ $result['submitted_at'] }}</span></div>
                    <div><span class="text-on-surface-variant">NIK (Tersamar):</span> <span class="font-mono text-on-surface block">{{ $result['nik_masked'] }}</span></div>
                    <div><span class="text-on-surface-variant">Kontak Terdaftar:</span> <span class="text-on-surface block">{{ $result['phone_masked'] }}</span></div>
                    <div class="col-span-full"><span class="text-on-surface-variant">Alamat:</span> <span class="text-on-surface block">{{ $result['address'] }}</span></div>
                </div>

                <!-- Specific Status Alerts (Revision / Rejected / Certificate Issued) -->
                @if ($result['status'] instanceof \App\Enums\ServiceRequestStatus && $result['status'] === \App\Enums\ServiceRequestStatus::RevisionRequested)
                    <div class="p-4 rounded-xl bg-secondary-container/20 border border-secondary text-xs sm:text-sm flex flex-col gap-2">
                        <div class="flex items-center gap-2 text-secondary font-bold">
                            <span class="material-symbols-outlined text-[20px]">warning</span>
                            <span>Perlu Perbaikan Dokumen / Data</span>
                        </div>
                        <p class="text-on-surface leading-relaxed">
                            Catatan Petugas: <strong>{{ $result['officer_notes'] ?? 'Dokumen KTP atau KK buram/tidak terbaca. Silakan hubungi petugas desa.' }}</strong>
                        </p>
                    </div>
                @elseif ($result['status'] instanceof \App\Enums\ServiceRequestStatus && $result['status'] === \App\Enums\ServiceRequestStatus::Rejected)
                    <div class="p-4 rounded-xl bg-error-container/40 border border-error text-xs sm:text-sm flex flex-col gap-2">
                        <div class="flex items-center gap-2 text-error font-bold">
                            <span class="material-symbols-outlined text-[20px]">cancel</span>
                            <span>Permohonan Ditolak</span>
                        </div>
                        <p class="text-on-surface leading-relaxed">
                            Alasan: <strong>{{ $result['rejection_reason'] ?? 'Data pemohon tidak terdaftar dalam basis data SIKS-NG/DTSEN atau desil di luar ketentuan batas maksimal.' }}</strong>
                        </p>
                        <span class="text-xs text-on-surface-variant">Untuk perbaikan data desil, silakan melakukan musyawarah desa (Musdes) di kantor desa setempat.</span>
                    </div>
                @elseif ($result['certificate'] && $result['certificate']->verification_code)
                    <div class="p-4 rounded-xl bg-tertiary-container/15 border border-tertiary text-xs sm:text-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-tertiary text-[32px]">verified</span>
                            <div class="flex flex-col">
                                <strong class="text-tertiary font-bold text-sm">Surat Keterangan Resmi Telah Diterbitkan</strong>
                                <span class="text-xs text-on-surface-variant">Nomor Surat: {{ $result['certificate']->certificate_number ?? '-' }}</span>
                            </div>
                        </div>
                        <a href="{{ route('certificate.verify', $result['certificate']->verification_code) }}" class="px-4 py-2 rounded-xl bg-tertiary text-white font-bold text-xs hover:bg-tertiary-container shadow-xs transition-colors flex items-center gap-1.5 self-start sm:self-auto">
                            <span class="material-symbols-outlined text-[16px]">qr_code</span>
                            <span>Verifikasi Keaslian Surat</span>
                        </a>
                    </div>
                @endif

                <!-- Timeline of Process Stages -->
                <div class="flex flex-col gap-4 pt-4 border-t border-surface-container">
                    <h3 class="text-sm font-bold text-primary flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px]">history</span>
                        <span>Riwayat Perjalanan Berkas & Catatan Petugas</span>
                    </h3>

                    <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-surface-container-high">
                        @forelse ($result['histories'] as $history)
                            <div class="relative flex items-start gap-3">
                                <div class="absolute -left-6 top-1 w-4 h-4 rounded-full bg-primary border-4 border-white shadow-xs"></div>
                                <div class="flex flex-col gap-0.5">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-bold text-on-surface uppercase">{{ $history->to_status }}</span>
                                        <span class="text-[11px] text-on-surface-variant">{{ $history->created_at->translatedFormat('d F Y, H:i') }} WIB</span>
                                    </div>
                                    @if ($history->notes)
                                        <p class="text-xs text-on-surface-variant leading-relaxed mt-0.5 bg-surface-container-low p-2.5 rounded-lg border border-surface-container">
                                            {{ $history->notes }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="relative flex items-start gap-3">
                                <div class="absolute -left-6 top-1 w-4 h-4 rounded-full bg-primary border-4 border-white shadow-xs"></div>
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-on-surface">Berkas Diajukan</span>
                                    <span class="text-[11px] text-on-surface-variant">{{ $result['submitted_at'] }}</span>
                                    <p class="text-xs text-on-surface-variant mt-1">Berkas pendaftaran berhasil masuk dan menunggu giliran pemeriksaan petugas verifikator.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
