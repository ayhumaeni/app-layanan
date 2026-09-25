<?php

namespace App\Filament\Resources\RehabilitationCases\Schemas;

use App\Enums\HandlingType;
use App\Enums\RehabilitationCaseStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RehabilitationCaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kasus & Identitas Klien')
                    ->description('Data klien, penanggung jawab kasus, dan sumber laporan')
                    ->components([
                        TextInput::make('case_number')
                            ->label('Nomor Kasus')
                            ->placeholder('Otomatis dibuat sistem (RHS-...)')
                            ->disabled()
                            ->dehydrated(false),
                        Select::make('client_id')
                            ->label('Klien Rehabilitasi')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('officer_id')
                            ->label('Petugas Penanggung Jawab Kasus')
                            ->relationship('officer', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('handling_type')
                            ->label('Bentuk Rencana Penanganan')
                            ->options(HandlingType::class)
                            ->default(HandlingType::Direct)
                            ->required(),
                        Select::make('service_request_id')
                            ->label('Rujukan dari Pengajuan Layanan (Bila Ada)')
                            ->relationship('serviceRequest', 'request_number')
                            ->searchable()
                            ->preload(),
                        Select::make('complaint_id')
                            ->label('Berasal dari Laporan Pengaduan (Bila Ada)')
                            ->relationship('complaint', 'complaint_number')
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                Section::make('Status & Riwayat Penanganan')
                    ->description('Tahapan penanganan rehabilitasi dari penerimaan hingga penutupan kasus')
                    ->components([
                        Select::make('status')
                            ->label('Status Kasus')
                            ->options(RehabilitationCaseStatus::class)
                            ->default(RehabilitationCaseStatus::Received)
                            ->required(),
                        DateTimePicker::make('received_at')
                            ->label('Waktu Kasus Diterima')
                            ->default(now())
                            ->required(),
                        DateTimePicker::make('closed_at')
                            ->label('Waktu Kasus Ditutup / Selesai'),
                        Textarea::make('handling_result')
                            ->label('Hasil Akhir Pelayanan / Penanganan (Wajib sebelum kasus ditutup)')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
