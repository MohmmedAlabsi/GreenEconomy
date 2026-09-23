<?php

namespace Database\Seeders;

use App\Models\Specialization;
use Database\Factories\SpecializationFactory;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class SpecializationSeeder extends Seeder
{
    /**
     * تخزين التخصصات الثابتة (SpecializationFactory::NAMES) في قاعدة البيانات.
     * التنفيذ آمن للتكرار: لا تُنشأ أي سجلات مكررة عند إعادة تشغيل الـ Seeder.
     */
    public function run(): void
    {
        // نفس الدور والـ guard المستخدمين في RoleSeeder، فيُعاد استخدامه إن وُجد ويُنشأ إن لم يوجد.
        $role = Role::firstOrCreate([
            'name'       => SpecializationFactory::ROLE_NAME,
            'guard_name' => 'api',
        ]);

        foreach (SpecializationFactory::NAMES as $name) {
            Specialization::firstOrCreate([
                'role_id' => $role->id,
                'name'    => $name,
            ]);
        }
    }
}
