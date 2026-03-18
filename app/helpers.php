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
