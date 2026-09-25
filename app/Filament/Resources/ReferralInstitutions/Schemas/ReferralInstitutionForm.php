<?php

namespace App\Filament\Resources\ReferralInstitutions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ReferralInstitutionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Lembaga Rujukan')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->label('Jenis Lembaga')
                    ->options([
                        'panti' => 'Panti Sosial',
                        'balai' => 'Balai Rehabilitasi',
                        'RS' => 'Rumah Sakit / Faskes',
                        'LKS' => 'Lembaga Kesejahteraan Sosial (LKS)',
                        'lainnya' => 'Lainnya',
                    ])
                    ->required(),
                TextInput::make('contact')
                    ->label('Kontak / Narahubung')
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true)
                    ->required(),
                Textarea::make('address')
                    ->label('Alamat Lembaga')
                    ->columnSpanFull(),
            ]);
    }
}
