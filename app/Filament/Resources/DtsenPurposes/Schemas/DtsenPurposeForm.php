<?php

namespace App\Filament\Resources\DtsenPurposes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DtsenPurposeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Kode Tujuan')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50),
                TextInput::make('name')
                    ->label('Nama Tujuan Penggunaan')
                    ->required()
                    ->maxLength(255),
                TextInput::make('max_decile')
                    ->label('Batas Maksimal Desil')
                    ->helperText('Surat keterangan hanya dapat terbit jika desil pemohon ≤ batas ini (1-10)')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(10)
                    ->default(5),
                TextInput::make('validity_days')
                    ->label('Masa Berlaku (Hari)')
                    ->helperText('Kosongkan jika berlaku tanpa batas waktu')
                    ->numeric()
                    ->minValue(1)
                    ->nullable(),
                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true)
                    ->required(),
            ]);
    }
}
