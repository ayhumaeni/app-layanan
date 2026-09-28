<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Enums\PublishStatus;
use App\Models\DownloadableForm;
use App\Models\InformationPage;
use App\Models\ServiceType;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Katalog Layanan & Informasi — SAPA SOSIAL Kab. Blitar')]
class ServiceCatalog extends Component
{
    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'kategori')]
    public string $category = 'all';

    public function selectCategory(string $category): void
    {
        $this->category = $category;
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->category = 'all';
    }

    public function render()
    {
        // Query Information Pages (which have rich articles & detail pages)
        $infoPagesQuery = InformationPage::with(['serviceType', 'downloadableForms'])
            ->where('publish_status', PublishStatus::Published);

        if (! empty(trim($this->search))) {
            $keyword = '%'.trim($this->search).'%';
            $infoPagesQuery->where(function ($q) use ($keyword) {
                $q->where('title', 'ILIKE', $keyword)
                    ->orWhere('description', 'ILIKE', $keyword)
                    ->orWhere('requirements', 'ILIKE', $keyword);
            });
        }

        if ($this->category !== 'all') {
            $infoPagesQuery->where('category', $this->category);
        }

        $informationPages = $infoPagesQuery->latest('published_at')->get();

        // Also query active ServiceTypes
        $serviceTypesQuery = ServiceType::with('requirements')
            ->where('is_active', true);

        if (! empty(trim($this->search))) {
            $keyword = '%'.trim($this->search).'%';
            $serviceTypesQuery->where(function ($q) use ($keyword) {
                $q->where('name', 'ILIKE', $keyword)
                    ->orWhere('description', 'ILIKE', $keyword)
                    ->orWhere('category', 'ILIKE', $keyword);
            });
        }

        $serviceTypes = $serviceTypesQuery->orderBy('id')->get();

        // Query Downloadable Forms
        $downloadableForms = DownloadableForm::with('informationPage')
            ->where('is_current', true)
            ->latest()
            ->get();

        return view('livewire.public.service-catalog', [
            'informationPages' => $informationPages,
            'serviceTypes' => $serviceTypes,
            'downloadableForms' => $downloadableForms,
        ]);
    }
}
