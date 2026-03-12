<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Store;
use App\Services\GestaoClickService;

class SyncGestaoClickStores extends Command
{
    protected $signature = 'app:sync-gestao-click-stores';
    protected $description = 'Sincronização das lojas do Gestão Click com o banco local';

    public function handle(GestaoClickService $gestaoClick)
    {
        $stores = $gestaoClick->getStores();

        $this->info(json_encode($stores, JSON_PRETTY_PRINT));

        // $this->info('Stores sincronizadas com sucesso.');
    }
}
