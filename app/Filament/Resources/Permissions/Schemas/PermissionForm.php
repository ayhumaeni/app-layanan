<?php

namespace App\Filament\Resources\Permissions\Schemas;

use App\Enums\PermissionType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\PermissionRegistrar;

class PermissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Izin Akses')
                    ->description('Detail kode dan penamaan izin akses sistem.')
                    ->components([
                        TextInput::make('name')
                            ->label('Kode Permission')
                            ->required()
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('label_display')
                            ->label('Nama / Deskripsi')
                            ->formatStateUsing(fn ($record) => $record ? (PermissionType::tryFrom($record->name)?->label() ?? $record->name) : '')
                            ->disabled(),
                        TextInput::make('group_display')
                            ->label('Kategori / Grup')
                            ->formatStateUsing(fn ($record) => $record ? (PermissionType::tryFrom($record->name)?->group() ?? 'Umum') : '')
                            ->disabled(),
                        TextInput::make('guard_name')
                            ->label('Guard')
                            ->default('web')
                            ->disabled(),
                    ])
                    ->columns(2),

                Section::make('Penetapan Peran (Roles)')
                    ->description('Atur peran mana saja yang memiliki izin akses ini.')
                    ->components([
                        Select::make('roles')
                            ->label('Diberikan kepada Peran (Roles)')
                            ->relationship('roles', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => match ($record->name) {
                                'administrator' => 'Administrator',
                                'petugas_dinsos' => 'Petugas Dinsos',
                                'pejabat_penandatangan' => 'Pejabat Penandatangan',
                                'pimpinan' => 'Pimpinan',
                                'operator_kecamatan_desa' => 'Operator Kec/Desa',
                                'masyarakat' => 'Masyarakat',
                                default => $record->name,
                            })
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->saveRelationshipsUsing(function ($record, $state) {
                                $record->syncRoles($state ?? []);
                                app(PermissionRegistrar::class)->forgetCachedPermissions();
                            }),
                    ]),
            ]);
    }
}
