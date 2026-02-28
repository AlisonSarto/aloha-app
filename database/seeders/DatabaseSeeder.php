<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'seller']);
        Role::create(['name' => 'client']);

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
