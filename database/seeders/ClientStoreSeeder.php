<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Store;

class ClientStoreSeeder extends Seeder
{
    public function run(): void
    {
        // Cria 50 lojas
        $stores = Store::factory(50)->create();

        // Cria 50 clientes
        $clients = Client::factory(50)->create();

        // Relaciona clientes com lojas aleatórias
        foreach ($clients as $client) {
            $client->stores()->attach(
                $stores->random(rand(1, 5))->pluck('id')->toArray()
            );
        }
    }
}
