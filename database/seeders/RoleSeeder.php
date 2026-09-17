<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super_admin',
                'description' => 'Mengelola seluruh data dan konfigurasi sistem.',
            ],
            [
                'name' => 'Operator KWT',
                'slug' => 'operator_kwt',
                'description' => 'Mengelola data tanam, monitoring, dan panen KWT.',
            ],
            [
                'name' => 'Koperasi',
                'slug' => 'koperasi',
                'description' => 'Melihat dashboard, hasil panen, dan produk siap jual.',
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['slug' => $role['slug']],
                [
                    'name' => $role['name'],
                    'description' => $role['description'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
