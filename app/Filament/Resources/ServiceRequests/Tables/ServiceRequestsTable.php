<?php

namespace App\Filament\Resources\ServiceRequests\Tables;

use App\Enums\MinistryDecision;
use App\Enums\ServiceRequestStatus;
use App\Models\Approval;
use App\Models\NumberSequence;
use App\Models\ServiceRequest;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ServiceRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('request_number')
                    ->label('No. Tiket')
                    ->searchable()
                    ->copyable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('serviceType.name')
                    ->label('Layanan')
                    ->searchable()
                    ->badge()
                    ->color(fn ($record) => match ($record->serviceType?->handler?->value ?? $record->serviceType?->handler) {
                        'dtsen' => 'warning',
                        'pbi' => 'success',
                        default => 'info',
                    })
                    ->sortable(),
                TextColumn::make('applicant_name')
                    ->label('Nama Pemohon')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('applicant_nik')
                    ->label('NIK')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label('WhatsApp')
                    ->searchable(),
                TextColumn::make('village.district.name')
                    ->label('Kecamatan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('village.name')
                    ->label('Desa / Kelurahan')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => $state instanceof ServiceRequestStatus ? $state->color() : ($state ? ServiceRequestStatus::tryFrom($state)?->color() ?? 'gray' : 'gray'))
                    ->formatStateUsing(fn ($state) => $state instanceof ServiceRequestStatus ? $state->label() : ($state ? ServiceRequestStatus::tryFrom($state)?->label() ?? $state : '-')),
                IconColumn::make('is_priority')
                    ->label('Prioritas')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedExclamationTriangle)
                    ->trueColor('danger')
                    ->falseIcon(null),
                TextColumn::make('officer.name')
                    ->label('Petugas')
                    ->placeholder('Belum ditugaskan')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('submitted_at')
                    ->label('Tgl Masuk')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('submitted_at', 'desc')
            ->filters([
                SelectFilter::make('service_type_id')
                    ->label('Jenis Layanan')
                    ->relationship('serviceType', 'name')
                    ->preload(),
                SelectFilter::make('status')
                    ->label('Status Layanan')
                    ->options(collect(ServiceRequestStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
                TernaryFilter::make('is_priority')
                    ->label('Hanya Prioritas / Darurat'),
                SelectFilter::make('district')
                    ->label('Kecamatan')
                    ->relationship('village.district', 'name')
                    ->preload(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),

                    // Workflow 1: Mulai Periksa Berkas (submitted -> document_check)
                    Action::make('checkDocuments')
                        ->label('Periksa Berkas')
                        ->icon(Heroicon::OutlinedDocumentMagnifyingGlass)
                        ->color('info')
                        ->visible(fn (ServiceRequest $record) => $record->status === ServiceRequestStatus::Submitted)
                        ->requiresConfirmation()
                        ->action(function (ServiceRequest $record): void {
                            $record->officer_id = $record->officer_id ?? Auth::id();
                            $record->recordStatusChange(
                                ServiceRequestStatus::DocumentCheck,
                                'Petugas mulai memeriksa kelengkapan berkas fisik / digital.',
                                Auth::id()
                            );
                            Notification::make()
                                ->title('Status diperbarui: Pemeriksaan Berkas')
                                ->success()
                                ->send();
                        }),

                    // Workflow 2: Minta Revisi Berkas (document_check -> revision_requested)
                    Action::make('requestRevision')
                        ->label('Minta Perbaikan Berkas')
                        ->icon(Heroicon::OutlinedArrowPath)
                        ->color('warning')
                        ->visible(fn (ServiceRequest $record) => in_array($record->status, [ServiceRequestStatus::DocumentCheck, ServiceRequestStatus::DataVerification, ServiceRequestStatus::EligibilityVerification]))
                        ->form([
                            Textarea::make('notes')
                                ->label('Catatan Kekurangan Berkas / Data yang Harus Diperbaiki')
                                ->required(),
                        ])
                        ->action(function (ServiceRequest $record, array $data): void {
                            $record->recordStatusChange(
                                ServiceRequestStatus::RevisionRequested,
                                'Perbaikan berkas diminta: '.$data['notes'],
                                Auth::id()
                            );
                            Notification::make()
                                ->title('Permintaan perbaikan berkas telah dicatat')
                                ->warning()
                                ->send();
                        }),

                    // Workflow 3: DTSEN - Input Hasil Verifikasi SIKS-NG (document_check -> data_verification)
                    Action::make('verifyDtsen')
                        ->label('Input Cek SIKS-NG (DTSEN)')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->color('primary')
                        ->visible(function (ServiceRequest $record): bool {
                            $handler = $record->serviceType?->handler?->value ?? $record->serviceType?->handler;

                            return $handler === 'dtsen' && in_array($record->status, [ServiceRequestStatus::DocumentCheck, ServiceRequestStatus::DataVerification]);
                        })
                        ->form([
                            Toggle::make('is_registered')
                                ->label('Terdaftar di SIKS-NG / DTSEN?')
                                ->default(fn (ServiceRequest $record) => $record->dtsenCertificate?->is_registered ?? true)
                                ->required(),
                            TextInput::make('decile')
                                ->label('Peringkat Desil (1-10)')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(10)
                                ->default(fn (ServiceRequest $record) => $record->dtsenCertificate?->decile)
                                ->required(),
                            Textarea::make('notes')
                                ->label('Catatan Pengecekan SIKS-NG')
                                ->default('Data telah diperiksa di SIKS-NG Kabupaten Blitar.'),
                        ])
                        ->action(function (ServiceRequest $record, array $data): void {
                            $cert = $record->dtsenCertificate()->firstOrCreate(['service_request_id' => $record->id]);
                            $cert->is_registered = $data['is_registered'];
                            $cert->decile = (int) $data['decile'];
                            $cert->checked_at = now();
                            $cert->checker_id = Auth::id();
                            $cert->save();

                            $record->recordStatusChange(
                                ServiceRequestStatus::DataVerification,
                                "Pengecekan SIKS-NG selesai: Desil {$data['decile']}, Terdaftar: ".($data['is_registered'] ? 'Ya' : 'Tidak'),
                                Auth::id()
                            );

                            Notification::make()
                                ->title('Hasil pengecekan SIKS-NG berhasil disimpan')
                                ->success()
                                ->send();
                        }),

                    // Workflow 4: DTSEN - Ajukan Persetujuan Kadis / Kabid (data_verification -> awaiting_approval)
                    Action::make('submitApprovalDtsen')
                        ->label('Ajukan Persetujuan Surat')
                        ->icon(Heroicon::OutlinedPaperAirplane)
                        ->color('success')
                        ->visible(function (ServiceRequest $record): bool {
                            $handler = $record->serviceType?->handler?->value ?? $record->serviceType?->handler;

                            return $handler === 'dtsen' && $record->status === ServiceRequestStatus::DataVerification;
                        })
                        ->requiresConfirmation()
                        ->action(function (ServiceRequest $record): void {
                            $cert = $record->dtsenCertificate;
                            if (! $cert || ! $cert->checked_at) {
                                Notification::make()
                                    ->title('Hasil verifikasi SIKS-NG wajib diisi terlebih dahulu!')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $maxDecile = $cert->dtsenPurpose?->max_decile ?? 5;
                            if ($cert->decile > $maxDecile) {
                                Notification::make()
                                    ->title("Desil ({$cert->decile}) melebihi batas maksimal ({$maxDecile}) untuk tujuan ini. Pengajuan harus ditolak.")
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $record->recordStatusChange(
                                ServiceRequestStatus::AwaitingApproval,
                                'Draf Surat Keterangan diajukan untuk paraf Kabid dan tanda tangan Kadis.',
                                Auth::id()
                            );

                            Notification::make()
                                ->title('Draf surat diajukan untuk persetujuan')
                                ->success()
                                ->send();
                        }),

                    // Workflow 5: DTSEN - Tanda Tangan Kadis & Terbitkan Surat (awaiting_approval -> issued)
                    Action::make('issueCertificate')
                        ->label('Tanda Tangan & Terbitkan SK')
                        ->icon(Heroicon::OutlinedSparkles)
                        ->color('success')
                        ->visible(function (ServiceRequest $record): bool {
                            $handler = $record->serviceType?->handler?->value ?? $record->serviceType?->handler;

                            return $handler === 'dtsen' && $record->status === ServiceRequestStatus::AwaitingApproval;
                        })
                        ->requiresConfirmation()
                        ->action(function (ServiceRequest $record): void {
                            $cert = $record->dtsenCertificate;
                            if (! $cert) {
                                return;
                            }

                            $certNumber = NumberSequence::nextNumber('400.9', now('Asia/Jakarta')->format('Y'), 4).'/409.105/'.now('Asia/Jakarta')->format('Y');
                            $verificationCode = strtoupper(Str::random(10));

                            $cert->certificate_number = $certNumber;
                            $cert->verification_code = $verificationCode;
                            $cert->issued_at = now();
                            $cert->signer_id = Auth::id();

                            if ($cert->dtsenPurpose?->validity_days) {
                                $cert->valid_until = now()->addDays($cert->dtsenPurpose->validity_days)->toDateString();
                            }

                            $cert->save();

                            // Record approval
                            Approval::create([
                                'approvable_type' => ServiceRequest::class,
                                'approvable_id' => $record->id,
                                'step' => 2,
                                'approver_id' => Auth::id(),
                                'decision' => 'approved',
                                'notes' => 'Surat Keterangan DTSEN disetujui dan ditandatangani.',
                                'decided_at' => now(),
                            ]);

                            $record->recordStatusChange(
                                ServiceRequestStatus::Issued,
                                "Surat Keterangan DTSEN diterbitkan dengan No: {$certNumber}, Kode Verifikasi: {$verificationCode}",
                                Auth::id()
                            );

                            Notification::make()
                                ->title("Surat berhasil diterbitkan: {$certNumber}")
                                ->success()
                                ->send();
                        }),

                    // Workflow 6: PBI - Verifikasi Kelayakan (document_check -> eligibility_verification)
                    Action::make('verifyPbi')
                        ->label('Verifikasi Kelayakan PBI')
                        ->icon(Heroicon::OutlinedClipboardDocumentCheck)
                        ->color('primary')
                        ->visible(function (ServiceRequest $record): bool {
                            $handler = $record->serviceType?->handler?->value ?? $record->serviceType?->handler;

                            return $handler === 'pbi' && in_array($record->status, [ServiceRequestStatus::DocumentCheck, ServiceRequestStatus::EligibilityVerification]);
                        })
                        ->form([
                            TextInput::make('decile')
                                ->label('Peringkat Desil')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(10)
                                ->default(fn (ServiceRequest $record) => $record->pbiReactivation?->decile)
                                ->required(),
                            Textarea::make('eligibility_notes')
                                ->label('Catatan Verifikasi Kelayakan Peserta')
                                ->default('Peserta memenuhi syarat reaktivasi jaminan kesehatan PBI-JK.')
                                ->required(),
                        ])
                        ->action(function (ServiceRequest $record, array $data): void {
                            $pbi = $record->pbiReactivation()->firstOrCreate(['service_request_id' => $record->id]);
                            $pbi->decile = (int) $data['decile'];
                            $pbi->eligibility_notes = $data['eligibility_notes'];
                            $pbi->save();

                            $record->recordStatusChange(
                                ServiceRequestStatus::EligibilityVerification,
                                'Verifikasi kelayakan selesai: Desil '.$data['decile'].'. Catatan: '.$data['eligibility_notes'],
                                Auth::id()
                            );

                            Notification::make()
                                ->title('Verifikasi kelayakan PBI berhasil dicatat')
                                ->success()
                                ->send();
                        }),

                    // Workflow 7: PBI - Terbitkan Rekomendasi (eligibility_verification/awaiting_approval -> recommendation_issued)
                    Action::make('issuePbiRecommendation')
                        ->label('Terbitkan Surat Rekomendasi PBI')
                        ->icon(Heroicon::OutlinedDocumentCheck)
                        ->color('success')
                        ->visible(function (ServiceRequest $record): bool {
                            $handler = $record->serviceType?->handler?->value ?? $record->serviceType?->handler;

                            return $handler === 'pbi' && in_array($record->status, [ServiceRequestStatus::EligibilityVerification, ServiceRequestStatus::AwaitingApproval]);
                        })
                        ->requiresConfirmation()
                        ->action(function (ServiceRequest $record): void {
                            $pbi = $record->pbiReactivation;
                            if (! $pbi) {
                                return;
                            }

                            $rekNumber = 'REK-PBI/'.now('Asia/Jakarta')->format('Ymd').'/'.Str::padLeft((string) random_int(1, 9999), 4, '0');
                            $pbi->recommendation_number = $rekNumber;
                            $pbi->recommendation_issued_at = now();
                            $pbi->signer_id = Auth::id();
                            $pbi->save();

                            $record->recordStatusChange(
                                ServiceRequestStatus::RecommendationIssued,
                                "Surat Rekomendasi Reaktivasi PBI-JK diterbitkan dengan No: {$rekNumber}",
                                Auth::id()
                            );

                            Notification::make()
                                ->title("Rekomendasi PBI terbit: {$rekNumber}")
                                ->success()
                                ->send();
                        }),

                    // Workflow 8: PBI - Input Usulan ke SIKS-NG Kemensos (recommendation_issued -> proposed_to_ministry)
                    Action::make('proposeToMinistry')
                        ->label('Input Usulan ke Kemensos')
                        ->icon(Heroicon::OutlinedCloudArrowUp)
                        ->color('info')
                        ->visible(function (ServiceRequest $record): bool {
                            $handler = $record->serviceType?->handler?->value ?? $record->serviceType?->handler;

                            return $handler === 'pbi' && $record->status === ServiceRequestStatus::RecommendationIssued;
                        })
                        ->form([
                            DateTimePicker::make('proposed_to_ministry_at')
                                ->label('Tanggal & Waktu Input di SIKS-NG')
                                ->default(now())
                                ->required(),
                            Textarea::make('notes')
                                ->label('Catatan Pengusulan')
                                ->default('Usulan reaktivasi telah diinput ke aplikasi SIKS-NG Kemensos RI.'),
                        ])
                        ->action(function (ServiceRequest $record, array $data): void {
                            $pbi = $record->pbiReactivation;
                            if ($pbi) {
                                $pbi->proposed_to_ministry_at = $data['proposed_to_ministry_at'];
                                $pbi->save();
                            }

                            $record->recordStatusChange(
                                ServiceRequestStatus::ProposedToMinistry,
                                'Usulan telah diinput ke SIKS-NG Kemensos RI.',
                                Auth::id()
                            );

                            Notification::make()
                                ->title('Pengusulan ke Kemensos telah dicatat')
                                ->success()
                                ->send();
                        }),

                    // Workflow 9: PBI - Catat Keputusan Kemensos (proposed_to_ministry -> ministry_approved/ministry_rejected)
                    Action::make('recordMinistryDecision')
                        ->label('Catat Keputusan Kemensos')
                        ->icon(Heroicon::OutlinedBuildingOffice)
                        ->color('warning')
                        ->visible(function (ServiceRequest $record): bool {
                            $handler = $record->serviceType?->handler?->value ?? $record->serviceType?->handler;

                            return $handler === 'pbi' && $record->status === ServiceRequestStatus::ProposedToMinistry;
                        })
                        ->form([
                            Select::make('decision')
                                ->label('Keputusan Kementerian Sosial')
                                ->options([
                                    'approved' => 'Disetujui oleh Kemensos',
                                    'rejected' => 'Ditolak oleh Kemensos',
                                ])
                                ->required(),
                            Textarea::make('notes')
                                ->label('Keterangan / Catatan Keputusan')
                                ->required(),
                        ])
                        ->action(function (ServiceRequest $record, array $data): void {
                            $isApproved = $data['decision'] === 'approved';
                            $pbi = $record->pbiReactivation;
                            if ($pbi) {
                                $pbi->ministry_decision = $isApproved ? MinistryDecision::Approved : MinistryDecision::Rejected;
                                $pbi->ministry_decided_at = now();
                                $pbi->save();
                            }

                            $newStatus = $isApproved ? ServiceRequestStatus::MinistryApproved : ServiceRequestStatus::MinistryRejected;
                            $record->recordStatusChange(
                                $newStatus,
                                'Keputusan Kemensos: '.($isApproved ? 'DISETUJUI' : 'DITOLAK').'. '.$data['notes'],
                                Auth::id()
                            );

                            Notification::make()
                                ->title('Keputusan Kemensos berhasil disimpan')
                                ->success()
                                ->send();
                        }),

                    // Workflow 10: PBI - Konfirmasi Kepesertaan Aktif Kembali (ministry_approved -> reactivated)
                    Action::make('confirmReactivated')
                        ->label('Konfirmasi BPJS Aktif')
                        ->icon(Heroicon::OutlinedCheckBadge)
                        ->color('success')
                        ->visible(function (ServiceRequest $record): bool {
                            $handler = $record->serviceType?->handler?->value ?? $record->serviceType?->handler;

                            return $handler === 'pbi' && $record->status === ServiceRequestStatus::MinistryApproved;
                        })
                        ->form([
                            DatePicker::make('reactivated_date')
                                ->label('Tanggal Efektif Aktif Kembali di BPJS')
                                ->default(now())
                                ->required(),
                        ])
                        ->action(function (ServiceRequest $record, array $data): void {
                            $pbi = $record->pbiReactivation;
                            if ($pbi) {
                                $pbi->reactivated_date = $data['reactivated_date'];
                                $pbi->save();
                            }

                            $record->recordStatusChange(
                                ServiceRequestStatus::Reactivated,
                                "Kepesertaan JKN-KIS telah aktif kembali per tanggal {$data['reactivated_date']}.",
                                Auth::id()
                            );

                            Notification::make()
                                ->title('Status kepesertaan aktif telah dikonfirmasi')
                                ->success()
                                ->send();
                        }),

                    // Workflow 11: Selesaikan Layanan (issued/reactivated/in_process -> completed)
                    Action::make('completeService')
                        ->label('Tandai Selesai')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->color('success')
                        ->visible(fn (ServiceRequest $record) => in_array($record->status, [ServiceRequestStatus::Issued, ServiceRequestStatus::Reactivated, ServiceRequestStatus::InProcess]))
                        ->form([
                            Textarea::make('service_result')
                                ->label('Hasil Pelayanan (Wajib diisi sebelum tiket ditutup)')
                                ->required()
                                ->default('Layanan telah selesai diproses dan diserahkan kepada pemohon.'),
                        ])
                        ->action(function (ServiceRequest $record, array $data): void {
                            $record->service_result = $data['service_result'];
                            $record->completed_at = now();
                            $record->recordStatusChange(
                                ServiceRequestStatus::Completed,
                                'Layanan selesai: '.$data['service_result'],
                                Auth::id()
                            );

                            Notification::make()
                                ->title('Tiket pengajuan berhasil diselesaikan!')
                                ->success()
                                ->send();
                        }),

                    // Workflow 12: Tolak Pengajuan
                    Action::make('rejectRequest')
                        ->label('Tolak Pengajuan')
                        ->icon(Heroicon::OutlinedXCircle)
                        ->color('danger')
                        ->visible(fn (ServiceRequest $record) => ! in_array($record->status, [ServiceRequestStatus::Completed, ServiceRequestStatus::Rejected]))
                        ->form([
                            Textarea::make('rejection_reason')
                                ->label('Alasan Penolakan (Wajib dicatat)')
                                ->required(),
                        ])
                        ->action(function (ServiceRequest $record, array $data): void {
                            $record->rejection_reason = $data['rejection_reason'];
                            $record->completed_at = now();
                            $record->recordStatusChange(
                                ServiceRequestStatus::Rejected,
                                'Pengajuan ditolak. Alasan: '.$data['rejection_reason'],
                                Auth::id()
                            );

                            Notification::make()
                                ->title('Pengajuan telah ditolak')
                                ->danger()
                                ->send();
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
