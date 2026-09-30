<?php

namespace App\Filament\Widgets;

use App\Enums\ServiceRequestStatus;
use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use App\Models\ServiceRequest;
use App\Services\DashboardStatisticsService;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class DtsenPendingApprovalWidget extends TableWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user && $user->hasAnyRole(['administrator', 'pejabat_penandatangan', 'pimpinan']);
    }

    public function table(Table $table): Table
    {
        $service = app(DashboardStatisticsService::class);
        $user = Auth::user();

        $query = ServiceRequest::query()
            ->where('status', ServiceRequestStatus::AwaitingApproval)
            ->latest('submitted_at');

        $service->applyServiceRequestFilters($query, $this->pageFilters, $user);

        return $table
            ->heading('Antrean SK DTSEN Menunggu Tanda Tangan / Persetujuan')
            ->description('Daftar berkas permohonan SK DTSEN yang telah diverifikasi dan siap untuk diparaf / ditandatangani.')
            ->query($query)
            ->columns([
                TextColumn::make('request_number')
                    ->label('Nomor Tiket')
                    ->badge()
                    ->color('warning')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('applicant_name')
                    ->label('Nama Pemohon')
                    ->searchable(),

                TextColumn::make('applicant_nik')
                    ->label('NIK')
                    ->copyable(),

                TextColumn::make('village.name')
                    ->label('Desa / Kecamatan')
                    ->formatStateUsing(fn ($record) => $record->village ? "{$record->village->name} ({$record->village->district?->name})" : '-'),

                TextColumn::make('submitted_at')
                    ->label('Diajukan Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('waiting_time')
                    ->label('Lama Menunggu')
                    ->badge()
                    ->color('danger')
                    ->state(fn ($record) => $record->submitted_at ? $record->submitted_at->diffForHumans() : '-'),
            ])
            ->recordActions([
                Action::make('buka')
                    ->label('Buka Berkas')
                    ->icon('heroicon-o-eye')
                    ->url(fn (ServiceRequest $record): string => ServiceRequestResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated([5, 10]);
    }
}
