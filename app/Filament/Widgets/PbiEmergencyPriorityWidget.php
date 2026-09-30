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

class PbiEmergencyPriorityWidget extends TableWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user && $user->hasAnyRole(['administrator', 'petugas_dinsos', 'pimpinan']);
    }

    public function table(Table $table): Table
    {
        $service = app(DashboardStatisticsService::class);
        $user = Auth::user();

        $query = ServiceRequest::query()
            ->whereHas('serviceType', fn ($q) => $q->where('code', 'like', '%PBI%'))
            ->where('is_priority', true)
            ->whereNotIn('status', [
                ServiceRequestStatus::Completed,
                ServiceRequestStatus::Rejected,
                ServiceRequestStatus::MinistryRejected,
            ])
            ->latest('submitted_at');

        $service->applyServiceRequestFilters($query, $this->pageFilters, $user);

        return $table
            ->heading('Reaktivasi PBI-JK Darurat Medis (Prioritas Belum Selesai)')
            ->description('Daftar pasien rawat inap / darurat medis yang memerlukan percepatan rekomendasi reaktivasi BPJS PBI-JK.')
            ->query($query)
            ->columns([
                TextColumn::make('request_number')
                    ->label('Nomor Tiket')
                    ->badge()
                    ->color('danger')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('applicant_name')
                    ->label('Nama Pasien / Pemohon')
                    ->searchable(),

                TextColumn::make('pbiReactivation.health_facility_name')
                    ->label('Fasilitas Kesehatan')
                    ->placeholder('-')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Tahap Proses')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof ServiceRequestStatus ? $state->label() : ($state ?? '-'))
                    ->color(fn ($state) => $state instanceof ServiceRequestStatus ? $state->color() : 'gray'),

                TextColumn::make('submitted_at')
                    ->label('Waktu Pengajuan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('waiting_time')
                    ->label('Durasi Antrean')
                    ->badge()
                    ->color('warning')
                    ->state(fn ($record) => $record->submitted_at ? $record->submitted_at->diffForHumans() : '-'),
            ])
            ->recordActions([
                Action::make('proses')
                    ->label('Detail Kasus')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (ServiceRequest $record): string => ServiceRequestResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated([5, 10]);
    }
}
