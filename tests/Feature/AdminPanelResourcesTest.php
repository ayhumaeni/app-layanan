<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdminPanelResourcesTest extends TestCase
{
    public function test_admin_can_access_filament_dashboard(): void
    {
        $admin = User::where('email', 'admin@blitarkab.go.id')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
    }

    public function test_admin_can_access_service_requests_page(): void
    {
        $admin = User::where('email', 'admin@blitarkab.go.id')->first();

        $response = $this->actingAs($admin)->get('/admin/service-requests');
        $response->assertStatus(200);
    }

    public function test_admin_can_access_rehabilitation_cases_and_clients_page(): void
    {
        $admin = User::where('email', 'admin@blitarkab.go.id')->first();

        $this->actingAs($admin)->get('/admin/rehabilitation-cases')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/rehabilitation-cases/create')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/clients')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/clients/create')->assertStatus(200);
    }

    public function test_admin_can_access_complaints_page(): void
    {
        $admin = User::where('email', 'admin@blitarkab.go.id')->first();

        $response = $this->actingAs($admin)->get('/admin/complaints');
        $response->assertStatus(200);
    }

    public function test_admin_can_access_master_data_pages(): void
    {
        $admin = User::where('email', 'admin@blitarkab.go.id')->first();

        $this->actingAs($admin)->get('/admin/districts')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/villages')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/work-units')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/service-types')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/dtsen-purposes')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/client-categories')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/referral-institutions')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/complaint-categories')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/users')->assertStatus(200);
    }

    public function test_admin_can_access_roles_and_permissions_pages(): void
    {
        $admin = User::where('email', 'admin@blitarkab.go.id')->first();

        $this->actingAs($admin)->get('/admin/roles')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/roles/create')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/permissions')->assertStatus(200);
    }

    public function test_staff_without_manage_users_permission_cannot_access_roles(): void
    {
        $petugas = User::where('email', 'petugas.dtsen@blitarkab.go.id')->first();
        $this->assertNotNull($petugas);

        $this->actingAs($petugas)->get('/admin/roles')->assertStatus(403);
        $this->actingAs($petugas)->get('/admin/permissions')->assertStatus(403);
    }

    public function test_masyarakat_cannot_access_admin_panel(): void
    {
        $masyarakat = User::where('email', 'budi.santoso@gmail.com')->first();
        $this->assertNotNull($masyarakat);

        $this->actingAs($masyarakat)->get('/admin')->assertStatus(403);
    }
}
