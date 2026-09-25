<?php

namespace App\Filament\Resources\ServiceTypes\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RequirementsRelationManager extends RelationManager
{
    protected static string $relationship = 'requirements';

    protected static ?string $title = 'Persyaratan Berkas Dokumen';

    protected static ?string $modelLabel = 'Persyaratan';

    protected static ?string $pluralModelLabel = 'Persyaratan';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Berkas Persyaratan')
                    ->required()
                    ->maxLength(255),
                Toggle::make('is_mandatory')
                    ->label('Wajib Diunggah')
                    ->default(true),
                TextInput::make('allowed_mimes')
                    ->label('Format File yang Diizinkan (contoh: pdf,jpg,png)')
                    ->required()
                    ->default('pdf,jpg,jpeg,png'),
                TextInput::make('sort_order')
                    ->label('Urutan Tampilan')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama Dokumen')
                    ->searchable(),
                IconColumn::make('is_mandatory')
                    ->label('Wajib')
                    ->boolean(),
                TextColumn::make('allowed_mimes')
                    ->label('Format File')
                    ->badge(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Persyaratan'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
