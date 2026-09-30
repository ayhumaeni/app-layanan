<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PermissionType;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Buat semua permission dari enum ──────────────────────
        foreach (PermissionType::cases() as $permission) {
            Permission::firstOrCreate([
                'name' => $permission->value,
                'guard_name' => 'web',
            ]);
        }

        // ── Roles ────────────────────────────────────────────────

        // Administrator — menggunakan Gate::before (Super-Admin), tapi tetap diberi semua permission
        // agar UI Filament otomatis menampilkan semua menu.
        $admin = Role::firstOrCreate(['name' => 'administrator', 'guard_name' => 'web']);
        $admin->syncPermissions(PermissionType::values());

        // Petugas Dinsos — proses pengajuan, pengaduan, rehabilitasi
        $petugas = Role::firstOrCreate(['name' => 'petugas_dinsos', 'guard_name' => 'web']);
        $petugas->syncPermissions([
            PermissionType::ManageServiceRequests->value,
            PermissionType::VerifyServiceRequests->value,
            PermissionType::VerifySiksng->value,
            PermissionType::ManageRehabilitation->value,
            PermissionType::ManageClients->value,
            PermissionType::ManageComplaints->value,
            PermissionType::DispatchComplaints->value,
            PermissionType::ViewDashboard->value,
            PermissionType::ViewReports->value,
        ]);

        // Pejabat Penandatangan — paraf & tanda tangan + lihat
        $pejabat = Role::firstOrCreate(['name' => 'pejabat_penandatangan', 'guard_name' => 'web']);
        $pejabat->syncPermissions([
            PermissionType::SignDocuments->value,
            PermissionType::ViewServiceRequests->value,
            PermissionType::ViewDashboard->value,
            PermissionType::ViewReports->value,
        ]);

        // Pimpinan — hanya baca: dashboard, laporan, ringkasan
        $pimpinan = Role::firstOrCreate(['name' => 'pimpinan', 'guard_name' => 'web']);
        $pimpinan->syncPermissions([
            PermissionType::ViewServiceRequests->value,
            PermissionType::ViewRehabilitation->value,
            PermissionType::ViewComplaints->value,
            PermissionType::ViewDashboard->value,
            PermissionType::ViewReports->value,
        ]);

        // Operator Kecamatan/Desa — bantu warga ajukan + pantau di wilayahnya
        $operator = Role::firstOrCreate(['name' => 'operator_kecamatan_desa', 'guard_name' => 'web']);
        $operator->syncPermissions([
            PermissionType::SubmitServiceRequests->value,
            PermissionType::SubmitComplaints->value,
            PermissionType::ViewServiceRequests->value,
            PermissionType::ViewComplaints->value,
            PermissionType::ViewDashboard->value,
        ]);

        // Masyarakat — HANYA portal publik (frontend), TANPA akses Filament
        $masyarakat = Role::firstOrCreate(['name' => 'masyarakat', 'guard_name' => 'web']);
        $masyarakat->syncPermissions([
            PermissionType::SubmitServiceRequests->value,
            PermissionType::SubmitComplaints->value,
        ]);
    }
}
