<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $roles = DB::table('roles')
            ->whereIn('slug', ['super_admin', 'operator_kwt', 'koperasi'])
            ->pluck('id', 'slug');

        $kwts = DB::table('kwts')
            ->pluck('id', 'code');

        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@kwt.test',
                'phone' => '081200000010',
                'password' => 'password123',
                'role_slug' => 'super_admin',
                'kwt_id' => null,
            ],
            [
                'name' => 'Operator KWT Mawar',
                'email' => 'operator1@kwt.test',
                'phone' => '081200000011',
                'password' => 'password123',
                'role_slug' => 'operator_kwt',
                'kwt_id' => $kwts['KWT-001'] ?? null,
            ],
            [
                'name' => 'Operator KWT Melati',
                'email' => 'operator2@kwt.test',
                'phone' => '081200000012',
                'password' => 'password123',
                'role_slug' => 'operator_kwt',
                'kwt_id' => $kwts['KWT-002'] ?? null,
            ],
            [
                'name' => 'Koperasi Desa',
                'email' => 'koperasi@kwt.test',
                'phone' => '081200000013',
                'password' => 'password123',
                'role_slug' => 'koperasi',
                'kwt_id' => null,
            ],
        ];

        foreach ($users as $userData) {
            $roleId = $roles[$userData['role_slug']] ?? null;

            $userId = DB::table('users')->updateOrInsert(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'phone' => $userData['phone'],
                    'password' => Hash::make($userData['password']),
                    'kwt_id' => $userData['kwt_id'],
                    'is_active' => true,
                    'email_verified_at' => $now,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );

            $user = DB::table('users')
                ->where('email', $userData['email'])
                ->first();

            if (!$user || !$roleId) {
                continue;
            }

            DB::table('role_user')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'role_id' => $roleId,
                ],
                [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
