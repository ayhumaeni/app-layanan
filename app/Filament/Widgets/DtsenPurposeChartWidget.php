<?php

namespace App\Filament\Widgets;

use App\Services\DashboardStatisticsService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\Auth;

class DtsenPurposeChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 4;

    protected ?string $heading = 'SK DTSEN Berdasarkan Tujuan Penggunaan';

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
        $result = $service->getDtsenByPurpose($this->pageFilters, $user);

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Surat Diterbitkan',
                    'data' => $result['counts'],
                    'backgroundColor' => [
                        '#10b981', '#06b6d4', '#6366f1', '#f59e0b',
                        '#ec4899', '#8b5cf6', '#14b8a6', '#f97316',
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
