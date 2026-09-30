<?php

namespace App\Filament\Resources\Permissions\Tables;

use App\Enums\PermissionType;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PermissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Kode Izin')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('description')
                    ->label('Deskripsi / Label')
                    ->state(fn ($record) => PermissionType::tryFrom($record->name)?->label() ?? $record->name)
                    ->searchable(query: function ($query, string $search) {
                        $matchedValues = [];
                        foreach (PermissionType::cases() as $case) {
                            if (str_contains(strtolower($case->label()), strtolower($search))) {
                                $matchedValues[] = $case->value;
                            }
                        }

                        return $query->whereIn('name', $matchedValues);
                    }),

                TextColumn::make('group')
                    ->label('Kategori')
                    ->badge()
                    ->state(fn ($record) => PermissionType::tryFrom($record->name)?->group() ?? 'Umum')
                    ->color(fn (string $state): string => match ($state) {
                        'Pengaturan Pengguna' => 'danger',
                        'Pelayanan' => 'success',
                        'Rehabilitasi Sosial' => 'warning',
                        'Pengaduan' => 'info',
                        'Data Master' => 'primary',
                        'Dashboard & Laporan' => 'secondary',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('roles.name')
                    ->label('Dimiliki oleh Peran')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'administrator' => 'Administrator',
                        'petugas_dinsos' => 'Petugas Dinsos',
                        'pejabat_penandatangan' => 'Pejabat Penandatangan',
                        'pimpinan' => 'Pimpinan',
                        'operator_kecamatan_desa' => 'Operator Kec/Desa',
                        'masyarakat' => 'Masyarakat',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'administrator' => 'danger',
                        'pejabat_penandatangan' => 'warning',
                        'pimpinan' => 'primary',
                        'petugas_dinsos' => 'success',
                        'operator_kecamatan_desa' => 'info',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label('Filter Peran')
                    ->relationship('roles', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => match ($record->name) {
                        'administrator' => 'Administrator',
                        'petugas_dinsos' => 'Petugas Dinsos',
                        'pejabat_penandatangan' => 'Pejabat Penandatangan',
                        'pimpinan' => 'Pimpinan',
                        'operator_kecamatan_desa' => 'Operator Kec/Desa',
                        'masyarakat' => 'Masyarakat',
                        default => $record->name,
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
