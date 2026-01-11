<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    \App\Models\User::updateOrCreate(
        ['email' => 'admin@test.com'],
        [
            'name' => 'Admin Qodri',
            'password' => bcrypt('password123'),
            'role' => 'admin'
        ]
    );

    \App\Models\User::updateOrCreate(
        ['email' => 'member@test.com'],
        [
            'name' => 'Member Syaddad',
            'password' => bcrypt('password123'),
            'role' => 'Member'
        ]
    );
}
}
