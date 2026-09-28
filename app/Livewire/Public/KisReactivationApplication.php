<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Enums\DocumentVerificationStatus;
use App\Enums\PbiReactivationReason;
use App\Enums\ServiceRequestStatus;
use App\Models\District;
use App\Models\PbiReactivation;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestDocument;
use App\Models\ServiceType;
use App\Models\StatusHistory;
use App\Models\Village;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Formulir Reaktivasi KIS / PBI-JK — SAPA SOSIAL Kab. Blitar')]
class KisReactivationApplication extends Component
{
    use WithFileUploads;

    public int $currentStep = 1;

    // Step 1: Data Peserta & Pemohon
    public string $applicant_name = '';

    public string $applicant_nik = '';

    public string $family_card_number = '';

    public string $phone = '';

    public ?int $district_id = null;

    public ?int $village_id = null;

    public string $address = '';

    public string $bpjs_card_number = '';

    public ?string $deactivated_date = null;

    // Step 2: Alasan Reaktivasi & Faskes
    public string $reason = 'emergency';

    public string $health_facility_name = '';

    public string $health_letter_number = '';

    // Step 3: Berkas Persyaratan
    public $ktp_file;

    public $kk_file;

    public $kis_file;

    public $faskes_file;

    // Step 4: Pernyataan
    public bool $agreement_confirmed = false;

    // Success State
    public bool $isSuccess = false;

    public ?string $submittedTicket = null;

    public ?string $submittedAt = null;

    public function mount(): void
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->applicant_name = (string) $user->name;
            $this->applicant_nik = (string) ($user->nik ?? '');
            $this->phone = (string) ($user->phone ?? '');
            $this->district_id = $user->district_id;
            $this->village_id = $user->village_id;
        }
    }

    public function updatedDistrictId(): void
    {
        $this->village_id = null;
    }

    public function isMedicalReason(): bool
    {
        return in_array($this->reason, ['emergency', 'chronic', 'catastrophic'], true);
    }

    public function isEmergency(): bool
    {
        return $this->reason === 'emergency';
    }

    public function nextStep(): void
    {
        $this->validateStep();
        if ($this->currentStep < 4) {
            $this->currentStep++;
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep(int $step): void
    {
        if ($step < $this->currentStep) {
            $this->currentStep = $step;
        }
    }

    protected function validateStep(): void
    {
        if ($this->currentStep === 1) {
            $this->validate([
                'applicant_name' => 'required|string|min:3|max:100',
                'applicant_nik' => 'required|digits:16',
                'family_card_number' => 'required|digits:16',
                'phone' => 'required|string|min:9|max:16',
                'district_id' => 'required|exists:districts,id',
                'village_id' => 'required|exists:villages,id',
                'address' => 'required|string|min:5|max:255',
                'bpjs_card_number' => 'required|string|min:10|max:16',
            ], [
                'applicant_name.required' => 'Nama lengkap peserta BPJS wajib diisi.',
                'applicant_nik.required' => 'NIK peserta wajib diisi.',
                'applicant_nik.digits' => 'NIK harus berjumlah 16 digit.',
                'family_card_number.required' => 'Nomor KK wajib diisi.',
                'family_card_number.digits' => 'Nomor KK harus berjumlah 16 digit.',
                'phone.required' => 'Nomor WhatsApp / telepon wajib diisi.',
                'district_id.required' => 'Pilih kecamatan domisili.',
                'village_id.required' => 'Pilih desa/kelurahan domisili.',
                'address.required' => 'Alamat lengkap wajib diisi.',
                'bpjs_card_number.required' => 'Nomor kartu BPJS / KIS wajib diisi.',
            ]);
        } elseif ($this->currentStep === 2) {
            $rules = [
                'reason' => 'required|in:emergency,chronic,catastrophic,newborn,other',
            ];
            if ($this->isMedicalReason()) {
                $rules['health_facility_name'] = 'required|string|min:3|max:100';
                $rules['health_letter_number'] = 'nullable|string|max:100';
            }
            $this->validate($rules, [
                'reason.required' => 'Pilih alasan permohonan reaktivasi.',
                'health_facility_name.required' => 'Nama Rumah Sakit / Puskesmas wajib diisi untuk alasan medis.',
            ]);
        } elseif ($this->currentStep === 3) {
            $rules = [
                'ktp_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:3072',
                'kk_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:3072',
                'kis_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:3072',
            ];
            if ($this->isMedicalReason()) {
                $rules['faskes_file'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:3072';
            }
            $this->validate($rules, [
                'ktp_file.required' => 'Foto KTP peserta wajib diunggah.',
                'kk_file.required' => 'Foto Kartu Keluarga wajib diunggah.',
                'kis_file.required' => 'Foto Kartu BPJS / KIS wajib diunggah.',
                'faskes_file.required' => 'Surat Keterangan Rawat / Faskes wajib dilampirkan untuk alasan medis.',
            ]);
        }
    }

    public function submit(): void
    {
        $this->validate([
            'agreement_confirmed' => 'accepted',
        ], [
            'agreement_confirmed.accepted' => 'Anda harus mencentang pernyataan kebenaran data sebelum mengirim.',
        ]);

        $serviceType = ServiceType::where('code', 'PBI')->firstOrFail();
        $isEmergency = $this->isEmergency();

        DB::transaction(function () use ($serviceType, $isEmergency) {
            // 1. ServiceRequest
            $request = new ServiceRequest;
            $request->service_type_id = $serviceType->id;
            $request->submitter_id = Auth::id();
            $request->applicant_name = $this->applicant_name;
            $request->applicant_nik = $this->applicant_nik;
            $request->family_card_number = $this->family_card_number;
            $request->address = $this->address;
            $request->village_id = $this->village_id;
            $request->phone = $this->phone;
            $request->is_priority = $isEmergency;
            $request->status = ServiceRequestStatus::Submitted;
            $request->submitted_at = now();
            $request->save();

            // 2. PbiReactivation record
            $pbi = new PbiReactivation;
            $pbi->service_request_id = $request->id;
            $pbi->participant_name = $this->applicant_name;
            $pbi->participant_nik = $this->applicant_nik;
            $pbi->bpjs_card_number = $this->bpjs_card_number;
            $pbi->deactivated_date = $this->deactivated_date ?: null;
            $pbi->reason = PbiReactivationReason::from($this->reason);
            $pbi->health_facility_name = $this->health_facility_name ?: null;
            $pbi->health_letter_number = $this->health_letter_number ?: null;
            $pbi->save();

            // 3. Save documents
            $ktpPath = $this->ktp_file->store('service_requests/'.$request->id.'/documents', 'local');
            ServiceRequestDocument::create([
                'service_request_id' => $request->id,
                'file_path' => $ktpPath,
                'original_name' => $this->ktp_file->getClientOriginalName(),
                'verification_status' => DocumentVerificationStatus::Pending,
                'notes' => 'KTP Peserta',
            ]);

            $kkPath = $this->kk_file->store('service_requests/'.$request->id.'/documents', 'local');
            ServiceRequestDocument::create([
                'service_request_id' => $request->id,
                'file_path' => $kkPath,
                'original_name' => $this->kk_file->getClientOriginalName(),
                'verification_status' => DocumentVerificationStatus::Pending,
                'notes' => 'Kartu Keluarga',
            ]);

            $kisPath = $this->kis_file->store('service_requests/'.$request->id.'/documents', 'local');
            ServiceRequestDocument::create([
                'service_request_id' => $request->id,
                'file_path' => $kisPath,
                'original_name' => $this->kis_file->getClientOriginalName(),
                'verification_status' => DocumentVerificationStatus::Pending,
                'notes' => 'Kartu BPJS/KIS Nonaktif',
            ]);

            if ($this->faskes_file) {
                $faskesPath = $this->faskes_file->store('service_requests/'.$request->id.'/documents', 'local');
                ServiceRequestDocument::create([
                    'service_request_id' => $request->id,
                    'file_path' => $faskesPath,
                    'original_name' => $this->faskes_file->getClientOriginalName(),
                    'verification_status' => DocumentVerificationStatus::Pending,
                    'notes' => 'Surat Keterangan Rawat / Faskes',
                ]);
            }

            // 4. Initial Status History
            StatusHistory::create([
                'statusable_type' => ServiceRequest::class,
                'statusable_id' => $request->id,
                'from_status' => null,
                'to_status' => ServiceRequestStatus::Submitted->value,
                'notes' => $isEmergency
                    ? 'Permohonan Reaktivasi KIS PBI-JK DARURAT MEDIS diajukan. Ditandai prioritas penanganan 1x24 jam.'
                    : 'Permohonan Reaktivasi KIS PBI-JK diajukan melalui portal SAPA SOSIAL.',
                'user_id' => Auth::id(),
                'created_at' => now(),
            ]);

            $this->submittedTicket = $request->request_number;
            $this->submittedAt = $request->submitted_at->translatedFormat('d F Y, H:i');
            $this->isSuccess = true;
        });
    }

    public function render()
    {
        $districts = District::orderBy('name')->get();
        $villages = $this->district_id
            ? Village::where('district_id', $this->district_id)->orderBy('name')->get()
            : collect();

        return view('livewire.public.kis-reactivation-application', [
            'districts' => $districts,
            'villages' => $villages,
        ]);
    }
}
