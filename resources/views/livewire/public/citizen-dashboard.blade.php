<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col gap-6" x-data="{ copied: false }">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs font-medium text-on-surface-variant">
        <a href="{{ route('home') }}" class="hover:text-primary transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-[15px]">home</span>
            <span>Beranda</span>
        </a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-primary font-bold">Akun Saya</span>
    </nav>

    <!-- Profile Greeting Card -->
    <div class="bg-surface-container-lowest rounded-2xl p-6 sm:p-8 border border-surface-container-high shadow-xs flex flex-col gap-5 relative overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-16 h-16 rounded-2xl bg-primary text-white flex items-center justify-center font-extrabold text-2xl shrink-0 shadow-md shadow-primary/20">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="flex flex-col">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-surface-container text-primary flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">shield_person</span>
                            <span>Warga Terdaftar</span>
                        </span>
                        <span class="text-xs text-tertiary font-bold flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">verified</span>
                            <span>KTP Terverifikasi</span>
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-on-surface tracking-tight mt-1">{{ $user->name }}</h1>
                    <div class="flex items-center gap-3 text-xs text-on-surface-variant mt-1 flex-wrap">
                        @if ($user->nik)
                            <span class="flex items-center gap-1 font-mono">
                                <span class="material-symbols-outlined text-[15px]">badge</span>
                                <span>NIK: {{ $user->nik }}</span>
                            </span>
                        @endif
                        @if ($user->phone)
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px]">call</span>
                                <span>{{ $user->phone }}</span>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Form Logout -->
            <form method="POST" action="{{ route('logout') }}" class="self-start sm:self-center">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-error bg-error-container/20 hover:bg-error-container/40 transition-colors flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">logout</span>
                    <span>Keluar Akun</span>
                </button>
            </form>
        </div>

        <!-- Domicile info strip -->
        <div class="p-3.5 rounded-xl bg-surface-container-low border border-surface-container flex items-center gap-2 text-xs text-on-surface-variant">
            <span class="material-symbols-outlined text-primary text-[18px]">location_on</span>
            <span>Domisili: <strong>{{ $user->village?->name ?? 'Desa' }}, Kec. {{ $user->district?->name ?? 'Kabupaten Blitar' }}</strong></span>
        </div>

        <!-- Quick CTAs -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
            <a href="{{ route('services.index') }}" class="min-h-[48px] px-4 rounded-xl bg-primary text-white font-bold text-xs sm:text-sm shadow-sm hover:bg-primary-container transition-all flex items-center justify-center gap-2 active:scale-95">
                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                <span>Ajukan Layanan Baru</span>
            </a>
            <a href="{{ route('complaints.create') }}" class="min-h-[48px] px-4 rounded-xl bg-secondary-container text-on-secondary-container font-bold text-xs sm:text-sm shadow-sm hover:brightness-95 transition-all flex items-center justify-center gap-2 active:scale-95">
                <span class="material-symbols-outlined text-[20px]">campaign</span>
                <span>Sampaikan Pengaduan</span>
            </a>
        </div>
    </div>

    <!-- Active Metrics -->
    <div class="grid grid-cols-3 gap-3 sm:gap-4">
        <div class="bg-surface-container-lowest p-4 rounded-2xl border border-surface-container-high shadow-xs flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[22px]">folder</span>
            </div>
            <div class="flex flex-col">
                <span class="text-xl sm:text-2xl font-extrabold text-on-surface leading-tight">{{ $totalCount }}</span>
                <span class="text-[11px] sm:text-xs text-on-surface-variant">Total Berkas</span>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-4 rounded-2xl border border-surface-container-high shadow-xs flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-3">
            <div class="w-10 h-10 rounded-xl bg-secondary-fixed/50 text-secondary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[22px]">pending_actions</span>
            </div>
            <div class="flex flex-col">
                <span class="text-xl sm:text-2xl font-extrabold text-secondary leading-tight">{{ $inProcessCount }}</span>
                <span class="text-[11px] sm:text-xs text-on-surface-variant">Dalam Proses</span>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-4 rounded-2xl border border-surface-container-high shadow-xs flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-3">
            <div class="w-10 h-10 rounded-xl bg-tertiary-fixed/60 text-tertiary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[22px]">task_alt</span>
            </div>
            <div class="flex flex-col">
                <span class="text-xl sm:text-2xl font-extrabold text-tertiary leading-tight">{{ $completedCount }}</span>
                <span class="text-[11px] sm:text-xs text-on-surface-variant">Telah Selesai</span>
            </div>
        </div>
    </div>

    <!-- Submissions History Section -->
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-on-surface">Riwayat & Perkembangan Pengajuan</h2>
        </div>

        <!-- Segmented Tab Bar -->
        <div class="p-1 bg-surface-container-high rounded-xl flex items-center gap-1 shadow-xs">
            <button wire:click="selectTab('all')" 
                    type="button" 
                    class="flex-1 py-2 rounded-lg font-bold text-xs sm:text-sm transition-all text-center cursor-pointer {{ $activeTab === 'all' ? 'bg-surface-container-lowest text-primary shadow-xs' : 'text-on-surface-variant hover:text-on-surface' }}">
                Semua ({{ $totalCount }})
            </button>
            <button wire:click="selectTab('in_process')" 
                    type="button" 
                    class="flex-1 py-2 rounded-lg font-bold text-xs sm:text-sm transition-all text-center cursor-pointer {{ $activeTab === 'in_process' ? 'bg-surface-container-lowest text-primary shadow-xs' : 'text-on-surface-variant hover:text-on-surface' }}">
                Dalam Proses ({{ $inProcessCount }})
            </button>
            <button wire:click="selectTab('completed')" 
                    type="button" 
                    class="flex-1 py-2 rounded-lg font-bold text-xs sm:text-sm transition-all text-center cursor-pointer {{ $activeTab === 'completed' ? 'bg-surface-container-lowest text-primary shadow-xs' : 'text-on-surface-variant hover:text-on-surface' }}">
                Selesai ({{ $completedCount }})
            </button>
        </div>

        <!-- List of Submissions -->
        <div class="flex flex-col gap-3">
            @forelse ($items as $item)
                <div class="bg-surface-container-lowest rounded-2xl p-4 sm:p-5 border border-surface-container-high shadow-xs hover:shadow-md transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 {{ $item['type'] === 'request' ? 'bg-primary/10 text-primary' : 'bg-secondary/10 text-secondary' }}">
                            <span class="material-symbols-outlined text-[24px]">
                                {{ $item['type'] === 'request' ? 'description' : 'campaign' }}
                            </span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-mono font-bold text-xs sm:text-sm text-primary">{{ $item['number'] }}</span>
                                <span class="px-2 py-0.2 rounded-full text-[10px] font-bold bg-surface-container text-on-surface-variant">
                                    {{ $item['category'] }}
                                </span>
                            </div>
                            <h3 class="text-sm sm:text-base font-bold text-on-surface leading-snug">{{ $item['title'] }}</h3>
                            <span class="text-[11px] text-on-surface-variant">Diajukan: {{ $item['date'] }}</span>
                            @if ($item['notes'])
                                <p class="text-xs text-on-surface-variant mt-1 bg-surface-container-low p-2 rounded-lg border border-surface-container">
                                    Catatan: {{ $item['notes'] }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center sm:flex-col sm:items-end justify-between sm:justify-center gap-2 pt-2 sm:pt-0 border-t sm:border-0 border-surface-container">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $item['is_completed'] ? 'bg-tertiary text-white' : 'bg-primary text-white' }}">
                            {{ $item['status_label'] }}
                        </span>
                        <a href="{{ route('tracking') }}?tiket={{ urlencode($item['number']) }}" class="px-3.5 py-1.5 rounded-lg bg-surface-container hover:bg-surface-container-high text-primary font-bold text-xs transition-colors flex items-center gap-1">
                            <span>Lacak</span>
                            <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-surface-container-lowest rounded-2xl p-8 border border-surface-container-high text-center flex flex-col items-center justify-center gap-3">
                    <div class="w-14 h-14 rounded-full bg-surface-container flex items-center justify-center text-outline">
                        <span class="material-symbols-outlined text-[32px]">folder_open</span>
                    </div>
                    <span class="font-bold text-sm text-on-surface">Belum ada riwayat berkas</span>
                    <p class="text-xs text-on-surface-variant max-w-sm">Anda belum pernah mengajukan layanan atau pengaduan sosial. Klik tombol di bawah untuk memulai.</p>
                    <div class="flex items-center gap-2 mt-2">
                        <a href="{{ route('services.index') }}" class="px-4 py-2 rounded-xl bg-primary text-white font-bold text-xs hover:bg-primary-container transition-colors">
                            Ajukan Layanan
                        </a>
                        <a href="{{ route('complaints.create') }}" class="px-4 py-2 rounded-xl bg-surface-container text-on-surface font-bold text-xs hover:bg-surface-container-high transition-colors">
                            Pengaduan
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
