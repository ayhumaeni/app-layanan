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
        <a href="{{ route('services.show', 'surat-keterangan-dtsen') }}" class="hover:text-primary transition-colors">Detail DTSEN</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-primary font-bold">Formulir Pengajuan</span>
    </nav>

    <!-- SUCCESS SCREEN -->
    @if ($isSuccess)
        <div class="bg-surface-container-lowest rounded-2xl p-6 sm:p-10 border border-surface-container-high shadow-md flex flex-col items-center text-center gap-6 animate-fade-in" x-data="{ copied: false }">
            <div class="w-20 h-20 rounded-full bg-tertiary-container/15 text-tertiary flex items-center justify-center shadow-xs">
                <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
            </div>

            <div class="flex flex-col gap-1 max-w-lg">
                <span class="text-xs font-bold text-tertiary uppercase tracking-wider">Berhasil Diajukan</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-on-surface tracking-tight">Pengajuan Berhasil Dikirim!</h1>
                <p class="text-xs sm:text-sm text-on-surface-variant leading-relaxed">
                    Permohonan Surat Keterangan DTSEN Anda telah tercatat secara resmi di pangkalan data Dinas Sosial Kabupaten Blitar.
                </p>
            </div>

            <!-- Big Copyable Ticket Box -->
            <div class="w-full max-w-md bg-surface-container-low rounded-2xl p-5 border border-primary/20 flex flex-col items-center gap-3">
                <span class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Nomor Tiket Resmi Anda</span>
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
                <span class="text-[11px] text-on-surface-variant" x-show="!copied">Simpan atau catat nomor tiket ini untuk melacak status dan mengunduh surat saat selesai.</span>
                <span class="text-[11px] text-tertiary font-bold" x-show="copied" style="display: none;">Nomor tiket berhasil disalin ke clipboard!</span>
            </div>

            <!-- Summary Card -->
            <div class="w-full max-w-md bg-surface-container-lowest rounded-xl p-4 border border-surface-container text-left text-xs space-y-2">
                <div class="flex justify-between py-1 border-b border-surface-container">
                    <span class="text-on-surface-variant">Nama Pemohon:</span>
                    <span class="font-bold text-on-surface">{{ $applicant_name }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-surface-container">
                    <span class="text-on-surface-variant">Tujuan Surat:</span>
                    <span class="font-bold text-on-surface">{{ $selectedPurpose?->name }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-surface-container">
                    <span class="text-on-surface-variant">Tanggal Pengajuan:</span>
                    <span class="font-semibold text-on-surface">{{ $submittedAt }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-on-surface-variant">Status:</span>
                    <span class="px-2 py-0.5 rounded-full font-bold bg-primary text-white text-[10px]">Diajukan (Pemeriksaan Berkas)</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full max-w-md pt-2">
                <a href="{{ route('tracking') }}?tiket={{ urlencode($submittedTicket) }}" class="w-full min-h-[48px] rounded-xl bg-primary text-white font-bold text-sm shadow-md hover:bg-primary-container transition-all flex items-center justify-center gap-2 active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">timeline</span>
                    <span>Lacak Status Tiket Ini</span>
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
                Formulir Pengajuan Surat Keterangan DTSEN
            </h1>
            <p class="text-xs sm:text-sm text-on-surface-variant">
                Data Tunggal Sosial Ekonomi Nasional · Dinas Sosial Kabupaten Blitar
            </p>
            <div class="flex flex-wrap items-center gap-2 mt-1">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-tertiary-fixed text-on-tertiary-fixed flex items-center gap-1">
                    <span class="material-symbols-outlined text-[15px] text-tertiary">check_circle</span>
                    <span>Layanan Gratis / Rp 0</span>
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-secondary-fixed text-on-secondary-fixed flex items-center gap-1">
                    <span class="material-symbols-outlined text-[15px]">schedule</span>
                    <span>Estimasi: 1-2 Hari Kerja</span>
                </span>
            </div>
        </div>

        <!-- Operator Assistance Callout -->
        <div class="bg-surface-container-high/60 rounded-2xl p-4 sm:p-5 border border-primary/10 flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-primary-container text-white flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[22px]">support_agent</span>
            </div>
            <div class="flex-1 text-xs sm:text-sm">
                <strong class="text-primary font-bold block mb-0.5">Butuh Bantuan Pengisian?</strong>
                <p class="text-on-surface leading-relaxed">
                    Pengajuan dapat dibantu oleh Operator Desa / Kelurahan atau Petugas Puskesos terdekat jika Anda mengalami kesulitan dalam mengunggah berkas.
                </p>
            </div>
        </div>

        <!-- Multi-Step Stepper Header -->
        <div class="bg-surface-container-lowest rounded-2xl p-4 sm:p-5 border border-surface-container-high shadow-xs">
            <div class="relative flex items-center justify-between max-w-xl mx-auto">
                <!-- Connecting Line Background -->
                <div class="absolute left-6 right-6 top-4 h-1 bg-surface-container -translate-y-1/2 z-0"></div>
                <!-- Active Progress Line -->
                <div class="absolute left-6 top-4 h-1 bg-primary -translate-y-1/2 z-0 transition-all duration-300"
                     style="width: {{ ($currentStep - 1) * 33.33 }}%;"></div>

                <!-- Step 1 -->
                <div class="relative z-10 flex flex-col items-center">
                    <button wire:click="goToStep(1)" type="button" 
                            class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all {{ $currentStep >= 1 ? 'bg-primary text-white shadow-xs' : 'bg-surface-container text-on-surface-variant' }}">
                        @if ($currentStep > 1) <span class="material-symbols-outlined text-[16px]">check</span> @else 1 @endif
                    </button>
                    <span class="text-[11px] font-bold mt-1.5 {{ $currentStep === 1 ? 'text-primary' : 'text-on-surface-variant' }}">Tujuan</span>
                </div>

                <!-- Step 2 -->
                <div class="relative z-10 flex flex-col items-center">
                    <button wire:click="goToStep(2)" type="button" 
                            class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all {{ $currentStep >= 2 ? 'bg-primary text-white shadow-xs' : 'bg-surface-container text-on-surface-variant' }}">
                        @if ($currentStep > 2) <span class="material-symbols-outlined text-[16px]">check</span> @else 2 @endif
                    </button>
                    <span class="text-[11px] font-bold mt-1.5 {{ $currentStep === 2 ? 'text-primary' : 'text-on-surface-variant' }}">Identitas</span>
                </div>

                <!-- Step 3 -->
                <div class="relative z-10 flex flex-col items-center">
                    <button wire:click="goToStep(3)" type="button" 
                            class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all {{ $currentStep >= 3 ? 'bg-primary text-white shadow-xs' : 'bg-surface-container text-on-surface-variant' }}">
                        @if ($currentStep > 3) <span class="material-symbols-outlined text-[16px]">check</span> @else 3 @endif
                    </button>
                    <span class="text-[11px] font-bold mt-1.5 {{ $currentStep === 3 ? 'text-primary' : 'text-on-surface-variant' }}">Berkas</span>
                </div>

                <!-- Step 4 -->
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

            <!-- STEP 1: TUJUAN PENGGUNAAN -->
            @if ($currentStep === 1)
                <div class="flex flex-col gap-4 animate-fade-in">
                    <div class="flex items-center justify-between pb-2 border-b border-surface-container">
                        <div>
                            <span class="text-xs font-bold text-secondary uppercase tracking-wider">Langkah 1 dari 4</span>
                            <h2 class="text-lg font-bold text-on-surface">Pilih Tujuan Penggunaan Surat</h2>
                        </div>
                        <span class="material-symbols-outlined text-primary text-[24px]">flag</span>
                    </div>

                    <p class="text-xs text-on-surface-variant">
                        Pilih peruntukan penerbitan Surat Keterangan DTSEN Anda. Batas desil akan diverifikasi otomatis oleh petugas dinas melalui sistem SIKS-NG.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        @foreach ($purposes as $purpose)
                            <label class="relative flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all {{ $dtsen_purpose_id === $purpose->id ? 'border-primary bg-primary/5 shadow-xs' : 'border-surface-container hover:border-outline-variant' }}">
                                <input type="radio" wire:model.live="dtsen_purpose_id" value="{{ $purpose->id }}" class="mt-1 text-primary focus:ring-primary h-4 w-4" />
                                <div class="flex flex-col">
                                    <span class="font-bold text-sm text-on-surface">{{ $purpose->name }}</span>
                                    <span class="text-xs text-on-surface-variant mt-1">
                                        Maksimal desil yang berlaku: <strong>Desil {{ $purpose->max_decile }}</strong>
                                    </span>
                                    <span class="text-[11px] text-primary font-semibold mt-1">Masa berlaku {{ $purpose->validity_days }} hari</span>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    @error('dtsen_purpose_id')
                        <span class="text-xs text-error font-medium">{{ $message }}</span>
                    @enderror

                    @if ($selectedPurpose && $selectedPurpose->code === 'lainnya')
                        <div class="flex flex-col gap-1.5 pt-3 animate-fade-in">
                            <label for="purpose_desc" class="text-xs font-bold text-on-surface">Jelaskan Keperluan Administrasi Lainnya <span class="text-error">*</span></label>
                            <input wire:model="purpose_description" type="text" id="purpose_desc" placeholder="Contoh: Pengajuan Beasiswa Daerah Non-KIP / Persyaratan Asuransi" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary" />
                            @error('purpose_description')
                                <span class="text-xs text-error font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif
                </div>

            <!-- STEP 2: DATA PEMOHON & SUBJEK -->
            @elseif ($currentStep === 2)
                <div class="flex flex-col gap-6 animate-fade-in">
                    <div class="flex items-center justify-between pb-2 border-b border-surface-container">
                        <div>
                            <span class="text-xs font-bold text-secondary uppercase tracking-wider">Langkah 2 dari 4</span>
                            <h2 class="text-lg font-bold text-on-surface">Data Pemohon & Orang yang Diterangkan</h2>
                        </div>
                        <span class="material-symbols-outlined text-primary text-[24px]">person</span>
                    </div>

                    <!-- Sub-section A: Data Pemohon -->
                    <div class="flex flex-col gap-4">
                        <h3 class="text-sm font-bold text-primary flex items-center gap-1.5">
                            <span class="w-2 h-4 rounded-full bg-primary"></span>
                            <span>A. Identitas Pemohon (Yang Mengajukan)</span>
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1">
                                <label class="text-xs font-bold text-on-surface">Nama Lengkap Pemohon <span class="text-error">*</span></label>
                                <input wire:model="applicant_name" type="text" placeholder="Sesuai KTP" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary" />
                                @error('applicant_name') <span class="text-[11px] text-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex flex-col gap-1">
                                <label class="text-xs font-bold text-on-surface">NIK Pemohon (16 Digit) <span class="text-error">*</span></label>
                                <input wire:model="applicant_nik" type="text" maxlength="16" placeholder="3505xxxxxxxxxxxx" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm font-mono border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary" />
                                @error('applicant_nik') <span class="text-[11px] text-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex flex-col gap-1">
                                <label class="text-xs font-bold text-on-surface">Nomor Kartu Keluarga (KK) <span class="text-error">*</span></label>
                                <input wire:model="family_card_number" type="text" maxlength="16" placeholder="3505xxxxxxxxxxxx" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm font-mono border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary" />
                                @error('family_card_number') <span class="text-[11px] text-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex flex-col gap-1">
                                <label class="text-xs font-bold text-on-surface">No. WhatsApp / Telepon <span class="text-error">*</span></label>
                                <input wire:model="phone" type="tel" placeholder="08xxxxxxxxxx" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary" />
                                @error('phone') <span class="text-[11px] text-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex flex-col gap-1">
                                <label class="text-xs font-bold text-on-surface">Kecamatan Domisili <span class="text-error">*</span></label>
                                <select wire:model.live="district_id" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary">
                                    <option value="">-- Pilih Kecamatan --</option>
                                    @foreach ($districts as $district)
                                        <option value="{{ $district->id }}">{{ $district->name }}</option>
                                    @endforeach
                                </select>
                                @error('district_id') <span class="text-[11px] text-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex flex-col gap-1">
                                <label class="text-xs font-bold text-on-surface">Desa / Kelurahan <span class="text-error">*</span></label>
                                <select wire:model="village_id" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary" {{ empty($district_id) ? 'disabled' : '' }}>
                                    <option value="">-- Pilih Desa/Kelurahan --</option>
                                    @foreach ($villages as $village)
                                        <option value="{{ $village->id }}">{{ $village->name }}</option>
                                    @endforeach
                                </select>
                                @error('village_id') <span class="text-[11px] text-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-full flex flex-col gap-1">
                                <label class="text-xs font-bold text-on-surface">Alamat Lengkap (RT/RW, Dusun, Jalan) <span class="text-error">*</span></label>
                                <textarea wire:model="address" rows="2" placeholder="Contoh: RT 02 RW 01 Dusun Krajan" class="w-full p-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                                @error('address') <span class="text-[11px] text-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Sub-section B: Data Orang yang Diterangkan -->
                    <div class="flex flex-col gap-4 pt-3 border-t border-surface-container">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-primary flex items-center gap-1.5">
                                <span class="w-2 h-4 rounded-full bg-secondary"></span>
                                <span>B. Identitas Orang yang Diterangkan (Siswa / Calon Mahasiswa / Keluarga)</span>
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="flex flex-col gap-1">
                                <label class="text-xs font-bold text-on-surface">Hubungan dengan Pemohon <span class="text-error">*</span></label>
                                <select wire:model.live="relationship_to_applicant" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary">
                                    <option value="Diri Sendiri">Diri Sendiri</option>
                                    <option value="Anak">Anak</option>
                                    <option value="Suami/Istri">Suami / Istri</option>
                                    <option value="Orang Tua">Orang Tua</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>

                            <div class="flex flex-col gap-1">
                                <label class="text-xs font-bold text-on-surface">Nama yang Diterangkan <span class="text-error">*</span></label>
                                <input wire:model="subject_name" type="text" placeholder="Nama lengkap siswa/anggota" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary" />
                                @error('subject_name') <span class="text-[11px] text-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex flex-col gap-1">
                                <label class="text-xs font-bold text-on-surface">NIK yang Diterangkan <span class="text-error">*</span></label>
                                <input wire:model="subject_nik" type="text" maxlength="16" placeholder="16 digit angka NIK" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm font-mono border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary" />
                                @error('subject_nik') <span class="text-[11px] text-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

            <!-- STEP 3: UNGGAH DOKUMEN -->
            @elseif ($currentStep === 3)
                <div class="flex flex-col gap-6 animate-fade-in">
                    <div class="flex items-center justify-between pb-2 border-b border-surface-container">
                        <div>
                            <span class="text-xs font-bold text-secondary uppercase tracking-wider">Langkah 3 dari 4</span>
                            <h2 class="text-lg font-bold text-on-surface">Unggah Berkas Persyaratan</h2>
                        </div>
                        <span class="material-symbols-outlined text-primary text-[24px]">upload_file</span>
                    </div>

                    <p class="text-xs text-on-surface-variant">
                        Format file: JPG, PNG, atau PDF (maks. 3 MB per berkas). Pastikan tulisan dan foto terlihat jelas tanpa pantulan cahaya.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                        <!-- Dropzone KTP -->
                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-bold text-on-surface flex items-center justify-between">
                                <span>Foto / Scan KTP Pemohon <span class="text-error">*</span></span>
                                @if ($ktp_file) <span class="text-tertiary font-bold text-[11px] flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">check_circle</span> Terpilih</span> @endif
                            </label>

                            <div class="relative border-2 border-dashed rounded-2xl p-6 text-center transition-all flex flex-col items-center justify-center gap-3 {{ $ktp_file ? 'border-tertiary bg-tertiary/5' : 'border-surface-container hover:border-primary bg-surface-container-low' }}">
                                <input type="file" wire:model="ktp_file" accept=".jpg,.jpeg,.png,.pdf" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" />
                                
                                <div class="w-12 h-12 rounded-xl bg-white text-primary flex items-center justify-center shadow-xs">
                                    <span class="material-symbols-outlined text-[26px]">badge</span>
                                </div>

                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-on-surface">
                                        {{ $ktp_file ? $ktp_file->getClientOriginalName() : 'Pilih atau Tarik Foto KTP' }}
                                    </span>
                                    <span class="text-[11px] text-on-surface-variant mt-0.5">JPG, PNG atau PDF maks. 3MB</span>
                                </div>

                                <span wire:loading wire:target="ktp_file" class="text-xs text-primary font-bold flex items-center gap-1">
                                    <span class="animate-spin material-symbols-outlined text-[16px]">sync</span>
                                    <span>Mengunggah...</span>
                                </span>
                            </div>
                            @error('ktp_file') <span class="text-xs text-error font-medium">{{ $message }}</span> @enderror
                        </div>

                        <!-- Dropzone KK -->
                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-bold text-on-surface flex items-center justify-between">
                                <span>Foto / Scan Kartu Keluarga (KK) <span class="text-error">*</span></span>
                                @if ($kk_file) <span class="text-tertiary font-bold text-[11px] flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">check_circle</span> Terpilih</span> @endif
                            </label>

                            <div class="relative border-2 border-dashed rounded-2xl p-6 text-center transition-all flex flex-col items-center justify-center gap-3 {{ $kk_file ? 'border-tertiary bg-tertiary/5' : 'border-surface-container hover:border-primary bg-surface-container-low' }}">
                                <input type="file" wire:model="kk_file" accept=".jpg,.jpeg,.png,.pdf" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" />
                                
                                <div class="w-12 h-12 rounded-xl bg-white text-primary flex items-center justify-center shadow-xs">
                                    <span class="material-symbols-outlined text-[26px]">family_restroom</span>
                                </div>

                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-on-surface">
                                        {{ $kk_file ? $kk_file->getClientOriginalName() : 'Pilih atau Tarik Foto KK' }}
                                    </span>
                                    <span class="text-[11px] text-on-surface-variant mt-0.5">JPG, PNG atau PDF maks. 3MB</span>
                                </div>

                                <span wire:loading wire:target="kk_file" class="text-xs text-primary font-bold flex items-center gap-1">
                                    <span class="animate-spin material-symbols-outlined text-[16px]">sync</span>
                                    <span>Mengunggah...</span>
                                </span>
                            </div>
                            @error('kk_file') <span class="text-xs text-error font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

            <!-- STEP 4: TINJAU & KIRIM -->
            @elseif ($currentStep === 4)
                <div class="flex flex-col gap-6 animate-fade-in">
                    <div class="flex items-center justify-between pb-2 border-b border-surface-container">
                        <div>
                            <span class="text-xs font-bold text-secondary uppercase tracking-wider">Langkah 4 dari 4</span>
                            <h2 class="text-lg font-bold text-on-surface">Tinjau Data Pengajuan</h2>
                        </div>
                        <span class="material-symbols-outlined text-primary text-[24px]">fact_check</span>
                    </div>

                    <p class="text-xs text-on-surface-variant">
                        Harap periksa kembali seluruh data dan kelengkapan dokumen Anda sebelum mengirim pengajuan ini ke petugas.
                    </p>

                    <!-- Summary Card 1: Tujuan -->
                    <div class="p-4 rounded-xl bg-surface-container-low border border-surface-container flex items-start justify-between gap-4">
                        <div class="flex flex-col gap-1">
                            <span class="text-[11px] font-bold text-on-surface-variant uppercase tracking-wider">Tujuan Penggunaan Surat</span>
                            <span class="text-sm font-bold text-primary">{{ $selectedPurpose?->name }}</span>
                            @if ($purpose_description)
                                <span class="text-xs text-on-surface-variant">Keterangan: {{ $purpose_description }}</span>
                            @endif
                        </div>
                        <button wire:click="goToStep(1)" type="button" class="text-xs font-bold text-secondary hover:underline flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">edit</span>
                            <span>Ubah</span>
                        </button>
                    </div>

                    <!-- Summary Card 2: Identitas -->
                    <div class="p-4 rounded-xl bg-surface-container-low border border-surface-container flex flex-col gap-3">
                        <div class="flex items-center justify-between border-b border-surface-container pb-2">
                            <span class="text-[11px] font-bold text-on-surface-variant uppercase tracking-wider">Identitas Pemohon & Subjek</span>
                            <button wire:click="goToStep(2)" type="button" class="text-xs font-bold text-secondary hover:underline flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">edit</span>
                                <span>Ubah</span>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                            <div><span class="text-on-surface-variant">Nama Pemohon:</span> <strong class="text-on-surface block">{{ $applicant_name }}</strong></div>
                            <div><span class="text-on-surface-variant">NIK Pemohon:</span> <span class="font-mono text-on-surface block">{{ $applicant_nik }}</span></div>
                            <div><span class="text-on-surface-variant">Nomor KK:</span> <span class="font-mono text-on-surface block">{{ $family_card_number }}</span></div>
                            <div><span class="text-on-surface-variant">No. WhatsApp:</span> <span class="text-on-surface block">{{ $phone }}</span></div>
                            <div><span class="text-on-surface-variant">Orang yang Diterangkan:</span> <strong class="text-on-surface block">{{ $subject_name }} ({{ $relationship_to_applicant }})</strong></div>
                            <div><span class="text-on-surface-variant">NIK yang Diterangkan:</span> <span class="font-mono text-on-surface block">{{ $subject_nik }}</span></div>
                            <div class="col-span-full"><span class="text-on-surface-variant">Alamat:</span> <span class="text-on-surface block">{{ $address }}</span></div>
                        </div>
                    </div>

                    <!-- Summary Card 3: Berkas -->
                    <div class="p-4 rounded-xl bg-surface-container-low border border-surface-container flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-tertiary text-[24px]">verified</span>
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-on-surface">2 Berkas Persyaratan Terunggah</span>
                                <span class="text-[11px] text-on-surface-variant">Foto KTP & Kartu Keluarga siap diverifikasi</span>
                            </div>
                        </div>
                        <button wire:click="goToStep(3)" type="button" class="text-xs font-bold text-secondary hover:underline flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">edit</span>
                            <span>Ubah</span>
                        </button>
                    </div>

                    <!-- Statement Checkbox -->
                    <div class="pt-2">
                        <label class="flex items-start gap-3 p-4 rounded-xl bg-surface-container border border-primary/20 cursor-pointer">
                            <input type="checkbox" wire:model="agreement_confirmed" class="mt-1 h-5 w-5 text-primary rounded focus:ring-primary" />
                            <span class="text-xs text-on-surface leading-relaxed">
                                <strong>Pernyataan Kebenaran Data:</strong> Saya menyatakan dengan sebenar-benarnya bahwa seluruh data, NIK, dan dokumen yang diunggah adalah benar, sah, dan dapat dipertanggungjawabkan sesuai hukum yang berlaku di Republik Indonesia.
                            </span>
                        </label>
                        @error('agreement_confirmed')
                            <span class="text-xs text-error font-medium mt-1.5 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            @endif

            <!-- Form Navigation Action Bar -->
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
                        <span>Kirim Permohonan DTSEN</span>
                    </button>
                @endif
            </div>
        </div>
    @endif
</div>
