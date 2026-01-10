<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    \App\Models\Equipment::create([
        'name' => 'Raket Yonex Voltric',
        'price' => 15000,
        'stock' => 10
    ]);

    \App\Models\Equipment::create([
        'name' => 'Sepatu Badminton Size 42',
        'price' => 20000,
        'stock' => 5
    ]);
}
}
