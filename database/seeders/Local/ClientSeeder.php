<?php

namespace Database\Seeders\Local;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Store;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::factory(20)->create();
    }
}
