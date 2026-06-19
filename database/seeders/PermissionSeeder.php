<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Permissions (guard: web)
        $permissions = [
            'dashboard.view',
            'members.view', 'members.manage',
            'periods.view', 'periods.manage',
            'payments.view', 'payments.manage',
            'expenses.view', 'expenses.manage',
            'reports.view',
            'users.manage',
            'roles.manage',
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // 2) Roles beserta permission-nya
        $matrix = [
            'superadmin' => $permissions,
            'bendahara' => [
                'dashboard.view',
                'members.view', 'members.manage',
                'periods.view', 'periods.manage',
                'payments.view', 'payments.manage',
                'expenses.view', 'expenses.manage',
                'reports.view',
            ],
            'anggota' => [
                'dashboard.view',
                'payments.view',
                'reports.view',
            ],
        ];

        foreach ($matrix as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($perms);
        }

        // 3) Assign role ke admin yang sudah ada (berdasarkan kolom legacy `role`)
        $map = ['superadmin' => 'superadmin', 'resident' => 'anggota'];
        foreach (Admin::all() as $admin) {
            $legacy = $admin->getRawOriginal('role') ?? 'resident';
            $target = $map[$legacy] ?? 'anggota';
            $admin->syncRoles($target);
        }
    }
}
