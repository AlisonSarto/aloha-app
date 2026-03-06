<?php

namespace Database\Seeders\Local;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@email.com',
        ])->assignRole('admin');

        User::factory()->create([
            'name' => 'Seller',
            'email' => 'seller@email.com',
        ])->assignRole('seller');

        User::factory()->create([
            'name' => 'Client',
            'email' => 'client@email.com',
        ])->assignRole('client');
    }
}
