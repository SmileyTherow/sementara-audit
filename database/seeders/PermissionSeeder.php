<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $permissions = [
            [
                'name' => 'Kelola KWT',
                'slug' => 'manage_kwt',
                'description' => 'Membuat, melihat, mengubah, dan menghapus data KWT.',
            ],
            [
                'name' => 'Kelola Akun',
                'slug' => 'manage_users',
                'description' => 'Mengelola akun pengguna sistem.',
            ],
            [
                'name' => 'Kelola Master Tanaman',
                'slug' => 'manage_commodities',
                'description' => 'Mengelola kategori dan master tanaman.',
            ],
            [
                'name' => 'Input Data Tanam',
                'slug' => 'create_cultivation',
                'description' => 'Membuat transaksi masa tanam.',
            ],
            [
                'name' => 'Mengubah Data Tanam',
                'slug' => 'update_cultivation',
                'description' => 'Mengubah data transaksi masa tanam.',
            ],
            [
                'name' => 'Input Data Panen',
                'slug' => 'create_harvest',
                'description' => 'Membuat transaksi panen.',
            ],
            [
                'name' => 'Mengubah Realisasi Panen',
                'slug' => 'update_harvest_realization',
                'description' => 'Mengubah jumlah realisasi panen.',
            ],
            [
                'name' => 'Melihat Dashboard',
                'slug' => 'view_dashboard',
                'description' => 'Melihat dashboard sesuai hak akses.',
            ],
            [
                'name' => 'Melihat Seluruh KWT',
                'slug' => 'view_all_kwts',
                'description' => 'Melihat data seluruh KWT.',
            ],
            [
                'name' => 'Melihat Laporan',
                'slug' => 'view_reports',
                'description' => 'Melihat laporan bulanan dan tahunan.',
            ],
            [
                'name' => 'Melihat Produk',
                'slug' => 'view_products',
                'description' => 'Melihat produk siap jual.',
            ],
            [
                'name' => 'Kelola Produk',
                'slug' => 'manage_products',
                'description' => 'Mengelola produk siap jual.',
            ],
            [
                'name' => 'Melihat Monitoring Panen',
                'slug' => 'view_harvest_monitoring',
                'description' => 'Melihat monitoring hasil panen.',
            ],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $permission['slug']],
                [
                    'name' => $permission['name'],
                    'description' => $permission['description'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        $rolePermissions = [
            'super_admin' => [
                'manage_kwt',
                'manage_users',
                'manage_commodities',
                'create_cultivation',
                'update_cultivation',
                'create_harvest',
                'update_harvest_realization',
                'view_dashboard',
                'view_all_kwts',
                'view_reports',
                'view_products',
                'manage_products',
                'view_harvest_monitoring',
            ],
            'operator_kwt' => [
                'create_cultivation',
                'update_cultivation',
                'create_harvest',
                'update_harvest_realization',
                'view_dashboard',
                'view_reports',
                'view_harvest_monitoring',
            ],
            'koperasi' => [
                'view_dashboard',
                'view_all_kwts',
                'view_reports',
                'view_products',
                'view_harvest_monitoring',
            ],
        ];

        foreach ($rolePermissions as $roleSlug => $permissionSlugs) {
            $role = DB::table('roles')
                ->where('slug', $roleSlug)
                ->first();

            if (!$role) {
                continue;
            }

            foreach ($permissionSlugs as $permissionSlug) {
                $permission = DB::table('permissions')
                    ->where('slug', $permissionSlug)
                    ->first();

                if (!$permission) {
                    continue;
                }

                DB::table('permission_role')->updateOrInsert(
                    [
                        'permission_id' => $permission->id,
                        'role_id' => $role->id,
                    ],
                    [
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }
    }
}
