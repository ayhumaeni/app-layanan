<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs font-medium text-on-surface-variant mb-4">
        <a href="{{ route('home') }}" class="hover:text-primary transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-[15px]">home</span>
            <span>Beranda</span>
        </a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <a href="{{ route('services.index') }}" class="hover:text-primary transition-colors">Katalog Layanan</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-primary font-bold truncate max-w-[200px] sm:max-w-none">{{ $page->title }}</span>
    </nav>

    <!-- Main Two Column Grid: Left Content (8 cols), Right Sticky Sidebar (4 cols) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Left Main Content Column -->
        <div class="lg:col-span-8 flex flex-col gap-8">
            <!-- Header Card -->
            <div class="bg-surface-container-lowest rounded-2xl p-6 sm:p-8 border border-surface-container-high shadow-xs flex flex-col gap-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-surface-container text-primary">
                        {{ $page->category->label() }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-tertiary-fixed text-on-tertiary-fixed flex items-center gap-1">
                        <span class="material-symbols-outlined text-[15px] text-tertiary">check_circle</span>
                        <span>Biaya: Rp 0 (Gratis)</span>
                    </span>
                    @if ($page->serviceType && $page->serviceType->sla_days)
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-surface-container-high text-on-surface-variant flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px]">schedule</span>
                            <span>Estimasi: {{ $page->serviceType->sla_days }} Hari Kerja</span>
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-primary tracking-tight leading-snug">
                    {{ $page->title }}
                </h1>

                <div class="flex items-center gap-3 text-xs text-on-surface-variant pt-2 border-t border-surface-container">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">domain</span>
                        <span>Dinas Sosial Kabupaten Blitar</span>
                    </span>
                    <span>•</span>
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">update</span>
                        <span>Diperbarui: {{ $page->updated_at->translatedFormat('d F Y') }}</span>
                    </span>
                </div>
            </div>

            <!-- Deskripsi Section -->
            <div class="bg-surface-container-lowest rounded-2xl p-6 sm:p-8 border border-surface-container-high shadow-xs flex flex-col gap-3">
                <div class="flex items-center gap-2 text-primary font-bold text-base sm:text-lg">
                    <span class="material-symbols-outlined text-[22px]">info</span>
                    <h2>Deskripsi Layanan</h2>
                </div>
                <div class="text-sm text-on-surface leading-relaxed whitespace-pre-line">
                    {{ $page->description }}
                </div>
            </div>

            <!-- Tujuan Penggunaan (If DTSEN) -->
            @if ($dtsenPurposes->isNotEmpty())
                <div class="bg-surface-container-lowest rounded-2xl p-6 sm:p-8 border border-surface-container-high shadow-xs flex flex-col gap-4">
                    <div class="flex items-center gap-2 text-primary font-bold text-base sm:text-lg">
                        <span class="material-symbols-outlined text-[22px]">flag</span>
                        <h2>Tujuan Penggunaan Surat Keterangan</h2>
                    </div>
                    <p class="text-xs text-on-surface-variant">
                        Surat Keterangan DTSEN dapat diterbitkan sesuai tujuan berikut dengan batas desil maksimal yang berlaku:
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ($dtsenPurposes as $purpose)
                            <div class="p-3.5 rounded-xl bg-surface-container-low border border-outline-variant/30 flex items-start gap-3">
                                <span class="material-symbols-outlined text-secondary text-[20px] shrink-0 mt-0.5">verified</span>
                                <div class="flex flex-col">
                                    <span class="font-bold text-xs sm:text-sm text-on-surface">{{ $purpose->name }}</span>
                                    <span class="text-[11px] text-on-surface-variant mt-0.5">
                                        Maks. Desil: <strong>Desil {{ $purpose->max_decile }}</strong> • Berlaku {{ $purpose->validity_days }} Hari
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Persyaratan Berkas Section -->
            <div class="bg-surface-container-lowest rounded-2xl p-6 sm:p-8 border border-surface-container-high shadow-xs flex flex-col gap-4">
                <div class="flex items-center gap-2 text-primary font-bold text-base sm:text-lg">
                    <span class="material-symbols-outlined text-[22px]">fact_check</span>
                    <h2>Persyaratan Dokumen</h2>
                </div>
                
                @if ($page->serviceType && $page->serviceType->requirements->isNotEmpty())
                    <div class="flex flex-col gap-2.5">
                        @foreach ($page->serviceType->requirements as $req)
                            <div class="p-3.5 rounded-xl bg-surface-container-low border border-surface-container flex items-start justify-between gap-3">
                                <div class="flex items-start gap-3">
                                    <span class="material-symbols-outlined text-primary text-[20px] shrink-0 mt-0.5">check_box</span>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-xs sm:text-sm text-on-surface">{{ $req->name }}</span>
                                        @if ($req->description)
                                            <span class="text-xs text-on-surface-variant mt-0.5">{{ $req->description }}</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold shrink-0 {{ $req->is_mandatory ? 'bg-error-container text-on-error-container' : 'bg-surface-container text-on-surface-variant' }}">
                                    {{ $req->is_mandatory ? 'Wajib' : 'Opsional' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @elseif ($page->requirements)
                    <div class="text-sm text-on-surface leading-relaxed whitespace-pre-line p-4 rounded-xl bg-surface-container-low">
                        {{ $page->requirements }}
                    </div>
                @else
                    <div class="text-xs text-on-surface-variant p-4 rounded-xl bg-surface-container-low">
                        KTP dan Kartu Keluarga (KK) pemohon yang masih berlaku.
                    </div>
                @endif
            </div>

            <!-- Alur Pelayanan (Procedure) Section -->
            @if ($page->procedure)
                <div class="bg-surface-container-lowest rounded-2xl p-6 sm:p-8 border border-surface-container-high shadow-xs flex flex-col gap-4">
                    <div class="flex items-center gap-2 text-primary font-bold text-base sm:text-lg">
                        <span class="material-symbols-outlined text-[22px]">alt_route</span>
                        <h2>Alur dan Tahapan Pelayanan</h2>
                    </div>
                    <div class="text-sm text-on-surface leading-relaxed whitespace-pre-line bg-surface-container-low p-4 rounded-xl">
                        {{ $page->procedure }}
                    </div>
                </div>
            @endif

            <!-- Waktu & Lokasi Pelayanan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-surface-container-lowest rounded-2xl p-6 border border-surface-container-high shadow-xs flex flex-col gap-2">
                    <div class="flex items-center gap-2 text-primary font-bold text-sm">
                        <span class="material-symbols-outlined text-[20px]">schedule</span>
                        <h3>Waktu Pelayanan</h3>
                    </div>
                    <p class="text-xs text-on-surface-variant leading-relaxed">
                        {{ $page->service_hours ?? 'Senin – Kamis: 08:00 – 15:30 WIB, Jumat: 08:00 – 14:30 WIB. Layanan pendaftaran daring (online) buka 24 jam.' }}
                    </p>
                </div>

                <div class="bg-surface-container-lowest rounded-2xl p-6 border border-surface-container-high shadow-xs flex flex-col gap-2">
                    <div class="flex items-center gap-2 text-primary font-bold text-sm">
                        <span class="material-symbols-outlined text-[20px]">location_on</span>
                        <h3>Lokasi Pelayanan</h3>
                    </div>
                    <p class="text-xs text-on-surface-variant leading-relaxed">
                        {{ $page->location ?? 'Kantor Dinas Sosial Kab. Blitar, Jl. Raya Kanigoro No. 1, atau melalui Operator Puskesos di Kantor Desa/Kecamatan setempat.' }}
                    </p>
                </div>
            </div>

            <!-- FAQs Khusus Layanan Ini -->
            @if ($page->faqs->isNotEmpty())
                <div class="bg-surface-container-lowest rounded-2xl p-6 sm:p-8 border border-surface-container-high shadow-xs flex flex-col gap-4" x-data="{ activeFaq: null }">
                    <div class="flex items-center gap-2 text-primary font-bold text-base sm:text-lg">
                        <span class="material-symbols-outlined text-[22px]">quiz</span>
                        <h2>Tanya Jawab Layanan</h2>
                    </div>
                    <div class="flex flex-col gap-2.5">
                        @foreach ($page->faqs as $faq)
                            <div class="border border-surface-container rounded-xl overflow-hidden">
                                <button @click="activeFaq = (activeFaq === {{ $faq->id }} ? null : {{ $faq->id }})" 
                                        type="button" 
                                        class="w-full p-4 flex items-center justify-between text-left gap-3 bg-surface-container-low hover:bg-surface-container transition-colors">
                                    <span class="font-bold text-xs sm:text-sm text-on-surface">{{ $faq->question }}</span>
                                    <span class="material-symbols-outlined text-[18px] text-primary shrink-0 transition-transform" :class="{ 'rotate-180': activeFaq === {{ $faq->id }} }">
                                        expand_more
                                    </span>
                                </button>
                                <div x-show="activeFaq === {{ $faq->id }}" class="p-4 text-xs sm:text-sm text-on-surface-variant leading-relaxed border-t border-surface-container bg-surface-container-lowest" style="display: none;">
                                    {{ $faq->answer }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Sticky Sidebar Column (Desktop) -->
        <div class="lg:col-span-4 sticky top-24 flex flex-col gap-6">
            <!-- Action Card -->
            <div class="bg-surface-container-lowest rounded-2xl p-6 border border-surface-container-high shadow-md flex flex-col gap-4">
                <span class="text-xs font-bold text-secondary uppercase tracking-wider">Aksi Cepat Warga</span>
                <h3 class="text-lg font-bold text-primary leading-tight">Siap mengajukan permohonan ini?</h3>
                <p class="text-xs text-on-surface-variant leading-relaxed">
                    Pastikan Anda telah menyiapkan foto KTP dan Kartu Keluarga yang jelas sebelum mengisi formulir.
                </p>

                <div class="flex flex-col gap-2.5 pt-2">
                    @if ($page->serviceType && $page->serviceType->code === 'DTSEN')
                        <a href="{{ route('services.dtsen.apply') }}" class="min-h-[48px] px-4 rounded-xl bg-primary hover:bg-primary-container text-white font-bold text-sm shadow-md transition-all flex items-center justify-center gap-2 active:scale-95">
                            <span class="material-symbols-outlined text-[20px]">assignment_turned_in</span>
                            <span>Ajukan Surat DTSEN</span>
                        </a>
                    @elseif ($page->serviceType && $page->serviceType->code === 'PBI')
                        <a href="{{ route('services.kis.apply') }}" class="min-h-[48px] px-4 rounded-xl bg-secondary-container hover:brightness-95 text-on-secondary-container font-bold text-sm shadow-md transition-all flex items-center justify-center gap-2 active:scale-95">
                            <span class="material-symbols-outlined text-[20px]">health_and_safety</span>
                            <span>Ajukan Reaktivasi KIS</span>
                        </a>
                    @else
                        <a href="{{ route('complaints.create') }}" class="min-h-[48px] px-4 rounded-xl bg-primary hover:bg-primary-container text-white font-bold text-sm shadow-md transition-all flex items-center justify-center gap-2 active:scale-95">
                            <span class="material-symbols-outlined text-[20px]">record_voice_over</span>
                            <span>Sampaikan Laporan / Kasus</span>
                        </a>
                    @endif

                    <a href="{{ route('complaints.create') }}" class="min-h-[44px] px-4 rounded-xl bg-surface-container hover:bg-surface-container-high text-primary font-bold text-xs transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">chat</span>
                        <span>Ada Pertanyaan atau Masalah?</span>
                    </a>
                </div>
            </div>

            <!-- Downloadable Forms in Sidebar if available -->
            @if ($page->downloadableForms->isNotEmpty())
                <div class="bg-surface-container-lowest rounded-2xl p-6 border border-surface-container-high shadow-xs flex flex-col gap-3">
                    <h3 class="text-sm font-bold text-on-surface flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-primary text-[18px]">download</span>
                        <span>Unduh Formulir Terkait</span>
                    </h3>
                    <div class="flex flex-col gap-2">
                        @foreach ($page->downloadableForms as $form)
                            <a href="{{ asset('storage/' . $form->file_path) }}" 
                               target="_blank" 
                               download 
                               class="p-3 rounded-xl bg-surface-container-low hover:bg-surface-container transition-colors border border-outline-variant/30 flex items-center justify-between gap-2 group">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="material-symbols-outlined text-error text-[20px] shrink-0">picture_as_pdf</span>
                                    <span class="text-xs font-semibold text-on-surface truncate group-hover:text-primary transition-colors">{{ $form->name }}</span>
                                </div>
                                <span class="material-symbols-outlined text-outline text-[16px] shrink-0 group-hover:text-primary transition-colors">download</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Help Box -->
            <div class="p-5 rounded-2xl bg-surface-container-low border border-surface-container flex flex-col gap-2.5">
                <span class="text-xs font-bold text-primary flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">headset_mic</span>
                    <span>Bantuan Petugas Puskesos</span>
                </span>
                <p class="text-xs text-on-surface-variant leading-relaxed">
                    Jika Anda kesulitan mengisi formulir, silakan datangi balai desa Anda atau hubungi hotline Dinsos Blitar:
                </p>
                <a href="https://wa.me/6281234567890" target="_blank" rel="noopener noreferrer" class="font-bold text-xs text-secondary hover:underline flex items-center gap-1 mt-1">
                    <span>Chat WhatsApp: 0812-3456-7890</span>
                    <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Mobile Sticky Action Bottom Bar -->
    <div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-surface/95 backdrop-blur-md p-3 border-t border-surface-container-high shadow-xl flex items-center gap-2">
        @if ($page->serviceType && $page->serviceType->code === 'DTSEN')
            <a href="{{ route('services.dtsen.apply') }}" class="flex-1 min-h-[48px] px-4 rounded-xl bg-primary text-white font-bold text-sm shadow-md flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[18px]">assignment_turned_in</span>
                <span>Ajukan Surat Sekarang</span>
            </a>
        @elseif ($page->serviceType && $page->serviceType->code === 'PBI')
            <a href="{{ route('services.kis.apply') }}" class="flex-1 min-h-[48px] px-4 rounded-xl bg-secondary-container text-on-secondary-container font-bold text-sm shadow-md flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[18px]">health_and_safety</span>
                <span>Ajukan Reaktivasi KIS</span>
            </a>
        @else
            <a href="{{ route('complaints.create') }}" class="flex-1 min-h-[48px] px-4 rounded-xl bg-primary text-white font-bold text-sm shadow-md flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[18px]">record_voice_over</span>
                <span>Lapor Sekarang</span>
            </a>
        @endif
        <a href="{{ route('home') }}" class="w-12 h-12 rounded-xl bg-surface-container text-on-surface flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-[20px]">home</span>
        </a>
    </div>
</div>
