<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Models\DtsenCertificate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Verifikasi Keaslian Surat DTSEN — SAPA SOSIAL Kab. Blitar')]
class CertificateVerification extends Component
{
    public string $code = '';

    public ?array $certificateData = null;

    public string $statusType = ''; // 'valid', 'expired', 'not_found'

    public string $errorMessage = '';

    public bool $hasChecked = false;

    public function mount(?string $code = null): void
    {
        if (! empty($code)) {
            $this->code = $code;
            $this->verify();
        }
    }

    public function verify(): void
    {
        $this->errorMessage = '';
        $this->certificateData = null;
        $this->statusType = '';
        $this->hasChecked = true;

        $cleanCode = trim($this->code);

        if (empty($cleanCode)) {
            $this->errorMessage = 'Silakan masukkan kode verifikasi surat yang tertera pada lembar barcode.';

            return;
        }

        // Query certificate
        $cert = DtsenCertificate::with([
            'serviceRequest.village.district',
            'dtsenPurpose',
            'signer',
        ])->where('verification_code', 'ILIKE', $cleanCode)
            ->orWhere('certificate_number', 'ILIKE', $cleanCode)
            ->first();

        if (! $cert || ! $cert->certificate_number) {
            $this->statusType = 'not_found';

            return;
        }

        // Check if expired
        $isExpired = false;
        if ($cert->valid_until && $cert->valid_until->isPast()) {
            $isExpired = true;
        }

        $this->statusType = $isExpired ? 'expired' : 'valid';

        // Prepare masked data
        $subjectNik = (string) $cert->subject_nik;
        $maskedNik = strlen($subjectNik) >= 16
            ? substr($subjectNik, 0, 6).'********'.substr($subjectNik, -2)
            : '****************';

        $familyCard = (string) ($cert->serviceRequest?->family_card_number ?? '');
        $maskedKk = strlen($familyCard) >= 16
            ? substr($familyCard, 0, 6).'********'.substr($familyCard, -2)
            : '****************';

        $this->certificateData = [
            'certificate_number' => $cert->certificate_number,
            'verification_code' => $cert->verification_code,
            'subject_name' => $cert->subject_name,
            'masked_nik' => $maskedNik,
            'masked_kk' => $maskedKk,
            'decile' => $cert->decile ?? 1,
            'purpose_name' => $cert->dtsenPurpose?->name ?? 'Keperluan Administrasi',
            'purpose_description' => $cert->purpose_description,
            'issued_at' => $cert->issued_at?->translatedFormat('d F Y') ?? '-',
            'valid_until' => $cert->valid_until?->translatedFormat('d F Y') ?? 'Tidak berbatas waktu',
            'signer_name' => $cert->signer?->name ?? 'Kepala Dinas Sosial Kab. Blitar',
            'signer_nip' => '19700115 199503 2 001',
            'district_name' => $cert->serviceRequest?->village?->district?->name ?? 'Kanigoro',
            'village_name' => $cert->serviceRequest?->village?->name ?? 'Satreyan',
            'is_expired' => $isExpired,
        ];
    }

    public function render()
    {
        return view('livewire.public.certificate-verification');
    }
}
