<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col gap-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs font-medium text-on-surface-variant">
        <a href="{{ route('home') }}" class="hover:text-primary transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-[15px]">home</span>
            <span>Beranda</span>
        </a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-primary font-bold">Verifikasi Dokumen Surat</span>
    </nav>

    <!-- Header Title -->
    <div class="flex flex-col gap-2">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container text-primary text-xs font-bold w-fit">
            <span class="material-symbols-outlined text-[16px]">verified_user</span>
            <span>Layanan Terbuka Mandiri</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-primary tracking-tight">
            Verifikasi Keaslian Surat Keterangan DTSEN
        </h1>
        <p class="text-xs sm:text-sm text-on-surface-variant leading-relaxed">
            Layanan publik mandiri tanpa login untuk memeriksa keabsahan surat resmi bertanda Barcode / Tanda Tangan Elektronik yang diterbitkan oleh Dinas Sosial Kabupaten Blitar.
        </p>
    </div>

    <!-- Verification Input Form -->
    <div class="bg-surface-container-lowest rounded-2xl p-6 sm:p-8 border border-surface-container-high shadow-xs flex flex-col gap-5">
        <div class="flex items-start gap-3 bg-surface-container-high/60 p-3.5 rounded-xl text-primary text-xs sm:text-sm">
            <span class="material-symbols-outlined text-[20px] shrink-0 mt-0.5">info</span>
            <p class="leading-relaxed">
                Pindai QR Code pada lembar surat cetak/PDF Anda dengan kamera ponsel, atau ketikkan kode verifikasi (contoh: <strong>VRF-DTSEN-202609-001</strong>) pada kotak di bawah.
            </p>
        </div>

        <form wire:submit="verify" class="flex flex-col gap-3">
            <div class="flex flex-col gap-1.5">
                <label for="verificationCode" class="text-xs font-bold text-on-surface flex items-center justify-between">
                    <span>Kode Verifikasi Surat</span>
                    <button type="button" wire:click="$set('code', 'VRF-DTSEN-202609-001')" class="text-xs text-primary font-bold hover:underline cursor-pointer">
                        Contoh: VRF-DTSEN-202609-001
                    </button>
                </label>
                <div class="relative flex items-center">
                    <input wire:model="code" 
                           type="text" 
                           id="verificationCode"
                           placeholder="Contoh: VRF-DTSEN-202609-001" 
                           class="w-full h-13 pl-4 pr-12 rounded-xl bg-surface-container-low text-on-surface text-sm sm:text-base font-mono font-bold tracking-wider uppercase focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary border border-surface-container transition-all" />
                    <span class="material-symbols-outlined absolute right-4 text-outline text-[22px]">qr_code_scanner</span>
                </div>
                <span class="text-[11px] text-on-surface-variant">Kode tercantum di bawah QR Code atau di pojok bawah surat resmi.</span>
            </div>

            <button type="submit" 
                    wire:loading.attr="disabled"
                    class="w-full min-h-[48px] bg-primary hover:bg-primary-container text-white rounded-xl font-bold text-sm shadow-sm transition-all cursor-pointer flex items-center justify-center gap-2 active:scale-95">
                <span class="material-symbols-outlined text-[20px]" wire:loading.remove wire:target="verify">search_check</span>
                <span class="animate-spin material-symbols-outlined text-[20px]" wire:loading wire:target="verify">sync</span>
                <span>Periksa Keaslian Surat</span>
            </button>
        </form>

        @if ($errorMessage)
            <div class="p-3.5 rounded-xl bg-error-container/40 text-on-error-container text-xs flex items-center gap-2 animate-fade-in">
                <span class="material-symbols-outlined text-error text-[18px]">error</span>
                <span>{{ $errorMessage }}</span>
            </div>
        @endif
    </div>

    <!-- RESULT SECTIONS -->
    @if ($hasChecked)
        @if ($statusType === 'valid')
            <div class="bg-surface-container-lowest rounded-2xl border border-tertiary/30 shadow-md p-6 sm:p-8 flex flex-col gap-6 animate-fade-in relative overflow-hidden">
                <!-- Top Verification Badge Banner -->
                <div class="flex items-center gap-3.5 bg-tertiary-container text-white p-4 rounded-xl shadow-xs">
                    <span class="material-symbols-outlined text-[32px] text-tertiary-fixed shrink-0" style="font-variation-settings: 'FILL' 1;">verified</span>
                    <div class="flex flex-col">
                        <strong class="font-extrabold text-sm sm:text-base tracking-wide">DOKUMEN RESMI TERVERIFIKASI & SAH</strong>
                        <span class="text-xs text-white/80">Tercatat dalam Pangkalan Data Resmi Dinas Sosial Kabupaten Blitar</span>
                    </div>
                </div>

                <!-- Certificate Metadata Details -->
                <div class="flex flex-col divide-y divide-surface-container text-xs sm:text-sm">
                    <div class="py-3 flex flex-col gap-0.5">
                        <span class="text-xs text-on-surface-variant">Nomor Surat Resmi:</span>
                        <span class="font-mono font-bold text-on-surface text-base sm:text-lg">{{ $certificateData['certificate_number'] }}</span>
                    </div>

                    <div class="py-3 flex flex-col gap-0.5">
                        <span class="text-xs text-on-surface-variant">Jenis Dokumen:</span>
                        <span class="font-bold text-primary">Surat Keterangan Data Tunggal Sosial Ekonomi Nasional (DTSEN)</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 py-3">
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs text-on-surface-variant">Nama Warga Terdata:</span>
                            <span class="font-bold text-on-surface text-base">{{ $certificateData['subject_name'] }}</span>
                        </div>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs text-on-surface-variant">Status Desil DTSEN:</span>
                            <span class="inline-flex items-center gap-1.5 text-primary font-bold">
                                <span class="material-symbols-outlined text-[18px]">family_restroom</span>
                                <span>Desil {{ $certificateData['decile'] }} (Tersinkronisasi SIKS-NG)</span>
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 py-3">
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs text-on-surface-variant">NIK (Privasi Terjaga):</span>
                            <span class="font-mono font-medium text-on-surface">{{ $certificateData['masked_nik'] }}</span>
                        </div>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs text-on-surface-variant">Nomor KK:</span>
                            <span class="font-mono font-medium text-on-surface">{{ $certificateData['masked_kk'] }}</span>
                        </div>
                    </div>

                    <div class="py-3 flex flex-col gap-0.5">
                        <span class="text-xs text-on-surface-variant">Tujuan Penggunaan:</span>
                        <span class="font-semibold text-on-surface">{{ $certificateData['purpose_name'] }}</span>
                        @if ($certificateData['purpose_description'])
                            <span class="text-xs text-on-surface-variant">({{ $certificateData['purpose_description'] }})</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 py-3">
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs text-on-surface-variant">Tanggal Diterbitkan:</span>
                            <span class="font-semibold text-on-surface">{{ $certificateData['issued_at'] }}</span>
                        </div>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs text-on-surface-variant">Masa Berlaku Hingga:</span>
                            <span class="font-semibold text-tertiary">{{ $certificateData['valid_until'] }}</span>
                        </div>
                    </div>

                    <div class="py-3 flex flex-col gap-0.5">
                        <span class="text-xs text-on-surface-variant">Pejabat Penandatangan Elektronik:</span>
                        <span class="font-bold text-on-surface">{{ $certificateData['signer_name'] }}</span>
                        <span class="text-xs text-on-surface-variant">Kepala Dinas Sosial Kabupaten Blitar</span>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-surface-container-low text-xs text-on-surface-variant flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[18px]">lock</span>
                    <span>Tanda Tangan Elektronik ini sah dan mengikat sesuai Undang-Undang ITE Republik Indonesia.</span>
                </div>
            </div>

        @elseif ($statusType === 'expired')
            <div class="bg-surface-container-lowest rounded-2xl border border-secondary shadow-md p-6 sm:p-8 flex flex-col gap-4 animate-fade-in">
                <div class="flex items-center gap-3 bg-secondary-container text-on-secondary-container p-4 rounded-xl">
                    <span class="material-symbols-outlined text-[32px] text-secondary shrink-0">timer_off</span>
                    <div class="flex flex-col">
                        <strong class="font-bold text-base">SURAT TELAH MELEWATI MASA BERLAKU (KEDALUWARSA)</strong>
                        <span class="text-xs">Masa berlaku surat keterangan ini telah habis pada {{ $certificateData['valid_until'] }}.</span>
                    </div>
                </div>
                <p class="text-xs sm:text-sm text-on-surface-variant leading-relaxed">
                    Surat dengan nomor <strong>{{ $certificateData['certificate_number'] }}</strong> atas nama <strong>{{ $certificateData['subject_name'] }}</strong> pernah diterbitkan secara sah, namun masa berlakunya telah berakhir. Silakan melakukan pengajuan surat keterangan baru jika masih dibutuhkan.
                </p>
                <div class="pt-2">
                    <a href="{{ route('services.dtsen.apply') }}" class="px-5 py-2.5 rounded-xl bg-primary text-white font-bold text-xs hover:bg-primary-container transition-colors inline-flex items-center gap-1.5">
                        <span>Ajukan Surat Baru &rarr;</span>
                    </a>
                </div>
            </div>

        @elseif ($statusType === 'not_found')
            <div class="bg-surface-container-lowest rounded-2xl border border-error/30 shadow-md p-6 sm:p-8 flex flex-col gap-4 animate-fade-in text-center items-center">
                <div class="w-16 h-16 rounded-full bg-error-container/40 text-error flex items-center justify-center">
                    <span class="material-symbols-outlined text-[36px]">gpp_bad</span>
                </div>
                <div class="flex flex-col gap-1 max-w-md">
                    <h3 class="text-lg font-bold text-error">Kode Verifikasi Tidak Ditemukan</h3>
                    <p class="text-xs sm:text-sm text-on-surface-variant leading-relaxed">
                        Kode <strong>"{{ htmlspecialchars($code) }}"</strong> tidak terdaftar dalam pangkalan data resmi Dinas Sosial Kabupaten Blitar. Pastikan Anda memasukkan kode dengan benar. Jika lembar fisik mencantumkan stempel yang mencurigakan, waspadai pemalsuan dokumen.
                    </p>
                </div>
            </div>
        @endif
    @endif
</div>
