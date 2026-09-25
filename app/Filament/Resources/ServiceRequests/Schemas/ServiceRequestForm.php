<?php

namespace App\Filament\Resources\ServiceRequests\Schemas;

use App\Enums\MinistryDecision;
use App\Enums\PbiReactivationReason;
use App\Enums\ServiceRequestStatus;
use App\Models\ServiceType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pemohon & Layanan')
                    ->description('Data identitas pemohon dan jenis layanan sosial yang diajukan')
                    ->components([
                        Select::make('service_type_id')
                            ->label('Jenis Layanan')
                            ->relationship('serviceType', 'name')
                            ->required()
                            ->live()
                            ->searchable()
                            ->preload(),
                        TextInput::make('request_number')
                            ->label('Nomor Tiket / Pengajuan')
                            ->placeholder('Otomatis dibuat sistem')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('applicant_name')
                            ->label('Nama Lengkap Pemohon')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('applicant_nik')
                            ->label('NIK Pemohon (16 Digit)')
                            ->required()
                            ->length(16),
                        TextInput::make('family_card_number')
                            ->label('Nomor Kartu Keluarga (KK)')
                            ->required()
                            ->length(16),
                        TextInput::make('phone')
                            ->label('Nomor WhatsApp / HP')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                        Select::make('village_id')
                            ->label('Desa / Kelurahan')
                            ->relationship('village', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Toggle::make('is_priority')
                            ->label('Prioritas Tinggi / Darurat')
                            ->helperText('Tandai jika kondisi membutuhkan penanganan darurat segera')
                            ->default(false),
                        Textarea::make('address')
                            ->label('Alamat Lengkap Pemohon')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Detail Surat Keterangan DTSEN')
                    ->description('Data pendukung khusus penerbitan Surat Keterangan DTSEN')
                    ->relationship('dtsenCertificate')
                    ->visible(function ($get): bool {
                        $serviceTypeId = $get('service_type_id');
                        if (! $serviceTypeId) {
                            return false;
                        }
                        $st = ServiceType::find($serviceTypeId);

                        return ($st?->handler?->value ?? $st?->handler) === 'dtsen';
                    })
                    ->components([
                        Select::make('dtsen_purpose_id')
                            ->label('Tujuan Penggunaan Surat')
                            ->relationship('dtsenPurpose', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        TextInput::make('subject_name')
                            ->label('Nama Orang yang Diterangkan')
                            ->helperText('Nama siswa / mahasiswa / anggota keluarga yang bersangkutan')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('subject_nik')
                            ->label('NIK Orang yang Diterangkan')
                            ->required()
                            ->length(16),
                        TextInput::make('relationship_to_applicant')
                            ->label('Hubungan dengan Pemohon')
                            ->placeholder('Contoh: Anak Kandung, Diri Sendiri, Istri')
                            ->required()
                            ->maxLength(100),
                        Toggle::make('is_registered')
                            ->label('Terdaftar dalam DTSEN / SIKS-NG')
                            ->default(false),
                        TextInput::make('decile')
                            ->label('Peringkat Desil SIKS-NG (1-10)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10),
                        TextInput::make('certificate_number')
                            ->label('Nomor Surat Keterangan')
                            ->placeholder('Diterbitkan saat disetujui Kadis')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('verification_code')
                            ->label('Kode Verifikasi QR')
                            ->placeholder('Otomatis dibuat saat surat terbit')
                            ->disabled()
                            ->dehydrated(false),
                        Textarea::make('purpose_description')
                            ->label('Keterangan Keperluan')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Detail Reaktivasi KIS / PBI-JK')
                    ->description('Data kepesertaan BPJS PBI dan riwayat fasilitas kesehatan')
                    ->relationship('pbiReactivation')
                    ->visible(function ($get): bool {
                        $serviceTypeId = $get('service_type_id');
                        if (! $serviceTypeId) {
                            return false;
                        }
                        $st = ServiceType::find($serviceTypeId);

                        return ($st?->handler?->value ?? $st?->handler) === 'pbi';
                    })
                    ->components([
                        TextInput::make('participant_name')
                            ->label('Nama Peserta JKN-KIS')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('participant_nik')
                            ->label('NIK Peserta')
                            ->required()
                            ->length(16),
                        TextInput::make('bpjs_card_number')
                            ->label('Nomor Kartu BPJS / KIS')
                            ->required()
                            ->maxLength(30),
                        DatePicker::make('deactivated_date')
                            ->label('Perkiraan Tanggal Nonaktif'),
                        Select::make('reason')
                            ->label('Alasan Reaktivasi')
                            ->options(PbiReactivationReason::class)
                            ->required()
                            ->live(),
                        TextInput::make('health_facility_name')
                            ->label('Nama Fasilitas Kesehatan / RS')
                            ->helperText('Wajib jika alasan darurat medis/penyakit kronis')
                            ->maxLength(255),
                        TextInput::make('health_letter_number')
                            ->label('Nomor Surat Keterangan Faskes')
                            ->maxLength(100),
                        TextInput::make('decile')
                            ->label('Peringkat Desil Kelayakan')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10),
                        TextInput::make('recommendation_number')
                            ->label('Nomor Surat Rekomendasi')
                            ->placeholder('Otomatis saat rekomendasi terbit')
                            ->disabled()
                            ->dehydrated(false),
                        DateTimePicker::make('proposed_to_ministry_at')
                            ->label('Tanggal Usulan Input SIKS-NG Kemensos'),
                        Select::make('ministry_decision')
                            ->label('Keputusan Kemensos')
                            ->options(MinistryDecision::class),
                        DatePicker::make('reactivated_date')
                            ->label('Tanggal Aktif Kembali di BPJS'),
                        Textarea::make('eligibility_notes')
                            ->label('Catatan Verifikasi Kelayakan')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Penugasan & Status Pelayanan')
                    ->description('Tahapan pemrosesan, disposisi, dan hasil layanan')
                    ->components([
                        Select::make('status')
                            ->label('Status Pengajuan')
                            ->options(ServiceRequestStatus::class)
                            ->default(ServiceRequestStatus::SUBMITTED)
                            ->required(),
                        Select::make('officer_id')
                            ->label('Petugas Penanggung Jawab')
                            ->relationship('officer', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('work_unit_id')
                            ->label('Unit Kerja / Bidang')
                            ->relationship('workUnit', 'name')
                            ->searchable()
                            ->preload(),
                        DateTimePicker::make('completed_at')
                            ->label('Waktu Selesai'),
                        Textarea::make('verification_result')
                            ->label('Hasil Verifikasi Dokumen / Data'),
                        Textarea::make('officer_notes')
                            ->label('Catatan Petugas'),
                        Textarea::make('assessment_notes')
                            ->label('Catatan Assessment Lapangan'),
                        Textarea::make('service_result')
                            ->label('Hasil Pelayanan Akhir'),
                        Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan (Bila Ditolak)')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
