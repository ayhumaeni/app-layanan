<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col gap-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs font-medium text-on-surface-variant">
        <a href="{{ route('home') }}" class="hover:text-primary transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-[15px]">home</span>
            <span>Beranda</span>
        </a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-primary font-bold">Pengaduan Sosial Warga</span>
    </nav>

    <!-- SUCCESS SCREEN -->
    @if ($isSuccess)
        <div class="bg-surface-container-lowest rounded-2xl p-6 sm:p-10 border border-surface-container-high shadow-md flex flex-col items-center text-center gap-6 animate-fade-in" x-data="{ copied: false }">
            <div class="w-20 h-20 rounded-full bg-secondary-container/20 text-secondary flex items-center justify-center shadow-xs">
                <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 1;">campaign</span>
            </div>

            <div class="flex flex-col gap-1 max-w-lg">
                <span class="text-xs font-bold text-secondary uppercase tracking-wider">Laporan Diterima</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-on-surface tracking-tight">Pengaduan Berhasil Terkirim!</h1>
                <p class="text-xs sm:text-sm text-on-surface-variant leading-relaxed">
                    Terima kasih atas kepedulian Anda. Laporan permasalahan sosial Anda telah tercatat dan segera didisposisikan kepada tim pelayanan sosial Dinas Sosial Kab. Blitar.
                </p>
            </div>

            <!-- Big Copyable Ticket Box -->
            <div class="w-full max-w-md bg-surface-container-low rounded-2xl p-5 border border-secondary/20 flex flex-col items-center gap-3">
                <span class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Nomor Tiket Laporan Anda</span>
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
                <span class="text-[11px] text-on-surface-variant" x-show="!copied">Catat nomor tiket ini untuk melihat perkembangan verifikasi dan penanganan oleh petugas.</span>
                <span class="text-[11px] text-tertiary font-bold" x-show="copied" style="display: none;">Nomor tiket berhasil disalin!</span>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3 w-full max-w-md pt-2">
                <a href="{{ route('tracking') }}?tiket={{ urlencode($submittedTicket) }}" class="w-full min-h-[48px] rounded-xl bg-primary text-white font-bold text-sm shadow-md hover:bg-primary-container transition-all flex items-center justify-center gap-2 active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">timeline</span>
                    <span>Lacak Penanganan Laporan</span>
                </a>
                <a href="{{ route('home') }}" class="w-full min-h-[48px] rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface font-bold text-sm transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">home</span>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>

    @else
        <!-- Header Title & Callout -->
        <div class="flex flex-col gap-2">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-primary tracking-tight">
                Formulir Pengaduan & Laporan Masalah Sosial
            </h1>
            <p class="text-xs sm:text-sm text-on-surface-variant">
                Layanan pengaduan masyarakat untuk perlindungan sosial, lansia terlantar, ODGJ, disabilitas, atau kendala bansos di Kabupaten Blitar
            </p>
        </div>

        <div class="bg-surface-container-high/60 rounded-2xl p-4 sm:p-5 border border-primary/10 flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-secondary text-white flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[22px]">privacy_tip</span>
            </div>
            <div class="flex-1 text-xs sm:text-sm">
                <strong class="text-primary font-bold block mb-0.5">Kerahasiaan & Privasi Pelapor Terjaga</strong>
                <p class="text-on-surface leading-relaxed">
                    Identitas pelapor dilindungi dan hanya digunakan oleh tim penelaah Dinas Sosial untuk verifikasi dan konfirmasi penanganan lapangan.
                </p>
            </div>
        </div>

        <!-- Main Form -->
        <form wire:submit="submit" class="bg-surface-container-lowest rounded-2xl p-6 sm:p-8 border border-surface-container-high shadow-xs flex flex-col gap-6">
            <!-- Section 1: Kategori -->
            <div class="flex flex-col gap-3">
                <div class="flex items-center gap-2 text-primary font-bold text-sm sm:text-base border-b border-surface-container pb-2">
                    <span class="material-symbols-outlined text-[20px]">category</span>
                    <h2>1. Kategori Permasalahan Sosial <span class="text-error">*</span></h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    @foreach ($categories as $cat)
                        <label class="p-3.5 rounded-xl border-2 flex items-start gap-3 cursor-pointer transition-all {{ $complaint_category_id === $cat->id ? 'border-primary bg-primary/5 shadow-xs' : 'border-surface-container hover:border-outline-variant' }}">
                            <input type="radio" wire:model.live="complaint_category_id" value="{{ $cat->id }}" class="mt-0.5 text-primary focus:ring-primary h-4 w-4" />
                            <span class="text-xs font-semibold text-on-surface leading-snug">{{ $cat->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('complaint_category_id') <span class="text-xs text-error font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Section 2: Lokasi Kejadian -->
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-2 text-primary font-bold text-sm sm:text-base border-b border-surface-container pb-2">
                    <span class="material-symbols-outlined text-[20px]">location_on</span>
                    <h2>2. Lokasi Permasalahan / Kejadian <span class="text-error">*</span></h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold text-on-surface">Kecamatan <span class="text-error">*</span></label>
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
                        <label class="text-xs font-bold text-on-surface">Patokan / Keterangan Lokasi Rinci (Opsional)</label>
                        <input wire:model="location_detail" type="text" placeholder="Contoh: Depan Musholla Al-Ikhlas, RT 03 RW 02 Dusun Kebonagung" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary" />
                    </div>
                </div>
            </div>

            <!-- Section 3: Deskripsi Permasalahan -->
            <div class="flex flex-col gap-3">
                <div class="flex items-center gap-2 text-primary font-bold text-sm sm:text-base border-b border-surface-container pb-2">
                    <span class="material-symbols-outlined text-[20px]">description</span>
                    <h2>3. Penjelasan Permasalahan <span class="text-error">*</span></h2>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-xs font-bold text-on-surface">Uraian / Kronologi Masalah</label>
                    <textarea wire:model.live.debounce.300ms="description" rows="4" placeholder="Jelaskan secara jelas siapa yang memerlukan bantuan, kondisi yang terjadi, dan bentuk bantuan yang mendesak..." class="w-full p-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary leading-relaxed"></textarea>
                    <div class="flex items-center justify-between text-[11px] text-on-surface-variant px-1 mt-0.5">
                        <span>Minimal 20 karakter agar jelas bagi petugas lapangan</span>
                        <span>{{ strlen($description) }} Karakter</span>
                    </div>
                    @error('description') <span class="text-xs text-error font-medium">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Section 4: Unggah Foto/Dokumen Bukti -->
            <div class="flex flex-col gap-3">
                <div class="flex items-center justify-between border-b border-surface-container pb-2">
                    <div class="flex items-center gap-2 text-primary font-bold text-sm sm:text-base">
                        <span class="material-symbols-outlined text-[20px]">photo_camera</span>
                        <h2>4. Foto / Bukti Pendukung (Opsional)</h2>
                    </div>
                    <span class="text-xs text-on-surface-variant">Maks. 4 File (Foto/PDF)</span>
                </div>

                <div class="relative border-2 border-dashed border-surface-container hover:border-primary bg-surface-container-low rounded-2xl p-5 text-center flex flex-col items-center justify-center gap-2 transition-all">
                    <input type="file" wire:model="attachments" multiple accept=".jpg,.jpeg,.png,.webp,.pdf" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" />
                    <span class="material-symbols-outlined text-primary text-[28px]">add_photo_alternate</span>
                    <span class="text-xs font-bold text-on-surface">Pilih Foto atau Dokumen Pendukung</span>
                    <span class="text-[11px] text-on-surface-variant">Format JPG, PNG, atau PDF (maks. 4MB per berkas)</span>
                    <span wire:loading wire:target="attachments" class="text-xs text-primary font-bold flex items-center gap-1">
                        <span class="animate-spin material-symbols-outlined text-[16px]">sync</span>
                        <span>Mengunggah...</span>
                    </span>
                </div>

                @if (!empty($attachments))
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 pt-1">
                        @foreach ($attachments as $index => $att)
                            <div class="p-2.5 rounded-xl bg-surface-container flex items-center justify-between gap-2 border border-surface-container-high">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="material-symbols-outlined text-[18px] text-primary shrink-0">attachment</span>
                                    <span class="text-[11px] font-semibold text-on-surface truncate">{{ $att->getClientOriginalName() }}</span>
                                </div>
                                <button type="button" wire:click="removeAttachment({{ $index }})" class="text-error hover:bg-error-container/30 p-1 rounded-lg transition-colors" title="Hapus">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
                @error('attachments.*') <span class="text-xs text-error font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Section 5: Data Pelapor -->
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-2 text-primary font-bold text-sm sm:text-base border-b border-surface-container pb-2">
                    <span class="material-symbols-outlined text-[20px]">person</span>
                    <h2>5. Identitas Pelapor <span class="text-error">*</span></h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold text-on-surface">Nama Lengkap Pelapor <span class="text-error">*</span></label>
                        <input wire:model="reporter_name" type="text" placeholder="Nama Anda" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary" />
                        @error('reporter_name') <span class="text-[11px] text-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold text-on-surface">Nomor WhatsApp / HP Aktif <span class="text-error">*</span></label>
                        <input wire:model="reporter_phone" type="tel" placeholder="08xxxxxxxxxx" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary" />
                        @error('reporter_phone') <span class="text-[11px] text-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Action -->
            <div class="pt-4 border-t border-surface-container flex items-center justify-between">
                <a href="{{ route('home') }}" class="min-h-[46px] px-5 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface font-bold text-xs flex items-center gap-1.5 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    <span>Kembali</span>
                </a>

                <button type="submit" wire:loading.attr="disabled" class="min-h-[48px] px-8 rounded-xl bg-secondary-container hover:brightness-95 text-on-secondary-container font-extrabold text-sm shadow-md transition-all cursor-pointer flex items-center gap-2 active:scale-95">
                    <span class="material-symbols-outlined text-[20px]" wire:loading.remove wire:target="submit">send</span>
                    <span class="animate-spin material-symbols-outlined text-[20px]" wire:loading wire:target="submit">sync</span>
                    <span>Kirim Laporan Pengaduan</span>
                </button>
            </div>
        </form>
    @endif
</div>
