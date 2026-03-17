<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ShowAppQr extends Command
{
    protected $signature = 'app:qr';
    protected $description = 'Exibe um QR Code para acessar a aplicação via celular';

    public function handle()
    {
        $ip = $this->getLocalIp();

        if (!$ip) {
            $this->error('Não foi possível encontrar o IP local.');
            return;
        }

        $url = "http://{$ip}:8000";

        $this->info("Acesse no celular:");
        $this->line($url);
    }

    private function getLocalIp()
    {
        return gethostbyname(gethostname());
    }
}
