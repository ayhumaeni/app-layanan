<?php

namespace App\Filament\Widgets;

use App\Services\DashboardStatisticsService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class SapaSosialOverviewWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $service = app(DashboardStatisticsService::class);
        $user = Auth::user();
        $stats = $service->getOverviewStats($this->pageFilters, $user);

        return [
            Stat::make('SK DTSEN Diterbitkan', (string) $stats['dtsen_issued'])
                ->description('Surat keterangan terbit pada periode terpilih')
                ->descriptionIcon('heroicon-o-document-check')
                ->color('success'),

            Stat::make('Menunggu Tanda Tangan SK', (string) $stats['dtsen_pending'])
                ->description('Draf menunggu paraf / tanda tangan pejabat')
                ->descriptionIcon('heroicon-o-pencil-square')
                ->color('warning'),

            Stat::make('PBI-JK Perlu Tindak Lanjut', (string) $stats['pbi_follow_up'])
                ->description('Diusulkan ke Kemensos & prioritas darurat')
                ->descriptionIcon('heroicon-o-clock')
                ->color('danger'),

            Stat::make('Kasus Rehabilitasi Aktif', (string) $stats['rehab_active'])
                ->description('Kasus aktif yang sedang ditangani')
                ->descriptionIcon('heroicon-o-heart')
                ->color('primary'),

            Stat::make('Tiket Dalam Penanganan', (string) $stats['active_in_process'])
                ->description('Total pengajuan & aduan belum selesai')
                ->descriptionIcon('heroicon-o-arrow-path')
                ->color('info'),
        ];
    }
}
