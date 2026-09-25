<?php

declare(strict_types=1);

namespace Database\Seeders;

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

        // Permissions
        $permissions = [
            'manage_users',
            'manage_master_data',
            'manage_services',
            'verify_services',
            'sign_documents',
            'manage_rehabilitation',
            'manage_complaints',
            'manage_information',
            'view_dashboard',
            'view_reports',
            'submit_requests',
            'submit_complaints',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Roles
        $admin = Role::firstOrCreate(['name' => 'administrator', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        $petugas = Role::firstOrCreate(['name' => 'petugas_dinsos', 'guard_name' => 'web']);
        $petugas->syncPermissions([
            'manage_services',
            'verify_services',
            'manage_rehabilitation',
            'manage_complaints',
            'view_dashboard',
            'view_reports',
        ]);

        $pejabat = Role::firstOrCreate(['name' => 'pejabat_penandatangan', 'guard_name' => 'web']);
        $pejabat->syncPermissions([
            'view_dashboard',
            'view_reports',
            'sign_documents',
        ]);

        $pimpinan = Role::firstOrCreate(['name' => 'pimpinan', 'guard_name' => 'web']);
        $pimpinan->syncPermissions([
            'view_dashboard',
            'view_reports',
        ]);

        $operator = Role::firstOrCreate(['name' => 'operator_kecamatan_desa', 'guard_name' => 'web']);
        $operator->syncPermissions([
            'submit_requests',
            'submit_complaints',
            'view_dashboard',
        ]);

        $masyarakat = Role::firstOrCreate(['name' => 'masyarakat', 'guard_name' => 'web']);
        $masyarakat->syncPermissions([
            'submit_requests',
            'submit_complaints',
        ]);
    }
}
