<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;       // استخدام موديل Spatie
use Spatie\Permission\Models\Permission; // استخدام موديل Spatie
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. تفريغ الكاش الخاص بالصلاحيات قبل الإنشاء
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // حدد الـ Guard المناسب لمشروعك (api هو الافتراضي لـ Sanctum)
        $guardName = 'api';

        // 2. قائمة الصلاحيات
        $permissions = [
            'view dashboard',
            'manage users',
            'manage roles',
            'manage permissions',
            'manage regions',
            'manage specializations',
            'manage feasibility requests',
            'manage feasibility studies',
            'manage consultations',
            'view knowledge base',
            'create feasibility request',
            'view own feasibility request',
            'answer consultations',
            'manage profile',
        ];

        // 3. إنشاء الصلاحيات في قاعدة البيانات
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name'       => $permission,
                'guard_name' => $guardName,
            ]);
        }

        // 4. إنشاء الأدوار
        $admin = Role::firstOrCreate([
            'name'       => 'Admin',
            'guard_name' => $guardName,
        ]);

        $farmer = Role::firstOrCreate([
            'name'       => 'Farmer',
            'guard_name' => $guardName,
        ]);

        $consultant = Role::firstOrCreate([
            'name'       => 'Agricultural Expert',
            'guard_name' => $guardName,
        ]);

        // 5. تعيين الصلاحيات لكل دور
        $admin->syncPermissions([
            'view dashboard',
            'manage users',
            'manage roles',
            'manage permissions',
            'manage regions',
            'manage specializations',
            'manage feasibility requests',
            'manage feasibility studies',
            'manage consultations',
            'view knowledge base',
            'manage profile',
        ]);

        $farmer->syncPermissions([
            'view dashboard',
            'create feasibility request',
            'view own feasibility request',
            'view knowledge base',
            'manage profile',
        ]);

        $consultant->syncPermissions([
            'view dashboard',
            'manage feasibility requests',
            'manage feasibility studies',
            'answer consultations',
            'view knowledge base',
            'manage profile',
        ]);
    }
}