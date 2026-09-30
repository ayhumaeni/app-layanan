<?php

namespace App\Filament\Widgets;

use App\Services\DashboardStatisticsService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\Auth;

class RegionalDistributionChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 8;

    protected ?string $heading = 'Sebaran Pengajuan Layanan Berdasarkan Kecamatan';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $service = app(DashboardStatisticsService::class);
        $user = Auth::user();
        $result = $service->getRegionalDistribution($this->pageFilters, $user);

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pengajuan',
                    'data' => $result['counts'],
                    'backgroundColor' => '#3b82f6',
                ],
            ],
            'labels' => $result['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
