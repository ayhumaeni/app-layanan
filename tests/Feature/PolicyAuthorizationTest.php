<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Complaint;
use App\Models\District;
use App\Models\RehabilitationCase;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\Village;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class PolicyAuthorizationTest extends TestCase
{
    protected ?User $admin;

    protected ?User $petugas;

    protected ?User $pimpinan;

    protected ?User $operatorKanigoro;

    protected ?User $masyarakatBudi;

    protected ?User $masyarakatSiti;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::where('email', 'admin@blitarkab.go.id')->first();
        $this->petugas = User::where('email', 'petugas.dtsen@blitarkab.go.id')->first();
        $this->pimpinan = User::where('email', 'sekdin@blitarkab.go.id')->first();
        $this->operatorKanigoro = User::where('email', 'operator.kanigoro@blitarkab.go.id')->first();
        $this->masyarakatBudi = User::where('email', 'budi.santoso@gmail.com')->first();
        $this->masyarakatSiti = User::where('email', 'siti.aminah@gmail.com')->first();
    }

    public function test_admin_bypasses_all_policies_via_gate_before(): void
    {
        $this->assertNotNull($this->admin);

        $district = District::first();
        $this->assertTrue(Gate::forUser($this->admin)->allows('delete', $district));

        $sr = ServiceRequest::first();
        $this->assertTrue(Gate::forUser($this->admin)->allows('delete', $sr));

        $case = RehabilitationCase::first();
        $this->assertTrue(Gate::forUser($this->admin)->allows('delete', $case));
    }

    public function test_service_request_policy_territory_scoping(): void
    {
        $this->assertNotNull($this->operatorKanigoro);

        // Cari desa di Kanigoro
        $kanigoroVillage = Village::whereHas('district', fn ($q) => $q->where('name', 'like', '%Kanigoro%'))->first();
        // Cari desa di luar Kanigoro (misal Wlingi)
        $wlingiVillage = Village::whereHas('district', fn ($q) => $q->where('name', 'like', '%Wlingi%'))->first();

        $this->assertNotNull($kanigoroVillage);
        $this->assertNotNull($wlingiVillage);

        $requestInKanigoro = ServiceRequest::where('village_id', $kanigoroVillage->id)->first();
        $requestInWlingi = ServiceRequest::where('village_id', $wlingiVillage->id)->first();

        if ($requestInKanigoro) {
            $this->assertTrue(Gate::forUser($this->operatorKanigoro)->allows('view', $requestInKanigoro));
        }

        if ($requestInWlingi) {
            $this->assertFalse(Gate::forUser($this->operatorKanigoro)->allows('view', $requestInWlingi));
            $this->assertFalse(Gate::forUser($this->operatorKanigoro)->allows('update', $requestInWlingi));
        }
    }

    public function test_service_request_policy_submitter_scoping(): void
    {
        $this->assertNotNull($this->masyarakatBudi);
        $this->assertNotNull($this->masyarakatSiti);

        $budiRequest = ServiceRequest::where('submitter_id', $this->masyarakatBudi->id)->first();
        $sitiRequest = ServiceRequest::where('submitter_id', $this->masyarakatSiti->id)->first();

        if ($budiRequest) {
            $this->assertTrue(Gate::forUser($this->masyarakatBudi)->allows('view', $budiRequest));
            $this->assertFalse(Gate::forUser($this->masyarakatSiti)->allows('view', $budiRequest));
        }

        if ($sitiRequest) {
            $this->assertTrue(Gate::forUser($this->masyarakatSiti)->allows('view', $sitiRequest));
            $this->assertFalse(Gate::forUser($this->masyarakatBudi)->allows('view', $sitiRequest));
        }
    }

    public function test_pimpinan_can_view_but_cannot_update_or_delete_service_request(): void
    {
        $this->assertNotNull($this->pimpinan);
        $sr = ServiceRequest::first();
        $this->assertNotNull($sr);

        $this->assertTrue(Gate::forUser($this->pimpinan)->allows('view', $sr));
        $this->assertFalse(Gate::forUser($this->pimpinan)->allows('update', $sr));
        $this->assertFalse(Gate::forUser($this->pimpinan)->allows('delete', $sr));
    }

    public function test_petugas_can_view_and_update_service_request_but_cannot_delete(): void
    {
        $this->assertNotNull($this->petugas);
        $sr = ServiceRequest::first();
        $this->assertNotNull($sr);

        $this->assertTrue(Gate::forUser($this->petugas)->allows('view', $sr));
        $this->assertTrue(Gate::forUser($this->petugas)->allows('update', $sr));
        $this->assertFalse(Gate::forUser($this->petugas)->allows('delete', $sr));
    }

    public function test_complaint_policy_ownership_and_roles(): void
    {
        $this->assertNotNull($this->masyarakatBudi);
        $this->assertNotNull($this->masyarakatSiti);

        $budiComplaint = Complaint::where('reporter_id', $this->masyarakatBudi->id)->first();
        if ($budiComplaint) {
            $this->assertTrue(Gate::forUser($this->masyarakatBudi)->allows('view', $budiComplaint));
            $this->assertFalse(Gate::forUser($this->masyarakatSiti)->allows('view', $budiComplaint));
        }

        // Petugas pengaduan
        $petugasPengaduan = User::where('email', 'petugas.pengaduan@blitarkab.go.id')->first();
        if ($petugasPengaduan && $budiComplaint) {
            $this->assertTrue(Gate::forUser($petugasPengaduan)->allows('view', $budiComplaint));
            $this->assertTrue(Gate::forUser($petugasPengaduan)->allows('update', $budiComplaint));
            $this->assertFalse(Gate::forUser($petugasPengaduan)->allows('delete', $budiComplaint));
        }
    }

    public function test_rehabilitation_case_officer_assignment_scoping(): void
    {
        $petugas1 = User::where('email', 'petugas.rehsos@blitarkab.go.id')->first();
        $petugas2 = User::where('email', 'petugas.dtsen@blitarkab.go.id')->first();
        $this->assertNotNull($petugas1);
        $this->assertNotNull($petugas2);

        $caseAssignedToPetugas1 = RehabilitationCase::where('officer_id', $petugas1->id)->first();
        if ($caseAssignedToPetugas1) {
            $this->assertTrue(Gate::forUser($petugas1)->allows('view', $caseAssignedToPetugas1));
            $this->assertTrue(Gate::forUser($petugas1)->allows('update', $caseAssignedToPetugas1));

            // Petugas 2 (bukan petugas kasus ini) tidak boleh mengedit
            $this->assertFalse(Gate::forUser($petugas2)->allows('update', $caseAssignedToPetugas1));
        }
    }

    public function test_master_data_policies_reject_unauthorized_users(): void
    {
        $this->assertNotNull($this->masyarakatBudi);
        $district = District::first();

        // Masyarakat tidak boleh membuat, mengedit, atau menghapus master data
        $this->assertFalse(Gate::forUser($this->masyarakatBudi)->allows('create', District::class));
        $this->assertFalse(Gate::forUser($this->masyarakatBudi)->allows('update', $district));
        $this->assertFalse(Gate::forUser($this->masyarakatBudi)->allows('delete', $district));
    }
}
