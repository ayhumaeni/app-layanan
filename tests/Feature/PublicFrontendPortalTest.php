<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class PublicFrontendPortalTest extends TestCase
{
    public function test_home_page_can_be_rendered(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('SAPA SOSIAL');
        $response->assertSee('Satu Pintu Layanan Sosial Kabupaten Blitar');
    }

    public function test_service_catalog_can_be_rendered(): void
    {
        $response = $this->get('/layanan');
        $response->assertStatus(200);
        $response->assertSee('Katalog resmi program');
    }

    public function test_service_detail_can_be_rendered(): void
    {
        $response = $this->get('/layanan/surat-keterangan-dtsen');
        $response->assertStatus(200);
        $response->assertSee('Surat Keterangan Data Tunggal Sosial Ekonomi Nasional (DTSEN)');
    }

    public function test_dtsen_application_form_can_be_rendered(): void
    {
        $response = $this->get('/layanan-sk-dtsen/ajukan');
        $response->assertStatus(200);
        $response->assertSee('Formulir Pengajuan Surat Keterangan DTSEN');
    }

    public function test_kis_reactivation_form_can_be_rendered(): void
    {
        $response = $this->get('/layanan-reaktivasi-kis/ajukan');
        $response->assertStatus(200);
        $response->assertSee('Formulir Reaktivasi KIS / PBI-JK');
    }

    public function test_complaint_form_can_be_rendered(): void
    {
        $response = $this->get('/pengaduan');
        $response->assertStatus(200);
        $response->assertSee('Formulir Pengaduan');
    }

    public function test_ticket_tracking_can_be_rendered_and_find_ticket(): void
    {
        $response = $this->get('/lacak?tiket=DTSEN-202609-00001');
        $response->assertStatus(200);
        $response->assertSee('Lacak Status Pengajuan');
        $response->assertSee('DTSEN-202609-00001');
    }

    public function test_certificate_verification_validates_official_code(): void
    {
        $response = $this->get('/verifikasi-surat/VRF-DTSEN-202609-001');
        $response->assertStatus(200);
        $response->assertSee('DOKUMEN RESMI TERVERIFIKASI');
        $response->assertSee('Ananda Rizky Pratama');
        $response->assertSee('400.9/012/409.105/2026');
    }

    public function test_login_and_register_pages_can_be_rendered(): void
    {
        $this->get('/masuk')->assertStatus(200)->assertSee('Masuk ke Akun Anda');
        $this->get('/daftar')->assertStatus(200)->assertSee('Pendaftaran Akun Baru');
    }

    public function test_citizen_dashboard_requires_authentication(): void
    {
        $response = $this->get('/akun');
        $response->assertRedirect('/masuk');
    }

    public function test_authenticated_citizen_can_access_dashboard(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/akun');
        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee('Akun Saya');
    }
}
