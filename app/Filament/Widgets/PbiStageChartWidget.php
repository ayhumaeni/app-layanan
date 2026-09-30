<?php

namespace App\Filament\Widgets;

use App\Services\DashboardStatisticsService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\Auth;

class PbiStageChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 5;

    protected ?string $heading = 'Reaktivasi PBI-JK per Tahapan Proses';

    protected int|string|array $columnSpan = 1;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user && $user->hasAnyRole(['administrator', 'petugas_dinsos', 'pejabat_penandatangan', 'pimpinan']);
    }

    protected function getData(): array
    {
        $service = app(DashboardStatisticsService::class);
        $user = Auth::user();
        $result = $service->getPbiStages($this->pageFilters, $user);

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Permohonan',
                    'data' => $result['counts'],
                    'backgroundColor' => [
                        '#f59e0b', '#3b82f6', '#8b5cf6', '#10b981', '#ef4444',
                    ],
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
