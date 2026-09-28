<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use App\Models\ComplaintAttachment;
use App\Models\ComplaintCategory;
use App\Models\District;
use App\Models\StatusHistory;
use App\Models\Village;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Formulir Pengaduan Sosial Warga — SAPA SOSIAL Kab. Blitar')]
class ComplaintForm extends Component
{
    use WithFileUploads;

    public ?int $complaint_category_id = null;

    public ?int $district_id = null;

    public ?int $village_id = null;

    public string $location_detail = '';

    public string $description = '';

    public string $reporter_name = '';

    public string $reporter_phone = '';

    /** @var array<int, mixed> */
    public array $attachments = [];

    public bool $isSuccess = false;

    public ?string $submittedTicket = null;

    public ?string $submittedAt = null;

    public function mount(): void
    {
        $firstCategory = ComplaintCategory::where('is_active', true)->first();
        if ($firstCategory) {
            $this->complaint_category_id = $firstCategory->id;
        }

        if (Auth::check()) {
            $user = Auth::user();
            $this->reporter_name = (string) $user->name;
            $this->reporter_phone = (string) ($user->phone ?? '');
            $this->district_id = $user->district_id;
            $this->village_id = $user->village_id;
        }
    }

    public function updatedDistrictId(): void
    {
        $this->village_id = null;
    }

    public function removeAttachment(int $index): void
    {
        unset($this->attachments[$index]);
        $this->attachments = array_values($this->attachments);
    }

    public function submit(): void
    {
        $this->validate([
            'complaint_category_id' => 'required|exists:complaint_categories,id',
            'district_id' => 'required|exists:districts,id',
            'village_id' => 'required|exists:villages,id',
            'location_detail' => 'nullable|string|max:255',
            'description' => 'required|string|min:20|max:2000',
            'reporter_name' => 'required|string|min:3|max:100',
            'reporter_phone' => 'required|string|min:9|max:16',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ], [
            'complaint_category_id.required' => 'Pilih kategori permasalahan yang dilaporkan.',
            'district_id.required' => 'Pilih kecamatan lokasi kejadian.',
            'village_id.required' => 'Pilih desa/kelurahan lokasi kejadian.',
            'description.required' => 'Jelaskan kronologi atau permasalahan yang diadukan.',
            'description.min' => 'Deskripsi laporan minimal 20 karakter agar jelas bagi petugas.',
            'reporter_name.required' => 'Nama pelapor wajib diisi.',
            'reporter_phone.required' => 'Nomor kontak pelapor wajib diisi untuk klarifikasi penanganan.',
        ]);

        DB::transaction(function () {
            $complaint = new Complaint;
            $complaint->complaint_category_id = $this->complaint_category_id;
            $complaint->reporter_id = Auth::id();
            $complaint->reporter_name = $this->reporter_name;
            $complaint->reporter_phone = $this->reporter_phone;
            $complaint->village_id = $this->village_id;
            $complaint->location_detail = $this->location_detail ?: null;
            $complaint->description = $this->description;
            $complaint->status = ComplaintStatus::Received;
            $complaint->reported_at = now();
            $complaint->save();

            // Store uploaded attachments if any
            if (! empty($this->attachments)) {
                foreach ($this->attachments as $file) {
                    $path = $file->store('complaints/'.$complaint->id.'/attachments', 'local');
                    $extension = strtolower((string) $file->getClientOriginalExtension());
                    $type = in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true) ? 'photo' : 'document';

                    ComplaintAttachment::create([
                        'complaint_id' => $complaint->id,
                        'file_path' => $path,
                        'type' => $type,
                    ]);
                }
            }

            // Initial status history
            StatusHistory::create([
                'statusable_type' => Complaint::class,
                'statusable_id' => $complaint->id,
                'from_status' => null,
                'to_status' => ComplaintStatus::Received->value,
                'notes' => 'Laporan pengaduan sosial berhasil dikirimkan oleh warga ke sistem SAPA SOSIAL.',
                'user_id' => Auth::id(),
                'created_at' => now(),
            ]);

            $this->submittedTicket = $complaint->complaint_number;
            $this->submittedAt = $complaint->reported_at->translatedFormat('d F Y, H:i');
            $this->isSuccess = true;
        });
    }

    public function render()
    {
        $categories = ComplaintCategory::where('is_active', true)->get();
        $districts = District::orderBy('name')->get();
        $villages = $this->district_id
            ? Village::where('district_id', $this->district_id)->orderBy('name')->get()
            : collect();

        return view('livewire.public.complaint-form', [
            'categories' => $categories,
            'districts' => $districts,
            'villages' => $villages,
        ]);
    }
}
