<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Pengguna')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Peran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'administrator' => 'danger',
                        'pejabat_penandatangan' => 'warning',
                        'pimpinan' => 'primary',
                        'petugas_dinsos' => 'success',
                        'operator_kecamatan_desa' => 'info',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('workUnit.name')
                    ->label('Unit Kerja')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('district.name')
                    ->label('Wilayah')
                    ->formatStateUsing(fn ($record) => $record->village ? "{$record->district?->name} / {$record->village->name}" : ($record->district?->name ?? '-'))
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label('Filter Peran')
                    ->relationship('roles', 'name'),
                SelectFilter::make('work_unit_id')
                    ->label('Filter Unit Kerja')
                    ->relationship('workUnit', 'name'),
                SelectFilter::make('district_id')
                    ->label('Filter Kecamatan')
                    ->relationship('district', 'name'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
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
