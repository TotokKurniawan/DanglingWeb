<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ─── Order ─────────────────────────────────────────────────
            [
                'key'         => 'order.buyer_cancel_timeout_minutes',
                'value'       => '',
                'type'        => 'integer',
                'group'       => 'order',
                'label'       => 'Batas Waktu Cancel Buyer (menit)',
                'description' => 'Waktu maksimum buyer dapat membatalkan order setelah dibuat. Kosongkan untuk tanpa batas.',
            ],
            [
                'key'         => 'order.auto_complete_hours',
                'value'       => '24',
                'type'        => 'integer',
                'group'       => 'order',
                'label'       => 'Auto-Complete Order (jam)',
                'description' => 'Jam setelah order di-accept maka otomatis dianggap selesai. 0 = nonaktif.',
            ],

            // ─── Seller ────────────────────────────────────────────────
            [
                'key'         => 'seller.default_radius_km',
                'value'       => '10',
                'type'        => 'integer',
                'group'       => 'seller',
                'label'       => 'Radius Pencarian Default (km)',
                'description' => 'Radius default pencarian seller jika buyer tidak menentukan.',
            ],
            [
                'key'         => 'seller.max_products',
                'value'       => '50',
                'type'        => 'integer',
                'group'       => 'seller',
                'label'       => 'Maks Produk per Seller',
                'description' => 'Jumlah maksimum produk yang boleh dimiliki satu seller.',
            ],

            // ─── Complaint ─────────────────────────────────────────────
            [
                'key'         => 'complaint.allow_anonymous',
                'value'       => '0',
                'type'        => 'boolean',
                'group'       => 'complaint',
                'label'       => 'Izinkan Keluhan Anonim',
                'description' => 'Apakah pembeli boleh mengirim keluhan tanpa mencantumkan nama.',
            ],

            // ─── General ──────────────────────────────────────────────
            [
                'key'         => 'app.maintenance_mode',
                'value'       => '0',
                'type'        => 'boolean',
                'group'       => 'general',
                'label'       => 'Mode Maintenance',
                'description' => 'Aktifkan untuk menonaktifkan akses API sementara.',
            ],
            [
                'key'         => 'app.announcement',
                'value'       => '',
                'type'        => 'string',
                'group'       => 'general',
                'label'       => 'Pengumuman',
                'description' => 'Teks pengumuman yang ditampilkan kepada semua user. Kosongkan jika tidak ada.',
            ],
        ];

        foreach ($settings as $data) {
            Setting::firstOrCreate(['key' => $data['key']], $data);
        }
    }
}
