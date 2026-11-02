<?php

namespace Database\Seeders\Core;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate([
            'email' => 'admin@email.com',
        ], [
            'name' => 'Admin',
            'password' => '123',
            'email_verified_at' => now(),
        ]);

        $admin->assignRole('admin');
    }
}
