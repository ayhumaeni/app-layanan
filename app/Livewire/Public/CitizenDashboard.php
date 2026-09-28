<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Models\Complaint;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard Akun Saya — SAPA SOSIAL Kab. Blitar')]
class CitizenDashboard extends Component
{
    public string $activeTab = 'all'; // 'all', 'in_process', 'completed'

    public function selectTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        $user = Auth::user();

        // Query user's ServiceRequests
        $requestsQuery = ServiceRequest::with(['serviceType', 'village.district'])
            ->where(function ($q) use ($user) {
                $q->where('submitter_id', $user->id)
                    ->orWhere('applicant_nik', $user->nik);
            });

        // Query user's Complaints
        $complaintsQuery = Complaint::with(['category', 'village.district'])
            ->where(function ($q) use ($user) {
                $q->where('reporter_id', $user->id)
                    ->orWhere('reporter_phone', $user->phone);
            });

        $allRequests = $requestsQuery->latest('submitted_at')->get();
        $allComplaints = $complaintsQuery->latest('reported_at')->get();

        // Calculate metrics
        $inProcessCount = 0;
        $completedCount = 0;

        foreach ($allRequests as $req) {
            if ($req->status->value === 'completed') {
                $completedCount++;
            } else {
                $inProcessCount++;
            }
        }

        foreach ($allComplaints as $comp) {
            if ($comp->status->value === 'resolved') {
                $completedCount++;
            } else {
                $inProcessCount++;
            }
        }

        // Filter items based on activeTab
        $items = collect();

        foreach ($allRequests as $req) {
            $isDone = ($req->status->value === 'completed');
            if ($this->activeTab === 'in_process' && $isDone) {
                continue;
            }
            if ($this->activeTab === 'completed' && ! $isDone) {
                continue;
            }

            $items->push([
                'type' => 'request',
                'number' => $req->request_number,
                'title' => $req->serviceType?->name ?? 'Pengajuan Layanan',
                'category' => $req->serviceType?->category ?? 'Layanan Sosial',
                'date' => $req->submitted_at?->translatedFormat('d M Y, H:i') ?? '-',
                'status_label' => $req->status->label(),
                'status_color' => $req->status->color(),
                'is_completed' => $isDone,
                'notes' => $req->officer_notes,
            ]);
        }

        foreach ($allComplaints as $comp) {
            $isDone = ($comp->status->value === 'resolved');
            if ($this->activeTab === 'in_process' && $isDone) {
                continue;
            }
            if ($this->activeTab === 'completed' && ! $isDone) {
                continue;
            }

            $items->push([
                'type' => 'complaint',
                'number' => $comp->complaint_number,
                'title' => 'Pengaduan: '.($comp->category?->name ?? 'Masalah Sosial'),
                'category' => 'Pengaduan Warga',
                'date' => $comp->reported_at?->translatedFormat('d M Y, H:i') ?? '-',
                'status_label' => $comp->status->label(),
                'status_color' => $comp->status->color(),
                'is_completed' => $isDone,
                'notes' => $comp->verification_result,
            ]);
        }

        return view('livewire.public.citizen-dashboard', [
            'user' => $user,
            'items' => $items,
            'totalCount' => $allRequests->count() + $allComplaints->count(),
            'inProcessCount' => $inProcessCount,
            'completedCount' => $completedCount,
        ]);
    }
}
