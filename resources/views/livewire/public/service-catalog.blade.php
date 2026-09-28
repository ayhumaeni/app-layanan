<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col gap-8">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col gap-2">
        <nav class="flex items-center gap-2 text-xs font-medium text-on-surface-variant">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors flex items-center gap-1">
                <span class="material-symbols-outlined text-[15px]">home</span>
                <span>Beranda</span>
            </a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary font-bold">Katalog Layanan & Informasi</span>
        </nav>
        <div class="flex flex-col gap-1 mt-1">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-primary tracking-tight">
                Layanan Sosial & Informasi Publik
            </h1>
            <p class="text-sm sm:text-base text-on-surface-variant leading-relaxed">
                Katalog resmi program bantuan sosial, rehabilitasi, jaminan kesehatan, dan unduhan formulir Dinas Sosial Kabupaten Blitar
            </p>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="flex flex-col gap-4">
        <!-- Big Search Bar -->
        <div class="relative w-full">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-primary text-[24px] pointer-events-none">search</span>
            <input wire:model.live.debounce.300ms="search" 
                   type="search" 
                   placeholder="Cari nama layanan, jenis bantuan bansos, atau kata kunci..." 
                   class="w-full h-14 pl-12 pr-10 rounded-2xl bg-surface-container-lowest text-on-surface text-sm sm:text-base placeholder:text-outline shadow-sm border border-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary transition-all" />
            @if ($search)
                <button wire:click="$set('search', '')" class="absolute right-4 top-1/2 -translate-y-1/2 text-outline hover:text-on-surface p-1">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            @endif
        </div>

        <!-- Filter Chips -->
        <div class="flex items-center gap-2 overflow-x-auto py-1 no-scrollbar -mx-4 px-4 sm:mx-0 sm:px-0">
            <button wire:click="selectCategory('all')" 
                    type="button" 
                    class="shrink-0 min-h-[44px] px-4 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 transition-all cursor-pointer {{ $category === 'all' ? 'bg-primary text-white shadow-xs' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined text-[18px]">apps</span>
                <span>Semua Layanan</span>
            </button>
            <button wire:click="selectCategory('program')" 
                    type="button" 
                    class="shrink-0 min-h-[44px] px-4 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 transition-all cursor-pointer {{ $category === 'program' ? 'bg-primary text-white shadow-xs' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined text-[18px]">volunteer_activism</span>
                <span>Program Bansos</span>
            </button>
            <button wire:click="selectCategory('rehabilitation')" 
                    type="button" 
                    class="shrink-0 min-h-[44px] px-4 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 transition-all cursor-pointer {{ $category === 'rehabilitation' ? 'bg-primary text-white shadow-xs' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined text-[18px]">healing</span>
                <span>Rehabilitasi Sosial</span>
            </button>
            <button wire:click="selectCategory('disability')" 
                    type="button" 
                    class="shrink-0 min-h-[44px] px-4 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 transition-all cursor-pointer {{ $category === 'disability' ? 'bg-primary text-white shadow-xs' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined text-[18px]">accessible</span>
                <span>Disabilitas & Lansia</span>
            </button>
            <button wire:click="selectCategory('complaint')" 
                    type="button" 
                    class="shrink-0 min-h-[44px] px-4 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 transition-all cursor-pointer {{ $category === 'complaint' ? 'bg-primary text-white shadow-xs' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined text-[18px]">campaign</span>
                <span>Pengaduan Warga</span>
            </button>
        </div>
    </div>

    <!-- Active Search / Filter Indicators -->
    @if ($search || $category !== 'all')
        <div class="flex items-center justify-between text-xs text-on-surface-variant -mt-3">
            <span class="font-medium">
                Menampilkan hasil untuk:
                @if ($search) <strong>"{{ $search }}"</strong> @endif
                @if ($category !== 'all') <span class="capitalize">kategori {{ $category }}</span> @endif
            </span>
            <button wire:click="clearFilters" class="text-error font-bold hover:underline flex items-center gap-1">
                <span class="material-symbols-outlined text-[15px]">refresh</span>
                <span>Reset Pencarian</span>
            </button>
        </div>
    @endif

    <!-- Service Catalog Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($informationPages as $page)
            <div class="bg-surface-container-lowest rounded-2xl p-6 border border-surface-container-high shadow-xs hover:shadow-md transition-all flex flex-col justify-between gap-5 group">
                <div class="flex flex-col gap-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-surface-container text-primary">
                            {{ $page->category->label() ?? 'Informasi Layanan' }}
                        </span>
                        @if ($page->serviceType && $page->serviceType->sla_days)
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-surface-container-high text-on-surface-variant flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px]">schedule</span>
                                <span>{{ $page->serviceType->sla_days }} Hari Kerja</span>
                            </span>
                        @endif
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-on-surface group-hover:text-primary transition-colors leading-snug">
                            {{ $page->title }}
                        </h2>
                        <p class="text-xs text-on-surface-variant mt-2 line-clamp-3 leading-relaxed">
                            {{ $page->description }}
                        </p>
                    </div>

                    @if ($page->requirements)
                        <div class="p-3 rounded-xl bg-surface-container-low text-xs text-on-surface-variant flex items-start gap-2">
                            <span class="material-symbols-outlined text-primary text-[17px] shrink-0 mt-0.5">fact_check</span>
                            <span class="line-clamp-2"><strong>Syarat:</strong> {{ Str::limit(strip_tags($page->requirements), 90) }}</span>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-2 pt-3 border-t border-surface-container">
                    <a href="{{ route('services.show', $page->slug) }}" class="flex-1 min-h-[44px] rounded-xl bg-surface-container hover:bg-surface-container-high text-primary font-bold text-xs flex items-center justify-center gap-1.5 transition-colors">
                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                        <span>Lihat Detail</span>
                    </a>

                    @if ($page->serviceType && $page->serviceType->code === 'DTSEN')
                        <a href="{{ route('services.dtsen.apply') }}" class="min-h-[44px] px-4 rounded-xl bg-primary text-white font-bold text-xs flex items-center justify-center gap-1 hover:bg-primary-container shadow-xs transition-colors">
                            <span>Ajukan</span>
                            <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                        </a>
                    @elseif ($page->serviceType && $page->serviceType->code === 'PBI')
                        <a href="{{ route('services.kis.apply') }}" class="min-h-[44px] px-4 rounded-xl bg-secondary-container text-on-secondary-container font-bold text-xs flex items-center justify-center gap-1 hover:brightness-95 shadow-xs transition-colors">
                            <span>Ajukan</span>
                            <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                        </a>
                    @elseif ($page->category->value === 'complaint')
                        <a href="{{ route('complaints.create') }}" class="min-h-[44px] px-4 rounded-xl bg-primary text-white font-bold text-xs flex items-center justify-center gap-1 hover:bg-primary-container shadow-xs transition-colors">
                            <span>Lapor</span>
                            <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-surface-container-lowest rounded-2xl border border-surface-container-high p-8 flex flex-col items-center justify-center gap-3">
                <div class="w-14 h-14 rounded-full bg-surface-container flex items-center justify-center text-outline">
                    <span class="material-symbols-outlined text-[32px]">search_off</span>
                </div>
                <h3 class="text-base font-bold text-on-surface">Tidak ada layanan yang sesuai</h3>
                <p class="text-xs text-on-surface-variant max-w-sm">Coba ubah kata kunci pencarian atau pilih kategori "Semua Layanan".</p>
                <button wire:click="clearFilters" class="mt-2 px-4 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-container transition-colors">
                    Lihat Semua Layanan
                </button>
            </div>
        @endforelse
    </div>

    <!-- Section Formulir Unduhan -->
    <div id="formulir-unduhan" class="mt-12 bg-surface-container-lowest rounded-2xl p-6 sm:p-8 border border-surface-container-high shadow-xs flex flex-col gap-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-surface-container">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary-container text-white flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[22px]">download_for_offline</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-on-surface">Formulir Unduhan Resmi</h2>
                    <p class="text-xs text-on-surface-variant">Dokumen template dan surat permohonan resmi dalam format PDF siap cetak</p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-surface-container text-primary self-start sm:self-auto">
                {{ $downloadableForms->count() }} Berkas Tersedia
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse ($downloadableForms as $form)
                <div class="p-4 rounded-xl bg-surface-container-low hover:bg-surface-container transition-colors border border-outline-variant/30 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-lg bg-error-container/30 text-error flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[24px]">picture_as_pdf</span>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="font-bold text-xs sm:text-sm text-on-surface truncate">{{ $form->name }}</span>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="px-2 py-0.2 rounded text-[10px] font-bold bg-tertiary-fixed text-on-tertiary-fixed">
                                    Versi {{ $form->version }}
                                </span>
                                <span class="text-[11px] text-on-surface-variant">
                                    Terkait: {{ $form->informationPage?->title ?? 'Umum' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ asset('storage/' . $form->file_path) }}" 
                       target="_blank" 
                       download 
                       class="px-3 py-2 rounded-lg bg-primary text-white text-xs font-bold hover:bg-primary-container transition-colors flex items-center gap-1 shrink-0">
                        <span class="material-symbols-outlined text-[16px]">download</span>
                        <span>Unduh</span>
                    </a>
                </div>
            @empty
                <div class="col-span-full text-center py-6 text-xs text-on-surface-variant">
                    Belum ada formulir unduhan publik yang diunggah.
                </div>
            @endforelse
        </div>
    </div>
</div>
