<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="theme-color" content="#00445c">
    <meta name="description" content="SAPA SOSIAL - Satu Pintu Layanan Sosial Kabupaten Blitar. Layanan penerbitan SK DTSEN, Reaktivasi KIS/PBI-JK, Rehabilitasi Sosial, dan Pengaduan Warga.">
    <meta name="keywords" content="Sapa Sosial, Dinsos Blitar, DTSEN, KIS PBI-JK, Bantuan Sosial, Pengaduan Sosial Kabupaten Blitar">

    <title>{{ $title ?? 'SAPA SOSIAL — Satu Pintu Layanan Sosial Kabupaten Blitar' }}</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Google Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>
<body class="bg-surface font-sans text-on-surface antialiased flex flex-col min-h-screen selection:bg-primary-container selection:text-white" x-data="{ mobileMenuOpen: false }">

    <!-- Top Citizen Notice & Emergency Hotline Bar -->
    <div class="bg-secondary-container text-on-secondary-container text-xs md:text-sm font-medium py-1.5 px-4 shadow-xs relative z-50">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-2">
            <div class="flex items-center gap-2 overflow-hidden truncate">
                <span class="material-symbols-outlined text-[18px] text-secondary shrink-0" style="font-variation-settings: 'FILL' 1;">verified</span>
                <span class="truncate">Seluruh layanan publik Dinas Sosial Kab. Blitar <strong>100% Bebas Biaya (Gratis)</strong>. Laporkan jika ada pungli!</span>
            </div>
            <a href="https://wa.me/6281234567890" target="_blank" rel="noopener noreferrer" class="shrink-0 flex items-center gap-1.5 bg-white/90 hover:bg-white text-secondary font-bold px-2.5 py-0.5 rounded-full text-xs shadow-xs transition-colors">
                <span class="material-symbols-outlined text-[15px]">headset_mic</span>
                <span>Hotline Dinsos</span>
            </a>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="sticky top-0 z-40 bg-surface/90 backdrop-blur-md border-b border-surface-container-high transition-shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-18">
                <!-- Branding -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 group focus:outline-none focus:ring-2 focus:ring-primary rounded-xl p-1">
                    <div class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center shadow-md shadow-primary/20 group-hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined text-[24px]">diversity_3</span>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1.5">
                            <span class="text-lg font-extrabold text-primary tracking-tight leading-none">SAPA SOSIAL</span>
                            <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-primary-container text-white leading-tight">BLITAR</span>
                        </div>
                        <span class="text-xs text-on-surface-variant font-medium leading-tight">Dinas Sosial Kabupaten Blitar</span>
                    </div>
                </a>

                <!-- Desktop Nav Links -->
                <nav class="hidden lg:flex items-center gap-1">
                    <a href="{{ route('home') }}" class="px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('home') ? 'bg-primary-container text-white shadow-xs' : 'text-on-surface hover:text-primary hover:bg-surface-container' }}">
                        Beranda
                    </a>
                    <a href="{{ route('services.index') }}" class="px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('services.*') ? 'bg-primary-container text-white shadow-xs' : 'text-on-surface hover:text-primary hover:bg-surface-container' }}">
                        Layanan & Informasi
                    </a>
                    <a href="{{ route('complaints.create') }}" class="px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('complaints.*') ? 'bg-primary-container text-white shadow-xs' : 'text-on-surface hover:text-primary hover:bg-surface-container' }}">
                        Pengaduan Warga
                    </a>
                    <a href="{{ route('tracking') }}" class="px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('tracking') ? 'bg-primary-container text-white shadow-xs' : 'text-on-surface hover:text-primary hover:bg-surface-container' }}">
                        Cek Status Tiket
                    </a>
                    <a href="{{ route('certificate.verify') }}" class="px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('certificate.verify') ? 'bg-primary-container text-white shadow-xs' : 'text-on-surface hover:text-primary hover:bg-surface-container' }}">
                        Verifikasi Surat
                    </a>
                </nav>

                <!-- Auth / Citizen Actions -->
                <div class="hidden sm:flex items-center gap-2">
                    @auth
                        <div class="flex items-center gap-2">
                            <a href="{{ route('citizen.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl bg-surface-container hover:bg-surface-container-high text-primary font-bold text-sm transition-colors border border-outline-variant/30">
                                <span class="material-symbols-outlined text-[18px]">account_circle</span>
                                <span class="max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="p-2 rounded-xl text-on-surface-variant hover:text-error hover:bg-error-container/20 transition-colors" title="Keluar">
                                    <span class="material-symbols-outlined text-[20px]">logout</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="h-10 px-4 rounded-xl text-sm font-bold text-primary hover:bg-surface-container transition-colors flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px]">login</span>
                            <span>Masuk</span>
                        </a>
                        <a href="{{ route('register') }}" class="h-10 px-4 rounded-xl text-sm font-bold bg-primary text-white hover:bg-primary-container shadow-xs hover:shadow transition-all flex items-center gap-1.5 active:scale-95">
                            <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                            <span>Daftar Akun</span>
                        </a>
                    @endauth
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="flex sm:hidden items-center gap-2">
                    @auth
                        <a href="{{ route('citizen.dashboard') }}" class="w-9 h-9 rounded-full bg-primary-container text-white flex items-center justify-center shadow-xs">
                            <span class="material-symbols-outlined text-[20px]">person</span>
                        </a>
                    @endauth
                    <button @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Buka Menu" class="p-2 rounded-xl text-on-surface hover:bg-surface-container transition-colors focus:outline-none focus:ring-2 focus:ring-primary">
                        <span class="material-symbols-outlined text-[26px]" x-show="!mobileMenuOpen">menu</span>
                        <span class="material-symbols-outlined text-[26px]" x-show="mobileMenuOpen" style="display: none;">close</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Dropdown Menu -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0 -translate-y-2" 
             x-transition:enter-end="opacity-100 translate-y-0" 
             x-transition:leave="transition ease-in duration-150" 
             x-transition:leave-start="opacity-100 translate-y-0" 
             x-transition:leave-end="opacity-0 -translate-y-2" 
             class="lg:hidden border-t border-surface-container-high bg-surface px-4 pt-3 pb-6 space-y-2 shadow-lg" 
             style="display: none;">
            <a href="{{ route('home') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm {{ request()->routeIs('home') ? 'bg-primary-container text-white' : 'text-on-surface hover:bg-surface-container' }}">
                <span class="material-symbols-outlined text-[20px]">home</span>
                <span>Beranda</span>
            </a>
            <a href="{{ route('services.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm {{ request()->routeIs('services.*') ? 'bg-primary-container text-white' : 'text-on-surface hover:bg-surface-container' }}">
                <span class="material-symbols-outlined text-[20px]">dataset</span>
                <span>Layanan & Informasi</span>
            </a>
            <a href="{{ route('complaints.create') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm {{ request()->routeIs('complaints.*') ? 'bg-primary-container text-white' : 'text-on-surface hover:bg-surface-container' }}">
                <span class="material-symbols-outlined text-[20px]">record_voice_over</span>
                <span>Pengaduan Warga</span>
            </a>
            <a href="{{ route('tracking') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm {{ request()->routeIs('tracking') ? 'bg-primary-container text-white' : 'text-on-surface hover:bg-surface-container' }}">
                <span class="material-symbols-outlined text-[20px]">timeline</span>
                <span>Cek Status Tiket</span>
            </a>
            <a href="{{ route('certificate.verify') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm {{ request()->routeIs('certificate.verify') ? 'bg-primary-container text-white' : 'text-on-surface hover:bg-surface-container' }}">
                <span class="material-symbols-outlined text-[20px]">verified</span>
                <span>Verifikasi Keaslian Surat DTSEN</span>
            </a>

            <div class="pt-3 border-t border-surface-container-high mt-3 space-y-2">
                @auth
                    <a href="{{ route('citizen.dashboard') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-surface-container font-bold text-sm text-primary">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">account_circle</span>
                            <span>Akun Saya ({{ auth()->user()->name }})</span>
                        </span>
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-xl text-error bg-error-container/20 font-bold text-sm">
                            <span class="material-symbols-outlined text-[20px]">logout</span>
                            <span>Keluar dari Akun</span>
                        </button>
                    </form>
                @else
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('login') }}" class="flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl border border-primary text-primary font-bold text-sm">
                            <span class="material-symbols-outlined text-[18px]">login</span>
                            <span>Masuk</span>
                        </a>
                        <a href="{{ route('register') }}" class="flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl bg-primary text-white font-bold text-sm">
                            <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                            <span>Daftar</span>
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 w-full">
        {{ $slot }}
    </main>

    <!-- Global Footer -->
    <footer class="bg-inverse-surface text-inverse-on-surface mt-16 pt-12 pb-8 border-t border-inverse-surface/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 pb-10 border-b border-surface-variant/10">
                <!-- Col 1: Identity -->
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-primary-container text-white flex items-center justify-center shadow-sm">
                            <span class="material-symbols-outlined text-[22px]">diversity_3</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-extrabold text-white text-base tracking-wide">SAPA SOSIAL</span>
                            <span class="text-xs text-inverse-on-surface/70">Dinas Sosial Kabupaten Blitar</span>
                        </div>
                    </div>
                    <p class="text-sm text-inverse-on-surface/80 leading-relaxed mt-1">
                        Satu Pintu Layanan Sosial Terpadu Pemerintah Kabupaten Blitar. Melayani masyarakat dengan ramah, transparan, dan akuntabel.
                    </p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-tertiary-container text-white text-xs font-semibold">
                            <span class="w-2 h-2 rounded-full bg-tertiary-fixed animate-ping"></span>
                            <span>Layanan Aktif</span>
                        </span>
                        <span class="text-xs text-inverse-on-surface/60">Tersinkronisasi SIKS-NG</span>
                    </div>
                </div>

                <!-- Col 2: Quick Links -->
                <div class="flex flex-col gap-2">
                    <h3 class="font-bold text-white text-sm uppercase tracking-wider mb-2">Layanan Utama</h3>
                    <ul class="space-y-2 text-sm text-inverse-on-surface/80">
                        <li><a href="{{ route('services.dtsen.apply') }}" class="hover:text-primary-fixed transition-colors flex items-center gap-1.5"><span class="material-symbols-outlined text-[15px]">arrow_right</span>Surat Keterangan DTSEN</a></li>
                        <li><a href="{{ route('services.kis.apply') }}" class="hover:text-primary-fixed transition-colors flex items-center gap-1.5"><span class="material-symbols-outlined text-[15px]">arrow_right</span>Reaktivasi KIS / PBI-JK</a></li>
                        <li><a href="{{ route('services.index') }}?kategori=rehabilitasi" class="hover:text-primary-fixed transition-colors flex items-center gap-1.5"><span class="material-symbols-outlined text-[15px]">arrow_right</span>Pelayanan Rehabilitasi Sosial</a></li>
                        <li><a href="{{ route('complaints.create') }}" class="hover:text-primary-fixed transition-colors flex items-center gap-1.5"><span class="material-symbols-outlined text-[15px]">arrow_right</span>Pengaduan & Laporan Warga</a></li>
                        <li><a href="{{ route('certificate.verify') }}" class="hover:text-primary-fixed transition-colors flex items-center gap-1.5"><span class="material-symbols-outlined text-[15px]">arrow_right</span>Cek Keaslian Berkas Digital</a></li>
                    </ul>
                </div>

                <!-- Col 3: Service Hours & Puskesos -->
                <div class="flex flex-col gap-2">
                    <h3 class="font-bold text-white text-sm uppercase tracking-wider mb-2">Jam Pelayanan Kantor</h3>
                    <div class="text-sm text-inverse-on-surface/80 space-y-1.5">
                        <div class="flex justify-between py-1 border-b border-white/5">
                            <span>Senin — Kamis:</span>
                            <span class="font-semibold text-white">08:00 – 15:30 WIB</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-white/5">
                            <span>Jumat:</span>
                            <span class="font-semibold text-white">08:00 – 14:30 WIB</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span>Sabtu — Minggu:</span>
                            <span class="text-secondary-container font-semibold">Tutup (Online 24 Jam)</span>
                        </div>
                    </div>
                    <div class="mt-3 p-3 rounded-xl bg-white/5 border border-white/10 text-xs text-inverse-on-surface/80">
                        <strong class="text-white block mb-0.5">Bantuan Petugas Desa:</strong>
                        Operator Puskesos dan Desa/Kelurahan setempat siap mendampingi pengajuan warga lansia atau tanpa gawai.
                    </div>
                </div>

                <!-- Col 4: Contact & Office -->
                <div class="flex flex-col gap-2">
                    <h3 class="font-bold text-white text-sm uppercase tracking-wider mb-2">Kontak & Alamat</h3>
                    <div class="text-sm text-inverse-on-surface/80 space-y-2.5">
                        <div class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-[18px] text-secondary-fixed shrink-0 mt-0.5">location_on</span>
                            <span>Jl. Raya Kanigoro No. 1, Kecamatan Kanigoro, Kabupaten Blitar, Jawa Timur 66171</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-secondary-fixed shrink-0">call</span>
                            <span>(0342) 801123</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-secondary-fixed shrink-0">mail</span>
                            <span>dinsos@blitarkab.go.id</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright -->
            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-inverse-on-surface/60 gap-4">
                <p>&copy; {{ date('Y') }} Dinas Sosial Kabupaten Blitar. Hak Cipta Dilindungi Undang-Undang.</p>
                <div class="flex items-center gap-4">
                    <a href="{{ route('tracking') }}" class="hover:text-white transition-colors">Pelacakan Tiket</a>
                    <a href="{{ route('services.index') }}" class="hover:text-white transition-colors">Katalog Layanan</a>
                    <a href="/admin" class="hover:text-white transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">lock</span>
                        <span>Portal Petugas</span>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
