<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Store;
use App\Services\GestaoClickService;

class SyncGestaoClickStores extends Command
{
    protected $signature = 'sync:stores';
    protected $description = 'Sincronização das lojas do Gestão Click com o banco local';

    public function handle(GestaoClickService $gestaoClick)
    {
        $this->info('Iniciando sincronização de lojas');

        $start = microtime(true);
        $stores = $gestaoClick->syncStores();
        $end = microtime(true);

        $time = $end - $start;

        $this->info('Lojas sincronizadas com sucesso ('.number_format($time, 3).'s)');
    }
}
