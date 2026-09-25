<?php

namespace App\Filament\Resources\Complaints\Tables;

use App\Enums\ComplaintStatus;
use App\Enums\HandlingType;
use App\Enums\RehabilitationCaseStatus;
use App\Models\Client;
use App\Models\ClientCategory;
use App\Models\Complaint;
use App\Models\Disposition;
use App\Models\RehabilitationCase;
use App\Models\WorkUnit;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ComplaintsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('complaint_number')
                    ->label('No. Laporan')
                    ->searchable()
                    ->copyable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('complaintCategory.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('warning')
                    ->searchable(),
                TextColumn::make('reporter_name')
                    ->label('Nama Pelapor')
                    ->searchable(),
                TextColumn::make('reporter_phone')
                    ->label('WhatsApp')
                    ->searchable(),
                TextColumn::make('village.district.name')
                    ->label('Kecamatan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('village.name')
                    ->label('Desa')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => $state instanceof ComplaintStatus ? $state->color() : ($state ? ComplaintStatus::tryFrom($state)?->color() ?? 'gray' : 'gray'))
                    ->formatStateUsing(fn ($state) => $state instanceof ComplaintStatus ? $state->label() : ($state ? ComplaintStatus::tryFrom($state)?->label() ?? $state : '-')),
                TextColumn::make('officer.name')
                    ->label('Petugas')
                    ->placeholder('Belum ditugaskan')
                    ->searchable(),
                TextColumn::make('reported_at')
                    ->label('Waktu Lapor')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('reported_at', 'desc')
            ->filters([
                SelectFilter::make('complaint_category_id')
                    ->label('Kategori Masalah')
                    ->relationship('complaintCategory', 'name')
                    ->preload(),
                SelectFilter::make('status')
                    ->label('Status Laporan')
                    ->options(collect(ComplaintStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
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

                    // Workflow 1: Verifikasi Awal (received -> verification)
                    Action::make('verifyComplaint')
                        ->label('Verifikasi Awal')
                        ->icon(Heroicon::OutlinedMagnifyingGlass)
                        ->color('info')
                        ->visible(fn (Complaint $record) => $record->status === ComplaintStatus::Received)
                        ->form([
                            Textarea::make('verification_result')
                                ->label('Hasil Verifikasi Awal Pengaduan')
                                ->required()
                                ->default('Laporan telah diperiksa dan dinyatakan valid untuk ditindaklanjuti.'),
                        ])
                        ->action(function (Complaint $record, array $data): void {
                            $record->verification_result = $data['verification_result'];
                            $record->officer_id = $record->officer_id ?? Auth::id();
                            $record->recordStatusChange(
                                ComplaintStatus::Verification,
                                'Verifikasi awal: '.$data['verification_result'],
                                Auth::id()
                            );
                            Notification::make()
                                ->title('Status pengaduan: Terverifikasi')
                                ->success()
                                ->send();
                        }),

                    // Workflow 2: Minta Klarifikasi (verification -> clarification_requested)
                    Action::make('requestClarification')
                        ->label('Minta Klarifikasi Pelapor')
                        ->icon(Heroicon::OutlinedQuestionMarkCircle)
                        ->color('warning')
                        ->visible(fn (Complaint $record) => in_array($record->status, [ComplaintStatus::Received, ComplaintStatus::Verification]))
                        ->form([
                            Textarea::make('notes')
                                ->label('Keterangan / Informasi yang Perlu Diklarifikasi oleh Pelapor')
                                ->required(),
                        ])
                        ->action(function (Complaint $record, array $data): void {
                            $record->recordStatusChange(
                                ComplaintStatus::ClarificationRequested,
                                'Klarifikasi diminta: '.$data['notes'],
                                Auth::id()
                            );
                            Notification::make()
                                ->title('Permintaan klarifikasi dicatat')
                                ->warning()
                                ->send();
                        }),

                    // Workflow 3: Disposisi Penanganan (verification -> dispatched)
                    Action::make('dispatchComplaint')
                        ->label('Disposisikan Laporan')
                        ->icon(Heroicon::OutlinedArrowRightCircle)
                        ->color('primary')
                        ->visible(fn (Complaint $record) => in_array($record->status, [ComplaintStatus::Received, ComplaintStatus::Verification, ComplaintStatus::ClarificationRequested]))
                        ->form([
                            Select::make('to_work_unit_id')
                                ->label('Unit Kerja Tujuan Disposisi')
                                ->options(WorkUnit::where('is_active', true)->pluck('name', 'id'))
                                ->required()
                                ->searchable(),
                            Select::make('to_user_id')
                                ->label('Petugas Khusus yang Ditugaskan (Opsional)')
                                ->relationship('officer', 'name')
                                ->searchable(),
                            Textarea::make('instructions')
                                ->label('Instruksi Penanganan')
                                ->required(),
                        ])
                        ->action(function (Complaint $record, array $data): void {
                            Disposition::create([
                                'dispositionable_type' => Complaint::class,
                                'dispositionable_id' => $record->id,
                                'from_user_id' => Auth::id(),
                                'to_work_unit_id' => $data['to_work_unit_id'],
                                'to_user_id' => $data['to_user_id'] ?? null,
                                'instructions' => $data['instructions'],
                                'disposed_at' => now(),
                            ]);

                            if (! empty($data['to_user_id'])) {
                                $record->officer_id = $data['to_user_id'];
                            }

                            $record->recordStatusChange(
                                ComplaintStatus::Dispatched,
                                'Laporan didisposisikan: '.$data['instructions'],
                                Auth::id()
                            );

                            Notification::make()
                                ->title('Laporan pengaduan berhasil didisposisikan')
                                ->success()
                                ->send();
                        }),

                    // Workflow 4: Mulai Penanganan (dispatched -> in_handling)
                    Action::make('startHandling')
                        ->label('Mulai Penanganan')
                        ->icon(Heroicon::OutlinedPlay)
                        ->color('info')
                        ->visible(fn (Complaint $record) => $record->status === ComplaintStatus::Dispatched)
                        ->requiresConfirmation()
                        ->action(function (Complaint $record): void {
                            $record->recordStatusChange(
                                ComplaintStatus::InHandling,
                                'Petugas mulai melakukan penanganan permasalahan di lapangan.',
                                Auth::id()
                            );
                            Notification::make()
                                ->title('Laporan dalam penanganan')
                                ->success()
                                ->send();
                        }),

                    // Workflow 5: Selesaikan Pengaduan (in_handling -> resolved)
                    Action::make('resolveComplaint')
                        ->label('Selesaikan Pengaduan')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->color('success')
                        ->visible(fn (Complaint $record) => in_array($record->status, [ComplaintStatus::Dispatched, ComplaintStatus::InHandling]))
                        ->form([
                            Textarea::make('action_taken')
                                ->label('Tindakan & Solusi Penanganan yang Telah Dijalankan')
                                ->required(),
                        ])
                        ->action(function (Complaint $record, array $data): void {
                            $record->action_taken = $data['action_taken'];
                            $record->resolved_at = now();
                            $record->recordStatusChange(
                                ComplaintStatus::Resolved,
                                'Pengaduan selesai: '.$data['action_taken'],
                                Auth::id()
                            );

                            Notification::make()
                                ->title('Laporan pengaduan berhasil diselesaikan!')
                                ->success()
                                ->send();
                        }),

                    // Workflow 6: Tandai Duplikat
                    Action::make('markDuplicate')
                        ->label('Tandai Duplikat')
                        ->icon(Heroicon::OutlinedDocumentDuplicate)
                        ->color('gray')
                        ->visible(fn (Complaint $record) => ! in_array($record->status, [ComplaintStatus::Resolved, ComplaintStatus::Duplicate]))
                        ->form([
                            Select::make('duplicate_of_id')
                                ->label('Pilih Laporan Pengaduan Induk')
                                ->options(fn (?Complaint $record) => Complaint::when($record, fn ($q) => $q->where('id', '!=', $record->id))->pluck('complaint_number', 'id'))
                                ->searchable()
                                ->required(),
                            Textarea::make('notes')
                                ->label('Catatan Keterangan Duplikasi')
                                ->default('Laporan ini serupa dengan laporan yang telah masuk sebelumnya.'),
                        ])
                        ->action(function (Complaint $record, array $data): void {
                            $record->duplicate_of_id = $data['duplicate_of_id'];
                            $record->recordStatusChange(
                                ComplaintStatus::Duplicate,
                                'Laporan ditandai sebagai duplikat. '.$data['notes'],
                                Auth::id()
                            );

                            Notification::make()
                                ->title('Laporan ditandai duplikat')
                                ->success()
                                ->send();
                        }),

                    // Workflow 7: Buat Kasus Rehabilitasi Sosial Terkait
                    Action::make('createRehabCase')
                        ->label('Buat Kasus Rehabilitasi')
                        ->icon(Heroicon::OutlinedHeart)
                        ->color('warning')
                        ->visible(fn (Complaint $record) => ! $record->rehabilitationCase()->exists())
                        ->form([
                            TextInput::make('client_name')
                                ->label('Nama Klien / Korban')
                                ->default(fn (?Complaint $record) => $record?->reporter_name)
                                ->required(),
                            Select::make('client_category_id')
                                ->label('Kategori Klien')
                                ->options(ClientCategory::pluck('name', 'id'))
                                ->required(),
                            Select::make('handling_type')
                                ->label('Bentuk Penanganan Awal')
                                ->options(HandlingType::class)
                                ->default(HandlingType::Direct)
                                ->required(),
                            Textarea::make('notes')
                                ->label('Catatan Penanganan Awal')
                                ->default(fn (?Complaint $record) => $record ? "Diteruskan dari laporan pengaduan {$record->complaint_number}: {$record->description}" : null),
                        ])
                        ->action(function (Complaint $record, array $data): void {
                            $client = Client::create([
                                'name' => $data['client_name'],
                                'client_category_id' => $data['client_category_id'],
                                'address' => $record->location_detail,
                                'village_id' => $record->village_id,
                                'phone' => $record->reporter_phone,
                                'gender' => 'L',
                            ]);

                            $rehabCase = RehabilitationCase::create([
                                'client_id' => $client->id,
                                'complaint_id' => $record->id,
                                'officer_id' => Auth::id(),
                                'handling_type' => $data['handling_type'],
                                'status' => RehabilitationCaseStatus::Received,
                                'received_at' => now(),
                            ]);

                            $record->recordStatusChange(
                                ComplaintStatus::InHandling,
                                "Diteruskan menjadi Kasus Rehabilitasi Sosial {$rehabCase->case_number}.",
                                Auth::id()
                            );

                            Notification::make()
                                ->title("Kasus Rehabilitasi {$rehabCase->case_number} berhasil dibuat!")
                                ->success()
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
