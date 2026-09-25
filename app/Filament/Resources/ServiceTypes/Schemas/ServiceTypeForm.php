<?php

namespace App\Filament\Resources\ServiceTypes\Schemas;

use App\Enums\ServiceRequestHandler;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServiceTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Kode Layanan')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50),
                TextInput::make('name')
                    ->label('Nama Layanan')
                    ->required()
                    ->maxLength(255),
                TextInput::make('category')
                    ->label('Kategori Layanan')
                    ->maxLength(100),
                Select::make('handler')
                    ->label('Tipe Alur Layanan')
                    ->options(ServiceRequestHandler::class)
                    ->default(ServiceRequestHandler::GENERIC)
                    ->required(),
                TextInput::make('sla_days')
                    ->label('Target Waktu SLA (Hari Kerja)')
                    ->numeric()
                    ->minValue(1)
                    ->nullable(),
                Toggle::make('needs_assessment')
                    ->label('Memerlukan Assessment')
                    ->default(false),
                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true)
                    ->required(),
                Textarea::make('description')
                    ->label('Deskripsi Layanan')
                    ->columnSpanFull(),
            ]);
    }
}
