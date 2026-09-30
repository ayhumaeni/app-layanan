<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Enums\PermissionType;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\PermissionRegistrar;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        $systemRoles = [
            'administrator',
            'petugas_dinsos',
            'pejabat_penandatangan',
            'pimpinan',
            'operator_kecamatan_desa',
            'masyarakat',
        ];

        return $schema
            ->components([
                Section::make('Informasi Peran')
                    ->description('Tentukan identitas peran pengguna di dalam sistem SAPA SOSIAL.')
                    ->components([
                        TextInput::make('name')
                            ->label('Nama Peran (Identifier)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->disabled(fn ($record) => $record && in_array($record->name, $systemRoles, true))
                            ->helperText(fn ($record) => $record && in_array($record->name, $systemRoles, true)
                                ? 'Peran bawaan sistem tidak dapat diubah namanya.'
                                : 'Gunakan huruf kecil dan garis bawah, contoh: operator_khusus, analis_data.'),
                        TextInput::make('guard_name')
                            ->label('Guard')
                            ->default('web')
                            ->required()
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(2),

                Section::make('Daftar Izin Akses (Permissions)')
                    ->description('Pilih izin akses yang diberikan untuk peran ini. Peran Administrator otomatis memiliki seluruh hak akses sistem.')
                    ->components([
                        CheckboxList::make('permissions')
                            ->label('Izin Akses')
                            ->relationship('permissions', 'name')
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                $enum = PermissionType::tryFrom($record->name);

                                return $enum ? "{$enum->label()} ({$record->name})" : $record->name;
                            })
                            ->getOptionDescriptionFromRecordUsing(function ($record) {
                                $enum = PermissionType::tryFrom($record->name);

                                return $enum ? "Grup: {$enum->group()}" : 'Grup: Umum';
                            })
                            ->columns(2)
                            ->gridDirection('row')
                            ->bulkToggleable()
                            ->searchable()
                            ->saveRelationshipsUsing(function ($record, $state) {
                                $record->syncPermissions($state ?? []);
                                app(PermissionRegistrar::class)->forgetCachedPermissions();
                            }),
                    ]),
            ]);
    }
}
