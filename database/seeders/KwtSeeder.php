<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KwtSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $kwts = [
            [
                'code' => 'KWT-001',
                'name' => 'KWT Mawar',
                'leader_name' => 'Ibu Siti',
                'phone' => '081200000001',
                'address' => 'Jl. Sukamaju No. 1',
                'village' => 'Sukamaju',
                'district' => 'Cikupa',
                'city' => 'Tangerang',
                'province' => 'Banten',
                'latitude' => -6.2367000,
                'longitude' => 106.5087000,
            ],
            [
                'code' => 'KWT-002',
                'name' => 'KWT Melati',
                'leader_name' => 'Ibu Rina',
                'phone' => '081200000002',
                'address' => 'Jl. Melati No. 2',
                'village' => 'Mekarsari',
                'district' => 'Panongan',
                'city' => 'Tangerang',
                'province' => 'Banten',
                'latitude' => -6.2198000,
                'longitude' => 106.4991000,
            ],
            [
                'code' => 'KWT-003',
                'name' => 'KWT Anggrek',
                'leader_name' => 'Ibu Lina',
                'phone' => '081200000003',
                'address' => 'Jl. Anggrek No. 3',
                'village' => 'Bojong',
                'district' => 'Cisauk',
                'city' => 'Tangerang',
                'province' => 'Banten',
                'latitude' => -6.2965000,
                'longitude' => 106.6398000,
            ],
            [
                'code' => 'KWT-004',
                'name' => 'KWT Seroja',
                'leader_name' => 'Ibu Dewi',
                'phone' => '081200000004',
                'address' => 'Jl. Seroja No. 4',
                'village' => 'Sukamulya',
                'district' => 'Pagedangan',
                'city' => 'Tangerang',
                'province' => 'Banten',
                'latitude' => -6.3021000,
                'longitude' => 106.6216000,
            ],
        ];

        foreach ($kwts as $kwt) {
            DB::table('kwts')->updateOrInsert(
                ['code' => $kwt['code']],
                [
                    'name' => $kwt['name'],
                    'leader_name' => $kwt['leader_name'],
                    'phone' => $kwt['phone'],
                    'address' => $kwt['address'],
                    'village' => $kwt['village'],
                    'district' => $kwt['district'],
                    'city' => $kwt['city'],
                    'province' => $kwt['province'],
                    'latitude' => $kwt['latitude'],
                    'longitude' => $kwt['longitude'],
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
