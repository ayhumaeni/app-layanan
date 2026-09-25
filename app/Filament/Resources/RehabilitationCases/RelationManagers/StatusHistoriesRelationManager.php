<?php

namespace App\Filament\Resources\RehabilitationCases\RelationManagers;

use App\Enums\RehabilitationCaseStatus;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StatusHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'statusHistories';

    protected static ?string $title = 'Riwayat Status Penanganan Kasus';

    protected static ?string $modelLabel = 'Riwayat Status';

    protected static ?string $pluralModelLabel = 'Riwayat Status';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('to_status')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i:s')
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('from_status')
                    ->label('Dari Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? (RehabilitationCaseStatus::tryFrom($state)?->label() ?? $state) : 'Awal'),
                TextColumn::make('to_status')
                    ->label('Menjadi Status')
                    ->badge()
                    ->color(fn ($state) => RehabilitationCaseStatus::tryFrom($state)?->color() ?? 'primary')
                    ->formatStateUsing(fn ($state) => RehabilitationCaseStatus::tryFrom($state)?->label() ?? $state),
                TextColumn::make('notes')
                    ->label('Catatan Keterangan')
                    ->wrap(),
                TextColumn::make('user.name')
                    ->label('Oleh')
                    ->placeholder('Sistem'),
            ])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
