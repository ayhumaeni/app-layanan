<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Alamat Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('No. Handphone / WhatsApp')
                    ->tel()
                    ->maxLength(20),
                TextInput::make('nik')
                    ->label('NIK (16 Digit)')
                    ->length(16)
                    ->numeric(),
                Select::make('roles')
                    ->label('Peran / Hak Akses (Role)')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                Select::make('work_unit_id')
                    ->label('Unit Kerja / Bidang')
                    ->relationship('workUnit', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('district_id')
                    ->label('Wilayah Kecamatan (Operator)')
                    ->relationship('district', 'name')
                    ->searchable()
                    ->preload()
                    ->live(),
                Select::make('village_id')
                    ->label('Wilayah Desa / Kelurahan (Operator)')
                    ->relationship(
                        'village',
                        'name',
                        modifyQueryUsing: fn ($query, $get) => $get('district_id') ? $query->where('district_id', $get('district_id')) : $query
                    )
                    ->searchable()
                    ->preload(),
                Toggle::make('is_active')
                    ->label('Akun Aktif')
                    ->default(true)
                    ->required(),
            ]);
    }
}
