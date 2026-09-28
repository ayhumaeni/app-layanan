<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Enums\PublishStatus;
use App\Models\Complaint;
use App\Models\Faq;
use App\Models\InformationPage;
use App\Models\ServiceRequest;
use App\Models\ServiceType;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('SAPA SOSIAL — Satu Pintu Layanan Sosial Kabupaten Blitar')]
class Home extends Component
{
    public string $quickTicketNumber = '';

    public ?array $quickTrackResult = null;

    public string $quickTrackError = '';

    public string $searchKeyword = '';

    public function trackQuickTicket(): void
    {
        $this->quickTrackError = '';
        $this->quickTrackResult = null;

        $cleanTicket = trim($this->quickTicketNumber);

        if (empty($cleanTicket)) {
            $this->quickTrackError = 'Silakan masukkan nomor tiket pengajuan Anda.';

            return;
        }

        // Search in ServiceRequest
        $serviceRequest = ServiceRequest::with(['serviceType', 'village.district'])
            ->where('request_number', 'ILIKE', $cleanTicket)
            ->first();

        if ($serviceRequest) {
            $this->quickTrackResult = [
                'type' => 'service_request',
                'number' => $serviceRequest->request_number,
                'service' => $serviceRequest->serviceType?->name ?? 'Layanan Sosial',
                'status_label' => $serviceRequest->status->label(),
                'status_color' => $serviceRequest->status->color(),
                'submitted_at' => $serviceRequest->submitted_at?->translatedFormat('d F Y, H:i') ?? '-',
                'applicant' => $serviceRequest->applicant_name,
                'notes' => $serviceRequest->officer_notes ?? 'Permohonan Anda sedang dalam antrean verifikasi petugas.',
            ];

            return;
        }

        // Search in Complaint
        $complaint = Complaint::with(['category', 'village.district'])
            ->where('complaint_number', 'ILIKE', $cleanTicket)
            ->first();

        if ($complaint) {
            $this->quickTrackResult = [
                'type' => 'complaint',
                'number' => $complaint->complaint_number,
                'service' => 'Pengaduan: '.($complaint->category?->name ?? 'Pengaduan Warga'),
                'status_label' => $complaint->status->label(),
                'status_color' => $complaint->status->color(),
                'submitted_at' => $complaint->reported_at?->translatedFormat('d F Y, H:i') ?? '-',
                'applicant' => $complaint->reporter_name,
                'notes' => $complaint->action_taken ?? 'Laporan pengaduan telah diterima dan dalam proses tindak lanjut.',
            ];

            return;
        }

        $this->quickTrackError = 'Nomor tiket "'.htmlspecialchars($cleanTicket).'" tidak ditemukan. Pastikan format nomor sudah benar atau periksa tanda terima Anda.';
    }

    public function searchServices(): void
    {
        if (! empty(trim($this->searchKeyword))) {
            $this->redirect(route('services.index').'?q='.urlencode(trim($this->searchKeyword)));
        }
    }

    public function render()
    {
        $priorityServices = ServiceType::where('is_active', true)
            ->whereIn('code', ['DTSEN', 'PBI', 'REHSOS'])
            ->orderBy('id')
            ->get();

        $faqs = Faq::where('is_active', true)
            ->orderBy('sort_order')
            ->take(5)
            ->get();

        $recentInfo = InformationPage::where('publish_status', PublishStatus::Published)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('livewire.public.home', [
            'priorityServices' => $priorityServices,
            'faqs' => $faqs,
            'recentInfo' => $recentInfo,
        ]);
    }
}
