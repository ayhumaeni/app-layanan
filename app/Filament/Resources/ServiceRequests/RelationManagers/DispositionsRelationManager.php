<?php

namespace App\Filament\Resources\ServiceRequests\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class DispositionsRelationManager extends RelationManager
{
    protected static string $relationship = 'dispositions';

    protected static ?string $title = 'Riwayat Disposisi Pelayanan';

    protected static ?string $modelLabel = 'Disposisi';

    protected static ?string $pluralModelLabel = 'Disposisi';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('from_user_id')
                    ->default(fn () => Auth::id()),
                DateTimePicker::make('disposed_at')
                    ->label('Waktu Disposisi')
                    ->default(now())
                    ->required(),
                Select::make('to_work_unit_id')
                    ->label('Disposisikan ke Unit Kerja')
                    ->relationship('toWorkUnit', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('to_user_id')
                    ->label('Penerima Disposisi (Petugas Khusus - Opsional)')
                    ->relationship('toUser', 'name')
                    ->searchable()
                    ->preload(),
                Textarea::make('instructions')
                    ->label('Instruksi / Catatan Disposisi')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('instructions')
            ->defaultSort('disposed_at', 'desc')
            ->columns([
                TextColumn::make('disposed_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('fromUser.name')
                    ->label('Dari')
                    ->searchable(),
                TextColumn::make('toWorkUnit.name')
                    ->label('Unit Tujuan')
                    ->badge()
                    ->searchable(),
                TextColumn::make('toUser.name')
                    ->label('Petugas')
                    ->placeholder('Semua Petugas Bidang')
                    ->searchable(),
                TextColumn::make('instructions')
                    ->label('Instruksi')
                    ->wrap(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Buat Disposisi Baru'),
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
