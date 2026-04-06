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
            Core\AdminSeeder::class,
        ]);

        if (app()->environment('local') && env('SEED_FAKE_DATA')) {
            $this->call([
                Local\ClientSeeder::class,
                Local\SellerSeeder::class,
            ]);
        }
    }
}
