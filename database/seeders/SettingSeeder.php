<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $settings = [
            [
                'key' => 'app_name',
                'value' => 'Sistem Pelaporan KWT',
                'description' => 'Nama aplikasi.',
            ],
            [
                'key' => 'whatsapp_number',
                'value' => '6281200000013',
                'description' => 'Nomor WhatsApp koperasi tanpa tanda plus.',
            ],
            [
                'key' => 'koperasi_contact',
                'value' => 'Koperasi Desa',
                'description' => 'Nama kontak koperasi.',
            ],
            [
                'key' => 'default_harvest_alert_days',
                'value' => '10,7',
                'description' => 'Alert panen yang digunakan.',
            ],
            [
                'key' => 'public_website_enabled',
                'value' => '1',
                'description' => 'Mengaktifkan website publik.',
            ],
            [
                'key' => 'dashboard_refresh_interval',
                'value' => '60',
                'description' => 'Interval refresh dashboard dalam detik.',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'description' => $setting['description'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
