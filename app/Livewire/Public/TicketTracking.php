<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Models\Complaint;
use App\Models\ServiceRequest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Lacak Status Tiket & Permohonan — SAPA SOSIAL Kab. Blitar')]
class TicketTracking extends Component
{
    #[Url(as: 'tiket')]
    public string $ticket_number = '';

    public string $security_digits = '';

    public ?array $result = null;

    public string $errorMessage = '';

    public bool $hasSearched = false;

    public function mount(): void
    {
        if (! empty($this->ticket_number)) {
            // Check if ticket exists to pre-populate or prompt for security digits
            $this->performSearch(bypassSecurity: false);
        }
    }

    public function trackTicket(): void
    {
        $this->performSearch(bypassSecurity: false);
    }

    protected function performSearch(bool $bypassSecurity = false): void
    {
        $this->errorMessage = '';
        $this->result = null;
        $this->hasSearched = true;

        $cleanTicket = trim($this->ticket_number);

        if (empty($cleanTicket)) {
            $this->errorMessage = 'Nomor tiket pengajuan wajib diisi.';

            return;
        }

        // Search ServiceRequest
        $serviceRequest = ServiceRequest::with([
            'serviceType',
            'village.district',
            'dtsenCertificate.dtsenPurpose',
            'pbiReactivation',
            'statusHistories.user',
            'documents',
        ])->where('request_number', 'ILIKE', $cleanTicket)->first();

        if ($serviceRequest) {
            // Verify security digits if provided or required
            if (! empty($this->security_digits)) {
                $lastNik = substr((string) $serviceRequest->applicant_nik, -4);
                $lastPhone = substr((string) $serviceRequest->phone, -4);
                if ($this->security_digits !== $lastNik && $this->security_digits !== $lastPhone) {
                    $this->errorMessage = '4 digit terakhir NIK atau No. HP tidak sesuai dengan data pemohon tiket ini.';

                    return;
                }
            }

            $this->result = [
                'type' => 'service_request',
                'id' => $serviceRequest->id,
                'number' => $serviceRequest->request_number,
                'service_name' => $serviceRequest->serviceType?->name ?? 'Layanan Sosial',
                'service_code' => $serviceRequest->serviceType?->code,
                'category' => $serviceRequest->serviceType?->category ?? 'Layanan Terpadu',
                'applicant_name' => $serviceRequest->applicant_name,
                'nik_masked' => substr((string) $serviceRequest->applicant_nik, 0, 6).'********'.substr((string) $serviceRequest->applicant_nik, -2),
                'phone_masked' => substr((string) $serviceRequest->phone, 0, 4).'****'.substr((string) $serviceRequest->phone, -3),
                'address' => $serviceRequest->address.', '.($serviceRequest->village?->name ?? '').', '.($serviceRequest->village?->district?->name ?? ''),
                'submitted_at' => $serviceRequest->submitted_at?->translatedFormat('d F Y, H:i') ?? '-',
                'status' => $serviceRequest->status,
                'status_label' => $serviceRequest->status->label(),
                'status_color' => $serviceRequest->status->color(),
                'is_priority' => (bool) $serviceRequest->is_priority,
                'officer_notes' => $serviceRequest->officer_notes,
                'rejection_reason' => $serviceRequest->rejection_reason,
                'service_result' => $serviceRequest->service_result,
                'histories' => $serviceRequest->statusHistories,
                'certificate' => $serviceRequest->dtsenCertificate,
                'pbi' => $serviceRequest->pbiReactivation,
                'documents' => $serviceRequest->documents,
            ];

            return;
        }

        // Search Complaint
        $complaint = Complaint::with([
            'category',
            'village.district',
            'statusHistories.user',
            'attachments',
        ])->where('complaint_number', 'ILIKE', $cleanTicket)->first();

        if ($complaint) {
            if (! empty($this->security_digits)) {
                $lastPhone = substr((string) $complaint->reporter_phone, -4);
                if ($this->security_digits !== $lastPhone) {
                    $this->errorMessage = '4 digit terakhir nomor HP pelapor tidak sesuai dengan data laporan ini.';

                    return;
                }
            }

            $this->result = [
                'type' => 'complaint',
                'id' => $complaint->id,
                'number' => $complaint->complaint_number,
                'service_name' => 'Laporan Pengaduan: '.($complaint->category?->name ?? 'Permasalahan Sosial'),
                'service_code' => 'ADU',
                'category' => 'Pengaduan Warga',
                'applicant_name' => $complaint->reporter_name,
                'nik_masked' => '-',
                'phone_masked' => substr((string) $complaint->reporter_phone, 0, 4).'****'.substr((string) $complaint->reporter_phone, -3),
                'address' => ($complaint->location_detail ? $complaint->location_detail.', ' : '').($complaint->village?->name ?? '').', '.($complaint->village?->district?->name ?? ''),
                'submitted_at' => $complaint->reported_at?->translatedFormat('d F Y, H:i') ?? '-',
                'status' => $complaint->status,
                'status_label' => $complaint->status->label(),
                'status_color' => $complaint->status->color(),
                'is_priority' => false,
                'officer_notes' => $complaint->verification_result,
                'rejection_reason' => null,
                'service_result' => $complaint->action_taken,
                'histories' => $complaint->statusHistories,
                'certificate' => null,
                'pbi' => null,
                'documents' => collect(),
            ];

            return;
        }

        $this->errorMessage = 'Tiket "'.htmlspecialchars($cleanTicket).'" tidak ditemukan. Mohon periksa kembali nomor tiket Anda.';
    }

    public function render()
    {
        return view('livewire.public.ticket-tracking');
    }
}
