<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col gap-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs font-medium text-on-surface-variant">
        <a href="{{ route('home') }}" class="hover:text-primary transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-[15px]">home</span>
            <span>Beranda</span>
        </a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <a href="{{ route('services.index') }}" class="hover:text-primary transition-colors">Layanan</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <a href="{{ route('services.show', 'reaktivasi-kis-pbi-jk') }}" class="hover:text-primary transition-colors">Detail KIS PBI</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-primary font-bold">Formulir Reaktivasi</span>
    </nav>

    <!-- SUCCESS SCREEN -->
    @if ($isSuccess)
        <div class="bg-surface-container-lowest rounded-2xl p-6 sm:p-10 border border-surface-container-high shadow-md flex flex-col items-center text-center gap-6 animate-fade-in" x-data="{ copied: false }">
            <div class="w-20 h-20 rounded-full bg-secondary-container/20 text-secondary flex items-center justify-center shadow-xs">
                <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1;">health_and_safety</span>
            </div>

            <div class="flex flex-col gap-1 max-w-lg">
                <span class="text-xs font-bold text-secondary uppercase tracking-wider">Permohonan Diterima</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-on-surface tracking-tight">Reaktivasi KIS Berhasil Diajukan!</h1>
                <p class="text-xs sm:text-sm text-on-surface-variant leading-relaxed">
                    Berkas Anda telah masuk antrean verifikasi kelayakan Dinas Sosial Kab. Blitar untuk diterbitkan surat rekomendasi pengusulan ke Kementerian Sosial RI.
                </p>
            </div>

            <!-- Big Copyable Ticket Box -->
            <div class="w-full max-w-md bg-surface-container-low rounded-2xl p-5 border border-secondary/20 flex flex-col items-center gap-3">
                <span class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Nomor Tiket Pelacakan</span>
                <div class="flex items-center justify-center gap-3">
                    <span class="text-2xl sm:text-3xl font-extrabold font-mono text-primary tracking-wider">{{ $submittedTicket }}</span>
                    <button @click="navigator.clipboard.writeText('{{ $submittedTicket }}'); copied = true; setTimeout(() => copied = false, 2500)" 
                            type="button" 
                            class="p-2.5 rounded-xl bg-surface-container hover:bg-surface-container-high text-primary transition-all cursor-pointer" 
                            title="Salin Nomor Tiket">
                        <span class="material-symbols-outlined text-[20px]" x-show="!copied">content_copy</span>
                        <span class="material-symbols-outlined text-[20px] text-tertiary" x-show="copied" style="display: none;">check</span>
                    </button>
                </div>
                <span class="text-[11px] text-on-surface-variant" x-show="!copied">Gunakan nomor ini untuk memantau proses verifikasi, pengusulan SIKS-NG, hingga kartu aktif.</span>
                <span class="text-[11px] text-tertiary font-bold" x-show="copied" style="display: none;">Nomor tiket berhasil disalin ke clipboard!</span>
            </div>

            <div class="w-full max-w-md bg-surface-container-lowest rounded-xl p-4 border border-surface-container text-left text-xs space-y-2">
                <div class="flex justify-between py-1 border-b border-surface-container">
                    <span class="text-on-surface-variant">Nama Peserta:</span>
                    <span class="font-bold text-on-surface">{{ $applicant_name }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-surface-container">
                    <span class="text-on-surface-variant">Nomor Kartu BPJS/KIS:</span>
                    <span class="font-mono font-bold text-on-surface">{{ $bpjs_card_number }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-surface-container">
                    <span class="text-on-surface-variant">Alasan:</span>
                    <span class="font-semibold text-on-surface capitalize">{{ $reason }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-on-surface-variant">Tanggal Pengajuan:</span>
                    <span class="font-semibold text-on-surface">{{ $submittedAt }}</span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3 w-full max-w-md pt-2">
                <a href="{{ route('tracking') }}?tiket={{ urlencode($submittedTicket) }}" class="w-full min-h-[48px] rounded-xl bg-primary text-white font-bold text-sm shadow-md hover:bg-primary-container transition-all flex items-center justify-center gap-2 active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">timeline</span>
                    <span>Lacak Status Tiket</span>
                </a>
                <a href="{{ route('home') }}" class="w-full min-h-[48px] rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface font-bold text-sm transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">home</span>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>

    @else
        <!-- Header Title & Badges -->
        <div class="flex flex-col gap-2">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-primary tracking-tight">
                Formulir Reaktivasi KIS / PBI-JK
            </h1>
            <p class="text-xs sm:text-sm text-on-surface-variant">
                Fasilitasi Pengaktifan Kembali Kepesertaan JKN-KIS PBI-JK · Dinas Sosial Kabupaten Blitar
            </p>
            <div class="flex flex-wrap items-center gap-2 mt-1">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-tertiary-fixed text-on-tertiary-fixed flex items-center gap-1">
                    <span class="material-symbols-outlined text-[15px] text-tertiary">check_circle</span>
                    <span>Layanan Bebas Biaya (Rp 0)</span>
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-secondary-fixed text-on-secondary-fixed flex items-center gap-1">
                    <span class="material-symbols-outlined text-[15px]">verified</span>
                    <span>Terhubung SIKS-NG Kemensos</span>
                </span>
            </div>
        </div>

        <!-- Priority Alert if Emergency -->
        @if ($this->isEmergency())
            <div class="bg-secondary-container text-on-secondary-container rounded-2xl p-4 sm:p-5 shadow-sm flex items-start gap-3.5 border border-secondary/30 animate-fade-in">
                <div class="w-10 h-10 rounded-xl bg-secondary text-white flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[22px]">emergency</span>
                </div>
                <div class="flex-1 text-xs sm:text-sm">
                    <strong class="font-bold block mb-0.5">Jalur Prioritas Darurat Medis Rumah Sakit</strong>
                    <p class="leading-relaxed">
                        Pengajuan dengan alasan kondisi darurat medis rawat inap akan diprioritaskan oleh petugas piket verifikasi dalam 1x24 jam kerja agar dapat segera diusulkan ke Kementerian Sosial.
                    </p>
                </div>
            </div>
        @endif

        <!-- Stepper Navigation -->
        <div class="bg-surface-container-lowest rounded-2xl p-4 sm:p-5 border border-surface-container-high shadow-xs">
            <div class="relative flex items-center justify-between max-w-xl mx-auto">
                <div class="absolute left-6 right-6 top-4 h-1 bg-surface-container -translate-y-1/2 z-0"></div>
                <div class="absolute left-6 top-4 h-1 bg-primary -translate-y-1/2 z-0 transition-all duration-300"
                     style="width: {{ ($currentStep - 1) * 33.33 }}%;"></div>

                <div class="relative z-10 flex flex-col items-center">
                    <button wire:click="goToStep(1)" type="button" 
                            class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all {{ $currentStep >= 1 ? 'bg-primary text-white shadow-xs' : 'bg-surface-container text-on-surface-variant' }}">
                        @if ($currentStep > 1) <span class="material-symbols-outlined text-[16px]">check</span> @else 1 @endif
                    </button>
                    <span class="text-[11px] font-bold mt-1.5 {{ $currentStep === 1 ? 'text-primary' : 'text-on-surface-variant' }}">Peserta</span>
                </div>

                <div class="relative z-10 flex flex-col items-center">
                    <button wire:click="goToStep(2)" type="button" 
                            class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all {{ $currentStep >= 2 ? 'bg-primary text-white shadow-xs' : 'bg-surface-container text-on-surface-variant' }}">
                        @if ($currentStep > 2) <span class="material-symbols-outlined text-[16px]">check</span> @else 2 @endif
                    </button>
                    <span class="text-[11px] font-bold mt-1.5 {{ $currentStep === 2 ? 'text-primary' : 'text-on-surface-variant' }}">Alasan</span>
                </div>

                <div class="relative z-10 flex flex-col items-center">
                    <button wire:click="goToStep(3)" type="button" 
                            class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all {{ $currentStep >= 3 ? 'bg-primary text-white shadow-xs' : 'bg-surface-container text-on-surface-variant' }}">
                        @if ($currentStep > 3) <span class="material-symbols-outlined text-[16px]">check</span> @else 3 @endif
                    </button>
                    <span class="text-[11px] font-bold mt-1.5 {{ $currentStep === 3 ? 'text-primary' : 'text-on-surface-variant' }}">Berkas</span>
                </div>

                <div class="relative z-10 flex flex-col items-center">
                    <button type="button" 
                            class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all {{ $currentStep === 4 ? 'bg-primary text-white shadow-xs' : 'bg-surface-container text-on-surface-variant' }}">
                        4
                    </button>
                    <span class="text-[11px] font-bold mt-1.5 {{ $currentStep === 4 ? 'text-primary' : 'text-on-surface-variant' }}">Tinjau</span>
                </div>
            </div>
        </div>

        <!-- FORM CONTENT BY STEP -->
        <div class="bg-surface-container-lowest rounded-2xl p-6 sm:p-8 border border-surface-container-high shadow-xs flex flex-col gap-6">

            <!-- STEP 1: DATA PESERTA -->
            @if ($currentStep === 1)
                <div class="flex flex-col gap-4 animate-fade-in">
                    <div class="flex items-center justify-between pb-2 border-b border-surface-container">
                        <div>
                            <span class="text-xs font-bold text-secondary uppercase tracking-wider">Langkah 1 dari 4</span>
                            <h2 class="text-lg font-bold text-on-surface">Data Peserta JKN-KIS / PBI</h2>
                        </div>
                        <span class="material-symbols-outlined text-primary text-[24px]">badge</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-bold text-on-surface">Nama Lengkap Peserta (Sesuai KTP) <span class="text-error">*</span></label>
                            <input wire:model="applicant_name" type="text" placeholder="Nama Lengkap" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary" />
                            @error('applicant_name') <span class="text-[11px] text-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-bold text-on-surface">NIK Peserta (16 Digit) <span class="text-error">*</span></label>
                            <input wire:model="applicant_nik" type="text" maxlength="16" placeholder="3505xxxxxxxxxxxx" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm font-mono border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary" />
                            @error('applicant_nik') <span class="text-[11px] text-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-bold text-on-surface">Nomor Kartu Keluarga (KK) <span class="text-error">*</span></label>
                            <input wire:model="family_card_number" type="text" maxlength="16" placeholder="3505xxxxxxxxxxxx" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm font-mono border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary" />
                            @error('family_card_number') <span class="text-[11px] text-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-bold text-on-surface">Nomor Kartu BPJS / KIS Nonaktif <span class="text-error">*</span></label>
                            <input wire:model="bpjs_card_number" type="text" placeholder="0001xxxxxxxxxxxx" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm font-mono border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary" />
                            @error('bpjs_card_number') <span class="text-[11px] text-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-bold text-on-surface">No. WhatsApp / HP Aktif <span class="text-error">*</span></label>
                            <input wire:model="phone" type="tel" placeholder="08xxxxxxxxxx" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary" />
                            @error('phone') <span class="text-[11px] text-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-bold text-on-surface">Perkiraan Tanggal Nonaktif (Opsional)</label>
                            <input wire:model="deactivated_date" type="date" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary" />
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-bold text-on-surface">Kecamatan Domisili <span class="text-error">*</span></label>
                            <select wire:model.live="district_id" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="">-- Pilih Kecamatan --</option>
                                @foreach ($districts as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                            @error('district_id') <span class="text-[11px] text-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-bold text-on-surface">Desa / Kelurahan <span class="text-error">*</span></label>
                            <select wire:model="village_id" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary" {{ empty($district_id) ? 'disabled' : '' }}>
                                <option value="">-- Pilih Desa/Kelurahan --</option>
                                @foreach ($villages as $v)
                                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                                @endforeach
                            </select>
                            @error('village_id') <span class="text-[11px] text-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-full flex flex-col gap-1">
                            <label class="text-xs font-bold text-on-surface">Alamat Lengkap Tempat Tinggal <span class="text-error">*</span></label>
                            <textarea wire:model="address" rows="2" placeholder="Alamat RT/RW, Dusun, Desa" class="w-full p-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                            @error('address') <span class="text-[11px] text-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

            <!-- STEP 2: ALASAN REAKTIVASI -->
            @elseif ($currentStep === 2)
                <div class="flex flex-col gap-5 animate-fade-in">
                    <div class="flex items-center justify-between pb-2 border-b border-surface-container">
                        <div>
                            <span class="text-xs font-bold text-secondary uppercase tracking-wider">Langkah 2 dari 4</span>
                            <h2 class="text-lg font-bold text-on-surface">Alasan Reaktivasi & Fasilitas Kesehatan</h2>
                        </div>
                        <span class="material-symbols-outlined text-primary text-[24px]">local_hospital</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <label class="relative flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all {{ $reason === 'emergency' ? 'border-secondary bg-secondary-container/10 shadow-xs' : 'border-surface-container hover:border-outline-variant' }}">
                            <input type="radio" wire:model.live="reason" value="emergency" class="mt-1 text-secondary focus:ring-secondary h-4 w-4" />
                            <div class="flex flex-col">
                                <span class="font-bold text-sm text-on-surface flex items-center gap-1.5">
                                    <span>Kondisi Darurat Medis RS</span>
                                    <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-error-container text-on-error-container">Prioritas</span>
                                </span>
                                <span class="text-xs text-on-surface-variant mt-1">
                                    Sedang rawat inap di IGD / RS dan membutuhkan jaminan pembiayaan segera.
                                </span>
                            </div>
                        </label>

                        <label class="relative flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all {{ $reason === 'chronic' ? 'border-primary bg-primary/5 shadow-xs' : 'border-surface-container hover:border-outline-variant' }}">
                            <input type="radio" wire:model.live="reason" value="chronic" class="mt-1 text-primary focus:ring-primary h-4 w-4" />
                            <div class="flex flex-col">
                                <span class="font-bold text-sm text-on-surface">Penyakit Kronis / Rutin</span>
                                <span class="text-xs text-on-surface-variant mt-1">
                                    Memerlukan pengobatan atau kontrol rutin medis berkesinambungan.
                                </span>
                            </div>
                        </label>

                        <label class="relative flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all {{ $reason === 'catastrophic' ? 'border-primary bg-primary/5 shadow-xs' : 'border-surface-container hover:border-outline-variant' }}">
                            <input type="radio" wire:model.live="reason" value="catastrophic" class="mt-1 text-primary focus:ring-primary h-4 w-4" />
                            <div class="flex flex-col">
                                <span class="font-bold text-sm text-on-surface">Penyakit Katastropik</span>
                                <span class="text-xs text-on-surface-variant mt-1">
                                    Penyakit berbiaya tinggi (cuci darah/hemodialisa, kanker, jantung, dll).
                                </span>
                            </div>
                        </label>

                        <label class="relative flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all {{ $reason === 'newborn' ? 'border-primary bg-primary/5 shadow-xs' : 'border-surface-container hover:border-outline-variant' }}">
                            <input type="radio" wire:model.live="reason" value="newborn" class="mt-1 text-primary focus:ring-primary h-4 w-4" />
                            <div class="flex flex-col">
                                <span class="font-bold text-sm text-on-surface">Bayi Baru Lahir dari Ibu PBI</span>
                                <span class="text-xs text-on-surface-variant mt-1">
                                    Pendaftaran bayi dari ibu yang telah menjadi peserta aktif PBI-JK.
                                </span>
                            </div>
                        </label>
                    </div>

                    @if ($this->isMedicalReason())
                        <div class="bg-surface-container-low rounded-xl p-4 sm:p-5 border border-surface-container flex flex-col gap-3.5 mt-2 animate-fade-in">
                            <span class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">domain</span>
                                <span>Informasi Fasilitas Kesehatan / Rumah Sakit Perujuk</span>
                            </span>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-bold text-on-surface">Nama Fasilitas Kesehatan (RS / Puskesmas) <span class="text-error">*</span></label>
                                    <input wire:model="health_facility_name" type="text" placeholder="Contoh: RSUD Ngudi Waluyo Wlingi" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-lowest text-on-surface text-sm border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary" />
                                    @error('health_facility_name') <span class="text-[11px] text-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-bold text-on-surface">Nomor Surat Keterangan Rawat / Rujukan</label>
                                    <input wire:model="health_letter_number" type="text" placeholder="Contoh: 445/102/RSUD/2026" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-lowest text-on-surface text-sm border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary" />
                                    @error('health_letter_number') <span class="text-[11px] text-error">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

            <!-- STEP 3: BERKAS PERSYARATAN -->
            @elseif ($currentStep === 3)
                <div class="flex flex-col gap-5 animate-fade-in">
                    <div class="flex items-center justify-between pb-2 border-b border-surface-container">
                        <div>
                            <span class="text-xs font-bold text-secondary uppercase tracking-wider">Langkah 3 dari 4</span>
                            <h2 class="text-lg font-bold text-on-surface">Unggah Berkas Persyaratan</h2>
                        </div>
                        <span class="material-symbols-outlined text-primary text-[24px]">upload_file</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        <!-- Dropzone KTP -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold text-on-surface flex items-center justify-between">
                                <span>KTP Peserta <span class="text-error">*</span></span>
                                @if ($ktp_file) <span class="text-tertiary text-[11px] font-bold">Terpilih</span> @endif
                            </label>
                            <div class="relative border-2 border-dashed rounded-xl p-4 text-center transition-all flex flex-col items-center justify-center gap-2 {{ $ktp_file ? 'border-tertiary bg-tertiary/5' : 'border-surface-container hover:border-primary bg-surface-container-low' }}">
                                <input type="file" wire:model="ktp_file" accept=".jpg,.jpeg,.png,.pdf" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" />
                                <span class="material-symbols-outlined text-primary text-[26px]">badge</span>
                                <span class="text-xs font-semibold text-on-surface">{{ $ktp_file ? $ktp_file->getClientOriginalName() : 'Unggah KTP' }}</span>
                            </div>
                            @error('ktp_file') <span class="text-[11px] text-error">{{ $message }}</span> @enderror
                        </div>

                        <!-- Dropzone KK -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold text-on-surface flex items-center justify-between">
                                <span>Kartu Keluarga (KK) <span class="text-error">*</span></span>
                                @if ($kk_file) <span class="text-tertiary text-[11px] font-bold">Terpilih</span> @endif
                            </label>
                            <div class="relative border-2 border-dashed rounded-xl p-4 text-center transition-all flex flex-col items-center justify-center gap-2 {{ $kk_file ? 'border-tertiary bg-tertiary/5' : 'border-surface-container hover:border-primary bg-surface-container-low' }}">
                                <input type="file" wire:model="kk_file" accept=".jpg,.jpeg,.png,.pdf" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" />
                                <span class="material-symbols-outlined text-primary text-[26px]">family_restroom</span>
                                <span class="text-xs font-semibold text-on-surface">{{ $kk_file ? $kk_file->getClientOriginalName() : 'Unggah KK' }}</span>
                            </div>
                            @error('kk_file') <span class="text-[11px] text-error">{{ $message }}</span> @enderror
                        </div>

                        <!-- Dropzone Kartu KIS -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold text-on-surface flex items-center justify-between">
                                <span>Kartu BPJS / KIS Nonaktif <span class="text-error">*</span></span>
                                @if ($kis_file) <span class="text-tertiary text-[11px] font-bold">Terpilih</span> @endif
                            </label>
                            <div class="relative border-2 border-dashed rounded-xl p-4 text-center transition-all flex flex-col items-center justify-center gap-2 {{ $kis_file ? 'border-tertiary bg-tertiary/5' : 'border-surface-container hover:border-primary bg-surface-container-low' }}">
                                <input type="file" wire:model="kis_file" accept=".jpg,.jpeg,.png,.pdf" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" />
                                <span class="material-symbols-outlined text-primary text-[26px]">credit_card</span>
                                <span class="text-xs font-semibold text-on-surface">{{ $kis_file ? $kis_file->getClientOriginalName() : 'Unggah Kartu KIS' }}</span>
                            </div>
                            @error('kis_file') <span class="text-[11px] text-error">{{ $message }}</span> @enderror
                        </div>

                        <!-- Dropzone Surat Faskes -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold text-on-surface flex items-center justify-between">
                                <span>Surat Ket. Rawat / Faskes {{ $this->isMedicalReason() ? '(Wajib)' : '(Opsional)' }}</span>
                                @if ($faskes_file) <span class="text-tertiary text-[11px] font-bold">Terpilih</span> @endif
                            </label>
                            <div class="relative border-2 border-dashed rounded-xl p-4 text-center transition-all flex flex-col items-center justify-center gap-2 {{ $faskes_file ? 'border-tertiary bg-tertiary/5' : 'border-surface-container hover:border-primary bg-surface-container-low' }}">
                                <input type="file" wire:model="faskes_file" accept=".jpg,.jpeg,.png,.pdf" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" />
                                <span class="material-symbols-outlined text-secondary text-[26px]">local_hospital</span>
                                <span class="text-xs font-semibold text-on-surface">{{ $faskes_file ? $faskes_file->getClientOriginalName() : 'Unggah Surat RS / Faskes' }}</span>
                            </div>
                            @error('faskes_file') <span class="text-[11px] text-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

            <!-- STEP 4: TINJAU & KIRIM -->
            @elseif ($currentStep === 4)
                <div class="flex flex-col gap-6 animate-fade-in">
                    <div class="flex items-center justify-between pb-2 border-b border-surface-container">
                        <div>
                            <span class="text-xs font-bold text-secondary uppercase tracking-wider">Langkah 4 dari 4</span>
                            <h2 class="text-lg font-bold text-on-surface">Tinjau Data Reaktivasi KIS</h2>
                        </div>
                        <span class="material-symbols-outlined text-primary text-[24px]">fact_check</span>
                    </div>

                    <div class="p-4 rounded-xl bg-surface-container-low border border-surface-container flex flex-col gap-3 text-xs">
                        <div class="flex items-center justify-between border-b border-surface-container pb-2">
                            <span class="font-bold text-primary text-sm">Ringkasan Data Peserta</span>
                            <button wire:click="goToStep(1)" type="button" class="text-secondary font-bold hover:underline flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">edit</span>
                                <span>Ubah</span>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <div><span class="text-on-surface-variant">Nama:</span> <strong class="text-on-surface block">{{ $applicant_name }}</strong></div>
                            <div><span class="text-on-surface-variant">NIK:</span> <span class="font-mono text-on-surface block">{{ $applicant_nik }}</span></div>
                            <div><span class="text-on-surface-variant">Nomor KIS:</span> <span class="font-mono text-on-surface block">{{ $bpjs_card_number }}</span></div>
                            <div><span class="text-on-surface-variant">WhatsApp:</span> <span class="text-on-surface block">{{ $phone }}</span></div>
                            <div><span class="text-on-surface-variant">Alasan:</span> <span class="text-on-surface font-semibold block capitalize">{{ $reason }}</span></div>
                            @if ($health_facility_name)
                                <div><span class="text-on-surface-variant">Faskes / RS:</span> <span class="text-on-surface font-semibold block">{{ $health_facility_name }}</span></div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-2">
                        <label class="flex items-start gap-3 p-4 rounded-xl bg-surface-container border border-primary/20 cursor-pointer">
                            <input type="checkbox" wire:model="agreement_confirmed" class="mt-1 h-5 w-5 text-primary rounded focus:ring-primary" />
                            <span class="text-xs text-on-surface leading-relaxed">
                                <strong>Pernyataan Kebenaran Data:</strong> Saya menyatakan bahwa berkas kepesertaan KIS/PBI-JK yang diajukan adalah sah dan benar milik peserta bersangkutan untuk keperluan verifikasi jaminan kesehatan daerah.
                            </span>
                        </label>
                        @error('agreement_confirmed')
                            <span class="text-xs text-error font-medium mt-1.5 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            @endif

            <!-- Navigation Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-surface-container mt-2">
                @if ($currentStep > 1)
                    <button wire:click="previousStep" type="button" class="min-h-[46px] px-5 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface font-bold text-xs flex items-center gap-1.5 transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                        <span>Sebelumnya</span>
                    </button>
                @else
                    <div></div>
                @endif

                @if ($currentStep < 4)
                    <button wire:click="nextStep" type="button" class="min-h-[46px] px-6 rounded-xl bg-primary hover:bg-primary-container text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all cursor-pointer active:scale-95">
                        <span>Langkah Selanjutnya</span>
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </button>
                @else
                    <button wire:click="submit" wire:loading.attr="disabled" type="button" class="min-h-[48px] px-8 rounded-xl bg-secondary-container hover:brightness-95 text-on-secondary-container font-extrabold text-sm shadow-md transition-all cursor-pointer flex items-center gap-2 active:scale-95">
                        <span class="material-symbols-outlined text-[20px]" wire:loading.remove wire:target="submit">send</span>
                        <span class="animate-spin material-symbols-outlined text-[20px]" wire:loading wire:target="submit">sync</span>
                        <span>Kirim Permohonan Reaktivasi</span>
                    </button>
                @endif
            </div>
        </div>
    @endif
</div>
