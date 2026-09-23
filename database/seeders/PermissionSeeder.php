<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'api';
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'studies.view-approved', 'studies.request', 'studies.view-own', 'studies.review-feedback', 'studies.manage',
            'visits.create', 'visits.view-own', 'visits.rate', 'visits.view-assigned', 'visits.update-step', 'visits.upload-report', 'visits.manage',
            'profiles.view-directory', 'profiles.manage-own', 'profiles.manage-all',
            'notifications.read-own', 'notifications.broadcast', 'notifications.manage',
            'settings.manage',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => $guard]);
        }

        $assignments = [
            'farmer' => ['studies.view-approved', 'studies.request', 'studies.view-own', 'visits.create', 'visits.view-own', 'visits.rate', 'profiles.view-directory', 'notifications.read-own'],
            'engineer' => ['studies.view-approved', 'studies.review-feedback', 'visits.view-assigned', 'visits.update-step', 'visits.upload-report', 'profiles.manage-own', 'notifications.read-own'],
            'admin' => $permissions,
        ];

        foreach ($assignments as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guard]);
            $role->syncPermissions($rolePermissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
