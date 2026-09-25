<?php

namespace App\Filament\Resources\RehabilitationCases\Tables;

use App\Enums\HandlingType;
use App\Enums\RehabilitationCaseStatus;
use App\Models\RehabilitationCase;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class RehabilitationCasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('case_number')
                    ->label('No. Kasus')
                    ->searchable()
                    ->copyable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('client.name')
                    ->label('Nama Klien')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('client.clientCategory.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('warning')
                    ->searchable(),
                TextColumn::make('officer.name')
                    ->label('Petugas')
                    ->placeholder('Belum ditugaskan')
                    ->searchable(),
                TextColumn::make('handling_type')
                    ->label('Penanganan')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof HandlingType ? $state->label() : ($state ? HandlingType::tryFrom($state)?->label() ?? $state : '-')),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => $state instanceof RehabilitationCaseStatus ? $state->color() : ($state ? RehabilitationCaseStatus::tryFrom($state)?->color() ?? 'gray' : 'gray'))
                    ->formatStateUsing(fn ($state) => $state instanceof RehabilitationCaseStatus ? $state->label() : ($state ? RehabilitationCaseStatus::tryFrom($state)?->label() ?? $state : '-')),
                TextColumn::make('received_at')
                    ->label('Diterima')
                    ->dateTime('d M Y')
                    ->sortable(),
                TextColumn::make('closed_at')
                    ->label('Ditutup')
                    ->dateTime('d M Y')
                    ->placeholder('Masih Aktif')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('received_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Kasus')
                    ->options(collect(RehabilitationCaseStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
                SelectFilter::make('handling_type')
                    ->label('Bentuk Penanganan')
                    ->options(collect(HandlingType::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),

                    // Workflow 1: Mulai Assessment (received -> assessment)
                    Action::make('startAssessment')
                        ->label('Mulai Assessment')
                        ->icon(Heroicon::OutlinedClipboardDocumentList)
                        ->color('warning')
                        ->visible(fn (RehabilitationCase $record) => $record->status === RehabilitationCaseStatus::Received)
                        ->requiresConfirmation()
                        ->action(function (RehabilitationCase $record): void {
                            $record->recordStatusChange(
                                RehabilitationCaseStatus::Assessment,
                                'Kasus mulai di-assessment oleh petugas rehabilitasi sosial.',
                                Auth::id()
                            );
                            Notification::make()
                                ->title('Status kasus diubah ke: Tahap Assessment')
                                ->success()
                                ->send();
                        }),

                    // Workflow 2: Tetapkan Rencana Pelayanan (assessment -> service_planning)
                    Action::make('planService')
                        ->label('Tetapkan Rencana Pelayanan')
                        ->icon(Heroicon::OutlinedDocumentPlus)
                        ->color('primary')
                        ->visible(fn (RehabilitationCase $record) => $record->status === RehabilitationCaseStatus::Assessment)
                        ->requiresConfirmation()
                        ->action(function (RehabilitationCase $record): void {
                            if ($record->assessments()->count() === 0) {
                                Notification::make()
                                    ->title('Hasil assessment wajib dicatat terlebih dahulu sebelum menyusun rencana pelayanan!')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $record->recordStatusChange(
                                RehabilitationCaseStatus::ServicePlanning,
                                'Rencana penanganan dan pelayanan sosial telah ditetapkan berdasarkan hasil assessment.',
                                Auth::id()
                            );

                            Notification::make()
                                ->title('Rencana pelayanan ditetapkan')
                                ->success()
                                ->send();
                        }),

                    // Workflow 3: Jalankan Pelayanan / Rujukan (service_planning -> in_service)
                    Action::make('startService')
                        ->label('Mulai Pelayanan / Rujukan')
                        ->icon(Heroicon::OutlinedPlay)
                        ->color('info')
                        ->visible(fn (RehabilitationCase $record) => $record->status === RehabilitationCaseStatus::ServicePlanning)
                        ->requiresConfirmation()
                        ->action(function (RehabilitationCase $record): void {
                            $record->recordStatusChange(
                                RehabilitationCaseStatus::InService,
                                'Klien mulai mendapatkan tindakan penanganan / rujukan lembaga.',
                                Auth::id()
                            );
                            Notification::make()
                                ->title('Klien sedang dalam pelayanan')
                                ->success()
                                ->send();
                        }),

                    // Workflow 4: Monitoring Perkembangan (in_service -> monitoring)
                    Action::make('startMonitoring')
                        ->label('Tahap Monitoring')
                        ->icon(Heroicon::OutlinedEye)
                        ->color('warning')
                        ->visible(fn (RehabilitationCase $record) => $record->status === RehabilitationCaseStatus::InService)
                        ->requiresConfirmation()
                        ->action(function (RehabilitationCase $record): void {
                            $record->recordStatusChange(
                                RehabilitationCaseStatus::Monitoring,
                                'Klien masuk tahap monitoring perkembangan hasil rehabilitasi.',
                                Auth::id()
                            );
                            Notification::make()
                                ->title('Tahap monitoring aktif')
                                ->success()
                                ->send();
                        }),

                    // Workflow 5: Tutup Kasus (in_service / monitoring -> closed)
                    Action::make('closeCase')
                        ->label('Selesaikan & Tutup Kasus')
                        ->icon(Heroicon::OutlinedCheckBadge)
                        ->color('success')
                        ->visible(fn (RehabilitationCase $record) => in_array($record->status, [RehabilitationCaseStatus::InService, RehabilitationCaseStatus::Monitoring]))
                        ->form([
                            Textarea::make('handling_result')
                                ->label('Hasil Akhir Penanganan Kasus (Wajib dicatat sebelum kasus ditutup)')
                                ->required(),
                        ])
                        ->action(function (RehabilitationCase $record, array $data): void {
                            $record->handling_result = $data['handling_result'];
                            $record->closed_at = now();
                            $record->recordStatusChange(
                                RehabilitationCaseStatus::Closed,
                                'Kasus rehabilitasi sosial ditutup: '.$data['handling_result'],
                                Auth::id()
                            );
                            Notification::make()
                                ->title('Kasus berhasil diselesaikan dan ditutup!')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
