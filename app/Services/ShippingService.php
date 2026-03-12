<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ShippingService
{
    public function calculate($destination)
    {
        $apiKey = config('services.google_maps.key');
        $origin = config('services.google_maps.origin');

        $response = Http::withOptions([
            'verify' => base_path('storage/certs/cacert.pem'),
        ])->get(
            "https://maps.googleapis.com/maps/api/distancematrix/json",
            [
                'origins' => $origin,
                'destinations' => $destination,
                'key' => $apiKey,
                'units' => 'metric'
            ]
        );

        $data = $response->json();

        $distanceMeters = $data['rows'][0]['elements'][0]['distance']['value'];

        $distanceKm = $distanceMeters / 1000;

        $shipping = floor($distanceKm) * 1; // R$1 por km

        return $shipping;
    }
}
