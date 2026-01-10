<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    \App\Models\Court::create([
        'name' => 'Lapangan VIP 1',
        'floor_type' => 'Karpet',
        'price_per_hour' => 50000,
        'photo' => 'karpet_vip.jpg'
    ]);

    \App\Models\Court::create([
        'name' => 'Lapangan Kayu 2',
        'floor_type' => 'Kayu',
        'price_per_hour' => 30000,
        'photo' => 'kayu_standard.jpg'
    ]);  //
    }
}
