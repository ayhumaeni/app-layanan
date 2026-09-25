<?php

namespace App\Filament\Resources\RehabilitationCases\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class MonitoringRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'monitoringRecords';

    protected static ?string $title = 'Catatan Monitoring & Perkembangan';

    protected static ?string $modelLabel = 'Catatan Monitoring';

    protected static ?string $pluralModelLabel = 'Catatan Monitoring';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('officer_id')
                    ->label('Petugas yang Melakukan Monitoring')
                    ->relationship('officer', 'name')
                    ->default(fn () => Auth::id())
                    ->required()
                    ->searchable()
                    ->preload(),
                DatePicker::make('monitoring_date')
                    ->label('Tanggal Monitoring')
                    ->default(now())
                    ->required(),
                Select::make('referral_id')
                    ->label('Terkait Rujukan Lembaga Tertentu (Opsional)')
                    ->relationship('referral', 'referral_number', fn ($query) => $query->where('rehabilitation_case_id', $this->getOwnerRecord()->id))
                    ->searchable()
                    ->preload(),
                Textarea::make('progress')
                    ->label('Catatan Perkembangan & Kondisi Klien Saat Ini')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('result_notes')
                    ->label('Catatan Hasil Evaluasi & Tindak Lanjut')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('progress')
            ->defaultSort('monitoring_date', 'desc')
            ->columns([
                TextColumn::make('monitoring_date')
                    ->label('Tgl Monitoring')
                    ->date('d M Y')
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('officer.name')
                    ->label('Petugas')
                    ->searchable(),
                TextColumn::make('referral.referral_number')
                    ->label('Rujukan Terkait')
                    ->placeholder('Internal')
                    ->badge(),
                TextColumn::make('progress')
                    ->label('Perkembangan')
                    ->limit(60)
                    ->wrap(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Catatan Monitoring'),
            ])
            ->recordActions([
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
