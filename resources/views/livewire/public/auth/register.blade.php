<div class="max-w-md mx-auto px-4 py-10 flex flex-col items-center">
    <!-- Header Emblem & Branding -->
    <div class="flex flex-col items-center text-center mb-6">
        <div class="w-16 h-16 rounded-2xl bg-primary text-white flex items-center justify-center shadow-md shadow-primary/20 mb-3">
            <span class="material-symbols-outlined text-[32px]">diversity_3</span>
        </div>
        <span class="text-xs font-bold text-primary tracking-wider uppercase">Dinas Sosial Kabupaten Blitar</span>
        <h1 class="text-2xl font-extrabold text-on-surface tracking-tight mt-0.5">SAPA SOSIAL</h1>
        <p class="text-xs text-on-surface-variant max-w-xs mt-1">Pendaftaran Akun Warga Terpadu</p>
    </div>

    <!-- Tab Switching -->
    <div class="w-full bg-surface-container-high p-1 rounded-xl flex items-center mb-6 shadow-xs">
        <a href="{{ route('login') }}" class="flex-1 py-2 rounded-lg font-bold text-xs sm:text-sm text-on-surface-variant hover:text-primary flex items-center justify-center gap-1.5 transition-all text-center">
            <span class="material-symbols-outlined text-[18px]">lock_open</span>
            <span>Masuk Akun</span>
        </a>
        <a href="{{ route('register') }}" class="flex-1 py-2 rounded-lg font-bold text-xs sm:text-sm text-white bg-primary shadow-xs flex items-center justify-center gap-1.5 transition-all text-center">
            <span class="material-symbols-outlined text-[18px]">person_add</span>
            <span>Daftar Akun Baru</span>
        </a>
    </div>

    <!-- Register Box -->
    <div class="w-full bg-surface-container-lowest rounded-2xl p-6 sm:p-8 border border-surface-container-high shadow-sm flex flex-col gap-5">
        <div class="flex flex-col gap-1">
            <div class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-secondary-fixed text-on-secondary-fixed text-[11px] font-bold w-fit mb-1">
                <span class="material-symbols-outlined text-[14px]">how_to_reg</span>
                <span>Khusus KTP Kabupaten Blitar</span>
            </div>
            <h2 class="text-lg font-bold text-on-surface">Pendaftaran Akun Baru</h2>
            <p class="text-xs text-on-surface-variant">Daftarkan identitas Anda agar riwayat layanan sosial tersimpan rapi.</p>
        </div>

        <form wire:submit="register" class="flex flex-col gap-3.5">
            <div class="flex flex-col gap-1">
                <label class="text-xs font-bold text-on-surface">Nama Lengkap (Sesuai KTP) <span class="text-error">*</span></label>
                <input wire:model="name" type="text" placeholder="Nama Lengkap" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm border border-surface-container focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary" />
                @error('name') <span class="text-[11px] text-error font-medium">{{ $message }}</span> @enderror
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-xs font-bold text-on-surface">NIK (16 Digit Angka) <span class="text-error">*</span></label>
                <input wire:model="nik" type="text" maxlength="16" placeholder="3505xxxxxxxxxxxx" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm font-mono border border-surface-container focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary" />
                @error('nik') <span class="text-[11px] text-error font-medium">{{ $message }}</span> @enderror
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-xs font-bold text-on-surface">No. WhatsApp / HP Aktif <span class="text-error">*</span></label>
                <input wire:model="phone" type="tel" placeholder="08xxxxxxxxxx" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm border border-surface-container focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary" />
                @error('phone') <span class="text-[11px] text-error font-medium">{{ $message }}</span> @enderror
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-xs font-bold text-on-surface">Alamat Email <span class="text-error">*</span></label>
                <input wire:model="email" type="email" placeholder="nama@email.com" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm border border-surface-container focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary" />
                @error('email') <span class="text-[11px] text-error font-medium">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-2.5">
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-bold text-on-surface">Kecamatan <span class="text-error">*</span></label>
                    <select wire:model.live="district_id" class="w-full h-11 px-3 rounded-xl bg-surface-container-low text-on-surface text-xs border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">Pilih</option>
                        @foreach ($districts as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                    @error('district_id') <span class="text-[10px] text-error">{{ $message }}</span> @enderror
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-xs font-bold text-on-surface">Desa/Kelurahan <span class="text-error">*</span></label>
                    <select wire:model="village_id" class="w-full h-11 px-3 rounded-xl bg-surface-container-low text-on-surface text-xs border border-surface-container focus:outline-none focus:ring-2 focus:ring-primary" {{ empty($district_id) ? 'disabled' : '' }}>
                        <option value="">Pilih</option>
                        @foreach ($villages as $v)
                            <option value="{{ $v->id }}">{{ $v->name }}</option>
                        @endforeach
                    </select>
                    @error('village_id') <span class="text-[10px] text-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-xs font-bold text-on-surface">Kata Sandi (Min. 6 Karakter) <span class="text-error">*</span></label>
                <input wire:model="password" type="password" placeholder="Minimal 6 karakter" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm border border-surface-container focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary" />
                @error('password') <span class="text-[11px] text-error font-medium">{{ $message }}</span> @enderror
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-xs font-bold text-on-surface">Ulangi Kata Sandi <span class="text-error">*</span></label>
                <input wire:model="password_confirmation" type="password" placeholder="Ketik ulang kata sandi" class="w-full h-11 px-3.5 rounded-xl bg-surface-container-low text-on-surface text-sm border border-surface-container focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary" />
            </div>

            <button type="submit" 
                    wire:loading.attr="disabled"
                    class="w-full min-h-[48px] bg-primary hover:bg-primary-container text-white rounded-xl font-bold text-sm shadow-md transition-all cursor-pointer flex items-center justify-center gap-2 active:scale-95 mt-2">
                <span class="material-symbols-outlined text-[20px]" wire:loading.remove wire:target="register">how_to_reg</span>
                <span class="animate-spin material-symbols-outlined text-[20px]" wire:loading wire:target="register">sync</span>
                <span>Daftar Akun Sekarang</span>
            </button>
        </form>

        <p class="text-center text-xs text-on-surface-variant">
            Sudah memiliki akun? <a href="{{ route('login') }}" class="font-bold text-primary hover:underline">Masuk di sini</a>
        </p>
    </div>
</div>
