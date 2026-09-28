<div class="max-w-md mx-auto px-4 py-12 flex flex-col items-center">
    <!-- Header Emblem & Branding -->
    <div class="flex flex-col items-center text-center mb-6">
        <div class="w-16 h-16 rounded-2xl bg-primary text-white flex items-center justify-center shadow-md shadow-primary/20 mb-3">
            <span class="material-symbols-outlined text-[32px]">diversity_3</span>
        </div>
        <span class="text-xs font-bold text-primary tracking-wider uppercase">Dinas Sosial Kabupaten Blitar</span>
        <h1 class="text-2xl font-extrabold text-on-surface tracking-tight mt-0.5">SAPA SOSIAL</h1>
        <p class="text-xs text-on-surface-variant max-w-xs mt-1">Satu Pintu Layanan & Bantuan Sosial Terpadu Warga Blitar</p>
    </div>

    <!-- Tab Switching -->
    <div class="w-full bg-surface-container-high p-1 rounded-xl flex items-center mb-6 shadow-xs">
        <a href="{{ route('login') }}" class="flex-1 py-2 rounded-lg font-bold text-xs sm:text-sm text-white bg-primary shadow-xs flex items-center justify-center gap-1.5 transition-all text-center">
            <span class="material-symbols-outlined text-[18px]">lock_open</span>
            <span>Masuk Akun</span>
        </a>
        <a href="{{ route('register') }}" class="flex-1 py-2 rounded-lg font-bold text-xs sm:text-sm text-on-surface-variant hover:text-primary flex items-center justify-center gap-1.5 transition-all text-center">
            <span class="material-symbols-outlined text-[18px]">person_add</span>
            <span>Daftar Akun Baru</span>
        </a>
    </div>

    <!-- Login Box -->
    <div class="w-full bg-surface-container-lowest rounded-2xl p-6 sm:p-8 border border-surface-container-high shadow-sm flex flex-col gap-5">
        <div class="flex flex-col gap-1">
            <h2 class="text-lg font-bold text-on-surface">Masuk ke Akun Anda</h2>
            <p class="text-xs text-on-surface-variant">Akses riwayat pengajuan dokumen dan pantau tiket permohonan Anda.</p>
        </div>

        @if ($errorMessage)
            <div class="p-3.5 rounded-xl bg-error-container/40 text-on-error-container text-xs flex items-center gap-2 animate-fade-in">
                <span class="material-symbols-outlined text-error text-[18px]">error</span>
                <span>{{ $errorMessage }}</span>
            </div>
        @endif

        <form wire:submit="login" class="flex flex-col gap-4" x-data="{ showPwd: false }">
            <div class="flex flex-col gap-1.5">
                <label for="identifier" class="text-xs font-bold text-on-surface">Nomor WhatsApp, NIK, atau Email <span class="text-error">*</span></label>
                <div class="relative flex items-center">
                    <span class="material-symbols-outlined absolute left-3.5 text-outline text-[20px]">badge</span>
                    <input wire:model="identifier" 
                           type="text" 
                           id="identifier"
                           placeholder="Contoh: 0812... / 3505... / email" 
                           class="w-full h-12 pl-11 pr-4 rounded-xl bg-surface-container-low text-on-surface text-sm border border-surface-container focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary transition-all" />
                </div>
                @error('identifier') <span class="text-[11px] text-error font-medium">{{ $message }}</span> @enderror
            </div>

            <div class="flex flex-col gap-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="text-xs font-bold text-on-surface">Kata Sandi <span class="text-error">*</span></label>
                </div>
                <div class="relative flex items-center">
                    <span class="material-symbols-outlined absolute left-3.5 text-outline text-[20px]">key</span>
                    <input wire:model="password" 
                           :type="showPwd ? 'text' : 'password'" 
                           id="password"
                           placeholder="Masukkan kata sandi akun" 
                           class="w-full h-12 pl-11 pr-11 rounded-xl bg-surface-container-low text-on-surface text-sm border border-surface-container focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary transition-all" />
                    <button type="button" @click="showPwd = !showPwd" class="absolute right-3 text-outline hover:text-on-surface p-1">
                        <span class="material-symbols-outlined text-[18px]" x-show="!showPwd">visibility</span>
                        <span class="material-symbols-outlined text-[18px]" x-show="showPwd" style="display: none;">visibility_off</span>
                    </button>
                </div>
                @error('password') <span class="text-[11px] text-error font-medium">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="remember" class="rounded text-primary focus:ring-primary h-4 w-4" />
                    <span class="text-xs text-on-surface-variant">Ingat saya di perangkat ini</span>
                </label>
            </div>

            <button type="submit" 
                    wire:loading.attr="disabled"
                    class="w-full min-h-[48px] bg-primary hover:bg-primary-container text-white rounded-xl font-bold text-sm shadow-md transition-all cursor-pointer flex items-center justify-center gap-2 active:scale-95 mt-1">
                <span class="material-symbols-outlined text-[20px]" wire:loading.remove wire:target="login">login</span>
                <span class="animate-spin material-symbols-outlined text-[20px]" wire:loading wire:target="login">sync</span>
                <span>Masuk ke Akun Saya</span>
            </button>
        </form>

        <div class="relative my-2 flex items-center justify-center">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-surface-container"></div></div>
            <span class="relative px-3 bg-surface-container-lowest text-[11px] text-outline uppercase tracking-wider">Belum Punya Akun?</span>
        </div>

        <a href="{{ route('register') }}" class="w-full min-h-[44px] rounded-xl border border-primary text-primary font-bold text-xs flex items-center justify-center gap-1.5 hover:bg-surface-container transition-colors">
            <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
            <span>Daftar Akun Warga Baru</span>
        </a>
    </div>
</div>
