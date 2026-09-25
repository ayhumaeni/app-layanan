<?php

namespace App\Filament\Resources\RehabilitationCases\RelationManagers;

use App\Enums\ReferralStatus;
use App\Models\Referral;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ReferralsRelationManager extends RelationManager
{
    protected static string $relationship = 'referrals';

    protected static ?string $title = 'Rujukan ke Lembaga Eksternal';

    protected static ?string $modelLabel = 'Rujukan';

    protected static ?string $pluralModelLabel = 'Rujukan';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('referral_number')
                    ->label('Nomor Rujukan')
                    ->placeholder('Otomatis dibuat (RJK-...)')
                    ->disabled()
                    ->dehydrated(false),
                Select::make('assessment_id')
                    ->label('Dasar Hasil Assessment')
                    ->relationship('assessment', 'recommendation', fn (Builder $query) => $query->where('rehabilitation_case_id', $this->getOwnerRecord()->id))
                    ->helperText('Hanya assessment yang menyatakan butuh rujukan yang dapat dipilih')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('referral_institution_id')
                    ->label('Lembaga Tujuan Rujukan')
                    ->relationship('referralInstitution', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('officer_id')
                    ->label('Petugas Pendamping')
                    ->relationship('officer', 'name')
                    ->default(fn () => Auth::id())
                    ->required()
                    ->searchable()
                    ->preload(),
                DatePicker::make('referral_date')
                    ->label('Tanggal Rujukan')
                    ->default(now())
                    ->required(),
                Select::make('status')
                    ->label('Status Rujukan')
                    ->options(ReferralStatus::class)
                    ->default(ReferralStatus::Draft)
                    ->required(),
                Textarea::make('service_result')
                    ->label('Hasil Pelayanan dari Lembaga Rujukan')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('referral_number')
            ->defaultSort('referral_date', 'desc')
            ->columns([
                TextColumn::make('referral_number')
                    ->label('No. Rujukan')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('referralInstitution.name')
                    ->label('Lembaga Tujuan')
                    ->searchable(),
                TextColumn::make('referralInstitution.type')
                    ->label('Jenis Lembaga')
                    ->badge(),
                TextColumn::make('officer.name')
                    ->label('Pendamping')
                    ->searchable(),
                TextColumn::make('referral_date')
                    ->label('Tgl Rujukan')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => $state instanceof ReferralStatus ? $state->color() : ($state ? ReferralStatus::tryFrom($state)?->color() ?? 'gray' : 'gray'))
                    ->formatStateUsing(fn ($state) => $state instanceof ReferralStatus ? $state->label() : ($state ? ReferralStatus::tryFrom($state)?->label() ?? $state : '-')),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Buat Surat Rujukan Baru')
                    ->before(function (CreateAction $action) {
                        $hasNeedsReferral = $this->getOwnerRecord()->assessments()->where('needs_referral', true)->exists();
                        if (! $hasNeedsReferral) {
                            Notification::make()
                                ->title('Rujukan tidak dapat dibuat!')
                                ->body('Belum ada assessment yang menyatakan klien membutuhkan rujukan ke pihak/lembaga luar.')
                                ->danger()
                                ->send();

                            $action->halt();
                        }
                    }),
            ])
            ->recordActions([
                Action::make('sendReferral')
                    ->label('Kirim Rujukan')
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->color('info')
                    ->visible(fn (Referral $record) => $record->status === ReferralStatus::Draft)
                    ->requiresConfirmation()
                    ->action(fn (Referral $record) => $record->recordStatusChange(ReferralStatus::Sent, 'Surat rujukan dikirimkan ke lembaga tujuan.')),

                Action::make('acceptReferral')
                    ->label('Diterima Lembaga')
                    ->icon(Heroicon::OutlinedCheck)
                    ->color('warning')
                    ->visible(fn (Referral $record) => $record->status === ReferralStatus::Sent)
                    ->requiresConfirmation()
                    ->action(fn (Referral $record) => $record->recordStatusChange(ReferralStatus::Accepted, 'Klien dan berkas rujukan telah diterima oleh lembaga tujuan.')),

                Action::make('completeReferral')
                    ->label('Rujukan Selesai')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (Referral $record) => in_array($record->status, [ReferralStatus::Accepted, ReferralStatus::InService]))
                    ->form([
                        Textarea::make('service_result')
                            ->label('Hasil Pelayanan dari Lembaga Rujukan')
                            ->required(),
                    ])
                    ->action(function (Referral $record, array $data): void {
                        $record->service_result = $data['service_result'];
                        $record->completed_at = now();
                        $record->recordStatusChange(ReferralStatus::Completed, 'Pelayanan rujukan selesai: '.$data['service_result']);
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
