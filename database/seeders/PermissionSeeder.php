<?php

namespace Database\Seeders;

use App\Support\PermissionModule;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds to populate all module permissions.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $modules = PermissionModule::all();

        foreach ($modules as $moduleKey => $module) {
            foreach ($module['permissions'] as $permissionName => $label) {
                Permission::firstOrCreate([
                    'name'       => $permissionName,
                    'guard_name' => 'web',
                ]);
            }
        }

        // Assign all permissions to 'admin'
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        // Assign check-in permissions to 'petugas_presensi'
        $petugasRole = Role::firstOrCreate(['name' => 'petugas_presensi', 'guard_name' => 'web']);
        $petugasRole->syncPermissions([
            'view_checkin',
            'process_checkin',
        ]);
    }
}

