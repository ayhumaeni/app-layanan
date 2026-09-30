<?php

namespace App\Filament\Widgets;

use App\Services\DashboardStatisticsService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\Auth;

class RehabilitationCasesChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 6;

    protected ?string $heading = 'Status Kasus Rehabilitasi Sosial';

    protected int|string|array $columnSpan = 1;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user && $user->hasAnyRole(['administrator', 'petugas_dinsos', 'pimpinan']);
    }

    protected function getData(): array
    {
        $service = app(DashboardStatisticsService::class);
        $user = Auth::user();
        $result = $service->getRehabilitationStatusCounts($this->pageFilters, $user);

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Kasus',
                    'data' => $result['counts'],
                    'backgroundColor' => [
                        '#06b6d4', '#f59e0b', '#ec4899', '#3b82f6', '#8b5cf6', '#10b981',
                    ],
                ],
            ],
            'labels' => $result['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
