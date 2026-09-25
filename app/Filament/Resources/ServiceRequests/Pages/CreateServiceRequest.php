<?php

namespace App\Filament\Resources\ServiceRequests\Pages;

use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use App\Models\ServiceRequest;
use App\Models\ServiceType;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceRequest extends CreateRecord
{
    protected static string $resource = ServiceRequestResource::class;

    protected function beforeCreate(): void
    {
        $serviceTypeId = $this->data['service_type_id'] ?? null;
        $applicantNik = $this->data['applicant_nik'] ?? null;

        if ($serviceTypeId && $applicantNik) {
            $st = ServiceType::find($serviceTypeId);
            if (($st?->handler?->value ?? $st?->handler) === 'dtsen') {
                $existing = ServiceRequest::where('applicant_nik', $applicantNik)
                    ->whereHas('serviceType', fn ($q) => $q->where('handler', 'dtsen'))
                    ->whereIn('status', ['submitted', 'document_check', 'data_verification', 'awaiting_approval', 'issued'])
                    ->first();

                if ($existing) {
                    Notification::make()
                        ->title('Peringatan: Potensi Duplikasi')
                        ->body("NIK {$applicantNik} masih memiliki pengajuan aktif ({$existing->request_number}) dengan status: {$existing->status->label()}.")
                        ->warning()
                        ->persistent()
                        ->send();
                }
            }
        }
    }
}
