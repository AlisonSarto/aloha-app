<?php

namespace Database\Seeders\Core;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Services\GestaoClickService;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $gestaoClick = app(GestaoClickService::class);
        $gestaoClick->syncStores();
    }
}
