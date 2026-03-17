<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'gestaoclick' => [
        'url' => env('GESTAOCLICK_URL'),
        'token' => env('GESTAOCLICK_ACCESS_TOKEN'),
        'secret' => env('GESTAOCLICK_SECRET_TOKEN'),
    ],

    'google_maps' => [
        'key' => env('GOOGLE_MAPS_KEY'),
        'origin' => env('GOOGLE_MAPS_ORIGIN'),
    ],

    'open_cnpj' => [
        'url' => env('OPEN_CNPJ_URL'),
    ],

];
