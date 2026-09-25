<?php

namespace App\Filament\Resources\ServiceRequests\Pages;

use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListServiceRequests extends ListRecords
{
    protected static string $resource = ServiceRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Buat Pengajuan Baru'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Pengajuan'),
            'dtsen' => Tab::make('SK DTSEN')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('serviceType', fn ($q) => $q->where('handler', 'dtsen'))),
            'pbi' => Tab::make('Reaktivasi PBI-JK')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('serviceType', fn ($q) => $q->where('handler', 'pbi'))),
            'generic' => Tab::make('Layanan Lainnya')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('serviceType', fn ($q) => $q->where('handler', 'generic'))),
            'need_action' => Tab::make('Perlu Verifikasi')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', [
                    'submitted',
                    'document_check',
                    'data_verification',
                    'eligibility_verification',
                ])),
            'awaiting_approval' => Tab::make('Menunggu TTD / Paraf')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'awaiting_approval')),
            'priority' => Tab::make('Darurat / Prioritas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_priority', true)),
        ];
    }
}
