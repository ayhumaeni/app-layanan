<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Enums\DocumentVerificationStatus;
use App\Enums\ServiceRequestStatus;
use App\Models\District;
use App\Models\DtsenCertificate;
use App\Models\DtsenPurpose;
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
#[Title('Formulir Pengajuan Surat Keterangan DTSEN — SAPA SOSIAL Kab. Blitar')]
class DtsenApplication extends Component
{
    use WithFileUploads;

    public int $currentStep = 1;

    // Step 1: Tujuan
    public ?int $dtsen_purpose_id = null;

    public string $purpose_description = '';

    // Step 2: Identitas Pemohon & Subjek
    public string $applicant_name = '';

    public string $applicant_nik = '';

    public string $family_card_number = '';

    public string $phone = '';

    public ?int $district_id = null;

    public ?int $village_id = null;

    public string $address = '';

    public string $subject_name = '';

    public string $subject_nik = '';

    public string $relationship_to_applicant = 'Diri Sendiri';

    // Step 3: Berkas Persyaratan
    public $ktp_file;

    public $kk_file;

    // Step 4: Pernyataan
    public bool $agreement_confirmed = false;

    // Result / Success state
    public bool $isSuccess = false;

    public ?string $submittedTicket = null;

    public ?string $submittedAt = null;

    public function mount(): void
    {
        // Pre-select default purpose if available
        $firstPurpose = DtsenPurpose::where('is_active', true)->first();
        if ($firstPurpose) {
            $this->dtsen_purpose_id = $firstPurpose->id;
        }

        // If citizen is logged in, prefill information
        if (Auth::check()) {
            $user = Auth::user();
            $this->applicant_name = (string) $user->name;
            $this->applicant_nik = (string) ($user->nik ?? '');
            $this->phone = (string) ($user->phone ?? '');
            $this->district_id = $user->district_id;
            $this->village_id = $user->village_id;

            // Default subject to applicant
            $this->subject_name = (string) $user->name;
            $this->subject_nik = (string) ($user->nik ?? '');
        }
    }

    public function updatedRelationshipToApplicant(string $value): void
    {
        if ($value === 'Diri Sendiri') {
            $this->subject_name = $this->applicant_name;
            $this->subject_nik = $this->applicant_nik;
        }
    }

    public function updatedApplicantName(string $value): void
    {
        if ($this->relationship_to_applicant === 'Diri Sendiri') {
            $this->subject_name = $value;
        }
    }

    public function updatedApplicantNik(string $value): void
    {
        if ($this->relationship_to_applicant === 'Diri Sendiri') {
            $this->subject_nik = $value;
        }
    }

    public function updatedDistrictId(): void
    {
        $this->village_id = null;
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
            $selectedPurpose = DtsenPurpose::find($this->dtsen_purpose_id);
            $rules = [
                'dtsen_purpose_id' => 'required|exists:dtsen_purposes,id',
            ];
            if ($selectedPurpose && $selectedPurpose->code === 'lainnya') {
                $rules['purpose_description'] = 'required|string|min:5|max:255';
            }
            $this->validate($rules, [
                'dtsen_purpose_id.required' => 'Pilih salah satu tujuan penggunaan surat keterangan.',
                'purpose_description.required' => 'Keterangan tujuan wajib diisi jika memilih opsi lainnya.',
            ]);
        } elseif ($this->currentStep === 2) {
            $this->validate([
                'applicant_name' => 'required|string|min:3|max:100',
                'applicant_nik' => 'required|digits:16',
                'family_card_number' => 'required|digits:16',
                'phone' => 'required|string|min:9|max:16',
                'district_id' => 'required|exists:districts,id',
                'village_id' => 'required|exists:villages,id',
                'address' => 'required|string|min:5|max:255',
                'subject_name' => 'required|string|min:3|max:100',
                'subject_nik' => 'required|digits:16',
                'relationship_to_applicant' => 'required|string',
            ], [
                'applicant_name.required' => 'Nama lengkap pemohon wajib diisi.',
                'applicant_nik.required' => 'NIK pemohon wajib diisi.',
                'applicant_nik.digits' => 'NIK harus berjumlah tepat 16 digit angka.',
                'family_card_number.required' => 'Nomor KK wajib diisi.',
                'family_card_number.digits' => 'Nomor Kartu Keluarga harus berjumlah 16 digit angka.',
                'phone.required' => 'Nomor WhatsApp / telepon wajib diisi untuk notifikasi tiket.',
                'district_id.required' => 'Pilih kecamatan domisili.',
                'village_id.required' => 'Pilih desa/kelurahan domisili.',
                'address.required' => 'Alamat lengkap RT/RW/Dusun wajib diisi.',
                'subject_name.required' => 'Nama orang yang diterangkan wajib diisi.',
                'subject_nik.digits' => 'NIK orang yang diterangkan harus berjumlah 16 digit.',
            ]);
        } elseif ($this->currentStep === 3) {
            $this->validate([
                'ktp_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:3072',
                'kk_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:3072',
            ], [
                'ktp_file.required' => 'Foto / Scan KTP wajib diunggah.',
                'ktp_file.max' => 'Ukuran file KTP maksimal 3 MB.',
                'kk_file.required' => 'Foto / Scan Kartu Keluarga wajib diunggah.',
                'kk_file.max' => 'Ukuran file KK maksimal 3 MB.',
            ]);
        }
    }

    public function submit(): void
    {
        $this->validate([
            'agreement_confirmed' => 'accepted',
        ], [
            'agreement_confirmed.accepted' => 'Anda harus mencentang pernyataan kebenaran data sebelum mengirim permohonan.',
        ]);

        $serviceType = ServiceType::where('code', 'DTSEN')->firstOrFail();

        DB::transaction(function () use ($serviceType) {
            // 1. Create ServiceRequest
            $request = new ServiceRequest;
            $request->service_type_id = $serviceType->id;
            $request->submitter_id = Auth::id();
            $request->applicant_name = $this->applicant_name;
            $request->applicant_nik = $this->applicant_nik;
            $request->family_card_number = $this->family_card_number;
            $request->address = $this->address;
            $request->village_id = $this->village_id;
            $request->phone = $this->phone;
            $request->status = ServiceRequestStatus::Submitted;
            $request->submitted_at = now();
            $request->save();

            // 2. Create DtsenCertificate record
            $cert = new DtsenCertificate;
            $cert->service_request_id = $request->id;
            $cert->dtsen_purpose_id = $this->dtsen_purpose_id;
            $cert->purpose_description = $this->purpose_description ?: null;
            $cert->subject_name = $this->subject_name;
            $cert->subject_nik = $this->subject_nik;
            $cert->relationship_to_applicant = $this->relationship_to_applicant;
            $cert->save();

            // 3. Store uploaded KTP & KK documents
            $ktpPath = $this->ktp_file->store('service_requests/'.$request->id.'/documents', 'local');
            ServiceRequestDocument::create([
                'service_request_id' => $request->id,
                'service_requirement_id' => $serviceType->requirements()->where('name', 'ILIKE', '%KTP%')->value('id'),
                'file_path' => $ktpPath,
                'original_name' => $this->ktp_file->getClientOriginalName(),
                'verification_status' => DocumentVerificationStatus::Pending,
                'notes' => 'Dokumen KTP pemohon',
            ]);

            $kkPath = $this->kk_file->store('service_requests/'.$request->id.'/documents', 'local');
            ServiceRequestDocument::create([
                'service_request_id' => $request->id,
                'service_requirement_id' => $serviceType->requirements()->where('name', 'ILIKE', '%Kartu Keluarga%')->value('id'),
                'file_path' => $kkPath,
                'original_name' => $this->kk_file->getClientOriginalName(),
                'verification_status' => DocumentVerificationStatus::Pending,
                'notes' => 'Dokumen Kartu Keluarga (KK)',
            ]);

            // 4. Record initial Status History
            StatusHistory::create([
                'statusable_type' => ServiceRequest::class,
                'statusable_id' => $request->id,
                'from_status' => null,
                'to_status' => ServiceRequestStatus::Submitted->value,
                'notes' => 'Permohonan Surat Keterangan DTSEN berhasil diajukan oleh pemohon melalui portal SAPA SOSIAL.',
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
        $purposes = DtsenPurpose::where('is_active', true)->get();
        $districts = District::orderBy('name')->get();
        $villages = $this->district_id
            ? Village::where('district_id', $this->district_id)->orderBy('name')->get()
            : collect();

        $selectedPurpose = DtsenPurpose::find($this->dtsen_purpose_id);

        return view('livewire.public.dtsen-application', [
            'purposes' => $purposes,
            'districts' => $districts,
            'villages' => $villages,
            'selectedPurpose' => $selectedPurpose,
        ]);
    }
}
