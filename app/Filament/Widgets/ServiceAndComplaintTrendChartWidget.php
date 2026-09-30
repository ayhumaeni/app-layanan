<?php

namespace App\Filament\Widgets;

use App\Services\DashboardStatisticsService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\Auth;

class ServiceAndComplaintTrendChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 7;

    protected ?string $heading = 'Tren Harian: Pengajuan Layanan vs Laporan Pengaduan';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $service = app(DashboardStatisticsService::class);
        $user = Auth::user();
        $result = $service->getTrendData($this->pageFilters, $user, 14);

        return [
            'datasets' => [
                [
                    'label' => 'Pengajuan Layanan',
                    'data' => $result['requests'],
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Laporan Pengaduan',
                    'data' => $result['complaints'],
                    'borderColor' => '#f97316',
                    'backgroundColor' => 'rgba(249, 115, 22, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $result['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
