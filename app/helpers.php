<?php

if (!function_exists('activeStore')) {
    function activeStore(): ?\App\Models\Store
    {
        static $store = null;

        if ($store === null) {
            $store = \App\Models\Store::find(session('store_id'));
        }

        return $store;
    }
}

if (!function_exists('formatCNPJ')) {
    function formatCNPJ(?string $cnpj): ?string
    {
        if (!$cnpj) return null;
        $cnpj = preg_replace('/\D/', '', $cnpj);
        if (strlen($cnpj) !== 14) return null;
        return sprintf('%s.%s.%s/%s-%s',
            substr($cnpj, 0, 2),
            substr($cnpj, 2, 3),
            substr($cnpj, 5, 3),
            substr($cnpj, 8, 4),
            substr($cnpj, 12, 2)
        );
    }
}

if (!function_exists('formatCPF')) {
    function formatCPF(?string $cpf): ?string
    {
        if (!$cpf) return null;
        $cpf = preg_replace('/\D/', '', $cpf);
        if (strlen($cpf) !== 11) return null;
        return sprintf('%s.%s.%s-%s',
            substr($cpf, 0, 3),
            substr($cpf, 3, 3),
            substr($cpf, 6, 3),
            substr($cpf, 9, 2)
        );
    }
}

if (!function_exists('formatIdentifier')) {
    function formatIdentifier(?\App\Models\Store $store): string
    {
        if ($store->cpf && strlen(preg_replace('/\D/', '', $store->cpf)) === 11) {
            return 'CPF: ' . formatCPF($store->cpf);
        } elseif ($store->cnpj && strlen(preg_replace('/\D/', '', $store->cnpj)) === 14) {
            return 'CNPJ: ' . formatCNPJ($store->cnpj);
        }
        return '';
    }
}
