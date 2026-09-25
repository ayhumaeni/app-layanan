<?php

namespace App\Filament\Resources\ServiceRequests\RelationManagers;

use App\Enums\DocumentVerificationStatus;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Berkas Persyaratan yang Diunggah';

    protected static ?string $modelLabel = 'Dokumen Persyaratan';

    protected static ?string $pluralModelLabel = 'Dokumen Persyaratan';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('service_requirement_id')
                    ->label('Jenis Berkas Persyaratan')
                    ->relationship('serviceRequirement', 'name')
                    ->required()
                    ->preload(),
                FileUpload::make('file_path')
                    ->label('Unggah File Dokumen')
                    ->disk('private')
                    ->directory('service_documents')
                    ->required()
                    ->storeFileNamesIn('original_name'),
                TextInput::make('original_name')
                    ->label('Nama Asli File')
                    ->disabled()
                    ->dehydrated(),
                Select::make('verification_status')
                    ->label('Status Verifikasi')
                    ->options(DocumentVerificationStatus::class)
                    ->default(DocumentVerificationStatus::Pending)
                    ->required(),
                Textarea::make('notes')
                    ->label('Catatan Petugas Pemeriksa')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('original_name')
            ->columns([
                TextColumn::make('serviceRequirement.name')
                    ->label('Jenis Persyaratan')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('original_name')
                    ->label('Nama File')
                    ->searchable(),
                TextColumn::make('verification_status')
                    ->label('Status Berkas')
                    ->badge()
                    ->color(fn ($state) => match ($state?->value ?? $state) {
                        'valid' => 'success',
                        'revision_needed' => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn ($state) => match ($state?->value ?? $state) {
                        'valid' => 'Sesuai / Valid',
                        'revision_needed' => 'Perlu Perbaikan',
                        default => 'Menunggu Verifikasi',
                    }),
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->label('Diunggah')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Unggah Dokumen'),
            ])
            ->recordActions([
                Action::make('markValid')
                    ->label('Valid')
                    ->icon(Heroicon::OutlinedCheck)
                    ->color('success')
                    ->action(fn ($record) => $record->update(['verification_status' => 'valid'])),
                Action::make('markRevision')
                    ->label('Minta Revisi')
                    ->icon(Heroicon::OutlinedXMark)
                    ->color('danger')
                    ->form([
                        Textarea::make('notes')
                            ->label('Alasan berkas perlu diperbaiki')
                            ->required(),
                    ])
                    ->action(fn ($record, array $data) => $record->update([
                        'verification_status' => 'revision_needed',
                        'notes' => $data['notes'],
                    ])),
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
