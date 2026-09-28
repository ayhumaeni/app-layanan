<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Enums\PublishStatus;
use App\Models\DtsenPurpose;
use App\Models\InformationPage;
use App\Models\PageVisit;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Detail Layanan — SAPA SOSIAL Kab. Blitar')]
class ServiceDetail extends Component
{
    public InformationPage $page;

    public string $slug;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
        $this->page = InformationPage::with(['serviceType.requirements', 'downloadableForms', 'faqs'])
            ->where('slug', $slug)
            ->where('publish_status', PublishStatus::Published)
            ->firstOrFail();

        // Record page visit
        try {
            PageVisit::create([
                'information_page_id' => $this->page->id,
                'ip_address' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 255),
                'visited_at' => now(),
            ]);
        } catch (\Throwable) {
            // Ignore visit logging failure
        }
    }

    public function render()
    {
        $dtsenPurposes = collect();
        if ($this->page->serviceType?->code === 'DTSEN') {
            $dtsenPurposes = DtsenPurpose::where('is_active', true)->get();
        }

        return view('livewire.public.service-detail', [
            'dtsenPurposes' => $dtsenPurposes,
        ]);
    }
}
