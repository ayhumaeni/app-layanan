<?php

namespace App\Filament\Resources\RehabilitationCases\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AssessmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assessments';

    protected static ?string $title = 'Hasil Assessment Klien';

    protected static ?string $modelLabel = 'Assessment';

    protected static ?string $pluralModelLabel = 'Assessment';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('officer_id')
                    ->label('Petugas yang Melakukan Assessment')
                    ->relationship('officer', 'name')
                    ->default(fn () => Auth::id())
                    ->required()
                    ->searchable()
                    ->preload(),
                DatePicker::make('assessment_date')
                    ->label('Tanggal Pelaksanaan Assessment')
                    ->default(now())
                    ->required(),
                Toggle::make('needs_referral')
                    ->label('Memerlukan Rujukan ke Lembaga Eksternal?')
                    ->helperText('Wajib dicentang jika penanganan kasus membutuhkan rujukan ke Panti/Balai/RS')
                    ->default(false),
                Textarea::make('result')
                    ->label('Hasil Assessment Kondisi Fisik & Mental Klien')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('service_needs')
                    ->label('Kebutuhan Pelayanan yang Diidentifikasi')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('recommendation')
                    ->label('Rekomendasi Rencana Penanganan')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('result')
            ->defaultSort('assessment_date', 'desc')
            ->columns([
                TextColumn::make('assessment_date')
                    ->label('Tgl Assessment')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('officer.name')
                    ->label('Petugas')
                    ->searchable(),
                IconColumn::make('needs_referral')
                    ->label('Perlu Rujukan?')
                    ->boolean(),
                TextColumn::make('recommendation')
                    ->label('Rekomendasi')
                    ->limit(50),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Catat Assessment Baru'),
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
