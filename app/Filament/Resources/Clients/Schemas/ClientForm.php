<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Lengkap Klien')
                    ->required()
                    ->maxLength(255),
                Select::make('client_category_id')
                    ->label('Kategori Klien')
                    ->relationship('clientCategory', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('nik')
                    ->label('NIK (Opsional jika tanpa identitas/terlantar)')
                    ->length(16),
                DatePicker::make('birth_date')
                    ->label('Tanggal Lahir / Perkiraan Umur'),
                Select::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ])
                    ->required(),
                TextInput::make('phone')
                    ->label('Kontak / No. Telepon (Bila Ada)')
                    ->tel()
                    ->maxLength(20),
                Select::make('village_id')
                    ->label('Desa / Kelurahan')
                    ->relationship('village', 'name')
                    ->searchable()
                    ->preload(),
                Textarea::make('address')
                    ->label('Alamat / Lokasi Ditemukan')
                    ->columnSpanFull(),
            ]);
    }
}
