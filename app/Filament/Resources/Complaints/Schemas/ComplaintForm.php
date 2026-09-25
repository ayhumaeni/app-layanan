<?php

namespace App\Filament\Resources\Complaints\Schemas;

use App\Enums\ComplaintStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ComplaintForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Laporan & Pelapor')
                    ->description('Detail permasalahan sosial dan identitas pelapor')
                    ->components([
                        TextInput::make('complaint_number')
                            ->label('Nomor Laporan Pengaduan')
                            ->placeholder('Otomatis dibuat (ADU-...)')
                            ->disabled()
                            ->dehydrated(false),
                        Select::make('complaint_category_id')
                            ->label('Kategori Permasalahan Sosial')
                            ->relationship('complaintCategory', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        TextInput::make('reporter_name')
                            ->label('Nama Lengkap Pelapor')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('reporter_phone')
                            ->label('Nomor WhatsApp / HP Pelapor')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                        Select::make('village_id')
                            ->label('Desa / Kelurahan Lokasi Kejadian')
                            ->relationship('village', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        DateTimePicker::make('reported_at')
                            ->label('Waktu Laporan Diterima')
                            ->default(now())
                            ->required(),
                        Textarea::make('location_detail')
                            ->label('Alamat / Titik Lokasi Kejadian')
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Uraian Permasalahan / Keluhan Sosial')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Pemeriksaan & Tindak Lanjut')
                    ->description('Catatan verifikasi lapangan, penanganan, dan penyelesaian laporan')
                    ->components([
                        Select::make('status')
                            ->label('Status Pengaduan')
                            ->options(ComplaintStatus::class)
                            ->default(ComplaintStatus::Received)
                            ->required(),
                        Select::make('officer_id')
                            ->label('Petugas yang Ditugaskan')
                            ->relationship('officer', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('duplicate_of_id')
                            ->label('Merupakan Duplikat Dari Laporan (Bila Ada)')
                            ->relationship('duplicateOf', 'complaint_number')
                            ->searchable()
                            ->preload(),
                        DateTimePicker::make('resolved_at')
                            ->label('Waktu Pengaduan Selesai'),
                        Textarea::make('verification_result')
                            ->label('Hasil Verifikasi Awal Lapangan')
                            ->columnSpanFull(),
                        Textarea::make('action_taken')
                            ->label('Tindakan & Solusi Penanganan yang Telah Dilakukan (Wajib sebelum selesai)')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
