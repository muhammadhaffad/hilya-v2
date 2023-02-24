<?php

namespace App\Services\ShippingCost;

use GuzzleHttp\Client;

class RajaOngkirShippingCostServiceImplement implements ShippingCostService
{
    public function getCosts(int $origin, int $destination, int $weight, string $courier): array
    {
        $client = new Client([
            'base_uri' => 'https://api.rajaongkir.com/starter/',
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'key' => env('RAJAONGKIR_API_KEY')
            ]
        ]);
        
        try {
            $response = $client->request('POST', 'cost', [
                'form_params' => [
                    'origin' => $origin,
                    'destination' => $destination,
                    'weight' => $weight,
                    'courier' => $courier
                ]
            ]);
            $response = json_decode($response->getBody(), true);
            $result = array(
                'courier' => $response['rajaongkir']['results'][0]['name'],
                'services' => array()
            );
            foreach ($response['rajaongkir']['results'][0]['costs'] as $cost) {
                $result['services'][$cost['service']] = $cost['cost'][0]['value'];
            }
            return $result;
        } catch (\Exception $e) {
            return array($e);
        }
    }
}
