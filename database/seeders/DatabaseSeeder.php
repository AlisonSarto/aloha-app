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
        $this->call([
            Core\RoleSeeder::class,
            Core\PriceTableSeeder::class,
        ]);

        if (app()->environment('local')) {
            $this->call([
                Local\UserSeeder::class,
                Local\ClientStoreSeeder::class,
            ]);
        }
    }
}
