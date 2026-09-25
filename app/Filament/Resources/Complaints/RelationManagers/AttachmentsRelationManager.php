<?php

namespace App\Filament\Resources\Complaints\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    protected static ?string $title = 'Foto & Dokumen Bukti Pendukung';

    protected static ?string $modelLabel = 'Lampiran';

    protected static ?string $pluralModelLabel = 'Lampiran';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label('Jenis Lampiran')
                    ->options([
                        'photo' => 'Foto Dokumentasi Lokasi / Kejadian',
                        'document' => 'Dokumen Pendukung (PDF/Surat)',
                    ])
                    ->default('photo')
                    ->required(),
                FileUpload::make('file_path')
                    ->label('File Bukti / Foto')
                    ->disk('private')
                    ->directory('complaint_attachments')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file_path')
            ->columns([
                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === 'photo' ? 'Foto Lokasi' : 'Dokumen'),
                TextColumn::make('file_path')
                    ->label('Nama / Path Berkas')
                    ->limit(50),
                TextColumn::make('created_at')
                    ->label('Diunggah')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Unggah Bukti Baru'),
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
