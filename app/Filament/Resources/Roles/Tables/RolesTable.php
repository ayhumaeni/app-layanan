<?php

namespace App\Filament\Resources\Roles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        $systemRoles = [
            'administrator',
            'petugas_dinsos',
            'pejabat_penandatangan',
            'pimpinan',
            'operator_kecamatan_desa',
            'masyarakat',
        ];

        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Peran')
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
                    })
                    ->description(fn ($record) => $record->name)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Jumlah Izin Akses')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Jumlah Pengguna')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('guard_name')
                    ->label('Guard')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, $record) use ($systemRoles) {
                        if (in_array($record->name, $systemRoles, true)) {
                            Notification::make()
                                ->title('Gagal Menghapus')
                                ->body('Peran bawaan sistem ('.$record->name.') tidak boleh dihapus!')
                                ->danger()
                                ->send();

                            $action->halt();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function ($records) use ($systemRoles) {
                            $deleted = 0;
                            foreach ($records as $record) {
                                if (! in_array($record->name, $systemRoles, true)) {
                                    $record->delete();
                                    $deleted++;
                                }
                            }

                            if ($deleted < count($records)) {
                                Notification::make()
                                    ->title('Sebagian Peran Dilewati')
                                    ->body('Peran bawaan sistem tidak dapat dihapus.')
                                    ->warning()
                                    ->send();
                            }
                        }),
                ]),
            ]);
    }
}
